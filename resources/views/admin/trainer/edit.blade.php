@extends('layouts.admin')

@section('title', 'Edit Trainer')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        /* Trainer Hero Section - CRM Style */
        .trainer-hero {
            background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
            border-radius: 24px;
            padding: 48px;
            color: #fff;
            margin-bottom: 36px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .trainer-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(138, 43, 226, 0.25) 0%, rgba(138, 43, 226, 0) 70%);
            border-radius: 50%;
            z-index: 1;
        }

        .trainer-hero::before {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -5%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(65, 105, 225, 0.2) 0%, rgba(65, 105, 225, 0) 70%);
            border-radius: 50%;
            z-index: 1;
        }

        .hero-label {
            background: rgba(138, 43, 226, 0.25);
            color: #c9a3ff;
            padding: 8px 18px;
            border-radius: 100px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: inline-block;
            margin-bottom: 20px;
            border: 1px solid rgba(138, 43, 226, 0.4);
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.8px;
            position: relative;
            z-index: 2;
        }

        .hero-subtitle {
            color: rgba(255, 255, 255, 0.75);
            max-width: 600px;
            line-height: 1.6;
            font-weight: 400;
            margin-bottom: 0;
            position: relative;
            z-index: 2;
            font-size: 1.05rem;
        }

        /* Form Card Styling */
        .trainer-form-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .trainer-form-card:hover {
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .form-section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #1a237e;
            letter-spacing: -0.3px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-label {
            color: #424242;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .input-group-text {
            background: #ede7f6;
            border: 1.5px solid #e9ecef;
            color: #3949ab;
            font-weight: 600;
        }

        .form-control {
            border: 1.5px solid #e9ecef;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 15px;
        }

        .form-control:focus {
            border-color: #3949ab;
            box-shadow: 0 0 0 3px rgba(57, 73, 171, 0.12);
            background-color: #f8f9ff;
        }

        .btn-submit {
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            color: #fff;
            font-weight: 700;
            padding: 14px 32px;
            border-radius: 12px;
            border: 0;
            box-shadow: 0 8px 20px rgba(26, 35, 126, 0.3);
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(26, 35, 126, 0.4);
            background: linear-gradient(135deg, #0d1642 0%, #283593 100%);
            color: #fff;
        }

        .btn-reset {
            background: #f5f5f5;
            color: #424242;
            font-weight: 600;
            padding: 14px 28px;
            border-radius: 12px;
            border: 1.5px solid #e0e0e0;
            transition: all 0.2s ease;
        }

        .btn-reset:hover {
            background: #eeeeee;
            border-color: #bdbdbd;
            color: #212121;
        }

        /* Info Panel */
        .info-panel {
            background: linear-gradient(135deg, #ede7f6 0%, #e8eaf6 100%);
            border-radius: 20px;
            padding: 28px;
            border: 2px solid #d1c4e9;
            border-left: 6px solid #3949ab;
        }

        .info-panel h5 {
            font-weight: 700;
            color: #1a237e;
            font-size: 18px;
            margin-bottom: 12px;
        }

        .info-panel h6 {
            font-weight: 700;
            font-size: 15px;
        }

        .info-panel li {
            font-size: 14px;
            padding: 6px 0;
            color: #424242;
        }

        .info-panel .bi-check-circle,
        .info-panel .bi-x-circle {
            font-size: 18px;
            margin-right: 10px;
            font-weight: 600;
        }

        .info-panel .text-success {
            color: #2e7d32 !important;
        }

        .info-panel .text-danger {
            color: #c62828 !important;
        }

        /* Sidebar Navigation - Clean Style */
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
            font-size: 0.9rem;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .sidebar-link i {
            font-size: 1.15rem;
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

        /* Responsive */
        @media (max-width: 768px) {
            .trainer-hero {
                padding: 32px 24px;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .trainer-sidebar {
                display: none !important;
            }

            .trainer-main {
                padding: 20px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="trainer-wrapper">
        <aside class="trainer-sidebar d-none d-lg-block">
            <span class="nav-menu-label">TRAINER MANAGEMENT</span>
            <a href="{{ route('admin.trainer.index') }}" class="sidebar-link">
                <i class="bi bi-people"></i> All Trainers
            </a>
            <a href="{{ route('admin.trainer.create') }}" class="sidebar-link">
                <i class="bi bi-person-plus"></i> Add New Trainer
            </a>

            <span class="nav-menu-label">QUICK ACCESS</span>
            <a href="#materialApprovalMenu"
                class="sidebar-link sidebar-parent {{ request()->routeIs('admin.material.*') ? 'active' : '' }}"
                data-bs-toggle="collapse" role="button"
                aria-expanded="{{ request()->routeIs('admin.material.*') ? 'true' : 'false' }}"
                aria-controls="materialApprovalMenu">
                <span><i class="bi bi-clipboard-check"></i> Material Approval</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </a>
            <div class="collapse sidebar-submenu {{ request()->routeIs('admin.material.*') ? 'show' : '' }}"
                id="materialApprovalMenu">
                <a href="{{ route('admin.material.approvals') }}"
                    class="sidebar-link {{ request()->routeIs('admin.material.approvals') ? 'active' : '' }}">
                    <i class="bi bi-hourglass-split"></i> Pending Review
                </a>
                <a href="{{ route('admin.material.approved') }}"
                    class="sidebar-link {{ request()->routeIs('admin.material.approved') ? 'active' : '' }}">
                    <i class="bi bi-check-circle"></i> Approved
                </a>
                <a href="{{ route('admin.material.rejected') }}"
                    class="sidebar-link {{ request()->routeIs('admin.material.rejected') ? 'active' : '' }}">
                    <i class="bi bi-x-circle"></i> Rejected
                </a>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.courses.index') }}" class="sidebar-link">
                <i class="bi bi-book"></i> Courses
            </a>
            <a href="{{ route('admin.events.history') }}" class="sidebar-link">
                <i class="bi bi-calendar-event"></i> Events
            </a>
        </aside>

        <main class="trainer-main">
            <!-- Hero Section -->
            <div class="trainer-hero mb-5">
                <div>
                    <span class="hero-label"><i class="bi bi-shield-check me-2"></i>Admin Panel</span>
                    <h1 class="hero-title">Edit Data Trainer</h1>
                    <p class="hero-subtitle">Perbarui informasi instruktur. Kosongkan password jika tidak ingin mengubahnya.
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-body p-4 p-md-5">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Terdapat kesalahan input!</strong>
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            {{-- PENTING: enctype="multipart/form-data" untuk upload foto --}}
                            <form action="{{ route('admin.trainer.update', $trainer) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-5">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-person-circle text-primary"></i> Informasi Pribadi
                                    </h5>

                                    {{-- Foto Profil --}}
                                    <div class="mb-4 text-center">
                                        <div class="position-relative d-inline-block">
                                            {{-- Gunakan accessor avatar_url dari Model User --}}
                                            <img src="{{ $trainer->avatar_url }}" class="rounded-circle shadow-sm mb-3"
                                                style="width: 100px; height: 100px; object-fit: cover; border: 3px solid #fff;">
                                        </div>
                                        <div>
                                            <label class="btn btn-sm btn-outline-primary rounded-pill"
                                                style="cursor: pointer;">
                                                <i class="bi bi-camera me-1"></i> Ganti Foto
                                                <input type="file" name="avatar" class="d-none" accept="image/*">
                                            </label>
                                            <div class="form-text small mt-1">Max 2MB (JPG, PNG)</div>
                                        </div>
                                    </div>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Nama Lengkap <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name', $trainer->name) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control"
                                                value="{{ old('email', $trainer->email) }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Nomor WhatsApp</label>
                                            <input type="text" name="phone" class="form-control"
                                                value="{{ old('phone', $trainer->phone) }}" placeholder="0812...">
                                        </div>

                                        {{-- INPUT BARU: Profesi --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Profesi / Jabatan</label>
                                            <input type="text" name="profession" class="form-control"
                                                value="{{ old('profession', $trainer->profession) }}"
                                                placeholder="Contoh: Senior Developer">
                                        </div>

                                        {{-- INPUT BARU: Institusi --}}
                                        <div class="col-md-6">
                                            <label class="form-label">Institusi / Perusahaan</label>
                                            <input type="text" name="institution" class="form-control"
                                                value="{{ old('institution', $trainer->institution) }}"
                                                placeholder="Contoh: PT. Maju Jaya">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-key text-primary"></i> Update Password
                                    </h5>
                                    <div class="alert alert-light border border-primary-subtle text-primary small mb-3">
                                        <i class="bi bi-info-circle me-1"></i> Kosongkan jika tidak ingin mengganti
                                        password.
                                    </div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Password Baru</label>
                                            <input type="password" name="password" class="form-control"
                                                placeholder="Minimal 6 karakter">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Konfirmasi Password</label>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                placeholder="Ulangi password baru">
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-5">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-file-text text-primary"></i> Bio & Keahlian
                                    </h5>
                                    <textarea name="bio" rows="4" class="form-control"
                                        placeholder="Ceritakan pengalaman trainer...">{{ old('bio', $trainer->bio) }}</textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                    <a href="{{ route('admin.trainer.index') }}" class="btn btn-light text-muted fw-bold">
                                        <i class="bi bi-arrow-left me-1"></i> Kembali
                                    </a>
                                    <button type="submit" class="btn btn-submit">
                                        <i class="bi bi-save me-2"></i> Simpan Perubahan
                                    </button>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <!-- Info Panel -->
                <div class="col-lg-4">
                    <div class="info-panel sticky-top" style="top: 100px;">
                        <h5><i class="bi bi-info-circle-fill me-2"></i>Panduan Edit Data</h5>

                        <h6 class="mt-4 mb-3"><i class="bi bi-shield-check text-success me-2"></i>Wajib Diisi</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check-circle text-success"></i>Nama Lengkap</li>
                            <li><i class="bi bi-check-circle text-success"></i>Email Aktif</li>
                        </ul>

                        <h6 class="mt-4 mb-3"><i class="bi bi-shield-exclamation text-warning me-2"></i>Opsional</h6>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-check-circle text-success"></i>Nomor WhatsApp</li>
                            <li><i class="bi bi-check-circle text-success"></i>Profesi & Institusi</li>
                            <li><i class="bi bi-check-circle text-success"></i>Bio & Keahlian</li>
                            <li><i class="bi bi-check-circle text-success"></i>Foto Profil</li>
                        </ul>

                        <div class="alert alert-warning mt-4 mb-0 small">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>Password:</strong> Kosongkan kolom password jika tidak ingin mengubahnya.
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection