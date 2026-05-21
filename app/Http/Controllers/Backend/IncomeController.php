<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Income;
use Illuminate\Http\Request;

class IncomeController extends Controller
{
    public function AllIncome(){
        $alldata = Income::with(['category'])->get();
        return view('admin.pages.income.all_income' , compact('alldata'));
    }

    public function AddIncome(){

    $categories = Category::all();

        return view('admin.pages.income.add_income', compact('categories'));
    }

    public function StoreIncome(Request $request){

     

        Income::create([
            'category_id' => $request->category_id,
            'creditor_name' => $request->creditor_name,
            'amount' => $request->amount,
            'date' => $request->date,
            'note' => $request->note,
        ]);

          $notification = array(
            'message' => 'کاربر موفقانه اضافه شد',
            'alert-type' => 'success'
        );



        return redirect()->route('all.income')->with($notification);
    }

    public function EditIncome(int $id){
        $editdata = Income::findOrFail($id);
        $categories = Category::all();
        return view('admin.pages.income.edit_income', compact('editdata', 'categories'));
    }

    public function UpdateIncome(Request $request){
        $income_id = $request->id;

        Income::findOrFail($income_id)->update([
            'category_id' => $request->category_id,
            'creditor_name' => $request->creditor_name,
            'amount' => $request->amount,
            'date' => $request->date,
            'note' => $request->note,
        ]);

          $notification = array(
            'message' => 'کاربر موفقانه اپدیت شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.income')->with($notification);

    }

    public function DeleteIncome(int $id){
        Income::findOrFail($id)->delete();

          $notification = array(
            'message' => 'کاربر موفقانه حذف شد',
            'alert-type' => 'success'
        );

        return redirect()->route('all.income')->with($notification);
    }

}
