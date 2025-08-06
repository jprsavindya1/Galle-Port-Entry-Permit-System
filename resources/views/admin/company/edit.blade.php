@extends('layouts.app')

@section('title', 'Edit Company')

@section('content')
<div class="container mt-4">
    <h3>Edit Company</h3>

    <form action="{{ route('admin.companies.update', $company) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Company Name<span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $company->name) }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="address" class="form-label">Company Address</label>
            <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="3">{{ old('address', $company->address) }}</textarea>
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
