@extends('layouts.app')

@section('title', 'Edit Permit Entry')

@section('content')
<!-- External Assets -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<!-- Select2 CSS for Designation field -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
    .form-control, .form-select, .select2-container--default .select2-selection--single {
        border-radius: 0.5rem;
        border: 1px solid #bbdefb;
        background-color: #f8fafc;
    }
    .form-control:focus, .form-select:focus, .select2-container--default.select2-container--focus .select2-selection--single {
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
    /* Select2 custom styling to match Bootstrap 5 height */
    .select2-container--default .select2-selection--single {
        height: calc(2.375rem + 2px); 
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-top: 0.375rem;
        padding-left: 0.75rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        top: 1px;
    }
    /* Hide dropdown arrow for disabled select elements */
    select:disabled {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: none !important;
    }
</style>

<div class="container py-4">
    <div class="user-dashboard-card mx-auto" style="max-width: 900px;">
        <div class="user-dashboard-title">
            <i class="bi bi-person-badge me-2"></i> Edit Temporary Permit Entry
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

        <form method="POST" action="{{ route('permit.updateSessionEntry', $index) }}">
            @csrf
            @method('PUT')
            
            <!-- Hidden inputs -->
            <input type="hidden" name="session_edit" value="1">
            <input type="hidden" name="company_name" value="{{ $permit['company_name'] ?? '' }}">
            <input type="hidden" name="company_address" value="{{ $permit['company_address'] ?? '' }}">

            {{-- DOCUMENTS ATTACHED SECTION --}}
            <fieldset class="mb-4">
                <legend class="col-form-label pt-0"><i class="bi bi-paperclip me-1"></i> Documents Attached</legend>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input doc-checkbox" id="doc_nic" name="documents[]" value="NIC"
                                {{ (isset($permit['documents']) && in_array('NIC', $permit['documents'])) || (!isset($permit['documents']) && ($permit['id_type'] ?? '') == 'NIC') ? 'checked' : '' }}
                                onchange="syncIdType('NIC')">
                            <label class="form-check-label" for="doc_nic">NIC</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input doc-checkbox" id="doc_passport" name="documents[]" value="Passport"
                                {{ (isset($permit['documents']) && in_array('Passport', $permit['documents'])) || (!isset($permit['documents']) && ($permit['id_type'] ?? '') == 'Passport') ? 'checked' : '' }}
                                onchange="syncIdType('Passport')">
                            <label class="form-check-label" for="doc_passport">Passport</label>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input doc-checkbox" id="doc_license" name="documents[]" value="License"
                                {{ (isset($permit['documents']) && in_array('License', $permit['documents'])) || (!isset($permit['documents']) && ($permit['id_type'] ?? '') == 'License') ? 'checked' : '' }}
                                onchange="syncIdType('License')">
                            <label class="form-check-label" for="doc_license">Driving License</label>
                        </div>
                    </div>
                </div>
            </fieldset>

            {{-- --- Section 1: Identification & Validity --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-card-heading me-2"></i> ID and Validity Period</div>

                <div class="row mb-3 align-items-start">
                    <div class="col-md-6">
                        <label for="id_type" class="form-label">Identification Type <span class="text-danger">*</span></label>
                        <select id="id_type" name="id_type" class="form-select" required disabled style="background-color: #e9ecef; cursor: not-allowed;" onchange="validateId(); setMaxToDate();">
                            <option value="">-- Select ID Type --</option>
                            <option value="NIC" {{ ($permit['id_type'] ?? '') == 'NIC' ? 'selected' : '' }}>NIC</option>
                            <option value="Passport" {{ ($permit['id_type'] ?? '') == 'Passport' ? 'selected' : '' }}>Passport</option>
                            <option value="License" {{ ($permit['id_type'] ?? '') == 'License' ? 'selected' : '' }}>Driving License</option>
                        </select>
                        <small class="text-muted d-block mt-1"><i class="bi bi-info-circle me-1"></i>ID type is controlled by document selection</small>
                    </div>
                    <div class="col-md-6">
                        <label for="id_number" class="form-label">Identification Number <span class="text-danger">*</span></label>
                        <input type="text" id="id_number" name="id_number" class="form-control" required
                               value="{{ $permit['id_number'] ?? '' }}" oninput="updateIdValidation(); handleIdNumberChange(); checkDuplicateInCart(); checkBlacklistStatus();" onblur="fetchPersonDetails();">
                        <div style="min-height: 20px;">
                            <span id="id_number_error" class="text-danger" style="font-size: 0.875rem; display: none;"></span>
                            <span id="blacklist_msg" class="small d-block" style="font-weight: 500;"></span>
                            <span id="duplicate_error" class="text-danger small"></span>
                        </div>
                    </div>
                </div>

                {{-- Passport Type Selection --}}
                <div class="row mb-3" id="passport_type_row" style="display: none;">
                    <div class="col-md-12">
                        <label class="form-label"><i class="bi bi-flag me-1"></i> Passport Type</label><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="passport_type" id="local_passport" value="local" class="form-check-input" {{ old('passport_type', $permit['passport_type'] ?? 'local') == 'local' ? 'checked' : '' }} onchange="toggleNicField()">
                            <label class="form-check-label" for="local_passport">Local Passport</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="passport_type" id="foreigner_passport" value="foreigner" class="form-check-input" {{ old('passport_type', $permit['passport_type'] ?? '') == 'foreigner' ? 'checked' : '' }} onchange="toggleNicField()">
                            <label class="form-check-label" for="foreigner_passport">Foreigner Passport</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-3" id="nic_number_row" style="display: none;">
                    <div class="col-md-12">
                        <label for="nic_number" id="nic_label" class="form-label"><i class="bi bi-card-text me-1"></i> NIC Number (Required)</label>
                        <input type="text" name="nic_number" id="nic_number" 
                               value="{{ old('nic_number', $permit['nic_number'] ?? '') }}" class="form-control"
                               oninput="this.value = this.value.toUpperCase(); validateNicNumber(); checkDuplicateInCart(); checkBlacklistStatusNic();"
                               placeholder="Enter NIC number to connect with other IDs">
                        <div style="min-height: 20px;">
                            <span id="nic_number_error" class="text-danger small d-block"></span>
                            <span id="nic_blacklist_msg" class="small d-block" style="font-weight: 500;"></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="from_date" class="form-label">From Date <span class="text-danger">*</span></label>
                        <input type="date" id="from_date" name="from_date" class="form-control" required
                               value="{{ $permit['from_date'] ?? '' }}" onchange="setMaxToDate()">
                    </div>
                    <div class="col-md-6">
                        <label for="to_date" class="form-label">To Date <span class="text-danger">*</span></label>
                        <input type="date" id="to_date" name="to_date" class="form-control" required
                               value="{{ $permit['to_date'] ?? '' }}">
                    </div>
                </div>
            </div>

            {{-- --- Section 2: Personal Details --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-person me-2"></i> Personal Details</div>

                <div class="mb-3">
                    <label for="full_name" class="form-label"><i class="bi bi-person-vcard me-1"></i> Full Name</label>
                    <input type="text" 
                           name="full_name" 
                           id="full_name"
                           value="{{ old('full_name', $permit['full_name'] ?? '') }}" 
                           class="form-control" 
                           required
                           oninput="this.value = this.value.toUpperCase();">
                </div>

                <div class="mb-3">
                    <label for="initials" class="form-label"><i class="bi bi-text-short me-1"></i> Name with Initials</label>
                    <input type="text" name="initials" id="initials" value="{{ $permit['initials'] }}" class="form-control" required>
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
                    <textarea name="residence_address" id="residence_address" rows="2" class="form-control">{{ $permit['residence_address'] ?? '' }}</textarea>
                </div>
            </div>

            {{-- --- Section 3: Permit Specifics & Actions --- --}}
            <div class="form-section-card">
                <div class="form-section-title"><i class="bi bi-ticket-perforated me-2"></i> Permit Specifications</div>
                
                <div class="mb-3 d-flex align-items-center">
                    <button type="button" onclick="checkAvailability(true)" class="btn btn-info me-3">
                        <i class="bi bi-search me-1"></i> Check Availability
                    </button>
                    <p id="availability-msg" class="fw-bold my-0"></p>
                </div>

                <div class="mb-3">
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

                @php
                    $selectedPasses = explode(',', $permit['pass_type']);
                @endphp
                <fieldset class="mb-3">
                    <legend class="col-form-label pt-0"><i class="bi bi-layers-half me-1"></i> Pass Type</legend><br>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="pass_type[]" value="onboard" id="pass_onboard" class="form-check-input" 
                            {{ in_array('onboard', $selectedPasses) ? 'checked' : '' }}>
                        <label class="form-check-label" for="pass_onboard">Onboard</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="pass_type[]" value="afloat" id="pass_afloat" class="form-check-input"
                            {{ in_array('afloat', $selectedPasses) ? 'checked' : '' }}>
                        <label class="form-check-label" for="pass_afloat">Afloat</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="pass_type[]" value="ashore" id="pass_ashore" class="form-check-input"
                            {{ in_array('ashore', $selectedPasses) ? 'checked' : '' }}>
                        <label class="form-check-label" for="pass_ashore">Ashore</label>
                    </div>
                </fieldset>

                <fieldset class="mb-4">
                    <legend class="col-form-label pt-0"><i class="bi bi-cash me-1"></i> Issue Type</legend><br>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="issue_type" id="issue_free" value="free" class="form-check-input" {{ $permit['issue_type'] == 'free' ? 'checked' : '' }}>
                        <label class="form-check-label" for="issue_free">Free Issue</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="issue_type" id="issue_payment" value="payment" class="form-check-input" {{ $permit['issue_type'] == 'payment' ? 'checked' : '' }}>
                        <label class="form-check-label" for="issue_payment">On Payment</label>
                    </div>
                </fieldset>

            </div>

            {{-- --- Action Buttons --- --}}
            <div class="d-flex justify-content-end pt-3">
                <button type="submit" id="updateBtn" class="btn btn-primary btn-lg me-3" disabled style="background-color: #9e9e9e !important; border-color: #9e9e9e !important; opacity: 0.65; cursor: not-allowed;">
                    <i class="bi bi-save me-1"></i> Update Entry
                </button>
                <a href="{{ route('permit.temporary') }}" class="btn btn-secondary btn-lg">
                    <i class="bi bi-x-circle me-1"></i> Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // Store checked form data to detect changes
        let checkedFormData = null;
        // Store ID validation state globally
        let isIdValid = false;
        // Store the last fetched ID number to track changes
        let lastFetchedIdNumber = '';
        // Store previous ID type to detect changes
        let previousIdType = null;
        // Store original values from the form (from temporary form)
        let originalValues = {
            id_type: '{{ $permit["id_type"] ?? "" }}',
            id_number: '{{ $permit["id_number"] ?? "" }}'
        };
        // Store blacklist status
        let isBlacklisted = false;

        // Function to check blacklist status for ID number
        window.checkBlacklistStatus = function() {
            const idNumber = document.getElementById('id_number').value.trim();
            const msgEl = document.getElementById('blacklist_msg');
            
            if (!idNumber) {
                msgEl.textContent = '';
                msgEl.style.color = '';
                isBlacklisted = false;
                return;
            }

            fetch("{{ route('permit.checkBlacklist') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ id_number: idNumber })
            })
            .then(res => res.json())
            .then(data => {
                if (data.blacklisted) {
                    msgEl.textContent = data.message;
                    msgEl.style.color = 'red';
                    isBlacklisted = true;
                    // Disable update button
                    const updateBtn = document.getElementById('updateBtn');
                    if (updateBtn) {
                        updateBtn.disabled = true;
                        updateBtn.style.opacity = '0.6';
                        updateBtn.style.cursor = 'not-allowed';
                    }
                    // Disable check availability button
                    const checkBtn = document.querySelector('button[onclick="checkAvailability(true)"]');
                    if (checkBtn) {
                        checkBtn.disabled = true;
                        checkBtn.style.opacity = '0.6';
                        checkBtn.style.cursor = 'not-allowed';
                    }
                } else {
                    msgEl.textContent = data.message;
                    msgEl.style.color = 'green';
                    isBlacklisted = false;
                    // Enable check availability button
                    const checkBtn = document.querySelector('button[onclick="checkAvailability(true)"]');
                    if (checkBtn) {
                        checkBtn.disabled = false;
                        checkBtn.style.opacity = '1';
                        checkBtn.style.cursor = 'pointer';
                    }
                }
            })
            .catch(error => {
                console.error("Failed to check blacklist:", error);
                msgEl.textContent = '';
                isBlacklisted = false;
            });
        }

        // Function to check blacklist status for NIC number field
        window.checkBlacklistStatusNic = function() {
            const nicNumber = document.getElementById('nic_number').value.trim();
            const msgEl = document.getElementById('nic_blacklist_msg');
            
            if (!nicNumber) {
                msgEl.textContent = '';
                msgEl.style.color = '';
                return;
            }

            fetch("{{ route('permit.checkBlacklist') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ nic_number: nicNumber })
            })
            .then(res => res.json())
            .then(data => {
                if (data.blacklisted) {
                    msgEl.textContent = data.message;
                    msgEl.style.color = 'red';
                    // Disable update button
                    const updateBtn = document.getElementById('updateBtn');
                    if (updateBtn) {
                        updateBtn.disabled = true;
                        updateBtn.style.opacity = '0.6';
                        updateBtn.style.cursor = 'not-allowed';
                    }
                    // Disable check availability button
                    const checkBtn = document.querySelector('button[onclick="checkAvailability(true)"]');
                    if (checkBtn) {
                        checkBtn.disabled = true;
                        checkBtn.style.opacity = '0.6';
                        checkBtn.style.cursor = 'not-allowed';
                    }
                } else {
                    msgEl.textContent = data.message;
                    msgEl.style.color = 'green';
                    // Enable check availability button only if ID is not blacklisted AND NIC is valid
                    if (!isBlacklisted && isNicValid) {
                        const checkBtn = document.querySelector('button[onclick="checkAvailability(true)"]');
                        if (checkBtn) {
                            checkBtn.disabled = false;
                            checkBtn.style.opacity = '1';
                            checkBtn.style.cursor = 'pointer';
                        }
                    }
                }
            })
            .catch(error => {
                console.error("Failed to check blacklist:", error);
                msgEl.textContent = '';
            });
        }

        // Function to handle ID number change - clear autofilled data when changed
        window.handleIdNumberChange = function() {
            const currentIdNumber = document.getElementById('id_number').value.trim();
            
            // If ID number has changed from the last fetched one, clear autofilled fields
            if (lastFetchedIdNumber && currentIdNumber !== lastFetchedIdNumber) {
                document.getElementById('full_name').value = '';
                document.getElementById('initials').value = '';
                document.getElementById('residence_address').value = '';
                
                // Clear designation using Select2
                $('#designation').val(null).trigger('change');
                
                // Reset the last fetched ID number
                lastFetchedIdNumber = '';
            }
        }

        // Function to fetch person details from database
        window.fetchPersonDetails = function() {
            const idNumber = document.getElementById('id_number').value.trim();
            
            if (!idNumber) {
                return;
            }

            fetch("{{ route('permit.fetchPersonDetails') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ id_number: idNumber })
            })
            .then(res => res.json())
            .then(data => {
                if (data.found) {
                    // Auto-fill the fields
                    document.getElementById('full_name').value = data.data.full_name || '';
                    document.getElementById('initials').value = data.data.initials || '';
                    
                    // Set designation using Select2
                    if (data.data.designation) {
                        $('#designation').val(data.data.designation).trigger('change');
                    }
                    
                    document.getElementById('residence_address').value = data.data.residence_address || '';
                    
                    // Store the fetched ID number
                    lastFetchedIdNumber = idNumber;
                    
                    console.log('Person details auto-filled successfully');
                }
            })
            .catch(error => {
                console.error("Failed to fetch person details:", error);
            });
        }

        // Function to check for duplicate ID in session cart
        window.checkDuplicateInCart = function() {
            const idNumber = document.getElementById('id_number').value.trim().toUpperCase();
            const nicNumber = document.getElementById('nic_number').value.trim().toUpperCase();
            const duplicateError = document.getElementById('duplicate_error');
            const updateBtn = document.getElementById('updateBtn');
            
            if (!idNumber && !nicNumber) {
                duplicateError.textContent = '';
                return;
            }

            let isDuplicate = false;

            // Get all session permits from the session
            const sessionPermits = @json(session('temporary_permit_cart', []));
            const currentEditIndex = {{ $index }};
            
            // Check against cart data for connected IDs
            sessionPermits.forEach((permit, index) => {
                // Skip the current entry being edited
                if (index === currentEditIndex) {
                    return;
                }
                
                const cartIdNumber = (permit.id_number || '').toUpperCase();
                const cartNicNumber = (permit.nic_number || '').toUpperCase();
                
                // Check if current ID number matches any existing ID numbers or NIC numbers
                if (idNumber && (cartIdNumber === idNumber || cartNicNumber === idNumber)) {
                    isDuplicate = true;
                    return;
                }
                
                // Check if current NIC number matches any existing ID numbers or NIC numbers
                if (nicNumber && (cartIdNumber === nicNumber || cartNicNumber === nicNumber)) {
                    isDuplicate = true;
                    return;
                }
            });

            if (isDuplicate) {
                duplicateError.textContent = `⚠️ The person who this identification number belongs to already has an entry in the cart. One person can only have one permit per submission.`;
                duplicateError.style.display = 'block';
                duplicateError.style.color = '#dc3545';
                duplicateError.style.fontWeight = '500';
                // Disable the update button
                if (updateBtn) {
                    updateBtn.disabled = true;
                    updateBtn.style.backgroundColor = '#9e9e9e';
                    updateBtn.style.borderColor = '#9e9e9e';
                    updateBtn.style.opacity = '0.65';
                    updateBtn.style.cursor = 'not-allowed';
                }
            } else {
                duplicateError.textContent = '';
                duplicateError.style.display = 'none';
            }
        }

        // Initialize Select2 on the Designation dropdown
        $('#designation').select2({
            placeholder: "-- Select Designation --",
            allowClear: true
        });

        // Set search input placeholder when dropdown opens
        $('#designation').on('select2:open', function () {
            setTimeout(() => {
                document.querySelector('.select2-search__field').placeholder = 'Search Designation...';
            }, 10);
        });
        
        // Call setMaxToDate on page load to initialize the date constraints
        document.addEventListener('DOMContentLoaded', function() {
            setMaxToDate();
            
            // Validate ID if it has a value on page load (e.g., after validation error)
            const idNumber = document.getElementById('id_number');
            if (idNumber.value.trim() !== "") {
                validateId(); // Initial validation
            }
            
            // Initialize NIC field visibility
            toggleNicField();
            
            // If Passport is selected, show passport type row
            if (idTypeDropdown.value === 'Passport') {
                document.getElementById('passport_type_row').style.display = 'block';
            }
            
            // Initialize previous ID type
            const idTypeDropdown = document.getElementById('id_type');
            previousIdType = idTypeDropdown.value;
            
            // Enable ID type dropdown before form submission
            const form = document.querySelector('form[action="{{ route('permit.updateSessionEntry', $index) }}"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Enable ID type dropdown before form submission
                    idTypeDropdown.disabled = false;
                    
                    // Check for duplicate error
                    const duplicateError = document.getElementById('duplicate_error');
                    if (duplicateError && duplicateError.textContent.trim() !== '') {
                        e.preventDefault();
                        alert('Cannot submit: This ID Number or name is already in the cart. Please use a different ID.');
                        idTypeDropdown.disabled = true;
                        return false;
                    }
                    
                    // Check if ID is valid before allowing submission
                    if (!isIdValid && document.getElementById('id_number').value.trim() !== '') {
                        e.preventDefault();
                        alert('Please enter a valid Identification Number before submitting.');
                        idTypeDropdown.disabled = true;
                        return false;
                    }
                });
            }
        });

        /**
         * Syncs the ID Type dropdown based on document checkbox selection
         * and locks/unlocks the dropdown accordingly
         */
        function syncIdType(selectedType) {
            const idTypeDropdown = document.getElementById('id_type');
            const idNumberInput = document.getElementById('id_number');
            const idNumberError = document.getElementById('id_number_error');
            const availabilityMsg = document.getElementById('availability-msg');
            const updateBtn = document.getElementById('updateBtn');
            const checkboxes = document.querySelectorAll('.doc-checkbox');
            
            // Find the checkbox that was just clicked
            let clickedCheckbox = null;
            checkboxes.forEach(cb => {
                if (cb.value === selectedType) {
                    clickedCheckbox = cb;
                }
            });
            
            if (clickedCheckbox && clickedCheckbox.checked) {
                // Uncheck all other document checkboxes
                checkboxes.forEach(cb => {
                    if (cb !== clickedCheckbox) {
                        cb.checked = false;
                    }
                });
                
                // Set the ID type dropdown to match the checked document
                idTypeDropdown.value = selectedType;
                
                // Clear ID blacklist message when switching document types
                const blacklistMsg = document.getElementById('blacklist_msg');
                if (blacklistMsg) {
                    blacklistMsg.textContent = '';
                    blacklistMsg.style.color = '';
                }
                
                // Clear NIC field and error messages when switching document types
                const nicInput = document.getElementById('nic_number');
                const nicError = document.getElementById('nic_number_error');
                const nicBlacklistMsg = document.getElementById('nic_blacklist_msg');
                if (nicInput) {
                    nicInput.value = '';
                    if (nicError) {
                        nicError.textContent = '';
                    }
                    nicInput.classList.remove('is-invalid');
                    if (nicBlacklistMsg) {
                        nicBlacklistMsg.textContent = '';
                        nicBlacklistMsg.style.color = '';
                    }
                    isNicValid = true;
                }
                
                // Reset blacklist status
                isBlacklisted = false;
                
                // Re-enable check availability button
                const checkBtn = document.querySelector('button[onclick="checkAvailability(true)"]');
                if (checkBtn) {
                    checkBtn.disabled = false;
                    checkBtn.style.opacity = '1';
                    checkBtn.style.cursor = 'pointer';
                }
                
                // Check if ID type has changed
                if (previousIdType !== null && previousIdType !== selectedType) {
                    // Check if switching back to original ID type from temporary form
                    if (selectedType === originalValues.id_type) {
                        // Restore original ID number
                        idNumberInput.value = originalValues.id_number;
                    } else {
                        // Clear the fields for a different ID type
                        idNumberInput.value = '';
                    }
                    
                    // Clear error and availability messages
                    idNumberError.textContent = '';
                    idNumberError.style.display = 'none';
                    if (availabilityMsg) {
                        availabilityMsg.textContent = '';
                    }
                    isIdValid = false;
                    updateBtn.disabled = true;
                    updateBtn.style.backgroundColor = '#9e9e9e';
                    updateBtn.style.borderColor = '#9e9e9e';
                    updateBtn.style.opacity = '0.65';
                    updateBtn.style.cursor = 'not-allowed';
                }
                
                // Update previous ID type
                previousIdType = selectedType;
                
                // Validate the current ID number
                validateId();
                setMaxToDate();
                toggleNicField();
                
                // If Passport selected, set default to local passport
                if (selectedType === 'Passport') {
                    if (!document.getElementById('local_passport').checked && !document.getElementById('foreigner_passport').checked) {
                        document.getElementById('local_passport').checked = true;
                    }
                }
            } else {
                // If all checkboxes are unchecked, keep the dropdown disabled
                let anyChecked = false;
                checkboxes.forEach(cb => {
                    if (cb.checked) anyChecked = true;
                });
                
                if (!anyChecked) {
                    idTypeDropdown.value = '';
                    previousIdType = null;
                }
            }
        }        /**
         * Validates ID number based on selected ID type
         */
        function validateId() {
            const idType = document.getElementById('id_type').value;
            const idNumber = document.getElementById('id_number').value.trim();
            const errorSpan = document.getElementById('id_number_error');

            if (!idNumber) {
                errorSpan.textContent = '';
                errorSpan.style.display = 'none';
                isIdValid = false;
                return false;
            }

            let valid = false;
            let errorMessage = '';

            switch(idType) {
                case 'NIC':
                    // Old format: 9 digits + V or New format: 12 digits
                    const nicPattern = /^(?:\d{9}[Vv]|\d{12})$/;
                    valid = nicPattern.test(idNumber);
                    errorMessage = 'Invalid NIC format. Use 9 digits + V or 12 digits';
                    break;
                case 'Passport':
                    // Starts with P, OL, or D followed by numbers
                    const passportPattern = /^(?:P|OL|D)\d+$/i;
                    valid = passportPattern.test(idNumber);
                    errorMessage = 'Invalid Passport format. Must start with P, OL, or D followed by numbers';
                    break;
                case 'License':
                    // Letter followed by 9 digits
                    const licensePattern = /^[A-Z]\d{9}$/i;
                    valid = licensePattern.test(idNumber);
                    errorMessage = 'Invalid License format. Must be a letter followed by 9 digits';
                    break;
                default:
                    errorMessage = 'Please select an ID type';
                    break;
            }

            if (valid) {
                errorSpan.textContent = '';
                errorSpan.style.display = 'none';
                isIdValid = true;
                // Re-enable check availability button if not blacklisted and NIC is valid
                if (!isBlacklisted && isNicValid) {
                    const checkBtn = document.querySelector('button[onclick="checkAvailability(true)"]');
                    if (checkBtn) {
                        checkBtn.disabled = false;
                        checkBtn.style.opacity = '1';
                        checkBtn.style.cursor = 'pointer';
                    }
                }
            } else {
                errorSpan.textContent = errorMessage;
                errorSpan.style.display = 'block';
                isIdValid = false;
                // Disable check availability button when ID is invalid
                const checkBtn = document.querySelector('button[onclick="checkAvailability(true)"]');
                if (checkBtn) {
                    checkBtn.disabled = true;
                    checkBtn.style.opacity = '0.6';
                    checkBtn.style.cursor = 'not-allowed';
                }
            }

            return valid;
        }

        // Make validateId available globally for inline event handlers
        window.updateIdValidation = function() {
            validateId();
            toggleNicField();
            // Clear NIC field when switching ID types
            document.getElementById('nic_number').value = '';
        }

        // Function to show/hide NIC Number field based on ID Type and Passport Type
        function toggleNicField() {
            const idType = document.getElementById('id_type').value;
            const nicInput = document.getElementById('nic_number');
            const nicLabel = document.getElementById('nic_label');
            const nicError = document.getElementById('nic_number_error');
            const nicBlacklistMsg = document.getElementById('nic_blacklist_msg');
            
            if (idType === 'Passport') {
                $('#passport_type_row').show();
                const localPassport = document.getElementById('local_passport').checked;
                const foreignerPassport = document.getElementById('foreigner_passport').checked;
                
                if (localPassport) {
                    $('#nic_number_row').show();
                    nicLabel.textContent = 'NIC Number (Required)';
                    nicInput.required = true;
                } else if (foreignerPassport) {
                    $('#nic_number_row').hide();
                    nicInput.value = '';
                    nicInput.required = false;
                    // Clear error and blacklist messages
                    nicError.textContent = '';
                    nicInput.classList.remove('is-invalid');
                    if (nicBlacklistMsg) {
                        nicBlacklistMsg.textContent = '';
                        nicBlacklistMsg.style.color = '';
                    }
                    isNicValid = true;
                } else {
                    // No radio selected, hide nic
                    $('#nic_number_row').hide();
                    nicInput.value = '';
                    nicInput.required = false;
                    // Clear error and blacklist messages
                    nicError.textContent = '';
                    nicInput.classList.remove('is-invalid');
                    if (nicBlacklistMsg) {
                        nicBlacklistMsg.textContent = '';
                        nicBlacklistMsg.style.color = '';
                    }
                    isNicValid = true;
                }
            } else if (idType === 'License') {
                $('#passport_type_row').hide();
                $('#nic_number_row').show();
                nicLabel.textContent = 'NIC Number (Required)';
                nicInput.required = true;
            } else {
                $('#passport_type_row').hide();
                $('#nic_number_row').hide();
                nicInput.value = '';
                nicInput.required = false;
                // Clear error and blacklist messages
                nicError.textContent = '';
                nicInput.classList.remove('is-invalid');
                if (nicBlacklistMsg) {
                    nicBlacklistMsg.textContent = '';
                    nicBlacklistMsg.style.color = '';
                }
                isNicValid = true;
            }
            
            // Re-enable check availability button if appropriate
            if (!nicInput.required || nicInput.value.trim() === '') {
                if (!isBlacklisted) {
                    const checkBtn = document.querySelector('button[onclick="checkAvailability()"]');
                    if (checkBtn) {
                        checkBtn.disabled = false;
                        checkBtn.style.opacity = '1';
                        checkBtn.style.cursor = 'pointer';
                    }
                }
            }
        }

        // Add event listeners for passport type radios to ensure toggleNicField is called
        document.getElementById('local_passport').addEventListener('change', toggleNicField);
        document.getElementById('foreigner_passport').addEventListener('change', toggleNicField);

        /**
         * Dynamically sets the maximum 'To Date' based on 'From Date' and 'Identification Type'.
         */
        function setMaxToDate() {
            const idType = document.getElementById('id_type').value;
            const fromDateInput = document.getElementById('from_date');
            const toDateInput = document.getElementById('to_date');

            const fromDate = new Date(fromDateInput.value);
            if (!fromDateInput.value) return;

            // Determine max days based on ID type
            let maxDays = 29; // NIC: Max 30 days total (from_date + 29)
            if (idType === 'Passport' || idType === 'License') {
                maxDays = 14; // Passport/License: Max 15 days total (from_date + 14)
            }

            const maxToDate = new Date(fromDate);
            maxToDate.setDate(maxToDate.getDate() + maxDays);

            const yyyy = maxToDate.getFullYear();
            const mm = String(maxToDate.getMonth() + 1).padStart(2, '0');
            const dd = String(maxToDate.getDate()).padStart(2, '0');
            
            const maxDateString = `${yyyy}-${mm}-${dd}`;

            toDateInput.min = fromDateInput.value;
            toDateInput.max = maxDateString;

            // Adjust 'To Date' if it falls outside the new allowed range
            if (toDateInput.value < toDateInput.min || toDateInput.value > toDateInput.max) {
                // Default to the minimum allowed date (same as from date)
                toDateInput.value = toDateInput.min; 
            }
        }

        /**
         * Validates NIC number format
         */
        function validateNicNumber() {
            const nicInput = document.getElementById('nic_number');
            const nicError = document.getElementById('nic_number_error');
            const value = nicInput.value.trim();
            
            // Clear blacklist message when NIC number is cleared
            const nicBlacklistMsg = document.getElementById('nic_blacklist_msg');
            if (nicBlacklistMsg && value === '') {
                nicBlacklistMsg.textContent = '';
                nicBlacklistMsg.style.color = '';
                
                // Re-enable check availability button if ID is not blacklisted
                if (!isBlacklisted) {
                    const checkBtn = document.querySelector('button[onclick="checkAvailability(true)"]');
                    if (checkBtn) {
                        checkBtn.disabled = false;
                        checkBtn.style.opacity = '1';
                        checkBtn.style.cursor = 'pointer';
                    }
                }
            }
            
            // If empty and not required, it's valid
            if (value === '' && !nicInput.required) {
                nicError.textContent = '';
                nicInput.classList.remove('is-invalid');
                isNicValid = true;
                return true;
            }
            
            // If empty and required, it's invalid
            if (value === '' && nicInput.required) {
                nicError.textContent = 'NIC Number is required.';
                nicInput.classList.add('is-invalid');
                isNicValid = false;
                return false;
            }
            
            // Validate NIC format: Old (9 digits + V) or New (12 digits)
            const nicRegex = /^(?:\d{9}[Vv]|\d{12})$/;
            
            if (!nicRegex.test(value)) {
                nicError.textContent = 'Enter a valid NIC number (9 digits + V for old format or 12 digits for new format)';
                nicInput.classList.add('is-invalid');
                isNicValid = false;
                // Disable check availability button when NIC is invalid
                const checkBtn = document.querySelector('button[onclick="checkAvailability(true)"]');
                if (checkBtn) {
                    checkBtn.disabled = true;
                    checkBtn.style.opacity = '0.6';
                    checkBtn.style.cursor = 'not-allowed';
                }
                return false;
            } else {
                nicError.textContent = '';
                nicInput.classList.remove('is-invalid');
                isNicValid = true;
                return true;
            }
        }

        /**
         * Checks the availability of the permit using the ID details and dates.
         * The `isEdit` flag ensures the company check is skipped in the backend.
         */
        function checkAvailability(isEdit = false) {
            const idType = document.getElementById('id_type').value;
            const idNumber = document.querySelector('input[name="id_number"]').value;
            const fullName = document.querySelector('input[name="full_name"]').value;
            const initials = document.querySelector('input[name="initials"]').value;
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;

            const msg = document.getElementById('availability-msg');
            const updateBtn = document.getElementById('updateBtn');
            
            msg.innerText = '';
            // Disable button while checking
            updateBtn.disabled = true;
            updateBtn.style.opacity = '0.6';
            updateBtn.style.cursor = 'not-allowed';

            // Check if blacklisted first
            if (isBlacklisted) {
                msg.innerText = 'Cannot check availability: This ID is blacklisted.';
                msg.style.color = 'red';
                return;
            }

            // Check for duplicate error first
            const duplicateError = document.getElementById('duplicate_error');
            if (duplicateError && duplicateError.textContent.trim() !== '') {
                msg.innerText = 'Cannot check availability: This ID Number or name is already in the cart.';
                msg.style.color = 'red';
                return;
            }

            // Validate ID before checking availability
            if (!isIdValid) {
                msg.innerText = 'Please enter a valid Identification Number';
                msg.style.color = 'red';
                return;
            }

            if (!idType || !idNumber || !fullName || !initials || !fromDate || !toDate) {
                msg.innerText = "Please fill in all required fields to check availability.";
                msg.style.color = 'red';
                return;
            }

            const body = {
                id_type: idType,
                id_number: idNumber,
                nic_number: document.getElementById('nic_number').value,
                full_name: fullName,
                initials: initials,
                from_date: fromDate,
                to_date: toDate,
                session_edit: isEdit // flag to skip company check in backend
            };

            fetch("{{ route('permit.checkAvailability') }}", {
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
                        id_type: idType,
                        id_number: idNumber,
                        nic_number: document.getElementById('nic_number').value,
                        full_name: fullName,
                        initials: initials,
                        from_date: fromDate,
                        to_date: toDate
                    };
                    
                    // Include passport_type if applicable
                    if (idType === 'Passport') {
                        checkedFormData.passport_type = document.querySelector('input[name="passport_type"]:checked')?.value || '';
                    }
                    
                    // Attach change listeners to form fields
                    attachChangeListeners();
                } else {
                    // Keep it grey when not available or has duplicate
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
                id_number: document.querySelector('input[name="id_number"]').value,
                nic_number: document.getElementById('nic_number').value,
                full_name: document.querySelector('input[name="full_name"]').value,
                initials: document.querySelector('input[name="initials"]').value,
                from_date: document.getElementById('from_date').value,
                to_date: document.getElementById('to_date').value
            };
            
            // Include passport_type if applicable
            if (currentData.id_type === 'Passport') {
                currentData.passport_type = document.querySelector('input[name="passport_type"]:checked')?.value || '';
            }
            
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
            const fieldSelectors = [
                { id: 'id_type', type: 'select' },
                { name: 'id_number', type: 'input' },
                { id: 'nic_number', type: 'input' },
                { name: 'full_name', type: 'input' },
                { name: 'initials', type: 'input' },
                { id: 'from_date', type: 'input' },
                { id: 'to_date', type: 'input' }
            ];
            
            fieldSelectors.forEach(selector => {
                const field = selector.id 
                    ? document.getElementById(selector.id)
                    : document.querySelector(`[name="${selector.name}"]`);
                
                if (field) {
                    // Remove existing listener if any
                    field.removeEventListener('change', handleFormChange);
                    field.removeEventListener('input', handleFormChange);
                    
                    // Add new listeners
                    field.addEventListener('change', handleFormChange);
                    if (selector.type !== 'select') {
                        field.addEventListener('input', handleFormChange);
                    }
                }
            });
            
            // Attach listeners to passport type radios
            const passportRadios = document.querySelectorAll('input[name="passport_type"]');
            passportRadios.forEach(radio => {
                radio.removeEventListener('change', handleFormChange);
                radio.addEventListener('change', handleFormChange);
            });
        }
    </script>
@endpush