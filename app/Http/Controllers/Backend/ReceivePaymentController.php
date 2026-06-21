<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use App\Models\ReceivePayment;
use Illuminate\Http\Request;

class ReceivePaymentController extends Controller
{
    public function AllReceivePayment()
    {
        $alldata = ReceivePayment::with('users', 'category')->latest()->get();

        return view('admin.pages.receive.all_receive_payment', compact('alldata'));
    }

    public function AddReceivePayment()
    {
        $users = User::where('role', 'user')->get();
        $categories = Category::paymentTypes()->get();

        if ($categories->isEmpty()) {
            $categories = Category::all();
        }

        return view('admin.pages.receive.add_receive_payment', compact('users', 'categories'));
    }

    public function StoreReceivePayment(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'month_of' => 'nullable|string',
        ]);

        $category = Category::findOrFail($validated['category_id']);

        if ($category->is_monthly_fee) {
            $request->validate(['month_of' => 'required|string']);
            $validated['month_of'] = $request->month_of;
        } else {
            $validated['month_of'] = null;
        }

        ReceivePayment::create($validated);

        $notification = [
            'message' => 'دریافت پرداخت با موفقیت اضافه شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.receive.payment')->with($notification);
    }

    public function EditReceivePayment($id)
    {
        $editData = ReceivePayment::with(['users', 'category'])->findOrFail($id);
        $users = User::where('role', 'user')->get();
        $categories = Category::paymentTypes()->get();

        if ($categories->isEmpty()) {
            $categories = Category::all();
        }

        return view(
            'admin.pages.receive.edit_receive_payment',
            compact('editData', 'users', 'categories')
        );
    }

    public function UpdateReceivePayment(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|exists:receive_payments,id',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
            'month_of' => 'nullable|string',
        ]);

        $category = Category::findOrFail($validated['category_id']);
        $data = ReceivePayment::findOrFail($validated['id']);

        if ($category->is_monthly_fee) {
            $request->validate(['month_of' => 'required|string']);
            $validated['month_of'] = $request->month_of;
        } else {
            $validated['month_of'] = null;
        }

        unset($validated['id']);
        $data->update($validated);

        $notification = [
            'message' => 'دریافت پرداخت با موفقیت به روز شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.receive.payment')->with($notification);
    }

    public function DeleteReceivePayment($id)
    {
        ReceivePayment::findOrFail($id)->delete();

        $notification = [
            'message' => 'دریافت پرداخت با موفقیت حذف شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.receive.payment')->with($notification);
    }
}
