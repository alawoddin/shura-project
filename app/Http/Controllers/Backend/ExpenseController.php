<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function AllExpense()
    {
        if (!auth()->user()->hasPermissionTo('all.expense')) {
            abort(403, 'Unauthorized Action');
        }

        $alldata = Expense::with(['category', 'sourceAccount'])->get();
        return view('admin.pages.expense.all_expense', compact('alldata'));
    }

    public function AddExpense()
    {
        $categories = Category::expenseAccounts()->get();
        $sourceAccounts = Category::cashAccounts()->get();

        if ($categories->isEmpty()) {
            $categories = Category::all();
        }

        return view('admin.pages.expense.add_expense', compact('categories', 'sourceAccounts'));
    }

    public function StoreExpense(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'source_account_id' => 'required|exists:categories,id',
            'expense_name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Expense::create([
            'category_id' => $request->category_id,
            'source_account_id' => $request->source_account_id,
            'expense_name' => $request->expense_name,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        $notification = [
            'message' => 'Expense Inserted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.expense')->with($notification);
    }

    public function EditExpense(int $id)
    {
        $editdata = Expense::findOrFail($id);
        $categories = Category::expenseAccounts()->get();
        $sourceAccounts = Category::cashAccounts()->get();

        if ($categories->isEmpty()) {
            $categories = Category::all();
        }

        return view('admin.pages.expense.edit_expense', compact('editdata', 'categories', 'sourceAccounts'));
    }

    public function UpdateExpense(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:expenses,id',
            'category_id' => 'required|exists:categories,id',
            'source_account_id' => 'required|exists:categories,id',
            'expense_name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        Expense::findOrFail($request->id)->update([
            'category_id' => $request->category_id,
            'source_account_id' => $request->source_account_id,
            'expense_name' => $request->expense_name,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        $notification = [
            'message' => 'Expense Updated Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.expense')->with($notification);
    }

    public function DeleteExpense(int $id)
    {
        Expense::findOrFail($id)->delete();

        $notification = [
            'message' => 'Expense Deleted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.expense')->with($notification);
    }
}
