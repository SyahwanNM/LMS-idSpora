<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuangan Event - {{ $event->title }}</title>
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
        .expense { color: #dc3545; font-weight: bold; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 8px; color: #999; padding: 10px 0; border-top: 1px solid #EEE; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LMS IDSPORA</h1>
        <p>Laporan Keuangan Event</p>
    </div>

    <div class="event-info">
        <table>
            <tr>
                <td class="label">Nama Event</td>
                <td class="val">: {{ $event->title }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Event</td>
                <td class="val">: {{ $event->event_date ? $event->event_date->format('d F Y') : '-' }}</td>
            </tr>
            <tr>
                <td class="label">Lokasi</td>
                <td class="val">: {{ $event->location ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Trainer/Speaker</td>
                <td class="val">: {{ $event->speaker ?? ($event->trainer->name ?? '-') }}</td>
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
                <td class="label">Total Biaya Operasional (Event Expenses)</td>
                <td class="value text-danger">- Rp {{ number_format($opExpenses, 0, ',', '.') }}</td>
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

    <div class="section-title">Rincian Pemasukan (Transaksi Peserta Settled)</div>
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 30%;">Nama Peserta / Email</th>
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

    <div class="section-title">Rincian Pengeluaran Operasional</div>
    <table class="details-table">
        <thead>
            <tr>
                <th>Item Pengeluaran</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 20%; text-align: right;">Harga Satuan</th>
                <th style="width: 25%; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($event->expenses as $exp)
            <tr>
                <td><strong>{{ $exp->item }}</strong></td>
                <td style="text-align: center;">{{ $exp->quantity }}</td>
                <td class="text-right">Rp {{ number_format($exp->unit_price, 0, ',', '.') }}</td>
                <td class="text-right expense">Rp {{ number_format($exp->total, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 15px; color: #6F767E;">Tidak ada rincian biaya operasional yang dicatat.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh Sistem LMS IdSpora pada {{ now()->format('d F Y, H:i:s') }}
    </div>
</body>
</html>
