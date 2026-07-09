@extends('layouts.trainer')

@section('title', 'Finance Trainer')

@php
    $pageTitle = 'Finance';
    $breadcrumbs = [
        ['label' => 'Dasbor', 'url' => route('trainer.dashboard')],
        ['label' => 'Keuangan']
    ];
@endphp

@push('styles')
<style>
    /* Google Fonts for modern typography */
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');

    .finance-page {
        font-family: 'Outfit', sans-serif;
        color: #334155;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow-x: hidden;
        box-sizing: border-box;
    }

    .finance-page * {
        box-sizing: border-box;
    }

    /* Grid Layouts */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 24px;
        width: 100%;
    }

    /* Cards */
    .stat-card {
        border-radius: 24px;
        padding: 32px;
        position: relative;
        overflow: hidden;
        color: white;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease;
        width: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .stat-card.navy {
        background: linear-gradient(135deg, #2e2050 0%, #624388 100%);
    }

    .stat-card.amber {
        background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
    }

    /* Decorative circles */
    .stat-card::before, .stat-card::after {
        content: '';
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        transition: transform 0.5s ease;
    }
    
    .stat-card::before {
        width: 150px;
        height: 150px;
        top: -40px;
        right: -40px;
    }
    
    .stat-card::after {
        width: 250px;
        height: 250px;
        bottom: -100px;
        right: 10%;
    }

    .stat-card:hover::before {
        transform: scale(1.1) translate(-10px, 10px);
    }
    .stat-card:hover::after {
        transform: scale(1.05) translate(10px, -10px);
    }

    /* Content styling */
    .stat-title {
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 12px;
        position: relative;
        z-index: 2;
    }

    .stat-amount {
        font-size: 42px;
        font-weight: 800;
        margin: 0 0 12px 0;
        line-height: 1.1;
        position: relative;
        z-index: 2;
        letter-spacing: -1px;
        word-break: break-word;
    }

    .stat-desc {
        font-size: 14px;
        color: rgba(255, 255, 255, 0.75);
        margin: 0;
        position: relative;
        z-index: 2;
        line-height: 1.5;
    }

    /* Breakdown Section */
    .breakdown-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
        width: 100%;
    }

    .bd-card {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid #f1f5f9;
        transition: box-shadow 0.3s ease;
        width: 100%;
    }

    .bd-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
    }

    .bd-title {
        font-size: 16px;
        font-weight: 800;
        color: #1e293b;
        margin: 0 0 16px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bd-title::before {
        content: '';
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #fbbf24;
    }

    .bd-item {
        padding: 16px;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 12px;
        border: 1px solid #f1f5f9;
        transition: transform 0.2s ease, background 0.2s ease;
        width: 100%;
    }

    .bd-item:last-child {
        margin-bottom: 0;
    }

    .bd-item:hover {
        transform: translateX(4px);
        background: #f1f5f9;
    }

    .bd-item-name {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 8px;
        font-size: 15px;
        word-break: break-word;
    }

    .bd-item-meta {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 13px;
        color: #64748b;
    }
    
    .bd-item-meta span {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .bd-item-meta span strong {
        color: #2e2050;
    }

    .bd-empty {
        text-align: center;
        padding: 30px 20px;
        color: #94a3b8;
        font-style: italic;
        background: #f8fafc;
        border-radius: 12px;
        width: 100%;
    }

    /* Tables */
    .table-section {
        background: #ffffff;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
        border: 1px solid #f1f5f9;
        margin-bottom: 24px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow: hidden;
        box-sizing: border-box;
    }

    .section-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 20px 0;
    }

    .table-container {
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow-x: auto;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        -webkit-overflow-scrolling: touch;
        display: block;
        box-sizing: border-box;
    }

    .modern-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .modern-table th {
        background: #f8fafc;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px;
        text-align: left;
        border-bottom: 2px solid #f1f5f9;
        white-space: nowrap;
    }

    .modern-table td {
        padding: 16px;
        color: #334155;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .modern-table tbody tr {
        transition: background 0.2s ease;
    }

    .modern-table tbody tr:hover {
        background: #f8fafc;
    }

    .modern-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Status badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 6px 12px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 99px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .badge.success { background: #dcfce7; color: #166534; }
    .badge.warning { background: #fef3c7; color: #92400e; }
    .badge.danger { background: #fee2e2; color: #991b1b; }
    .badge.info { background: #e0e7ff; color: #3730a3; }

    /* Action Buttons */
    .btn-invoice {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 700;
        color: white;
        background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
        border-radius: 8px;
        text-decoration: none;
        box-shadow: 0 4px 10px rgba(27, 23, 99, 0.2);
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-invoice:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(27, 23, 99, 0.3);
        color: white;
    }

    .pagination-wrapper {
        margin-top: 20px;
        width: 100%;
        max-width: 100%;
        min-width: 0;
        overflow-x: auto;
        box-sizing: border-box;
    }
    
    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: #64748b;
        background: #f8fafc;
        border-radius: 12px;
        font-weight: 500;
        width: 100%;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .stat-grid, .breakdown-grid {
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .stat-grid, .breakdown-grid {
            grid-template-columns: 1fr;
        }
        .stat-card {
            padding: 24px;
        }
        .stat-amount {
            font-size: 32px;
        }
        .table-section {
            padding: 16px;
            border-radius: 16px;
        }
        .bd-card {
            padding: 16px;
            border-radius: 16px;
        }
        .section-title {
            font-size: 16px;
        }
    }

    @media (max-width: 480px) {
        .stat-card {
            padding: 20px;
        }
        .stat-amount {
            font-size: 28px;
        }
        .stat-title {
            font-size: 12px;
        }
        .stat-desc {
            font-size: 12px;
        }
        .bd-item {
            padding: 12px;
        }
        .bd-item-name {
            font-size: 14px;
        }
        .bd-item-meta {
            font-size: 12px;
            gap: 6px;
        }
        .table-container {
            border-radius: 8px;
        }
        .btn-invoice {
            padding: 6px 12px;
            font-size: 11px;
        }
    }
</style>
@endpush

@section('content')
<div class="finance-page">
    
    <!-- Top Statistics Grid -->
    <div class="stat-grid">
        <!-- Realized Earnings -->
        <div class="stat-card navy">
            <div class="stat-title">Total Pendapatan Aktual</div>
            <h2 class="stat-amount">Rp {{ number_format($totalEarned ?? 0, 0, ',', '.') }}</h2>
            <p class="stat-desc">
                Akumulasi pembayaran selesai dari course atau event Anda. Dana yang sudah terealisasi berdasarkan transaksi riil.
            </p>
        </div>

        <!-- Available Wallet Balance -->
        <div class="stat-card emerald" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <div class="stat-title">Saldo Course Tersedia</div>
            <h2 class="stat-amount">Rp {{ number_format(Auth::user()->wallet_balance ?? 0, 0, ',', '.') }}</h2>
            <p class="stat-desc">
                Saldo bagi hasil course Anda yang siap dicairkan. Saldo pada course akan dicairkan setiap bulannya.
            </p>
        </div>

        <!-- Estimated Earnings -->
        <div class="stat-card amber">
            <div class="stat-title">Estimasi Pendapatan</div>
            <h2 class="stat-amount">Rp {{ number_format($estimatedTotal ?? 0, 0, ',', '.') }}</h2>
            <p class="stat-desc">
                Proyeksi total berdasarkan skema persentase trainer, fee event, dan jumlah peserta aktif saat ini.
            </p>
        </div>
    </div>

    <!-- Breakdown Grid -->
    <div class="breakdown-grid">
        <!-- Event Breakdown -->
        <div class="bd-card">
            <h3 class="bd-title">Estimasi Event</h3>
            @if(isset($events) && $events->count() > 0)
                @foreach($events as $row)
                    <div class="bd-item">
                        <div class="bd-item-name">{{ $row['event']->title ?? '-' }}</div>
                        <div class="bd-item-meta">
                            <span>Peserta: <strong>{{ number_format($row['active_participants_count'] ?? ($row['event']->active_participants_count ?? 0)) }}</strong></span>
                            <span>Fee: <strong>Rp {{ number_format($row['fee_trainer'] ?? 0, 0, ',', '.') }}</strong></span>
                            <span style="color: #0f172a; font-weight: 700; width: 100%;">Estimasi: Rp {{ number_format($row['estimated_fee'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bd-empty">Belum ada estimasi dari event.</div>
            @endif
        </div>

        <!-- Course Breakdown -->
        <div class="bd-card">
            <h3 class="bd-title">Estimasi Course</h3>
            @if(isset($courses) && count($courses) > 0)
                @foreach($courses as $row)
                    <div class="bd-item">
                        <div class="bd-item-name">{{ $row['course']->name ?? '-' }}</div>
                        <div class="bd-item-meta">
                            <span>Peserta: <strong>{{ number_format($row['active_students'] ?? 0) }}</strong></span>
                            <span>Skema: <strong>{{ $row['scheme_percent'] ?? 0 }}%</strong></span>
                            <span style="color: #0f172a; font-weight: 700; width: 100%;">Estimasi: Rp {{ number_format($row['estimated_revenue'] ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bd-empty">Belum ada estimasi dari course.</div>
            @endif
        </div>
    </div>

    <!-- Recent Payments Table -->
    <div class="table-section">
        <h3 class="section-title">Transaksi Pemasukan Terbaru</h3>
        
        @if($payments->count() === 0)
            <div class="empty-state">
                <i class="bi bi-inbox fs-2 mb-2 d-block"></i>
                Belum ada pembayaran yang masuk dari peserta.
            </div>
        @else
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>ID Transaksi</th>
                            <th>Peserta</th>
                            <th>Sumber (Course/Event)</th>
                            <th>Status</th>
                            <th style="text-align: right;">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">{{ optional($payment->created_at)->format('d M Y') }}</div>
                                    <div style="font-size: 12px; color: #64748b;">{{ optional($payment->created_at)->format('H:i') }} WIB</div>
                                </td>
                                <td style="font-family: monospace; font-size: 13px; font-weight: 600;">{{ $payment->order_id ?: '-' }}</td>
                                <td style="font-weight: 600;">{{ optional($payment->user)->name ?: '-' }}</td>
                                <td>
                                    @if($payment->course)
                                        <span class="badge info" style="margin-bottom: 4px;">Course</span><br>
                                        {{ $payment->course->name }}
                                    @elseif($payment->event)
                                        <span class="badge warning" style="margin-bottom: 4px;">Event</span><br>
                                        {{ $payment->event->title }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><span class="badge success">Settled</span></td>
                                <td style="text-align: right; font-weight: 700; font-size: 16px; color: #16a34a;">
                                    +Rp {{ number_format($payment->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrapper">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

    <!-- Payouts Table -->
    <div class="table-section">
        <h3 class="section-title">Riwayat Pencairan Saldo & Fee</h3>

        @if($payouts->count() === 0)
            <div class="empty-state">
                <i class="bi bi-wallet2 fs-2 mb-2 d-block"></i>
                Belum ada riwayat pencairan dana oleh Admin.
            </div>
        @else
            <div class="table-container">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>Tanggal Pencairan</th>
                            <th>Deskripsi / Catatan</th>
                            <th>Jenis Pencairan</th>
                            <th>Status</th>
                            <th style="text-align: right;">Jumlah</th>
                            <th style="text-align: center;">Bukti / Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payouts as $payout)
                            <tr>
                                <td>
                                    <div style="font-weight: 600; color: #0f172a;">
                                        {{ $payout->payment_date ? $payout->payment_date->format('d M Y') : $payout->created_at->format('d M Y') }}
                                    </div>
                                    <div style="font-size: 12px; color: #64748b;">
                                        {{ $payout->payment_date ? $payout->payment_date->format('H:i') : $payout->created_at->format('H:i') }} WIB
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight: 700;">{{ $payout->title }}</div>
                                    @if($payout->notes)
                                        <div style="font-size: 13px; color: #64748b; margin-top: 4px;">
                                            <i class="bi bi-chat-left-text" style="margin-right: 4px;"></i> {{ $payout->notes }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    @if($payout->type == 'course_payout')
                                        <span class="badge info">Saldo Course</span>
                                    @else
                                        <span class="badge warning">Fee Event</span>
                                    @endif
                                </td>
                                <td>
                                    @if($payout->status == 'approved')
                                        <span class="badge success"><i class="bi bi-check-circle-fill" style="margin-right: 4px;"></i> Lunas</span>
                                    @elseif($payout->status == 'pending')
                                        <span class="badge warning"><i class="bi bi-clock-fill" style="margin-right: 4px;"></i> Proses</span>
                                    @else
                                        <span class="badge danger"><i class="bi bi-x-circle-fill" style="margin-right: 4px;"></i> Ditolak</span>
                                    @endif
                                </td>
                                <td style="text-align: right; font-weight: 800; font-size: 16px; color: #0f172a;">
                                    Rp {{ number_format($payout->amount, 0, ',', '.') }}
                                </td>
                                <td style="text-align: center;">
                                    @if($payout->status == 'approved')
                                        <a href="{{ route('trainer.finance.payouts.invoice', $payout->id) }}" target="_blank" class="btn-invoice">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-receipt" viewBox="0 0 16 16">
                                                <path d="M1.92.506a.5.5 0 0 1 .434.14L3 1.293l.646-.647a.5.5 0 0 1 .708 0L5 1.293l.646-.647a.5.5 0 0 1 .708 0L7 1.293l.646-.647a.5.5 0 0 1 .708 0L9 1.293l.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .708 0l.646.647.646-.647a.5.5 0 0 1 .801.13l.5 1A.5.5 0 0 1 15 2v12a.5.5 0 0 1-.053.224l-.5 1a.5.5 0 0 1-.8.13L13 14.707l-.646.647a.5.5 0 0 1-.708 0L11 14.707l-.646.647a.5.5 0 0 1-.708 0L9 14.707l-.646.647a.5.5 0 0 1-.708 0L7 14.707l-.646.647a.5.5 0 0 1-.708 0L5 14.707l-.646.647a.5.5 0 0 1-.708 0L3 14.707l-.646.647a.5.5 0 0 1-.801-.13l-.5-1A.5.5 0 0 1 1 14V2a.5.5 0 0 1 .053-.224l.5-1a.5.5 0 0 1 .367-.27zM0 2v12c0 .27.22.5.5.5h15a.5.5 0 0 0 .5-.5V2a.5.5 0 0 0-.5-.5H.5a.5.5 0 0 0-.5.5zm3.5 3a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 0 1h-8a.5.5 0 0 1-.5-.5zm0 2a.5.5 0 0 1 .5-.5h5a.5.5 0 0 1 0 1h-5a.5.5 0 0 1-.5-.5z"/>
                                            </svg>
                                            Invoice
                                        </a>
                                    @else
                                        <span style="color: #cbd5e1; font-weight: 600; font-size: 13px;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection