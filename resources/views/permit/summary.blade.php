<h2>Permit Summary</h2>

<table border="1" cellpadding="5">
    <tr>
        <th>Full Name</th>
        <th>ID</th>
        <th>Pass Type</th>
        <th>Issue</th>
        <th>Company</th>
    </tr>
    @foreach ($cart as $permit)
        <tr>
            <td>{{ $permit['full_name'] }}</td>
            <td>{{ $permit['id_number'] }}</td>
            <td>{{ $permit['pass_type'] }}</td>
            <td>{{ $permit['issue_type'] }}</td>
            <td>{{ $permit['company_name'] }}</td>
        </tr>
    @endforeach
</table>

<h3>Total Payment Due: Rs. {{ $total }}</h3>

<form method="POST" action="{{ route('permit.submitAll') }}">
    @csrf
    <button type="submit">Confirm & Submit All</button>
</form>
