@extends('layouts.admin')

@section('title', 'Material Approvals - Pending')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        :root {
            --admin-primary: #1e1b4b;
            --admin-secondary: #4338ca;
            --admin-bg: #f8fafc;
            --admin-card-bg: #ffffff;
            --admin-border: #e2e8f0;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;

            --status-pending-bg: #fffbeb;
            --status-pending-text: #b45309;
        }

        body {
            background-color: var(--admin-bg);
        }

        .material-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
        }

        .material-sidebar {
            width: 260px;
            background: var(--admin-card-bg);
            padding: 24px 16px;
            border-right: 1px solid var(--admin-border);
            flex-shrink: 0;
            position: sticky;
            top: 72px;
            height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .nav-menu-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--admin-text-muted);
            letter-spacing: 1px;
            margin: 24px 0 12px 16px;
            display: block;
        }

        .nav-menu-label:first-child {
            margin-top: 0;
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

        /* --- MAIN CONTENT --- */
        .material-main {
            flex-grow: 1;
            padding: 32px;
            overflow-x: hidden;
        }

        /* --- HEADER & STATS --- */
        .page-header {
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--admin-primary);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--admin-card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--admin-border);
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        }

        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-card.pending .stat-icon {
            background: #fef3c7;
            color: #d97706;
        }

        .stat-card.approved .stat-icon {
            background: #dcfce7;
            color: #166534;
        }

        .stat-card.rejected .stat-icon {
            background: #fee2e2;
            color: #991b1b;
        }

        .stat-info h3 {
            font-size: 1.8rem;
            font-weight: 800;
            margin: 0 0 4px 0;
            color: var(--admin-text-main);
            line-height: 1;
        }

        .stat-info p {
            margin: 0;
            color: var(--admin-text-muted);
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* --- TABLE CARD --- */
        .content-card {
            background: var(--admin-card-bg);
            border-radius: 20px;
            border: 1px solid var(--admin-border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .toolbar {
            padding: 20px 24px;
            border-bottom: 1px solid var(--admin-border);
            background: #fff;
            display: flex;
            gap: 16px;
            align-items: center;
            justify-content: space-between;
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.9rem;
            background: #f8fafc;
        }

        .search-box i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-box input:focus {
            border-color: var(--admin-secondary);
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.1);
        }

        .table {
            margin-bottom: 0;
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background: #f8fafc;
            color: var(--admin-text-muted);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 16px 24px;
            border-bottom: 1px solid var(--admin-border);
            white-space: nowrap;
        }

        .table td {
            padding: 20px 24px;
            vertical-align: middle;
            border-bottom: 1px solid var(--admin-border);
        }

        .table tr:hover {
            background-color: #f8fafc;
        }

        /* --- COMPONENT STYLES --- */
        .course-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .course-thumb {
            width: 80px;
            height: 60px;
            border-radius: 8px;
            object-fit: cover;
            border: 1px solid var(--admin-border);
            background: #eee;
        }

        .course-title {
            font-weight: 700;
            color: var(--admin-text-main);
            margin: 0 0 4px 0;
            font-size: 0.95rem;
        }

        .badge-cat {
            background: #e2e8f0;
            color: #475569;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.7rem;
            font-weight: 700;
        }

        .trainer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .trainer-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
        }

        .trainer-name {
            font-weight: 600;
            color: var(--admin-text-main);
            font-size: 0.9rem;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .badge-status::before {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
        }

        .badge-pending {
            background: var(--status-pending-bg);
            color: var(--status-pending-text);
        }

        .badge-pending::before {
            background: var(--status-pending-text);
        }

        .btn-action {
            background: #fff;
            border: 1px solid #cbd5e1;
            color: var(--admin-text-main);
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-action:hover {
            border-color: var(--admin-secondary);
            color: var(--admin-secondary);
            background: #f8fafc;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        @media (max-width: 992px) {
            .material-sidebar {
                display: none;
            }

            .stat-grid {
                grid-template-columns: 1fr;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }
        }
    </style>
@endsection

@section('content')
    <div class="material-wrapper">
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

        <main class="material-main">

            <div class="page-header">
                <div>
                    <h1 class="page-title">Material Approvals</h1>
                    <p class="text-muted mb-0">Review materi dari trainer sebelum tayang ke publik.</p>
                </div>
                <button class="btn btn-action" onclick="window.location.reload();">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <div class="stat-grid">
                <div class="stat-card pending" style="border: 2px solid #fbbf24;">
                    <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                    <div class="stat-info">
                        <h3>{{ $totalPending ?? 0 }}</h3>
                        <p>Menunggu Review</p>
                    </div>
                </div>
                <div class="stat-card approved">
                    <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
                    <div class="stat-info">
                        <h3>{{ $totalApproved ?? 0}}</h3>
                        <p>Total Disetujui</p>
                    </div>
                </div>
                <div class="stat-card rejected">
                    <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
                    <div class="stat-info">
                        <h3>{{ $totalRejected ?? 0 }}</h3>
                        <p>Perlu Revisi</p>
                    </div>
                </div>
            </div>

            <div class="content-card">
                <div class="toolbar">
                    <form method="GET" class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" placeholder="Cari course atau nama trainer..."
                            value="{{ request('search') }}">
                    </form>

                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.material.approved') }}" class="btn-action"
                            style="color: #166534; border-color:#bbf7d0; background:#f0fdf4;">
                            Lihat Disetujui <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Materi (Course)</th>
                                <th>Trainer</th>
                                <th>Isi Modul</th>
                                <th>Tanggal Submit</th>
                                <th>Status</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingMaterials as $material)
                                <tr>
                                    <td>
                                        <div class="course-info">
                                            <img src="{{ $material->card_thumbnail ?? 'https://via.placeholder.com/160x120/e2e8f0/64748b?text=Cover' }}"
                                                class="course-thumb" alt="Cover">
                                            <div>
                                                <h6 class="course-title">{{ Str::limit($material->name, 40) }}</h6>
                                                <span class="badge-cat"><i
                                                        class="bi bi-folder2 me-1"></i>{{ $material->category->name ?? 'Umum' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="trainer-info">
                                            <img src="{{ $material->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=Trainer' }}"
                                                class="trainer-avatar">
                                            <div>
                                                <div class="trainer-name">{{ $material->trainer?->name ?? 'Anonim' }}</div>
                                                <div style="font-size: 0.75rem; color:#64748b;">{{ $material->trainer?->email }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #334155;">{{ $material->modules_count }} File/Kuis
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #334155;">
                                            {{ $material->created_at->format('d M Y') }}</div>
                                        <div style="font-size: 0.75rem; color:#64748b;">
                                            {{ $material->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <span class="badge-status badge-pending">Review Pending</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.material.show', $material->id) }}" class="btn-action"
                                            style="background: var(--admin-primary); color:white; border:none;">
                                            Mulai Review <i class="bi bi-play-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            <h5 class="fw-bold text-dark">Antrean Kosong</h5>
                                            <p class="text-muted mb-0">Hore! Tidak ada materi yang perlu di-review saat ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pendingMaterials->hasPages())
                    <div class="p-3 border-top">
                        {{ $pendingMaterials->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection