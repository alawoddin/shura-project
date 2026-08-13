<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    protected $guarded = [];

    protected $casts = [
        'last_payment_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function category()
    {
        return $this->belongsTo(
            Category::class
        );
    }

    public function sourceAccount()
    {
        return $this->belongsTo(Category::class, 'source_account_id');
    }

    public function payments()
    {
        return $this->hasMany(CreditPayment::class);
    }
}
