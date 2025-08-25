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

        /* Hide buttons during print */
        @media print {
            #printControls {
                display: none !important;
            }
            body {
                margin: 0;
            }

            .permit-container {
                page-break-after: always; /* Print each permit on a new segment */
            }
        }

        /* Container for print controls */
        #printControls {
            margin: 20px;
            text-align: center;
        }
        #printControls button {
            margin: 0 10px;
            padding: 10px 20px;
            font-size: 16px;
        }
        <style>
    /* Container for print controls */
    #printControls {
        margin: 20px 0;
        text-align: center;
    }

    #printControls button {
        margin: 0 12px;
        padding: 12px 28px;
        font-size: 16px;
        font-weight: 600;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        box-shadow: 0 3px 6px rgba(0,0,0,0.16);
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
        color: #fff;
    }

    /* Print Again - Primary style (blue) */
    #btnPrintAgain {
        background-color: #0d6efd; /* Bootstrap primary blue */
    }
    #btnPrintAgain:hover {
        background-color: #0b5ed7;
        box-shadow: 0 5px 12px rgba(13, 110, 253, 0.4);
    }

    /* Back button - secondary style (gray) */
    #btnBack {
        background-color: #6c757d; /* Bootstrap secondary gray */
    }
    #btnBack:hover {
        background-color: #5c636a;
        box-shadow: 0 5px 12px rgba(108, 117, 125, 0.4);
    }
    /* Back button - secondary style (gray) */
    #btnBackInvoice{
        background-color: #5e8eb8ff; /* Bootstrap secondary gray */
    }
    #btnBackInvoice:hover {
        background-color: #5c636a;
        box-shadow: 0 5px 12px rgba(108, 117, 125, 0.4);
    }
</style>

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

            <div class="field name">{{ $permit->full_name }}</div>
            <div class="field designation">{{ $permit->designation }}</div>

            <div class="field company_name">{{ $permit->company_name ?? $permit->company_name }}</div>
            <div class="field from-date">from {{ $permit->from_date }}</div>

            <div class="field reason">{{ $permit->reason }}</div>
            <div class="field to-date">To: {{ $permit->to_date }}</div>

            <div class="field id-type">{{ $permit->id_type }} - {{ $permit->id_number }}</div>
            <div class="field time">Time: {{ \Carbon\Carbon::parse($permit->entry_time ?? now())->format('H:i') }}</div>

            <div class="field total-amount">{{ number_format($payment->amount_total ?? 0, 2) }}</div>
            <div class="field permit-type">{{ strtoupper($permit->pass_type) }}</div>
        </div>
    @endforeach

    <!-- Print Controls -->
    <div id="printControls">
        <button id="btnPrintAgain">Print Again</button>
        <button id="btnBackInvoice">Back to Invoice Page</button>
        <button id="btnBack">Back to Temporary Permit Form</button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };

        document.getElementById('btnPrintAgain').addEventListener('click', function() {
            window.print();
        });

        document.getElementById('btnBack').addEventListener('click', function() {
            window.location.href = "{{ route('permit.temporary') }}";
        });
        document.getElementById('btnBackInvoice').addEventListener('click', function() {
            window.location.href = "{{ route('payment.invoice', ['submission_id' => $submission_id]) }}";
        });

    </script>
</body>
</html>
