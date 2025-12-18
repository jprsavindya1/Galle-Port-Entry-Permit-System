@extends('layouts.app')

@section('content')
<style>
/* =====================================================
   CONTINUOUS DOT-MATRIX BATCH PRINT
   BASED ON WORKING SINGLE PERMIT PAGE
   ===================================================== */

/* Hide UI during print */
@media print {
    nav.navbar,
    .sidebar-slpa-logo,
    #printControls {
        display: none !important;
    }

    html, body, main {
        margin: 0 !important;
        padding: 0 !important;
    }
}

/* ❌ NO @page size — continuous tractor feed */

/* =====================================================
   PERMIT CONTAINER (UNCHANGED FROM SINGLE PRINT)
   ===================================================== */
.permit-container {
    position: relative;
    width: 719px;   /* 19cm @ 96 DPI */
    height: 340px;  /* 9cm @ 96 DPI */

    background-image: url('{{ asset('images/permit_background.png') }}');
    background-size: cover;
    background-repeat: no-repeat;

    font-family: Arial, sans-serif;

    /* 🔑 CRITICAL — keep these offsets */
    margin-top: -30px;
    margin-left: -80px;
}

/* =====================================================
   FIELD STYLES (UNCHANGED)
   ===================================================== */
.field {
    position: absolute;
    font-size: 16px;
    font-weight: bold;
}

/* =====================================================
   FIELD POSITIONS (UNCHANGED PX VALUES)
   ===================================================== */
.temporary-permit { top: 87px; left: 151px; }
.person-label     { top: 38px; left: 341px; }
.permit-title     { top: 87px; left: 359px; font-size: 20px; }
.permit-number    { top: 38px; left: 605px; }

.name             { top: 132px; left: 151px; }
.designation      { top: 132px; left: 416px; }

.company_name     { top: 170px; left: 151px; }
.from-date        { top: 95px;  left: 605px; }

.reason           { top: 215px; left: 151px; }
.to-date          { top: 132px; left: 605px; }

.id-type          { top: 170px; left: 605px; }

.total-amount     { top: 219px; left: 416px; }
.time             { top: 208px; left: 605px; font-size: 12px; }

.pass-type        { top: 253px; left: 321px; }

/* =====================================================
   SCREEN CONTROLS
   ===================================================== */
#printControls {
    margin: 20px;
    text-align: center;
}
#printControls button {
    margin: 0 10px;
    padding: 12px 24px;
    font-size: 16px;
    font-weight: 600;
    border-radius: 8px;
    cursor: pointer;
}
</style>

@foreach ($permits as $permit)

@php
    switch ($permit->type) {
        case 'MP':
            $title_en = 'Monthly Permit';
            $title_si = 'මාසික බලපත්‍රය';
            $person_label = 'Person පුද්ගල';
            break;

        case 'VH':
            $title_en = 'Temporary Permit';
            $title_si = 'තාවකාලික බලපත්‍රය';
            $person_label = 'Vehicle රථවාහන';
            break;

        default:
            $title_en = 'Temporary Permit';
            $title_si = 'තාවකාලික බලපත්‍රය';
            $person_label = 'Person පුද්ගල';
    }
@endphp

<div class="permit-container">

    <div class="field temporary-permit">{{ $title_en }}</div>
    <div class="field person-label">{{ $person_label }}</div>
    <div class="field permit-title">{{ $title_si }}</div>
    <div class="field permit-number">{{ $permit->permit_id }}</div>

    @if($permit->type === 'VH')
        <div class="field name">Owner: {{ $permit->owner_name }}</div>
        <div class="field company_name">{{ $permit->company_name }}</div>
        <div class="field from-date">
            From: {{ \Carbon\Carbon::parse($permit->from_date)->format('Y-m-d') }}
        </div>
        <div class="field id-type">
            Vehicle No: {{ $permit->vehicle_number }}
        </div>
        <div class="field to-date">
            To: {{ \Carbon\Carbon::parse($permit->to_date)->format('Y-m-d') }}
        </div>
        <div class="field reason">
            Reason: {{ $permit->reason }}
        </div>
    @else
        <div class="field name">{{ $permit->full_name }}</div>
        <div class="field designation">{{ $permit->designation }}</div>
        <div class="field company_name">{{ $permit->company_name }}</div>
        <div class="field from-date">
            {{ \Carbon\Carbon::parse($permit->from_date)->format('Y-m-d') }}
        </div>
        <div class="field to-date">
            {{ \Carbon\Carbon::parse($permit->to_date)->format('Y-m-d') }}
        </div>
        <div class="field id-type">{{ $permit->id_number }}</div>
        <div class="field reason">{{ $permit->reason }}</div>
    @endif

    <div class="field time">
        {{ \Carbon\Carbon::parse($permit->entry_time ?? now())->format('Y-m-d H:i') }}
    </div>

    <div class="field total-amount">
        {{ number_format($payment->amount_total ?? 0, 2) }}
    </div>

    <div class="field pass-type">
        {{ strtoupper($permit->pass_type ?? '') }}
    </div>

</div>

@endforeach

<!-- ============================
     SCREEN CONTROLS
     ============================ -->
<div id="printControls">
    <button onclick="window.print()">Print Again</button>

    <button onclick="window.location.href='{{ route('payment.invoice', ['submission_id' => $submission_id]) }}'">
        Back to Invoice
    </button>

    <button onclick="window.location.href='{{ route('dashboard') }}'">
        Home
    </button>
</div>

<script>
window.onload = function () {
    window.print();
};
</script>
@endsection
