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
</style>

<div class="container py-4">
    <div class="user-dashboard-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="user-dashboard-title">
                <i class="bi bi-shield-x me-2"></i> Blacklist History
            </div>
            <div>
                <a href="{{ route('blacklist.exportPdf', request()->only('search', 'status_filter')) }}" class="btn btn-warning me-2">
                    <i class="bi bi-file-earmark-pdf"></i> Export PDF
                </a>
                <a href="{{ route('blacklist.exportExcel', request()->only('search', 'status_filter')) }}" class="btn btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Export Excel
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Filter Form -->
        <form method="GET" action="{{ route('blacklist.history') }}" class="mb-3 row g-2 align-items-end">
            <div class="col-md-8">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="NIC, Name, Company, Vehicle..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label">Action</label>
                <select name="status_filter" class="form-select">
                    <option value="">All Actions</option>
                    <option value="created" {{ request('status_filter') === 'created' ? 'selected' : '' }}>Blacklisted</option>
                    <option value="updated" {{ request('status_filter') === 'updated' ? 'selected' : '' }}>Updated</option>
                    <option value="reinstated" {{ request('status_filter') === 'reinstated' ? 'selected' : '' }}>Reinstated</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Blacklist History Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead>
                    <tr>
                        <th>NIC</th>
                        <th>Full Name</th>
                        <th>Company</th>
                        <th>Vehicle No</th>
                        <th>Reason</th>
                        <th>Action</th>
                        <th>Performed By</th>
                        <th>Performed On</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($blacklistHistory as $entry)
                    <tr>
                        <td>{{ $entry->nic ?? '-' }}</td>
                        <td>{{ $entry->full_name ?? '-' }}</td>
                        <td>{{ $entry->company_name ?? '-' }}</td>
                        <td>{{ $entry->vehicle_number ?? '-' }}</td>
                        <td>{{ $entry->reason ?? '-' }}</td>
                        <td>
                            @if($entry->action === 'created')
                                <span class="badge bg-danger">Blacklisted</span>
                            @elseif($entry->action === 'updated')
                                <span class="badge bg-info">Updated</span>
                            @elseif($entry->action === 'reinstated')
                                <span class="badge bg-success">Reinstated</span>
                            @elseif($entry->action === 'deleted')
                                <span class="badge bg-success">Reinstated</span>
                            @else
                                <span class="badge bg-secondary">{{ ucfirst($entry->action) }}</span>
                            @endif
                        </td>
                        <td>
                            @if($entry->action === 'reinstated' || $entry->action === 'deleted')
                                {{ $entry->reinstated_by ?? $entry->admin_name ?? '-' }}
                            @else
                                {{ $entry->admin_name ?? '-' }}
                            @endif
                        </td>
                        <td>
                            @if($entry->action === 'reinstated' || $entry->action === 'deleted')
                                {{ $entry->reinstated_on ? \Carbon\Carbon::parse($entry->reinstated_on)->format('Y-m-d H:i') : ($entry->created_at ? $entry->created_at->format('Y-m-d H:i') : '-') }}
                            @else
                                {{ $entry->created_at ? $entry->created_at->format('Y-m-d H:i') : '-' }}
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No blacklist history found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{ $blacklistHistory->appends(request()->all())->links() }}

        <div class="mt-3">
            <a href="{{ route('blacklist.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Blacklist
            </a>
        </div>

    </div>
</div>
@endsection
