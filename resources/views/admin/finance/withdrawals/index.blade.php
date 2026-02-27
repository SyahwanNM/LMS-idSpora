@extends('layouts.admin')

@section('title', 'Manage Withdrawals')

@section('navbar')
    @include('partials.navbar-finance')
@endsection

@section('styles')
<!-- Google Fonts: Inter/Roboto Style -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --ids-primary: #FFB703;
        --ids-secondary: #FB8500;
        --ids-bg: #F8F9FA;
        --ids-card-bg: #FFFFFF;
        --ids-text-main: #1A1D1F;
        --ids-text-muted: #6F767E;
        --ids-border: #EFEFEF;
    }

    body {
        background-color: var(--ids-bg) !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .withdrawal-wrapper {
        display: flex;
        min-height: calc(100vh - 100px);
        margin: 0 -12px;
    }

    .withdrawal-sidebar {
        width: 240px;
        background: #fff;
        padding: 24px;
        border-right: 1px solid var(--ids-border);
        display: none;
    }

    @media (min-width: 992px) {
        .withdrawal-sidebar { display: block; }
    }

    .nav-menu-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--ids-text-muted);
        letter-spacing: 1px;
        margin-bottom: 16px;
        display: block;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: var(--ids-text-main);
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 4px;
        transition: all 0.2s;
    }

    .sidebar-link i {
        font-size: 1.2rem;
        margin-right: 12px;
        color: var(--ids-text-muted);
    }

    .sidebar-link:hover { background: #F4F4F4; color: var(--ids-text-main); }
    .sidebar-link.active { background: #FEF6E6; color: var(--ids-text-main); }
    .sidebar-link.active i { color: var(--ids-secondary); }

    .withdrawal-main {
        flex: 1;
        padding: 24px;
    }

    .card-premium {
        background: #fff;
        border: 1px solid var(--ids-border);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }

    .status-tab {
        display: inline-flex;
        background: #F4F4F4;
        padding: 4px;
        border-radius: 12px;
        margin-bottom: 24px;
    }

    .status-tab .btn {
        border-radius: 10px;
        padding: 8px 20px;
        border: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s;
    }

    .status-tab .btn.active {
        background: #fff;
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        color: var(--ids-secondary);
    }

    .table thead th {
        background: #F8F9FA;
        border-bottom: 1px solid var(--ids-border);
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        padding: 16px;
        color: var(--ids-text-muted);
    }

    .table tbody td {
        padding: 16px;
        vertical-align: middle;
        font-size: 0.95rem;
        border-bottom: 1px solid var(--ids-border);
    }

    .user-pill {
        display: flex;
        align-items: center;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: #FEF6E6;
        color: #FB8500;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-right: 12px;
        font-size: 0.8rem;
    }

    .amount-text {
        font-weight: 700;
        color: var(--ids-text-main);
    }

    .bank-info {
        font-size: 0.85rem;
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.8rem;
    }

    .btn-approve {
        background: #16a34a;
        color: #fff;
        border: none;
        padding: 6px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .btn-reject {
        background: #dc2626;
        color: #fff;
        border: none;
        padding: 6px 16px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 20px;
        border: none;
    }

    .modal-header {
        border-bottom: 1px solid var(--ids-border);
        padding: 24px;
    }

    .modal-body {
        padding: 24px;
    }

    /* Glassmorphism Header */
    .withdrawal-hero {
        background: linear-gradient(135deg, #1A1D1F 0%, #33383C 100%);
        border-radius: 24px;
        padding: 32px;
        color: #fff;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .withdrawal-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(251, 133, 0, 0.15) 0%, rgba(251, 133, 0, 0) 70%);
        border-radius: 50%;
        z-index: 1;
    }

    .hero-label {
        background: rgba(251, 133, 0, 0.2);
        color: #FFB703;
        padding: 6px 16px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        display: inline-block;
        margin-bottom: 16px;
        border: 1px solid rgba(251, 133, 0, 0.3);
    }

    .hero-title {
        font-size: 2.25rem;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    .hero-subtitle {
        color: rgba(255, 255, 255, 0.6);
        max-width: 500px;
        line-height: 1.6;
        font-weight: 400;
        margin-bottom: 0;
    }

    .calendar-badge {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 12px 20px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .calendar-icon {
        width: 40px;
        height: 40px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFB703;
    }

    .calendar-text {
        display: flex;
        flex-direction: column;
    }

    .date-label {
        font-size: 10px;
        color: rgba(255, 255, 255, 0.5);
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .date-value {
        font-size: 14px;
        font-weight: 700;
        color: #fff;
    }
@endsection

@section('content')
<div class="withdrawal-wrapper" style="margin-top: 0;">
    @include('partials.finance-sidebar', ['class' => 'withdrawal-sidebar'])

    <main class="withdrawal-main">
        <!-- Premium Hero Header -->
        <div class="withdrawal-hero d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="z-2">
                <span class="hero-label">Authorization Central</span>
                <h1 class="hero-title">Persetujuan Payout</h1>
                <p class="hero-subtitle">Validasi permintaan penarikan dana dari reseller dan rekanan. Pastikan bukti transfer valid sebelum melakukan verifikasi.</p>
            </div>
            <div class="z-2 mt-4 mt-md-0">
                <div class="calendar-badge shadow-lg">
                    <div class="calendar-icon">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                    <div class="calendar-text">
                        <span class="date-label">Antrean Per Hari Ini</span>
                        <span class="date-value">{{ now()->format('l, d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-premium">
            <div class="status-tab">
                <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" class="btn {{ $status == 'pending' ? 'active' : '' }}">Pending</a>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'approved']) }}" class="btn {{ $status == 'approved' ? 'active' : '' }}">Berhasil</a>
                <a href="{{ route('admin.withdrawals.index', ['status' => 'rejected']) }}" class="btn {{ $status == 'rejected' ? 'active' : '' }}">Ditolak</a>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Bank Account</th>
                            <th>Amount</th>
                            <th>Request Date</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdrawals as $w)
                            <tr>
                                <td>
                                    <div class="user-pill">
                                        <div class="user-avatar text-uppercase">
                                            {{ substr($w->user->name ?? 'U', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $w->user->name ?? 'Deleted User' }}</div>
                                            <div class="text-muted small">{{ $w->user->email ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $w->bank_name }}</div>
                                    <div class="bank-info text-muted">
                                        {{ $w->account_number }} a/n {{ $w->account_holder }}
                                    </div>
                                </td>
                                <td>
                                    <div class="amount-text text-primary">Rp {{ number_format($w->amount, 0, ',', '.') }}</div>
                                </td>
                                <td>
                                    <div class="small text-muted">{{ $w->created_at->format('d M Y') }}</div>
                                    <div class="small text-muted">{{ $w->created_at->format('H:i') }}</div>
                                </td>
                                <td class="text-center">
                                    @if($w->status == 'pending')
                                        <button class="btn-approve me-1" data-bs-toggle="modal" data-bs-target="#modalApprove{{ $w->id }}">Approve</button>
                                        <button class="btn-reject" data-bs-toggle="modal" data-bs-target="#modalReject{{ $w->id }}">Reject</button>
                                    @elseif($w->status == 'approved')
                                        <span class="badge-status bg-success-subtle text-success">Approved</span>
                                        @if($w->proof_of_transfer)
                                            <a href="{{ asset('uploads/' . $w->proof_of_transfer) }}" target="_blank" class="ms-2 small text-primary"><i class="bi bi-image"></i> Bukti</a>
                                        @endif
                                    @else
                                        <span class="badge-status bg-danger-subtle text-danger">Rejected</span>
                                        <div class="small text-muted mt-1">{{ Str::limit($w->rejected_reason, 20) }}</div>
                                    @endif
                                </td>
                            </tr>

                            <!-- Modal Approve -->
                            <div class="modal fade" id="modalApprove{{ $w->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.withdrawals.approve', $w->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="fw-bold mb-0">Konfirmasi Penarikan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p class="text-muted small mb-4">Pastikan Anda telah melakukan transfer ke rekening berikut:</p>
                                                <div class="p-3 bg-light rounded-3 mb-4">
                                                    <div class="row mb-2">
                                                        <div class="col-4 text-muted small">Bank</div>
                                                        <div class="col-8 fw-bold">{{ $w->bank_name }}</div>
                                                    </div>
                                                    <div class="row mb-2">
                                                        <div class="col-4 text-muted small">No Rekening</div>
                                                        <div class="col-8 fw-bold">{{ $w->account_number }}</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-4 text-muted small">Atas Nama</div>
                                                        <div class="col-8 fw-bold">{{ $w->account_holder }}</div>
                                                    </div>
                                                </div>

                                                <label class="form-label small fw-bold">Unggah Bukti Transfer</label>
                                                <input type="file" name="proof_of_transfer" class="form-control" required>
                                                <small class="text-muted">PNG, JPG atau JPEG (Max 2MB)</small>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold">Konfirmasi & Approve</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Reject -->
                            <div class="modal fade" id="modalReject{{ $w->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.withdrawals.reject', $w->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="fw-bold mb-0">Tolak Penarikan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <label class="form-label small fw-bold">Alasan Penolakan</label>
                                                <textarea name="rejected_reason" class="form-control" rows="4" placeholder="Contoh: No rekening tidak valid atau data tidak sesuai." required></textarea>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Tolak Penarikan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <img src="https://illustrations.popsy.co/amber/no-messages.svg" alt="no-data" style="width: 150px;" class="mb-3">
                                    <p class="text-muted">Tidak ada permintaan penarikan dengan status {{ $status }}.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $withdrawals->links() }}
            </div>
        </div>
    </main>
</div>
@endsection
