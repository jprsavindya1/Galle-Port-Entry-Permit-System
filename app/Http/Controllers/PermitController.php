<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\TemporaryPermit;
use App\Models\MonthlyPermit;
use App\Models\VehiclePermit;
use App\Models\Designation;
use App\Models\Reason;
use App\Models\Blacklist;
use App\Models\Company;
use App\Models\CancelledPermit;
class PermitController extends Controller
{
    /*
     * Show list of submitted permits.
     * Supports search filtering by company name, ID number, or full name.
     */
   public function submittedList(Request $request)
{
    // Select only common columns for UNION compatibility (with table prefix for clarity)
    $selectColumns = [
        \DB::raw("'TP' as type"),
        'temporary_permits.id',
        'temporary_permits.permit_id',
        'temporary_permits.submission_id',
        'temporary_permits.company_name',
        'temporary_permits.from_date',
        'temporary_permits.to_date',
        'temporary_permits.issue_type',
        'temporary_permits.status',
        'temporary_permits.created_at',
        'temporary_permits.updated_at',
        // Temporary specific
        'temporary_permits.id_type',
        'temporary_permits.id_number',
        'temporary_permits.full_name',
        'temporary_permits.initials',
        'temporary_permits.designation',
        'temporary_permits.company_address',
        'temporary_permits.residence_address',
        'temporary_permits.pass_type',
        'temporary_permits.reason',
        \DB::raw('NULL as vehicle_number'),
        \DB::raw('NULL as vehicle_type'),
        \DB::raw('NULL as owner_name'),
        \DB::raw('NULL as owner_address'),
        \DB::raw('NULL as revenue_license_number'),
        \DB::raw('NULL as insurance_number'),
        \DB::raw('NULL as police_issue_date'),
        \DB::raw('NULL as police_expire_date'),
    ];
    
    $temporaryQuery = TemporaryPermit::select($selectColumns)
        ->whereDoesntHave('cancelledPermitTrashed');
    
    // Monthly permits - adjust columns to match
    $monthlyQuery = MonthlyPermit::select([
        \DB::raw("'MP' as type"),
        'monthly_permits.id',
        'monthly_permits.permit_id',
        'monthly_permits.submission_id',
        'monthly_permits.company_name',
        'monthly_permits.from_date',
        'monthly_permits.to_date',
        'monthly_permits.issue_type',
        'monthly_permits.status',
        'monthly_permits.created_at',
        'monthly_permits.updated_at',
        // Monthly specific
        'monthly_permits.id_type',
        'monthly_permits.id_number',
        'monthly_permits.full_name',
        'monthly_permits.initials',
        'monthly_permits.designation',
        'monthly_permits.company_address',
        'monthly_permits.residence_address',
        'monthly_permits.pass_type',
        'monthly_permits.reason',
        \DB::raw('NULL as vehicle_number'),
        \DB::raw('NULL as vehicle_type'),
        \DB::raw('NULL as owner_name'),
        \DB::raw('NULL as owner_address'),
        \DB::raw('NULL as revenue_license_number'),
        \DB::raw('NULL as insurance_number'),
        'monthly_permits.police_issue_date',
        'monthly_permits.police_expire_date',
    ])
    ->whereDoesntHave('cancelledPermitTrashed');
    
    // Vehicle permits - adjust columns to match
    $vehicleQuery = VehiclePermit::select([
        \DB::raw("'VP' as type"),
        'vehicle_permits.id',
        'vehicle_permits.permit_id',
        'vehicle_permits.submission_id',
        'vehicle_permits.company_name',
        'vehicle_permits.from_date',
        'vehicle_permits.to_date',
        'vehicle_permits.issue_type',
        'vehicle_permits.status',
        'vehicle_permits.created_at',
        'vehicle_permits.updated_at',
        // Vehicle specific
        \DB::raw('NULL as id_type'),
        \DB::raw('NULL as id_number'),
        \DB::raw('NULL as full_name'),
        \DB::raw('NULL as initials'),
        \DB::raw('NULL as designation'),
        \DB::raw('NULL as company_address'),
        'vehicle_permits.owner_address as residence_address',
        \DB::raw('NULL as pass_type'),
        'vehicle_permits.reason',
        'vehicle_permits.vehicle_number',
        'vehicle_permits.vehicle_type',
        'vehicle_permits.owner_name',
        'vehicle_permits.owner_address',
        'vehicle_permits.revenue_license_number',
        'vehicle_permits.insurance_number',
        \DB::raw('NULL as police_issue_date'),
        \DB::raw('NULL as police_expire_date'),
    ])
    ->whereDoesntHave('cancelledPermitTrashed');

    // ---  Search (NOT affected by date filter) ---
    if ($request->filled('q')) {
        $search = $request->q;
        
        $temporaryQuery->where(function($q) use ($search) {
            $q->where('company_name', 'like', "%$search%")
              ->orWhere('id_number', 'like', "%$search%")
              ->orWhere('full_name', 'like', "%$search%")
              ->orWhere('permit_id', 'like', "%$search%")
              ->orWhere('submission_id', 'like', "%$search%");
        });
        
        $monthlyQuery->where(function($q) use ($search) {
            $q->where('company_name', 'like', "%$search%")
              ->orWhere('id_number', 'like', "%$search%")
              ->orWhere('full_name', 'like', "%$search%")
              ->orWhere('permit_id', 'like', "%$search%")
              ->orWhere('submission_id', 'like', "%$search%");
        });
        
        $vehicleQuery->where(function($q) use ($search) {
            $q->where('company_name', 'like', "%$search%")
              ->orWhere('vehicle_number', 'like', "%$search%")
              ->orWhere('owner_name', 'like', "%$search%")
              ->orWhere('permit_id', 'like', "%$search%")
              ->orWhere('submission_id', 'like', "%$search%");
        });
    }

    // --- Date filter (filters by payment date via join) ---
    if (!$request->filled('q')) {
        $filterDate = $request->filled('date') ? $request->date : now()->toDateString();
        
        // Add payment join and filter for temporary permits
        $temporaryQuery->join('payments', 'temporary_permits.submission_id', '=', 'payments.submission_id')
            ->whereDate('payments.payment_date', $filterDate);
        
        // Add payment join and filter for monthly permits
        $monthlyQuery->join('payments', 'monthly_permits.submission_id', '=', 'payments.submission_id')
            ->whereDate('payments.payment_date', $filterDate);
        
        // Add payment join and filter for vehicle permits
        $vehicleQuery->join('payments', 'vehicle_permits.submission_id', '=', 'payments.submission_id')
            ->whereDate('payments.payment_date', $filterDate);
    }

    // Union all three queries
    $allPermits = $temporaryQuery->union($monthlyQuery)->union($vehicleQuery);
    
    // Get results and convert to collection for grouping
    $permits = \DB::table(\DB::raw("({$allPermits->toSql()}) as permits"))
        ->mergeBindings($allPermits->getQuery())
        ->orderBy('submission_id', 'desc')
        ->paginate(15);

    return view('permit.submitted', compact('permits'));
}

    /*
     * Show the edit form for a single permit entry from DB.
     */
public function edit($permitType, $permitId)
{
    // Determine which model to use based on permit type
    switch ($permitType) {
        case 'temporary':
            $permit = TemporaryPermit::findOrFail($permitId);
            break;
        case 'monthly':
            $permit = MonthlyPermit::findOrFail($permitId);
            break;
        case 'vehicle':
            $permit = VehiclePermit::findOrFail($permitId);
            break;
        default:
            abort(404, 'Invalid permit type');
    }
    
    // load required dropdown data
    $designations = Designation::orderBy('name')->get();
    $reasons = Reason::orderBy('name')->get();
    $companies = Company::orderBy('name')->get();

    // preselect company name & address based on current permit
    $companyName = $permit->company_name ?? '';
    $companyAddress = $permit->company_address ?? '';

    return view('permit.edit', compact(
        'permit',
        'permitType',
        'designations',
        'reasons',
        'companies',
        'companyName',
        'companyAddress'
    ));
}


    /*
     * Update a permit entry with validated data.
     */
    public function update(Request $request, $permitType, $permitId)
{
    // Find the permit based on type
    switch ($permitType) {
        case 'temporary':
            $permit = TemporaryPermit::findOrFail($permitId);
            break;
        case 'monthly':
            $permit = MonthlyPermit::findOrFail($permitId);
            break;
        case 'vehicle':
            $permit = VehiclePermit::findOrFail($permitId);
            break;
        default:
            abort(404, 'Invalid permit type');
    }
    
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
    public function destroy($permitType, $permitId)
    {
        // Find the permit based on type
        switch ($permitType) {
            case 'temporary':
                $permit = TemporaryPermit::findOrFail($permitId);
                break;
            case 'monthly':
                $permit = MonthlyPermit::findOrFail($permitId);
                break;
            case 'vehicle':
                $permit = VehiclePermit::findOrFail($permitId);
                break;
            default:
                abort(404, 'Invalid permit type');
        }
        
        $permit->delete();
        return redirect()->route('permits.submitted')->with('success', 'Permit deleted successfully.');
    }

    /*
     * generate Permit Id
     */
   protected function generatePermitId(string $type): string
{
    $yearPrefix = now()->format('y'); // e.g., '25' for 2025

    // Check both the new separate table AND the old permits table to avoid duplicates
    switch ($type) {
        case 'TP':
            $latestNew = TemporaryPermit::where('permit_id', 'like', $type . $yearPrefix . '%')
                ->orderBy('permit_id', 'desc')
                ->first();
            break;
        case 'MP':
            $latestNew = MonthlyPermit::where('permit_id', 'like', $type . $yearPrefix . '%')
                ->orderBy('permit_id', 'desc')
                ->first();
            break;
        case 'VP':
            $latestNew = VehiclePermit::where('permit_id', 'like', $type . $yearPrefix . '%')
                ->orderBy('permit_id', 'desc')
                ->first();
            break;
        default:
            throw new \Exception("Invalid permit type: $type");
    }

    // Also check the old permits table
    $latestOld = \App\Models\Permit::where('permit_id', 'like', $type . $yearPrefix . '%')
        ->orderBy('permit_id', 'desc')
        ->first();

    // Get the highest number from both tables
    $nextNumber = 1;
    
    if ($latestNew) {
        $lastCounter = (int) substr($latestNew->permit_id, -4);
        $nextNumber = max($nextNumber, $lastCounter + 1);
    }
    
    if ($latestOld) {
        $lastCounter = (int) substr($latestOld->permit_id, -4);
        $nextNumber = max($nextNumber, $lastCounter + 1);
    }

    $month = now()->format('m');
    return $type . $yearPrefix . $month . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
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

public function cancel(Request $request, $permitType, $permitId)
{
    // Find the permit based on type
    switch ($permitType) {
        case 'temporary':
            $permit = TemporaryPermit::findOrFail($permitId);
            $type = 'TP';
            break;
        case 'monthly':
            $permit = MonthlyPermit::findOrFail($permitId);
            $type = 'MP';
            break;
        case 'vehicle':
            $permit = VehiclePermit::findOrFail($permitId);
            $type = 'VP';
            break;
        default:
            abort(404, 'Invalid permit type');
    }
    
    $reason = $request->cancel_reason_select === 'Other'
        ? $request->cancel_reason_other
        : $request->cancel_reason_select;

    // 1. Update the original permit in the new table
    $permit->update([
        'status' => 'cancelled',
        'cancel_reason' => $reason,
    ]);

    // 2. Also update in the old permits table if it exists
    \App\Models\Permit::where('permit_id', $permit->permit_id)
        ->update([
            'status' => 'cancelled',
            'cancel_reason' => $reason,
        ]);

    // 3. Insert into cancelled_permits table if not exists
    CancelledPermit::updateOrCreate(
        ['permit_id' => $permit->permit_id],
        [
            'invoice_id'        => $permit->submission_id,
            'submission_id'     => $permit->submission_id,
            'type'              => $type,
            'id_type'           => $permit->id_type ?? null,
            'id_number'         => $permit->id_number ?? null,
            'full_name'         => $permit->full_name ?? null,
            'initials'          => $permit->initials ?? null,
            'designation'       => $permit->designation ?? null,
            'company_name'      => $permit->company_name,
            'company_address'   => $permit->company_address ?? null,
            'residence_address' => $permit->residence_address ?? null,
            'pass_type'         => $permit->pass_type ?? null,
            'issue_type'        => $permit->issue_type,
            'reason'            => $permit->reason ?? null,
            'vehicle_type'      => $permit->vehicle_type ?? null,
            'vehicle_number'    => $permit->vehicle_number ?? null,
            'owner_name'        => $permit->owner_name ?? null,
            'owner_address'     => $permit->owner_address ?? null,
            'remarks'           => $permit->remarks ?? null,
            'cancel_reason'     => $reason,
            'cancelled_at'      => now(),
            'cancelled_by'      => auth()->user()->name ?? 'System',
        ]
    );

    return response()->json([
        'id' => $permit->id,
        'status' => 'cancelled',
    ]);
}

public function activate($permitType, $permitId)
{
    // Find the permit based on type
    switch ($permitType) {
        case 'temporary':
            $permit = TemporaryPermit::findOrFail($permitId);
            break;
        case 'monthly':
            $permit = MonthlyPermit::findOrFail($permitId);
            break;
        case 'vehicle':
            $permit = VehiclePermit::findOrFail($permitId);
            break;
        default:
            abort(404, 'Invalid permit type');
    }
    
    // 1. Update the permit status in the new table
    $permit->update([
        'status' => 'active',
        'cancel_reason' => null,
    ]);

    // 2. Also update in the old permits table if it exists
    \App\Models\Permit::where('permit_id', $permit->permit_id)
        ->update([
            'status' => 'active',
            'cancel_reason' => null,
        ]);

    // 3. Permanently remove the CancelledPermit row
    \DB::table('cancelled_permits')
        ->where('permit_id', $permit->permit_id)
        ->delete(); // <-- this bypasses SoftDeletes completely

    return response()->json([
        'id' => $permit->id,
        'status' => 'activated',
    ]);
}




   // Check check Availability
  
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
            'current_permit_id' => 'nullable|integer',
            'permit_type' => 'nullable|string|in:temporary,monthly', // which table to check
        ]);

        // Check blacklist first
        if ($reason = $this->isBlacklisted($data)) {
            return response()->json([
                'available' => false, 
                'message' => "Blacklisted: $reason"
            ]);
        }

        // Determine which model to check based on permit_type
        $permitType = $data['permit_type'] ?? 'temporary';
        $modelClass = $permitType === 'monthly' ? MonthlyPermit::class : TemporaryPermit::class;

        // Check for conflicts only with active permits
        $conflictQuery = $modelClass::where('status', 'active')
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
            });

        // Exclude the current permit if editing
        if (!empty($data['current_permit_id'])) {
            $conflictQuery->where('id', '!=', $data['current_permit_id']);
        }

        $conflict = $conflictQuery->exists();

        if ($conflict) {
            return response()->json([
                'available' => false, 
                'message' => 'Permit NOT available for this period or person.'
            ]);
        }

        return response()->json([
            'available' => true, 
            'message' => 'Success!'
        ]);

    } catch (\Exception $e) {
        \Log::error('Availability check failed: ' . $e->getMessage());
        return response()->json([
            'available' => false, 
            'message' => 'Server error occurred.'
        ], 500);
    }
}

/**
 * Fetch person details from database based on ID number
 */
public function fetchPersonDetails(Request $request)
{
    try {
        $idNumber = $request->input('id_number');
        
        if (empty($idNumber)) {
            return response()->json([
                'found' => false,
                'message' => 'ID number is required'
            ]);
        }

        // Try to find in TemporaryPermit first
        $permit = TemporaryPermit::where('id_number', $idNumber)
            ->orderBy('created_at', 'desc')
            ->first();

        // If not found, try MonthlyPermit
        if (!$permit) {
            $permit = MonthlyPermit::where('id_number', $idNumber)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if ($permit) {
            return response()->json([
                'found' => true,
                'data' => [
                    'full_name' => $permit->full_name,
                    'initials' => $permit->initials,
                    'designation' => $permit->designation ?? null,
                    'residence_address' => $permit->residence_address ?? null,
                ]
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'No records found for this ID number'
        ]);

    } catch (\Exception $e) {
        \Log::error('Fetch person details failed: ' . $e->getMessage());
        return response()->json([
            'found' => false,
            'message' => 'Server error occurred.'
        ], 500);
    }
}
    
   
}
