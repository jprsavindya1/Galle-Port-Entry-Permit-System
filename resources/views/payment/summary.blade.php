@extends('layouts.app')

@section('title', 'Payment Summary')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Payment Summary for Submission ID: <strong>{{ $submissionId }}</strong></h2>

    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Full Name</th>
                <th>ID Type</th>
                <th>ID Number</th>
                <th>From</th>
                <th>To</th>
                <th>Days</th>
                <th>Rate</th>
                <th>NBT</th>
                <th>VAT</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $i = 1;
                $rateTotal = 0;
                $nbtTotal = 0;
                $vatTotal = 0;
            @endphp

            @foreach ($detailedPayments as $payment)
                @php
                    $entry = $payment['entry'];
                    $days = \Carbon\Carbon::parse($entry['from_date'])->diffInDays(\Carbon\Carbon::parse($entry['to_date'])) + 1;

                    // Accumulate only if not free
                    if ($entry['issue_type'] !== 'free') {
                        $rateTotal += $payment['rate'];
                        $nbtTotal += $payment['nbt'];
                        $vatTotal += $payment['vat'];
                    }
                @endphp
                <tr>
                    <td>{{ $i++ }}</td>
                    <td>{{ $entry['full_name'] }}</td>
                    <td>{{ $entry['id_type'] }}</td>
                    <td>{{ $entry['id_number'] }}</td>
                    <td>{{ $entry['from_date'] }}</td>
                    <td>{{ $entry['to_date'] }}</td>
                    <td>{{ $days }}</td>
                    <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($payment['rate'], 2) }}</td>
                    <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($payment['nbt'], 2) }}</td>
                    <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($payment['vat'], 2) }}</td>
                    <td><strong>{{ number_format($payment['total'], 2) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
    <tr>
        <td colspan="10" class="text-end"><strong>Total Amount</strong></td>
        <td><strong>{{ number_format($totalPayment, 2) }}</strong></td>
    </tr>
</tfoot>

    </table>

    <form method="POST" action="{{ route('payment.submit') }}">
        @csrf
        <button type="submit" class="btn btn-success btn-lg">Confirm & Pay</button>
        <a href="{{ route('permit.temporary') }}" class="btn btn-secondary btn-lg ms-2">Cancel</a>
    </form>
</div>
@endsection
