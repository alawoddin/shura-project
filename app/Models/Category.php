<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    protected $guarded = [];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    protected $casts = [
        'is_monthly_fee' => 'boolean',
        'balance' => 'decimal:2',
    ];

    public function scopePaymentTypes($query)
    {
        return $query->where('account_type', 'payment_type');
    }

    public function scopeCashAccounts($query)
    {
        return $query->where('account_type', 'cash');
    }

    /** Payment types allowed on the «دریافت فیس و قرض» page: member fee and loan accounts. */
    public function scopeFeeAndLoanTypes($query)
    {
        return $query->where(function ($q) {
            $q->where('is_monthly_fee', true)
                ->orWhere('account_type', 'receivable');
        });
    }

    public function isLoanType(): bool
    {
        return $this->account_type === 'receivable' && ! $this->is_monthly_fee;
    }

    public function scopeAidAccounts($query)
    {
        return $query->where('account_type', 'aid');
    }

    /** Accounts that hold money and can send / receive funds. */
    public function scopeMoneyAccounts($query)
    {
        return $query->whereIn('account_type', ['cash', 'aid', 'income']);
    }

    public function getAccountTypeLabelAttribute(): string
    {
        return [
            'receivable' => 'حساب‌های دریافتنی (قرض)',
            'payable' => 'حساب‌های پرداختنی',
            'cash' => 'حساب نقدی / صندوق',
            'aid' => 'حساب کمک‌ها',
            'income' => 'منبع درآمد',
            'expense' => 'حساب مصارف',
            'payment_type' => 'نوع پرداخت',
        ][$this->account_type] ?? 'عمومی';
    }

    /**
     * A member-fee account: ticked «فیس ماهانه», or named as a fee account
     * (the word "fee" or «فیس», e.g. "Member Fee Income"). Not shown on the income page.
     */
    public function isMemberFeeAccount(): bool
    {
        return (bool) $this->is_monthly_fee
            || preg_match('/\bfees?\b|فیس/iu', (string) $this->name) === 1;
    }

    public function scopeIncomeSources($query)
    {
        return $query->where('account_type', 'income');
    }

    public function scopeExpenseAccounts($query)
    {
        return $query->where('account_type', 'expense');
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

     public function receivePayments()
    {
        return $this->hasMany(ReceivePayment::class);
    }
}
