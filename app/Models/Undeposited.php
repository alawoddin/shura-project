<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Undeposited extends Model
{
     protected $guarded = [];

     public function income()
{
    return $this->belongsTo(
        Income::class
    );
}

public function targetAccount()
{
    return $this->belongsTo(Category::class, 'target_account_id');
}

}
