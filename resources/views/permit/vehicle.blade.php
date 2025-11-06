@extends('layouts.app')

@section('title', 'Vehicle Permit Form')

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
    /* Table Styling */
    .user-dashboard-table { 
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
            <i class="bi bi-car-front-fill me-2"></i> Sri Lanka Ports Authority - Vehicle Permit Form
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('permit.vehicle.addToSession') }}" method="POST">
            @csrf
            
            {{-- DOCUMENTS ATTACHED SECTION --}}
            <fieldset class="mb-4">
                <legend class="col-form-label pt-0"><i class="bi bi-paperclip me-1"></i> Documents Attached </legend>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="doc_revenue_licence" value="1" id="doc_revenue_licence" class="form-check-input">
                            <label class="form-check-label" for="doc_revenue_licence">Revenue Licence</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" name="doc_insurance" value="1" id="doc_insurance" class="form-check-input">
                            <label class="form-check-label" for="doc_insurance">Insurance</label>
                        </div>
                    </div>
                </div>
            </fieldset>
            {{-- END DOCUMENTS ATTACHED --}}
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="vehicle_type" class="form-label"><i class="bi bi-truck me-1"></i> Vehicle Type</label>
                    <select name="vehicle_type" id="vehicle_type" class="form-select" required>
                        <option value="">-- Select Vehicle Type --</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->name }}" {{ old('vehicle_type') == $vehicle->name ? 'selected' : '' }}>
                                {{ $vehicle->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="vehicle_number" class="form-label"><i class="bi bi-hash me-1"></i> Vehicle Number</label>
                    <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" required value="{{ old('vehicle_number') }}" oninput="this.value = this.value.toUpperCase();" onblur="fetchVehicleDetails()">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="revenue_license_number" class="form-label"><i class="bi bi-card-list me-1"></i> Revenue License Number</label>
                    <input type="text" class="form-control" name="revenue_license_number" id="revenue_license_number" required value="{{ old('revenue_license_number') }}" oninput="this.value = this.value.toUpperCase();">
                </div>
                <div class="col-md-6">
                    <label for="insurance_number" class="form-label"><i class="bi bi-shield-check me-1"></i> Insurance Number</label>
                    <input type="text" class="form-control" name="insurance_number" id="insurance_number" value="{{ old('insurance_number') }}" oninput="this.value = this.value.toUpperCase();">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="from_date" class="form-label"><i class="bi bi-calendar-date me-1"></i> From Date</label>
                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{ old('from_date') }}" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="to_date" class="form-label"><i class="bi bi-calendar-range me-1"></i> To Date</label>
                    <input type="date" class="form-control" name="to_date" id="to_date" value="{{ old('to_date') }}" min="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="owner_name" class="form-label"><i class="bi bi-person-circle me-1"></i> Owner's Name</label>
                <input type="text" class="form-control" name="owner_name" id="owner_name" required value="{{ old('owner_name') }}"
                oninput="this.value = this.value.toUpperCase();">
            </div>
            
            <div class="mb-3">
                <label for="company_name" class="form-label"><i class="bi bi-buildings me-1"></i> Company Name</label>
                <select name="company_name" id="company_name" class="form-select" required>
                    <option value="">-- Select Company --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->name }}" data-address="{{ $company->address }}"
                            {{ old('company_name', $companyName ?? '') == $company->name ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="mb-4">
                <button type="button" onclick="checkVehicleAvailability()" class="btn btn-info me-2"><i class="bi bi-check-circle-fill me-1"></i> Check Availability</button>
                <p id="availability-msg" class="fw-bold d-inline-block"></p>
            </div>
            
            <div class="mb-3">
                <label for="owner_address" class="form-label"><i class="bi bi-house me-1"></i> Owner's Address</label>
                <input type="text" class="form-control" name="owner_address" id="owner_address" required value="{{ old('owner_address') }}"
                oninput="this.value = this.value.toUpperCase();">
            </div>

            <fieldset class="mb-4">
                <legend class="col-form-label pt-0"><i class="bi bi-cash me-1"></i> Issue Type</legend><br>
                @php
                    $savedIssueType = session('vehicle_permit_cart') && count(session('vehicle_permit_cart')) > 0 
                        ? session('vehicle_permit_cart')[0]['issue_type'] 
                        : old('issue_type', 'free');
                @endphp
                @if(session('vehicle_permit_cart') && count(session('vehicle_permit_cart')) > 0)
                    <div class="alert alert-warning py-2 px-3 mb-2" style="font-size: 0.85rem;">
                        <i class="bi bi-info-circle-fill me-1"></i> Previous choice: <strong>{{ ucfirst($savedIssueType) }}</strong> (you can change if needed)
                    </div>
                @endif
                <div class="form-check form-check-inline">
                    <input type="radio" name="issue_type" id="issue_free" value="free" class="form-check-input" {{ $savedIssueType == 'free' ? 'checked' : '' }}>
                    <label class="form-check-label" for="issue_free">Free Issue</label>
                </div>
                <div class="form-check form-check-inline">
                    <input type="radio" name="issue_type" id="issue_payment" value="payment" class="form-check-input" {{ $savedIssueType == 'payment' ? 'checked' : '' }}>
                    <label class="form-check-label" for="issue_payment">On Payment</label>
                </div>
            </fieldset>

            <div class="mb-3">
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

            <div class="mb-4">
                <label for="remarks" class="form-label"><i class="bi bi-info-circle me-1"></i> Remarks (Optional)</label>
                <input type="text" class="form-control" name="remarks" id="remarks" value="{{ old('remarks') }}">
            </div>

            <button type="submit" id="addToListBtn" class="btn btn-primary" disabled style="background-color: #9e9e9e !important; border-color: #9e9e9e !important; opacity: 0.65; cursor: not-allowed;">
                <i class="bi bi-plus-circle me-1"></i> Add to List
            </button>
        </form>
    </div>

    {{-- Cart Section --}}
    @if(session('vehicle_permit_cart') && count(session('vehicle_permit_cart')) > 0)
    <div class="user-dashboard-card mt-4">
        <div class="user-dashboard-title" style="margin-bottom: 1rem; font-size: 1.5rem;">
            Current Permit Requests for Company: {{ session('company_name') }}
        </div>
        
        <div class="table-responsive">
            {{-- Note: The original code used $cart, but the session key is 'vehicle_permit_cart' --}}
            <table class="table user-dashboard-table align-middle">
                <thead>
                    <tr>
                        <th>Vehicle Type</th>
                        <th>Vehicle Number</th>
                        <th>Owner</th>
                        <th>From - To</th>
                        <th>Issue Type</th>
                        <th class="text-center">Edit</th>
                        <th class="text-center">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('vehicle_permit_cart') as $index => $entry)
                        <tr>
                            <td>{{ $entry['vehicle_type'] }}</td>
                            <td>{{ $entry['vehicle_number'] }}</td>
                            <td>{{ $entry['owner_name'] }}</td>
                            <td>{{ $entry['from_date'] }} to {{ $entry['to_date'] }}</td>
                            <td>{{ ucfirst($entry['issue_type']) }}</td>
                            <td class="text-center"><a href="{{ route('permit.vehicle.editVehicleSessionEntry', $index) }}" class="user-action-btn edit"><i class="bi bi-pencil-square"></i> Edit</a></td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('permit.vehicle.removeVehicleSessionEntry', $index) }}" style="display:inline;" class="delete-cart-form">
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

        <form action="{{ route('permit.vehicle.submitAllVehicle') }}" method="POST" class="mt-4 text-center">
            @csrf
            <button type="submit" class="btn btn-success"><i class="bi bi-send-fill me-1"></i> Submit All</button>
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
                    
                    console.log('Vehicle details auto-filled successfully');
                }
            })
            .catch(error => {
                console.error("Failed to fetch vehicle details:", error);
            });
        }

        // --- Select2 Initialization ---
        $(document).ready(function() {
            // Initialize Company Name Select2
            $('#company_name').select2({
                placeholder: "-- Select Company --",
                allowClear: true
            });

            // Set search input placeholder when company dropdown opens
            $('#company_name').on('select2:open', function () {
                setTimeout(() => {
                    const searchField = document.querySelector('.select2-search__field');
                    if (searchField) {
                        searchField.placeholder = 'Search company...';
                    }
                }, 10);
            });
            
            // Note: setCompanyAddress function was not present in original Vehicle Form script, 
            // but the function call was present in the HTML's onchange. I've left the HTML 
            // `onchange="setCompanyAddress()"` removed and rely on jQuery below for clarity.
        });


        // --- Availability Check Function ---
        window.checkVehicleAvailability = function() {
            const vehicleNumber = document.getElementById('vehicle_number').value.trim();
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

            if (!vehicleNumber || !fromDate || !toDate || !companyName) {
                msg.innerText = "Please fill in all required fields.";
                msg.style.color = 'red';
                return;
            }

            fetch("{{ route('permit.vehicle.checkVehicleAvailability') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    vehicle_number: vehicleNumber,
                    from_date: fromDate,
                    to_date: toDate,
                    company_name: companyName
                })
            })
            .then(response => response.json())
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
                        vehicle_number: vehicleNumber,
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
            .catch(() => {
                msg.innerText = "Error checking availability. Please try again.";
                msg.style.color = 'red';
            });
        }

        // Function to check if form data has changed
        function hasFormDataChanged() {
            if (!checkedFormData) return false;
            
            const currentData = {
                vehicle_number: document.getElementById('vehicle_number').value.trim(),
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
            const fields = ['vehicle_number', 'from_date', 'to_date', 'company_name'];
            
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

        // --- setMaxToDate Function (Preserved as requested) ---
        function setMaxToDate() {
            // This function is generally used for person permits, but preserved here as requested.
            const idType = document.getElementById('id_type')?.value;
            const fromDateInput = document.getElementById('from_date');
            const toDateInput = document.getElementById('to_date');

            if (!fromDateInput || !toDateInput) return;
            if (!fromDateInput.value) return;

            const fromDate = new Date(fromDateInput.value);

            let maxDays = 29; 
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

        document.addEventListener('DOMContentLoaded', function() {
            const idTypeInput = document.getElementById('id_type');
            const fromDateInput = document.getElementById('from_date');

            if (fromDateInput) {
                fromDateInput.addEventListener('change', setMaxToDate);
            }
            if (idTypeInput) {
                idTypeInput.addEventListener('change', setMaxToDate);
            }
        });

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