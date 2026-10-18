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
use App\Models\Undeposited;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    public function getDashboardStats(): array
    {
        $month = (int) now()->month;
        $year = (int) now()->year;

        $income = DB::table('incomes')
            ->leftJoin('undepositeds', 'undepositeds.income_id', '=', 'incomes.id')
            ->selectRaw('
                COALESCE(SUM(incomes.amount), 0) as total_all,
                COALESCE(SUM(CASE WHEN MONTH(incomes.date) = ? AND YEAR(incomes.date) = ? THEN incomes.amount ELSE 0 END), 0) as month_all,
                COALESCE(SUM(CASE WHEN undepositeds.status = ? THEN incomes.amount ELSE 0 END), 0) as total_transferred,
                COALESCE(SUM(CASE WHEN undepositeds.status = ? AND MONTH(incomes.date) = ? AND YEAR(incomes.date) = ? THEN incomes.amount ELSE 0 END), 0) as month_transferred
            ', [$month, $year, 'transferred', 'transferred', $month, $year])
            ->first();

        $receive = ReceivePayment::selectRaw('
                COALESCE(SUM(CASE WHEN transaction_type = "credit" THEN amount ELSE 0 END), 0) as total_credit,
                COALESCE(SUM(CASE WHEN transaction_type = "debit" THEN amount ELSE 0 END), 0) as total_debit,
                COALESCE(SUM(CASE WHEN transaction_type = "credit" AND MONTH(date) = ? AND YEAR(date) = ? THEN amount ELSE 0 END), 0) as month_credit,
                COALESCE(SUM(CASE WHEN transaction_type = "debit" AND MONTH(date) = ? AND YEAR(date) = ? THEN amount ELSE 0 END), 0) as month_debit
            ', [$month, $year, $month, $year])
            ->first();

        $members = MemberFinancialReport::selectRaw('
                COALESCE(SUM(debit), 0) as total_debit,
                COALESCE(SUM(credit), 0) as total_credit,
                COALESCE(SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN debit ELSE 0 END), 0) as month_debit,
                COALESCE(SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN credit ELSE 0 END), 0) as month_credit
            ', [$month, $year, $month, $year])
            ->first();

        $expenses = Expense::selectRaw('
                COALESCE(SUM(amount), 0) as total_amount,
                COALESCE(SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN amount ELSE 0 END), 0) as month_amount
            ', [$month, $year])
            ->first();

        $aids = Aids::selectRaw('
                COALESCE(SUM(amount), 0) as total_amount,
                COALESCE(SUM(CASE WHEN MONTH(date) = ? AND YEAR(date) = ? THEN amount ELSE 0 END), 0) as month_amount
            ', [$month, $year])
            ->first();

        $loans = Credit::selectRaw('
                COALESCE(SUM(amount), 0) as disbursed,
                COALESCE(SUM(CASE WHEN status = "active" THEN remaining_amount ELSE 0 END), 0) as remaining
            ')
            ->first();

        $users = User::selectRaw('
                COUNT(*) as total_users,
                COALESCE(SUM(CASE WHEN MONTH(created_at) = ? AND YEAR(created_at) = ? THEN 1 ELSE 0 END), 0) as month_users
            ', [$month, $year])
            ->first();

        $totalIncomeAll = (float) $income->total_all;
        $totalIncome = (float) $income->total_transferred;
        $monthIncomeAll = (float) $income->month_all;
        $monthIncome = (float) $income->month_transferred;

        $totalReceiveCredit = (float) $receive->total_credit;
        $totalReceiveDebit = (float) $receive->total_debit;
        $totalReceive = $totalReceiveCredit - $totalReceiveDebit;
        $monthReceiveCredit = (float) $receive->month_credit;
        $monthReceiveDebit = (float) $receive->month_debit;
        $monthReceive = $monthReceiveCredit - $monthReceiveDebit;

        $totalMemberDebit = (float) $members->total_debit;
        $totalMemberCredit = (float) $members->total_credit;
        $monthMemberDebit = (float) $members->month_debit;
        $monthMemberCredit = (float) $members->month_credit;

        $totalDebit = $totalReceiveDebit + $totalMemberDebit;
        $totalCredit = $totalReceiveCredit + $totalMemberCredit;
        $monthDebit = $monthReceiveDebit + $monthMemberDebit;
        $monthCredit = $monthReceiveCredit + $monthMemberCredit;

        $totalExpense = (float) $expenses->total_amount;
        $monthExpense = (float) $expenses->month_amount;

        $totalAids = (float) $aids->total_amount;
        $monthAids = (float) $aids->month_amount;

        $totalLoansDisbursed = (float) $loans->disbursed;
        $totalLoansRemaining = (float) $loans->remaining;

        $totalMoneyIn = $totalIncome + $totalReceive;
        $totalBalance = $totalMoneyIn;
        $currentBalance = $totalBalance - $totalExpense - $totalAids;
        $monthCurrentBalance = ($monthIncome + $monthReceive) - $monthExpense - $monthAids;

        $totalCreditPool = $totalBalance - $totalExpense - $totalAids;
        $totalCreditAvailable = max(0, $totalCreditPool - $totalLoansDisbursed);

        $incomes = Income::with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        $accountSummaries = $this->getAccountBalanceSummaries();

        return [
            'incomes' => $incomes,
            'accountSummaries' => $accountSummaries,
            'totalIncomeAll' => $totalIncomeAll,
            'totalIncome' => $totalIncome,
            'monthIncomeAll' => $monthIncomeAll,
            'monthIncome' => $monthIncome,
            'totalExpense' => $totalExpense,
            'monthExpense' => $monthExpense,
            'totalAids' => $totalAids,
            'monthAids' => $monthAids,
            'totalBalance' => $totalBalance,
            'currentBalance' => $currentBalance,
            'monthCurrentBalance' => $monthCurrentBalance,
            'totalReceive' => $totalReceive,
            'monthReceive' => $monthReceive,
            'totalReceiveCredit' => $totalReceiveCredit,
            'totalReceiveDebit' => $totalReceiveDebit,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'monthDebit' => $monthDebit,
            'monthCredit' => $monthCredit,
            'totalMemberDebit' => $totalMemberDebit,
            'totalMemberCredit' => $totalMemberCredit,
            'totalCreditAvailable' => $totalCreditAvailable,
            'totalLoansDisbursed' => $totalLoansDisbursed,
            'totalLoansRemaining' => $totalLoansRemaining,
            'totalUser' => (int) $users->total_users,
            'monthUser' => (int) $users->month_users,
            'totalcredit' => $totalCreditAvailable,
            'remaining_amount' => $totalLoansRemaining,
        ];
    }

    public function getAccountBalanceSummaries()
    {
        $cashAccounts = Category::cashAccounts()->orderBy('name')->get();
        $incomeAccounts = Category::incomeSources()->orderBy('name')->get();
        $accounts = $cashAccounts->concat($incomeAccounts);

        $expenseTotals = Expense::selectRaw('source_account_id, SUM(amount) as total')
            ->groupBy('source_account_id')
            ->pluck('total', 'source_account_id');

        $aidTotals = Aids::selectRaw('source_account_id, SUM(amount) as total')
            ->groupBy('source_account_id')
            ->pluck('total', 'source_account_id');

        $loanTotals = Credit::selectRaw('source_account_id, SUM(amount) as total')
            ->groupBy('source_account_id')
            ->pluck('total', 'source_account_id');

        $transferTotals = Undeposited::query()
            ->where('status', 'transferred')
            ->whereNotNull('target_account_id')
            ->join('incomes', 'undepositeds.income_id', '=', 'incomes.id')
            ->selectRaw('undepositeds.target_account_id as account_id, SUM(incomes.amount) as total')
            ->groupBy('undepositeds.target_account_id')
            ->pluck('total', 'account_id');

        $incomeByCategory = Income::selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->pluck('total', 'category_id');

        return $accounts->map(function ($account) use ($expenseTotals, $aidTotals, $loanTotals, $transferTotals, $incomeByCategory) {
            $expenseOut = (float) ($expenseTotals[$account->id] ?? 0);
            $aidOut = (float) ($aidTotals[$account->id] ?? 0);
            $loanOut = (float) ($loanTotals[$account->id] ?? 0);
            $transferIn = (float) ($transferTotals[$account->id] ?? 0);
            $incomeIn = (float) ($incomeByCategory[$account->id] ?? 0);
            $moneyIn = $account->account_type === 'cash' ? $transferIn : $incomeIn;
            $moneyOut = $expenseOut + $aidOut + $loanOut;

            return [
                'id' => $account->id,
                'name' => $account->name,
                'account_type' => $account->account_type,
                'money_in' => $moneyIn,
                'expense_out' => $expenseOut,
                'aid_out' => $aidOut,
                'loan_out' => $loanOut,
                'money_out' => $moneyOut,
                'remaining' => (float) $account->balance,
            ];
        });
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
