@extends('layouts.app')
@section('title', 'Designation List')
@section('content')

<div class="container mt-4">
    <h3>Designations</h3>
    <a href="{{ route('admin.designations.create') }}" class="btn btn-primary mb-3">Add Designation</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr><th>#</th><th>Name</th><th>Actions</th></tr>
        </thead>
        <tbody>
            @foreach($designations as $designation)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $designation->name }}</td>
                <td>
                    <a href="{{ route('admin.designations.edit', $designation->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    <form action="{{ route('admin.designations.destroy', $designation->id) }}" method="POST" class="d-inline delete-form">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger delete-btn">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete button clicks
        const deleteForms = document.querySelectorAll('.delete-form');
        
        deleteForms.forEach(form => {
            const deleteBtn = form.querySelector('.delete-btn');
            if (deleteBtn) {
                deleteBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Delete Designation?',
                        text: 'Are you sure you want to delete this designation?',
                        icon: 'warning',
                        iconColor: '#e53935',
                        showCancelButton: true,
                        confirmButtonColor: '#e53935',
                        cancelButtonColor: '#757575',
                        confirmButtonText: 'Yes, Delete',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            popup: 'delete-popup',
                            title: 'delete-title',
                            confirmButton: 'delete-confirm-btn',
                            cancelButton: 'delete-cancel-btn'
                        },
                        buttonsStyling: true,
                        width: '400px'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            }
        });
    });
</script>

<style>
    /* Custom SweetAlert2 styling for delete action */
    .delete-popup {
        border-radius: 0.75rem !important;
        padding: 1.5rem !important;
    }
    
    .delete-title {
        color: #e53935 !important;
        font-size: 1.25rem !important;
        font-weight: 600 !important;
    }
    
    .swal2-html-container {
        font-size: 0.95rem !important;
        color: #555 !important;
    }
    
    .delete-confirm-btn {
        border-radius: 0.375rem !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
    }
    
    .delete-cancel-btn {
        border-radius: 0.375rem !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
    }
    
    .swal2-icon.swal2-warning {
        border-color: #e53935 !important;
        color: #e53935 !important;
    }
</style>

@endsection
