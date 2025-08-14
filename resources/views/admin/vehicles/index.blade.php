@extends('layouts.app')
@section('title', 'Vehicle List')

@section('content')
<div class="container mt-4">
    <h3>Vehicle List</h3>
    <div id="dynamic-content">
        @include('admin.vehicles._list', ['vehicles' => $vehicles])
    </div>
</div>
@endsection
