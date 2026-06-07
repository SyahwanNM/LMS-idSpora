@extends('layouts.admin')

@section('title', 'Detail Trainer')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        :root {
            --primary-blue: #2563eb;
            --primary-dark: #1e40af;
            --surface-color: #ffffff;
            --bg-color: #f8fafc;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border-light: #e2e8f0;
            --shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.04);
            --radius-md: 16px;
            --radius-lg: 24px;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
        }

        .trainer-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
        }

        .trainer-sidebar {
            width: 260px;
            background: #fff;
            padding: 24px 16px;
            border-right: 1px solid #eee;
            flex-shrink: 0;
            position: sticky;
            top: 72px;
            height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .trainer-main {
            flex-grow: 1;
            padding: 32px;
            background-color: #F8F9FA;
        }

        /* Sidebar Navigation */
        .nav-menu-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 1px;
            margin-bottom: 12px;
            margin-top: 24px;
            display: block;
            padding-left: 16px;
        }

        .nav-menu-label:first-child {
            margin-top: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 11px 16px;
            color: #1e293b;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .sidebar-link i {
            font-size: 18px;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: #f8fafc;
            color: #3949ab;
        }

        .sidebar-link:hover i {
            color: #3949ab;
        }

        .sidebar-link.active {
            background-color: #3949ab;
            color: #fff;
        }

        .sidebar-link.active i {
            color: #fff;
        }

        .sidebar-parent {
            justify-content: space-between;
        }

        .sidebar-parent .sidebar-chevron {
            font-size: 0.8rem;
            transition: transform 0.2s ease;
        }

        .sidebar-parent[aria-expanded='true'] .sidebar-chevron {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            margin: 4px 0 8px;
        }

        .sidebar-submenu .sidebar-link {
            margin-left: 14px;
            padding: 7px 10px;
            font-size: 0.82rem;
            border-radius: 8px;
        }

        .sidebar-submenu .sidebar-link i {
            font-size: 0.95rem;
        }

        /* Hero Card */
        .hero-card {
            background: var(--surface-color);
            border-radius: var(--radius-md);
            padding: 32px;
            box-shadow: var(--shadow-sm);
            margin-bottom: 24px;
            border: 1px solid var(--border-light);
        }

        .hero-avatar {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            background-color: #1e3a8a;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 800;
            position: relative;
        }

        .status-dot {
            width: 18px;
            height: 18px;
            background-color: var(--success-color);
            border: 3px solid #fff;
            border-radius: 50%;
            position: absolute;
            bottom: 4px;
            right: 4px;
        }

        .badge-status {
            background-color: #d1fae5;
            color: #059669;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
        }

        .hero-meta {
            font-size: 14.5px;
            color: var(--text-muted);
            display: flex;
            gap: 24px;
            align-items: center;
        }

        /* Tabs Nav */
        .nav-tabs-custom {
            border-bottom: 1px solid var(--border-light);
            background: #fff;
            border-radius: var(--radius-md);
            padding: 12px 24px 0 24px;
            display: flex;
            gap: 32px;
            margin-bottom: 24px;
            box-shadow: var(--shadow-sm);
            list-style: none;
        }

        .nav-tabs-custom .nav-link {
            border: none;
            background: transparent;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 15px;
            padding: 16px 8px;
            border-bottom: 3px solid transparent;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            cursor: pointer;
        }

        .nav-tabs-custom .nav-link:hover {
            color: var(--primary-blue);
        }

        .nav-tabs-custom .nav-link.active {
            color: var(--primary-blue);
            border-bottom: 3px solid var(--primary-blue);
        }

        .tab-pane {
            display: none;
        }
        .tab-pane.active {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* General Card styles */
        .content-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            border: 1px solid #e5e7eb;
        }

        .content-card-title {
            font-size: 18px;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 20px;
        }

        /* Stat Boxes */
        .stat-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-grid-3 {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-box {
            background: #fff;
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            padding: 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: var(--shadow-sm);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        .stat-icon.blue { background: #eff6ff; color: #3b82f6; }
        .stat-icon.green { background: #f0fdf4; color: #22c55e; }
        .stat-icon.purple { background: #faf5ff; color: #a855f7; }
        .stat-icon.orange { background: #fff7ed; color: #f97316; }
        .stat-icon.red { background: #fef2f2; color: #ef4444; }

        .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0 0 4px 0;
            line-height: 1;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-muted);
            font-weight: 600;
            margin: 0;
        }

        .stat-sublabel {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* Form elements & tables */
        .table {
            vertical-align: middle;
        }
        
        .table th {
            font-weight: 700;
            color: var(--text-muted);
            font-size: 13px;
            text-transform: uppercase;
            border-bottom: 2px solid var(--border-light);
            padding: 16px;
        }
        
        .table td {
            padding: 16px;
            color: var(--text-main);
            font-weight: 500;
            font-size: 14px;
            border-bottom: 1px solid var(--border-light);
        }

        .badge-event {
            background: #eff6ff;
            color: #3b82f6;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        .badge-course {
            background: #faf5ff;
            color: #a855f7;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
        }

        .sidebar-right {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .info-row {
            display: flex;
            margin-bottom: 16px;
        }
        .info-label {
            width: 140px;
            color: var(--text-muted);
            font-weight: 600;
        }
        .info-value {
            flex: 1;
            color: var(--text-main);
            font-weight: 600;
        }
        
        .btn-outline-primary, .btn-outline-danger {
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 600;
        }
        
        /* Dropdown action button fixes */
        .dropdown-menu-action {
            border-radius: 12px;
            border: 1px solid var(--border-light);
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            padding: 8px;
        }
        .dropdown-menu-action .dropdown-item {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            font-size: 13.5px;
            color: var(--text-main);
        }
        .dropdown-menu-action .dropdown-item i {
            margin-right: 8px;
            color: var(--text-muted);
        }
        .dropdown-menu-action .dropdown-item:hover {
            background-color: #f8fafc;
            color: var(--primary-blue);
        }
        .dropdown-menu-action .dropdown-item:hover i {
            color: var(--primary-blue);
        }
        .dropdown-menu-action .dropdown-item.text-danger:hover {
            background-color: #fef2f2;
            color: #ef4444;
        }
        .dropdown-menu-action .dropdown-item.text-danger:hover i {
            color: #ef4444;
        }
    </style>
@endsection

@section('content')
    <div class="trainer-wrapper">
        <!-- Sidebar Navigation -->
        @include('admin.trainer.partials.sidebar')

        <main class="trainer-main">
            <!-- Breadcrumbs -->
            <div class="d-flex align-items-center gap-2 mb-4 text-muted fw-semibold" style="font-size: 14px;">
                <span>Dashboard</span>
                <i class="bi bi-chevron-right" style="font-size: 12px;"></i>
                <span>Trainer</span>
                <i class="bi bi-chevron-right" style="font-size: 12px;"></i>
                <span class="text-dark">Detail Trainer</span>
            </div>

            <!-- Hero Section -->
            @php
                // Initials extract
                $initials = collect(explode(' ', $trainer->name))->map(function($segment) {
                    return strtoupper(substr($segment, 0, 1));
                })->take(2)->implode('');
            @endphp
            <div class="hero-card d-flex justify-content-between align-items-center mb-4 bg-white p-4" style="border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
                <div class="d-flex align-items-center gap-4">
                    <div class="hero-avatar position-relative" style="width: 72px; height: 72px; background-color: #1e3a8a; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; font-weight: 700;">
                        LO
                        <div class="position-absolute bg-success rounded-circle border border-2 border-white" style="width: 16px; height: 16px; bottom: 2px; right: 2px;"></div>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <h2 class="mb-0 fw-bold" style="font-size: 24px; color: #0f172a;">{{ $trainer->name ?? 'Loren' }}</h2>
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1" style="font-size: 12px; font-weight: 600;">Aktif</span>
                        </div>
                        <div class="text-muted mb-2" style="font-size: 13.5px;">
                            Dosen ITB &bull; AI Trainer &bull; Bergabung {{ \Carbon\Carbon::parse($trainer->created_at)->translatedFormat('d M Y') }}
                        </div>
                        <div class="d-flex align-items-center gap-3 text-dark fw-medium" style="font-size: 13.5px;">
                            <div class="d-flex align-items-center gap-2"><i class="bi bi-envelope text-muted"></i> {{ $trainer->email ?? 'loren@gmail.com' }}</div>
                            <div class="text-muted">|</div>
                            <div class="d-flex align-items-center gap-2"><i class="bi bi-telephone text-muted"></i> 123456</div>
                            <div class="text-muted">|</div>
                            <div class="d-flex align-items-center gap-2"><i class="bi bi-whatsapp text-muted"></i> 0812-3456-7890</div>
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button class="btn btn-outline-primary d-flex align-items-center gap-2 rounded-pill px-4 fw-semibold" style="font-size: 13.5px;"><i class="bi bi-pencil"></i> Edit Trainer</button>
                    <button class="btn btn-outline-primary d-flex align-items-center gap-2 rounded-pill px-4 fw-semibold" style="font-size: 13.5px;"><i class="bi bi-calendar-event"></i> Undang ke Event</button>
                    <button class="btn btn-outline-danger d-flex align-items-center gap-2 rounded-pill px-4 fw-semibold" style="font-size: 13.5px;"><i class="bi bi-slash-circle"></i> Nonaktifkan</button>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav-tabs-custom nav" id="trainerTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="profil-tab" data-bs-toggle="tab" data-bs-target="#tab-profil" type="button" role="tab">
                        <i class="bi bi-person"></i> Profil & Akun
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="event-tab" data-bs-toggle="tab" data-bs-target="#tab-event" type="button" role="tab">
                        <i class="bi bi-calendar2-check"></i> Event & Course
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="materi-tab" data-bs-toggle="tab" data-bs-target="#tab-materi" type="button" role="tab">
                        <i class="bi bi-journal-text"></i> Materi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="deadline-tab" data-bs-toggle="tab" data-bs-target="#tab-deadline" type="button" role="tab">
                        <i class="bi bi-calendar-x"></i> Deadline Materi
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rating-tab" data-bs-toggle="tab" data-bs-target="#tab-rating" type="button" role="tab">
                        <i class="bi bi-star"></i> Rating & Ulasan
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="sertifikat-tab" data-bs-toggle="tab" data-bs-target="#tab-sertifikat" type="button" role="tab">
                        <i class="bi bi-award"></i> Sertifikat
                    </button>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="trainerTabsContent" style="display: block !important; visibility: visible !important; min-height: 500px;">
                
                                                <!-- TAB 1: Profil & Akun -->
                <div class="tab-pane show active" id="tab-profil" role="tabpanel" aria-labelledby="profil-tab" style="display: block !important; opacity: 1 !important; visibility: visible !important;">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Informasi Profil -->
                            <div class="content-card mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0 fw-bold" style="font-size: 16px; color: #1e293b;">Informasi Profil</h5>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" style="font-size: 12px;"><i class="bi bi-pencil me-1"></i> Edit</button>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Nama Lengkap</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13.5px;">{{ $trainer->name ?? 'Loren' }}</div></div>
                                        <div class="d-flex mb-3 align-items-center"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Email</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill d-flex align-items-center gap-2" style="font-size: 13.5px;">{{ $trainer->email ?? 'loren@gmail.com' }} <span class="badge bg-success bg-opacity-10 text-success rounded-1 px-2 py-1" style="font-size: 10px; font-weight: 700;">Terverifikasi</span></div></div>
                                        <div class="d-flex mb-3 align-items-center"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">No. WhatsApp</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill d-flex align-items-center gap-2" style="font-size: 13.5px;">0812-3456-7890 <span class="badge bg-success bg-opacity-10 text-success rounded-1 px-2 py-1" style="font-size: 10px; font-weight: 700;">Terverifikasi</span></div></div>
                                        <div class="d-flex mb-3 align-items-center"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Status Akun</div><div class="me-3 text-muted">:</div><div class="fw-semibold flex-fill" style="font-size: 13.5px;"><span class="badge bg-success text-white rounded-1 px-3 py-1" style="font-weight: 600;">Aktif</span></div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Profesi</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13.5px;">Dosen</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Institusi</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13.5px;">ITB</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Website</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13.5px;">-</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">LinkedIn</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13.5px;">https://linkedin.com/in/loren</div></div>
                                        <div class="d-flex mb-3 align-items-center"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Role</div><div class="me-3 text-muted">:</div><div class="fw-semibold flex-fill" style="font-size: 13.5px;"><span class="badge bg-primary text-white rounded-1 px-3 py-1" style="font-weight: 600;">Trainer</span></div></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Tanggal Bergabung</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13.5px;">{{ \Carbon\Carbon::parse($trainer->created_at)->translatedFormat('d M Y') }}</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Terakhir Diperbarui</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13.5px;">21 Mei 2024, 15:50</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Keahlian (Skill)</div><div class="me-3 text-muted">:</div>
                                            <div class="fw-semibold flex-fill d-flex flex-wrap gap-2" style="font-size: 13.5px;"> 
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-1 px-2 py-1" style="font-weight: 600;">Artificial Intelligence</span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-1 px-2 py-1" style="font-weight: 600;">Machine Learning</span>
                                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-1 px-2 py-1" style="font-weight: 600;">Data Science</span>
                                            </div>
                                        </div>
                                        <div class="d-flex mb-3 mt-2"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Pendidikan Terakhir</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13.5px;">S1 - ITB</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Sertifikasi</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13.5px;">AI Fundamentals, Data Scientist<br>Bootcamp</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 150px; font-size: 13.5px;">Bahasa</div><div class="me-3 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13.5px;">Indonesia, English</div></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4 mb-md-0">
                                    <div class="content-card h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="mb-0 fw-bold" style="font-size: 16px; color: #1e293b;">Informasi Akun</h5>
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" style="font-size: 12px;"><i class="bi bi-pencil me-1"></i> Edit</button>
                                        </div>
                                        <div class="d-flex mb-3 align-items-center"><div class="text-muted" style="min-width: 130px; font-size: 13px;">Username</div><div class="me-2 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13px;">loren.trainer</div></div>
                                        <div class="d-flex mb-3 align-items-center"><div class="text-muted" style="min-width: 130px; font-size: 13px;">Password</div><div class="me-2 text-muted">:</div><div class="fw-semibold text-dark flex-fill d-flex justify-content-between align-items-center" style="font-size: 13px;">******** <button class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1 fw-semibold" style="font-size: 11px;">Ubah Password</button></div></div>
                                        <div class="d-flex mb-3 align-items-center"><div class="text-muted" style="min-width: 130px; font-size: 13px;">Login Terakhir</div><div class="me-2 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13px;">21 Mei 2024, 15:30</div></div>
                                        <div class="d-flex mb-3 align-items-center"><div class="text-muted" style="min-width: 130px; font-size: 13px;">Status Keamanan</div><div class="me-2 text-muted">:</div><div class="fw-semibold flex-fill d-flex align-items-center gap-2" style="font-size: 13px;"><span class="badge bg-success bg-opacity-10 text-success rounded-1 px-2 py-1" style="font-weight: 700;">Aman</span> <span class="text-muted fw-normal" style="font-size: 11px;">Tidak ada aktivitas mencurigakan.</span></div></div>
                                        <div class="d-flex mb-3 align-items-center"><div class="text-muted" style="min-width: 130px; font-size: 13px;">Verifikasi 2 Langkah</div><div class="me-2 text-muted">:</div><div class="fw-semibold flex-fill d-flex align-items-center gap-2" style="font-size: 13px;"><span class="badge bg-light text-muted border rounded-1 px-2 py-1" style="font-weight: 600;">Tidak Aktif</span> <a href="#" class="fw-bold text-primary text-decoration-none" style="font-size: 12px;">Aktifkan</a></div></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="content-card h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-4">
                                            <h5 class="mb-0 fw-bold" style="font-size: 16px; color: #1e293b;">Informasi Alamat</h5>
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" style="font-size: 12px;"><i class="bi bi-pencil me-1"></i> Edit</button>
                                        </div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 100px; font-size: 13px;">Alamat</div><div class="me-2 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13px;">JL. Ganesha No. 10, Bandung,<br>Jawa Barat, 40132</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 100px; font-size: 13px;">Kota</div><div class="me-2 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13px;">Bandung</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 100px; font-size: 13px;">Provinsi</div><div class="me-2 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13px;">Jawa Barat</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 100px; font-size: 13px;">Kode Pos</div><div class="me-2 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13px;">40132</div></div>
                                        <div class="d-flex mb-3"><div class="text-muted" style="min-width: 100px; font-size: 13px;">Negara</div><div class="me-2 text-muted">:</div><div class="fw-semibold text-dark flex-fill" style="font-size: 13px;">Indonesia</div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 sidebar-right">
                            <div class="content-card mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0 fw-bold" style="font-size: 16px; color: #1e293b;">Foto & Identitas</h5>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" style="font-size: 12px;"><i class="bi bi-pencil me-1"></i> Edit</button>
                                </div>
                                <div class="text-center my-4 pt-2">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($trainer->name) }}&background=e0f2fe&color=0ea5e9&size=140" class="rounded-circle mb-4" alt="Avatar">
                                    
                                    <div class="border rounded-3 p-3 mb-3 mx-auto" style="max-width: 220px; border-style: solid !important; border-color: #e2e8f0 !important;">
                                        <a href="#" class="text-primary fw-semibold text-decoration-none d-block mb-1" style="font-size: 13.5px;"><i class="bi bi-upload me-1"></i> Ubah Foto</a>
                                        <div class="text-muted" style="font-size: 11.5px;">JPG, PNG (Max. 2MB)</div>
                                    </div>
                                </div>
                                
                                <div class="alert bg-primary bg-opacity-10 border-0 text-start text-primary d-flex align-items-start gap-2 mb-0 rounded-3" style="padding: 12px;">
                                    <i class="bi bi-info-circle mt-1" style="font-size: 14px;"></i>
                                    <span style="font-size: 12.5px; line-height: 1.5;">Foto ini akan ditampilkan pada sertifikat dan profil publik.</span>
                                </div>
                            </div>
                            
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="mb-0 fw-bold" style="font-size: 16px; color: #1e293b;">Catatan Admin</h5>
                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold" style="font-size: 11px;">Tambah Catatan</button>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex gap-2 align-items-center">
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 11px; background-color: #1e3a8a;">AD</div>
                                            <span class="fw-bold text-dark" style="font-size: 13.5px;">Admin idSpora</span>
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-1 px-2" style="font-size: 10px;">Admin</span>
                                        </div>
                                        <span class="text-muted" style="font-size: 11px;">21 Mei 2024, 15:50</span>
                                    </div>
                                    <p class="mb-0 text-secondary" style="font-size: 13.5px;">Trainer aktif, responsif, dan materi berkualitas baik.</p>
                                </div>
                                <div class="border-top pt-3 mt-4">
                                    <span class="text-muted" style="font-size: 11.5px;">Catatan ini hanya dapat dilihat oleh admin.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 2: Event & Course -->
                <div class="tab-pane" id="tab-event" role="tabpanel" aria-labelledby="event-tab">
                    <div class="row">
                        <div class="col-lg-7">
                            <h5 class="fw-bold mb-3 text-dark">Ringkasan Aktivitas</h5>
                            <div class="stat-grid-4">
                                <div class="stat-box vertical">
                                    <div class="stat-icon purple mb-2"><i class="bi bi-calendar2-event"></i></div>
                                    <div class="stat-value">4</div>
                                    <div class="stat-label text-capitalize">Total Event</div>
                                    <div class="stat-sublabel"><span class="text-success">&bull; Selesai</span> &bull; Berjalan</div>
                                </div>
                                <div class="stat-box vertical">
                                    <div class="stat-icon green mb-2"><i class="bi bi-journal-bookmark"></i></div>
                                    <div class="stat-value">5</div>
                                    <div class="stat-label text-capitalize">Total Course</div>
                                    <div class="stat-sublabel"><span class="text-success">&bull; Aktif</span> <span class="text-warning">&bull; 1 Draft</span></div>
                                </div>
                                <div class="stat-box vertical">
                                    <div class="stat-icon orange mb-2"><i class="bi bi-people"></i></div>
                                    <div class="stat-value">12</div>
                                    <div class="stat-label text-capitalize">Total Peserta</div>
                                    <div class="stat-sublabel">Event & Course</div>
                                </div>
                                <div class="stat-box vertical">
                                    <div class="stat-icon blue mb-2"><i class="bi bi-clock-history"></i></div>
                                    <div class="stat-value">68</div>
                                    <div class="stat-label text-capitalize">Total Jam Mengajar</div>
                                    <div class="stat-sublabel">Event & Course</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <h5 class="fw-bold mb-3 text-dark">Performa Mengajar</h5>
                            <div class="stat-grid-3">
                                <div class="stat-box vertical">
                                    <div class="stat-icon green mb-2" style="background:#d1fae5; color:#059669;"><i class="bi bi-star-fill"></i></div>
                                    <div class="stat-value fs-4">4.8<span class="text-muted fs-6">/5</span></div>
                                    <div class="stat-label text-capitalize" style="font-size:12px;">Rata-rata Rating</div>
                                    <div class="stat-sublabel">Dari 25 ulasan</div>
                                </div>
                                <div class="stat-box vertical">
                                    <div class="stat-icon blue mb-2"><i class="bi bi-hand-thumbs-up"></i></div>
                                    <div class="stat-value fs-4">98%</div>
                                    <div class="stat-label text-capitalize" style="font-size:12px;">Tingkat Penyelesaian</div>
                                    <div class="stat-sublabel">Materi Disetujui</div>
                                </div>
                                <div class="stat-box vertical">
                                    <div class="stat-icon purple mb-2"><i class="bi bi-trophy"></i></div>
                                    <div class="stat-value fs-4">3</div>
                                    <div class="stat-label text-capitalize" style="font-size:12px;">Penghargaan</div>
                                    <div class="stat-sublabel">Sebagai Trainer</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="content-card h-100">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="content-card-title mb-0">Daftar Event</h5>
                                    <div class="d-flex gap-2">
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3"><option>Semua Status</option></select>
                                        <div class="position-relative">
                                            <input type="text" class="form-control form-control-sm bg-light border-0 rounded-pill ps-3 pe-4" placeholder="Cari event...">
                                            <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-2 text-muted" style="font-size: 12px;"></i>
                                        </div>
                                        <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-plus"></i> Undang ke Event</button>
                                    </div>
                                </div>
                                <table class="table table-borderless table-hover">
                                    <thead><tr><th>Nama Event</th><th>Tanggal</th><th>Peran</th><th>Status Event</th><th>Peserta</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <tr>
                                            <td><div class="d-flex align-items-center gap-2"><div class="rounded p-2 text-white" style="background:#1e3a8a"><i class="bi bi-calendar-event"></i></div> <span class="fw-bold">Webinar AI Dasar</span></div></td>
                                            <td><div class="text-dark fw-bold">10 Juni 2026</div><div class="text-muted small">09.00 - 12.00 WIB</div></td>
                                            <td>Pembicara</td>
                                            <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Selesai</span></td>
                                            <td>245 Peserta</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary rounded-pill">Lihat Detail</button>
                                                <button class="btn btn-sm btn-light border-0"><i class="bi bi-three-dots-vertical"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><div class="d-flex align-items-center gap-2"><div class="rounded p-2 text-white" style="background:#5b21b6"><i class="bi bi-calendar-event"></i></div> <span class="fw-bold">Seminar Data Science</span></div></td>
                                            <td><div class="text-dark fw-bold">5 Juni 2026</div><div class="text-muted small">13.00 - 16.00 WIB</div></td>
                                            <td>Pembicara</td>
                                            <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Selesai</span></td>
                                            <td>187 Peserta</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary rounded-pill">Lihat Detail</button>
                                                <button class="btn btn-sm btn-light border-0"><i class="bi bi-three-dots-vertical"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small">
                                    <span>Menampilkan 1 - 4 dari 4 event</span>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-left"></i></button>
                                        <button class="btn btn-sm btn-primary">1</button>
                                        <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="content-card h-100">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="content-card-title mb-0">Daftar Course</h5>
                                    <div class="d-flex gap-2">
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3"><option>Semua Status</option></select>
                                        <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-plus"></i> Buat Course</button>
                                    </div>
                                </div>
                                <table class="table table-borderless table-hover">
                                    <thead><tr><th>Nama Course</th><th>Status</th><th>Modul</th><th>Peserta</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <tr>
                                            <td><div class="d-flex align-items-center gap-2"><div class="rounded p-2 text-white" style="background:#1e3a8a"><i class="bi bi-book"></i></div> <div><div class="fw-bold text-dark">AI Dasar untuk Pemula</div><div class="small text-muted">Diperbarui 12 Juni 2026</div></div></div></td>
                                            <td><span class="badge bg-success text-white rounded-pill px-3">Aktif</span></td>
                                            <td>8 Modul</td>
                                            <td>245</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary rounded-pill">Lihat Detail</button>
                                                <button class="btn btn-sm btn-light border-0"><i class="bi bi-three-dots-vertical"></i></button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><div class="d-flex align-items-center gap-2"><div class="rounded p-2 text-white" style="background:#5b21b6"><i class="bi bi-book"></i></div> <div><div class="fw-bold text-dark">Data Science Fundamentals</div><div class="small text-muted">Diperbarui 10 Juni 2026</div></div></div></td>
                                            <td><span class="badge bg-success text-white rounded-pill px-3">Aktif</span></td>
                                            <td>10 Modul</td>
                                            <td>187</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary rounded-pill">Lihat Detail</button>
                                                <button class="btn btn-sm btn-light border-0"><i class="bi bi-three-dots-vertical"></i></button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small">
                                    <span>Menampilkan 1 - 5 dari 5 course</span>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-left"></i></button>
                                        <button class="btn btn-sm btn-primary">1</button>
                                        <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 3: Materi -->
                <div class="tab-pane" id="tab-materi" role="tabpanel" aria-labelledby="materi-tab">
                    <div class="row">
                        <div class="col-lg-8">
                            <h5 class="fw-bold mb-3 text-dark">Deadline Prioritas <span class="fw-normal fs-6 text-muted ms-2">Fokus materi yang membutuhkan tindakan Anda</span></h5>
                            <div class="stat-grid-4">
                                <div class="stat-box vertical border-danger border-opacity-50 position-relative">
                                    <div class="stat-icon red mb-2"><i class="bi bi-clock"></i></div>
                                    <div class="stat-value text-danger">3</div>
                                    <div class="stat-label text-capitalize text-danger">Terlambat</div>
                                    <div class="stat-sublabel text-danger mb-3">Perlu segera ditindaklanjuti</div>
                                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3 w-100 text-danger bg-danger bg-opacity-10 border-0">Lihat Semua</button>
                                </div>
                                <div class="stat-box vertical border-warning border-opacity-50 position-relative">
                                    <div class="stat-icon orange mb-2"><i class="bi bi-calendar-event"></i></div>
                                    <div class="stat-value text-warning">2</div>
                                    <div class="stat-label text-capitalize text-warning">Hari Ini</div>
                                    <div class="stat-sublabel text-warning mb-3">Deadline hari ini</div>
                                    <button class="btn btn-sm btn-warning rounded-pill px-3 w-100 text-white">Kirim Reminder</button>
                                </div>
                                <div class="stat-box vertical position-relative">
                                    <div class="stat-icon orange mb-2"><i class="bi bi-clock-history"></i></div>
                                    <div class="stat-value text-warning">5</div>
                                    <div class="stat-label text-capitalize">Mendekati</div>
                                    <div class="stat-sublabel mb-3">Deadline 3 hari ke depan</div>
                                    <button class="btn btn-sm btn-outline-warning rounded-pill px-3 w-100 bg-warning bg-opacity-10 border-0 text-warning">Lihat Semua</button>
                                </div>
                                <div class="stat-box vertical position-relative border-purple border-opacity-50">
                                    <div class="stat-icon purple mb-2"><i class="bi bi-pencil-square"></i></div>
                                    <div class="stat-value" style="color: #a855f7;">7</div>
                                    <div class="stat-label text-capitalize" style="color: #a855f7;">Perlu Revisi</div>
                                    <div class="stat-sublabel mb-3" style="color: #a855f7;">Menunggu perbaikan</div>
                                    <button class="btn btn-sm rounded-pill px-3 w-100" style="background-color: #faf5ff; color: #a855f7;">Lihat Semua</button>
                                </div>
                            </div>
                            
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-primary rounded-pill px-3">Semua</button>
                                        <button class="btn btn-sm btn-outline-danger rounded-pill px-3 bg-danger bg-opacity-10 border-0">Terlambat</button>
                                        <button class="btn btn-sm btn-outline-warning rounded-pill px-3 bg-warning bg-opacity-10 border-0 text-warning">Hari ini</button>
                                        <button class="btn btn-sm btn-outline-warning rounded-pill px-3 bg-warning bg-opacity-10 border-0 text-warning">&le; 3 Hari</button>
                                        <button class="btn btn-sm btn-outline-success rounded-pill px-3 bg-success bg-opacity-10 border-0 text-success">&le; 7 Hari</button>
                                        <button class="btn btn-sm rounded-pill px-3" style="background-color: #faf5ff; color: #a855f7;">Revisi</button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3"><option>Semua Tipe</option></select>
                                        <div class="position-relative">
                                            <input type="text" class="form-control form-control-sm bg-light border-0 rounded-pill ps-3 pe-4" placeholder="Cari materi...">
                                            <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-2 text-muted" style="font-size: 12px;"></i>
                                        </div>
                                        <button class="btn btn-sm btn-primary rounded-pill px-3"><i class="bi bi-plus"></i> Upload Materi</button>
                                    </div>
                                </div>
                                
                                <table class="table table-borderless table-hover">
                                    <thead><tr><th>Judul Materi</th><th>Tipe</th><th>Terkait</th><th>Deadline</th><th>Sisa Waktu</th><th>Status</th><th>Versi</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <tr>
                                            <td><div class="d-flex align-items-center gap-2"><div class="rounded p-2 text-white" style="background:#1e3a8a"><i class="bi bi-journal-text"></i></div> <div><div class="fw-bold text-dark">Deep Learning Fundamentals</div><div class="small text-muted">Dasar-dasar Deep Learning</div></div></div></td>
                                            <td><span class="badge-course">Course</span></td>
                                            <td class="fw-bold">Machine Learning</td>
                                            <td><div class="fw-bold text-dark">12 Juni 2026</div><div class="small text-muted">23:59 WIB</div></td>
                                            <td><div class="text-danger fw-bold">Hari ini</div><div class="small text-danger">Terlambat</div></td>
                                            <td><span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Perlu Revisi</span></td>
                                            <td>v1</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat</button>
                                                    <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-action shadow-sm">
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> Detail Materi</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-chat-square-text"></i> Review Materi</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-calendar"></i> Ubah Deadline</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-clock-history"></i> Riwayat Review</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download Materi</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-star"></i> Tandai Prioritas</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-archive"></i> Arsipkan Materi</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small">
                                    <span>Menampilkan 1 - 5 dari 124 materi</span>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-left"></i></button>
                                        <button class="btn btn-sm btn-primary">1</button>
                                        <button class="btn btn-sm btn-light border">2</button>
                                        <button class="btn btn-sm btn-light border">3</button>
                                        <button class="btn btn-sm btn-light border">...</button>
                                        <button class="btn btn-sm btn-light border">25</button>
                                        <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 sidebar-right">
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="content-card-title mb-0">Deadline Mendekati</h5>
                                    <a href="#" class="text-decoration-none small text-primary fw-bold">Lihat Semua</a>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-danger fs-4"><i class="bi bi-clock"></i></div>
                                        <div><div class="fw-bold text-dark">Deep Learning</div><div class="small text-muted">Course: Machine Learning</div></div>
                                    </div>
                                    <div class="text-end"><div class="fw-bold text-danger">Hari ini</div><div class="small text-muted">12 Juni 2026</div></div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="text-warning fs-4"><i class="bi bi-clock"></i></div>
                                        <div><div class="fw-bold text-dark">Data Science Intro</div><div class="small text-muted">Course: Data Science Basics</div></div>
                                    </div>
                                    <div class="text-end"><div class="fw-bold text-warning">2 hari lagi</div><div class="small text-muted">15 Juni 2026</div></div>
                                </div>
                            </div>
                            <div class="content-card">
                                <h5 class="content-card-title">Statistik Materi</h5>
                                <div class="d-flex align-items-center gap-4 py-2">
                                    <div style="width: 120px; height: 120px; border-radius: 50%; border: 15px solid #22c55e; border-top-color: #f59e0b; border-right-color: #ef4444; position: relative;">
                                    </div>
                                    <div>
                                        <div class="mb-2"><span style="color:#22c55e;">&bull;</span> Approved <span class="fw-bold ms-2">99 (79.8%)</span></div>
                                        <div class="mb-2"><span style="color:#f59e0b;">&bull;</span> Pending Review <span class="fw-bold ms-2">18 (14.5%)</span></div>
                                        <div class="mb-2"><span style="color:#ef4444;">&bull;</span> Perlu Revisi <span class="fw-bold ms-2">7 (5.6%)</span></div>
                                        <div class="mb-2"><span style="color:#94a3b8;">&bull;</span> Draft <span class="fw-bold ms-2">0 (0%)</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="content-card">
                                <h5 class="content-card-title">Aksi Cepat</h5>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <button class="btn btn-outline-primary w-100 h-100 p-3 d-flex flex-column align-items-start gap-2 bg-primary bg-opacity-10 border-0" style="border-radius: 12px;">
                                            <i class="bi bi-bell-fill fs-4"></i>
                                            <span class="fw-bold text-start" style="font-size:13px;">Kirim Reminder Terpilih</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-primary w-100 h-100 p-3 d-flex flex-column align-items-start gap-2" style="border-radius: 12px; background: #faf5ff; border: 0; color: #a855f7;">
                                            <i class="bi bi-calendar-check-fill fs-4"></i>
                                            <span class="fw-bold text-start" style="font-size:13px;">Atur Deadline Massal</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-success w-100 h-100 p-3 d-flex flex-column align-items-start gap-2 bg-success bg-opacity-10 border-0" style="border-radius: 12px;">
                                            <i class="bi bi-file-earmark-text-fill fs-4"></i>
                                            <span class="fw-bold text-start" style="font-size:13px;">Template Deadline</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-warning w-100 h-100 p-3 d-flex flex-column align-items-start gap-2 bg-warning bg-opacity-10 text-warning border-0" style="border-radius: 12px;">
                                            <i class="bi bi-bar-chart-fill fs-4"></i>
                                            <span class="fw-bold text-start" style="font-size:13px;">Laporan Deadline</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="content-card">
                                <h5 class="content-card-title">Pengaturan Deadline</h5>
                                <p class="text-muted small mb-3">Kelola aturan deadline default untuk event dan course.</p>
                                <button class="btn btn-outline-primary w-100 rounded-pill"><i class="bi bi-gear-fill me-2"></i> Buka Pengaturan</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 4: Deadline Materi (Placeholder) -->
                <div class="tab-pane" id="tab-deadline" role="tabpanel" aria-labelledby="deadline-tab">
                    <div class="content-card text-center py-5">
                        <i class="bi bi-calendar-x text-muted mb-3 d-block" style="font-size: 48px;"></i>
                        <h4 class="fw-bold text-dark mb-2">Manajemen Deadline Materi</h4>
                        <p class="text-muted mb-0">Fitur untuk mengatur dan mengelola deadline materi trainer sedang dalam pengembangan.</p>
                    </div>
                </div>

                <!-- TAB 5: Rating & Ulasan -->
                <div class="tab-pane" id="tab-rating" role="tabpanel" aria-labelledby="rating-tab">
                    <div class="row">
                        <div class="col-lg-8">
                            <h5 class="fw-bold mb-3 text-dark">Ringkasan Rating</h5>
                            <div class="stat-grid-3">
                                <div class="content-card text-center mb-0 d-flex flex-column justify-content-center">
                                    <div class="text-primary fw-bold" style="font-size: 64px; line-height: 1;">4.8<i class="bi bi-star-fill text-warning ms-2" style="font-size: 32px;"></i></div>
                                    <div class="text-muted fw-bold mb-2">Dari 152 penilaian</div>
                                    <div><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-4 py-2 fs-6">Sangat Baik</span></div>
                                </div>
                                <div class="content-card mb-0 d-flex flex-column justify-content-center px-4">
                                    <div class="rating-bar-container"><div class="rating-bar-number">5 <i class="bi bi-star-fill text-warning" style="font-size: 10px;"></i></div><div class="rating-bar-track"><div class="rating-bar-fill" style="width: 60.5%"></div></div><div class="rating-bar-stat">92 (60.5%)</div></div>
                                    <div class="rating-bar-container"><div class="rating-bar-number">4 <i class="bi bi-star-fill text-warning" style="font-size: 10px;"></i></div><div class="rating-bar-track"><div class="rating-bar-fill" style="width: 32.9%"></div></div><div class="rating-bar-stat">50 (32.9%)</div></div>
                                    <div class="rating-bar-container"><div class="rating-bar-number">3 <i class="bi bi-star-fill text-warning" style="font-size: 10px;"></i></div><div class="rating-bar-track"><div class="rating-bar-fill" style="width: 5.3%"></div></div><div class="rating-bar-stat">8 (5.3%)</div></div>
                                    <div class="rating-bar-container"><div class="rating-bar-number">2 <i class="bi bi-star-fill text-warning" style="font-size: 10px;"></i></div><div class="rating-bar-track"><div class="rating-bar-fill" style="width: 1.3%"></div></div><div class="rating-bar-stat">2 (1.3%)</div></div>
                                    <div class="rating-bar-container"><div class="rating-bar-number">1 <i class="bi bi-star-fill text-warning" style="font-size: 10px;"></i></div><div class="rating-bar-track"><div class="rating-bar-fill" style="width: 0%"></div></div><div class="rating-bar-stat">0 (0%)</div></div>
                                </div>
                                <div class="d-flex flex-column gap-3">
                                    <div class="content-card mb-0 d-flex align-items-center gap-3">
                                        <div class="stat-icon green"><i class="bi bi-emoji-smile"></i></div>
                                        <div><div class="fs-3 fw-bold text-dark">98%</div><div class="fw-bold text-primary">Peserta puas</div></div>
                                    </div>
                                    <div class="content-card mb-0 d-flex align-items-center gap-3">
                                        <div class="stat-icon purple"><i class="bi bi-chat-square-text"></i></div>
                                        <div><div class="fs-3 fw-bold text-dark">126</div><div class="fw-bold text-primary">Total Ulasan</div></div>
                                    </div>
                                </div>
                            </div>

                            <div class="content-card mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-primary rounded-pill px-3">Semua Ulasan</button>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-0 bg-light position-relative">Perlu Dibalas <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">12</span></button>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-0 bg-light">Rating 5</button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3"><option>Semua Event</option></select>
                                    </div>
                                </div>
                                <table class="table table-borderless table-hover">
                                    <thead><tr><th>Peserta</th><th>Event</th><th>Rating</th><th>Ulasan</th><th>Tanggal</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <tr>
                                            <td><div class="d-flex align-items-center gap-2"><div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">RS</div> <div><div class="fw-bold text-dark">Rina Sari</div></div></div></td>
                                            <td><div class="fw-bold text-dark">Webinar AI Batch 3</div><div class="text-muted small">20 Mei 2024</div></td>
                                            <td><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i><i class="bi bi-star-fill text-warning"></i></td>
                                            <td style="max-width: 200px;"><div class="text-dark small">Penjelasan sangat jelas...</div></td>
                                            <td><div class="fw-bold text-dark">20 Mei 2024</div><div class="small text-muted">10:30 WIB</div></td>
                                            <td><button class="btn btn-sm btn-outline-primary rounded-pill px-3">Balas</button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-lg-4 sidebar-right">
                            <div class="content-card">
                                <h5 class="content-card-title">Rating per Kategori</h5>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center gap-2"><i class="bi bi-mortarboard text-success bg-success bg-opacity-10 p-2 rounded"></i> <span class="fw-bold text-dark">Penguasaan Materi</span></div>
                                    <div class="fw-bold text-warning"><i class="bi bi-star-fill me-1"></i>4.9</div>
                                </div>
                            </div>
                            <div class="content-card">
                                <h5 class="content-card-title">Tren Rating</h5>
                                <div class="text-center py-4 text-muted border rounded-3 bg-light">
                                    <i class="bi bi-graph-up fs-1 mb-2 d-block"></i>
                                    Line Chart Tren
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- TAB 5: Sertifikat -->
                <div class="tab-pane" id="tab-sertifikat" role="tabpanel" aria-labelledby="sertifikat-tab">
                    <div class="row">
                        <div class="col-lg-8">
                            <h5 class="fw-bold mb-3 text-dark">Ringkasan Sertifikat</h5>
                            <div class="stat-grid-4">
                                <div class="stat-box vertical">
                                    <div class="stat-icon blue mb-2"><i class="bi bi-award"></i></div>
                                    <div class="stat-value text-primary">38</div>
                                    <div class="stat-label text-capitalize">Total Sertifikat</div>
                                    <div class="stat-sublabel">Semua waktu</div>
                                </div>
                                <div class="stat-box vertical border-success border-opacity-50">
                                    <div class="stat-icon green mb-2"><i class="bi bi-calendar2-check"></i></div>
                                    <div class="stat-value text-success">22</div>
                                    <div class="stat-label text-capitalize text-success">Sertifikat Event</div>
                                    <div class="stat-sublabel">Semua waktu</div>
                                </div>
                                <div class="stat-box vertical border-purple border-opacity-50" style="border-color: #a855f7;">
                                    <div class="stat-icon purple mb-2"><i class="bi bi-mortarboard"></i></div>
                                    <div class="stat-value" style="color: #a855f7;">16</div>
                                    <div class="stat-label text-capitalize" style="color: #a855f7;">Sertifikat Course</div>
                                    <div class="stat-sublabel">Semua waktu</div>
                                </div>
                                <div class="stat-box vertical border-warning border-opacity-50">
                                    <div class="stat-icon orange mb-2"><i class="bi bi-patch-check"></i></div>
                                    <div class="stat-value text-warning">7</div>
                                    <div class="stat-label text-capitalize text-warning">Sertifikat Terbit</div>
                                    <div class="stat-sublabel">Bulan Ini</div>
                                </div>
                            </div>
                            
                            <div class="content-card mt-4">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-sm btn-primary rounded-pill px-3">Semua Sertifikat</button>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-0 bg-light">Event</button>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-0 bg-light">Course</button>
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-0 bg-light">Bulan Ini</button>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3"><option>Semua Tipe</option></select>
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-3"><option>Semua Tahun</option></select>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="position-relative w-50">
                                        <input type="text" class="form-control form-control-sm bg-light border-0 rounded-pill ps-3 pe-4" placeholder="Cari sertifikat...">
                                        <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-2 text-muted" style="font-size: 12px;"></i>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 small">
                                        <span class="text-muted">Urutkan:</span>
                                        <select class="form-select form-select-sm border-0 bg-light rounded-pill px-2 py-1"><option>Terbaru</option></select>
                                    </div>
                                </div>
                                <table class="table table-borderless table-hover">
                                    <thead><tr><th>Nama Sertifikat</th><th>Tipe</th><th>Event / Course</th><th>Tanggal Terbit</th><th>Status</th><th>Aksi</th></tr></thead>
                                    <tbody>
                                        <tr>
                                            <td><div class="d-flex align-items-center gap-2"><div class="border p-1 bg-white rounded shadow-sm text-center" style="width:40px; height:30px;"><i class="bi bi-file-earmark-text text-primary"></i></div> <div><div class="fw-bold text-dark">Webinar AI Batch 3</div><div class="small text-muted">ID: CERT-2024-0001</div></div></div></td>
                                            <td><span class="badge-event">Event</span></td>
                                            <td><div class="fw-bold text-dark">Webinar AI Batch 3</div></td>
                                            <td><div class="fw-bold text-dark">20 Mei 2024</div><div class="small text-muted">10:30 WIB</div></td>
                                            <td><span class="badge-terbit">Terbit</span></td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3">Lihat</button>
                                                <button class="btn btn-sm btn-light border-0"><i class="bi bi-download"></i></button>
                                                <div class="btn-group">
                                                    <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown"><i class="bi bi-three-dots-vertical"></i></button>
                                                    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-action shadow-sm">
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-eye"></i> Lihat Detail Sertifikat</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-download"></i> Download Sertifikat</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-envelope"></i> Kirim Ulang Sertifikat</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-printer"></i> Cetak Sertifikat</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-clock-history"></i> Riwayat Distribusi</a></li>
                                                        <li><a class="dropdown-item" href="#"><i class="bi bi-calendar-event"></i> Lihat Event/Course</a></li>
                                                        <li><hr class="dropdown-divider"></li>
                                                        <li><a class="dropdown-item text-danger" href="#"><i class="bi bi-archive"></i> Arsipkan Sertifikat</a></li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="d-flex justify-content-between align-items-center mt-3 text-muted small">
                                    <span>Menampilkan 1 - 5 dari 38 sertifikat</span>
                                    <div class="d-flex gap-1">
                                        <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-left"></i></button>
                                        <button class="btn btn-sm btn-primary">1</button>
                                        <button class="btn btn-sm btn-light border">2</button>
                                        <button class="btn btn-sm btn-light border">3</button>
                                        <button class="btn btn-sm btn-light border">...</button>
                                        <button class="btn btn-sm btn-light border">8</button>
                                        <button class="btn btn-sm btn-light border"><i class="bi bi-chevron-right"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 sidebar-right">
                            <div class="content-card">
                                <h5 class="content-card-title">Distribusi Sertifikat</h5>
                                <div class="d-flex align-items-center gap-4 py-2">
                                    <div style="width: 100px; height: 100px; border-radius: 50%; border: 15px solid #3b82f6; border-right-color: #a855f7; border-bottom-color: #a855f7; position: relative;">
                                    </div>
                                    <div>
                                        <div class="mb-2"><span style="color:#3b82f6;">&bull;</span> Event <span class="fw-bold ms-2">22 (58%)</span></div>
                                        <div class="mb-2"><span style="color:#a855f7;">&bull;</span> Course <span class="fw-bold ms-2">16 (42%)</span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="content-card-title mb-0">Sertifikat per Kategori</h5>
                                    <a href="#" class="text-decoration-none small text-primary fw-bold">Lihat Semua</a>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2"><i class="bi bi-robot text-primary bg-primary bg-opacity-10 p-2 rounded"></i> <span class="fw-bold text-dark small">AI & Machine Learning</span></div>
                                    <div class="small fw-bold text-warning"><i class="bi bi-star-fill me-1"></i> 12 (31.6%)</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2"><i class="bi bi-bar-chart text-success bg-success bg-opacity-10 p-2 rounded"></i> <span class="fw-bold text-dark small">Data Science</span></div>
                                    <div class="small fw-bold text-warning"><i class="bi bi-star-fill me-1"></i> 9 (23.7%)</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                    <div class="d-flex align-items-center gap-2"><i class="bi bi-code-slash text-purple bg-purple bg-opacity-10 p-2 rounded" style="color: #a855f7; background: #faf5ff;"></i> <span class="fw-bold text-dark small">Programming</span></div>
                                    <div class="small fw-bold text-warning"><i class="bi bi-star-fill me-1"></i> 7 (18.4%)</div>
                                </div>
                            </div>
                            <div class="content-card">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="content-card-title mb-0">Tren Penerbitan Sertifikat</h5>
                                    <span class="badge bg-light text-dark border">6 Bulan Terakhir</span>
                                </div>
                                <div class="text-center py-4 text-muted border rounded-3 bg-light">
                                    <i class="bi bi-graph-up fs-1 mb-2 d-block"></i>
                                    Line Chart Tren
                                </div>
                            </div>
                            <div class="content-card">
                                <h5 class="content-card-title">Aksi Cepat</h5>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <button class="btn btn-outline-primary w-100 h-100 p-3 d-flex flex-column align-items-start gap-2 bg-primary bg-opacity-10 border-0" style="border-radius: 12px;">
                                            <i class="bi bi-files fs-4"></i>
                                            <span class="fw-bold text-start" style="font-size:13px;">Generate Sertifikat Massal</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn w-100 h-100 p-3 d-flex flex-column align-items-start gap-2" style="border-radius: 12px; background: #faf5ff; border: 0; color: #a855f7;">
                                            <i class="bi bi-envelope-paper fs-4"></i>
                                            <span class="fw-bold text-start" style="font-size:13px;">Kirim Ulang Sertifikat</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-success w-100 h-100 p-3 d-flex flex-column align-items-start gap-2 bg-success bg-opacity-10 border-0" style="border-radius: 12px;">
                                            <i class="bi bi-download fs-4"></i>
                                            <span class="fw-bold text-start" style="font-size:13px;">Download Rekap Sertifikat</span>
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button class="btn btn-outline-warning w-100 h-100 p-3 d-flex flex-column align-items-start gap-2 bg-warning bg-opacity-10 text-warning border-0" style="border-radius: 12px;">
                                            <i class="bi bi-gear fs-4"></i>
                                            <span class="fw-bold text-start" style="font-size:13px;">Template Sertifikat</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>
@endsection

@push('admin-trainer-scripts')
<script>
    // Fallback script to ensure Tabs always switch correctly if Bootstrap JS fails to initialize.
    document.addEventListener('DOMContentLoaded', function() {
        const triggerTabList = [].slice.call(document.querySelectorAll('#trainerTabs button'))
        triggerTabList.forEach(function (triggerEl) {
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                // Remove active from all buttons
                document.querySelectorAll('#trainerTabs button').forEach(b => {
                    b.classList.remove('active');
                });
                // Add active to clicked button
                this.classList.add('active');
                
                // Hide all tab panes
                document.querySelectorAll('.tab-pane').forEach(p => {
                    p.classList.remove('show', 'active');
                    p.style.setProperty('display', 'none', 'important');
                    p.style.setProperty('opacity', '0', 'important');
                    p.style.setProperty('visibility', 'hidden', 'important');
                });
                // Show target pane
                const target = document.querySelector(this.getAttribute('data-bs-target'));
                if (target) {
                    target.classList.add('show', 'active');
                    target.style.setProperty('display', 'block', 'important');
                    target.style.setProperty('opacity', '1', 'important');
                    target.style.setProperty('visibility', 'visible', 'important');
                }
            });
        });
    });
</script>
@endpush
