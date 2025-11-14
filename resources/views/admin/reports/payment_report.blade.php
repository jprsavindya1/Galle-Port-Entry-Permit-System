@extends('layouts.app')

@section('content')
<style>
    /* --- Subtle Blue Theme & Layout Adjustments --- */
    :root {
        --bs-blue: #4a86e8;
        --light-blue: #e8f0fe;
        --primary-light: #4a86e8;
        --bg-color: #f8faff; /* Very light blue background */
    }
    
    main {
        /* Scoped to main content area only, not affecting navbar */
        background-color: var(--bg-color);
        font-family: 'Inter', sans-serif;
    }

    main .container {
        max-width: 1400px; /* Maximize usable width for table */
    }

    main h4, main h6 {
        color: #1e3a8a; /* Darker blue for headings */
        font-weight: 600;
    }

    /* Card Styling - scoped to main */
    main .card {
        border: 1px solid #cceeff; /* Light blue border */
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(74, 134, 232, 0.08);
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

    /* Primary Button Style (Filter) - scoped to main to NOT affect navbar */
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
        font-size: 0.85rem; /* Smaller font for more columns */
    }

    main .table-bordered {
        border-color: #a0c3ff; /* Lighter border color */
    }
    
    /* Table Header */
    main .table thead th {
        background-color: #bbd0ff; /* Header background blue */
        color: #1e3a8a; /* Dark text */
        font-weight: 600;
        white-space: nowrap; /* Prevent header wrap */
        padding: 0.5rem 0.6rem; /* Reduced padding for compactness */
    }

    /* Table Body Cells */
    main .table tbody td {
        padding: 0.5rem 0.6rem; /* Reduced padding for compactness */
        white-space: nowrap; /* Keep content from wrapping to save horizontal space */
    }

    /* Ensure table-responsive container handles overflow gracefully */
    main .table-responsive {
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(74, 134, 232, 0.05);
    }
</style>

<div class="container py-4">
    <h4 class="mb-4">Payment / Batch Report</h4>

    <!-- Filters -->
    <form method="GET" action="{{ route('reports.payment') }}" class="row g-2 mb-4 align-items-end">
        <div class="col-6 col-md-3">
            <label class="form-label visually-hidden" for="type-select">Permit Type</label>
            <select id="type-select" name="type" class="form-select">
                <option value="">All Types</option>
                <option value="TP" {{ request('type')=='TP'?'selected':'' }}>TP</option>
                <option value="MP" {{ request('type')=='MP'?'selected':'' }}>MP</option>
                <option value="VH" {{ request('type')=='VH'?'selected':'' }}>VH</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label visually-hidden" for="range-select">Time Range</label>
            <select id="range-select" name="range" class="form-select">
                <option value="">All Time</option>
                <option value="day" {{ request('range')=='day'?'selected':'' }}>Day</option>
                <option value="week" {{ request('range')=='week'?'selected':'' }}>Week</option>
                <option value="month" {{ request('range')=='month'?'selected':'' }}>Month</option>
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label visually-hidden" for="date-input">Specific Date</label>
            <input id="date-input" type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-6 col-md-3">
            <button class="btn btn-primary w-100">
                <i class="fas fa-filter me-2"></i>Filter
            </button>
        </div>
    </form>

    <!-- Export Buttons -->
    <div class="mb-4 d-flex justify-content-end">
        <a href="{{ route('reports.payment.pdf', request()->query()) }}" 
           class="btn btn-sm btn-danger me-2" target="_blank">
            <i class="fas fa-file-pdf"></i> Export PDF
        </a>
        <a href="{{ route('reports.payment.csv', request()->query()) }}" 
           class="btn btn-sm btn-success" target="_blank">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
    </div>

    <!-- Summary -->
    <div class="card mb-4">
        <div class="card-body">
            <h6><i class="fas fa-calculator me-2"></i>Summary Totals</h6>
            <p class="mb-0">
                Rate: <span class="fw-medium">{{ number_format($summary['rate_total'],2) }}</span> |
                SSL: <span class="fw-medium">{{ number_format($summary['ssl_total'],2) }}</span> |
                VAT: <span class="fw-medium">{{ number_format($summary['vat_total'],2) }}</span> |
                Total: <strong class="text-primary-light">{{ number_format($summary['amount_total'],2) }}</strong>
            </p>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Submission ID</th>
                    <th>Permit Type</th>
                    <th>Company</th>
                    <th>Entry Count</th>
                    <th>Rate</th>
                    <th>SSL</th>
                    <th>VAT</th>
                    <th>Total</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                    <tr>
                        <td>{{ $p->invoice_id }}</td>
                        <td>{{ $p->submission_id }}</td>
                        <td>{{ $p->permit_type }}</td>
                        <td>{{ $p->permits->first()->company_name ?? '-' }}</td>
                        <td>{{ $p->entry_count }}</td>
                        <td>{{ number_format($p->rate_total,2) }}</td>
                        <td>{{ number_format($p->ssl_total,2) }}</td>
                        <td>{{ number_format($p->vat_total,2) }}</td>
                        <td class="fw-bold text-primary-light">{{ number_format($p->amount_total,2) }}</td>
                        <td>{{ $p->paid_at ? $p->paid_at->format('Y-m-d H:i') : ($p->payment_date ? $p->payment_date->format('Y-m-d') : '-') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
