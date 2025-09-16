@extends('layouts.app')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<div class="container">
    <h2 class="my-4">Edit Vehicle Permit Session Entry</h2>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('permit.vehicle.updateVehicleSessionEntry', $index) }}">
        @csrf
        @method('PUT')

        <!-- Hidden flag to tell backend this is editing -->
        <input type="hidden" name="session_edit" value="1">
        <input type="hidden" name="company_name" value="{{ $permit['company_name'] ?? '' }}">
        <input type="hidden" name="company_address" value="{{ $permit['company_address'] ?? '' }}">

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Vehicle Type</label>
                <select name="vehicle_type" class="form-select" required>
                    <option value="">-- Select Vehicle Type --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->name }}"
                            {{ old('vehicle_type', $permit['vehicle_type']) == $vehicle->name ? 'selected' : '' }}>
                            {{ $vehicle->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-6">
                <label>Vehicle Number</label>
                <input type="text" name="vehicle_number" class="form-control"
                       value="{{ old('vehicle_number', $permit['vehicle_number']) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Revenue License Number</label>
                <input type="text" name="revenue_license_number" class="form-control"
                       value="{{ old('revenue_license_number', $permit['revenue_license_number']) }}" required>
            </div>
            <div class="col-md-6">
                <label>Insurance Number</label>
                <input type="text" name="insurance_number" class="form-control"
                       value="{{ old('insurance_number', $permit['insurance_number']) }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>From Date</label>
                <input type="date" name="from_date" id="from_date" class="form-control"
                       value="{{ old('from_date', $permit['from_date']) }}" required>
            </div>
            <div class="col-md-6">
                <label>To Date</label>
                <input type="date" name="to_date" id="to_date" class="form-control"
                       value="{{ old('to_date', $permit['to_date']) }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label>Owner's Name</label>
            <input type="text" name="owner_name" class="form-control"
                   value="{{ old('owner_name', $permit['owner_name']) }}" required>
        </div>

        <div class="mb-3">
            <label>Owner's Address</label>
            <input type="text" name="owner_address" class="form-control"
                   value="{{ old('owner_address', $permit['owner_address']) }}" required>
        </div>

      <button type="button" onclick="checkVehicleAvailability()" class="btn btn-info mb-3"> 
            Check Availability
        </button>
        <p id="availability-msg" class="fw-bold"></p>


        <fieldset class="mb-3">
            <legend class="col-form-label pt-0">Issue Type</legend>
            @php $issueType = old('issue_type', $permit['issue_type'] ?? 'free'); @endphp
            <div class="form-check form-check-inline">
                <input type="radio" name="issue_type" value="free" class="form-check-input"
                       {{ $issueType === 'free' ? 'checked' : '' }}>
                <label class="form-check-label">Free Issue</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" name="issue_type" value="payment" class="form-check-input"
                       {{ $issueType === 'payment' ? 'checked' : '' }}>
                <label class="form-check-label">On Payment</label>
            </div>
        </fieldset>

        <div class="mb-3">
            <label for="reason" class="form-label">Reason for Visit</label>
            <select name="reason" id="reason" class="form-select" required>
                <option value="">-- Select --</option>
                @foreach($reasons as $reason)
                    <option value="{{ $reason->name }}"
                        {{ old('reason', $permit['reason']) == $reason->name ? 'selected' : '' }}>
                        {{ ucfirst($reason->name) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Remarks</label>
            <input type="text" name="remarks" class="form-control"
                   value="{{ old('remarks', $permit['remarks']) }}">
        </div>

        <button type="submit" class="btn btn-primary">Update Entry</button>
        <a href="{{ route('permit.vehicle') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection

@push('scripts')
<script>
function checkVehicleAvailability() {
    let vehicle_number = document.querySelector('input[name="vehicle_number"]').value;
    let from_date = document.querySelector('input[name="from_date"]').value;
    let to_date = document.querySelector('input[name="to_date"]').value;
    let company_name = document.querySelector('input[name="company_name"]').value;

    fetch("{{ route('permit.vehicle.checkVehicleAvailability') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            vehicle_number,
            from_date,
            to_date,
            company_name
        })
    })
    .then(res => res.json())
    .then(data => {
        let msgEl = document.getElementById("availability-msg");
        msgEl.textContent = data.message;
        msgEl.style.color = data.available ? "green" : "red";
    })
    .catch(err => {
        console.error(err);
        document.getElementById("availability-msg").textContent = "Error checking availability.";
    });
}
</script>
@endpush
