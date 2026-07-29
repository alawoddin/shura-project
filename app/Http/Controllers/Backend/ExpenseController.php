<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Expense;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

    public function AllExpense()
    {
        if (! auth()->user()->hasPermissionTo('all.expense')) {
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

        try {
            DB::transaction(function () use ($request) {
                $this->financialService->decreaseAccountBalance(
                    (int) $request->source_account_id,
                    (float) $request->amount
                );

                Expense::create([
                    'category_id' => $request->category_id,
                    'source_account_id' => $request->source_account_id,
                    'expense_name' => $request->expense_name,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'description' => $request->description,
                ]);
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

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

        try {
            DB::transaction(function () use ($request) {
                $expense = Expense::findOrFail($request->id);
                $oldAmount = (float) $expense->amount;
                $oldSourceAccountId = $expense->source_account_id;

                if ($oldSourceAccountId != $request->source_account_id) {
                    $this->financialService->increaseAccountBalance($oldSourceAccountId, $oldAmount);
                    $this->financialService->decreaseAccountBalance(
                        (int) $request->source_account_id,
                        (float) $request->amount
                    );
                } else {
                    $this->financialService->adjustAccountBalance(
                        (int) $request->source_account_id,
                        $oldAmount,
                        (float) $request->amount
                    );
                }

                $expense->update([
                    'category_id' => $request->category_id,
                    'source_account_id' => $request->source_account_id,
                    'expense_name' => $request->expense_name,
                    'amount' => $request->amount,
                    'date' => $request->date,
                    'description' => $request->description,
                ]);
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        $notification = [
            'message' => 'Expense Updated Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.expense')->with($notification);
    }

    public function DeleteExpense(int $id)
    {
        DB::transaction(function () use ($id) {
            $expense = Expense::findOrFail($id);
            $this->financialService->increaseAccountBalance(
                $expense->source_account_id,
                (float) $expense->amount
            );
            $expense->delete();
        });

        $notification = [
            'message' => 'Expense Deleted Successfully',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.expense')->with($notification);
    }
}
