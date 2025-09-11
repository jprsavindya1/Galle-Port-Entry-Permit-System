<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{// Fillable fields 
    protected $fillable = [
        'rate', 'nbt', 'vat', 'ssc',
    ];
}
