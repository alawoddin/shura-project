<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\Aids;
use App\Models\Category;
use App\Models\User;
use App\Services\FinancialService;
use App\Services\LoanEnforcementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AidController extends Controller
{
    public function __construct(
        protected FinancialService $financialService,
        protected LoanEnforcementService $loanEnforcement
    ) {}

    public function AllAids()
    {
        $alldata = Aids::with(['user', 'category', 'sourceAccount'])->latest('date')->get();
        $aidAccounts = Category::aidAccounts()->orderBy('name')->get();

        $aidTotals = Aids::selectRaw('source_account_id, SUM(amount) as total')
            ->groupBy('source_account_id')
            ->pluck('total', 'source_account_id');

        [, $blockedUsers] = $this->loanEnforcement->partitionByDebt(
            User::where('role', 'user')->orderBy('name')->get()
        );

        return view('admin.pages.aid.all_aids', compact('alldata', 'aidAccounts', 'aidTotals', 'blockedUsers'));
    }

    public function AddAid()
    {
        [$users, $blockedUsers] = $this->loanEnforcement->partitionByDebt(
            User::where('role', 'user')->orderBy('name')->get()
        );
        $sourceAccounts = Category::aidAccounts()->orderBy('name')->get();

        return view('admin.pages.aid.add_aid', compact('users', 'blockedUsers', 'sourceAccounts'));
    }

    /**
     * Aid may only be paid from an account marked as an aid account.
     * When editing, the account already stored on the record stays valid.
     */
    protected function aidAccountRule(?int $currentAccountId = null): array
    {
        return [
            'required',
            function ($attribute, $value, $fail) use ($currentAccountId) {
                if ($currentAccountId && (int) $value === $currentAccountId) {
                    return;
                }

                if (! Category::aidAccounts()->whereKey($value)->exists()) {
                    $fail('لطفاً یک حساب کمک‌ها را انتخاب کنید.');
                }
            },
        ];
    }

    public function StoreAid(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'source_account_id' => $this->aidAccountRule(),
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->user_id);
        $this->loanEnforcement->syncUserLoanStatus((int) $user->id);
        $user->refresh();

        if (! $this->loanEnforcement->canReceiveAid($user)) {
            return back()->withInput()->with([
                'message' => $this->loanEnforcement->aidBlockReason($user),
                'alert-type' => 'error',
            ]);
        }

        try {
            DB::transaction(function () use ($request) {
                $this->financialService->decreaseAccountBalance(
                    (int) $request->source_account_id,
                    (float) $request->amount
                );

                Aids::create([
                    'user_id' => $request->user_id,
                    'category_id' => $request->source_account_id,
                    'source_account_id' => $request->source_account_id,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'description' => $request->description,
                ]);
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $notification = [
            'message' => 'کمک مالی با موفقیت اضافه شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.aid')->with($notification);
    }

    public function EditAid($id)
    {
        $editdata = Aids::findOrFail($id);
        $users = User::where('role', 'user')->orderBy('name')->get();
        $sourceAccounts = Category::aidAccounts()
            ->orWhere('id', $editdata->source_account_id)
            ->orderBy('name')
            ->get();

        return view('admin.pages.aid.edit_aid', compact('editdata', 'users', 'sourceAccounts'));
    }

    public function UpdateAid(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:aids,id',
            'user_id' => 'required|exists:users,id',
            'source_account_id' => $this->aidAccountRule(
                (int) Aids::whereKey($request->id)->value('source_account_id') ?: null
            ),
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $user = User::findOrFail($request->user_id);
        $this->loanEnforcement->syncUserLoanStatus((int) $user->id);
        $user->refresh();

        if (! $this->loanEnforcement->canReceiveAid($user)) {
            return back()->withInput()->with([
                'message' => $this->loanEnforcement->aidBlockReason($user),
                'alert-type' => 'error',
            ]);
        }

        try {
            DB::transaction(function () use ($request) {
                $aid = Aids::findOrFail($request->id);
                $oldAmount = (float) $aid->amount;
                $oldSourceAccountId = $aid->source_account_id;

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

                $aid->update([
                    'user_id' => $request->user_id,
                    'category_id' => $request->source_account_id,
                    'source_account_id' => $request->source_account_id,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'description' => $request->description,
                ]);
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $notification = [
            'message' => 'کمک مالی با موفقیت به روز شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.aid')->with($notification);
    }

    public function DeleteAid($id)
    {
        DB::transaction(function () use ($id) {
            $aid = Aids::findOrFail($id);
            $this->financialService->increaseAccountBalance(
                $aid->source_account_id,
                (float) $aid->amount
            );
            $aid->delete();
        });

        $notification = [
            'message' => 'کمک مالی با موفقیت حذف شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.aid')->with($notification);
    }
}
