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
use App\Helpers\IdGeneratorHelper;
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
        'temporary_permits.application_number',
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
        // Print tracking
        'temporary_permits.is_printed',
        'temporary_permits.printed_at',
        'temporary_permits.printed_by',
    ];
    
    $temporaryQuery = TemporaryPermit::select($selectColumns)
        ->whereDoesntHave('cancelledPermitTrashed');
    
    // Monthly permits - adjust columns to match
    $monthlyQuery = MonthlyPermit::select([
        \DB::raw("'MP' as type"),
        'monthly_permits.id',
        'monthly_permits.permit_id',
        'monthly_permits.application_number',
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
        // Print tracking
        'monthly_permits.is_printed',
        'monthly_permits.printed_at',
        'monthly_permits.printed_by',
    ])
    ->whereDoesntHave('cancelledPermitTrashed');
    
    // Vehicle permits - adjust columns to match
    $vehicleQuery = VehiclePermit::select([
        \DB::raw("'VH' as type"),
        'vehicle_permits.id',
        'vehicle_permits.permit_id',
        'vehicle_permits.application_number',
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
        // Print tracking
        'vehicle_permits.is_printed',
        'vehicle_permits.printed_at',
        'vehicle_permits.printed_by',
    ])
    ->whereDoesntHave('cancelledPermitTrashed');

    // ---  Search (NOT affected by date filter) ---
    if ($request->filled('q')) {
        $search = $request->q;
        
        // Temporary permits search with invoice_id via payment join
        $temporaryQuery->leftJoin('payments as temp_payments', 'temporary_permits.submission_id', '=', 'temp_payments.submission_id')
            ->where(function($q) use ($search) {
                $q->where('temporary_permits.company_name', 'like', "%$search%")
                  ->orWhere('temporary_permits.id_number', 'like', "%$search%")
                  ->orWhere('temporary_permits.full_name', 'like', "%$search%")
                  ->orWhere('temporary_permits.initials', 'like', "%$search%")
                  ->orWhere('temporary_permits.permit_id', 'like', "%$search%")
                  ->orWhere('temporary_permits.application_number', 'like', "%$search%")
                  ->orWhere('temporary_permits.submission_id', 'like', "%$search%")
                  ->orWhere('temp_payments.invoice_id', 'like', "%$search%");
            });
        
        // Monthly permits search with invoice_id via payment join
        $monthlyQuery->leftJoin('payments as monthly_payments', 'monthly_permits.submission_id', '=', 'monthly_payments.submission_id')
            ->where(function($q) use ($search) {
                $q->where('monthly_permits.company_name', 'like', "%$search%")
                  ->orWhere('monthly_permits.id_number', 'like', "%$search%")
                  ->orWhere('monthly_permits.full_name', 'like', "%$search%")
                  ->orWhere('monthly_permits.initials', 'like', "%$search%")
                  ->orWhere('monthly_permits.permit_id', 'like', "%$search%")
                  ->orWhere('monthly_permits.application_number', 'like', "%$search%")
                  ->orWhere('monthly_permits.submission_id', 'like', "%$search%")
                  ->orWhere('monthly_payments.invoice_id', 'like', "%$search%");
            });
        
        // Vehicle permits search with invoice_id via payment join
        $vehicleQuery->leftJoin('payments as vehicle_payments', 'vehicle_permits.submission_id', '=', 'vehicle_payments.submission_id')
            ->where(function($q) use ($search) {
                $q->where('vehicle_permits.company_name', 'like', "%$search%")
                  ->orWhere('vehicle_permits.vehicle_number', 'like', "%$search%")
                  ->orWhere('vehicle_permits.owner_name', 'like', "%$search%")
                  ->orWhere('vehicle_permits.revenue_license_number', 'like', "%$search%")
                  ->orWhere('vehicle_permits.insurance_number', 'like', "%$search%")
                  ->orWhere('vehicle_permits.permit_id', 'like', "%$search%")
                  ->orWhere('vehicle_permits.application_number', 'like', "%$search%")
                  ->orWhere('vehicle_permits.submission_id', 'like', "%$search%")
                  ->orWhere('vehicle_payments.invoice_id', 'like', "%$search%");
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
     * generate Permit Id with collision-free logic
     */
    protected function generatePermitId(string $type): string
    {
        return IdGeneratorHelper::generatePermitId($type);
    }

    /**
     * Generate unique application number for cart entries (collision-free)
     * Format: AP + YYMMDD + count (e.g., AP251112150)
     */
    protected function generateApplicationNumber(): string
    {
        return IdGeneratorHelper::generateApplicationNumber();
    }



    /*
     ***********  blacklist check *********   
    */
    
    /**
     * Check if a NIC/ID number or vehicle number is blacklisted (real-time check)
     */
    public function checkBlacklist(Request $request)
    {
        $nic = trim($request->input('nic_number', '') ?: $request->input('id_number', ''));
        $vehicleNumber = trim($request->input('vehicle_number', ''));
        
        if (empty($nic) && empty($vehicleNumber)) {
            return response()->json([
                'blacklisted' => false,
                'message' => ''
            ]);
        }

        $query = Blacklist::query();
        
        if (!empty($nic)) {
            $query->whereRaw('BINARY `nic` = ?', [$nic]);
        }
        
        if (!empty($vehicleNumber)) {
            $query->orWhere('vehicle_number', $vehicleNumber);
        }
        
        $entry = $query->first();
        
        if ($entry) {
            $reason = $entry->reason ?? 'This entry is blacklisted';
            return response()->json([
                'blacklisted' => true,
                'message' => '⚠️ BLACKLISTED: ' . $reason
            ]);
        }
        
        return response()->json([
            'blacklisted' => false,
            'message' => '✓ Not blacklisted'
        ]);
    }
     
protected function isBlacklisted(array $data, string $type = null): ?string
{
    \Log::info('Checking blacklist for:', $data);

    $query = \App\Models\Blacklist::query();

    $query->where(function ($q) use ($data) {
        $nic = trim($data['nic_number'] ?? $data['id_number'] ?? '');
        $fullName = trim($data['full_name'] ?? '');
        $company = strtolower(trim($data['company_name'] ?? ''));
        $vehicle = trim($data['vehicle_number'] ?? '');

        if (!empty($nic)) {
            $q->orWhereRaw('BINARY `nic` = ?', [$nic]);
        }

        if (!empty($fullName)) {
            $q->orWhere('full_name', $fullName);
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
            $type = 'VH';
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

        // Determine which permit type is being requested
        $permitType = $data['permit_type'] ?? 'temporary';

        // FOR TEMPORARY PERMITS: Check monthly permits FIRST (monthly blocks temporary)
        if ($permitType === 'temporary') {
            $monthlyConflict = MonthlyPermit::where('status', 'active')
                ->where(function ($query) use ($data) {
                    $query->where(function ($q) use ($data) {
                        $q->where('full_name', $data['full_name'])
                          ->where('initials', $data['initials']);
                    })
                    ->orWhere('id_number', $data['id_number']);
                })
                ->where(function ($query) use ($data) {
                    // Check for ANY overlap
                    $query->where('from_date', '<=', $data['to_date'])
                          ->where('to_date', '>=', $data['from_date']);
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

            // Then check for other temporary permit conflicts
            $tempConflict = TemporaryPermit::where('status', 'active')
                ->where(function ($query) use ($data) {
                    $query->where(function ($q) use ($data) {
                        $q->where('full_name', $data['full_name'])
                          ->where('initials', $data['initials']);
                    })
                    ->orWhere('id_number', $data['id_number']);
                })
                ->where(function ($query) use ($data) {
                    // Check for ANY overlap
                    $query->where('from_date', '<=', $data['to_date'])
                          ->where('to_date', '>=', $data['from_date']);
                });

            // Exclude current permit if editing
            if (!empty($data['current_permit_id'])) {
                $tempConflict->where('id', '!=', $data['current_permit_id']);
            }

            $tempConflict = $tempConflict->first();

            if ($tempConflict) {
                $fromDate = \Carbon\Carbon::parse($tempConflict->from_date)->format('d M Y');
                $toDate = \Carbon\Carbon::parse($tempConflict->to_date)->format('d M Y');
                return response()->json([
                    'available' => false,
                    'message' => "This person already has an ACTIVE TEMPORARY PERMIT (ID: {$tempConflict->permit_id}) valid from {$fromDate} to {$toDate}. Please choose different dates."
                ]);
            }
        }

        // FOR MONTHLY PERMITS: Only check against other monthly permits (monthly can override temporary)
        if ($permitType === 'monthly') {
            $monthlyConflict = MonthlyPermit::where('status', 'active')
                ->where(function ($query) use ($data) {
                    $query->where(function ($q) use ($data) {
                        $q->where('full_name', $data['full_name'])
                          ->where('initials', $data['initials']);
                    })
                    ->orWhere('id_number', $data['id_number']);
                })
                ->where(function ($query) use ($data) {
                    // Check for ANY overlap
                    $query->where('from_date', '<=', $data['to_date'])
                          ->where('to_date', '>=', $data['from_date']);
                });

            // Exclude current permit if editing
            if (!empty($data['current_permit_id'])) {
                $monthlyConflict->where('id', '!=', $data['current_permit_id']);
            }

            $monthlyConflict = $monthlyConflict->first();

            if ($monthlyConflict) {
                $fromDate = \Carbon\Carbon::parse($monthlyConflict->from_date)->format('d M Y');
                $toDate = \Carbon\Carbon::parse($monthlyConflict->to_date)->format('d M Y');
                return response()->json([
                    'available' => false,
                    'message' => "This person already has an ACTIVE MONTHLY PERMIT (ID: {$monthlyConflict->permit_id}) valid from {$fromDate} to {$toDate}. Please choose different dates or cancel the existing permit first."
                ]);
            }
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
