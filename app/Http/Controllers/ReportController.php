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
        $permitsQuery = Permit::query();

        if ($query) {
            $permitsQuery->where(function($q) use ($query) {
                $q->where('id_number', 'like', "%$query%")
                  ->orWhere('full_name', 'like', "%$query%")
                  ->orWhere('company_name', 'like', "%$query%");
            });
        }

        if ($type) {
            $permitsQuery->where('type', $type);
        }

        $permits = $permitsQuery->orderBy('from_date', 'desc')->get();
    }

    // Pass query and type to the blade so search/filter values stay
    return view('admin.reports.user_report', compact('permits', 'query', 'type'));
}


    // Export PDF
    public function exportUserPdf(Request $request)
    {
        $query = $request->query('query');
        $type  = $request->query('type');

        $permits = Permit::query();

        if ($query) {
            $permits = $permits->where(function($q) use ($query) {
                $q->where('id_number', 'like', "%$query%")
                  ->orWhere('full_name', 'like', "%$query%")
                  ->orWhere('company_name', 'like', "%$query%");
            });
        }

        if ($type) {
            $permits = $permits->where('type', $type);
        }

        $permits = $permits->with('payment')->orderBy('from_date', 'desc')->get();

        return Pdf::loadView('admin.reports.user_report_pdf', compact('permits', 'query', 'type'))
                  ->setPaper('a4', 'landscape')
                  ->download('user_report.pdf');
    }

    // Export CSV
    public function exportUserCsv(Request $request)
    {
        $query = $request->query('query');
        $type  = $request->query('type');

        $permits = Permit::query();

        if ($query) {
            $permits = $permits->where(function($q) use ($query) {
                $q->where('id_number', 'like', "%$query%")
                  ->orWhere('full_name', 'like', "%$query%")
                  ->orWhere('company_name', 'like', "%$query%");
            });
        }

        if ($type) {
            $permits = $permits->where('type', $type);
        }

        $permits = $permits->with('payment')->orderBy('from_date', 'desc')->get();

        $fileName = 'user_report.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $columns = [
            'Permit ID', 'Permit Type', 'Full Name', 'ID Number', 'Company Name',
            'From Date', 'To Date', 'Issue Type', 'Reason', 'Status', 'Submission ID', 'Invoice ID'
        ];

        $callback = function() use ($permits, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($permits as $permit) {
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

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}