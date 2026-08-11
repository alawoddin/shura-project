<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeyPerson extends Model
{
    protected $table = 'key_personnel';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
