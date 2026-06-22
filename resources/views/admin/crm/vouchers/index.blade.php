@extends('layouts.crm')

@section('title', 'Loyalty & Voucher')

@section('styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--crm-primary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before {
        content: ''; display: inline-block; width: 16px; height: 2px;
        background: var(--crm-primary); border-radius: 2px;
    }
    .page-title { font-size: 1.5rem; font-weight: 800; color: var(--crm-navy); letter-spacing: -0.8px; margin: 0; }
    .page-subtitle { font-size: 0.8rem; color: var(--crm-text-subtle); margin: 5px 0 0; }

    .voucher-card {
        background: #fff;
        border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius-lg);
        box-shadow: var(--crm-shadow-sm);
        transition: transform 0.2s, box-shadow 0.2s;
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    .voucher-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--crm-shadow-md);
    }
    .voucher-header {
        background: linear-gradient(135deg, var(--crm-primary) 0%, var(--crm-primary-dark) 100%);
        color: white;
        padding: 1.25rem;
        position: relative;
    }
    .voucher-header::after {
        content: '';
        position: absolute;
        bottom: -8px; left: 0; right: 0;
        height: 8px;
        background-image: radial-gradient(circle, transparent, transparent 50%, #fff 50%, #fff 100%);
        background-size: 16px 16px;
    }
    .voucher-body {
        padding: 1.5rem 1.25rem 1.25rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .voucher-code-badge {
        font-family: monospace;
        font-size: 1.1rem;
        font-weight: 800;
        background: rgba(255,255,255,0.15);
        color: white;
        padding: 4px 10px;
        border-radius: 6px;
        letter-spacing: 0.5px;
        display: inline-block;
    }
    .voucher-points {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--crm-secondary);
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .status-badge {
        font-size: 0.65rem; font-weight: 700; letter-spacing: 0.4px;
        padding: 3px 9px; border-radius: 100px; display: inline-block;
    }
    .status-badge.active { background: rgba(16,185,129,0.1); color: #10b981; }
    .status-badge.inactive { background: var(--crm-border-soft); color: var(--crm-text-muted); }

    .redemption-log-table {
        margin-top: 2rem;
    }
    .nav-tabs-custom {
        border-bottom: 2px solid var(--crm-border-soft);
        margin-bottom: 1.5rem;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        background: none;
        color: var(--crm-text-subtle);
        font-weight: 600;
        font-size: 0.85rem;
        padding: 0.75rem 1.25rem;
        position: relative;
        transition: color 0.2s;
    }
    .nav-tabs-custom .nav-link:hover {
        color: var(--crm-navy);
    }
    .nav-tabs-custom .nav-link.active {
        color: var(--crm-primary);
    }
    .nav-tabs-custom .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px; left: 0; right: 0;
        height: 2px;
        background: var(--crm-primary);
    }
    .action-icon {
        width: 28px; height: 28px; border-radius: 7px; display: inline-flex;
        align-items: center; justify-content: center; font-size: 0.8rem;
        color: var(--crm-text-muted); transition: all 0.2s;
        text-decoration: none; background: transparent; border: none; cursor: pointer;
    }
    .action-icon:hover { background: var(--crm-border-soft); color: var(--crm-primary); }
    .action-icon.danger:hover { background: rgba(239,68,68,0.1); color: #ef4444; }
</style>
@endsection

@section('content')

{{-- Session Alerts --}}
@if(session('success'))
<div class="alert d-flex align-items-center gap-2 mb-4" style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:12px;padding:0.85rem 1.25rem;color:#065f46;font-size:0.85rem;font-weight:600;">
    <i class="bi bi-check-circle-fill" style="color:#10b981;"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert d-flex align-items-center gap-2 mb-4" style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:12px;padding:0.85rem 1.25rem;color:#991b1b;font-size:0.85rem;font-weight:600;">
    <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;"></i> {{ session('error') }}
</div>
@endif

{{-- Page Header --}}
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <div class="page-eyebrow">Loyalty System</div>
        <h1 class="page-title">Loyalty & Voucher</h1>
        <p class="page-subtitle">Kelola master voucher, skema poin rewards, dan pantau log klaim voucher oleh pelanggan.</p>
    </div>
    <div class="mt-3 mt-md-0">
        <a href="{{ route('admin.crm.vouchers.create') }}" class="btn btn-sm px-3 fw-700 hover-scale text-white" style="background:var(--crm-primary);border-radius:8px;font-size:0.8rem;padding:0.6rem 1.25rem;">
            <i class="bi bi-plus-lg me-1"></i> Buat Voucher Baru
        </a>
    </div>
</div>

<ul class="nav nav-tabs-custom" id="voucherTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="vouchers-list-tab" data-bs-toggle="tab" data-bs-target="#vouchers-list" type="button" role="tab" aria-controls="vouchers-list" aria-selected="true">
            <i class="bi bi-ticket-perforated me-1"></i> Daftar Voucher Master
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="redemptions-log-tab" data-bs-toggle="tab" data-bs-target="#redemptions-log" type="button" role="tab" aria-controls="redemptions-log" aria-selected="false">
            <i class="bi bi-clock-history me-1"></i> Log Klaim Voucher (Redemptions)
        </button>
    </li>
</ul>

<div class="tab-content" id="voucherTabsContent">
    {{-- Tab: Voucher List --}}
    <div class="tab-pane fade show active" id="vouchers-list" role="tabpanel" aria-labelledby="vouchers-list-tab">
        <div class="row g-4">
            @forelse($vouchers as $voucher)
            <div class="col-md-6 col-lg-4">
                <div class="voucher-card">
                    <div class="voucher-header d-flex justify-content-between align-items-start">
                        <div>
                            <div class="voucher-code-badge mb-2">{{ $voucher->code }}</div>
                            <h5 class="fw-800 m-0" style="font-size:1rem;color:white;">{{ $voucher->name }}</h5>
                        </div>
                        <div>
                            @if($voucher->active)
                                <span class="status-badge active">Aktif</span>
                            @else
                                <span class="status-badge inactive">Nonaktif</span>
                            @endif
                        </div>
                    </div>
                    <div class="voucher-body">
                        <p style="font-size:0.78rem;color:var(--crm-text-muted);margin-bottom:1rem;flex-grow:1;">
                            {{ $voucher->description ?: 'Tidak ada deskripsi.' }}
                        </p>

                        <div class="p-3 rounded-3 mb-3" style="background:var(--crm-border-soft);border:1px solid var(--crm-border);">
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size:0.75rem;color:var(--crm-text-subtle);">Nilai Potongan:</span>
                                <span class="fw-800 text-navy" style="font-size:0.78rem;">
                                    @if($voucher->discount_type === 'percentage')
                                        {{ $voucher->discount_value }}%
                                    @else
                                        Rp{{ number_format($voucher->discount_value, 0, ',', '.') }}
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span style="font-size:0.75rem;color:var(--crm-text-subtle);">Min. Pembelian:</span>
                                <span class="fw-700 text-navy" style="font-size:0.78rem;">Rp{{ number_format($voucher->min_purchase, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span style="font-size:0.75rem;color:var(--crm-text-subtle);">Total Diklaim:</span>
                                <span class="fw-700 text-navy" style="font-size:0.78rem;">{{ $voucher->times_redeemed }} kali</span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top:1px dashed var(--crm-border);">
                            <div class="voucher-points">
                                <i class="bi bi-star-fill"></i>
                                <span>{{ $voucher->points_required }} Poin</span>
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.crm.vouchers.edit', $voucher) }}" class="action-icon hover-scale" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button"
                                        class="action-icon danger hover-scale btn-trigger-delete-voucher"
                                        title="Hapus Voucher"
                                        data-voucher-id="{{ $voucher->id }}"
                                        data-voucher-code="{{ $voucher->code }}"
                                        data-delete-url="{{ route('admin.crm.vouchers.destroy', $voucher) }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card-minimal text-center py-5">
                    <div class="empty-state-wrapper">
                        <div class="empty-state-icon hover-scale">
                            <i class="bi bi-ticket-perforated"></i>
                        </div>
                        <h6 class="fw-800 text-navy mb-1">Voucher Belum Tersedia</h6>
                        <p class="text-muted smaller mb-0">Tekan tombol "Buat Voucher Baru" untuk mulai mendefinisikan voucher rewards.</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Tab: Redemption Log --}}
    <div class="tab-pane fade" id="redemptions-log" role="tabpanel" aria-labelledby="redemptions-log-tab">
        <div class="card-minimal">
            <div class="table-responsive">
                <table class="crm-table">
                    <thead>
                        <tr>
                            <th style="padding-left:1.25rem;">Pelanggan</th>
                            <th>Info Voucher</th>
                            <th>Kode Khusus</th>
                            <th>Tanggal Penukaran</th>
                            <th>Masa Berlaku</th>
                            <th>Status Penggunaan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($redemptions as $log)
                        <tr>
                            <td style="padding-left:1.25rem;">
                                <a href="{{ route('admin.crm.customers.show', $log->user) }}" class="d-flex align-items-center gap-3 text-decoration-none">
                                    <img src="{{ $log->user->avatar_url }}" style="width:32px;height:32px;border-radius:8px;object-fit:cover;border:1px solid var(--crm-border);"
                                         onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($log->user->name) }}&background=7c3aed&color=fff&bold=true'">
                                    <div>
                                        <div style="font-weight:700;font-size:0.8rem;color:var(--crm-navy);">{{ $log->user->name }}</div>
                                        <div style="font-size:0.68rem;color:var(--crm-text-subtle);">{{ $log->user->email }}</div>
                                    </div>
                                </a>
                            </td>
                            <td>
                                <div style="font-weight:700;font-size:0.8rem;color:var(--crm-navy);">{{ $log->voucher->name }}</div>
                                <div style="font-size:0.68rem;color:var(--crm-text-subtle);">Kode Asal: {{ $log->voucher->code }}</div>
                            </td>
                            <td>
                                <span class="font-monospace fw-800 text-primary" style="font-size:0.82rem;background:var(--crm-primary-bg);padding:2px 6px;border-radius:4px;">{{ $log->code }}</span>
                            </td>
                            <td>
                                <div style="font-size:0.8rem;font-weight:600;color:var(--crm-navy-soft);">{{ $log->redeemed_at->translatedFormat('d M Y H:i') }}</div>
                                <div style="font-size:0.68rem;color:var(--crm-text-subtle);">{{ $log->redeemed_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div style="font-size:0.8rem;font-weight:600;color:var(--crm-navy-soft);">{{ $log->expires_at ? $log->expires_at->translatedFormat('d M Y H:i') : 'Seterusnya' }}</div>
                                @if($log->expires_at)
                                    @if($log->expires_at->isPast() && !$log->is_used)
                                        <div style="font-size:0.68rem;color:#ef4444;font-weight:600;"><i class="bi bi-exclamation-circle-fill me-1"></i>Expired</div>
                                    @else
                                        <div style="font-size:0.68rem;color:var(--crm-text-subtle);">{{ $log->expires_at->diffForHumans() }}</div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($log->is_used)
                                    <span style="font-size:0.7rem;font-weight:700;color:#047857;background:rgba(16,185,129,0.1);padding:3px 8px;border-radius:100px;">
                                        <i class="bi bi-check-lg me-1"></i>Digunakan
                                    </span>
                                    @if($log->used_at)
                                        <div style="font-size:0.65rem;color:var(--crm-text-subtle);margin-top:2px;">{{ $log->used_at->translatedFormat('d M Y H:i') }}</div>
                                    @endif
                                @else
                                    @if($log->expires_at && $log->expires_at->isPast())
                                        <span style="font-size:0.7rem;font-weight:700;color:var(--crm-text-muted);background:var(--crm-border-soft);padding:3px 8px;border-radius:100px;">
                                            Tidak Tersedia
                                        </span>
                                    @else
                                        <span style="font-size:0.7rem;font-weight:700;color:#d97706;background:rgba(245,158,11,0.1);padding:3px 8px;border-radius:100px;">
                                            Belum Dipakai
                                        </span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state-wrapper">
                                    <div class="empty-state-icon hover-scale">
                                        <i class="bi bi-clock-history"></i>
                                    </div>
                                    <h6 class="fw-800 text-navy mb-1">Belum Ada Klaim</h6>
                                    <p class="text-muted smaller mb-0">Riwayat penukaran voucher rewards oleh pelanggan akan ditampilkan di sini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($redemptions->hasPages())
            <div style="padding:1rem 1.25rem;border-top:1px solid var(--crm-border-soft);">
                {{ $redemptions->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('modals')
{{-- Delete Voucher Confirmation Modal --}}
<div class="modal fade" id="deleteVoucherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <div class="modal-body p-4">

                <div class="text-center mb-4">
                    <div style="width:60px;height:60px;border-radius:50%;background:rgba(239,68,68,0.1);display:inline-flex;align-items:center;justify-content:center;color:#ef4444;font-size:1.75rem;">
                        <i class="bi bi-trash-fill"></i>
                    </div>
                    <h5 class="fw-800 mt-3 mb-1" style="font-size:1.1rem;color:var(--crm-navy);">Hapus Voucher Master?</h5>
                    <p style="font-size:0.8rem;color:var(--crm-text-subtle);margin:0;">Voucher <strong id="del-voucher-code" class="text-navy"></strong> akan dihapus permanen dari basis data.</p>
                </div>

                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-sm fw-700 px-4"
                            style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:10px;font-size:0.85rem;padding:0.6rem 1.5rem;"
                            data-bs-dismiss="modal">Batal</button>
                    <form id="deleteVoucherForm" action="#" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm fw-700 px-4 text-white"
                                style="background:#ef4444;border-radius:10px;font-size:0.85rem;padding:0.6rem 1.5rem;">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-trigger-delete-voucher').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('del-voucher-code').textContent = btn.dataset.voucherCode || '';
            document.getElementById('deleteVoucherForm').action = btn.dataset.deleteUrl;

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteVoucherModal'));
            modal.show();
        });
    });
});
</script>
@endpush
