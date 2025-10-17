@extends('layouts.app')
@section('title', 'Edit Company')
@section('content')
<div class="container py-4">
    <div class="user-dashboard-card mx-auto" style="max-width:700px;">
        <div class="user-dashboard-title"><i class="bi bi-building me-2"></i> Edit Company</div>

        @include('admin.companies._form', [
            'action' => route('admin.companies.update', $company),
            'method' => 'PUT',
            'company' => $company
        ])
    </div>
</div>
@endsection
