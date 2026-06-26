<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Performa Reseller - LMS IdSpora</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background-color: #ffffff;
            font-size: 12px;
            padding: 20px;
        }
        .header-container {
            border-bottom: 3px double #334155;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }
        .logo-title {
            font-size: 24px;
            font-weight: 700;
            color: #1e1b4b;
            letter-spacing: -0.5px;
        }
        .report-meta {
            font-size: 11px;
            color: #64748b;
            line-height: 1.5;
        }
        .stat-card {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            background-color: #f8fafc;
            text-align: center;
        }
        .stat-val {
            font-size: 18px;
            font-weight: 700;
            color: #4c1d95;
            margin-bottom: 2px;
        }
        .stat-lbl {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            font-weight: 600;
        }
        .table {
            margin-top: 25px;
            font-size: 11px;
        }
        .table th {
            background-color: #f1f5f9 !important;
            color: #334155;
            font-weight: 600;
            border-bottom: 2px solid #cbd5e1;
            padding: 10px;
        }
        .table td {
            padding: 10px;
            vertical-align: middle;
            border-bottom: 1px solid #e2e8f0;
        }
        .badge-paid {
            background-color: #d1fae5;
            color: #065f46;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: 600;
            display: inline-block;
        }
        .footer-sig {
            margin-top: 50px;
            font-size: 11px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .stat-card {
                background-color: #f8fafc !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .table th {
                background-color: #f1f5f9 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

    <!-- Printable Report Layout -->
    <div class="header-container d-flex justify-content-between align-items-end">
        <div>
            <div class="logo-title mb-1">IdSpora Reseller</div>
            <div class="report-meta">
                <strong>Laporan Performa Program Reseller</strong><br>
                LMS IdSpora Platform
            </div>
        </div>
        <div class="text-end report-meta">
            <strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('d F Y, H:i') }} WIB<br>
            <strong>Periode Laporan:</strong> {{ $rangeText }}<br>
            <strong>Dicetak Oleh:</strong> Admin Sistem
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="row g-3">
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-val">{{ $activeResellersCount }}</div>
                <div class="stat-lbl">Total Reseller Terdaftar</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-val">{{ $totalSalesCount }}</div>
                <div class="stat-lbl">Penjualan Referral Sukses</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-val">Rp {{ number_format($totalKomisi, 0, ',', '.') }}</div>
                <div class="stat-lbl">Total Komisi Terbayar</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <table class="table table-striped w-100">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Reseller</th>
                <th style="width: 20%;">Pembeli</th>
                <th style="width: 25%;">Deskripsi Produk</th>
                <th style="width: 15%; text-align: right;">Komisi (IDR)</th>
            </tr>
        </thead>
        <tbody>
            @forelse($referrals as $index => $ref)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $ref->created_at->translatedFormat('d M Y, H:i') }}</td>
                    <td>
                        <strong class="d-block">{{ $ref->user->name ?? '-' }}</strong>
                        <span class="text-muted" style="font-size: 10px;">{{ $ref->user->email ?? '-' }}</span>
                    </td>
                    <td>
                        <strong class="d-block">{{ $ref->referredUser->name ?? '-' }}</strong>
                        <span class="text-muted" style="font-size: 10px;">{{ $ref->referredUser->email ?? '-' }}</span>
                    </td>
                    <td>{{ $ref->description ?? '-' }}</td>
                    <td style="text-align: right; font-weight: 600;">Rp {{ number_format($ref->amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Belum ada transaksi referral yang terdaftar untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <!-- Trigger window print on load -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
</body>
</html>
