@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="mb-4">Edit Permit</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('permits.update', $permit) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">ID Type</label>
            <select name="id_type" class="form-select" required>
                <option value="NIC" {{ $permit->id_type == 'NIC' ? 'selected' : '' }}>NIC</option>
                <option value="Passport" {{ $permit->id_type == 'Passport' ? 'selected' : '' }}>Passport</option>
                <option value="License" {{ $permit->id_type == 'License' ? 'selected' : '' }}>License</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">ID Number</label>
            <input type="text" name="id_number" class="form-control" value="{{ $permit->id_number }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">From Date</label>
            <input type="date" name="from_date" class="form-control" value="{{ $permit->from_date }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">To Date</label>
            <input type="date" name="to_date" class="form-control" value="{{ $permit->to_date }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" value="{{ $permit->full_name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Initials</label>
            <input type="text" name="initials" class="form-control" value="{{ $permit->initials }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Designation</label>
            <input type="text" name="designation" class="form-control" value="{{ $permit->designation }}">
        </div>

        <div class="mb-3">
            <label class="form-label">Company Name</label>
            <input type="text" name="company_name" class="form-control" value="{{ $permit->company_name }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Company Address</label>
            <textarea name="company_address" class="form-control" rows="2">{{ $permit->company_address }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Residence Address</label>
            <textarea name="residence_address" class="form-control" rows="2">{{ $permit->residence_address }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label d-block">Pass Type</label>
            @php $passTypes = explode(',', $permit->pass_type); @endphp
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="pass_type[]" value="onboard" {{ in_array('onboard', $passTypes) ? 'checked' : '' }}>
                <label class="form-check-label">Onboard</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="pass_type[]" value="afloat" {{ in_array('afloat', $passTypes) ? 'checked' : '' }}>
                <label class="form-check-label">Afloat</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="pass_type[]" value="ashore" {{ in_array('ashore', $passTypes) ? 'checked' : '' }}>
                <label class="form-check-label">Ashore</label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label d-block">Issue Type</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="issue_type" value="free" {{ $permit->issue_type == 'free' ? 'checked' : '' }}>
                <label class="form-check-label">Free Issue</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="issue_type" value="payment" {{ $permit->issue_type == 'payment' ? 'checked' : '' }}>
                <label class="form-check-label">On Payment</label>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Reason for Visit</label>
            <select name="reason" class="form-select" required>
                <option value="inspection" {{ $permit->reason == 'inspection' ? 'selected' : '' }}>Inspection</option>
                <option value="delivery" {{ $permit->reason == 'delivery' ? 'selected' : '' }}>Delivery</option>
                <option value="official_visit" {{ $permit->reason == 'official_visit' ? 'selected' : '' }}>Official Visit</option>
                <option value="maintenance" {{ $permit->reason == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="other" {{ $permit->reason == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Permit</button>
    </form>
</div>
@endsection
