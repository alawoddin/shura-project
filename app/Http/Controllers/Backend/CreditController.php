<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Credit;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class CreditController extends Controller
{
    public function AllCredits()
    {
        $alldata = Credit::with('user', 'category')->get();
        return view('admin.pages.credit.all_credit', compact('alldata'));
    }

    public function AddCredit()
    {
        $users = User::all();
        $categories = Category::all();

        return view('admin.pages.credit.add_credit' , compact('users', 'categories'));
    }

    public function StoreCredit(Request $request)
    {
        Credit::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'date' => $request->date,
            'amount' => $request->amount,
            'remaining_amount' => $request->amount,
            'description' => $request->description,
        ]);

         $notification = array(
            'message' => 'Credit Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.credits')->with($notification);


    }

    public function EditCredit($id)
    {
        $credit = Credit::findOrFail($id);
        $users = User::all();
        $categories = Category::all();

        return view('admin.pages.credit.edit_credit', compact('credit', 'users', 'categories'));
    }

    public function UpdateCredit(Request $request)
    {
        $id = $request->id;

        Credit::findOrFail($id)->update([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'date' => $request->date,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

         $notification = array(
            'message' => 'Credit Updated Successfully',
            'alert-type' => 'success'
        );
        return redirect()->route('all.credits')->with($notification);
    }

    public function DeleteCredit($id)
    {
            Credit::findOrFail($id)->delete();
    
            $notification = array(
                'message' => 'Credit Deleted Successfully',
                'alert-type' => 'success'
            );

            return redirect()->route('all.credits')->with($notification);
    }
}
