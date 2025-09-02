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

    <form method="POST" action="{{ route('permits.update', $permit) }}" id="edit-permit-form">
        @csrf
        @method('PUT')

        <input type="hidden" name="permit_type" value="{{ $permit->type }}">

        {{-- ID Fields --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">ID Type</label>
                <select name="id_type" id="id_type" class="form-select" required>
                    <option value="NIC" {{ old('id_type', $permit->id_type) == 'NIC' ? 'selected' : '' }}>NIC</option>
                    <option value="Passport" {{ old('id_type', $permit->id_type) == 'Passport' ? 'selected' : '' }}>Passport</option>
                    <option value="Driving License" {{ old('id_type', $permit->id_type) == 'Driving License' ? 'selected' : '' }}>Driving License</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">ID Number</label>
                <input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number', $permit->id_number) }}" required>
            </div>
        </div>

        {{-- Dates --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date', $permit->from_date) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('to_date', $permit->to_date) }}" required>
            </div>
        </div>

        {{-- Full Name --}}
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" id="full_name" class="form-control"
                   value="{{ old('full_name', $permit->full_name) }}" required
                   style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
        </div>

        {{-- Initials --}}
        <div class="mb-3">
            <label class="form-label">Initials</label>
            <input type="text" name="initials" id="initials" class="form-control" value="{{ old('initials', $permit->initials) }}" required>
        </div>

        {{-- Check Availability Button --}}
        <div class="mb-3">
            <button type="button" class="btn btn-info" onclick="checkPermitAvailability(true)">
                Check Availability
            </button>
            <p id="availability-msg" class="fw-bold mt-2"></p>
        </div>

        {{-- Designation --}}
        <div class="mb-3">
            <label for="designation" class="form-label">Designation</label>
            <select name="designation" id="designation" class="form-select" required>
                <option value="">-- Select Designation --</option>
                @foreach($designations as $designation)
                    <option value="{{ $designation->name }}" 
                        {{ old('designation', $permit->designation) == $designation->name ? 'selected' : '' }}>
                        {{ $designation->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Company Info --}}
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Company Name</label>
                <select name="company_name" id="company_name" class="form-select" required>
                    <option value="">-- Select Company --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->name }}" data-address="{{ $company->address }}"
                            {{ old('company_name', $companyName) == $company->name ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">Company Address</label>
                <input type="text" name="company_address" id="company_address"
                    class="form-control" value="{{ old('company_address', $companyAddress) }}" readonly>
            </div>
        </div>

        {{-- Residence Address --}}
        <div class="mb-3">
            <label class="form-label">Residence Address</label>
            <input type="text" name="residence_address" class="form-control"
                   value="{{ old('residence_address', $permit->residence_address) }}">
        </div>

        {{-- Reason --}}
        <div class="mb-3">
            <label for="reason" class="form-label">Reason for Visit</label>
            <select name="reason" id="reason" class="form-select" required>
                <option value="">-- Select Reason --</option>
                @foreach($reasons as $reason)
                    <option value="{{ $reason->name }}" 
                        {{ old('reason', $permit->reason) == $reason->name ? 'selected' : '' }}>
                        {{ ucfirst($reason->name) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Police Report (Monthly only) --}}
        @if($permit->type === 'MP')
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Police Report Issue Date</label>
                    <input type="date" name="police_issue_date" class="form-control" value="{{ old('police_issue_date', $permit->police_issue_date) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Police Report Expiry Date</label>
                    <input type="date" name="police_expire_date" class="form-control" value="{{ old('police_expire_date', $permit->police_expire_date) }}" required>
                </div>
            </div>
        @endif

        {{-- Submit --}}
        <button type="submit" class="btn btn-primary">Update Permit</button>
        <a href="{{ route('permits.submitted') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
function checkPermitAvailability(isEdit = false) {
    const permitType = "{{ $permit->type }}";
    const idType = document.getElementById('id_type').value;
    const idNumber = document.getElementById('id_number').value;
    const fullName = document.getElementById('full_name').value;
    const initials = document.getElementById('initials').value;
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    const companyName = document.getElementById('company_name')?.value || '';
    const currentPermitId = {{ $permit->id ?? 'null' }};

    const msg = document.getElementById('availability-msg');
    msg.innerText = '';

    if (!idType || !idNumber || !fullName || !initials || !fromDate || !toDate) {
        msg.innerText = "Please fill in all required fields.";
        msg.style.color = 'red';
        return;
    }

    let payload = {
        permit_type: permitType,
        id_type: idType,
        id_number: idNumber,
        full_name: fullName,
        initials: initials,
        from_date: fromDate,
        to_date: toDate,
        session_edit: isEdit,
        current_permit_id: currentPermitId
    };

    if (permitType === 'TP' || permitType === 'MP') {
        payload.company_name = companyName;
    }

    if (permitType === 'VP') {
        payload.vehicle_number = document.querySelector('input[name="vehicle_number"]').value;
    }

    fetch("{{ route('permit.checkAvailability') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify(payload)
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

// Auto-fill company address
document.getElementById('company_name').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const address = selected.getAttribute('data-address') || '';
    document.getElementById('company_address').value = address;
});
</script>
@endpush
