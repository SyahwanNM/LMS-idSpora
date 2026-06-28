@extends('layouts.admin')

@section('title', 'Transaksi Pengeluaran')

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
    
    /* Vanilla JS Tab Switcher */
    .crm-tab-switcher { 
        display: inline-flex; 
        background: var(--ids-bg); 
        padding: 4px; 
        border-radius: 12px;
        gap: 4px;
        list-style: none;
        margin: 0;
    }
    .crm-tab-switcher button {
        font-size: 0.8rem; 
        font-weight: 700; 
        padding: 8px 18px;
        border-radius: 9px; 
        border: none;
        color: var(--ids-text-muted);
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .crm-tab-switcher button:hover {
        color: var(--ids-text-main);
    }
    .crm-tab-switcher button.active {
        background: #fff;
        color: var(--ids-primary);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .custom-tab-pane {
        display: none;
    }
    .custom-tab-pane.active {
        display: block;
        animation: fadeIn 0.3s ease;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
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
        text-transform: uppercase;
    }
    .badge-status.approved { background: #e0f2f1; color: #00897b; }
    .badge-status.pending { background: #fff3e0; color: #fb8c00; }
    .badge-status.rejected { background: #ffebee; color: #e53935; }
    
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
    .btn-crm-primary { background: var(--ids-primary); color: var(--ids-text-main); }
    .btn-crm-primary:hover { background: var(--ids-secondary); color: #fff; transform: translateY(-1px); }
    .btn-crm-outline { background: white; border: 1px solid var(--ids-border); color: var(--ids-text-main); }
    .btn-crm-outline:hover { background: var(--ids-bg); }
    .btn-action-sm { padding: 4px 10px; font-size: 0.75rem; }
    .btn-success { background: #00897b; color: white; }
    .btn-danger { background: #e53935; color: white; }
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
            <h1 class="hero-title" style="font-size: 1.8rem; font-weight: 700; margin-bottom: 5px;">Persetujuan & Pengeluaran</h1>
            <p class="hero-subtitle text-muted">Kelola dan setujui berbagai transaksi pengeluaran (Reseller, Trainer, Event, dan Manual).</p>
        </div>
        <div class="header-actions">
            <button type="button" class="btn-crm btn-crm-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="bi bi-plus-lg"></i> Tambah Pengeluaran Manual
            </button>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="crm-card mb-4">
        <div class="crm-card-body p-3">
            <form action="{{ route('admin.finance.expenses') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold">Bulan</label>
                    <select name="month" class="form-select">
                        <option value="">Semua Bulan</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($i)->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small font-weight-bold">Tahun</label>
                    <select name="year" class="form-select">
                        @for($i = date('Y'); $i >= 2023; $i--)
                            <option value="{{ $i }}" {{ (request('year') ?? date('Y')) == $i ? 'selected' : '' }}>
                                {{ $i }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-crm btn-crm-primary px-4">
                        <i class="bi bi-filter"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('admin.finance.expenses') }}" class="btn btn-crm btn-outline-secondary px-3">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex align-items-center mb-4">
        <ul class="crm-tab-switcher" id="expenseTabSwitcher">
            <li><button type="button" class="custom-tab-btn active" data-target="tab-reseller">Payout Reseller @if($pendingWithdrawalsCount > 0)<span class="badge bg-danger ms-1">{{ $pendingWithdrawalsCount }}</span>@endif</button></li>
            <li><button type="button" class="custom-tab-btn" data-target="tab-event">Cost Event</button></li>
            <li><button type="button" class="custom-tab-btn" data-target="tab-manual">Manual</button></li>
        </ul>
    </div>

    <!-- Payout Reseller Tab -->
    <div id="tab-reseller" class="custom-tab-pane active">
        <div class="crm-card">
            <div class="crm-card-header">
                <h2 class="crm-card-title">Persetujuan Payout Reseller</h2>
            </div>
            <div class="table-responsive">
                <table class="crm-table">
                    <thead>
                        <tr>
                            <th>TANGGAL</th>
                            <th>USER</th>
                            <th>BANK & REKENING</th>
                            <th>JUMLAH</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdrawals as $w)
                            <tr>
                                <td>{{ $w->created_at->format('d M Y H:i') }}</td>
                                <td>{{ $w->user->name ?? 'Unknown' }}</td>
                                <td>
                                    <strong>{{ $w->bank_name }}</strong><br>
                                    <small>{{ $w->account_number }} ({{ $w->account_holder }})</small>
                                </td>
                                <td style="font-weight: 600;">Rp {{ number_format($w->amount, 0, ',', '.') }}</td>
                                <td><span class="badge-status {{ strtolower($w->status) }}">{{ $w->status }}</span></td>
                                <td>
                                    @if($w->status == 'pending')
                                        <button class="btn btn-sm btn-success btn-action-sm mb-1" data-bs-toggle="modal" data-bs-target="#approveWithdrawalModal{{ $w->id }}">Setujui</button>
                                        <button class="btn btn-sm btn-danger btn-action-sm" data-bs-toggle="modal" data-bs-target="#rejectWithdrawalModal{{ $w->id }}">Tolak</button>
                                    @elseif($w->status == 'approved' && $w->proof_of_transfer)
                                        <a href="{{ asset('uploads/' . $w->proof_of_transfer) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-action-sm">Bukti</a>
                                    @elseif($w->status == 'rejected')
                                        <small class="text-danger" title="{{ $w->rejected_reason }}">Lihat Alasan</small>
                                    @endif
                                </td>
                            </tr>
                            
                            <!-- Modal Approve Reseller Payout -->
                            @if($w->status == 'pending')
                            <div class="modal fade" id="approveWithdrawalModal{{ $w->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                        <form action="{{ route('admin.withdrawals.approve', $w->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Setujui Pencairan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Upload bukti transfer untuk pencairan sejumlah <strong>Rp {{ number_format($w->amount, 0, ',', '.') }}</strong> ke rekening <strong>{{ $w->bank_name }} - {{ $w->account_number }} a/n {{ $w->account_holder }}</strong>.</p>
                                                <div class="mb-3">
                                                    <label class="form-label">Bukti Transfer (Wajib)</label>
                                                    <input type="file" name="proof_of_transfer" class="form-control" accept="image/*" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Setujui & Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal Reject Reseller Payout -->
                            <div class="modal fade" id="rejectWithdrawalModal{{ $w->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                        <form action="{{ route('admin.withdrawals.reject', $w->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Pencairan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Alasan Penolakan (Wajib)</label>
                                                    <textarea name="rejected_reason" class="form-control" rows="3" required placeholder="Contoh: Nomor rekening tidak valid"></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak Pencairan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                            <tr><td colspan="6" class="text-center py-4">Belum ada request payout.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($withdrawals->hasPages())
            <div class="crm-card-footer" style="padding: 1rem 1.5rem; border-top: 1px solid var(--crm-border-soft);">
                {{ $withdrawals->links('pagination::bootstrap-5') }}
            </div>
            @endif
        </div>
    </div>
    
    
    <!-- Cost Event Tab -->
    <div id="tab-event" class="custom-tab-pane">
        <div class="crm-card">
            <div class="crm-card-header">
                <h2 class="crm-card-title">Persetujuan Cost Event</h2>
            </div>
            <div class="table-responsive">
                <table class="crm-table">
                    <thead>
                        <tr>
                            <th>EVENT</th>
                            <th>ITEM</th>
                            <th>QTY & HARGA</th>
                            <th>TOTAL</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($eventExpenses as $ee)
                            <tr>
                                <td>{{ $ee->event->title ?? 'Unknown' }}</td>
                                <td>{{ $ee->item }}</td>
                                <td>{{ $ee->quantity }} x Rp {{ number_format($ee->unit_price, 0, ',', '.') }}</td>
                                <td style="font-weight: 600;">Rp {{ number_format($ee->total, 0, ',', '.') }}</td>
                                <td><span class="badge-status {{ strtolower($ee->status) }}">{{ $ee->status }}</span></td>
                                <td>
                                    @if($ee->status == 'pending')
                                        <button class="btn btn-sm btn-success btn-action-sm mb-1" data-bs-toggle="modal" data-bs-target="#approveEventModal{{ $ee->id }}">Setujui</button>
                                        <button class="btn btn-sm btn-danger btn-action-sm" data-bs-toggle="modal" data-bs-target="#rejectEventModal{{ $ee->id }}">Tolak</button>
                                    @elseif($ee->status == 'approved' && $ee->proof_of_payment)
                                        <a href="{{ asset('storage/' . $ee->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-action-sm">Bukti</a>
                                    @endif
                                </td>
                            </tr>
                            
                            <!-- Modal Approve Event Expense -->
                            @if($ee->status == 'pending')
                            <div class="modal fade" id="approveEventModal{{ $ee->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                        <form action="{{ route('admin.finance.event-expense.approve', $ee->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Setujui Cost Event</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Upload bukti pembayaran untuk cost event <strong>{{ $ee->item }}</strong> sejumlah <strong>Rp {{ number_format($ee->total, 0, ',', '.') }}</strong>.</p>
                                                <div class="mb-3">
                                                    <label class="form-label">Bukti Pembayaran (Wajib)</label>
                                                    <input type="file" name="proof_of_payment" class="form-control" accept="image/*" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Setujui & Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal Reject Event Expense -->
                            <div class="modal fade" id="rejectEventModal{{ $ee->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                        <form action="{{ route('admin.finance.event-expense.reject', $ee->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Cost Event</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Alasan Penolakan (Wajib)</label>
                                                    <textarea name="rejected_reason" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                            <tr><td colspan="6" class="text-center py-4">Belum ada data cost event.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Manual Expenses Tab -->
    <div id="tab-manual" class="custom-tab-pane">
        <div class="crm-card">
            <div class="crm-card-header">
                <h2 class="crm-card-title">Pengeluaran Manual</h2>
            </div>
            <div class="table-responsive">
                <table class="crm-table">
                    <thead>
                        <tr>
                            <th>TANGGAL</th>
                            <th>KETERANGAN</th>
                            <th>KATEGORI</th>
                            <th>JUMLAH</th>
                            <th>STATUS</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($generalExpenses as $ge)
                            <tr>
                                <td>{{ $ge->expense_date->format('d M Y') }}</td>
                                <td>{{ $ge->description }}</td>
                                <td>{{ $ge->category ?? '-' }}</td>
                                <td style="font-weight: 600;">Rp {{ number_format($ge->amount, 0, ',', '.') }}</td>
                                <td><span class="badge-status {{ strtolower($ge->status ?? 'approved') }}">{{ $ge->status ?? 'APPROVED' }}</span></td>
                                <td>
                                    @if($ge->status == 'pending')
                                        <button class="btn btn-sm btn-success btn-action-sm mb-1" data-bs-toggle="modal" data-bs-target="#approveExpenseModal{{ $ge->id }}">Setujui</button>
                                        <button class="btn btn-sm btn-danger btn-action-sm" data-bs-toggle="modal" data-bs-target="#rejectExpenseModal{{ $ge->id }}">Tolak</button>
                                    @elseif($ge->status == 'approved' && $ge->proof_of_payment)
                                        <a href="{{ asset('storage/' . $ge->proof_of_payment) }}" target="_blank" class="btn btn-sm btn-outline-primary btn-action-sm">Bukti</a>
                                    @endif
                                </td>
                            </tr>
                            
                            <!-- Modal Approve Manual Expense -->
                            @if($ge->status == 'pending')
                            <div class="modal fade" id="approveExpenseModal{{ $ge->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                        <form action="{{ route('admin.finance.manual-expense.approve', $ge->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Setujui Pengeluaran Manual</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Upload bukti pembayaran untuk <strong>{{ $ge->description }}</strong> sejumlah <strong>Rp {{ number_format($ge->amount, 0, ',', '.') }}</strong>.</p>
                                                <div class="mb-3">
                                                    <label class="form-label">Bukti Pembayaran (Wajib)</label>
                                                    <input type="file" name="proof_of_payment" class="form-control" accept="image/*" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Setujui & Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Modal Reject Manual Expense -->
                            <div class="modal fade" id="rejectExpenseModal{{ $ge->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                        <form action="{{ route('admin.finance.manual-expense.reject', $ge->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header">
                                                <h5 class="modal-title">Tolak Pengeluaran Manual</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Alasan Penolakan (Wajib)</label>
                                                    <textarea name="rejected_reason" class="form-control" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Tolak</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                            <tr><td colspan="5" class="text-center py-4">Belum ada pengeluaran manual.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    </main>
</div>

<!-- Modal Tambah Pengeluaran Manual -->
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <form action="{{ route('admin.finance.store-expense') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Pengeluaran Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Deskripsi / Keterangan</label>
                        <input type="text" name="description" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jumlah (Rp)</label>
                        <input type="number" name="amount" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Pengeluaran</label>
                        <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori (Opsional)</label>
                        <input type="text" name="category" class="form-control" placeholder="Contoh: Operasional, Pemasaran">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bukti Pembayaran (Wajib)</label>
                        <input type="file" name="proof_of_payment" class="form-control" accept="image/*" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-crm btn-crm-primary">Simpan Pengeluaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabBtns = document.querySelectorAll('.custom-tab-btn');
    const tabPanes = document.querySelectorAll('.custom-tab-pane');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetId = this.getAttribute('data-target');
            
            // Remove active class from all
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding pane
            this.classList.add('active');
            document.getElementById(targetId).classList.add('active');
        });
    });
});
</script>
@endsection
