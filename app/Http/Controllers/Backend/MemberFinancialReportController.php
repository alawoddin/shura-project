<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\MemberFinancialReport;
use App\Models\User;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MemberFinancialReportController extends Controller
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

    public function AllMemberFinancialReport()
    {
        $alldata = MemberFinancialReport::with('member', 'linkedCredit')->latest()->get();

        return view(
            'admin.pages.member_financial_report.all_report',
            compact('alldata')
        );
    }

    public function AddFinancialReport()
    {
        $users = User::all();

        return view(
            'admin.pages.member_financial_report.add_report',
            compact('users')
        );
    }

    public function StoreFinancialReport(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'debit' => 'nullable|numeric|min:0',
            'credit' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $lastBalance = MemberFinancialReport::where('member_id', $request->member_id)
                    ->latest()
                    ->first();

                $previousBalance = $lastBalance ? $lastBalance->balance : 0;
                $newBalance = $previousBalance + ($request->debit ?? 0) - ($request->credit ?? 0);

                $report = MemberFinancialReport::create([
                    'member_id' => $request->member_id,
                    'date' => $request->date,
                    'description' => $request->description,
                    'debit' => $request->debit ?? 0,
                    'credit' => $request->credit ?? 0,
                    'balance' => $newBalance,
                ]);

                if ((float) ($request->credit ?? 0) > 0) {
                    $this->financialService->syncCreditFromFinancialReport($report);
                }
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $notification = [
            'message' => 'گزارش مالی با موفقیت ثبت شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.member.financial.report')->with($notification);
    }

    public function EditFinancialReport($id)
    {
        $editdata = MemberFinancialReport::findOrFail($id);
        $users = User::all();

        return view(
            'admin.pages.member_financial_report.edit_report',
            compact('editdata', 'users')
        );
    }

    public function UpdateFinancialReport(Request $request)
    {
        $report_id = $request->id;

        $request->validate([
            'member_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'debit' => 'nullable|numeric|min:0',
            'credit' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $report_id) {
                $report = MemberFinancialReport::findOrFail($report_id);
                $previousCredit = (float) $report->credit;

                $lastBalance = MemberFinancialReport::where('member_id', $request->member_id)
                    ->where('id', '<', $report_id)
                    ->latest()
                    ->first();

                $previousBalance = $lastBalance ? $lastBalance->balance : 0;
                $newBalance = $previousBalance + ($request->debit ?? 0) - ($request->credit ?? 0);

                $report->update([
                    'member_id' => $request->member_id,
                    'date' => $request->date,
                    'description' => $request->description,
                    'debit' => $request->debit ?? 0,
                    'credit' => $request->credit ?? 0,
                    'balance' => $newBalance,
                ]);

                $this->financialService->syncCreditFromFinancialReport($report->fresh(), $previousCredit);
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $notification = [
            'message' => 'گزارش مالی با موفقیت به روز شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.member.financial.report')->with($notification);
    }

    public function DeleteFinancialReport($id)
    {
        DB::transaction(function () use ($id) {
            $report = MemberFinancialReport::findOrFail($id);
            $this->financialService->deleteLinkedCredit($report);
            $report->delete();
        });

        $notification = [
            'message' => 'گزارش مالی با موفقیت حذف شد',
            'alert-type' => 'success',
        ];

        return redirect()->back()->with($notification);
    }
}
