@extends('layouts.app')
@section('title', 'Add Reason')
@section('content')

<div class="container mt-4">
    <h3>Add Reason</h3>
    @include('admin.reasons._form', [
        'action' => route('admin.reasons.store'),
        'method' => 'POST'
    ])
</div>

@endsection
