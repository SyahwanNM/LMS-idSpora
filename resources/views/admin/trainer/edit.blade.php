@extends('layouts.admin')

@section('title', 'Edit Trainer')

@section('navbar')
    @include('partials.navbar-trainer')
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
            font-size: 40px;
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
            font-size: 17px;
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
            font-size: 17px;
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

        /* Responsive */
        @media (max-width: 768px) {
            .trainer-hero {
                padding: 32px 24px;
            }

            .hero-title {
                font-size: 29px;
            }

            .hero-subtitle {
                font-size: 16px;
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
        <!-- Sidebar Navigation -->
        <aside class="trainer-sidebar d-none d-lg-block">
            <span class="nav-menu-label">TRAINER MANAGEMENT</span>
            <a href="{{ route('admin.trainer.index') }}" class="sidebar-link">
                <i class="bi bi-people"></i> All Trainers
            </a>
            <a href="{{ route('admin.trainer.create') }}" class="sidebar-link">
                <i class="bi bi-person-plus"></i> Add New Trainer
            </a>

            <span class="nav-menu-label">QUICK ACCESS</span>
            <a href="{{ route('admin.material.approvals') }}" class="sidebar-link">
                <i class="bi bi-clipboard-check"></i> Material Approval
            </a>
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
            <!-- Hero Header -->
            <div class="trainer-hero">
                <span class="hero-label">Admin Panel</span>
                <h1 class="hero-title">Edit Data Trainer</h1>
                <p class="hero-subtitle">
                    Perbarui informasi trainer <strong>{{ $trainer->name }}</strong>. Kosongkan password jika tidak ingin
                    mengubahnya.
                </p>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card trainer-form-card">
                        <div class="card-body p-5">
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4"
                                    role="alert">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>Terdapat kesalahan!</strong> Silakan periksa form kembali.
                                    <ul class="mb-0 mt-2">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif

                            <form action="{{ route('admin.trainer.update', $trainer) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Personal Information Section -->
                                <div class="mb-5">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-person-circle" style="color: #3949ab;"></i>
                                        Informasi Pribadi
                                    </h5>

                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label">Nama Lengkap <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                                <input type="text" name="name"
                                                    class="form-control @error('name') is-invalid @enderror"
                                                    placeholder="Contoh: Budi Santoso"
                                                    value="{{ old('name', $trainer->name) }}" required>
                                            </div>
                                            @error('name')<div class="text-danger small mt-2"><i
                                            class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Credentials Section -->
                                <div class="mb-5">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-lock-circle" style="color: #3949ab;"></i>
                                        Kredensial Akun
                                    </h5>

                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label">Alamat Email <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                                <input type="email" name="email"
                                                    class="form-control @error('email') is-invalid @enderror"
                                                    placeholder="budi@example.com"
                                                    value="{{ old('email', $trainer->email) }}" required>
                                            </div>
                                            @error('email')<div class="text-danger small mt-2"><i
                                            class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Nomor WhatsApp</label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                                <input type="text" name="phone"
                                                    class="form-control @error('phone') is-invalid @enderror"
                                                    placeholder="+628123456789" value="{{ old('phone', $trainer->phone) }}">
                                            </div>
                                            @error('phone')<div class="text-danger small mt-2"><i
                                            class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Password Baru <small class="text-muted">(Kosongkan
                                                    jika tidak diubah)</small></label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                                <input type="password" name="password" id="passwordInput"
                                                    class="form-control @error('password') is-invalid @enderror"
                                                    placeholder="Minimal 6 karakter">
                                            </div>
                                            @error('password')<div class="text-danger small mt-2"><i
                                            class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Konfirmasi Password Baru</label>
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-text"><i class="bi bi-check-circle"></i></span>
                                                <input type="password" name="password_confirmation"
                                                    id="passwordConfirmInput" class="form-control"
                                                    placeholder="Ketik ulang password">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bio Section -->
                                <div class="mb-5">
                                    <h5 class="form-section-title">
                                        <i class="bi bi-file-text" style="color: #3949ab;"></i>
                                        Informasi Tambahan
                                    </h5>

                                    <div class="row g-4">
                                        <div class="col-md-12">
                                            <label class="form-label">Bio / Deskripsi</label>
                                            <textarea name="bio" rows="4"
                                                class="form-control @error('bio') is-invalid @enderror"
                                                placeholder="Deskripsi singkat tentang trainer (keahlian, pengalaman, sertifikasi, dll)">{{ old('bio', $trainer->bio) }}</textarea>
                                            @error('bio')<div class="text-danger small mt-2"><i
                                            class="bi bi-exclamation-circle me-1"></i>{{ $message }}</div>@enderror
                                            <small class="text-muted mt-2">Misalnya: Instruktur UI/UX Design dengan 10+
                                                tahun pengalaman</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="d-flex justify-content-between align-items-center pt-4 border-top">
                                    <a href="{{ route('admin.trainer.index') }}" class="btn btn-reset">
                                        <i class="bi bi-arrow-left me-2"></i>Kembali
                                    </a>
                                    <div class="d-flex gap-3">
                                        <a href="{{ route('admin.trainer.show', $trainer) }}" class="btn btn-reset">
                                            <i class="bi bi-eye me-2"></i>Lihat Detail
                                        </a>
                                        <button type="submit" class="btn btn-submit">
                                            <i class="bi bi-check-circle me-2"></i>Simpan Perubahan
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Info Panel -->
                <div class="col-xl-4">
                    <div class="info-panel sticky-top" style="top: 100px;">
                        <h5>
                            <i class="bi bi-info-circle" style="color: #3949ab;"></i>
                            Tips Edit Data Trainer
                        </h5>

                        <div class="alert alert-warning border-0 mt-3 mb-3" style="background: #fff3cd;">
                            <i class="bi bi-exclamation-triangle-fill me-2" style="color: #856404;"></i>
                            <small><strong>Perhatian!</strong> Perubahan email akan mengubah kredensial login
                                trainer.</small>
                        </div>

                        <h6 class="text-success mt-3 mb-3">
                            <i class="bi bi-check-circle me-2" style="color: #2e7d32;"></i>
                            Yang Bisa Diubah:
                        </h6>
                        <ul class="list-unstyled mb-4">
                            <li><i class="bi bi-check-circle text-success me-2"></i>Nama Lengkap</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Email & Nomor HP</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Password Login</li>
                            <li><i class="bi bi-check-circle text-success me-2"></i>Bio / Keahlian</li>
                        </ul>

                        <h6 class="text-info mb-3">
                            <i class="bi bi-info-circle me-2" style="color: #0288d1;"></i>
                            Info Trainer:
                        </h6>
                        <ul class="list-unstyled">
                            <li class="mb-2"><small><strong>Bergabung:</strong>
                                    {{ $trainer->created_at->format('d M Y') }}</small></li>
                            <li class="mb-2"><small><strong>Total Kelas:</strong>
                                    {{ $trainer->courses_as_trainer_count ?? 0 }}</small></li>
                            <li class="mb-2"><small><strong>Total Event:</strong>
                                    {{ $trainer->events_as_trainer_count ?? 0 }}</small></li>
                        </ul>
                    </div>
                </div>
        </main>
    </div>
@endsection