@extends('layouts.app')

@section('title', 'Profile Settings')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .profile-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        padding: 2rem;
        margin-bottom: 2rem;
        border: none;
    }
    .profile-title {
        font-size: 1.75rem;
        font-weight: 600;
        color: #1976d2;
        letter-spacing: 0.5px;
        margin-bottom: 1.5rem;
    }
    .profile-subtitle {
        font-size: 0.9rem;
        color: #555;
        margin-bottom: 1.5rem;
    }
    .form-control {
        border-radius: 0.5rem;
        border: 1px solid #bbdefb;
        background-color: #f8fafc;
    }
    .form-control:focus {
        border-color: #1976d2;
        box-shadow: 0 0 0 0.25rem rgba(25, 118, 210, 0.25);
    }
    .form-label {
        font-weight: 500;
        color: #1976d2;
    }
    .btn-primary {
        background-color: #1976d2;
        border-color: #1976d2;
        border-radius: 0.5rem;
        font-weight: 500;
        padding: 0.5rem 1.5rem;
        transition: background-color 0.2s;
    }
    .btn-primary:hover {
        background-color: #1565c0;
        border-color: #1565c0;
    }
    .role-badge {
        background: linear-gradient(135deg, #1e3a8a 0%, #1976d2 100%);
        color: white;
        font-weight: 600;
        padding: 0.35rem 1rem;
        border-radius: 0.5rem;
        display: inline-block;
        font-size: 0.85rem;
        text-transform: uppercase;
    }
</style>

<div class="container py-4" style="max-width: 900px;">
    <!-- Title Header -->
    <div class="d-flex align-items-center mb-4 text-[#13314C]">
        <i class="bi bi-person-gear fs-2 me-3 text-[#1976d2]"></i>
        <div>
            <h2 class="mb-1" style="font-weight: 700;">Profile Settings</h2>
            <p class="text-muted mb-0" style="font-size: 0.9rem;">Manage your user account profile information and password.</p>
        </div>
    </div>

    <!-- Profile Information Section -->
    <div class="profile-card">
        <div class="profile-title d-flex align-items-center">
            <i class="bi bi-person-circle me-2"></i> Profile Information
        </div>
        <p class="profile-subtitle">Update your account's profile details and email address.</p>

        @if (session('status') === 'profile-updated')
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>Profile information updated successfully!</div>
            </div>
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

        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
            </div>

            <div class="mb-4">
                <label class="form-label d-block">Account Role</label>
                <span class="role-badge">{{ str_replace('-', ' ', $user->role) }}</span>
                <small class="text-muted d-block mt-1">Your role is managed by the system administrator and cannot be modified here.</small>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Save Changes
            </button>
        </form>
    </div>

    <!-- Update Password Section -->
    <div class="profile-card">
        <div class="profile-title d-flex align-items-center">
            <i class="bi bi-shield-lock me-2"></i> Update Password
        </div>
        <p class="profile-subtitle">Ensure your account is using a long, random password to stay secure.</p>

        @if (session('status') === 'password-updated')
            <div class="alert alert-success d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>Password changed successfully!</div>
            </div>
        @endif

        @if ($errors->updatePassword->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->updatePassword->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('put')

            <div class="mb-3">
                <label for="update_password_current_password" class="form-label">Current Password</label>
                <input type="password" class="form-control" id="update_password_current_password" name="current_password" required autocomplete="current-password">
            </div>

            <div class="mb-3">
                <label for="update_password_password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="update_password_password" name="password" required autocomplete="new-password">
            </div>

            <div class="mb-4">
                <label for="update_password_password_confirmation" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="update_password_password_confirmation" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-key me-1"></i> Update Password
            </button>
        </form>
    </div>
</div>
@endsection
