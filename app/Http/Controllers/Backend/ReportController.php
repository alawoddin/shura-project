<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function AllReport() {
        
        return view('admin.pages.report.all_report');
    }

    public function SearchReportsByDate(Request $request) {

        return view('admin.pages.report.search_by_date');
    }

    public function AllReportsByMonth(Request $request) {
        return view('admin.pages.report.search_by_month');

    }

    public function AllReportsByYear(Request $request) {
        return view('admin.pages.report.search_by_year');

    }


}
