@extends('layouts.app')
@section('title', 'Edit Reason')
@section('content')

<div class="container mt-4">
    <h3>Edit Reason</h3>
    @include('admin.reasons._form', [
        'action' => route('admin.reasons.update', $reason->id),
        'method' => 'PUT',
        'reason' => $reason
    ])
</div>

@endsection
