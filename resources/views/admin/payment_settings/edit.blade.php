@extends('layouts.app')

@section('title', 'Edit Payment Information')

@section('content')
<div class="container mt-4">
    <h2>Edit Payment Information</h2>
<!-- payment variables edit interface, rate - nbt - vat -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('admin.payment_settings.update') }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="rate" class="form-label">Base Rate</label>
            <input type="number" step="0.01" name="rate" id="rate" value="{{ old('rate', $settings->rate) }}" class="form-control" required>
            @error('rate') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="nbt" class="form-label">NBT (%)</label>
            <input type="number" step="0.01" name="nbt" id="nbt" value="{{ old('nbt', $settings->nbt) }}" class="form-control" required>
            @error('nbt') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="vat" class="form-label">VAT (%)</label>
            <input type="number" step="0.01" name="vat" id="vat" value="{{ old('vat', $settings->vat) }}" class="form-control" required>
            @error('vat') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="mb-3">
            <label for="ssl" class="form-label">SSL Amount</label>
            <input type="number" step="0.01" name="ssl" id="ssl" 
                value="{{ old('ssl', $settings->ssl) }}" 
                class="form-control" required>
            @error('ssl') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="{{ route('dashboard') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection
