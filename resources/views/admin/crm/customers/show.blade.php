@extends('layouts.crm')

@section('title', 'Detail Customer')

@section('styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--crm-primary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before { content: ''; display: inline-block; width: 16px; height: 2px; background: var(--crm-primary); border-radius: 2px; }

    .profile-banner {
        background: linear-gradient(135deg, var(--crm-navy) 0%, #1e1b4b 100%);
        height: 120px; border-radius: 20px 20px 0 0; position: relative;
    }
    .profile-info-card {
        background: #fff; border-radius: 0 0 20px 20px;
        padding: 0 2rem 2rem; border: 1px solid var(--crm-border-soft);
        border-top: none; position: relative; margin-top: -40px;
    }
    .avatar-wrapper {
        position: relative; margin-top: -60px; display: inline-block;
    }
    .profile-avatar-big {
        width: 120px; height: 120px; border-radius: 24px;
        border: 5px solid #fff; object-fit: cover;
        box-shadow: var(--crm-shadow-lg); background: #fff;
    }
    
    .stat-box {
        padding: 1rem; background: var(--crm-border-soft); border-radius: 12px;
        text-align: center; border: 1px solid transparent; transition: all 0.2s;
    }
    .stat-box:hover { border-color: var(--crm-primary-light); background: #fff; transform: translateY(-2px); box-shadow: var(--crm-shadow-sm); }
    .stat-value { font-size: 1.25rem; font-weight: 800; color: var(--crm-navy); display: block; line-height: 1.2; }
    .stat-label { font-size: 0.65rem; font-weight: 700; color: var(--crm-text-subtle); text-transform: uppercase; letter-spacing: 0.5px; }

    .info-group { margin-bottom: 1.25rem; }
    .info-group:last-child { margin-bottom: 0; }
    .info-label { font-size: 0.68rem; font-weight: 700; color: var(--crm-text-subtle); text-transform: uppercase; letter-spacing: 0.5px; display: block; margin-bottom: 4px; }
    .info-value { font-size: 0.88rem; font-weight: 600; color: var(--crm-navy); display: flex; align-items: center; gap: 8px; }

    .tab-switcher { display: flex; gap: 4px; background: var(--crm-border-soft); border-radius: 10px; padding: 4px; width: fit-content; }
    .tab-switcher button {
        font-size: 0.78rem; font-weight: 600; padding: 8px 20px;
        border-radius: 8px; border: none; background: transparent; color: var(--crm-text-muted);
        transition: all 0.2s;
    }
    .tab-switcher button.active { background: #fff; color: var(--crm-primary); box-shadow: 0 1px 3px rgba(0,0,0,0.08); }

    .activity-card {
        border: 1px solid var(--crm-border-soft); border-radius: 16px; padding: 1.25rem;
        transition: all 0.2s; background: #fff;
    }
    .activity-card:hover { border-color: var(--crm-primary-light); box-shadow: var(--crm-shadow-sm); }
</style>
@endsection

@section('content')
{{-- Page Header --}}
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size:0.75rem;margin-bottom:8px;">
                <li class="breadcrumb-item"><a href="{{ route('admin.crm.dashboard') }}" style="color:var(--crm-primary);text-decoration:none;font-weight:600;">CRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.crm.customers.index') }}" style="color:var(--crm-text-subtle);text-decoration:none;">Customers</a></li>
                <li class="breadcrumb-item active" style="color:var(--crm-navy);font-weight:700;">Detail</li>
            </ol>
        </nav>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--crm-navy);letter-spacing:-0.8px;margin:0;">Manajemen Profil Customer</h1>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-sm px-3 fw-600" style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:8px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <a href="{{ route('admin.crm.customers.edit', $customer) }}" class="btn btn-sm px-3 fw-700" style="background:var(--crm-primary);color:#fff;border-radius:8px;box-shadow:0 4px 10px rgba(124,58,237,0.2);">
            <i class="bi bi-pencil-square me-1"></i> Edit Profil
        </a>
        <button type="button" class="btn btn-sm px-3 fw-700 hover-scale"
                style="background:rgba(239,68,68,0.08);color:#ef4444;border:1px solid rgba(239,68,68,0.2);border-radius:8px;"
                data-customer-id="{{ $customer->id }}"
                data-customer-name="{{ $customer->name }}"
                data-customer-email="{{ $customer->email }}"
                data-customer-role="{{ ucfirst($customer->role) }}"
                data-delete-url="{{ route('admin.crm.customers.destroy', $customer) }}"
                onclick="openDeleteModalShow(this)">
            <i class="bi bi-trash3 me-1"></i> Hapus Akun
        </button>
    </div>
</div>

{{-- Profile Header Card --}}
<div class="mb-5">
    <div class="profile-banner"></div>
    <div class="profile-info-card">
        <div class="row align-items-end">
            <div class="col-md-auto">
                <div class="avatar-wrapper">
                    <img src="{{ $customer->avatar_url }}" class="profile-avatar-big" alt="avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=6d28d9&color=fff&size=128'">
                </div>
            </div>
            <div class="col-md mt-3 mt-md-0 ps-md-4">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                    <h2 style="font-size:1.4rem;font-weight:800;color:var(--crm-navy);margin:0;">{{ $customer->name }}</h2>
                    @if($customer->role === 'reseller')
                        <span class="badge" style="background:rgba(245,158,11,0.1);color:#d97706;font-weight:700;font-size:0.65rem;padding:4px 10px;border-radius:100px;">RESELLER</span>
                    @elseif($customer->role === 'trainer')
                        <span class="badge" style="background:rgba(6,182,212,0.1);color:#0891b2;font-weight:700;font-size:0.65rem;padding:4px 10px;border-radius:100px;">TRAINER</span>
                    @else
                        <span class="badge" style="background:rgba(124,58,237,0.1);color:var(--crm-primary);font-weight:700;font-size:0.65rem;padding:4px 10px;border-radius:100px;">CUSTOMER</span>
                    @endif
                </div>
                <div style="font-size:0.85rem;color:var(--crm-text-subtle);display:flex;align-items:center;gap:15px;flex-wrap:wrap;">
                    <span><i class="bi bi-envelope me-1"></i> {{ $customer->email }}</span>
                    <span><i class="bi bi-calendar-check me-1"></i> Terdaftar: {{ $customer->created_at->translatedFormat('d M Y') }}</span>
                </div>
            </div>
            <div class="col-md-auto mt-4 mt-lg-0">
                <div class="d-flex gap-3">
                    <div class="stat-box">
                        <span class="stat-value">{{ $registrations->count() }}</span>
                        <span class="stat-label">Events</span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-value">{{ $enrollments->count() }}</span>
                        <span class="stat-label">Courses</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Left Sidebar: Info --}}
    <div class="col-lg-4">
        <div class="card-minimal p-4 mb-4">
            <h6 class="fw-800 text-navy mb-4" style="font-size:0.95rem;">Informasi Kontak</h6>
            
            <div class="info-group">
                <span class="info-label">Nomor Telepon</span>
                <div class="info-value">
                    <i class="bi bi-phone text-primary"></i>
                    {{ $customer->phone ?? 'Belum ditambahkan' }}
                </div>
            </div>

            <div class="info-group">
                <span class="info-label">Website / Portfolio</span>
                <div class="info-value">
                    <i class="bi bi-globe2 text-primary"></i>
                    @if($customer->website)
                        <a href="{{ $customer->website }}" target="_blank" style="color:var(--crm-primary);text-decoration:none;">
                            {{ Str::limit(str_replace(['http://', 'https://'], '', $customer->website), 25) }}
                        </a>
                    @else
                        <span class="text-muted">Tidak tersedia</span>
                    @endif
                </div>
            </div>

            <hr style="border-color:var(--crm-border-soft);margin:1.5rem 0;">

            <h6 class="fw-800 text-navy mb-3" style="font-size:0.85rem;">Biografi</h6>
            <p style="font-size:0.82rem;color:var(--crm-text-subtle);line-height:1.6;margin:0;">
                {{ $customer->bio ?? 'Customer ini belum menuliskan biografi singkat mereka. Informasi profil lengkap memudahkan interaksi komunitas.' }}
            </p>
        </div>

        <div class="card-minimal p-4" style="background:var(--crm-border-soft);border:none;">
            <div class="d-flex align-items-center gap-3">
                <div style="width:40px;height:40px;border-radius:10px;background:#fff;color:var(--crm-primary);display:flex;align-items:center;justify-content:center;font-size:1.2rem;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <div style="font-size:0.75rem;font-weight:700;color:var(--crm-navy);">Account Status</div>
                    <div style="font-size:0.7rem;color:var(--crm-text-muted);">Verified Member</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right Content: Activity --}}
    <div class="col-lg-8">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h6 class="fw-800 text-navy mb-0" style="font-size:1.1rem;letter-spacing:-0.5px;">Riwayat Aktivitas</h6>
            <div class="tab-switcher" id="activityTabs">
                <button class="active" onclick="showSection('events', this)">Events</button>
                <button onclick="showSection('courses', this)">Courses</button>
            </div>
        </div>

        {{-- Events Section --}}
        <div id="section-events" class="activity-section">
            <div class="row g-3">
                @forelse($registrations as $reg)
                <div class="col-12">
                    <div class="activity-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <div style="font-size:0.95rem;font-weight:800;color:var(--crm-navy);letter-spacing:-0.3px;">{{ $reg->event?->title ?? 'Program N/A' }}</div>
                                <div style="font-size:0.75rem;color:var(--crm-text-subtle);"><i class="bi bi-calendar3 me-1"></i> {{ $reg->event?->event_date ? \Carbon\Carbon::parse($reg->event->event_date)->translatedFormat('d M Y') : 'Online' }}</div>
                            </div>
                            <div class="text-end">
                                <div style="font-size:0.65rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;">Registered</div>
                                <div style="font-size:0.8rem;font-weight:700;color:var(--crm-navy);">{{ $reg->created_at->format('d M Y') }}</div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $statusColor = $reg->status === 'active' ? '#059669' : '#6b7280';
                                $statusBg = $reg->status === 'active' ? 'rgba(16,185,129,0.1)' : 'rgba(107,114,128,0.1)';
                            @endphp
                            <span class="badge" style="background:{{ $statusBg }};color:{{ $statusColor }};font-size:0.65rem;font-weight:700;border-radius:6px;padding:4px 10px;">{{ strtoupper($reg->status) }}</span>
                            
                            @if($reg->attendance_status)
                                <span class="badge" style="background:rgba(6,182,212,0.1);color:#0891b2;font-size:0.65rem;font-weight:700;border-radius:6px;padding:4px 10px;">PRESENT</span>
                            @endif

                            @if($reg->certificate_number)
                                <span class="badge" style="background:rgba(124,58,237,0.1);color:var(--crm-primary);font-size:0.65rem;font-weight:700;border-radius:6px;padding:4px 10px;">CERTIFIED</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white border border-dashed rounded-4">
                        <i class="bi bi-calendar-x display-4 text-muted opacity-25"></i>
                        <p class="text-muted small mt-3">Belum ada riwayat pendaftaran event</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Courses Section --}}
        <div id="section-courses" class="activity-section d-none">
            <div class="row g-3">
                @forelse($enrollments as $enr)
                <div class="col-md-6">
                    <div class="activity-card h-100">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div style="width:44px;height:44px;border-radius:12px;background:rgba(124,58,237,0.08);color:var(--crm-primary);display:flex;align-items:center;justify-content:center;font-size:1.3rem;">
                                <i class="bi bi-book"></i>
                            </div>
                            <div>
                                <div style="font-size:0.85rem;font-weight:800;color:var(--crm-navy);line-height:1.3;">{{ $enr->course->name ?? 'N/A' }}</div>
                                <div style="font-size:0.7rem;color:var(--crm-text-subtle);">{{ $enr->course->category->name ?? 'General' }}</div>
                            </div>
                        </div>
                        
                        <div class="d-flex flex-column gap-2 mb-3">
                            <div class="d-flex justify-content-between">
                                <span style="font-size:0.75rem;color:var(--crm-text-subtle);font-weight:600;">Status</span>
                                <span class="badge" style="background:rgba(16,185,129,0.1);color:#059669;font-size:0.65rem;font-weight:700;border-radius:100px;">{{ strtoupper($enr->status) }}</span>
                            </div>
                            @if($enr->completed_at)
                            <div class="d-flex justify-content-between">
                                <span style="font-size:0.75rem;color:var(--crm-text-subtle);font-weight:600;">Selesai pada</span>
                                <span style="font-size:0.75rem;color:var(--crm-navy);font-weight:700;">{{ $enr->completed_at->format('d M Y') }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                            <span style="font-size:0.68rem;color:var(--crm-text-muted);">Enrolled {{ $enr->created_at->format('d/m/Y') }}</span>
                            @if($enr->certificate_number)
                                <i class="bi bi-patch-check-fill text-primary" title="Certified" style="font-size:1rem;"></i>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5 bg-white border border-dashed rounded-4">
                        <i class="bi bi-journal-x display-4 text-muted opacity-25"></i>
                        <p class="text-muted small mt-3">Belum ada riwayat kursus</p>
                    </div>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    function showSection(section, btn) {
        // Toggle Sections
        document.getElementById('section-events').classList.toggle('d-none', section !== 'events');
        document.getElementById('section-courses').classList.toggle('d-none', section !== 'courses');
        
        // Toggle Buttons
        const buttons = document.querySelectorAll('#activityTabs button');
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    }

    function openDeleteModalShow(btn) {
        document.getElementById('del-show-name').textContent  = btn.dataset.customerName  || '-';
        document.getElementById('del-show-email').textContent = btn.dataset.customerEmail || '-';
        document.getElementById('del-show-role').textContent  = btn.dataset.customerRole  || '-';
        document.getElementById('deleteCustomerShowForm').action = btn.dataset.deleteUrl;
        bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteCustomerShowModal')).show();
    }

    function submitDeleteShow(btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menghapus...';
        const cancelBtn = btn.closest('.d-flex').querySelector('[data-bs-dismiss="modal"]');
        if (cancelBtn) { cancelBtn.disabled = true; cancelBtn.classList.add('disabled'); }
        document.getElementById('deleteCustomerShowForm').submit();
    }
</script>
@endsection

@push('modals')
{{-- Delete Customer Confirmation Modal (Show Page) --}}
<div class="modal fade" id="deleteCustomerShowModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <div class="modal-body p-4">

                {{-- Icon --}}
                <div class="text-center mb-4">
                    <div style="width:60px;height:60px;border-radius:50%;background:rgba(239,68,68,0.1);display:inline-flex;align-items:center;justify-content:center;color:#ef4444;font-size:1.75rem;">
                        <i class="bi bi-person-x-fill"></i>
                    </div>
                    <h5 class="fw-800 mt-3 mb-1" style="font-size:1.1rem;color:var(--crm-navy);">Hapus Akun User?</h5>
                    <p style="font-size:0.8rem;color:var(--crm-text-subtle);margin:0;">Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</p>
                </div>

                {{-- Customer Info --}}
                <div class="mb-4 p-3" style="background:var(--crm-border-soft);border-radius:12px;border:1px dashed var(--crm-border);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-700 text-muted" style="font-size:0.7rem;text-transform:uppercase;">Nama:</span>
                        <span id="del-show-name" class="fw-800" style="font-size:0.82rem;color:var(--crm-navy);">-</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-700 text-muted" style="font-size:0.7rem;text-transform:uppercase;">Email:</span>
                        <span id="del-show-email" class="fw-700" style="font-size:0.78rem;color:var(--crm-primary);">-</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-700 text-muted" style="font-size:0.7rem;text-transform:uppercase;">Peran:</span>
                        <span id="del-show-role" class="fw-700" style="font-size:0.78rem;color:var(--crm-navy);">-</span>
                    </div>
                </div>

                {{-- Warning --}}
                <div style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.15);border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.5rem;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill mt-1" style="color:#ef4444;font-size:0.85rem;flex-shrink:0;"></i>
                        <span style="font-size:0.75rem;color:#dc2626;font-weight:600;line-height:1.5;">Menghapus akun ini akan menghapus semua data terkait user tersebut dari sistem secara permanen.</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-sm fw-700 px-4"
                            style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:10px;font-size:0.85rem;padding:0.6rem 1.5rem;"
                            data-bs-dismiss="modal">Batal</button>
                    <form id="deleteCustomerShowForm" action="#" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="submitDeleteShow(this)" class="btn btn-sm fw-700 px-4"
                                style="background:#ef4444;color:#fff;border-radius:10px;font-size:0.85rem;padding:0.6rem 1.5rem;">
                            <i class="bi bi-trash3-fill me-1"></i> Ya, Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush
