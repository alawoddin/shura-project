<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MemberFinancialReport;
use Illuminate\Http\Request;

class MemberFinancialReportController extends Controller
{
    public function AllMemberFinancialReport(){
        $alldata = MemberFinancialReport::with('member')->latest()->get();

        return view(
            'admin.pages.member_financial_report.all_report',
            compact('alldata')
        );
    }
}
