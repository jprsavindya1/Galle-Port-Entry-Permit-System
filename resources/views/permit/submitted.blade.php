@extends('layouts.app')

@section('content')
<!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

<div class="container">
    <h1 class="mb-4">Submitted Permit Requests <small class="text-muted">(Grouped by Submission)</small></h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Filter Form -->
    <form id="filter-form" method="GET" action="{{ route('permits.submitted') }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="date" name="date" class="form-control" 
                   value="{{ request('date', \Carbon\Carbon::today()->toDateString()) }}" 
                   onchange="document.getElementById('filter-form').submit();">
        </div>
        <div class="col-md-3">
            <input type="text" name="q" class="form-control" placeholder="Search by Company, ID, or Name" value="{{ request('q') }}">
        </div>
        <div class="col-md-auto">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    @if($permits->count())
        @php $grouped = $permits->groupBy('submission_id'); @endphp

        @foreach($grouped as $submissionId => $group)
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <strong>Submission ID:</strong> {{ $submissionId }}
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="overflow-x:auto;">
                        <table class="table table-bordered table-hover m-0">
                            <thead class="table-light">
                                <tr>
                                    @if($group->first()->type === 'VP')
                                        <th>Vehicle Number</th>
                                        <th>Owner's Name</th>
                                        <th>Revenue License No.</th>
                                        <th>Insurance Number</th>
                                        <th>Company Name</th>
                                        <th>Issue Type</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                    @elseif($group->first()->type === 'MP')
                                        <th>ID Type</th>
                                        <th>ID Number</th>
                                        <th>Full Name</th>
                                        <th>Company Name</th>
                                        <th>Pass Type</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Issue Type</th>
                                        <th>Police Report Issue Date</th>
                                        {{-- Optionally also show expire date --}}
                                        {{-- <th>Police Report Expire Date</th> --}}
                                    @else {{-- TP or other --}}
                                        <th>ID Type</th>
                                        <th>ID Number</th>
                                        <th>Full Name</th>
                                        <th>Company Name</th>
                                        <th>Pass Type</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                        <th>Issue Type</th>
                                    @endif

                                    <th class="sticky-col status-col" style="width:120px;">Status</th>
                                    <th class="sticky-col actions-col" style="width:120px;">Actions</th>
                                    <th class="sticky-col view-col" style="width:120px;">View</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($group as $permit)
                                    <tr id="permit-row-{{ $permit->id }}">
                                        @if($permit->type === 'VP')
                                            <td>{{ $permit->vehicle_number }}</td>
                                            <td>{{ $permit->owner_name }}</td>
                                            <td>{{ $permit->revenue_license_number }}</td>
                                            <td>{{ $permit->insurance_number ?? '-' }}</td>
                                            <td>{{ $permit->company_name }}</td>
                                            <td>{{ ucfirst($permit->issue_type) }}</td>
                                            <td>{{ $permit->from_date }}</td>
                                            <td>{{ $permit->to_date }}</td>

                                        @elseif($permit->type === 'MP')
                                            <td>{{ $permit->id_type }}</td>
                                            <td>{{ $permit->id_number }}</td>
                                            <td>{{ $permit->full_name }}</td>
                                            <td>{{ $permit->company_name }}</td>
                                            <td>{{ $permit->pass_type }}</td>
                                            <td>{{ $permit->from_date }}</td>
                                            <td>{{ $permit->to_date }}</td>
                                            <td>{{ $permit->issue_type }}</td>
                                            <td>{{ $permit->police_issue_date ?? '-' }}</td>
                                            {{-- Optionally also show expire date if you added it to the header --}}
                                            {{-- <td>{{ $permit->police_expire_date ?? '-' }}</td> --}}

                                        @else {{-- TP or other --}}
                                            <td>{{ $permit->id_type }}</td>
                                            <td>{{ $permit->id_number }}</td>
                                            <td>{{ $permit->full_name }}</td>
                                            <td>{{ $permit->company_name }}</td>
                                            <td>{{ $permit->pass_type }}</td>
                                            <td>{{ $permit->from_date }}</td>
                                            <td>{{ $permit->to_date }}</td>
                                            <td>{{ $permit->issue_type }}</td>
                                        @endif


                                        <!-- Status -->
                                        <td class="sticky-col status-col" id="status-col-{{ $permit->id }}">
                                            @if($permit->status === 'active')
                                                <button type="button" 
                                                        class="btn btn-sm btn-success w-100" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#cancelModal{{ $permit->id }}">
                                                    Active
                                                </button>

                                                <!-- Cancel Modal -->
                                                <div class="modal fade" id="cancelModal{{ $permit->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('permits.cancel', $permit) }}" method="POST" class="cancel-permit-form">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title">Cancel Permit</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <label class="form-label">Reason</label>
                                                                    <select name="cancel_reason_select" class="form-select" {{ (!in_array(Auth::user()->role, ['admin','super-admin'])) ? 'disabled' : '' }}>
                                                                        <option value="Expired Date">Expired Date</option>
                                                                        <option value="Lost Permit">Lost Permit</option>
                                                                        <option value="Security Concern">Security Concern</option>
                                                                        <option value="Expired Police Report / Insurance">Expired Police Report / Insurance</option>
                                                                        <option value="Fraudulent">Fraudulent</option>
                                                                        <option value="Other">Other</option>
                                                                    </select>
                                                                    <input type="text" name="cancel_reason_other" class="form-control mt-2" placeholder="If Other, type here"
                                                                           {{ (!in_array(Auth::user()->role, ['admin','super-admin'])) ? 'disabled' : '' }}>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    @if(in_array(Auth::user()->role, ['admin','super-admin']))
                                                                        <button type="submit" class="btn btn-danger w-100">Confirm Cancel</button>
                                                                    @else
                                                                        <button type="button" class="btn btn-danger w-100" disabled>
                                                                            Confirm Cancel (Admins Only)
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <form action="{{ route('admin.cancelled_permits.activate', ['permit' => $permit->id]) }}" method="POST" class="activate-permit-form">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger w-100" {{ (!in_array(Auth::user()->role, ['admin','super-admin'])) ? 'disabled' : '' }}>
                                                        Cancelled
                                                    </button>
                                                </form>
                                            @endif
                                        </td>

                                        <!-- Actions -->
<td class="sticky-col actions-col">
    <a href="{{ route('permits.edit', $permit) }}"
       class="btn btn-sm btn-warning w-100 mb-1"
       @if($permit->status === 'cancelled') disabled style="pointer-events: none; opacity: 0.5;" @endif>
       Edit
    </a>
</td>

<!-- View -->
<td class="sticky-col view-col">
    <a href="{{ route('payment.invoice', $permit->submission_id) }}"
       class="btn btn-sm btn-warning w-100 mb-1"
       @if($permit->status === 'cancelled') disabled style="pointer-events: none; opacity: 0.5;" @endif>
       View Group
    </a>

    <a href="{{ route('permit.print.single', $permit->id) }}"
       target="_blank"
       class="btn btn-sm btn-primary w-100"
       @if($permit->status === 'cancelled') disabled style="pointer-events: none; opacity: 0.5;" @endif>
       Print
    </a>
</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        <div class="d-flex justify-content-center">
            {{ $permits->withQueryString()->links() }}
        </div>
    @else
        <div class="alert alert-info">
            No permits found.
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.table-responsive { position: relative; }
.sticky-col {
    position: -webkit-sticky;
    position: sticky;
    background-color: #fff;
    z-index: 2;
    border-left: 1px solid #dee2e6;
    border-right: 1px solid #dee2e6;
}
.status-col { left: 0; }
.actions-col { left: 120px; }
.view-col { left: 240px; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const container = document.querySelector('.container');

    // Delegate Cancel Permit
    container.addEventListener('submit', async function(e){
        if(e.target.classList.contains('cancel-permit-form')){
            e.preventDefault();

            @if(!in_array(Auth::user()->role, ['admin','super-admin']))
                alert("Only admins can cancel permits.");
                return;
            @endif

            let form = e.target;
            let actionUrl = form.getAttribute('action');
            let formData = new FormData(form);

            try {
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) throw new Error(await response.text());

                const data = await response.json();
                if (data.status === 'cancelled') {
                    // hide modal
                    let modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                    modal.hide();

                    // Replace Active button with Cancelled form
                    const statusCol = document.querySelector(`#status-col-${data.id}`);
                    statusCol.innerHTML = `
                        <form action="/admin/cancelled_permits/${data.id}/activate" method="POST" class="activate-permit-form">
                            <input type="hidden" name="_token" value="${form.querySelector('input[name=_token]').value}">
                            <button type="submit" class="btn btn-sm btn-danger w-100" ${!['admin','super-admin'].includes("{{ Auth::user()->role }}") ? 'disabled' : ''}>
                                Cancelled
                            </button>
                        </form>
                    `;

                    // Disable Edit / View / Print buttons
                    const row = document.querySelector(`#permit-row-${data.id}`);
                    row.querySelectorAll('a.btn').forEach(btn => {
                        btn.setAttribute('disabled', 'disabled');
                        btn.style.pointerEvents = 'none';
                        btn.style.opacity = 0.2;
                        btn.style.backgroundColor = '#6c757d'; // Bootstrap's secondary grey
                        btn.style.borderColor = '#6c757d';

                    });
                }

            } catch(err) {
                console.error(err);
                alert("Network error. Check console.");
            }
        }
    });

    // Delegate Activate Permit
    container.addEventListener('submit', async function(e){
        if(e.target.classList.contains('activate-permit-form')){
            e.preventDefault();

            @if(!in_array(Auth::user()->role, ['admin','super-admin']))
                alert("Only admins can activate cancelled permits.");
                return;
            @endif

            let form = e.target;
            let actionUrl = form.getAttribute('action');
            let formData = new FormData(form);

            try {
                const response = await fetch(actionUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (!response.ok) throw new Error(await response.text());

                const data = await response.json();
                if (data.status === 'activated') {
                    // Replace Cancelled button with Active + Modal
                    const statusCol = document.querySelector(`#status-col-${data.id}`);
                    statusCol.innerHTML = `
                        <button type="button" class="btn btn-sm btn-success w-100" data-bs-toggle="modal" data-bs-target="#cancelModal${data.id}">
                            Active
                        </button>

                        <div class="modal fade" id="cancelModal${data.id}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="/permits/${data.id}/cancel" method="POST" class="cancel-permit-form">
                                        <input type="hidden" name="_token" value="${form.querySelector('input[name=_token]').value}">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Cancel Permit</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label">Reason</label>
                                            <select name="cancel_reason_select" class="form-select">
                                                <option value="Expired Date">Expired Date</option>
                                                <option value="Lost Permit">Lost Permit</option>
                                                <option value="Security Concern">Security Concern</option>
                                                <option value="Expired Police Report / Insurance">Expired Police Report / Insurance</option>
                                                <option value="Fraudulent">Fraudulent</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <input type="text" name="cancel_reason_other" class="form-control mt-2" placeholder="If Other, type here">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-danger w-100">Confirm Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `;

                    // Enable Edit / View / Print buttons
                    const row = document.querySelector(`#permit-row-${data.id}`);
                    row.querySelectorAll('a.btn').forEach(btn => {
                        btn.removeAttribute('disabled');
                        btn.style.pointerEvents = '';
                        btn.style.opacity = '';
                        btn.style.backgroundColor = '';
                        btn.style.borderColor = '';

                    });
                }

            } catch(err) {
                console.error(err);
                alert("Network error. Check console.");
            }
        }
    });

});
</script>
@endpush
