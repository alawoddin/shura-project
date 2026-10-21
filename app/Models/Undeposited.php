<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Undeposited extends Model
{
    protected $guarded = [];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

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
