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
use Illuminate\Validation\Rule;

class IncomeController extends Controller
{
    public function __construct(
        protected FinancialService $financialService
    ) {}

    public function AllIncome()
    {
        // Member-fee accounts are not shown on the income page.
        $alldata = Income::with('category')
            ->whereDoesntHave('category', fn ($q) => $q->where('is_monthly_fee', true))
            ->whereHas('undeposited', function ($query) {
            $query->where('status', 'transferred');
        })->latest()->get();

        return view('admin.pages.income.all_income', compact('alldata'));
    }

    public function AddIncome()
    {
        // Member-fee accounts are not offered on the income page.
        $categories = Category::incomeSources()->get()
            ->reject(fn (Category $category) => $category->isMemberFeeAccount())
            ->values();
        $users = User::all();

        return view('admin.pages.income.add_income', compact('categories', 'users'));
    }

    public function StoreIncome(Request $request)
    {
        $request->validate([
            'category_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $category = Category::incomeSources()->find($value);

                    if (! $category || $category->isMemberFeeAccount()) {
                        $fail('لطفاً یک حساب درآمد را انتخاب کنید (حساب‌های فیس اعضا مجاز نیست).');
                    }
                },
            ],
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
        // Member-fee accounts are not offered; the account already saved on this income stays selectable.
        $categories = Category::incomeSources()->get()
            ->reject(fn (Category $category) => $category->isMemberFeeAccount()
                && $category->id !== (int) $editdata->category_id)
            ->values();

        if (! $categories->contains('id', $editdata->category_id) && ($current = Category::find($editdata->category_id))) {
            $categories->push($current);
        }
        $users = User::all();

        return view('admin.pages.income.edit_income', compact('editdata', 'categories', 'users'));
    }

    public function UpdateIncome(Request $request)
    {
        $income_id = $request->id;

        $request->validate([
            'category_id' => [
                'required',
                function ($attribute, $value, $fail) use ($income_id) {
                    $isCurrent = (int) Income::whereKey($income_id)->value('category_id') === (int) $value;

                    $category = Category::incomeSources()->find($value);

                    if (! $isCurrent && (! $category || $category->isMemberFeeAccount())) {
                        $fail('لطفاً یک حساب درآمد را انتخاب کنید (حساب‌های فیس اعضا مجاز نیست).');
                    }
                },
            ],
            'creditor_name' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
        ]);

        DB::transaction(function () use ($request, $income_id) {
            $income = Income::findOrFail($income_id);
            $oldAmount = (float) $income->amount;
            $oldCategoryId = (int) $income->category_id;

            $income->update([
                'category_id' => $request->category_id,
                'creditor_name' => $request->creditor_name,
                'amount' => $request->amount,
                'date' => $request->date,
                'note' => $request->note,
            ]);

            $this->financialService->applyIncomeRecordChange(
                $income,
                $oldAmount,
                $oldCategoryId,
                (float) $request->amount,
                (int) $request->category_id
            );
        });

        $notification = [
            'message' => 'درآمد با موفقیت به روز شد',
            'alert-type' => 'success',
        ];

        return redirect()->route('all.income')->with($notification);
    }

    
    public function DeleteIncome(int $id){
        DB::transaction(function () use ($id) {
            $income = Income::findOrFail($id);
            $this->financialService->reverseIncomeRecord($income);
            $income->delete();
        });

        return redirect()->route('all.income')->with([
            'message' => 'درآمد با موفقیت حذف شد',
            'alert-type' => 'success',
        ]);
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

        $alreadyTransferred = false;

        DB::transaction(function () use ($request, $id, &$alreadyTransferred) {
            $undeposited = Undeposited::with('income')->lockForUpdate()->findOrFail($id);

            // Never deposit the same income twice (double click / resubmitted form).
            if ($undeposited->status === 'transferred') {
                $alreadyTransferred = true;

                return;
            }

            $amount = (float) $undeposited->income->amount;
            $incomeCategoryId = $undeposited->income->category_id;

            $undeposited->update([
                'status' => 'transferred',
                'target_account_id' => $request->target_account_id,
            ]);

            $this->financialService->decreaseAccountBalance($incomeCategoryId, $amount, true);
            $this->financialService->increaseAccountBalance((int) $request->target_account_id, $amount);
        });

        if ($alreadyTransferred) {
            return back()->with([
                'message' => 'این درآمد قبلاً به حساب واریز شده است.',
                'alert-type' => 'warning',
            ]);
        }

        $notification = [
            'message' => 'مبلغ با موفقیت به حساب منتقل شد',
            'alert-type' => 'success',
        ];

        return back()->with($notification);
    }
}
