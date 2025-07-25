<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit; 
use Illuminate\Support\Str;

class TemporaryPermitController extends PermitController
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
    $datePrefix = now()->format('Ymd'); // e.g., 20250723
    $type = 'TP'; // Temporary Permit

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
        $entry['type'] = $type;
        $entry['permit_id'] = $this->generatePermitId($type); // <--- Add permit_id here

        Permit::create($entry);
    }

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
        'company_name' => 'nullable|string',
    ]);

    // Blacklist check
    if ($reason = $this->isBlacklisted($data)) {
        return response()->json(['available' => false, 'message' => "Blacklisted: $reason"]);
    }

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
}
