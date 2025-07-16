@extends('layouts.app')

@section('content')
<h1>Edit Permit</h1>

@if($errors->any())
    <div style="color:red;">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('permits.update', $permit) }}">
    @csrf
    @method('PUT')

    <label>ID Type:</label><br>
    <select name="id_type" required>
        <option value="NIC" {{ $permit->id_type == 'NIC' ? 'selected' : '' }}>NIC</option>
        <option value="Passport" {{ $permit->id_type == 'Passport' ? 'selected' : '' }}>Passport</option>
        <option value="License" {{ $permit->id_type == 'License' ? 'selected' : '' }}>License</option>
    </select><br><br>

    <label>ID Number:</label><br>
    <input type="text" name="id_number" value="{{ $permit->id_number }}" required><br><br>

    <label>From Date:</label><br>
    <input type="date" name="from_date" value="{{ $permit->from_date }}" required><br><br>

    <label>To Date:</label><br>
    <input type="date" name="to_date" value="{{ $permit->to_date }}" required><br><br>

    <label>Full Name:</label><br>
    <input type="text" name="full_name" value="{{ $permit->full_name }}" required><br><br>

    <label>Initials:</label><br>
    <input type="text" name="initials" value="{{ $permit->initials }}" required><br><br>

    <label>Designation:</label><br>
    <input type="text" name="designation" value="{{ $permit->designation }}"><br><br>

    <label>Company Name:</label><br>
    <input type="text" name="company_name" value="{{ $permit->company_name }}" required><br><br>

    <label>Company Address:</label><br>
    <textarea name="company_address" rows="2" cols="40">{{ $permit->company_address }}</textarea><br><br>

    <label>Residence Address:</label><br>
    <textarea name="residence_address" rows="2" cols="40">{{ $permit->residence_address }}</textarea><br><br>

    <label>Pass Type:</label><br>
    @php
        $passTypes = explode(',', $permit->pass_type);
    @endphp
    <input type="checkbox" name="pass_type[]" value="onboard" {{ in_array('onboard', $passTypes) ? 'checked' : '' }}> Onboard<br>
    <input type="checkbox" name="pass_type[]" value="afloat" {{ in_array('afloat', $passTypes) ? 'checked' : '' }}> Afloat<br>
    <input type="checkbox" name="pass_type[]" value="ashore" {{ in_array('ashore', $passTypes) ? 'checked' : '' }}> Ashore<br><br>

    <label>Issue Type:</label><br>
    <input type="radio" name="issue_type" value="free" {{ $permit->issue_type == 'free' ? 'checked' : '' }}> Free Issue
    <input type="radio" name="issue_type" value="payment" {{ $permit->issue_type == 'payment' ? 'checked' : '' }}> On Payment<br><br>

    <label>Reason for Visit:</label><br>
    <select name="reason" required>
        <option value="inspection" {{ $permit->reason == 'inspection' ? 'selected' : '' }}>Inspection</option>
        <option value="delivery" {{ $permit->reason == 'delivery' ? 'selected' : '' }}>Delivery</option>
        <option value="official_visit" {{ $permit->reason == 'official_visit' ? 'selected' : '' }}>Official Visit</option>
        <option value="maintenance" {{ $permit->reason == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
        <option value="other" {{ $permit->reason == 'other' ? 'selected' : '' }}>Other</option>
    </select><br><br>

    <button type="submit">Update Permit</button>
</form>
@endsection
