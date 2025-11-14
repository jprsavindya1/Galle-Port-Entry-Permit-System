<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VehiclePermit; // Changed from Permit
use App\Models\MonthlyPermit;  
use Illuminate\Support\Str;
use App\Models\Vehicle; 
use App\Models\Company;
use App\Models\Designation;
use App\Models\Reason;
use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Helpers\IdGeneratorHelper;
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
        'doc_revenue_licence' => 'nullable|boolean',
        'doc_insurance' => 'nullable|boolean',
    ]);

    // Ensure company_address exists
    $validated['company_address'] = $validated['company_address'] ?? '';
    
    // Convert checkbox values (checkboxes only send value if checked, otherwise null)
    $validated['doc_revenue_licence'] = $request->has('doc_revenue_licence') ? 1 : 0;
    $validated['doc_insurance'] = $request->has('doc_insurance') ? 1 : 0;

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

    // Check for duplicate vehicle number in session cart
    foreach ($cart as $existingEntry) {
        if (strtolower(trim($existingEntry['vehicle_number'])) === strtolower(trim($validated['vehicle_number']))) {
            return redirect()->route('permit.vehicle')
                ->withErrors(['vehicle_number' => 'This vehicle number is already added to the cart. Cannot add duplicate entries.'])
                ->withInput();
        }
    }

    // Convert pass_type array to comma-separated string (if you still use pass_type)
    if (isset($validated['pass_type']) && is_array($validated['pass_type'])) {
        $validated['pass_type'] = implode(',', $validated['pass_type']);
    }

    // Application number will be generated on submission (no gaps for abandoned carts)

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
    $sslRate = $settings->ssl ?? 2.5;   // SSL % (e.g., 2.5)
    $vatRate = $settings->vat ?? 18;    // VAT % (e.g., 18)

    foreach ($cart as $item) {
        $item['type'] = 'VH';

        $vehicle = Vehicle::find($item['vehicle_type']);
        if (!$vehicle) continue;

        $days = \Carbon\Carbon::parse($item['from_date'])->diffInDays(\Carbon\Carbon::parse($item['to_date'])) + 1;

        $tRate = $vehicle->rate * $days;

        if ($item['issue_type'] === 'free') {
            $tRate = 0;
            $ssl = 0;
            $vat = 0;
            $amount = 0;
        } else {
            // Original formula: SSL = (tRate / 97.5) * 2.5
            // Convert readable SSL% to original format: (100 - SSL%)
            $sslDivisor = 100 - $sslRate;
            $dblNSSL = ($tRate / $sslDivisor) * $sslRate;
            $ssl = round($dblNSSL, 2);
            
            // VAT calculation uses unrounded SSL (original formula)
            $dblVAT = (($tRate + $dblNSSL) / 100) * $vatRate;
            $vat = round($dblVAT, 2);
            
            $amount = round($tRate + $ssl + $vat, 2);
        }

        $totalPayment += $amount;

        $detailedPayments[] = [
            'entry' => $item,
            'rate'  => $tRate,
            'ssl'   => $ssl,
            'vat'   => $vat,
            'total' => $amount,
        ];


    }

    return view('permit.payment_summary', compact('cart', 'detailedPayments', 'totalPayment', 'submissionId'));
}


/**
 * Show form pre-filled with vehicle permit data from session cart by index.
 */
public function editVehicleSessionEntry($index)
{
    $cart = session()->get('vehicle_permit_cart', []);

    if (!isset($cart[$index])) {
        return redirect()->route('permit.vehicle')->with('error', 'Vehicle permit entry not found.');
    }

    // Use the same variable name the Blade expects
    $permit = $cart[$index];

    // Provide lists for selects used in the view
    $vehicles = \App\Models\Vehicle::all();
    $companies = \App\Models\Company::all();
    $reasons = \App\Models\Reason::orderBy('name')->get();

    // Preserve company session values (if any)
    $companyName = session('vehicle_company_name');
    $companyAddress = session('vehicle_company_address');

    return view('permit.edit_vehicle_session_entry', compact(
        'permit',
        'index',
        'vehicles',
        'companies',
        'reasons',
        'companyName',
        'companyAddress'
    ));
}

/**
 * Update a session entry at $index with validated data.
 */
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
        // company_name/company_address typically come from session or hidden inputs
        'company_name' => 'nullable|string',
        'company_address' => 'nullable|string',
        'remarks' => 'nullable|string',
        'reason' => 'required|string',
        'insurance_number' => 'nullable|string',
    ]);

    $cart = session()->get('vehicle_permit_cart', []);

    if (!isset($cart[$index])) {
        return redirect()->route('permit.vehicle')->with('error', 'Vehicle permit entry not found.');
    }

    // Ensure company fields are preserved if not submitted (hidden inputs in form)
    $validated['company_name'] = $validated['company_name'] ?? session('vehicle_company_name');
    $validated['company_address'] = $validated['company_address'] ?? session('vehicle_company_address');

    // If you previously used pass_type you may want to normalize it here:
    if (isset($validated['pass_type']) && is_array($validated['pass_type'])) {
        $validated['pass_type'] = implode(',', $validated['pass_type']);
    }

    $cart[$index] = $validated;

    // Save back to session
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

    $conflict = VehiclePermit::where('status', 'active')
        ->where('vehicle_number', $data['vehicle_number'])
        ->where(function ($query) use ($data) {
            $query->whereBetween('from_date', [$data['from_date'], $data['to_date']])
                  ->orWhereBetween('to_date', [$data['from_date'], $data['to_date']])
                  ->orWhere(function ($q) use ($data) {
                      $q->where('from_date', '<=', $data['from_date'])
                        ->where('to_date', '>=', $data['to_date']);
                  });
        })
        ->first();

    if ($conflict) {
        $fromDate = \Carbon\Carbon::parse($conflict->from_date)->format('d M Y');
        $toDate = \Carbon\Carbon::parse($conflict->to_date)->format('d M Y');
        return response()->json([
            'available' => false, 
            'message' => "This vehicle already has an ACTIVE VEHICLE PERMIT (ID: {$conflict->permit_id}) valid from {$fromDate} to {$toDate}. Please choose different dates."
        ]);
    }

    return response()->json(['available' => true, 'message' => 'Success!']);
}

/**
 * Fetch vehicle details from database based on vehicle number
 */
public function fetchVehicleDetails(Request $request)
{
    try {
        $vehicleNumber = $request->input('vehicle_number');
        
        if (empty($vehicleNumber)) {
            return response()->json([
                'found' => false,
                'message' => 'Vehicle number is required'
            ]);
        }

        // Find the most recent permit with this vehicle number
        $permit = VehiclePermit::where('vehicle_number', $vehicleNumber)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($permit) {
            return response()->json([
                'found' => true,
                'data' => [
                    'revenue_license_number' => $permit->revenue_license_number,
                    'insurance_number' => $permit->insurance_number,
                    'owner_name' => $permit->owner_name,
                    'owner_address' => $permit->owner_address,
                ]
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'No records found for this vehicle number'
        ]);

    } catch (\Exception $e) {
        \Log::error('Fetch vehicle details failed: ' . $e->getMessage());
        return response()->json([
            'found' => false,
            'message' => 'Server error occurred.'
        ], 500);
    }
}

public function submitAllVehicle(Request $request)
{
    $cart = session()->get('vehicle_permit_cart', []);

    if (empty($cart)) {
        return redirect()->route('permit.vehicle')->with('error', 'No vehicle permits to submit.');
    }

    // Generate unique submission ID using collision-free helper
    $submissionId = IdGeneratorHelper::generateSubmissionId();

    // Generate application numbers for all permits in batch (collision-free)
    $applicationNumbers = IdGeneratorHelper::generateMultipleApplicationNumbers(count($cart));
    
    // Add submission ID, application number, and permit type to each entry
    foreach ($cart as $index => $entry) {
        $entry['submission_id'] = $submissionId;
        $entry['application_number'] = $applicationNumbers[$index];
        $entry['type'] = 'VH';
        $entry['permit_id'] = $this->generatePermitId('VH'); // ✅ New yearly-reset permit ID
        $cart[$index] = $entry;
    }

 // Store updated cart to new session key for payment step
    session(['payment_cart' => $cart]);
    session(['payment_submission_id' => $submissionId]);

    return redirect()->route('payment.summary');
}

}
