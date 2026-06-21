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
        $alldata = Credit::with('user', 'category', 'sourceAccount')->get();
        return view('admin.pages.credit.all_credit', compact('alldata'));
    }

    public function AddCredit()
    {
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        $sourceAccounts = Category::cashAccounts()->get();

        return view('admin.pages.credit.add_credit', compact('users', 'categories', 'sourceAccounts'));
    }

    public function StoreCredit(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'source_account_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        Credit::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'source_account_id' => $request->source_account_id,
            'date' => $request->date,
            'amount' => $request->amount,
            'remaining_amount' => $request->amount,
            'description' => $request->description,
        ]);

        $notification = [
            'message' => 'Credit Inserted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.credits')->with($notification);
    }

    public function EditCredit($id)
    {
        $credit = Credit::findOrFail($id);
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        $sourceAccounts = Category::cashAccounts()->get();

        return view('admin.pages.credit.edit_credit', compact('credit', 'users', 'categories', 'sourceAccounts'));
    }

    public function UpdateCredit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:credits,id',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'source_account_id' => 'required|exists:categories,id',
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string',
        ]);

        Credit::findOrFail($request->id)->update([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'source_account_id' => $request->source_account_id,
            'date' => $request->date,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        $notification = [
            'message' => 'Credit Updated Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.credits')->with($notification);
    }

    public function DeleteCredit($id)
    {
        Credit::findOrFail($id)->delete();

        $notification = [
            'message' => 'Credit Deleted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.credits')->with($notification);
    }

    public function PaidCredit(Request $request)
    {
        $credit = Credit::findOrFail($request->id);
        $paidAmount = $request->paid_amount;

        if ($paidAmount > $credit->remaining_amount) {
            $notification = [
                'message' => 'Paid amount cannot be greater than remaining amount',
                'alert-type' => 'error',
            ];

            return redirect()->route('all.credits')->with($notification);
        }

        $credit->amount -= $paidAmount;
        $credit->remaining_amount -= $paidAmount;

        if ($credit->amount <= 0) {
            $credit->amount = 0;
            $credit->remaining_amount = 0;
            $credit->status = 'paid';
        }

        $credit->save();

        $notification = [
            'message' => 'Credit Paid Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.credits')->with($notification);
    }
}
