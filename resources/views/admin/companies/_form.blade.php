<form action="{{ $action }}" method="POST" class="ajax-form">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label">Company Name<span class="text-danger">*</span></label>
        <input type="text" name="name" 
               class="form-control @error('name') is-invalid @enderror" 
               value="{{ old('name', $company->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Company Address</label>
        <textarea name="address" rows="3" 
                  class="form-control @error('address') is-invalid @enderror">{{ old('address', $company->address ?? '') }}</textarea>
        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="{{ route('admin.companies.index') }}" class="btn btn-secondary ajax-link">Cancel</a>
</form>
