<!-- resources/views/permit/monthly.blade.php -->
@extends('layouts.app')

@section('content')
<!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    
<div class="container">
    <h2 class="my-4">Monthly Permit Form</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('permit.monthly.addToSession') }}">
        @csrf

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="id_type" class="form-label">ID Type</label>
                <input type="text" class="form-control" name="id_type" value="NIC" readonly>
            </div>
            <div class="col-md-6">
                <label for="id_number" class="form-label">ID Number</label>
                <input type="text" class="form-control" name="id_number" value="{{ old('id_number') }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" class="form-control" name="from_date" value="{{ old('from_date') }}" required>
            </div>
            <div class="col-md-6">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" class="form-control" name="to_date" value="{{ old('to_date') }}" required>
            </div>
        </div>

        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" class="form-control" name="full_name" value="{{ old('full_name') }}" required>
        </div>

        <div class="mb-3">
            <label for="initials" class="form-label">Name with Initials</label>
            <input type="text" class="form-control" name="initials" value="{{ old('initials') }}" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" class="form-control" name="company_name" value="{{ old('company_name', $companyName ?? '') }}" required>
            </div>

        </div>

        <!-- Availability Check Button -->
        <button type="button" onclick="checkMonthlyAvailability()" class="btn btn-info mb-3">Check Availability</button>
        <p id="monthly-availability-msg" class="fw-bold"></p>

        <div class="mb-3">
            <label for="designation" class="form-label">Designation</label>
            <input type="text" class="form-control" name="designation" value="{{ old('designation') }}">
        </div>
  <div class="col-md-6">
                <label for="company_address" class="form-label">Company Address</label>
                <textarea class="form-control" name="company_address" rows="2">{{ old('company_address', $companyAddress ?? '') }}</textarea>
            </div>
        <div class="mb-3">
            <label for="residence_address" class="form-label">Residence Address</label>
            <textarea class="form-control" name="residence_address" rows="2">{{ old('residence_address') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Pass Type</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="pass_type[]" value="onboard" {{ is_array(old('pass_type')) && in_array('onboard', old('pass_type')) ? 'checked' : '' }}>
                <label class="form-check-label">Onboard</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="pass_type[]" value="afloat" {{ is_array(old('pass_type')) && in_array('afloat', old('pass_type')) ? 'checked' : '' }}>
                <label class="form-check-label">Afloat</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="pass_type[]" value="ashore" {{ is_array(old('pass_type')) && in_array('ashore', old('pass_type')) ? 'checked' : '' }}>
                <label class="form-check-label">Ashore</label>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Issue Type</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="issue_type" value="free" {{ old('issue_type', 'free') == 'free' ? 'checked' : '' }}>
                <label class="form-check-label">Free</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="issue_type" value="payment" {{ old('issue_type') == 'payment' ? 'checked' : '' }}>
                <label class="form-check-label">On Payment</label>
            </div>
        </div>

        <div class="mb-3">
            <label for="reason" class="form-label">Reason for Visit</label>
            <select name="reason" class="form-select" required>
                <option value="">-- Select --</option>
                <option value="inspection" {{ old('reason') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                <option value="delivery" {{ old('reason') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                <option value="official_visit" {{ old('reason') == 'official_visit' ? 'selected' : '' }}>Official Visit</option>
                <option value="maintenance" {{ old('reason') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="police_issue_date" class="form-label">Police Report Issue Date</label>
                <input type="date" class="form-control" name="police_issue_date" value="{{ old('police_issue_date') }}" required>
            </div>
            <div class="col-md-6">
                <label for="police_expire_date" class="form-label">Police Report Expiry Date</label>
                <input type="date" class="form-control" name="police_expire_date" value="{{ old('police_expire_date') }}" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Add to Monthly Permit List</button>
    </form>
    @if(session('monthly_permit_cart') && count(session('monthly_permit_cart')) > 0)
    <h3 class="mt-5">Current Monthly Permit Requests for Company: {{ session('monthly_company_name') }}</h3>
    <table class="table table-bordered">
        <thead class="table-light">
            <tr>
                <th>ID Type</th>
                <th>ID Number</th>
                <th>From Date</th>
                <th>To Date</th>
                <th>Full Name</th>
                <th>Initials</th>
                <th>Pass Type</th>
                <th>Issue Type</th>
                <th>Police Report Issue</th>
                <th>Police Report Expiry</th>
                <th>Reason</th>
                <th>Edit</th>
            </tr>
        </thead>
        <tbody>
            @foreach(session('monthly_permit_cart') as $index => $permit)
                <tr>
                    <td>{{ $permit['id_type'] }}</td>
                    <td>{{ $permit['id_number'] }}</td>
                    <td>{{ $permit['from_date'] }}</td>
                    <td>{{ $permit['to_date'] }}</td>
                    <td>{{ $permit['full_name'] }}</td>
                    <td>{{ $permit['initials'] }}</td>
                    <td>{{ $permit['pass_type'] }}</td>
                    <td>{{ $permit['issue_type'] }}</td>
                    <td>{{ $permit['police_issue_date'] }}</td>
                    <td>{{ $permit['police_expire_date'] }}</td>
                    <td>{{ $permit['reason'] }}</td>
                    <td>
                        <a href="{{ route('permit.monthly.editSessionEntry', $index) }}" class="btn btn-sm btn-warning">Edit</a>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <form method="POST" action="{{ route('permit.monthly.submit') }}">
        @csrf
        <button type="submit" class="btn btn-success">Submit All Monthly Permits</button>
    </form>
@endif

    </div>
<!-- Availability Check Script -->
<script>
function checkMonthlyAvailability() {
    const idNumber = document.querySelector('input[name="id_number"]').value;
    const fullName = document.querySelector('input[name="full_name"]').value;
    const initials = document.querySelector('input[name="initials"]').value;
    const fromDate = document.querySelector('input[name="from_date"]').value;
    const toDate = document.querySelector('input[name="to_date"]').value;
    const companyName = document.querySelector('input[name="company_name"]').value;
    const vehicleNumber = ""; // optional — if you add a vehicle number field, grab its value here

    if (!idNumber || !fullName || !initials || !fromDate || !toDate) {
        alert("Please fill in ID Number, Full Name, Initials, and both From and To dates.");
        return;
    }

    fetch("{{ route('permit.monthly.checkAvailability') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            id_number: idNumber,
            full_name: fullName,
            initials: initials,
            from_date: fromDate,
            to_date: toDate,
            company_name: companyName,
            vehicle_number: vehicleNumber // optional
        })
    })
    .then(res => res.json())
    .then(data => {
        const msg = document.getElementById('monthly-availability-msg');
        msg.innerText = data.message;
        msg.style.color = data.available ? 'green' : 'red';
    })
    .catch(error => {
        console.error("Availability check failed:", error);
    });
}

</script>
@endsection
