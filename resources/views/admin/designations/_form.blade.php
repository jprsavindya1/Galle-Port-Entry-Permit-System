<form action="{{ $action }}" method="POST" class="ajax-form">
    @csrf
    @if($method === 'PUT') @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label">Designation Name <span class="text-danger">*</span></label>
        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $designation->name ?? '') }}" required>
        @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <button type="submit" class="btn btn-success">Save</button>
    <a href="{{ route('admin.designations.index') }}" class="btn btn-secondary ajax-link">Cancel</a>
</form>
