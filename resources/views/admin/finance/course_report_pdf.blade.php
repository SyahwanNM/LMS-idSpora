<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Course - {{ $course->name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 11px; color: #333; line-height: 1.4; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #444; padding-bottom: 15px; }
        .header h1 { margin: 0; color: #1A1D1F; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 5px 0 0; color: #6F767E; font-size: 12px; }
        
        .event-info { margin-bottom: 20px; background-color: #F8F9FA; padding: 12px 15px; border-radius: 8px; border: 1px solid #EFEFEF; }
        .event-info table { width: 100%; border-collapse: collapse; }
        .event-info td { padding: 4px 0; font-size: 11px; vertical-align: top; }
        .event-info .label { color: #6F767E; width: 25%; }
        .event-info .val { font-weight: bold; color: #1A1D1F; }

        .summary-container { margin-bottom: 25px; }
        .summary-title { font-size: 13px; font-weight: bold; margin-bottom: 8px; border-left: 4px solid #FFB703; padding-left: 10px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 8px 10px; border: 1px solid #EEE; }
        .summary-table .label { color: #6F767E; width: 45%; }
        .summary-table .value { font-weight: bold; text-align: right; font-size: 12px; }
        .summary-table .net-profit { background-color: #FEF6E6; color: #FB8500; font-size: 14px; }

        .section-title { font-size: 13px; font-weight: bold; margin-bottom: 8px; margin-top: 15px; border-left: 4px solid #1A1D1F; padding-left: 10px; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .details-table th { background-color: #1A1D1F; color: #FFF; padding: 8px 10px; text-align: left; text-transform: uppercase; font-size: 9px; }
        .details-table td { padding: 6px 10px; border-bottom: 1px solid #EEE; font-size: 10px; }
        .details-table tr:nth-child(even) { background-color: #F8F9FA; }
        
        .text-right { text-align: right; }
        .income { color: #28a745; font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; padding: 10px 0; border-top: 1px solid #EEE; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LMS IDSPORA</h1>
        <p>Laporan Keuangan Course</p>
    </div>

    <div class="event-info">
        <table>
            <tr>
                <td class="label">Nama Course</td>
                <td class="val">: {{ $course->name }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Cetak</td>
                <td class="val">: {{ now()->format('d F Y') }}</td>
            </tr>
            <tr>
                <td class="label">Total Siswa Terdaftar</td>
                <td class="val">: {{ $transactions->count() }} pendaftaran sukses</td>
            </tr>
        </table>
    </div>

    <div class="summary-container">
        <div class="summary-title">Ringkasan Keuangan</div>
        <table class="summary-table">
            <tr>
                <td class="label">Total Pendapatan Kotor (Gross Income)</td>
                <td class="value text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label">Total Komisi Reseller</td>
                <td class="value text-danger">- Rp {{ number_format($commissions, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td class="label"><strong>Estimasi Pendapatan Bersih (Net Profit)</strong></td>
                <td class="value net-profit">Rp {{ number_format($netProfit, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <div class="section-title">Rincian Pemasukan (Transaksi Siswa Settled)</div>
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 30%;">Nama Siswa / Email</th>
                <th style="width: 20%;">Order ID</th>
                <th style="width: 15%;">Metode</th>
                <th style="width: 20%; text-align: right;">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $trx)
            <tr>
                <td>{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    <strong>{{ $trx->user->name ?? 'Deleted User' }}</strong><br>
                    <span style="font-size: 8px; color: #6F767E;">{{ $trx->user->email ?? '-' }}</span>
                </td>
                <td><code>{{ $trx->order_id }}</code></td>
                <td style="text-transform: uppercase;">{{ $trx->method }}</td>
                <td class="text-right income">Rp {{ number_format($trx->amount, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; padding: 15px; color: #6F767E;">Belum ada data transaksi masuk.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem LMS IdSpora pada {{ now()->format('d F Y, H:i:s') }}
    </div>
</body>
</html>
