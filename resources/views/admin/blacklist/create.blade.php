@extends('layouts.app')

@section('title', 'Add Blacklist Entry')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

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
</style>

<div class="container py-4">
    <div class="user-dashboard-card mx-auto" style="max-width:720px;">
        <div class="user-dashboard-title"><i class="bi bi-person-dash me-2"></i> Add Blacklist Entry</div>

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('blacklist.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="nic" class="form-label">NIC</label>
                <input type="text" name="nic" class="form-control" value="{{ old('nic') }}">
            </div>

            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}">
            </div>

            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" name="company_name" class="form-control" value="{{ old('company_name') }}">
            </div>

            <div class="mb-3">
                <label for="vehicle_number" class="form-label">Vehicle Number</label>
                <input type="text" name="vehicle_number" class="form-control" value="{{ old('vehicle_number') }}">
            </div>

            <div class="mb-3">
                <label for="reason" class="form-label">Reason <span class="text-danger">*</span></label>
                <textarea name="reason" class="form-control" required>{{ old('reason') }}</textarea>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2"><i class="bi bi-plus-circle me-1"></i> Add Entry</button>
                <a href="{{ route('blacklist.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i> Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection
