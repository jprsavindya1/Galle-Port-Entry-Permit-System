@extends('layouts.app')

@section('title', 'Monthly Permit Form')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
    main .btn-success {
        background-color: #4caf50;
        border-color: #4caf50;
        border-radius: 0.5rem;
        font-weight: 500;
    }
    main .btn-secondary {
        background-color: #90a4ae;
        border-color: #90a4ae;
        color: #fff;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: background-color 0.2s, box-shadow 0.2s;
    }
    main .btn-secondary:hover {
        background-color: #78909c;
        border-color: #78909c;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    .user-dashboard-table { /* Styling for the list table */
        background: #f5faff;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-top: 1rem;
    }
    .user-dashboard-table thead th {
        background: #e3f2fd;
        color: #1976d2;
        font-weight: 500;
        border-bottom: 2px solid #bbdefb;
    }
    .user-dashboard-table tbody td {
        background: #f8fafc;
        color: #333;
        vertical-align: middle;
    }
    .user-action-btn {
        background: none;
        border: none;
        font-size: 0.9rem;
        cursor: pointer;
    }
    .user-action-btn.edit { color: #ff9800; }
    .user-action-btn.delete { color: #f44336; }
    
    /* Hide dropdown arrow for disabled select elements */
    select:disabled {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        background-image: none !important;
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
    /* Fieldset Legend Styling */
    legend {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1976d2;
        border-bottom: 1px solid #bbdefb;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        width: auto;
    }

</style>

<div class="container py-4">
    <div class="user-dashboard-card">
        <div class="user-dashboard-title">
            <i class="bi bi-calendar-check me-2"></i> Sri Lanka Ports Authority - Monthly Permit Form
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('permit.monthly.addMonthlyToSession') }}">
            @csrf

            {{-- DOCUMENTS ATTACHED SECTION --}}
            <fieldset class="mb-4">
                <legend class="col-form-label pt-0"><i class="bi bi-paperclip me-1"></i> Documents Attached </legend>
                <div class="row gx-2">
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" name="doc_nic" value="1" id="doc_nic" class="form-check-input" {{ old('doc_nic') ? 'checked' : '' }}>
                            <label class="form-check-label" for="doc_nic">NIC</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" name="doc_police_report" value="1" id="doc_police_report" class="form-check-input" {{ old('doc_police_report') ? 'checked' : '' }}>
                            <label class="form-check-label" for="doc_police_report">Police Report</label>
                        </div>
                    </div>
                </div>
            </fieldset>
            {{-- END DOCUMENTS ATTACHED --}}

            <div class="row mb-3 align-items-start">
                <div class="col-md-3">
                    <label for="id_type" class="form-label"><i class="bi bi-card-heading me-1"></i> ID Type</label>
                    {{-- ID Type is fixed to NIC for monthly permits, making it readonly --}}
                    <input type="text" class="form-control" name="id_type" id="id_type" value="NIC" readonly style="background-color: #f8fafc; color: #1976d2; font-weight: 500;">
                </div>
                <div class="col-md-9">
                    <label for="id_number" class="form-label"><i class="bi bi-hash me-1"></i> ID Number</label>
                    <input type="text" class="form-control" name="id_number" id="id_number" value="{{ old('id_number') }}" required
                        oninput="this.value = this.value.toUpperCase(); handleIdNumberChange(); checkDuplicateInCart();"
                        onblur="fetchPersonDetails();">
                    <div style="min-height: 20px;">
                        <span id="id_number_error" class="text-danger small d-block"></span>
                        <span id="duplicate_error" class="text-danger small d-block"></span>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="from_date" class="form-label"><i class="bi bi-calendar-date me-1"></i> From Date</label>
                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{ old('from_date') }}" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="to_date" class="form-label"><i class="bi bi-calendar-range me-1"></i> To Date</label>
                    {{-- To Date will be auto-calculated to 30 days from from_date, so it is made readonly --}}
                    <input type="date" class="form-control" name="to_date" id="to_date" value="{{ old('to_date') }}" min="{{ date('Y-m-d') }}" required readonly>
                </div>
            </div>

            <div class="mb-3">
                <label for="full_name" class="form-label"><i class="bi bi-person-vcard me-1"></i> Full Name</label>
                <input type="text" name="full_name" id="full_name" 
                        value="{{ old('full_name') }}" 
                        class="form-control" 
                        required
                        oninput="this.value = this.value.toUpperCase();">
            </div>

            <div class="mb-3">
                <label for="initials" class="form-label"><i class="bi bi-person-badge me-1"></i> Name with Initials</label>
                <input type="text" class="form-control" name="initials" id="initials" value="{{ old('initials') }}" required
                oninput="this.value = this.value.toUpperCase();">
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="company_name" class="form-label"><i class="bi bi-buildings me-1"></i> Company Name</label>
                    <select name="company_name" id="company_name" class="form-select" onchange="setCompanyAddress()" required
                        @if(session('monthly_permit_cart') && count(session('monthly_permit_cart')) > 0) disabled style="background-color: #e9ecef; cursor: not-allowed;" @endif>
                        <option value="">-- Select Company --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->name }}" data-address="{{ $company->address }}"
                                {{ old('company_name', $companyName ?? '') == $company->name ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @if(session('monthly_permit_cart') && count(session('monthly_permit_cart')) > 0)
                        <small class="text-muted d-block mt-1"><i class="bi bi-info-circle me-1"></i>Company is locked for this session</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="designation" class="form-label"><i class="bi bi-briefcase me-1"></i> Designation</label>
                    <select name="designation" id="designation" class="form-select" required>
                        <option value="">-- Select Designation --</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation->name }}" {{ old('designation') == $designation->name ? 'selected' : '' }}>
                                {{ $designation->name }}
                            </option>
                        @endforeach
                    </select>
                    <div style="min-height: 20px;">
                        <span id="designation_error" class="text-danger small d-block"></span>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label for="company_address" class="form-label"><i class="bi bi-geo-alt me-1"></i> Company Address</label>
                <textarea name="company_address" id="company_address" rows="2" class="form-control" required readonly>{{ old('company_address', $companyAddress ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="residence_address" class="form-label"><i class="bi bi-house me-1"></i> Residence Address</label>
                <textarea class="form-control" name="residence_address" rows="2">{{ old('residence_address') }}</textarea>
            </div>

           
            <div class="mb-4">
                <button type="button" onclick="checkMonthlyAvailability()" class="btn btn-info me-2"><i class="bi bi-check-circle-fill me-1"></i> Check Availability</button>
                <button type="button" onclick="clearForm()" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> Clear Form</button>
                <p id="availability-msg" class="fw-bold d-block mt-2" style="font-size: 0.95rem; line-height: 1.5;"></p>
            </div>
            {{-- Police Report Dates - Required for Monthly Permits --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="police_issue_date" class="form-label"><i class="bi bi-calendar-check me-1"></i> Police Report Issue Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="police_issue_date" name="police_issue_date" value="{{ old('police_issue_date') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="police_expire_date" class="form-label"><i class="bi bi-calendar-x me-1"></i> Police Report Expire Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="police_expire_date" name="police_expire_date" value="{{ old('police_expire_date') }}" required>
                            </div>
                        </div>

            {{-- PASS TYPE AND ISSUE TYPE - Grouped and Styled --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <fieldset>
                        <legend class="col-form-label pt-0"><i class="bi bi-pass me-1"></i> Pass Type</legend><br>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="pass_type[]" value="onboard" id="pass_onboard" class="form-check-input" 
                                {{ is_array(old('pass_type')) && in_array('onboard', old('pass_type')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="pass_onboard">Onboard</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="pass_type[]" value="afloat" id="pass_afloat" class="form-check-input"
                                {{ is_array(old('pass_type')) && in_array('afloat', old('pass_type')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="pass_afloat">Afloat</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="pass_type[]" value="ashore" id="pass_ashore" class="form-check-input"
                                {{ is_array(old('pass_type')) && in_array('ashore', old('pass_type')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="pass_ashore">Ashore</label>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset>
                        <legend class="col-form-label pt-0"><i class="bi bi-cash me-1"></i> Issue Type</legend><br>
                        @php
                            $savedIssueType = session('monthly_permit_cart') && count(session('monthly_permit_cart')) > 0 
                                ? session('monthly_permit_cart')[0]['issue_type'] 
                                : old('issue_type', 'free');
                        @endphp
                        <div class="form-check form-check-inline">
                            <input type="radio" name="issue_type" id="issue_free" value="free" class="form-check-input" {{ $savedIssueType == 'free' ? 'checked' : '' }}>
                            <label class="form-check-label" for="issue_free">Free Issue</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="issue_type" id="issue_payment" value="payment" class="form-check-input" {{ $savedIssueType == 'payment' ? 'checked' : '' }}>
                            <label class="form-check-label" for="issue_payment">On Payment</label>
                        </div>
                    </fieldset>
                </div>
            </div>
            {{-- END PASS/ISSUE TYPE --}}

            <div class="mb-4">
                <label for="reason" class="form-label"><i class="bi bi-file-earmark-text me-1"></i> Reason for Visit</label>
                <select name="reason" id="reason" class="form-select" required>
                    <option value="">-- Select --</option>
                    @foreach($reasons as $reason)
                        <option value="{{ $reason->name }}" {{ old('reason') == $reason->name ? 'selected' : '' }}>
                            {{ ucfirst($reason->name) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" id="addToListBtn" class="btn btn-primary" disabled style="background-color: #9e9e9e !important; border-color: #9e9e9e !important; opacity: 0.65; cursor: not-allowed;">
                <i class="bi bi-plus-circle me-1"></i> Add to Monthly Permit List
            </button>
        </form>
    </div>

    @if(session('monthly_permit_cart') && count(session('monthly_permit_cart')) > 0)
    <div class="user-dashboard-card mt-4">
        <div class="user-dashboard-title" style="margin-bottom: 1rem; font-size: 1.5rem;">
            Current Monthly Permit Requests for Company: {{ session('company_name') }}
        </div>
        
        <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID Type</th>
                        <th>ID Number</th>
                        <th>Full Name</th>
                        <th>Initials</th>
                        <th>Pass Type</th>
                        <th>Issue Type</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Reason</th>
                        <th class="text-center">Edit</th>
                        <th class="text-center">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('monthly_permit_cart') as $index => $permit)
                        <tr>
                            <td>{{ $permit['id_type'] }}</td>
                            <td>{{ $permit['id_number'] }}</td>
                            <td>{{ $permit['full_name'] }}</td>
                            <td>{{ $permit['initials'] }}</td>
                            <td>{{ $permit['pass_type'] }}</td>
                            <td>{{ ucfirst($permit['issue_type']) }}</td>
                            <td>{{ \Carbon\Carbon::parse($permit['from_date'])->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($permit['to_date'])->format('d M Y') }}</td>
                            <td>{{ $permit['reason'] }}</td>
                            <td>
                                <a href="{{ route('permit.monthly.editMonthlySessionEntry', $index) }}" class="user-action-btn edit"><i class="bi bi-pencil-square"></i> Edit</a>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('permit.monthly.removeMonthlySessionEntry', $index) }}" style="display:inline;" class="delete-cart-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="user-action-btn delete delete-cart-btn"><i class="bi bi-trash"></i> Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <form method="POST" action="{{ route('permit.monthly.submitAll') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="btn btn-success btn-lg" style="font-size: 1.25rem; padding: 0.75rem 2rem;"><i class="bi bi-send-fill me-2"></i> Submit</button>
        </form>
    </div>
    @endif
</div>

@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Store checked form data to detect changes
        let checkedFormData = null;
        // Store ID validation state globally
        let isIdValid = false;
        // Store the last fetched ID number to track changes
        let lastFetchedIdNumber = '';

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

        // Function to check for duplicate NIC in session cart
        window.checkDuplicateInCart = function() {
            const idNumber = document.getElementById('id_number').value.trim();
            const fullName = document.getElementById('full_name').value.trim();
            const duplicateError = document.getElementById('duplicate_error');
            const addBtn = document.getElementById('addToListBtn');
            
            if (!idNumber && !fullName) {
                duplicateError.textContent = '';
                return;
            }

            // Get current cart from the page - use table.table selector since there's no specific class
            const cartRows = document.querySelectorAll('.table-responsive table tbody tr');
            let isDuplicate = false;

            cartRows.forEach(row => {
                const cartIdNumber = row.cells[1]?.textContent.trim().toUpperCase();
                const cartFullName = row.cells[2]?.textContent.trim().toUpperCase();
                if ((idNumber && cartIdNumber === idNumber.toUpperCase()) || (fullName && cartFullName === fullName.toUpperCase())) {
                    isDuplicate = true;
                }
            });

            if (isDuplicate) {
                duplicateError.textContent = '⚠️ This NIC or name is already in the cart. One person can only have one permit per submission.';
                duplicateError.style.display = 'block';
                duplicateError.style.color = '#dc3545';
                duplicateError.style.fontWeight = '500';
                // Disable the add button
                if (addBtn) {
                    addBtn.disabled = true;
                    addBtn.style.backgroundColor = '#9e9e9e';
                    addBtn.style.borderColor = '#9e9e9e';
                    addBtn.style.opacity = '0.65';
                    addBtn.style.cursor = 'not-allowed';
                }
            } else {
                duplicateError.textContent = '';
                duplicateError.style.display = 'none';
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

        // --- Document Ready and Select2 Initialization ---
        $(document).ready(function() {
            // Initialize Company Name Select2
            $('#company_name').select2({
                placeholder: "-- Select Company --",
                allowClear: true
            });

            $('#company_name').on('select2:open', function () {
                setTimeout(() => {
                    const searchField = document.querySelector('.select2-search__field');
                    if (searchField) {
                        searchField.placeholder = 'Search company...';
                    }
                }, 10);
            });

            // Initialize Designation Select2
            $('#designation').select2({
                placeholder: "-- Select Designation --",
                allowClear: true
            });
            
            $('#designation').on('select2:open', function () {
                setTimeout(() => {
                    const searchField = document.querySelector('.select2-search__field');
                    if (searchField) {
                        searchField.placeholder = 'Search Designation...';
                    }
                }, 10);
            });
            
            // Clear designation error when a value is selected
            $('#designation').on('change', function() {
                const designationError = document.getElementById('designation_error');
                if (this.value && designationError) {
                    designationError.textContent = '';
                    designationError.style.display = 'none';
                }
            });
            
            // Sync company address on change 
            $('#company_name').on('change', function() {
                setCompanyAddress();
            });

            // Initial auto-fill for to_date if from_date is pre-filled (e.g., from old data)
            const fromInput = document.getElementById('from_date');
            if (fromInput.value) {
                 autoFillToDate();
            }
        });

        // --- ID Validation Function ---
        document.addEventListener("DOMContentLoaded", function () {
            const idType = document.getElementById("id_type");
            const idNumber = document.getElementById("id_number");
            const errorSpan = document.getElementById("id_number_error");

            function validateId() {
                let type = idType.value;
                let value = idNumber.value.trim();
                let regex, message = "";

                if (type === "NIC") {
                    // Old (9 digits + V/X) for cards before 2016 OR New (12 digits) for cards after 2016
                    regex = /^(?:\d{9}[VXvx]|\d{12})$/;
                    message = "Enter a valid NIC number (9 digits + V for old format or 12 digits for new format).";
                } 
                // Note: For Monthly Permit, ID type is locked to NIC, so other validations are generally not needed.

                if (!regex.test(value) && value !== "") {
                    errorSpan.textContent = message;
                    idNumber.classList.add("is-invalid");
                    isIdValid = false;
                    return false;
                } else {
                    errorSpan.textContent = "";
                    idNumber.classList.remove("is-invalid");
                    isIdValid = (value !== "");
                    return true;
                }
            }

            // Run validation on typing
            idNumber.addEventListener("input", validateId);
            
            // Run validation on page load if ID number is pre-filled (from old() values)
            if (idNumber.value.trim() !== "") {
                validateId();
            }
        });

        // --- Form Submission Validation ---
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('permit.monthly.addMonthlyToSession') }}"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    // Validate ID number before form submission
                    if (!isIdValid) {
                        e.preventDefault();
                        alert('Please enter a valid NIC number before submitting.');
                        document.getElementById('id_number').focus();
                        return false;
                    }
                    
                    // Validate designation before form submission
                    const designation = document.getElementById('designation').value;
                    if (!designation) {
                        e.preventDefault();
                        const designationError = document.getElementById('designation_error');
                        designationError.textContent = '⚠️ Please select a designation';
                        designationError.style.display = 'block';
                        designationError.style.color = '#dc3545';
                        designationError.style.fontWeight = '500';
                        alert('Please select a designation before submitting.');
                        document.getElementById('designation').focus();
                        return false;
                    }
                    
                    // Enable company dropdown if disabled (for cart session)
                    const companyDropdown = document.getElementById('company_name');
                    if (companyDropdown && companyDropdown.disabled) {
                        companyDropdown.disabled = false;
                    }
                });
            }
        });

        // --- Date Auto-Fill Logic (Monthly is 30 days) ---
        function autoFillToDate() {
            const fromInput = document.getElementById('from_date');
            const toInput = document.getElementById('to_date');

            if (!fromInput.value) {
                toInput.value = '';
                return;
            }

            let fromDate = new Date(fromInput.value);
            let toDate = new Date(fromDate);

            // Add 29 days (making it a total of 30 days including the start day)
            toDate.setDate(fromDate.getDate() + 29);

            // Format as YYYY-MM-DD
            let month = (toDate.getMonth() + 1).toString().padStart(2, '0');
            let day = toDate.getDate().toString().padStart(2, '0');
            let year = toDate.getFullYear();

            toInput.value = `${year}-${month}-${day}`;
        }
        
        // Listen for changes on from_date to auto-fill to_date
        document.getElementById('from_date').addEventListener('change', autoFillToDate);


        // --- Company Address Setter ---
        window.setCompanyAddress = function() {
            const select = document.getElementById('company_name');
            const selectedOption = select.options[select.selectedIndex];
            const address = selectedOption ? selectedOption.getAttribute('data-address') : null;
            document.getElementById('company_address').value = address || '';
        }

        // --- Availability Check ---
        window.checkMonthlyAvailability = function() {
            const idType = document.getElementById('id_type').value;
            const idNumber = document.getElementById('id_number').value;
            const fullName = document.getElementById('full_name').value;
            const initials = document.getElementById('initials').value;
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            const companyName = document.getElementById('company_name').value;
            const designation = document.getElementById('designation').value;

            const msg = document.getElementById('availability-msg');
            const addBtn = document.getElementById('addToListBtn');
            
            msg.innerText = '';
            // Clear designation error
            document.getElementById('designation_error').textContent = '';
            
            // Disable button while checking
            addBtn.disabled = true;
            addBtn.style.opacity = '0.6';
            addBtn.style.cursor = 'not-allowed';

            // Check for duplicate error first
            const duplicateError = document.getElementById('duplicate_error');
            if (duplicateError && duplicateError.textContent.trim() !== '') {
                msg.innerText = 'Cannot check availability: This NIC or name is already in the cart.';
                msg.style.color = 'red';
                return;
            }

            // Check for empty fields and build detailed message
            const missingFields = [];
            if (!idType) missingFields.push('ID Type');
            if (!idNumber) missingFields.push('ID Number');
            if (!fullName) missingFields.push('Full Name');
            if (!initials) missingFields.push('Name with Initials');
            if (!fromDate) missingFields.push('From Date');
            if (!toDate) missingFields.push('To Date');
            if (!companyName) missingFields.push('Company Name');
            if (!designation) {
                missingFields.push('Designation');
                document.getElementById('designation_error').textContent = '⚠️ Please select a designation';
                document.getElementById('designation_error').style.display = 'block';
                document.getElementById('designation_error').style.color = '#dc3545';
                document.getElementById('designation_error').style.fontWeight = '500';
            }

            if (missingFields.length > 0) {
                msg.innerText = "Please fill in: " + missingFields.join(', ');
                msg.style.color = 'red';
                return;
            }

            // Check document checkboxes - both NIC and Police Report must be checked
            const docNic = document.getElementById('doc_nic').checked;
            const docPoliceReport = document.getElementById('doc_police_report').checked;

            if (!docNic || !docPoliceReport) {
                msg.innerText = "Please check both required documents: NIC and Police Report";
                msg.style.color = 'red';
                return;
            }

            // Validate ID number before checking availability
            if (!isIdValid) {
                msg.innerText = "Please enter a valid NIC number before checking availability";
                msg.style.color = 'red';
                return;
            }

            fetch("{{ route('permit.monthly.checkMonthlyAvailability') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    id_type: idType,
                    id_number: idNumber,
                    full_name: fullName,
                    initials: initials,
                    from_date: fromDate,
                    to_date: toDate,
                    company_name: companyName
                })
            })
            .then(res => res.json())
            .then(data => {
                msg.innerText = data.message;
                msg.style.color = data.available ? 'green' : 'red';
                
                // Enable button only if available
                if (data.available) {
                    addBtn.disabled = false;
                    addBtn.style.backgroundColor = '';
                    addBtn.style.borderColor = '';
                    addBtn.style.opacity = '1';
                    addBtn.style.cursor = 'pointer';
                    
                    // Store the checked form data
                    checkedFormData = {
                        id_type: idType,
                        id_number: idNumber,
                        full_name: fullName,
                        initials: initials,
                        from_date: fromDate,
                        to_date: toDate,
                        company_name: companyName
                    };
                    
                    // Attach change listeners to form fields
                    attachChangeListeners();
                } else {
                    // Keep it grey when not available
                    addBtn.style.backgroundColor = '#9e9e9e';
                    addBtn.style.borderColor = '#9e9e9e';
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
                to_date: document.getElementById('to_date').value,
                company_name: document.getElementById('company_name').value
            };
            
            return Object.keys(checkedFormData).some(key => checkedFormData[key] !== currentData[key]);
        }

        // Function to disable button when form data changes
        function handleFormChange() {
            if (hasFormDataChanged()) {
                const addBtn = document.getElementById('addToListBtn');
                const msg = document.getElementById('availability-msg');
                
                addBtn.disabled = true;
                addBtn.style.backgroundColor = '#9e9e9e';
                addBtn.style.borderColor = '#9e9e9e';
                addBtn.style.opacity = '0.65';
                addBtn.style.cursor = 'not-allowed';
                
                msg.innerText = 'Form data changed. Please check availability again.';
                msg.style.color = 'orange';
                
                checkedFormData = null;
            }
        }

        // Function to attach change listeners to form fields
        function attachChangeListeners() {
            const fields = ['id_type', 'id_number', 'full_name', 'initials', 'from_date', 'to_date', 'company_name'];
            
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

        // Clear Form function - preserves company data and cart state
        window.clearForm = function() {
            try {
                // Check if there are any entries in the cart
                const cartRows = document.querySelectorAll('.table-responsive table tbody tr');
                const hasCartEntries = cartRows.length > 0;
                
                // Store company-related data before clearing (only if cart has entries)
                let companyName = '';
                let companyAddress = '';
                let designation = '';
                
                if (hasCartEntries) {
                    const companyField = document.getElementById('company_name');
                    const addressField = document.getElementById('company_address');
                    const designationField = document.getElementById('designation');
                    if (companyField) companyName = companyField.value;
                    if (addressField) companyAddress = addressField.value;
                    if (designationField) designation = designationField.value;
                }
                
                // Clear all form fields with null checks
                const idNumber = document.getElementById('id_number');
                const fullName = document.getElementById('full_name');
                const initials = document.getElementById('initials');
                const fromDate = document.getElementById('from_date');
                const toDate = document.getElementById('to_date');
                const residenceAddress = document.getElementById('residence_address');
                const reason = document.getElementById('reason');
                const policeReportFrom = document.getElementById('police_report_from_date');
                const policeReportTo = document.getElementById('police_report_to_date');
                
                if (idNumber) idNumber.value = '';
                if (fullName) fullName.value = '';
                if (initials) initials.value = '';
                if (fromDate) fromDate.value = '';
                if (toDate) toDate.value = '';
                if (residenceAddress) residenceAddress.value = '';
                if (reason) reason.value = '';
                if (policeReportFrom) policeReportFrom.value = '';
                if (policeReportTo) policeReportTo.value = '';
                
                // Clear company fields if no cart entries
                if (!hasCartEntries) {
                    const companyField = document.getElementById('company_name');
                    const addressField = document.getElementById('company_address');
                    const designationField = document.getElementById('designation');
                    
                    if (companyField) {
                        companyField.value = '';
                        $('#company_name').val(null).trigger('change');
                    }
                    if (addressField) addressField.value = '';
                    if (designationField) {
                        designationField.value = '';
                        $('#designation').val(null).trigger('change');
                    }
                }
                
                // Clear error messages
                const idNumberError = document.getElementById('id_number_error');
                const duplicateError = document.getElementById('duplicate_error');
                if (idNumberError) idNumberError.textContent = '';
                if (duplicateError) duplicateError.textContent = '';
                
                // Reset document checkboxes - IMPORTANT: Clear these explicitly
                const docNic = document.getElementById('doc_nic');
                const docPoliceReport = document.getElementById('doc_police_report');
                
                console.log('Clearing checkboxes:', docNic, docPoliceReport); // Debug
                
                if (docNic) {
                    docNic.checked = false;
                    docNic.removeAttribute('checked');
                    console.log('NIC checkbox cleared'); // Debug
                }
                if (docPoliceReport) {
                    docPoliceReport.checked = false;
                    docPoliceReport.removeAttribute('checked');
                    console.log('Police Report checkbox cleared'); // Debug
                }
                
                // ID type is always NIC for monthly, but reset it
                const idType = document.getElementById('id_type');
                if (idType) idType.value = 'NIC';
                
                // Reset pass type and issue type radio buttons
                const passTypeRadios = document.querySelectorAll('input[name="pass_type"]');
                passTypeRadios.forEach(radio => {
                    radio.checked = false;
                    radio.removeAttribute('checked');
                });
                
                const issueTypeRadios = document.querySelectorAll('input[name="issue_type"]');
                issueTypeRadios.forEach(radio => {
                    radio.checked = false;
                    radio.removeAttribute('checked');
                });
                
                // Restore company-related data only if cart has entries
                if (hasCartEntries) {
                    const companyField = document.getElementById('company_name');
                    const addressField = document.getElementById('company_address');
                    const designationField = document.getElementById('designation');
                    
                    if (companyField) {
                        companyField.value = companyName;
                        $('#company_name').trigger('change.select2');
                    }
                    if (addressField) addressField.value = companyAddress;
                    if (designationField) {
                        designationField.value = designation;
                        $('#designation').trigger('change.select2');
                    }
                }
                
                // Reset validation states
                isIdValid = false;
                checkedFormData = null;
                
                // Disable the "Add to List" button
                const addBtn = document.getElementById('addToListBtn');
                if (addBtn) {
                    addBtn.disabled = true;
                    addBtn.style.backgroundColor = '#9e9e9e';
                    addBtn.style.borderColor = '#9e9e9e';
                    addBtn.style.opacity = '0.65';
                    addBtn.style.cursor = 'not-allowed';
                }
                
                // Show success message
                const msg = document.getElementById('availability-msg');
                if (msg) {
                    msg.innerText = 'Form cleared successfully.';
                    msg.style.color = '#2196F3';
                    
                    // Clear the message after 3 seconds
                    setTimeout(() => {
                        msg.innerText = '';
                    }, 3000);
                }
            } catch (error) {
                console.error('Error in clearForm:', error);
            }
        }

        // SweetAlert2 for cart delete confirmation
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-cart-form');
            
            deleteForms.forEach(form => {
                const deleteBtn = form.querySelector('.delete-cart-btn');
                if (deleteBtn) {
                    deleteBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        Swal.fire({
                            title: 'Remove Entry?',
                            text: 'Are you sure you want to remove this entry?',
                            icon: 'warning',
                            iconColor: '#e53935',
                            showCancelButton: true,
                            confirmButtonColor: '#e53935',
                            cancelButtonColor: '#757575',
                            confirmButtonText: 'Yes, Remove',
                            cancelButtonText: 'Cancel',
                            customClass: {
                                popup: 'delete-popup',
                                title: 'delete-title',
                                confirmButton: 'delete-confirm-btn',
                                cancelButton: 'delete-cancel-btn'
                            },
                            buttonsStyling: true,
                            width: '400px'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                }
            });
        });
    </script>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        /* Custom SweetAlert2 styling for delete action */
        .delete-popup {
            border-radius: 0.75rem !important;
            padding: 1.5rem !important;
        }
        
        .delete-title {
            color: #e53935 !important;
            font-size: 1.25rem !important;
            font-weight: 600 !important;
        }
        
        .swal2-html-container {
            font-size: 0.95rem !important;
            color: #555 !important;
        }
        
        .delete-confirm-btn {
            border-radius: 0.375rem !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
        }
        
        .delete-cancel-btn {
            border-radius: 0.375rem !important;
            padding: 0.5rem 1rem !important;
            font-size: 0.9rem !important;
            font-weight: 500 !important;
        }
        
        .swal2-icon.swal2-warning {
            border-color: #e53935 !important;
            color: #e53935 !important;
        }
    </style>
@endpush