<?php

namespace App\Services;

use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\User;
use Carbon\Carbon;

class LoanEnforcementService
{
    public const OVERDUE_MONTHS = 6;

    public function recordCreditPayment(Credit $credit, float $amount, ?Carbon $paidAt = null, ?string $note = null): void
    {
        $paidAt = $paidAt ?? now();

        CreditPayment::create([
            'credit_id' => $credit->id,
            'user_id' => $credit->user_id,
            'amount' => $amount,
            'paid_at' => $paidAt,
            'note' => $note,
        ]);

        $credit->update(['last_payment_at' => $paidAt]);

        $this->syncUserLoanStatus((int) $credit->user_id);
    }

    public function getReferencePaymentDate(Credit $credit): Carbon
    {
        if ($credit->last_payment_at) {
            return Carbon::parse($credit->last_payment_at);
        }

        return Carbon::parse($credit->date);
    }

    public function getMonthsWithoutPayment(Credit $credit): int
    {
        return (int) $this->getReferencePaymentDate($credit)->diffInMonths(now());
    }

    public function isCreditOverdue(Credit $credit): bool
    {
        if ($credit->status !== 'active' || (float) $credit->remaining_amount <= 0) {
            return false;
        }

        return $this->getMonthsWithoutPayment($credit) >= self::OVERDUE_MONTHS;
    }

    public function userHasOverdueLoan(User $user): bool
    {
        return $user->credits()
            ->where('status', 'active')
            ->where('remaining_amount', '>', 0)
            ->get()
            ->contains(fn (Credit $credit) => $this->isCreditOverdue($credit));
    }

    public function userHasActiveLoan(User $user): bool
    {
        return $user->credits()
            ->where('status', 'active')
            ->where('remaining_amount', '>', 0)
            ->exists();
    }

    public function __construct(protected FinancialService $financialService) {}

    /**
     * Number of unpaid monthly-fee months (system debt) for the member.
     */
    public function unpaidFeeMonths(User $user): int
    {
        return (int) $this->financialService->getMemberFeeRemaining($user)['unpaid_months_count'];
    }

    public function userHasOverdueFeeDebt(User $user): bool
    {
        return $this->unpaidFeeMonths($user) >= self::OVERDUE_MONTHS;
    }

    /**
     * A member who has been in debt for 6 months or more (unpaid loan or unpaid
     * monthly fees) is blocked: no aid and no new loan.
     */
    public function isBlockedForDebt(User $user): bool
    {
        return $user->status === 'suspended'
            || $this->userHasOverdueLoan($user)
            || $this->userHasOverdueFeeDebt($user);
    }

    public function canReceiveAid(User $user): bool
    {
        return ! $this->isBlockedForDebt($user);
    }

    public function canReceiveLoan(User $user): bool
    {
        return ! $this->isBlockedForDebt($user);
    }

    public function aidBlockReason(User $user): ?string
    {
        if ($user->status === 'suspended') {
            return 'حساب این کاربر به دلیل عدم پرداخت قرض بیش از ۶ ماه تعلیق شده است.';
        }

        if ($this->userHasOverdueLoan($user)) {
            return 'این کاربر بیش از ۶ ماه قرض خود را پرداخت نکرده و واجد دریافت کمک یا قرض نیست.';
        }

        if ($this->userHasOverdueFeeDebt($user)) {
            return 'این کاربر ' . $this->unpaidFeeMonths($user) . ' ماه فیس ماهانه بدهکار است (بیش از ۶ ماه) و واجد دریافت کمک یا قرض نیست.';
        }

        return null;
    }

    /**
     * Split members into eligible and debt-blocked lists (for form dropdowns).
     *
     * @return array{0: \Illuminate\Support\Collection, 1: \Illuminate\Support\Collection}
     */
    public function partitionByDebt($users): array
    {
        $blocked = collect();
        $eligible = collect();

        foreach ($users as $user) {
            if ($this->isBlockedForDebt($user)) {
                $user->debt_block_reason = $this->aidBlockReason($user);
                $blocked->push($user);
            } else {
                $eligible->push($user);
            }
        }

        return [$eligible, $blocked];
    }

    public function syncUserLoanStatus(int $userId): void
    {
        $user = User::find($userId);

        if (! $user || $user->role !== 'user') {
            return;
        }

        if (in_array($user->status, ['inactive', 'dead', 'pending'], true)) {
            return;
        }

        $shouldSuspend = $this->userHasOverdueLoan($user);

        if ($shouldSuspend && $user->status !== 'suspended') {
            $user->update(['status' => 'suspended']);

            return;
        }

        if (! $shouldSuspend && $user->status === 'suspended') {
            $user->update(['status' => 'active']);
        }
    }

    public function syncAllLoanStatuses(): void
    {
        $userIds = Credit::query()
            ->where('status', 'active')
            ->where('remaining_amount', '>', 0)
            ->distinct()
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $this->syncUserLoanStatus((int) $userId);
        }

        User::where('status', 'suspended')
            ->where('role', 'user')
            ->whereDoesntHave('credits', function ($query) {
                $query->where('status', 'active')->where('remaining_amount', '>', 0);
            })
            ->update(['status' => 'active']);
    }

    public function getSuspendedLoanUsers()
    {
        return User::with(['credits' => fn ($q) => $q->where('status', 'active')->where('remaining_amount', '>', 0)])
            ->where('role', 'user')
            ->where('status', 'suspended')
            ->get();
    }
}
