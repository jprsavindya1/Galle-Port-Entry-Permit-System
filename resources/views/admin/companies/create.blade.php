@extends('layouts.app')
@section('title', 'Add Company')
@section('content')
<div class="container py-4">
    <div class="user-dashboard-card mx-auto" style="max-width:700px;">
        <div class="user-dashboard-title"><i class="bi bi-building me-2"></i> Add New Company</div>

        @include('admin.companies._form', [
            'action' => route('admin.companies.store'),
            'method' => 'POST',
            'company' => new \App\Models\Company
        ])
    </div>
</div>
@endsection
