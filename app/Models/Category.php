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
            'receivable' => 'حساب‌های دریافتنی',
            'payable' => 'حساب‌های پرداختنی',
            'cash' => 'حساب نقدی / صندوق',
            'aid' => 'حساب کمک‌ها',
            'income' => 'منبع درآمد',
            'expense' => 'حساب مصارف',
            'payment_type' => 'نوع پرداخت',
        ][$this->account_type] ?? 'عمومی';
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
