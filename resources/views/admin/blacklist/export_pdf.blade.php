<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Blacklist Report</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #333; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h3>Blacklist Report</h3>
    <table>
        <thead>
            <tr>
                <th>NIC</th>
                <th>Full Name</th>
                <th>Company</th>
                <th>Vehicle</th>
                <th>Reason</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($blacklists as $entry)
                <tr>
                    <td>{{ $entry->nic }}</td>
                    <td>{{ $entry->full_name }}</td>
                    <td>{{ $entry->company_name }}</td>
                    <td>{{ $entry->vehicle_number }}</td>
                    <td>{{ $entry->reason }}</td>
                    <td>{{ $entry->created_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
