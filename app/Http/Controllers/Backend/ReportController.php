<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\Aids;
use App\Models\Credit;
use App\Models\CreditPayment;
use App\Models\Expense;
use App\Models\Income;
use App\Models\ReceivePayment;
use App\Models\User;
use App\Services\FinancialService;
use App\Services\LoanEnforcementService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected FinancialService $financialService,
        protected LoanEnforcementService $loanEnforcement
    ) {}

    /** گزارشات عمومی */
    public function AllReport()
    {
        return view('admin.pages.report.all_report');
    }

    public function SearchReportsByDate(Request $request)
    {
        $request->validate(['date' => 'required|date']);
        $date = $request->date;

        $summary = $this->financialService->getPeriodFinancialSummary(date: $date);

        return view('admin.pages.report.search_by_date', array_merge(
            ['date' => $date],
            $summary
        ));
    }

    public function AllReportsByMonth(Request $request)
    {
        $request->validate([
            'month' => 'required|numeric|between:1,12',
        ]);

        $month = (int) $request->month;
        $summary = $this->financialService->getPeriodFinancialSummary(month: $month);

        return view('admin.pages.report.search_by_month', array_merge(
            ['month' => $month],
            $summary
        ));
    }

    /**
     * گزارشات سالانه — month by month money in / money out for one year.
     */
    public function AnnualReport(Request $request)
    {
        $currentYear = (int) now()->year;
        $year = (int) $request->input('year', $currentYear);

        if ($year < 2000 || $year > $currentYear + 1) {
            $year = $currentYear;
        }

        $fees = $this->monthlySums(
            ReceivePayment::whereNull('credit_id')->whereYear('date', $year),
            'date',
            'CASE WHEN transaction_type = "debit" THEN -amount ELSE amount END'
        );
        $incomes = $this->monthlySums(
            Income::whereYear('date', $year)
                ->whereHas('undeposited', fn ($q) => $q->where('status', 'transferred')),
            'date'
        );
        $deposits = $this->monthlySums(
            AccountTransaction::where('type', 'deposit')->whereYear('date', $year),
            'date'
        );
        $loanRepayments = $this->monthlySums(CreditPayment::whereYear('paid_at', $year), 'paid_at');
        $expenses = $this->monthlySums(Expense::whereYear('date', $year), 'date');
        $aids = $this->monthlySums(Aids::whereYear('date', $year), 'date');
        $loans = $this->monthlySums(Credit::whereYear('date', $year), 'date');

        $months = [];
        $totals = array_fill_keys([
            'fees', 'incomes', 'deposits', 'loan_repayments', 'money_in',
            'expenses', 'aids', 'loans', 'money_out', 'net',
        ], 0.0);

        for ($m = 1; $m <= 12; $m++) {
            $row = [
                'month' => $m,
                'label' => sprintf('%04d-%02d', $year, $m),
                'fees' => (float) ($fees[$m] ?? 0),
                'incomes' => (float) ($incomes[$m] ?? 0),
                'deposits' => (float) ($deposits[$m] ?? 0),
                'loan_repayments' => (float) ($loanRepayments[$m] ?? 0),
                'expenses' => (float) ($expenses[$m] ?? 0),
                'aids' => (float) ($aids[$m] ?? 0),
                'loans' => (float) ($loans[$m] ?? 0),
            ];
            $row['money_in'] = $row['fees'] + $row['incomes'] + $row['deposits'] + $row['loan_repayments'];
            $row['money_out'] = $row['expenses'] + $row['aids'] + $row['loans'];
            $row['net'] = $row['money_in'] - $row['money_out'];

            foreach ($totals as $key => $value) {
                $totals[$key] = $value + $row[$key];
            }

            $months[] = $row;
        }

        $firstYear = (int) collect([
            ReceivePayment::min('date'),
            Income::min('date'),
            Expense::min('date'),
            Credit::min('date'),
            Aids::min('date'),
        ])->filter()->map(fn ($d) => (int) substr((string) $d, 0, 4))->min() ?: $currentYear;

        $years = range($currentYear, min($firstYear, $currentYear));

        return view('admin.pages.report.annual_report', compact('year', 'years', 'months', 'totals'));
    }

    /**
     * گزارش کاربران — choose one member and a period (all / one day / one month / one year)
     * and see that member's loans, loan payments, fees and aid for that period,
     * with a day-by-day or month-by-month breakdown.
     */
    public function UsersReport(Request $request)
    {
        $members = User::where('role', 'user')->orderBy('name')->get(['id', 'name', 'father_name', 'status']);
        $selectedId = $request->integer('user_id') ?: null;

        $period = in_array($request->input('period'), ['all', 'day', 'month', 'year'], true)
            ? $request->input('period')
            : 'all';
        $dayInput = (string) $request->input('day', now()->toDateString());
        $monthInput = (string) $request->input('month', now()->format('Y-m'));
        $yearInput = (int) $request->input('year', now()->year);

        if (! $selectedId) {
            return view('admin.pages.report.users_report', [
                'members' => $members,
                'member' => null,
                'period' => $period,
                'dayInput' => $dayInput,
                'monthInput' => $monthInput,
                'yearInput' => $yearInput,
            ]);
        }

        $member = User::where('role', 'user')->findOrFail($selectedId);

        // ── The chosen period ────────────────────────────────────────────────
        [$from, $to, $periodLabel] = $this->userReportPeriod($period, $dayInput, $monthInput, $yearInput);
        $inPeriod = function ($query, string $column) use ($from, $to) {
            if ($from && $to) {
                $query->whereBetween($column, [$from->toDateString(), $to->toDateString()]);
            }

            return $query;
        };
        $inPeriodDateTime = function ($query, string $column) use ($from, $to) {
            if ($from && $to) {
                $query->whereBetween($column, [$from->copy()->startOfDay(), $to->copy()->endOfDay()]);
            }

            return $query;
        };

        // Fees paid in the period
        $feePayments = $inPeriod(ReceivePayment::with('category')
            ->where('user_id', $member->id)
            ->whereNull('credit_id'), 'date')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();

        // Loans taken in the period (with all their payments)
        $loans = $inPeriod(Credit::with(['sourceAccount', 'payments' => fn ($q) => $q->orderByDesc('paid_at')])
            ->where('user_id', $member->id), 'date')
            ->orderByDesc('date')
            ->get()
            ->map(function (Credit $credit) {
                $credit->paid_amount = (float) $credit->amount - (float) $credit->remaining_amount;
                $credit->months_without_payment = $credit->status === 'active'
                    ? $this->loanEnforcement->getMonthsWithoutPayment($credit)
                    : 0;
                $credit->is_overdue = $this->loanEnforcement->isCreditOverdue($credit);

                return $credit;
            });

        // Loan payments made in the period (for any of the member's loans)
        $loanPayments = $inPeriodDateTime(CreditPayment::with('credit')
            ->where('user_id', $member->id), 'paid_at')
            ->orderByDesc('paid_at')
            ->get();

        // Aid received in the period
        $aids = $inPeriod(Aids::with('sourceAccount')
            ->where('user_id', $member->id), 'date')
            ->orderByDesc('date')
            ->get();

        // ── Totals for the period ────────────────────────────────────────────
        $feesPaid = (float) $feePayments->where('transaction_type', 'credit')->sum('amount')
            - (float) $feePayments->where('transaction_type', 'debit')->sum('amount');

        $summary = [
            'monthly_fee' => (float) $member->monthly_fee,
            'loans_taken' => (float) $loans->sum('amount'),
            'loans_count' => $loans->count(),
            'loan_payments' => (float) $loanPayments->sum('amount'),
            'loan_payments_count' => $loanPayments->count(),
            'fees_paid' => $feesPaid,
            'fee_records' => $feePayments->count(),
            'aid_total' => (float) $aids->sum('amount'),
            'aid_count' => $aids->count(),
        ];

        // ── Current status (today, not tied to the period) ───────────────────
        $feeStatus = $this->financialService->getMemberFeeRemaining($member);
        $current = [
            'loans_remaining' => (float) Credit::where('user_id', $member->id)->where('status', 'active')->sum('remaining_amount'),
            'active_loans' => Credit::where('user_id', $member->id)->where('status', 'active')->where('remaining_amount', '>', 0)->count(),
            'unpaid_months' => (int) $feeStatus['unpaid_months_count'],
            'fee_debt' => (float) $feeStatus['remaining_amount'],
        ];

        // ── Breakdown: day by day for a month, month by month for a year / all ──
        $breakdown = $this->userReportBreakdown($period, $from, $to, $loans, $loanPayments, $feePayments, $aids);

        $blockReason = $this->loanEnforcement->aidBlockReason($member);

        return view('admin.pages.report.users_report', compact(
            'members',
            'member',
            'period',
            'dayInput',
            'monthInput',
            'yearInput',
            'periodLabel',
            'feeStatus',
            'feePayments',
            'loans',
            'loanPayments',
            'aids',
            'summary',
            'current',
            'breakdown',
            'blockReason'
        ));
    }

    /**
     * @return array{0: ?\Carbon\Carbon, 1: ?\Carbon\Carbon, 2: string}
     */
    protected function userReportPeriod(string $period, string $day, string $month, int $year): array
    {
        try {
            return match ($period) {
                'day' => [
                    \Carbon\Carbon::parse($day)->startOfDay(),
                    \Carbon\Carbon::parse($day)->endOfDay(),
                    'روز ' . \Carbon\Carbon::parse($day)->format('Y-m-d'),
                ],
                'month' => [
                    \Carbon\Carbon::createFromFormat('Y-m-d', $month . '-01')->startOfMonth(),
                    \Carbon\Carbon::createFromFormat('Y-m-d', $month . '-01')->endOfMonth(),
                    'ماه ' . $month,
                ],
                'year' => [
                    \Carbon\Carbon::create($year, 1, 1)->startOfYear(),
                    \Carbon\Carbon::create($year, 1, 1)->endOfYear(),
                    'سال ' . $year,
                ],
                default => [null, null, 'همه زمان‌ها'],
            };
        } catch (\Throwable) {
            return [null, null, 'همه زمان‌ها'];
        }
    }

    /**
     * Rows of [label, loans taken, loan payments, fees paid, aid] grouped by day (month period)
     * or by month (year / all periods). Empty for a single day.
     */
    protected function userReportBreakdown(string $period, $from, $to, $loans, $loanPayments, $feePayments, $aids): array
    {
        if ($period === 'day') {
            return [];
        }

        $format = $period === 'month' ? 'Y-m-d' : 'Y-m';
        $key = fn ($date) => \Carbon\Carbon::parse($date)->format($format);

        $rows = [];
        $add = function (string $bucket, string $field, float $amount) use (&$rows) {
            $rows[$bucket] ??= ['label' => $bucket, 'loans' => 0.0, 'loan_payments' => 0.0, 'fees' => 0.0, 'aid' => 0.0];
            $rows[$bucket][$field] += $amount;
        };

        foreach ($loans as $loan) {
            $add($key($loan->date), 'loans', (float) $loan->amount);
        }
        foreach ($loanPayments as $payment) {
            $add($key($payment->paid_at), 'loan_payments', (float) $payment->amount);
        }
        foreach ($feePayments as $payment) {
            $sign = $payment->transaction_type === 'debit' ? -1 : 1;
            $add($key($payment->date), 'fees', $sign * (float) $payment->amount);
        }
        foreach ($aids as $aid) {
            $add($key($aid->date), 'aid', (float) $aid->amount);
        }

        krsort($rows);

        return array_values($rows);
    }

    /**
     * Sum an amount per month (1-12) for the given query.
     */
    protected function monthlySums($query, string $dateColumn, string $amountExpression = 'amount')
    {
        return $query
            ->selectRaw("MONTH({$dateColumn}) as m, SUM({$amountExpression}) as total")
            ->groupBy('m')
            ->pluck('total', 'm');
    }
}
