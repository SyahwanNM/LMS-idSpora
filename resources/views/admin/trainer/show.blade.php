@extends('layouts.admin')

@section('title', 'Detail Trainer')

@section('navbar')
    @include('partials.navbar-trainer')
@endsection

@section('styles')
    <style>
        /* Trainer Hero Section */
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

        .hero-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 4px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        }

        /* Detail Cards */
        .detail-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #e9ecef;
            margin-bottom: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .detail-card h5 {
            font-size: 18px;
            font-weight: 700;
            color: #1a237e;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .detail-row {
            display: flex;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #6c757d;
            width: 150px;
            flex-shrink: 0;
            font-size: 14px;
        }

        .detail-value {
            color: #212529;
            flex-grow: 1;
            font-size: 14px;
        }

        /* Stats Card */
        .stat-box {
            background: linear-gradient(135deg, #ede7f6 0%, #e8eaf6 100%);
            border-radius: 16px;
            padding: 24px;
            text-align: center;
            border: 2px solid #d1c4e9;
            transition: all 0.3s ease;
        }

        .stat-box:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(57, 73, 171, 0.15);
        }

        .stat-box .stat-icon {
            font-size: 40px;
            color: #3949ab;
            margin-bottom: 12px;
        }

        .stat-box .stat-number {
            font-size: 32px;
            font-weight: 700;
            color: #1a237e;
            line-height: 1;
            margin-bottom: 8px;
        }

        .stat-box .stat-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 600;
        }

        /* Action Buttons */
        .btn-action-large {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.2s ease;
        }

        .btn-edit-large {
            background: linear-gradient(135deg, #1a237e 0%, #3949ab 100%);
            color: #fff;
            border: 0;
        }

        .btn-edit-large:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(26, 35, 126, 0.3);
            color: #fff;
        }

        .btn-delete-large {
            background: #fff;
            color: #c62828;
            border: 2px solid #c62828;
        }

        .btn-delete-large:hover {
            background: #c62828;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(198, 40, 40, 0.3);
        }

        /* Sidebar Navigation */
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

            .trainer-sidebar {
                display: none !important;
            }

            .trainer-main {
                padding: 20px;
            }

            .detail-label {
                width: 120px;
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
            <!-- Hero Header with Trainer Info -->
            <div class="trainer-hero">
                <div class="d-flex align-items-center gap-4 position-relative" style="z-index: 2;">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($trainer->name) }}&background=fff&color=3949ab&bold=true&size=200"
                        class="hero-avatar" alt="{{ $trainer->name }}">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <h1 class="mb-0" style="font-size: 40px; font-weight: 800;">{{ $trainer->name }}</h1>
                            @php
                                $isActive = $trainer->created_at >= now()->subDays(30);
                            @endphp
                            <span class="badge"
                                style="background: {{ $isActive ? '#2e7d32' : '#c62828' }}; padding: 8px 16px; font-size: 14px;">
                                {{ $isActive ? '🟢 Aktif' : '🔴 Nonaktif' }}
                            </span>
                        </div>
                        <p class="mb-3" style="font-size: 18px; opacity: 0.9;">
                            <i class="bi bi-envelope-fill me-2"></i>{{ $trainer->email }}
                        </p>
                        @if($trainer->phone)
                            <p class="mb-0" style="font-size: 18px; opacity: 0.9;">
                                <i class="bi bi-telephone-fill me-2"></i>{{ $trainer->phone }}
                            </p>
                        @endif
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <a href="{{ route('admin.trainer.edit', $trainer) }}" class="btn btn-light btn-action-large">
                            <i class="bi bi-pencil-square me-2"></i>Edit Data
                        </a>
                        <form action="{{ route('admin.trainer.destroy', $trainer) }}" method="POST"
                            onsubmit="return confirm('⚠️ Apakah Anda yakin ingin menghapus trainer {{ $trainer->name }}?\n\nData yang terhapus tidak dapat dikembalikan!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-light w-100 btn-action-large">
                                <i class="bi bi-trash-fill me-2"></i>Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Statistics -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="bi bi-book-fill"></i>
                        </div>
                        <div class="stat-number">{{ $trainer->courses_as_trainer_count ?? 0 }}</div>
                        <div class="stat-label">Total Kelas</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="bi bi-calendar-event-fill"></i>
                        </div>
                        <div class="stat-number">{{ $trainer->events_as_trainer_count ?? 0 }}</div>
                        <div class="stat-label">Total Event</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-box">
                        <div class="stat-icon">
                            <i class="bi bi-calendar-check-fill"></i>
                        </div>
                        <div class="stat-number">{{ $trainer->created_at->diffInDays(now()) }}</div>
                        <div class="stat-label">Hari Bergabung</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-6">
                    <!-- Personal Information -->
                    <div class="detail-card">
                        <h5>
                            <i class="bi bi-person-circle" style="color: #3949ab;"></i>
                            Informasi Pribadi
                        </h5>
                        <div class="detail-row">
                            <div class="detail-label">Nama Lengkap</div>
                            <div class="detail-value"><strong>{{ $trainer->name }}</strong></div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">{{ $trainer->email }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">No. WhatsApp</div>
                            <div class="detail-value">{{ $trainer->phone ?? '—' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Role</div>
                            <div class="detail-value">
                                <span class="badge" style="background: #3949ab; padding: 6px 12px;">
                                    <i class="bi bi-person-badge me-1"></i>Trainer
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Bio / Skills -->
                    <div class="detail-card">
                        <h5>
                            <i class="bi bi-file-text-fill" style="color: #3949ab;"></i>
                            Bio & Keahlian
                        </h5>
                        @if($trainer->bio)
                            <p class="mb-0" style="color: #424242; line-height: 1.7;">
                                {{ $trainer->bio }}
                            </p>
                        @else
                            <p class="text-muted mb-0">
                                <i class="bi bi-info-circle me-2"></i>Belum ada deskripsi keahlian.
                            </p>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6">
                    <!-- Account Information -->
                    <div class="detail-card">
                        <h5>
                            <i class="bi bi-shield-check" style="color: #3949ab;"></i>
                            Informasi Akun
                        </h5>
                        <div class="detail-row">
                            <div class="detail-label">Bergabung Pada</div>
                            <div class="detail-value">
                                <strong>{{ $trainer->created_at->format('d F Y') }}</strong>
                                <small class="text-muted ms-2">
                                    ({{ $trainer->created_at->diffForHumans() }})
                                </small>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status Akun</div>
                            <div class="detail-value">
                                @php
                                    $isActive = $trainer->created_at >= now()->subDays(30);
                                @endphp
                                <span class="badge"
                                    style="background: {{ $isActive ? '#2e7d32' : '#c62828' }}; padding: 6px 12px;">
                                    {{ $isActive ? '🟢 Aktif' : '🔴 Nonaktif' }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Last Update</div>
                            <div class="detail-value">
                                {{ $trainer->updated_at->format('d F Y, H:i') }}
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="detail-card">
                        <h5>
                            <i class="bi bi-lightning-charge-fill" style="color: #3949ab;"></i>
                            Aksi Cepat
                        </h5>
                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.trainer.edit', $trainer) }}" class="btn btn-edit-large">
                                <i class="bi bi-pencil-square me-2"></i>Edit Data Trainer
                            </a>
                            <a href="{{ route('admin.trainer.index') }}" class="btn btn-outline-secondary btn-action-large">
                                <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar
                            </a>
                            <form action="{{ route('admin.trainer.destroy', $trainer) }}" method="POST"
                                onsubmit="return confirm('⚠️ Apakah Anda yakin ingin menghapus trainer {{ $trainer->name }}?\n\nData yang terhapus tidak dapat dikembalikan!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-delete-large w-100">
                                    <i class="bi bi-trash-fill me-2"></i>Hapus Trainer
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection