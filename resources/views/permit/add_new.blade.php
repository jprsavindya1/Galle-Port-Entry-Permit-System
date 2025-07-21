@extends('layouts.app')

@section('title', 'Add New Permit')

@section('content')
<div class="container">
    <h2 class="mb-4">Add New Permit</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <a href="{{ route('permit.temporary') }}" class="card text-center text-decoration-none text-dark shadow-sm">
                <div class="card-body">
                    <h4>Temporary Permit</h4>
                    <p>Create a new temporary permit request.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('permit.monthly.create') }}" class="card text-center text-decoration-none text-dark shadow-sm">
                <div class="card-body">
                    <h4>Monthly Permit</h4>
                    <p>Create a new monthly permit request.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a href="{{ route('permit.vehicle.create') }}" class="card text-center text-decoration-none text-dark shadow-sm">
                <div class="card-body">
                    <h4>Vehicle Permit</h4>
                    <p>Create a new vehicle permit request.</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
