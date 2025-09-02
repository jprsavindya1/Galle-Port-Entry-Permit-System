<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit; 
use App\Models\MonthlyPermit;  
use Illuminate\Support\Str;
use App\Models\Vehicle; 
class VehiclePermitController extends PermitController
{
   ////////////////////////////////////vehicle////////////////////////////

   public function createVehicle()
{
     $cart = session()->get('vehicle_permit_cart', []);
    $vehicles = Vehicle::all(); // fetch all vehicles for dropdown
    return view('permit.vehicle', compact('cart', 'vehicles'));
}
public function addVehicleToSession(Request $request)
{
    $validated = $request->validate([
        'vehicle_name' => 'required|string',
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
    $cart[] = $validated;
    session(['vehicle_permit_cart' => $cart]);

    return redirect()->route('permit.vehicle')->with('success', 'Vehicle permit added to list.');
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
        'vehicle_name' => 'required|string',
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
    $latest = Permit::where('submission_id', 'like', $datePrefix . $type . '%')
                    ->orderBy('submission_id', 'desc')
                    ->first();

    $nextNumber = 1001;

    if ($latest) {
        $lastCounter = (int)substr($latest->submission_id, -4);
        $nextNumber = $lastCounter + 1;
    }

    $submissionId = $datePrefix . $type . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    foreach ($cart as $entry) {
        if (isset($entry['pass_type']) && is_array($entry['pass_type'])) {
            $entry['pass_type'] = implode(',', $entry['pass_type']);
        }

        $entry['submission_id'] = $submissionId;
        $entry['type'] = $type;
        $entry['permit_id'] = $this->generatePermitId($type); 

        Permit::create($entry);
    }

    session()->forget('vehicle_permit_cart');

    return redirect()->route('permit.vehicle')->with('success', 'All vehicle permits submitted!');
}

}
