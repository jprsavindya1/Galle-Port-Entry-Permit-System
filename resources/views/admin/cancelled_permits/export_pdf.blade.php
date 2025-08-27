<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cancelled Permits Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 15px; }
    </style>
</head>
<body>
    <h2>Cancelled Permits Report</h2>
    <table>
        <thead>
            <tr>
                <th>Permit ID</th>
                <th>Invoice ID</th>
                <th>Submission ID</th>
                <th>ID Number</th>
                <th>Full Name</th>
                <th>Company Name</th>
                <th>Vehicle Number</th>
                <th>Cancel Reason</th>
                <th>Cancelled At</th>
                <th>Cancelled By</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cancelledPermits as $permit)
                <tr>
                    <td>{{ $permit->permit_id }}</td>
                    <td>{{ $permit->invoice_id }}</td>
                    <td>{{ $permit->submission_id }}</td>
                    <td>{{ $permit->id_number }}</td>
                    <td>{{ $permit->full_name }}</td>
                    <td>{{ $permit->company_name }}</td>
                    <td>{{ $permit->vehicle_number }}</td>
                    <td>{{ $permit->cancel_reason }}</td>
                    <td>{{ $permit->cancelled_at }}</td>
                    <td>{{ $permit->cancelled_by }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
