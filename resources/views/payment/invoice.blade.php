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

.badge-free {
    background: #64b5f6;
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


/* ===== Print Styles (80mm Thermal Paper) ===== */
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
        padding: 2mm; /* Minimal padding for thermal paper */
    }

    #print-area * {
        visibility: visible;
    }

    /* Hide all navigation, header, footer, sidebar, and non-printable elements */
    .btn, a.btn, button, .no-print, .print-controls,
    nav, header, footer, .navbar, .nav, .sidebar, 
    .sidebar-slpa-logo, .d-flex > .sidebar-slpa-logo,
    .navigation, .menu, .header, .footer {
        display: none !important;
        visibility: hidden !important;
    }

    /* Ensure main content takes full width when printing */
    main, main.flex-grow-1 {
        width: 100% !important;
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Remove flexbox layout during print */
    .d-flex {
        display: block !important;
    }

    /* Optimize header for thermal paper */
    .invoice-title {
        font-size: 12pt !important;
        text-align: center;
        margin-bottom: 3mm !important;
    }

    .mb-4 h6, .mb-4 p {
        font-size: 8pt !important;
        margin-bottom: 1mm !important;
        line-height: 1.2 !important;
    }

    .badge-status {
        font-size: 7pt !important;
        padding: 1px 3px !important;
    }

    .badge-free {
        font-size: 7pt !important;
        padding: 1px 3px !important;
    }

    /* Table optimized for 80mm paper */
    table {
        width: 100% !important;
        table-layout: auto !important; /* Auto layout works better for narrow paper */
        border-collapse: collapse !important;
        font-size: 4pt !important; /* Extra extra small font for 80mm paper */
        margin: 2mm 0 !important;
    }

    .invoice-table, .invoice-table tbody, .invoice-table tbody tr {
        font-size: 4pt !important;
    }

    .invoice-table th, .invoice-table td {
        border: 1px solid #000 !important;
        padding: 0.3mm !important; /* Ultra minimal padding */
        text-align: left !important;
        background-color: transparent !important;
        line-height: 0.9 !important; /* Super tight line height */
        font-size: 4pt !important; /* Force small font on cells */
        
        /* Force text to wrap and fit */
        overflow: hidden;
        white-space: normal;
        word-wrap: break-word; 
        word-break: break-word;
        max-width: 0; /* Forces equal distribution of columns */
    }

    .invoice-table th {
        background-color: #e0e0e0 !important;
        color: #000 !important;
        font-weight: bold !important;
        font-size: 4pt !important; /* Match table font size */
    }

    /* Remove min-width constraints for thermal paper */
    .invoice-table th[style*="min-width"],
    .invoice-table td[style*="min-width"] {
        min-width: auto !important;
    }

    /* Override inline styles */
    .invoice-table td[style] {
        font-size: 4pt !important;
    }

    thead {
        display: table-header-group !important;
    }

    /* Summary section optimization */
    .summary-card {
        border: 1px solid #000 !important;
        padding: 2mm !important;
        margin-top: 3mm !important;
    }

    .summary-card h5 {
        font-size: 9pt !important;
        margin-bottom: 2mm !important;
    }

    .summary-card p {
        font-size: 7pt !important;
        margin-bottom: 1mm !important;
    }

    .summary-card h4 {
        font-size: 10pt !important;
        margin-top: 2mm !important;
    }

    html, body {
        height: 100%;
        overflow: visible;
    }

    /* 80mm thermal paper size (80mm width x continuous length) */
    @page {
        size: 80mm auto; /* Width: 80mm, Height: auto (continuous) */
        margin: 2mm; /* Minimal margins */
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
            <p>
                <strong>Status:</strong> 
                @php
                    // Check if any permit is free or all are payment
                    $hasFree = $permits->contains(function($permit) {
                        return strtolower($permit->issue_type ?? '') === 'free';
                    });
                    $hasPayment = $permits->contains(function($permit) {
                        return strtolower($permit->issue_type ?? '') === 'payment';
                    });
                    
                    // If all free
                    if ($hasFree && !$hasPayment) {
                        $statusBadge = '<span class="badge-free"><i class="bi bi-check-circle-fill me-1"></i>Free</span>';
                    } 
                    // If all payment or mixed
                    else {
                        $statusBadge = '<span class="badge-status"><i class="bi bi-credit-card-fill me-1"></i>Paid</span>';
                    }
                @endphp
                {!! $statusBadge !!}
            </p>
        </div>

        <div class="invoice-table-container mb-4">
            <div class="table-responsive">
                <table class="table align-middle invoice-table">
                    <thead>
                        <tr>
                            <th>Application No.</th>
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
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permits as $permit)
                        <tr>
                            <td><strong>{{ $permit->application_number ?? 'N/A' }}</strong></td>
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
            <a href="{{ route('permit.print', $payment->submission_id) }}" target="_blank" id="batchPrintBtn" class="btn btn-primary btn-custom" style="pointer-events: none; opacity: 0.5; cursor: not-allowed;">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const printInvoiceBtn = document.querySelector('button[onclick="window.print()"]');
        const batchPrintBtn = document.getElementById('batchPrintBtn');
        
        if (printInvoiceBtn && batchPrintBtn) {
            printInvoiceBtn.addEventListener('click', function() {
                // Enable batch print button after print invoice is clicked
                batchPrintBtn.style.pointerEvents = 'auto';
                batchPrintBtn.style.opacity = '1';
                batchPrintBtn.style.cursor = 'pointer';
            });
        }
    });
</script>
@endsection