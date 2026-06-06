<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Payout #INV-PAY-{{ $payment->id }} – LMS idSpora</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        :root {
            --ids-primary: #FB8500;
            --ids-primary-dark: #E07A00;
            --ids-success: #10B981;
            --ids-dark: #1E293B;
            --ids-muted: #64748B;
            --ids-border: #E2E8F0;
            --ids-bg-light: #F8F9FA;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ids-dark);
            background-color: #F1F5F9;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
        }

        .invoice-container {
            width: 100%;
            max-width: 800px;
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.03), 0 1px 3px rgba(0, 0, 0, 0.01);
            border: 1px solid var(--ids-border);
            padding: 48px;
            position: relative;
            box-sizing: border-box;
        }

        /* Floating action header */
        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            border-bottom: 1px solid var(--ids-border);
            padding-bottom: 20px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .btn-invoice {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 700;
            font-size: 0.85rem;
            padding: 10px 20px;
            border-radius: 12px;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-invoice-primary {
            background: linear-gradient(135deg, #FFB703 0%, #FB8500 100%);
            color: #FFFFFF;
            box-shadow: 0 4px 12px rgba(251, 133, 0, 0.15);
        }

        .btn-invoice-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(251, 133, 0, 0.25);
        }

        .btn-invoice-secondary {
            background: #FFFFFF;
            border: 1px solid var(--ids-border);
            color: var(--ids-dark);
        }

        .btn-invoice-secondary:hover {
            background: var(--ids-bg-light);
        }

        /* Invoice Branding Section */
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 40px;
        }

        .logo-section h2 {
            margin: 0 0 6px 0;
            font-weight: 800;
            font-size: 1.8rem;
            letter-spacing: -1px;
            color: var(--ids-dark);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-section h2 i {
            color: var(--ids-primary);
        }

        .logo-section p {
            margin: 0;
            color: var(--ids-muted);
            font-size: 0.85rem;
            font-weight: 600;
        }

        .meta-section {
            text-align: right;
        }

        .meta-section h3 {
            margin: 0 0 8px 0;
            font-weight: 800;
            font-size: 1.4rem;
            color: var(--ids-primary);
        }

        .meta-grid {
            display: grid;
            grid-template-columns: auto auto;
            gap: 6px 16px;
            text-align: left;
            font-size: 0.85rem;
        }

        .meta-label {
            color: var(--ids-muted);
            font-weight: 700;
            text-transform: uppercase;
            font-size: 10px;
            letter-spacing: 0.5px;
        }

        .meta-value {
            color: var(--ids-dark);
            font-weight: 700;
        }

        /* Address and Info Blocks */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
            padding: 24px;
            background: var(--ids-bg-light);
            border-radius: 16px;
            border: 1px solid var(--ids-border);
        }

        .info-block h4 {
            margin: 0 0 12px 0;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--ids-muted);
        }

        .info-details p {
            margin: 0 0 6px 0;
            font-size: 0.9rem;
            line-height: 1.4;
        }

        .info-name {
            font-weight: 800;
            color: var(--ids-dark);
            font-size: 0.95rem;
            margin-bottom: 8px !important;
        }

        /* Table Details */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .items-table th {
            text-align: left;
            padding: 14px 16px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--ids-muted);
            border-bottom: 2px solid var(--ids-border);
        }

        .items-table td {
            padding: 18px 16px;
            font-size: 0.9rem;
            border-bottom: 1px solid var(--ids-border);
            color: var(--ids-dark);
        }

        .item-desc {
            font-weight: 700;
        }

        .item-sub {
            color: var(--ids-muted);
            font-size: 0.8rem;
            margin-top: 4px;
        }

        /* Total Box */
        .total-box {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 40px;
        }

        .total-card {
            width: 100%;
            max-width: 320px;
            background: var(--ids-bg-light);
            border: 1px solid var(--ids-border);
            border-radius: 16px;
            padding: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .total-row:last-child {
            margin-bottom: 0;
            padding-top: 12px;
            border-top: 1px dashed var(--ids-border);
        }

        .total-label {
            color: var(--ids-muted);
            font-weight: 600;
        }

        .total-value {
            font-weight: 700;
            color: var(--ids-dark);
        }

        .grand-total {
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--ids-success);
        }

        /* Status Stamp */
        .status-badge {
            background: #E8F9F3;
            color: #10B981;
            border: 1px solid rgba(16, 185, 129, 0.2);
            border-radius: 30px;
            padding: 4px 12px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        /* Attachment Proof section */
        .attachment-section {
            margin-top: 48px;
            border-top: 1px solid var(--ids-border);
            padding-top: 32px;
        }

        .attachment-section h4 {
            margin: 0 0 16px 0;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--ids-muted);
        }

        .proof-wrapper {
            background: var(--ids-bg-light);
            border: 1px dashed var(--ids-border);
            border-radius: 16px;
            padding: 20px;
            text-align: center;
        }

        .proof-img {
            max-width: 100%;
            max-height: 380px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid #E2E8F0;
        }

        /* Print Specific Styling */
        @media print {
            body {
                background: #FFFFFF;
                padding: 0;
            }

            .invoice-container {
                box-shadow: none;
                border: none;
                padding: 0;
                width: 100%;
                max-width: 100%;
            }

            .action-bar {
                display: none !important;
            }

            .proof-wrapper {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        
        <!-- Floating Action Header -->
        <div class="action-bar">
            <div>
                <a href="{{ route('admin.finance.trainers') }}" class="btn-invoice btn-invoice-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
            <div class="action-buttons">
                <button onclick="window.print()" class="btn-invoice btn-invoice-primary">
                    <i class="bi bi-printer-fill"></i> Cetak / Simpan PDF
                </button>
            </div>
        </div>

        <!-- Invoice Branding Section -->
        <div class="invoice-header">
            <div class="logo-section">
                <img src="{{ asset('aset/logo idspora_dark.png') }}" alt="Logo idSpora" style="height: 48px; width: auto; margin-bottom: 8px; display: block;">
                <p>LMS idSpora Finance Department</p>
                <p style="font-weight: 500; font-size: 0.8rem; margin-top: 4px; color: var(--ids-muted);">
                    Fakultas ilmu terapan telkom university
                </p>
            </div>
            
            <div class="meta-section">
                <h3>PAYOUT RECEIPT</h3>
                <div class="meta-grid">
                    <span class="meta-label">Invoice No:</span>
                    <span class="meta-value">INV-PAY-{{ str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</span>
                    
                    <span class="meta-label">Disburse Date:</span>
                    <span class="meta-value">{{ $payment->payment_date ? $payment->payment_date->format('d M Y, H:i') : $payment->created_at->format('d M Y, H:i') }}</span>
                    
                    <span class="meta-label">Status:</span>
                    <span class="meta-value">
                        <span class="status-badge">
                            <i class="bi bi-check-circle-fill"></i> LUNAS / PAID
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <!-- Address and Info Blocks -->
        <div class="info-grid">
            <div class="info-block">
                <h4>DIBAYARKAN KEPADA:</h4>
                <div class="info-details">
                    <p class="info-name">{{ $payment->trainer->name ?? $payment->trainer_name }}</p>
                    <p class="text-muted" style="font-size: 0.85rem;"><i class="bi bi-envelope me-1"></i>{{ $payment->trainer->email ?? 'N/A' }}</p>
                    <p class="text-muted" style="font-size: 0.85rem;"><i class="bi bi-person-badge me-1"></i>Role: Trainer Utama</p>
                </div>
            </div>
            
            <div class="info-block">
                <h4>REKENING PENERIMA:</h4>
                <div class="info-details">
                    @if($payment->trainer && $payment->trainer->bank_name)
                        <p class="info-name">{{ $payment->trainer->bank_name }}</p>
                        <p style="font-size: 0.95rem; font-weight: 700; color: var(--ids-primary);">
                            No. Rek: {{ $payment->trainer->bank_account_number }}
                        </p>
                        <p class="text-muted" style="font-size: 0.85rem;">Atas Nama: {{ $payment->trainer->bank_account_name }}</p>
                    @else
                        <p class="info-name" style="color: var(--ids-muted); font-style: italic;">Transfer Manual</p>
                        <p class="text-muted" style="font-size: 0.85rem;">Metode pembayaran di luar koordinasi sistem otomatis.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Table Details -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 60%">Deskripsi Transaksi</th>
                    <th style="width: 20%">Kategori</th>
                    <th style="width: 20%; text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-desc">{{ $payment->title }}</div>
                        <div class="item-sub">
                            @if($payment->type == 'course_payout')
                                Penarikan saldo dari hasil bagi hasil penjualan materi kursus digital terdaftar.
                            @else
                                Fee/komisi mengajar untuk event selesai: <strong>{{ $payment->event->title ?? '-' }}</strong>.
                            @endif
                        </div>
                    </td>
                    <td>
                        <span style="font-weight: 700;">
                            {{ $payment->type == 'course_payout' ? 'Course Payout' : 'Event Fee' }}
                        </span>
                    </td>
                    <td style="text-align: right; font-weight: 800;">
                        Rp {{ number_format($payment->amount, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Total Box -->
        <div class="total-box">
            <div class="total-card">
                <div class="total-row">
                    <span class="total-label">Subtotal:</span>
                    <span class="total-value">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Biaya Transfer / Admin:</span>
                    <span class="total-value">Rp 0</span>
                </div>
                <div class="total-row">
                    <span class="total-label">Grand Total:</span>
                    <span class="total-value grand-total">Rp {{ number_format($payment->amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- internal Remarks -->
        @if($payment->notes)
            <div style="background: rgba(251, 133, 0, 0.03); border-left: 4px solid var(--ids-primary); padding: 16px; border-radius: 8px; margin-bottom: 40px;">
                <span class="meta-label" style="display: block; margin-bottom: 6px;">Catatan Penarikan:</span>
                <span style="font-size: 0.85rem; font-style: italic; color: var(--ids-dark);">"{{ $payment->notes }}"</span>
            </div>
        @endif

        <!-- Attachment Section (Proof of transfer hidden per request) -->

    </div>
</body>
</html>
