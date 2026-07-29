<?php

namespace App\Services;

use App\Exceptions\InsufficientBalanceException;
use App\Models\Aids;
use App\Models\Category;
use App\Models\Credit;
use App\Models\Expense;
use App\Models\Income;
use App\Models\MemberFinancialReport;
use App\Models\ReceivePayment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    public function getDashboardStats(): array
    {
        $totalIncome = $this->getTransferredIncomeTotal();
        $monthIncome = $this->getTransferredIncomeTotal(now()->month, now()->year);

        $totalReceiveCredit = (float) ReceivePayment::where('transaction_type', 'credit')->sum('amount');
        $totalReceiveDebit = (float) ReceivePayment::where('transaction_type', 'debit')->sum('amount');
        $totalReceive = $totalReceiveCredit - $totalReceiveDebit;

        $monthReceiveCredit = (float) ReceivePayment::where('transaction_type', 'credit')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');
        $monthReceiveDebit = (float) ReceivePayment::where('transaction_type', 'debit')
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');
        $monthReceive = $monthReceiveCredit - $monthReceiveDebit;

        $totalExpense = (float) Expense::sum('amount');
        $monthExpense = (float) Expense::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->sum('amount');

        $totalAids = (float) Aids::sum('amount');
        $totalLoansDisbursed = (float) Credit::sum('amount');
        $totalLoansRemaining = (float) Credit::where('status', 'active')->sum('remaining_amount');

        $totalBalance = $totalIncome + $totalReceive;
        $currentBalance = $totalBalance - $totalExpense - $totalAids;
        $monthCurrentBalance = ($monthIncome + $monthReceive) - $monthExpense;

        $totalCreditPool = $totalBalance - $totalExpense - $totalAids;
        $totalCredit = max(0, $totalCreditPool - $totalLoansDisbursed);

        $incomes = Income::with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        return [
            'incomes' => $incomes,
            'totalIncome' => $totalIncome,
            'monthIncome' => $monthIncome,
            'totalExpense' => $totalExpense,
            'monthExpense' => $monthExpense,
            'totalBalance' => $totalBalance,
            'currentBalance' => $currentBalance,
            'monthCurrentBalance' => $monthCurrentBalance,
            'totalReceive' => $totalReceive,
            'monthReceive' => $monthReceive,
            'totalCredit' => $totalCredit,
            'totalLoansDisbursed' => $totalLoansDisbursed,
            'totalLoansRemaining' => $totalLoansRemaining,
            'totalUser' => User::count(),
            'monthUser' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'totalcredit' => $totalCredit,
            'remaining_amount' => $totalLoansRemaining,
        ];
    }

    public function getTransferredIncomeTotal(?int $month = null, ?int $year = null): float
    {
        $query = Income::whereHas('undeposited', fn ($q) => $q->where('status', 'transferred'));

        if ($month && $year) {
            $query->whereMonth('date', $month)->whereYear('date', $year);
        }

        return (float) $query->sum('amount');
    }

    public function getAccountBalance(int $accountId): float
    {
        return (float) Category::whereKey($accountId)->value('balance');
    }

    public function increaseAccountBalance(int $accountId, float $amount): void
    {
        if ($amount <= 0) {
            return;
        }

        Category::whereKey($accountId)->increment('balance', $amount);
    }

    public function decreaseAccountBalance(int $accountId, float $amount, bool $allowNegative = false): void
    {
        if ($amount <= 0) {
            return;
        }

        DB::transaction(function () use ($accountId, $amount, $allowNegative) {
            $account = Category::lockForUpdate()->findOrFail($accountId);

            if (! $allowNegative && (float) $account->balance < $amount) {
                throw new InsufficientBalanceException(
                    "Insufficient balance in {$account->name}. Available: {$account->balance}, Required: {$amount}"
                );
            }

            $account->decrement('balance', $amount);
        });
    }

    public function adjustAccountBalance(int $accountId, float $oldAmount, float $newAmount): void
    {
        $difference = $newAmount - $oldAmount;

        if ($difference > 0) {
            $this->decreaseAccountBalance($accountId, $difference);
        } elseif ($difference < 0) {
            $this->increaseAccountBalance($accountId, abs($difference));
        }
    }

    public function getDefaultCashAccountId(): ?int
    {
        return Category::cashAccounts()->orderBy('id')->value('id');
    }

    public function resolveReceivePaymentAccountId(?int $categoryId): ?int
    {
        if (! $categoryId) {
            return $this->getDefaultCashAccountId();
        }

        $category = Category::find($categoryId);

        if (! $category) {
            return $this->getDefaultCashAccountId();
        }

        $mapped = Category::cashAccounts()
            ->where('name', 'like', '%' . explode(' ', $category->name)[0] . '%')
            ->value('id');

        return $mapped ?? $this->getDefaultCashAccountId();
    }

    public function applyReceivePaymentBalance(ReceivePayment $payment, ?string $previousType = null, ?float $previousAmount = null): void
    {
        if ($previousType !== null && $previousAmount !== null) {
            $this->reverseReceivePaymentBalance($previousType, $previousAmount, $payment->category_id);
        }

        $accountId = $this->resolveReceivePaymentAccountId($payment->category_id);

        if (! $accountId) {
            return;
        }

        if ($payment->transaction_type === 'debit') {
            $this->decreaseAccountBalance($accountId, (float) $payment->amount);
        } else {
            $this->increaseAccountBalance($accountId, (float) $payment->amount);
        }
    }

    public function reverseReceivePaymentBalance(string $transactionType, float $amount, ?int $categoryId): void
    {
        $accountId = $this->resolveReceivePaymentAccountId($categoryId);

        if (! $accountId) {
            return;
        }

        if ($transactionType === 'debit') {
            $this->increaseAccountBalance($accountId, $amount);
        } else {
            $this->decreaseAccountBalance($accountId, $amount, true);
        }
    }

    public function syncCreditFromFinancialReport(MemberFinancialReport $report, ?float $previousCredit = null): void
    {
        $creditAmount = (float) ($report->credit ?? 0);

        DB::transaction(function () use ($report, $creditAmount, $previousCredit) {
            if ($report->credit_id) {
                $credit = Credit::lockForUpdate()->find($report->credit_id);

                if ($credit) {
                    if ($creditAmount <= 0) {
                        $this->reverseLoanDisbursement($credit);
                        $credit->delete();
                        $report->update(['credit_id' => null]);

                        return;
                    }

                    $oldAmount = (float) $credit->amount;
                    $paidPortion = $oldAmount - (float) $credit->remaining_amount;

                    $credit->update([
                        'user_id' => $report->member_id,
                        'date' => $report->date,
                        'description' => $report->description,
                        'amount' => $creditAmount,
                        'remaining_amount' => max(0, $creditAmount - $paidPortion),
                        'status' => ($creditAmount - $paidPortion) <= 0 ? 'paid' : 'active',
                    ]);

                    if ($credit->source_account_id) {
                        $this->adjustAccountBalance(
                            $credit->source_account_id,
                            $oldAmount,
                            $creditAmount
                        );
                    }

                    return;
                }
            }

            if ($creditAmount <= 0) {
                return;
            }

            $sourceAccountId = $this->getDefaultCashAccountId();
            $loanCategory = Category::where('account_type', 'receivable')->first()
                ?? Category::first();

            if ($sourceAccountId) {
                $this->decreaseAccountBalance($sourceAccountId, $creditAmount);
            }

            $credit = Credit::create([
                'user_id' => $report->member_id,
                'category_id' => $loanCategory?->id,
                'source_account_id' => $sourceAccountId,
                'date' => $report->date,
                'amount' => $creditAmount,
                'remaining_amount' => $creditAmount,
                'description' => $report->description,
                'status' => 'active',
            ]);

            $report->update(['credit_id' => $credit->id]);
        });
    }

    public function deleteLinkedCredit(MemberFinancialReport $report): void
    {
        if (! $report->credit_id) {
            return;
        }

        DB::transaction(function () use ($report) {
            $credit = Credit::find($report->credit_id);

            if ($credit) {
                $this->reverseLoanDisbursement($credit);
                $credit->delete();
            }
        });
    }

    public function disburseLoan(Credit $credit): void
    {
        if ($credit->source_account_id) {
            $this->decreaseAccountBalance($credit->source_account_id, (float) $credit->amount);
        }
    }

    public function reverseLoanDisbursement(Credit $credit): void
    {
        if ($credit->source_account_id) {
            $this->increaseAccountBalance($credit->source_account_id, (float) $credit->amount);
        }
    }

    public function getUnpaidMembers(): array
    {
        $members = User::where('role', 'user')
            ->where('monthly_fee', '>', 0)
            ->get();

        $unpaid = [];

        foreach ($members as $member) {
            $monthsToCheck = ReceivePayment::where('user_id', $member->id)
                ->whereNotNull('month_of')
                ->pluck('month_of')
                ->unique()
                ->push(now()->format('Y-m'))
                ->unique();

            foreach ($monthsToCheck as $month) {
                $paid = (float) ReceivePayment::where('user_id', $member->id)
                    ->where('month_of', $month)
                    ->where('transaction_type', 'credit')
                    ->sum('amount');

                $expected = (float) $member->monthly_fee;

                if ($paid < $expected) {
                    $unpaid[] = [
                        'user' => $member,
                        'month' => $month,
                        'expected' => $expected,
                        'paid' => $paid,
                        'remaining' => $expected - $paid,
                    ];
                }
            }
        }

        return $unpaid;
    }

    public function getPendingPaymentNotifications()
    {
        return ReceivePayment::with(['users', 'category'])
            ->where('review_status', 'pending_review')
            ->latest()
            ->get();
    }

    public function markPaymentAsReviewed(int $paymentId): void
    {
        ReceivePayment::whereKey($paymentId)->update(['review_status' => 'reviewed']);
    }

    public function markAllPaymentsAsReviewed(): void
    {
        ReceivePayment::where('review_status', 'pending_review')->update(['review_status' => 'reviewed']);
    }
}
