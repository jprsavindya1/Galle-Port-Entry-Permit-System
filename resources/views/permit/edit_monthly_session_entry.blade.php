@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="my-4">Edit Monthly Permit Session Entry</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('permit.monthly.updateSessionEntry', $index) }}">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-6">
                <label>ID Type</label>
                <input type="text" class="form-control" name="id_type" value="NIC" readonly>
            </div>
            <div class="col-md-6">
                <label>ID Number</label>
                <input type="text" class="form-control" name="id_number" value="{{ old('id_number', $entry['id_number']) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>From Date</label>
                <input type="date" class="form-control" name="from_date" value="{{ old('from_date', $entry['from_date']) }}" required>
            </div>
            <div class="col-md-6">
                <label>To Date</label>
                <input type="date" class="form-control" name="to_date" value="{{ old('to_date', $entry['to_date']) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label>Full Name</label>
            <input type="text" class="form-control" name="full_name" value="{{ old('full_name', $entry['full_name']) }}" required>
        </div>

        <div class="mb-3">
            <label>Name with Initials</label>
            <input type="text" class="form-control" name="initials" value="{{ old('initials', $entry['initials']) }}" required>
        </div>

        <div class="mb-3">
            <label>Designation</label>
            <input type="text" class="form-control" name="designation" value="{{ old('designation', $entry['designation']) }}">
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Company Name</label>
                <input type="text" class="form-control" name="company_name" value="{{ old('company_name', $entry['company_name']) }}" required>
            </div>
            <div class="col-md-6">
                <label>Company Address</label>
                <textarea class="form-control" name="company_address" rows="2" required>{{ old('company_address', $entry['company_address']) }}</textarea>
            </div>
        </div>

        <div class="mb-3">
            <label>Residence Address</label>
            <textarea class="form-control" name="residence_address" rows="2">{{ old('residence_address', $entry['residence_address']) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Pass Type</label><br>
           @php
            $selectedTypes = old('pass_type', isset($entry['pass_type']) ? explode(',', $entry['pass_type']) : []);
            @endphp

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="pass_type[]" value="onboard" {{ in_array('onboard', $selectedTypes) ? 'checked' : '' }}>
                <label class="form-check-label">Onboard</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="pass_type[]" value="afloat" {{ in_array('afloat', $selectedTypes) ? 'checked' : '' }}>
                <label class="form-check-label">Afloat</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="pass_type[]" value="ashore" {{ in_array('ashore', $selectedTypes) ? 'checked' : '' }}>
                <label class="form-check-label">Ashore</label>
            </div>
        </div>

        <div class="mb-3">
            <label>Issue Type</label><br>
            @php $issueType = old('issue_type', $entry['issue_type'] ?? 'free'); @endphp
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="issue_type" value="free" {{ $issueType === 'free' ? 'checked' : '' }}>
                <label class="form-check-label">Free</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="issue_type" value="payment" {{ $issueType === 'payment' ? 'checked' : '' }}>
                <label class="form-check-label">On Payment</label>
            </div>
        </div>

        <div class="mb-3">
            <label>Reason for Visit</label>
            <select name="reason" class="form-select" required>
                @php $reason = old('reason', $entry['reason']) @endphp
                <option value="">-- Select --</option>
                <option value="inspection" {{ $reason == 'inspection' ? 'selected' : '' }}>Inspection</option>
                <option value="delivery" {{ $reason == 'delivery' ? 'selected' : '' }}>Delivery</option>
                <option value="official_visit" {{ $reason == 'official_visit' ? 'selected' : '' }}>Official Visit</option>
                <option value="maintenance" {{ $reason == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="other" {{ $reason == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Police Report Issue Date</label>
                <input type="date" class="form-control" name="police_issue_date" value="{{ old('police_issue_date', $entry['police_issue_date']) }}" required>
            </div>
            <div class="col-md-6">
                <label>Police Report Expiry Date</label>
                <input type="date" class="form-control" name="police_expire_date" value="{{ old('police_expire_date', $entry['police_expire_date']) }}" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Entry</button>
        <a href="{{ route('permit.monthly') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
