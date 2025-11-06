@extends('layouts.app')

@section('content')
<style>
    /* --- Subtle Blue Theme & Layout Adjustments --- */
    :root {
        --bs-blue: #4a86e8;
        --light-blue: #e8f0fe;
        --primary-light: #4a86e8;
        --bg-color: #f8faff; /* Very light blue background */
        --dark-heading: #1e3a8a;
    }
    
    main {
        background-color: var(--bg-color);
        font-family: 'Inter', sans-serif;
    }

    main .container {
        max-width: 100%; /* Use full width available */
        padding-left: 1rem;
        padding-right: 1rem;
    }

    main h4, main h5, main h6 {
        color: var(--dark-heading); 
        font-weight: 600;
    }

    /* Card Styling - scoped to main */
    main .card {
        border: 1px solid #cceeff; /* Light blue border */
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(74, 134, 232, 0.08);
        background-color: #ffffff; /* Ensure cards are white */
    }

    /* Form Controls - scoped to main */
    main .form-select, main .form-control {
        border-radius: 8px;
        border-color: #bbd0ff;
        transition: border-color 0.2s;
    }
    main .form-select:focus, main .form-control:focus {
        border-color: var(--primary-light);
        box-shadow: 0 0 0 0.25rem rgba(74, 134, 232, 0.25);
    }

    /* Primary Button Style (Search) - scoped to main to NOT affect navbar logout */
    main .btn-primary {
        background-color: var(--bs-blue);
        border-color: var(--bs-blue);
        border-radius: 8px;
        font-weight: 500;
        transition: background-color 0.2s, transform 0.1s;
    }
    main .btn-primary:hover {
        background-color: #3b6cd9;
        border-color: #3b6cd9;
        transform: translateY(-1px);
    }

    /* Export Buttons - scoped to main */
    main .btn-danger, main .btn-success {
        border-radius: 8px;
        font-weight: 500;
        transition: opacity 0.2s;
    }
    
    /* --- Table Specific Styling for Compactness and Theme - scoped to main --- */
    main .table {
        --bs-table-bg: #ffffff;
        --bs-table-striped-bg: var(--light-blue); /* Light blue stripe */
        border-radius: 10px;
        overflow: hidden; 
        font-size: 0.75rem; /* Smaller font for better fit */
    }

    main .table-bordered {
        border-color: #a0c3ff; /* Lighter border color */
    }
    
    /* Table Header */
    main .table thead th {
        background-color: #bbd0ff; /* Header background blue */
        color: var(--dark-heading); 
        font-weight: 600;
        white-space: nowrap; /* Prevent header wrap */
        padding: 0.35rem 0.4rem; /* Reduced padding for compactness */
        font-size: 0.7rem; /* Smaller header font */
    }

    /* Table Body Cells */
    main .table tbody td {
        padding: 0.35rem 0.4rem; /* Reduced padding for compactness */
        white-space: normal; /* Allow wrapping for better fit */
        word-wrap: break-word;
        max-width: 150px; /* Prevent cells from growing too wide */
        font-size: 0.75rem;
    }
    
    /* Specific column width controls */
    main .table tbody td:nth-child(1) { max-width: 90px; } /* Permit ID */
    main .table tbody td:nth-child(4) { max-width: 120px; } /* Company */
    main .table tbody td:nth-child(5), 
    main .table tbody td:nth-child(6) { max-width: 85px; } /* Dates */
    main .table tbody td:nth-child(8) { max-width: 100px; } /* Reason */
    main .table tbody td:nth-child(9) { max-width: 65px; white-space: nowrap; } /* Docs */
    main .table tbody td:nth-child(10) { max-width: 75px; } /* Status */
    main .table tbody td:nth-child(11),
    main .table tbody td:nth-child(12) { max-width: 110px; } /* Submission/Invoice ID */

    /* Ensure table-responsive container handles overflow gracefully */
    main .table-responsive {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(74, 134, 232, 0.05);
    }
    
    /* Status Badges */
    main .badge-status-active {
        background-color: #4CAF50 !important; /* Green */
        color: white;
    }
    main .badge-status-cancelled {
        background-color: #F44336 !important; /* Red */
        color: white;
    }
</style>

<div class="container-fluid py-4">
    <h4 class="mb-3">User / Entity Permit Report</h4>

    <!-- Filters -->
    <form method="GET" action="{{ route('reports.user') }}" class="row g-2 mb-3 align-items-end">
        <div class="col-md-5">
            <label class="form-label visually-hidden" for="query-input">Search NIC / Name / Company</label>
            <input id="query-input" type="text" name="query" class="form-control" placeholder="Enter NIC / Name / Company" value="{{ request('query') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label visually-hidden" for="type-select">Permit Type</label>
            <select id="type-select" name="type" class="form-select">
                <option value="">All Types</option>
                <option value="TP" {{ request('type')=='TP'?'selected':'' }}>TP</option>
                <option value="MP" {{ request('type')=='MP'?'selected':'' }}>MP</option>
                <option value="VP" {{ request('type')=='VP'?'selected':'' }}>VP</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">
                <i class="fas fa-search me-1"></i>Search
            </button>
        </div>
    </form>

    @if(isset($permits) && $permits->isNotEmpty())
        <!-- Export Buttons -->
        <div class="mb-4 d-flex justify-content-end">
            <a href="{{ route('reports.user.pdf', request()->query()) }}" class="btn btn-sm btn-danger me-2" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="{{ route('reports.user.csv', request()->query()) }}" class="btn btn-sm btn-success" target="_blank">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
        </div>

        @php $grouped = $permits->groupBy('type'); @endphp

        <!-- Table per Permit Type -->
        @foreach($grouped as $type => $permitsByType)
            <div class="mb-5">
                <h5 class="mb-3">{{ strtoupper($type) }} Permits</h5>

                <!-- Summary Totals for this Group -->
                @php
                    // Recalculating summary inside the loop to ensure accurate counts for the current type
                    $summary = [
                        'count' => $permitsByType->count(),
                        'active' => $permitsByType->where('status','Active')->count(),
                        'cancelled' => $permitsByType->where('status','Cancelled')->count(),
                    ];
                @endphp
                <div class="card mb-3">
                    <div class="card-body py-2">
                        <p class="mb-0">
                            Total Permits: <span class="fw-bold text-primary-light">{{ $summary['count'] }}</span> |
                            Active: <span class="fw-medium text-success">{{ $summary['active'] }}</span> |
                            Cancelled: <span class="fw-medium text-danger">{{ $summary['cancelled'] }}</span>
                        </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead>
                            <tr>
                                <th>Permit ID</th>
                                @if($type === 'VP')
                                    <th>Owner Name</th>
                                    <th>Vehicle Number</th>
                                @else
                                    <th>Full Name</th>
                                    <th>ID Number</th>
                                @endif
                                <th>Company Name</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Issue Type</th>
                                <th>Reason</th>
                                <th>Docs</th>
                                <th>Status</th>
                                <th>Submission ID</th>
                                <th>Invoice ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permitsByType as $permit)
                                <tr>
                                    <td>{{ $permit->permit_id }}</td>
                                    @if($type === 'VP')
                                        <td>{{ $permit->owner_name }}</td>
                                        <td>{{ $permit->vehicle_number }}</td>
                                    @else
                                        <td>{{ $permit->full_name }}</td>
                                        <td>{{ $permit->id_number }}</td>
                                    @endif
                                    <td>{{ $permit->company_name }}</td>
                                    <td>{{ $permit->from_date }}</td>
                                    <td>{{ $permit->to_date }}</td>
                                    <td>{{ ucfirst($permit->issue_type) }}</td>
                                    <td title="{{ $permit->reason }}">{{ Str::limit($permit->reason, 15) }}</td>
                                    <td>
                                        @php
                                            $submittedDocs = [];
                                            $hasAnyDoc = false;
                                            
                                            if ($type === 'TP') {
                                                if ($permit->doc_nic) { $submittedDocs[] = 'NIC'; $hasAnyDoc = true; }
                                                if ($permit->doc_passport) { $submittedDocs[] = 'Passport'; $hasAnyDoc = true; }
                                                if ($permit->doc_driving_licence) { $submittedDocs[] = 'Driving Licence'; $hasAnyDoc = true; }
                                            } elseif ($type === 'MP') {
                                                if ($permit->doc_nic) { $submittedDocs[] = 'NIC'; $hasAnyDoc = true; }
                                                if ($permit->doc_police_report) { $submittedDocs[] = 'Police Report'; $hasAnyDoc = true; }
                                            } elseif ($type === 'VP') {
                                                if ($permit->doc_revenue_licence) { $submittedDocs[] = 'Revenue Licence'; $hasAnyDoc = true; }
                                                if ($permit->doc_insurance) { $submittedDocs[] = 'Insurance'; $hasAnyDoc = true; }
                                            }
                                            
                                            $tooltipText = count($submittedDocs) > 0 ? implode(', ', $submittedDocs) : 'No documents submitted';
                                        @endphp
                                        <span title="{{ $tooltipText }}" style="cursor: help; font-size: 1rem;">
                                            @if($hasAnyDoc)
                                                <span style="color: green;">✓</span>
                                            @else
                                                <span style="color: #999;">✗</span>
                                            @endif
                                        </span>
                                    </td> 
                                    <td>
                                        <span class="badge rounded-pill 
                                            @if($permit->status === 'Active') badge-status-active
                                            @elseif($permit->status === 'Cancelled') badge-status-cancelled
                                            @else text-bg-secondary @endif
                                        ">
                                            {{ $permit->status }}
                                        </span>
                                    </td>
                                    <td>{{ $permit->submission_id }}</td>
                                    <td>{{ $permit->payment->invoice_id ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @elseif(request()->has('query') || request()->has('type'))
        <div class="alert alert-warning mt-4 rounded-lg shadow">
            <i class="fas fa-exclamation-triangle me-2"></i> No permits found for the given criteria. Please try a different search.
        </div>
    @else
        <div class="alert alert-info mt-4 rounded-lg shadow">
            <i class="fas fa-info-circle me-2"></i> Enter a search query (NIC, Name, or Company) or select a permit type to view the report.
        </div>
    @endif
</div>
@endsection
