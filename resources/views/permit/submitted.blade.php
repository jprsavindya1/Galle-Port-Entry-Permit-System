@extends('layouts.app')

@section('content')
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
                                    @else
                                        <th>ID Type</th>
                                        <th>ID Number</th>
                                        <th>Full Name</th>
                                        <th>Company Name</th>
                                        <th>Pass Type</th>
                                        <th>From Date</th>
                                        <th>To Date</th>
                                         <th>issue type</th>
                                    @endif
                                    <th class="sticky-col status-col" style="width:120px;">Status</th>
                                    <th class="sticky-col actions-col" style="width:120px;">Actions</th>
                                    <th class="sticky-col view-col" style="width:120px;">View</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($group as $permit)
                                    <tr>
                                        @if($permit->type === 'VP')
                                            <td>{{ $permit->vehicle_number }}</td>
                                            <td>{{ $permit->owner_name }}</td>
                                            <td>{{ $permit->revenue_license_number }}</td>
                                            <td>{{ $permit->insurance_number ?? '-' }}</td>
                                            <td>{{ $permit->company_name }}</td>
                                            <td>{{ ucfirst($permit->issue_type) }}</td>
                                            <td>{{ $permit->from_date }}</td>
                                            <td>{{ $permit->to_date }}</td>
                                        @else
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
                                        <td class="sticky-col status-col">
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
                                                            <form action="{{ route('permits.cancel', $permit) }}" 
                                                                  method="POST" 
                                                                  class="cancel-permit-form">
                                                                @csrf
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
                                            @else
                                                <form action="{{ route('permits.activate', $permit) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger w-100">Cancelled</button>
                                                </form>
                                            @endif
                                        </td>

                                        <!-- Actions -->
                                        <td class="sticky-col actions-col">
                                            <a href="{{ route('permits.edit', $permit) }}" class="btn btn-sm btn-warning w-100 mb-1">Edit</a>
                                            @if(Auth::user()->role === 'admin' || Auth::user()->role === 'super-admin')
                                                <form action="{{ route('permits.destroy', $permit) }}" method="POST" onsubmit="return confirm('Delete this permit?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger w-100">Delete</button>
                                                </form>
                                            @endif
                                        </td>

                                        <!-- View -->
                                        <td class="sticky-col view-col">
                                            <a href="{{ route('payment.invoice', $permit->submission_id) }}" class="btn btn-sm btn-warning w-100 mb-1">View Group</a>
                                            <a href="{{ route('permit.print.single', $permit->id) }}" target="_blank" class="btn btn-sm btn-primary w-100">Print</a>
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
.actions-col { left: 120px; } /* adjust width if button width changes */
.view-col { left: 240px; } /* adjust width if previous two columns width changes */
</style>
@endpush

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll('.cancel-permit-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            let actionUrl = form.getAttribute('action');
            let formData = new FormData(form);
            fetch(actionUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'cancelled') {
                    let modal = bootstrap.Modal.getInstance(form.closest('.modal'));
                    modal.hide();
                    let btn = document.querySelector('button[data-bs-target="#cancelModal' + data.id + '"]');
                    btn.outerHTML = `
                        <form action="/permits/${data.id}/activate" method="POST">
                            <input type="hidden" name="_token" value="${form.querySelector('input[name="_token"]').value}">
                            <button type="submit" class="btn btn-sm btn-danger w-100">Cancelled</button>
                        </form>
                    `;
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>
@endpush
