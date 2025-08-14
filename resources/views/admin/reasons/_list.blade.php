<div class="container mt-4">
    <h3>Reasons List</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <div>
    <form id="reason-search-form" method="GET" action="{{ route('admin.reasons.index') }}" class="d-flex mb-3 ajax-search-form">
    <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search reasons...">
    <button type="submit" class="btn btn-outline-primary">Search</button>   
</form>
<a href="{{ route('admin.reasons.create') }}" class="btn btn-primary mb-3 ajax-link">Add New Reasons</a>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
                <th>#</th>
                <th>Name</th>
                <th width="140">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($reasons as $reason)
                <tr>
                    <td>{{ $loop->iteration + ($reasons->currentPage() - 1) * $reasons->perPage() }}</td>
                    <td>{{ ucfirst($reason->name) }}</td>
                    <td>
                        <a href="{{ route('admin.reasons.edit', $reason) }}" class="btn btn-sm btn-warning ajax-link">Edit</a>
                        <form action="{{ route('admin.reasons.destroy', $reason) }}" method="POST" class="d-inline ajax-delete" data-reload-url="{{ route('admin.reasons.index') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">No reasons found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $reasons->withQueryString()->links() }}
</div>
