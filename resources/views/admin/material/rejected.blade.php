@extends('layouts.admin')

@section('title', 'Rejected Materials')

@section('styles')
    <style>
        /* Custom Navbar */
        .material-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(211, 47, 47, 0.1);
            padding: 16px 32px;
            box-shadow: 0 2px 12px rgba(211, 47, 47, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .material-navbar h4 {
            font-weight: 800;
            color: #d32f2f;
            margin: 0;
            font-size: 1.5rem;
        }

        .material-navbar .breadcrumb {
            margin: 0;
            background: transparent;
            padding: 0;
            font-size: 0.85rem;
        }

        /* Main Layout */
        .material-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
        }

        .material-sidebar {
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

        .material-main {
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
            background-color: #ffebee;
            color: #d32f2f;
        }

        .sidebar-link:hover i {
            color: #d32f2f;
        }

        .sidebar-link.active {
            background-color: #d32f2f;
            color: #fff;
        }

        .sidebar-link.active i {
            color: #fff;
        }

        /* Content Card */
        .content-card {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid #f0f0f0;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f5f5f5;
        }

        .content-header h5 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #212529;
            margin: 0;
        }

        /* Toolbar */
        .toolbar {
            display: flex;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .search-box input:focus {
            border-color: #d32f2f;
            outline: none;
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #adb5bd;
        }

        /* Table Styles */
        .materials-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .materials-table thead th {
            background: #f8f9fa;
            color: #495057;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
        }

        .materials-table tbody td {
            padding: 20px 16px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }

        .materials-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .materials-table tbody tr:hover {
            background-color: #fff8f8;
        }

        /* Material Info */
        .material-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .material-thumbnail {
            width: 80px;
            height: 60px;
            border-radius: 10px;
            object-fit: cover;
            border: 2px solid #f0f0f0;
        }

        .material-details h6 {
            font-size: 0.95rem;
            font-weight: 700;
            color: #212529;
            margin: 0 0 6px 0;
        }

        .material-category {
            display: inline-block;
            padding: 4px 10px;
            background: #f8f9fa;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6c757d;
        }

        /* Trainer Info */
        .trainer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .trainer-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid #f0f0f0;
        }

        .trainer-name {
            font-weight: 600;
            color: #212529;
            font-size: 0.9rem;
        }

        /* Status Badge */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-rejected {
            background: #f8d7da;
            color: #721c24;
        }

        /* Rejection Reason */
        .rejection-reason {
            font-size: 0.85rem;
            color: #dc3545;
            line-height: 1.6;
            background: #fff5f5;
            padding: 12px;
            border-radius: 8px;
            border-left: 3px solid #dc3545;
            margin-top: 8px;
        }

        /* Module Count Badge */
        .module-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #e3f2fd;
            color: #1565c0;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
        }

        /* Action Button */
        .btn-view {
            background: linear-gradient(135deg, #d32f2f 0%, #f44336 100%);
            color: #fff;
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.85rem;
            border: 0;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-view:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(211, 47, 47, 0.3);
            color: #fff;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }

        .empty-state h5 {
            color: #6c757d;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #adb5bd;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .material-sidebar {
                display: none;
            }

            .material-main {
                padding: 20px;
            }

            .toolbar {
                flex-direction: column;
            }

            .search-box {
                min-width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Custom Navbar -->
    <nav class="material-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>❌ Rejected Materials</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.material.approvals') }}">Material Approval</a>
                        </li>
                        <li class="breadcrumb-item active">Rejected</li>
                    </ol>
                </nav>
            </div>
        </div>
    </nav>

    <div class="material-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="material-sidebar d-none d-lg-block">
            <span class="nav-menu-label">APPROVAL STATUS</span>
            <a href="{{ route('admin.material.approvals') }}" class="sidebar-link">
                <i class="bi bi-hourglass-split"></i> Pending Review
            </a>
            <a href="{{ route('admin.material.approved') }}" class="sidebar-link">
                <i class="bi bi-check-circle"></i> Approved
            </a>
            <a href="{{ route('admin.material.rejected') }}" class="sidebar-link active">
                <i class="bi bi-x-circle"></i> Rejected
            </a>

            <span class="nav-menu-label">QUICK ACCESS</span>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.trainer.index') }}" class="sidebar-link">
                <i class="bi bi-people"></i> Trainers
            </a>
            <a href="{{ route('admin.courses.index') }}" class="sidebar-link">
                <i class="bi bi-book"></i> All Courses
            </a>
        </aside>

        <main class="material-main">
            <!-- Main Content Card -->
            <div class="content-card">
                <div class="content-header">
                    <h5>🔴 Materi yang Ditolak / Perlu Revisi</h5>
                    <span class="badge bg-danger" style="padding: 10px 16px; font-size: 0.9rem;">
                        {{ $rejectedMaterials->total() }} Materi
                    </span>
                </div>

                <!-- Toolbar -->
                <div class="toolbar">
                    <form method="GET" class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" placeholder="Cari judul materi atau nama trainer..."
                            value="{{ request('search') }}">
                    </form>

                    @if(request('search'))
                        <a href="{{ route('admin.material.rejected') }}" class="btn btn-outline-secondary"
                            style="padding: 12px 20px; border-radius: 12px; font-weight: 600;">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </a>
                    @endif
                </div>

                <!-- Materials Table -->
                @if($rejectedMaterials->count() > 0)
                    <div class="table-responsive">
                        <table class="materials-table">
                            <thead>
                                <tr>
                                    <th>Materi</th>
                                    <th>Trainer</th>
                                    <th>Ditolak</th>
                                    <th>Alasan Penolakan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rejectedMaterials as $material)
                                    <tr>
                                        <td>
                                            <div class="material-info">
                                                <img src="{{ $material->card_thumbnail ?? 'https://via.placeholder.com/160x120/e3f2fd/1565c0?text=Course' }}"
                                                    alt="{{ $material->name }}" class="material-thumbnail">
                                                <div class="material-details">
                                                    <h6>{{ Str::limit($material->name, 50) }}</h6>
                                                    <span class="material-category">
                                                        <i class="bi bi-folder me-1"></i>
                                                        {{ $material->category->name ?? 'Uncategorized' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="trainer-info">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($material->trainer->name ?? 'Unknown') }}&background=d32f2f&color=fff&bold=true&size=80"
                                                    alt="{{ $material->trainer->name ?? 'Unknown' }}" class="trainer-avatar">
                                                <div>
                                                    <div class="trainer-name">{{ $material->trainer->name ?? 'Unknown' }}</div>
                                                    <small class="text-muted">{{ $material->trainer->email ?? '-' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #495057;">
                                                {{ $material->rejected_at ? $material->rejected_at->format('d M Y') : '-' }}
                                            </div>
                                            <small class="text-muted">
                                                {{ $material->rejected_at ? $material->rejected_at->diffForHumans() : '' }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($material->rejection_reason)
                                                <div class="rejection-reason">
                                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                                    {{ Str::limit($material->rejection_reason, 100) }}
                                                </div>
                                            @else
                                                <em class="text-muted">Tidak ada catatan</em>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="status-badge status-rejected">
                                                ❌ Rejected
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.material.show', $material) }}" class="btn-view">
                                                <i class="bi bi-eye-fill"></i> Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $rejectedMaterials->links() }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>Tidak Ada Materi Ditolak</h5>
                        <p>Semua materi sudah disetujui atau sedang dalam review.</p>
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection