<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Services\FinancialService;
use Illuminate\Http\Request;

class UnpaidPaymentController extends Controller
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

    public function index()
    {
        $unpaidMembers = $this->financialService->getUnpaidMembers();
        $pendingNotifications = $this->financialService->getPendingPaymentNotifications();

        return view(
            'admin.pages.unpaid.all_unpaid_payments',
            compact('unpaidMembers', 'pendingNotifications')
        );
    }

    public function markReviewed(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|exists:receive_payments,id',
        ]);

        $this->financialService->markPaymentAsReviewed(
            (int) $validated['payment_id']
        );

        return back()->with([
            'message' => 'اعلان پرداخت به عنوان بررسی‌شده علامت‌گذاری شد',
            'alert-type' => 'success',
        ]);
    }

    public function markAllReviewed()
    {
        $this->financialService->markAllPaymentsAsReviewed();

        return back()->with([
            'message' => 'تمام اعلان‌های پرداخت بررسی شدند',
            'alert-type' => 'success',
        ]);
    }
}