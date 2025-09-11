@extends('layouts.app')
@section('title', 'Add Vehicle')
@section('content')
<div class="container mt-4">
    <h3>Add New Vehicle</h3>
    <form action="{{ route('admin.vehicles.store') }}" method="POST" class="ajax-form">
        @csrf

        <div class="mb-3">
            <label class="form-label">Vehicle Name <span class="text-danger">*</span></label>
            <input type="text" name="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $vehicle->name ?? '') }}" required>
            @error('name') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Vehicle Code <span class="text-danger">*</span></label>
            <input type="text" name="code" 
                   class="form-control @error('code') is-invalid @enderror" 
                   value="{{ old('code', $vehicle->code ?? '') }}" required>
            @error('code') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Base Rate (Rs) <span class="text-danger">*</span></label>
            <input type="number" name="rate" step="0.01" 
                   class="form-control @error('rate') is-invalid @enderror" 
                   value="{{ old('rate', $vehicle->rate ?? '') }}" required>
            @error('rate') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary ajax-link">Cancel</a>
    </form>
</div>
@endsection
