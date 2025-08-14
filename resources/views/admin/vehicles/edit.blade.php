@extends('layouts.app')
@section('title', 'Edit Vehicle')
@section('content')
<div class="container mt-4">
    <h3>Edit Vehicle</h3>
    @include('admin.vehicles._form', [
        'action' => route('admin.vehicles.update', $vehicle),
        'method' => 'PUT',
        'vehicle' => $vehicle
    ])
</div>
@endsection
