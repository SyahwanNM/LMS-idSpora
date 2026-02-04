@extends('layouts.crm')

@section('title', 'CRM Dashboard')

@section('styles')
<style>
    .kpi-card {
        padding: 1.5rem;
        height: 100%;
    }
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .kpi-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--crm-text-muted);
        margin-bottom: 0.25rem;
    }
    .kpi-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--crm-navy);
    }
    .table-responsive {
        border-radius: 12px;
    }
    .customer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        object-fit: cover;
    }
    .top-customer-item {
        padding: 1rem 0;
        border-bottom: 1px solid var(--crm-border);
    }
    .top-customer-item:last-child {
        border-bottom: none;
    }
    .trend-indicator {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
    }
</style>
@endsection

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h3 class="fw-bold text-navy mb-1">CRM Overview</h3>
        <p class="text-muted small mb-0">Manajemen hubungan pelanggan dan analitik operasional</p>
    </div>
    <div class="col-auto">
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm bg-white">
                <i class="bi bi-download me-1"></i> Laporan
            </button>
            <button class="btn btn-sm px-3" style="background: var(--crm-primary); color: white;">
                <i class="bi bi-plus-lg me-1"></i> Tambah Data
            </button>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <span>{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Strategic Statistics -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card-minimal kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background: var(--crm-accent-light); color: var(--crm-primary);">
                    <i class="bi bi-people"></i>
                </div>
                <span class="trend-indicator" style="background: #ecfdf5; color: #10b981;">
                    <i class="bi bi-arrow-up"></i> 12%
                </span>
            </div>
            <div class="kpi-title">TOTAL CUSTOMER</div>
            <div class="kpi-value">{{ number_format($totalCustomers) }}</div>
            <div class="mt-2 small text-muted">
                <span class="text-dark fw-medium">{{ number_format($activeCustomersCount) }}</span> aktif berinteraksi
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card-minimal kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background: #fffbeb; color: var(--crm-secondary);">
                    <i class="bi bi-calendar2-check"></i>
                </div>
            </div>
            <div class="kpi-title">REGISTRASI EVENT</div>
            <div class="kpi-value">{{ number_format($totalRegistrations) }}</div>
            <div class="mt-2 small text-muted">
                Peserta aktif di semua event
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card-minimal kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background: var(--crm-accent-light); color: var(--crm-primary);">
                    <i class="bi bi-journal-bookmark"></i>
                </div>
            </div>
            <div class="kpi-title">ENROLLMENT COURSE</div>
            <div class="kpi-value">{{ number_format($totalEnrollments) }}</div>
            <div class="mt-2 small text-muted">
                Total pembelajaran aktif
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card-minimal kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="kpi-icon" style="background: #fffbeb; color: var(--crm-secondary);">
                    <i class="bi bi-chat-dots"></i>
                </div>
                @if($newSupportMessages > 0)
                <span class="badge rounded-pill shadow-sm" style="background: var(--crm-primary); color: white;">{{ $newSupportMessages }} Baru</span>
                @endif
            </div>
            <div class="kpi-title">SUPPORT TICKET</div>
            <div class="kpi-value">{{ number_format($newSupportMessages) }}</div>
            <div class="mt-2 small text-muted">
                Pesan bantuan yang perlu dibalas
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Activity Section -->
    <div class="col-lg-8">
        <div class="card-minimal mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Registrasi Terbaru</h5>
                <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-link btn-sm text-decoration-none p-0">Lihat Semua</a>
            </div>
            <div class="card-body px-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light bg-opacity-50">
                            <tr>
                                <th class="ps-4">Pelanggan</th>
                                <th>Event / Program</th>
                                <th>Tanggal</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRegistrations as $registration)
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $registration->user->avatar_url }}" class="customer-avatar me-3 border" alt="user">
                                        <div>
                                            <div class="fw-bold text-navy small">{{ $registration->user->name }}</div>
                                            <div class="text-muted smaller" style="font-size: 0.7rem;">{{ $registration->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fw-semibold small text-truncate" style="max-width: 200px;">{{ $registration->event?->title ?? 'N/A' }}</div>
                                    <div class="text-muted smaller" style="font-size: 0.7rem;">IDSPora Program</div>
                                </td>
                                <td>
                                    <div class="small fw-medium">{{ $registration->created_at->format('d M') }}</div>
                                    <div class="text-muted smaller" style="font-size: 0.7rem;">{{ $registration->created_at->format('H:i') }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-1 px-2 rounded-2" style="font-size: 0.65rem;">
                                        AKTIF
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" style="width: 48px; opacity: 0.3;" alt="empty">
                                    <p class="text-muted small mt-2">Belum ada registrasi baru</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="card-minimal p-4">
                    <h6 class="fw-bold mb-3">Breakdown Peran User</h6>
                    <div class="d-flex flex-column gap-3">
                        <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person text-primary me-2"></i>
                                <span class="small fw-medium">Customer Umum</span>
                            </div>
                            <span class="fw-bold">{{ $totalCustomers }}</span>
                        </div>
                        <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-box-seam text-warning me-2"></i>
                                <span class="small fw-medium">Reseller Affiliate</span>
                            </div>
                            <span class="fw-bold">{{ $totalResellers }}</span>
                        </div>
                        <div class="p-3 bg-light rounded-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-person-workspace text-info me-2"></i>
                                <span class="small fw-medium">Trainer / Pemateri</span>
                            </div>
                            <span class="fw-bold">{{ $totalTrainers }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card-minimal p-4 h-100">
                    <h6 class="fw-bold mb-3">Event Paling Populer</h6>
                    @forelse($topEvents as $event)
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="text-truncate me-2" style="max-width: 180px;">
                            <div class="small fw-bold">{{ $event->title }}</div>
                            <div class="text-muted smaller" style="font-size: 0.7rem;">{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d M') : 'Online' }}</div>
                        </div>
                        <span class="badge bg-primary rounded-pill smaller">{{ $event->registrations_count }} Peserta</span>
                    </div>
                    @empty
                    <p class="text-muted small">Belum ada data event.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Side Content / Analytics -->
    <div class="col-lg-4">
        <div class="card-minimal h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">Pelanggan Teraktif</h5>
                <p class="text-muted smaller mb-0">Berdasarkan total aktivitas program</p>
            </div>
            <div class="card-body px-4">
                @forelse($topCustomers as $customer)
                <div class="top-customer-item">
                    <div class="d-flex align-items-center">
                        <img src="{{ $customer->avatar_url }}" class="customer-avatar me-3 border" style="width: 45px; height: 45px; border-radius: 50%;">
                        <div class="flex-grow-1">
                            <div class="fw-bold text-navy small">{{ $customer->name }}</div>
                            <div class="text-muted smaller d-flex gap-2">
                                <span><i class="bi bi-calendar-event me-1"></i> {{ $customer->event_registrations_count }}</span>
                                <span><i class="bi bi-journal-bookmark me-1"></i> {{ $customer->enrollments_count }}</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('admin.crm.customers.show', $customer->id) }}" class="btn btn-outline-primary btn-sm p-1 rounded-circle" style="width: 28px; height: 28px; line-height: 1;">
                                <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-center text-muted py-4">Belum ada data aktivitas</p>
                @endforelse
                
                <div class="mt-4 p-4 rounded-4 text-center" style="background: var(--crm-accent-light); border: 1px solid rgba(109, 40, 217, 0.1);">
                    <div class="avatar-circle mx-auto mb-3 bg-white d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px; border-color: var(--crm-secondary);">
                        <i class="bi bi-stars" style="color: var(--crm-primary); font-size: 1.75rem;"></i>
                    </div>
                    <h6 class="fw-bold mb-2" style="color: var(--crm-primary);">Tips Pengelolaan</h6>
                    <p class="smaller text-muted mb-3">Gunakan filter pada halaman Pelanggan untuk mengekspor data berdasarkan role reseller.</p>
                    <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-sm w-100 rounded-pill" style="background: var(--crm-primary); color: white;">Kelola Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
