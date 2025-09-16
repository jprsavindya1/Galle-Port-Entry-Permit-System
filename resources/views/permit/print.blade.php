<!DOCTYPE html>
<html>
<head>
    <title>Print Permit</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Arial', sans-serif;
        }

        .permit-container {
            position: relative;
            width: 1120px;
            height: 792px; /* One permit = one section on roll */
            background-image: url('{{ asset('images/permit_background.png') }}');
            background-size: cover;
            background-repeat: no-repeat;
            page-break-after: always; /*continuous printing, one permit per "page" */
        }

        .field {
            position: absolute;
            font-size: 16px;
            font-weight: bold;
        }

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

        @media print {
            #printControls { display: none !important; }
            body { margin: 0; }
            .permit-container { page-break-after: always; }
        }

        #printControls {
            margin: 20px;
            text-align: center;
        }
        #printControls button {
            margin: 0 10px;
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>

@foreach ($permits as $permit)

@php
    switch ($permit->type) {
        case 'MP':
            $title_en = "Monthly Permit";
            $title_si = "මාසික බලපත්‍රය";
            $person_label = "Person පුද්ගල";
            break;

        case 'VP':
            $title_en = "Vehicle Permit";
            $title_si = "රථවාහන බලපත්‍රය";
            $person_label = "Vehicle රථවාහන";
            break;

        default: // TP
            $title_en = "Temporary Permit";
            $title_si = "තාවකාලික බලපත්‍රය";
            $person_label = "Person පුද්ගල";
            break;
    }
@endphp

<div class="permit-container">
    <div class="field temporary-permit">{{ $title_en }}</div>
    <div class="field person-label">{{ $person_label }}</div>
    <div class="field permit-title">{{ $title_si }}</div>
    <div class="field permit-number">{{ $permit->permit_id }}</div>

    @if($permit->type === 'VP')
    <!-- Vehicle Permit Layout -->
    <div class="field name">{{ $permit->owner_name }}</div>
    <div class="field designation">Address: {{ $permit->owner_address }}</div>

    <div class="field company_name">{{ $permit->company_name }}</div>
    <div class="field from-date">From: {{ $permit->from_date }}</div>

    <div class="field id-type">Vehicle No: {{ $permit->vehicle_number }}</div>
    <div class="field to-date">To: {{ $permit->to_date }}</div>

    <div class="field reason">{{ $permit->reason }}</div> <!-- added reason -->
@else
    <!-- Person-Oriented Layout (TP / MP) -->
    <div class="field name">{{ $permit->full_name }}</div>
    <div class="field designation">{{ $permit->designation }}</div>

    <div class="field company_name">{{ $permit->company_name ?? '' }}</div>
    <div class="field from-date">From: {{ $permit->from_date }}</div>

    <div class="field reason">{{ $permit->reason }}</div>
    <div class="field to-date">To: {{ $permit->to_date }}</div>

    <div class="field id-type">{{ $permit->id_type }} - {{ $permit->id_number }}</div>
@endif


    <div class="field time">
        Time: {{ \Carbon\Carbon::parse($permit->entry_time ?? now())->format('H:i') }}
    </div>

    <div class="field total-amount">{{ number_format($payment->amount_total ?? 0, 2) }}</div>

    @if($permit->type === 'VP')
        <!-- For Vehicle permits: show Remarks instead of Pass Type -->
        <div class="field permit-type">Remarks: {{ $permit->remarks }}</div>
    @else
        <!-- For Temporary / Monthly permits: keep Pass Type -->
        <div class="field permit-type">{{ strtoupper($permit->pass_type) }}</div>
    @endif
</div>

@endforeach

<!-- Print Controls -->
<div id="printControls">
    <button id="btnPrintAgain">Print Again</button>
    <button id="btnBackInvoice">Back to Invoice Page</button>

    @if($payment->permit_type === 'TP')
        <button id="btnBack">Back to Temporary Permit Form</button>
    @elseif($payment->permit_type === 'MP')
        <button id="btnBack">Back to Monthly Permit Form</button>
    @else
        <button id="btnBack">Back</button>
    @endif
</div>

<script>
    window.onload = function() { window.print(); };

    document.getElementById('btnPrintAgain').addEventListener('click', function() {
        window.print();
    });

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
</script>
</body>
</html>
