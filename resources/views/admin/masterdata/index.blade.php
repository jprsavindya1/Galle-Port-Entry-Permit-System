@extends('layouts.app')

@section('title', 'Edit Master Data')
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
    .dashboard-card h4 {
    font-weight: 700; 
    font-size: 1.2rem;
}
</style>
@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Edit Master Data</h2>
    <div class="row g-4">

        <div class="col-md-3">
            <a href="{{ route('admin.companies.index') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h4>Companies</h4>
                    <p>Manage company info</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.designations.index') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h4>Designations</h4>
                    <p>Manage job titles</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('admin.companies.index') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h4>Entry Reasons</h4>
                    <p>Manage entry purposes</p>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="{{ route('permits.submitted') }}" class="card dashboard-card text-center text-decoration-none text-dark shadow-sm rounded-3 h-100">
                <div class="card-body">
                    <h4>Vehicle List</h4>
                    <p>Manage vehicle records</p>
                </div>
            </a>
        </div>

    </div>
</div>
@endsection
