@extends('layouts.app')
@section('content')

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Temporary Permit Form</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

    <script>
        function setMaxToDate() {
            const idType = document.getElementById('id_type').value;
            const fromDateInput = document.getElementById('from_date');
            const toDateInput = document.getElementById('to_date');

            const fromDate = new Date(fromDateInput.value);
            if (!fromDateInput.value) return;

            let maxDays = 29; // default max days for NIC
            if (idType === 'Passport' || idType === 'License') {
                maxDays = 14;
            }

            const maxToDate = new Date(fromDate);
            maxToDate.setDate(maxToDate.getDate() + maxDays);

            // Format date to yyyy-mm-dd
            const yyyy = maxToDate.getFullYear();
            const mm = String(maxToDate.getMonth() + 1).padStart(2, '0');
            const dd = String(maxToDate.getDate()).padStart(2, '0');

            toDateInput.min = fromDateInput.value;
            toDateInput.max = `${yyyy}-${mm}-${dd}`;

            // reset to_date if out of new bounds
            if (toDateInput.value < toDateInput.min || toDateInput.value > toDateInput.max) {
                toDateInput.value = toDateInput.min;
            }
        }

        function checkAvailability() {
    const idType = document.getElementById('id_type').value;
    const idNumber = document.querySelector('input[name="id_number"]').value;
    const fullName = document.querySelector('input[name="full_name"]').value;
    const initials = document.querySelector('input[name="initials"]').value;
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;
    const companyName = document.getElementById('company_name').value; // ✅ Add this line

    const msg = document.getElementById('availability-msg');
    msg.innerText = ''; // Reset previous message

    if (!idType || !idNumber || !fullName || !initials || !fromDate || !toDate) {
        msg.innerText = "Please fill in all required fields.";
        msg.style.color = 'red';
        return;
    }

    fetch("{{ route('permit.checkAvailability') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({
            id_type: idType,
            id_number: idNumber,
            full_name: fullName,
            initials: initials,
            from_date: fromDate,
            to_date: toDate,
            company_name: companyName 
        })
    })
    .then(res => {
        if (!res.ok) {
            return res.text().then(text => {
                throw new Error(`Server error: ${text}`);
            });
        }
        return res.json();
    })
    .then(data => {
        msg.innerText = data.message;
        msg.style.color = data.available ? 'green' : 'red';
    })
    .catch(error => {
        console.error("Availability check failed:", error);
        msg.innerText = "Something went wrong during availability check.";
        msg.style.color = 'red';
    });
}


    </script>
</head>
<body>
    <div class="container my-4">
        <h1 class="mb-4">Sri Lanka Ports Authority - Temporary Permit Form</h1>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('permit.addToSession') }}">
            @csrf
            <input type="hidden" name="type" value="temporary">

            <div class="mb-3">
                <label for="id_type" class="form-label">Identification Type</label>
                <select name="id_type" id="id_type" onchange="setMaxToDate()" class="form-select" required>
                   <option value="NIC" {{ old('id_type', $permit->id_type ?? '') == 'NIC' ? 'selected' : '' }}>NIC</option>
<option value="Passport" {{ old('id_type', $permit->id_type ?? '') == 'Passport' ? 'selected' : '' }}>Passport</option>
<option value="Driving License" {{ old('id_type', $permit->id_type ?? '') == 'Driving License' ? 'selected' : '' }}>Driving License</option>

                </select>
            </div>

            <div class="mb-3">
                <label for="id_number" class="form-label">Identificaton Number</label>
                <input type="text" name="id_number" id="id_number" value="{{ old('id_number') }}" class="form-control" required>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="from_date" class="form-label">From Date</label>
                    <input type="date" id="from_date" name="from_date" value="{{ old('from_date') }}" onchange="setMaxToDate()" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="to_date" class="form-label">To Date</label>
                    <input type="date" id="to_date" name="to_date" value="{{ old('to_date') }}" class="form-control" required>
                </div>
            </div>

            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" name="full_name" id="full_name" value="{{ old('full_name') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="initials" class="form-label">Name with Initials</label>
                <input type="text" name="initials" id="initials" value="{{ old('initials') }}" class="form-control" required>
            </div>

               <div class="mb-3">
                <label for="company_name" class="form-label">Company Name</label>
                <input type="text" name="company_name" id="company_name" 
    value="{{ old('company_name', $companyName) }}" 
    class="form-control" required>
            </div>
            
            <button type="button" onclick="checkAvailability()" class="btn btn-info mb-3">Check Availability</button>
            <p id="availability-msg" class="fw-bold"></p>
            <div class="mb-3">
                <label for="designation" class="form-label">Designation</label>
                <input type="text" name="designation" id="designation" value="{{ old('designation') }}" class="form-control">
            </div>

         

            <div class="mb-3">
                <label for="company_address" class="form-label">Company Address</label>
                <textarea name="company_address" id="company_address" rows="2" class="form-control" required>{{ old('company_address', $companyAddress) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="residence_address" class="form-label">Residence Address</label>
                <textarea name="residence_address" id="residence_address" rows="2" class="form-control">{{ old('residence_address') }}</textarea>
            </div>

            <fieldset class="mb-3">
                <legend class="col-form-label pt-0">Pass Type</legend>
                <div class="form-check">
                    <input type="checkbox" name="pass_type[]" value="onboard" id="pass_onboard" class="form-check-input" 
                        {{ is_array(old('pass_type')) && in_array('onboard', old('pass_type')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="pass_onboard">Onboard</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="pass_type[]" value="afloat" id="pass_afloat" class="form-check-input"
                        {{ is_array(old('pass_type')) && in_array('afloat', old('pass_type')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="pass_afloat">Afloat</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="pass_type[]" value="ashore" id="pass_ashore" class="form-check-input"
                        {{ is_array(old('pass_type')) && in_array('ashore', old('pass_type')) ? 'checked' : '' }}>
                    <label class="form-check-label" for="pass_ashore">Ashore</label>
                </div>
            </fieldset>

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

            <div class="mb-3">
                <label for="reason" class="form-label">Reason for Visit</label>
                <select name="reason" id="reason" class="form-select" required>
                    <option value="">-- Select --</option>
                    <option value="inspection" {{ old('reason') == 'inspection' ? 'selected' : '' }}>Inspection</option>
                    <option value="delivery" {{ old('reason') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                    <option value="official_visit" {{ old('reason') == 'official_visit' ? 'selected' : '' }}>Official Visit</option>
                    <option value="maintenance" {{ old('reason') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="other" {{ old('reason') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Add to List</button>
        </form>
  <div></div>
        @if(session('permit_cart') && count(session('permit_cart')) > 0)
            <h3 class="mt-5">Current Permit Requests for Company: {{ session('company_name') }}</h3>
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
                        <th>Reason</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <tbody>
                  @foreach(session('permit_cart') as $index => $permit)
                        <tr>
                            <td>{{ $permit['id_type'] }}</td>
                            <td>{{ $permit['id_number'] }}</td>
                            <td>{{ $permit['from_date'] }}</td>
                            <td>{{ $permit['to_date'] }}</td>
                            <td>{{ $permit['full_name'] }}</td>
                            <td>{{ $permit['initials'] }}</td>
                            <td>{{ $permit['pass_type'] }}</td>
                            <td>{{ $permit['issue_type'] }}</td>
                            <td>{{ $permit['reason'] }}</td>
                             <td><a href="{{ route('permit.editSessionEntry', $index) }}" class="btn btn-sm btn-warning">Edit</a>
                </td>
                <td>

    <form method="POST" action="{{ route('permit.removeSessionEntry', $index) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to remove this entry?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
    </form>
</td>

                        </tr>
                    @endforeach
                </tbody>
            </table>

            <form method="POST" action="{{ route('permit.submitAll') }}">
                @csrf
                <button type="submit" class="btn btn-success">Submit All Permit Requests</button>
            </form>
        @endif
    </div>
    

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
@endsection