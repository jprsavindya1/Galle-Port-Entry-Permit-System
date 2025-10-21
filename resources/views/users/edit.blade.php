@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<style>
    .user-dashboard-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        padding: 2rem 2rem 1.5rem 2rem;
        margin-bottom: 2rem;
        border: none;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }
    .user-dashboard-title {
        font-size: 2rem;
        font-weight: 600;
        color: #1976d2;
        letter-spacing: 1px;
        margin-bottom: 1.5rem;
        text-align: center;
    }
    .form-label {
        font-weight: 500;
        color: #1976d2;
    }
    main .form-control {
        border-radius: 0.5rem;
        border: 1px solid #bbdefb;
        background: #f8fafc;
        color: #333;
    }
    main .btn-primary {
        background: #1976d2;
        border-radius: 0.5rem;
        font-weight: 500;
        border: none;
    }
    main .btn-primary:hover {
        background: #1565c0;
    }
    main .btn-secondary {
        border-radius: 0.5rem;
        font-weight: 500;
        background: #bbdefb;
        color: #1976d2;
        border: none;
    }
    .btn-secondary:hover {
        background: #e3f2fd;
        color: #1565c0;
    }
</style>

<div class="container py-4">
    <div class="user-dashboard-card">
        <div class="user-dashboard-title">
            <i class="bi bi-pencil-square me-2"></i> Edit User
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>There were some problems with your input:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <div class="input-group">
                    <span class="input-group-text bg-info text-white"><i class="bi bi-shield-lock"></i></span>
                    <select name="role" class="form-select" required style="font-weight:500;">
                        <option value="clerk" @selected(old('role', $user->role) === 'clerk')>🗂️ Clerk</option>
                        @if(auth()->user()->role === 'super-admin' || auth()->user()->role === 'admin')
                            <option value="admin" @selected(old('role', $user->role) === 'admin')>🛡️ Admin</option>
                        @endif
                        @if(auth()->user()->role === 'super-admin')
                            <option value="super-admin" @selected(old('role', $user->role) === 'super-admin')>👑 Super Admin</option>
                        @endif
                    </select>
                </div>
            </div>

            <!-- Dummy hidden fields to stop Chrome/Edge autofill -->
            <input type="text" name="fake_username" style="display:none">
            <input type="password" name="fake_password" style="display:none">

            <div class="mb-3">
                <label class="form-label">New Password <small>(leave blank to keep current)</small></label>
                <input type="password" name="password" class="form-control" autocomplete="new-password">
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>
<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection
