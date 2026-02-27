@extends('layouts.admin')

@section('title', 'Detail Keuangan Event')

@section('navbar')
    @include('partials.navbar-finance')
@endsection

@section('styles')
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

    .finance-wrapper {
        display: flex;
        min-height: calc(100vh - 100px);
        margin: 0 -12px;
    }

    .finance-main { flex: 1; padding: 24px; }

    .finance-sidebar {
        width: 240px;
        background: #fff;
        padding: 24px;
        border-right: 1px solid var(--ids-border);
        display: none;
    }

    @media (min-width: 992px) {
        .finance-sidebar { display: block; }
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

    .sidebar-link i { font-size: 1.2rem; margin-right: 12px; color: var(--ids-text-muted); }
    .sidebar-link:hover { background: #F4F4F4; color: var(--ids-text-main); }
    .sidebar-link.active { background: #FEF6E6; color: var(--ids-text-main); }
    .sidebar-link.active i { color: var(--ids-secondary); }

    .card-premium {
        background: #fff;
        border: 1px solid var(--ids-border);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        margin-bottom: 24px;
    }

    .stat-mini-label { font-size: 11px; color: var(--ids-text-muted); text-transform: uppercase; font-weight: 700; }
    .stat-mini-value { font-size: 1.25rem; font-weight: 800; color: var(--ids-text-main); }

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
    }

    .badge-status {
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 11px;
        font-weight: 700;
    }

    .bg-success-light { background: #E6FFFA; color: #00BFA5; }
    .bg-warning-light { background: #FFF9E6; color: #FFB700; }
    .bg-danger-light { background: #FFE6E6; color: #FF4D4D; }

    .btn-back {
        color: var(--ids-text-muted);
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 16px;
    }
    .btn-back:hover { color: var(--ids-text-main); }
</style>
@endsection

@section('content')
<div class="finance-wrapper" style="margin-top: 0;">
    @include('partials.finance-sidebar')

    <main class="finance-main">
        <a href="{{ route('admin.finance.events') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar Event
        </a>

        <div class="row g-4 mb-4">
            <div class="col-md-12">
                <div class="card-premium" style="background: linear-gradient(135deg, #1A1D1F 0%, #33383C 100%); color: #fff;">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="fw-bold mb-1">{{ $event->title }}</h2>
                            <p class="text-white-50 mb-0">Rincian Arus Kas & Analisis Keuntungan Lab</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <div class="stat-mini-label text-white-50">Laba Bersih Estimasi</div>
                            <div class="stat-mini-value text-white" style="font-size: 2rem;">Rp {{ number_format($totalIncome - $opExpenses - $commissions, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Stats -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card-premium text-center">
                    <div class="stat-mini-label">Total Pendapatan (Gross)</div>
                    <div class="stat-mini-value text-success">Rp {{ number_format($totalIncome, 0, ',', '.') }}</div>
                    <small class="text-muted">Dari pendaftaran settled</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-premium text-center">
                    <div class="stat-mini-label">Biaya Operasional (Ops)</div>
                    <div class="stat-mini-value text-danger">Rp {{ number_format($opExpenses, 0, ',', '.') }}</div>
                    <small class="text-muted">Dari rincian expenses event</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-premium text-center">
                    <div class="stat-mini-label">Komisi Reseller</div>
                    <div class="stat-mini-value text-warning">Rp {{ number_format($commissions, 0, ',', '.') }}</div>
                    <small class="text-muted">Berdasarkan deskripsi referral</small>
                </div>
            </div>
        </div>

        <!-- Transaction List -->
        <h5 class="fw-bold mb-3">Daftar Transaksi Peserta</h5>
        <div class="card-premium">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Order ID</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $t)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $t->user->name ?? 'Deleted User' }}</div>
                                    <small class="text-muted">{{ $t->user->email ?? '-' }}</small>
                                </td>
                                <td><code class="text-primary">{{ $t->order_id }}</code></td>
                                <td class="fw-bold">Rp {{ number_format($t->amount, 0, ',', '.') }}</td>
                                <td><span class="text-uppercase small">{{ $t->method }}</span></td>
                                <td>
                                    @if($t->status == 'settled')
                                        <span class="badge-status bg-success-light">Settled</span>
                                    @elseif($t->status == 'pending')
                                        <span class="badge-status bg-warning-light">Pending</span>
                                    @else
                                        <span class="badge-status bg-danger-light">Rejected</span>
                                    @endif
                                </td>
                                <td class="text-muted small">{{ $t->created_at->format('d M Y, H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada transaksi untuk event ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        </div>

        <!-- Expense Details (from EventExpenses) -->
        <h5 class="fw-bold mb-3">Rincian Pengeluaran Operasional</h5>
        <div class="card-premium">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($event->expenses as $exp)
                            <tr>
                                <td class="fw-bold">{{ $exp->item }}</td>
                                <td>{{ $exp->quantity }}</td>
                                <td>Rp {{ number_format($exp->unit_price, 0, ',', '.') }}</td>
                                <td class="fw-bold text-danger">Rp {{ number_format($exp->total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Tidak ada rincian biaya operasional yang diinput.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if($event->expenses->count() > 0)
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-end">Total Biaya Ops:</th>
                            <th class="text-danger">Rp {{ number_format($opExpenses, 0, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </main>
</div>
@endsection
