@extends('layouts.admin')

@section('title', 'Material Approvals - Trainer Management')

@section('navbar')
    @include('partials.navbar-trainer')
@endsection

@section('styles')
    <style>
        /* === MENGGUNAKAN CSS YANG SAMA DENGAN DASHBOARD UTAMA === */

        /* Trainer Hero Section */
        .trainer-hero {
            background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
            border-radius: 24px;
            padding: 32px;
            color: #fff;
            margin-bottom: 24px;
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
        }

        .hero-title {
            font-size: 1.85rem;
            font-weight: 800;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .hero-subtitle {
            color: rgba(255, 255, 255, 0.75);
            font-size: 1rem;
            margin-bottom: 0;
        }

        /* Table Card & Header */
        .trainer-table-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .table-header-row {
            background: linear-gradient(to right, #f8f9ff 0%, #f5f7ff 100%);
            border-bottom: 2px solid #e3f2fd;
        }

        .table-header-row th {
            color: #1a237e;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            padding: 14px;
        }

        .table tbody tr {
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9ff;
            box-shadow: inset 0 0 10px rgba(57, 73, 171, 0.05);
        }

        .table tbody td {
            padding: 14px;
            vertical-align: middle;
        }

        /* Statistics Cards */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 20px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            border-color: #3949ab;
            box-shadow: 0 4px 16px rgba(57, 73, 171, 0.1);
            transform: translateY(-2px);
        }

        .stat-card .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 14px;
        }

        .stat-card .stat-value {
            font-size: 1.7rem;
            font-weight: 700;
            color: #1a237e;
            line-height: 1;
            margin-bottom: 6px;
        }

        .stat-card .stat-label {
            font-size: 0.9rem;
            color: #6c757d;
            font-weight: 500;
        }

        /* CSS TAMBAHAN UNTUK MATERIAL APPROVALS */
        .stat-card.stat-warning .stat-icon {
            background: linear-gradient(135deg, #f57f17 0%, #ffb300 100%);
            color: #fff;
        }

        .stat-card.stat-danger .stat-icon {
            background: linear-gradient(135deg, #c62828 0%, #e53935 100%);
            color: #fff;
        }

        /* Filter Dropdown & Search */
        .filter-select {
            border-radius: 10px;
            border: 1.5px solid #e9ecef;
            padding: 8px 12px;
            font-size: 0.9rem;
            min-width: 180px;
        }

        .filter-select:focus {
            border-color: #3949ab;
            box-shadow: 0 0 0 0.2rem rgba(57, 73, 171, 0.12);
        }

        .search-input {
            border-radius: 12px;
            border: 1.5px solid #e9ecef;
            padding: 10px 16px;
            font-size: 0.9rem;
        }

        .search-input:focus {
            border-color: #3949ab;
            box-shadow: 0 0 0 0.2rem rgba(57, 73, 171, 0.12);
            background-color: #f8f9ff;
        }

        /* Status Badge */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .status-badge::before {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
        }

        .status-approved {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-approved::before {
            background: #2e7d32;
        }

        .status-pending {
            background: #fff8e1;
            color: #f57f17;
        }

        .status-pending::before {
            background: #f57f17;
        }

        .status-revision {
            background: #fce4ec;
            color: #c62828;
        }

        .status-revision::before {
            background: #c62828;
        }

        /* Type Badges */
        .badge-type {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            border: 1px solid #e9ecef;
            color: #495057;
            background: #f8f9fa;
        }

        /* Action Buttons */
        .btn-action {
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            border: 1.5px solid #e9ecef;
            font-weight: 600;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-action-review {
            color: #fff;
            background: linear-gradient(135deg, #3949ab 0%, #5c6bc0 100%);
            border: none;
        }

        .btn-action-review:hover {
            background: linear-gradient(135deg, #283593 0%, #3949ab 100%);
            color: #fff;
        }

        .btn-action-view {
            color: #1976d2;
            background-color: #e3f2fd;
        }

        /* Empty State & Sidebar (Sama persis) */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
        }

        .empty-state i {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 20px;
        }

        .trainer-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);

            

        }

        .trainer-sidebar {
            width: 220px;
            background: #fff;
            padding: 16px 10px;
            border-right: 1px solid #eee;
            flex-shrink: 0;
            position: sticky;
            top: 72px;
            height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .trainer-main {
            flex-grow: 1;
            padding: 24px;
            background-color: #F8F9FA;
        }

        .nav-menu-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 1px;
            margin-bottom: 8px;
            margin-top: 16px;
            display: block;
            padding-left: 12px;
        }

        .nav-menu-label:first-child {
            margin-top: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 9px 12px;
            color: #1e293b;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 0.86rem;
            transition: all 0.2s ease;
            gap: 10px;
        }

        .sidebar-link i {
            font-size: 1rem;
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

        @media (max-width: 768px) {
            .trainer-hero {
                padding: 24px;
            }

            .hero-title {
                font-size: 1.5rem;
            }

            .hero-subtitle {
                font-size: 0.9rem;
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
            <a href="{{ route('admin.material.approvals') }}" class="sidebar-link active">
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
            <div class="trainer-hero mb-5">
                <div class="hero-header d-flex justify-content-between align-items-start">
                    <div>
                        <h1 class="hero-title">
                            <i class="bi bi-clipboard-check-fill me-3"></i>Material Approvals
                        </h1>
                        <p class="hero-subtitle">Validasi dan pantau kualitas materi yang diunggah oleh trainer sebelum
                            dipublikasikan ke peserta.</p>
                    </div>
                    <button class="btn btn-light rounded-3 shadow-sm" style="font-weight: 600; padding: 10px 20px;"
                        onclick="window.location.reload();">
                        <i class="bi bi-arrow-clockwise me-2"></i>Refresh Data
                    </button>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Berhasil!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="stat-card stat-warning">
                        <div class="stat-icon">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="stat-value">{{ $totalPending ?? 0 }}</div>
                        <div class="stat-label">Pending Review</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card stat-success">
                        <div class="stat-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="stat-value">{{ $approvedTodayCount ?? 0}}</div>
                        <div class="stat-label">Disetujui Hari Ini</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card stat-danger">
                        <div class="stat-icon">
                            <i class="bi bi-x-circle-fill"></i>
                        </div>
                        <div class="stat-value">{{ $revisionCount ?? 2 }}</div>
                        <div class="stat-label">Perlu Direvisi</div>
                    </div>
                </div>
            </div>

            <div class="row mb-4 g-3">
                <div class="col-lg-5">
                    <form action="{{ route('admin.material.approvals') }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="status" value="{{ request('status') }}">
                        <div class="flex-grow-1">
                            <input type="text" name="search" class="form-control search-input"
                                placeholder="🔍 Cari judul materi atau nama trainer..." value="{{ request('search') }}">
                        </div>
                        <button type="submit" class="btn btn-primary rounded-3 px-4"
                            style="background: #3949ab; border: none; font-weight: 600;">
                            Cari
                        </button>
                    </form>
                </div>
                <div class="col-lg-4">
                    <form action="{{ route('admin.material.approvals') }}" method="GET" class="d-flex gap-2">
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        <select name="status" class="form-select filter-select" onchange="this.form.submit()">
                            <option value="">📋 Semua Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending Review
                            </option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Disetujui
                            </option>
                            <option value="revision" {{ request('status') == 'revision' ? 'selected' : '' }}>❌ Perlu Revisi
                            </option>
                        </select>
                    </form>
                </div>
                <div class="col-lg-3">
                    <div class="text-end">
                        <small class="text-muted d-block mb-1">Total Antrean</small>
                        <strong style="color: #1a237e; font-size: 1.1rem;">{{ $pendingMaterials->total() ?? 19 }}
                            Materi</strong>
                    </div>
                </div>
            </div>

            <div class="card trainer-table-card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-header-row">
                            <tr>
                                <th class="ps-4" style="min-width: 250px;">Detail Materi</th>
                                <th style="min-width: 180px;">Trainer</th>
                                <th class="text-center" style="width: 120px;">Total Modul</th>
                                <th style="width: 150px;">Tanggal Upload</th>
                                <th class="text-center" style="width: 140px;">Status</th>
                                <th class="text-center pe-4" style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingMaterials as $material)
                                <tr>
                                    <td class="ps-4">
                                        <h6 class="mb-1 fw-bold" style="color: #1a237e;">{{ $material->name }}</h6>
                                        <small class="text-muted">Kategori: {{ $material->category->name ?? 'Umum' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="{{ 'https://ui-avatars.com/api/?name=' . urlencode($material->trainer->name ?? 'Trainer') . '&background=3949ab&color=fff&bold=true' }}"
                                                class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
                                            <span class="fw-semibold text-dark small"
                                                style="color: #424242;">{{ $material->trainer->name ?? 'Anonim' }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-type"><i class="bi bi-collection text-primary me-1"></i>
                                            {{ $material->modules_count }} Modul</span>
                                    </td>
                                    <td>
                                        <div class="small" style="color: #424242;">{{ $material->created_at->format('d M Y') }}
                                        </div>
                                        <small class="text-muted">{{ $material->created_at->format('H:i') }} WIB</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="status-badge status-pending">Pending Review</span>
                                    </td>
                                    <td class="text-center pe-4">
                                        <a href="{{ route('admin.material.show', $material->id) }}"
                                            class="btn btn-action btn-action-review shadow-sm">
                                            <i class="bi bi-search me-1"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">Belum ada antrean materi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Gunakan kode ini jika data dari controller sudah siap --}}
                {{-- @if($materials->hasPages()) --}}
                <div class="card-footer bg-light border-top" style="padding: 14px 18px;">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            Menampilkan <strong>{{ $pendingMaterials->firstItem() ?? 0 }}</strong> sampai
                            <strong>{{ $pendingMaterials->lastItem() ?? 0 }}</strong> dari
                            <strong>{{ $pendingMaterials->total() ?? 0 }}</strong> antrean materi
                        </small>
                        <div>
                            {{ $pendingMaterials->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@section('scripts')
    <script>
        // Success message auto-hide after 5 seconds
        setTimeout(function () {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
@endsection