@extends('layouts.app')

@section('title', 'Admin Activity Logs')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .log-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        padding: 2rem;
        margin-bottom: 2rem;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .badge-role {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
        border-radius: 0.5rem;
    }
    .badge-super-admin {
        background-color: #f8d7da;
        color: #842029;
    }
    .badge-admin {
        background-color: #cff4fc;
        color: #087990;
    }
    .badge-clerk {
        background-color: #d1e7dd;
        color: #0f5132;
    }
    .badge-staff {
        background-color: #fff3cd;
        color: #664d03;
    }
    .badge-unknown {
        background-color: #e2e3e5;
        color: #41464b;
    }
    .form-control, .form-select {
        border-radius: 0.5rem;
        border: 1px solid #ced4da;
    }
    .form-control:focus, .form-select:focus {
        border-color: #1976d2;
        box-shadow: 0 0 0 0.25rem rgba(25, 118, 210, 0.25);
    }
    .btn-action {
        border-radius: 0.5rem;
        font-weight: 500;
    }
    .table th {
        font-weight: 600;
        color: #13314C;
        background-color: #f8fafc;
        border-bottom: 2px solid #dee2e6;
    }
    pre {
        margin-bottom: 0;
        font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
</style>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 text-[#13314C]">
        <div class="d-flex align-items-center">
            <i class="bi bi-journal-text fs-2 me-3 text-[#1976d2]"></i>
            <div>
                <h2 class="mb-1" style="font-weight: 700;">Activity Logs</h2>
                <p class="text-muted mb-0" style="font-size: 0.9rem;">View and track system activity and administrative audit trails.</p>
            </div>
        </div>
    </div>

    <!-- Search & Filter Card -->
    <div class="log-card mb-4">
        <form method="GET" action="{{ route('admin.activity_logs.index') }}" class="row g-3">
            <div class="col-md-5">
                <label for="search" class="form-label font-semibold text-[#13314C]" style="font-size: 0.85rem;">Search</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0" id="search" name="search" value="{{ request('search') }}" placeholder="Search by user, action, or IP address...">
                </div>
            </div>

            <div class="col-md-4">
                <label for="role_filter" class="form-label font-semibold text-[#13314C]" style="font-size: 0.85rem;">Filter by Role</label>
                <select class="form-select" id="role_filter" name="role_filter">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ request('role_filter') == $role ? 'selected' : '' }}>
                            {{ ucwords(str_replace('-', ' ', $role)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-action w-100" style="background-color: #1976d2; border-color: #1976d2;">
                    <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <a href="{{ route('admin.activity_logs.index') }}" class="btn btn-outline-secondary btn-action w-100">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="log-card">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead>
                    <tr>
                        <th style="width: 15%;">Timestamp</th>
                        <th style="width: 15%;">User</th>
                        <th style="width: 12%;">Role</th>
                        <th style="width: 18%;">Action</th>
                        <th style="width: 10%;">IP Address</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td class="text-muted" style="font-size: 0.85rem;">
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                <small class="d-block text-black-50">{{ $log->created_at->diffForHumans() }}</small>
                            </td>
                            <td>
                                <div class="font-semibold text-dark">{{ $log->user_name ?? 'System / Guest' }}</div>
                                @if($log->user_id)
                                    <small class="text-muted">ID: {{ $log->user_id }}</small>
                                @endif
                            </td>
                            <td>
                                @if($log->role === 'super-admin')
                                    <span class="badge-role badge-super-admin">Super Admin</span>
                                @elseif($log->role === 'admin')
                                    <span class="badge-role badge-admin">Admin</span>
                                @elseif($log->role === 'clerk')
                                    <span class="badge-role badge-clerk">Clerk</span>
                                @elseif($log->role === 'staff')
                                    <span class="badge-role badge-staff">Staff</span>
                                @else
                                    <span class="badge-role badge-unknown">{{ $log->role ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td class="font-semibold text-[#1976d2]">
                                {{ $log->action }}
                            </td>
                            <td style="font-family: monospace; font-size: 0.85rem;">
                                {{ $log->ip_address }}
                            </td>
                            <td>
                                @if($log->details)
                                    @php
                                        // Decode details if string, or keep if array
                                        $detailsArray = is_string($log->details) ? json_decode($log->details, true) : $log->details;
                                    @endphp
                                    <details>
                                        <summary class="text-primary font-medium" style="cursor: pointer; font-size: 0.85rem; outline: none; user-select: none;">
                                            Expand Details
                                        </summary>
                                        <div class="mt-2 bg-light p-2 rounded border" style="max-height: 200px; overflow-y: auto;">
                                            <pre style="font-size: 0.8rem; line-height: 1.4; color: #333;">{{ json_encode($detailsArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                                        </div>
                                    </details>
                                @else
                                    <span class="text-muted" style="font-size: 0.85rem;">No details</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-info-circle fs-3 d-block mb-2"></i>
                                No activity logs found matching the filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div class="text-muted" style="font-size: 0.85rem;">
                Showing {{ $logs->firstItem() ?? 0 }} to {{ $logs->lastItem() ?? 0 }} of {{ $logs->total() }} entries
            </div>
            <div>
                {{ $logs->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
