<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\FinancialService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

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

    public function AllReportsByYear(Request $request)
    {
        $request->validate(['year' => 'required|integer|min:2000']);

        $year = (int) $request->year;
        $summary = $this->financialService->getPeriodFinancialSummary(year: $year);

        return view('admin.pages.report.search_by_year', array_merge(
            ['year' => $year],
            $summary
        ));
    }
}
