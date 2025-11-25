@extends('layouts.app')

@section('title', 'Edit Master Data')

@section('content')

<style>
    .user-dashboard-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f8fafc 100%);
        border-radius: 1rem;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        padding: 1.25rem;
        margin-top: 1.5rem;
        border: none;
    }
    .user-dashboard-title {
        font-size: 1.6rem;
        font-weight: 600;
        color: #1976d2;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
    }
    .master-grid .master-card {
        background: #fff;
        border: 1px solid #e3f2fd;
        border-radius: .75rem;
        padding: 1.3rem 1rem;
        /* center contents vertically and horizontally */
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: transform .15s ease, box-shadow .15s ease;
        cursor: pointer;
        height: 100%;
    }
    .master-grid .master-card:hover { transform: translateY(-6px); box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
    .master-grid .card-icon { width:56px; height:56px; opacity:.95; margin-bottom:1rem }
    .master-grid h4 { color:#0d47a1; font-weight:700; margin-bottom:.25rem }
    .master-grid p { color:#386fa4; margin-bottom:0 }
    #dynamic-content { margin-top:1.25rem }
    
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
    
    /* Custom SweetAlert2 styling for success alerts */
    .success-popup {
        border-radius: 0.75rem !important;
        padding: 1.5rem !important;
    }
    
    .success-title {
        color: #4caf50 !important;
        font-size: 1.25rem !important;
        font-weight: 600 !important;
    }
    
    .success-confirm-btn {
        border-radius: 0.375rem !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
        background-color: #4caf50 !important;
        border-color: #4caf50 !important;
    }
    
    /* Custom SweetAlert2 styling for error alerts */
    .error-popup {
        border-radius: 0.75rem !important;
        padding: 1.5rem !important;
    }
    
    .error-title {
        color: #e53935 !important;
        font-size: 1.25rem !important;
        font-weight: 600 !important;
    }
    
    .error-confirm-btn {
        border-radius: 0.375rem !important;
        padding: 0.5rem 1rem !important;
        font-size: 0.9rem !important;
        font-weight: 500 !important;
        background-color: #e53935 !important;
        border-color: #e53935 !important;
    }
</style>
<div class="container py-4">
    <div class="user-dashboard-card">
        <div class="user-dashboard-title"><i class="bi bi-tools me-2"></i> Edit Master Data</div>

        <div class="row g-4 master-grid">

            <div class="col-md-3">
                <div class="master-card load-section" data-url="{{ route('admin.companies.index') }}">
                    <div class="icon-wrapper">
                        <img src="{{ asset('images/company.gif') }}" class="card-icon" alt="Companies Icon">
                    </div>
                    <h4>Companies</h4>
                    <p>Manage company information</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="master-card load-section" data-url="{{ route('admin.designations.index') }}">
                    <div class="icon-wrapper">
                        <img src="{{ asset('images/career.gif') }}" class="card-icon" alt="Designations Icon">
                    </div>
                    <h4>Designations</h4>
                    <p>Manage Job Titles</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="master-card load-section" data-url="{{ route('admin.reasons.index') }}">
                    <div class="icon-wrapper">
                        <img src="{{ asset('images/metal-detector.gif') }}" class="card-icon" alt="Reasons Icon">
                    </div>
                    <h4>Reasons</h4>
                    <p>Manage Entry Reasons</p>
                </div>
            </div>

            <div class="col-md-3">
                <div class="master-card load-section" data-url="{{ route('admin.vehicles.index') }}">
                    <div class="icon-wrapper">
                        <img src="{{ asset('images/car.gif') }}" class="card-icon" alt="Vehicles Icon">
                    </div>
                    <h4>Vehicles</h4>
                    <p>Manage Vehicles</p>
                </div>
            </div>

            <!-- Add other cards -->

        </div>

        <!-- Dynamic Content Area -->
        <div id="dynamic-content"></div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(function(){
    function loadAjaxContent(url) {
        $('#dynamic-content').html('<div class="text-center p-5"><div class="spinner-border text-primary"></div></div>');
        $.ajax({
            url: url,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(data){
                $('#dynamic-content').html(data);
            },
            error: function(){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load content.',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'error-popup',
                        title: 'error-title',
                        confirmButton: 'error-confirm-btn'
                    }
                });
            }
        });
    }

    // Click master card
    $('.load-section').on('click', function(){
        let url = $(this).data('url');
        loadAjaxContent(url);
    });

    // Ajax link clicks inside dynamic content (Add/Edit links)
    $('#dynamic-content').on('click', '.ajax-link', function(e){
        e.preventDefault();
        loadAjaxContent($(this).attr('href'));
    });

    // Ajax pagination links inside dynamic content
    $(document).on('click', 'a[href*="?page="]', function(e){
        e.preventDefault();
        loadAjaxContent($(this).attr('href'));
    });

    // Ajax delete inside dynamic content with SweetAlert2
    $('#dynamic-content').on('submit', '.ajax-delete', function(e){
        e.preventDefault();
        
        let form = $(this);
        let reloadUrl = form.data('reload-url');  // URL to reload after delete

        Swal.fire({
            title: 'Delete Item?',
            text: 'Are you sure you want to delete this item?',
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
                    success: function(response){
                        if(response.success){
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: 'Item deleted successfully.',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'success-popup',
                                    title: 'success-title',
                                    confirmButton: 'success-confirm-btn'
                                }
                            });
                            if(reloadUrl){
                                loadAjaxContent(reloadUrl);
                            } else {
                                alert('Reload URL not specified.');
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message || 'Delete failed.',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'error-popup',
                                    title: 'error-title',
                                    confirmButton: 'error-confirm-btn'
                                }
                            });
                        }
                    },
                    error: function(){
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while deleting.',
                            confirmButtonText: 'OK',
                            customClass: {
                                popup: 'error-popup',
                                title: 'error-title',
                                confirmButton: 'error-confirm-btn'
                            }
                        });
                    }
                });
            }
        });
    });

    // Ajax form submit (create/edit) inside dynamic content
    $('#dynamic-content').on('submit', 'form.ajax-form', function(e){
        e.preventDefault();

        let form = $(this);
        let url = form.attr('action');
        let method = form.find('input[name=_method]').val() || form.attr('method');

        $.ajax({
            url: url,
            type: method,
            data: form.serialize(),
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response){
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'success-popup',
                            title: 'success-title',
                            confirmButton: 'success-confirm-btn'
                        }
                    });
                    $('#dynamic-content').html(response.html);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to save. Please check your input.',
                        confirmButtonText: 'OK',
                        customClass: {
                            popup: 'error-popup',
                            title: 'error-title',
                            confirmButton: 'error-confirm-btn'
                        }
                    });
                }
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to save. Please check your input.',
                    confirmButtonText: 'OK',
                    customClass: {
                        popup: 'error-popup',
                        title: 'error-title',
                        confirmButton: 'error-confirm-btn'
                    }
                });
            }
        });
    });

    // Universal AJAX search submit for any form with class 'ajax-search-form' inside dynamic content
    $('#dynamic-content').on('submit', 'form.ajax-search-form', function(e){
        e.preventDefault();
        let url = $(this).attr('action');
        let query = $(this).serialize();
        loadAjaxContent(url + '?' + query);
    });
});
</script>

<!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection
