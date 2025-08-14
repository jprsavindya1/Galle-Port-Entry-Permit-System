<div class="container mt-4">
    <h3>Designation List</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

  <form id="company-search-form" method="GET" action="{{ route('admin.designations.index') }}" class="d-flex mb-3 ajax-search-form">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search companies..." class="form-control me-2" />
    <button type="submit" class="btn btn-outline-primary">Search</button>
</form>

   
    <a href="{{ route('admin.designations.create') }}" 
       class="btn btn-primary mb-3 ajax-link">Add New Designation</a>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Name</th>
                <th width="140">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($designations as $designation)
                <tr>
                    <td>{{ $loop->iteration + ($designations->currentPage() - 1) * $designations->perPage() }}</td>
                    <td>{{ $designation->name }}</td>
                    <td>
                        <a href="{{ route('admin.designations.edit', $designation) }}" 
                           class="btn btn-sm btn-warning ajax-link">Edit</a>
                        <form action="{{ route('admin.designations.destroy', $designation) }}" method="POST" class="d-inline ajax-delete" data-reload-url="{{ route('admin.designations.index') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3">No designations found.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $designations->withQueryString()->links() }}
</div>
