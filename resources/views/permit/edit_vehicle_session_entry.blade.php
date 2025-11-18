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

            {{-- DOCUMENTS ATTACHED SECTION --}}
            <fieldset class="mb-4">
                <legend class="col-form-label pt-0"><i class="bi bi-paperclip me-1"></i> Documents Attached</legend>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="doc_revenue_licence" name="documents[]" value="Revenue License"
                                {{ (isset($permit['documents']) && in_array('Revenue License', $permit['documents'])) || !isset($permit['documents']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="doc_revenue_licence">Revenue License</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="doc_insurance" name="documents[]" value="Insurance"
                                {{ (isset($permit['documents']) && in_array('Insurance', $permit['documents'])) || !isset($permit['documents']) ? 'checked' : '' }}>
                            <label class="form-check-label" for="doc_insurance">Insurance</label>
                        </div>
                    </div>
                </div>
            </fieldset>

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
                               oninput="this.value = this.value.toUpperCase(); handleVehicleNumberChange(); checkDuplicateInCart();"
                               onblur="fetchVehicleDetails();">
                        <span id="duplicate_error" class="text-danger small"></span>
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
                    <label for="owner_address" class="form-label"><i class="bi bi-house-door me-1"></i> Owner's Address (Optional)</label>
                    <input type="text" name="owner_address" id="owner_address" class="form-control"
                           value="{{ old('owner_address', $permit['owner_address']) }}">
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
// Store the last fetched vehicle number to track changes
let lastFetchedVehicleNumber = '';

// Function to handle vehicle number change - clear autofilled data when changed
window.handleVehicleNumberChange = function() {
    const currentVehicleNumber = document.getElementById('vehicle_number').value.trim();
    
    // If vehicle number has changed from the last fetched one, clear autofilled fields
    if (lastFetchedVehicleNumber && currentVehicleNumber !== lastFetchedVehicleNumber) {
        document.getElementById('revenue_license_number').value = '';
        document.getElementById('insurance_number').value = '';
        document.getElementById('owner_name').value = '';
        document.getElementById('owner_address').value = '';
        
        // Reset the last fetched vehicle number
        lastFetchedVehicleNumber = '';
    }
}

// Function to fetch vehicle details from database
window.fetchVehicleDetails = function() {
    const vehicleNumber = document.getElementById('vehicle_number').value.trim();
    
    if (!vehicleNumber) {
        return;
    }

    fetch("{{ route('permit.vehicle.fetchVehicleDetails') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ vehicle_number: vehicleNumber })
    })
    .then(res => res.json())
    .then(data => {
        if (data.found) {
            // Auto-fill the fields
            document.getElementById('revenue_license_number').value = data.data.revenue_license_number || '';
            document.getElementById('insurance_number').value = data.data.insurance_number || '';
            document.getElementById('owner_name').value = data.data.owner_name || '';
            document.getElementById('owner_address').value = data.data.owner_address || '';
            
            // Store the fetched vehicle number
            lastFetchedVehicleNumber = vehicleNumber;
            
            console.log('Vehicle details auto-filled successfully');
        }
    })
    .catch(error => {
        console.error("Failed to fetch vehicle details:", error);
    });
}

// Function to check for duplicate vehicle numbers in the cart (excluding current entry)
window.checkDuplicateInCart = function() {
    const currentVehicleNumber = document.getElementById('vehicle_number').value.trim();
    const duplicateError = document.getElementById('duplicate_error');
    const updateBtn = document.getElementById('updateBtn');
    
    if (!currentVehicleNumber) {
        duplicateError.textContent = '';
        duplicateError.style.display = 'none';
        return;
    }
    
    // Get all session vehicle permits from the session
    const sessionVehiclePermits = @json(session('vehicle_permit_cart', []));
    const currentEditIndex = {{ $index }};
    
    console.log('Checking duplicates...', {
        currentVehicleNumber,
        currentEditIndex,
        sessionVehiclePermits
    });
    
    let isDuplicate = false;
    
    // Check each permit in the session
    sessionVehiclePermits.forEach((permit, index) => {
        // Skip the current entry being edited
        if (index === currentEditIndex) {
            return;
        }
        
        // Compare vehicle numbers (case insensitive)
        if (permit.vehicle_number && permit.vehicle_number.toUpperCase() === currentVehicleNumber.toUpperCase()) {
            isDuplicate = true;
        }
    });
    
    if (isDuplicate) {
        duplicateError.textContent = '⚠️ This Vehicle Number is already in the cart. Cannot have duplicate entries.';
        duplicateError.style.display = 'block';
        duplicateError.style.color = '#dc3545';
        duplicateError.style.fontWeight = '500';
        if (updateBtn) {
            updateBtn.disabled = true;
            updateBtn.style.opacity = '0.6';
            updateBtn.style.cursor = 'not-allowed';
        }
    } else {
        duplicateError.textContent = '';
        duplicateError.style.display = 'none';
        // Only enable if other validations pass
        const msgEl = document.getElementById("availability-msg");
        if (updateBtn && (!msgEl || msgEl.textContent === '' || msgEl.style.color !== 'red')) {
            updateBtn.disabled = false;
            updateBtn.style.opacity = '1';
            updateBtn.style.cursor = 'pointer';
        }
    }
}

/**
 * Function to check the availability of the vehicle permit for the specified dates and vehicle number.
 * This is the original logic preserved from your request.
 */
function checkVehicleAvailability() {
    let vehicle_number = document.querySelector('input[name="vehicle_number"]').value;
    let from_date = document.querySelector('input[name="from_date"]').value;
    let to_date = document.querySelector('input[name="to_date"]').value;
    let company_name = document.querySelector('input[name="company_name"]').value;
    let revenue_license_number = document.querySelector('input[name="revenue_license_number"]').value;
    let insurance_number = document.querySelector('input[name="insurance_number"]').value;
    let vehicle_type = document.querySelector('select[name="vehicle_type"]').value;
    let owner_name = document.querySelector('input[name="owner_name"]').value;
    let updateBtn = document.getElementById("updateBtn");

    // Disable button while checking
    updateBtn.disabled = true;
    updateBtn.style.opacity = '0.6';
    updateBtn.style.cursor = 'not-allowed';

    // Comprehensive validation of all required fields
    let msgEl = document.getElementById("availability-msg");
    
    // Check for duplicate error first
    const duplicateError = document.getElementById('duplicate_error');
    if (duplicateError && duplicateError.textContent.trim() !== '') {
        msgEl.textContent = 'Cannot check availability: This Vehicle Number is already in the cart.';
        msgEl.style.color = 'red';
        return;
    }
    
    if (!vehicle_number) {
        msgEl.textContent = "Please enter Vehicle Number.";
        msgEl.style.color = "red";
        return;
    }
    
    if (!vehicle_type) {
        msgEl.textContent = "Please select Vehicle Type.";
        msgEl.style.color = "red";
        return;
    }
    
    if (!revenue_license_number) {
        msgEl.textContent = "Please enter Revenue License Number.";
        msgEl.style.color = "red";
        return;
    }
    
    if (!insurance_number) {
        msgEl.textContent = "Please enter Insurance Number.";
        msgEl.style.color = "red";
        return;
    }
    
    if (!owner_name) {
        msgEl.textContent = "Please enter Owner's Name.";
        msgEl.style.color = "red";
        return;
    }
    
    if (!from_date) {
        msgEl.textContent = "Please enter From Date.";
        msgEl.style.color = "red";
        return;
    }
    
    if (!to_date) {
        msgEl.textContent = "Please enter To Date.";
        msgEl.style.color = "red";
        return;
    }
    
    if (!company_name) {
        msgEl.textContent = "Please enter Company Name.";
        msgEl.style.color = "red";
        return;
    }

    // Check document checkboxes - both Revenue License and Insurance must be checked
    const docRevenueLicence = document.getElementById('doc_revenue_licence').checked;
    const docInsurance = document.getElementById('doc_insurance').checked;

    if (!docRevenueLicence || !docInsurance) {
        msgEl.textContent = "Please check both required documents: Revenue License and Insurance";
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
        
        // Check if there's a duplicate error before enabling
        const duplicateError = document.getElementById('duplicate_error');
        const hasDuplicateError = duplicateError && duplicateError.textContent.trim() !== '';
        
        // Enable button only if available AND no duplicate error
        if (data.available && !hasDuplicateError) {
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
            // Keep it grey when not available or has duplicate
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

// Add form submission validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action="{{ route('permit.vehicle.updateVehicleSessionEntry', $index) }}"]');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Check for duplicate error
            const duplicateError = document.getElementById('duplicate_error');
            if (duplicateError && duplicateError.textContent.trim() !== '') {
                e.preventDefault();
                alert('Cannot submit: This Vehicle Number is already in the cart. Please use a different vehicle number.');
                return false;
            }
        });
    }
});
</script>
@endpush