@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h3>Cancelled Permit Details</h3>

    <table class="table table-bordered">
        @foreach($cancelledPermit->getAttributes() as $key => $value)
            <tr>
                <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                <td>{{ $value }}</td>
            </tr>
        @endforeach
    </table>

    <a href="{{ route('admin.cancelled_permits.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
