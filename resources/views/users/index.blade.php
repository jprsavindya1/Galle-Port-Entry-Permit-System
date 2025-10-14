@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<style>
    .user-dashboard-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        padding: 2rem 2rem 1.5rem 2rem;
        margin-bottom: 2rem;
        border: none;
    }
    .user-dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .user-dashboard-title {
        font-size: 2rem;
        font-weight: 600;
        color: #1976d2;
        letter-spacing: 1px;
    }
    .user-dashboard-table {
        background: #f5faff;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .user-dashboard-table th {
        background: #e3f2fd;
        color: #1976d2;
        font-weight: 500;
        border-bottom: 2px solid #bbdefb;
    }
    .user-dashboard-table td {
        background: #f8fafc;
        color: #333;
        vertical-align: middle;
    }
    .user-avatar {
        width: 36px;
        height: 36px;
        background: #90caf9;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        margin-right: 0.5rem;
        box-shadow: 0 1px 4px rgba(25,118,210,0.08);
    }
    .user-role-badge {
        background: #bbdefb;
        color: #1976d2;
        font-weight: 500;
        border-radius: 0.5rem;
        padding: 0.25rem 0.75rem;
        font-size: 0.95rem;
    }
    .user-action-btn {
        font-size: 0.95rem;
        padding: 0.35rem 0.8rem;
        border-radius: 0.5rem;
        margin-right: 0.25rem;
        transition: background 0.2s, color 0.2s;
    }
    .user-action-btn.edit {
        background: #fff3e0;
        color: #ff9800;
        border: 1px solid #ffe0b2;
    }
    .user-action-btn.edit:hover {
        background: #ffe0b2;
        color: #e65100;
    }
    .user-action-btn.delete {
        background: #ffebee;
        color: #e53935;
        border: 1px solid #ffcdd2;
    }
    .user-action-btn.delete:hover {
        background: #ffcdd2;
        color: #b71c1c;
    }
</style>

<div class="container py-4">
    <div class="user-dashboard-card">
        <div class="user-dashboard-header">
            <div class="user-dashboard-title">
                <i class="bi bi-people-fill me-2"></i> User Management
            </div>
            <a href="{{ route('users.create') }}" class="btn btn-success" style="border-radius:0.5rem;font-weight:500;">
                <i class="bi bi-person-plus me-1"></i> Add New User
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Search + Filter Form -->
        <form method="GET" action="{{ route('users.index') }}" class="row g-3 mb-3 align-items-end" style="background:linear-gradient(135deg,#e3f2fd 0%,#f8fafc 100%);border-radius:0.75rem;padding:1.25rem 1rem;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
            <div class="col-md-4">
                <label class="form-label mb-1" for="search"><i class="bi bi-search me-1"></i> Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Search by name or email" style="border-radius:0.5rem;border:1px solid #bbdefb;background:#f8fafc;">
            </div>
            <div class="col-md-3">
                <label class="form-label mb-1" for="role"><i class="bi bi-shield-lock me-1"></i> Role</label>
                <div class="input-group">
                    <span class="input-group-text bg-info text-white"><i class="bi bi-shield-lock"></i></span>
                    <select name="role" id="role" class="form-select" onchange="this.form.submit()" style="font-weight:500;">
                        <option value="">All Roles</option>
                        <option value="super-admin" {{ request('role') == 'super-admin' ? 'selected' : '' }}>👑 Super Admin</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>🛡️ Admin</option>
                        <option value="clerk" {{ request('role') == 'clerk' ? 'selected' : '' }}>🗂️ Clerk</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100" style="border-radius:0.5rem;font-weight:500;"><i class="bi bi-search"></i> Search</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('users.index') }}" class="btn btn-secondary w-100" style="border-radius:0.5rem;font-weight:500;"><i class="bi bi-arrow-clockwise"></i> Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table user-dashboard-table align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar">{{ strtoupper(substr($user->name,0,1)) }}</div>
                                <span>{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td><span class="user-role-badge">{{ ucfirst($user->role) }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('users.edit', $user) }}" class="user-action-btn edit"><i class="bi bi-pencil-square"></i> Edit</a>
                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="user-action-btn delete" onclick="return confirm('Delete this user?')"><i class="bi bi-trash"></i> Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No users found</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($users, 'links'))
            <div class="mt-3">
                {{ $users->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Bootstrap Icons CDN -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endsection
