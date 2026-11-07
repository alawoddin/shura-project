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
use Illuminate\Validation\ValidationException;

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

    public function MemberFeeStatus(Request $request, int $userId)
    {
        $user = User::where('role', 'user')->findOrFail($userId);
        $excludeId = $request->integer('exclude') ?: null;

        return response()->json(
            $this->financialService->getMemberFeeRemaining($user, $excludeId ? (string) $excludeId : null)
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
            'transaction_type' => 'nullable|in:credit',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'months' => 'nullable|array|max:' . self::MAX_FEE_MONTHS,
            'months.*' => 'date_format:Y-m|distinct',
            'credit_id' => 'nullable|exists:credits,id',
        ], $this->monthValidationMessages());

        // Only credit (money received) is allowed on this page.
        $validated['transaction_type'] = 'credit';

        $category = Category::findOrFail($validated['category_id']);

        if ($category->isLoanType()) {
            return $this->storeLoanRepayment($request, $validated);
        }

        return $this->storeFeePayments($request, $validated, $category);
    }

    /**
     * Fee payment for the months the admin selected (e.g. Jan, Feb, Aug).
     * One record is created per month so the monthly fee report stays correct.
     */
    protected function storeFeePayments(Request $request, array $validated, Category $category)
    {
        $total = round((float) $validated['amount'], 2);
        $member = User::findOrFail($validated['user_id']);

        // [month => amount]; a fee type without months is a single record with no month.
        $amounts = $category->is_monthly_fee
            ? $this->feeMonthAmounts($member, $request->input('months', []), $total)
            : [null => $total];

        try {
            DB::transaction(function () use ($validated, $amounts) {
                foreach ($amounts as $month => $amount) {
                    $payment = ReceivePayment::create([
                        'user_id' => $validated['user_id'],
                        'category_id' => $validated['category_id'],
                        'transaction_type' => $validated['transaction_type'],
                        'date' => $validated['date'],
                        'amount' => $amount,
                        'description' => $validated['description'] ?? null,
                        'month_of' => $month === '' ? null : $month,
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

        $monthsCount = count($amounts);
        $message = $monthsCount > 1
            ? "فیس {$monthsCount} ماه با موفقیت ثبت شد (" . implode('، ', array_keys($amounts)) . ')'
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
            'transaction_type' => 'nullable|in:credit',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'months' => 'nullable|array|max:' . self::MAX_FEE_MONTHS,
            'months.*' => 'date_format:Y-m|distinct',
        ], $this->monthValidationMessages());

        // Only credit (money received) is allowed on this page.
        $validated['transaction_type'] = 'credit';
        unset($validated['months']);

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

        $total = round((float) $validated['amount'], 2);
        $member = User::findOrFail($validated['user_id']);
        $excludeId = (int) $payment->user_id === (int) $member->id ? (string) $payment->id : null;

        // [month => amount] for the selected months (this payment itself is left out of the "paid" check).
        $amounts = $category->is_monthly_fee
            ? $this->feeMonthAmounts($member, $request->input('months', []), $total, $excludeId)
            : [null => $total];

        $previousType = $payment->transaction_type;
        $previousAmount = (float) $payment->amount;
        $previousCategoryId = $payment->category_id;

        try {
            DB::transaction(function () use ($payment, $validated, $amounts, $previousType, $previousAmount, $previousCategoryId) {
                $this->financialService->reverseReceivePaymentBalance(
                    $previousType,
                    $previousAmount,
                    $previousCategoryId
                );

                unset($validated['id']);
                $first = true;

                foreach ($amounts as $month => $amount) {
                    $data = array_merge($validated, [
                        'amount' => $amount,
                        'month_of' => $month === '' ? null : $month,
                    ]);

                    if ($first) {
                        $payment->update($data);
                        $this->financialService->applyReceivePaymentBalance($payment->fresh());
                        $first = false;
                        continue;
                    }

                    $extra = ReceivePayment::create($data + ['review_status' => 'pending_review']);
                    $this->financialService->applyReceivePaymentBalance($extra);
                }
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

    protected function monthValidationMessages(): array
    {
        return [
            'transaction_type.in' => 'در این صفحه فقط دریافت (بستانکار) مجاز است.',
            'months.max' => 'حداکثر ' . self::MAX_FEE_MONTHS . ' ماه را می‌توانید یکجا پرداخت کنید.',
            'months.*.date_format' => 'ماه انتخاب‌شده درست نیست.',
            'months.*.distinct' => 'یک ماه دو بار انتخاب شده است.',
        ];
    }

    /**
     * Work out how much of the total goes to each selected month.
     * If the total equals what is due for the selected months, each month gets its own due
     * (unpaid remainder, or the monthly fee for a coming month); otherwise the total is split evenly.
     *
     * @return array<string, float> [Y-m => amount]
     */
    protected function feeMonthAmounts(User $member, array $months, float $total, ?string $excludePaymentId = null): array
    {
        $months = array_values(array_unique(array_filter($months)));
        sort($months);

        if (! $months) {
            throw ValidationException::withMessages([
                'months' => 'لطفاً حداقل یک ماه را برای پرداخت فیس انتخاب کنید.',
            ]);
        }

        $status = $this->financialService->getMemberFeeRemaining($member, $excludePaymentId);
        $monthlyFee = (float) $status['monthly_fee'];
        $registerMonth = $status['register_month'];
        $currentMonth = now()->format('Y-m');
        $unpaid = collect($status['months'])->pluck('remaining', 'month');

        $dues = [];

        foreach ($months as $month) {
            if ($monthlyFee <= 0) {
                $dues[$month] = 0.0;
            } elseif ($unpaid->has($month)) {
                $dues[$month] = (float) $unpaid[$month];
            } elseif ($month > $currentMonth) {
                $dues[$month] = $monthlyFee;
            } elseif ($month < $registerMonth) {
                throw ValidationException::withMessages([
                    'months' => "ماه {$month} قبل از ماه ثبت‌نام عضو ({$registerMonth}) است.",
                ]);
            } else {
                throw ValidationException::withMessages([
                    'months' => "فیس ماه {$month} قبلاً کامل پرداخت شده است.",
                ]);
            }
        }

        $dueTotal = round(array_sum($dues), 2);

        if ($dueTotal > 0 && abs($dueTotal - $total) < 0.01) {
            return array_map(fn ($due) => round($due, 2), $dues);
        }

        // Split evenly; the last month takes the rounding remainder so the sum equals the total.
        $count = count($months);
        $perMonth = floor(($total / $count) * 100) / 100;
        $amounts = [];

        foreach ($months as $i => $month) {
            $amounts[$month] = $i === $count - 1
                ? round($total - $perMonth * ($count - 1), 2)
                : $perMonth;
        }

        return $amounts;
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
