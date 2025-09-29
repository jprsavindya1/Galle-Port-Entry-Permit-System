<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permit;
use Pdf;
use App\Models\Payment;
use Carbon\Carbon;

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
public function paymentReport(Request $request)
{
    $type = $request->query('type');   // TP / MP / VP / all
    $range = $request->query('range'); // day / week / month
    $date = $request->query('date');   // reference date (optional)

    $payments = Payment::with('permits');

    // Filter by permit type
    if ($type) {
        $payments->where('permit_type', $type);
    }

    // Date filters
    $refDate = $date ? Carbon::parse($date) : Carbon::today();

    if ($range === 'day') {
        $payments->whereDate('payment_date', $refDate);
    } elseif ($range === 'week') {
       $payments->whereBetween('payment_date', [
        $refDate->copy()->startOfWeek(),
        $refDate->copy()->endOfWeek(),
    ]);
    } elseif ($range === 'month') {
        $payments->whereMonth('payment_date', $refDate->month)
                 ->whereYear('payment_date', $refDate->year);
    }

    $payments = $payments->orderBy('payment_date', 'desc')->get();

    // Summary totals
    $summary = [
        'rate_total'  => $payments->sum('rate_total'),
        'ssl_total'   => $payments->sum('ssl_total'),
        'vat_total'   => $payments->sum('vat_total'),
        'amount_total'=> $payments->sum('amount_total'),
    ];

    return view('admin.reports.payment_report', compact('payments', 'summary', 'type', 'range', 'date'));
}

public function exportPaymentPdf(Request $request)
{
    $type  = $request->query('type');
    $range = $request->query('range');
    $date  = $request->query('date');

    $paymentsQuery = Payment::with('permits');

    if ($type) {
        $paymentsQuery->where('permit_type', $type);
    }

    $refDate = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::today();

    if ($range === 'day') {
        $paymentsQuery->whereDate('payment_date', $refDate);
    } elseif ($range === 'week') {
        $paymentsQuery->whereBetween('payment_date', [$refDate->startOfWeek(), $refDate->endOfWeek()]);
    } elseif ($range === 'month') {
        $paymentsQuery->whereMonth('payment_date', $refDate->month)
                      ->whereYear('payment_date', $refDate->year);
    }

    $payments = $paymentsQuery->orderBy('payment_date', 'desc')->get();

    $summary = [
        'rate_total'   => $payments->sum('rate_total'),
        'ssl_total'    => $payments->sum('ssl_total'),
        'vat_total'    => $payments->sum('vat_total'),
        'amount_total' => $payments->sum('amount_total'),
    ];

    return \Pdf::loadView('admin.reports.payment_report_pdf', compact('payments', 'summary', 'type', 'range', 'date'))
              ->setPaper('a4', 'landscape')
              ->download('payment_report.pdf');
}

public function exportPaymentCsv(Request $request)
{
    $type  = $request->query('type');
    $range = $request->query('range');
    $date  = $request->query('date');

    $paymentsQuery = Payment::with('permits');

    if ($type) {
        $paymentsQuery->where('permit_type', $type);
    }

    $refDate = $date ? \Carbon\Carbon::parse($date) : \Carbon\Carbon::today();

    if ($range === 'day') {
        $paymentsQuery->whereDate('payment_date', $refDate);
    } elseif ($range === 'week') {
        $paymentsQuery->whereBetween('payment_date', [$refDate->startOfWeek(), $refDate->endOfWeek()]);
    } elseif ($range === 'month') {
        $paymentsQuery->whereMonth('payment_date', $refDate->month)
                      ->whereYear('payment_date', $refDate->year);
    }

    $payments = $paymentsQuery->orderBy('payment_date', 'desc')->get();

    $fileName = 'payment_report.csv';
    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename=\"$fileName\"",
    ];

    $callback = function() use ($payments) {
        $file = fopen('php://output', 'w');

        // Header
        fputcsv($file, [
            'Invoice ID', 'Submission ID', 'Permit Type', 'Company Name',
            'Entry Count', 'Rate Total', 'SSL Total', 'VAT Total', 'Amount Total', 'Payment Date'
        ]);

        foreach ($payments as $p) {
            fputcsv($file, [
                $p->invoice_id,
                $p->submission_id,
                $p->permit_type,
                $p->permits->first()->company_name ?? '-',
                $p->entry_count,
                $p->rate_total,
                $p->ssl_total,
                $p->vat_total,
                $p->amount_total,
                $p->payment_date,
            ]);
        }

        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
}



}

