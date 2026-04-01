@extends('layouts.admin')

@section('title', 'Approved Materials')

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
            color: #166534;
            /* Warna hijau khusus approved */
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
            border-color: #166534;
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(22, 101, 52, 0.1);
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
            border-color: #166534;
            outline: none;
            box-shadow: 0 0 0 3px rgba(22, 101, 52, 0.1);
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

        /* Status khusus Approved */
        .badge-approved-status {
            background: #dcfce7;
            color: #166534;
        }

        .badge-approved-status::before {
            background: #166534;
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
        }
    </style>
@endsection

@section('content')
    <div class="material-wrapper">
        @include('admin.partials.trainer-sidebar')

        <main class="material-main">
            <div class="page-header mb-4">
                <div>
                    <h1 class="page-title"><i class="bi bi-check-circle-fill me-2"></i>Materi Disetujui</h1>
                    <p class="text-muted mb-0">Daftar kelas yang sudah tervalidasi dan aktif di platform.</p>
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
                    <div class="toolbar-left">
                        <form method="GET" class="toolbar-form">
                            <div class="search-box">
                                <i class="bi bi-search"></i>
                                <input type="text" name="search" placeholder="Cari materi yang disetujui..."
                                    value="{{ request('search') }}">
                            </div>
                            <select class="filter-select" name="deadline_filter" onchange="this.form.submit()">
                                <option value="all" {{ ($deadlineFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua
                                    Deadline
                                </option>
                                <option value="overdue" {{ ($deadlineFilter ?? 'all') === 'overdue' ? 'selected' : '' }}>
                                    Overdue
                                </option>
                                <option value="on_time" {{ ($deadlineFilter ?? 'all') === 'on_time' ? 'selected' : '' }}>Tepat
                                    Waktu</option>
                                <option value="no_deadline" {{ ($deadlineFilter ?? 'all') === 'no_deadline' ? 'selected' : '' }}>
                                    Tanpa Deadline</option>
                            </select>
                        </form>
                    </div>

                    <div class="toolbar-right">
                        @if($hasActiveFilter)
                            <span class="btn-action"
                                style="cursor:default;color:#334155;border-color:#cbd5e1;background:#f8fafc;">
                                Filter:
                                {{ $deadlineLabelMap[$activeDeadlineFilter] ?? 'Semua Deadline' }}{{ $activeSearch !== '' ? ' • Pencarian aktif' : '' }}
                            </span>
                            <a href="{{ route('admin.material.approved') }}" class="btn-action">
                                <i class="bi bi-x-circle"></i> Reset
                            </a>
                        @endif
                        <a href="{{ route('admin.material.rejected') }}" class="btn-action"
                            style="color: #991b1b; border-color:#fecaca; background:#fef2f2;">
                            Lihat Revisi <i class="bi bi-arrow-right"></i>
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
                                <th>Tanggal Disetujui</th>
                                <th>Status</th>
                                <th>Monitoring Deadline</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($approvedMaterials as $material)
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
                                            {{ $material->approved_at ? $material->approved_at->format('d M Y') : '-' }}
                                        </div>
                                        <div style="font-size: 0.75rem; color:#64748b;">
                                            {{ $material->approved_at ? $material->approved_at->diffForHumans() : '' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-status badge-approved-status">Live</span>
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
                                        <a href="{{ route('admin.material.show', $material->id) }}" class="btn-action"
                                            style="color:#166534; border-color:#bbf7d0; background:#f0fdf4;">
                                            Detail <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                @if(($approvedEventModules ?? collect())->isEmpty())
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                <i class="bi bi-inbox"></i>
                                                <h5 class="fw-bold text-dark">Belum ada materi</h5>
                                                <p class="text-muted mb-0">Belum ada materi kelas yang disetujui saat ini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if(!($approvedEventModules ?? collect())->isEmpty())
                    <div class="px-3 pt-3 border-top">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div>
                                <div class="fw-bold text-dark">Module Event (Trainer) - Approved</div>
                                <div class="text-muted small">Modul event yang sudah diverifikasi admin.</div>
                            </div>
                            <span class="badge" style="background:#dcfce7;color:#166534;border:1px solid #bbf7d0;">
                                {{ ($approvedEventModules ?? collect())->count() }} approved
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Trainer</th>
                                    <th>Tanggal Disetujui</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($approvedEventModules as $event)
                                    <tr>
                                        <td>
                                            <div>
                                                <h6 class="course-title">{{ Str::limit($event->title, 48) }}</h6>
                                                <div class="text-muted" style="font-size:0.75rem;">
                                                    {{ $event->jenis ?? '-' }}{{ $event->event_date ? ' • ' . $event->event_date->format('d M Y') : '' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="trainer-info">
                                                <img src="{{ $event->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=Trainer' }}"
                                                    class="trainer-avatar">
                                                <div>
                                                    <div class="trainer-name">{{ $event->trainer?->name ?? 'Anonim' }}</div>
                                                    <div style="font-size: 0.75rem; color:#64748b;">{{ $event->trainer?->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-weight: 600; color: #334155;">
                                                {{ $event->module_verified_at?->format('d M Y') ?? '-' }}
                                            </div>
                                            <div style="font-size: 0.75rem; color:#64748b;">
                                                {{ $event->module_verified_at?->diffForHumans() ?? '' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge-status badge-approved-status">Live</span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                                <a href="{{ $event->module_file_url }}" target="_blank" class="btn-action"
                                                    style="color:#166534; border-color:#bbf7d0; background:#f0fdf4;">
                                                    Lihat <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('admin.events.show', $event) }}" class="btn-action">
                                                    Detail <i class="bi bi-arrow-right"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if($approvedMaterials->hasPages())
                    <div class="p-3 border-top">
                        {{ $approvedMaterials->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            </div>
        </main>
    </div>
@endsection