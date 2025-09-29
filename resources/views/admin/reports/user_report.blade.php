@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">User / Entity Permit Report</h4>

    <!-- Filters -->
    <form method="GET" action="{{ route('reports.user') }}" class="row g-2 mb-3">
        <div class="col-md-4">
            <input type="text" name="query" class="form-control" placeholder="Enter NIC / Name / Company" value="{{ request('query') }}">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="TP" {{ request('type')=='TP'?'selected':'' }}>TP</option>
                <option value="MP" {{ request('type')=='MP'?'selected':'' }}>MP</option>
                <option value="VP" {{ request('type')=='VP'?'selected':'' }}>VP</option>
            </select>
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Search</button>
        </div>
    </form>

    @if(isset($permits) && $permits->isNotEmpty())
        <!-- Export Buttons -->
        <div class="mb-3 d-flex justify-content-end">
            <a href="{{ route('reports.user.pdf', request()->query()) }}" class="btn btn-sm btn-danger me-2" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
            <a href="{{ route('reports.user.csv', request()->query()) }}" class="btn btn-sm btn-success" target="_blank">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
        </div>

        @php $grouped = $permits->groupBy('type'); @endphp

        <!-- Table per Permit Type -->
        @foreach($grouped as $type => $permitsByType)
            <div class="mb-4">
                <h5 class="mb-3">{{ $type }} Permits</h5>

                <!-- Summary Totals -->
                @php
                    $summary = [
                        'count' => $permitsByType->count(),
                        'active' => $permitsByType->where('status','Active')->count(),
                        'cancelled' => $permitsByType->where('status','Cancelled')->count(),
                    ];
                @endphp
                <div class="card mb-2">
                    <div class="card-body">
                        <p>
                            Total: {{ $summary['count'] }} |
                            Active: {{ $summary['active'] }} |
                            Cancelled: {{ $summary['cancelled'] }}
                        </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Permit ID</th>
                                @if($type === 'VP')
                                    <th>Owner Name</th>
                                    <th>Vehicle Number</th>
                                @else
                                    <th>Full Name</th>
                                    <th>ID Number</th>
                                @endif
                                <th>Company Name</th>
                                <th>From Date</th>
                                <th>To Date</th>
                                <th>Issue Type</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Submission ID</th>
                                <th>Invoice ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permitsByType as $permit)
                                <tr>
                                    <td>{{ $permit->permit_id }}</td>
                                    @if($type === 'VP')
                                        <td>{{ $permit->owner_name }}</td>
                                        <td>{{ $permit->vehicle_number }}</td>
                                    @else
                                        <td>{{ $permit->full_name }}</td>
                                        <td>{{ $permit->id_number }}</td>
                                    @endif
                                    <td>{{ $permit->company_name }}</td>
                                    <td>{{ $permit->from_date }}</td>
                                    <td>{{ $permit->to_date }}</td>
                                    <td>{{ ucfirst($permit->issue_type) }}</td>
                                    <td>{{ $permit->reason }}</td>
                                    <td>{{ $permit->status }}</td>
                                    <td>{{ $permit->submission_id }}</td>
                                    <td>{{ $permit->payment->invoice_id ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    @elseif(request()->has('query') || request()->has('type'))
        <div class="alert alert-warning">No permits found for the given criteria.</div>
    @endif
</div>
@endsection
