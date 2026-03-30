<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice_no }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 13px; line-height: 1.5; margin: 0; padding: 0; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .header { display: table; width: 100%; margin-bottom: 40px; }
        .header-left { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { display: table-cell; width: 50%; text-align: right; vertical-align: top; }
        
        .logo { font-size: 28px; font-weight: bold; color: #FB8500; text-transform: uppercase; margin-bottom: 5px; }
        .company-info { color: #666; font-size: 11px; }
        
        .invoice-title { font-size: 24px; font-weight: bold; margin-bottom: 10px; color: #1A1D1F; }
        .invoice-meta { color: #666; font-size: 12px; }
        
        .billing-info { display: table; width: 100%; margin-bottom: 30px; }
        .billing-to { display: table-cell; width: 50%; vertical-align: top; }
        .billing-label { font-size: 10px; font-weight: bold; color: #999; text-transform: uppercase; margin-bottom: 5px; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th { background-color: #F8F9FA; padding: 12px 15px; text-align: left; font-size: 11px; text-transform: uppercase; color: #666; border-bottom: 2px solid #EEE; }
        .table td { padding: 15px; border-bottom: 1px solid #EEE; vertical-align: middle; }
        
        .summary { margin-left: auto; width: 250px; }
        .summary-row { display: table; width: 100%; margin-bottom: 8px; }
        .summary-label { display: table-cell; width: 50%; color: #666; }
        .summary-value { display: table-cell; width: 50%; text-align: right; font-weight: bold; }
        .summary-total { border-top: 2px solid #EEE; padding-top: 10px; margin-top: 10px; font-size: 18px; color: #FB8500; }
        
        .status-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; background: #D4EDDA; color: #155724; }
        
        .footer { margin-top: 50px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #EEE; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div class="header-left">
                <div class="logo">IDSPORA</div>
                <div class="company-info">
                    LMS IdSpora Platform<br>
                    Digital Learning & Event Management<br>
                    support@idspora.com
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-meta">
                    Nomor: {{ $invoice_no }}<br>
                    Tanggal: {{ $date }}<br>
                    Metode: {{ $method }}
                </div>
            </div>
        </div>

        <div class="billing-info">
            <div class="billing-to">
                <div class="billing-label">Tagihan Untuk</div>
                <div style="font-weight: bold; font-size: 14px;">{{ $user->name ?? 'Pelanggan IDSPORA' }}</div>
                <div style="color: #666;">{{ $user->email ?? '-' }}</div>
            </div>
            <div style="display: table-cell; width: 50%; text-align: right;">
                <div class="billing-label">Status Pembayaran</div>
                <div class="status-badge">PAID</div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Item Deskripsi</th>
                    <th style="text-align: right;">Harga</th>
                    <th style="text-align: center;">Jumlah</th>
                    <th style="text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight: bold;">{{ $itemName }}</div>
                        <div style="font-size: 11px; color: #666;">Akses penuh ke modul/event</div>
                    </td>
                    <td style="text-align: right;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td style="text-align: center;">1</td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <div class="summary-label">Subtotal</div>
                <div class="summary-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Tax (0%)</div>
                <div class="summary-value">Rp 0</div>
            </div>
            <div class="summary-row summary-total">
                <div class="summary-label">TOTAL</div>
                <div class="summary-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="footer">
            Terima kasih telah melakukan pembelian di LMS IdSpora.<br>
            Invoice ini dihasilkan secara otomatis dan sah tanpa tanda tangan.<br>
            Simpan invoice ini sebagai bukti pembayaran yang sah.
        </div>
    </div>
</body>
</html>
