<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    // Fillable fields 
    protected $fillable = [
        'rate',         // Base rate for temporary permits (per day)
        'monthly_rate', // Fixed rate for monthly permits (30 days)
        'ssl',          // SSL percentage
        'vat',          // VAT percentage
    ];
}
