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

    /** The old year search now opens the full annual report page. */
    public function AllReportsByYear(Request $request)
    {
        $request->validate(['year' => 'required|integer|min:2000']);

        return redirect()->route('annual.report', ['year' => (int) $request->year]);
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
     * گزارش کاربران — per-member report, separate from the general reports.
     */
    public function UsersReport(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $members = User::where('role', 'user')
            ->when($search !== '', fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy('name')
            ->get();

        $feesPaid = ReceivePayment::query()
            ->whereNull('credit_id')
            ->where('transaction_type', 'credit')
            ->whereHas('category', fn ($q) => $q->where('is_monthly_fee', true))
            ->selectRaw('user_id, SUM(amount) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $loanTotals = Credit::selectRaw('user_id, SUM(amount) as total, SUM(CASE WHEN status = "active" THEN remaining_amount ELSE 0 END) as remaining')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $aidTotals = Aids::selectRaw('user_id, SUM(amount) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $rows = $members->map(function (User $member) use ($feesPaid, $loanTotals, $aidTotals) {
            $feeStatus = $this->financialService->getMemberFeeRemaining($member);
            $unpaidMonths = (int) $feeStatus['unpaid_months_count'];
            $hasOverdueLoan = $this->loanEnforcement->userHasOverdueLoan($member);

            return [
                'user' => $member,
                'monthly_fee' => (float) $member->monthly_fee,
                'fees_paid' => (float) ($feesPaid[$member->id] ?? 0),
                'unpaid_months' => $unpaidMonths,
                'fee_debt' => (float) $feeStatus['remaining_amount'],
                'loans_total' => (float) ($loanTotals[$member->id]->total ?? 0),
                'loan_remaining' => (float) ($loanTotals[$member->id]->remaining ?? 0),
                'aid_received' => (float) ($aidTotals[$member->id] ?? 0),
                'blocked' => $member->status === 'suspended'
                    || $hasOverdueLoan
                    || $unpaidMonths >= LoanEnforcementService::OVERDUE_MONTHS,
            ];
        });

        $totals = [
            'fees_paid' => $rows->sum('fees_paid'),
            'fee_debt' => $rows->sum('fee_debt'),
            'loan_remaining' => $rows->sum('loan_remaining'),
            'aid_received' => $rows->sum('aid_received'),
            'blocked' => $rows->where('blocked', true)->count(),
        ];

        return view('admin.pages.report.users_report', compact('rows', 'totals', 'search'));
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
