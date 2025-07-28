@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container mt-5">
    <h2>Edit User</h2>

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
            <label>Name</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name', $user->name) }}">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email', $user->email) }}">
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="clerk" @selected(old('role', $user->role) === 'clerk')>Clerk</option>
                @if(auth()->user()->role === 'super-admin' || auth()->user()->role === 'admin')
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                @endif
                @if(auth()->user()->role === 'super-admin')
                    <option value="super-admin" @selected(old('role', $user->role) === 'super-admin')>Super Admin</option>
                @endif
            </select>
        </div>

        <div class="mb-3">
            <label>New Password <small>(leave blank to keep current)</small></label>
            <input type="password" name="password" class="form-control">
        </div>

        <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
