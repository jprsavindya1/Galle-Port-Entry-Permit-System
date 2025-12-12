@extends('layouts.app')
@section('title', 'Add Vehicle')
@section('content')
<div class="container mt-4">
    <h3>Add New Vehicle</h3>
    <form action="{{ route('admin.vehicles.store') }}" method="POST" class="ajax-form">
        @csrf

        <div class="mb-3">
            <label class="form-label">Permit Type <span class="text-danger">*</span></label>
            <select name="permit_type" id="permitType" class="form-select @error('permit_type') is-invalid @enderror" required>
                <option value="">Select Permit Type</option>
                <option value="Daily" {{ old('permit_type') == 'Daily' ? 'selected' : '' }}>Daily</option>
                <option value="Monthly" {{ old('permit_type') == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="Annually" {{ old('permit_type') == 'Annually' ? 'selected' : '' }}>Annually</option>
            </select>
            @error('permit_type') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Vehicle Description <span class="text-danger">*</span></label>
            <input type="text" name="vehicle_description" id="vehicleDescription"
                   class="form-control @error('vehicle_description') is-invalid @enderror" 
                   value="{{ old('vehicle_description') }}" 
                   placeholder="e.g., Car, Motorcycle, Truck" required>
            @error('vehicle_description') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label d-flex align-items-center">
                <i class="bi bi-tag-fill me-2 text-primary"></i>
                Full Vehicle Name 
                <span class="badge bg-success ms-2" style="font-size: 0.7rem;">Auto-generated</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-primary text-white">
                    <i class="bi bi-check-circle-fill"></i>
                </span>
                <input type="text" name="name" id="fullVehicleName"
                       class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name') }}" readonly 
                       style="background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%); 
                              border: 2px solid #2196F3; 
                              font-weight: 600; 
                              color: #1565C0; 
                              font-size: 1.05rem;
                              cursor: not-allowed;">
            </div>
            @error('name') 
                <div class="invalid-feedback d-block">{{ $message }}</div> 
            @else
                <small class="form-text text-muted">
                    <i class="bi bi-info-circle me-1"></i>
                    This field is automatically generated from your selections above
                </small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Vehicle Code <span class="text-danger">*</span></label>
            <input type="text" name="code" id="vehicleCode"
                   class="form-control @error('code') is-invalid @enderror" 
                   value="{{ old('code', $nextCode ?? 'V001') }}" required>
            @error('code') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @else
                <small class="form-text text-muted">Auto-generated, but you can change it if needed</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Base Rate (Rs) <span class="text-danger">*</span></label>
            <input type="number" name="rate" step="0.01" 
                   class="form-control @error('rate') is-invalid @enderror" 
                   value="{{ old('rate', $vehicle->rate ?? '') }}" required>
            @error('rate') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        <button type="submit" class="btn btn-success">Save</button>
        <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary ajax-link">Cancel</a>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const permitType = document.getElementById('permitType');
    const vehicleDescription = document.getElementById('vehicleDescription');
    const fullVehicleName = document.getElementById('fullVehicleName');
    
    function updateVehicleName() {
        const type = permitType.value;
        const description = vehicleDescription.value.trim();
        
        if (type && description) {
            fullVehicleName.value = description + ' - ' + type;
        } else {
            fullVehicleName.value = '';
        }
    }
    
    permitType.addEventListener('change', updateVehicleName);
    vehicleDescription.addEventListener('input', updateVehicleName);
    
    // Initialize on page load if there are old values
    updateVehicleName();
});
</script>
@endsection
