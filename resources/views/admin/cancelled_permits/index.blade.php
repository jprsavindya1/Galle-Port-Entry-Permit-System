@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .user-dashboard-card { background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%); border-radius:1rem; box-shadow:0 3px 15px rgba(0,0,0,0.08); padding:1.25rem; }
    .user-dashboard-title { font-size:1.5rem; font-weight:600; color:#1976d2; margin-bottom:0.75rem; border-bottom:1px solid #bbdefb; padding-bottom:0.5rem; }
    main .form-control, main .form-select { border-radius:0.5rem; border:1px solid #bbdefb; background:#f8fafc; }
    main .form-label { color:#1976d2; font-weight:500; }
    main .table thead th { background:#e3f2fd; color:#1976d2; font-weight:600; }
    main .btn-primary { background:#1976d2; border-color:#1976d2; }
    main .btn-secondary { background:#6c757d; border-color:#6c757d; color:#fff; }
    .export-group a { margin-right:0.5rem; }
</style>

<div class="container py-4">
    <div class="user-dashboard-card">
        <div class="user-dashboard-title"><i class="bi bi-file-earmark-x-fill me-2"></i> Cancelled Permits</div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Filter Form: Search + Date -->
        <form method="GET" action="{{ route('admin.cancelled_permits.index') }}" class="mb-3 row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="Permit ID, Invoice, NIC, Name, Company, Vehicle..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ request('from_date', $fromDate ?? '') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ request('to_date', $toDate ?? '') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Cancelled Permits Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>Permit ID</th>
                        <th>Invoice ID</th>
                        <th>Name</th>
                        <th>Company</th>
                        <th>Vehicle No</th>
                        <th>Cancelled Reason</th>
                        <th>Cancelled At</th>
                        <th>Cancelled By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($cancelledPermits as $permit)
                    <tr>
                        <td>{{ $permit->permit_id }}</td>
                        <td>{{ $permit->invoice_id }}</td>
                        <td>
                            @if(isset($permit->type) && $permit->type === 'VH')
                                {{ $permit->owner_name ?? ($permit->full_name ?? '-') }}
                            @else
                                {{ $permit->full_name ?? ($permit->owner_name ?? '-') }}
                            @endif
                        </td>
                        <td>{{ $permit->company_name }}</td>
                        <td>{{ $permit->vehicle_number }}</td>
                        <td>{{ $permit->cancel_reason }}</td>
                        <td>{{ $permit->cancelled_at }}</td>
                        <td>{{ $permit->cancelled_by }}</td>
                        <td>
                            <a href="{{ route('admin.cancelled_permits.show', $permit->id) }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted">No cancelled permits found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $cancelledPermits->appends(request()->all())->links() }}

        <!-- Export Buttons -->
        <div class="mb-3 export-group">
            <a href="{{ route('admin.cancelled_permits.exportPdf', request()->only('from_date','to_date')) }}" class="btn btn-warning">
                <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
            </a>

            <a href="{{ route('admin.cancelled_permits.exportExcel', request()->only('from_date','to_date')) }}" class="btn btn-success">
                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
            </a>
        </div>

    </div>
</div>
@endsection
