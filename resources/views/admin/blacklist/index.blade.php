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
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($blacklists as $entry)
            @php
                $log = $entry->activities->first();
            @endphp
            <tr>
                <td>{{ $entry->nic }}</td>
                <td>{{ $entry->full_name }}</td>
                <td>{{ $entry->company_name }}</td>
                <td>{{ $entry->vehicle_number }}</td>
                <td>{{ $entry->reason }}</td>
                <td>{{ $log->user_name ?? '—' }}</td>
                <td>{{ $log ? $log->created_at->format('Y-m-d H:i') : '—' }}</td>
                <td>
                    <a href="{{ route('blacklist.edit', $entry) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('blacklist.destroy', $entry) }}" method="POST" style="display:inline;">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this entry?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center">No results found.</td></tr>
        @endforelse
    </tbody>
</table>


    {{ $blacklists->links() }}
</div>
@endsection
