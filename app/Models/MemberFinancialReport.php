<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberFinancialReport extends Model
{
     protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }

    public function linkedCredit()
    {
        return $this->belongsTo(Credit::class, 'credit_id');
    }
}
