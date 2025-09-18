@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">User / Entity Permit Report</h4>

    <!-- Search & Filters -->
    <div class="card shadow-sm mb-4">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between gap-2">
            
            <!-- Left: Search Form + Permit Type Filter -->
            <form action="{{ route('reports.user') }}" method="GET" class="d-flex gap-2 align-items-center" style="min-width: 250px; flex-grow:1;">
                <input type="text" name="query" class="form-control" placeholder="Enter NIC / Name / Company" value="{{ $query ?? '' }}" style="flex:1; min-width:150px;">

                <select name="type" class="form-select" style="width:120px; height:38px;">
                    <option value="">All Types</option>
                    <option value="TP" {{ (isset($type) && $type=='TP')?'selected':'' }}>TP</option>
                    <option value="MP" {{ (isset($type) && $type=='MP')?'selected':'' }}>MP</option>
                    <option value="VP" {{ (isset($type) && $type=='VP')?'selected':'' }}>VP</option>
                </select>

                <button type="submit" class="btn btn-primary" style="height:38px;">Search</button>
            </form>

            <!-- Right: Export / Print Buttons (reserve space even if empty) -->
            <div class="d-flex gap-2 flex-wrap ms-auto" style="min-width:240px;">
                @if(isset($permits) && $permits->isNotEmpty())
                    <a href="{{ route('reports.user.pdf', ['query' => $query ?? '', 'type' => $type ?? '']) }}" target="_blank" class="btn btn-danger">Export PDF</a>
                    <a href="{{ route('reports.user.csv', ['query' => $query ?? '', 'type' => $type ?? '']) }}" class="btn btn-success">Export CSV</a>
                @else
                    <!-- empty placeholder to keep layout -->
                    <span style="display:inline-block; width:100%;"></span>
                @endif
            </div>

        </div>
    </div>

   <!-- Results -->
@if(isset($permits) && $permits->isNotEmpty())
    @php $grouped = $permits->groupBy('type'); @endphp

    @foreach($grouped as $type => $permitsByType)
        <div class="mb-4">
            <h5 class="mb-3">{{ $type }} Permits</h5>
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
