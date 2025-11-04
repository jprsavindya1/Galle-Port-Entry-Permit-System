@extends('layouts.app')

@section('title', 'Manage Reasons')

@section('content')
<div class="p-3">
    <h2>Manage Reasons</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div id="reasons-list">
        @include('admin.reasons._list', ['reasons' => $reasons])
    </div>
</div>

<script>
$(function() {
    // AJAX search submit
    $(document).on('submit', '#reason-search-form', function(e) {
        e.preventDefault();
        let url = $(this).attr('action');
        let query = $(this).serialize();
        $.get(url + '?' + query, function(data) {
            $('#reasons-list').html(data);
            if(history.pushState) {
                let newurl = url + '?' + query;
                window.history.pushState({path:newurl}, '', newurl);
            }
        });
    });

    // AJAX pagination links
    $(document).on('click', '#reasons-list .pagination a', function(e) {
        e.preventDefault();
        let url = $(this).attr('href');
        $.get(url, function(data) {
            $('#reasons-list').html(data);
            if(history.pushState) {
                window.history.pushState({path:url}, '', url);
            }
        });
    });

    // AJAX delete with SweetAlert2
    $(document).on('submit', '.ajax-delete', function(e) {
        e.preventDefault();
        
        let form = $(this);
        
        Swal.fire({
            title: 'Delete Reason?',
            text: 'Are you sure you want to delete this reason?',
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
                $.ajax({
                    url: form.attr('action'),
                    type: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        if(res.success){
                            // Reload current page after delete
                            let currentUrl = window.location.href;
                            $.get(currentUrl, function(data) {
                                $('#reasons-list').html(data);
                            });
                        } else {
                            alert('Delete failed.');
                        }
                    },
                    error: function(){
                        alert('Error occurred while deleting.');
                    }
                });
            }
        });
    });
});
</script>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
