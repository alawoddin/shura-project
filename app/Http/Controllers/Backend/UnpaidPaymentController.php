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

        return view('admin.pages.unpaid.all_unpaid_payments', compact('unpaidMembers', 'pendingNotifications'));
    }

    public function markReviewed(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|exists:receive_payments,id',
        ]);

        $this->financialService->markPaymentAsReviewed($request->payment_id);

        $notification = [
            'message' => 'اعلان پرداخت به عنوان بررسی شده علامت گذاری شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('unpaid.payments')->with($notification);
    }

    public function markAllReviewed()
    {
        $this->financialService->markAllPaymentsAsReviewed();

        $notification = [
            'message' => 'تمام اعلان‌های پرداخت بررسی شدند',
            'alert-type' => 'success',
        ];

        return redirect()->route('unpaid.payments')->with($notification);
    }
}
