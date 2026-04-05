@extends('layouts.admin')

@section('title', 'Manage Trainers')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        /* Trainer Hero Section */
        .trainer-hero {
            background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
            border-radius: 24px;
            padding: 32px 36px;
            color: #fff;
            margin-bottom: 28px;
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

        .hero-header {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: flex-start;
            align-items: flex-start;
            gap: 12px;
            flex-wrap: nowrap;
        }

        .hero-title {
            font-size: 2.15rem;
            font-weight: 800;
            margin-bottom: 6px;
            letter-spacing: -0.6px;
            display: flex;
            align-items: center;
            line-height: 1.1;
        }

        .hero-title i {
            font-size: 1.6rem;
            line-height: 1;
            flex-shrink: 0;
        }

        .hero-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 16px;
            margin-bottom: 0;
            line-height: 1.5;
            max-width: 600px;
        }

        /* Table Card */
        .trainer-table-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        /* Table Header */
        .table-header-row {
            background: linear-gradient(to right, #f8f9ff 0%, #f5f7ff 100%);
            border-bottom: 2px solid #e3f2fd;
        }

        .table-header-row th {
            color: #1a237e;
            font-weight: 700;
            font-size: 14px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 16px;
        }

        /* Table Body */
        .table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
            box-shadow: inset 0 0 10px rgba(57, 73, 171, 0.05);
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
        }

        /* Badge Styling */
        .badge-course {
            background: #e3f2fd;
            color: #1a237e;
            border: 1.5px solid #bbdefb;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 20px;
        }

        .badge-event {
            background: #f3e5f5;
            color: #6a1b9a;
            border: 1.5px solid #e1bee7;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 20px;
        }

        /* Statistics Cards */
        .stat-card {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            gap: 16px;
            height: 100%;
        }

        .stat-card:hover {
            border-color: #3949ab;
            box-shadow: 0 8px 24px rgba(57, 73, 171, 0.12);
            transform: translateY(-4px);
        }

        .stat-card .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .stat-card.stat-primary .stat-icon {
            background: linear-gradient(135deg, #3949ab 0%, #5c6bc0 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(57, 73, 171, 0.3);
        }

        .stat-card.stat-success .stat-icon {
            background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(46, 125, 50, 0.3);
        }

        .stat-card.stat-info .stat-icon {
            background: linear-gradient(135deg, #0288d1 0%, #039be5 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(2, 136, 209, 0.3);
        }

        .stat-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 800;
            color: #1a237e;
            line-height: 1.1;
            margin-bottom: 4px;
        }

        .stat-card .stat-label {
            font-size: 14px;
            color: #64748b;
            font-weight: 600;
            line-height: 1.2;
        }

        /* Filter Dropdown */
        .filter-select {
            border-radius: 10px;
            border: 1.5px solid #e9ecef;
            padding: 10px 12px;
            font-size: 14px;
            min-width: 180px;
            height: 44px;
        }

        .filter-select:focus {
            border-color: #3949ab;
            box-shadow: 0 0 0 0.2rem rgba(57, 73, 171, 0.12);
        }

        /* Status Badge */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge.status-active {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-badge.status-active::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #2e7d32;
        }

        .status-badge.status-inactive {
            background: #fce4ec;
            color: #c62828;
        }

        .status-badge.status-inactive::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #c62828;
        }

        /* Action Buttons */
        .btn-action {
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
            border: 1.5px solid #e9ecef;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-action-view {
            color: #1976d2;
            background-color: #e3f2fd;
        }

        .btn-action-view:hover {
            background-color: #90caf9;
            color: #fff;
            border-color: #64b5f6;
        }

        .btn-action-edit {
            color: #1a237e;
            background-color: #e8eaf6;
        }

        .btn-action-edit:hover {
            background-color: #5c6bc0;
            color: #fff;
            border-color: #3949ab;
        }

        .btn-action-delete {
            color: #c62828;
            background-color: #ffebee;
        }

        .btn-action-delete:hover {
            background-color: #ef5350;
            color: #fff;
            border-color: #e53935;
        }

        /* Search Bar */
        .search-input {
            border-radius: 12px;
            border: 1.5px solid #e9ecef;
            padding: 10px 16px;
            font-size: 14px;
            height: 44px;
        }

        .search-input:focus {
            border-color: #3949ab;
            box-shadow: 0 0 0 0.2rem rgba(57, 73, 171, 0.12);
            background-color: #f8f9ff;
        }

        /* Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 64px;
            color: #ccc;
            margin-bottom: 20px;
        }

        /* Sidebar Navigation - Clean Style */
        .trainer-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
            overflow-x: hidden;
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
            min-width: 0;
            padding: 32px;
            background-color: #F8F9FA;
            overflow-x: auto;
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

            .hero-header {
                align-items: stretch;
                flex-wrap: wrap;
            }

            .hero-title {
                font-size: 1.75rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .table {
                font-size: 14px;
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
        @include('admin.trainer._sidebar')
        @include('admin.trainer._top-text-color')

        <!-- Main Content -->
        <main class="trainer-main">
            <!-- Hero Section -->
            <div class="trainer-hero mb-5">
                <div class="hero-header">
                    <div>
                        <h1 class="hero-title">
                            <i class="bi bi-person-badge-fill me-3"></i>Trainer Management
                        </h1>
                        <p class="hero-subtitle">Kelola akun instruktur, monitor penugasan kelas, dan track performa trainer
                            secara real-time.</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card stat-primary">
                        <div class="stat-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $totalTrainers }}</div>
                            <div class="stat-label">Total Trainer</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card stat-success">
                        <div class="stat-icon">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $activeTrainers }}</div>
                            <div class="stat-label">Trainer Aktif (30 Hari)</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card stat-info">
                        <div class="stat-icon">
                            <i class="bi bi-easel-fill"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $teachingTrainers }}</div>
                            <div class="stat-label">Sedang Mengajar</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="row mb-4 g-3">
                <div class="col-lg-5">
                    <form action="{{ route('admin.trainer.index') }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                        <div class="flex-grow-1">
                            <input type="text" name="search" class="form-control search-input"
                                placeholder="Cari nama, email, atau nomor HP..." value="{{ request('search') }}">
                        </div>
                        <button type="submit" class="btn btn-primary rounded-3 px-4"
                            style="background: #3949ab; border: none; font-weight: 600; height: 44px;">
                            <i class="bi bi-search me-1"></i>Cari
                        </button>
                        @if(request('search') || request('sort'))
                            <a href="{{ route('admin.trainer.index') }}" class="btn btn-outline-secondary rounded-3 px-3"
                                style="height: 44px; display: flex; align-items: center;" title="Reset Filter">
                                <i class="bi bi-x-circle"></i>
                            </a>
                        @endif
                    </form>
                </div>
                <div class="col-lg-4">
                    <form action="{{ route('admin.trainer.index') }}" method="GET">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="sort" class="form-select filter-select" onchange="this.form.submit()">
                            <option value="">Urutkan Berdasarkan...</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru Bergabung
                            </option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama Bergabung
                            </option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)
                            </option>
                        </select>
                    </form>
                </div>
                <div class="col-lg-3">
                    <div class="text-end">
                        <small class="text-muted d-block mb-1">Total Data</small>
                        <strong style="color: #1a237e; font-size: 18px;">{{ $trainers->total() }} Trainer</strong>
                    </div>
                </div>
            </div>

            <!-- Table Card -->
            <div class="card trainer-table-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-header-row">
                            <tr>
                                <th class="ps-4" style="min-width: 220px;">Trainer</th>
                                <th style="min-width: 200px;">Kontak</th>
                                <th style="min-width: 180px;">Keahlian</th>
                                <th class="text-center" style="width: 100px;">Status</th>
                                <th class="text-center" style="width: 100px;">Kelas</th>
                                <th class="text-center" style="width: 100px;">Sesi</th>
                                <th style="width: 130px;">Bergabung</th>
                                <th class="text-center pe-4" style="width: 160px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trainers as $trainer)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <img src="https://ui-avatars.com/api/?name={{ urlencode($trainer->name) }}&background=3949ab&color=fff&bold=true"
                                                class="rounded-circle" style="width: 44px; height: 44px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold" style="color: #1a237e;">{{ $trainer->name }}</h6>
                                                <small class="text-muted">Instruktur</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small mb-1">
                                            <i class="bi bi-envelope text-muted me-1"></i>
                                            <span style="color: #424242;">{{ $trainer->email }}</span>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="bi bi-telephone text-muted me-1"></i>
                                            {{ $trainer->phone ?? '—' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="small" style="color: #424242;">
                                            @if($trainer->bio)
                                                {{ Str::limit($trainer->bio, 50) }}
                                            @else
                                                <span class="text-muted">Belum ada keahlian</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $isActive = $trainer->created_at >= now()->subDays(30);
                                        @endphp
                                        <span class="status-badge {{ $isActive ? 'status-active' : 'status-inactive' }}">
                                            {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-course">
                                            {{ $trainer->courses_as_trainer_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-event">
                                            {{ $trainer->events_as_trainer_count ?? 0 }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $trainer->created_at->format('d M Y') }}
                                        </small>
                                    </td>
                                    <td class="text-center pe-4">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.trainer.show', $trainer) }}"
                                                class="btn btn-action btn-action-view" title="Lihat Detail">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            <a href="{{ route('admin.trainer.edit', $trainer) }}"
                                                class="btn btn-action btn-action-edit" title="Edit Trainer">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="{{ route('admin.trainer.destroy', $trainer) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus trainer {{ $trainer->name }}?\n\nData yang terhapus tidak dapat dikembalikan!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-action btn-action-delete"
                                                    title="Hapus Trainer">
                                                    <i class="bi bi-trash-fill"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            <h6 class="fw-bold mt-3" style="color: #1a237e;">Belum Ada Data Trainer</h6>
                                            <p class="text-muted mb-0">Silakan <a href="{{ route('admin.trainer.create') }}"
                                                    style="color: #3949ab; font-weight: 600;">tambah trainer baru</a> untuk
                                                mulai menugaskan kelas.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Footer with Pagination -->
                @if($trainers->hasPages())
                    <div class="card-footer bg-light border-top p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                Menampilkan <strong>{{ $trainers->firstItem() }}</strong> sampai
                                <strong>{{ $trainers->lastItem() }}</strong> dari <strong>{{ $trainers->total() }}</strong>
                                trainer
                            </small>
                            <div>
                                {{ $trainers->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection

@section('scripts')
    <script>
        // Success message     auto-hide after 5 seconds
        setTimeout(function () {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endsection