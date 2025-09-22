<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit;
use Pdf;

class ReportController extends Controller
{
    // Display User Report Form + Results
    public function userReportForm(Request $request)
    {
        $query = $request->query('query'); // search text
        $type = $request->query('type');   // permit type: TP / MP / VP

        $permits = collect(); // default empty collection

        // Only fetch permits if search text or type is provided
        if ($query || $type) {
            $permitsQuery = Permit::withTrashed(); // include cancelled permits (soft deleted)

            if ($query) {
                $permitsQuery->where(function($q) use ($query) {
                    $q->where('id_number', 'like', "%$query%")
                      ->orWhere('full_name', 'like', "%$query%")
                      ->orWhere('company_name', 'like', "%$query%")
                      ->orWhere('owner_name', 'like', "%$query%")
                      ->orWhere('vehicle_number', 'like', "%$query%");
                });
            }

            if ($type) {
                $permitsQuery->where('type', $type);
            }

            $permits = $permitsQuery->with('payment')->orderBy('from_date', 'desc')->get();

            // Update status for display
            $permits->transform(function($permit) {
                $permit->status = $permit->trashed() || $permit->status === 'cancelled' ? 'Cancelled' : 'Active';
                return $permit;
            });
        }

        return view('admin.reports.user_report', compact('permits', 'query', 'type'));
    }


    // Export PDF
    public function exportUserPdf(Request $request)
    {
        $query = $request->query('query');
        $type  = $request->query('type');

        $permitsQuery = Permit::withTrashed()->with('payment');

        if ($query) {
            $permitsQuery->where(function($q) use ($query) {
                $q->where('id_number', 'like', "%$query%")
                  ->orWhere('full_name', 'like', "%$query%")
                  ->orWhere('company_name', 'like', "%$query%")
                  ->orWhere('owner_name', 'like', "%$query%")
                  ->orWhere('vehicle_number', 'like', "%$query%");
            });
        }

        if ($type) {
            $permitsQuery->where('type', $type);
        }

        $permits = $permitsQuery->orderBy('from_date', 'desc')->get();

        // Update status for display
        $permits->transform(function($permit) {
            $permit->status = $permit->trashed() || $permit->status === 'cancelled' ? 'Cancelled' : 'Active';
            return $permit;
        });

        return Pdf::loadView('admin.reports.user_report_pdf', compact('permits', 'query', 'type'))
                  ->setPaper('a4', 'landscape')
                  ->download('user_report.pdf');
    }


    // Export CSV
    public function exportUserCsv(Request $request)
    {
        $query = $request->query('query');
        $type  = $request->query('type');

        $permitsQuery = Permit::withTrashed()->with('payment');

        if ($query) {
            $permitsQuery->where(function($q) use ($query) {
                $q->where('id_number', 'like', "%$query%")
                  ->orWhere('full_name', 'like', "%$query%")
                  ->orWhere('company_name', 'like', "%$query%")
                  ->orWhere('owner_name', 'like', "%$query%")
                  ->orWhere('vehicle_number', 'like', "%$query%");
            });
        }

        if ($type) {
            $permitsQuery->where('type', $type);
        }

        $permits = $permitsQuery->orderBy('from_date', 'desc')->get();

        // Update status for display
        $permits->transform(function($permit) {
            $permit->status = $permit->trashed() || $permit->status === 'cancelled' ? 'Cancelled' : 'Active';
            return $permit;
        });

        $fileName = 'user_report.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function() use ($permits) {
            $file = fopen('php://output', 'w');

            fputcsv($file, [
                'Permit ID', 'Permit Type', 
                'Name / Owner', 'ID Number / Vehicle No',
                'Company Name', 'From Date', 'To Date',
                'Issue Type', 'Reason', 'Status',
                'Submission ID', 'Invoice ID'
            ]);

            foreach ($permits as $permit) {
                if ($permit->type === 'VP') {
                    fputcsv($file, [
                        $permit->permit_id,
                        $permit->type,
                        $permit->owner_name,
                        $permit->vehicle_number,
                        $permit->company_name,
                        $permit->from_date,
                        $permit->to_date,
                        $permit->issue_type,
                        $permit->reason,
                        $permit->status,
                        $permit->submission_id,
                        $permit->payment->invoice_id ?? '-',
                    ]);
                } else {
                    fputcsv($file, [
                        $permit->permit_id,
                        $permit->type,
                        $permit->full_name,
                        $permit->id_number,
                        $permit->company_name,
                        $permit->from_date,
                        $permit->to_date,
                        $permit->issue_type,
                        $permit->reason,
                        $permit->status,
                        $permit->submission_id,
                        $permit->payment->invoice_id ?? '-',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
