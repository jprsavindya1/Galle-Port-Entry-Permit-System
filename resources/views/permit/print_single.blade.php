<!DOCTYPE html>
<html>
<head>
    <title>Print Permit</title>
    <style>
        body { margin: 0; padding: 0; font-family: 'Arial', sans-serif; }
        .permit-container {
            position: relative;
            width: 1120px;
            height: 792px;
            background-image: url('{{ asset('images/permit_background.png') }}');
            background-size: cover;
            background-repeat: no-repeat;
            page-break-after: always;
        }
        .field { position: absolute; font-size: 16px; font-weight: bold; }

        .temporary-permit { top: 60px; left: 100px; }
        .person-label { top: 20px; left: 500px; }
        .permit-title { top: 60px; left: 400px; font-size: 20px; }
        .permit-number { top: 20px; right: 100px; }
        .name { top: 110px; left: 100px; }
        .designation { top: 110px; left: 500px; }
        .company_name { top: 160px; left: 100px; }
        .from-date { top: 70px; left: 900px; }
        .reason { top: 210px; left: 100px; }
        .to-date { top: 110px; left: 900px; }
        .id-type { top: 160px; left: 900px; }
        .total-amount { top: 210px; left: 600px; }
        .time { top: 200px; left: 900px; }
        .permit-type { top: 250px; left: 420px; font-size: 20px; font-weight: bold; }

        @media print { #printControls { display: none !important; } body { margin: 0; } }

        #printControls { margin: 20px; text-align: center; }
        #printControls button {
            margin: 0 10px;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        #printControls button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        #printControls button:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        #btnPrintAgain {
            background-color: #3b82f6;
            color: white;
        }
        #btnPrintAgain:hover {
            background-color: #2563eb;
        }
        #btnBackInvoice {
            background-color: #0ea5e9;
            color: white;
        }
        #btnBackInvoice:hover {
            background-color: #0284c7;
        }
        #btnBack {
            background-color: #06b6d4;
            color: white;
        }
        #btnBack:hover {
            background-color: #0891b2;
        }
        #btnHome {
            background-color: #1e40af;
            color: white;
        }
        #btnHome:hover {
            background-color: #1e3a8a;
        }
    </style>
</head>
<body>

@php
    switch ($permit->type) {
        case 'MP':
            $title_en = "Monthly Permit"; $title_si = "මාසික බලපත්‍රය"; $person_label = "Person පුද්ගල"; break;
        case 'VP':
            $title_en = "Temporary Permit"; $title_si = "තාවකාලික බලපත්‍රය"; $person_label = "Vehicle රථවාහන"; break;
        default:
            $title_en = "Temporary Permit"; $title_si = "තාවකාලික බලපත්‍රය"; $person_label = "Person පුද්ගල"; break;
    }
@endphp

<div class="permit-container">
    <div class="field temporary-permit">{{ $title_en }}</div>
    <div class="field person-label">{{ $person_label }}</div>
    <div class="field permit-title">{{ $title_si }}</div>
    <div class="field permit-number">{{ $permit->permit_id }}</div>

    @if($permit->type === 'VP')
        <div class="field name">Owner: {{ $permit->owner_name }}</div>
        <!--<div class="field designation">Address: {{ $permit->owner_address }}</div>-->
        <div class="field company_name">{{ $permit->company_name }}</div>
        <div class="field from-date">From: {{ $permit->from_date }}</div>
        <div class="field id-type">Vehicle No: {{ $permit->vehicle_number }}</div>
        <div class="field to-date">To: {{ $permit->to_date }}</div>
        <div class="field reason">Reason: {{ $permit->reason }}</div> <!-- display reason -->
    @else
        <div class="field name">{{ $permit->full_name }}</div>
        <div class="field designation">{{ $permit->designation }}</div>
        <div class="field company_name">{{ $permit->company_name ?? '' }}</div>
        <div class="field from-date">From: {{ $permit->from_date }}</div>
        <div class="field reason">{{ $permit->reason }}</div>
        <div class="field to-date">To: {{ $permit->to_date }}</div>
        <div class="field id-type">{{ $permit->id_type }} - {{ $permit->id_number }}</div>
    @endif

    <div class="field time">Time: {{ \Carbon\Carbon::parse($permit->entry_time ?? now())->format('H:i') }}</div>
    <div class="field total-amount">{{ number_format($payment->amount_total ?? 0, 2) }}</div>

    <!--@if($permit->type === 'VP')
        <div class="field permit-type">Remarks: {{ $permit->remarks }}</div>--> <!-- replace pass type with remarks -->
    <!--@else
        <div class="field permit-type">{{ strtoupper($permit->pass_type) }}</div>
    @endif-->
</div>

<div id="printControls">
    <button id="btnPrintAgain">Print Again</button>
    <button id="btnBackInvoice">Back to Invoice</button>
    @if($payment->permit_type === 'TP')
        <button id="btnBack">Back to Temporary Permit</button>
    @elseif($payment->permit_type === 'MP')
        <button id="btnBack">Back to Monthly Permit</button>
    @elseif($payment->permit_type === 'VP')
        <button id="btnBack">Back to Vehicle Permit</button>
    @else
        <button id="btnBack">Back</button>
    @endif
    <button id="btnHome">Home</button>
</div>

<script>
    window.onload = function() { window.print(); };
    document.getElementById('btnPrintAgain').addEventListener('click', () => window.print());
    document.getElementById('btnBack').addEventListener('click', function() {
        @if($payment->permit_type === 'TP')
            window.location.href = "{{ route('permit.temporary') }}";
        @elseif($payment->permit_type === 'MP')
            window.location.href = "{{ route('permit.monthly') }}";
        @else
            window.location.href = "{{ url()->previous() }}";
        @endif
    });
    document.getElementById('btnBackInvoice').addEventListener('click', function() {
        window.location.href = "{{ route('payment.invoice', ['submission_id' => $submission_id]) }}";
    });
    document.getElementById('btnHome').addEventListener('click', function() {
        window.location.href = "{{ route('dashboard') }}";
    });
</script>

</body>
</html>
