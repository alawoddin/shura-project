<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberFinancialReport extends Model
{
     protected $guarded = [];

    public function member(){
        return $this->belongsTo(User::class, 'member_id');
    }
}
