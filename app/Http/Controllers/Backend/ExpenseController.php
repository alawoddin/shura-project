<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function AllExpense(){
        $alldata = Expense::with(['category'])->get();
        return view('admin.pages.expense.all_expense', compact('alldata'));
    }

    public function AddExpense(){

    $categories = Category::all();


        return view('admin.pages.expense.add_expense', compact('categories'));
    }

    public function StoreExpense(Request $request){
        Expense::create([
            'category_id' => $request->category_id,
            'expense_name' => $request->expense_name,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        $notification = array(
            'message' => 'Expense Inserted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.expense')->with($notification);
    }

    public function EditExpense(int $id){
        $editdata = Expense::findOrFail($id);
        $categories = Category::all();
        return view('admin.pages.expense.edit_expense', compact('editdata', 'categories'));
    }

    public function UpdateExpense(Request $request){
        $expense_id = $request->id;

        Expense::findOrFail($expense_id)->update([
            'category_id' => $request->category_id,
            'expense_name' => $request->expense_name,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        $notification = array(
            'message' => 'Expense Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.expense')->with($notification);
    }

    public function DeleteExpense(int $id){
        Expense::findOrFail($id)->delete();

        $notification = array(
            'message' => 'Expense Deleted Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.expense')->with($notification);
    }
}
