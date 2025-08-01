@extends('layouts.app')

@section('title', 'Add Blacklist Entry')

@section('content')
<!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    
    <!-- blacklist entry form-->
<div class="container">
    <h2>Add Blacklist Entry</h2>

    <form action="{{ route('blacklist.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="nic" class="form-label">NIC</label>
            <input type="text" name="nic" class="form-control" value="{{ old('nic') }}">
        </div>

        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}">
        </div>

        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}">
        </div>

        <div class="mb-3">
            <label for="vehicle_number" class="form-label">Vehicle Number</label>
            <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number') }}">
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
            <textarea name="reason" class="form-control" required>{{ old('reason') }}</textarea>
        </div>

        <button type="submit" class="btn btn-success">Add Entry</button>
        <a href="{{ route('blacklist.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
