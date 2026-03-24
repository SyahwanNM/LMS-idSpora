@extends('layouts.admin')

@section('title', 'Rejected Materials')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        /* === GLOBAL ADMIN STYLES === */
        :root {
            --admin-primary: #1e1b4b;
            --admin-secondary: #4338ca;
            --admin-bg: #f8fafc;
            --admin-card-bg: #ffffff;
            --admin-border: #e2e8f0;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;
        }

        body {
            background-color: var(--admin-bg);
        }

        .material-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
        }

        /* --- SIDEBAR --- */
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

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            color: var(--admin-text-main);
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .sidebar-link i {
            font-size: 1.1rem;
            color: var(--admin-text-muted);
        }

        .sidebar-link:hover {
            background-color: #f1f5f9;
            color: var(--admin-secondary);
        }

        .sidebar-link.active {
            background-color: var(--admin-primary);
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

        /* --- HEADER --- */
        .page-header {
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: #991b1b;
            /* Warna merah khusus rejected */
            margin-bottom: 8px;
            letter-spacing: -0.5px;
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
            border-color: #991b1b;
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(153, 27, 27, 0.1);
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

        /* Status khusus Rejected */
        .badge-rejected-status {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-rejected-status::before {
            background: #991b1b;
        }

        /* Catatan Revisi */
        .rejection-note {
            background: #fff5f5;
            border-left: 3px solid #ef4444;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.8rem;
            color: #7f1d1d;
            margin-top: 8px;
            display: inline-block;
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

        .btn-back-header {
            background: #fff;
            border: 1px solid #cbd5e1;
            color: var(--admin-text-main);
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-back-header:hover {
            background: #f1f5f9;
            color: var(--admin-primary);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 16px;
            display: block;
        }

        @media (max-width: 992px) {
            .material-sidebar {
                display: none;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="material-wrapper">
        <aside class="material-sidebar d-none d-lg-block">
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
            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title"><i class="bi bi-exclamation-octagon-fill me-2"></i>Perlu Revisi</h1>
                    <p class="text-muted mb-0">Materi yang dikembalikan ke trainer karena tidak sesuai standar.</p>
                </div>
                <a href="{{ route('admin.material.approvals') }}" class="btn-back-header">
                    <i class="bi bi-arrow-left"></i> Kembali ke Antrean
                </a>
            </div>

            <div class="content-card">
                <div class="toolbar">
                    @php
                        $activeDeadlineFilter = $deadlineFilter ?? 'all';
                        $activeSearch = trim((string) request('search', ''));
                        $hasActiveFilter = ($activeDeadlineFilter !== 'all') || ($activeSearch !== '');
                        $deadlineLabelMap = [
                            'all' => 'Semua Deadline',
                            'overdue' => 'Overdue',
                            'on_time' => 'Tepat Waktu',
                            'no_deadline' => 'Tanpa Deadline',
                        ];
                    @endphp
                    <form method="GET" class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" placeholder="Cari materi yang direvisi..."
                            value="{{ request('search') }}">
                        <select name="deadline_filter" onchange="this.form.submit()"
                            style="border:1px solid #e2e8f0;border-radius:8px;padding:6px 10px;font-size:.85rem;">
                            <option value="all" {{ ($deadlineFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Deadline
                            </option>
                            <option value="overdue" {{ ($deadlineFilter ?? 'all') === 'overdue' ? 'selected' : '' }}>Overdue
                            </option>
                            <option value="on_time" {{ ($deadlineFilter ?? 'all') === 'on_time' ? 'selected' : '' }}>Tepat
                                Waktu</option>
                            <option value="no_deadline" {{ ($deadlineFilter ?? 'all') === 'no_deadline' ? 'selected' : '' }}>
                                Tanpa Deadline</option>
                        </select>
                    </form>

                    @if($hasActiveFilter)
                        <span class="btn-action" style="cursor:default;color:#334155;border-color:#cbd5e1;background:#f8fafc;">
                            Filter:
                            {{ $deadlineLabelMap[$activeDeadlineFilter] ?? 'Semua Deadline' }}{{ $activeSearch !== '' ? ' • Pencarian aktif' : '' }}
                        </span>
                        <a href="{{ route('admin.material.rejected') }}" class="btn-action">
                            <i class="bi bi-x-circle"></i> Reset
                        </a>
                    @endif
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Materi (Course)</th>
                                <th>Trainer</th>
                                <th>Alasan Penolakan</th>
                                <th>Tanggal Ditolak</th>
                                <th>Status</th>
                                <th>Monitoring Deadline</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rejectedMaterials as $material)
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
                                    <td style="max-width: 250px;">
                                        <div class="rejection-note">
                                            <i class="bi bi-chat-text-fill me-1"></i>
                                            {{ Str::limit($material->rejection_reason, 60) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #334155;">
                                            {{ $material->rejected_at ? $material->rejected_at->format('d M Y') : '-' }}
                                        </div>
                                        <div style="font-size: 0.75rem; color:#64748b;">
                                            {{ $material->rejected_at ? $material->rejected_at->diffForHumans() : '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-status badge-rejected-status">Revisi</span>
                                    </td>
                                    <td>
                                        @php $monitor = $deadlineMonitoring[$material->id] ?? null; @endphp
                                        <div style="font-weight: 600; color: #334155;">
                                            {{ $monitor['deadline_text'] ?? 'Belum ditentukan' }}
                                        </div>
                                        <div
                                            style="font-size: 0.75rem; color: {{ ($monitor['status'] ?? '') === 'late' ? '#b91c1c' : '#64748b' }};">
                                            {{ $monitor['status_text'] ?? 'Tanpa deadline' }}
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.material.show', $material->id) }}" class="btn-action">
                                            Cek <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">
                                        <div class="empty-state">
                                            <i class="bi bi-inbox"></i>
                                            <h5 class="fw-bold text-dark">Tidak Ada Revisi</h5>
                                            <p class="text-muted mb-0">Semua materi sudah disetujui atau sedang dalam antrean
                                                review.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($rejectedMaterials->hasPages())
                    <div class="p-3 border-top">
                        {{ $rejectedMaterials->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection