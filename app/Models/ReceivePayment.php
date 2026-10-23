<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ReceivePayment extends Model
{
    protected $guarded = [];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
    
    public function users()
    {
        return $this->belongsTo(
            User::class,
            'user_id'
        );
    }

    public function category()
    {
        return $this->belongsTo(
            Category::class
        );
    }

    
}
