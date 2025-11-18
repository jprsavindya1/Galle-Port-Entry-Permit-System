@extends('layouts.app')

@section('title', 'Payment Summary')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* --- User Dashboard Styles Applied to Page --- */
    .user-dashboard-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        padding: 2rem;
        margin-bottom: 2rem;
        border: none;
    }
    .user-dashboard-title {
        font-size: 2rem;
        font-weight: 600;
        color: #000000ff;
        letter-spacing: 1px;
        margin-bottom: 1.5rem;
    }
    /* Table Styling for consistency */
    .user-dashboard-table { 
        background: #f5faff;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-top: 1rem;
        border: 1px solid #bbdefb; /* Added light border */
    }
    .user-dashboard-table thead th {
        background: #e3f2fd;
        color: #1976d2;
        font-weight: 600;
        border-bottom: 2px solid #bbdefb;
        vertical-align: middle;
        text-align: center;
    }
    .user-dashboard-table tbody td {
        background: #fff; /* White background for rows */
        color: #333;
        vertical-align: middle;
        font-size: 0.95rem;
        text-align: center;
    }
    .user-dashboard-table tfoot td {
        background: #c2dff7ff; /* Light blue for totals */
        color: #000000ff;
        font-size: 1.1rem;
        font-weight: 300;
    }
    .user-dashboard-table .text-end {
        text-align: right !important;
    }
    
    /* Button Styling */
    .btn-success {
        background-color: #4caf50;
        border-color: #4caf50;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: background-color 0.2s;
    }
    .btn-secondary {
        background-color: #9e9e9e;
        border-color: #9e9e9e;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: background-color 0.2s;
    }
    .btn-danger {
        background-color: #f44336;
        border-color: #f44336;
        border-radius: 0.3rem;
    }
    .table .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
    /* Specific table cell alignment for data consistency */
    .user-dashboard-table tbody td:nth-child(2),
    .user-dashboard-table tbody td:nth-child(3),
    .user-dashboard-table tbody td:nth-child(4),
    .user-dashboard-table tbody td:nth-child(8),
    .user-dashboard-table tfoot td:nth-child(8),
    .user-dashboard-table tfoot td:nth-child(9) {
        /* General alignment for names, numbers, and currency columns */
        text-align: left;
    }
    
    .user-dashboard-table tbody td:nth-child(7),
    .user-dashboard-table tbody td:nth-child(9),
    .user-dashboard-table tbody td:nth-child(10),
    .user-dashboard-table tbody td:nth-child(11),
    .user-dashboard-table tbody td:nth-child(12),
    .user-dashboard-table tfoot td:nth-child(9),
    .user-dashboard-table tfoot td:nth-child(10),
    .user-dashboard-table tfoot td:nth-child(11),
    .user-dashboard-table tfoot td:nth-child(12) {
        /* Right alignment for all currency columns */
        text-align: right;
        padding-right: 1rem;
    }

    .user-dashboard-table thead th:nth-child(9),
    .user-dashboard-table thead th:nth-child(10),
    .user-dashboard-table thead th:nth-child(11),
    .user-dashboard-table thead th:nth-child(12) {
        text-align: right;
        padding-right: 1rem;
    }

</style>

<div class="container py-4">
    <div class="user-dashboard-card">
        <h2 class="user-dashboard-title">
            <i class="bi bi-cash-stack me-2"></i> Payment Summary for Submission ID: <strong>{{ $submissionId }}</strong>
        </h2>

        @php
            // Using a default of 'TP' (Temporary Permit) if empty
            $firstType = 'TP'; 
            if(!empty($detailedPayments) && isset($detailedPayments[0]['entry']['type'])) {
                $firstType = $detailedPayments[0]['entry']['type'];
            }
        @endphp

        {{-- ================= TEMPORARY & MONTHLY PERMITS (TP/MP) ================= --}}
        @if ($firstType !== 'VH')
            <div class="table-responsive">
                <table class="table user-dashboard-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 3%">#</th>
                            <th style="width: 20%; text-align: left !important;">Full Name</th>
                            <th style="width: 8%">ID Type</th>
                            <th style="width: 12%">ID Number</th>
                            <th style="width: 7%">From</th>
                            <th style="width: 7%">To</th>
                            <th style="width: 8%">Base Rate (LKR)</th>
                            <th style="width: 5%">Days</th>
                            <th style="width: 8%">Rate (LKR)</th>
                            <th style="width: 7%">SSL (LKR)</th>
                            <th style="width: 7%">VAT (LKR)</th>
                            <th style="width: 9%">Total (LKR)</th>
                            <th style="width: 7%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                            $rateTotal = 0;
                            $sslTotal = 0;
                            $vatTotal = 0;
                            $baseRateTotal = 0;
                        @endphp

                        @foreach ($detailedPayments as $index => $payment)
                            @php
                                $entry = $payment['entry'];
                                $days = \Carbon\Carbon::parse($entry['from_date'])->diffInDays(\Carbon\Carbon::parse($entry['to_date'])) + 1;

                                $rate = $payment['rate'] ?? 0;
                                $ssl = $payment['ssl'] ?? 0;
                                $vat = $payment['vat'] ?? 0;
                                $baseRate = $days > 0 ? $rate / $days : 0;

                                // Only sum up for non-free issues
                                if ($entry['issue_type'] !== 'free') {
                                    $rateTotal += $rate;
                                    $sslTotal  += $ssl;
                                    $vatTotal  += $vat;
                                    if ($entry['type'] !== 'MP') {
                                        $baseRateTotal += $baseRate;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td style="text-align: left !important;">{{ $entry['full_name'] ?? 'N/A' }}</td>
                                <td>{{ $entry['id_type'] ?? '-' }}</td>
                                <td style="text-align: left !important;">{{ $entry['id_number'] ?? '-' }}</td>
                                <td>{{ $entry['from_date'] }}</td>
                                <td>{{ $entry['to_date'] }}</td>
                                <td>{{ $entry['issue_type'] === 'free' ? '0.00' : ($entry['type'] === 'MP' ? '-' : number_format($baseRate, 2)) }}</td>
                                <td>{{ $days }}</td>
                                <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($rate, 2) }}</td>
                                <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($ssl, 2) }}</td>
                                <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($vat, 2) }}</td>
                                <td><strong>{{ number_format($payment['total'], 2) }}</strong></td>
                                <td>
                                    <form method="POST" action="{{ route('permit.remove', $index) }}" class="delete-payment-form">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger delete-payment-btn" type="button"><i class="bi bi-trash"></i> Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-end"><strong>Total Payable Amount (LKR)</strong></td>
                            <td><strong>=</strong></td>
                            <td><strong>{{ number_format($rateTotal, 2) }}</strong></td>
                            <td><strong>{{ number_format($sslTotal, 2) }}</strong></td>
                            <td><strong>{{ number_format($vatTotal, 2) }}</strong></td>
                            <td><strong>{{ number_format($totalPayment, 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        {{-- ================= VEHICLE PERMITS (VH) ================= --}}
        @if ($firstType === 'VH')
            <div class="table-responsive">
                <table class="table user-dashboard-table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 3%">#</th>
                            <th style="width: 18%; text-align: left !important;">Owner Name</th>
                            <th style="width: 10%">Vehicle Number</th>
                            <th style="width: 12%">Revenue License</th>
                            <th style="width: 8%">From</th>
                            <th style="width: 8%">To</th>
                            <th style="width: 8%">Base Rate (LKR)</th>
                            <th style="width: 5%">Days</th>
                            <th style="width: 8%">Rate (LKR)</th>
                            <th style="width: 7%">SSL (LKR)</th>
                            <th style="width: 7%">VAT (LKR)</th>
                            <th style="width: 9%">Total (LKR)</th>
                            <th style="width: 7%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $i = 1;
                            $rateTotal = 0;
                            $sslTotal = 0;
                            $vatTotal = 0;
                            $baseRateTotal = 0;
                        @endphp

                        @foreach ($detailedPayments as $index => $payment)
                            @php
                                $entry = $payment['entry'];
                                $calculatedDays = \Carbon\Carbon::parse($entry['from_date'])->diffInDays(\Carbon\Carbon::parse($entry['to_date'])) + 1;
                                $maxDays = str_contains(strtolower($entry['vehicle_type'] ?? ''), 'monthly') ? 29 : 28;
                                $days = min($calculatedDays, $maxDays);

                                $originalRate = $payment['rate'] ?? 0;
                                $ssl = $payment['ssl'] ?? 0;
                                $vat = $payment['vat'] ?? 0;

                                if (str_contains(strtolower($entry['vehicle_type'] ?? ''), 'monthly')) {
                                    // For monthly, rate is fixed, not per day
                                    $rate = $originalRate;
                                    $baseRate = '-';
                                } else {
                                    // For daily, recalculate rate based on capped days
                                    $dailyRate = $calculatedDays > 0 ? $originalRate / $calculatedDays : 0;
                                    $rate = $dailyRate * $days;
                                    $baseRate = $dailyRate;
                                }

                                if ($entry['issue_type'] !== 'free') {
                                    $rateTotal += $rate;
                                    $sslTotal  += $ssl;
                                    $vatTotal  += $vat;
                                    if (is_numeric($baseRate)) {
                                        $baseRateTotal += $baseRate;
                                    }
                                }
                            @endphp
                            <tr>
                                <td>{{ $i++ }}</td>
                                <td style="text-align: left !important;">{{ $entry['owner_name'] ?? 'N/A' }}</td>
                                <td>{{ $entry['vehicle_number'] ?? '-' }}</td>
                                <td style="text-align: left !important;">{{ $entry['revenue_license_number'] ?? '-' }}</td>
                                <td>{{ $entry['from_date'] }}</td>
                                <td>{{ $entry['to_date'] }}</td>
                                <td>{{ $entry['issue_type'] === 'free' ? '0.00' : (is_numeric($baseRate) ? number_format($baseRate, 2) : '-') }}</td>
                                <td>{{ $days }}</td>
                                <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($rate, 2) }}</td>
                                <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($ssl, 2) }}</td>
                                <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($vat, 2) }}</td>
                                <td><strong>{{ number_format($payment['total'], 2) }}</strong></td>
                                <td>
                                    <form method="POST" action="{{ route('permit.remove', $index) }}" class="delete-payment-form">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger delete-payment-btn" type="button"><i class="bi bi-trash"></i> Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="7" class="text-end"><strong>Total Payable Amount (LKR)</strong></td>
                            <td><strong>=</strong></td>
                            <td><strong>{{ number_format($rateTotal, 2) }}</strong></td>
                            <td><strong>{{ number_format($sslTotal, 2) }}</strong></td>
                            <td><strong>{{ number_format($vatTotal, 2) }}</strong></td>
                            <td><strong>{{ number_format($totalPayment, 2) }}</strong></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif

        {{-- ================= ACTION BUTTONS ================= --}}
        <div class="mt-4 text-center">
            <form method="POST" action="{{ route('payment.submit') }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="bi bi-wallet2 me-1"></i> Confirm & Pay
                </button>
            </form>
            
            @php
                // Determine the back route dynamically
                switch($firstType ?? 'TP') {
                    case 'TP': $backRoute = route('permit.temporary'); break;
                    case 'MP': $backRoute = route('permit.monthly'); break;
                    case 'VH': $backRoute = route('permit.vehicle'); break;
                    default: $backRoute = url()->previous();
                }
            @endphp
            <a href="{{ $backRoute }}" class="btn btn-secondary btn-lg ms-2">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </a>
        </div>

    </div>
</div>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete button clicks in payment summary
        const deleteForms = document.querySelectorAll('.delete-payment-form');
        
        deleteForms.forEach(form => {
            const deleteBtn = form.querySelector('.delete-payment-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Remove Entry?',
                        text: 'Are you sure you want to remove this entry from the payment summary?',
                        icon: 'warning',
                        iconColor: '#e53935',
                        showCancelButton: true,
                        confirmButtonColor: '#e53935',
                        cancelButtonColor: '#757575',
                        confirmButtonText: 'Yes, Remove',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'delete-popup',
                            title: 'delete-title',
                            confirmButton: 'delete-confirm-btn',
                            cancelButton: 'delete-cancel-btn'
                        },
                        buttonsStyling: true,
                        width: '400px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    });
</script>

<style>
    /* Custom SweetAlert2 styling for delete action */
    .delete-popup {
        border-radius: 0.75rem !important;
        padding: 1.5rem !important;
    }
    
    .delete-title {
        color: #e53935 !important;
        font-size: 1.25rem !important;
        font-weight: 600 !important;
    }
    
    .swal2-html-container {
        font-size: 0.95rem !important;
        color: #555 !important;
    }
    
    .delete-confirm-btn {
        border-radius: 0.375rem !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
    }
    
    .delete-cancel-btn {
        border-radius: 0.375rem !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
    }
    
    .swal2-icon.swal2-warning {
        border-color: #e53935 !important;
        color: #e53935 !important;
    }
</style>

@endsection