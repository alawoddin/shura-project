<?php

namespace App\Http\Controllers\Backend;

use App\Exceptions\InsufficientBalanceException;
use App\Http\Controllers\Controller;
use App\Models\AccountTransaction;
use App\Models\Category;
use App\Services\FinancialService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * ارسال و دریافت پول در حساب‌ها
 * Deposit money into an account, withdraw (send) money out of an account,
 * or transfer money between two accounts.
 */
class AccountTransactionController extends Controller
{
    public function __construct(protected FinancialService $financialService) {}

    public function index(Request $request)
    {
        $accounts = Category::moneyAccounts()->orderBy('account_type')->orderBy('name')->get();
        $selectedAccountId = $request->integer('account') ?: null;

        $transactions = AccountTransaction::with(['fromAccount', 'toAccount', 'creator'])
            ->when($selectedAccountId, function ($query) use ($selectedAccountId) {
                $query->where(function ($q) use ($selectedAccountId) {
                    $q->where('from_account_id', $selectedAccountId)
                        ->orWhere('to_account_id', $selectedAccountId);
                });
            })
            ->latest('date')
            ->latest('id')
            ->get();

        $types = AccountTransaction::TYPES;

        return view('admin.pages.account_transaction.index', compact(
            'accounts',
            'transactions',
            'types',
            'selectedAccountId'
        ));
    }

    public function store(Request $request)
    {
        $accountIds = Category::moneyAccounts()->pluck('id')->all();

        $request->validate([
            'type' => ['required', Rule::in(array_keys(AccountTransaction::TYPES))],
            'from_account_id' => [
                Rule::requiredIf(in_array($request->type, ['withdraw', 'transfer'], true)),
                'nullable',
                Rule::in($accountIds),
            ],
            'to_account_id' => [
                Rule::requiredIf(in_array($request->type, ['deposit', 'transfer'], true)),
                'nullable',
                Rule::in($accountIds),
                'different:from_account_id',
            ],
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'party' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'from_account_id.required' => 'لطفاً حساب ارسال‌کننده را انتخاب کنید.',
            'to_account_id.required' => 'لطفاً حساب دریافت‌کننده را انتخاب کنید.',
            'to_account_id.different' => 'حساب ارسال‌کننده و دریافت‌کننده نباید یکی باشد.',
            'amount.min' => 'مقدار باید بیشتر از صفر باشد.',
        ]);

        $type = $request->type;
        $fromId = in_array($type, ['withdraw', 'transfer'], true) ? (int) $request->from_account_id : null;
        $toId = in_array($type, ['deposit', 'transfer'], true) ? (int) $request->to_account_id : null;
        $amount = (float) $request->amount;

        try {
            DB::transaction(function () use ($request, $type, $fromId, $toId, $amount) {
                if ($fromId) {
                    $this->financialService->decreaseAccountBalance($fromId, $amount);
                }

                if ($toId) {
                    $this->financialService->increaseAccountBalance($toId, $amount);
                }

                AccountTransaction::create([
                    'type' => $type,
                    'from_account_id' => $fromId,
                    'to_account_id' => $toId,
                    'amount' => $amount,
                    'date' => $request->date,
                    'party' => $request->party,
                    'description' => $request->description,
                    'created_by' => Auth::id(),
                ]);
            });
        } catch (InsufficientBalanceException $e) {
            return back()->withInput()->with([
                'message' => 'موجودی حساب کافی نیست. ' . $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        return redirect()
            ->route('all.account.transactions', array_filter(['account' => $request->integer('return_account') ?: null]))
            ->with([
                'message' => 'تراکنش با موفقیت ثبت شد',
                'alert-type' => 'success',
            ]);
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                $transaction = AccountTransaction::lockForUpdate()->findOrFail($id);
                $amount = (float) $transaction->amount;

                // Reverse the balance movement of the original transaction.
                if ($transaction->to_account_id) {
                    $this->financialService->decreaseAccountBalance($transaction->to_account_id, $amount);
                }

                if ($transaction->from_account_id) {
                    $this->financialService->increaseAccountBalance($transaction->from_account_id, $amount);
                }

                $transaction->delete();
            });
        } catch (InsufficientBalanceException $e) {
            return back()->with([
                'message' => 'این تراکنش قابل حذف نیست؛ پول آن قبلاً از حساب مصرف شده است. ' . $e->getMessage(),
                'alert-type' => 'error',
            ]);
        }

        return back()->with([
            'message' => 'تراکنش حذف شد و موجودی حساب‌ها برگردانده شد',
            'alert-type' => 'success',
        ]);
    }
}
