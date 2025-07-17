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
                <input type="text" class="form-control" name="vehicle_type" required value="{{ old('vehicle_type') }}">
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
            
           <div class="row mb-3">
    <div class="col-md-4">
        <label>From Date</label>
        <input type="date" class="form-control" name="from_date" id="from_date" required value="{{ old('from_date') }}">
    </div>
    <div class="col-md-4">
        <label>To Date</label>
        <input type="date" class="form-control" name="to_date" id="to_date" required value="{{ old('to_date') }}">
    </div>
    <div class="col-md-4 d-flex align-items-end">
        <button type="button" id="checkAvailabilityBtn" class="btn btn-info w-100">Check Availability</button>
    </div>
        </div>

        <div id="availabilityMessage" class="mb-3"></div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Issue Type</label>
                <select name="issue_type" class="form-control" required>
                    <option value="">-- Select --</option>
                    <option value="free" {{ old('issue_type') == 'free' ? 'selected' : '' }}>Free</option>
                    <option value="payment" {{ old('issue_type') == 'payment' ? 'selected' : '' }}>Payment</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Owner's Name</label>
                <input type="text" class="form-control" name="owner_name" required value="{{ old('owner_name') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label>Owner's Address</label>
                <input type="text" class="form-control" name="owner_address" required value="{{ old('owner_address') }}">
            </div>
            <div class="col-md-6">
                <label>Company Name</label>
                <input type="text" class="form-control" name="company_name" value="{{ old('company_name') }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label>Remarks</label>
                <input type="text" class="form-control" name="remarks" value="{{ old('remarks') }}">
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Add to List</button>
    </form>

    <hr><br>
    <br>
    <h3>Pending Vehicle Permit Entries</h3>
    <br>
    @if(count($cart))
        <table class="table table-bordered">
            <thead>
                <tr>
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
                        <td>{{ $entry['vehicle_number'] }}</td>
                        <td>{{ $entry['owner_name'] }}</td>
                        <td>{{ $entry['from_date'] }} to {{ $entry['to_date'] }}</td>
                        <td>{{ ucfirst($entry['issue_type']) }}</td>
                        <td>
                            <a href="{{ route('permit.vehicle.editSessionEntry', $index) }}" class="btn btn-sm btn-warning">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <form action="{{ route('permit.vehicle.submitAll') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">Submit All</button>
        </form>
    @else
        <p>No entries yet.</p>
    @endif
</div>

<script>
document.getElementById('checkAvailabilityBtn').addEventListener('click', function() {
    const vehicleNumber = document.getElementById('vehicle_number').value.trim();
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    const availabilityMessage = document.getElementById('availabilityMessage');

    availabilityMessage.innerHTML = ''; // clear previous message

    if (!vehicleNumber || !fromDate || !toDate) {
        availabilityMessage.innerHTML = '<div class="alert alert-warning">Please fill Vehicle Number, From Date, and To Date to check availability.</div>';
        return;
    }

    fetch("{{ route('permit.vehicle.checkAvailability') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            vehicle_number: vehicleNumber,
            from_date: fromDate,
            to_date: toDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.available) {
            availabilityMessage.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
        } else {
            availabilityMessage.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
        }
    })
    .catch(() => {
        availabilityMessage.innerHTML = '<div class="alert alert-danger">Error checking availability. Please try again.</div>';
    });
});
</script>

@endsection
