<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditPayment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function credit()
    {
        return $this->belongsTo(Credit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
