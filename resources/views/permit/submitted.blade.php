@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Submitted Permit Requests <small class="text-muted">(Grouped by Submission)</small></h1>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form id="filter-form" method="GET" action="{{ route('permits.submitted') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="date" name="date" class="form-control" 
                   value="{{ request('date', \Carbon\Carbon::today()->toDateString()) }}" 
                   onchange="document.getElementById('filter-form').submit();">
        </div>
        <div class="col-md-3">
            <input type="text" name="q" class="form-control" placeholder="Search by Company, ID, or Name" value="{{ request('q') }}">
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    @if($permits->count())
        @php
            $grouped = $permits->groupBy('submission_id');
        @endphp

        @foreach($grouped as $submissionId => $group)
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <strong>Submission ID:</strong> {{ $submissionId }}
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover m-0">
                            <thead class="table-light">
                                <tr>
                                    @if($group->first()->type === 'VP')
                                        <th>Vehicle Number</th>
                                        <th>Owner's Name</th>
                                        <th>Revenue License No.</th>
                                        <th>Insurance Number</th>
                                        <th>Company Name</th>
                                        <th>Issue Type</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Actions</th>
                                    @else
                                        <th>ID Type</th>
                                        <th>ID Number</th>
                                        <th>Full Name</th>
                                        <th>Company Name</th>
                                        <th>Pass Type</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                        <th>View</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group as $permit)
                                <tr>
                                    @if($permit->type === 'VP')
                                        <td>{{ $permit->vehicle_number }}</td>
                                        <td>{{ $permit->owner_name }}</td>
                                        <td>{{ $permit->revenue_license_number }}</td>
                                        <td>{{ $permit->insurance_number ?? '-' }}</td>
                                        <td>{{ $permit->company_name }}</td>
                                        <td>{{ ucfirst($permit->issue_type) }}</td>
                                        <td>{{ $permit->from_date }}</td>
                                        <td>{{ $permit->to_date }}</td>
                                    @else
                                        <td>{{ $permit->id_type }}</td>
                                        <td>{{ $permit->id_number }}</td>
                                        <td>{{ $permit->full_name }}</td>
                                        <td>{{ $permit->company_name }}</td>
                                        <td>{{ $permit->pass_type }}</td>
                                        <td>{{ $permit->from_date }}</td>
                                        <td>{{ $permit->to_date }}</td>
                                    @endif
                                    <td>
    @if($permit->status === 'active')
        <form action="{{ route('permits.cancel', $permit) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success">Active</button>
        </form>
    @else
        <form action="{{ route('permits.activate', $permit) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-danger">Cancelled</button>
        </form>
    @endif
</td>
         <td>
            <a href="{{ route('permits.edit', $permit) }}" class="btn btn-sm btn-warning">Edit</a>
           @if(Auth::user()->role === 'admin' || Auth::user()->role === 'super-admin')
        <form action="{{ route('permits.destroy', $permit) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this permit?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
        </form>
    @endif
             </td>
                <td>
                    <a href="{{ route('payment.invoice', $permit->submission_id) }}" class="btn btn-sm btn-info">View Group</a>
                     <a href="{{ route('permit.print.single', $permit->id) }}" target="_blank" class="btn btn-sm btn-primary">Print</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-center">
            {{ $permits->withQueryString()->links() }}
        </div>
    @else
        <div class="alert alert-info">
            No permits found.
        </div>
    @endif
</div>
@endsection
