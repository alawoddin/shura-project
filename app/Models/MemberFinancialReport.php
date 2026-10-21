<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class MemberFinancialReport extends Model
{
    protected $guarded = [];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function linkedCredit()
    {
        return $this->belongsTo(Credit::class, 'credit_id');
    }
}
