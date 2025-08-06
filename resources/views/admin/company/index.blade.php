@extends('layouts.app')

@section('title', 'Company List')

@section('content')
<div class="container mt-4">
    <h3>Company List</h3>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

   <form method="GET" action="{{ route('admin.companies.index') }}" class="d-flex mb-3">
    <input
        type="text"
        name="search"
        value="{{ request('search') }}"
        class="form-control me-2"
        placeholder="Search company..."
    >
    <button type="submit" class="btn btn-outline-primary">Search</button>
</form>


    <a href="{{ route('admin.companies.create') }}" class="btn btn-primary mb-3">Add New Company</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Company Name</th>
                <th>Address</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($companies as $company)
                <tr>
                    <td>{{ $loop->iteration + ($companies->currentPage() - 1) * $companies->perPage() }}</td>
                    <td>{{ $company->name }}</td>
                    <td>{{ $company->address ?? '-' }}</td>
                    <td>
                        <a href="{{ route('admin.companies.edit', $company) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form action="{{ route('admin.companies.destroy', $company) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure to delete this company?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No companies found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $companies->withQueryString()->links() }}
</div>
@endsection
