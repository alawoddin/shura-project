<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\Category;
use App\Models\User;
use App\Services\FinancialService;
use App\Services\LoanEnforcementService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditController extends Controller
{
    public function __construct(
        protected FinancialService $financialService,
        protected LoanEnforcementService $loanEnforcement
    ) {}

    public function AllCredits()
    {
        $this->loanEnforcement->syncAllLoanStatuses();

        $alldata = Credit::with('user', 'category', 'sourceAccount', 'payments')->latest()->get();
        $suspendedUsers = $this->loanEnforcement->getSuspendedLoanUsers();
        $loanService = $this->loanEnforcement;

        return view('admin.pages.credit.all_credit', compact('alldata', 'suspendedUsers', 'loanService'));
    }

    public function AddCredit()
    {
        $users = User::where('role', 'user')->where('status', '!=', 'suspended')->get();
        $categories = Category::all();
        $sourceAccounts = Category::cashAccounts()->get();

        return view('admin.pages.credit.add_credit', compact('users', 'categories', 'sourceAccounts'));
    }

    public function StoreCredit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'source_account_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $this->financialService->decreaseAccountBalance(
                    (int) $request->source_account_id,
                    (float) $request->amount
                );

                Credit::create([
                    'user_id' => $request->user_id,
                    'category_id' => $request->category_id,
                    'source_account_id' => $request->source_account_id,
                    'date' => $request->date,
                    'amount' => $request->amount,
                    'remaining_amount' => $request->amount,
                    'description' => $request->description,
                    'last_payment_at' => null,
                ]);
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $this->loanEnforcement->syncUserLoanStatus((int) $request->user_id);

        return redirect()->route('all.credits')->with([
            'message' => 'Credit Inserted Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function EditCredit($id)
    {
        $credit = Credit::findOrFail($id);
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        $sourceAccounts = Category::cashAccounts()->get();

        return view('admin.pages.credit.edit_credit', compact('credit', 'users', 'categories', 'sourceAccounts'));
    }

    public function UpdateCredit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:credits,id',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'source_account_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $credit = Credit::findOrFail($request->id);
                $oldAmount = (float) $credit->amount;
                $oldSourceAccountId = $credit->source_account_id;
                $paidPortion = $oldAmount - (float) $credit->remaining_amount;

                if ($oldSourceAccountId != $request->source_account_id) {
                    $this->financialService->increaseAccountBalance($oldSourceAccountId, $oldAmount);
                    $this->financialService->decreaseAccountBalance(
                        (int) $request->source_account_id,
                        (float) $request->amount
                    );
                } else {
                    $this->financialService->adjustAccountBalance(
                        (int) $request->source_account_id,
                        $oldAmount,
                        (float) $request->amount
                    );
                }

                $newAmount = (float) $request->amount;

                $credit->update([
                    'user_id' => $request->user_id,
                    'category_id' => $request->category_id,
                    'source_account_id' => $request->source_account_id,
                    'date' => $request->date,
                    'amount' => $newAmount,
                    'remaining_amount' => max(0, $newAmount - $paidPortion),
                    'description' => $request->description,
                    'status' => ($newAmount - $paidPortion) <= 0 ? 'paid' : 'active',
                ]);
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $credit = Credit::findOrFail($request->id);
        $this->loanEnforcement->syncUserLoanStatus((int) $credit->user_id);

        return redirect()->route('all.credits')->with([
            'message' => 'Credit Updated Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function DeleteCredit($id)
    {
        $credit = Credit::findOrFail($id);
        $userId = $credit->user_id;

        DB::transaction(function () use ($id) {
            $credit = Credit::findOrFail($id);
            $this->financialService->reverseLoanDisbursement($credit);
            $credit->delete();
        });

        $this->loanEnforcement->syncUserLoanStatus((int) $userId);

        return redirect()->route('all.credits')->with([
            'message' => 'Credit Deleted Successfully',
            'alert-type' => 'success',
        ]);
    }

    public function PaidCredit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:credits,id',
            'paid_amount' => 'required|numeric|min:0.01',
            'paid_at' => 'nullable|date',
        ]);

        $credit = Credit::findOrFail($request->id);
        $paidAmount = (float) $request->paid_amount;
        $paidAt = $request->paid_at ? Carbon::parse($request->paid_at) : now();

        if ($paidAmount > $credit->remaining_amount) {
            return redirect()->route('all.credits')->with([
                'message' => 'Paid amount cannot be greater than remaining amount',
                'alert-type' => 'error',
            ]);
        }

        DB::transaction(function () use ($credit, $paidAmount, $paidAt) {
            $credit->remaining_amount -= $paidAmount;

            if ($credit->remaining_amount <= 0) {
                $credit->remaining_amount = 0;
                $credit->status = 'paid';
            }

            $credit->save();

            if ($credit->source_account_id) {
                $this->financialService->increaseAccountBalance($credit->source_account_id, $paidAmount);
            }

            $this->loanEnforcement->recordCreditPayment($credit, $paidAmount, $paidAt);
        });

        return redirect()->route('all.credits')->with([
            'message' => 'Credit Paid Successfully',
            'alert-type' => 'success',
        ]);
    }
}
