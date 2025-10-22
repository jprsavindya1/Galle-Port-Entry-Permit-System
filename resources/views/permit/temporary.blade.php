@extends('layouts.app')

@section('title', 'Temporary Permit Form')

@section('content')
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
        margin-bottom: 1.5rem; /* Added margin for separation */
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
    main .table {
        background: #f5faff;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    main .table thead th {
        background: #e3f2fd;
        color: #1976d2;
        font-weight: 500;
        border-bottom: 2px solid #bbdefb;
    }
    main .table tbody td {
        background: #f8fafc;
        color: #333;
        vertical-align: middle;
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

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(2.375rem + 2px);
    }
    .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
        background-color: #1976d2 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }
    .user-action-btn.edit { color: #ff9800; }
    .user-action-btn.delete { color: #f44336; }
    
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
            <i class="bi bi-person-badge-fill me-2"></i> Sri Lanka Ports Authority - Temporary Permit Form
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

        <form method="POST" action="{{ route('permit.addToSession') }}">
            @csrf
            <input type="hidden" name="type" value="temporary">
            
            <div class="row mb-3 align-items-end">
                <div class="col-md-4">
                    <label for="id_type" class="form-label"><i class="bi bi-card-heading me-1"></i> Identification Type</label>
                    <select name="id_type" id="id_type" onchange="updateIdValidation(); setMaxToDate()" class="form-select" required>
                        <option value="NIC" {{ old('id_type', $permit->id_type ?? '') == 'NIC' ? 'selected' : '' }}>NIC</option>
                        <option value="Passport" {{ old('id_type', $permit->id_type ?? '') == 'Passport' ? 'selected' : '' }}>Passport</option>
                        <option value="Driving License" {{ old('id_type', $permit->id_type ?? '') == 'Driving License' ? 'selected' : '' }}>Driving License</option>
                    </select>
                </div>

                <div class="col-md-8">
                    <label for="id_number" class="form-label"><i class="bi bi-hash me-1"></i> Identification Number</label>
                    <input type="text" name="id_number" id="id_number" 
                            value="{{ old('id_number') }}" class="form-control" required
                            oninput="this.value = this.value.toUpperCase();">
                    <span id="id_number_error" class="text-danger small"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="from_date" class="form-label"><i class="bi bi-calendar-date me-1"></i> From Date</label>
                    <input type="date" id="from_date" name="from_date" value="{{ old('from_date') }}" onchange="setMaxToDate()" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="to_date" class="form-label"><i class="bi bi-calendar-range me-1"></i> To Date</label>
                    <input type="date" id="to_date" name="to_date" value="{{ old('to_date') }}" class="form-control" required>
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
                <input type="text" name="initials" id="initials" value="{{ old('initials') }}" class="form-control" required
                oninput="this.value = this.value.toUpperCase();">
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="company_name" class="form-label"><i class="bi bi-buildings me-1"></i> Company Name</label>
                    <select name="company_name" id="company_name" class="form-select" onchange="setCompanyAddress()" required>
                        <option value="">-- Select Company --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->name }}" data-address="{{ $company->address }}"
                                {{ old('company_name', $companyName ?? '') == $company->name ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
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
                </div>
            </div>

            <div class="mb-3">
                <label for="company_address" class="form-label"><i class="bi bi-geo-alt me-1"></i> Company Address</label>
                <textarea name="company_address" id="company_address" rows="2" class="form-control" required readonly>{{ old('company_address', $companyAddress ?? '') }}</textarea>
            </div>

            <div class="mb-3">
                <label for="residence_address" class="form-label"><i class="bi bi-house me-1"></i> Residence Address</label>
                <textarea name="residence_address" id="residence_address" rows="2" class="form-control">{{ old('residence_address') }}</textarea>
            </div>

            <div class="mb-3">
                <button type="button" onclick="checkAvailability()" class="btn btn-info me-2"><i class="bi bi-check-circle-fill me-1"></i> Check Availability</button>
                <p id="availability-msg" class="fw-bold d-inline-block"></p>
            </div>
            
            {{-- PASS TYPE AND ISSUE TYPE - ALIGNMENT IMPROVEMENT APPLIED --}}
            <div class="row mb-4 justify-content-between">
                <div class="col-md-5">
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
                <div class="col-md-5">
                    <fieldset>
                        <legend class="col-form-label pt-0"><i class="bi bi-cash me-1"></i> Issue Type</legend><br>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="issue_type" id="issue_free" value="free" class="form-check-input" {{ old('issue_type', 'free') == 'free' ? 'checked' : '' }}>
                            <label class="form-check-label" for="issue_free">Free Issue</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="issue_type" id="issue_payment" value="payment" class="form-check-input" {{ old('issue_type') == 'payment' ? 'checked' : '' }}>
                            <label class="form-check-label" for="issue_payment">On Payment</label>
                        </div>
                    </fieldset>
                </div>
            </div>
            {{-- END ALIGNMENT IMPROVEMENT --}}

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
                <i class="bi bi-plus-circle me-1"></i> Add to List
            </button>
        </form>
    </div>

    @if(session('temporary_permit_cart') && count(session('temporary_permit_cart')) > 0)
    <div class="user-dashboard-card mt-4">
        <div class="user-dashboard-title" style="margin-bottom: 1rem; font-size: 1.5rem;">
            Current Permit Requests for Company: {{ session('company_name') }}
        </div>
        
        <div class="table-responsive">
            <table class="table user-dashboard-table align-middle">
                <thead>
                    <tr>
                        <th>ID Type</th>
                        <th>ID Number</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Full Name</th>
                        <th>Initials</th>
                        <th>Pass Type</th>
                        <th>Issue Type</th>
                        <th>Reason</th>
                        <th class="text-center">Edit</th>
                        <th class="text-center">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('temporary_permit_cart') as $index => $permit)
                        <tr>
                            <td>{{ $permit['id_type'] }}</td>
                            <td>{{ $permit['id_number'] }}</td>
                            <td>{{ $permit['from_date'] }}</td>
                            <td>{{ $permit['to_date'] }}</td>
                            <td>{{ $permit['full_name'] }}</td>
                            <td>{{ $permit['initials'] }}</td>
                            <td><span class="user-role-badge">{{ $permit['pass_type'] }}</span></td>
                            <td>{{ $permit['issue_type'] }}</td>
                            <td>{{ $permit['reason'] }}</td>
                            <td class="text-center"><a href="{{ route('permit.editSessionEntry', $index) }}" class="user-action-btn edit"><i class="bi bi-pencil-square"></i> Edit</a></td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('permit.removeSessionEntry', $index) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this entry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="user-action-btn delete"><i class="bi bi-trash"></i> Remove</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <form method="POST" action="{{ route('permit.submitAll') }}" class="mt-4 text-center">
            @csrf
            <button type="submit" class="btn btn-success"><i class="bi bi-send-fill me-1"></i> Submit All Permit Requests</button>
        </form>
    </div>
    @endif
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        // All existing functions are preserved.
        // The DOMContentLoaded listener for ID validation
        document.addEventListener("DOMContentLoaded", function () {
            const idType = document.getElementById("id_type");
            const idNumber = document.getElementById("id_number");
            const errorSpan = document.getElementById("id_number_error");

            // Define updateIdValidation as a global function so it can be called from onchange
            window.updateIdValidation = function() {
                validateId();
            }

            function validateId() {
                let type = idType.value;
                let value = idNumber.value.trim();
                let regex, message = "";

                if (type === "NIC") {
                    // Old (9 digits + V/X) OR New (12 digits)
                    regex = /^(?:\d{9}[VXvx]|\d{12})$/;
                    message = "Enter a valid NIC number (123456789V or 123456789123).";
                } else if (type === "Passport") {
                    // 1 or 2 letters + 6–7 digits
                    regex = /^[A-Z]{1,2}\d{6,7}$/i;
                    message = "Enter a valid Passport Number (N1234567 or PP1234567).";
                } else if (type === "Driving License") {
                    // NIC (old/new) OR 8 digits
                    regex = /^(?:\d{7,8}|[A-Z]\d{7}|\d{9}[VXvx]|\d{12})$/;
                    message = "Enter a valid Driving License Number (12345678 or A1234567).";
                }

                if (!regex.test(value) && value !== "") {
                    errorSpan.textContent = message;
                    idNumber.classList.add("is-invalid");
                    return false;
                } else {
                    errorSpan.textContent = "";
                    idNumber.classList.remove("is-invalid");
                    return true;
                }
            }

            // Run validation on typing & changing
            idNumber.addEventListener("input", validateId);
            idType.addEventListener("change", validateId);
        });

        // setMaxToDate function
        window.setMaxToDate = function() {
            const idType = document.getElementById('id_type').value;
            const fromDateInput = document.getElementById('from_date');
            const toDateInput = document.getElementById('to_date');

            const fromDate = new Date(fromDateInput.value);
            if (!fromDateInput.value) return;

            let maxDays = 29; // default max days for NIC
            if (idType === 'Passport' || idType === 'Driving License') {
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
        
        // checkAvailability function
        window.checkAvailability = function() {
            const idType = document.getElementById('id_type').value;
            const idNumber = document.getElementById('id_number').value;
            const fullName = document.getElementById('full_name').value;
            const initials = document.getElementById('initials').value;
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            const companyName = document.getElementById('company_name').value;

            const msg = document.getElementById('availability-msg');
            const addBtn = document.getElementById('addToListBtn');
            
            msg.innerText = '';
            // Disable button while checking
            addBtn.disabled = true;
            addBtn.style.opacity = '0.6';
            addBtn.style.cursor = 'not-allowed';

            if (!idType || !idNumber || !fullName || !initials || !fromDate || !toDate || !companyName) {
                msg.innerText = "Please fill in all required fields.";
                msg.style.color = 'red';
                return;
            }

            fetch("{{ route('permit.checkAvailability') }}", {
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
                } else {
                    // Keep it grey when not available
                    addBtn.style.backgroundColor = '#9e9e9e';
                    addBtn.style.borderColor = '#9e9e9e';
                }
            })
            .catch(error => {
                console.error("Availability check failed:", error);
                msg.innerText = "Something went wrong during availability check.";
                msg.style.color = 'red';
            });
        }

        // setCompanyAddress function
        window.setCompanyAddress = function() {
            const select = document.getElementById('company_name');
            const selectedOption = select.options[select.selectedIndex];
            // Check if the option has a data-address attribute
            const address = selectedOption ? selectedOption.getAttribute('data-address') : null;
            document.getElementById('company_address').value = address || '';
        }

        // jQuery Select2 initialization and related logic
        $(document).ready(function() {
            // Initialize Company Name Select2
            $('#company_name').select2({
                placeholder: "-- Select Company --",
                allowClear: true
            });

            // Set search input placeholder when company dropdown opens
            $('#company_name').on('select2:open', function () {
                setTimeout(() => {
                    // Check if the search field exists before setting placeholder
                    const searchField = document.querySelector('.select2-search__field');
                    if (searchField) {
                        searchField.placeholder = 'Search company...';
                    }
                }, 10);
            });

            // Sync company address on change 
            $('#company_name').on('change', function() {
                // Call the pure JavaScript function to handle the address logic
                setCompanyAddress();
                // Also call setMaxToDate in case ID type changes affect date limits
                setMaxToDate();
            });

            // Initialize Designation Select2
            $('#designation').select2({
                placeholder: "-- Select Designation --",
                allowClear: true
            });
            
            // Set search input placeholder when designation dropdown opens
            $('#designation').on('select2:open', function () {
                setTimeout(() => {
                    const searchField = document.querySelector('.select2-search__field');
                    if (searchField) {
                        searchField.placeholder = 'Search Designation...';
                    }
                }, 10);
            });
            
            // Initial call to set max date on load if from_date is set
            setMaxToDate();
        });
    </script>
@endpush