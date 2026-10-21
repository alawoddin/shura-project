<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EthnicBranch extends Model
{
    protected $guarded = [];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function representatives()
    {
        return $this->hasMany(Representative::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
