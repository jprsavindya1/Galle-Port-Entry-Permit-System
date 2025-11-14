<!DOCTYPE html>
<html>
<head>
    <title>Permit Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 10px; }
        h4 { margin-bottom: 10px; }
        p { margin: 2px 0; }
    </style>
</head>
<body>
    <h2>Permit Report</h2>

    @if($permits->isNotEmpty())
        @php
            $firstPermit = $permits->first();
            $uniqueUsers = $permits->pluck('id_number')->unique()->count();
        @endphp

        <p><strong>ID Number:</strong> {{ $uniqueUsers === 1 ? $firstPermit->id_number : 'Multiple Users' }}</p>
        <p><strong>Full Name / Owner:</strong> {{ $uniqueUsers === 1 ? ($firstPermit->type === 'VH' ? $firstPermit->owner_name : $firstPermit->full_name) : 'Multiple Users' }}</p>
    @elseif($query)
        <p><strong>Search Query:</strong> {{ $query }}</p>
    @endif

    @if($permits->isEmpty())
        <p>No permits found.</p>
    @else
        @php
            $grouped = $permits->groupBy('type');
        @endphp

        @foreach($grouped as $type => $permitsByType)
            <h4>{{ $type }} Permits</h4>
            <table>
                <thead>
                    <tr>
                        <th>Permit ID</th>
                        @if($type === 'VH')
                            <th>Owner Name</th>
                            <th>Vehicle Number</th>
                        @else
                            <th>Full Name</th>
                            <th>ID Number</th>
                        @endif
                        <th>Company Name</th>
                        <th>From Date</th>
                        <th>To Date</th>
                        <th>Issue Type</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Submission ID</th>
                        <th>Invoice ID</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permitsByType as $permit)
                        <tr>
                            <td>{{ $permit->permit_id }}</td>
                            @if($type === 'VH')
                                <td>{{ $permit->owner_name }}</td>
                                <td>{{ $permit->vehicle_number }}</td>
                            @else
                                <td>{{ $permit->full_name }}</td>
                                <td>{{ $permit->id_number }}</td>
                            @endif
                            <td>{{ $permit->company_name }}</td>
                            <td>{{ $permit->from_date }}</td>
                            <td>{{ $permit->to_date }}</td>
                            <td>{{ ucfirst($permit->issue_type) }}</td>
                            <td>{{ $permit->reason }}</td>
                            <td>{{ $permit->status }}</td>
                            <td>{{ $permit->submission_id }}</td>
                            <td>{{ $permit->payment->invoice_id ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endforeach
    @endif
</body>
</html>
