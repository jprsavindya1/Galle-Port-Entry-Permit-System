@extends('layouts.app')

@section('title', 'Edit Permit')

@section('content')
{{-- External CSS links are necessary for the provided style to work. Assuming they are available in layouts.app or will be included here. --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    /* --- User Dashboard Styles Applied to Form --- */
    .user-dashboard-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        padding: 2rem; /* Adjusted for better form padding */
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
    }
    .form-control, .form-select, .input-group-text {
        border-radius: 0.5rem;
        border: 1px solid #bbdefb; /* Light blue border */
        background-color: #f8fafc; /* Very light background */
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
    }
    /* Select2 Styling Overrides */
    .select2-container--default .select2-selection--single {
        height: calc(2.375rem + 2px); 
        border: 1px solid #bbdefb !important;
        border-radius: 0.5rem !important;
        background-color: #f8fafc !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.375rem + 2px);
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
</style>

<div class="container py-4">
    <div class="user-dashboard-card mx-auto" style="max-width: 900px;">
        <div class="user-dashboard-title text-center">
            <i class="bi bi-pencil-square me-2"></i> Edit Permit ({{ $permit->type }})
        </div>

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

            {{-- --- Section 1: ID & Duration --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-person-vcard me-2"></i> Personal Identification & Validity</div>

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="id_type" class="form-label">ID Type</label>
                        <select name="id_type" id="id_type" class="form-select" required onchange="updateIdValidation();">
                            <option value="NIC" {{ old('id_type', $permit->id_type) == 'NIC' ? 'selected' : '' }}>NIC</option>
                            <option value="Passport" {{ old('id_type', $permit->id_type) == 'Passport' ? 'selected' : '' }}>Passport</option>
                            <option value="Driving License" {{ old('id_type', $permit->id_type) == 'Driving License' ? 'selected' : '' }}>Driving License</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="id_number" class="form-label">ID Number</label>
                        <input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number', $permit->id_number) }}" required oninput="this.value = this.value.toUpperCase(); updateIdValidation();">
                        <span id="id_number_error" class="text-danger small"></span>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date', $permit->from_date) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('to_date', $permit->to_date) }}" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" name="full_name" id="full_name" class="form-control"
                            value="{{ old('full_name', $permit->full_name) }}" required
                            style="text-transform: uppercase;" oninput="this.value = this.value.toUpperCase();">
                </div>

                <div class="mb-4">
                    <label for="initials" class="form-label">Initials</label>
                    <input type="text" name="initials" id="initials" class="form-control" value="{{ old('initials', $permit->initials) }}" required>
                </div>

                {{-- Check Availability Button --}}
                <div class="d-flex align-items-center p-3 bg-light rounded border">
                    <button type="button" class="btn btn-info me-3" onclick="checkPermitAvailability(true)">
                        <i class="bi bi-check-circle-fill me-1"></i> Check Availability
                    </button>
                    <p id="availability-msg" class="fw-bold my-0"></p>
                </div>
            </div>

            {{-- --- Section 2: Professional & Location --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-briefcase me-2"></i> Professional & Contact Details</div>
                
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

                <div class="row mb-3">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <label for="company_name" class="form-label">Company Name</label>
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
                        <label for="company_address" class="form-label">Company Address</label>
                        <input type="text" name="company_address" id="company_address"
                            class="form-control bg-light" value="{{ old('company_address', $companyAddress) }}" readonly>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="residence_address" class="form-label">Residence Address</label>
                    <input type="text" name="residence_address" id="residence_address" class="form-control"
                            value="{{ old('residence_address', $permit->residence_address) }}">
                </div>
            </div>

            {{-- --- Section 3: Reason & Police Report (Conditional) --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-file-earmark-text me-2"></i> Permit Details</div>
                
                <div class="mb-4">
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
                            <label for="police_issue_date" class="form-label">Police Report Issue Date</label>
                            <input type="date" name="police_issue_date" id="police_issue_date" class="form-control" value="{{ old('police_issue_date', $permit->police_issue_date) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="police_expire_date" class="form-label">Police Report Expiry Date</label>
                            <input type="date" name="police_expire_date" id="police_expire_date" class="form-control" value="{{ old('police_expire_date', $permit->police_expire_date) }}" required>
                        </div>
                    </div>
                @endif
            </div>

            {{-- --- Submit & Cancel --- --}}
            <div class="d-flex justify-content-end pt-3">
                <button type="submit" class="btn btn-primary btn-lg me-3">
                    <i class="bi bi-save me-1"></i> Update Permit
                </button>
                <a href="{{ route('permits.submitted') }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // Initialize Select2 for better UX on dropdowns
    $(document).ready(function() {
        $('#designation').select2({
            placeholder: "-- Select Designation --",
            allowClear: true
        });
        $('#company_name').select2({
            placeholder: "-- Select Company --",
            allowClear: true
        });
        $('#reason').select2({
            placeholder: "-- Select Reason --",
            allowClear: true
        });

        // Set search input placeholder when dropdown opens
        $('.form-select').on('select2:open', function () {
            const placeholderText = $(this).attr('id') === 'company_name' ? 'Search company...' : 
                                    ($(this).attr('id') === 'designation' ? 'Search Designation...' : 'Search...');
            setTimeout(() => {
                const searchField = document.querySelector('.select2-search__field');
                if (searchField) {
                    searchField.placeholder = placeholderText;
                }
            }, 10);
        });

        // Sync company address on change 
        $('#company_name').on('change', function() {
            const selected = this.options[this.selectedIndex];
            const address = selected ? selected.getAttribute('data-address') || '' : '';
            document.getElementById('company_address').value = address;
        });

        // Initial address set on page load if a company is already selected
        const initialCompanySelect = document.getElementById('company_name');
        if (initialCompanySelect.value) {
            const selectedOption = initialCompanySelect.options[initialCompanySelect.selectedIndex];
            if (selectedOption) {
                 document.getElementById('company_address').value = selectedOption.getAttribute('data-address') || '';
            }
        }

        // Initialize validation on page load
        validateId();
    });

    /**
     * Validates ID number based on selected ID type
     */
    function validateId() {
        const idType = document.getElementById('id_type').value;
        const idNumber = document.getElementById('id_number').value.trim();
        const errorSpan = document.getElementById('id_number_error');

        if (!idNumber) {
            errorSpan.textContent = '';
            return true;
        }

        let isValid = false;
        let errorMessage = '';

        switch(idType) {
            case 'NIC':
                // Old format: 9 digits + V/X or New format: 12 digits
                const nicPattern = /^(?:\d{9}[VXvx]|\d{12})$/;
                isValid = nicPattern.test(idNumber);
                errorMessage = 'Invalid NIC format. Use 9 digits + V/X or 12 digits';
                break;
            case 'Passport':
                // 1 or 2 letters followed by 6 or 7 digits
                const passportPattern = /^[A-Z]{1,2}\d{6,7}$/i;
                isValid = passportPattern.test(idNumber);
                errorMessage = 'Invalid Passport format. Use 1-2 letters followed by 6-7 digits';
                break;
            case 'Driving License':
                // 7-8 digits OR letter + 7 digits OR old NIC format OR new NIC format
                const licensePattern = /^(?:\d{7,8}|[A-Z]\d{7}|\d{9}[VXvx]|\d{12})$/;
                isValid = licensePattern.test(idNumber);
                errorMessage = 'Invalid License format';
                break;
            default:
                isValid = true;
        }

        if (isValid) {
            errorSpan.textContent = '';
            errorSpan.style.display = 'none';
        } else {
            errorSpan.textContent = errorMessage;
            errorSpan.style.display = 'block';
        }

        return isValid;
    }

    // Make validateId available globally for inline event handlers
    window.updateIdValidation = validateId;

    // --- Original Script Functions (Preserved) ---

    /**
     * Checks the availability of the permit based on current form values.
     * @param {boolean} isEdit - Indicates if the check is performed during an edit session.
     */
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
            // This part is preserved from the original JS, assuming the VP form has this input
            const vehicleNumberInput = document.querySelector('input[name="vehicle_number"]');
            if (vehicleNumberInput) {
                payload.vehicle_number = vehicleNumberInput.value;
            }
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

    // Auto-fill company address function (now using pure JS for compatibility)
    // Note: The jQuery change listener handles the call to update this.
    // document.getElementById('company_name').addEventListener('change', function() {
    //     const selected = this.options[this.selectedIndex];
    //     const address = selected.getAttribute('data-address') || '';
    //     document.getElementById('company_address').value = address;
    // });
</script>
@endpush