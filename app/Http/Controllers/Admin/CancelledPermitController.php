<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\CancelledPermit;
use App\Models\Permit; 
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class CancelledPermitController extends Controller
{
    public function index(Request $request)
    {
        $query = CancelledPermit::query();

        $fromDate = $request->from_date ?: Carbon::now()->subDays(30)->format('Y-m-d');
        $toDate   = $request->to_date ?: Carbon::now()->format('Y-m-d');

        $query->whereBetween('cancelled_at', [
            $fromDate . ' 00:00:00',
            $toDate . ' 23:59:59'
        ]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('permit_id', 'like', "%$search%")
                  ->orWhere('invoice_id', 'like', "%$search%")
                  ->orWhere('submission_id', 'like', "%$search%")
                  ->orWhere('id_number', 'like', "%$search%")
                  ->orWhere('full_name', 'like', "%$search%")
                  ->orWhere('company_name', 'like', "%$search%")
                  ->orWhere('vehicle_number', 'like', "%$search%");
            });
        }

        $cancelledPermits = $query->orderBy('cancelled_at', 'desc')->paginate(15);

        return view('admin.cancelled_permits.index', compact('cancelledPermits', 'fromDate', 'toDate'));
    }

    public function show($id)
    {
        $cancelledPermit = CancelledPermit::findOrFail($id);
        return view('admin.cancelled_permits.show', compact('cancelledPermit'));
    }

    // Soft delete cancelled permit
    public function destroy($id)
    {
        $permit = CancelledPermit::findOrFail($id);

        $permit->addLog('soft_deleted', auth()->id(), [
            'permit_id' => $permit->permit_id,
            'full_name' => $permit->full_name,
            'company_name' => $permit->company_name,
            'vehicle_number' => $permit->vehicle_number,
            'cancel_reason' => $permit->cancel_reason,
        ]);

        $permit->delete();

        return redirect()->route('admin.cancelled_permits.index')
                         ->with('success', 'Cancelled permit soft-deleted and logged.');
    }

    // Restore trashed permit
    public function restore($id)
    {
        $permit = CancelledPermit::onlyTrashed()->findOrFail($id);

        $permit->addLog('restored', auth()->id(), [
            'permit_id' => $permit->permit_id,
            'full_name' => $permit->full_name,
            'company_name' => $permit->company_name,
            'vehicle_number' => $permit->vehicle_number,
        ]);

        $permit->restore();

        return redirect()->route('admin.cancelled_permits.trash')
            ->with('success', 'Permit restored successfully.');
    }

    // Show trashed permits
    public function trash(Request $request)
    {
        $search = $request->input('search');

        $trashedPermits = CancelledPermit::onlyTrashed()
            ->when($search, function ($query) use ($search) {
                $query->where('permit_id', 'like', "%{$search}%")
                      ->orWhere('full_name', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%")
                      ->orWhere('vehicle_number', 'like', "%{$search}%");
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate(15);

        return view('admin.cancelled_permits.trash', compact('trashedPermits'));
    }

    // Permanently delete
    public function forceDelete($id)
    {
        $cancelled = CancelledPermit::onlyTrashed()->findOrFail($id);

        $cancelled->addLog('force_deleted', auth()->id(), [
            'permit_id'      => $cancelled->permit_id,
            'full_name'      => $cancelled->full_name,
            'company_name'   => $cancelled->company_name,
            'vehicle_number' => $cancelled->vehicle_number,
            'cancel_reason'  => $cancelled->cancel_reason,
        ]);

        // Delete related payment
        Payment::where('submission_id', $cancelled->submission_id)->delete();

        // Delete original permit from new tables
        $type = $cancelled->type; // 'TP', 'MP', 'VH'
        if ($type === 'TP') {
            \App\Models\TemporaryPermit::where('permit_id', $cancelled->permit_id)->forceDelete();
        } elseif ($type === 'MP') {
            \App\Models\MonthlyPermit::where('permit_id', $cancelled->permit_id)->forceDelete();
        } elseif ($type === 'VH') {
            \App\Models\VehiclePermit::where('permit_id', $cancelled->permit_id)->forceDelete();
        }

        // Also delete from old permits table if it exists
        Permit::where('permit_id', $cancelled->permit_id)->delete();

        // Finally delete from cancelled_permits
        $cancelled->forceDelete();

        return redirect()->route('admin.cancelled_permits.trash')
            ->with('success', 'Permit permanently deleted with related records.');
    }

    // Export PDF
    public function exportPdf(Request $request)
    {
        $fromDate = $request->from_date ?: null;
        $toDate   = $request->to_date ?: null;

        $query = CancelledPermit::query();
        if ($fromDate && $toDate) {
            $query->whereBetween('cancelled_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $cancelledPermits = $query->get();

        $pdf = Pdf::loadView('admin.cancelled_permits.export_pdf', compact('cancelledPermits'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('cancelled_permits.pdf');
    }

    // Export Excel
    public function exportExcel(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');

        $query = CancelledPermit::query();
        if ($fromDate && $toDate) {
            $query->whereBetween('cancelled_at', [
                $fromDate . ' 00:00:00',
                $toDate . ' 23:59:59'
            ]);
        }

        $cancelledPermits = $query->get();

        $fileName = 'cancelled_permits.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $columns = [
            'Permit ID','Invoice ID','Submission ID','ID Number',
            'Full Name','Company Name','Vehicle Number',
            'Cancel Reason','Cancelled At','Cancelled By'
        ];

        $callback = function() use ($cancelledPermits, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($cancelledPermits as $permit) {
                fputcsv($file, [
                    $permit->permit_id,
                    $permit->invoice_id,
                    $permit->submission_id,
                    $permit->id_number,
                    $permit->full_name,
                    $permit->company_name,
                    $permit->vehicle_number,
                    $permit->cancel_reason,
                    $permit->cancelled_at,
                    $permit->cancelled_by
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Cancel a permit (AJAX)
    public function cancel(Request $request, $permitId)
    {
        // First try to find the permit in new models
        $permit = \App\Models\TemporaryPermit::find($permitId);
        $type = 'TP';
        
        if (!$permit) {
            $permit = \App\Models\MonthlyPermit::find($permitId);
            $type = 'MP';
        }
        
        if (!$permit) {
            $permit = \App\Models\VehiclePermit::find($permitId);
            $type = 'VH';
        }
        
        // Fallback to legacy Permit if still not found
        if (!$permit) {
            $permit = Permit::findOrFail($permitId);
            $type = $permit->type ?? 'TP';
        }

        // Update permit status
        $permit->status = 'cancelled';
        $permit->save();

        // Also update old permits table if it exists
        Permit::where('permit_id', $permit->permit_id)
            ->update(['status' => 'cancelled']);

        // Update or create cancelled permit (avoid duplicates)
        $cancelled = CancelledPermit::updateOrCreate(
            ['permit_id' => $permit->permit_id],
            [
                'invoice_id'    => $permit->submission_id ?? ($permit->invoice_id ?? null),
                'submission_id' => $permit->submission_id ?? ($permit->invoice_id ?? null),
                'type'          => $type,
                'id_number'     => $permit->id_number ?? null,
                'full_name'     => $permit->full_name ?? null,
                'company_name'  => $permit->company_name ?? null,
                'vehicle_number'=> $permit->vehicle_number ?? null,
                'cancel_reason' => $request->cancel_reason_other ?: $request->cancel_reason_select,
                'cancelled_at'  => now(),
                'cancelled_by'  => auth()->user()->name ?? 'System',
            ]
        );

        $cancelled->addLog('cancelled', auth()->id(), [
            'permit_id' => $cancelled->permit_id,
            'full_name' => $cancelled->full_name,
            'company_name' => $cancelled->company_name,
            'vehicle_number' => $cancelled->vehicle_number,
            'cancel_reason' => $cancelled->cancel_reason
        ]);

        return response()->json([
            'status' => 'cancelled',
            'id' => $permit->id,
        ]);
    }

    // Activate a cancelled permit (AJAX) without adding to trash
    public function activate(Request $request, $permitId)
    {
        // First try to find the permit in new models
        $permit = \App\Models\TemporaryPermit::find($permitId);
        
        if (!$permit) {
            $permit = \App\Models\MonthlyPermit::find($permitId);
        }
        
        if (!$permit) {
            $permit = \App\Models\VehiclePermit::find($permitId);
        }
        
        // Fallback to legacy Permit if still not found
        if (!$permit) {
            $permit = Permit::findOrFail($permitId);
        }

        // Update permit status to active
        $permit->status = 'active';
        $permit->cancel_reason = null;
        $permit->save();

        // Also update old permits table if it exists
        Permit::where('permit_id', $permit->permit_id)
            ->update([
                'status' => 'active',
                'cancel_reason' => null
            ]);

        // Permanently remove the cancelled permit entry (no soft delete)
        $cancelled = CancelledPermit::where('permit_id', $permit->permit_id)->first();
        if ($cancelled) {
            $cancelled->addLog('activated', auth()->id(), [
                'permit_id' => $cancelled->permit_id,
                'full_name' => $cancelled->full_name,
                'company_name' => $cancelled->company_name,
                'vehicle_number' => $cancelled->vehicle_number,
            ]);

            \DB::table('cancelled_permits')->where('id', $cancelled->id)->delete();
        }

        return response()->json([
            'status' => 'activated',
            'id' => $permit->id,
        ]);
    }
}
