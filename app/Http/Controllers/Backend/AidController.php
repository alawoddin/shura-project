<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Aids;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;

class AidController extends Controller
{
    public function AllAids(){
        $alldata = Aids::with(['user', 'category'])->get();
        return view('admin.pages.aid.all_aids', compact('alldata'));
    }

    public function AddAid(){
        $users = User::all();
        $categories = Category::all();

        return view('admin.pages.aid.add_aid', compact('users', 'categories'));
    }

    public function StoreAid(Request $request){
        Aids::create([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

         $notification = array(
            'message' => 'کمک مالی با موفقیت اضافه شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.aid')->with($notification);
    }

    public function EditAid($id){
        $editdata = Aids::findOrFail($id);
        $users = User::all();
        $categories = Category::all();

        return view('admin.pages.aid.edit_aid', compact('editdata', 'users', 'categories'));
    }

    public function UpdateAid(Request $request){
        $aid = Aids::findOrFail($request->id);
        $aid->update([
            'user_id' => $request->user_id,
            'category_id' => $request->category_id,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

         $notification = array(
            'message' => 'کمک مالی با موفقیت به روز شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.aid')->with($notification);
    }

    public function DeleteAid($id){
        Aids::findOrFail($id)->delete();

         $notification = array(
            'message' => 'کمک مالی با موفقیت حذف شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.aid')->with($notification);
    }
}
