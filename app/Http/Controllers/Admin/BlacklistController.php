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

    // ===== Export PDF =====
public function exportPdf(Request $request)
{
    $search = $request->input('search');

    // Active blacklist entries
    $blacklistsQuery = Blacklist::query();
    if ($search) {
        $blacklistsQuery->where('nic', 'like', "%{$search}%")
            ->orWhere('full_name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->orWhere('vehicle_number', 'like', "%{$search}%")
            ->orWhere('reason', 'like', "%{$search}%");
    }
    $blacklists = $blacklistsQuery->get();

    // Blacklist history entries only when search exists
    $histories = collect();
    if ($search) {
        $historiesQuery = \App\Models\BlacklistHistory::query();
        $historiesQuery->where('nic', 'like', "%{$search}%")
            ->orWhere('full_name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->orWhere('vehicle_number', 'like', "%{$search}%")
            ->orWhere('reason', 'like', "%{$search}%");
        $histories = $historiesQuery->get();
    }

    // Merge active and history entries
    $allEntries = $blacklists->merge($histories);

    // Pass merged collection to PDF view
    $pdf = Pdf::loadView('admin.blacklist.export_pdf', compact('allEntries'))
        ->setPaper('a4', 'landscape');

    return $pdf->download('blacklist_report.pdf');
}

    // ===== Export Excel (CSV) =====
public function exportExcel(Request $request)
{
    $search = $request->input('search');

    // Get active blacklist entries
    $blacklistsQuery = Blacklist::query();
    if ($search) {
        $blacklistsQuery->where('nic', 'like', "%{$search}%")
            ->orWhere('full_name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->orWhere('vehicle_number', 'like', "%{$search}%")
            ->orWhere('reason', 'like', "%{$search}%");
    }
    $blacklists = $blacklistsQuery->get();

    // Get historical blacklist entries if search exists
    $histories = collect();
    if ($search) {
        $historiesQuery = \App\Models\BlacklistHistory::query();
        $historiesQuery->where('nic', 'like', "%{$search}%")
            ->orWhere('full_name', 'like', "%{$search}%")
            ->orWhere('company_name', 'like', "%{$search}%")
            ->orWhere('vehicle_number', 'like', "%{$search}%")
            ->orWhere('reason', 'like', "%{$search}%");

        $histories = $historiesQuery->get();
    }

    // Merge active and history entries
    $allEntries = $blacklists->merge($histories);

    $fileName = 'blacklist_report.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
    ];

    $columns = ['NIC', 'Full Name', 'Company Name', 'Vehicle Number', 'Reason', 'Action', 'Performed By', 'Date/Time'];

    $callback = function() use ($allEntries, $columns) {
        $file = fopen('php://output', 'w');
        fputcsv($file, $columns);

        foreach ($allEntries as $entry) {
            $isHistory = $entry instanceof \App\Models\BlacklistHistory;
            $action = $isHistory ? $entry->action : 'active';
            $performedBy = $isHistory ? $entry->admin_name : ($entry->activities->first()->user_name ?? '—');
            $dateTime = $isHistory ? $entry->created_at : ($entry->activities->first()->created_at ?? $entry->created_at);

            fputcsv($file, [
                $entry->nic,
                $entry->full_name,
                $entry->company_name,
                $entry->vehicle_number,
                $entry->reason,
                ucfirst($action),
                $performedBy,
                $dateTime->format('Y-m-d H:i'),
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}

}
