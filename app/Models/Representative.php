<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Representative extends Model
{
    protected $guarded = [];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function ethnicBranch()
    {
        return $this->belongsTo(EthnicBranch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
