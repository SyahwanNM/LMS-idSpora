@extends('layouts.admin')

@section('title', 'Manajemen Support')

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

    .status-pill {
        font-weight: 700;
        font-size: 0.65rem;
        padding: 0.4rem 0.85rem;
        border-radius: 10px;
        text-transform: uppercase;
    }
    .type-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }
    .attachment-preview {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        object-fit: cover;
        transition: transform 0.2s;
    }
    .support-table thead th {
        background: #f8fafc;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        padding: 1.25rem 1rem;
    }
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
        <a href="{{ route('admin.crm.broadcast.index') }}" class="sidebar-link">
            <i class="bi bi-megaphone"></i> Blast Broadcast
        </a>

        <span class="nav-menu-label">BANTUAN</span>
        <a href="{{ route('admin.crm.support.index') }}" class="sidebar-link active">
            <i class="bi bi-headset"></i> Tiket Support
        </a>
    </aside>

    <main class="crm-main">
        <div class="crm-hero d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <span class="hero-label">Support Helpdesk</span>
                <h1 class="hero-title">Tiket Support</h1>
                <p class="hero-subtitle mb-0">Manajemen masukan, pertanyaan, dan kendala teknis dari seluruh ekosistem IDSPora.</p>
            </div>
            <div class="mt-4 mt-md-0">
                <div class="bg-white bg-opacity-10 border border-white border-opacity-10 p-3 rounded-4 backdrop-blur shadow-sm">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary p-2 rounded-3">
                            <i class="bi bi-headset fs-4"></i>
                        </div>
                        <div>
                            <div class="small opacity-75 fw-600">Total Tiket Aktif</div>
                            <div class="fs-5 fw-800">{{ $messages->total() }} Laporan</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4 py-3" style="border-radius: 15px;" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-3 fs-4"></i>
            <div class="fw-600">{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Filter Box -->
<div class="card-minimal border-0 shadow-sm mb-5">
    <div class="card-body p-4">
        <form action="{{ route('admin.crm.support.index') }}" method="GET" class="row g-4 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-700 small text-navy opacity-75">Saring Jenis Pesan</label>
                <select name="type" class="form-select bg-light border-0 py-2 fs-6 shadow-none" onchange="this.form.submit()">
                    <option value="">Semua Jenis</option>
                    <option value="kendala" {{ request('type') == 'kendala' ? 'selected' : '' }}>🚨 Kendala / Bug</option>
                    <option value="pertanyaan" {{ request('type') == 'pertanyaan' ? 'selected' : '' }}>❓ Pertanyaan</option>
                    <option value="masukan" {{ request('type') == 'masukan' ? 'selected' : '' }}>💡 Masukan</option>
                    <option value="lainnya" {{ request('type') == 'lainnya' ? 'selected' : '' }}>📋 Lainnya</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-700 small text-navy opacity-75">Status Tiket</label>
                <select name="status" class="form-select bg-light border-0 py-2 fs-6 shadow-none" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>🆕 Baru</option>
                    <option value="processed" {{ request('status') == 'processed' ? 'selected' : '' }}>⚙️ Diproses</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>✅ Selesai</option>
                    <option value="ignored" {{ request('status') == 'ignored' ? 'selected' : '' }}>⏭️ Diabaikan</option>
                </select>
            </div>
            <div class="col-md-3">
                <a href="{{ route('admin.crm.support.index') }}" class="btn btn-light w-100 fw-700 py-2 rounded-3 shadow-sm border">
                    <i class="bi bi-arrow-counterclockwise me-2"></i> Reset Filter
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card-minimal border-0 shadow-sm overflow-hidden mb-5">
    <div class="table-responsive">
        <table class="table support-table hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Pengirim</th>
                    <th>Subjek & Jenis</th>
                    <th>Cuplikan Pesan</th>
                    <th class="text-center">Lampiran</th>
                    <th>Status</th>
                    <th class="text-end pe-4">Opsi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $msg)
                    <tr class="activity-row" data-bs-toggle="modal" data-bs-target="#modalMsg{{ $msg->id }}">
                        <td class="ps-4">
                            <div class="fw-800 text-navy fs-6 mb-0">{{ $msg->name }}</div>
                            <div class="text-muted smaller">{{ $msg->email }}</div>
                            <div class="smaller text-primary mt-1 fw-600">{{ $msg->created_at->translatedFormat('d M Y, H:i') }}</div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center mb-1">
                                <span class="type-indicator 
                                    {{ $msg->type == 'kendala' ? 'bg-danger' : 
                                       ($msg->type == 'pertanyaan' ? 'bg-info' : 
                                       ($msg->type == 'masukan' ? 'bg-success' : 'bg-secondary')) }}">
                                </span>
                                <span class="fw-700 smaller text-uppercase letter-spacing-1 opacity-75">{{ $msg->type }}</span>
                            </div>
                            <div class="fw-700 text-dark small text-truncate" style="max-width: 200px;">{{ $msg->subject }}</div>
                        </td>
                        <td>
                            <div class="text-muted smaller lh-sm" style="max-width: 280px; white-space: normal;">
                                {{ Str::limit($msg->message, 85) }}
                            </div>
                        </td>
                        <td class="text-center">
                            @if($msg->attachment)
                                <a href="{{ asset('uploads/' . $msg->attachment) }}" target="_blank" onclick="event.stopPropagation()">
                                    <img src="{{ asset('uploads/' . $msg->attachment) }}" class="attachment-preview border shadow-sm">
                                </a>
                            @else
                                <span class="text-muted smaller fw-600 opacity-50"><i class="bi bi-dash"></i></span>
                            @endif
                        </td>
                        <td>
                            @php
                                $statusStyles = [
                                    'new' => ['bg' => 'bg-primary text-white', 'label' => 'BARU'],
                                    'processed' => ['bg' => 'bg-warning bg-opacity-10 text-warning border-warning border-opacity-25', 'label' => 'DIPROSES'],
                                    'resolved' => ['bg' => 'bg-success bg-opacity-10 text-success border-success border-opacity-25', 'label' => 'SELESAI'],
                                    'ignored' => ['bg' => 'bg-light text-muted border', 'label' => 'DIABAIKAN']
                                ][$msg->status] ?? ['bg' => 'bg-light text-muted', 'label' => $msg->status];
                            @endphp
                            <span class="status-pill {{ $statusStyles['bg'] }} border">{{ $statusStyles['label'] }}</span>
                        </td>
                        <td class="text-end pe-4" onclick="event.stopPropagation()">
                            <div class="dropdown">
                                <button class="btn action-btn border-0 shadow-none ms-auto" data-bs-toggle="dropdown">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2 mt-2">
                                    <li><h6 class="dropdown-header text-muted small fw-800">Ubah Status Tiket</h6></li>
                                    <li>
                                        <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="processed">
                                            <button type="submit" class="dropdown-item py-2 fw-600 smaller"><i class="bi bi-arrow-repeat me-2 text-warning"></i> Tandai Diproses</button>
                                        </form>
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="resolved">
                                            <button type="submit" class="dropdown-item py-2 fw-600 smaller"><i class="bi bi-check-circle me-2 text-success"></i> Tandai Selesai</button>
                                        </form>
                                    </li>
                                    <li><hr class="dropdown-divider opacity-50"></li>
                                    <li>
                                        <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="status" value="ignored">
                                            <button type="submit" class="dropdown-item py-2 fw-600 smaller text-danger"><i class="bi bi-x-circle me-2"></i> Abaikan Tiket</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>


                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <div class="py-5">
                                <i class="bi bi-chat-left-dots text-muted opacity-10 display-1"></i>
                                <h5 class="text-muted mt-3 fw-700">Tidak ada tiket support ditemukan</h5>
                                <p class="text-muted small">Semua tenang, belum ada laporan kendala baru.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($messages->hasPages())
    <div class="card-footer bg-light bg-opacity-50 border-0 py-4 px-4">
        {{ $messages->links() }}
    </div>
    @endif
</div>

<!-- Modals moved outside the table for stability -->
@foreach($messages as $msg)
    @php
        $statusStyles = [
            'new' => ['bg' => 'bg-primary text-white', 'label' => 'BARU'],
            'processed' => ['bg' => 'bg-warning bg-opacity-10 text-warning border-warning border-opacity-25', 'label' => 'DIPROSES'],
            'resolved' => ['bg' => 'bg-success bg-opacity-10 text-success border-success border-opacity-25', 'label' => 'SELESAI'],
            'ignored' => ['bg' => 'bg-light text-muted border', 'label' => 'DIABAIKAN']
        ][$msg->status] ?? ['bg' => 'bg-light text-muted', 'label' => $msg->status];
    @endphp
    <div class="modal fade" id="modalMsg{{ $msg->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg overflow-hidden" style="border-radius: 24px; color: var(--crm-navy);">
                <div class="modal-header bg-navy text-white border-0 p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                            <i class="bi bi-chat-dots-fill fs-4"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-800 mb-0">Detail Support Ticket</h5>
                            <span class="smaller opacity-75">ID Tiket: #SPT-{{ $msg->id }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 bg-light bg-opacity-50">
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                                <label class="text-muted smaller fw-800 text-uppercase mb-2 d-block opacity-50">Pengirim Pesan</label>
                                <div class="d-flex align-items-center">
                                    <div class="bg-primary bg-opacity-10 text-primary fw-800 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; border-radius: 10px;">
                                        {{ strtoupper(substr($msg->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-800 text-navy mb-0">{{ $msg->name }}</div>
                                        <div class="text-muted smaller">{{ $msg->email }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="bg-white p-4 rounded-4 shadow-sm h-100">
                                <label class="text-muted smaller fw-800 text-uppercase mb-2 d-block opacity-50">Jenis & Status</label>
                                <div class="d-flex flex-column gap-2">
                                    <div>
                                        <span class="badge bg-soft-primary text-primary fw-700 px-3 rounded-pill">{{ strtoupper($msg->type) }}</span>
                                        <span class="status-pill {{ $statusStyles['bg'] }} ms-2 border">{{ $statusStyles['label'] }}</span>
                                    </div>
                                    <div class="smaller text-muted fw-600"><i class="bi bi-clock me-1"></i> {{ $msg->created_at->translatedFormat('d F Y, H:i') }} WIB</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white p-4 rounded-4 shadow-sm mb-4">
                        <label class="text-muted smaller fw-800 text-uppercase mb-3 d-block opacity-50">Subjek & Isi Pesan</label>
                        <h5 class="fw-800 text-navy mb-3">{{ $msg->subject }}</h5>
                        <div class="lh-lg text-dark fw-500" style="white-space: pre-wrap; font-size: 1rem;">{{ $msg->message }}</div>
                    </div>

                    @if($msg->attachment)
                    <div class="bg-white p-4 rounded-4 shadow-sm">
                        <label class="text-muted smaller fw-800 text-uppercase mb-3 d-block opacity-50">Lampiran Bukti / Gambar</label>
                        <a href="{{ asset('uploads/' . $msg->attachment) }}" target="_blank" class="d-block overflow-hidden rounded-4 border">
                            <img src="{{ asset('uploads/' . $msg->attachment) }}" class="img-fluid w-100 hover-zoom">
                        </a>
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-0 p-4 bg-white">
                    <div class="w-100">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="d-flex flex-wrap gap-2">
                                @if($msg->status !== 'processed' && $msg->status !== 'resolved')
                                <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="status" value="processed">
                                    <button type="submit" class="btn btn-warning text-white px-3 rounded-pill fw-700 smaller shadow-sm">
                                        <i class="bi bi-arrow-repeat me-1"></i> Proses Tiket
                                    </button>
                                </form>
                                @endif

                                @if($msg->status !== 'resolved')
                                <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="status" value="resolved">
                                    <button type="submit" class="btn btn-success px-3 rounded-pill fw-700 smaller shadow-sm">
                                        <i class="bi bi-check-circle me-1"></i> Selesaikan
                                    </button>
                                </form>
                                @endif

                                @if($msg->status === 'new')
                                <form action="{{ route('admin.crm.support.updateStatus', $msg) }}" method="POST" class="m-0">
                                    @csrf
                                    <input type="hidden" name="status" value="ignored">
                                    <button type="submit" class="btn btn-outline-danger px-3 rounded-pill fw-700 smaller">
                                        <i class="bi bi-x-circle me-1"></i> Abaikan
                                    </button>
                                </form>
                                @endif
                            </div>
                            <button type="button" class="btn btn-navy px-4 rounded-pill fw-700 shadow-sm" data-bs-dismiss="modal">Tutup Detail</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach
@endsection

