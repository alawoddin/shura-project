<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use App\Models\ReceivePayment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function AllReport() {
        
        return view('admin.pages.report.all_report');
    }

public function SearchReportsByDate(Request $request)
{
    $date = $request->date;

    $expenses = Expense::whereDate('date', $date)->get();

    $receives = ReceivePayment::with(['users', 'category'])
    ->whereDate('date', $date)
    ->get();

    $incomes = Income::whereDate('date', $date)->get();

    $totalExpense = $expenses->sum('amount');

    $totalReceive = $receives->sum('amount');

    $totalIncome = $incomes->sum('amount');

    $balance = ($totalReceive + $totalIncome) - $totalExpense;

    return view(
        'admin.pages.report.search_by_date',
        compact(
            'date',
            'expenses',
            'receives',
            'incomes',
            'totalExpense',
            'totalReceive',
            'totalIncome',
            'balance'
        )
    );
}

   public function AllReportsByMonth(Request $request)
{
    $request->validate([
        'month' => 'required|numeric|between:1,12'
    ]);

    $month = $request->month;

    $expenses = Expense::whereMonth('date', $month)->get();

    $receives = ReceivePayment::with(['users', 'category'])
        ->whereMonth('date', $month)
        ->get();

    $incomes = Income::whereMonth('date', $month)->get();

    $totalExpense = $expenses->sum('amount');
    $totalReceive = $receives->sum('amount');
    $totalIncome = $incomes->sum('amount');

    $balance = ($totalReceive + $totalIncome) - $totalExpense;

    return view(
        'admin.pages.report.search_by_month',
        compact(
            'month',
            'expenses',
            'receives',
            'incomes',
            'totalExpense',
            'totalReceive',
            'totalIncome',
            'balance'
        )
    );
}

    public function AllReportsByYear(Request $request)
{
    $year = $request->year;

    $expenses = Expense::whereYear('date', $year)->get();

    $receives = ReceivePayment::with(['users','category'])
        ->whereYear('date', $year)
        ->get();

    $incomes = Income::whereYear('date', $year)->get();

    $totalExpense = $expenses->sum('amount');

    $totalReceive = $receives->sum('amount');

    $totalIncome = $incomes->sum('amount');

    $balance = ($totalReceive + $totalIncome) - $totalExpense;

    return view(
        'admin.pages.report.search_by_year',
        compact(
            'year',
            'expenses',
            'receives',
            'incomes',
            'totalExpense',
            'totalReceive',
            'totalIncome',
            'balance'
        )
    );
}


}
