<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'name',
        'code',     // new unique vehicle code
        'rate',     // new base rate
    ];
}
