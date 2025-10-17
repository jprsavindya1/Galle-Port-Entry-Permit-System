@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Cancelled Permits</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filter Form: Search + Date -->
    <form method="GET" action="{{ route('admin.cancelled_permits.index') }}" class="mb-3 row g-2 align-items-end">
        <div class="col-md-4">
            <label>Search</label>
            <input type="text" name="search" class="form-control" 
                   placeholder="Permit ID, Invoice, NIC, Name, Company, Vehicle..." 
                   value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label>From Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ request('from_date', $fromDate ?? '') }}">
        </div>
        <div class="col-md-3">
            <label>To Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ request('to_date', $toDate ?? '') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <!-- Cancelled Permits Table -->
    <table class="table table-bordered table-striped">
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
                    @if(isset($permit->type) && $permit->type === 'VP')
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
                    <a href="{{ route('admin.cancelled_permits.show', $permit->id) }}" class="btn btn-sm btn-info">View</a>
                    <!-- <form action="{{ route('admin.cancelled_permits.destroy', $permit->id) }}" method="POST" style="display:inline-block">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this record?')">Delete</button>
                    </form>-->
                </td>
            </tr>
        @empty
            <tr><td colspan="9">No cancelled permits found.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{ $cancelledPermits->appends(request()->all())->links() }}

    <!-- Export Buttons -->
    <div class="mb-3">
        <a href="{{ route('admin.cancelled_permits.exportPdf', request()->only('from_date','to_date')) }}" class="btn btn-warning">
            Export PDF
        </a>

        <a href="{{ route('admin.cancelled_permits.exportExcel', request()->only('from_date','to_date')) }}" class="btn btn-success">
            Export Excel
        </a>


    </div>
<!--
    <div class="mb-3">
        <a href="{{ route('admin.cancelled_permits.trash') }}" class=" btn btn-danger mb-3">
            View Trash
        </a>
    </div>-->
</div>
@endsection
