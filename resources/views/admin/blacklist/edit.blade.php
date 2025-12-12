@extends('layouts.app')

@section('title', 'Edit Blacklist Entry')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .user-dashboard-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: none;
    }
    .user-dashboard-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1976d2;
        letter-spacing: 1px;
        margin-bottom: 1rem;
        border-bottom: 1px solid #bbdefb;
        padding-bottom: 0.5rem;
    }
    .form-control, .form-select {
        border-radius: 0.5rem;
        border: 1px solid #bbdefb;
        background-color: #f8fafc;
    }
    main .form-control:focus, main .form-select:focus {
        border-color: #1976d2;
        box-shadow: 0 0 0 0.25rem rgba(25, 118, 210, 0.12);
    }
    main .form-label { color: #1976d2; font-weight:500; }
    main .btn-primary { background-color:#1976d2; border-color:#1976d2; border-radius:0.5rem; font-weight:500; }
    main .btn-secondary { border-radius:0.5rem; font-weight:500; border-color:#6c757d; background-color:#6c757d; color:#fff; }
</style>

<div class="container py-4">
    <div class="user-dashboard-card mx-auto" style="max-width:720px;">
        <div class="user-dashboard-title"><i class="bi bi-person-dash me-2"></i> Edit Blacklist Entry</div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('blacklist.update', $blacklist) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="nic" class="form-label">NIC</label>
                <input type="text" name="nic" id="nic" class="form-control" value="{{ old('nic', $blacklist->nic) }}" style="text-transform: uppercase;" placeholder="e.g., 123456789V" oninput="validateNic()" onblur="validateNic()">
                <span id="nic_error" class="text-danger" style="font-size: 0.875rem;"></span>
            </div>

            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $blacklist->full_name) }}" style="text-transform: uppercase;">
            </div>

            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" name="company_name" id="company_name" class="form-control" value="{{ old('company_name', $blacklist->company_name) }}" style="text-transform: uppercase;" oninput="checkFormValidity()" onblur="checkFormValidity()">
            </div>

            <div class="mb-3">
                <label for="vehicle_number" class="form-label">Vehicle Number</label>
                <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" value="{{ old('vehicle_number', $blacklist->vehicle_number) }}" style="text-transform: uppercase;" placeholder="e.g., ABC-1234" oninput="validateVehicleNumber()" onblur="validateVehicleNumber()">
                <span id="vehicle_number_error" class="text-danger" style="font-size: 0.875rem;"></span>
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                <textarea name="reason" id="reason" class="form-control" required oninput="checkFormValidity()" onblur="checkFormValidity()">{{ old('reason', $blacklist->reason) }}</textarea>
            </div>

            <div id="validationMessage" class="alert alert-warning" style="display: none; font-size: 0.9rem;">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <span id="validationText"></span>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" id="submitBtn" class="btn btn-primary me-2"><i class="bi bi-save me-1"></i> Update Entry</button>
                <a href="{{ route('blacklist.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    // NIC validation function
    function validateNic() {
        const nicInput = document.getElementById('nic');
        const nicError = document.getElementById('nic_error');
        let value = nicInput.value.trim();
        
        // If empty, clear error (it's optional)
        if (value === '') {
            nicError.textContent = '';
            nicInput.classList.remove('is-invalid');
            checkFormValidity();
            return true;
        }
        
        // Convert to uppercase for validation
        value = value.toUpperCase();
        
        // Validate NIC format: Old (9 digits + V) or New (12 digits)
        const nicRegex = /^(?:\d{9}V|\d{12})$/;
        
        if (!nicRegex.test(value)) {
            nicError.textContent = 'Enter a valid NIC number (9 digits + V for old format or 12 digits for new format)';
            nicInput.classList.add('is-invalid');
            checkFormValidity();
            return false;
        } else {
            nicError.textContent = '';
            nicInput.classList.remove('is-invalid');
            checkFormValidity();
            return true;
        }
    }
    
    // Vehicle number validation function
    function validateVehicleNumber() {
        const vehicleInput = document.getElementById('vehicle_number');
        const vehicleError = document.getElementById('vehicle_number_error');
        const value = vehicleInput.value.trim();
        
        // If empty, clear error (it's optional)
        if (value === '') {
            vehicleError.textContent = '';
            vehicleInput.classList.remove('is-invalid');
            checkFormValidity();
            return true;
        }
        
        // Validate vehicle number format: uppercase letters, numbers, and hyphens
        const vehicleRegex = /^[A-Z0-9\-]+$/;
        
        if (!vehicleRegex.test(value)) {
            vehicleError.textContent = 'Vehicle Number must contain only uppercase letters, numbers, and hyphens';
            vehicleInput.classList.add('is-invalid');
            checkFormValidity();
            return false;
        } else {
            vehicleError.textContent = '';
            vehicleInput.classList.remove('is-invalid');
            checkFormValidity();
            return true;
        }
    }
    
    // Check overall form validity
    function checkFormValidity() {
        const nicInput = document.getElementById('nic');
        const vehicleInput = document.getElementById('vehicle_number');
        const companyInput = document.getElementById('company_name');
        const reasonInput = document.getElementById('reason');
        const submitBtn = document.getElementById('submitBtn');
        const validationMessage = document.getElementById('validationMessage');
        const validationText = document.getElementById('validationText');
        
        const nicValue = nicInput.value.trim();
        const vehicleValue = vehicleInput.value.trim();
        const companyValue = companyInput.value.trim();
        const reasonValue = reasonInput.value.trim();
        
        // Check if at least one of NIC, Vehicle Number, or Company Name is provided
        const hasIdentifier = nicValue !== '' || vehicleValue !== '' || companyValue !== '';
        
        // Check if reason is filled
        const hasReason = reasonValue !== '';
        
        // Check if there are any validation errors
        const hasNicError = nicInput.classList.contains('is-invalid');
        const hasVehicleError = vehicleInput.classList.contains('is-invalid');
        
        // Build validation message
        let missingFields = [];
        if (!hasIdentifier) {
            missingFields.push('at least one of NIC, Vehicle Number, or Company Name');
        }
        if (!hasReason) {
            missingFields.push('Reason');
        }
        if (hasNicError) {
            missingFields.push('valid NIC format');
        }
        if (hasVehicleError) {
            missingFields.push('valid Vehicle Number format');
        }
        
        // Enable button only if all conditions are met
        if (hasIdentifier && hasReason && !hasNicError && !hasVehicleError) {
            submitBtn.disabled = false;
            submitBtn.style.opacity = '1';
            submitBtn.style.cursor = 'pointer';
            validationMessage.style.display = 'none';
        } else {
            submitBtn.disabled = true;
            submitBtn.style.opacity = '0.65';
            submitBtn.style.cursor = 'not-allowed';
            
            // Show validation message
            if (missingFields.length > 0) {
                validationText.textContent = 'Please provide: ' + missingFields.join(', ');
                validationMessage.style.display = 'block';
            }
        }
    }
    
    // Run validation on page load
    document.addEventListener('DOMContentLoaded', function() {
        const nicInput = document.getElementById('nic');
        const vehicleInput = document.getElementById('vehicle_number');
        
        if (nicInput && nicInput.value.trim() !== '') {
            validateNic();
        }
        if (vehicleInput && vehicleInput.value.trim() !== '') {
            validateVehicleNumber();
        }
        
        checkFormValidity();
    });
</script>

@endsection
