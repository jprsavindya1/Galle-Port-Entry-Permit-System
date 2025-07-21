@extends('layouts.app')

@section('content')
<!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<div class="container">
    <h2>Edit Vehicle Permit Entry</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('permit.vehicle.updateSessionEntry', $index) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label>Vehicle Type</label>
            <input type="text" name="vehicle_type" class="form-control" value="{{ old('vehicle_type', $entry['vehicle_type']) }}" required>
        </div>

        <div class="mb-3">
            <label>Vehicle Number</label>
            <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number', $entry['vehicle_number']) }}" required>
        </div>

        <div class="mb-3">
            <label>Revenue License Number</label>
            <input type="text" name="revenue_license_number" class="form-control" value="{{ old('revenue_license_number', $entry['revenue_license_number']) }}" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>From Date</label>
                <input type="date" name="from_date" class="form-control" value="{{ old('from_date', $entry['from_date']) }}" required>
            </div>
            <div class="col-md-6">
                <label>To Date</label>
                <input type="date" name="to_date" class="form-control" value="{{ old('to_date', $entry['to_date']) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label>Issue Type</label>
            <select name="issue_type" class="form-control" required>
                <option value="free" {{ old('issue_type', $entry['issue_type']) === 'free' ? 'selected' : '' }}>Free</option>
                <option value="payment" {{ old('issue_type', $entry['issue_type']) === 'payment' ? 'selected' : '' }}>Payment</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Vehicle Owner's Name</label>
            <input type="text" name="owner_name" class="form-control" value="{{ old('owner_name', $entry['owner_name']) }}" required>
        </div>

        <div class="mb-3">
            <label>Vehicle Owner's Address</label>
            <textarea name="owner_address" class="form-control" required>{{ old('owner_address', $entry['owner_address']) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Company Name</label>
            <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $entry['company_name']) }}" required>
        </div>

        <div class="mb-3">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control">{{ old('remarks', $entry['remarks']) }}</textarea>
        </div>

        <button type="submit" class="btn btn-primary">Update Entry</button>
        <a href="{{ route('permit.vehicle') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
