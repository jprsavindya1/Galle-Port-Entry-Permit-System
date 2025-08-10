@extends('layouts.app')
@section('title', 'Edit Company')
@section('content')
<div class="container mt-4">
    <h3>Edit Company</h3>
    @include('admin.companies._form', [
        'action' => route('admin.companies.update', $company),
        'method' => 'PUT',
        'company' => $company
    ])
</div>
@endsection
