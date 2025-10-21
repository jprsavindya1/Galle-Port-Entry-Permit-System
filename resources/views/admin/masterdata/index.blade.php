@extends('layouts.app')

@section('title', 'Edit Master Data')

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
</style>


@section('content')
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
        $.get(url, function(data){
            $('#dynamic-content').html(data);
        }).fail(function(){
            $('#dynamic-content').html('<div class="alert alert-danger">Failed to load content.</div>');
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

    // Ajax delete inside dynamic content
    $('#dynamic-content').on('submit', '.ajax-delete', function(e){
        e.preventDefault();
        if (!confirm('Are you sure you want to delete this item?')) return;

        let form = $(this);
        let reloadUrl = form.data('reload-url');  // URL to reload after delete

        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(response){
                if(response.success){
                    if(reloadUrl){
                        loadAjaxContent(reloadUrl);
                    } else {
                        alert('Reload URL not specified.');
                    }
                } else {
                    alert(response.message || 'Delete failed.');
                }
            },
            error: function(){
                alert('An error occurred while deleting.');
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
            success: function(response){
                $('#dynamic-content').html(response);
            },
            error: function(xhr){
                alert('Failed to save. Please check your input.');
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

@endsection
