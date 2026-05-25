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

}
