@extends('layouts.app')

@section('title', 'Blacklist Management')

@section('content')
<div class="container">
    <h3 class="fw-bold">Blacklist Entries</h3><br>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search and Export -->
    <form method="GET" action="{{ route('blacklist.index') }}" class="mb-3 d-flex gap-2">
        <input type="text" name="search" class="form-control" placeholder="Search by NIC, Name, Company, or Vehicle" value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('blacklist.index') }}" class="btn btn-secondary">Reset</a>
    </form>

    <div class="mb-3">
        <a href="{{ route('blacklist.exportPdf', ['search' => request('search')]) }}" class="btn btn-danger">Export PDF</a>
        <a href="{{ route('blacklist.exportExcel', ['search' => request('search')]) }}" class="btn btn-success">Export Excel</a>
        <a href="{{ route('blacklist.create') }}" class="btn btn-primary float-end">Add New Entry</a>
    </div>

    <table class="table table-bordered">
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
                <th>Actions</th>
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
                        <a href="'.route('blacklist.edit', $entry).'" class="btn btn-sm btn-warning">Edit</a>
                        <form action="'.route('blacklist.destroy', $entry).'" method="POST" style="display:inline;">
                            '.csrf_field().method_field('DELETE').'
                            <button class="btn btn-sm btn-danger" onclick="return confirm(\'Delete this entry?\')">Delete</button>
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
                    <td>{!! $actions !!}</td>
                @endif
            </tr>
        @empty
            <tr>
                <td colspan="{{ $showReinstated ? ($showActions ? 11 : 9) : ($showActions ? 9 : 8) }}" class="text-center">No results found.</td>
            </tr>
        @endforelse
    </tbody>
</table>


    @if(!request('search'))
        {{ $blacklists->links() }}
    @endif
</div>
@endsection
