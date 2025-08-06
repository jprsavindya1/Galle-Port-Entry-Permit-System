@extends('layouts.app')
@section('title', 'Designation List')
@section('content')

<div class="container mt-4">
    <h3>Designations</h3>
    <a href="{{ route('admin.designations.create') }}" class="btn btn-primary mb-3">Add Designation</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr><th>#</th><th>Name</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @foreach($designations as $designation)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $designation->name }}</td>
                <td>
                    <a href="{{ route('admin.designations.edit', $designation->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.designations.destroy', $designation->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this designation?');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
