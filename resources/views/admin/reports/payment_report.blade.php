@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Payment / Batch Report</h4>

    <!-- Filters -->
    <form method="GET" action="{{ route('reports.payment') }}" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="type" class="form-select">
                <option value="">All Types</option>
                <option value="TP" {{ request('type')=='TP'?'selected':'' }}>TP</option>
                <option value="MP" {{ request('type')=='MP'?'selected':'' }}>MP</option>
                <option value="VP" {{ request('type')=='VP'?'selected':'' }}>VP</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="range" class="form-select">
                <option value="">All Time</option>
                <option value="day" {{ request('range')=='day'?'selected':'' }}>Day</option>
                <option value="week" {{ request('range')=='week'?'selected':'' }}>Week</option>
                <option value="month" {{ request('range')=='month'?'selected':'' }}>Month</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="date" name="date" class="form-control" value="{{ request('date') }}">
        </div>
        <div class="col-md-3">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

<!-- Export Buttons -->
<div class="mb-3 d-flex justify-content-end">
    <a href="{{ route('reports.payment.pdf', request()->query()) }}" 
       class="btn btn-sm btn-danger me-2" target="_blank">
        <i class="fas fa-file-pdf"></i> Export PDF
    </a>
    <a href="{{ route('reports.payment.csv', request()->query()) }}" 
       class="btn btn-sm btn-success" target="_blank">
        <i class="fas fa-file-csv"></i> Export CSV
    </a>
</div>

    <!-- Summary -->
    <div class="card mb-3">
        <div class="card-body">
            <h6>Summary Totals</h6>
            <p>Rate: {{ number_format($summary['rate_total'],2) }} |
               SSL: {{ number_format($summary['ssl_total'],2) }} |
               VAT: {{ number_format($summary['vat_total'],2) }} |
               Total: <strong>{{ number_format($summary['amount_total'],2) }}</strong>
            </p>
        </div>
    </div>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Submission ID</th>
                    <th>Permit Type</th>
                    <th>Company</th>
                    <th>Entry Count</th>
                    <th>Rate</th>
                    <th>SSL</th>
                    <th>VAT</th>
                    <th>Total</th>
                    <th>Payment Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $p)
                    <tr>
                        <td>{{ $p->invoice_id }}</td>
                        <td>{{ $p->submission_id }}</td>
                        <td>{{ $p->permit_type }}</td>
                        <td>{{ $p->permits->first()->company_name ?? '-' }}</td>
                        <td>{{ $p->entry_count }}</td>
                        <td>{{ number_format($p->rate_total,2) }}</td>
                        <td>{{ number_format($p->ssl_total,2) }}</td>
                        <td>{{ number_format($p->vat_total,2) }}</td>
                        <td><strong>{{ number_format($p->amount_total,2) }}</strong></td>
                        <td>{{ $p->payment_date->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
