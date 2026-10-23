<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class KeyPerson extends Model
{
    protected $table = 'key_personnel';

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
