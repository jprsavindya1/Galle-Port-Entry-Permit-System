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
                <div class="row gx-2">
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" name="doc_revenue_licence" value="1" id="doc_revenue_licence" class="form-check-input" {{ old('doc_revenue_licence') ? 'checked' : '' }}>
                            <label class="form-check-label" for="doc_revenue_licence">Revenue Licence</label>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-check">
                            <input type="checkbox" name="doc_insurance" value="1" id="doc_insurance" class="form-check-input" {{ old('doc_insurance') ? 'checked' : '' }}>
                            <label class="form-check-label" for="doc_insurance">Insurance</label>
                        </div>
                    </div>
                </div>
            </fieldset>
            {{-- END DOCUMENTS ATTACHED --}}
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="vehicle_type" class="form-label"><i class="bi bi-truck me-1"></i> Vehicle Type</label>
                    <select name="vehicle_type" id="vehicle_type" class="form-select" required onchange="handleVehicleTypeChange()">
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
                    <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" required value="{{ old('vehicle_number') }}" oninput="this.value = this.value.toUpperCase(); handleVehicleNumberChange(); checkDuplicateInCart();" onblur="fetchVehicleDetails();">
                    <span id="duplicate_error" class="text-danger small d-block"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="revenue_license_number" class="form-label"><i class="bi bi-card-list me-1"></i> Revenue License Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="revenue_license_number" id="revenue_license_number" required value="{{ old('revenue_license_number') }}" oninput="this.value = this.value.toUpperCase();">
                </div>
                <div class="col-md-6">
                    <label for="nic_number" class="form-label"><i class="bi bi-card-text me-1"></i> NIC Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nic_number" id="nic_number" required value="{{ old('nic_number') }}" oninput="this.value = this.value.toUpperCase(); validateNicNumber(); checkDuplicateInCart();" placeholder="">
                    <span id="nic_number_error" class="text-danger small"></span>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="insurance_number" class="form-label"><i class="bi bi-shield-check me-1"></i> Insurance Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="insurance_number" id="insurance_number" required value="{{ old('insurance_number') }}" oninput="this.value = this.value.toUpperCase();">
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
                <select name="company_name" id="company_name" class="form-select" required
                    @if(session('vehicle_permit_cart') && count(session('vehicle_permit_cart')) > 0) disabled style="background-color: #e9ecef; cursor: not-allowed;" @endif>
                    <option value="">-- Select Company --</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->name }}" data-address="{{ $company->address }}"
                            {{ old('company_name', $companyName ?? '') == $company->name ? 'selected' : '' }}>
                            {{ $company->name }}
                        </option>
                    @endforeach
                </select>
                @if(session('vehicle_permit_cart') && count(session('vehicle_permit_cart')) > 0)
                    <small class="text-muted d-block mt-1"><i class="bi bi-info-circle me-1"></i>Company is locked for this session</small>
                @endif
            </div>
            
            <div class="mb-4">
                <button type="button" onclick="checkVehicleAvailability()" class="btn btn-info me-2"><i class="bi bi-check-circle-fill me-1"></i> Check Availability</button>
                <button type="button" onclick="clearForm()" class="btn btn-secondary"><i class="bi bi-arrow-counterclockwise me-1"></i> Clear Form</button>
                <p id="availability-msg" class="fw-bold d-block mt-2" style="font-size: 0.95rem; line-height: 1.5;"></p>
            </div>
            
            <div class="mb-3">
                <label for="owner_address" class="form-label"><i class="bi bi-house me-1"></i> Owner's Address </label>
                <input type="text" class="form-control" name="owner_address" id="owner_address" value="{{ old('owner_address') }}"
                oninput="this.value = this.value.toUpperCase();">
            </div>

            <fieldset class="mb-4">
                <legend class="col-form-label pt-0"><i class="bi bi-cash me-1"></i> Issue Type</legend><br>
                @php
                    $savedIssueType = session('vehicle_permit_cart') && count(session('vehicle_permit_cart')) > 0 
                        ? session('vehicle_permit_cart')[0]['issue_type'] 
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
                        <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Vehicle Type</th>
                        <th>Vehicle Number</th>
                        <th>Owner Name</th>
                        <th>Owner Address</th>
                        <th>Reason</th>
                        <th>Revenue License</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th class="text-center">Edit</th>
                        <th class="text-center">Remove</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(session('vehicle_permit_cart') as $index => $permit)
                        <tr>
                            <td>{{ $permit['vehicle_type'] }}</td>
                            <td>{{ $permit['vehicle_number'] }}</td>
                            <td>{{ $permit['owner_name'] }}</td>
                            <td>{{ $permit['owner_address'] }}</td>
                            <td>{{ $permit['reason'] }}</td>
                            <td>{{ $permit['revenue_license_number'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($permit['from_date'])->format('d M Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($permit['to_date'])->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('permit.vehicle.editVehicleSessionEntry', $index) }}" class="user-action-btn edit"><i class="bi bi-pencil-square"></i> Edit</a>
                            </td>
                            <td>
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
            <button type="submit" class="btn btn-success btn-lg" style="font-size: 1.25rem; padding: 0.75rem 2rem;"><i class="bi bi-send-fill me-2"></i> Submit</button>
        </form>
    </div>
    @endif
</div>
@endsection

@push('scripts')
    <script>
        // Store cart data for duplicate checking
        window.cartData = @json(session('vehicle_permit_cart', []));
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Store NIC validation state globally
        let isNicValid = false; // Required field so default to false
        
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

        // Function to validate NIC Number field
        window.validateNicNumber = function() {
            const nicInput = document.getElementById('nic_number');
            const nicError = document.getElementById('nic_number_error');
            const value = nicInput.value.trim();
            
            // If empty and required, it's invalid
            if (value === '') {
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
                return false;
            } else {
                nicError.textContent = '';
                nicInput.classList.remove('is-invalid');
                isNicValid = true;
                return true;
            }
        };

        // Function to handle vehicle type change and set date restrictions
        window.handleVehicleTypeChange = function() {
            const vehicleType = document.getElementById('vehicle_type').value.toLowerCase();
            const fromDateInput = document.getElementById('from_date');
            const toDateInput = document.getElementById('to_date');

            if (!fromDateInput || !toDateInput) return;

            // Reset any existing restrictions
            toDateInput.removeAttribute('max');
            toDateInput.removeAttribute('readonly');
            toDateInput.min = fromDateInput.value || '';

            if (vehicleType.includes('daily')) {
                // For daily vehicles: max 28 days selection
                if (fromDateInput.value) {
                    const fromDate = new Date(fromDateInput.value);
                    const maxToDate = new Date(fromDate);
                    maxToDate.setDate(maxToDate.getDate() + 28);

                    const yyyy = maxToDate.getFullYear();
                    const mm = String(maxToDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(maxToDate.getDate()).padStart(2, '0');

                    toDateInput.max = `${yyyy}-${mm}-${dd}`;

                    // If current to_date exceeds the max or is empty, set to from_date
                    if (!toDateInput.value || new Date(toDateInput.value) > maxToDate) {
                        toDateInput.value = fromDateInput.value;
                    }
                }
            } else if (vehicleType.includes('monthly')) {
                // For monthly vehicles: auto-set exactly 29 days from start date
                if (fromDateInput.value) {
                    const fromDate = new Date(fromDateInput.value);
                    const toDate = new Date(fromDate);
                    toDate.setDate(toDate.getDate() + 29);

                    const yyyy = toDate.getFullYear();
                    const mm = String(toDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(toDate.getDate()).padStart(2, '0');

                    toDateInput.value = `${yyyy}-${mm}-${dd}`;
                    toDateInput.setAttribute('readonly', 'readonly');
                }
            } else if (vehicleType.includes('annually')) {
                // For annual vehicles: auto-set exactly 364 days from start date
                if (fromDateInput.value) {
                    const fromDate = new Date(fromDateInput.value);
                    const toDate = new Date(fromDate);
                    toDate.setDate(toDate.getDate() + 364);

                    const yyyy = toDate.getFullYear();
                    const mm = String(toDate.getMonth() + 1).padStart(2, '0');
                    const dd = String(toDate.getDate()).padStart(2, '0');

                    toDateInput.value = `${yyyy}-${mm}-${dd}`;
                    toDateInput.setAttribute('readonly', 'readonly');
                }
            }
        }

        // Function to check for duplicate vehicle number and NIC in session cart
        window.checkDuplicateInCart = function() {
            const vehicleNumber = document.getElementById('vehicle_number').value.trim().toUpperCase();
            const nicNumber = document.getElementById('nic_number').value.trim().toUpperCase();
            const duplicateError = document.getElementById('duplicate_error');
            const addBtn = document.getElementById('addToListBtn');
            
            if (!vehicleNumber && !nicNumber) {
                duplicateError.textContent = '';
                return;
            }

            let isDuplicate = false;
            let duplicateType = '';

            // Check against cart data for vehicle numbers and NIC numbers
            if (window.cartData && window.cartData.length > 0) {
                for (const entry of window.cartData) {
                    const cartVehicleNumber = (entry.vehicle_number || '').toUpperCase();
                    const cartNicNumber = (entry.nic_number || '').toUpperCase();
                    
                    // Check if current vehicle number matches any existing vehicle numbers
                    if (vehicleNumber && cartVehicleNumber === vehicleNumber) {
                        isDuplicate = true;
                        duplicateType = 'vehicle number';
                        break;
                    }
                    
                    // Check if current NIC number matches any existing NIC numbers
                    if (nicNumber && cartNicNumber === nicNumber) {
                        isDuplicate = true;
                        duplicateType = 'NIC number';
                        break;
                    }
                }
            }

            if (isDuplicate) {
                duplicateError.textContent = `⚠️ This ${duplicateType} is already in the cart. One vehicle/person can only have one permit per submission.`;
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
            const vehicleType = document.getElementById('vehicle_type').value;
            const vehicleNumber = document.getElementById('vehicle_number').value.trim();
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            const companyName = document.getElementById('company_name').value;
            const revenueLicenseNumber = document.getElementById('revenue_license_number').value.trim();
            const insuranceNumber = document.getElementById('insurance_number').value.trim();
            const ownerName = document.getElementById('owner_name').value.trim();
            const msg = document.getElementById('availability-msg');
            const addBtn = document.getElementById('addToListBtn');

            msg.innerText = '';
            // Disable button while checking
            addBtn.disabled = true;
            addBtn.style.opacity = '0.6';
            addBtn.style.cursor = 'not-allowed';

    // Check for duplicate error first
    const duplicateError = document.getElementById('duplicate_error');
    if (duplicateError && duplicateError.textContent.trim() !== '') {
        msg.innerText = 'Cannot check availability: This Vehicle Number or NIC Number is already in the cart.';
        msg.style.color = 'red';
        return;
    }            // Check for empty fields and build detailed message
            const missingFields = [];
            if (!vehicleType) missingFields.push('Vehicle Type');
            if (!vehicleNumber) missingFields.push('Vehicle Number');
            if (!document.getElementById('nic_number').value.trim()) missingFields.push('NIC Number');
            if (!revenueLicenseNumber) missingFields.push('Revenue License Number');
            if (!insuranceNumber) missingFields.push('Insurance Number');
            if (!fromDate) missingFields.push('From Date');
            if (!toDate) missingFields.push('To Date');
            if (!ownerName) missingFields.push('Owner\'s Name');
            if (!companyName) missingFields.push('Company Name');

            if (missingFields.length > 0) {
                msg.innerText = "Please fill in: " + missingFields.join(', ');
                msg.style.color = 'red';
                return;
            }

            // Check document checkboxes - both Revenue License and Insurance must be checked
            const docRevenueLicence = document.getElementById('doc_revenue_licence').checked;
            const docInsurance = document.getElementById('doc_insurance').checked;

            if (!docRevenueLicence || !docInsurance) {
                msg.innerText = "Please check both required documents: Revenue License and Insurance";
                msg.style.color = 'red';
                return;
            }

            // Validate NIC number before checking availability
            if (!isNicValid) {
                msg.innerText = 'Please enter a valid NIC Number before checking availability.';
                msg.style.color = 'red';
                return;
            }

            // Check for duplicate in cart before making API call
            const cartRows = document.querySelectorAll('.table-responsive table tbody tr');
            let isDuplicate = false;

            cartRows.forEach(row => {
                const cartVehicleNumber = row.cells[1]?.textContent.trim().toUpperCase(); // Column 1 is vehicle number
                if (cartVehicleNumber === vehicleNumber.toUpperCase()) {
                    isDuplicate = true;
                }
            });

            if (isDuplicate) {
                msg.innerText = "⚠️ This vehicle number is already in the cart. Cannot add duplicate entries.";
                msg.style.color = 'red';
                addBtn.disabled = true;
                addBtn.style.backgroundColor = '#9e9e9e';
                addBtn.style.borderColor = '#9e9e9e';
                addBtn.style.opacity = '0.65';
                addBtn.style.cursor = 'not-allowed';
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
                    nic_number: document.getElementById('nic_number').value.trim(),
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
                        doc_revenue_licence: document.getElementById('doc_revenue_licence').checked ? '1' : '0',
                        doc_insurance: document.getElementById('doc_insurance').checked ? '1' : '0',
                        vehicle_type: document.getElementById('vehicle_type').value,
                        vehicle_number: vehicleNumber,
                        nic_number: document.getElementById('nic_number').value.trim(),
                        revenue_license_number: document.getElementById('revenue_license_number').value.trim(),
                        insurance_number: document.getElementById('insurance_number').value.trim(),
                        from_date: fromDate,
                        to_date: toDate,
                        owner_name: document.getElementById('owner_name').value.trim(),
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
                doc_revenue_licence: document.getElementById('doc_revenue_licence').checked ? '1' : '0',
                doc_insurance: document.getElementById('doc_insurance').checked ? '1' : '0',
                vehicle_type: document.getElementById('vehicle_type').value,
                vehicle_number: document.getElementById('vehicle_number').value.trim(),
                nic_number: document.getElementById('nic_number').value.trim(),
                revenue_license_number: document.getElementById('revenue_license_number').value.trim(),
                insurance_number: document.getElementById('insurance_number').value.trim(),
                from_date: document.getElementById('from_date').value,
                to_date: document.getElementById('to_date').value,
                owner_name: document.getElementById('owner_name').value.trim(),
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
            const fields = [
                'doc_revenue_licence', 'doc_insurance', 'vehicle_type', 'vehicle_number', 'nic_number',
                'revenue_license_number', 'insurance_number', 'from_date', 'to_date', 
                'owner_name', 'company_name'
            ];
            
            fields.forEach(fieldId => {
                const field = document.getElementById(fieldId);
                if (field) {
                    // Remove existing listener if any
                    field.removeEventListener('change', handleFormChange);
                    field.removeEventListener('input', handleFormChange);
                    
                    // Add change listener for all fields (works for checkboxes, selects, and inputs)
                    field.addEventListener('change', handleFormChange);
                    
                    // Add input listener for text inputs (not needed for checkboxes/selects)
                    if (field.type !== 'checkbox' && field.tagName !== 'SELECT') {
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
            const vehicleTypeInput = document.getElementById('vehicle_type');
            const nicNumberInput = document.getElementById('nic_number');

            if (fromDateInput) {
                fromDateInput.addEventListener('change', function() {
                    setMaxToDate();
                    handleVehicleTypeChange(); // Also update vehicle type restrictions
                });
            }
            if (vehicleTypeInput) {
                vehicleTypeInput.addEventListener('change', handleVehicleTypeChange);
            }
            if (idTypeInput) {
                idTypeInput.addEventListener('change', setMaxToDate);
            }
            
            // Add NIC validation event listeners
            if (nicNumberInput) {
                nicNumberInput.addEventListener('blur', validateNicNumber);
                
                // Run validation on page load if NIC number is pre-filled
                if (nicNumberInput.value.trim() !== '') {
                    validateNicNumber();
                }
            }
            
            // Enable company dropdown before form submission
            const form = document.querySelector('form[action="{{ route('permit.vehicle.addToSession') }}"]');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const companyDropdown = document.getElementById('company_name');
                    if (companyDropdown && companyDropdown.disabled) {
                        companyDropdown.disabled = false;
                    }
                });
            }
        });

        // Clear Form function - preserves company data and cart state
        window.clearForm = function() {
            try {
                // Check if there are any entries in the cart
                const cartRows = document.querySelectorAll('.table-responsive table tbody tr');
                const hasCartEntries = cartRows.length > 0;
                
                // Store company name before clearing (only if cart has entries)
                let companyName = '';
                
                if (hasCartEntries) {
                    const companyField = document.getElementById('company_name');
                    if (companyField) companyName = companyField.value;
                }
                
                // Clear all form fields with null checks
                const vehicleNumber = document.getElementById('vehicle_number');
                const nicNumber = document.getElementById('nic_number');
                const fromDate = document.getElementById('from_date');
                const toDate = document.getElementById('to_date');
                const vehicleType = document.getElementById('vehicle_type');
                const revenueLicenseNumber = document.getElementById('revenue_license_number');
                const insuranceNumber = document.getElementById('insurance_number');
                const ownerName = document.getElementById('owner_name');
                const ownerAddress = document.getElementById('owner_address');
                const reason = document.getElementById('reason');
                const remarks = document.getElementById('remarks');
                
                if (vehicleNumber) vehicleNumber.value = '';
                if (nicNumber) nicNumber.value = '';
                if (fromDate) fromDate.value = '';
                if (toDate) {
                    toDate.value = '';
                    toDate.removeAttribute('readonly'); // Reset readonly for monthly and annual vehicles
                    toDate.removeAttribute('max'); // Reset max date restriction
                }
                if (vehicleType) vehicleType.value = '';
                if (revenueLicenseNumber) revenueLicenseNumber.value = '';
                if (insuranceNumber) insuranceNumber.value = '';
                if (ownerName) ownerName.value = '';
                if (ownerAddress) ownerAddress.value = '';
                if (reason) reason.value = '';
                if (remarks) remarks.value = '';
                
                // Clear company field if no cart entries
                if (!hasCartEntries) {
                    const companyField = document.getElementById('company_name');
                    if (companyField) {
                        companyField.value = '';
                        $('#company_name').val(null).trigger('change'); // Clear Select2
                    }
                }
                
                // Clear error messages
                const duplicateError = document.getElementById('duplicate_error');
                if (duplicateError) duplicateError.textContent = '';
                
                const nicError = document.getElementById('nic_number_error');
                if (nicError) nicError.textContent = '';
                
                // Reset document checkboxes - IMPORTANT: Clear these explicitly
                const docRevenueLicence = document.getElementById('doc_revenue_licence');
                const docInsurance = document.getElementById('doc_insurance');
                
                console.log('Clearing checkboxes:', docRevenueLicence, docInsurance); // Debug
                
                if (docRevenueLicence) {
                    docRevenueLicence.checked = false;
                    docRevenueLicence.removeAttribute('checked');
                    console.log('Revenue Licence cleared'); // Debug
                }
                if (docInsurance) {
                    docInsurance.checked = false;
                    docInsurance.removeAttribute('checked');
                    console.log('Insurance cleared'); // Debug
                }
                
                // Reset issue type radio buttons
                const issueTypeRadios = document.querySelectorAll('input[name="issue_type"]');
                issueTypeRadios.forEach(radio => {
                    radio.checked = false;
                    radio.removeAttribute('checked');
                });
                
                // Restore company name only if cart has entries
                if (hasCartEntries) {
                    const companyField = document.getElementById('company_name');
                    if (companyField) {
                        companyField.value = companyName;
                        $('#company_name').trigger('change.select2'); // Update Select2 display
                    }
                }
                
                // Reset validation states
                isNicValid = false; // Reset to false since NIC is required
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