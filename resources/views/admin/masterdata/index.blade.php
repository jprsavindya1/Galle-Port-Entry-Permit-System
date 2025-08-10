@extends('layouts.app')

@section('title', 'Edit Master Data')

<style>
    .dashboard-card {
        transition: transform 0.2s, box-shadow 0.2s;
        cursor: pointer;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        border-right: 4px solid #6a9ed5ff;
    }
    .dashboard-card h4 {
        font-weight: 700;
        font-size: 1.2rem;
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
                    <h4>Companies</h4>
                    <p>Manage company info</p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card dashboard-card text-center shadow-sm rounded-3 h-100 load-section"
                 data-url="{{ route('admin.designations.index') }}">
                <div class="card-body">
                    <h4>Designations</h4>
                    <p>Manage job titles</p>
                </div>
            </div>
        </div>

        <!-- Add other cards as needed -->

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

    // Ajax link clicks inside dynamic content
    $('#dynamic-content').on('click', '.ajax-link', function(e){
        e.preventDefault();
        loadAjaxContent($(this).attr('href'));
    });
$('#dynamic-content').on('submit', '.ajax-delete', function(e){
    e.preventDefault();
    if (!confirm('Are you sure you want to delete this item?')) return;

    let form = $(this);
    let reloadUrl = form.data('reload-url');  // get URL to reload after delete

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

    // Ajax search form submit inside dynamic content
    $('#dynamic-content').on('submit', '#designation-search-form', function(e){
        e.preventDefault();
        let url = $(this).attr('action');
        let query = $(this).serialize();
        loadAjaxContent(url + '?' + query);
    });
});
</script>

@endsection
