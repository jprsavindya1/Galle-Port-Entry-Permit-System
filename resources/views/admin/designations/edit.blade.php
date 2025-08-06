@extends('layouts.app')
@section('title', 'Edit Designation')
@section('content')

<div class="container mt-4">
    <h3>Edit Designation</h3>
    <form action="{{ route('admin.designations.update', $designation->id) }}" method="POST">
        @csrf @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Designation Name</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $designation->name) }}">
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <button class="btn btn-success">Update</button>
        <a href="{{ route('admin.designations.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
