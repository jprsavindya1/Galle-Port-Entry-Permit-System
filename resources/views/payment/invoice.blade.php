@extends('layouts.app') 

@section('title', 'Payment Invoice')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
/* ===== Shared Dashboard Look ===== */
.invoice-card {
    background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
    border-radius: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    padding: 2.5rem;
    margin-bottom: 2rem;
    border: none;
    width: 100%;
    max-width: 1100px;
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
    font-size: 0.85rem;
    padding: 0.75rem 0.5rem;
    text-align: center;
    white-space: nowrap;
}
.invoice-table td {
    background: #fff;
    color: #333;
    vertical-align: middle;
    font-size: 0.85rem;
    padding: 0.75rem 0.5rem;
    border-color: #ddd;
    text-align: center;
    white-space: nowrap;
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

/* Print Status Badges */
.print-status-badge {
    display: inline-block;
    padding: 0.3rem 0.6rem;
    border-radius: 0.4rem;
    font-weight: 600;
    font-size: 0.75rem;
}
.print-status-printed {
    background: #c8e6c9;
    color: #2e7d32;
    border: 1px solid #81c784;
}
.print-status-not-printed {
    background: #e0e0e0;
    color: #616161;
    border: 1px solid #bdbdbd;
}
.print-info-text {
    font-size: 0.7rem;
    color: #666;
    line-height: 1.3;
    margin-top: 0.2rem;
}

.batch-print-status {
    background: linear-gradient(135deg, #e8f5e9 0%, #ffffff 100%);
    border: 2px solid #81c784;
    border-radius: 0.75rem;
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.batch-print-status.all-printed {
    background: linear-gradient(135deg, #c8e6c9 0%, #e8f5e9 100%);
}

.batch-print-status.not-printed {
    background: linear-gradient(135deg, #fff3e0 0%, #ffffff 100%);
    border-color: #ffb74d;
}

.batch-print-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    flex-shrink: 0;
}

.batch-print-icon.printed {
    background: #4caf50;
    color: white;
}

.batch-print-icon.not-printed {
    background: #ff9800;
    color: white;
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
.disabled-batch-print {
    pointer-events: none;
    opacity: 0.5;
    cursor: not-allowed;
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


/* ===== Print Styles (A4/Portrait Optimized) ===== */
@media print {
    body {
        margin: 0;
        padding: 0;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        visibility: hidden;
        background: white;
    }

    #print-area, .container {
        visibility: visible;
        position: absolute;
        left: 0;
        top: 0;
        width: 100% !important;
        max-width: 100% !important;
        background: white;
        padding: 0 !important;
        margin: 0 !important;
        display: block !important;
    }

    #print-area * {
        visibility: visible;
    }

    .invoice-card {
        background: transparent !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Hide non-printable elements */
    .btn, a.btn, button, .no-print, .print-controls,
    nav, header, footer, .navbar, .nav, .sidebar, 
    .sidebar-slpa-logo, .d-flex > .sidebar-slpa-logo,
    .navigation, .menu, .header, .footer, .batch-print-status {
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

    /* Header Styling */
    .invoice-title {
        font-size: 18pt !important;
        text-align: center;
        margin-bottom: 6mm !important;
        color: #000 !important;
        font-weight: bold !important;
    }

    .mb-4 h6, .mb-4 p {
        font-size: 11pt !important;
        margin-bottom: 2mm !important;
        line-height: 1.4 !important;
        color: #000 !important;
        font-weight: bold !important;
    }

    .badge-status, .badge-free {
        font-size: 10pt !important;
        padding: 2px 6px !important;
        background: #fff !important;
        color: #000 !important;
        border: 1px solid #000 !important;
        font-weight: bold !important;
        display: inline-block !important;
    }
    
    .bi-credit-card-fill, .bi-check-circle-fill {
        color: #000 !important;
    }

    /* Table styles for printing */
    table, .invoice-table {
        width: 100% !important;
        table-layout: auto !important; /* Dynamic sizing based on column content */
        border-collapse: collapse !important;
        margin: 5mm 0 !important;
    }

    /* Hide print status column when printing */
    .invoice-table th:last-child,
    .invoice-table td:last-child {
        display: none !important;
    }

    .invoice-table th, .invoice-table td {
        border: 1px solid #000 !important;
        padding: 2.5mm 3mm !important;
        text-align: center !important;
        vertical-align: middle !important;
        background-color: #fff !important;
        line-height: 1.3 !important;
        font-size: 10pt !important;
        color: #000 !important;
        font-weight: bold !important;
        white-space: normal !important;
        word-wrap: break-word; 
        word-break: break-word;
    }

    .invoice-table th {
        background-color: #f1f5f9 !important; /* Subtle background for table header */
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }

    thead {
        display: table-header-group !important;
    }

    /* Remove min-width constraints for print */
    .invoice-table th[style*="min-width"],
    .invoice-table td[style*="min-width"] {
        min-width: auto !important;
    }

    /* Override inline styles */
    .invoice-table td[style] {
        font-size: 10pt !important;
    }

    /* Summary section styles */
    .summary-card {
        border: 1.5px solid #000 !important;
        padding: 5mm !important;
        margin-top: 6mm !important;
        background: #fff !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        max-width: 100% !important;
    }

    .summary-card h5 {
        font-size: 12pt !important;
        margin-bottom: 3mm !important;
        color: #000 !important;
        font-weight: bold !important;
    }

    .summary-card p {
        font-size: 11pt !important;
        margin-bottom: 2mm !important;
        color: #000 !important;
        font-weight: bold !important;
    }

    .summary-card h4 {
        font-size: 13pt !important;
        margin-top: 3mm !important;
        color: #000 !important;
        font-weight: bold !important;
    }
    
    .summary-card .bi {
        color: #000 !important;
    }

    html, body {
        height: auto;
        overflow: visible;
    }

    @page {
        size: portrait;
        margin: 15mm;
    }
}
</style>

<div id="print-area" class="container py-4 d-flex justify-content-center">
    <div class="invoice-card">
        <div class="invoice-header">
            <div class="invoice-title">
                <i class="bi bi-receipt-cutoff me-2"></i> Payment Receipt
            </div>
            <button onclick="enableBatchPrint(); window.print()" class="btn btn-primary btn-custom no-print">
                <i class="bi bi-printer"></i> Print Receipt
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

        @php
            // Check if all permits are printed
            $allPrinted = $permits->every(fn($permit) => $permit->is_printed);
            $somePrinted = $permits->contains(fn($permit) => $permit->is_printed);
            $printedCount = $permits->filter(fn($permit) => $permit->is_printed)->count();
            $totalCount = $permits->count();
        @endphp

        @if($allPrinted)
            <div class="batch-print-status all-printed no-print">
                <div class="batch-print-icon printed">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div>
                    <h6 class="mb-1" style="color: #2e7d32; font-weight: 600;">
                        <i class="bi bi-printer-fill me-1"></i> All Permits Printed
                    </h6>
                    <p class="mb-0" style="font-size: 0.9rem; color: #666;">
                        All {{ $totalCount }} permit(s) in this batch have been printed successfully.
                    </p>
                </div>
            </div>
        @elseif($somePrinted)
            <div class="batch-print-status not-printed no-print">
                <div class="batch-print-icon not-printed">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <h6 class="mb-1" style="color: #e65100; font-weight: 600;">
                        <i class="bi bi-printer me-1"></i> Partially Printed
                    </h6>
                    <p class="mb-0" style="font-size: 0.9rem; color: #666;">
                        {{ $printedCount }} of {{ $totalCount }} permit(s) have been printed. Please print remaining permits.
                    </p>
                </div>
            </div>
        @else
            <div class="batch-print-status not-printed no-print">
                <div class="batch-print-icon not-printed">
                    <i class="bi bi-printer"></i>
                </div>
                <div>
                    <h6 class="mb-1" style="color: #e65100; font-weight: 600;">
                        <i class="bi bi-printer me-1"></i> Not Printed Yet
                    </h6>
                    <p class="mb-0" style="font-size: 0.9rem; color: #666;">
                        None of the {{ $totalCount }} permit(s) in this batch have been printed. Use the "Batch Print Permits" button below.
                    </p>
                </div>
            </div>
        @endif

        <div class="invoice-table-container mb-4">
            <div class="table-responsive">
                <table class="table align-middle invoice-table">
                    <thead>
                        <tr>
                            <th>Application No.</th>
                            <th>Permit ID</th>
                            @if($payment->permit_type === 'VH')
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
                            @if($payment->permit_type !== 'VH')
                                <th>Pass Type</th>
                            @endif
                            <th style="min-width: 130px;">Print Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // Pre-load all users to avoid N+1 queries
                            $userIds = $permits->pluck('printed_by')->filter()->unique();
                            $users = \App\Models\User::whereIn('id', $userIds)->pluck('name', 'id');
                        @endphp
                        @foreach($permits as $permit)
                        <tr>
                            <td><strong>{{ $permit->application_number ?? 'N/A' }}</strong></td>
                            <td>{{ $permit->permit_id }}</td>
                            @if($payment->permit_type === 'VH')
                                <td style="text-align: left;">{{ $permit->owner_name ?? '-' }}</td>
                                <td>{{ $permit->vehicle_number ?? '-' }}</td>
                                <td>{{ $permit->revenue_license_number ?? '-' }}</td>
                            @else
                                <td style="text-align: left;">
                                    <div class="d-flex align-items-center justify-content-start">
                                        @if($permit->photo_path)
                                            <img src="{{ asset('storage/' . $permit->photo_path) }}" alt="Photo" class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover; border: 1px solid #bbdefb;">
                                        @else
                                            <div class="user-avatar me-2" style="width: 30px; height: 30px; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; background: #90caf9; color: #fff; border-radius: 50%;">
                                                {{ strtoupper(substr($permit->full_name, 0, 1)) }}
                                            </div>
                                        @endif
                                        <span>{{ $permit->full_name ?? '-' }}</span>
                                    </div>
                                </td>
                                <td>{{ $permit->id_type ?? '-' }}</td>
                                <td>
                                    <div>{{ $permit->id_number ?? '-' }}</div>
                                    <div class="mt-1 no-print">
                                        @if($permit->doc_nic_path)
                                            <a href="{{ asset('storage/' . $permit->doc_nic_path) }}" target="_blank" class="text-primary me-2 small fw-bold" style="text-decoration:none;"><i class="bi bi-file-earmark-pdf-fill text-primary me-1"></i>NIC</a>
                                        @endif
                                        @if($permit->type === 'TP' && $permit->doc_passport_path)
                                            <a href="{{ asset('storage/' . $permit->doc_passport_path) }}" target="_blank" class="text-primary me-2 small fw-bold" style="text-decoration:none;"><i class="bi bi-file-earmark-pdf-fill text-primary me-1"></i>Passport</a>
                                        @endif
                                        @if($permit->type === 'TP' && $permit->doc_driving_licence_path)
                                            <a href="{{ asset('storage/' . $permit->doc_driving_licence_path) }}" target="_blank" class="text-primary small fw-bold" style="text-decoration:none;"><i class="bi bi-file-earmark-pdf-fill text-primary me-1"></i>DL</a>
                                        @endif
                                        @if($permit->type === 'MP' && $permit->doc_police_report_path)
                                            <a href="{{ asset('storage/' . $permit->doc_police_report_path) }}" target="_blank" class="text-primary small fw-bold" style="text-decoration:none;"><i class="bi bi-file-earmark-pdf-fill text-primary me-1"></i>Police</a>
                                        @endif
                                    </div>
                                </td>
                            @endif
                            <td style="text-align: left;">{{ $permit->company_name ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($permit->from_date)->format('Y-m-d') }}</td>
                            <td>{{ \Carbon\Carbon::parse($permit->to_date)->format('Y-m-d') }}</td>
                            @if($payment->permit_type !== 'VH')
                                <td>{{ ucfirst($permit->pass_type ?? '-') }}</td>
                            @endif
                            <td>
                                @if($permit->is_printed)
                                    <div class="text-center">
                                        <span class="print-status-badge print-status-printed">
                                            <i class="bi bi-check-circle-fill me-1"></i> Printed
                                        </span>
                                        <div class="print-info-text">
                                            {{ \Carbon\Carbon::parse($permit->printed_at)->format('M d, H:i') }}
                                        </div>
                                        @if($permit->printed_by)
                                            <div class="print-info-text">
                                                By: {{ $users[$permit->printed_by] ?? 'Unknown' }}
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="text-center">
                                        <span class="print-status-badge print-status-not-printed">
                                            <i class="bi bi-x-circle me-1"></i> Not Printed
                                        </span>
                                    </div>
                                @endif
                            </td>
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
            <a href="{{ route('permit.print', $payment->submission_id) }}" 
               id="batchPrintBtn" 
               class="btn btn-primary btn-custom{{ (!$allPrinted && !$somePrinted) ? ' disabled-batch-print' : '' }}">
                <i class="bi bi-printer-fill me-1"></i> 
                @if($allPrinted)
                    Reprint All Permits
                @elseif($somePrinted)
                    Print Remaining Permits
                @else
                    Batch Print Permits
                @endif
            </a>

            @if($payment->permit_type === 'TP')
                <a href="{{ route('permit.temporary') }}" class="btn btn-secondary btn-custom"><i class="bi bi-arrow-left-circle"></i> Back to Temporary Permit</a>
            @elseif($payment->permit_type === 'MP')
                <a href="{{ route('permit.monthly') }}" class="btn btn-secondary btn-custom"><i class="bi bi-arrow-left-circle"></i> Back to Monthly Permit</a>
            @elseif($payment->permit_type === 'VH')
                <a href="{{ route('permit.vehicle') }}" class="btn btn-secondary btn-custom"><i class="bi bi-arrow-left-circle"></i> Back to Vehicle Permit</a>
            @else
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-custom"><i class="bi bi-arrow-left-circle"></i> Back</a>
            @endif
        </div>
    </div>
</div>

<script>
    function enableBatchPrint() {
        const batchPrintBtn = document.getElementById('batchPrintBtn');
        if (batchPrintBtn) {
            batchPrintBtn.classList.remove('disabled-batch-print');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const batchPrintBtn = document.getElementById('batchPrintBtn');
        
        // Add click tracking for batch print
        if (batchPrintBtn) {
            batchPrintBtn.addEventListener('click', function(e) {
                // Check if still disabled
                if (this.classList.contains('disabled-batch-print')) {
                    e.preventDefault();
                    return;
                }
                
                // Show a loading indicator
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Opening Print View...';
                this.disabled = true;
                
                // Re-enable after a delay
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            });
        }
    });
</script>
@endsection