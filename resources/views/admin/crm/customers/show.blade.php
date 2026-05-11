@extends('layouts.crm')

@section('title', 'Detail Customer')

@section('styles')

<style>
    .profile-header-card {
        background: linear-gradient(135deg, var(--crm-primary), var(--crm-primary-dark));
        color: white;
        border-radius: 24px;
        overflow: hidden;
        border: none;
        position: relative;
    }
    .profile-header-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .profile-avatar-big {
        width: 120px;
        height: 120px;
        border-radius: 20px;
        border: 4px solid rgba(255, 255, 255, 0.2);
        object-fit: cover;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .info-label {
        color: var(--crm-text-muted);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 0.25rem;
    }
    .info-value {
        color: var(--crm-navy);
        font-weight: 600;
        font-size: 0.95rem;
    }
    .icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--crm-accent-light);
        color: var(--crm-primary);
        font-size: 1.1rem;
    }
    .timeline-item {
        position: relative;
        padding-left: 30px;
        padding-bottom: 25px;
        border-left: 2px solid var(--crm-border);
    }
    .timeline-item:last-child {
        border-left: 2px solid transparent;
        padding-bottom: 0;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--crm-primary);
        border: 2px solid white;
        box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.1);
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.crm.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.crm.customers.index') }}" class="text-decoration-none">Customers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>
        <h3 class="fw-800 text-navy mb-0">Manajemen Profil Akun</h3>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-white shadow-sm border-0 px-3 fw-600 rounded-3">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
        <a href="{{ route('admin.crm.customers.edit', $customer) }}" class="btn btn-primary shadow-lg border-0 px-3 fw-600 rounded-3">
            <i class="bi bi-pencil-square me-2"></i> Edit Profil
        </a>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="card profile-header-card border-0">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="{{ $customer->avatar_url }}" class="profile-avatar-big" alt="avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=fff&color=6d28d9&size=128'">
                    </div>
                    <div class="col ms-3">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <h2 class="fw-800 mb-0">{{ $customer->name }}</h2>
                            @if($customer->role === 'reseller')
                                <span class="badge bg-warning text-dark fw-700 px-3 rounded-pill shadow-sm">RESELLER</span>
                            @elseif($customer->role === 'trainer')
                                <span class="badge bg-info text-dark fw-700 px-3 rounded-pill shadow-sm">TRAINER</span>
                            @else
                                <span class="badge bg-white text-primary fw-700 px-3 rounded-pill shadow-sm">CUSTOMER</span>
                            @endif
                        </div>
                        <p class="mb-0 opacity-75 fs-5 fw-500"><i class="bi bi-envelope me-2"></i>{{ $customer->email }}</p>
                        <div class="mt-4">
                            <span class="badge bg-white text-primary px-3 py-2 rounded-pill shadow-sm small">
                                <i class="bi bi-clock-history me-1"></i> Terdaftar sejak {{ $customer->created_at->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Contact & Personal Info -->
    <div class="col-lg-4">
        <div class="card-minimal border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-800 text-navy mb-4">Kontak & Informasi</h5>
            
            <div class="d-flex align-items-center mb-4">
                <div class="icon-box me-3">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div>
                    <span class="info-label">Nomor Telepon</span>
                    <span class="info-value">{{ $customer->phone ?? 'Belum ditambahkan' }}</span>
                </div>
            </div>

            <div class="d-flex align-items-center mb-4 text-truncate">
                <div class="icon-box me-3">
                    <i class="bi bi-globe"></i>
                </div>
                <div>
                    <span class="info-label">Website / Portfolio</span>
                    @if($customer->website)
                        <a href="{{ $customer->website }}" target="_blank" class="info-value text-primary text-decoration-none">
                            {{ Str::limit(str_replace(['http://', 'https://'], '', $customer->website), 25) }} <i class="bi bi-box-arrow-up-right smaller ms-1"></i>
                        </a>
                    @else
                        <span class="info-value text-muted">Tidak tersedia</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center mb-4">
                <div class="icon-box me-3">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div>
                    <span class="info-label">Terdaftar Pada</span>
                    <span class="info-value">{{ $customer->created_at->format('d F Y') }}</span>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <h6 class="fw-700 text-navy mb-2">Biografi Singkat</h6>
            <p class="text-muted small lh-lg">
                {{ $customer->bio ?? 'Customer ini belum menuliskan biografi mereka. Informasi profil yang lengkap memudahkan interaksi dalam komunitas.' }}
            </p>
        </div>

    </div>

    <!-- Activities & Programs -->
    <div class="col-lg-8">
        <!-- Mini Menu Toggle -->
        <div class="mb-4 d-flex justify-content-center">
            <div class="bg-light p-1 rounded-pill border d-inline-flex shadow-sm" style="min-width: 300px;">
                <button type="button" id="toggle-events" class="btn btn-primary rounded-pill flex-grow-1 fw-700 py-2 transition-all" onclick="showSection('events')">
                    <i class="bi bi-calendar-event me-2"></i>Riwayat Event
                </button>
                <button type="button" id="toggle-courses" class="btn btn-light rounded-pill flex-grow-1 fw-700 py-2 transition-all" onclick="showSection('courses')">
                    <i class="bi bi-book me-2"></i>Riwayat Course
                </button>
            </div>
        </div>

        <!-- Events Section -->
        <div id="section-events" class="activity-section">
            <div class="card-minimal border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-800 text-navy mb-0">Event Terdaftar ({{ $registrations->count() }})</h5>
                </div>
                <div class="card-body p-4">
                    @forelse($registrations as $registration)
                        <div class="p-3 bg-white rounded-4 border mb-3 hover-lift transition-all">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-800 text-navy fs-5">{{ $registration->event?->title ?? 'Program N/A' }}</div>
                                    <div class="text-muted small mb-3">
                                        <i class="bi bi-calendar3 me-1"></i> {{ $registration->event?->event_date ? \Carbon\Carbon::parse($registration->event?->event_date)->format('d M Y') : 'Online/Flexible' }}
                                    </div>
                                    <div class="d-flex flex-wrap gap-2">
                                        <span class="badge {{ $registration->status === 'active' ? 'bg-success' : 'bg-secondary' }} px-3 rounded-pill" style="font-size: 0.65rem;">
                                            STATUS: {{ strtoupper($registration->status) }}
                                        </span>
                                        @if($registration->attendance_status)
                                            <span class="badge bg-info px-3 rounded-pill text-white" style="font-size: 0.65rem;">
                                                ABSENSI: {{ strtoupper($registration->attendance_status) }}
                                            </span>
                                        @endif
                                        @if($registration->certificate_number)
                                            <span class="badge bg-primary px-3 rounded-pill text-white" style="font-size: 0.65rem;">
                                                CERT: {{ $registration->certificate_number }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="smaller text-muted fw-600">Registrasi pada</div>
                                    <div class="fw-700 text-navy">{{ $registration->created_at->format('d M Y') }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                            <i class="bi bi-calendar-x opacity-25 display-4"></i>
                            <p class="text-muted mt-2 mb-0">Tidak ada riwayat pendaftaran event</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Courses Section -->
        <div id="section-courses" class="activity-section d-none">
            <div class="card-minimal border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                    <h5 class="fw-800 text-navy mb-0">Course Terdaftar ({{ $enrollments->count() }})</h5>
                </div>
                <div class="card-body p-4">
                    @if($enrollments->count() > 0)
                        <div class="row g-3">
                            @foreach($enrollments as $enrollment)
                                <div class="col-md-6">
                                    <div class="p-4 bg-white rounded-4 border h-100 hover-lift transition-all">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
                                                <i class="bi bi-book fs-4"></i>
                                            </div>
                                            <div class="fw-800 text-navy fs-6">{{ $enrollment->course->name ?? 'N/A' }}</div>
                                        </div>
                                        
                                        <div class="space-y-2">
                                            <div class="d-flex justify-content-between smaller">
                                                <span class="text-muted fw-600">Status</span>
                                                <span class="badge {{ $enrollment->status === 'active' ? 'bg-primary' : 'bg-success' }} rounded-pill px-2">{{ strtoupper($enrollment->status) }}</span>
                                            </div>
                                            @if($enrollment->completed_at)
                                                <div class="d-flex justify-content-between smaller mt-2">
                                                    <span class="text-muted fw-600">Selesai pada</span>
                                                    <span class="fw-700 text-navy">{{ $enrollment->completed_at->format('d M Y') }}</span>
                                                </div>
                                            @endif
                                            @if($enrollment->certificate_number)
                                                <div class="d-flex justify-content-between smaller mt-2">
                                                    <span class="text-muted fw-600">No. Sertifikat</span>
                                                    <span class="fw-700 text-primary">{{ $enrollment->certificate_number }}</span>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center">
                                            <span class="smaller text-muted">Enrolled {{ $enrollment->created_at->format('d/m/Y') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                            <i class="bi bi-journal-x opacity-25 display-4"></i>
                            <p class="text-muted mt-2 mb-0">Belum ada riwayat kursus</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

<script>
    function showSection(section) {
        // Toggle Sections
        document.getElementById('section-events').classList.toggle('d-none', section !== 'events');
        document.getElementById('section-courses').classList.toggle('d-none', section !== 'courses');
        
        // Toggle Buttons
        const btnEvents = document.getElementById('toggle-events');
        const btnCourses = document.getElementById('toggle-courses');
        
        if (section === 'events') {
            btnEvents.classList.replace('btn-light', 'btn-primary');
            btnCourses.classList.replace('btn-primary', 'btn-light');
        } else {
            btnEvents.classList.replace('btn-primary', 'btn-light');
            btnCourses.classList.replace('btn-light', 'btn-primary');
        }
    }
</script>
        </div>


    </div>
</div>

@endsection
