<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{

    protected $guarded = [];
    

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function sourceAccount()
    {
        return $this->belongsTo(Category::class, 'source_account_id');
    }
}
