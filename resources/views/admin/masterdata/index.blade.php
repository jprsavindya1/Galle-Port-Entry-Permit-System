@extends('layouts.app')

@section('title', 'Edit Master Data')

<style>
.dashboard-card {
    position: relative;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    overflow: hidden;
    cursor: pointer;
}

.dashboard-card::after {
    content: "";
    position: absolute;
    right: 0;
    top: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(180deg, #0073e6, #4fc3f7);
    transition: width 0.3s ease;
    z-index: 1;
}

.dashboard-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

.dashboard-card:hover::after {
    width: 6px;
}

/* --- Icon styling --- */
.icon-wrapper {
    width: 100%;
    display: flex;
    justify-content: center;
    margin-bottom: 0.75rem;
}

.card-icon {
    width: 50px;
    height: 50px;
    opacity: 0.7;
    transition: transform 0.3s ease, opacity 0.3s ease;
    filter: grayscale(100%);
}

.dashboard-card:hover .card-icon {
    opacity: 1;
    transform: scale(1.15);
    filter: grayscale(0%);
}

/* --- Text consistency --- */
.dashboard-card h4 {
    font-weight: 600;
    margin-top: 0.5rem;
    color: #222;
}

.dashboard-card p {
    font-size: 0.95rem;
    color: #555;
    margin-bottom: 0;
}
</style>


@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Edit Master Data</h2>
    <div class="row g-4">

      <div class="col-md-3">
    <div class="card dashboard-card text-center shadow-sm rounded-3 h-100 load-section"
         data-url="{{ route('admin.companies.index') }}">
        <div class="card-body">
            <div class="icon-wrapper">
                <img src="{{ asset('images/company.gif') }}" class="card-icon" alt="Companies Icon">
            </div>
            <h4>Companies</h4>
            <p>Manage company information</p>
        </div>
    </div>
</div>


        <div class="col-md-3">
            <div class="card dashboard-card text-center shadow-sm rounded-3 h-100 load-section"
                 data-url="{{ route('admin.designations.index') }}">
                <div class="card-body">
                     <div class="icon-wrapper">
                <img src="{{ asset('images/career.gif') }}" class="card-icon" alt="Companies Icon">
            </div>
                    <h4>Designations</h4>
                    <p>Manage Job Titles</p>
                </div>
            </div>
        </div>

       <div class="col-md-3">
    <div class="card dashboard-card text-center shadow-sm rounded-3 h-100 load-section"
         data-url="{{ route('admin.reasons.index') }}">
        <div class="card-body">
             <div class="icon-wrapper">
                <img src="{{ asset('images/metal-detector.gif') }}" class="card-icon" alt="Companies Icon">
            </div>
            <h4>Reasons</h4>
            <p>Manage Entry Reasons</p>
        </div>
    </div>
</div>

<div class="col-md-3">
    <div class="card dashboard-card text-center shadow-sm rounded-3 h-100 load-section"
         data-url="{{ route('admin.vehicles.index') }}">
        <div class="card-body">
             <div class="icon-wrapper">
                <img src="{{ asset('images/car.gif') }}" class="card-icon" alt="Companies Icon">
            </div>
            <h4>Vehicles</h4>
            <p>Manage Vehicles</p>
        </div>
    </div>
</div>

        <!-- Add other cards -->

    </div>

    <!-- Dynamic Content Area -->
    <div id="dynamic-content" class="mt-4"></div>
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

    // Click dashboard card
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
