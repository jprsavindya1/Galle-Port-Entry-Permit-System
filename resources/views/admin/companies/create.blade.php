@extends('layouts.app')
@section('title', 'Add Company')
@section('content')
<div class="container mt-4">
    <h3>Add New Company</h3>
    @include('admin.companies._form', [
        'action' => route('admin.companies.store'),
        'method' => 'POST',
        'company' => new \App\Models\Company
    ])
</div>
@endsection
