@extends('layouts.app')

@section('content')
<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<div class="container">
    <h2>Vehicle Permit Form</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('permit.vehicle.addToSession') }}" method="POST">
        @csrf
<div class="row mb-3">
        <div class="col-md-6">
            <label>Vehicle Type</label>
            <select name="vehicle_type" class="form-control" required>
                <option value="">-- Select Vehicle Type --</option>
                @foreach($vehicles as $vehicle)
                    <option value="{{ $vehicle->name }}" {{ old('vehicle_type') == $vehicle->name ? 'selected' : '' }}>
                        {{ $vehicle->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label>Vehicle Number</label>
            <input type="text" class="form-control" name="vehicle_number" id="vehicle_number" required value="{{ old('vehicle_number') }}">
        </div>
    </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Revenue License Number</label>
                <input type="text" class="form-control" name="revenue_license_number" required value="{{ old('revenue_license_number') }}">
            </div>
            <div class="col-md-6">
                <label>Insurance Number</label>
                <input type="text" class="form-control" name="insurance_number" value="{{ old('insurance_number') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" class="form-control" name="from_date" id="from_date" value="{{ old('from_date') }}" required>
            </div>
            <div class="col-md-6">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" class="form-control" name="to_date" id="to_date" value="{{ old('to_date') }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label>Owner's Name</label>
            <input type="text" class="form-control" name="owner_name" required value="{{ old('owner_name') }}"
            oninput="this.value = this.value.toUpperCase();">

            <label for="company_name" class="form-label">Company Name</label>
            <select name="company_name" id="company_name" class="form-select" onchange="setCompanyAddress()" required>
                <option value="">-- Select Company --</option>
                @foreach($companies as $company)
                    <option value="{{ $company->name }}" data-address="{{ $company->address }}"
                        {{ old('company_name', $companyName) == $company->name ? 'selected' : '' }}>
                        {{ $company->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button type="button" onclick="checkVehicleAvailability()" class="btn btn-info">Check Availability</button>
            <p id="availability-msg" class="fw-bold"></p>
        </div>

        <div id="availabilityMessage" class="mb-3"></div>

        <fieldset class="mb-3">
            <legend class="col-form-label pt-0">Issue Type</legend>
            <div class="form-check form-check-inline">
                <input type="radio" name="issue_type" id="issue_free" value="free" class="form-check-input" {{ old('issue_type', 'free') == 'free' ? 'checked' : '' }}>
                <label class="form-check-label" for="issue_free">Free Issue</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" name="issue_type" id="issue_payment" value="payment" class="form-check-input" {{ old('issue_type') == 'payment' ? 'checked' : '' }}>
                <label class="form-check-label" for="issue_payment">On Payment</label>
            </div>
        </fieldset>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Owner's Address</label>
                <input type="text" class="form-control" name="owner_address" required value="{{ old('owner_address') }}"
                oninput="this.value = this.value.toUpperCase();">
            </div>
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">Reason for Visit</label>
            <select name="reason" id="reason" class="form-select" required>
                <option value="">-- Select --</option>
                @foreach($reasons as $reason)
                    <option value="{{ $reason->name }}" {{ old('reason') == $reason->name ? 'selected' : '' }}>
                        {{ ucfirst($reason->name) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label>Remarks</label>
                <input type="text" class="form-control" name="remarks" value="{{ old('remarks') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Add to List</button>
    </form>

   
     @if(session('vehicle_permit_cart') && count(session('vehicle_permit_cart')) > 0)
        <h3 class="mt-5">Current Permit Requests for Company: {{ session('company_name') }}</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Vehicle Type</th>
                    <th>Vehicle Number</th>
                    <th>Owner</th>
                    <th>From - To</th>
                    <th>Issue Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($cart as $index => $entry)
                    <tr>
                        <td>{{ $entry['vehicle_type'] }}</td>
                        <td>{{ $entry['vehicle_number'] }}</td>
                        <td>{{ $entry['owner_name'] }}</td>
                        <td>{{ $entry['from_date'] }} to {{ $entry['to_date'] }}</td>
                        <td>{{ ucfirst($entry['issue_type']) }}</td>
                        <td><a href="{{ route('permit.vehicle.editVehicleSessionEntry', $index) }}" class="btn btn-sm btn-warning">Edit</a></td>
                        <td>
                            <form method="POST" action="{{ route('permit.vehicle.removeVehicleSessionEntry', $index) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this entry?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('permit.vehicle.submitAllVehicle') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Submit All</button>
        </form>
    @else
        <p></p>
    @endif
</div>

<script>
function checkVehicleAvailability() {
    const vehicleNumber = document.getElementById('vehicle_number').value.trim();
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    const availabilityMessage = document.getElementById('availabilityMessage');
     const companyName = document.getElementById('company_name').value;

    availabilityMessage.innerHTML = '';

    if (!vehicleNumber || !fromDate || !toDate) {
        availabilityMessage.innerHTML = '<div class="alert alert-warning">Please fill Vehicle Number, From Date, and To Date to check availability.</div>';
        return;
    }

    fetch("{{ route('permit.vehicle.checkVehicleAvailability') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            vehicle_number: vehicleNumber,
            from_date: fromDate,
            to_date: toDate,
            company_name: companyName
        })
    })
    .then(response => response.json())
    .then(data => {
        availabilityMessage.innerHTML = '<div class="alert ' + (data.available ? 'alert-success' : 'alert-danger') + '">' + data.message + '</div>';
    })
    .catch(() => {
        availabilityMessage.innerHTML = '<div class="alert alert-danger">Error checking availability. Please try again.</div>';
    });
}
</script>

@endsection
