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
                <th>Added By</th>
                <th>Added On</th>
                <th>Status</th>
                @if($isHistory)
                    <th>Reinstated By</th>
                    <th>Reinstated On</th>
                @endif
            </tr>
        </thead>
        <tbody>
            @foreach ($entries as $entry)
                @if($isHistory)
                    @php
                        $status = $entry->status ?? ucfirst($entry->action);
                        $addedBy = $entry->admin_name ?? '—';
                        $addedOn = $entry->created_at;
                        $reinstatedBy = $entry->reinstated_by ?? '—';
                        $reinstatedOn = $entry->reinstated_on ? \Carbon\Carbon::parse($entry->reinstated_on)->format('Y-m-d H:i') : '—';
                    @endphp
                    <tr class="history">
                        <td>{{ $entry->nic }}</td>
                        <td>{{ $entry->full_name }}</td>
                        <td>{{ $entry->company_name }}</td>
                        <td>{{ $entry->vehicle_number }}</td>
                        <td>{{ $entry->reason }}</td>
                        <td>{{ $addedBy }}</td>
                        <td>{{ $addedOn->format('Y-m-d H:i') }}</td>
                        <td>{{ $status }}</td>
                        <td>{{ $reinstatedBy }}</td>
                        <td>{{ $reinstatedOn }}</td>
                    </tr>
                @else
                    @php
                        $status = 'Blacklisted';
                        $addedBy = $entry->activities->first()->user_name ?? '—';
                        $addedOn = $entry->activities->first()->created_at ?? $entry->created_at;
                    @endphp
                    <tr>
                        <td>{{ $entry->nic }}</td>
                        <td>{{ $entry->full_name }}</td>
                        <td>{{ $entry->company_name }}</td>
                        <td>{{ $entry->vehicle_number }}</td>
                        <td>{{ $entry->reason }}</td>
                        <td>{{ $addedBy }}</td>
                        <td>{{ $addedOn->format('Y-m-d H:i') }}</td>
                        <td>{{ $status }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>
</body>
</html>
