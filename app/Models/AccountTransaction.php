<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AccountTransaction extends Model
{
    use LogsActivity;

    protected $guarded = [];

    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
    ];

    public const TYPES = [
        'deposit' => 'دریافت پول',
        'withdraw' => 'ارسال پول',
        'transfer' => 'انتقال بین حساب‌ها',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function fromAccount()
    {
        return $this->belongsTo(Category::class, 'from_account_id');
    }

    public function toAccount()
    {
        return $this->belongsTo(Category::class, 'to_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getTypeLabelAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
