@extends('layouts.app')

@section('title', 'Edit Vehicle Permit Entry')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* --- User Dashboard Styles Applied to Form --- */
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
        text-align: center; /* Centered for the edit title */
    }
    .form-control, .form-select {
        border-radius: 0.5rem;
        border: 1px solid #bbdefb; /* Light blue border */
        background-color: #f8fafc; /* Very light background */
    }
    .form-control:focus, .form-select:focus {
        border-color: #1976d2;
        box-shadow: 0 0 0 0.25rem rgba(25, 118, 210, 0.25);
    }
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
            <i class="bi bi-car-front-fill me-2"></i> Edit Vehicle Permit Session Entry
        </div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form method="POST" action="{{ route('permit.vehicle.updateVehicleSessionEntry', $index) }}">
            @csrf
            @method('PUT')

            <input type="hidden" name="session_edit" value="1">
            <input type="hidden" name="company_name" value="{{ $permit['company_name'] ?? '' }}">
            <input type="hidden" name="company_address" value="{{ $permit['company_address'] ?? '' }}">

            {{-- --- Section 1: Vehicle Details --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-gear me-2"></i> Vehicle Identification</div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="vehicle_type" class="form-label"><i class="bi bi-truck me-1"></i> Vehicle Type</label>
                        <select name="vehicle_type" id="vehicle_type" class="form-select" required>
                            <option value="">-- Select Vehicle Type --</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->name }}"
                                    {{ old('vehicle_type', $permit['vehicle_type']) == $vehicle->name ? 'selected' : '' }}>
                                    {{ $vehicle->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="vehicle_number" class="form-label"><i class="bi bi-hash me-1"></i> Vehicle Number</label>
                        <input type="text" name="vehicle_number" id="vehicle_number" class="form-control"
                               value="{{ old('vehicle_number', $permit['vehicle_number']) }}" required
                               oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="revenue_license_number" class="form-label"><i class="bi bi-journal-text me-1"></i> Revenue License Number</label>
                        <input type="text" name="revenue_license_number" id="revenue_license_number" class="form-control"
                               value="{{ old('revenue_license_number', $permit['revenue_license_number']) }}" required
                               oninput="this.value = this.value.toUpperCase();">
                    </div>
                    <div class="col-md-6">
                        <label for="insurance_number" class="form-label"><i class="bi bi-shield-fill-check me-1"></i> Insurance Number</label>
                        <input type="text" name="insurance_number" id="insurance_number" class="form-control"
                               value="{{ old('insurance_number', $permit['insurance_number']) }}"
                               oninput="this.value = this.value.toUpperCase();">
                    </div>
                </div>
            </div>

            {{-- --- Section 2: Owner & Duration --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-calendar-check me-2"></i> Owner & Validity</div>

                <div class="mb-3">
                    <label for="owner_name" class="form-label"><i class="bi bi-person-circle me-1"></i> Owner's Name</label>
                    <input type="text" name="owner_name" id="owner_name" class="form-control"
                           value="{{ old('owner_name', $permit['owner_name']) }}" required
                           oninput="this.value = this.value.toUpperCase();">
                </div>

                <div class="mb-4">
                    <label for="owner_address" class="form-label"><i class="bi bi-house-door me-1"></i> Owner's Address</label>
                    <input type="text" name="owner_address" id="owner_address" class="form-control"
                           value="{{ old('owner_address', $permit['owner_address']) }}" required>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="from_date" class="form-label"><i class="bi bi-calendar-date me-1"></i> From Date</label>
                        <input type="date" name="from_date" id="from_date" class="form-control"
                               value="{{ old('from_date', $permit['from_date']) }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="to_date" class="form-label"><i class="bi bi-calendar-range me-1"></i> To Date</label>
                        <input type="date" name="to_date" id="to_date" class="form-control"
                               value="{{ old('to_date', $permit['to_date']) }}" min="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
            </div>

            {{-- --- Section 3: Permit Info --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-card-checklist me-2"></i> Permit Information</div>

                <div class="mb-3 d-flex align-items-center">
                    <button type="button" onclick="checkVehicleAvailability()" class="btn btn-info me-3"> 
                        <i class="bi bi-check-circle-fill me-1"></i> Check Availability
                    </button>
                    <p id="availability-msg" class="fw-bold my-0"></p>
                </div>

                <fieldset class="mb-3">
                    <legend class="col-form-label pt-0"><i class="bi bi-cash me-1"></i> Issue Type</legend><br>
                    @php $issueType = old('issue_type', $permit['issue_type'] ?? 'free'); @endphp
                    <div class="form-check form-check-inline">
                        <input type="radio" name="issue_type" value="free" class="form-check-input" id="issue_free"
                                {{ $issueType === 'free' ? 'checked' : '' }}>
                        <label class="form-check-label" for="issue_free">Free Issue</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="issue_type" value="payment" class="form-check-input" id="issue_payment"
                                {{ $issueType === 'payment' ? 'checked' : '' }}>
                        <label class="form-check-label" for="issue_payment">On Payment</label>
                    </div>
                </fieldset>

                <div class="mb-3">
                    <label for="reason" class="form-label"><i class="bi bi-file-earmark-text me-1"></i> Reason for Visit</label>
                    <select name="reason" id="reason" class="form-select" required>
                        <option value="">-- Select --</option>
                        @foreach($reasons as $reason)
                            <option value="{{ $reason->name }}"
                                {{ old('reason', $permit['reason']) == $reason->name ? 'selected' : '' }}>
                                {{ ucfirst($reason->name) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="remarks" class="form-label"><i class="bi bi-chat-left-text me-1"></i> Remarks (Optional)</label>
                    <input type="text" name="remarks" id="remarks" class="form-control"
                           value="{{ old('remarks', $permit['remarks']) }}">
                </div>
            </div>

            {{-- --- Action Buttons --- --}}
            <div class="d-flex justify-content-end pt-3">
                <button type="submit" id="updateBtn" class="btn btn-primary btn-lg me-3" disabled style="background-color: #9e9e9e !important; border-color: #9e9e9e !important; opacity: 0.65; cursor: not-allowed;">
                    <i class="bi bi-save me-1"></i> Update Entry
                </button>
                <a href="{{ route('permit.vehicle') }}" class="btn btn-secondary btn-lg">
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
 * Function to check the availability of the vehicle permit for the specified dates and vehicle number.
 * This is the original logic preserved from your request.
 */
function checkVehicleAvailability() {
    let vehicle_number = document.querySelector('input[name="vehicle_number"]').value;
    let from_date = document.querySelector('input[name="from_date"]').value;
    let to_date = document.querySelector('input[name="to_date"]').value;
    let company_name = document.querySelector('input[name="company_name"]').value;
    let updateBtn = document.getElementById("updateBtn");

    // Disable button while checking
    updateBtn.disabled = true;
    updateBtn.style.opacity = '0.6';
    updateBtn.style.cursor = 'not-allowed';

    // Check for required fields before making the API call
    if (!vehicle_number || !from_date || !to_date || !company_name) {
        let msgEl = document.getElementById("availability-msg");
        msgEl.textContent = "Please fill in Vehicle Number, Company, From Date, and To Date.";
        msgEl.style.color = "red";
        return;
    }

    fetch("{{ route('permit.vehicle.checkVehicleAvailability') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            vehicle_number,
            from_date,
            to_date,
            company_name
        })
    })
    .then(res => res.json())
    .then(data => {
        let msgEl = document.getElementById("availability-msg");
        msgEl.textContent = data.message;
        msgEl.style.color = data.available ? "green" : "red";
        
        // Enable button only if available
        if (data.available) {
            updateBtn.disabled = false;
            updateBtn.style.backgroundColor = '';
            updateBtn.style.borderColor = '';
            updateBtn.style.opacity = '1';
            updateBtn.style.cursor = 'pointer';
            
            // Store the checked form data
            checkedFormData = {
                vehicle_number: vehicle_number,
                from_date: from_date,
                to_date: to_date,
                company_name: company_name
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
    .catch(err => {
        console.error(err);
        document.getElementById("availability-msg").textContent = "Error checking availability.";
    });
}

// Function to check if form data has changed
function hasFormDataChanged() {
    if (!checkedFormData) return false;
    
    const currentData = {
        vehicle_number: document.querySelector('input[name="vehicle_number"]').value,
        from_date: document.querySelector('input[name="from_date"]').value,
        to_date: document.querySelector('input[name="to_date"]').value,
        company_name: document.querySelector('input[name="company_name"]').value
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
        
        msg.textContent = 'Form data changed. Please check availability again.';
        msg.style.color = 'orange';
        
        checkedFormData = null;
    }
}

// Function to attach change listeners to form fields
function attachChangeListeners() {
    const fieldNames = ['vehicle_number', 'from_date', 'to_date', 'company_name'];
    
    fieldNames.forEach(fieldName => {
        const field = document.querySelector(`[name="${fieldName}"]`);
        if (field) {
            // Remove existing listener if any
            field.removeEventListener('change', handleFormChange);
            field.removeEventListener('input', handleFormChange);
            
            // Add new listeners
            field.addEventListener('change', handleFormChange);
            if (field.type !== 'select-one') {
                field.addEventListener('input', handleFormChange);
            }
        }
    });
}
</script>
@endpush