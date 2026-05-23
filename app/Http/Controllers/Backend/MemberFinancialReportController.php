<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\MemberFinancialReport;
use App\Models\User;
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

    public function AddFinancialReport(){
        $users = User::all();

        return view(
            'admin.pages.member_financial_report.add_report',
            compact('users')
        );
    }

    public function StoreFinancialReport(Request $request){
        $request->validate([
            'member_id' => 'required',
            'date' => 'required',
        ]);

        MemberFinancialReport::create([
            'member_id' => $request->member_id,
            'date' => $request->date,
            'description' => $request->description,
            'debit' => $request->debit ?? 0,
            'credit' => $request->credit ?? 0,
            'balance' => $request->balance ?? 0,
        ]);

        return redirect()->route('all.member.financial.report');
    }

    public function EditFinancialReport($id){
        $editdata = MemberFinancialReport::findOrFail($id);

        $users = User::all();

        return view(
            'admin.pages.member_financial_report.edit_report',
            compact('editdata', 'users')
        );
    }

    public function UpdateFinancialReport(Request $request){
        $report_id = $request->id;

        $request->validate([
            'member_id' => 'required',
            'date' => 'required',
        ]);

        MemberFinancialReport::findOrFail($report_id)->update([

            'member_id' => $request->member_id,
            'date' => $request->date,
            'description' => $request->description,
            'debit' => $request->debit ?? 0,
            'credit' => $request->credit ?? 0,
            'balance' => $request->balance ?? 0,

        ]);

        return redirect()->route('all.member.financial.report');
    }
}
