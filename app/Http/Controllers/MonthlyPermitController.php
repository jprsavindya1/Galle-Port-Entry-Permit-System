<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit;
use App\Models\MonthlyPermit;  
use Illuminate\Support\Str;
use App\Models\Company;
use App\Models\Designation;
use App\Models\Reason;
use App\Models\Payment;
class MonthlyPermitController extends PermitController
{
    /*********************************************************
     ****************** Monthly Permits***********************
     *********************************************************/

    public function createMonthly()
    {
        $cart = session()->get('monthly_permit_cart', []);

    if (empty($cart)) {
        session()->forget(['monthly_company_name', 'monthly_company_address']);
        $companyName = null;
        $companyAddress = null;
    } else {
        $companyName = session('monthly_company_name');
        $companyAddress = session('monthly_company_address');
    }

    $companies = Company::all(); // fetching companies
    $designations = Designation::all();// fetching designations
    $reasons = Reason::orderBy('name')->get();
    // Add companies to the compact array
    return view('permit.monthly', compact('cart', 'companyName', 'companyAddress', 'companies', 'designations', 'reasons'));
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

         // If company info already stored in session, enforce consistency across entries

    $sessionCompanyName = strtolower(trim(session('monthly_company_name')));
    $sessionCompanyAddress = strtolower(trim(session('monthly_company_address') ?? ''));

    $newCompanyName = strtolower(trim($validated['company_name']));
    $newCompanyAddress = strtolower(trim($validated['company_address'] ?? ''));

if (session()->has('monthly_company_name')) {
    if ($newCompanyName !== $sessionCompanyName || $newCompanyAddress !== $sessionCompanyAddress) {
        return redirect()->route('permit.monthly')
            ->withErrors(['company_name' => 'All entries must have the same company name and address.'])
            ->withInput();
    }
} else {
    session(['monthly_company_name' => $validated['company_name']]);
    session(['monthly_company_address' => $validated['company_address']]);
}


        // Convert pass_type array to comma-separated string for storage
        $validated['pass_type'] = implode(',', $validated['pass_type']);

        // Add the validated permit entry to session cart
        $cart[] = $validated;
        session(['monthly_permit_cart' => $cart]);

        return redirect()->route('permit.monthly')->with('success', 'Monthly Permit entry added to list.');
    }
/*
     * Submit all permit entries stored in the session cart to the database.
     * Generates a unique submission ID to group these permits as a chunk.
     * Clears the session cart and company info after successful submission.
     */
    public function paymentMonthlySummary()
    {
        $cart = session()->get('payment_cart', []);
        $submissionId = session('payment_submission_id');

        if (empty($cart) || !$submissionId) {
            return redirect()->route('permit.monthly')->with('error', 'No data available for monthly payment summary.');
        }

        $totalPayment = 0;
        $detailedPayments = [];

        $settings = PaymentSetting::first();

        $prices = [
            'onboard' => $settings->price_onboard ?? 100,
            'afloat' => $settings->price_afloat ?? 80,
            'ashore' => $settings->price_ashore ?? 50,
        ];

        $nbtRate = $settings->nbt ?? 2;
        $vatRate = $settings->vat ?? 15;

        foreach ($cart as $item) {
            $days = \Carbon\Carbon::parse($item['from_date'])->diffInDays($item['to_date']) + 1;

            $passes = explode(',', $item['pass_type']);
            $rate = 0;

            foreach ($passes as $pass) {
                $rate += ($prices[$pass] ?? 0);
            }

            $tRate = $rate * $days;

            if ($item['issue_type'] === 'free') {
                $tRate = 0;
                $nbt = 0;
                $vat = 0;
                $amount = 0;
            } else {
                $nbt = round(($tRate / 98) * $nbtRate, 2);
                $vat = round((($tRate + $nbt) / 100) * $vatRate, 2);
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

        return view('permit.payment_summary', compact('cart', 'detailedPayments', 'totalPayment', 'submissionId'));
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
        return redirect()->route('permit.monthly')->with('message', 'All entries removed.');
    }

    return redirect()->route('payment.summary')->with('message', 'Entry removed.');
}

/*
     * Submit all permit entries stored in the session cart to the database.
     * Generates a unique submission ID to group these permits as a chunk.
     * Clears the session cart and company info after successful submission.
     */
   public function submitAllMonthly(Request $request)
{
    $cart = session()->get('monthly_permit_cart', []);

    if (empty($cart)) {
        return redirect()->route('permit.monthly')->with('error', 'No permit entries to submit.');
    }

    $datePrefix = now()->format('Ymd'); // e.g., 20250901
    $type = 'MP'; // Monthly Permit

    $nextNumber = 1001;

    // Find the latest submission_id in BOTH permits and payments
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


    /**
     * Check if a permit can be issued without conflicts.
     */
    public function checkMonthlyAvailability(Request $request)
{
    // Determine if this is an edit session check
    $isEditSession = $request->input('session_edit', false);

    // Validate input
    $data = $request->validate([
        'id_type' => 'required|string',
        'id_number' => 'required|string',
        'full_name' => 'required|string',
        'initials' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date',
        // Only require company_name if NOT edit session
        'company_name' => $isEditSession ? 'nullable|string' : 'required|string',
    ]);

    // Blacklist check
    if ($reason = $this->isBlacklisted($data)) {
        return response()->json([
            'available' => false, 
            'message' => "Blacklisted: $reason"
        ]);
    }

    // Only check against MONTHLY permits
    $conflict = Permit::where('type', 'MP')
    ->where('status', 'active')
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
        return response()->json([
            'available' => false, 
            'message' => 'Monthly permit NOT available for this person or date range.'
        ]);
    }

    return response()->json([
        'available' => true, 
        'message' => 'Monthly permit available!'
    ]);
}

public function editMonthlySessionEntry($index)
    {
        
    // Get the monthly permit cart from session
    $cart = session()->get('monthly_permit_cart', []);

    // Check if the index exists in the cart
    if (!isset($cart[$index])) {
        return redirect()->route('permit.monthly')->with('error', 'Permit entry not found.');
    }

    // Get the specific permit entry
    $permit = $cart[$index];

    // Fetch dropdown data
    $reasons = Reason::orderBy('name')->get();
    $companies = Company::all();
    $designations = Designation::all();

    // Get company name and address from the permit entry (fallback to session if needed)
    $companyName = $permit['company_name'] ?? session('monthly_company_name', null);
    $companyAddress = $permit['company_address'] ?? session('monthly_company_address', null);

    // Pass all necessary variables to the edit view
    return view('permit.edit_monthly_session_entry', compact(
        'permit','index','reasons','companies','designations','companyName','companyAddress'));
}

   /**
 * Update monthly permit entry in session cart by index.
 */
public function updateMonthlySessionEntry(Request $request, $index)
{
    // Check if this update comes from an edit session (flag from JS)
    $isEditSession = $request->has('session_edit') && $request->session_edit;

    $validated = $request->validate([
        'id_type' => 'in:NIC',
        'id_number' => 'required|string',
        'from_date' => 'required|date',
        'to_date' => 'required|date|after_or_equal:from_date',
        'full_name' => 'required|string',
        'initials' => 'required|string',
        'designation' => 'nullable|string',
        'company_name' => $isEditSession ? 'nullable|string' : 'required|string', 
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
        return redirect()->route('permit.monthly')->with('error', 'Permit entry not found.');
    }

    // Only check company consistency if NOT editing in-session
    if (!$isEditSession) {
        $sessionCompanyName = strtolower(trim(session('monthly_company_name')));
        $sessionCompanyAddress = strtolower(trim(session('monthly_company_address') ?? ''));

        $newCompanyName = strtolower(trim($validated['company_name']));
        $newCompanyAddress = strtolower(trim($validated['company_address'] ?? ''));

        if ($newCompanyName !== $sessionCompanyName || $newCompanyAddress !== $sessionCompanyAddress) {
            return redirect()->route('permit.monthly')
                ->withErrors(['company_name' => 'Company name and address must match existing entries.'])
                ->withInput();
        }
    }

    // Implode pass_type array to string
    $validated['pass_type'] = implode(',', $validated['pass_type']);

    // Update the session entry
    $cart[$index] = $validated;
    session(['monthly_permit_cart' => $cart]);

    return redirect()->route('permit.monthly')->with('success', 'Permit entry updated successfully.');
}


public function removeMonthlySessionEntry($index)
{
    $cart = session('monthly_permit_cart', []);

    if (isset($cart[$index])) {
        unset($cart[$index]);
    }

    // Reindex the array
    $cart = array_values($cart);

    session(['monthly_permit_cart' => $cart]);

    if (count($cart) === 0) {
        return redirect()->route('permit.monthly')->with('message', 'All entries removed.');
    }

    return redirect()->route('permit.monthly')->with('message', 'Entry removed.');
}

       public function createMonthlyPermit()
{
    $companies = Company::all(); // Fetch all companies
    $companyName = old('company_name', session('monthly_company_name')); 
    $companyAddress = old('company_address', session('monthly_company_address')); 
    return view('permits.monthly', compact('companies', 'companyName', 'companyAddress'));
}

}
