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
        .history { background-color: #f9f9f9; }
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
                <th>Action</th>
                <th>Performed By</th>
                <th>Date/Time</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($allEntries as $entry)
                @php
                    $isHistory = $entry instanceof \App\Models\BlacklistHistory;
                    $action = $isHistory ? $entry->action : 'active';
                    $performedBy = $isHistory ? $entry->admin_name : ($entry->activities->first()->user_name ?? '—');
                    $dateTime = $isHistory ? $entry->created_at : ($entry->activities->first()->created_at ?? $entry->created_at);
                @endphp
                <tr class="{{ $isHistory ? 'history' : '' }}">
                    <td>{{ $entry->nic }}</td>
                    <td>{{ $entry->full_name }}</td>
                    <td>{{ $entry->company_name }}</td>
                    <td>{{ $entry->vehicle_number }}</td>
                    <td>{{ $entry->reason }}</td>
                    <td>{{ ucfirst($action) }}</td>
                    <td>{{ $performedBy }}</td>
                    <td>{{ $dateTime->format('Y-m-d H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
