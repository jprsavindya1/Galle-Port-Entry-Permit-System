@extends('layouts.app')

@section('content')
<!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    
<div class="container my-4">
    <h1>Edit Permit Entry</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('permit.updateSessionEntry', $index) }}">
        @csrf
        @method('PUT')
        
            <!-- This hidden input tells backend this is an edit session -->
    <input type="hidden" name="session_edit" value="1">

<input type="hidden" name="company_name" value="{{ $permit['company_name'] ?? '' }}">
<input type="hidden" name="company_address" value="{{ $permit['company_address'] ?? '' }}">

        <div class="mb-3">
            <label for="id_type" class="form-label">Identification Type</label>
            <select name="id_type" id="id_type" onchange="setMaxToDate()" class="form-select" required>
                <option value="NIC" {{ $permit['id_type'] == 'NIC' ? 'selected' : '' }}>NIC Number</option>
                <option value="Passport" {{ $permit['id_type'] == 'Passport' ? 'selected' : '' }}>Passport Number</option>
                <option value="License" {{ $permit['id_type'] == 'License' ? 'selected' : '' }}>Driving Licence</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="id_number" class="form-label">ID Number</label>
            <input type="text" name="id_number" id="id_number" value="{{ $permit['id_number'] }}" class="form-control" required>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="from_date" class="form-label">From Date</label>
                <input type="date" id="from_date" name="from_date" value="{{ $permit['from_date'] }}" onchange="setMaxToDate()" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="to_date" class="form-label">To Date</label>
                <input type="date" id="to_date" name="to_date" value="{{ $permit['to_date'] }}" class="form-control" required>
            </div>
        </div>

<div class="mb-3">
    <label for="full_name" class="form-label">Full Name</label>
    <input type="text" 
           name="full_name" 
           id="full_name"
           value="{{ old('full_name', $permit['full_name'] ?? '') }}" 
           class="form-control" 
           required
           style="text-transform: uppercase;" 
           oninput="this.value = this.value.toUpperCase();">
</div>



        <div class="mb-3">
            <label for="initials" class="form-label">Name with Initials</label>
            <input type="text" name="initials" id="initials" value="{{ $permit['initials'] }}" class="form-control" required>
        </div>

       <button type="button" onclick="checkAvailability(true)" class="btn btn-info mb-3">
            Check Availability
        </button>
        <p id="availability-msg" class="fw-bold"></p>

 <div class="mb-3">
    <label for="designation" class="form-label">Designation</label>
    <select name="designation" id="designation" class="form-select" required>
        <option value="">-- Select Designation --</option>
        @foreach($designations as $designation)
            <option value="{{ $designation->name }}" 
                {{ old('designation', $permit['designation']) == $designation->name ? 'selected' : '' }}>
                {{ $designation->name }}
            </option>
        @endforeach
    </select>
</div>


        <div class="mb-3">
            <label for="residence_address" class="form-label">Residence Address</label>
            <textarea name="residence_address" id="residence_address" rows="2" class="form-control">{{ $permit['residence_address'] ?? '' }}</textarea>
        </div>

        @php
            $selectedPasses = explode(',', $permit['pass_type']);
        @endphp

        <fieldset class="mb-3">
            <legend class="col-form-label pt-0">Pass Type</legend>
            <div class="form-check">
                <input type="checkbox" name="pass_type[]" value="onboard" id="pass_onboard" class="form-check-input" 
                    {{ in_array('onboard', $selectedPasses) ? 'checked' : '' }}>
                <label class="form-check-label" for="pass_onboard">Onboard</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="pass_type[]" value="afloat" id="pass_afloat" class="form-check-input"
                    {{ in_array('afloat', $selectedPasses) ? 'checked' : '' }}>
                <label class="form-check-label" for="pass_afloat">Afloat</label>
            </div>
            <div class="form-check">
                <input type="checkbox" name="pass_type[]" value="ashore" id="pass_ashore" class="form-check-input"
                    {{ in_array('ashore', $selectedPasses) ? 'checked' : '' }}>
                <label class="form-check-label" for="pass_ashore">Ashore</label>
            </div>
        </fieldset>

        <fieldset class="mb-3">
            <legend class="col-form-label pt-0">Issue Type</legend>
            <div class="form-check form-check-inline">
                <input type="radio" name="issue_type" id="issue_free" value="free" class="form-check-input" {{ $permit['issue_type'] == 'free' ? 'checked' : '' }}>
                <label class="form-check-label" for="issue_free">Free Issue</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" name="issue_type" id="issue_payment" value="payment" class="form-check-input" {{ $permit['issue_type'] == 'payment' ? 'checked' : '' }}>
                <label class="form-check-label" for="issue_payment">On Payment</label>
            </div>
        </fieldset>

    
<select name="reason" class="form-select" required>
    <option value="">-- Select --</option>
    @foreach($reasons as $reason)
        <option value="{{ $reason->name }}" {{ old('reason', $permit['reason']) == $reason->name ? 'selected' : '' }}>
            {{ ucfirst($reason->name) }}
        </option>
    @endforeach
</select>

        <button type="submit" class="btn btn-primary">Update Entry</button>
        <a href="{{ route('permit.temporary') }}" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
@endsection

@push('scripts')

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    
    <style>
        .select2-container--default .select2-selection--single {
            height: calc(2.375rem + 2px); 
            border: 1px solid #ced4da;
            border-radius: 0.375rem;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 1.5;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }
    </style>
@endpush

@push('scripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
         
    $('#designation').select2({
    placeholder: "-- Select Designation --",
    allowClear: true
    });
        // Set search input placeholder when dropdown opens
    $('#designation').on('select2:open', function () {
        setTimeout(() => {
            document.querySelector('.select2-search__field').placeholder = 'Search Designation...';
        }, 10);
    });
 
    function setMaxToDate() {
        const idType = document.getElementById('id_type').value;
        const fromDateInput = document.getElementById('from_date');
        const toDateInput = document.getElementById('to_date');

        const fromDate = new Date(fromDateInput.value);
        if (!fromDateInput.value) return;

        let maxDays = 29; // default max days for NIC
        if (idType === 'Passport' || idType === 'Driving License') {
            maxDays = 14;
        }

        const maxToDate = new Date(fromDate);
        maxToDate.setDate(maxToDate.getDate() + maxDays);

        const yyyy = maxToDate.getFullYear();
        const mm = String(maxToDate.getMonth() + 1).padStart(2, '0');
        const dd = String(maxToDate.getDate()).padStart(2, '0');

        toDateInput.min = fromDateInput.value;
        toDateInput.max = `${yyyy}-${mm}-${dd}`;

        if (toDateInput.value < toDateInput.min || toDateInput.value > toDateInput.max) {
            toDateInput.value = toDateInput.min;
        }
    }

    function checkAvailability(isEdit = false) {
    const idType = document.getElementById('id_type').value;
    const idNumber = document.querySelector('input[name="id_number"]').value;
    const fullName = document.querySelector('input[name="full_name"]').value;
    const initials = document.querySelector('input[name="initials"]').value;
    const fromDate = document.getElementById('from_date').value;
    const toDate = document.getElementById('to_date').value;

    const msg = document.getElementById('availability-msg');
    msg.innerText = '';

    if (!idType || !idNumber || !fullName || !initials || !fromDate || !toDate) {
        msg.innerText = "Please fill in all required fields.";
        msg.style.color = 'red';
        return;
    }

    const body = {
        id_type: idType,
        id_number: idNumber,
        full_name: fullName,
        initials: initials,
        from_date: fromDate,
        to_date: toDate,
        session_edit: isEdit // flag to skip company check in backend
    };

    // Include company_name only if NOT edit
    if (!isEdit) {
        body.company_name = document.getElementById('company_name')?.value || '';
    }

    fetch("{{ route('permit.checkAvailability') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify(body)
    })
    .then(res => res.json())
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
@endpush
