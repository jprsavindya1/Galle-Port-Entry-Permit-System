<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TemporaryPermit; // Changed from Permit
use App\Models\MonthlyPermit;  
use App\Models\Company;
use Illuminate\Support\Str;
use App\Models\Designation;
use App\Models\Reason;
use App\Models\PaymentSetting;
class TemporaryPermitController extends PermitController
{
    /*
     * Show the temporary permit form along with permits currently in session cart.
     */
 public function createTemporary()
{
    $cart = session()->get('temporary_permit_cart', []);

    if (empty($cart)) {
        session()->forget(['temporary_company_name', 'temporary_company_address']);
        $companyName = null;
        $companyAddress = null;
    } else {
        $companyName = session('temporary_company_name');
        $companyAddress = session('temporary_company_address');
    }

    $companies = Company::all(); // fetching companies
    $designations = Designation::all();// fetching designations
    $reasons = Reason::orderBy('name')->get();
    // Add companies to the compact array
    return view('permit.temporary', compact('cart', 'companyName', 'companyAddress', 'companies', 'designations', 'reasons'));
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
            'doc_nic' => 'nullable|boolean',
            'doc_passport' => 'nullable|boolean',
            'doc_driving_licence' => 'nullable|boolean',
        ]);
        
        // Convert checkbox values (checkboxes only send value if checked, otherwise null)
        $validated['doc_nic'] = $request->has('doc_nic') ? 1 : 0;
        $validated['doc_passport'] = $request->has('doc_passport') ? 1 : 0;
        $validated['doc_driving_licence'] = $request->has('doc_driving_licence') ? 1 : 0;

        $cart = session()->get('temporary_permit_cart', []);

        // If company info already stored in session, enforce consistency across entries

    $sessionCompanyName = strtolower(trim(session('temporary_company_name')));
    $sessionCompanyAddress = strtolower(trim(session('temporary_company_address') ?? ''));

    $newCompanyName = strtolower(trim($validated['company_name']));
    $newCompanyAddress = strtolower(trim($validated['company_address'] ?? ''));

if (session()->has('temporary_company_name')) {
    if ($newCompanyName !== $sessionCompanyName || $newCompanyAddress !== $sessionCompanyAddress) {
        return redirect()->route('permit.temporary')
            ->withErrors(['company_name' => 'All entries must have the same company name and address.'])
            ->withInput();
    }
} else {
    session(['temporary_company_name' => $validated['company_name']]);
    session(['temporary_company_address' => $validated['company_address']]);
}

        // Check for duplicate ID number in session cart
        foreach ($cart as $existingEntry) {
            if (strtolower(trim($existingEntry['id_number'])) === strtolower(trim($validated['id_number']))) {
                return redirect()->route('permit.temporary')
                    ->withErrors(['id_number' => 'This ID number is already added to the cart. Cannot add duplicate entries.'])
                    ->withInput();
            }
        }

        // Convert pass_type array to comma-separated string for storage
        $validated['pass_type'] = implode(',', $validated['pass_type']);

        // Add the validated permit entry to session cart
        $cart[] = $validated;
        session(['temporary_permit_cart' => $cart]);

        return redirect()->route('permit.temporary')->with('success', 'Permit entry added to list.');
    }

    /*
     * Display a summary page showing all permit entries in the session cart,
     * including a total payment calculation based on pass types.
     */
    public function showSummary()
    {
        $cart = session()->get('temporary_permit_cart', []);

        $totalPayment = 0;
        $detailedPayments = [];

        // Load payment settings from DB or use defaults
        $settings = PaymentSetting::first();

        $baseRate = $settings->rate ?? 100;
        $sslRate = $settings->ssl ?? 2.5;   // SSL % (e.g., 2.5)
        $vatRate = $settings->vat ?? 18;    // VAT % (e.g., 18)

        foreach ($cart as $item) {
            $days = \Carbon\Carbon::parse($item['from_date'])->diffInDays($item['to_date']) + 1;

            $tRate = $baseRate * $days;

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
                'rate' => $item['issue_type'] === 'free' ? 0 : $tRate,
                'ssl' => $ssl,
                'vat' => $vat,
                'total' => $amount,
            ];
        }

        return view('permit.summary', compact('cart', 'detailedPayments', 'totalPayment'));
    }

    /*
     * Submit all permit entries stored in the session cart to the database.
     * Generates a unique submission ID to group these permits as a chunk.
     * Clears the session cart and company info after successful submission.
     */
    public function paymentSummary()
    {
        $cart = session()->get('payment_cart', []);
        $submissionId = session('payment_submission_id');

        if (empty($cart) || !$submissionId) {
            return redirect()->route('permit.temporary')->with('error', 'No data available for payment summary.');
        }

        $totalPayment = 0;
        $detailedPayments = [];

        $settings = PaymentSetting::first();

        $baseRate = $settings->rate ?? 100;
        $sslRate = $settings->ssl ?? 2.5;   // SSL % (e.g., 2.5)
        $vatRate = $settings->vat ?? 18;    // VAT % (e.g., 18)

        foreach ($cart as $item) {
            $days = \Carbon\Carbon::parse($item['from_date'])->diffInDays($item['to_date']) + 1;

            $tRate = $baseRate * $days;

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
                'rate' => $item['issue_type'] === 'free' ? 0 : $tRate,
                'ssl' => $ssl,
                'vat' => $vat,
                'total' => $amount,
            ];
        }

        return view('permit.payment_summary', compact('cart', 'detailedPayments', 'totalPayment', 'submissionId'));
    }

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
        return redirect()->route('permit.temporary')->with('message', 'All entries removed.');
    }

    return redirect()->route('payment.summary')->with('message', 'Entry removed.');
}

    /*
     * Submit all permit entries stored in the session cart to the database.
     * Generates a unique submission ID to group these permits as a chunk.
     * Clears the session cart and company info after successful submission.
     */
   public function submitAll(Request $request)
{
    $cart = session()->get('temporary_permit_cart', []);

    if (empty($cart)) {
        return redirect()->route('permit.temporary')->with('error', 'No permit entries to submit.');
    }
// Create unique submission ID to group permits
    $datePrefix = now()->format('Ymd');
    $type = 'TP';

    $latest = TemporaryPermit::where('submission_id', 'like', $datePrefix . $type . '%')
        ->orderBy('submission_id', 'desc')
        ->first();

    $nextNumber = 1001;
    if ($latest) {
        $lastId = $latest->submission_id;
        $lastCounter = (int) substr($lastId, -4);
        $nextNumber = $lastCounter + 1;
    }

    $submissionId = $datePrefix . $type . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

    // Add submission ID, type, and yearly-reset permit ID
    foreach ($cart as $index => $entry) {
        $entry['submission_id'] = $submissionId;
        $entry['type'] = $type;
        $entry['permit_id'] = $this->generatePermitId($type); // ✅ New yearly-reset permit ID
        $cart[$index] = $entry;
    }

    session(['payment_cart' => $cart]);
    session(['payment_submission_id' => $submissionId]);

    return redirect()->route('payment.summary');
}

    /**
     * Check if a permit can be issued without conflicts.
     */
   public function checkAvailability(Request $request)
{
    $isEditSession = $request->has('session_edit') && $request->session_edit;

    $data = $request->validate([
        'id_type' => 'required|string',
        'id_number' => 'required|string',
        'full_name' => 'required|string',
        'initials' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date',
        'company_name' => $isEditSession ? 'nullable|string' : 'required|string',
    ]);

    // Blacklist check
    if ($reason = $this->isBlacklisted($data)) {
        return response()->json(['available' => false, 'message' => "Blacklisted: $reason"]);
    }

    // First check for active MONTHLY permits (higher priority)
    $monthlyConflict = MonthlyPermit::where('status', 'active')
        ->where(function ($query) use ($data) {
            $query->where(function ($q) use ($data) {
                $q->where('full_name', $data['full_name'])
                  ->where('initials', $data['initials']);
            })
            ->orWhere('id_number', $data['id_number']);
        })
        ->where(function ($query) use ($data) {
            // Check for ANY overlap (including exact same dates)
            $query->where(function ($q) use ($data) {
                // Existing permit starts before or on new permit end date
                $q->where('from_date', '<=', $data['to_date'])
                  // AND existing permit ends after or on new permit start date
                  ->where('to_date', '>=', $data['from_date']);
            });
        })
        ->first();

    if ($monthlyConflict) {
        $fromDate = \Carbon\Carbon::parse($monthlyConflict->from_date)->format('d M Y');
        $toDate = \Carbon\Carbon::parse($monthlyConflict->to_date)->format('d M Y');
        return response()->json([
            'available' => false,
            'message' => "This person already has an ACTIVE MONTHLY PERMIT (ID: {$monthlyConflict->permit_id}) valid from {$fromDate} to {$toDate}. Temporary permit cannot be issued during this period."
        ]);
    }

    // Then check for other TEMPORARY permit conflicts
    $tempConflict = TemporaryPermit::where('status', 'active')
        ->where(function ($query) use ($data) {
            $query->where(function ($q) use ($data) {
                $q->where('full_name', $data['full_name'])
                  ->where('initials', $data['initials']);
            })
            ->orWhere('id_number', $data['id_number']);
        })
        ->where(function ($query) use ($data) {
            // Check for ANY overlap (including exact same dates)
            $query->where(function ($q) use ($data) {
                // Existing permit starts before or on new permit end date
                $q->where('from_date', '<=', $data['to_date'])
                  // AND existing permit ends after or on new permit start date
                  ->where('to_date', '>=', $data['from_date']);
            });
        })
        ->first();

    if ($tempConflict) {
        $fromDate = \Carbon\Carbon::parse($tempConflict->from_date)->format('d M Y');
        $toDate = \Carbon\Carbon::parse($tempConflict->to_date)->format('d M Y');
        return response()->json([
            'available' => false,
            'message' => "This person already has an ACTIVE TEMPORARY PERMIT (ID: {$tempConflict->permit_id}) valid from {$fromDate} to {$toDate}. Please choose different dates."
        ]);
    }

    return response()->json(['available' => true, 'message' => 'Success!']);
}


public function editSessionEntry($index)
{
    // Get the temporary permit cart from session
    $cart = session()->get('temporary_permit_cart', []);

    // Check if the index exists in the cart
    if (!isset($cart[$index])) {
        return redirect()->route('permit.temporary')->with('error', 'Permit entry not found.');
    }

    // Get the specific permit entry
    $permit = $cart[$index];

    // Fetch dropdown data
    $reasons = Reason::orderBy('name')->get();
    $companies = Company::all();
    $designations = Designation::all();

    // Get company name and address from the permit entry (fallback to session if needed)
    $companyName = $permit['company_name'] ?? session('temporary_company_name', null);
    $companyAddress = $permit['company_address'] ?? session('temporary_company_address', null);

    // Pass all necessary variables to the edit view
    return view('permit.edit_session_entry', compact(
        'permit','index','reasons','companies','designations','companyName','companyAddress'));
}


    /*
     * Update the permit entry in session cart by index (Temporary permits).
     */
    public function updateSessionEntry(Request $request, $index)
{
    // Detect if this is an edit session
    $isEditSession = $request->has('session_edit') && $request->session_edit;

    $validated = $request->validate([
        'id_type' => 'required|string',
        'id_number' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'full_name' => 'required|string',
        'initials' => 'required|string',
        'designation' => 'required|string',
        'company_name' => $isEditSession ? 'nullable|string' : 'required|string', // <-- changed
        'company_address' => 'nullable|string',
        'residence_address' => 'nullable|string',
        'pass_type' => 'required|array|min:1',
        'pass_type.*' => 'in:onboard,afloat,ashore',
        'issue_type' => 'required|string|in:free,payment',
        'reason' => 'required|string',
    ]);

    $cart = session()->get('temporary_permit_cart', []);

    if (!isset($cart[$index])) {
        return redirect()->route('permit.temporary')->with('error', 'Permit entry not found.');
    }

    // Only check company consistency if NOT edit session
    if (!$isEditSession) {
        $sessionCompanyName = strtolower(trim(session('temporary_company_name')));
        $sessionCompanyAddress = strtolower(trim(session('temporary_company_address') ?? ''));

        $newCompanyName = strtolower(trim($validated['company_name']));
        $newCompanyAddress = strtolower(trim($validated['company_address'] ?? ''));

        if ($newCompanyName !== $sessionCompanyName || $newCompanyAddress !== $sessionCompanyAddress) {
            return redirect()->route('permit.temporary')
                ->withErrors(['company_name' => 'Company name and address must match existing entries.'])
                ->withInput();
        }
    }

    // Implode pass_type array to string
    $validated['pass_type'] = implode(',', $validated['pass_type']);

    // Update the session entry
    $cart[$index] = $validated;
    session(['temporary_permit_cart' => $cart]);

    return redirect()->route('permit.temporary')->with('success', 'Permit entry updated successfully.');
}


public function removeTemporaryEntry($index)
{
    $cart = session('temporary_permit_cart', []);

    if (isset($cart[$index])) {
        unset($cart[$index]);
    }

    // Reindex the array
    $cart = array_values($cart);

    session(['temporary_permit_cart' => $cart]);

    if (count($cart) === 0) {
        return redirect()->route('permit.temporary')->with('message', 'All entries removed.');
    }

    return redirect()->route('permit.temporary')->with('message', 'Entry removed.');
}



public function createTemporaryPermit()
{
    $companies = Company::all(); // Fetch all companies
    $companyName = old('company_name', session('temporary_company_name')); // Optional
    $companyAddress = old('company_address', session('temporary_company_address')); // Optional

    return view('permits.temporary', compact('companies', 'companyName', 'companyAddress'));
}



}
