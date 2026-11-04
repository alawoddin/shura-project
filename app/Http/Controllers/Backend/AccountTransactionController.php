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
 * انتقال بین حساب‌ها (transfer) و دریافت پول (deposit) for money accounts.
 */
class AccountTransactionController extends Controller
{
    public function __construct(protected FinancialService $financialService) {}

    /** انتقال بین حساب‌ها — move money from one account to another. */
    public function index(Request $request)
    {
        return $this->page($request, 'transfer');
    }

    /** دریافت پول — receive money into an account. */
    public function deposits(Request $request)
    {
        return $this->page($request, 'deposit');
    }

    protected function page(Request $request, string $mode)
    {
        $accounts = Category::moneyAccounts()->orderBy('account_type')->orderBy('name')->get();
        $selectedAccountId = $request->integer('account') ?: null;

        $transactions = AccountTransaction::with(['fromAccount', 'toAccount', 'creator'])
            ->where('type', $mode)
            ->when($selectedAccountId, function ($query) use ($selectedAccountId) {
                $query->where(function ($q) use ($selectedAccountId) {
                    $q->where('from_account_id', $selectedAccountId)
                        ->orWhere('to_account_id', $selectedAccountId);
                });
            })
            ->latest('date')
            ->latest('id')
            ->get();

        return view('admin.pages.account_transaction.index', compact(
            'accounts',
            'transactions',
            'selectedAccountId',
            'mode'
        ));
    }

    public function store(Request $request)
    {
        $accountIds = Category::moneyAccounts()->pluck('id')->all();
        $isTransfer = $request->input('type') === 'transfer';

        $request->validate([
            'type' => ['required', Rule::in(array_keys(AccountTransaction::TYPES))],
            'from_account_id' => [
                Rule::requiredIf($isTransfer),
                'nullable',
                Rule::in($accountIds),
            ],
            'to_account_id' => [
                'required',
                Rule::in($accountIds),
                $isTransfer ? 'different:from_account_id' : 'nullable',
            ],
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'party' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ], [
            'from_account_id.required' => 'لطفاً حساب مبدأ را انتخاب کنید.',
            'to_account_id.required' => 'لطفاً حساب مقصد را انتخاب کنید.',
            'to_account_id.different' => 'حساب مبدأ و مقصد نباید یکی باشد.',
            'amount.min' => 'مقدار باید بیشتر از صفر باشد.',
        ]);

        $type = $request->type;
        $fromId = $isTransfer ? (int) $request->from_account_id : null;
        $toId = (int) $request->to_account_id;
        $amount = (float) $request->amount;

        try {
            DB::transaction(function () use ($request, $type, $fromId, $toId, $amount) {
                if ($fromId) {
                    $this->financialService->decreaseAccountBalance($fromId, $amount);
                }

                $this->financialService->increaseAccountBalance($toId, $amount);

                AccountTransaction::create([
                    'type' => $type,
                    'from_account_id' => $fromId,
                    'to_account_id' => $toId,
                    'amount' => $amount,
                    'date' => $request->date,
                    'party' => $type === 'transfer' ? null : $request->party,
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

        $route = $type === 'deposit' ? 'all.account.deposits' : 'all.account.transactions';

        return redirect()
            ->route($route, array_filter(['account' => $request->integer('return_account') ?: null]))
            ->with([
                'message' => $type === 'deposit' ? 'پول با موفقیت دریافت شد' : 'انتقال با موفقیت انجام شد',
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
