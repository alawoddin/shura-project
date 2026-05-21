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

}
