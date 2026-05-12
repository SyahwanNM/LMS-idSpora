@extends('layouts.admin')

@section('title', 'Transaksi Pemasukan')

@section('navbar')
    @include('partials.navbar-finance')
@endsection

@section('styles')
    @include('partials.finance-styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--ids-secondary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before { content: ''; display: inline-block; width: 16px; height: 2px; background: var(--ids-secondary); border-radius: 2px; }
    
    .crm-card {
        background: #fff;
        border: 1px solid var(--ids-border);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .crm-card-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--ids-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .crm-card-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--ids-text-main);
        margin: 0;
    }
    .crm-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: collapse;
    }
    .crm-table th {
        background: #f8f9fa;
        color: var(--ids-text-muted);
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid var(--ids-border);
    }
    .crm-table td {
        padding: 1rem 1.5rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--ids-border);
        color: var(--ids-text-main);
        font-size: 0.88rem;
    }
    .badge-status {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-status.settled { background: #e0f2f1; color: #00897b; }
    .badge-status.pending { background: #fff3e0; color: #fb8c00; }
    
    .btn-crm {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-crm-primary {
        background: var(--ids-primary);
        color: var(--ids-text-main);
    }
    .btn-crm-primary:hover {
        background: var(--ids-secondary);
        color: #fff;
        transform: translateY(-1px);
    }
</style>
@endsection

@section('content')
<div class="finance-wrapper" style="margin-top: 0;">
    <!-- Sidebar -->
    @include('partials.finance-sidebar')

    <!-- Main Content -->
    <main class="finance-main">
    <div class="crm-page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="page-eyebrow">Finance Dashboard</div>
            <h1 class="hero-title" style="font-size: 1.8rem; font-weight: 700; margin-bottom: 5px;">Pemasukan Transaksi</h1>
            <p class="hero-subtitle text-muted">Pantau semua transaksi masuk dari event, course, maupun manual.</p>
        </div>
        <div class="header-actions d-flex gap-2">
            <div class="balance-card shadow-sm px-4 py-2 bg-white" style="border-radius: 12px; border: 1px solid var(--ids-border);">
                <div class="text-muted" style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Saldo Kas Saat Ini</div>
                <div class="fw-800" style="font-size: 1.25rem; font-weight: 800; color: {{ $currentBalance >= 0 ? '#16a34a' : '#dc2626' }}">
                    Rp {{ number_format($currentBalance, 0, ',', '.') }}
                </div>
            </div>
            <button type="button" class="btn-crm btn-crm-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addIncomeModal">
                <i class="bi bi-plus-lg"></i> Tambah Pemasukan Manual
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="crm-card mb-4">
        <div class="crm-card-body p-3">
            <form id="filterForm" action="{{ route('admin.finance.incomes') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-auto d-flex align-items-center">
                    <i class="bi bi-funnel-fill text-muted me-2"></i>
                    <span class="small fw-bold text-muted">Filter Periode</span>
                </div>
                <div class="col-md-3">
                    <select name="month" class="form-select" onchange="this.form.submit()">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="year" class="form-select" onchange="this.form.submit()">
                        @for($i = date('Y'); $i >= 2023; $i--)
                            <option value="{{ $i }}" {{ (request('year') ?? date('Y')) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-auto">
                    @if(request('month') || request('year'))
                        <a href="{{ route('admin.finance.incomes') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="bi bi-x me-1"></i>Reset Filter
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Filtered Total Banner --}}
    @if($filterLabel)
    <div class="alert border-0 mb-4 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #ecfdf5, #d1fae5); border-radius: 12px; padding: 16px 20px;">
        <div>
            <div class="fw-bold" style="color: #065f46;"><i class="bi bi-calendar-check-fill me-2"></i>Menampilkan pemasukan: {{ $filterLabel }}</div>
            <div class="small" style="color: #047857;">{{ $incomes->total() }} transaksi ditemukan</div>
        </div>
        <div class="text-end">
            <div class="small fw-bold" style="color: #065f46;">Total Periode Ini</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: #16a34a;">Rp {{ number_format($filteredTotal, 0, ',', '.') }}</div>
        </div>
    </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="crm-card">
        <div class="crm-card-header">
            <h2 class="crm-card-title">Semua Pemasukan</h2>
        </div>
        <div class="table-responsive">
            <table class="crm-table">
                <thead>
                    <tr>
                        <th>TANGGAL</th>
                        <th>ORDER ID / REF</th>
                        <th>USER</th>
                        <th>KETERANGAN / SUMBER</th>
                        <th>METODE</th>
                        <th>JUMLAH</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomes as $income)
                        <tr>
                            <td>{{ $income->created_at->format('d M Y H:i') }}</td>
                            <td>{{ $income->order_id ?? '-' }}</td>
                            <td>{{ $income->user->name ?? 'Guest' }}</td>
                            <td>
                                @if($income->event_id)
                                    Event: {{ $income->event->title ?? 'Unknown' }}
                                @elseif($income->course_id)
                                    Course: {{ $income->course->name ?? 'Unknown' }}
                                @else
                                    {{ $income->metadata['description'] ?? 'Pemasukan Manual (Luar Sistem)' }}
                                @endif
                            </td>
                            <td>{{ ucfirst(str_replace('_', ' ', $income->method)) }}</td>
                            <td style="font-weight: 600;">Rp {{ number_format($income->amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge-status {{ strtolower($income->status) == 'settled' ? 'settled' : 'pending' }}">
                                    {{ strtoupper($income->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4" style="color: var(--crm-text-muted);">
                                Belum ada data pemasukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($incomes->hasPages())
            <div class="crm-card-footer" style="padding: 1rem 1.5rem; border-top: 1px solid var(--crm-border-soft);">
                {{ $incomes->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
    </main>
</div>

<!-- Add Income Modal -->
<div class="modal fade" id="addIncomeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <form action="{{ route('admin.finance.store-income') }}" method="POST">
                @csrf
                <div class="modal-header" style="border-bottom: 1px solid var(--crm-border-soft); padding: 1.5rem;">
                    <h5 class="modal-title" style="font-weight: 700; color: var(--crm-text-dark);">Tambah Pemasukan Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 1.5rem;">
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--crm-text-muted);">KETERANGAN / SUMBER PEMASUKAN</label>
                        <input type="text" name="description" class="form-control" placeholder="Contoh: Sponsor Acara A" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--crm-text-muted);">JUMLAH (Rp)</label>
                        <input type="number" name="amount" class="form-control" placeholder="1000000" min="1" required style="border-radius: 8px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" style="font-size: 0.8rem; font-weight: 600; color: var(--crm-text-muted);">TANGGAL DITERIMA</label>
                        <input type="date" name="received_date" class="form-control" value="{{ date('Y-m-d') }}" required style="border-radius: 8px;">
                    </div>
                </div>
                <div class="modal-footer" style="border-top: 1px solid var(--crm-border-soft); padding: 1.25rem 1.5rem;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Batal</button>
                    <button type="submit" class="btn-crm btn-crm-primary">Simpan Pemasukan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
