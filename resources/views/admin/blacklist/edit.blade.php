@extends('layouts.app')

@section('title', 'Edit Blacklist Entry')

@section('content')
<div class="container">
    <h2>Edit Blacklist Entry</h2>

    <form action="{{ route('blacklist.update', $blacklist) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="nic" class="form-label">NIC</label>
            <input type="text" name="nic" class="form-control" value="{{ old('nic', $blacklist->nic) }}">
        </div>

        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $blacklist->full_name) }}">
        </div>

        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $blacklist->company_name) }}">
        </div>

        <div class="mb-3">
            <label for="vehicle_number" class="form-label">Vehicle Number</label>
            <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number', $blacklist->vehicle_number) }}">
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
            <textarea name="reason" class="form-control" required>{{ old('reason', $blacklist->reason) }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Update Entry</button>
        <a href="{{ route('blacklist.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
