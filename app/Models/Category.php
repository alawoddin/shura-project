<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $guarded = [];

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
