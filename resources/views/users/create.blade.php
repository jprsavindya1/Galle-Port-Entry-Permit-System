@extends('layouts.app')

@section('title', 'Create User')

@section('content')
<div class="container mt-5">
    <h2>Create New User</h2>

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

    <form action="{{ route('users.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
        </div>

        <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-control" required>
                <option value="clerk" @selected(old('role') === 'clerk')>Clerk</option>
                @if(auth()->user()->role === 'super-admin' || auth()->user()->role === 'admin')
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                @endif
                @if(auth()->user()->role === 'super-admin')
                    <option value="super-admin" @selected(old('role') === 'super-admin')>Super Admin</option>
                @endif
            </select>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success">Create User</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
