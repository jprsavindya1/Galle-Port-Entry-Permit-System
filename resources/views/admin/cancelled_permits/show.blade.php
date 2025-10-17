@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    .user-dashboard-card { background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%); border-radius:1rem; box-shadow:0 3px 15px rgba(0,0,0,0.08); padding:1.25rem; }
    .user-dashboard-title { font-size:1.5rem; font-weight:600; color:#1976d2; margin-bottom:0.75rem; border-bottom:1px solid #bbdefb; padding-bottom:0.5rem; }
    .detail-table th { width:30%; background:#f1f9ff; color:#1976d2; font-weight:600; }
    .detail-table td { background:#fff; }
    .btn-secondary { background:#6c757d; border-color:#6c757d; color:#fff; border-radius:0.5rem; }
</style>

<div class="container py-4">
    <div class="user-dashboard-card mx-auto" style="max-width:900px;">
        <div class="user-dashboard-title"><i class="bi bi-file-earmark-x-fill me-2"></i> Cancelled Permit Details</div>

        <div class="table-responsive">
            <table class="table table-bordered detail-table">
                <tbody>
                @foreach($cancelledPermit->getAttributes() as $key => $value)
                    <tr>
                        <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            <a href="{{ route('admin.cancelled_permits.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left-circle me-1"></i> Back</a>
        </div>
    </div>
</div>

@endsection
