@extends('layouts.admin')

@section('title', 'Kelola Customer')

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

    .filter-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid #eee;
    }
    .customer-list-table thead th {
        background: #f8fafc;
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        border-top: none;
        padding: 1.25rem 1rem;
    }
    .customer-list-table tbody td {
        padding: 1.25rem 1rem;
    }
    .avatar-wrapper img {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .role-badge {
        font-weight: 700;
        font-size: 0.65rem;
        padding: 0.4rem 0.85rem;
        border-radius: 10px;
        text-transform: uppercase;
    }
    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.2s;
        color: #64748b;
    }
    .action-btn:hover {
        background: #f1f5f9;
        color: #6d28d9;
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
        <a href="{{ route('admin.crm.customers.index') }}" class="sidebar-link active">
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
        <a href="{{ route('admin.crm.support.index') }}" class="sidebar-link">
            <i class="bi bi-headset"></i> Tiket Support
        </a>
    </aside>

    <main class="crm-main">
        <div class="crm-hero d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <span class="hero-label">Database Master</span>
                <h1 class="hero-title">Direktori Pelanggan</h1>
                <p class="hero-subtitle mb-0">Kelola informasi profil, status peran, dan pantau statistik keterlibatan pengguna.</p>
            </div>
            <div class="mt-4 mt-md-0">
                <button class="btn btn-white shadow-sm border px-4 py-2 rounded-3 text-navy fw-600">
                    <i class="bi bi-file-earmark-arrow-down me-2"></i> Ekspor CSV
                </button>
            </div>
        </div>

<!-- Search and Filter -->
<div class="filter-card mb-5 overflow-hidden border-0">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.crm.customers.index') }}" class="row g-4 align-items-end">
            <div class="col-lg-5">
                <label class="form-label fw-700 small text-navy opacity-75">Cari Pelanggan</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control bg-light border-0 py-2 fs-6 shadow-none" placeholder="Cari berdasarkan nama, email, nomor hp..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-4">
                <label class="form-label fw-700 small text-navy opacity-75">Saring Berdasarkan Peran</label>
                <select name="role" class="form-select bg-light border-0 py-2 fs-6 shadow-none">
                    <option value="">Semua Peran</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User Umum</option>
                    <option value="reseller" {{ request('role') == 'reseller' ? 'selected' : '' }}>Reseller</option>
                    <option value="trainer" {{ request('role') == 'trainer' ? 'selected' : '' }}>Trainer</option>
                </select>
            </div>
            <div class="col-lg-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 fw-700 py-2 rounded-3">
                        <i class="bi bi-funnel-fill me-2"></i> Filter
                    </button>
                    @if(request('search') || request('role'))
                    <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-light px-3 py-2 rounded-3 shadow-sm border" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Customers List -->
<div class="card-minimal border-0 shadow-sm overflow-hidden">
    <div class="table-responsive">
        <table class="table customer-list-table hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4">Identitas Pelanggan</th>
                    <th>Detail Kontak</th>
                    <th>Role & Status</th>
                    <th class="text-center">Keterlibatan</th>
                    <th>Tanggal Join</th>
                    <th class="text-end pe-4">Opsi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr class="activity-row" onclick="window.location='{{ route('admin.crm.customers.show', $customer) }}'">
                    <td class="ps-4">
                        <div class="d-flex align-items-center">
                            <div class="avatar-wrapper me-3">
                                <img src="{{ $customer->avatar_url }}" alt="avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=6d28d9&color=fff'">
                            </div>
                            <div>
                                <div class="fw-800 text-navy fs-6 mb-0">{{ $customer->name }}</div>
                                <div class="text-muted smaller">ID: #{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-700 text-dark">{{ $customer->email }}</div>
                        <div class="text-muted smaller"><i class="bi bi-phone me-1"></i> {{ $customer->phone ?? '-' }}</div>
                    </td>
                    <td>
                        @if($customer->role === 'reseller')
                            <span class="role-badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">RESELLER</span>
                        @elseif($customer->role === 'trainer')
                            <span class="role-badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">TRAINER</span>
                        @else
                            <span class="role-badge bg-light text-muted border">CUSTOMER</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-4">
                            <div class="text-center">
                                <div class="fw-800 text-navy mb-0">{{ $customer->event_registrations_count ?? 0 }}</div>
                                <div class="smaller text-muted opacity-50 fw-600">Events</div>
                            </div>
                            <div class="text-center">
                                <div class="fw-800 text-navy mb-0">{{ $customer->enrollments_count ?? 0 }}</div>
                                <div class="smaller text-muted opacity-50 fw-600">Courses</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-700 text-navy">{{ $customer->created_at->translatedFormat('d M Y') }}</div>
                        <div class="smaller text-muted">{{ $customer->created_at->diffForHumans() }}</div>
                    </td>
                    <td class="text-end pe-4" onclick="event.stopPropagation()">
                        <div class="dropdown">
                            <button class="btn action-btn border-0 shadow-none mx-auto" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 py-2 mt-2">
                                <li><a class="dropdown-item py-2 fw-600 smaller" href="{{ route('admin.crm.customers.show', $customer) }}"><i class="bi bi-person-badge me-2 text-primary"></i> Detail Lengkap</a></li>
                                <li><a class="dropdown-item py-2 fw-600 smaller" href="{{ route('admin.crm.customers.edit', $customer) }}"><i class="bi bi-pencil-square me-2 text-info"></i> Ubah Data</a></li>
                                <li><hr class="dropdown-divider opacity-50"></li>
                                <li><a class="dropdown-item py-2 fw-600 smaller text-danger" href="#"><i class="bi bi-trash3 me-2"></i> Hapus Akun</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="py-5">
                            <i class="bi bi-people text-muted opacity-10 display-1"></i>
                            <h5 class="text-muted mt-3 fw-700">Tidak ada pelanggan ditemukan</h5>
                            <p class="text-muted small">Coba sesuaikan kata kunci atau filter pencarian Anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="card-footer bg-light bg-opacity-50 border-0 py-4 px-4">
        {{ $customers->appends(request()->query())->links() }}
    </div>
    @endif
</div>
    </main>
</div>
@endsection

