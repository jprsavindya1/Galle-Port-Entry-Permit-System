@extends('layouts.app')

@section('title', 'Edit Permit')

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

        <form method="POST" action="{{ route('permits.update', [$permitType, $permit->id]) }}" id="edit-permit-form">
            @csrf
            @method('PUT')

            <input type="hidden" name="permit_type" value="{{ $permit->type }}">

            @if($permitType === 'vehicle')
                {{-- --- Section 1: Vehicle Identification --- --}}
                <div class="form-section-card">
                    <div class="form-section-title"><i class="bi bi-car-front-fill me-2"></i> Vehicle Identification</div>

                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="vehicle_type" class="form-label">Vehicle Type</label>
                            <select name="vehicle_type" id="vehicle_type" class="form-select" required>
                                <option value="">-- Select Vehicle Type --</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->name }}"
                                        {{ old('vehicle_type', $permit->vehicle_type) == $vehicle->name ? 'selected' : '' }}>
                                        {{ $vehicle->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="vehicle_number" class="form-label">Vehicle Number</label>
                            <input type="text" name="vehicle_number" id="vehicle_number" class="form-control" value="{{ old('vehicle_number', $permit->vehicle_number) }}" required oninput="this.value = this.value.toUpperCase(); checkBlacklistStatusVehicle();">
                            <div style="min-height: 20px;">
                                <span id="vehicle_blacklist_msg" class="small d-block" style="font-weight: 500;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="revenue_license_number" class="form-label">Revenue License Number</label>
                            <input type="text" name="revenue_license_number" id="revenue_license_number" class="form-control" value="{{ old('revenue_license_number', $permit->revenue_license_number) }}" required oninput="this.value = this.value.toUpperCase();">
                        </div>
                        <div class="col-md-6">
                            <label for="nic_number" class="form-label">NIC Number (Required)</label>
                            <input type="text" name="nic_number" id="nic_number" class="form-control" value="{{ old('nic_number', $permit->nic_number) }}" required oninput="this.value = this.value.toUpperCase(); checkBlacklistStatusNic();">
                            <div style="min-height: 20px;">
                                <span id="nic_blacklist_msg" class="small d-block" style="font-weight: 500;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="insurance_number" class="form-label">Insurance Number</label>
                            <input type="text" name="insurance_number" id="insurance_number" class="form-control" value="{{ old('insurance_number', $permit->insurance_number) }}" oninput="this.value = this.value.toUpperCase();">
                        </div>
                    </div>

                    <fieldset class="mb-3">
                        <legend class="col-form-label pt-0" style="font-size: 1rem;"><i class="bi bi-paperclip me-1"></i> Documents Attached</legend>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="doc_revenue_licence" name="doc_revenue_licence" value="1"
                                {{ old('doc_revenue_licence', $permit->doc_revenue_licence) ? 'checked' : '' }}>
                            <label class="form-check-label" for="doc_revenue_licence">Revenue License</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" class="form-check-input" id="doc_insurance" name="doc_insurance" value="1"
                                {{ old('doc_insurance', $permit->doc_insurance) ? 'checked' : '' }}>
                            <label class="form-check-label" for="doc_insurance">Insurance</label>
                        </div>
                    </fieldset>
                </div>

                {{-- --- Section 2: Owner & Duration --- --}}
                <div class="form-section-card">
                    <div class="form-section-title"><i class="bi bi-person-circle me-2"></i> Owner & Validity</div>

                    <div class="mb-3">
                        <label for="owner_name" class="form-label">Owner's Name</label>
                        <input type="text" name="owner_name" id="owner_name" class="form-control" value="{{ old('owner_name', $permit->owner_name) }}" required oninput="this.value = this.value.toUpperCase();">
                    </div>

                    <div class="mb-4">
                        <label for="owner_address" class="form-label">Owner's Address (Optional)</label>
                        <input type="text" name="owner_address" id="owner_address" class="form-control" value="{{ old('owner_address', $permit->owner_address) }}">
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date', $permit->from_date ? $permit->from_date->format('Y-m-d') : '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('to_date', $permit->to_date ? $permit->to_date->format('Y-m-d') : '') }}" required>
                        </div>
                    </div>

                    {{-- Check Availability Button --}}
                    <div class="d-flex align-items-center p-3 bg-light rounded border">
                        <button type="button" class="btn btn-info me-3" onclick="checkVehicleAvailability()">
                            <i class="bi bi-check-circle-fill me-1"></i> Check Availability
                        </button>
                        <p id="availability-msg" class="fw-bold my-0"></p>
                    </div>
                </div>

                {{-- --- Section 3: Permit Details --- --}}
                <div class="form-section-card">
                    <div class="form-section-title"><i class="bi bi-card-checklist me-2"></i> Permit Details</div>

                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <select name="company_name" id="company_name" class="form-select" required>
                            <option value="">-- Select Company --</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->name }}"
                                    {{ old('company_name', $permit->company_name) == $company->name ? 'selected' : '' }}>
                                    {{ $company->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <fieldset class="mb-3">
                        <legend class="col-form-label pt-0" style="font-size: 1rem;"><i class="bi bi-cash me-1"></i> Issue Type</legend>
                        @php $issueType = old('issue_type', $permit->issue_type ?? 'free'); @endphp
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

                    <div class="mb-4">
                        <label for="remarks" class="form-label">Remarks (Optional)</label>
                        <input type="text" name="remarks" id="remarks" class="form-control" value="{{ old('remarks', $permit->remarks) }}">
                    </div>
                </div>
            @else
                {{-- --- Section 1: ID & Duration --- --}}
                <div class="form-section-card">
                    <div class="form-section-title"><i class="bi bi-person-vcard me-2"></i> Personal Identification & Validity</div>

                    @if($permit->photo_path)
                        <div class="row mb-4 align-items-center bg-light p-3 rounded border">
                            <div class="col-md-3 text-center">
                                <img src="{{ asset('storage/' . $permit->photo_path) }}" alt="Profile Photo" class="img-thumbnail rounded-circle shadow" style="width: 100px; height: 100px; object-fit: cover;">
                            </div>
                            <div class="col-md-9">
                                <h6 class="text-primary fw-bold mb-1"><i class="bi bi-image me-1"></i> Applicant Profile Photo</h6>
                                <p class="text-muted small mb-0">This photo is printed on the entry permit and used at the verification gates.</p>
                            </div>
                        </div>
                    @endif

                    @if($permit->doc_nic_path || $permit->doc_passport_path || $permit->doc_driving_licence_path || ($permit->type === 'MP' && $permit->doc_police_report_path))
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="bi bi-paperclip me-1"></i> Uploaded Documents Scan</label>
                            <div class="list-group">
                                @if($permit->doc_nic_path)
                                    <a href="{{ asset('storage/' . $permit->doc_nic_path) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> NIC Scan File</span>
                                        <span class="badge bg-primary rounded-pill"><i class="bi bi-eye-fill"></i> View</span>
                                    </a>
                                @endif
                                @if($permit->doc_passport_path)
                                    <a href="{{ asset('storage/' . $permit->doc_passport_path) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Passport Scan File</span>
                                        <span class="badge bg-primary rounded-pill"><i class="bi bi-eye-fill"></i> View</span>
                                    </a>
                                @endif
                                @if($permit->doc_driving_licence_path)
                                    <a href="{{ asset('storage/' . $permit->doc_driving_licence_path) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Driving Licence Scan File</span>
                                        <span class="badge bg-primary rounded-pill"><i class="bi bi-eye-fill"></i> View</span>
                                    </a>
                                @endif
                                @if($permit->type === 'MP' && $permit->doc_police_report_path)
                                    <a href="{{ asset('storage/' . $permit->doc_police_report_path) }}" target="_blank" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Police Clearance Report Scan</span>
                                        <span class="badge bg-primary rounded-pill"><i class="bi bi-eye-fill"></i> View</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

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
                            <input type="text" name="id_number" id="id_number" class="form-control" value="{{ old('id_number', $permit->id_number) }}" required oninput="this.value = this.value.toUpperCase(); updateIdValidation(); checkBlacklistStatus();">
                            <div style="min-height: 20px;">
                                <span id="id_number_error" class="text-danger small d-block"></span>
                                <span id="blacklist_msg" class="small d-block" style="font-weight: 500;"></span>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" value="{{ old('from_date', $permit->from_date ? $permit->from_date->format('Y-m-d') : '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" name="to_date" id="to_date" class="form-control" value="{{ old('to_date', $permit->to_date ? $permit->to_date->format('Y-m-d') : '') }}" required>
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

                    @php
                        $selectedPasses = old('pass_type', explode(',', $permit->pass_type ?? ''));
                    @endphp
                    <div class="mb-3">
                        <label class="form-label d-block">Pass Type (Select all that apply)</label>
                        <div class="form-check form-check-inline">
                            <input type="checkbox" name="pass_type[]" value="onboard" id="pass_onboard" class="form-check-input"
                                {{ in_array('onboard', $selectedPasses) ? 'checked' : '' }}>
                            <label class="form-check-label" for="pass_onboard">On Board</label>
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
                    </div>

                    <fieldset class="mb-3">
                        <legend class="col-form-label pt-0" style="font-size: 1rem;"><i class="bi bi-cash me-1"></i> Issue Type</legend>
                        @php $issueType = old('issue_type', $permit->issue_type ?? 'free'); @endphp
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
                                <input type="date" name="police_issue_date" id="police_issue_date" class="form-control" value="{{ old('police_issue_date', $permit->police_issue_date ? $permit->police_issue_date->format('Y-m-d') : '') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="police_expire_date" class="form-label">Police Report Expiry Date</label>
                                <input type="date" name="police_expire_date" id="police_expire_date" class="form-control" value="{{ old('police_expire_date', $permit->police_expire_date ? $permit->police_expire_date->format('Y-m-d') : '') }}" required>
                            </div>
                        </div>
                    @endif
                </div>

                @if($permit->type === 'TP' && $permit->is_yacht_crew)
                <div class="form-section-card" style="background: #fffde7; border: 1px solid #fff59d;">
                    <div class="form-section-title text-warning-dark"><i class="bi bi-ship-fill text-warning me-2"></i> Yacht Marina Crew Details</div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Yacht Name</label>
                            <input type="text" class="form-control bg-light" value="{{ $permit->yacht_name }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Yacht Agent</label>
                            <input type="text" class="form-control bg-light" value="{{ $permit->yacht_agent }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Passport Country</label>
                            <input type="text" class="form-control bg-light" value="{{ $permit->passport_country }}" readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Visa Expiry Date</label>
                            <input type="text" class="form-control bg-light" value="{{ $permit->visa_expiry }}" readonly>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" {{ $permit->customs_clearance ? 'checked' : '' }} disabled>
                                <label class="form-check-label fw-bold text-success">Customs & Immigration Cleared</label>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            @endif

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
    // Store blacklist status
    let isBlacklisted = false;
    let isVehicleBlacklisted = false;
    let isNicBlacklisted = false;

    // Check blacklist for Temporary/Monthly
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
                const checkBtn = document.querySelector('button[onclick="checkPermitAvailability(true)"]');
                if (checkBtn) checkBtn.disabled = true;
            } else {
                msgEl.textContent = data.message;
                msgEl.style.color = 'green';
                isBlacklisted = false;
                const checkBtn = document.querySelector('button[onclick="checkPermitAvailability(true)"]');
                if (checkBtn) checkBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error("Failed to check blacklist:", error);
            msgEl.textContent = '';
            isBlacklisted = false;
        });
    }

    // Check vehicle blacklist
    window.checkBlacklistStatusVehicle = function() {
        const vehicleNumber = document.getElementById('vehicle_number').value.trim();
        const msgEl = document.getElementById('vehicle_blacklist_msg');
        
        if (!vehicleNumber) {
            msgEl.textContent = '';
            msgEl.style.color = '';
            isVehicleBlacklisted = false;
            return;
        }

        fetch("{{ route('permit.checkBlacklist') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ vehicle_number: vehicleNumber })
        })
        .then(res => res.json())
        .then(data => {
            if (data.blacklisted) {
                msgEl.textContent = data.message;
                msgEl.style.color = 'red';
                isVehicleBlacklisted = true;
                const checkBtn = document.querySelector('button[onclick="checkVehicleAvailability()"]');
                if (checkBtn) checkBtn.disabled = true;
            } else {
                msgEl.textContent = data.message;
                msgEl.style.color = 'green';
                isVehicleBlacklisted = false;
                const checkBtn = document.querySelector('button[onclick="checkVehicleAvailability()"]');
                if (checkBtn && !isNicBlacklisted) checkBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error("Failed to check vehicle blacklist:", error);
            msgEl.textContent = '';
            isVehicleBlacklisted = false;
        });
    }

    // Check NIC blacklist for vehicle owner/driver
    window.checkBlacklistStatusNic = function() {
        const nicNumber = document.getElementById('nic_number').value.trim();
        const msgEl = document.getElementById('nic_blacklist_msg');
        
        if (!nicNumber) {
            msgEl.textContent = '';
            msgEl.style.color = '';
            isNicBlacklisted = false;
            return;
        }

        fetch("{{ route('permit.checkBlacklist') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ id_number: nicNumber })
        })
        .then(res => res.json())
        .then(data => {
            if (data.blacklisted) {
                msgEl.textContent = data.message;
                msgEl.style.color = 'red';
                isNicBlacklisted = true;
                const checkBtn = document.querySelector('button[onclick="checkVehicleAvailability()"]');
                if (checkBtn) checkBtn.disabled = true;
            } else {
                msgEl.textContent = data.message;
                msgEl.style.color = 'green';
                isNicBlacklisted = false;
                const checkBtn = document.querySelector('button[onclick="checkVehicleAvailability()"]');
                if (checkBtn && !isVehicleBlacklisted) checkBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error("Failed to check NIC blacklist:", error);
            msgEl.textContent = '';
            isNicBlacklisted = false;
        });
    }

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
        $('#vehicle_type').select2({
            placeholder: "-- Select Vehicle Type --",
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
            const addressEl = document.getElementById('company_address');
            if (addressEl) {
                addressEl.value = address;
            }
        });

        // Initial address set on page load if a company is already selected
        const initialCompanySelect = document.getElementById('company_name');
        if (initialCompanySelect && initialCompanySelect.value) {
            const selectedOption = initialCompanySelect.options[initialCompanySelect.selectedIndex];
            const addressEl = document.getElementById('company_address');
            if (selectedOption && addressEl) {
                 addressEl.value = selectedOption.getAttribute('data-address') || '';
            }
        }

        // Initialize validation on page load
        if (document.getElementById('id_type')) {
            validateId();
        }
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
                const nicPattern = /^(?:\d{9}[Vv]|\d{12})$/;
                isValid = nicPattern.test(idNumber);
                errorMessage = 'Invalid NIC format. Use 9 digits + V or 12 digits';
                break;
            case 'Passport':
                const passportPattern = /^[A-Z]{1,2}\d{6,7}$/i;
                isValid = passportPattern.test(idNumber);
                errorMessage = 'Invalid Passport format. Use 1-2 letters followed by 6-7 digits';
                break;
            case 'Driving License':
                const licensePattern = /^(?:\d{7,8}|[A-Z]\d{6,8}|\d{9}[VXvx]|\d{12})$/;
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

    window.updateIdValidation = validateId;

    /**
     * Checks the availability of the personal permit.
     */
    function checkPermitAvailability(isEdit = true) {
        const permitType = "{{ $permit->type }}"; // TP or MP
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

        if (isBlacklisted) {
            msg.innerText = 'Cannot check availability: This ID is blacklisted.';
            msg.style.color = 'red';
            return;
        }

        if (!idType || !idNumber || !fullName || !initials || !fromDate || !toDate) {
            msg.innerText = "Please fill in all required fields.";
            msg.style.color = 'red';
            return;
        }

        let payload = {
            permit_type: permitType === 'TP' ? 'temporary' : 'monthly',
            id_type: idType,
            id_number: idNumber,
            full_name: fullName,
            initials: initials,
            from_date: fromDate,
            to_date: toDate,
            current_permit_id: currentPermitId
        };

        if (permitType === 'TP' || permitType === 'MP') {
            payload.company_name = companyName;
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

    /**
     * Checks the availability of the vehicle permit.
     */
    function checkVehicleAvailability() {
        const vehicleNumber = document.getElementById('vehicle_number').value;
        const nicNumber = document.getElementById('nic_number').value;
        const fromDate = document.getElementById('from_date').value;
        const toDate = document.getElementById('to_date').value;
        const companyName = document.getElementById('company_name').value;
        const currentPermitId = {{ $permit->id ?? 'null' }};

        const msg = document.getElementById('availability-msg');
        msg.innerText = '';

        if (isVehicleBlacklisted || isNicBlacklisted) {
            msg.innerText = 'Cannot check availability: This vehicle/NIC is blacklisted.';
            msg.style.color = 'red';
            return;
        }

        if (!vehicleNumber || !nicNumber || !fromDate || !toDate || !companyName) {
            msg.innerText = "Please fill in all required fields.";
            msg.style.color = 'red';
            return;
        }

        fetch("{{ route('permit.vehicle.checkVehicleAvailability') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                vehicle_number: vehicleNumber,
                nic_number: nicNumber,
                from_date: fromDate,
                to_date: toDate,
                company_name: companyName,
                current_permit_id: currentPermitId
            })
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
</script>
@endpush