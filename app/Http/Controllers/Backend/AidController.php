<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Aids;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class AidController extends Controller
{
    public function AllAids()
    {
        $alldata = Aids::with(['user', 'category', 'sourceAccount'])->get();
        return view('admin.pages.aid.all_aids', compact('alldata'));
    }

    public function AddAid()
    {
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        $sourceAccounts = Category::cashAccounts()->get();

        return view('admin.pages.aid.add_aid', compact('users', 'categories', 'sourceAccounts'));
    }

    public function StoreAid(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'source_account_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Aids::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'source_account_id' => $request->source_account_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        $notification = [
            'message' => 'کمک مالی با موفقیت اضافه شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.aid')->with($notification);
    }

    public function EditAid($id)
    {
        $editdata = Aids::findOrFail($id);
        $users = User::where('role', 'user')->get();
        $categories = Category::all();
        $sourceAccounts = Category::cashAccounts()->get();

        return view('admin.pages.aid.edit_aid', compact('editdata', 'users', 'categories', 'sourceAccounts'));
    }

    public function UpdateAid(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:aids,id',
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'source_account_id' => 'required|exists:categories,id',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Aids::findOrFail($request->id)->update([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'source_account_id' => $request->source_account_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        $notification = [
            'message' => 'کمک مالی با موفقیت به روز شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.aid')->with($notification);
    }

    public function DeleteAid($id)
    {
        Aids::findOrFail($id)->delete();

        $notification = [
            'message' => 'کمک مالی با موفقیت حذف شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.aid')->with($notification);
    }
}
