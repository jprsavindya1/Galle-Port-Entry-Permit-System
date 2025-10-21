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
                    <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" required value="{{ old('vehicle_number') }}" oninput="this.value = this.value.toUpperCase();">
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
                    <input type="date" class="form-control" name="from_date" id="from_date" value="{{ old('from_date') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="to_date" class="form-label"><i class="bi bi-calendar-range me-1"></i> To Date</label>
                    <input type="date" class="form-control" name="to_date" id="to_date" value="{{ old('to_date') }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="owner_name" class="form-label"><i class="bi bi-person-circle me-1"></i> Owner's Name</label>
                <input type="text" class="form-control" name="owner_name" id="owner_name" required value="{{ old('owner_name') }}"
                oninput="this.value = this.value.toUpperCase();">
            </div>
            
            <div class="row mb-4 align-items-end">
                <div class="col-md-8">
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

                <div class="col-md-4">
                    <button type="button" onclick="checkVehicleAvailability()" class="btn btn-info w-100"><i class="bi bi-check-circle-fill me-1"></i> Check Availability</button>
                </div>
            </div>
            
            {{-- This paragraph was empty in the original code, using the div below instead --}}
            {{-- <p id="availability-msg" class="fw-bold"></p> --}} 
            <div id="availabilityMessage" class="mb-3"></div> 
            
            <div class="mb-3">
                <label for="owner_address" class="form-label"><i class="bi bi-house me-1"></i> Owner's Address</label>
                <input type="text" class="form-control" name="owner_address" id="owner_address" required value="{{ old('owner_address') }}"
                oninput="this.value = this.value.toUpperCase();">
            </div>

            <fieldset class="mb-4">
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

            <button type="submit" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i> Add to List</button>
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
                                <form method="POST" action="{{ route('permit.vehicle.removeVehicleSessionEntry', $index) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this entry?');">
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

    <script>
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
            const availabilityMessage = document.getElementById('availabilityMessage');

            availabilityMessage.innerHTML = '';

            if (!vehicleNumber || !fromDate || !toDate || !companyName) {
                availabilityMessage.innerHTML = '<div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill me-1"></i> Please fill **Vehicle Number**, **Company Name**, **From Date**, and **To Date** to check availability.</div>';
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
                availabilityMessage.innerHTML = '<div class="alert ' + (data.available ? 'alert-success' : 'alert-danger') + '"><i class="bi bi-' + (data.available ? 'check-circle' : 'x-circle') + '-fill me-1"></i> ' + data.message + '</div>';
            })
            .catch(() => {
                availabilityMessage.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-octagon-fill me-1"></i> Error checking availability. Please try again.</div>';
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
    </script>
@endpush