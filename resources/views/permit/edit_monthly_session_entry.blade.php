@extends('layouts.app')

@section('title', 'Edit Monthly Permit Entry')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* --- Blue Theme Dashboard Styles --- */
    .user-dashboard-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        padding: 2rem;
        margin-bottom: 2rem;
        border: none;
    }
    .user-dashboard-title {
        font-size: 2rem;
        font-weight: 600;
        color: #1976d2;
        letter-spacing: 1px;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #bbdefb;
        text-align: center;
    }
    .form-control, .form-select, .form-check-input {
        border-radius: 0.5rem;
        border: 1px solid #bbdefb;
        background-color: #f8fafc;
    }
    .form-control:focus, .form-select:focus {
        border-color: #1976d2;
        box-shadow: 0 0 0 0.25rem rgba(25, 118, 210, 0.25);
    }
    main .form-label {
        font-weight: 500;
        color: #1976d2;
    }
    main .btn-primary {
        background-color: #1976d2;
        border-color: #1976d2;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: background-color 0.2s;
    }
    main .btn-info {
        background-color: #4fc3f7;
        border-color: #4fc3f7;
        color: #fff;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: background-color 0.2s;
    }
    main .btn-secondary {
        border-radius: 0.5rem;
        font-weight: 500;
        border-color: #6c757d;
        background-color: #6c757d;
        color: #fff;
    }
    /* Grouping Card for Sections */
    .form-section-card {
        background-color: #ffffff;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #bbdefb;
        box-shadow: 0 1px 5px rgba(0,0,0,0.05);
    }
    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1976d2;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #bbdefb;
    }
    legend {
        font-size: 1.1rem;
        font-weight: 600;
        color: #1976d2;
        border-bottom: 1px solid #bbdefb;
        padding-bottom: 0.25rem;
        margin-bottom: 1rem;
        width: auto;
    }
</style>

<div class="container py-4">
    <div class="user-dashboard-card mx-auto" style="max-width: 900px;">
        <div class="user-dashboard-title">
            <i class="bi bi-person-fill-gear me-2"></i> Edit Monthly Permit Session Entry
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('permit.monthly.updateMonthlySessionEntry', $index) }}">
            @csrf
            @method('PUT')
            
            <input type="hidden" name="session_edit" value="1">
            <input type="hidden" name="company_name" value="{{ $permit['company_name'] ?? '' }}">
            <input type="hidden" name="company_address" value="{{ $permit['company_address'] ?? '' }}">

            {{-- --- Section 1: Identification & Validity --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-card-heading me-2"></i> ID and Validity Period</div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="id_type" class="form-label"><i class="bi bi-person-badge me-1"></i> ID Type</label>
                        <input type="text" class="form-control" name="id_type" id="id_type" value="NIC" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="id_number" class="form-label"><i class="bi bi-hash me-1"></i> ID Number</label>
                        <input type="text" class="form-control" name="id_number" id="id_number" 
                               value="{{ old('id_number', $permit['id_number']) }}" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="from_date" class="form-label"><i class="bi bi-calendar-date me-1"></i> From Date</label>
                        <input type="date" class="form-control" name="from_date" id="from_date" 
                               value="{{ old('from_date', $permit['from_date']) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="to_date" class="form-label"><i class="bi bi-calendar-range me-1"></i> To Date</label>
                        <input type="date" class="form-control" name="to_date" id="to_date" 
                               value="{{ old('to_date', $permit['to_date']) }}" required>
                    </div>
                </div>

                <div class="mb-3 d-flex align-items-center">
                    <button type="button" onclick="checkMonthlyAvailability(true)" class="btn btn-info me-3">
                        <i class="bi bi-search me-1"></i> Check Availability
                    </button>
                    <p id="availability-msg" class="fw-bold my-0"></p>
                </div>
            </div>

            {{-- --- Section 2: Personal & Contact Details --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-person me-2"></i> Personal Details</div>

                <div class="mb-3">
                    <label for="full_name" class="form-label"><i class="bi bi-person-vcard me-1"></i> Full Name</label>
                    <input type="text" name="full_name" id="full_name"
                           value="{{ old('full_name', $permit['full_name'] ?? '') }}" 
                           class="form-control" required
                           oninput="this.value = this.value.toUpperCase();">
                </div>

                <div class="mb-3">
                    <label for="initials" class="form-label"><i class="bi bi-text-short me-1"></i> Name with Initials</label>
                    <input type="text" class="form-control" name="initials" id="initials" 
                           value="{{ old('initials', $permit['initials']) }}" required>
                </div>

                <div class="mb-3">
                    <label for="designation" class="form-label"><i class="bi bi-briefcase me-1"></i> Designation</label>
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

                <div class="mb-4">
                    <label for="residence_address" class="form-label"><i class="bi bi-house-door me-1"></i> Residence Address</label>
                    <textarea class="form-control" name="residence_address" id="residence_address" rows="2">{{ old('residence_address', $permit['residence_address']) }}</textarea>
                </div>
            </div>

            {{-- --- Section 3: Permit Details & Police Report --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-ticket-perforated me-2"></i> Permit Specifications</div>

                <fieldset class="mb-3">
                    <legend class="col-form-label pt-0"><i class="bi bi-layers-half me-1"></i> Pass Type</legend><br>
                    @php
                        $selectedTypes = old('pass_type', isset($permit['pass_type']) ? explode(',', $permit['pass_type']) : []);
                    @endphp
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="pass_type[]" value="onboard" id="pass_onboard" {{ in_array('onboard', $selectedTypes) ? 'checked' : '' }}>
                        <label class="form-check-label" for="pass_onboard">Onboard</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="pass_type[]" value="afloat" id="pass_afloat" {{ in_array('afloat', $selectedTypes) ? 'checked' : '' }}>
                        <label class="form-check-label" for="pass_afloat">Afloat</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" name="pass_type[]" value="ashore" id="pass_ashore" {{ in_array('ashore', $selectedTypes) ? 'checked' : '' }}>
                        <label class="form-check-label" for="pass_ashore">Ashore</label>
                    </div>
                </fieldset>

                <fieldset class="mb-3">
                    <legend class="col-form-label pt-0"><i class="bi bi-cash me-1"></i> Issue Type</legend><br>
                    @php $issueType = old('issue_type', $permit['issue_type'] ?? 'free'); @endphp
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="issue_type" id="issue_free" value="free" {{ $issueType === 'free' ? 'checked' : '' }}>
                        <label class="form-check-label" for="issue_free">Free</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="issue_type" id="issue_payment" value="payment" {{ $issueType === 'payment' ? 'checked' : '' }}>
                        <label class="form-check-label" for="issue_payment">On Payment</label>
                    </div>
                </fieldset>

                <div class="mb-4">
                    <label for="reason" class="form-label"><i class="bi bi-file-earmark-text me-1"></i> Reason for Visit</label>
                    <select name="reason" id="reason" class="form-select" required>
                        <option value="">-- Select --</option>
                        @foreach($reasons as $reason)
                            <option value="{{ $reason->name }}" {{ old('reason', $permit['reason']) == $reason->name ? 'selected' : '' }}>
                                {{ ucfirst($reason->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="police_issue_date" class="form-label"><i class="bi bi-calendar-check me-1"></i> Police Report Issue Date</label>
                        <input type="date" class="form-control" name="police_issue_date" id="police_issue_date" 
                               value="{{ old('police_issue_date', $permit['police_issue_date']) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="police_expire_date" class="form-label"><i class="bi bi-calendar-x me-1"></i> Police Report Expiry Date</label>
                        <input type="date" class="form-control" name="police_expire_date" id="police_expire_date" 
                               value="{{ old('police_expire_date', $permit['police_expire_date']) }}" required>
                    </div>
                </div>
            </div>

            {{-- --- Action Buttons --- --}}
            <div class="d-flex justify-content-end pt-3">
                <button type="submit" id="updateBtn" class="btn btn-primary btn-lg me-3" disabled style="background-color: #9e9e9e !important; border-color: #9e9e9e !important; opacity: 0.65; cursor: not-allowed;">
                    <i class="bi bi-save me-1"></i> Update Entry
                </button>
                <a href="{{ route('permit.monthly') }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Store checked form data to detect changes
let checkedFormData = null;

/**
 * Function to check the availability of the Monthly Permit.
 * The `isEdit` flag tells the backend to handle the check in an edit context.
 */
function checkMonthlyAvailability(isEdit = false) {
    const idType = document.getElementById('id_type').value;
    const idNumber = document.getElementById('id_number').value;
    const fullName = document.getElementById('full_name').value;
    const initials = document.getElementById('initials').value;
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;

    const msg = document.getElementById('availability-msg');
    const updateBtn = document.getElementById('updateBtn');
    
    msg.innerText = '';
    // Disable button while checking
    updateBtn.disabled = true;
    updateBtn.style.opacity = '0.6';
    updateBtn.style.cursor = 'not-allowed';

    if (!idType || !idNumber || !fullName || !initials || !fromDate || !toDate) {
        msg.innerText = "Please fill in all required fields to check availability.";
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
        
        // Enable button only if available
        if (data.available) {
            updateBtn.disabled = false;
            updateBtn.style.backgroundColor = '';
            updateBtn.style.borderColor = '';
            updateBtn.style.opacity = '1';
            updateBtn.style.cursor = 'pointer';
            
            // Store the checked form data
            checkedFormData = {
                id_type: idType,
                id_number: idNumber,
                full_name: fullName,
                initials: initials,
                from_date: fromDate,
                to_date: toDate
            };
            
            // Attach change listeners to form fields
            attachChangeListeners();
        } else {
            // Keep it grey when not available
            updateBtn.style.backgroundColor = '#9e9e9e';
            updateBtn.style.borderColor = '#9e9e9e';
            checkedFormData = null;
        }
    })
    .catch(error => {
        console.error("Availability check failed:", error);
        msg.innerText = "Something went wrong during availability check.";
        msg.style.color = 'red';
    });
}

// Function to check if form data has changed
function hasFormDataChanged() {
    if (!checkedFormData) return false;
    
    const currentData = {
        id_type: document.getElementById('id_type').value,
        id_number: document.getElementById('id_number').value,
        full_name: document.getElementById('full_name').value,
        initials: document.getElementById('initials').value,
        from_date: document.getElementById('from_date').value,
        to_date: document.getElementById('to_date').value
    };
    
    return Object.keys(checkedFormData).some(key => checkedFormData[key] !== currentData[key]);
}

// Function to disable button when form data changes
function handleFormChange() {
    if (hasFormDataChanged()) {
        const updateBtn = document.getElementById('updateBtn');
        const msg = document.getElementById('availability-msg');
        
        updateBtn.disabled = true;
        updateBtn.style.backgroundColor = '#9e9e9e';
        updateBtn.style.borderColor = '#9e9e9e';
        updateBtn.style.opacity = '0.65';
        updateBtn.style.cursor = 'not-allowed';
        
        msg.innerText = 'Form data changed. Please check availability again.';
        msg.style.color = 'orange';
        
        checkedFormData = null;
    }
}

// Function to attach change listeners to form fields
function attachChangeListeners() {
    const fields = ['id_type', 'id_number', 'full_name', 'initials', 'from_date', 'to_date'];
    
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            // Remove existing listener if any
            field.removeEventListener('change', handleFormChange);
            field.removeEventListener('input', handleFormChange);
            
            // Add new listeners
            field.addEventListener('change', handleFormChange);
            if (field.tagName !== 'SELECT') {
                field.addEventListener('input', handleFormChange);
            }
        }
    });
}
</script>
@endpush