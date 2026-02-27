@extends('layouts.admin')

@section('title', 'CRM Dashboard')

@section('navbar')
    @include('partials.navbar-crm')
@endsection

@section('styles')
<style>
    /* Glassmorphism Header */
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

    .crm-hero::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(109, 40, 217, 0.15) 0%, rgba(109, 40, 217, 0) 70%);
        border-radius: 50%;
        z-index: 1;
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
        color: #a78bfa;
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

    .sidebar-link:hover i {
        color: #6d28d9;
    }

    .sidebar-link.active {
        background-color: #6d28d9;
        color: #fff;
    }

    .sidebar-link.active i {
        color: #fff;
    }

    .kpi-card {
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        transition: transform 0.3s ease;
    }
    .kpi-card:hover { border-color: #6d28d9; }
</style>
@endsection

@section('content')
<div class="crm-wrapper">
    <aside class="crm-sidebar-new d-none d-lg-block" style="position: sticky; top: 72px; height: calc(100vh - 72px);">
        <span class="nav-menu-label mt-0">DASHBOARD</span>
        <a href="{{ route('admin.crm.dashboard') }}" class="sidebar-link active">
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
        <a href="{{ route('admin.crm.support.index') }}" class="sidebar-link">
            <i class="bi bi-headset"></i> Tiket Support
        </a>
    </aside>

    <main class="crm-main">
        <!-- Premium Hero Header -->
        <div class="crm-hero d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="z-2">
                <span class="hero-label">Relations Center</span>
                <h1 class="hero-title">CRM Dashboard</h1>
                <p class="hero-subtitle">Pantau performa ekosistem IDSPora, kelola interaksi pelanggan, hingga optimasi feedback dalam satu kendali terpadu.</p>
            </div>
            <div class="z-2 mt-4 mt-md-0">
                <div class="calendar-badge shadow-lg">
                    <div class="calendar-icon">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                    <div class="calendar-text">
                        <span class="date-label">Laporan Per Hari Ini</span>
                        <span class="date-value">{{ now()->format('l, d F Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4 gap-2">
            <button class="btn btn-white shadow-sm border px-4 py-2 rounded-3 text-navy fw-600">
                <i class="bi bi-download me-2"></i> Ekspor Data
            </button>
            <a href="{{ route('admin.crm.broadcast.create') }}" class="btn btn-primary shadow-lg border-0 px-4 py-2 rounded-3 fw-600">
                <i class="bi bi-megaphone-fill me-2"></i> Kirim Broadcast
            </a>
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

<!-- Strategic Statistics -->
<div class="row g-4 mb-5">
    <div class="col-md-6 col-xl">
        <div class="card-minimal kpi-card" style="--bg-accent: var(--crm-primary);">
            <div class="kpi-icon" style="background: rgba(109, 40, 217, 0.1); color: var(--crm-primary);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="kpi-title">Total Customer</div>
            <div class="kpi-value mb-2">{{ number_format($totalCustomers) }}</div>
            <div class="small d-flex align-items-center gap-2">
                <span class="badge-soft" style="background: rgba(109, 40, 217, 0.1); color: var(--crm-primary);">
                    {{ number_format($activeCustomersCount) }} Aktif
                </span>
                <span class="text-muted smaller">Bulan ini</span>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl">
        <div class="card-minimal kpi-card" style="--bg-accent: #f59e0b;">
            <div class="kpi-icon" style="background: rgba(245, 158, 11, 0.1); color: #d97706;">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
            <div class="kpi-title">Registrasi Event</div>
            <div class="kpi-value mb-2">{{ number_format($totalRegistrations) }}</div>
            <div class="small text-muted">Aktivitas pendaftaran</div>
        </div>
    </div>
    <div class="col-md-6 col-xl">
        <div class="card-minimal kpi-card" style="--bg-accent: #10b981;">
            <div class="kpi-icon" style="background: rgba(16, 185, 129, 0.1); color: #059669;">
                <i class="bi bi-book-half"></i>
            </div>
            <div class="kpi-title">Course Enrollment</div>
            <div class="kpi-value mb-2">{{ number_format($totalEnrollments) }}</div>
            <div class="small text-muted">Modul dipelajari</div>
        </div>
    </div>
    <div class="col-md-6 col-xl">
        <div class="card-minimal kpi-card" style="--bg-accent: #ec4899;">
            <div class="kpi-icon" style="background: rgba(236, 72, 153, 0.1); color: #db2777;">
                <i class="bi bi-chat-heart-fill"></i>
            </div>
            <div class="kpi-title">Support Ticket</div>
            <div class="kpi-value mb-2">{{ number_format($newSupportMessages) }}</div>
            <div class="mt-1">
                <a href="{{ route('admin.crm.support.index') }}" class="text-decoration-none fw-700 smaller" style="color: #db2777;">
                    Update Status <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Activity Section -->
    <div class="col-lg-8">
        <div class="card-minimal mb-5 border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-800 text-navy mb-0">Registrasi Terbaru</h5>
                    <p class="text-muted smaller mb-0">Pantau pendaftar program IDSPora secara real-time</p>
                </div>
                <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-light btn-sm rounded-3 px-3 fw-600">Lihat Semua</a>
            </div>
            <div class="card-body px-0 pb-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr class="text-muted small">
                                <th class="ps-4 py-3 fw-600">CUSTOMER</th>
                                <th class="py-3 fw-600">PROGRAM / EVENT</th>
                                <th class="py-3 fw-600">WAKTU</th>
                                <th class="pe-4 text-end fw-600">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRegistrations as $registration)
                            <tr class="activity-row border-bottom" onclick="window.location='{{ route('admin.crm.customers.show', $registration->user->id) }}'">
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $registration->user->avatar_url }}" class="customer-avatar me-3 border" alt="user" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($registration->user->name) }}&background=6d28d9&color=fff'">
                                        <div>
                                            <div class="fw-700 text-navy fs-6">{{ $registration->user->name }}</div>
                                            <div class="text-muted smaller">{{ $registration->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-600 text-dark small text-truncate" style="max-width: 250px;">{{ $registration->event?->title ?? 'Program N/A' }}</div>
                                    <span class="badge bg-soft-primary text-primary smaller" style="font-size: 0.65rem;">IDSPORA EVENT</span>
                                </td>
                                <td class="py-3">
                                    <div class="small fw-700 text-navy">{{ $registration->created_at->translatedFormat('d M') }}</div>
                                    <div class="text-muted smaller">{{ $registration->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-icon btn-light rounded-circle btn-sm">
                                        <i class="bi bi-chevron-right"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="py-4">
                                        <i class="bi bi-inbox text-muted display-4"></i>
                                        <p class="text-muted mt-3">Belum ada registrasi baru yang tercatat.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div class="card-minimal p-4 border-0 shadow-sm h-100">
                    <h6 class="fw-800 text-navy mb-4">Segmentasi Peran</h6>
                    <div class="space-y-4">
                        @php 
                            $maxVal = max($totalCustomers, $totalResellers, $totalTrainers, 1);
                        @endphp
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-600 small text-muted">Customer Umum</span>
                                <span class="fw-700 small">{{ $totalCustomers }}</span>
                            </div>
                            <div class="stat-bar">
                                <div class="stat-bar-fill bg-primary" style="width: {{ ($totalCustomers/$maxVal)*100 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-600 small text-muted">Reseller Affiliate</span>
                                <span class="fw-700 small">{{ $totalResellers }}</span>
                            </div>
                            <div class="stat-bar">
                                <div class="stat-bar-fill bg-warning" style="width: {{ ($totalResellers/$maxVal)*100 }}%"></div>
                            </div>
                        </div>
                        <div class="mb-0">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-600 small text-muted">Trainer / Pemateri</span>
                                <span class="fw-700 small">{{ $totalTrainers }}</span>
                            </div>
                            <div class="stat-bar">
                                <div class="stat-bar-fill bg-info" style="width: {{ ($totalTrainers/$maxVal)*100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-minimal p-4 border-0 shadow-sm h-100">
                    <h6 class="fw-800 text-navy mb-4">Event Terpopuler</h6>
                    @forelse($topEvents as $event)
                    <div class="d-flex align-items-center mb-4 last-mb-0">
                        <div class="bg-light rounded-3 p-2 me-3">
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-700 text-navy small text-truncate">{{ $event->title }}</div>
                            <div class="text-muted smaller">{{ $event->registrations_count }} Pendaftar Terverifikasi</div>
                        </div>
                        <div class="text-primary fw-800">{{ round(($event->registrations_count / max($totalRegistrations, 1)) * 100) }}%</div>
                    </div>
                    @empty
                    <p class="text-muted small">Belum ada data event populer.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Side Content / Analytics -->
    <div class="col-lg-4">
        <div class="card-minimal border-0 shadow-sm h-100 sticky-top" style="top: 100px; z-index: 10;">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h5 class="fw-800 text-navy mb-0">Engagement Rank</h5>
                <p class="text-muted smaller mb-0">Customer dengan interaksi tertinggi hari ini</p>
            </div>
            <div class="card-body px-4">
                <div class="my-4">
                    @forelse($topCustomers as $index => $customer)
                    <div class="d-flex align-items-center mb-4">
                        <div class="position-relative me-3">
                            <img src="{{ $customer->avatar_url }}" class="rounded-circle border border-2 border-white shadow-sm" style="width: 48px; height: 48px; object-fit: cover;" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=f1f5f9&color=6d28d9'">
                            <span class="position-absolute bottom-0 end-0 badge rounded-pill bg-primary border border-2 border-white fw-800" style="font-size: 0.6rem;">#{{ $index + 1 }}</span>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-700 text-navy small text-truncate">{{ $customer->name }}</div>
                            <div class="d-flex gap-3 mt-1">
                                <span class="smaller text-muted"><i class="bi bi-calendar-event me-1"></i> {{ $customer->event_registrations_count }} Event</span>
                                <span class="smaller text-muted"><i class="bi bi-journal-bookmark me-1"></i> {{ $customer->enrollments_count }} Course</span>
                            </div>
                        </div>
                        <a href="{{ route('admin.crm.customers.show', $customer->id) }}" class="btn btn-icon btn-soft-primary rounded-circle btn-sm">
                            <i class="bi bi-person-fill"></i>
                        </a>
                    </div>
                    @empty
                    <p class="text-center text-muted py-5">Belum ada data aktivitas tersedia</p>
                    @endforelse
                </div>
                
                <div class="quick-action-card mt-5">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                            <i class="bi bi-lightbulb text-white fs-4"></i>
                        </div>
                        <h6 class="fw-800 mb-0">Insights & Tips</h6>
                    </div>
                    <p class="smaller mb-4 opacity-75">Tingkatkan konversi dengan mengirimkan kupon promosi khusus untuk segmen yang belum mendaftar event apapun.</p>
                    <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-white w-100 rounded-pill fw-700 shadow-sm">Buka Data Pelanggan</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

