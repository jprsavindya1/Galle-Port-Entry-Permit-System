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
            <label class="form-label">Vehicle Name <span class="text-danger">*</span></label>
            <input type="text" name="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $vehicle->name ?? '') }}" required style="border-radius:0.5rem;border:1px solid #bbdefb;background:#fff">
            @error('name') 
                <div class="invalid-feedback">{{ $message }}</div> 
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Vehicle Code <span class="text-danger">*</span></label>
            <input type="text" name="code" 
                   class="form-control @error('code') is-invalid @enderror" 
                   value="{{ old('code', $vehicle->code ?? '') }}" required style="border-radius:0.5rem;border:1px solid #bbdefb;background:#fff">
            @error('code') 
                <div class="invalid-feedback">{{ $message }}</div> 
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
