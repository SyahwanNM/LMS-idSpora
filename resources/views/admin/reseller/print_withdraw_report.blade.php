<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Penarikan Dana Reseller - LMS IdSpora</title>
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
        .badge-status {
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-approved {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-rejected {
            background-color: #fee2e2;
            color: #991b1b;
        }
        .footer-sig {
            margin-top: 50px;
            font-size: 11px;
        }
        @media print {
            body {
                padding: 0;
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
                <strong>Laporan Penarikan Dana Reseller</strong><br>
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
                <div class="stat-val">{{ $totalWithdrawalsCount }}</div>
                <div class="stat-lbl">Total Pengajuan Penarikan</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-val">Rp {{ number_format($totalApprovedAmount, 0, ',', '.') }}</div>
                <div class="stat-lbl">Total Dana Cair (Disetujui)</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stat-card">
                <div class="stat-val">Rp {{ number_format($totalPendingAmount, 0, ',', '.') }}</div>
                <div class="stat-lbl">Total Dana Tertunda (Pending)</div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <table class="table table-striped w-100">
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal Pengajuan</th>
                <th style="width: 18%;">Reseller</th>
                <th style="width: 20%;">Detail Rekening Bank</th>
                <th style="width: 12%; text-align: right;">Penarikan (Gross)</th>
                <th style="width: 10%; text-align: right;">Biaya Admin</th>
                <th style="width: 13%; text-align: right;">Transfer Bersih</th>
                <th style="width: 10%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($withdrawals as $index => $wd)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $wd->created_at->translatedFormat('d M Y, H:i') }}</td>
                    <td>
                        <strong class="d-block">{{ $wd->user->name ?? '-' }}</strong>
                        <span class="text-muted" style="font-size: 10px;">{{ $wd->user->email ?? '-' }}</span>
                    </td>
                    <td>
                        <strong class="d-block">{{ strtoupper($wd->bank_name ?? '-') }}</strong>
                        <span class="d-block text-secondary" style="font-size: 10px;">Rek: {{ $wd->account_number ?? '-' }}</span>
                        <span class="text-muted" style="font-size: 10px;">A/N: {{ $wd->account_holder ?? '-' }}</span>
                    </td>
                    <td style="text-align: right;">Rp {{ number_format($wd->amount, 0, ',', '.') }}</td>
                    <td style="text-align: right; color: #dc2626;">Rp {{ number_format($wd->admin_fee ?? 3000, 0, ',', '.') }}</td>
                    <td style="text-align: right; font-weight: 600; color: #16a34a;">Rp {{ number_format($wd->net_amount ?? ($wd->amount - ($wd->admin_fee ?? 3000)), 0, ',', '.') }}</td>
                    <td style="text-align: center;">
                        <span class="badge-status {{ $wd->status === 'approved' ? 'badge-approved' : ($wd->status === 'pending' ? 'badge-pending' : 'badge-rejected') }}">
                            {{ strtoupper($wd->status) }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center py-4 text-muted">Belum ada transaksi penarikan dana yang terdaftar untuk periode ini.</td>
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
