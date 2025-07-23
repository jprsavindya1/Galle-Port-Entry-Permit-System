<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Permit;

class PermitController extends Controller
{
    /*
     * Show list of submitted permits.
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
     * generate Permit Id
     */
    protected function generatePermitId(string $type): string
{
    $datePrefix = now()->format('ym'); // e.g., '2508' for August 2025

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


    


   
}
