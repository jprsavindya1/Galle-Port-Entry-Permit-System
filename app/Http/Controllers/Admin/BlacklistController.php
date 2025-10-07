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

        $blacklists = Blacklist::query()
            ->when($search, function ($query, $search) {
                $query->where('nic', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('company_name', 'like', "%{$search}%")
                    ->orWhere('vehicle_number', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

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

        // ✅ Create and assign to variable
        $blacklist = Blacklist::create($data);

        // ✅ Log activity
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

        // ✅ Log update
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
        // Save details before delete
        $details = [
            'nic' => $blacklist->nic,
            'full_name' => $blacklist->full_name,
            'company_name' => $blacklist->company_name,
            'vehicle_number' => $blacklist->vehicle_number,
            'reason' => $blacklist->reason,
        ];

        $blacklist->delete();

        // ✅ Log delete
        ActivityLogHelper::logActivity('Deleted Blacklist Entry', null, $blacklist->id, $details);

        return redirect()->route('blacklist.index')->with('success', 'Blacklist entry deleted.');
    }

    // ===== Export PDF =====
    public function exportPdf(Request $request)
    {
        $search = $request->input('search');

        $query = Blacklist::query();
        if ($search) {
            $query->where('nic', 'like', "%{$search}%")
                ->orWhere('full_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('vehicle_number', 'like', "%{$search}%")
                ->orWhere('reason', 'like', "%{$search}%");
        }

        $blacklists = $query->get();

        $pdf = Pdf::loadView('admin.blacklist.export_pdf', compact('blacklists'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('blacklist_report.pdf');
    }

    // ===== Export Excel (CSV) =====
    public function exportExcel(Request $request)
    {
        $search = $request->input('search');

        $query = Blacklist::query();
        if ($search) {
            $query->where('nic', 'like', "%{$search}%")
                ->orWhere('full_name', 'like', "%{$search}%")
                ->orWhere('company_name', 'like', "%{$search}%")
                ->orWhere('vehicle_number', 'like', "%{$search}%")
                ->orWhere('reason', 'like', "%{$search}%");
        }

        $blacklists = $query->get();

        $fileName = 'blacklist_report.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $columns = ['NIC', 'Full Name', 'Company Name', 'Vehicle Number', 'Reason', 'Created At'];

        $callback = function() use ($blacklists, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($blacklists as $entry) {
                fputcsv($file, [
                    $entry->nic,
                    $entry->full_name,
                    $entry->company_name,
                    $entry->vehicle_number,
                    $entry->reason,
                    $entry->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
