<form action="{{ $action }}" method="POST" class="ajax-form">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label">Vehicle Name <span class="text-danger">*</span></label>
        <input type="text" name="name" 
               class="form-control @error('name') is-invalid @enderror" 
               value="{{ old('name', $vehicle->name ?? '') }}" required>
        @error('name') 
            <div class="invalid-feedback">{{ $message }}</div> 
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Vehicle Code <span class="text-danger">*</span></label>
        <input type="text" name="code" 
               class="form-control @error('code') is-invalid @enderror" 
               value="{{ old('code', $vehicle->code ?? '') }}" required>
        @error('code') 
            <div class="invalid-feedback">{{ $message }}</div> 
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Base Rate (Rs) <span class="text-danger">*</span></label>
        <input type="number" step="0.01" name="rate" 
               class="form-control @error('rate') is-invalid @enderror" 
               value="{{ old('rate', $vehicle->rate ?? 0) }}" required>
        @error('rate') 
            <div class="invalid-feedback">{{ $message }}</div> 
        @enderror
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="{{ route('admin.vehicles.index') }}" class="btn btn-secondary ajax-link">Cancel</a>
</form>
