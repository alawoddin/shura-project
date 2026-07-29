<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Income;
use App\Models\User;
use App\Models\Undeposited;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

    public function AllIncome()
    {
        $alldata = Income::with('category')->whereHas('undeposited', function ($query) {
            $query->where('status', 'transferred');
        })->latest()->get();

        return view('admin.pages.income.all_income', compact('alldata'));
    }

    public function AddIncome()
    {
        $categories = Category::incomeSources()->get();
        $users = User::all();

        if ($categories->isEmpty()) {
            $categories = Category::all();
        }

        return view('admin.pages.income.add_income', compact('categories', 'users'));
    }

    public function StoreIncome(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'creditor_name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'note' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $income = Income::create([
                'category_id' => $request->category_id,
                'creditor_name' => $request->creditor_name,
                'amount' => $request->amount,
                'date' => $request->date,
                'note' => $request->note,
            ]);

            Undeposited::create([
                'income_id' => $income->id,
                'status' => 'pending',
            ]);

            $this->financialService->increaseAccountBalance(
                (int) $request->category_id,
                (float) $request->amount
            );
        });

        $notification = [
            'message' => 'درآمد با موفقیت ثبت شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('undeposited.income')->with($notification);
    }

    public function EditIncome(int $id)
    {
        $editdata = Income::findOrFail($id);
        $categories = Category::incomeSources()->get();
        $users = User::all();

        if ($categories->isEmpty()) {
            $categories = Category::all();
        }

        return view('admin.pages.income.edit_income', compact('editdata', 'categories', 'users'));
    }

    public function UpdateIncome(Request $request)
    {
        $income_id = $request->id;

        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'creditor_name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $income_id) {
            $income = Income::findOrFail($income_id);
            $oldAmount = (float) $income->amount;
            $oldCategoryId = $income->category_id;

            $income->update([
                'category_id' => $request->category_id,
                'creditor_name' => $request->creditor_name,
                'amount' => $request->amount,
                'date' => $request->date,
                'note' => $request->note,
            ]);

            if ($oldCategoryId != $request->category_id) {
                $this->financialService->decreaseAccountBalance($oldCategoryId, $oldAmount, true);
                $this->financialService->increaseAccountBalance((int) $request->category_id, (float) $request->amount);
            } else {
                $this->financialService->adjustAccountBalance(
                    (int) $request->category_id,
                    $oldAmount,
                    (float) $request->amount
                );
            }
        });

        $notification = [
            'message' => 'درآمد با موفقیت به روز شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.income')->with($notification);
    }

    public function DeleteIncome(int $id)
    {
        DB::transaction(function () use ($id) {
            $income = Income::findOrFail($id);
            $this->financialService->decreaseAccountBalance(
                $income->category_id,
                (float) $income->amount,
                true
            );
            $income->delete();
        });

        $notification = [
            'message' => 'درآمد با موفقیت حذف شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.income')->with($notification);
    }

    public function UndepositedIncome()
    {
        $undepositedIncomes = Undeposited::with(['income.category', 'targetAccount'])->where('status', 'pending')->get();
        $cashAccounts = Category::cashAccounts()->get();

        return view('admin.pages.income.undeposited_income', compact('undepositedIncomes', 'cashAccounts'));
    }

    public function TransferIncome(Request $request, $id)
    {
        $request->validate([
            'target_account_id' => 'required|exists:categories,id',
        ]);

        DB::transaction(function () use ($request, $id) {
            $undeposited = Undeposited::with('income')->findOrFail($id);
            $amount = (float) $undeposited->income->amount;
            $incomeCategoryId = $undeposited->income->category_id;

            $undeposited->update([
                'status' => 'transferred',
                'target_account_id' => $request->target_account_id,
            ]);

            $this->financialService->decreaseAccountBalance($incomeCategoryId, $amount, true);
            $this->financialService->increaseAccountBalance((int) $request->target_account_id, $amount);
        });

        $notification = [
            'message' => 'مبلغ با موفقیت به حساب منتقل شد',
            'alert-type' => 'success',
        ];

        return back()->with($notification);
    }
}
