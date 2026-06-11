@extends('layouts.admin-trainer')

@section('title', 'Tambah Trainer Baru')

@push('admin-trainer-styles')
    <style>
        /* Modern Premium SaaS Styling */
        .trainer-hero {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            border-radius: 24px;
            padding: 40px;
            color: #ffffff;
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.15);
        }

        .trainer-hero::after {
            content: '';
            position: absolute;
            top: -40%;
            right: -10%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.28) 0%, rgba(99, 102, 241, 0) 70%);
            filter: blur(20px);
            border-radius: 50%;
            z-index: 1;
        }

        .trainer-hero::before {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 250px;
            height: 250px;
            background: radial-gradient(circle, rgba(14, 165, 233, 0.2) 0%, rgba(14, 165, 233, 0) 70%);
            filter: blur(20px);
            border-radius: 50%;
            z-index: 1;
        }

        .hero-label {
            background: rgba(255, 255, 255, 0.08);
            color: #cbd5e1;
            padding: 6px 14px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            display: inline-block;
            margin-bottom: 16px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 2.2rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.8px;
            position: relative;
            z-index: 2;
        }

        .hero-subtitle {
            color: #94a3b8;
            max-width: 600px;
            line-height: 1.6;
            font-weight: 400;
            margin-bottom: 0;
            position: relative;
            z-index: 2;
            font-size: 1rem;
        }

        /* Identity Card Styling */
        .trainer-identity-card {
            border: 0;
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            background: #ffffff;
            position: relative;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .trainer-identity-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, #3949ab, #6366f1, #0ea5e9);
        }

        .badge-role {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #3949ab;
            background: rgba(57, 73, 171, 0.08);
            padding: 6px 12px;
            border-radius: 100px;
            display: inline-block;
        }

        /* Detail Card Styling */
        .trainer-detail-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            background: #ffffff;
            border: 1px solid #e2e8f0;
        }

        .form-section-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.3px;
            padding-bottom: 12px;
            margin-bottom: 24px;
            border-bottom: 1.5px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .form-section-title::after {
            content: '';
            position: absolute;
            bottom: -1.5px;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: #3949ab;
            border-radius: 2px;
        }

        /* Unified Input Group UX */
        .input-group-custom {
            position: relative;
            display: flex;
            align-items: center;
            width: 100%;
            background-color: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .input-group-custom:focus-within {
            border-color: #3949ab;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(57, 73, 171, 0.08), 0 4px 12px rgba(57, 73, 171, 0.04);
        }

        .input-group-custom .input-icon {
            padding: 12px 16px;
            color: #64748b;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.25s ease;
        }

        .input-group-custom:focus-within .input-icon {
            color: #3949ab;
        }

        .input-group-custom .form-control-custom {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            padding: 12px 16px 12px 0;
            font-size: 0.95rem;
            color: #1e293b;
            flex-grow: 1;
            width: 100%;
            outline: none;
        }

        .input-group-custom .btn-toggle-pass-custom {
            border: none;
            background: transparent;
            color: #64748b;
            padding: 0 16px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .input-group-custom .btn-toggle-pass-custom:hover {
            color: #3949ab;
        }

        /* Standard inputs (without icons) */
        .form-control-standard {
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 0.95rem;
            background-color: #f8fafc;
            color: #1e293b;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            width: 100%;
            outline: none;
        }

        .form-control-standard:focus {
            border-color: #3949ab;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(57, 73, 171, 0.08), 0 4px 12px rgba(57, 73, 171, 0.04);
        }

        .form-label {
            font-weight: 700;
            color: #334155;
            font-size: 0.88rem;
            margin-bottom: 8px;
        }

        /* Avatar Upload UX */
        .avatar-upload-preview-container {
            width: 110px;
            height: 110px;
            position: relative;
            flex-shrink: 0;
        }

        .avatar-upload-preview {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid #ffffff;
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.1);
            background: #f1f5f9;
        }

        .avatar-upload-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .avatar-upload-badge {
            position: absolute;
            bottom: 2px;
            right: 2px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #3949ab;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2.5px solid #ffffff;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .avatar-upload-badge:hover {
            transform: scale(1.1);
            background: #283593;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.18);
        }

        /* Button Styling */
        .btn-submit {
            background: linear-gradient(135deg, #3949ab 0%, #5c6bc0 100%);
            border: none;
            border-radius: 12px;
            padding: 12px 28px;
            font-weight: 700;
            color: #ffffff;
            font-size: 0.95rem;
            letter-spacing: 0.3px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(57, 73, 171, 0.25);
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #283593 0%, #3949ab 100%);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(57, 73, 171, 0.35);
            color: #ffffff;
        }

        .btn-reset {
            border-radius: 12px;
            border: 1.5px solid #cbd5e1;
            padding: 12px 24px;
            font-weight: 600;
            color: #475569;
            background-color: #ffffff;
            transition: all 0.2s ease;
            font-size: 0.95rem;
        }

        .btn-reset:hover {
            border-color: #94a3b8;
            background-color: #f8fafc;
            color: #1e1b4b;
        }

        /* Custom Invalid Styling */
        .input-group-custom.is-invalid,
        .form-control-standard.is-invalid {
            border-color: #ef4444 !important;
        }
        .input-group-custom.is-invalid:focus-within,
        .form-control-standard.is-invalid:focus {
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.12) !important;
        }

        /* Responsive and Mobile Robustness */
        @media (max-width: 991.98px) {
            .trainer-hero {
                padding: 30px;
                border-radius: 20px;
                margin-bottom: 24px;
            }

            .hero-title {
                font-size: 1.8rem;
            }

            .hero-subtitle {
                font-size: 0.95rem;
            }

            .trainer-identity-card {
                height: auto !important;
                margin-bottom: 12px;
            }
        }

        @media (max-width: 576px) {
            .trainer-hero {
                padding: 24px 20px;
                border-radius: 16px;
                margin-bottom: 16px;
            }

            .hero-title {
                font-size: 1.45rem;
                letter-spacing: -0.5px;
            }

            .hero-subtitle {
                font-size: 0.85rem;
                line-height: 1.5;
            }

            .trainer-identity-card .card-body,
            .trainer-detail-card .card-body {
                padding: 24px 16px !important;
            }

            /* Stack action buttons vertically on mobile */
            .action-buttons-container {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 12px;
            }

            .action-buttons-right {
                flex-direction: column !important;
                gap: 12px !important;
                width: 100%;
            }

            .action-buttons-container .btn-reset,
            .action-buttons-container .btn-submit {
                width: 100%;
                justify-content: center;
                display: flex;
                align-items: center;
                padding-top: 12px;
                padding-bottom: 12px;
            }
        }
    </style>
@endpush

@section('admin-trainer-content')
    <!-- Hero Section -->
    <div class="trainer-hero mb-4">
        <div>
            <span class="hero-label"><i class="bi bi-shield-check me-2"></i>Panel Admin</span>
            <h1 class="hero-title">Daftarkan Trainer Baru</h1>
            <p class="hero-subtitle">Tambahkan instruktur baru ke platform idSpora. Atur kredensial akses dan informasi pribadi dengan mudah.</p>
        </div>
    </div>

    <form action="{{ route('admin.trainer.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row g-4">
            <!-- Left Column: Profile & Core Identity -->
            <div class="col-xl-4 col-lg-5">
                <div class="card trainer-identity-card h-100 shadow-sm border-0">
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-between">
                        <div>
                            <!-- Badge Label -->
                            <div class="identity-header mb-4">
                                <span class="badge-role">TRAINER PROFILE</span>
                            </div>

                            <!-- Avatar Upload UI -->
                            <div class="d-flex flex-column align-items-center mb-4">
                                <div class="avatar-upload-preview-container mb-3">
                                    <div class="avatar-upload-preview">
                                        <img id="avatarPreview" src="{{ asset('aset/default-avatar.png') }}" onerror="this.src='https://ui-avatars.com/api/?name=Trainer&background=f1f5f9&color=3949ab&size=120'" alt="Avatar Preview">
                                    </div>
                                    <label for="avatarInput" class="avatar-upload-badge">
                                        <i class="bi bi-camera-fill"></i>
                                    </label>
                                    <input type="file" name="avatar" id="avatarInput" class="d-none" accept="image/*">
                                </div>
                                <h6 class="mb-1 fw-bold text-dark" id="displayTrainerName">Nama Instruktur</h6>
                                <span class="text-muted small" id="displayTrainerEmail">trainer@email.com</span>
                            </div>

                            <!-- Core Inputs -->
                            <div class="text-start mt-4">
                                <div class="mb-4">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <div class="input-group-custom @error('name') is-invalid @enderror">
                                        <span class="input-icon"><i class="bi bi-person"></i></span>
                                        <input type="text" name="name" id="nameInput"
                                            class="form-control-custom"
                                            placeholder="Contoh: Budi Santoso" value="{{ old('name') }}" required>
                                    </div>
                                    @error('name')
                                        <div class="text-danger small mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Alamat Email <span class="text-danger">*</span></label>
                                    <div class="input-group-custom @error('email') is-invalid @enderror">
                                        <span class="input-icon"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" id="emailInput"
                                            class="form-control-custom"
                                            placeholder="budi@example.com" value="{{ old('email') }}" required>
                                    </div>
                                    @error('email')
                                        <div class="text-danger small mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="identity-footer mt-4 pt-3 border-top text-start">
                            <p class="text-muted small mb-0"><i class="bi bi-info-circle me-1 text-primary"></i> Pastikan alamat email aktif untuk menerima kredensial akun.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Security, Contact, & Additional Info -->
            <div class="col-xl-8 col-lg-7">
                <div class="d-flex flex-column gap-4">
                    
                    <!-- Card 1: Security -->
                    <div class="card trainer-detail-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="form-section-title">
                                <i class="bi bi-shield-lock" style="color: #3949ab;"></i>
                                Keamanan Akun
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Password <span class="text-danger">*</span></label>
                                    <div class="input-group-custom @error('password') is-invalid @enderror">
                                        <span class="input-icon"><i class="bi bi-key"></i></span>
                                        <input type="password" name="password" id="passwordInput"
                                            class="form-control-custom"
                                            placeholder="Minimal 6 karakter" required>
                                        <button class="btn-toggle-pass-custom" type="button" id="togglePassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <div class="text-danger small mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                    <div class="input-group-custom">
                                        <span class="input-icon"><i class="bi bi-check-circle"></i></span>
                                        <input type="password" name="password_confirmation" id="passwordConfirmInput"
                                            class="form-control-custom"
                                            placeholder="Ketik ulang password" required>
                                        <button class="btn-toggle-pass-custom" type="button" id="togglePasswordConfirm">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Contact & Website -->
                    <div class="card trainer-detail-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="form-section-title">
                                <i class="bi bi-chat-left-text" style="color: #3949ab;"></i>
                                Kontak & Website
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nomor WhatsApp</label>
                                    <div class="input-group-custom @error('phone') is-invalid @enderror">
                                        <span class="input-icon"><i class="bi bi-whatsapp"></i></span>
                                        <input type="text" name="phone"
                                            class="form-control-custom"
                                            placeholder="08123456789" value="{{ old('phone') }}">
                                    </div>
                                    @error('phone')
                                        <div class="text-danger small mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Website</label>
                                    <div class="input-group-custom @error('website') is-invalid @enderror">
                                        <span class="input-icon"><i class="bi bi-globe"></i></span>
                                        <input type="text" name="website"
                                            class="form-control-custom"
                                            placeholder="https://example.com" value="{{ old('website') }}">
                                    </div>
                                    @error('website')
                                        <div class="text-danger small mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3: Additional details -->
                    <div class="card trainer-detail-card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h5 class="form-section-title">
                                <i class="bi bi-briefcase" style="color: #3949ab;"></i>
                                Profesi & Biografi
                            </h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Profesi / Jabatan</label>
                                    <input type="text" name="profession"
                                        class="form-control-standard @error('profession') is-invalid @enderror"
                                        placeholder="Contoh: Senior Developer" value="{{ old('profession') }}">
                                    @error('profession')
                                        <div class="text-danger small mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Institusi / Perusahaan</label>
                                    <input type="text" name="institution"
                                        class="form-control-standard @error('institution') is-invalid @enderror"
                                        placeholder="Contoh: PT. Maju Jaya" value="{{ old('institution') }}">
                                    @error('institution')
                                        <div class="text-danger small mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label">Bio / Deskripsi</label>
                                    <textarea name="bio" rows="4"
                                        class="form-control-standard @error('bio') is-invalid @enderror"
                                        placeholder="Deskripsi singkat tentang trainer (keahlian, pengalaman, sertifikasi, dll)">{{ old('bio') }}</textarea>
                                    @error('bio')
                                        <div class="text-danger small mt-2">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </div>
                                    @enderror
                                    <small class="text-muted mt-2 d-block">Misalnya: Instruktur UI/UX Design dengan 10+ tahun pengalaman</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-body p-3 d-flex justify-content-between align-items-center action-buttons-container">
                            <a href="{{ route('admin.trainer.index') }}" class="btn btn-reset">
                                <i class="bi bi-arrow-left me-2"></i>Kembali
                            </a>
                            <div class="d-flex gap-3 action-buttons-right">
                                <button type="reset" class="btn btn-reset">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Atur Ulang
                                </button>
                                <button type="submit" class="btn btn-submit">
                                    <i class="bi bi-check-circle me-2"></i>Simpan & Daftarkan
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
@endsection

@push('admin-trainer-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Avatar Preview
            const avatarInput = document.getElementById('avatarInput');
            const avatarPreview = document.getElementById('avatarPreview');
            if (avatarInput && avatarPreview) {
                avatarInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            avatarPreview.src = e.target.result;
                        }
                        reader.readAsDataURL(file);
                    }
                });
            }

            // Live preview data binding
            const nameInput = document.getElementById('nameInput');
            const displayTrainerName = document.getElementById('displayTrainerName');
            if (nameInput && displayTrainerName) {
                // Initialize
                displayTrainerName.textContent = nameInput.value.trim() || 'Nama Instruktur';
                nameInput.addEventListener('input', function() {
                    displayTrainerName.textContent = this.value.trim() || 'Nama Instruktur';
                });
            }

            const emailInput = document.getElementById('emailInput');
            const displayTrainerEmail = document.getElementById('displayTrainerEmail');
            if (emailInput && displayTrainerEmail) {
                // Initialize
                displayTrainerEmail.textContent = emailInput.value.trim() || 'trainer@email.com';
                emailInput.addEventListener('input', function() {
                    displayTrainerEmail.textContent = this.value.trim() || 'trainer@email.com';
                });
            }

            // Toggle Passwords
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('passwordInput');
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordInput.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('bi-eye');
                    this.querySelector('i').classList.toggle('bi-eye-slash');
                });
            }

            const togglePasswordConfirm = document.getElementById('togglePasswordConfirm');
            const passwordConfirmInput = document.getElementById('passwordConfirmInput');
            if (togglePasswordConfirm && passwordConfirmInput) {
                togglePasswordConfirm.addEventListener('click', function() {
                    const type = passwordConfirmInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    passwordConfirmInput.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('bi-eye');
                    this.querySelector('i').classList.toggle('bi-eye-slash');
                });
            }
        });
    </script>
@endpush