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

        // $users = User::all();
        // $categories =Category::all();
        // $alldata = ReceivePayment::latest()->get();

        $alldata = ReceivePayment::with('users', 'category')->latest()->get();


        return view('admin.pages.receive.all_receive_payment', compact('alldata'));
    }

    public function AddReceivePayment()
    {
        $users = User::all();
        $categories = Category::all();
        return view('admin.pages.receive.add_receive_payment', compact('users', 'categories'));
    }

    public function StoreReceivePayment(Request $request)
    {

        ReceivePayment::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'date' => $request->date,
            'month_of' => $request->month_of,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        $notification = array(
            'message' => 'دریافت پرداخت با موفقیت اضافه شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.receive.payment')->with($notification);
    }

    public function EditReceivePayment($id)
    {
        $editData = ReceivePayment::with([
            'users',
            'category'
        ])->findOrFail($id);

        $users = User::all();

        $categories = Category::all();

        return view(
            'admin.pages.receive.edit_receive_payment',
            compact(
                'editData',
                'users',
                'categories'
            )
        );
    }

    public function UpdateReceivePayment(Request $request)
    {
        $id = $request->id;
        $data = ReceivePayment::findOrFail($id);

        $data->update([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'date' => $request->date,
            'month_of' => $request->month_of,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        $notification = array(
            'message' => 'دریافت پرداخت با موفقیت به روز شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.receive.payment')->with($notification);
    }

    public function DeleteReceivePayment($id)
    {
        $data = ReceivePayment::findOrFail($id);
        $data->delete();

        $notification = array(
            'message' => 'دریافت پرداخت با موفقیت حذف شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.receive.payment')->with($notification);
    }
}
