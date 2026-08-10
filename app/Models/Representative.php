<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Representative extends Model
{
    protected $guarded = [];

    public function ethnicBranch()
    {
        return $this->belongsTo(EthnicBranch::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
