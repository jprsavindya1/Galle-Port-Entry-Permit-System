@extends('layouts.app')

@section('title', 'Submitted Permit Requests')

@section('content')
{{-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" /> --}}

<style>
    /* Match users/index.blade.php color palette and card/table styles */
    .permit-dashboard-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        padding: 2rem 2rem 1.5rem 2rem;
        margin-bottom: 2rem;
        border: none;
    }
    .permit-dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    .permit-dashboard-title {
        font-size: 2rem;
        font-weight: 600;
        color: #1976d2;
        letter-spacing: 1px;
    }
    .permit-filter-form-section {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 0.75rem;
        padding: 1.25rem 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 1.5rem;
    }

    /* Table Styles */
    .permit-submission-table {
        background: #f5faff;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .permit-submission-table th {
        background: #e3f2fd;
        color: #1976d2;
        font-weight: 500;
        border-bottom: 2px solid #bbdefb;
    }
    .permit-submission-table td {
        background: #f8fafc;
        color: #333;
        vertical-align: middle;
    }
    .card-header-submission {
        background-color:  #bbd0ff !important; /* A nice blue color for the submission ID header */
        color: #1e3a8a; !important;
        font-size: 1.1rem;
        font-weight: 600;
        border-top-left-radius: 0.75rem;
        border-top-right-radius: 0.75rem;
    }

    /* Reuse user styles for avatars and badges */
    .user-avatar {
        width: 36px;
        height: 36px;
        background: #90caf9;
        color: #fff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.1rem;
        margin-right: 0.5rem;
        box-shadow: 0 1px 4px rgba(25,118,210,0.08);
    }
    .user-role-badge {
        background: #bbdefb;
        color: #1976d2;
        font-weight: 500;
        border-radius: 0.5rem;
        padding: 0.25rem 0.75rem;
        font-size: 0.95rem;
    }

    .user-action-btn {
        font-size: 0.95rem;
        padding: 0.35rem 0.8rem;
        border-radius: 0.5rem;
        margin-right: 0.25rem;
        transition: background 0.2s, color 0.2s;
    }
    .user-action-btn.edit {
        background: #fff3e0;
        color: #ff9800;
        border: 1px solid #ffe0b2;
    }
    .user-action-btn.edit:hover {
        background: #ffe0b2;
        color: #e65100;
    }
    .user-action-btn.delete {
        background: #ffebee;
        color: #e53935;
        border: 1px solid #ffcdd2;
    }
    .user-action-btn.delete:hover {
        background: #ffcdd2;
        color: #b71c1c;
    }
    * ADJUST THIS VALUE: This pixel height should be visually inspected 
   in your browser to match the exact height of the filter/search section.
   ~120px to 140px is a common range for single-line form inputs with labels.
*/
.report-card-fixed-height {
    min-height: 125px; 
}

/* Ensure the card body within the fixed height remains a flex container to align the button */
.report-card-fixed-height .card-body {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
    /* Report card: place Generate button below label */
    .report-card .card-body { padding: 0.75rem 1rem; display:flex; flex-direction:column; justify-content:center; align-items:center; gap:0.75rem; height:100%; }
    .report-card .d-flex.align-items-center { gap: 0.5rem; justify-content:center; }
    .generate-btn { margin-top: 0; }
    @media (min-width: 576px) {
        /* Keep cards visually balanced on larger screens */
        .report-card .card-body { max-height:120px; }
        .generate-btn { min-width: 120px; }
    }
</style>

<div class="row mb-4 align-items-start">
    <div class="col-md-8">
        <div class="permit-filter-form-section">
            <form id="filter-form" method="GET" action="{{ route('permits.submitted') }}" class="row g-2 align-items-end">
                
                <div class="col-md-4">
                    <label class="form-label mb-1" for="date-filter"><i class="fas fa-calendar-alt me-1"></i> Filter by Date</label>
                    <input type="date" name="date" id="date-filter" class="form-control"
                        value="{{ request('date', \Carbon\Carbon::today()->toDateString()) }}"
                        onchange="document.getElementById('filter-form').submit();" style="border-radius:0.5rem;border:1px solid #bbdefb;background:#fff;">
                </div>
                
                <div class="col-md-5">
                    <label class="form-label mb-1" for="search-query"><i class="fas fa-search me-1"></i> Search</label>
                    <input type="text" name="q" id="search-query" class="form-control" placeholder="Company, ID, or Name" value="{{ request('q') }}" style="border-radius:0.5rem;border:1px solid #bbdefb;background:#fff;">
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100" style="border-radius:0.5rem;font-weight:500;">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="row g-3"> 

            <div class="col-12 col-sm-6">
                <div class="card shadow-sm w-100 report-card report-card-fixed-height">
                    <div class="card-body">
                        <div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-file-alt fa-2x text-primary"></i>
                                <span class="ms-2 fw-bold">User Reports</span>
                            </div>
                        </div>
                        <a href="{{ route('reports.user') }}" class="btn btn-primary btn-sm generate-btn mt-auto">Generate</a>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6">
                <div class="card shadow-sm w-100 report-card report-card-fixed-height">
                    <div class="card-body">
                        <div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-coins fa-2x text-success"></i>
                                <span class="ms-2 fw-bold">Revenue Reports</span>
                            </div>
                        </div>
                        <a href="{{ route('reports.payment') }}" class="btn btn-success btn-sm generate-btn mt-auto">Generate</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        @if($permits->count())
            @php $grouped = $permits->groupBy('submission_id'); @endphp

            @foreach($grouped as $submissionId => $group)
                <div class="card mb-4 permit-submission-table">
                    <div class="card-header card-header-submission">
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


                                            <td class="sticky-col status-col" id="status-col-{{ $permit->id }}">
                                                @if($permit->status === 'active')
                                                    <button type="button"
                                                            class="btn btn-sm btn-success w-100"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cancelModal{{ $permit->id }}">
                                                        Active
                                                    </button>

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

                                            <td class="sticky-col actions-col">
                                                <a href="{{ route('permits.edit', $permit) }}"
                                                   class="btn btn-sm {{ $permit->status === 'cancelled' ? 'btn-secondary' : 'btn-warning' }} w-100 mb-1"
                                                   @if($permit->status === 'cancelled') style="pointer-events: none; opacity: 0.5;" aria-disabled="true" @endif>
                                                    Edit
                                                </a>
                                            </td>

                                            <td class="sticky-col view-col">
                                                <a href="{{ route('payment.invoice', $permit->submission_id) }}"
                                                   class="btn btn-sm {{ $permit->status === 'cancelled' ? 'btn-secondary' : 'btn-warning' }} w-100 mb-1"
                                                   @if($permit->status === 'cancelled') style="pointer-events: none; opacity: 0.5;" aria-disabled="true" @endif>
                                                    View Group
                                                </a>

                                                <a href="{{ route('permit.print.single', $permit->id) }}"
                                                   target="_blank"
                                                   class="btn btn-sm {{ $permit->status === 'cancelled' ? 'btn-secondary' : 'btn-primary' }} w-100"
                                                   @if($permit->status === 'cancelled') style="pointer-events: none; opacity: 0.5;" aria-disabled="true" @endif>
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
</div>
@endsection

@push('styles')
<style>
main .table-responsive { position: relative; }
main .sticky-col {
    position: -webkit-sticky;
    position: sticky;
    background-color: #f8fafc; /* Change sticky background to match new row background */
    z-index: 2;
    border-left: 1px solid #dee2e6;
    border-right: 1px solid #dee2e6;
}
main .status-col { left: 0; }
main .actions-col { left: 120px; }
main .view-col { left: 240px; }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
@endpush

@push('scripts')
<script>

document.addEventListener("DOMContentLoaded", function () {
    

    const container = document.querySelector('.container');
    const userRole = "{{ Auth::user()->role }}";

    // Delegate Cancel Permit
    container.addEventListener('submit', async function(e){
            if(e.target.classList.contains('cancel-permit-form')){
            e.preventDefault();

            if(!['admin','super-admin'].includes(userRole)) {
                alert("Only admins can cancel permits.");
                return;
            }

            let form = e.target;
            let actionUrl = form.getAttribute('action');
                // Build form data and ensure the final cancel_reason is set.
                let formData = new FormData(form);
                const cancelSelect = form.querySelector('select[name="cancel_reason_select"]');
                const cancelOther = form.querySelector('input[name="cancel_reason_other"]');
                let finalReason = '';
                if (cancelSelect) {
                    finalReason = cancelSelect.value;
                    if (finalReason === 'Other') {
                        // require typed reason when Other is selected
                        if (!cancelOther || !cancelOther.value.trim()) {
                            alert('Please enter a reason when "Other" is selected.');
                            return;
                        }
                        finalReason = cancelOther.value.trim();
                    }
                } else if (cancelOther && cancelOther.value.trim()) {
                    finalReason = cancelOther.value.trim();
                }
                // Ensure backend receives `cancel_reason` field
                if (finalReason) {
                    formData.set('cancel_reason', finalReason);
                }

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
                    if (modal) {
                         modal.hide();
                    }

                    // Replace Active button with Cancelled form
                    const statusCol = document.querySelector(`#status-col-${data.id}`);
              
                    statusCol.innerHTML = `
                        <form action="/admin/cancelled_permits/${data.id}/activate" method="POST" class="activate-permit-form">
                            <input type="hidden" name="_token" value="${form.querySelector('input[name=_token]').value}">
                            <button type="submit" class="btn btn-sm btn-danger w-100" ${!['admin','super-admin'].includes(userRole) ? 'disabled' : ''}>
                                Cancelled
                            </button>
                        </form>
                    `;

                    // Disable Edit / View / Print buttons
                    const row = document.querySelector(`#permit-row-${data.id}`);
                    row.querySelectorAll('a.btn').forEach(btn => {
                        btn.setAttribute('disabled', 'disabled');
                        btn.style.pointerEvents = 'none';
                        btn.style.opacity = 0.5;
                        btn.classList.remove('btn-warning');
                        btn.classList.remove('btn-primary');
                        btn.classList.add('btn-secondary'); // Use secondary for cancelled actions
                        btn.style.backgroundColor = '';
                        btn.style.borderColor = '';
                    });
                }

            } catch(err) {
                console.error(err);
                alert("Network error or server error. Check console.");
            }
        }
    });

    // Delegate Activate Permit
    container.addEventListener('submit', async function(e){
        if(e.target.classList.contains('activate-permit-form')){
            e.preventDefault();

            if(!['admin','super-admin'].includes(userRole)) {
                alert("Only admins can activate cancelled permits.");
                return;
            }

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
                                            <select name="cancel_reason_select" class="form-select" ${!['admin','super-admin'].includes(userRole) ? 'disabled' : ''}>
                                                <option value="Expired Date">Expired Date</option>
                                                <option value="Lost Permit">Lost Permit</option>
                                                <option value="Security Concern">Security Concern</option>
                                                <option value="Expired Police Report / Insurance">Expired Police Report / Insurance</option>
                                                <option value="Fraudulent">Fraudulent</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <input type="text" name="cancel_reason_other" class="form-control mt-2" placeholder="If Other, type here" ${!['admin','super-admin'].includes(userRole) ? 'disabled' : ''}>
                                        </div>
                                        <div class="modal-footer">
                                            ${['admin','super-admin'].includes(userRole) ? 
                                                '<button type="submit" class="btn btn-danger w-100">Confirm Cancel</button>' : 
                                                '<button type="button" class="btn btn-danger w-100" disabled>Confirm Cancel (Admins Only)</button>'}
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
                        // Restore original classes
                        btn.classList.remove('btn-secondary'); 
                        if (btn.textContent.trim() === 'Edit' || btn.textContent.trim() === 'View Group') {
                            btn.classList.add('btn-warning');
                        } else if (btn.textContent.trim() === 'Print') {
                            btn.classList.add('btn-primary');
                        }
                    });
                }

            } catch(err) {
                console.error(err);
                alert("Network error or server error. Check console.");
            }
        }
    });
    
    // Initialize modals which were part of the initial static HTML
    document.querySelectorAll('.modal').forEach(modalElement => {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: false
            });
        }
    });

});
</script>
@endpush