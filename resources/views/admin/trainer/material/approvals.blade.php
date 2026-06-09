@extends('layouts.admin-trainer')

@section('title', 'Persetujuan Materi - Menunggu')

@push('admin-trainer-styles')
    <style>
        :root {
            --admin-primary: #1a237e;
            --admin-secondary: #3949ab;
            --admin-bg: #f8fafc;
            --admin-card-bg: #ffffff;
            --admin-border: #e2e8f0;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;
            --status-pending-bg: #fffbeb;
            --status-pending-text: #b45309;
        }

        .material-page {
            width: 100%;
        }

        .page-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            flex-wrap: wrap;
            background: linear-gradient(135deg, #1a237e 0%, #283593 55%, #3949ab 100%);
            border-radius: 24px;
            padding: 30px 34px;
            color: #fff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, .12);
            box-shadow: 0 16px 34px rgba(26, 35, 126, .18);
        }

        .page-header::after {
            content: '';
            position: absolute;
            right: -80px;
            top: -80px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, .22) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
        }

        .page-header>* {
            position: relative;
            z-index: 2;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 900;
            color: #fff;
            margin-bottom: 8px;
            letter-spacing: -0.6px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-subtitle {
            margin: 0;
            color: rgba(255, 255, 255, .86);
            font-size: .95rem;
            line-height: 1.6;
        }

        .btn-header-action {
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .34);
            color: #fff;
            height: 44px;
            padding: 0 18px;
            border-radius: 12px;
            font-size: .86rem;
            font-weight: 800;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            backdrop-filter: blur(2px);
        }

        .btn-header-action:hover {
            background: rgba(255, 255, 255, .28);
            color: #fff;
        }

        .btn-header-action:focus {
            outline: none;
        }

        .stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--admin-card-bg);
            border-radius: 18px;
            padding: 20px;
            border: 1px solid var(--admin-border);
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
            transition: .2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(15, 23, 42, .08);
        }

        .stat-icon {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }

        .stat-card.pending {
            border-color: #fde68a;
            background: linear-gradient(180deg, #fffdf5 0%, #fff 100%);
        }

        .stat-card.pending .stat-icon {
            background: #fff7e6;
            color: #b45309;
        }

        .stat-card.approved {
            border-color: #bbf7d0;
            background: linear-gradient(180deg, #f8fff9 0%, #fff 100%);
        }

        .stat-card.approved .stat-icon {
            background: #ecfdf3;
            color: #166534;
        }

        .stat-card.rejected {
            border-color: #fecdd3;
            background: linear-gradient(180deg, #fff8f9 0%, #fff 100%);
        }

        .stat-card.rejected .stat-icon {
            background: #fff1f2;
            color: #be123c;
        }

        .stat-info h3 {
            font-size: 1.6rem;
            font-weight: 900;
            margin: 0 0 4px;
            color: var(--admin-text-main);
            line-height: 1;
        }

        .stat-info p {
            margin: 0;
            color: var(--admin-text-muted);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        .content-card {
            background: var(--admin-card-bg);
            border-radius: 22px;
            border: 1px solid var(--admin-border);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .06);
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
            padding: 10px 16px 10px 42px;
            height: 44px;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            font-size: .9rem;
            background: #f8fafc;
            line-height: 1.2;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-box input:focus {
            border-color: var(--admin-secondary);
            outline: none;
            background: #fff;
            box-shadow: 0 0 0 3px rgba(57, 73, 171, .12);
        }

        .filter-select {
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 0 12px;
            height: 44px;
            font-size: .88rem;
            min-width: 180px;
            background: #fff;
            color: #334155;
        }

        .filter-select:focus {
            border-color: var(--admin-secondary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(57, 73, 171, .12);
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
            font-size: .75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .5px;
            padding: 16px 24px;
            border-bottom: 1px solid var(--admin-border);
            white-space: nowrap;
        }

        .table td {
            padding: 16px 24px;
            vertical-align: middle;
            border-bottom: 1px solid var(--admin-border);
            font-size: .84rem;
        }

        .table tr:hover {
            background-color: #f8fafc;
        }

        .course-info {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .course-title {
            font-weight: 800;
            color: var(--admin-text-main);
            margin: 0 0 5px;
            font-size: .9rem;
            line-height: 1.35;
        }

        .badge-cat {
            background: #e8eaf6;
            color: #3949ab;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
        }

        .trainer-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .trainer-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(15, 23, 42, .12);
        }

        .trainer-name {
            font-weight: 700;
            color: var(--admin-text-main);
            font-size: .85rem;
        }

        .badge-status {
            padding: 6px 11px;
            border-radius: 999px;
            font-size: .7rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            line-height: 1.1;
            white-space: nowrap;
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
            height: 42px;
            padding: 0 16px;
            border-radius: 10px;
            font-size: .85rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            transition: .2s;
            white-space: nowrap;
        }

        .btn-action:hover {
            border-color: var(--admin-secondary);
            color: var(--admin-secondary);
            background: #f8fafc;
        }

        .btn-primary-action {
            background: var(--admin-secondary);
            color: #fff;
            border: none;
        }

        .btn-primary-action:hover {
            background: #283593;
            color: #fff;
        }

        .btn-icon-action {
            width: 40px;
            height: 40px;
            padding: 0;
            justify-content: center;
            border-radius: 10px;
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
            padding: 56px 20px;
        }

        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 16px;
        }

        @media (max-width: 1200px) {
            .stat-grid {
                grid-template-columns: 1fr;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .toolbar-left,
            .toolbar-right,
            .toolbar-form,
            .search-box,
            .filter-select {
                width: 100%;
            }

            .toolbar-left {
                flex: none !important;
            }

            .toolbar-right {
                justify-content: flex-start;
                margin-left: 0;
            }
        }

        @media (max-width: 768px) {
            .page-header {
                padding: 26px;
            }

            .page-title {
                font-size: 1.6rem;
            }

            .btn-header-action {
                width: 100%;
            }
        }
    </style>
@endpush

@section('admin-trainer-content')
    <div class="material-page">

        <div class="page-header">
            <div>
                <h1 class="page-title">
                    <i class="bi bi-hourglass-split"></i>
                    Persetujuan Materi
                </h1>
                <p class="page-subtitle">
                    Review materi course dan event dari trainer sebelum ditayangkan ke publik.
                </p>
            </div>

            <button class="btn-header-action" onclick="window.location.reload();">
                <i class="bi bi-arrow-clockwise"></i>
                Segarkan
            </button>
        </div>



        <div class="stat-grid">
            <div class="stat-card pending">
                <div class="stat-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalPending ?? 0 }}</h3>
                    <p>Menunggu Tinjauan</p>
                </div>
            </div>

            <div class="stat-card approved">
                <div class="stat-icon">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalApproved ?? 0 }}</h3>
                    <p>Total Disetujui</p>
                </div>
            </div>

            <div class="stat-card rejected">
                <div class="stat-icon">
                    <i class="bi bi-x-circle-fill"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $totalRejected ?? 0 }}</h3>
                    <p>Perlu Revisi</p>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="toolbar">
                <div class="toolbar-left">
                    <form method="GET" class="toolbar-form">
                        <div class="search-box">
                            <i class="bi bi-search"></i>
                            <input type="text" name="search" placeholder="Cari course atau nama trainer..."
                                value="{{ request('search') }}">
                        </div>

                        <select class="filter-select" name="deadline_filter" onchange="this.form.submit()">
                            <option value="all" {{ ($deadlineFilter ?? 'all') === 'all' ? 'selected' : '' }}>
                                Semua Deadline
                            </option>
                            <option value="overdue" {{ ($deadlineFilter ?? 'all') === 'overdue' ? 'selected' : '' }}>
                                Lewat Tenggat
                            </option>
                            <option value="on_time" {{ ($deadlineFilter ?? 'all') === 'on_time' ? 'selected' : '' }}>
                                Tepat Waktu
                            </option>
                            <option value="no_deadline" {{ ($deadlineFilter ?? 'all') === 'no_deadline' ? 'selected' : '' }}>
                                Tanpa Deadline
                            </option>
                        </select>
                    </form>
                </div>

                <div class="toolbar-right">
                    <a href="{{ route('admin.trainer.material.approved') }}" class="btn-action"
                        style="color:#166534;border-color:#bbf7d0;background:#f0fdf4;">
                        Lihat Disetujui
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Materi Course</th>
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
                                            <h6 class="course-title">
                                                {{ \Illuminate\Support\Str::limit($material->name, 40) }}
                                            </h6>
                                            <span class="badge-cat">
                                                <i class="bi bi-folder2 me-1"></i>
                                                {{ $material->category->name ?? 'Umum' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="trainer-info">
                                        <img src="{{ $material->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($material->trainer?->name ?? 'Trainer') . '&background=3949ab&color=fff&bold=true' }}"
                                            class="trainer-avatar" alt="Trainer">

                                        <div>
                                            <div class="trainer-name">
                                                {{ $material->trainer?->name ?? 'Anonim' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight:700;color:#334155;">
                                        {{ $material->modules_count ?? 0 }} File/Kuis
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight:700;color:#334155;">
                                        {{ $material->updated_at?->format('d M Y') ?? $material->created_at?->format('d M Y') }}
                                    </div>
                                    <div style="font-size:.75rem;color:#64748b;">
                                        {{ $material->updated_at?->diffForHumans() ?? $material->created_at?->diffForHumans() }}
                                    </div>
                                </td>

                                <td>
                                    <span class="badge-status badge-pending">
                                        Menunggu Tinjauan
                                    </span>
                                </td>

                                <td>
                                    @php $monitor = $deadlineMonitoring[$material->id] ?? null; @endphp

                                    <div style="font-weight:700;color:#334155;">
                                        {{ $monitor['deadline_text'] ?? 'Belum ditentukan' }}
                                    </div>

                                    <div
                                        style="font-size:.75rem;color:{{ ($monitor['status'] ?? '') === 'late' ? '#b91c1c' : '#64748b' }};">
                                        {{ $monitor['status_text'] ?? 'Tanpa deadline' }}
                                    </div>
                                </td>

                                <td>
                                    <a href="{{ route('admin.trainer.material.show', $material->id) }}"
                                        class="btn-action btn-primary-action">
                                        Tinjau
                                        <i class="bi bi-play-fill"></i>
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
                                            <p class="text-muted mb-0">
                                                Hore! Tidak ada materi yang perlu di-review saat ini.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($pendingMaterials, 'hasPages') && $pendingMaterials->hasPages())
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
                            <option value="all" {{ ($deadlineFilter ?? 'all') === 'all' ? 'selected' : '' }}>
                                Semua Deadline
                            </option>
                            <option value="overdue" {{ ($deadlineFilter ?? 'all') === 'overdue' ? 'selected' : '' }}>
                                Overdue
                            </option>
                            <option value="on_time" {{ ($deadlineFilter ?? 'all') === 'on_time' ? 'selected' : '' }}>
                                Tepat Waktu
                            </option>
                            <option value="no_deadline" {{ ($deadlineFilter ?? 'all') === 'no_deadline' ? 'selected' : '' }}>
                                Tanpa Deadline
                            </option>
                        </select>
                    </form>
                </div>

                <div class="toolbar-right">
                    <a href="{{ route('admin.trainer.material.approved') }}" class="btn-action"
                        style="color:#166534;border-color:#bbf7d0;background:#f0fdf4;">
                        Lihat Disetujui
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Materi Event</th>
                            <th>Trainer</th>
                            <th>Isi Modul</th>
                            <th>Tanggal Submit</th>
                            <th>Status</th>
                            <th>Tenggat</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse(($pendingEventModules ?? collect()) as $eventModule)
                            <tr>
                                <td>
                                    <div class="course-info">
                                        <div>
                                            <h6 class="course-title">
                                                {{ \Illuminate\Support\Str::limit($eventModule->display_title ?? $eventModule->event?->title ?? '-', 48) }}
                                            </h6>
                                            <div class="text-muted" style="font-size:.75rem;">
                                                {{ $eventModule->event?->jenis ?? '' }}
                                                @if($eventModule->event?->event_date)
                                                    • {{ $eventModule->event->event_date->format('d M Y') }}
                                                @endif
                                                @if(empty($eventModule->event))
                                                    <div class="text-muted" style="font-size:.75rem;">Sumber:
                                                        {{ \Illuminate\Support\Str::limit($eventModule->original_name ?? '—', 40) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div class="trainer-info">
                                        <img src="{{ $eventModule->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($eventModule->trainer?->name ?? 'Trainer') . '&background=3949ab&color=fff&bold=true' }}"
                                            class="trainer-avatar" alt="Trainer">

                                        <div>
                                            <div class="trainer-name">
                                                {{ $eventModule->trainer?->name ?? 'Anonim' }}
                                            </div>
                                            <div style="font-size:.75rem;color:#64748b;">
                                                {{ $eventModule->trainer?->email }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight:700;color:#334155;">
                                        1 Dokumen Modul
                                    </div>
                                </td>

                                <td>
                                    <div style="font-weight:700;color:#334155;">
                                        {{ $eventModule->material_submitted_at?->format('d M Y') ?? ($eventModule->created_at?->format('d M Y') ?? '-') }}
                                    </div>
                                    <div style="font-size:.75rem;color:#64748b;">
                                        {{ $eventModule->material_submitted_at?->diffForHumans() ?? '' }}
                                    </div>
                                </td>

                                <td>
                                    <span class="badge-status badge-pending">
                                        Menunggu Tinjauan
                                    </span>
                                </td>

                                <td>
                                    @php
                                        $eventDeadline = $eventModule->event?->material_deadline;
                                        $eventLate = $eventDeadline ? now()->gt($eventDeadline) : false;
                                    @endphp

                                    <div style="font-weight:700;color:#334155;">
                                        {{ $eventDeadline ? \Carbon\Carbon::parse($eventDeadline)->format('d M Y H:i') : 'Belum ditentukan' }}
                                    </div>

                                    <div style="font-size:.75rem;color:{{ $eventLate ? '#b91c1c' : '#64748b' }};">
                                        {{ $eventLate ? 'Melewati deadline' : 'Tanpa deadline' }}
                                    </div>
                                </td>

                                <td class="text-end">
                                    <div class="event-action-group">
                                        <a href="{{ route('admin.event-material.stream', $eventModule->event_id) }}?assignment_id={{ $eventModule->id }}"
                                            target="_blank" class="btn-action btn-icon-action" title="Lihat modul"
                                            aria-label="Lihat modul">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <form action="{{ route('admin.event-material.approve', $eventModule->event_id) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="module_id" value="{{ $eventModule->id }}">

                                            <button type="submit" class="btn-action btn-icon-action"
                                                style="color:#166534;border-color:#bbf7d0;background:#f0fdf4;" title="Setujui"
                                                aria-label="Setujui">
                                                <i class="bi bi-check2-circle"></i>
                                            </button>
                                        </form>

                                        <button class="btn-action btn-icon-action" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#rejectEventModule-{{ $eventModule->id }}" aria-expanded="false"
                                            aria-controls="rejectEventModule-{{ $eventModule->id }}"
                                            style="color:#991b1b;border-color:#fecaca;background:#fef2f2;" title="Tolak"
                                            aria-label="Tolak">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </div>

                                    <div class="collapse mt-2" id="rejectEventModule-{{ $eventModule->id }}">
                                        <form action="{{ route('admin.event-material.reject', $eventModule->event_id) }}"
                                            method="POST" class="d-flex flex-column gap-2">
                                            @csrf
                                            <input type="hidden" name="module_id" value="{{ $eventModule->id }}">

                                            <textarea name="rejection_reason" rows="2" class="form-control"
                                                placeholder="Alasan penolakan (wajib)" required></textarea>

                                            <div class="d-flex justify-content-end">
                                                <button type="submit" class="btn btn-sm btn-danger">
                                                    <i class="bi bi-send me-1"></i>
                                                    Kirim Penolakan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state" style="padding:36px 20px;">
                                        <i class="bi bi-folder2-open"></i>
                                        <h6 class="fw-bold mt-2 mb-1" style="color:#334155;">
                                            Belum ada materi event pending
                                        </h6>
                                        <p class="text-muted mb-0">
                                            Saat ini tidak ada modul event yang menunggu verifikasi.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection