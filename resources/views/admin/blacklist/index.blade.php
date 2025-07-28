@extends('layouts.app')

@section('title', 'Blacklist Management')

@section('content')
<!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    
<div class="container">
<h3 class="fw-bold">Blacklist Entries</h3><br>


    

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>NIC</th>
                <th>Full Name</th>
                <th>Company</th>
                <th>Vehicle</th>
                <th>Reason</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($blacklists as $entry)
                <tr>
                    <td>{{ $entry->nic }}</td>
                    <td>{{ $entry->full_name }}</td>
                    <td>{{ $entry->company_name }}</td>
                    <td>{{ $entry->vehicle_number }}</td>
                    <td>{{ $entry->reason }}</td>
                    <td>
                        <a href="{{ route('blacklist.edit', $entry) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('blacklist.destroy', $entry) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this entry?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('blacklist.create') }}" class="btn btn-primary mb-3">Add New Entry</a>

    {{ $blacklists->links() }}
</div>
@endsection
