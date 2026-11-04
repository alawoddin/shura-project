<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Credit;
use App\Models\User;
use App\Models\ReceivePayment;
use App\Services\FinancialService;
use App\Services\LoanEnforcementService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * دریافت فیس و قرض
 * Receives member fees (one or several months at once) and loan repayments.
 */
class ReceivePaymentController extends Controller
{
    public const MAX_FEE_MONTHS = 36;

    public function __construct(
        protected FinancialService $financialService,
        protected LoanEnforcementService $loanEnforcement
    ) {}

    public function AllReceivePayment()
    {
        $alldata = ReceivePayment::with('users', 'category', 'credit')->latest()->get();

        return view('admin.pages.receive.all_receive_payment', compact('alldata'));
    }

    public function AddReceivePayment()
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        $categories = Category::feeAndLoanTypes()->orderBy('name')->get();

        return view('admin.pages.receive.add_receive_payment', compact('users', 'categories'));
    }

    public function MemberFeeStatus(int $userId)
    {
        $user = User::where('role', 'user')->findOrFail($userId);

        return response()->json(
            $this->financialService->getMemberFeeRemaining($user)
        );
    }

    /**
     * Active (unpaid) loans of a member, for the loan dropdown on the receive page.
     */
    public function MemberLoans(int $userId)
    {
        $user = User::where('role', 'user')->findOrFail($userId);

        $loans = Credit::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('remaining_amount', '>', 0)
            ->orderBy('date')
            ->get()
            ->map(fn (Credit $credit) => [
                'id' => $credit->id,
                'date' => (string) $credit->date,
                'amount' => (float) $credit->amount,
                'remaining_amount' => (float) $credit->remaining_amount,
                'months_without_payment' => $this->loanEnforcement->getMonthsWithoutPayment($credit),
            ]);

        return response()->json([
            'user_name' => $user->name,
            'loans' => $loans,
        ]);
    }

    public function StoreReceivePayment(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    if (! Category::feeAndLoanTypes()->whereKey($value)->exists()) {
                        $fail('نوع پرداخت باید فیس اعضا یا قرض باشد.');
                    }
                },
            ],
            'transaction_type' => 'required|in:credit,debit',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'month_of' => 'nullable|date_format:Y-m',
            'months_count' => 'nullable|integer|min:1|max:' . self::MAX_FEE_MONTHS,
            'credit_id' => 'nullable|exists:credits,id',
        ], [
            'month_of.date_format' => 'ماه را درست انتخاب کنید.',
            'months_count.max' => 'حداکثر ' . self::MAX_FEE_MONTHS . ' ماه را می‌توانید یکجا پرداخت کنید.',
        ]);

        $category = Category::findOrFail($validated['category_id']);

        if ($category->isLoanType()) {
            return $this->storeLoanRepayment($request, $validated);
        }

        return $this->storeFeePayments($request, $validated, $category);
    }

    /**
     * Fee payment for one or several months at once (فیس چند ماه یکجا).
     * One record is created per month so the monthly fee report stays correct.
     */
    protected function storeFeePayments(Request $request, array $validated, Category $category)
    {
        $monthsCount = 1;

        if ($category->is_monthly_fee) {
            $request->validate(['month_of' => 'required|date_format:Y-m'], [
                'month_of.required' => 'لطفاً ماه را انتخاب کنید.',
            ]);
            $monthsCount = (int) ($validated['months_count'] ?? 1);
        }

        $total = round((float) $validated['amount'], 2);
        $perMonth = floor(($total / $monthsCount) * 100) / 100;
        $startMonth = $category->is_monthly_fee
            ? Carbon::createFromFormat('Y-m-d', $validated['month_of'] . '-01')->startOfMonth()
            : null;

        try {
            DB::transaction(function () use ($validated, $monthsCount, $total, $perMonth, $startMonth) {
                for ($i = 0; $i < $monthsCount; $i++) {
                    $isLast = $i === $monthsCount - 1;
                    // The last month takes the rounding remainder so the sum equals the total.
                    $amount = $isLast ? round($total - $perMonth * ($monthsCount - 1), 2) : $perMonth;

                    $payment = ReceivePayment::create([
                        'user_id' => $validated['user_id'],
                        'category_id' => $validated['category_id'],
                        'transaction_type' => $validated['transaction_type'],
                        'date' => $validated['date'],
                        'amount' => $amount,
                        'description' => $validated['description'] ?? null,
                        'month_of' => $startMonth?->copy()->addMonthsNoOverflow($i)->format('Y-m'),
                        'review_status' => 'pending_review',
                    ]);

                    $this->financialService->applyReceivePaymentBalance($payment);
                }
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $message = $monthsCount > 1
            ? "فیس {$monthsCount} ماه با موفقیت ثبت شد"
            : 'دریافت پرداخت با موفقیت اضافه شد';

        if ($category->is_monthly_fee) {
            $member = User::find($validated['user_id']);
            $remaining = $this->financialService->getMemberFeeRemaining($member);
            $count = $remaining['unpaid_months_count'];

            if ($count > 0) {
                $message .= " — هنوز {$count} ماه فیس به سیستم بدهکار است"
                    . ' (باقی‌مانده: ' . number_format($remaining['remaining_amount'], 2) . ')';
            } else {
                $message .= ' — تمام فیس‌های ماهانه تسویه شد.';
            }

            $this->loanEnforcement->syncUserLoanStatus((int) $member->id);
        }

        return redirect()->route('all.receive.payment')->with([
            'message' => $message,
            'alert-type' => 'success',
        ]);
    }

    /**
     * Loan repayment (دریافت قرض): reduces the loan's remaining amount and returns
     * the money to the account the loan was paid from.
     */
    protected function storeLoanRepayment(Request $request, array $validated)
    {
        $request->validate(['credit_id' => 'required|exists:credits,id'], [
            'credit_id.required' => 'لطفاً قرض مربوطه را انتخاب کنید.',
        ]);

        $credit = Credit::findOrFail($validated['credit_id']);
        $amount = round((float) $validated['amount'], 2);

        if ((int) $credit->user_id !== (int) $validated['user_id']) {
            return back()->withInput()->with([
                'message' => 'این قرض مربوط به عضو انتخاب‌شده نیست.',
                'alert-type' => 'error',
            ]);
        }

        if ($credit->status !== 'active' || (float) $credit->remaining_amount <= 0) {
            return back()->withInput()->with([
                'message' => 'این قرض قبلاً تسویه شده است.',
                'alert-type' => 'error',
            ]);
        }

        if ($amount > (float) $credit->remaining_amount) {
            return back()->withInput()->with([
                'message' => 'مقدار پرداخت از باقی‌مانده قرض (' . number_format($credit->remaining_amount, 2) . ') بیشتر است.',
                'alert-type' => 'error',
            ]);
        }

        DB::transaction(function () use ($validated, $credit, $amount) {
            $credit = Credit::lockForUpdate()->findOrFail($credit->id);

            $credit->remaining_amount = max(0, (float) $credit->remaining_amount - $amount);
            $credit->status = $credit->remaining_amount <= 0 ? 'paid' : 'active';
            $credit->save();

            $accountId = $credit->source_account_id ?? $this->financialService->getDefaultCashAccountId();

            if ($accountId) {
                $this->financialService->increaseAccountBalance((int) $accountId, $amount);
            }

            $creditPayment = $this->loanEnforcement->recordCreditPayment(
                $credit,
                $amount,
                Carbon::parse($validated['date']),
                $validated['description'] ?? null
            );

            ReceivePayment::create([
                'user_id' => $validated['user_id'],
                'category_id' => $validated['category_id'],
                'credit_id' => $credit->id,
                'credit_payment_id' => $creditPayment->id,
                'transaction_type' => 'credit',
                'date' => $validated['date'],
                'amount' => $amount,
                'description' => $validated['description'] ?? null,
                'month_of' => null,
                'review_status' => 'pending_review',
            ]);
        });

        $credit->refresh();
        $message = $credit->status === 'paid'
            ? 'قسط قرض ثبت شد — این قرض کاملاً تسویه شد.'
            : 'قسط قرض ثبت شد — باقی‌مانده قرض: ' . number_format($credit->remaining_amount, 2);

        return redirect()->route('all.receive.payment')->with([
            'message' => $message,
            'alert-type' => 'success',
        ]);
    }

    public function EditReceivePayment($id)
    {
        $editData = ReceivePayment::with(['users', 'category'])->findOrFail($id);

        if ($editData->isLoanRepayment()) {
            return redirect()->route('all.receive.payment')->with([
                'message' => 'قسط قرض قابل ویرایش نیست. لطفاً آن را حذف و دوباره ثبت کنید.',
                'alert-type' => 'warning',
            ]);
        }

        $users = User::where('role', 'user')->orderBy('name')->get();
        $categories = Category::where('is_monthly_fee', true)
            ->orWhere('id', $editData->category_id)
            ->orderBy('name')
            ->get();

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

        if ($payment->isLoanRepayment()) {
            return redirect()->route('all.receive.payment')->with([
                'message' => 'قسط قرض قابل ویرایش نیست. لطفاً آن را حذف و دوباره ثبت کنید.',
                'alert-type' => 'warning',
            ]);
        }

        if ($category->isLoanType()) {
            return back()->withInput()->with([
                'message' => 'برای ثبت قسط قرض، یک دریافت جدید اضافه کنید.',
                'alert-type' => 'error',
            ]);
        }

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
            if ($payment->isLoanRepayment()) {
                $credit = Credit::lockForUpdate()->find($payment->credit_id);

                if ($credit) {
                    $accountId = $credit->source_account_id ?? $this->financialService->getDefaultCashAccountId();

                    if ($accountId) {
                        $this->financialService->decreaseAccountBalance((int) $accountId, (float) $payment->amount, true);
                    }

                    $this->loanEnforcement->reverseCreditPayment(
                        $credit,
                        (float) $payment->amount,
                        $payment->credit_payment_id
                    );
                }
            } else {
                $this->financialService->reverseReceivePaymentBalance(
                    $payment->transaction_type,
                    (float) $payment->amount,
                    $payment->category_id
                );
            }

            $payment->delete();
        });

        $notification = [
            'message' => 'دریافت پرداخت با موفقیت حذف شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.receive.payment')->with($notification);
    }
}
