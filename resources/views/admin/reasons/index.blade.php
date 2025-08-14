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

    // AJAX delete
    $(document).on('submit', '.ajax-delete', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to delete this reason?')) return;

        let form = $(this);
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
    });
});
</script>
@endsection
