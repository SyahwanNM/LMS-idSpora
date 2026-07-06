@extends('layouts.crm')

@section('title', 'Tiket Support')

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
    .filter-input {
        border: 1px solid var(--crm-border); border-radius: 8px;
        padding: 0.5rem 0.85rem; font-size: 0.82rem; color: var(--crm-navy);
        background: var(--crm-border-soft); outline: none; width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .filter-input:focus { border-color: var(--crm-primary-light); box-shadow: 0 0 0 3px rgba(124,58,237,0.1); background: #fff; }
    .type-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
    .status-badge {
        font-size: 0.65rem; font-weight: 700; padding: 3px 9px; border-radius: 100px; display: inline-block;
    }
    .status-new      { background: rgba(124,58,237,0.1); color: var(--crm-primary); }
    .status-processed{ background: rgba(245,158,11,0.1); color: #d97706; }
    .status-resolved { background: rgba(16,185,129,0.1); color: #059669; }
    .status-ignored  { background: var(--crm-border-soft); color: var(--crm-text-muted); }
</style>
@endsection

@section('content')
{{-- Page Header --}}
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <div class="page-eyebrow">Support Helpdesk</div>
        <h1 class="page-title">Tiket Support</h1>
        <p class="page-subtitle">Manajemen masukan, pertanyaan, dan kendala dari ekosistem IDSPora.</p>
    </div>
    <div class="kpi-card-v2 mt-3 mt-md-0" style="--kpi-color:#7c3aed;padding:0.9rem 1.2rem;min-width:160px;">
        <div class="d-flex align-items-center gap-3">
            <div class="kpi-icon-v2" style="background:rgba(124,58,237,0.1);color:var(--crm-primary);margin:0;width:36px;height:36px;">
                <i class="bi bi-headset" style="font-size:1rem;"></i>
            </div>
            <div>
                <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;">Total Tiket</div>
                <div style="font-size:1.4rem;font-weight:800;color:var(--crm-navy);letter-spacing:-0.5px;line-height:1;">{{ $messages->total() }}</div>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
@endif

{{-- Filter --}}
<div class="card-minimal p-3 mb-4">
    <form action="{{ route('admin.crm.support.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-4">
            <label style="font-size:0.7rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;display:block;">Jenis Pesan</label>
            <select name="type" class="filter-input" onchange="this.form.submit()">
                <option value="">Semua Jenis</option>
                <option value="kendala"    {{ request('type')=='kendala'    ?'selected':'' }}>🚨 Kendala / Bug</option>
                <option value="pertanyaan" {{ request('type')=='pertanyaan' ?'selected':'' }}>❓ Pertanyaan</option>
                <option value="masukan"    {{ request('type')=='masukan'    ?'selected':'' }}>💡 Masukan</option>
                <option value="lainnya"    {{ request('type')=='lainnya'    ?'selected':'' }}>📋 Lainnya</option>
            </select>
        </div>
        <div class="col-md-4">
            <label style="font-size:0.7rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;display:block;">Status</label>
            <select name="status" class="filter-input" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                <option value="new"       {{ request('status')=='new'       ?'selected':'' }}>🆕 Baru</option>
                <option value="processed" {{ request('status')=='processed' ?'selected':'' }}>⚙️ Diproses</option>
                <option value="resolved"  {{ request('status')=='resolved'  ?'selected':'' }}>✅ Selesai</option>
                <option value="ignored"   {{ request('status')=='ignored'   ?'selected':'' }}>⏭️ Diabaikan</option>
            </select>
        </div>
        <div class="col-md-4">
            <a href="{{ route('admin.crm.support.index') }}" class="btn btn-sm px-3 fw-600 hover-scale" style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:8px;font-size:0.8rem;">
                <i class="bi bi-x-lg me-1"></i> Reset Filter
            </a>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="card-minimal mb-4">
    <div class="table-responsive">
        <table class="crm-table">
            <thead>
                <tr>
                    <th style="padding-left:1.25rem;">Pengirim</th>
                    <th>Subjek & Jenis</th>
                    <th>Cuplikan Pesan</th>
                    <th style="text-align:center;">Lampiran</th>
                    <th>Status</th>
                    <th style="padding-right:1.25rem;text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $msg)
                @php
                    $typeColor = match($msg->type) {
                        'kendala' => '#ef4444', 'pertanyaan' => '#06b6d4',
                        'masukan' => '#10b981', default => '#94a3b8'
                    };
                @endphp
                <tr data-bs-toggle="modal" data-bs-target="#modalMsg{{ $msg->id }}">
                    <td style="padding-left:1.25rem;">
                        <div style="font-weight:700;font-size:0.85rem;color:var(--crm-navy);">{{ $msg->name }}</div>
                        <div style="font-size:0.72rem;color:var(--crm-text-subtle);">{{ $msg->email }}</div>
                        <div style="font-size:0.7rem;color:var(--crm-primary);font-weight:600;margin-top:2px;">{{ $msg->created_at->translatedFormat('d M, H:i') }}</div>
                    </td>
                    <td>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="type-dot" style="background:{{ $typeColor }};"></span>
                            <span style="font-size:0.65rem;font-weight:700;text-transform:uppercase;color:{{ $typeColor }};">{{ $msg->type }}</span>
                        </div>
                        <div style="font-size:0.82rem;font-weight:600;color:var(--crm-navy-soft);" class="text-truncate" style="max-width:200px;">{{ $msg->subject }}</div>
                    </td>
                    <td>
                        <div style="font-size:0.78rem;color:var(--crm-text-muted);line-height:1.5;max-width:260px;">{{ Str::limit($msg->message, 80) }}</div>
                    </td>
                    <td style="text-align:center;">
                        @if($msg->attachment)
                            <a href="{{ asset('uploads/'.$msg->attachment) }}" target="_blank" onclick="event.stopPropagation()">
                                <img src="{{ asset('uploads/'.$msg->attachment) }}" style="width:36px;height:36px;border-radius:8px;object-fit:cover;border:1.5px solid var(--crm-border);">
                            </a>
                        @else
                            <span style="color:var(--crm-border);font-size:1rem;"><i class="bi bi-dash"></i></span>
                        @endif
                    </td>
                    <td>
                        <span class="status-badge status-{{ $msg->status }}">
                            {{ ['new'=>'Baru','processed'=>'Diproses','resolved'=>'Selesai','ignored'=>'Diabaikan'][$msg->status] ?? $msg->status }}
                        </span>
                    </td>
                    <td style="padding-right:1.25rem;text-align:right;" onclick="event.stopPropagation()">
                        <div class="dropdown">
                            <button class="action-icon border-0 bg-transparent" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg py-2" style="border-radius:12px;min-width:180px;font-size:0.82rem;">
                                <li><h6 class="dropdown-header" style="font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.5px;">Ubah Status</h6></li>
                                <li>
                                    <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                                        @csrf <input type="hidden" name="status" value="processed">
                                        <button type="submit" class="dropdown-item py-2 fw-600"><i class="bi bi-arrow-repeat me-2 text-warning"></i>Tandai Diproses</button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                                        @csrf <input type="hidden" name="status" value="resolved">
                                        <button type="submit" class="dropdown-item py-2 fw-600"><i class="bi bi-check-circle me-2 text-success"></i>Tandai Selesai</button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                                        @csrf <input type="hidden" name="status" value="ignored">
                                        <button type="submit" class="dropdown-item py-2 fw-600 text-danger"><i class="bi bi-x-circle me-2"></i>Abaikan</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state-wrapper">
                            <div class="empty-state-icon hover-scale">
                                <i class="bi bi-chat-left-dots"></i>
                            </div>
                            <h6 class="fw-800 text-navy mb-1">Tidak Ada Tiket Baru</h6>
                            <p class="text-muted smaller mb-0">Semua tenang, belum ada tiket support masuk saat ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($messages->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--crm-border-soft);">
        {{ $messages->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>

{{-- Detail Modals --}}
@push('modals')
@foreach($messages as $msg)
@php
    $statusCls = ['new'=>'status-new','processed'=>'status-processed','resolved'=>'status-resolved','ignored'=>'status-ignored'][$msg->status] ?? 'status-ignored';
    $statusLbl = ['new'=>'Baru','processed'=>'Diproses','resolved'=>'Selesai','ignored'=>'Diabaikan'][$msg->status] ?? $msg->status;
    $typeColor = match($msg->type) { 'kendala'=>'#ef4444','pertanyaan'=>'#06b6d4','masukan'=>'#10b981',default=>'#94a3b8' };
@endphp
<div class="modal fade" id="modalMsg{{ $msg->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <div class="modal-header border-0 p-4" style="background:var(--crm-navy);">
                <div class="d-flex align-items-center gap-3">
                    <div style="width:40px;height:40px;border-radius:10px;background:rgba(255,255,255,0.1);display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-chat-dots-fill text-white fs-5"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-800 text-white">Detail Support Ticket</h6>
                        <span style="font-size:0.72rem;color:rgba(255,255,255,0.6);">#SPT-{{ str_pad($msg->id,5,'0',STR_PAD_LEFT) }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="background:var(--crm-border-soft);">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="card-minimal p-3">
                            <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;">Pengirim</div>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:36px;height:36px;border-radius:9px;background:rgba(124,58,237,0.1);color:var(--crm-primary);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:0.9rem;">{{ strtoupper(substr($msg->name,0,1)) }}</div>
                                <div>
                                    <div style="font-weight:700;font-size:0.85rem;color:var(--crm-navy);">{{ $msg->name }}</div>
                                    <div style="font-size:0.72rem;color:var(--crm-text-subtle);">{{ $msg->email }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card-minimal p-3">
                            <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;">Jenis & Status</div>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge-soft" style="background:{{ $typeColor }}20;color:{{ $typeColor }};">{{ strtoupper($msg->type) }}</span>
                                <span class="status-badge {{ $statusCls }}">{{ $statusLbl }}</span>
                            </div>
                            <div style="font-size:0.72rem;color:var(--crm-text-subtle);margin-top:8px;"><i class="bi bi-clock me-1"></i>{{ $msg->created_at->translatedFormat('d F Y, H:i') }} WIB</div>
                        </div>
                    </div>
                </div>
                <div class="card-minimal p-4 mb-3">
                    <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;">Subjek & Isi Pesan</div>
                    <h6 class="fw-800 mb-3" style="color:var(--crm-navy);">{{ $msg->subject }}</h6>
                    <div style="font-size:0.875rem;color:var(--crm-navy-soft);line-height:1.7;white-space:pre-wrap;">{{ $msg->message }}</div>
                </div>
                @if($msg->attachment)
                <div class="card-minimal p-4">
                    <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:10px;">Lampiran</div>
                    <a href="{{ asset('uploads/'.$msg->attachment) }}" target="_blank">
                        <img src="{{ asset('uploads/'.$msg->attachment) }}" class="img-fluid" style="border-radius:10px;border:1px solid var(--crm-border);">
                    </a>
                </div>
                @endif
            </div>
            <div class="modal-footer border-0 p-4 bg-white" style="border-radius:0 0 20px 20px;">
                <div class="d-flex flex-wrap gap-2 w-100 justify-content-between align-items-center">
                    <div class="d-flex flex-wrap gap-2">
                        @if($msg->status !== 'processed' && $msg->status !== 'resolved')
                        <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                            @csrf <input type="hidden" name="status" value="processed">
                            <button type="submit" class="btn btn-sm fw-700 px-3" style="background:#f59e0b;color:#fff;border-radius:8px;"><i class="bi bi-arrow-repeat me-1"></i>Proses</button>
                        </form>
                        @endif
                        @if($msg->status !== 'resolved')
                        <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                            @csrf <input type="hidden" name="status" value="resolved">
                            <button type="submit" class="btn btn-sm fw-700 px-3" style="background:#10b981;color:#fff;border-radius:8px;"><i class="bi bi-check-circle me-1"></i>Selesaikan</button>
                        </form>
                        @endif
                        @if($msg->status === 'new')
                        <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                            @csrf <input type="hidden" name="status" value="ignored">
                            <button type="submit" class="btn btn-sm fw-700 px-3" style="background:var(--crm-border-soft);color:#ef4444;border-radius:8px;"><i class="bi bi-x-circle me-1"></i>Abaikan</button>
                        </form>
                        @endif
                    </div>
                    <button type="button" class="btn btn-sm fw-700 px-4" style="background:var(--crm-navy);color:#fff;border-radius:8px;" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach
@endpush
@endsection
