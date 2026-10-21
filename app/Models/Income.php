<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Income extends Model
{
    protected $guarded = [];
    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function undeposited()
{
    return $this->hasOne(
        Undeposited::class
    );
}
}
