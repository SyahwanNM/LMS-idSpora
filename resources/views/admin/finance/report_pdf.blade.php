<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan - {{ $periodName }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #444; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #1A1D1F; font-size: 24px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #6F767E; font-size: 14px; }
        
        .summary-container { margin-bottom: 30px; }
        .summary-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; border-left: 4px solid #FFB703; padding-left: 10px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 10px; border: 1px solid #EEE; }
        .summary-table .label { color: #6F767E; width: 40%; }
        .summary-table .value { font-weight: bold; text-align: right; font-size: 13px; }
        .summary-table .net-profit { background-color: #FEF6E6; color: #FB8500; font-size: 16px; }

        .details-title { font-size: 14px; font-weight: bold; margin-bottom: 10px; }
        .details-table { width: 100%; border-collapse: collapse; }
        .details-table th { background-color: #1A1D1F; color: #FFF; padding: 10px; text-align: left; text-transform: uppercase; font-size: 10px; }
        .details-table td { padding: 8px 10px; border-bottom: 1px solid #EEE; }
        .details-table tr:nth-child(even) { background-color: #F8F9FA; }
        
        .text-right { text-align: right; }
        .income { color: #28a745; font-weight: bold; }
        .expense { color: #dc3545; font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #999; padding: 10px 0; border-top: 1px solid #EEE; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LMS IDSPORA</h1>
        <p>Laporan Keuangan Platform</p>
        <p style="font-size: 12px; font-weight: bold;">{{ $periodName }}</p>
    </div>

    <div class="summary-container">
        <div class="summary-title">Ringkasan Eksekutif</div>
        <table class="summary-table">
            <tr>
                <td class="label">Total Omzet (Gross Revenue)</td>
                <td class="value">Rp {{ number_format($totalOmzet, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-size: 10px; color: #6F767E;">
                <td class="label" style="padding-left: 25px;">- Pendapatan Event</td>
                <td class="value">Rp {{ number_format($eventRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr style="font-size: 10px; color: #6F767E;">
                <td class="label" style="padding-left: 25px;">- Pendapatan Course</td>
                <td class="value">Rp {{ number_format($courseRevenue, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Komisi Reseller (Cost)</td>
                <td class="value" style="color: #dc3545;">- Rp {{ number_format($totalCommissions, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>Pendapatan Bersih (Net Profit)</strong></td>
                <td class="value net-profit">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="details-title">Rincian Transaksi Arus Kas</div>
    <table class="details-table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Keterangan / Transaksi</th>
                <th>Metode</th>
                <th>Status</th>
                <th class="text-right">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $trx)
            <tr>
                <td>{{ $trx['date']->format('d/m/Y H:i') }}</td>
                <td>{{ $trx['description'] }}</td>
                <td>{{ $trx['method'] }}</td>
                <td>{{ $trx['status'] }}</td>
                <td class="text-right {{ $trx['type'] == 'income' ? 'income' : 'expense' }}">
                    {{ $trx['type'] == 'income' ? '+' : '-' }} Rp {{ number_format($trx['amount'], 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem LMS IdSpora pada {{ now()->format('d F Y, H:i:s') }}
    </div>
</body>
</html>
