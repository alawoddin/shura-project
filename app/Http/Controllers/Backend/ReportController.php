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
     * گزارش کاربران — choose one member and see that member's full report:
     * fees, loans (with payments), aid received and debt status.
     */
    public function UsersReport(Request $request)
    {
        $members = User::where('role', 'user')->orderBy('name')->get(['id', 'name', 'father_name', 'status']);
        $selectedId = $request->integer('user_id') ?: null;

        if (! $selectedId) {
            return view('admin.pages.report.users_report', ['members' => $members, 'member' => null]);
        }

        $member = User::where('role', 'user')->findOrFail($selectedId);

        // Fees
        $feeStatus = $this->financialService->getMemberFeeRemaining($member);
        $feePayments = ReceivePayment::with('category')
            ->where('user_id', $member->id)
            ->whereNull('credit_id')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->get();
        $feesPaid = (float) $feePayments->where('transaction_type', 'credit')->sum('amount');

        // Loans (کریدیت‌ها)
        $loans = Credit::with(['sourceAccount', 'payments' => fn ($q) => $q->orderByDesc('paid_at')])
            ->where('user_id', $member->id)
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

        // Aid received
        $aids = Aids::with('sourceAccount')
            ->where('user_id', $member->id)
            ->orderByDesc('date')
            ->get();

        $summary = [
            'monthly_fee' => (float) $member->monthly_fee,
            'fees_paid' => $feesPaid,
            'unpaid_months' => (int) $feeStatus['unpaid_months_count'],
            'fee_debt' => (float) $feeStatus['remaining_amount'],
            'loans_total' => (float) $loans->sum('amount'),
            'loans_paid' => (float) $loans->sum('paid_amount'),
            'loans_remaining' => (float) $loans->where('status', 'active')->sum('remaining_amount'),
            'loans_count' => $loans->count(),
            'aid_total' => (float) $aids->sum('amount'),
            'aid_count' => $aids->count(),
        ];

        $blockReason = $this->loanEnforcement->aidBlockReason($member);

        return view('admin.pages.report.users_report', compact(
            'members',
            'member',
            'feeStatus',
            'feePayments',
            'loans',
            'aids',
            'summary',
            'blockReason'
        ));
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
