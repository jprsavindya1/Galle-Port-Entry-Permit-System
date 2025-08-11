<?php


namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
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
    $baseRate = $settings->rate;  // All values Set by admin
    $nbtRate = $settings->nbt;        
    $vatRate = $settings->vat;         

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

    $permitType = $cart[0]['type'] ?? 'unknown';

    // Save all permits
    foreach ($cart as $entry) {
        $entry['permit_id'] = $this->generatePermitId($entry['type']);
        $entry['pass_type'] = is_array($entry['pass_type']) ? implode(',', $entry['pass_type']) : $entry['pass_type'];
        Permit::create($entry);
    }

    // Get settings
    $settings = PaymentSetting::first();
    $rate = $settings->rate ?? 0;
    $nbtRate = $settings->nbt ?? 0;
    $vatRate = $settings->vat ?? 0;

    // Calculate totals
    $entryCount = 0;
    $rateTotal = 0;
    $nbtTotal = 0;
    $vatTotal = 0;

    foreach ($cart as $entry) {
        if ($entry['issue_type'] !== 'free') {
            $entryCount++;

            $days = \Carbon\Carbon::parse($entry['from_date'])->diffInDays($entry['to_date']) + 1;
            $baseRate = $rate * $days;
            $nbt = ($baseRate * $nbtRate) / 100;
            $vat = (($baseRate + $nbt) * $vatRate) / 100;

            $rateTotal += $baseRate;
            $nbtTotal += $nbt;
            $vatTotal += $vat;
        }
    }
$yearMonth = now()->format('Ym'); // "202507"
$prefix = 'INV-' . $yearMonth . '-';

// Get latest invoice for the month
$latestInvoice = Payment::where('invoice_id', 'like', $prefix . '%')
    ->orderBy('invoice_id', 'desc')
    ->first();

if ($latestInvoice && preg_match('/-(\d+)$/', $latestInvoice->invoice_id, $matches)) {
    $nextNumber = intval($matches[1]) + 1;
} else {
    $nextNumber = 1;
}

// Final invoice ID
$invoiceId = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    $amountTotal = $rateTotal + $nbtTotal + $vatTotal;

    // Save to payments table
    Payment::create([
        'submission_id' => $submissionId,
        'invoice_id' => $invoiceId,
        'permit_type' => $permitType,
        'entry_count' => $entryCount,
        'rate_total' => $rateTotal,
        'nbt_total' => $nbtTotal,
        'vat_total' => $vatTotal,
        'amount_total' => $amountTotal,
        'status' => 'Paid',
        'payment_date' => now(),
        'paid_at' => now(),
    ]);

    // Clear session
    session()->forget(['permit_cart', 'payment_cart', 'payment_submission_id', 'company_name', 'company_address']);

    return redirect()->route('payment.invoice', ['submission_id' => $submissionId]);

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
    $totalAmount = session()->get('payment_total');

    if (empty($cart) || !$submissionId || !$totalAmount) {
        return redirect()->route('permit.temporary')->with('error', 'Session expired or invalid.');
    }

    // ==== Generate Invoice ID ====
    $yearMonth = now()->format('Ym'); // "202507"
    $prefix = 'INV-' . $yearMonth . '-';

    $latestInvoice = Payment::where('invoice_id', 'like', $prefix . '%')
        ->orderBy('invoice_id', 'desc')
        ->first();

    if ($latestInvoice && preg_match('/-(\d+)$/', $latestInvoice->invoice_id, $matches)) {
        $nextNumber = intval($matches[1]) + 1;
    } else {
        $nextNumber = 1;
    }

    $invoiceId = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    // ==== Save Payment ====
    Payment::create([
       'submission_id' => $submissionId,
    'invoice_id' => $invoiceId,  
    'amount_total' => $totalAmount,
    'rate_total' => 0,
    'nbt_total' => 0,
    'vat_total' => 0,
    'entry_count' => count($cart),
    'permit_type' => $cart[0]['type'] ?? 'unknown',
    'status' => 'Paid',
    'payment_date' => now(),
    'paid_at' => now(),
    ]);

    session()->forget(['permit_cart', 'company_name', 'company_address', 'submission_id', 'payment_total']);

    return redirect()->route('payment.invoice', ['submission_id' => $submissionId]);
}


public function invoice($submissionId)
{
    $payment = Payment::where('submission_id', $submissionId)->firstOrFail();
    $permits = Permit::where('submission_id', $submissionId)->get();

    return view('payment.invoice', compact('payment', 'permits'));
}

}
