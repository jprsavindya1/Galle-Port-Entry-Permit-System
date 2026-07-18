@extends('layouts.app')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;700&display=swap');

    /* Hide nav and sidebar during print */
    @media print {
        nav.navbar, .sidebar-slpa-logo, #printControls { display: none !important; }
        main { padding: 0 !important; margin: 0 !important; }
        html, body { margin: 0 !important; padding: 0 !important; background: none !important; }
        .permit-container { page-break-after: avoid; box-shadow: none !important; border: 2px solid #000 !important; margin: 0 !important; }
    }

    @page {
        size: letter portrait;
        margin: 10mm;
    }

    html, body {
        background-color: #f3f4f6;
        font-family: 'Noto Sans Sinhala', 'Arial', sans-serif;
    }

    .permit-container {
        width: 750px;
        background-color: #fff;
        border: 2px solid #000;
        margin: 20px auto;
        padding: 0;
        box-sizing: border-box;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        color: #000;
    }

    .permit-header {
        display: flex;
        align-items: center;
        border-bottom: 2px solid #000;
        padding: 10px;
    }

    .header-logo {
        width: 80px;
        height: auto;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .header-logo img {
        max-width: 100%;
        max-height: 75px;
    }

    .header-title-container {
        flex: 1;
        text-align: center;
        padding: 0 10px;
    }

    .header-title-si {
        font-size: 18px;
        font-weight: bold;
        margin: 0;
        line-height: 1.2;
    }

    .header-title-en {
        font-size: 14px;
        font-weight: bold;
        margin: 2px 0 0 0;
        letter-spacing: 0.5px;
    }

    .header-port-si-en {
        font-size: 13px;
        font-weight: normal;
        margin: 3px 0 0 0;
    }

    .header-type-si-en {
        font-size: 16px;
        font-weight: bold;
        margin: 5px 0 0 0;
        line-height: 1.2;
    }

    .header-right-meta {
        width: 120px;
        display: flex;
        justify-content: flex-end;
    }

    .category-badge {
        border: 2px solid #000;
        padding: 6px 12px;
        font-weight: bold;
        text-align: center;
        background-color: #fff;
    }

    .permit-main-body {
        display: flex;
        border-bottom: 2px solid #000;
    }

    .body-left-col {
        width: 63%;
        box-sizing: border-box;
    }

    .body-right-col {
        width: 37%;
        border-left: 2px solid #000;
        box-sizing: border-box;
    }

    .grid-table, .metadata-table {
        width: 100%;
        border-collapse: collapse;
    }

    .grid-table td {
        border-bottom: 1px solid #000;
        padding: 8px 10px;
        height: 52px;
        box-sizing: border-box;
        vertical-align: middle;
    }

    .grid-table tr:last-child td {
        border-bottom: none;
    }

    .label-cell {
        width: 32%;
        border-right: 1px solid #000;
        font-size: 11px;
        line-height: 1.2;
        padding: 5px !important;
    }

    .label-si {
        display: block;
        font-weight: bold;
        color: #000;
    }

    .label-en {
        display: block;
        color: #555;
        font-weight: bold;
    }

    .value-cell {
        width: 68%;
        font-size: 14px;
        font-weight: bold;
        vertical-align: middle;
        padding-left: 12px !important;
    }

    .designation-sub {
        font-size: 11px;
        font-weight: normal;
        color: #444;
        margin-left: 5px;
    }

    .value-fee {
        font-size: 15px;
        color: #000;
    }

    /* Metadata Table Styles */
    .metadata-table td {
        border-bottom: 1px solid #000;
        padding: 6px 10px;
        font-size: 11.5px;
        box-sizing: border-box;
        height: 34.6px;
        vertical-align: middle;
    }

    .metadata-table tr:last-child td {
        border-bottom: none;
    }

    .meta-cell {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .meta-period-header {
        background-color: #fafafa;
        font-weight: bold;
        text-align: center;
        justify-content: center !important;
    }

    .label-si-inline {
        font-weight: bold;
    }

    .value-text-bold {
        font-weight: bold;
        font-size: 13px;
        text-align: right;
    }

    .highlight-red {
        color: #d32f2f;
        font-size: 14px;
    }

    /* Bottom Row layout */
    .permit-bottom-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        padding: 15px 12px 10px 12px;
        box-sizing: border-box;
    }

    .bottom-signature-left {
        width: 190px;
        text-align: center;
    }

    .bottom-checkboxes-mid {
        width: 270px;
        display: flex;
        justify-content: center;
    }

    .bottom-signature-right {
        width: 190px;
        text-align: center;
    }

    .signature-line {
        border-top: 1px solid #000;
        margin-bottom: 5px;
        height: 1px;
        width: 100%;
    }

    .signature-label {
        font-size: 11px;
        line-height: 1.2;
    }

    .signature-label-si {
        font-weight: bold;
        display: block;
    }

    .signature-label-en {
        color: #555;
        font-weight: bold;
        display: block;
    }

    /* Checkboxes box styling */
    .pass-checkboxes-box {
        display: flex;
        border: 2px solid #000;
        width: 250px;
        height: 55px;
        box-sizing: border-box;
    }

    .pass-option-col {
        flex: 1;
        border-right: 1px solid #000;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: center;
        padding: 2px 4px;
        position: relative;
        text-align: center;
        background-color: #fff;
    }

    .pass-option-col:last-child {
        border-right: none;
    }

    .pass-label-top {
        font-size: 10px;
        font-weight: bold;
        line-height: 1;
    }

    .pass-label-bottom {
        font-size: 8.5px;
        font-weight: bold;
        color: #444;
        line-height: 1;
    }

    .pass-cross-overlay {
        font-size: 24px;
        font-weight: bold;
        color: #000;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        letter-spacing: -1.5px;
        user-select: none;
        line-height: 1;
    }

    .act-footer {
        border-top: 2px solid #000;
        padding: 5px 10px;
        font-size: 8.5px;
        text-align: center;
        line-height: 1.3;
        background-color: #fdfdfd;
    }

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

@php
    switch ($permit->type) {
        case 'MP':
            $title_en = "Monthly Permit"; 
            $title_si = "මාසික බලපත්‍රය"; 
            break;
        case 'VH':
            $title_en = "Temporary Permit"; 
            $title_si = "තාවකාලික බලපත්‍රය"; 
            break;
        default:
            $title_en = "Temporary Permit"; 
            $title_si = "තාවකාලික බලපත්‍රය"; 
            break;
    }
@endphp

<div class="permit-container">
    <!-- Header -->
    <div class="permit-header">
        <div class="header-logo">
            <img src="{{ asset('images/Sri_Lanka_Ports_Authority_logo.png') }}" alt="SLPA Logo">
        </div>
        <div class="header-title-container">
            <h1 class="header-title-si">ශ්‍රී ලංකා වරාය අධිකාරිය</h1>
            <h2 class="header-title-en">SRI LANKA PORTS AUTHORITY</h2>
            <div class="header-port-si-en">ගාලු වරාය - Port of Galle</div>
            <div class="header-type-si-en">{{ $title_si }} - {{ $title_en }}</div>
        </div>
        <div class="header-right-meta">
            <div class="category-badge">
                <div style="font-size: 13px; font-weight: bold; line-height: 1.2;">{{ $permit->type === 'VH' ? 'රථවාහන' : 'පුද්ගල' }}</div>
                <div style="font-size: 11px; font-weight: bold; text-transform: uppercase;">{{ $permit->type === 'VH' ? 'Vehicle' : 'Person' }}</div>
            </div>
        </div>
    </div>

    <!-- Main Grid Layout -->
    <div class="permit-main-body">
        <!-- Left Column (Grid Details) -->
        <div class="body-left-col">
            <table class="grid-table">
                <tr>
                    <td class="label-cell">
                        <span class="label-si">නම</span>
                        <span class="label-en">Name</span>
                    </td>
                    <td class="value-cell">
                        {{ $permit->type === 'VH' ? 'Owner: ' . $permit->owner_name : $permit->full_name }}
                        @if($permit->type !== 'VH' && !empty($permit->designation))
                            <span class="designation-sub">({{ $permit->designation }})</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label-cell">
                        <span class="label-si">ආයතනයේ නම</span>
                        <span class="label-en">Company Name</span>
                    </td>
                    <td class="value-cell">{{ $permit->company_name ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">
                        <span class="label-si">කාර්යයේ විස්තරය</span>
                        <span class="label-en">Reason</span>
                    </td>
                    <td class="value-cell">{{ $permit->reason ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label-cell">
                        <span class="label-si">බලපත්‍ර ගාස්තුව</span>
                        <span class="label-en">Permit Fee</span>
                    </td>
                    <td class="value-cell value-fee">Rs. {{ number_format($payment->amount_total ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>

        <!-- Right Column (Metadata) -->
        <div class="body-right-col">
            <table class="metadata-table">
                <tr>
                    <td class="meta-cell">
                        <span class="label-si-inline">බලපත්‍ර අංකය / <span class="label-en">Permit No</span></span>
                        <span class="value-text-bold highlight-red">{{ $permit->permit_id }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="meta-cell meta-period-header">
                        <span class="label-si-inline">වලංගු කාලය / <span class="label-en">VALID PERIOD</span></span>
                    </td>
                </tr>
                <tr>
                    <td class="meta-cell">
                        <span class="label-si-inline">දින සිට / <span class="label-en">From</span></span>
                        <span class="value-text-bold">{{ \Carbon\Carbon::parse($permit->from_date)->format('d-M-Y') }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="meta-cell">
                        <span class="label-si-inline">දින දක්වා / <span class="label-en">To</span></span>
                        <span class="value-text-bold">{{ \Carbon\Carbon::parse($permit->to_date)->format('d-M-Y') }}</span>
                    </td>
                </tr>
                <tr>
                    <td class="meta-cell">
                        @if($permit->type === 'VH')
                            <span class="label-si-inline">වාහන අංකය / <span class="label-en">Vehicle No</span></span>
                            <span class="value-text-bold">{{ $permit->vehicle_number }}</span>
                        @else
                            <span class="label-si-inline">රා.හැ.අ. අංකය / <span class="label-en">N.I.C. No</span></span>
                            <span class="value-text-bold">{{ $permit->id_number }}</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="meta-cell">
                        <span class="label-si-inline">නි.ක. වේලාව / <span class="label-en">Time of Issue</span></span>
                        <span class="value-text-bold">{{ \Carbon\Carbon::parse($permit->entry_time ?? $permit->created_at ?? now())->format('h:i:s A') }}</span>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Bottom Row (Signatures & Checkboxes) -->
    <div class="permit-bottom-row">
        <!-- Left: Signature of User -->
        <div class="bottom-signature-left">
            <div class="signature-line"></div>
            <div class="signature-label">
                <span class="signature-label-si">පාවිච්චි කරන්නාගේ අත්සන</span>
                <span class="signature-label-en">Signature of User</span>
            </div>
        </div>

        <!-- Middle: Checkboxes -->
        <div class="bottom-checkboxes-mid">
            @php
                $pass_type_lower = strtolower($permit->pass_type ?? '');
                $cross_onboard = true;
                $cross_afloat = true;
                $cross_ashore = true;

                if (stripos($pass_type_lower, 'onboard') !== false || stripos($pass_type_lower, 'on board') !== false) {
                    $cross_onboard = false;
                } elseif (stripos($pass_type_lower, 'afloat') !== false) {
                    $cross_afloat = false;
                } elseif (stripos($pass_type_lower, 'ashore') !== false) {
                    $cross_ashore = false;
                } else {
                    // Default fallback: keep ashore clear
                    $cross_ashore = false;
                }
            @endphp
            <div class="pass-checkboxes-box">
                <div class="pass-option-col">
                    <span class="pass-label-top">නැව මත</span>
                    @if($cross_onboard)
                        <span class="pass-cross-overlay">XXX</span>
                    @endif
                    <span class="pass-label-bottom">On Board</span>
                </div>
                <div class="pass-option-col">
                    <span class="pass-label-top">දිය මත</span>
                    @if($cross_afloat)
                        <span class="pass-cross-overlay">XXX</span>
                    @endif
                    <span class="pass-label-bottom">Afloat</span>
                </div>
                <div class="pass-option-col">
                    <span class="pass-label-top">ගොඩබිම</span>
                    @if($cross_ashore)
                        <span class="pass-cross-overlay">XXX</span>
                    @endif
                    <span class="pass-label-bottom">Ashore</span>
                </div>
            </div>
        </div>

        <!-- Right: For Chairman Signature -->
        <div class="bottom-signature-right">
            <div class="signature-line"></div>
            <div class="signature-label">
                <span class="signature-label-si">සභාපති වෙනුවට</span>
                <span class="signature-label-en">For Chairman / S.L.P.A</span>
            </div>
        </div>
    </div>

    <!-- Footer Act Text -->
    <div class="act-footer">
        1979 අංක 51 දරණ ශ්‍රී ලංකා වරාය අධිකාරි පනතේ අංක 85 (2) ඉහලින් කියවෙන අංක 67 (1) (x) දරණ ව්‍යවස්ථා යටතේ ශ්‍රී ලංකා වරාය අධිකාරිය විසින් නිකුත් කරන ලදී.<br>
        Issued under the Regulation made under Section 67 (1) (x) read with Section 85 (2) of the Sri Lanka Ports Authority Act. No. 51 of 1979.
    </div>
</div>

<div id="printControls">
    <button id="btnPrintAgain">Print Again</button>
    <button id="btnBackInvoice">Back to Invoice</button>
    @if($payment->permit_type === 'TP')
        <button id="btnBack">Back to Temporary Permit</button>
    @elseif($payment->permit_type === 'MP')
        <button id="btnBack">Back to Monthly Permit</button>
    @elseif($payment->permit_type === 'VH')
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
@endsection
