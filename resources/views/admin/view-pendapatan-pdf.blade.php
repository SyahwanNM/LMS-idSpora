<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Financial Report - {{ $course->name ?? '-' }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 13px; color: #1f2937; margin: 30px; }
        h1 { font-size: 18px; font-weight: 700; margin: 0 0 4px; }
        .subtitle { color: #6b7280; font-size: 12px; margin-bottom: 20px; }
        .stats-grid { display: flex; gap: 16px; margin-bottom: 20px; }
        .stat-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 14px; flex: 1; }
        .stat-box p { margin: 0; font-size: 11px; color: #6b7280; }
        .stat-box h5 { margin: 4px 0 0; font-size: 14px; font-weight: 700; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th { background: #f1f5f9; text-align: left; padding: 8px 10px; font-size: 12px; }
        td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; font-size: 12px; }
        .section-title { font-size: 14px; font-weight: 700; margin: 18px 0 8px; }
        .total-row td { font-weight: 700; background: #f8fafc; }
        .profit-row td { font-weight: 700; color: {{ ($stats['profit'] ?? 0) >= 0 ? '#16a34a' : '#dc2626' }}; }
    </style>
</head>
<body>
    <h1>{{ $course->name ?? '-' }}</h1>
    <p class="subtitle">Financial Detail Report &amp; Course Content</p>

    <div class="stats-grid">
        <div class="stat-box"><p>Date</p><h5>{{ ($stats['created_at'] ?? null) ? $stats['created_at']->format('d/m/Y') : '-' }}</h5></div>
        <div class="stat-box"><p>Total Participants</p><h5>{{ (int)($stats['participants'] ?? 0) }}</h5></div>
        <div class="stat-box"><p>Status</p><h5>{{ $stats['status'] ?? '-' }}</h5></div>
        <div class="stat-box"><p>Price Per Unit</p><h5>Rp. {{ number_format((float)($stats['unit_price'] ?? 0), 0, ',', '.') }}</h5></div>
    </div>

    <div class="section-title">Breakdown Income</div>
    <table>
        <tr><th>Description</th><th>Qty</th><th>Unit Price</th><th>Total</th></tr>
        @foreach($income_rows ?? [['label' => 'Tiket Pendaftar', 'qty' => $stats['participants'] ?? 0, 'unit' => $stats['unit_price'] ?? 0, 'total' => $stats['revenue_total'] ?? 0]] as $incRow)
        <tr><td>{{ $incRow['label'] }}</td><td>{{ (int)($incRow['qty'] ?? 0) }}</td><td>Rp. {{ number_format((float)($incRow['unit'] ?? 0), 0, ',', '.') }}</td><td>Rp. {{ number_format((float)($incRow['total'] ?? 0), 0, ',', '.') }}</td></tr>
        @endforeach
        <tr class="total-row"><td>Total Income</td><td></td><td></td><td>Rp. {{ number_format((float)($stats['revenue_total'] ?? 0), 0, ',', '.') }}</td></tr>
    </table>

    <div class="section-title">Breakdown Expenses</div>
    <table>
        <tr><th>Expense Item</th><th>Amount</th></tr>
        @forelse($expense_rows as $row)
        <tr><td>{{ $row['item'] }}</td><td>Rp. {{ number_format((float)($row['total'] ?? 0), 0, ',', '.') }}</td></tr>
        @empty
        <tr><td colspan="2">No expenses recorded.</td></tr>
        @endforelse
        <tr class="total-row"><td>Total Expenses</td><td>Rp. {{ number_format((float)($stats['expense_total'] ?? 0), 0, ',', '.') }}</td></tr>
    </table>

    <table>
        <tr class="profit-row">
            <td>{{ ($stats['profit'] ?? 0) >= 0 ? 'Profit' : 'Loss' }}</td>
            <td>Rp. {{ number_format(abs((float)($stats['profit'] ?? 0)), 0, ',', '.') }}</td>
        </tr>
    </table>
</body>
</html>
