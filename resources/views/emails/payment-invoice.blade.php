<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoiceNumber }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #334155;
            margin: 0;
            padding: 0;
            background-color: #f8fafc;
            -webkit-text-size-adjust: none;
            -ms-text-size-adjust: none;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f8fafc;
            padding: 40px 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
        }
        .header {
            padding: 32px 32px 24px 32px;
            border-bottom: 1px solid #f1f5f9;
        }
        .logo-img {
            max-height: 38px;
            width: auto;
            display: block;
        }
        .invoice-title-badge {
            background-color: #f1f5f9;
            color: #1e293b;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: inline-block;
        }
        .content-body {
            padding: 32px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 32px;
            border-collapse: collapse;
        }
        .meta-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            padding-bottom: 4px;
            font-weight: 600;
        }
        .meta-value {
            font-size: 14px;
            color: #1e293b;
            font-weight: 500;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }
        .items-th {
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #475569;
            background-color: #f8fafc;
            padding: 10px 16px;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 700;
        }
        .items-td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            color: #334155;
        }
        .item-type {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 2px;
        }
        .item-name {
            font-weight: 600;
            color: #1e293b;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }
        .totals-label {
            text-align: right;
            font-size: 14px;
            color: #64748b;
            padding: 8px 16px;
        }
        .totals-value {
            text-align: right;
            font-size: 14px;
            color: #1e293b;
            font-weight: 600;
            padding: 8px 16px;
            width: 130px;
        }
        .grand-total-label {
            text-align: right;
            font-size: 15px;
            color: #1e293b;
            font-weight: 700;
            padding: 16px 16px;
            border-top: 1px solid #e2e8f0;
        }
        .grand-total-value {
            text-align: right;
            font-size: 18px;
            color: #10b981;
            font-weight: 800;
            padding: 16px 16px;
            border-top: 1px solid #e2e8f0;
            width: 130px;
        }
        .badge-success {
            color: #10b981;
            font-weight: 700;
            font-size: 13px;
        }
        .btn-container {
            text-align: center;
            margin-top: 8px;
            margin-bottom: 16px;
        }
        .btn-download {
            background-color: #0f172a;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            display: inline-block;
            transition: background-color 0.2s;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 32px;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }
        .footer-text {
            font-size: 12px;
            color: #94a3b8;
            line-height: 1.6;
            margin: 0;
        }
        .footer-brand {
            font-weight: 700;
            color: #64748b;
        }
    </style>
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="center">
            @if(empty($isPdf))
            <!-- Email Greeting Message -->
            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="max-width: 600px; margin-bottom: 20px; text-align: left;">
                <tr>
                    <td style="padding: 0 10px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #334155;">
                        <h3 style="margin-top: 0; margin-bottom: 8px; color: #0f172a; font-weight: 700;">Halo, {{ $userName }}!</h3>
                        <p style="margin: 0; font-size: 15px; line-height: 1.6; color: #475569;">
                            Terima kasih telah melakukan pembelian di <strong>idSpora</strong>. Pembayaran Anda telah kami terima dan konfirmasi secara otomatis.
                            Berikut adalah rincian bukti transaksi resmi Anda. Anda juga dapat mengunduh dokumen PDF invoice yang dilampirkan langsung pada email ini.
                        </p>
                    </td>
                </tr>
            </table>
            @endif

            <table class="container" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                
                <!-- Header -->
                <tr>
                    <td class="header">
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td align="left" valign="middle">
                                    @if(!empty($isPdf) && !empty($logoSrc))
                                        <img src="{{ $logoSrc }}" class="logo-img" alt="Logo idSpora">
                                    @elseif(isset($message))
                                        <img src="{{ $message->embed(public_path('aset/logo idspora_dark.png')) }}" class="logo-img" alt="Logo idSpora">
                                    @else
                                        <img src="{{ asset('aset/logo idspora_dark.png') }}" class="logo-img" alt="Logo idSpora">
                                    @endif
                                </td>
                                <td align="right" valign="middle">
                                    <div class="invoice-title-badge">Invoice</div>
                                    <div style="font-size: 14px; font-weight: 700; color: #334155; margin-top: 6px;">#{{ $invoiceNumber }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Content Body -->
                <tr>
                    <td class="content-body">
                        
                        <!-- Customer & Payment Details -->
                        <table class="meta-table" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td width="50%" align="left" valign="top" style="padding-right: 16px;">
                                    <div class="meta-label">Ditagih Kepada</div>
                                    <div class="meta-value" style="font-weight: 700;">{{ $userName }}</div>
                                    <div class="meta-value" style="color: #64748b; font-size: 13px; margin-top: 2px;">{{ $userEmail }}</div>
                                </td>
                                <td width="50%" align="left" valign="top" style="padding-left: 16px;">
                                    <div class="meta-label">Detail Pembayaran</div>
                                    <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="font-size: 13px;">
                                        <tr>
                                            <td style="color: #64748b; padding-bottom: 2px;">Tanggal:</td>
                                            <td style="color: #334155; font-weight: 600; text-align: right; padding-bottom: 2px;">{{ $paidAt }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color: #64748b; padding-bottom: 2px;">Metode:</td>
                                            <td style="color: #334155; font-weight: 600; text-align: right; text-transform: uppercase; padding-bottom: 2px;">{{ str_replace('_', ' ', $paymentMethod) }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color: #64748b; padding-bottom: 2px;">Order ID:</td>
                                            <td style="color: #334155; font-weight: 600; font-family: monospace; text-align: right; padding-bottom: 2px;">{{ $orderId }}</td>
                                        </tr>
                                        <tr>
                                            <td style="color: #64748b;">Status:</td>
                                            <td class="badge-success" style="text-align: right;">PAID</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Items Table -->
                        <table class="items-table" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <thead>
                                <tr>
                                    <th class="items-th" width="70%">Deskripsi Item</th>
                                    <th class="items-th" width="30%" style="text-align: right;">Jumlah</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="items-td">
                                        <div class="item-type">@if($itemType === 'event') Event Registration @else Online Course @endif</div>
                                        <div class="item-name">{{ $itemTitle }}</div>
                                    </td>
                                    <td class="items-td" style="text-align: right; font-weight: 600; color: #1e293b;">
                                        @if($amount <= 0)
                                            Free
                                        @else
                                            Rp {{ number_format($amount, 0, ',', '.') }}
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Totals Section -->
                        <table class="totals-table" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td class="totals-label">Subtotal</td>
                                <td class="totals-value">
                                    @if($amount <= 0)
                                        Rp 0
                                    @else
                                        Rp {{ number_format($amount, 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="totals-label">Diskon / Referral</td>
                                <td class="totals-value" style="color: #64748b;">Rp 0</td>
                            </tr>
                            <tr>
                                <td class="grand-total-label">Total Dibayar</td>
                                <td class="grand-total-value">
                                    @if($amount <= 0)
                                        FREE
                                    @else
                                        Rp {{ number_format($amount, 0, ',', '.') }}
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <!-- Download PDF Button (Only visible in Email, hidden in PDF) -->
                        @if(empty($isPdf))
                        <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td class="btn-container">
                                    <a href="{{ url('/payment/invoice/' . $orderId . '/download') }}" class="btn-download" target="_blank">
                                        Unduh Invoice (PDF)
                                    </a>
                                </td>
                            </tr>
                        </table>
                        @endif

                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td class="footer">
                        <p class="footer-text">
                            Terima kasih atas kepercayaan Anda belajar bersama kami.<br>
                            Email ini diterbitkan sebagai bukti transaksi resmi dan sah.<br><br>
                            © {{ date('Y') }} <span class="footer-brand">idSpora</span>. All rights reserved.
                        </p>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
</table>

</body>
</html>
