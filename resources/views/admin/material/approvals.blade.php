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

        html {
            scrollbar-gutter: stable;
        }

        .material-wrapper {
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
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            background: linear-gradient(135deg, #1a237e 0%, #283593 55%, #3949ab 100%);
            border-radius: 20px;
            padding: 24px 26px;
            color: #fff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 14px 30px rgba(26, 35, 126, 0.2);
        }

        .page-header::after {
            content: '';
            position: absolute;
            right: -80px;
            top: -80px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.22) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
        }

        .page-header>div,
        .page-header>button {
            position: relative;
            z-index: 2;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .page-subtitle {
            margin: 0;
            color: rgba(255, 255, 255, 0.86);
            font-size: 0.92rem;
        }

        .btn-header-action {
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.34);
            color: #fff;
            height: 42px;
            padding: 0 16px;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            backdrop-filter: blur(2px);
        }

        .btn-header-action:hover {
            background: rgba(255, 255, 255, 0.28);
            color: #fff;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--admin-card-bg);
            border-radius: 16px;
            padding: 18px 20px;
            border: 1px solid var(--admin-border);
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 6px 14px rgba(15, 23, 42, 0.04);
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.07);
        }

        .stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .stat-card.pending .stat-icon {
            background: #fff7e6;
            color: #b45309;
        }

        .stat-card.approved .stat-icon {
            background: #ecfdf3;
            color: #166534;
        }

        .stat-card.rejected .stat-icon {
            background: #fff1f2;
            color: #be123c;
        }

        .stat-card.pending {
            border-color: #fde68a;
            background: linear-gradient(180deg, #fffdf5 0%, #ffffff 100%);
        }

        .stat-card.approved {
            border-color: #bbf7d0;
            background: linear-gradient(180deg, #f8fff9 0%, #ffffff 100%);
        }

        .stat-card.rejected {
            border-color: #fecdd3;
            background: linear-gradient(180deg, #fff8f9 0%, #ffffff 100%);
        }

        .stat-info h3 {
            font-size: 1.45rem;
            font-weight: 800;
            margin: 0 0 2px 0;
            color: var(--admin-text-main);
            line-height: 1;
        }

        .stat-info p {
            margin: 0;
            color: var(--admin-text-muted);
            font-size: 0.76rem;
            font-weight: 700;
            letter-spacing: 0.3px;
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
            gap: 14px;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .toolbar-left {
            flex: 1 1 560px;
            min-width: 280px;
        }

        .toolbar-form {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
            flex: 1 1 340px;
            min-width: 220px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 16px 10px 40px;
            height: 44px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 0.9rem;
            background: #f8fafc;
            line-height: 1.2;
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

        .filter-select {
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 0 12px;
            height: 44px;
            font-size: 0.88rem;
            min-width: 180px;
            background: #fff;
            color: #334155;
        }

        .filter-select:focus {
            border-color: var(--admin-secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 56, 202, 0.1);
        }

        .toolbar-right {
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: flex-end;
            flex-wrap: wrap;
            margin-left: auto;
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
            padding: 16px 24px;
            vertical-align: middle;
            border-bottom: 1px solid var(--admin-border);
            font-size: 0.84rem;
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
            font-size: 0.88rem;
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
            font-size: 0.84rem;
        }

        .badge-status {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            line-height: 1.1;
        }

        .badge-status::before {
            content: '';
            width: 5px;
            height: 5px;
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
            height: 44px;
            padding: 0 16px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .btn-action:hover {
            border-color: var(--admin-secondary);
            color: var(--admin-secondary);
            background: #f8fafc;
        }

        .btn-icon-action {
            width: 40px;
            height: 40px;
            padding: 0;
            justify-content: center;
        }

        .btn-icon-action i {
            margin: 0;
            font-size: 1rem;
            line-height: 1;
        }

        .event-action-group {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            gap: 8px;
            flex-wrap: nowrap;
        }

        .event-action-group form {
            margin: 0;
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

            .toolbar-left {
                width: 100%;
            }

            .toolbar-form {
                width: 100%;
            }

            .search-box {
                width: 100%;
            }

            .filter-select {
                width: 100%;
            }

            .toolbar-right {
                width: 100%;
                justify-content: flex-start;
                margin-left: 0;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }

            .btn-header-action {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endsection

@section('content')
    <div class="material-wrapper">
        @include('admin.partials.trainer-sidebar')

        <main class="material-main">

            <div class="page-header">
                <div>
                    <h1 class="page-title"><i class="bi bi-hourglass-split"></i> Material Approvals</h1>
                    <p class="page-subtitle">Review materi dari trainer sebelum tayang ke publik.</p>
                </div>
                <button class="btn btn-header-action" onclick="window.location.reload();">
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
                <div class="stat-card pending">
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
                    @php
                        $activeDeadlineFilter = $deadlineFilter ?? 'all';
                    @endphp
                    <div class="toolbar-left">
                        <form method="GET" class="toolbar-form">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" name="search" placeholder="Cari course atau nama trainer..."
                                    value="{{ request('search') }}">
                            </div>
                            <select class="filter-select" name="deadline_filter" onchange="this.form.submit()">
                                <option value="all" {{ ($deadlineFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Deadline</option>
                                <option value="overdue" {{ ($deadlineFilter ?? 'all') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                                <option value="on_time" {{ ($deadlineFilter ?? 'all') === 'on_time' ? 'selected' : '' }}>Tepat Waktu</option>
                                <option value="no_deadline" {{ ($deadlineFilter ?? 'all') === 'no_deadline' ? 'selected' : '' }}>Tanpa Deadline</option>
                            </select>
                        </form>
                    </div>

                    <div class="toolbar-right">
                        <a href="{{ route('admin.material.approved') }}" class="btn-action" style="color: #166534; border-color:#bbf7d0; background:#f0fdf4;">
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
                                <th>Tenggat</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pendingMaterials as $material)
                                <tr>
                                    <td>
                                        <div class="course-info">
                                            <div>
                                                <h6 class="course-title">{{ Str::limit($material->name, 40) }}</h6>
                                                <span class="badge-cat"><i class="bi bi-folder2 me-1"></i>{{ $material->category->name ?? 'Umum' }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="trainer-info">
                                            <img src="{{ $material->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=Trainer' }}" class="trainer-avatar">
                                            <div>
                                                <div class="trainer-name">{{ $material->trainer?->name ?? 'Anonim' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #334155;">{{ $material->modules_count }} File/Kuis</div>
                                    </td>
                                    <td>
                                        <div style="font-weight: 600; color: #334155;">{{ $material->updated_at?->format('d M Y') ?? $material->created_at->format('d M Y') }}</div>
                                        <div style="font-size: 0.75rem; color:#64748b;">{{ $material->updated_at?->diffForHumans() ?? $material->created_at->diffForHumans() }}</div>
                                    </td>
                                    <td>
                                        <span class="badge-status badge-pending">Review Pending</span>
                                    </td>
                                    <td>
                                        @php $monitor = $deadlineMonitoring[$material->id] ?? null; @endphp
                                        <div style="font-weight: 600; color: #334155;">{{ $monitor['deadline_text'] ?? 'Belum ditentukan' }}</div>
                                        <div style="font-size: 0.75rem; color: {{ ($monitor['status'] ?? '') === 'late' ? '#b91c1c' : '#64748b' }};">{{ $monitor['status_text'] ?? 'Tanpa deadline' }}</div>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('admin.material.show', $material->id) }}" class="btn-action" style="background: var(--admin-primary); color:white; border:none;">
                                            Review <i class="bi bi-play-fill"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                @if(($pendingEventModules ?? collect())->isEmpty())
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <h5 class="fw-bold text-dark">Antrean Kosong</h5>
                                                <p class="text-muted mb-0">Hore! Tidak ada materi yang perlu di-review saat ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($pendingMaterials->hasPages())
                    <div class="p-3 border-top">
                        {{ $pendingMaterials->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>

            <div class="content-card mt-4">
                <div class="toolbar">
                    <div class="toolbar-left">
                        <form method="GET" class="toolbar-form">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" name="search" placeholder="Cari modul event yang menunggu review..."
                                    value="{{ request('search') }}">
                            </div>
                            <select class="filter-select" name="deadline_filter" onchange="this.form.submit()">
                                <option value="all" {{ ($deadlineFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua
                                    Deadline</option>
                                <option value="overdue" {{ ($deadlineFilter ?? 'all') === 'overdue' ? 'selected' : '' }}>
                                    Overdue</option>
                                <option value="on_time" {{ ($deadlineFilter ?? 'all') === 'on_time' ? 'selected' : '' }}>Tepat
                                    Waktu</option>
                                <option value="no_deadline" {{ ($deadlineFilter ?? 'all') === 'no_deadline' ? 'selected' : '' }}>
                                    Tanpa Deadline</option>
                            </select>
                        </form>
                    </div>

                    <div class="toolbar-right">
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
                                <th>Materi (Event)</th>
                                <th>Trainer</th>
                                <th>Isi Modul</th>
                                <th>Tanggal Submit</th>
                                <th>Status</th>
                                <th>Tenggat</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(($pendingEventModules ?? collect()) as $event)
                                    <tr>
                                        <td>
                                            <div class="course-info" style="gap:12px;">
                                                <div>
                                                    <h6 class="course-title">{{ Str::limit($event->title, 48) }}</h6>
                                                    <div class="text-muted" style="font-size:0.75rem;">
                                                        {{ $event->jenis ?? '-' }}{{ $event->event_date ? ' • ' . $event->event_date->format('d M Y') : '' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="trainer-info">
                                                <img src="{{ $event->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=Trainer' }}" class="trainer-avatar">
                                                <div>
                                                    <div class="trainer-name">{{ $event->trainer?->name ?? 'Anonim' }}</div>
                                                    <div style="font-size: 0.75rem; color:#64748b;">{{ $event->trainer?->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #334155;">1 Dokumen Modul</div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #334155;">{{ $event->module_submitted_at?->format('d M Y') ?? ($event->created_at?->format('d M Y') ?? '-') }}</div>
                                            <div style="font-size: 0.75rem; color:#64748b;">{{ $event->module_submitted_at?->diffForHumans() ?? '' }}</div>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-pending">Review Pending</span>
                                        </td>
                                        <td>
                                            @php
                                                $eventDeadline = $event->material_deadline;
                                                $eventLate = $eventDeadline ? now()->gt($eventDeadline) : false;
                                            @endphp
                                            <div style="font-weight: 600; color: #334155;">{{ $eventDeadline ? $eventDeadline->format('d M Y H:i') : 'Belum ditentukan' }}</div>
                                            <div style="font-size: 0.75rem; color: {{ $eventLate ? '#b91c1c' : '#64748b' }};">{{ $eventLate ? 'Melewati deadline' : 'Tanpa deadline' }}</div>
                                        </td>
                                        <td class="text-end">
                                            <div class="event-action-group">
                                                <a href="{{ $event->module_file_url }}" target="_blank" class="btn-action btn-icon-action" title="Lihat modul" aria-label="Lihat modul">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <form action="{{ route('admin.event-material.approve', $event) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="btn-action btn-icon-action" style="color:#166534;border-color:#bbf7d0;background:#f0fdf4;" title="Approve" aria-label="Approve">
                                                        <i class="bi bi-check2-circle"></i>
                                                    </button>
                                                </form>
                                                <button class="btn-action btn-icon-action" type="button" data-bs-toggle="collapse" data-bs-target="#rejectEventModule-{{ $event->id }}" aria-expanded="false" aria-controls="rejectEventModule-{{ $event->id }}" style="color:#991b1b;border-color:#fecaca;background:#fef2f2;" title="Tolak" aria-label="Tolak">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </div>
                                            <div class="collapse mt-2" id="rejectEventModule-{{ $event->id }}">
                                                <form action="{{ route('admin.event-material.reject', $event) }}" method="POST" class="d-flex flex-column gap-2">
                                                    @csrf
                                                    <textarea name="rejection_reason" rows="2" class="form-control" placeholder="Alasan penolakan (wajib)" required></textarea>
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" class="btn btn-sm btn-danger"><i class="bi bi-send me-1"></i>Kirim Penolakan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state" style="padding: 36px 20px;">
                                                <i class="bi bi-folder2-open"></i>
                                                <h6 class="fw-bold mt-2 mb-1" style="color:#334155;">Belum ada materi event pending</h6>
                                                <p class="text-muted mb-0">Saat ini tidak ada modul event yang menunggu verifikasi.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
@endsection