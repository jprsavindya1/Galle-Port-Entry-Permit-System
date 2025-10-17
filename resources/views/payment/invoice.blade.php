@extends('layouts.app') 

@section('title', 'Payment Invoice')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ===== Shared Dashboard Look ===== */
.invoice-card {
    background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
    border-radius: 1rem;
    box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    padding: 2rem; /* Adjusted padding */
    margin-bottom: 2rem;
    border: none;
}
.invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}
.invoice-title {
    font-size: 2rem;
    font-weight: 600;
    color: #1976d2;
    letter-spacing: 1px;
}
.invoice-table-container {
    background: #f5faff;
    border-radius: 0.75rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    border: 1px solid #bbdefb; /* Added border for consistency */
    margin-top: 1rem;
}
.invoice-table th {
    background: #e3f2fd;
    color: #1976d2;
    font-weight: 600;
    border-bottom: 2px solid #bbdefb;
    font-size: 0.8rem; /* Smaller font for headers */
    padding: 0.5rem;
    text-align: center;
}
.invoice-table td {
    background: #fff;
    color: #333;
    vertical-align: middle;
    font-size: 0.8rem;
    padding: 0.5rem;
    border-color: #ddd;
    text-align: center;
}
.invoice-table tbody tr:nth-child(even) td {
    background: #f8fafc; /* Subtle striping */
}

.badge-status {
    background: #4bce63ff;
    color: #0c0c0cff;
    font-weight: 700;
    border-radius: 0.5rem;
    padding: 0.35rem 0.85rem;
    display: inline-block;
    font-size: 0.9rem;
}

/* ===== Buttons ===== */
.btn-custom {
    border-radius: 0.5rem;
    font-weight: 500;
}
.btn-primary {
    background-color: #1976d2;
    border-color: #1976d2;
}
.btn-secondary {
    background-color: #9e9e9e;
    border-color: #9e9e9e;
}

/* ===== Summary Card Styling ===== */
.summary-card {
    background-color: #fff;
    border: 1px solid #bbdefb;
    border-radius: 0.75rem;
    padding: 1.5rem;
    box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}
.summary-card h5 {
    color: #1976d2;
    font-weight: 600;
}
.summary-card p {
    margin-bottom: 0.5rem;
    font-size: 1rem;
}
.summary-card h4 {
    color: #4caf50; /* Green for total */
    font-weight: 700;
    margin-top: 1rem;
}


/* ===== Print Styles (Ensure these work correctly) ===== */
@media print {
    body {
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact;
        visibility: hidden;
        background: white;
    }

    #print-area {
        visibility: visible;
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        background: white;
        padding: 0.5cm; /* Slightly reduced overall padding for more space */
    }

    #print-area * {
        visibility: visible;
    }

    .btn, a.btn, button, .no-print, .print-controls {
        display: none !important;
    }

   table {
        width: 100% !important;
        table-layout: fixed !important; /* CRITICAL: Forces columns to respect width */
        border-collapse: collapse !important;
        font-size: 7pt !important; /* Made font even smaller for aggressive fit */
    }

     .invoice-table th, .invoice-table td {
        border: 1px solid #000 !important;
        padding: 2px !important; /* Minimal padding */
        text-align: left !important;
        background-color: transparent !important;
        
        /* --- CRITICAL: FORCE TEXT TO FIT CELL --- */
        overflow: hidden;
        white-space: normal;
        word-wrap: break-word; 
        word-break: break-all; /* MOST AGGRESSIVE BREAKING */
    }

    .invoice-table th {
        background-color: #f0f0f0 !important;
        color: #000 !important;
    }

    thead {
        display: table-header-group !important;
    }

    html, body {
        height: 100%;
        overflow: hidden;
    }

    @page {
        size: A4 portrait;
        margin: 10mm;
    }

}
</style>

<div id="print-area" class="container py-4">
    <div class="invoice-card">
        <div class="invoice-header">
            <div class="invoice-title">
                <i class="bi bi-receipt-cutoff me-2"></i> Payment Invoice
            </div>
            <button onclick="window.print()" class="btn btn-primary btn-custom no-print">
                <i class="bi bi-printer"></i> Print Invoice
            </button>
        </div>

        <div class="mb-4">
            <h6><strong>Submission ID:</strong> {{ $payment->submission_id }}</h6>
            <h6><strong>Invoice ID:</strong> {{ $payment->invoice_id }}</h6>
            <p class="mb-1"><strong>Payment Date:</strong> {{ $payment->paid_at->format('Y-m-d H:i') }}</p>
            <p class="mb-1"><strong>Permit Type:</strong> {{ $payment->permit_type }}</p>
            <p class="mb-1"><strong>Entry Count:</strong> {{ $payment->entry_count }}</p>
            <p><strong>Status:</strong> <span class="badge-status">{{ $payment->status ?? 'Paid' }}</span></p>
        </div>

        <div class="invoice-table-container mb-4">
            <div class="table-responsive">
                <table class="table align-middle invoice-table">
                    <thead>
                        <tr>
                            <th>Permit ID</th>
                            @if($payment->permit_type === 'VP')
                                <th style="min-width: 150px;">Owner Name</th>
                                <th style="min-width: 100px;">Vehicle Number</th>
                                <th style="min-width: 120px;">Revenue License</th>
                            @else
                                <th style="min-width: 150px;">Full Name</th>
                                <th style="min-width: 100px;">ID Type</th>
                                <th style="min-width: 120px;">ID Number</th>
                            @endif
                            <th style="min-width: 150px;">Company</th>
                            <th>From</th>
                            <th>To</th>
                            @if($payment->permit_type !== 'VP')
                                <th>Pass Type</th>
                            @endif
                            <th>Issue Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permits as $permit)
                        <tr>
                            <td>{{ $permit->permit_id }}</td>
                            @if($payment->permit_type === 'VP')
                                <td style="text-align: left;">{{ $permit->owner_name ?? '-' }}</td>
                                <td>{{ $permit->vehicle_number ?? '-' }}</td>
                                <td>{{ $permit->revenue_license_number ?? '-' }}</td>
                            @else
                                <td style="text-align: left;">{{ $permit->full_name ?? '-' }}</td>
                                <td>{{ $permit->id_type ?? '-' }}</td>
                                <td>{{ $permit->id_number ?? '-' }}</td>
                            @endif
                            <td style="text-align: left;">{{ $permit->company_name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($permit->from_date)->format('Y-m-d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($permit->to_date)->format('Y-m-d') }}</td>
                            @if($payment->permit_type !== 'VP')
                                <td>{{ ucfirst($permit->pass_type ?? '-') }}</td>
                            @endif
                            <td>{{ ucfirst($permit->issue_type) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="summary-card mb-4">
            <h5 class="mb-3"><i class="bi bi-cash-stack me-2"></i> Payment Summary</h5>
            <p><strong>Rate Total:</strong> Rs. {{ number_format($payment->rate_total, 2) }}</p>
            <p><strong>SSL:</strong> Rs. {{ number_format($payment->ssl_total, 2) }}</p>
            <p><strong>VAT:</strong> Rs. {{ number_format($payment->vat_total, 2) }}</p>
            <h4 class="mt-3"><strong>Total Amount Paid:</strong> Rs. {{ number_format($payment->amount_total, 2) }}</h4>
        </div>

        <div class="d-flex justify-content-between no-print">
            <a href="{{ route('permit.print', $payment->submission_id) }}" target="_blank" class="btn btn-primary btn-custom">
                <i class="bi bi-printer-fill me-1"></i> Batch Print Permits
            </a>

            @if($payment->permit_type === 'TP')
                <a href="{{ route('permit.temporary') }}" class="btn btn-secondary btn-custom"><i class="bi bi-arrow-left-circle"></i> Back to Temporary Permit</a>
            @elseif($payment->permit_type === 'MP')
                <a href="{{ route('permit.monthly') }}" class="btn btn-secondary btn-custom"><i class="bi bi-arrow-left-circle"></i> Back to Monthly Permit</a>
            @elseif($payment->permit_type === 'VP')
                <a href="{{ route('permit.vehicle') }}" class="btn btn-secondary btn-custom"><i class="bi bi-arrow-left-circle"></i> Back to Vehicle Permit</a>
            @else
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-custom"><i class="bi bi-arrow-left-circle"></i> Back</a>
            @endif
        </div>
    </div>
</div>
@endsection