<style>
    .form-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        padding: 1.25rem;
        box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        border: none;
    }
    .form-card .form-label { font-weight:600; color:#0d47a1 }
    .form-actions { margin-top:1rem }
    .btn-cancel { background:#fff; border:1px solid #cfd8dc; color:#455a64 }
</style>

<div class="form-card">
    <form action="{{ $action }}" method="POST" class="ajax-form">
        @csrf
        @if($method === 'PUT') @method('PUT') @endif

        <div class="mb-3">
            <label class="form-label">Permit Type <span class="text-danger">*</span></label>
            <select name="permit_type" id="permitTypeModal" class="form-select @error('permit_type') is-invalid @enderror" required style="border-radius:0.5rem;border:1px solid #bbdefb;background:#fff">
                <option value="">Select Permit Type</option>
                @php
                    $currentType = '';
                    if (isset($vehicle->name)) {
                        if (stripos($vehicle->name, 'daily') !== false) $currentType = 'Daily';
                        elseif (stripos($vehicle->name, 'monthly') !== false) $currentType = 'Monthly';
                        elseif (stripos($vehicle->name, 'annually') !== false) $currentType = 'Annually';
                    }
                @endphp
                <option value="Daily" {{ old('permit_type', $currentType) == 'Daily' ? 'selected' : '' }}>Daily</option>
                <option value="Monthly" {{ old('permit_type', $currentType) == 'Monthly' ? 'selected' : '' }}>Monthly</option>
                <option value="Annually" {{ old('permit_type', $currentType) == 'Annually' ? 'selected' : '' }}>Annually</option>
            </select>
            @error('permit_type') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Vehicle Description <span class="text-danger">*</span></label>
            @php
                $currentDesc = '';
                if (isset($vehicle->name)) {
                    $currentDesc = preg_replace('/\s*-?\s*(daily|monthly|annually)\s*/i', '', $vehicle->name);
                }
            @endphp
            <input type="text" name="vehicle_description" id="vehicleDescriptionModal"
                   class="form-control @error('vehicle_description') is-invalid @enderror" 
                   value="{{ old('vehicle_description', $currentDesc) }}" 
                   placeholder="e.g., Car, Motorcycle, Truck" required style="border-radius:0.5rem;border:1px solid #bbdefb;background:#fff">
            @error('vehicle_description') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label d-flex align-items-center">
                <i class="bi bi-tag-fill me-2 text-primary"></i>
                Full Vehicle Name 
                <span class="badge bg-success ms-2" style="font-size: 0.65rem;">Auto-generated</span>
            </label>
            <div class="input-group">
                <span class="input-group-text bg-primary text-white" style="border-radius:0.5rem 0 0 0.5rem;">
                    <i class="bi bi-check-circle-fill"></i>
                </span>
                <input type="text" name="name" id="fullVehicleNameModal"
                       class="form-control @error('name') is-invalid @enderror" 
                       value="{{ old('name', $vehicle->name ?? '') }}" readonly 
                       style="border-radius:0 0.5rem 0.5rem 0;
                              background: linear-gradient(135deg, #e3f2fd 0%, #f8f9fa 100%); 
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
                   value="{{ old('code', $vehicle->code ?? $nextCode ?? 'V001') }}" required style="border-radius:0.5rem;border:1px solid #bbdefb;background:#fff">
            @error('code') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @else
                <small class="form-text text-muted">Auto-generated, but you can change it if needed</small>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Base Rate (Rs) <span class="text-danger">*</span></label>
            <input type="number" step="0.01" name="rate" 
                   class="form-control @error('rate') is-invalid @enderror" 
                   value="{{ old('rate', $vehicle->rate ?? 0) }}" required style="border-radius:0.5rem;border:1px solid #bbdefb;background:#fff">
            @error('rate') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-success" style="border-radius:0.5rem;font-weight:500;"><i class="bi bi-save me-1"></i> Save</button>
            <a href="{{ route('admin.vehicles.index') }}" class="btn btn-cancel ajax-link" style="border-radius:0.5rem;margin-left:0.5rem;"><i class="bi bi-x-lg me-1"></i> Cancel</a>
        </div>
    </form>
</div>

<script>
(function() {
    const permitType = document.getElementById('permitTypeModal');
    const vehicleDescription = document.getElementById('vehicleDescriptionModal');
    const fullVehicleName = document.getElementById('fullVehicleNameModal');
    
    if (permitType && vehicleDescription && fullVehicleName) {
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
        
        // Initialize on page load
        updateVehicleName();
    }
})();
</script>
