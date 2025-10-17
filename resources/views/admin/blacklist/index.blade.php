@extends('layouts.app')

@section('title', 'Blacklist Management')

@section('content')
<style>
    .user-dashboard-card { background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%); border-radius: 1rem; box-shadow: 0 3px 15px rgba(0,0,0,0.08); padding: 2rem 2rem 1.5rem 2rem; margin-bottom: 2rem; border: none; }
    .user-dashboard-title { font-size: 1.75rem; font-weight: 600; color: #1976d2; }
    .user-dashboard-table { background: #f5faff; border-radius: 0.75rem; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
    .user-dashboard-table th { background: #e3f2fd; color: #1976d2; font-weight: 500; border-bottom: 2px solid #bbdefb; }
    .user-dashboard-table td { background: #f8fafc; color: #333; vertical-align: middle; }
    .user-action-btn { font-size: 0.95rem; padding: 0.35rem 0.8rem; border-radius: 0.5rem; margin-right: 0.25rem; transition: background 0.2s, color 0.2s; }
    .user-action-btn.edit { background: #fff3e0; color: #ff9800; border: 1px solid #ffe0b2; }
    .user-action-btn.delete { background: #ffebee; color: #e53935; border: 1px solid #ffcdd2; }
    .user-action-btn.edit:hover { background:#ffe0b2; color:#e65100 }
    .user-action-btn.delete:hover { background:#ffcdd2; color:#b71c1c }
</style>

<div class="container py-4">
    <div class="user-dashboard-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="user-dashboard-title">Blacklist Entries</div>
            <div>
                <a href="{{ route('blacklist.exportPdf', ['search' => request('search')]) }}" class="btn btn-warning me-2">Export PDF</a>
                <a href="{{ route('blacklist.exportExcel', ['search' => request('search')]) }}" class="btn btn-success me-2">Export Excel</a>
                <a href="{{ route('blacklist.create') }}" class="btn btn-primary">Add New Entry</a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="GET" action="{{ route('blacklist.index') }}" class="row g-3 mb-3 align-items-end" style="background:linear-gradient(135deg,#e3f2fd 0%,#f8fafc 100%);border-radius:0.75rem;padding:1rem;">
            <div class="col-md-8">
                <label class="form-label mb-1">Search</label>
                <input type="text" name="search" class="form-control" placeholder="Search by NIC, Name, Company, or Vehicle" value="{{ request('search') }}" style="border-radius:0.5rem;border:1px solid #bbdefb;background:#f8fafc;">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Search</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('blacklist.index') }}" class="btn btn-secondary w-100">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table user-dashboard-table align-middle">
                <thead>
                    <tr>
                        <th>NIC</th>
                        <th>Full Name</th>
                        <th>Company</th>
                        <th>Vehicle</th>
                        <th>Reason</th>
                        <th>Added By</th>
                        <th>Added On</th>
                        <th>Status</th>
                        @php
                            $showReinstated = $blacklists->contains(fn($entry) => $entry instanceof \App\Models\BlacklistHistory);
                            $showActions = $blacklists->contains(fn($entry) => !($entry instanceof \App\Models\BlacklistHistory));
                        @endphp
                        @if($showReinstated)
                            <th>Reinstated By</th>
                            <th>Reinstated On</th>
                        @endif
                        @if($showActions)
                            <th class="text-center">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse ($blacklists as $entry)
                        @php
                            $isHistory = $entry instanceof \App\Models\BlacklistHistory;

                            if ($isHistory) {
                                $addedBy = $entry->admin_name ?? '—';
                                $addedOn = $entry->created_at->format('Y-m-d H:i');
                                $status = $entry->status ?? ucfirst($entry->action ?? 'Deleted');
                                $reinstatedBy = $entry->reinstated_by ?? '—';
                                $reinstatedOn = $entry->reinstated_on ? \Carbon\Carbon::parse($entry->reinstated_on)->format('Y-m-d H:i') : '—';
                                $actions = null;
                            } else {
                                $log = $entry->activities->first();
                                $addedBy = $log->user_name ?? '—';
                                $addedOn = $log ? $log->created_at->format('Y-m-d H:i') : '—';
                                $status = 'Blacklisted';
                                $reinstatedBy = null;
                                $reinstatedOn = null;
                                $actions = '
                                    <a href="'.route('blacklist.edit', $entry).'" class="user-action-btn edit"><i class="bi bi-pencil-square"></i> Edit</a>
                                    <form action="'.route('blacklist.destroy', $entry).'" method="POST" style="display:inline;">
                                        '.csrf_field().method_field('DELETE').'
                                        <button class="user-action-btn delete" onclick="return confirm(\'Delete this entry?\')"><i class="bi bi-trash"></i> Delete</button>
                                    </form>
                                ';
                            }
                        @endphp
                        <tr class="{{ $isHistory ? 'table-secondary' : '' }}">
                            <td>{{ $entry->nic }}</td>
                            <td>{{ $entry->full_name }}</td>
                            <td>{{ $entry->company_name }}</td>
                            <td>{{ $entry->vehicle_number }}</td>
                            <td>{{ $entry->reason }}</td>
                            <td>{{ $addedBy }}</td>
                            <td>{{ $addedOn }}</td>
                            <td>{{ $status }}</td>
                            @if($isHistory)
                                <td>{{ $reinstatedBy }}</td>
                                <td>{{ $reinstatedOn }}</td>
                            @endif
                            @if(!$isHistory)
                                <td class="text-center">{!! $actions !!}</td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $showReinstated ? ($showActions ? 11 : 9) : ($showActions ? 9 : 8) }}" class="text-center text-muted">No results found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(!request('search'))
            <div class="mt-3">{{ $blacklists->links() }}</div>
        @endif
    </div>
</div>

    <!-- Bootstrap Icons CDN (ensure icons render the same as users index) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    @endsection
