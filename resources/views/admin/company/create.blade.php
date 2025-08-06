@extends('layouts.app')

@section('title', 'Add Company')

@section('content')
<div class="container mt-4">
    <h3>Add New Company</h3>

    <form action="{{ route('admin.companies.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Company Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Company Address</label>
            <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="3">{{ old('address') }}</textarea>
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
