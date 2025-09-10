@extends('layouts.app')

@section('content')
<div class="container my-4">
    <h1 class="mb-4 fw-bold text-danger" style="font-size: 2rem;">Trashed Permits</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Search Form -->
    <form method="GET" action="{{ route('admin.cancelled_permits.trash') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by permit ID, name, company or vehicle" value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    @if($trashedPermits->isEmpty())
        <div class="alert alert-info">No cancelled permits in trash.</div>
    @else
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark text-white">
                    <tr>
                        <th>Permit ID</th>
                        <th>Invoice No</th>
                        <th>ID Number</th>
                        <th>Company</th>
                        <th>Vehicle Number</th>
                        <th>Cancelled By</th>
                        <th>Cancelled At</th>
                        <th>Deleted At</th>
                        <th>Activity Log</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trashedPermits as $permit)
                        @php
                            $logs = is_string($permit->activity_log) ? json_decode($permit->activity_log, true) : $permit->activity_log;
                            $invoice = $permit->invoice_id ?? optional($permit->payment)->invoice_id ?? '-';
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $permit->permit_id }}</td>
                            <td class="text-success">{{ $invoice }}</td>
                            <td class="text-info">{{ $permit->id_number }}</td>
                            <td>{{ $permit->company_name }}</td>
                            <td class="text-secondary">{{ $permit->vehicle_number }}</td>
                            <td class="text-danger">{{ $permit->cancelled_by ?? '-' }}</td>
                            <td>{{ $permit->cancelled_at }}</td>
                            <td>{{ $permit->deleted_at }}</td>
                            <td>
                                @if(!empty($logs) && is_array($logs))
                                    <button class="btn btn-sm btn-outline-dark toggle-log-btn" 
                                        type="button" 
                                        data-bs-target="#log-{{ $permit->id }}">
                                        View Logs ({{ count($logs) }})
                                    </button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <!-- Restore -->
                                <form action="{{ route('admin.cancelled_permits.restore', $permit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm">Restore</button>
                                </form>

                                <!-- Force Delete 
                                <form action="{{ route('admin.cancelled_permits.forceDelete', $permit->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Permanently delete this permit?')">Delete</button>
                                </form>
                                -->
                            </td>
                        </tr>

                        @if(!empty($logs) && is_array($logs))
                        <tr>
                            <td colspan="10" class="p-0">
                                <div class="collapse" id="log-{{ $permit->id }}">
                                    <div class="p-2 bg-white border-start border-primary">
                                        @foreach($logs as $log)
                                            <div class="border rounded p-2 mb-2">
                                                <div class="small mb-1">
                                                    <strong class="text-danger">Action:</strong> <span class="text-dark">{{ $log['action'] ?? '-' }}</span> |
                                                    <strong class="text-warning">User:</strong> <span class="text-dark">{{ $log['user_name'] ?? 'N/A' }} ({{ $log['role'] ?? '-' }})</span> |
                                                    <strong class="text-info">Timestamp:</strong> <span class="text-dark">{{ $log['timestamp'] ?? '-' }}</span>
                                                </div>
                                                @if(!empty($log['details']) && is_array($log['details']))
                                                    <div style="overflow-x:auto">
                                                        <table class="table table-sm table-bordered mb-0">
                                                            <tbody>
                                                                @foreach($log['details'] as $key => $value)
                                                                    <tr>
                                                                        <th class="p-1 text-dark">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                                        <td class="p-1 text-dark">{{ $value }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end">
            {{ $trashedPermits->withQueryString()->links() }}
        </div>
    @endif
</div>

<div class="mb-3">
    <a href="{{ route('admin.cancelled_permits.index') }}" class="btn btn-warning mb-3">Go Back</a>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelectorAll('.toggle-log-btn').forEach(button => {
    const targetSelector = button.getAttribute('data-bs-target');
    const collapseEl = document.querySelector(targetSelector);
    const bsCollapse = new bootstrap.Collapse(collapseEl, { toggle: false });

    collapseEl.addEventListener('show.bs.collapse', () => {
        button.textContent = `Hide Logs (${collapseEl.querySelectorAll('div.border').length})`;
    });
    collapseEl.addEventListener('hide.bs.collapse', () => {
        button.textContent = `View Logs (${collapseEl.querySelectorAll('div.border').length})`;
    });

    button.addEventListener('click', () => bsCollapse.toggle());
});
</script>
@endpush
@endsection
