<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PaymentSetting;

class PaymentSettingController extends Controller
{
    public function edit()
    {
        $settings = PaymentSetting::first();

       
        if (!$settings) {
            $settings = PaymentSetting::create([]);
        }

        return view('admin.payment_settings.edit', compact('settings'));
    }
    /*
     ***********  Payment setttngs dynamic variables *********   
    */
   public function update(Request $request)
{
    $validated = $request->validate([
        'rate' => 'required|numeric|min:0',
        'nbt' => 'required|numeric|min:0',
        'vat' => 'required|numeric|min:0',
        'ssc'  => 'required|numeric|min:0',
    ]);

    $settings = PaymentSetting::first();

    if (!$settings) {
        $settings = PaymentSetting::create($validated);
    } else {
        $settings->update($validated);
    }

    return redirect()->route('admin.payment_settings.edit')->with('success', 'Payment settings updated successfully.');
}

}
