@extends('layouts.admin')

@section('title', 'Kelola Trainer & Saldo – Finance Hub')

@section('navbar')
    @include('partials.navbar-finance')
@endsection

@section('styles')
    @include('partials.finance-styles')
    <style>
        /* Crucial layout visibility fix - override global display none on tab-content */
        .finance-main .tab-content {
            display: block !important;
        }

        /* Typography & Variables */
        :root {
            --ids-primary-gradient: linear-gradient(135deg, #FFB703 0%, #FB8500 100%);
            --ids-success-gradient: linear-gradient(135deg, #10B981 0%, #059669 100%);
            --ids-info-gradient: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            --ids-shadow-premium: 0 10px 30px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.02);
            --ids-shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.08), 0 1px 10px rgba(0, 0, 0, 0.03);
            --ids-border-color: #EFEFEF;
            --ids-text-color: #1E293B;
            --ids-text-muted: #64748B;
        }

        /* Premium Wrapper & Main Grid */
        .finance-main {
            background-color: #F8F9FA;
            min-height: calc(100vh - 72px);
            padding: 2.5rem;
        }

        /* Card Designs */
        .premium-card {
            background: #FFFFFF;
            border: 1px solid var(--ids-border-color);
            border-radius: 20px;
            box-shadow: var(--ids-shadow-premium);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--ids-shadow-hover);
        }

        /* Trainer Card specific styling */
        .trainer-card {
            border-top: 4px solid #FFB703;
        }

        /* Avatar Circle with initials */
        .avatar-initials {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: #FFF7E6;
            color: #FB8500;
            font-weight: 700;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: inset 0 2px 4px rgba(251, 133, 0, 0.06);
            letter-spacing: -0.5px;
        }

        /* Balance display */
        .balance-box {
            background: #F8F9FA;
            border: 1px dashed var(--ids-border-color);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            margin: 1.25rem 0;
        }

        .balance-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--ids-text-muted);
            font-weight: 700;
            margin-bottom: 4px;
        }

        .balance-amount {
            font-size: 1.5rem;
            font-weight: 800;
            color: #10B981;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Pill Navigation Styling */
        .nav-pills-custom {
            background: #FFFFFF;
            padding: 6px;
            border-radius: 16px;
            box-shadow: var(--ids-shadow-premium);
            border: 1px solid var(--ids-border-color);
            display: inline-flex;
            width: fit-content;
        }

        .nav-pills-custom .nav-link {
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 10px 24px;
            color: var(--ids-text-muted);
            transition: all 0.2s ease;
            border: none;
            background: transparent;
        }

        .nav-pills-custom .nav-link.active {
            background: var(--ids-primary-gradient);
            color: #FFFFFF !important;
            box-shadow: 0 4px 15px rgba(251, 133, 0, 0.25);
        }

        .nav-pills-custom .nav-link:hover:not(.active) {
            background: #F8F9FA;
            color: var(--ids-text-color);
        }

        /* Action Buttons */
        .btn-premium-primary {
            background: var(--ids-primary-gradient);
            border: none;
            color: #FFFFFF;
            font-weight: 700;
            border-radius: 12px;
            padding: 11px 20px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(251, 133, 0, 0.15);
        }

        .btn-premium-primary:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(251, 133, 0, 0.25);
            color: #FFFFFF;
        }

        .btn-premium-primary:disabled {
            background: #E2E8F0;
            color: #94A3B8;
            box-shadow: none;
            cursor: not-allowed;
        }

        .btn-premium-secondary {
            background: #FFFFFF;
            border: 1px solid var(--ids-border-color);
            color: var(--ids-text-color);
            font-weight: 600;
            border-radius: 12px;
            padding: 9px 18px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }

        .btn-premium-secondary:hover {
            background: #F8F9FA;
            border-color: #CBD5E1;
            color: var(--ids-text-color);
        }

        /* Table Aesthetics */
        .premium-table-card {
            border-radius: 20px;
            box-shadow: var(--ids-shadow-premium);
            border: 1px solid var(--ids-border-color);
            background: #FFFFFF;
            overflow: hidden;
        }

        .premium-table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .premium-table th {
            background: #F8F9FA;
            color: var(--ids-text-muted);
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 24px;
            border-bottom: 1px solid var(--ids-border-color);
        }

        .premium-table td {
            padding: 18px 24px;
            border-bottom: 1px solid var(--ids-border-color);
            color: var(--ids-text-color);
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .premium-table tr:last-child td {
            border-bottom: none;
        }

        /* Badge Styling */
        .pill-badge {
            font-size: 0.75rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 30px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .pill-badge-success {
            background: #E8F9F3;
            color: #10B981;
        }

        .pill-badge-warning {
            background: #FFF7E6;
            color: #FB8500;
        }

        .pill-badge-muted {
            background: #F1F5F9;
            color: #64748B;
        }

        /* Pulsing indicator */
        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: currentColor;
            display: inline-block;
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.7; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.7; }
        }

        /* Header Summary Stats */
        .stat-badge-premium {
            background: rgba(255, 183, 3, 0.1);
            color: #FB8500;
            border: 1px solid rgba(255, 183, 3, 0.2);
            border-radius: 50px;
            padding: 6px 16px;
            font-size: 0.85rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .stat-eyebrow {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 700;
            color: #FB8500;
            margin-bottom: 6px;
        }

        /* Modal styling overrides */
        .modal-content {
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
            border: none;
            overflow: hidden;
        }

        .modal-header {
            padding: 24px 32px 16px;
        }

        .modal-body {
            padding: 16px 32px 24px;
        }

        .modal-footer {
            padding: 16px 32px 32px;
        }

        .modal-form-label {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--ids-text-muted);
            margin-bottom: 8px;
        }

        .form-control-premium {
            border-radius: 12px;
            padding: 12px 16px;
            border: 1px solid #E2E8F0;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }

        .form-control-premium:focus {
            border-color: #FFB703;
            box-shadow: 0 0 0 4px rgba(255, 183, 3, 0.12);
            outline: none;
        }
    </style>
@endsection

@section('content')
<div class="finance-wrapper" style="margin-top: 0;">
    @include('partials.finance-sidebar')

    <main class="finance-main">
        <!-- Premium Header Area -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-5">
            <div>
                <div class="stat-eyebrow">Finance Administration</div>
                <h1 class="hero-title fw-800 text-dark mb-2" style="font-size: 2.2rem; letter-spacing: -1px;">Kelola Trainer & Saldo</h1>
                <p class="text-muted mb-0" style="font-size: 0.95rem;">Pantau pendistribusian dana, input fee mengajar, serta kelola pencairan saldo course trainer secara transparan.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <span class="stat-badge-premium">
                    <i class="bi bi-people-fill"></i>
                    {{ $trainers->count() }} Terdaftar
                </span>
                <span class="badge bg-white text-dark border rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1 shadow-sm font-weight-700" style="font-size: 0.85rem;">
                    <i class="bi bi-shield-fill-check text-success"></i> Minimum Payout: Rp {{ number_format($minDisburse, 0, ',', '.') }}
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 mb-4 shadow-sm p-3 d-flex align-items-center" role="alert">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 32px; height: 32px; flex-shrink:0;">
                    <i class="bi bi-check-lg fs-5"></i>
                </div>
                <div>
                    <span class="fw-bold d-block text-dark" style="font-size: 0.95rem;">Transaksi Berhasil</span>
                    <span class="text-muted small">{{ session('success') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="top: 50%; transform: translateY(-50%);"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 mb-4 shadow-sm p-3 d-flex align-items-center" role="alert">
                <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 32px; height: 32px; flex-shrink:0;">
                    <i class="bi bi-exclamation-triangle-fill fs-6"></i>
                </div>
                <div>
                    <span class="fw-bold d-block text-dark" style="font-size: 0.95rem;">Transaksi Gagal</span>
                    <span class="text-muted small">{{ session('error') }}</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" style="top: 50%; transform: translateY(-50%);"></button>
            </div>
        @endif

        <!-- Custom Tab Pills Navigation -->
        <ul class="nav nav-pills nav-pills-custom mb-5 gap-1" id="trainerTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="course-tab" data-bs-toggle="pill" data-bs-target="#course-payout" type="button" role="tab" aria-controls="course-payout" aria-selected="true">
                    <i class="bi bi-wallet2 me-2"></i>Saldo Course
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="event-tab" data-bs-toggle="pill" data-bs-target="#event-fee" type="button" role="tab" aria-controls="event-fee" aria-selected="false">
                    <i class="bi bi-calendar-check me-2"></i>Fee Event
                    @if($pendingEventFees->count() > 0)
                        <span class="badge bg-danger ms-2 rounded-circle px-2" style="font-size: 10px; font-weight: 800;">{{ $pendingEventFees->count() }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="pill" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">
                    <i class="bi bi-clock-history me-2"></i>Riwayat Payout
                </button>
            </li>
        </ul>

        <!-- Tab Panes Content -->
        <div class="tab-content" id="trainerTabContent">
            
            <!-- SECTION 1: SALDO COURSE -->
            <div class="tab-pane fade show active" id="course-payout" role="tabpanel" aria-labelledby="course-tab">
                <div class="row g-4">
                    @forelse($trainers as $trainer)
                    <div class="col-md-6 col-lg-4">
                        <div class="premium-card trainer-card p-4 h-100 d-flex flex-column justify-content-between">
                            <div>
                                @php
                                    $hasBank = !empty($trainer->bank_name) && !empty($trainer->bank_account_number);
                                @endphp
                                <!-- Top Row: Avatar & Status Badge -->
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div class="avatar-initials">
                                        {{ strtoupper(substr($trainer->name, 0, 2)) }}
                                    </div>
                                    @if(($trainer->wallet_balance ?? 0) >= $minDisburse)
                                        @if($hasBank)
                                            <span class="pill-badge pill-badge-success">
                                                <span class="pulse-dot"></span> Siap Cair
                                            </span>
                                        @else
                                            <span class="pill-badge pill-badge-warning">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Belum Set Rekening
                                            </span>
                                        @endif
                                    @else
                                        <span class="pill-badge pill-badge-muted">
                                            Belum Capai Batas
                                        </span>
                                    @endif
                                </div>

                                <!-- Trainer Info -->
                                <h5 class="fw-bold text-dark mb-1">{{ $trainer->name }}</h5>
                                <p class="text-muted small mb-3"><i class="bi bi-envelope me-1"></i>{{ $trainer->email }}</p>

                                <!-- Balance Box -->
                                <div class="balance-box">
                                    <div class="balance-title">Saldo Tersedia</div>
                                    <div class="balance-amount">Rp {{ number_format($trainer->wallet_balance ?? 0, 0, ',', '.') }}</div>
                                </div>
                            </div>

                            <!-- Action button -->
                            <div class="mt-3">
                                @if(($trainer->wallet_balance ?? 0) >= $minDisburse)
                                    @if($hasBank)
                                        <button class="btn btn-premium-primary w-100 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#disburseModal{{ $trainer->id }}">
                                            <i class="bi bi-cash-stack"></i> Cairkan Saldo Sekarang
                                        </button>
                                    @else
                                        <button class="btn btn-premium-primary w-100 d-flex align-items-center justify-content-center gap-2" disabled style="background: #E2E8F0; color: #94A3B8; border: none; cursor: not-allowed;">
                                            <i class="bi bi-exclamation-circle"></i> Rekening Belum Set
                                        </button>
                                        <small class="text-danger text-center d-block mt-2" style="font-size: 0.75rem; font-weight: 600;">Trainer belum mengatur rekening bank</small>
                                    @endif
                                @else
                                    <button class="btn btn-premium-primary w-100 d-flex align-items-center justify-content-center gap-2" disabled>
                                        <i class="bi bi-cash-stack"></i> Saldo Belum Cukup
                                    </button>
                                    <small class="text-muted text-center d-block mt-2" style="font-size: 0.75rem;">Minimum pencairan Rp {{ number_format($minDisburse, 0, ',', '.') }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="premium-card p-5 text-center">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center p-4 mb-4">
                                <i class="bi bi-people text-muted fs-1"></i>
                            </div>
                            <h5 class="fw-bold">Tidak Ada Data Trainer</h5>
                            <p class="text-muted mb-0">Belum ada akun pengajar terdaftar dalam sistem finance hub.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- SECTION 2: FEE EVENT -->
            <div class="tab-pane fade" id="event-fee" role="tabpanel" aria-labelledby="event-tab">
                
                <!-- PART B: Pending Fee Requests needing payment -->
                <div class="premium-card">
                    <div class="p-4 border-bottom border-light d-flex justify-content-between align-items-center" style="background: #FFFBF2;">
                        <div>
                            <h5 class="fw-800 text-dark mb-1"><i class="bi bi-hourglass-split text-warning me-2"></i>Permintaan Fee Event (Menunggu Pembayaran)</h5>
                            <p class="text-muted small mb-0">Daftar fee event yang sudah diinput dan sedang mengantre untuk proses transfer manual oleh admin.</p>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Trainer Penerima</th>
                                    <th>Event / Pelatihan</th>
                                    <th>Jumlah Fee</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingEventFees as $fee)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-initials bg-warning-subtle text-warning font-weight-700" style="width: 32px; height: 32px; font-size: 0.8rem; border-radius: 8px;">
                                                {{ strtoupper(substr($fee->trainer->name ?? 'T', 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $fee->trainer->name ?? '-' }}</div>
                                                <div class="text-muted small" style="font-size: 11px;">{{ $fee->trainer->email ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $fee->event->title ?? '-' }}</div>
                                        <div class="text-muted small">{{ $fee->notes }}</div>
                                    </td>
                                    <td>
                                        <span class="fw-800 text-success" style="font-size: 1rem;">Rp {{ number_format($fee->amount, 0, ',', '.') }}</span>
                                    </td>
                                    @php
                                        $feeHasBank = !empty($fee->trainer?->bank_name) && !empty($fee->trainer?->bank_account_number);
                                    @endphp
                                    <td class="text-end">
                                        @if($feeHasBank)
                                            <button class="btn btn-premium-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#payFeeModal{{ $fee->id }}">
                                                <i class="bi bi-wallet2 me-1"></i> Bayar Sekarang
                                            </button>
                                        @else
                                            <button class="btn btn-secondary btn-sm px-3 opacity-75" disabled title="Trainer belum mengatur rekening bank">
                                                <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Belum Set Rekening
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted">
                                        <div class="p-3"><i class="bi bi-check-all text-success fs-1"></i></div>
                                        Tidak ada permintaan fee event yang pending saat ini. Semua lunas!
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- SECTION 3: RIWAYAT PAYOUT -->
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <div class="premium-card">
                    <div class="p-4 border-bottom border-light">
                        <h5 class="fw-800 text-dark mb-1"><i class="bi bi-clock-history me-2 text-info"></i>Riwayat Pencairan & Pembayaran Fee</h5>
                        <p class="text-muted small mb-0">Menampilkan hingga 10 transaksi pembayaran terakhir kepada pihak pengajar yang telah disetujui.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="premium-table">
                            <thead>
                                <tr>
                                    <th>Tanggal Pembayaran</th>
                                    <th>Trainer Penerima</th>
                                    <th>Kategori / Reference</th>
                                    <th>Jumlah Dibayarkan</th>
                                    <th class="text-end">Bukti Pembayaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payoutHistory as $history)
                                <tr>
                                    <td>
                                        <span class="text-dark"><i class="bi bi-calendar3 me-2 text-muted"></i>{{ $history->payment_date ? $history->payment_date->format('d M Y') : '-' }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-bold">{{ $history->trainer->name ?? $history->trainer_name }}</span>
                                    </td>
                                    <td>
                                        @if($history->type == 'course_payout')
                                            <span class="pill-badge pill-badge-success"><i class="bi bi-book-half me-1"></i> Course Payout</span>
                                        @else
                                            <span class="pill-badge pill-badge-warning"><i class="bi bi-award-fill me-1"></i> Event Fee</span>
                                            <div class="small text-muted mt-1" style="font-size: 11px;">{{ $history->event->title ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-800 text-dark">Rp {{ number_format($history->amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-2 justify-content-end">
                                            <a href="{{ route('admin.finance.payouts.invoice', $history->id) }}" target="_blank" class="btn btn-premium-primary btn-sm d-inline-flex align-items-center gap-1" style="box-shadow: none; font-size: 0.8rem; padding: 6px 14px;">
                                                <i class="bi bi-receipt"></i> Invoice
                                            </a>
                                            @if($history->proof_file)
                                                <a href="{{ asset('storage/'.$history->proof_file) }}" target="_blank" class="btn btn-premium-secondary btn-sm d-inline-flex align-items-center gap-1" style="font-size: 0.8rem; padding: 5px 12px;">
                                                    <i class="bi bi-file-earmark-image"></i> Bukti Transfer
                                                </a>
                                            @else
                                                <span class="text-muted small italic align-self-center">Tidak ada lampiran</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <div class="p-3"><i class="bi bi-slash-circle fs-1"></i></div>
                                        Belum ada riwayat pembayaran yang terverifikasi dalam sistem.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <!-- ==========================================
             MODALS ELEVATED AT TOP LEVEL
             ========================================== -->

        <!-- 1. Payout/Disburse Modals (Saldo Course) -->
        @foreach($trainers as $trainer)
            @if(($trainer->wallet_balance ?? 0) >= $minDisburse)
            <div class="modal fade" id="disburseModal{{ $trainer->id }}" tabindex="-1" aria-labelledby="disburseModalLabel{{ $trainer->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <form action="{{ route('admin.finance.trainers.disburse', $trainer->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header border-0 pb-0">
                                <div>
                                    <h5 class="modal-title fw-800 text-dark" id="disburseModalLabel{{ $trainer->id }}">Proses Pencairan Saldo</h5>
                                    <p class="text-muted small mb-0">Lakukan transfer bank secara manual terlebih dahulu sebelum memproses form ini.</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-start">
                                <!-- Amount box -->
                                <div class="text-center p-3 rounded-4 mb-4" style="background: #E8F9F3;">
                                    <span class="text-muted small d-block uppercase fw-bold" style="font-size: 10px; letter-spacing: 1px;">NOMINAL PENCAIRAN</span>
                                    <h3 class="fw-800 text-success mb-0" style="font-size: 1.85rem;">Rp {{ number_format($trainer->wallet_balance, 0, ',', '.') }}</h3>
                                </div>

                                <!-- Trainer Info Details -->
                                <div class="p-3 bg-light rounded-3 mb-4">
                                    <div class="row g-2">
                                        <div class="col-5 text-muted small fw-bold">NAMA RECEIVER:</div>
                                        <div class="col-7 small text-dark fw-bold">{{ $trainer->name }}</div>
                                        
                                        <div class="col-5 text-muted small fw-bold">BANK DETAILS:</div>
                                        <div class="col-7 small text-dark fw-bold">
                                            @if($trainer->bank_name)
                                                {{ $trainer->bank_name }} - {{ $trainer->bank_account_number }}
                                            @else
                                                <span class="text-danger italic"><i class="bi bi-exclamation-triangle-fill me-1"></i>Belum set rekening</span>
                                            @endif
                                        </div>
                                        
                                        @if($trainer->bank_account_name)
                                        <div class="col-5 text-muted small fw-bold">ATAS NAMA:</div>
                                        <div class="col-7 small text-dark fw-bold">{{ $trainer->bank_account_name }}</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Upload Proof of Payment -->
                                <div class="mb-3">
                                    <label class="form-label modal-form-label"><i class="bi bi-cloud-arrow-up-fill me-1"></i>Bukti Transfer (Image/PNG/JPG/JPEG)</label>
                                    <input type="file" name="proof_of_payment" class="form-control form-control-premium" required>
                                    <div class="form-text small">Maksimum ukuran file: 5MB</div>
                                </div>

                                <!-- Notes -->
                                <div class="mb-0">
                                    <label class="form-label modal-form-label"><i class="bi bi-chat-left-text-fill me-1"></i>Catatan Internal (Optional)</label>
                                    <textarea name="notes" class="form-control form-control-premium" rows="2" placeholder="Pencairan dana salary / fee course untuk trainer..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-premium-secondary px-4 py-2" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-premium-primary px-4 py-2" {{ !$hasBank ? 'disabled' : '' }}><i class="bi bi-check-circle-fill me-1"></i> Konfirmasi & Cairkan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        @endforeach

        <!-- 3. Pay Fee & Reject Fee Modals (Permintaan Fee Event) -->
        @foreach($pendingEventFees as $fee)
            @php
                $feeHasBank = !empty($fee->trainer?->bank_name) && !empty($fee->trainer?->bank_account_number);
            @endphp
            <!-- Pay Fee Modal -->
            <div class="modal fade" id="payFeeModal{{ $fee->id }}" tabindex="-1" aria-labelledby="payFeeModalLabel{{ $fee->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content text-start">
                        <form action="{{ route('admin.finance.event-fee.approve', $fee->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-header border-0 pb-0">
                                <div>
                                    <h5 class="modal-title fw-800 text-dark" id="payFeeModalLabel{{ $fee->id }}">Upload Bukti Pembayaran</h5>
                                    <p class="text-muted small mb-0">Harap lakukan transfer manual sejumlah nominal di bawah ini.</p>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-4 text-center">
                                <div class="p-3 rounded-4 mb-4" style="background: #E8F9F3;">
                                    <small class="text-muted uppercase fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">TOTAL TRANSFER</small>
                                    <h3 class="fw-800 text-success mb-0" style="font-size: 1.85rem;">Rp {{ number_format($fee->amount, 0, ',', '.') }}</h3>
                                </div>
                                <!-- Trainer Info Details -->
                                <div class="p-3 bg-light rounded-3 mb-4 text-start">
                                    <div class="row g-2">
                                        <div class="col-5 text-muted small fw-bold">TRAINER PENERIMA:</div>
                                        <div class="col-7 small text-dark fw-bold">{{ $fee->trainer->name ?? $fee->trainer_name }}</div>
                                        
                                        <div class="col-5 text-muted small fw-bold">BANK DETAILS:</div>
                                        <div class="col-7 small text-dark fw-bold">
                                            @if($feeHasBank)
                                                {{ $fee->trainer->bank_name }} - {{ $fee->trainer->bank_account_number }}
                                            @else
                                                <span class="text-danger italic"><i class="bi bi-exclamation-triangle-fill me-1"></i>Belum set rekening</span>
                                            @endif
                                        </div>
                                        
                                        @if($fee->trainer?->bank_account_holder || $fee->trainer?->bank_account_name)
                                        <div class="col-5 text-muted small fw-bold">ATAS NAMA:</div>
                                        <div class="col-7 small text-dark fw-bold">{{ $fee->trainer->bank_account_holder ?? $fee->trainer->bank_account_name }}</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-start mb-3">
                                    <label class="form-label modal-form-label"><i class="bi bi-cloud-arrow-up-fill me-1"></i>Upload Bukti Transfer</label>
                                    <input type="file" name="proof_of_payment" class="form-control form-control-premium" required {{ !$feeHasBank ? 'disabled' : '' }}>
                                    <div class="form-text small">Ukuran file maksimal: 5MB (PNG/JPG/JPEG)</div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0">
                                <button type="button" class="btn btn-premium-secondary px-4 py-2" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-premium-primary px-4 py-2" {{ !$feeHasBank ? 'disabled' : '' }}><i class="bi bi-check2-circle"></i> Konfirmasi Pembayaran</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        @endforeach

    </main>
</div>
@endsection
