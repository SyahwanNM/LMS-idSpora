<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Referral - {{ $user->name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 13px; line-height: 1.5; margin: 0; padding: 0; background: #f4f6f9; }
        .invoice-box { max-width: 800px; margin: 40px auto; padding: 30px; border: 1px solid #eee; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-radius: 8px; }
        .header { display: table; width: 100%; margin-bottom: 40px; }
        .header-left { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { display: table-cell; width: 50%; text-align: right; vertical-align: top; }
       
        .logo { font-size: 28px; font-weight: bold; color: #FFC107; text-transform: uppercase; margin-bottom: 5px; }
        .company-info { color: #666; font-size: 11px; }
        
        .invoice-title { font-size: 22px; font-weight: bold; margin-bottom: 10px; color: #1A1D1F; text-transform: uppercase; }
        .invoice-meta { color: #666; font-size: 12px; }
        
        .billing-info { display: table; width: 100%; margin-bottom: 30px; }
        .billing-to { display: table-cell; width: 50%; vertical-align: top; }
        .billing-label { font-size: 10px; font-weight: bold; color: #999; text-transform: uppercase; margin-bottom: 5px; }
        
        .table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .table th { background-color: #F8F9FA; padding: 12px 15px; text-align: left; font-size: 11px; text-transform: uppercase; color: #666; border-bottom: 2px solid #EEE; }
        .table td { padding: 12px 15px; border-bottom: 1px solid #EEE; vertical-align: middle; }
        
        .summary { margin-left: auto; width: 300px; }
        .summary-row { display: table; width: 100%; margin-bottom: 8px; }
        .summary-label { display: table-cell; width: 50%; color: #666; }
        .summary-value { display: table-cell; width: 50%; text-align: right; font-weight: bold; }
        
        .summary-total { border-top: 2px solid #EEE; padding-top: 10px; margin-top: 10px; font-size: 18px; color: #FFC107; }
        
        .badge-success { background: #D4EDDA; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-warning { background: #FFF3CD; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        
        .footer { margin-top: 50px; text-align: center; color: #999; font-size: 10px; border-top: 1px solid #EEE; padding-top: 20px; }

        /* Sembunyikan tombol print saat dicetak */
        @media print {
            .no-print { display: none; }
            .invoice-box { border: none; box-shadow: none; margin: 0; padding: 0; }
            body { background: #fff; }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button onclick="window.print()" style="background: #FFC107; color: #212529; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; font-weight: bold; font-family: inherit;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" style="vertical-align: -2px; margin-right: 5px;" viewBox="0 0 16 16">
                  <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0z"/>
                </svg>
                Cetak / Simpan PDF
            </button>
            <a href="{{ route('reseller.index') }}" style="margin-left: 15px; color: #666; text-decoration: none; font-weight: bold;">Kembali</a>
        </div>

        <div class="header">
            <div class="header-left">
                <img src="{{ asset('images/logo-idspora-light.png') }}" alt="Logo idSpora" 
                 class="img-fluid nav-logo" style="max-width:80px; height:auto;">
                <div class="company-info">
                    LMS IdSpora Platform<br>
                    Laporan Kinerja Reseller<br>
                    idspora.contact@gmail.com
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">LAPORAN REFERRAL</div>
                <div class="invoice-meta">
                    Tanggal Cetak: {{ now()->format('d M Y, H:i') }}<br>
                    Total Transaksi: {{ $history->count() }}
                </div>
            </div>
        </div>

        <div class="billing-info">
            <div class="billing-to">
                <div class="billing-label">Laporan Milik</div>
                <div style="font-weight: bold; font-size: 14px;">{{ $user->name }}</div>
                <div style="color: #666;">Kode: {{ $user->referral_code }}</div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Pengguna Baru</th>
                    <th>Detail Transaksi</th>
                    <th style="text-align: center;">Status</th>
                    <th style="text-align: right;">Komisi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($history as $item)
                <tr>
                    <td style="color: #666; font-size: 11px;">{{ $item->created_at->format('d/m/Y') }}</td>
                    <td style="font-weight: bold;">{{ $item->referredUser->name ?? 'User Anonim' }}</td>
                    <td style="color: #666;">{{ $item->description ?? 'Pembelian Event/Course' }}</td>
                    <td style="text-align: center;">
                        @if($item->status == 'paid')
                            <span class="badge-success">PAID</span>
                        @else
                            <span class="badge-warning">PENDING</span>
                        @endif
                    </td>
                    <td style="text-align: right; font-weight: bold;">Rp {{ number_format($item->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: #999;">Belum ada riwayat transaksi referral.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="summary">
            <div class="summary-row">
                <div class="summary-label">Komisi Pending</div>
                <div class="summary-value" style="color: #b4b4b4;">Rp {{ number_format($pendingKomisi, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row summary-total">
                <div class="summary-label">TOTAL TERBAYAR</div>
                <div class="summary-value">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="footer">
            Terima kasih telah menjadi bagian dari Reseller IdSpora.<br>
            Laporan ini dihasilkan secara otomatis dan sah sebagai bukti rekapitulasi komisi.<br>
            Pastikan menjaga kerahasiaan data ini.
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>