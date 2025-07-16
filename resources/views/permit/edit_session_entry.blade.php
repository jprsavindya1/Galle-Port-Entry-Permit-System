@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1>Edit Permit Entry</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('permit.updateSessionEntry', $index) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="id_type" class="form-label">Identification Type</label>
            <select name="id_type" id="id_type" onchange="setMaxToDate()" class="form-select" required>
                <option value="NIC" {{ $permit['id_type'] == 'NIC' ? 'selected' : '' }}>NIC Number</option>
                <option value="Passport" {{ $permit['id_type'] == 'Passport' ? 'selected' : '' }}>Passport Number</option>
                <option value="License" {{ $permit['id_type'] == 'License' ? 'selected' : '' }}>Driving Licence</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_number" class="form-label">ID Number</label>
            <input type="text" name="id_number" id="id_number" value="{{ $permit['id_number'] }}" class="form-control" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" id="from_date" name="from_date" value="{{ $permit['from_date'] }}" onchange="setMaxToDate()" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" id="to_date" name="to_date" value="{{ $permit['to_date'] }}" class="form-control" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" id="full_name" value="{{ $permit['full_name'] }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="initials" class="form-label">Name with Initials</label>
            <input type="text" name="initials" id="initials" value="{{ $permit['initials'] }}" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="designation" class="form-label">Designation</label>
            <input type="text" name="designation" id="designation" value="{{ $permit['designation'] ?? '' }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="company_name" class="form-label">Company Name</label>
            <input type="text" name="company_name" id="company_name" value="{{ $permit['company_name'] }}" class="form-control">
        </div>

        <div class="mb-3">
            <label for="company_address" class="form-label">Company Address</label>
            <textarea name="company_address" id="company_address" rows="2" class="form-control">{{ $permit['company_address'] ?? '' }}</textarea>
        </div>

        <div class="mb-3">
            <label for="residence_address" class="form-label">Residence Address</label>
            <textarea name="residence_address" id="residence_address" rows="2" class="form-control">{{ $permit['residence_address'] ?? '' }}</textarea>
        </div>

        @php
            $selectedPasses = explode(',', $permit['pass_type']);
        @endphp

        <fieldset class="mb-3">
            <legend class="col-form-label pt-0">Pass Type</legend>
            <div class="form-check">
                <input type="checkbox" name="pass_type[]" value="onboard" id="pass_onboard" class="form-check-input" 
                    {{ in_array('onboard', $selectedPasses) ? 'checked' : '' }}>
                <label class="form-check-label" for="pass_onboard">Onboard</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="pass_type[]" value="afloat" id="pass_afloat" class="form-check-input"
                    {{ in_array('afloat', $selectedPasses) ? 'checked' : '' }}>
                <label class="form-check-label" for="pass_afloat">Afloat</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="pass_type[]" value="ashore" id="pass_ashore" class="form-check-input"
                    {{ in_array('ashore', $selectedPasses) ? 'checked' : '' }}>
                <label class="form-check-label" for="pass_ashore">Ashore</label>
            </div>
        </fieldset>

        <fieldset class="mb-3">
            <legend class="col-form-label pt-0">Issue Type</legend>
            <div class="form-check form-check-inline">
                <input type="radio" name="issue_type" id="issue_free" value="free" class="form-check-input" {{ $permit['issue_type'] == 'free' ? 'checked' : '' }}>
                <label class="form-check-label" for="issue_free">Free Issue</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" name="issue_type" id="issue_payment" value="payment" class="form-check-input" {{ $permit['issue_type'] == 'payment' ? 'checked' : '' }}>
                <label class="form-check-label" for="issue_payment">On Payment</label>
            </div>
        </fieldset>

        <div class="mb-3">
            <label for="reason" class="form-label">Reason for Visit</label>
            <select name="reason" id="reason" class="form-select" required>
                <option value="">-- Select --</option>
                <option value="inspection" {{ $permit['reason'] == 'inspection' ? 'selected' : '' }}>Inspection</option>
                <option value="delivery" {{ $permit['reason'] == 'delivery' ? 'selected' : '' }}>Delivery</option>
                <option value="official_visit" {{ $permit['reason'] == 'official_visit' ? 'selected' : '' }}>Official Visit</option>
                <option value="maintenance" {{ $permit['reason'] == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="other" {{ $permit['reason'] == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update Entry</button>
        <a href="{{ route('permit.temporary') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>

<script>
    // Reuse your setMaxToDate JS here for the date logic
    function setMaxToDate() {
        const idType = document.getElementById('id_type').value;
        const fromDateInput = document.getElementById('from_date');
        const toDateInput = document.getElementById('to_date');

        const fromDate = new Date(fromDateInput.value);
        if (!fromDateInput.value) return;

        let maxDays = 29; // default max days for NIC
        if (idType === 'Passport' || idType === 'License') {
            maxDays = 14;
        }

        const maxToDate = new Date(fromDate);
        maxToDate.setDate(maxToDate.getDate() + maxDays);

        const yyyy = maxToDate.getFullYear();
        const mm = String(maxToDate.getMonth() + 1).padStart(2, '0');
        const dd = String(maxToDate.getDate()).padStart(2, '0');

        toDateInput.min = fromDateInput.value;
        toDateInput.max = `${yyyy}-${mm}-${dd}`;

        if (toDateInput.value < toDateInput.min || toDateInput.value > toDateInput.max) {
            toDateInput.value = toDateInput.min;
        }
    }
</script>
@endsection
