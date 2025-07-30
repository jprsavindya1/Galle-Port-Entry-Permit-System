<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit;
use App\Models\Payment;
use App\Models\PaymentSetting;  
class PaymentController extends Controller
{
   public function summary()
{
    $cart = session('payment_cart', []);
    $submissionId = session('payment_submission_id');

    if (empty($cart) || !$submissionId) {
        return redirect()->route('permit.temporary')->with('error', 'No permit data found for payment.');
    }

    // Get settings from DB
    $settings = PaymentSetting::first();
    $baseRate = $settings->rate;  // Set by admin, no default
    $nbtRate = $settings->nbt;         // Set by admin, no default
    $vatRate = $settings->vat;         // Set by admin, no default

    $detailedPayments = [];
    $totalPayment = 0;

    foreach ($cart as $item) {
        $days = \Carbon\Carbon::parse($item['from_date'])->diffInDays($item['to_date']) + 1;

        $tRate = $baseRate * $days;

        if ($item['issue_type'] === 'free') {
            $nbt = 0;
            $vat = 0;
            $amount = 0;
        } else {
            $nbt = round(($tRate * $nbtRate) / 100, 2);
            $vat = round((($tRate + $nbt) * $vatRate) / 100, 2);
            $amount = round($tRate + $nbt + $vat, 2);
        }

        $totalPayment += $amount;

        $detailedPayments[] = [
            'entry' => $item,
            'rate' => $item['issue_type'] === 'free' ? 0 : $tRate,
            'nbt' => $nbt,
            'vat' => $vat,
            'total' => $amount,
        ];
    }

    return view('payment.summary', compact(
        'cart',
        'submissionId',
        'detailedPayments',
        'totalPayment'
    ));
}




    public function submit(Request $request)
    {
        $cart = session('payment_cart', []);
        $submissionId = session('payment_submission_id');

        if (empty($cart) || !$submissionId) {
            return redirect()->route('permit.temporary')->with('error', 'No permit data found.');
        }

        foreach ($cart as $entry) {
            $entry['permit_id'] = $this->generatePermitId($entry['type']);
            $entry['pass_type'] = is_array($entry['pass_type']) ? implode(',', $entry['pass_type']) : $entry['pass_type'];
            Permit::create($entry);
        }

        // Clear payment cart
        session()->forget(['payment_cart', 'payment_submission_id']);

        return redirect()->route('permit.temporary')->with('success', 'Payment successful and permits submitted!');
    }

    // Moved here from PermitController
    private function generatePermitId($type)
    {
        $prefix = $type;
        $yearMonth = now()->format('ym');

        $latest = Permit::where('permit_id', 'like', $prefix . $yearMonth . '%')
            ->orderBy('permit_id', 'desc')
            ->first();

        $nextNumber = 1;
        if ($latest) {
            $lastId = $latest->permit_id;
            $lastCounter = (int)substr($lastId, -4);
            $nextNumber = $lastCounter + 1;
        }

        return $prefix . $yearMonth . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    public function submitPayment(Request $request)
{
    $cart = session()->get('permit_cart', []);
    $submissionId = session()->get('submission_id');
    $totalAmount = session()->get('payment_total'); // Set this earlier when showing summary

    if (empty($cart) || !$submissionId || !$totalAmount) {
        return redirect()->route('permit.temporary')->with('error', 'Session expired or invalid.');
    }

    // Save payment info
    Payment::create([
        'submission_id' => $submissionId,
        'amount' => $totalAmount,
        'status' => 'Paid',
        'payment_date' => now(),
    ]);

    // Clear session
    session()->forget(['permit_cart', 'company_name', 'company_address', 'submission_id', 'payment_total']);

    return redirect()->route('permit.temporary')->with('success', 'Payment successful and permits submitted!');
}

}
