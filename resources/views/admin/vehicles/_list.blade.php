@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<form id="vehicle-search-form" method="GET" action="{{ route('admin.vehicles.index') }}" class="d-flex mb-3 ajax-search-form">
    <input type="text" name="search" value="{{ request('search') }}" class="form-control me-2" placeholder="Search vehicles...">
    <button type="submit" class="btn btn-outline-primary">Search</button>
</form>

<a href="{{ route('admin.vehicles.create') }}" class="btn btn-primary mb-3 ajax-link">Add New Vehicle</a>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>#</th>
            <th>Vehicle Name</th>
            <th>Vehicle Code</th>
            <th>Base Rate (Rs)</th>
            <th width="140">Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($vehicles as $vehicle)
            <tr>
                <td>{{ $loop->iteration + ($vehicles->currentPage() - 1) * $vehicles->perPage() }}</td>
                <td>{{ $vehicle->name }}</td>
                <td>{{ $vehicle->code }}</td>
                <td>{{ number_format($vehicle->rate, 2) }}</td>
                <td>
                    <a href="{{ route('admin.vehicles.edit', $vehicle) }}" class="btn btn-sm btn-warning ajax-link">Edit</a>
                    <form action="{{ route('admin.vehicles.destroy', $vehicle) }}" method="POST" class="d-inline ajax-delete" data-reload-url="{{ route('admin.vehicles.index') }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No vehicles found.</td></tr>
        @endforelse
    </tbody>
</table>

{{ $vehicles->withQueryString()->links() }}
