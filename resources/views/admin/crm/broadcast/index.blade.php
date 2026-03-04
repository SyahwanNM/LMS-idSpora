@extends('layouts.admin')

@section('title', 'Riwayat Broadcast')

@section('navbar')
    @include('partials.navbar-crm')
@endsection

@section('styles')
<style>
    /* Hero Header */
    .crm-hero {
        background: linear-gradient(135deg, #1A1D1F 0%, #2A2F34 100%);
        border-radius: 24px;
        padding: 32px;
        color: #fff;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .hero-label {
        background: rgba(109, 40, 217, 0.2);
        color: #a78bfa;
        padding: 6px 16px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        display: inline-block;
        margin-bottom: 16px;
        border: 1px solid rgba(139, 92, 246, 0.3);
    }

    .hero-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    /* Sidebar-like Nav */
    .crm-wrapper {
        display: flex;
        min-height: calc(100vh - 72px);
    }

    .crm-sidebar-new {
        width: 260px;
        background: #fff;
        padding: 24px;
        border-right: 1px solid #eee;
        flex-shrink: 0;
    }

    .crm-main {
        flex-grow: 1;
        padding: 32px;
        background-color: #F8F9FA;
    }

    .nav-menu-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 1px;
        margin-bottom: 16px;
        display: block;
        margin-top: 24px;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: #1e293b;
        text-decoration: none;
        border-radius: 12px;
        margin-bottom: 4px;
        font-weight: 600;
        transition: all 0.2s;
        gap: 12px;
    }

    .sidebar-link i {
        font-size: 1.1rem;
        color: #64748b;
    }

    .sidebar-link:hover {
        background-color: #f1f5f9;
        color: #6d28d9;
    }

    .sidebar-link.active {
        background-color: #6d28d9;
        color: #fff;
    }

    .sidebar-link.active i {
        color: #fff;
    }

    .ls-wide { letter-spacing: 0.5px; }
    .smaller { font-size: 0.85rem; }
</style>
@endsection

@section('content')
<div class="crm-wrapper">
    <aside class="crm-sidebar-new d-none d-lg-block" style="position: sticky; top: 72px; height: calc(100vh - 72px);">
        <span class="nav-menu-label mt-0">DASHBOARD</span>
        <a href="{{ route('admin.crm.dashboard') }}" class="sidebar-link">
            <i class="bi bi-speedometer2"></i> Analitik Ringkas
        </a>
        <a href="{{ route('admin.crm.customers.index') }}" class="sidebar-link">
            <i class="bi bi-people"></i> Data Pelanggan
        </a>

        <span class="nav-menu-label">OPERASIONAL</span>
        <a href="{{ route('admin.crm.feedback.index') }}" class="sidebar-link">
            <i class="bi bi-chat-heart"></i> Analisis Feedback
        </a>
        <a href="{{ route('admin.crm.broadcast.index') }}" class="sidebar-link active">
            <i class="bi bi-megaphone"></i> Blast Broadcast
        </a>

        <span class="nav-menu-label">BANTUAN</span>
        <a href="{{ route('admin.crm.support.index') }}" class="sidebar-link">
            <i class="bi bi-headset"></i> Tiket Support
        </a>
    </aside>

    <main class="crm-main">
        <div class="crm-hero d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <span class="hero-label">Broadcasting Module</span>
                <h1 class="hero-title">Blast Broadcast</h1>
                <p class="hero-subtitle mb-0">Kelola riwayat pengiriman pesan massal dan optimasi strategi jangkauan pengguna.</p>
            </div>
            <div class="mt-4 mt-md-0">
                <a href="{{ route('admin.crm.broadcast.create') }}" class="btn btn-primary shadow-lg border-0 px-4 py-2 rounded-3 fw-600">
                    <i class="bi bi-plus-lg me-1"></i> Buat Blast Baru
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card-minimal overflow-hidden shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8fafc;">
                        <tr style="font-size: 13px; color: #64748b; font-weight: 700;">
                            <th class="ps-4 py-3">TANGGAL</th>
                            <th class="py-3">JUDUL / SUBJEK</th>
                            <th class="py-3">SEGMEN</th>
                            <th class="py-3">PLATFORM</th>
                            <th class="py-3 text-center">TARGET</th>
                            <th class="py-3 text-center">STATUS</th>
                            <th class="py-3 text-center pe-4">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($broadcasts as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-navy small">{{ $item->created_at->format('d M Y') }}</div>
                                <div class="text-muted smaller" style="font-size: 0.7rem;">{{ $item->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td>
                                <div class="fw-bold small text-truncate" style="max-width: 250px;">{{ $item->title }}</div>
                                <div class="text-muted smaller" style="font-size: 0.75rem;">{{ Str::limit($item->message, 50) }}</div>
                            </td>
                            <td>
                                @php
                                    $segments = [
                                        'all' => ['bg-light text-dark', 'Semua'],
                                        'reseller' => ['bg-warning-subtle text-warning', 'Reseller'],
                                        'trainer' => ['bg-info-subtle text-info', 'Trainer'],
                                        'no_event' => ['bg-danger-subtle text-danger', 'Belum Ikut Event']
                                    ];
                                    $seg = $segments[$item->segment] ?? ['bg-secondary-subtle', 'Lainnya'];
                                @endphp
                                <span class="badge rounded-pill {{ $seg[0] }}" style="font-size: 0.7rem;">{{ $seg[1] }}</span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    @if(in_array($item->platform, ['email', 'both']))
                                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10" title="Email"><i class="bi bi-envelope"></i></span>
                                    @endif
                                    @if(in_array($item->platform, ['whatsapp', 'both']))
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10" title="WhatsApp"><i class="bi bi-whatsapp"></i></span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold">{{ $item->target_count }}</div>
                                <div class="smaller text-muted" style="font-size: 0.65rem;">User</div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill" style="background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; font-size: 0.7rem;">
                                    {{ strtoupper($item->status) }}
                                </span>
                            </td>
                            <td class="text-center pe-4">
                                <button class="btn btn-sm btn-light rounded-circle shadow-sm" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Detail -->
                        <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                                    <div class="modal-header border-0 pb-0">
                                        <h5 class="modal-title fw-bold">Detail Broadcast</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <div class="mb-4">
                                            <label class="text-muted smaller fw-bold text-uppercase ls-wide mb-1">Judul / Subjek</label>
                                            <div class="p-3 border rounded-3 bg-light fw-bold">{{ $item->title }}</div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="text-muted smaller fw-bold text-uppercase ls-wide mb-1">Pesan</label>
                                            <div class="p-3 border rounded-3 bg-white" style="white-space: pre-wrap; font-size: 0.9rem;">{{ $item->message }}</div>
                                        </div>
                                        <div class="row g-3">
                                            <div class="col-6">
                                                <div class="p-3 bg-light rounded-4">
                                                    <div class="text-muted smaller fw-bold text-uppercase mb-1">Segmen</div>
                                                    <div class="fw-bold">{{ $seg[1] }}</div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="p-3 bg-light rounded-4">
                                                    <div class="text-muted smaller fw-bold text-uppercase mb-1">Platform</div>
                                                    <div class="fw-bold">{{ ucfirst($item->platform) }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-megaphone text-muted opacity-25" style="font-size: 3rem;"></i>
                                    <p class="text-muted small mt-2">Belum ada riwayat broadcast pengiriman.</p>
                                    <a href="{{ route('admin.crm.broadcast.create') }}" class="btn btn-link btn-sm text-decoration-none">Kirim Broadcast Pertama</a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>
@endsection
