@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<div class="container">
    <h2 class="my-4">Edit Monthly Permit Session Entry</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('permit.monthly.updateMonthlySessionEntry', $index) }}">
        @csrf
        @method('PUT')
    <!-- This hidden input tells backend this is an edit session -->
    <input type="hidden" name="session_edit" value="1">
    <input type="hidden" name="company_name" value="{{ $permit['company_name'] ?? '' }}">
<input type="hidden" name="company_address" value="{{ $permit['company_address'] ?? '' }}">

        <div class="row mb-3">
            <div class="col-md-6">
                <label>ID Type</label>
                <input type="text" class="form-control" name="id_type" id="id_type" value="NIC" readonly>
            </div>
            <div class="col-md-6">
                <label>ID Number</label>
                <input type="text" class="form-control" name="id_number" id="id_number" value="{{ old('id_number', $permit['id_number']) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>From Date</label>
                <input type="date" class="form-control" name="from_date" id="from_date" value="{{ old('from_date', $permit['from_date']) }}" required>
            </div>
            <div class="col-md-6">
                <label>To Date</label>
                <input type="date" class="form-control" name="to_date" id="to_date" value="{{ old('to_date', $permit['to_date']) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" 
                   name="full_name" 
                   id="full_name"
                   value="{{ old('full_name', $permit['full_name'] ?? '') }}" 
                   class="form-control" 
                   required
                   style="text-transform: uppercase;" 
                   oninput="this.value = this.value.toUpperCase();">
        </div>

        <div class="mb-3">
            <label>Name with Initials</label>
            <input type="text" class="form-control" name="initials" id="initials" value="{{ old('initials', $permit['initials']) }}" required>
        </div>

        <button type="button" onclick="checkMonthlyAvailability(true)" class="btn btn-info mb-3">
            Check Availability
        </button>
        <p id="availability-msg" class="fw-bold"></p>

        <div class="mb-3">
            <label for="designation" class="form-label">Designation</label>
            <select name="designation" id="designation" class="form-select" required>
                <option value="">-- Select Designation --</option>
                @foreach($designations as $designation)
                    <option value="{{ $designation->name }}" 
                        {{ old('designation', $permit['designation']) == $designation->name ? 'selected' : '' }}>
                        {{ $designation->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Residence Address</label>
            <textarea class="form-control" name="residence_address" id="residence_address" rows="2">{{ old('residence_address', $permit['residence_address']) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Pass Type</label><br>
            @php
                $selectedTypes = old('pass_type', isset($permit['pass_type']) ? explode(',', $permit['pass_type']) : []);
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
            @php $issueType = old('issue_type', $permit['issue_type'] ?? 'free'); @endphp
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="issue_type" value="free" {{ $issueType === 'free' ? 'checked' : '' }}>
                <label class="form-check-label">Free</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="issue_type" value="payment" {{ $issueType === 'payment' ? 'checked' : '' }}>
                <label class="form-check-label">On Payment</label>
            </div>
        </div>

        <select name="reason" class="form-select" required>
            <option value="">-- Select --</option>
            @foreach($reasons as $reason)
                <option value="{{ $reason->name }}" {{ old('reason', $permit['reason']) == $reason->name ? 'selected' : '' }}>
                    {{ ucfirst($reason->name) }}
                </option>
            @endforeach
        </select>

        <div class="row mb-3 mt-3">
            <div class="col-md-6">
                <label>Police Report Issue Date</label>
                <input type="date" class="form-control" name="police_issue_date" id="police_issue_date" value="{{ old('police_issue_date', $permit['police_issue_date']) }}" required>
            </div>
            <div class="col-md-6">
                <label>Police Report Expiry Date</label>
                <input type="date" class="form-control" name="police_expire_date" id="police_expire_date" value="{{ old('police_expire_date', $permit['police_expire_date']) }}" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Update Entry</button>
        <a href="{{ route('permit.monthly') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
function checkMonthlyAvailability(isEdit = false) {
    const idType = document.getElementById('id_type').value;
    const idNumber = document.getElementById('id_number').value;
    const fullName = document.getElementById('full_name').value;
    const initials = document.getElementById('initials').value;
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;

    const msg = document.getElementById('availability-msg');
    msg.innerText = '';

    if (!idType || !idNumber || !fullName || !initials || !fromDate || !toDate) {
        msg.innerText = "Please fill in all required fields.";
        msg.style.color = 'red';
        return;
    }

    const body = {
        id_type: idType,
        id_number: idNumber,
        full_name: fullName,
        initials: initials,
        from_date: fromDate,
        to_date: toDate,
        session_edit: isEdit // flag to skip company check in backend
    };

    fetch("{{ route('permit.monthly.checkMonthlyAvailability') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify(body)
    })
    .then(res => res.json())
    .then(data => {
        msg.innerText = data.message;
        msg.style.color = data.available ? 'green' : 'red';
    })
    .catch(error => {
        console.error("Availability check failed:", error);
        msg.innerText = "Something went wrong during availability check.";
        msg.style.color = 'red';
    });
}
</script>
@endpush
