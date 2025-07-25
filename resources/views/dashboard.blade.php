@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<!-- Custom Hover Styles -->
<style>
    .dashboard-card {
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
         border-right: 4px solid #6a9ed5ff;
    }
</style>

<div class="container">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('permit.temporary') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h4>Temporary Permit</h4>
                    <p>Create a new temporary permit request.</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('permit.monthly') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm  rounded-3 h-100">
                <div class="card-body">
                    <h4>Monthly Permit</h4>
                    <p>Create a new monthly permit request.</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('permit.vehicle') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm  rounded-3 h-100">
                <div class="card-body">
                    <h4>Vehicle Permit</h4>
                    <p>Create a new vehicle permit request.</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('permits.submitted') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h4>View all permit requests</h4>
                    <p>Permit List</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('blacklist.index') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm  rounded-3 h-100">
                <div class="card-body">
                    <h4>Edit BlackList</h4>
                    <p>BlackList</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('permits.submitted') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h4>Edit vehicle list</h4>
                    <p>Vehicle List</p>
                </div>
            </a>
        </div>

        <div class="col-md-4">
            <a href="{{ route('permits.submitted') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm  rounded-3 h-100">
                <div class="card-body">
                    <h4>Edit Payment information</h4>
                    <p>Payment calculation</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
