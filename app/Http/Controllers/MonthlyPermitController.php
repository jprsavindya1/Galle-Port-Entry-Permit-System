<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit; 
use Illuminate\Support\Str;

class MonthlyPermitController extends PermitController
{
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
        $entry['permit_id'] = $this->generatePermitId($type); 

        Permit::create($entry);
    }

    session()->forget(['monthly_permit_cart', 'monthly_company_name', 'monthly_company_address']);

    return redirect()->route('permit.monthly')->with('success', 'All monthly permits submitted successfully!');
}

}
