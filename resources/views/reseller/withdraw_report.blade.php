<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Penarikan Dana - {{ $user->name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; font-size: 13px; line-height: 1.5; margin: 0; padding: 0; background: #f4f6f9; }
        .invoice-box { max-width: 800px; margin: 40px auto; padding: 30px; border: 1px solid #eee; background: #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-radius: 8px; }
        .header { display: table; width: 100%; margin-bottom: 40px; }
        .header-left { display: table-cell; width: 50%; vertical-align: top; }
        .header-right { display: table-cell; width: 50%; text-align: right; vertical-align: top; }
        
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
        
        /* WARNA BADGE STATUS */
        .badge-success { background: #D4EDDA; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-warning { background: #FFF3CD; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .badge-danger { background: #F8D7DA; color: #721C24; padding: 4px 8px; border-radius: 4px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        
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
                Cetak / Simpan PDF
            </button>
            <a href="{{ route('reseller.index') }}" style="margin-left: 15px; color: #666; text-decoration: none; font-weight: bold;">Kembali</a>
        </div>

        <div class="header">
            <div class="header-left">
                <img src="{{ asset('images/logo-idspora-light.png') }}" alt="Logo idSpora" style="max-width:80px; height:auto;">
                <div class="company-info">
                    LMS IdSpora Platform<br>
                    Laporan Mutasi Penarikan<br>
                    idspora.contact@gmail.com
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-title">RIWAYAT PENARIKAN</div>
                <div class="invoice-meta">
                    Tanggal Cetak: {{ now()->format('d M Y, H:i') }}<br>
                    Total Pengajuan: {{ $withdrawals->count() }}
                </div>
            </div>
        </div>

        <div class="billing-info">
            <div class="billing-to">
                <div class="billing-label">Laporan Milik</div>
                <div style="font-weight: bold; font-size: 14px;">{{ $user->name }}</div>
                <div style="color: #666;">Kode Reseller: {{ $user->referral_code }}</div>
            </div>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>ID Penarikan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Pengguna</th>
                    <th>Bank Tujuan</th>
                    <th>Nomor Rekening</th>
                    <th style="text-align: right;">Nominal Penarikan</th>
                    <th style="text-align: right;">Biaya Admin</th>
                    <th style="text-align: right;">Bersih Diterima</th>
                    <th style="text-align: center;">Status</th>
                    <th>Tanggal Diproses</th>
                </tr>
            </thead>
            <tbody>
                @forelse($withdrawals as $wd)
                @php 
                    $isRejected = strtolower($wd->status) == 'rejected'; 
                    $strikeStyle = $isRejected ? 'text-decoration: line-through; opacity: 0.5;' : '';
                    
                    // Mask the account number
                    $accountLen = strlen($wd->account_number);
                    if ($accountLen > 4) {
                        $maskedRaw = str_repeat('•', $accountLen - 4) . substr($wd->account_number, -4);
                    } else {
                        $maskedRaw = $wd->account_number;
                    }
                    preg_match_all('/.{1,4}/u', $maskedRaw, $matches);
                    $maskedAccount = implode(' ', $matches[0]);
                @endphp
                <tr>
                    <td>
                        <div style="font-weight: bold; {{ $strikeStyle }}">#WD-{{ str_pad($wd->id, 4, '0', STR_PAD_LEFT) }}</div>
                        <div style="color: #666; font-size: 11px; {{ $strikeStyle }}">{{ $wd->bank_name }}</div>
                    </td>
                    <td style="color: #666; font-size: 11px; {{ $strikeStyle }}">
                        <div>{{ $wd->created_at->format('d M Y') }}</div>
                        <div>{{ $wd->created_at->format('H:i') }} WIB</div>
                    </td>
                    <td style="font-weight: bold; {{ $strikeStyle }}">{{ $wd->user->name ?? $user->name }}</td>
                    <td style="color: #333; {{ $strikeStyle }}">{{ $wd->bank_name }}</td>
                    <td>
                        <div style="font-weight: bold; {{ $strikeStyle }}">{{ $maskedAccount }}</div>
                        <div style="color: #666; font-size: 11px; {{ $strikeStyle }}">A/n. {{ $wd->account_holder }}</div>
                    </td>
                    <td style="text-align: right; {{ $strikeStyle }}">
                        Rp {{ number_format($wd->amount, 0, ',', '.') }}
                    </td>
                    <td style="text-align: right; color: #666; {{ $strikeStyle }}">
                        Rp {{ number_format($wd->admin_fee ?? 3000, 0, ',', '.') }}
                    </td>
                    <td style="text-align: right; font-weight: bold; {{ $isRejected ? 'text-decoration: line-through; opacity: 0.5; color: #dc3545;' : 'color: #198754;' }}">
                        Rp {{ number_format($wd->net_amount ?? ($wd->amount - ($wd->admin_fee ?? 3000)), 0, ',', '.') }}
                    </td>
                    <td style="text-align: center;">
                        @if(strtolower($wd->status) == 'approved')
                            <span class="badge-success">APPROVED</span>
                        @elseif($isRejected)
                            <span class="badge-danger">REJECTED</span>
                        @else
                            <span class="badge-warning">PENDING</span>
                        @endif
                    </td>
                    <td style="color: #666; font-size: 11px;">
                        @if($wd->status != 'pending')
                            <div>{{ $wd->updated_at->format('d M Y') }}</div>
                            <div>{{ $wd->updated_at->format('H:i') }} WIB</div>
                        @else
                            <span style="font-style: italic; color: #999;">Belum diproses</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align: center; color: #999; padding: 30px;">Belum ada riwayat penarikan dana.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @php
            $totalRejected = $withdrawals->filter(fn($item) => strtolower($item->status) === 'rejected')->sum('amount');
        @endphp
        <div class="summary">
            <div class="summary-row">
                <div class="summary-label">Dana Pending</div>
                <div class="summary-value" style="color: #b4b4b4;">Rp {{ number_format($totalPending, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row">
                <div class="summary-label">Dana Ditolak</div>
                <div class="summary-value" style="color: #ef4444;">Rp {{ number_format($totalRejected, 0, ',', '.') }}</div>
            </div>
            <div class="summary-row summary-total">
                <div class="summary-label">TOTAL CAIR</div>
                <div class="summary-value">Rp {{ number_format($totalApproved, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="footer">
            Laporan ini dihasilkan secara otomatis dan sah sebagai bukti mutasi penarikan dana komisi.<br>
            Pastikan menjaga kerahasiaan data finansial ini.
        </div>
    </div>

    <script>
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
