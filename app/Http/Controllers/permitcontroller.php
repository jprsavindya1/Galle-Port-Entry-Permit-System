<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Permit;
use App\Models\Blacklist;
class PermitController extends Controller
{
    /*
     * Show list of submitted permits.
     * Supports search filtering by company name, ID number, or full name.
     */
   public function submittedList(Request $request)
{
    $query = Permit::query()->with('payment');

    // Search by company, ID, or name
    if ($request->filled('q')) {
        $search = $request->q;
        $query->where(function($q) use ($search) {
            $q->where('company_name', 'like', "%$search%")
              ->orWhere('id_number', 'like', "%$search%")
              ->orWhere('full_name', 'like', "%$search%");
        });
    }

    // Filter by Payment Date
    $filterDate = $request->filled('date') ? $request->date : now()->toDateString();

    $query->whereHas('payment', function($q) use ($filterDate) {
        $q->whereDate('payment_date', $filterDate);
    });

    $permits = $query->orderBy('submission_id', 'desc')->paginate(15);

    return view('permit.submitted', compact('permits'));
}


    /*
     * Show the edit form for a single permit entry from DB.
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
     * generate Permit Id
     */
    protected function generatePermitId(string $type): string
{
    $datePrefix = now()->format('ym'); // '2508'  August 2025

    $latest = Permit::where('permit_id', 'like', $type . $datePrefix . '%')
        ->orderBy('permit_id', 'desc')
        ->first();

    $nextNumber = 1001;

    if ($latest) {
        $lastId = $latest->permit_id;
        $lastCounter = (int)substr($lastId, -4);
        $nextNumber = $lastCounter + 1;
    }

    return $type . $datePrefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
}


    /*
     ***********  blacklist check *********   
    */
     
protected function isBlacklisted(array $data, string $type = null): ?string
{
    \Log::info('Checking blacklist for:', $data);

    $query = \App\Models\Blacklist::query();

    $query->where(function ($q) use ($data) {
        $nic = trim($data['id_number'] ?? '');
        $fullName = trim($data['full_name'] ?? '');
        $company = strtolower(trim($data['company_name'] ?? ''));
        $vehicle = trim($data['vehicle_number'] ?? '');

        if (!empty($fullName)) {
            $q->orWhere('full_name', $fullName);
        }

        if (!empty($nic)) {
            $q->orWhereRaw('BINARY `nic` = ?', [$nic]);
        }

        if (!empty($company)) {
            $q->orWhereRaw('LOWER(TRIM(company_name)) = ?', [$company]);
        }

        if (!empty($vehicle)) {
            $q->orWhere('vehicle_number', $vehicle);
        }
    });

    if (!empty($type)) {
        $query->where('type', $type);
    }

    $entry = $query->first();

    \Log::info('Blacklist matched entry:', [$entry]);

    return $entry ? ($entry->reason ?? 'Blacklisted') : null;
}

public function cancel(Permit $permit)
{
    $permit->update(['status' => 'cancelled']);
    return redirect()->back()->with('success', 'Permit cancelled successfully.');
}

public function activate(Permit $permit)
{
    $permit->update(['status' => 'active']);
    return redirect()->back()->with('success', 'Permit activated successfully.');
}


    public function checkAvailability(Request $request)
{
    try {
        \Log::info('Availability Check Request:', $request->all());

        $data = $request->validate([
            'id_type' => 'required|string',
            'id_number' => 'required|string',
            'full_name' => 'required|string',
            'initials' => 'required|string',
            'from_date' => 'required|date',
            'to_date' => 'required|date',
            'company_name' => 'nullable|string',
        ]);

        // Check blacklist first
        if ($reason = $this->isBlacklisted($data)) {
            return response()->json(['available' => false, 'message' => "Blacklisted: $reason"]);
        }

        // Check for conflicts only with active permits
        $conflict = Permit::where('status', 'active')
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
            return response()->json(['available' => false, 'message' => 'Permit NOT available for this period or person.']);
        }

        return response()->json(['available' => true, 'message' => 'Permit available!']);
    } catch (\Exception $e) {
        \Log::error('Availability check failed: ' . $e->getMessage());
        return response()->json(['available' => false, 'message' => 'Server error occurred.'], 500);
    }
}


   
}
