@extends('layouts.crm')

@section('title', 'Blast Broadcast')

@section('styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--crm-primary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before { content: ''; display: inline-block; width: 16px; height: 2px; background: var(--crm-primary); border-radius: 2px; }
    .page-title { font-size: 1.5rem; font-weight: 800; color: var(--crm-navy); letter-spacing: -0.8px; margin: 0; }
    .page-subtitle { font-size: 0.8rem; color: var(--crm-text-subtle); margin: 5px 0 0; }
    .platform-chip {
        display: inline-flex; align-items: center; gap: 4px;
        font-size: 0.68rem; font-weight: 700; padding: 3px 8px;
        border-radius: 6px;
    }
    .platform-chip.email { background: rgba(124,58,237,0.08); color: var(--crm-primary); }
    .platform-chip.wa    { background: rgba(16,185,129,0.08); color: #059669; }
    .seg-pill {
        font-size: 0.65rem; font-weight: 700; padding: 3px 9px;
        border-radius: 100px; display: inline-block;
    }
</style>
@endsection

@section('content')
{{-- Page Header --}}
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <div class="page-eyebrow">Broadcasting Module</div>
        <h1 class="page-title">Blast Broadcast</h1>
        <p class="page-subtitle">Riwayat pengiriman pesan massal dan manajemen strategi jangkauan.</p>
    </div>
    <a href="{{ route('admin.crm.broadcast.create') }}" class="btn btn-sm fw-700 px-4 mt-3 mt-md-0 hover-scale"
       style="background:var(--crm-primary);color:#fff;border-radius:9px;font-size:0.82rem;padding-top:0.55rem;padding-bottom:0.55rem;">
        <i class="bi bi-plus-lg me-1"></i> Buat Broadcast Baru
    </a>
</div>

@if(session('success'))
@endif

{{-- Table --}}
<div class="card-minimal">
    <div class="table-responsive">
        <table class="crm-table">
            <thead>
                <tr>
                    <th style="padding-left:1.25rem;">Tanggal</th>
                    <th>Judul & Pesan</th>
                    <th>Segmen</th>
                    <th>Platform</th>
                    <th style="text-align:center;">Target</th>
                    <th style="text-align:center;">Status</th>
                    <th style="padding-right:1.25rem;text-align:center;">Detail</th>
                </tr>
            </thead>
            <tbody>
                @forelse($broadcasts as $item)
                @php
                    $segMap = ['all'=>['bg:var(--crm-border-soft);color:var(--crm-text-muted);','Semua User'],'reseller'=>['background:rgba(245,158,11,0.1);color:#d97706;','Reseller'],'trainer'=>['background:rgba(6,182,212,0.1);color:#0891b2;','Trainer'],'no_event'=>['background:rgba(239,68,68,0.1);color:#dc2626;','Belum Ikut Event']];
                    $seg = $segMap[$item->segment] ?? ['background:var(--crm-border-soft);color:var(--crm-text-muted);','Lainnya'];
                @endphp
                <tr>
                    <td style="padding-left:1.25rem;">
                        <div style="font-weight:700;font-size:0.82rem;color:var(--crm-navy);">{{ $item->created_at->translatedFormat('d M Y') }}</div>
                        <div style="font-size:0.72rem;color:var(--crm-text-subtle);">{{ $item->created_at->format('H:i') }} WIB</div>
                    </td>
                    <td>
                        <div style="font-weight:700;font-size:0.82rem;color:var(--crm-navy);" class="text-truncate" style="max-width:220px;">{{ $item->title }}</div>
                        <div style="font-size:0.72rem;color:var(--crm-text-subtle);">{{ Str::limit($item->message, 50) }}</div>
                    </td>
                    <td>
                        <span class="seg-pill" style="{{ $seg[0] }}">{{ $seg[1] }}</span>
                    </td>
                    <td>
                        <div class="d-flex gap-1 flex-wrap">
                            @if(in_array($item->platform, ['email','both']))
                                <span class="platform-chip email"><i class="bi bi-envelope-fill"></i> Email</span>
                            @endif
                            @if(in_array($item->platform, ['whatsapp','both']))
                                <span class="platform-chip wa"><i class="bi bi-whatsapp"></i> WhatsApp</span>
                            @endif
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <div style="font-weight:800;font-size:0.9rem;color:var(--crm-navy);">{{ $item->target_count }}</div>
                        <div style="font-size:0.65rem;color:var(--crm-text-subtle);font-weight:600;">User</div>
                    </td>
                    <td style="text-align:center;">
                        <span style="font-size:0.65rem;font-weight:700;padding:3px 9px;border-radius:100px;background:rgba(16,185,129,0.1);color:#059669;">
                            {{ strtoupper($item->status) }}
                        </span>
                    </td>
                    <td style="padding-right:1.25rem;text-align:center;">
                        <button class="action-icon hover-scale" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}" title="Lihat Detail">
                            <i class="bi bi-eye"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state-wrapper">
                            <div class="empty-state-icon hover-scale">
                                <i class="bi bi-megaphone"></i>
                            </div>
                            <h6 class="fw-800 text-navy mb-1">Belum Ada Broadcast</h6>
                            <p class="text-muted smaller mb-3">Mulai jangkau customer Anda dengan mengirimkan pesan massal pertama.</p>
                            <a href="{{ route('admin.crm.broadcast.create') }}" class="btn btn-sm px-4 fw-700" style="background:var(--crm-primary);color:#fff;border-radius:8px;">Kirim Sekarang</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Modals --}}
@push('modals')
@foreach($broadcasts as $item)
@php
    $segMap = ['all'=>'Semua User','reseller'=>'Reseller','trainer'=>'Trainer','no_event'=>'Belum Ikut Event'];
    $segLabel = $segMap[$item->segment] ?? 'Lainnya';
@endphp
<div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <div class="modal-header border-0 p-4" style="background:var(--crm-navy);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-megaphone-fill text-white"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-800 text-white">Detail Broadcast</h6>
                        <span style="font-size:0.7rem;color:rgba(255,255,255,0.6);">{{ $item->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background:var(--crm-border-soft);">
                <div class="card-minimal p-3 mb-3">
                    <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Judul / Subjek</div>
                    <div style="font-weight:700;font-size:0.9rem;color:var(--crm-navy);">{{ $item->title }}</div>
                </div>
                <div class="card-minimal p-3 mb-3">
                    <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Isi Pesan</div>
                    <div style="font-size:0.85rem;color:var(--crm-navy-soft);line-height:1.7;white-space:pre-wrap;">{{ $item->message }}</div>
                </div>
                @if($item->link)
                <div class="card-minimal p-3 mb-3">
                    <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;">Link / URL Tujuan</div>
                    <div style="font-size:0.8rem;font-weight:700;color:var(--crm-primary);word-break:break-all;">
                        <a href="{{ $item->link }}" target="_blank" style="color:var(--crm-primary);text-decoration:none;">
                            {{ $item->link }} <i class="bi bi-box-arrow-up-right ms-1" style="font-size:0.75rem;"></i>
                        </a>
                    </div>
                </div>
                @endif
                <div class="row g-2">
                    <div class="col-6">
                        <div class="card-minimal p-3">
                            <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Segmen</div>
                            <div style="font-weight:700;font-size:0.85rem;color:var(--crm-navy);">{{ $segLabel }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card-minimal p-3">
                            <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Platform</div>
                            <div style="font-weight:700;font-size:0.85rem;color:var(--crm-navy);">{{ ucfirst($item->platform) }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card-minimal p-3">
                            <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Total Target</div>
                            <div style="font-weight:800;font-size:1.2rem;color:var(--crm-navy);">{{ $item->target_count }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card-minimal p-3">
                            <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;">Status</div>
                            <span style="font-size:0.72rem;font-weight:700;padding:3px 9px;border-radius:100px;background:rgba(16,185,129,0.1);color:#059669;">{{ strtoupper($item->status) }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white" style="border-radius:0 0 20px 20px;">
                <button type="button" class="btn btn-sm fw-700 px-4" style="background:var(--crm-navy);color:#fff;border-radius:8px;" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endpush
@endsection
