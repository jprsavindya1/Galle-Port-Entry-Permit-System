@extends('layouts.app')

@section('title', 'Create User')

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
    .form-control {
        border-radius: 0.5rem;
        border: 1px solid #bbdefb;
        background: #f8fafc;
        color: #333;
    }
    .btn-success {
        background: #1976d2;
        border-radius: 0.5rem;
        font-weight: 500;
        border: none;
    }
    .btn-success:hover {
        background: #1565c0;
    }
    .btn-secondary {
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
            <i class="bi bi-person-plus me-2"></i> Create New User
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

        <form action="{{ route('users.store') }}" method="POST" autocomplete="off">
            @csrf

            <div class="mb-3">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}" autocomplete="off">
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       class="form-control" 
                       required 
                       value="{{ old('email') }}" 
                       autocomplete="off"
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$">
                <small id="email-validation-message" class="text-muted">Enter a valid email address</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Role</label>
                <div class="input-group">
                    <span class="input-group-text bg-info text-white"><i class="bi bi-shield-lock"></i></span>
                    <select name="role" class="form-select" required style="font-weight:500;">
                        <option value="clerk" @selected(old('role') === 'clerk')>🗂️ Clerk</option>
                        @if(auth()->user()->role === 'super-admin' || auth()->user()->role === 'admin')
                            <option value="admin" @selected(old('role') === 'admin')>🛡️ Admin</option>
                        @endif
                        @if(auth()->user()->role === 'super-admin')
                            <option value="super-admin" @selected(old('role') === 'super-admin')>👑 Super Admin</option>
                        @endif
                    </select>
                </div>
            </div>

            <!-- Dummy hidden fields to stop Chrome/Edge autofill -->
            <input type="text" name="fake_username" style="display:none">
            <input type="password" name="fake_password" style="display:none">

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="position-relative">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           class="form-control" 
                           required 
                           autocomplete="new-password"
                           minlength="8"
                           style="padding-right: 45px;">
                    
                    <!-- Eye Icon Toggle -->
                    <button type="button" 
                            onclick="togglePasswordField('password', 'eye-icon-password', 'eye-slash-icon-password')" 
                            class="position-absolute border-0 bg-transparent"
                            style="top: 50%; right: 10px; transform: translateY(-50%);"
                            tabindex="-1">
                        <svg id="eye-icon-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-secondary" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg id="eye-slash-icon-password" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-secondary d-none" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <small class="text-muted">Minimum 8 characters</small>
            </div>

            <div class="mb-3">
                <label class="form-label">Confirm Password</label>
                <div class="position-relative">
                    <input type="password" 
                           id="password_confirmation" 
                           name="password_confirmation" 
                           class="form-control" 
                           required 
                           autocomplete="new-password"
                           style="padding-right: 45px;">
                    
                    <!-- Eye Icon Toggle -->
                    <button type="button" 
                            onclick="togglePasswordField('password_confirmation', 'eye-icon-confirm', 'eye-slash-icon-confirm')" 
                            class="position-absolute border-0 bg-transparent"
                            style="top: 50%; right: 10px; transform: translateY(-50%);"
                            tabindex="-1">
                        <svg id="eye-icon-confirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-secondary" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <svg id="eye-slash-icon-confirm" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-secondary d-none" style="width: 20px; height: 20px;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                        </svg>
                    </button>
                </div>
                <small id="password-match-message" class="text-muted"></small>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">Create User</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
            </div>
        </form>
    </div>
</div>

<!-- Password Toggle and Validation Script -->
<script>
    function togglePasswordField(inputId, eyeIconId, eyeSlashIconId) {
        const passwordInput = document.getElementById(inputId);
        const eyeIcon = document.getElementById(eyeIconId);
        const eyeSlashIcon = document.getElementById(eyeSlashIconId);
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.classList.add('d-none');
            eyeSlashIcon.classList.remove('d-none');
        } else {
            passwordInput.type = 'password';
            eyeIcon.classList.remove('d-none');
            eyeSlashIcon.classList.add('d-none');
        }
    }

    // Real-time password validation
    document.addEventListener('DOMContentLoaded', function() {
        const password = document.getElementById('password');
        const passwordConfirmation = document.getElementById('password_confirmation');
        const matchMessage = document.getElementById('password-match-message');
        const email = document.getElementById('email');
        const emailMessage = document.getElementById('email-validation-message');
        const form = password.closest('form');

        // Email validation
        function validateEmail() {
            const emailRegex = /^[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i;
            const value = email.value.trim();

            if (value === '') {
                emailMessage.textContent = 'Email is required';
                emailMessage.className = 'text-danger';
                email.setCustomValidity('Email is required');
                return false;
            } else if (!emailRegex.test(value)) {
                emailMessage.textContent = '✗ Invalid email format';
                emailMessage.className = 'text-danger';
                email.setCustomValidity('Invalid email format');
                return false;
            } else {
                emailMessage.textContent = '✓ Valid email';
                emailMessage.className = 'text-success';
                email.setCustomValidity('');
                return true;
            }
        }

        function validatePasswordMatch() {
            if (passwordConfirmation.value === '') {
                matchMessage.textContent = '';
                matchMessage.className = 'text-muted';
                return true;
            }

            if (password.value === passwordConfirmation.value) {
                matchMessage.textContent = '✓ Passwords match';
                matchMessage.className = 'text-success';
                return true;
            } else {
                matchMessage.textContent = '✗ Passwords do not match';
                matchMessage.className = 'text-danger';
                return false;
            }
        }

        function validatePasswordStrength() {
            const value = password.value;
            if (value.length < 8) {
                password.setCustomValidity('Password must be at least 8 characters long');
            } else {
                password.setCustomValidity('');
            }
        }

        email.addEventListener('input', validateEmail);
        email.addEventListener('blur', validateEmail);

        password.addEventListener('input', function() {
            validatePasswordStrength();
            validatePasswordMatch();
        });

        passwordConfirmation.addEventListener('input', validatePasswordMatch);

        form.addEventListener('submit', function(e) {
            validatePasswordStrength();
            
            if (!validateEmail()) {
                e.preventDefault();
                email.focus();
                alert('Please enter a valid email address.');
                return;
            }
            
            if (!validatePasswordMatch()) {
                e.preventDefault();
                passwordConfirmation.focus();
                alert('Passwords do not match. Please check and try again.');
            }
        });
    });
</script>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection
