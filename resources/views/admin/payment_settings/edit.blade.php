@extends('layouts.app')

@section('title', 'Edit Payment Information')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<style>
    .user-dashboard-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        padding: 1.5rem;
        margin-bottom: 2rem;
        border: none;
    }
    .user-dashboard-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1976d2;
        letter-spacing: 1px;
        margin-bottom: 1rem;
        border-bottom: 1px solid #bbdefb;
        padding-bottom: 0.5rem;
    }
    .form-control, .form-select {
        border-radius: 0.5rem;
        border: 1px solid #bbdefb;
        background-color: #f8fafc;
    }
    main .form-control:focus, main .form-select:focus {
        border-color: #1976d2;
        box-shadow: 0 0 0 0.25rem rgba(25, 118, 210, 0.12);
    }
    main .form-label { color: #1976d2; font-weight:500; }
    main .btn-primary { background-color:#1976d2; border-color:#1976d2; border-radius:0.5rem; font-weight:500; }
    main .btn-secondary { border-radius:0.5rem; font-weight:500; border-color:#6c757d; background-color:#6c757d; color:#fff; }
    small.text-danger { display:block; margin-top:0.25rem; }
    
    /* Custom SweetAlert2 styling */
    .swal2-popup {
        border-radius: 1rem;
        font-family: inherit;
    }
    .swal2-title {
        color: #1976d2;
        font-size: 1.5rem;
        font-weight: 600;
    }
    .swal2-html-container {
        color: #555;
    }
    .swal2-confirm {
        background-color: #1976d2 !important;
        border-radius: 0.5rem;
        font-weight: 500;
        padding: 0.5rem 2rem;
    }
</style>

<div class="container py-4">
    <div class="user-dashboard-card mx-auto" style="max-width:720px;">
        <div class="user-dashboard-title"><i class="bi bi-cash-stack me-2"></i> Edit Payment Settings</div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.payment_settings.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="rate" class="form-label">Base Rate (Temporary Permits - Per Day)</label>
                <input type="number" step="0.01" name="rate" id="rate" value="{{ old('rate', $settings->rate) }}" class="form-control" required>
                <small class="text-muted">Rate per day for temporary permits</small>
                @error('rate') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="monthly_rate" class="form-label">Monthly Rate (Fixed for 30 Days)</label>
                <input type="number" step="0.01" name="monthly_rate" id="monthly_rate" value="{{ old('monthly_rate', $settings->monthly_rate ?? 3000) }}" class="form-control" required>
                <small class="text-muted">Fixed rate for all monthly permits (not multiplied by days)</small>
                @error('monthly_rate') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="ssl" class="form-label">SSL (%)</label>
                <input type="number" step="0.01" name="ssl" id="ssl" value="{{ old('ssl', $settings->ssl) }}" class="form-control" required>
                @error('ssl') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mb-3">
                <label for="vat" class="form-label">VAT (%)</label>
                <input type="number" step="0.01" name="vat" id="vat" value="{{ old('vat', $settings->vat) }}" class="form-control" required>
                @error('vat') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-save me-1"></i> Save Changes</button>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Show success message with SweetAlert
    @if(session('success'))
        Swal.fire({
            title: 'Success!',
            text: '{{ session('success') }}',
            icon: 'success',
            iconColor: '#1976d2',
            confirmButtonColor: '#1976d2',
            confirmButtonText: 'OK',
            timer: 3000,
            timerProgressBar: true,
            customClass: {
                popup: 'swal2-popup',
                title: 'swal2-title',
                confirmButton: 'swal2-confirm'
            }
        });
    @endif
</script>

@endsection
