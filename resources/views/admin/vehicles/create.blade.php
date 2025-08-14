@extends('layouts.app')
@section('title', 'Add Vehicle')
@section('content')
<div class="container mt-4">
    <h3>Add New Vehicle</h3>
    @include('admin.vehicles._form', [
        'action' => route('admin.vehicles.store'),
        'method' => 'POST',
        'vehicle' => new \App\Models\Vehicle
    ])
</div>
@endsection
