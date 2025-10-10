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

    if ($search) {
        // Only show history entries when searching
        $historiesQuery = \App\Models\BlacklistHistory::query();
        $historiesQuery->where('nic', 'like', "%{$search}%")
            ->orWhere('full_name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->orWhere('vehicle_number', 'like', "%{$search}%")
            ->orWhere('reason', 'like', "%{$search}%");
        $entries = $historiesQuery->get();
        $isHistory = true;
    } else {
        // Only show active blacklist entries when no search
        $blacklistsQuery = Blacklist::query();
        $entries = $blacklistsQuery->get();
        $isHistory = false;
    }

    $pdf = Pdf::loadView('admin.blacklist.export_pdf', [
        'entries' => $entries,
        'isHistory' => $isHistory,
    ])->setPaper('a4', 'landscape');

    return $pdf->download('blacklist_report.pdf');
}


   public function exportExcel(Request $request)
{
    $search = $request->input('search');

    // --- Get Active Blacklist Entries ---
    $blacklistsQuery = \App\Models\Blacklist::query();
    if ($search) {
        $blacklistsQuery->where(function($q) use ($search) {
            $q->where('nic', 'like', "%{$search}%")
              ->orWhere('full_name', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('vehicle_number', 'like', "%{$search}%")
              ->orWhere('reason', 'like', "%{$search}%");
        });
    }
    $blacklists = $blacklistsQuery->get();

    // --- Get Blacklist History Entries ---
    $historiesQuery = \App\Models\BlacklistHistory::query();
    if ($search) {
        $historiesQuery->where(function($q) use ($search) {
            $q->where('nic', 'like', "%{$search}%")
              ->orWhere('full_name', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('vehicle_number', 'like', "%{$search}%")
              ->orWhere('reason', 'like', "%{$search}%");
        });
    }
    $histories = $historiesQuery->get();

    // --- Merge Both ---
    $allEntries = $blacklists->merge($histories);

    // --- CSV Setup ---
    $fileName = 'blacklist_report.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
    ];

    $search = $request->input('search');

    if ($search) {
        // Only export history entries when searching
        $columns = ['NIC', 'Full Name', 'Company Name', 'Vehicle Number', 'Reason', 'Added By', 'Added On', 'Status', 'Reinstated By', 'Reinstated On'];
        $historiesQuery = \App\Models\BlacklistHistory::query();
        $historiesQuery->where('nic', 'like', "%{$search}%")
            ->orWhere('full_name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->orWhere('vehicle_number', 'like', "%{$search}%")
            ->orWhere('reason', 'like', "%{$search}%");
        $entries = $historiesQuery->get();
        $isHistory = true;
    } else {
        // Only export active blacklist entries when no search
        $columns = ['NIC', 'Full Name', 'Company Name', 'Vehicle Number', 'Reason', 'Added By', 'Added On', 'Status'];
        $blacklistsQuery = Blacklist::query();
        $entries = $blacklistsQuery->get();
        $isHistory = false;
    }

    $file = fopen('php://output', 'w');
    fputcsv($file, $columns);
    foreach ($entries as $entry) {
        if ($isHistory) {
            $status = $entry->status ?? ucfirst($entry->action);
            $addedBy = $entry->admin_name ?? '—';
            $addedOn = $entry->created_at ? "'" . $entry->created_at->format('Y-m-d H:i') : '—';
            $reinstatedBy = $entry->reinstated_by ?? '—';
            $reinstatedOn = $entry->reinstated_on ? "'" . \Carbon\Carbon::parse($entry->reinstated_on)->format('Y-m-d H:i') : '—';
            fputcsv($file, [
                $entry->nic,
                $entry->full_name,
                $entry->company_name,
                $entry->vehicle_number,
                $entry->reason,
                $addedBy,
                $addedOn,
                $status,
                $reinstatedBy,
                $reinstatedOn,
            ]);
        } else {
            $status = 'Blacklisted';
            $addedBy = $entry->activities->first()->user_name ?? '—';
            $addedOn = $entry->activities->first()->created_at ?? $entry->created_at;
            $addedOnFormatted = $addedOn ? "'" . $addedOn->format('Y-m-d H:i') : '—';
            fputcsv($file, [
                $entry->nic,
                $entry->full_name,
                $entry->company_name,
                $entry->vehicle_number,
                $entry->reason,
                $addedBy,
                $addedOnFormatted,
                $status,
            ]);
        }
    }
    fclose($file);
    return response()->stream(function() {}, 200, $headers);
}
}