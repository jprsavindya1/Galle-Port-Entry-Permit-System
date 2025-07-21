<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Permit;

class PermitController extends Controller
{
    /*
     * Show the temporary permit form along with permits currently in session cart.
     */
    public function createTemporary()
    {
        $cart = session()->get('permit_cart', []);
        $companyName = session('company_name');
        $companyAddress = session('company_address');

        return view('permit.temporary', compact('cart', 'companyName', 'companyAddress'));
    }

    /*
     * Add a permit entry to the session cart.
     * Validates input and enforces that all entries share the same company info.
     */
    public function addToSession(Request $request)
    {
        $validated = $request->validate([
            'id_type' => 'required|string',
            'id_number' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'full_name' => 'required|string',
            'initials' => 'required|string',
            'designation' => 'nullable|string',
            'company_name' => 'required|string',
            'company_address' => 'nullable|string',
            'residence_address' => 'nullable|string',
            'pass_type' => 'required|array|min:1',
            'pass_type.*' => 'in:onboard,afloat,ashore',
            'issue_type' => 'required|string|in:free,payment',
            'reason' => 'required|string',
        ]);

        $cart = session()->get('permit_cart', []);

        // If company info already stored in session, enforce consistency across entries
        if (session()->has('company_name')) {
            if ($validated['company_name'] !== session('company_name') ||
                ($validated['company_address'] ?? '') !== session('company_address')) {
                return redirect()->route('permit.temporary')
                    ->withErrors(['company_name' => 'All entries must have the same company name and address.'])
                    ->withInput();
            }
        } else {
            // Store company info in session if not already present
            session(['company_name' => $validated['company_name']]);
            session(['company_address' => $validated['company_address']]);
        }

        // Convert pass_type array to comma-separated string for storage
        $validated['pass_type'] = implode(',', $validated['pass_type']);

        // Add the validated permit entry to session cart
        $cart[] = $validated;
        session(['permit_cart' => $cart]);

        return redirect()->route('permit.temporary')->with('success', 'Permit entry added to list.');
    }

    /*
     * Display a summary page showing all permit entries in the session cart,
     * including a total payment calculation based on pass types.
     */
    public function showSummary()
    {
        $cart = session()->get('permit_cart', []);

        $totalPayment = 0;
        foreach ($cart as $item) {
            if ($item['issue_type'] === 'payment') {
                $prices = [
                    'onboard' => 100,
                    'afloat' => 80,
                    'ashore' => 50,
                ];

                $passes = explode(',', $item['pass_type']);
                foreach ($passes as $pass) {
                    $totalPayment += $prices[$pass] ?? 0;
                }
            }
        }

        return view('permit.summary', compact('cart', 'totalPayment'));
    }

    /*
     * Submit all permit entries stored in the session cart to the database.
     * Generates a unique submission ID to group these permits as a chunk.
     * Clears the session cart and company info after successful submission.
     */
    public function submitAll(Request $request)
    {
        $cart = session()->get('permit_cart', []);

        if (empty($cart)) {
            return redirect()->route('permit.temporary')->with('error', 'No permit entries to submit.');
        }

        // Create unique submission ID to group permits
        $datePrefix = now()->format('Ymd'); // e.g., 20250720
        $type = 'TP'; // Temporary Permit type

        $latest = Permit::where('submission_id', 'like', $datePrefix . $type . '%')
                ->orderBy('submission_id', 'desc')
                ->first();

        $nextNumber = 1001;

        if ($latest) {
            $lastId = $latest->submission_id;
            $lastCounter = (int)substr($lastId, -4);
            $nextNumber = $lastCounter + 1;
        }

        $submissionId = $datePrefix . $type . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        foreach ($cart as $entry) {
            $entry['pass_type'] = is_array($entry['pass_type']) ? implode(',', $entry['pass_type']) : $entry['pass_type'];
            $entry['submission_id'] = $submissionId;
            $entry['type'] = $type; // TP / MP / VP

            Permit::create($entry);
        }

        // Clear session data after submission
        session()->forget(['permit_cart', 'company_name', 'company_address']);

        return redirect()->route('permit.temporary')->with('success', 'All permits submitted successfully!');
    }

    /**
     * Check if a permit can be issued without conflicts.
     */
    public function checkAvailability(Request $request)
    {
        $data = $request->validate([
            'id_type' => 'required|string',
            'id_number' => 'required|string',
            'full_name' => 'required|string',
            'initials' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        // Query for existing permits that conflict by person or ID and date range
        $conflict = Permit::where(function ($query) use ($data) {
            $query->where(function ($q) use ($data) {
                $q->where('full_name', $data['full_name'])
                  ->where('initials', $data['initials']);
            })
            ->orWhere('id_number', $data['id_number']);
        })
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
            return response()->json(['available' => false, 'message' => 'Permit NOT available for this period or person.']);
        }

        return response()->json(['available' => true, 'message' => 'Permit available!']);
    }

    /*
     * Show paginated list of submitted permits.
     * Supports search filtering by company name, ID number, or full name.
     */
    public function submittedList(Request $request)
    {
        $query = Permit::query();

        if ($request->has('q')) {
            $search = $request->input('q');
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%$search%")
                  ->orWhere('id_number', 'like', "%$search%")
                  ->orWhere('full_name', 'like', "%$search%");
            });
        }

        $permits = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('permit.submitted', compact('permits'));
    }

    /*
     * Show the edit form for a single permit entry (from DB).
     */
    public function edit(Permit $permit)
    {
        return view('permit.edit', compact('permit'));
    }

    /*
     * Update a permit entry with validated data.
     */
    public function update(Request $request, Permit $permit)
{
    $validated = $request->validate([
        'id_type' => 'required|string',
        'id_number' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'full_name' => 'required|string',
        'initials' => 'required|string',
        'designation' => 'nullable|string',
        'company_name' => 'required|string',
        'company_address' => 'nullable|string',
        'residence_address' => 'nullable|string',
        'pass_type' => 'required|array',          
        'pass_type.*' => 'in:onboard,afloat,ashore', 
        'issue_type' => 'required|string|in:free,payment',
        'reason' => 'required|string',
    ]);

   
    $validated['pass_type'] = implode(',', $validated['pass_type']);

    $permit->update($validated);

    return redirect()->route('permits.submitted')->with('success', 'Permit updated successfully.');
}


    /*
     * Delete a permit entry from DB
     */
    public function destroy(Permit $permit)
    {
        $permit->delete();
        return redirect()->route('permits.submitted')->with('success', 'Permit deleted successfully.');
    }

    /*
     * Show form pre-filled with permit data from session cart by index (Temporary permits).
     */
    public function editSessionEntry($index)
    {
        $cart = session()->get('permit_cart', []);

        if (!isset($cart[$index])) {
            return redirect()->route('permit.temporary')->with('error', 'Permit entry not found.');
        }

        $permit = $cart[$index];

        // Pass $index to form for PUT route
        return view('permit.edit_session_entry', compact('permit', 'index'));
    }

    /*
     * Update the permit entry in session cart by index (Temporary permits).
     */
    public function updateSessionEntry(Request $request, $index)
    {
        $validated = $request->validate([
            'id_type' => 'required|string',
            'id_number' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'full_name' => 'required|string',
            'initials' => 'required|string',
            'designation' => 'nullable|string',
            'company_name' => 'required|string',
            'company_address' => 'nullable|string',
            'residence_address' => 'nullable|string',
            'pass_type' => 'required|array|min:1',
            'pass_type.*' => 'in:onboard,afloat,ashore',
            'issue_type' => 'required|string|in:free,payment',
            'reason' => 'required|string',
        ]);

        $cart = session()->get('permit_cart', []);

        if (!isset($cart[$index])) {
            return redirect()->route('permit.temporary')->with('error', 'Permit entry not found.');
        }

        // Implode pass_type array to string
        $validated['pass_type'] = implode(',', $validated['pass_type']);

        // Update the session entry
        $cart[$index] = $validated;
        session(['permit_cart' => $cart]);

        return redirect()->route('permit.temporary')->with('success', 'Permit entry updated successfully.');
    }

    /*********************************************************
     * Monthly Permits
     *********************************************************/

    public function createMonthly()
    {
        $cart = session()->get('monthly_permit_cart', []);
        $companyName = session('monthly_company_name');
        $companyAddress = session('monthly_company_address');
        return view('permit.monthly', compact('cart', 'companyName', 'companyAddress'));
    }

    public function addMonthlyToSession(Request $request)
    {
        $validated = $request->validate([
            'id_type' => 'in:NIC', // only NIC
            'id_number' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'full_name' => 'required|string',
            'initials' => 'required|string',
            'designation' => 'nullable|string',
            'company_name' => 'required|string',
            'company_address' => 'nullable|string',
            'residence_address' => 'nullable|string',
            'pass_type' => 'required|array|min:1',
            'pass_type.*' => 'in:onboard,afloat,ashore',
            'issue_type' => 'required|string|in:free,payment',
            'reason' => 'required|string',
            'police_issue_date' => 'required|date',
            'police_expire_date' => 'required|date|after:police_issue_date',
        ]);

        $cart = session()->get('monthly_permit_cart', []);

        // Check consistency of company info
        if (session()->has('monthly_company_name')) {
            if (
                $validated['company_name'] !== session('monthly_company_name') ||
                ($validated['company_address'] ?? '') !== session('monthly_company_address')
            ) {
                return redirect()->route('permit.monthly')
                    ->withErrors(['company_name' => 'All entries must have the same company name and address.'])
                    ->withInput();
            }
        } else {
            session(['monthly_company_name' => $validated['company_name']]);
            session(['monthly_company_address' => $validated['company_address']]);
        }

        $validated['pass_type'] = implode(',', $validated['pass_type']);
        $cart[] = $validated;
        session(['monthly_permit_cart' => $cart]);

        return redirect()->route('permit.monthly')->with('success', 'Monthly permit entry added.');
    }

    /*
     * Show form pre-filled with monthly permit data from session cart by index.
     */
    public function editMonthlySessionEntry($index)
    {
        $cart = session()->get('monthly_permit_cart', []);

        if (!isset($cart[$index])) {
            return redirect()->route('permit.monthly')->with('error', 'Monthly permit entry not found.');
        }

        $permit = $cart[$index];

        return view('permit.edit_monthly_session_entry', ['entry' => $permit, 'index' => $index]);

    }

    /*
     * Update monthly permit entry in session cart by index.
     */
    public function updateMonthlySessionEntry(Request $request, $index)
    {
        $validated = $request->validate([
            'id_type' => 'in:NIC',
            'id_number' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'full_name' => 'required|string',
            'initials' => 'required|string',
            'designation' => 'nullable|string',
            'company_name' => 'required|string',
            'company_address' => 'nullable|string',
            'residence_address' => 'nullable|string',
            'pass_type' => 'required|array|min:1',
            'pass_type.*' => 'in:onboard,afloat,ashore',
            'issue_type' => 'required|string|in:free,payment',
            'reason' => 'required|string',
            'police_issue_date' => 'required|date',
            'police_expire_date' => 'required|date|after:police_issue_date',
        ]);

        $cart = session()->get('monthly_permit_cart', []);

        if (!isset($cart[$index])) {
            return redirect()->route('permit.monthly')->with('error', 'Monthly permit entry not found.');
        }

        $validated['pass_type'] = implode(',', $validated['pass_type']);

        $cart[$index] = $validated;
        session(['monthly_permit_cart' => $cart]);

        return redirect()->route('permit.monthly')->with('success', 'Monthly permit entry updated successfully.');
    }

    public function checkMonthlyAvailability(Request $request)
    {
        $data = $request->validate([
            'id_number' => 'required|string',
            'full_name' => 'required|string',
            'initials' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
        ]);

        $conflict = Permit::where('type', 'MP') // monthly permits only
            ->where(function ($query) use ($data) {
                $query->where(function ($q) use ($data) {
                    $q->where('full_name', $data['full_name'])
                      ->where('initials', $data['initials']);
                })
                ->orWhere('id_number', $data['id_number']);
            })
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
            return response()->json(['available' => false, 'message' => 'Monthly permit NOT available for this period or person.']);
        }

        return response()->json(['available' => true, 'message' => 'Monthly permit available!']);
    }

    public function submitAllMonthly(Request $request)
    {
        $cart = session()->get('monthly_permit_cart', []);

        if (empty($cart)) {
            return redirect()->route('permit.monthly')->with('error', 'No permit entries to submit.');
        }

        $datePrefix = now()->format('Ymd'); // Example: 20250720
        $type = 'MP';

        // Find the latest submission ID for this date and type
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
            $entry['pass_type'] = is_array($entry['pass_type']) ? implode(',', $entry['pass_type']) : $entry['pass_type'];
            $entry['submission_id'] = $submissionId;
            $entry['type'] = $type;

            Permit::create($entry);
        }

                session()->forget(['monthly_permit_cart', 'monthly_company_name', 'monthly_company_address']);

                return redirect()->route('permit.monthly')->with('success', 'All monthly permits submitted successfully!');
    }



   ////////////////////////////////////vehicle

   public function createVehicle()
{
    $cart = session()->get('vehicle_permit_cart', []);
    return view('permit.vehicle', compact('cart'));
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
public function submitAllVehicle(Request $request)
{
    $cart = session()->get('vehicle_permit_cart', []);

    if (empty($cart)) {
        return redirect()->route('permit.vehicle')->with('error', 'No vehicle permits to submit.');
    }

    $datePrefix = now()->format('Ymd');
    $type = 'VP';

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
        $entry['submission_id'] = $submissionId;
        $entry['type'] = $type;
        Permit::create($entry);
    }

    session()->forget('vehicle_permit_cart');
    return redirect()->route('permit.vehicle')->with('success', 'All vehicle permits submitted!');
}
public function checkVehicleAvailability(Request $request)
{
    $data = $request->validate([
        'vehicle_number' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
    ]);

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

}
