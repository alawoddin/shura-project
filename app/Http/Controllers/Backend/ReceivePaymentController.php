<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\ReceivePayment;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceivePaymentController extends Controller
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

    public function AllReceivePayment()
    {
        $alldata = ReceivePayment::with('users', 'category')->latest()->get();

        return view('admin.pages.receive.all_receive_payment', compact('alldata'));
    }

    public function AddReceivePayment()
    {
        $users = User::where('role', 'user')->get();
        $categories = Category::paymentTypes()->get();

        if ($categories->isEmpty()) {
            $categories = Category::all();
        }

        return view('admin.pages.receive.add_receive_payment', compact('users', 'categories'));
    }

    public function StoreReceivePayment(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'transaction_type' => 'required|in:credit,debit',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'month_of' => 'nullable|string',
        ]);

        $category = Category::findOrFail($validated['category_id']);

        if ($category->is_monthly_fee) {
            $request->validate(['month_of' => 'required|string']);
            $validated['month_of'] = $request->month_of;
        } else {
            $validated['month_of'] = null;
        }

        try {
            DB::transaction(function () use ($validated) {
                $payment = ReceivePayment::create([
                    ...$validated,
                    'review_status' => 'pending_review',
                ]);

                $this->financialService->applyReceivePaymentBalance($payment);
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $notification = [
            'message' => 'دریافت پرداخت با موفقیت اضافه شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.receive.payment')->with($notification);
    }

    public function EditReceivePayment($id)
    {
        $editData = ReceivePayment::with(['users', 'category'])->findOrFail($id);
        $users = User::where('role', 'user')->get();
        $categories = Category::paymentTypes()->get();

        if ($categories->isEmpty()) {
            $categories = Category::all();
        }

        return view(
            'admin.pages.receive.edit_receive_payment',
            compact('editData', 'users', 'categories')
        );
    }

    public function UpdateReceivePayment(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:receive_payments,id',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'transaction_type' => 'required|in:credit,debit',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'month_of' => 'nullable|string',
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $payment = ReceivePayment::findOrFail($validated['id']);

        if ($category->is_monthly_fee) {
            $request->validate(['month_of' => 'required|string']);
            $validated['month_of'] = $request->month_of;
        } else {
            $validated['month_of'] = null;
        }

        $previousType = $payment->transaction_type;
        $previousAmount = (float) $payment->amount;
        $previousCategoryId = $payment->category_id;

        try {
            DB::transaction(function () use ($payment, $validated, $previousType, $previousAmount, $previousCategoryId) {
                $this->financialService->reverseReceivePaymentBalance(
                    $previousType,
                    $previousAmount,
                    $previousCategoryId
                );

                unset($validated['id']);
                $payment->update($validated);

                $this->financialService->applyReceivePaymentBalance($payment->fresh());
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $notification = [
            'message' => 'دریافت پرداخت با موفقیت به روز شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.receive.payment')->with($notification);
    }

    public function DeleteReceivePayment($id)
    {
        $payment = ReceivePayment::findOrFail($id);

        DB::transaction(function () use ($payment) {
            $this->financialService->reverseReceivePaymentBalance(
                $payment->transaction_type,
                (float) $payment->amount,
                $payment->category_id
            );

            $payment->delete();
        });

        $notification = [
            'message' => 'دریافت پرداخت با موفقیت حذف شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.receive.payment')->with($notification);
    }
}
