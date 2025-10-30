<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blacklist;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Helpers\ActivityLogHelper;

class BlacklistController extends Controller
{
   public function index(Request $request)
{
    $search = $request->input('search');

    if ($search) {
        // Include active and history entries when searching
        $blacklists = \App\Models\Blacklist::query()
            ->where('nic', 'like', "%{$search}%")
            ->orWhere('full_name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->orWhere('vehicle_number', 'like', "%{$search}%")
            ->orWhere('reason', 'like', "%{$search}%")
            ->get();

        $histories = \App\Models\BlacklistHistory::query()
            ->where('nic', 'like', "%{$search}%")
            ->orWhere('full_name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->orWhere('vehicle_number', 'like', "%{$search}%")
            ->orWhere('reason', 'like', "%{$search}%")
            ->get();

        // Merge results
        $blacklists = $blacklists->merge($histories);
    } else {
        // Only show active blacklists when no search
        $blacklists = \App\Models\Blacklist::latest()->paginate(10);
    }

    return view('admin.blacklist.index', compact('blacklists', 'search'));
}

    public function history(Request $request)
    {
        $search = $request->input('search');
        $status_filter = $request->input('status_filter');

        $query = \App\Models\BlacklistHistory::query();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('nic', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('company_name', 'like', "%{$search}%")
                  ->orWhere('vehicle_number', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%");
            });
        }

        if ($status_filter) {
            $query->where('action', $status_filter);
        }

        $blacklistHistory = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.blacklist.history', compact('blacklistHistory'));
    }


    public function create()
    {
        return view('admin.blacklist.create');
        
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nic' => 'nullable|string',
            'full_name' => 'nullable|string',
            'company_name' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'reason' => 'required|string',
        ]);

        // Create and assign to variable
        $blacklist = Blacklist::create($data);

        // Log to BlacklistHistory as "created"
        ActivityLogHelper::logBlacklistHistory('created', $blacklist);

        //  Log activity
        ActivityLogHelper::logActivity('Created Blacklist Entry', $blacklist, null, [
            'nic' => $blacklist->nic,
            'full_name' => $blacklist->full_name,
            'company_name' => $blacklist->company_name,
            'vehicle_number' => $blacklist->vehicle_number,
            'reason' => $blacklist->reason,
        ]);

        return redirect()->route('blacklist.index')->with('success', 'Blacklist entry added.');
    }

    public function edit(Blacklist $blacklist)
    {
        return view('admin.blacklist.edit', compact('blacklist'));
    }

    public function update(Request $request, Blacklist $blacklist)
    {
        $data = $request->validate([
            'nic' => 'nullable|string',
            'full_name' => 'nullable|string',
            'company_name' => 'nullable|string',
            'vehicle_number' => 'nullable|string',
            'reason' => 'required|string',
        ]);

        $blacklist->update($data);

        // Log to BlacklistHistory as "updated"
        ActivityLogHelper::logBlacklistHistory('updated', $blacklist);

        // Log update
        ActivityLogHelper::logActivity('Updated Blacklist Entry', $blacklist, null, [
            'nic' => $blacklist->nic,
            'full_name' => $blacklist->full_name,
            'company_name' => $blacklist->company_name,
            'vehicle_number' => $blacklist->vehicle_number,
            'reason' => $blacklist->reason,
        ]);

        return redirect()->route('blacklist.index')->with('success', 'Blacklist entry updated.');
    }

    public function destroy(Blacklist $blacklist)
{
    // Log to BlacklistHistory as "reinstated" instead of "deleted"
    ActivityLogHelper::logBlacklistHistory('reinstated', $blacklist);

    // Save details before deletion (optional extra activity log)
    $details = [
        'nic'            => $blacklist->nic,
        'full_name'      => $blacklist->full_name,
        'company_name'   => $blacklist->company_name,
        'vehicle_number' => $blacklist->vehicle_number,
        'reason'         => $blacklist->reason,
    ];

    // Delete the active blacklist entry
    $blacklist->delete();

    // Log activity
    ActivityLogHelper::logActivity('Deleted Blacklist Entry', null, $blacklist->id, $details);

    return redirect()->route('blacklist.index')->with('success', 'Blacklist entry deleted.');
}
public function exportPdf(Request $request)
{
    $search = $request->input('search');
    $status_filter = $request->input('status_filter');

    // Export history entries
    $query = \App\Models\BlacklistHistory::query();
    
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('nic', 'like', "%{$search}%")
              ->orWhere('full_name', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('vehicle_number', 'like', "%{$search}%")
              ->orWhere('reason', 'like', "%{$search}%");
        });
    }
    
    if ($status_filter) {
        $query->where('action', $status_filter);
    }
    
    $entries = $query->orderBy('created_at', 'desc')->get();

    $pdf = Pdf::loadView('admin.blacklist.export_pdf', [
        'entries' => $entries,
        'isHistory' => true,
    ])->setPaper('a4', 'landscape');

    return $pdf->download('blacklist_history_report.pdf');
}


   public function exportExcel(Request $request)
{
    $search = $request->input('search');
    $status_filter = $request->input('status_filter');

    // Export history entries
    $query = \App\Models\BlacklistHistory::query();
    
    if ($search) {
        $query->where(function($q) use ($search) {
            $q->where('nic', 'like', "%{$search}%")
              ->orWhere('full_name', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('vehicle_number', 'like', "%{$search}%")
              ->orWhere('reason', 'like', "%{$search}%");
        });
    }
    
    if ($status_filter) {
        $query->where('action', $status_filter);
    }
    
    $entries = $query->orderBy('created_at', 'desc')->get();

    // CSV Setup
    $fileName = 'blacklist_history_report.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
    ];

    $columns = ['NIC', 'Full Name', 'Company Name', 'Vehicle Number', 'Reason', 'Action', 'Performed By', 'Performed On'];

    $callback = function() use ($entries, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($entries as $entry) {
            // Determine action display text
            if ($entry->action === 'created') {
                $actionText = 'Blacklisted';
            } elseif ($entry->action === 'reinstated' || $entry->action === 'deleted') {
                $actionText = 'Reinstated';
            } else {
                $actionText = ucfirst($entry->action ?? '-');
            }
            
            // Determine who performed the action
            if ($entry->action === 'reinstated' || $entry->action === 'deleted') {
                $performedBy = $entry->reinstated_by ?? $entry->admin_name ?? '—';
            } else {
                $performedBy = $entry->admin_name ?? '—';
            }
            
            // Determine when the action was performed
            if ($entry->action === 'reinstated' || $entry->action === 'deleted') {
                $performedOn = $entry->reinstated_on ? "'" . \Carbon\Carbon::parse($entry->reinstated_on)->format('Y-m-d H:i') : ($entry->created_at ? "'" . $entry->created_at->format('Y-m-d H:i') : '—');
            } else {
                $performedOn = $entry->created_at ? "'" . $entry->created_at->format('Y-m-d H:i') : '—';
            }
            
            fputcsv($file, [
                $entry->nic ?? '-',
                $entry->full_name ?? '-',
                $entry->company_name ?? '-',
                $entry->vehicle_number ?? '-',
                $entry->reason ?? '-',
                $actionText,
                $performedBy,
                $performedOn,
            ]);
        }
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}
}