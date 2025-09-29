<!DOCTYPE html>
<html>
<head>
    <title>Payment Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #000; padding: 4px; text-align: left; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-bottom: 10px; }
        p { margin: 2px 0; }
    </style>
</head>
<body>
    <h2>Payment / Batch Report</h2>

    <p><strong>Filter:</strong>
        Type: {{ $type ?? 'All' }},
        Range: {{ $range ?? 'All' }},
        Date: {{ $date ?? '-' }}
    </p>

    <p>
        <strong>Summary Totals:</strong><br>
        Rate: {{ number_format($summary['rate_total'],2) }} |
        SSL: {{ number_format($summary['ssl_total'],2) }} |
        VAT: {{ number_format($summary['vat_total'],2) }} |
        Total: <strong>{{ number_format($summary['amount_total'],2) }}</strong>
    </p>

    <table>
        <thead>
            <tr>
                <th>Invoice ID</th>
                <th>Submission ID</th>
                <th>Permit Type</th>
                <th>Company</th>
                <th>Entry Count</th>
                <th>Rate</th>
                <th>SSL</th>
                <th>VAT</th>
                <th>Total</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
                <tr>
                    <td>{{ $p->invoice_id }}</td>
                    <td>{{ $p->submission_id }}</td>
                    <td>{{ $p->permit_type }}</td>
                    <td>{{ $p->permits->first()->company_name ?? '-' }}</td>
                    <td>{{ $p->entry_count }}</td>
                    <td>{{ number_format($p->rate_total,2) }}</td>
                    <td>{{ number_format($p->ssl_total,2) }}</td>
                    <td>{{ number_format($p->vat_total,2) }}</td>
                    <td><strong>{{ number_format($p->amount_total,2) }}</strong></td>
                    <td>{{ $p->payment_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
