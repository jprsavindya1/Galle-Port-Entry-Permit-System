@extends('layouts.app')

@section('title', 'Payment Invoice')

@section('content')
<div id="print-area" class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><strong>Payment Invoice - </h2><h5>{{ $payment->invoice_id }}</strong></h5>
        <button onclick="window.print()" class="btn btn-primary">Print Invoice</button>
    </div>

    <div class="card invoice-header mb-4 p-4">
         <h6>Submission ID: <strong>{{ $payment->submission_id }}</strong></h6>
        <h5>Invoice ID: {{ $payment->invoice_id }}</h5>
        <p class="mb-1">Payment Date: {{ $payment->paid_at->format('Y-m-d H:i') }}</p>
        <p class="mb-1">Permit Type: {{ $payment->permit_type }}</p>
        <p class="mb-1">Entry Count: {{ $payment->entry_count }}</p>
        <p>Status: <span class="badge bg-success">{{ $payment->status ?? 'Paid' }}</span></p>
    </div>

    <div class="card mb-4 p-3">
        <h5 class="mb-3">Permit Details</h5>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Permit ID</th>
                        <th>Full Name</th>
                        <th>ID Type</th>
                        <th>ID Number</th>
                        <th>Company</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Pass Type</th>
                        <th>Issue Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permits as $permit)
                    <tr>
                        <td>{{ $permit->permit_id }}</td>
                        <td>{{ $permit->full_name }}</td>
                        <td>{{ $permit->id_type }}</td>
                        <td>{{ $permit->id_number }}</td>
                        <td>{{ $permit->company_name }}</td>
                        <td>{{ \Carbon\Carbon::parse($permit->from_date)->format('Y-m-d') }}</td>
                        <td>{{ \Carbon\Carbon::parse($permit->to_date)->format('Y-m-d') }}</td>
                        <td>{{ ucfirst($permit->pass_type) }}</td>
                        <td>{{ ucfirst($permit->issue_type) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card payment-summary p-4">
        <h5 class="mb-3">Payment Summary</h5>
        <p><strong>Rate Total:</strong> Rs. {{ number_format($payment->rate_total, 2) }}</p>
        <p><strong>NBT ({{ DB::table('payment_settings')->value('nbt') }}%):</strong> Rs. {{ number_format($payment->nbt_total, 2) }}</p>
        <p><strong>VAT ({{ DB::table('payment_settings')->value('vat') }}%):</strong> Rs. {{ number_format($payment->vat_total, 2) }}</p>
        <h4 class="mt-3"><strong>Total Amount: Rs. {{ number_format($payment->amount_total, 2) }}</strong></h4>
    </div>

    <a href="{{ route('permit.temporary') }}" class="btn btn-secondary mt-4">Back to Temporary Permit Form</a>
</div>
@endsection

<style>
@media print {
    /* Hide everything */
    body * {
        visibility: hidden !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    /* Except print-area */
    #print-area, #print-area * {
        visibility: visible !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }

    /* Position print-area at top left */
    #print-area {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        padding: 1cm !important;
        background: white !important;
    }

    /* Remove print buttons */
    #print-area .btn {
        display: none !important;
    }

    /* Table formatting */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 12pt;
    }

    th, td {
        border: 1px solid black !important;
        padding: 6px !important;
        text-align: left;
    }

    thead {
        display: table-header-group !important;
        background-color: #f0f0f0 !important;
    }

    tr {
        page-break-inside: avoid !important;
    }
}
</style>
