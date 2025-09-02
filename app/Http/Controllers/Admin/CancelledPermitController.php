<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\CancelledPermit;
use App\Models\Permit; 
use App\Models\Payment;// Main permit model
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
        $permit = CancelledPermit::onlyTrashed()->findOrFail($id);

        $permit->addLog('force_deleted', auth()->id(), [
            'permit_id' => $permit->permit_id,
            'full_name' => $permit->full_name,
            'company_name' => $permit->company_name,
            'vehicle_number' => $permit->vehicle_number,
            'cancel_reason' => $permit->cancel_reason,
        ]);

        $permit->forceDelete();

        return redirect()->route('admin.cancelled_permits.trash')
            ->with('success', 'Permit permanently deleted.');
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
        $permit = Permit::findOrFail($permitId);

        $permit->status = 'cancelled';
        $permit->save();

        $cancelled = new CancelledPermit();
        $cancelled->permit_id = $permit->permit_id;
        $cancelled->invoice_id = $permit->invoice_id ?? null;
        $cancelled->submission_id = $permit->submission_id;
        $cancelled->id_number = $permit->id_number ?? null;
        $cancelled->full_name = $permit->full_name ?? null;
        $cancelled->company_name = $permit->company_name ?? null;
        $cancelled->vehicle_number = $permit->vehicle_number ?? null;
        $cancelled->cancel_reason = $request->cancel_reason_other ?: $request->cancel_reason_select;
        $cancelled->cancelled_at = now();
        $cancelled->cancelled_by = auth()->user()->name;
        $cancelled->save();

        // Log activity
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

    // Activate a cancelled permit (AJAX)
    public function activate(Request $request, $permitId)
    {
        $permit = Permit::findOrFail($permitId);

        $permit->status = 'active';
        $permit->save();

        $cancelled = CancelledPermit::where('permit_id', $permit->permit_id)->first();
        if ($cancelled) {
            $cancelled->addLog('activated', auth()->id(), [
                'permit_id' => $cancelled->permit_id,
                'full_name' => $cancelled->full_name,
                'company_name' => $cancelled->company_name,
                'vehicle_number' => $cancelled->vehicle_number,
            ]);

            $cancelled->delete();
        }

        return response()->json([
            'status' => 'activated',
            'id' => $permit->id,
        ]);
    }
}
