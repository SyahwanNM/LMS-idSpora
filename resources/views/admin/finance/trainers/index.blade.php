@extends('layouts.admin')

@section('title', 'Kelola Trainer & Saldo')

@section('navbar')
    @include('partials.navbar-finance')
@endsection

@section('styles')
    @include('partials.finance-styles')
    <style>
        .trainer-card {
            background: #fff;
            border-radius: 16px;
            border: 1px solid var(--ids-border);
            transition: all 0.2s ease;
        }
        .trainer-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.05); }
        .balance-value { font-size: 1.4rem; font-weight: 800; color: #16a34a; }
        .section-header { border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; margin-bottom: 20px; font-weight: 800; color: #1e293b; }
        .nav-pills-custom .nav-link { border-radius: 10px; font-weight: 600; padding: 10px 20px; color: #64748b; }
        .nav-pills-custom .nav-link.active { background-color: var(--ids-primary); color: #000; }
        .badge-pill { padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 700; }
    </style>
@endsection

@section('content')
<div class="finance-wrapper" style="margin-top: 0;">
    @include('partials.finance-sidebar')

    <main class="finance-main">
        <div class="crm-page-header d-flex justify-content-between align-items-center mb-4">
            <div>
                <div class="page-eyebrow">Finance Dashboard</div>
                <h1 class="hero-title" style="font-size: 1.8rem; font-weight: 700; margin-bottom: 5px;">Kelola Trainer & Saldo</h1>
                <p class="hero-subtitle text-muted">Kelola pencairan saldo course & fee event untuk para pengajar.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 mb-4 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 mb-4 shadow-sm" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <ul class="nav nav-pills nav-pills-custom mb-4 gap-2" id="trainerTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="course-tab" data-bs-toggle="pill" data-bs-target="#course-payout" type="button">
                    <i class="bi bi-wallet2 me-2"></i>Saldo Course
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="event-tab" data-bs-toggle="pill" data-bs-target="#event-fee" type="button">
                    <i class="bi bi-calendar-check me-2"></i>Fee Event
                    @if($pendingEventFees->count() > 0)
                        <span class="badge bg-danger ms-1" style="font-size: 10px;">{{ $pendingEventFees->count() }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="history-tab" data-bs-toggle="pill" data-bs-target="#history" type="button">
                    <i class="bi bi-clock-history me-2"></i>Riwayat
                </button>
            </li>
        </ul>

        <div class="tab-content" id="trainerTabContent">
            <!-- Course Payouts Section -->
            <div class="tab-pane fade show active" id="course-payout" role="tabpanel">
                <div class="row g-4">
                    @forelse($trainers as $trainer)
                    <div class="col-md-6 col-lg-4">
                        <div class="trainer-card p-4 h-100 d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $trainer->avatar_url }}" class="rounded-circle me-3" style="width: 48px; height: 48px; object-fit: cover;">
                                <div class="overflow-hidden">
                                    <h6 class="mb-0 fw-bold text-truncate">{{ $trainer->name }}</h6>
                                    <small class="text-muted">{{ $trainer->email }}</small>
                                </div>
                            </div>
                            <div class="mb-4">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 0.5px;">Saldo Tersedia</small>
                                <div class="balance-value">Rp {{ number_format($trainer->wallet_balance ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="mt-auto">
                                @if($trainer->can_disburse)
                                    <button class="btn btn-primary w-100 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#disburseModal{{ $trainer->id }}">
                                        Cairkan Saldo
                                    </button>
                                @elseif($trainer->pending_payout)
                                    <button class="btn btn-warning w-100 rounded-pill fw-bold disabled">
                                        <i class="bi bi-hourglass-split me-1"></i>Proses Pending
                                    </button>
                                @else
                                    <button class="btn btn-light w-100 rounded-pill fw-bold disabled text-muted" style="font-size: 11px;">
                                        Min. Rp {{ number_format($minDisburse/1000, 0) }}rb untuk cair
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Disburse Modal -->
                        @if($trainer->can_disburse)
                        <div class="modal fade" id="disburseModal{{ $trainer->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 rounded-4 shadow">
                                    <form action="{{ route('admin.finance.trainers.disburse', $trainer->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold">Pencairan Saldo Trainer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4">
                                            <div class="p-3 bg-light rounded-3 mb-4 text-center">
                                                <small class="text-muted d-block mb-1">JUMLAH PENCAIRAN</small>
                                                <h3 class="fw-800 text-success mb-0">Rp {{ number_format($trainer->wallet_balance ?? 0, 0, ',', '.') }}</h3>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Rekening Tujuan ({{ $trainer->bank_name ?? 'N/A' }})</label>
                                                <div class="p-2 border rounded-3 bg-white">
                                                    <div class="small fw-bold">{{ $trainer->bank_account_holder ?? $trainer->name }}</div>
                                                    <div class="text-primary fw-bold">{{ $trainer->bank_account_number ?? 'Belum diatur' }}</div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label small fw-bold">Upload Bukti Transfer</label>
                                                <input type="file" name="proof_of_payment" class="form-control" required>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label small fw-bold">Catatan (Optional)</label>
                                                <textarea name="notes" class="form-control" rows="2" placeholder="Contoh: Fee Course periode Mei 2026"></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Proses Pencairan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="text-center py-5 bg-white rounded-4 border border-dashed">
                            <i class="bi bi-people text-muted display-1 mb-3"></i>
                            <h5 class="fw-bold">Belum Ada Trainer</h5>
                            <p class="text-muted">Tidak ada pengguna dengan role 'trainer' yang ditemukan.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Event Fee Section -->
            <div class="tab-pane fade" id="event-fee" role="tabpanel">
                <!-- Section 1: Ended Events Needing Fee Request -->
                <div class="crm-card mb-5">
                    <div class="crm-card-header bg-light-subtle">
                        <h5 class="crm-card-title"><i class="bi bi-calendar-check me-2 text-primary"></i>Event Selesai (Butuh Input Fee)</h5>
                        <p class="small text-muted mb-0">Event yang sudah selesai namun fee trainer belum diinput.</p>
                    </div>
                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>EVENT</th>
                                    <th>TRAINER</th>
                                    <th>TANGGAL SELESAI</th>
                                    <th class="text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($endedEvents as $event)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $event->title }}</div>
                                        <div class="small text-muted">Rp {{ number_format($event->price, 0, ',', '.') }}</div>
                                    </td>
                                    <td>{{ $event->trainer->name ?? 'N/A' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($event->ended_at)->format('d M Y') }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#requestFeeModal{{ $event->id }}">
                                            Input Fee
                                        </button>

                                        <!-- Request Fee Modal -->
                                        <div class="modal fade" id="requestFeeModal{{ $event->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 rounded-4 shadow text-start">
                                                    <form action="{{ route('admin.finance.events.fee-request', $event->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title fw-bold">Input Fee Trainer Event</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">Nama Event</label>
                                                                <input type="text" class="form-control bg-light" value="{{ $event->title }}" readonly>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label small fw-bold">Jumlah Fee (Rp)</label>
                                                                <input type="number" name="amount" class="form-control" placeholder="0" required>
                                                            </div>
                                                            <div class="mb-0">
                                                                <label class="form-label small fw-bold">Catatan</label>
                                                                <textarea name="notes" class="form-control" rows="2" placeholder="Fee mengajar event..."></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Buat Permintaan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">Tidak ada event selesai yang menunggu input fee.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Section 2: Pending Event Fee Requests -->
                <div class="crm-card">
                    <div class="crm-card-header" style="background-color: #fff9f0;">
                        <h5 class="crm-card-title"><i class="bi bi-hourglass-split me-2 text-warning"></i>Permintaan Fee Event (Menunggu Pembayaran)</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>TRAINER</th>
                                    <th>EVENT / KETERANGAN</th>
                                    <th>JUMLAH</th>
                                    <th class="text-end">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingEventFees as $fee)
                                <tr>
                                    <td>{{ $fee->trainer->name ?? '-' }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $fee->event->title ?? '-' }}</div>
                                        <div class="small text-muted">{{ $fee->notes }}</div>
                                    </td>
                                    <td class="fw-bold text-success">Rp {{ number_format($fee->amount, 0, ',', '.') }}</td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#payFeeModal{{ $fee->id }}">
                                            Bayar Sekarang
                                        </button>
                                        <button class="btn btn-sm btn-link text-danger text-decoration-none" data-bs-toggle="modal" data-bs-target="#rejectFeeModal{{ $fee->id }}">
                                            Tolak
                                        </button>

                                        <!-- Pay Fee Modal -->
                                        <div class="modal fade" id="payFeeModal{{ $fee->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 rounded-4 shadow text-start">
                                                    <form action="{{ route('admin.finance.event-fee.approve', $fee->id) }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title fw-bold">Proses Pembayaran Fee</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4 text-center">
                                                            <div class="mb-4">
                                                                <small class="text-muted">JUMLAH TRANSFER</small>
                                                                <h3 class="fw-800 text-success">Rp {{ number_format($fee->amount, 0, ',', '.') }}</h3>
                                                            </div>
                                                            <div class="text-start mb-3">
                                                                <label class="form-label small fw-bold">Bukti Transfer</label>
                                                                <input type="file" name="proof_of_payment" class="form-control" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Konfirmasi Bayar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reject Fee Modal -->
                                        <div class="modal fade" id="rejectFeeModal{{ $fee->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 rounded-4 shadow text-start">
                                                    <form action="{{ route('admin.finance.event-fee.reject', $fee->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header border-0 pb-0">
                                                            <h5 class="modal-title fw-bold text-danger">Tolak Permintaan Fee</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <label class="form-label small fw-bold">Alasan Penolakan</label>
                                                            <textarea name="rejected_reason" class="form-control" rows="3" required placeholder="Jelaskan alasan penolakan..."></textarea>
                                                        </div>
                                                        <div class="modal-footer border-0 pt-0 pb-4 px-4">
                                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Ya, Tolak</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">Tidak ada permintaan fee event pending.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- History Section -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div class="crm-card">
                    <div class="crm-card-header">
                        <h5 class="crm-card-title">Riwayat Pencairan & Fee (10 Terakhir)</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="crm-table">
                            <thead>
                                <tr>
                                    <th>TANGGAL</th>
                                    <th>TRAINER</th>
                                    <th>JENIS / REF</th>
                                    <th>JUMLAH</th>
                                    <th>BUKTI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($payoutHistory as $history)
                                <tr>
                                    <td>{{ $history->payment_date->format('d M Y') }}</td>
                                    <td>{{ $history->trainer->name ?? $history->trainer_name }}</td>
                                    <td>
                                        @if($history->type == 'course_payout')
                                            <span class="badge-pill bg-info text-white">Course Payout</span>
                                        @else
                                            <span class="badge-pill bg-primary text-white">Event Fee</span>
                                            <div class="small text-muted">{{ $history->event->title ?? '-' }}</div>
                                        @endif
                                    </td>
                                    <td class="fw-bold">Rp {{ number_format($history->amount, 0, ',', '.') }}</td>
                                    <td>
                                        @if($history->proof_file)
                                            <a href="{{ asset('storage/'.$history->proof_file) }}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill py-0" style="font-size: 10px;">
                                                <i class="bi bi-file-earmark-image"></i> Lihat
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
