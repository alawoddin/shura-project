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

    public function canReceiveAid(User $user): bool
    {
        if ($user->status === 'suspended') {
            return false;
        }

        if ($this->userHasOverdueLoan($user)) {
            return false;
        }

        return true;
    }

    public function aidBlockReason(User $user): ?string
    {
        if ($user->status === 'suspended') {
            return 'حساب این کاربر به دلیل عدم پرداخت قرض بیش از ۶ ماه تعلیق شده است.';
        }

        if ($this->userHasOverdueLoan($user)) {
            return 'این کاربر بیش از ۶ ماه قرض خود را پرداخت نکرده و واجد دریافت کمک نیست.';
        }

        return null;
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
