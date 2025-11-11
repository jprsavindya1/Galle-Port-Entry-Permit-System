<style>
    .company-dashboard-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        padding: 1.5rem;
        margin-top: 1.5rem;
        border: none;
    }
    .company-dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .company-dashboard-title {
        font-size: 1.6rem;
        font-weight: 600;
        color: #1976d2;
        letter-spacing: 0.5px;
    }
    .company-dashboard-table {
        background: #f5faff;
        border-radius: 0.75rem;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .company-dashboard-table th {
        background: #e3f2fd;
        color: #1976d2;
        font-weight: 500;
        border-bottom: 2px solid #bbdefb;
    }
    .company-dashboard-table td {
        background: #f8fafc;
        color: #333;
        vertical-align: middle;
    }
    .company-action-btn {
        font-size: 0.9rem;
        padding: 0.35rem 0.6rem;
        border-radius: 0.45rem;
        margin-right: 0.25rem;
        transition: background 0.2s, color 0.2s;
    }
    .company-action-btn.edit { background: #fff3e0; color:#ff9800; border:1px solid #ffe0b2 }
    .company-action-btn.delete { background: #ffebee; color:#e53935; border:1px solid #ffcdd2 }
</style>

<div class="container" id="designation-list-container">
    <div class="company-dashboard-card">
        <div class="company-dashboard-header">
            <div class="company-dashboard-title"><i class="bi bi-people me-2"></i> Designation List</div>
            <a href="{{ route('admin.designations.create') }}" class="btn btn-success ajax-link" style="border-radius:0.5rem;font-weight:500;"><i class="bi bi-plus-lg me-1"></i> Add New Designation</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form id="designation-search-form" method="GET" action="{{ route('admin.designations.index') }}" class="row g-3 mb-3 align-items-end ajax-search-form" style="background:linear-gradient(135deg,#e3f2fd 0%,#f8fafc 100%);border-radius:0.75rem;padding:0.75rem;">
            <div class="col-md-8">
                <label class="form-label mb-1" for="search"><i class="bi bi-search me-1"></i> Search</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control" placeholder="Search by designation" style="border-radius:0.5rem;border:1px solid #bbdefb;background:#f8fafc;">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100" style="border-radius:0.5rem;font-weight:500;"><i class="bi bi-search"></i> Search</button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('admin.designations.index') }}" class="btn btn-secondary w-100" style="border-radius:0.5rem;font-weight:500;"><i class="bi bi-arrow-clockwise"></i> Reset</a>
            </div>
        </form>

        <div class="table-responsive company-dashboard-table">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Name</th>
                        <th class="text-center" style="width:150px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($designations as $designation)
                        <tr>
                            <td>{{ $loop->iteration + ($designations->currentPage() - 1) * $designations->perPage() }}</td>
                            <td>{{ $designation->name }}</td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.designations.edit', $designation) }}" class="company-action-btn edit btn btn-sm ajax-link"><i class="bi bi-pencil-square me-1"></i> Edit</a>
                                    <form action="{{ route('admin.designations.destroy', $designation) }}" method="POST" class="ajax-delete" data-reload-url="{{ route('admin.designations.index') }}">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="company-action-btn delete btn btn-sm"><i class="bi bi-trash me-1"></i> Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted">No designations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(method_exists($designations, 'links'))
            <div class="mt-3">
                {{ $designations->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('designation-search-form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchValue = this.querySelector('input[name="search"]').value;
            const url = this.getAttribute('action') + '?search=' + encodeURIComponent(searchValue);
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => { document.getElementById('designation-list-container').innerHTML = html; })
                .catch(error => { console.error('Search failed:', error); alert('Search failed. Please try again.'); });
        });
    }
    
    document.addEventListener('click', function(e) {
        if (e.target.closest('.pagination a')) {
            e.preventDefault();
            const url = e.target.closest('.pagination a').getAttribute('href');
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(response => response.text())
                .then(html => { document.getElementById('designation-list-container').innerHTML = html; })
                .catch(error => { console.error('Pagination failed:', error); });
        }
    });
});
</script>
@endpush
