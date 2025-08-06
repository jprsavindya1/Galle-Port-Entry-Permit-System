@extends('layouts.app')
@section('title', 'Add Designation')
@section('content')

<div class="container mt-4">
    <h3>Add Designation</h3>
    <form action="{{ route('admin.designations.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Designation Name</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>
        <button class="btn btn-success">Save</button>
        <a href="{{ route('admin.designations.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
