@extends('layouts.app')

@section('title', 'Payment Summary')

@section('content')
<div class="container mt-5">
    <h2 class="mb-4">Payment Summary for Submission ID: <strong>{{ $submissionId }}</strong></h2>

    @php
        $firstType = 'TP'; // default type
        if(!empty($detailedPayments) && isset($detailedPayments[0]['entry']['type'])) {
            $firstType = $detailedPayments[0]['entry']['type'];
        }
    @endphp

    {{-- ================= TEMPORARY & MONTHLY PERMITS ================= --}}
    @if ($firstType !== 'VP')
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
                    <th>SSL</th>
                    <th>VAT</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                    $rateTotal = 0;
                    $sslTotal = 0;
                    $vatTotal = 0;
                @endphp

                @foreach ($detailedPayments as $index => $payment)
                    @php
                        $entry = $payment['entry'];
                        $days = \Carbon\Carbon::parse($entry['from_date'])->diffInDays(\Carbon\Carbon::parse($entry['to_date'])) + 1;

                        $rate = $payment['rate'] ?? 0;
                        $ssl  = $payment['ssl'] ?? 0;
                        $vat  = $payment['vat'] ?? 0;

                        if ($entry['issue_type'] !== 'free') {
                            $rateTotal += $rate;
                            $sslTotal  += $ssl;
                            $vatTotal  += $vat;
                        }
                    @endphp
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $entry['full_name'] ?? 'N/A' }}</td>
                        <td>{{ $entry['id_type'] ?? '-' }}</td>
                        <td>{{ $entry['id_number'] ?? '-' }}</td>
                        <td>{{ $entry['from_date'] }}</td>
                        <td>{{ $entry['to_date'] }}</td>
                        <td>{{ $days }}</td>
                        <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($rate, 2) }}</td>
                        <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($ssl, 2) }}</td>
                        <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($vat, 2) }}</td>
                        <td><strong>{{ number_format($payment['total'], 2) }}</strong></td>
                        <td>
                            <form method="POST" action="{{ route('permit.remove', $index) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="text-end"><strong>Totals</strong></td>
                    <td><strong>{{ number_format($rateTotal, 2) }}</strong></td>
                    <td><strong>{{ number_format($sslTotal, 2) }}</strong></td>
                    <td><strong>{{ number_format($vatTotal, 2) }}</strong></td>
                    <td><strong>{{ number_format($totalPayment, 2) }}</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- ================= VEHICLE PERMITS ================= --}}
    @if ($firstType === 'VP')
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Owner Name</th>
                    <th>Vehicle Number</th>
                    <th>Revenue License</th>
                    <th>From</th>
                    <th>To</th>
                    <th>Days</th>
                    <th>Rate</th>
                    <th>SSL</th>
                    <th>VAT</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $i = 1;
                    $rateTotal = 0;
                    $sslTotal = 0;
                    $vatTotal = 0;
                @endphp

                @foreach ($detailedPayments as $index => $payment)
                    @php
                        $entry = $payment['entry'];
                        $days = \Carbon\Carbon::parse($entry['from_date'])->diffInDays(\Carbon\Carbon::parse($entry['to_date'])) + 1;

                        $rate = $payment['rate'] ?? 0;
                        $ssl  = $payment['ssl'] ?? 0;
                        $vat  = $payment['vat'] ?? 0;

                        if ($entry['issue_type'] !== 'free') {
                            $rateTotal += $rate;
                            $sslTotal  += $ssl;
                            $vatTotal  += $vat;
                        }
                    @endphp
                    <tr>
                        <td>{{ $i++ }}</td>
                        <td>{{ $entry['owner_name'] ?? 'N/A' }}</td>
                        <td>{{ $entry['vehicle_number'] ?? '-' }}</td>
                        <td>{{ $entry['revenue_license_number'] ?? '-' }}</td>
                        <td>{{ $entry['from_date'] }}</td>
                        <td>{{ $entry['to_date'] }}</td>
                        <td>{{ $days }}</td>
                        <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($rate, 2) }}</td>
                        <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($ssl, 2) }}</td>
                        <td>{{ $entry['issue_type'] === 'free' ? '0.00' : number_format($vat, 2) }}</td>
                        <td><strong>{{ number_format($payment['total'], 2) }}</strong></td>
                        <td>
                            <form method="POST" action="{{ route('permit.remove', $index) }}">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="7" class="text-end"><strong>Totals</strong></td>
                    <td><strong>{{ number_format($rateTotal, 2) }}</strong></td>
                    <td><strong>{{ number_format($sslTotal, 2) }}</strong></td>
                    <td><strong>{{ number_format($vatTotal, 2) }}</strong></td>
                    <td><strong>{{ number_format($totalPayment, 2) }}</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- ================= ACTION BUTTONS ================= --}}
    <form method="POST" action="{{ route('payment.submit') }}">
    @csrf
    <button type="submit" class="btn btn-success btn-lg">Confirm & Pay</button>
    @php
        // Determine the back route dynamically
        switch($firstType ?? 'TP') {
            case 'TP': $backRoute = route('permit.temporary'); break;
            case 'MP': $backRoute = route('permit.monthly'); break;
            case 'VP': $backRoute = route('permit.vehicle'); break;
            default: $backRoute = url()->previous();
        }
    @endphp
    <a href="{{ $backRoute }}" class="btn btn-secondary btn-lg ms-2">Cancel</a>
</form>

</div>
@endsection
