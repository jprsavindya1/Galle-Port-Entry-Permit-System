<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit; 
use App\Models\MonthlyPermit;  
use Illuminate\Support\Str;
use App\Models\Vehicle; 
use App\Models\Company;
use App\Models\Designation;
use App\Models\Reason;
use App\Models\Payment;
use App\Models\PaymentSetting;
class VehiclePermitController extends PermitController
{
   ////////////////////////////////////vehicle////////////////////////////

   public function createVehicle()
{
     $cart = session()->get('vehicle_permit_cart', []);
    $vehicles = Vehicle::all(); // fetch all vehicles for dropdown

    if (empty($cart)) {
        session()->forget(['vehicle_company_name', 'vehicle_company_address']);
        $companyName = null;
        $companyAddress = null;
    } else {
        $companyName = session('vehicle_company_name');
        $companyAddress = session('vehicle_company_address');
    }

    $companies = Company::all(); // fetching companies
    $reasons = Reason::orderBy('name')->get();
    // Add companies to the compact array
    return view('permit.vehicle', compact('cart', 'companyName', 'companyAddress', 'companies', 'vehicles', 'reasons'));
  
}
public function addVehicleToSession(Request $request)
{
    $validated = $request->validate([
        'vehicle_type' => 'required|string',
        'vehicle_number' => 'required|string',
        'revenue_license_number' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'issue_type' => 'required|string|in:free,payment',
        'owner_name' => 'required|string',
        'owner_address' => 'required|string',
        'company_name' => 'required|string',
        'company_address' => 'nullable|string',
        'remarks' => 'nullable|string',
        'reason' => 'required|string',
        'insurance_number' => 'nullable|string',
    ]);

    // Ensure company_address exists
    $validated['company_address'] = $validated['company_address'] ?? '';

    $cart = session()->get('vehicle_permit_cart', []);

    // Enforce company consistency across entries
    $sessionCompanyName = strtolower(trim(session('vehicle_company_name') ?? ''));
    $sessionCompanyAddress = strtolower(trim(session('vehicle_company_address') ?? ''));

    $newCompanyName = strtolower(trim($validated['company_name']));
    $newCompanyAddress = strtolower(trim($validated['company_address']));

    if (session()->has('vehicle_company_name')) {
        if ($newCompanyName !== $sessionCompanyName || $newCompanyAddress !== $sessionCompanyAddress) {
            return redirect()->route('permit.vehicle')
                ->withErrors(['company_name' => 'All entries must have the same company name and address.'])
                ->withInput();
        }
    } else {
        session(['vehicle_company_name' => $validated['company_name']]);
        session(['vehicle_company_address' => $validated['company_address']]);
    }

    // Convert pass_type array to comma-separated string (if you still use pass_type)
    if (isset($validated['pass_type']) && is_array($validated['pass_type'])) {
        $validated['pass_type'] = implode(',', $validated['pass_type']);
    }

    $cart[] = $validated;
    session(['vehicle_permit_cart' => $cart]);

    return redirect()->route('permit.vehicle')->with('success', 'Vehicle Permit entry added to list.');
}


public function paymentVehicleSummary()
{
    $cart = session()->get('vehicle_permit_cart', []);
    $submissionId = session('payment_submission_id');

    if (empty($cart) || !$submissionId) {
        return redirect()->route('permit.vehicle')->with('error', 'No data available for vehicle payment summary.');
    }

    $totalPayment = 0;
    $detailedPayments = [];

    $settings = PaymentSetting::first();
    $sscAmount = $settings->ssc ?? 0;
    $vatRate  = $settings->vat ?? 15;

    foreach ($cart as $item) {
        // Ensure type exists
        $item['type'] = 'VP';

        $vehicle = Vehicle::find($item['vehicle_type']); // vehicle_type stores vehicle ID
        if (!$vehicle) continue;

        $days = \Carbon\Carbon::parse($item['from_date'])->diffInDays(\Carbon\Carbon::parse($item['to_date'])) + 1;

        // Base rate per vehicle × days
        $baseRate = $vehicle->rate * $days;

        if ($item['issue_type'] === 'free') {
            $tRate = 0;
            $ssc = 0;
            $vat = 0;
            $amount = 0;
        } else {
            $tRate = $baseRate;
            $ssc = $sscAmount;
            $vat = round(($tRate + $ssc) * ($vatRate / 100), 2);
            $amount = round($tRate + $ssc + $vat, 2);
        }

        $totalPayment += $amount;

        $detailedPayments[] = [
            'entry' => $item,
            'rate'  => $tRate,
            'nbt'   => 0,          // VP never uses NBT
            'ssc'   => $ssc,       // always set
            'vat'   => $vat,
            'total' => $amount,
        ];
    }

    return view('permit.payment_summary', compact('cart', 'detailedPayments', 'totalPayment', 'submissionId'));
}


public function editVehicleSessionEntry($index)
{
    $cart = session()->get('vehicle_permit_cart', []);

    if (!isset($cart[$index])) {
        return redirect()->route('permit.vehicle')->with('error', 'Vehicle permit entry not found.');
    }

    $entry = $cart[$index];
    return view('permit.edit_vehicle_session_entry', compact('entry', 'index'));
}
public function updateVehicleSessionEntry(Request $request, $index)
{
    $validated = $request->validate([
        'vehicle_type' => 'required|string',
        'vehicle_number' => 'required|string',
        'revenue_license_number' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'issue_type' => 'required|string|in:free,payment',
        'owner_name' => 'required|string',
        'owner_address' => 'required|string',
        'company_name' => 'nullable|string',
        'remarks' => 'nullable|string',
        'insurance_number' => 'nullable|string',
    ]);

    $cart = session()->get('vehicle_permit_cart', []);

    if (!isset($cart[$index])) {
        return redirect()->route('permit.vehicle')->with('error', 'Vehicle permit entry not found.');
    }

    $cart[$index] = $validated;
    session(['vehicle_permit_cart' => $cart]);

    return redirect()->route('permit.vehicle')->with('success', 'Vehicle permit updated.');
}
public function removeVehicleSessionEntry($index)
{
    $cart = session('vehicle_permit_cart', []);

    if (isset($cart[$index])) {
        unset($cart[$index]);
    }

    // Reindex the array
    $cart = array_values($cart);

    session(['vehicle_permit_cart' => $cart]);

    if (count($cart) === 0) {
        return redirect()->route('permit.vehicle')->with('message', 'All entries removed.');
    }

    return redirect()->route('permit.vehicle')->with('message', 'Entry removed.');
}

 /*
     * Show form pre-filled with monthly permit data from session cart by index.
     */
public function removeEntry($index)
{
    $cart = session('payment_cart', []);

    if (isset($cart[$index])) {
        unset($cart[$index]);
    }

    // Clean and reindex the array
    $cart = array_values(array_filter($cart));

    // Update session
    session(['payment_cart' => $cart]);

    if (count($cart) === 0) {
        // Clear submission ID if needed
        session()->forget('payment_submission_id');
        return redirect()->route('permit.vehicle')->with('message', 'All entries removed.');
    }

    return redirect()->route('payment.summary')->with('message', 'Entry removed.');
}
    /*
     ***********  vehicle availability check with number and date *********   
    */
public function checkVehicleAvailability(Request $request)
{
    $data = $request->validate([
        'vehicle_number' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'company_name' => 'nullable|string',
    ]);

    if ($reason = $this->isBlacklisted($data)) {
        return response()->json(['available' => false, 'message' => "Blacklisted: $reason"]);
    }

    $conflict = Permit::where('type', 'VP')
        ->where('vehicle_number', $data['vehicle_number'])
        ->where(function ($query) use ($data) {
            $query->whereBetween('from_date', [$data['from_date'], $data['to_date']])
                  ->orWhereBetween('to_date', [$data['from_date'], $data['to_date']])
                  ->orWhere(function ($q) use ($data) {
                      $q->where('from_date', '<=', $data['from_date'])
                        ->where('to_date', '>=', $data['to_date']);
                  });
        })
        ->exists();

    if ($conflict) {
        return response()->json(['available' => false, 'message' => 'Vehicle permit NOT available for these dates.']);
    }

    return response()->json(['available' => true, 'message' => 'Vehicle permit available.']);
}

public function submitAllVehicle(Request $request)
{
    $cart = session()->get('vehicle_permit_cart', []);

    if (empty($cart)) {
        return redirect()->route('permit.vehicle')->with('error', 'No vehicle permits to submit.');
    }

    $datePrefix = now()->format('Ymd');
    $type = 'VP';

    // Generate submission_id
     $latestPermit = Permit::where('submission_id', 'like', $datePrefix . $type . '%')
        ->orderBy('submission_id', 'desc')
        ->first();

    $latestPayment = Payment::where('submission_id', 'like', $datePrefix . $type . '%')
        ->orderBy('submission_id', 'desc')
        ->first();

    // Determine the highest counter used
    $lastCounter = 0;
    if ($latestPermit) {
        $lastCounter = (int) substr($latestPermit->submission_id, -4);
    }
    if ($latestPayment) {
        $paymentCounter = (int) substr($latestPayment->submission_id, -4);
        if ($paymentCounter > $lastCounter) {
            $lastCounter = $paymentCounter;
        }
    }

    $nextNumber = $lastCounter + 1;
    $submissionId = $datePrefix . $type . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    // Add submission ID and permit type to each entry
    foreach ($cart as $index => $entry) {
        $entry['submission_id'] = $submissionId;
        $entry['type'] = $type;
        $cart[$index] = $entry;
    }

    // Store updated cart to new session key for payment step
    session(['payment_cart' => $cart]);
    session(['payment_submission_id' => $submissionId]);

    return redirect()->route('payment.summary');

}

}
