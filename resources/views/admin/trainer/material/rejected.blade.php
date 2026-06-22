@extends('layouts.admin-trainer')

@section('title', 'Materi Ditolak')

@push('admin-trainer-styles')
@push('admin-trainer-styles')
    <style>
        :root {
            --admin-primary: #1e1b4b;
            --admin-secondary: #1e1b4b;
            --admin-accent: #1e1b4b;
            --admin-bg: #f8fafc;
            --admin-card-bg: #ffffff;
            --admin-border: #e2e8f0;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;
        }

        /* --- COMPONENT STYLES (page-specific) --- */
        /* --- COMPONENT STYLES (page-specific) --- */

        /* --- HEADER --- */
        .page-header {
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            background-color: var(--admin-secondary);
            border-radius: 20px;
            padding: 24px 26px;
            color: #fff;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 20px 40px rgba(30, 27, 75, 0.12);
        }

        .page-header::after {
            content: '';
            position: absolute;
            right: -80px;
            top: -80px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, rgba(255, 255, 255, 0) 70%);
            pointer-events: none;
        }

        .page-header>div,
        .page-header>.page-header-actions {
            position: relative;
            z-index: 2;
        }

        .page-header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
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
            box-shadow: 0 0 0 3px rgba(30, 27, 75, 0.1);
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
            box-shadow: 0 0 0 3px rgba(30, 27, 75, 0.1);
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
            background: rgba(255, 255, 255, 0.18);
            border: 1px solid rgba(255, 255, 255, 0.34);
            color: #fff;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 0.84rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            backdrop-filter: blur(2px);
        }

        .btn-back-header:hover {
            background: rgba(255, 255, 255, 0.28);
            color: #fff;
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

        @media (max-width: 1200px) {

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .toolbar-left {
                width: 100%;
                flex: none !important;
            }

            .toolbar-form {
                width: 100%;
            }

            .search-box {
                width: 100%;
                flex: none !important;
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

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: stretch !important;
                gap: 16px;
                padding: 20px !important;
            }
            .page-header-actions {
                flex-direction: column;
                width: 100%;
            }
            .page-header-actions .btn-back-header,
            .page-header-actions .btn-action {
                width: 100% !important;
                justify-content: center;
            }
            .toolbar {
                padding: 16px;
                gap: 12px;
                flex-direction: column !important;
                align-items: stretch !important;
            }
            .toolbar-left, .toolbar-form {
                width: 100%;
                flex-direction: column !important;
                align-items: stretch !important;
            }
            .search-box {
                width: 100% !important;
                flex: none !important;
            }
            .filter-select {
                width: 100% !important;
            }
            .toolbar-right {
                width: 100%;
                margin-left: 0;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }
            .toolbar-right .btn-action {
                width: 100% !important;
                justify-content: center;
            }
        }

        /* Responsive Table to Cards conversion on mobile/tablet */
        @media (max-width: 991.98px) {
            .table, .table thead, .table tbody, .table tr, .table td {
                display: block;
                width: 100% !important;
                box-sizing: border-box;
            }

            .table thead {
                display: none; /* Hide standard headers */
            }

            .table tr {
                margin-bottom: 24px;
                border: 1px solid var(--admin-border);
                border-radius: 18px;
                padding: 20px;
                background: #fff;
                box-shadow: 0 4px 12px rgba(15, 23, 42, 0.01);
            }

            .table td {
                padding: 10px 0;
                border-bottom: none;
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
            }

            .table td::before {
                content: attr(data-label);
                font-weight: 800;
                color: var(--admin-text-muted);
                font-size: 0.74rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                text-align: left;
                margin-right: 16px;
            }

            /* Highlight Title Column */
            .table td[data-label="Materi Course"],
            .table td[data-label="Materi Event"] {
                border-bottom: 1px solid #f1f5f9;
                padding-bottom: 14px;
                margin-bottom: 10px;
                display: block;
                text-align: left;
            }

            .table td[data-label="Materi Course"]::before,
            .table td[data-label="Materi Event"]::before {
                display: none;
            }

            /* Action Column style */
            .table td:last-child {
                border-top: 1px solid #f1f5f9;
                padding-top: 14px;
                margin-top: 10px;
                display: flex;
                justify-content: stretch;
            }

            .table td:last-child::before {
                display: none;
            }

            .btn-action {
                width: 100%;
                justify-content: center;
                height: 40px;
            }
        }
    </style>
@endpush
@endpush

@section('admin-trainer-content')
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title"><i class="bi bi-exclamation-octagon-fill me-2"></i>Perlu Revisi</h1>
            <p class="page-subtitle">Materi yang dikembalikan ke trainer karena tidak sesuai standar.</p>
        </div>
        <div class="page-header-actions">
            <a href="{{ route('admin.trainer.material.approvals') }}" class="btn-back-header">
                <i class="bi bi-arrow-left"></i> Kembali ke Antrean
            </a>
            <a href="{{ route('admin.trainer.material.approved') }}" class="btn-back-header" style="background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.25);">
                Lihat Disetujui <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Unified Toolbar -->
    <div class="content-card mb-4">
        @php
            $activeDeadlineFilter = $deadlineFilter ?? 'all';
            $activeSearch = trim((string) request('search', ''));
            $hasActiveFilter = ($activeDeadlineFilter !== 'all') || ($activeSearch !== '');
            $deadlineLabelMap = [
                'all' => 'Semua Deadline',
                'overdue' => 'Lewat Tenggat',
                'on_time' => 'Tepat Waktu',
                'no_deadline' => 'Tanpa Deadline',
            ];
        @endphp
        <div class="toolbar">
            <div class="toolbar-left">
                <form method="GET" class="toolbar-form">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text" name="search" placeholder="Cari materi course atau event yang direvisi..."
                            value="{{ request('search') }}">
                    </div>
                    <select class="filter-select" name="deadline_filter" onchange="this.form.submit()">
                        <option value="all" {{ ($deadlineFilter ?? 'all') === 'all' ? 'selected' : '' }}>Semua Deadline</option>
                        <option value="overdue" {{ ($deadlineFilter ?? 'all') === 'overdue' ? 'selected' : '' }}>Lewat Tenggat</option>
                        <option value="on_time" {{ ($deadlineFilter ?? 'all') === 'on_time' ? 'selected' : '' }}>Tepat Waktu</option>
                        <option value="no_deadline" {{ ($deadlineFilter ?? 'all') === 'no_deadline' ? 'selected' : '' }}>Tanpa Deadline</option>
                    </select>
                </form>
            </div>

            <div class="toolbar-right">
                @if($hasActiveFilter)
                    <span class="btn-action" style="cursor:default;color:#334155;border-color:#cbd5e1;background:#f8fafc;height:44px;display:inline-flex;align-items:center;">
                        Filter: {{ $deadlineLabelMap[$activeDeadlineFilter] ?? 'Semua Deadline' }}{{ $activeSearch !== '' ? ' • Pencarian aktif' : '' }}
                    </span>
                    <a href="{{ route('admin.trainer.material.rejected') }}" class="btn-action" style="height:44px;display:inline-flex;align-items:center;">
                        <i class="bi bi-x-circle"></i> Reset
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="content-card">


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
                            <td data-label="Materi Course">
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
                            <td data-label="Trainer">
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
                            <td data-label="Alasan Penolakan" style="max-width: 250px;">
                                <div class="rejection-note">
                                    <i class="bi bi-chat-text-fill me-1"></i>
                                    {{ Str::limit($material->rejection_reason, 60) }}
                                </div>
                            </td>
                            <td data-label="Tanggal Ditolak">
                                <div style="font-weight: 600; color: #334155;">
                                    {{ $material->rejected_at ? $material->rejected_at->format('d M Y') : '-' }}
                                </div>
                                <div style="font-size: 0.75rem; color:#64748b;">
                                    {{ $material->rejected_at ? $material->rejected_at->diffForHumans() : '' }}
                                </div>
                            </td>
                            <td data-label="Status">
                                <span class="badge-status badge-rejected-status">Revisi</span>
                            </td>
                            <td data-label="Tenggat">
                                @php $monitor = $deadlineMonitoring[$material->id] ?? null; @endphp
                                <div style="font-weight: 600; color: #334155;">
                                    {{ $monitor['deadline_text'] ?? 'Belum ditentukan' }}
                                </div>
                                <div
                                    style="font-size: 0.75rem; color: {{ ($monitor['status'] ?? '') === 'late' ? '#b91c1c' : '#64748b' }};">
                                    {{ $monitor['status_text'] ?? 'Tanpa deadline' }}
                                </div>
                            </td>
                            <td data-label="Aksi" class="text-end">
                                <a href="{{ route('admin.trainer.material.show', $material->id) }}" class="btn-action">
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

    <div class="content-card mt-4">


        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Trainer</th>
                        <th>Alasan Penolakan</th>
                        <th>Tanggal Ditolak</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($rejectedEventModules ?? collect()) as $event)
                        @php
                            $rejectionReasons = $event->trainerModules->pluck('rejection_reason')->filter()->unique()->implode(', ');
                            $latestRejectedAt = $event->trainerModules->max('reviewed_at');
                        @endphp
                        <tr>
                            <td data-label="Materi Event">
                                <div>
                                    <h6 class="course-title">{{ \Illuminate\Support\Str::limit($event->title ?? '-', 48) }}</h6>
                                    <div class="text-muted" style="font-size:0.72rem;">
                                        {{ $event->jenis ?? '-' }}{{ $event->event_date ? ' • ' . $event->event_date->format('d M Y') : '' }}
                                    </div>
                                </div>
                            </td>
                            <td data-label="Trainer">
                                <div class="trainer-info">
                                    <img src="{{ $event->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=Trainer' }}"
                                        class="trainer-avatar" alt="Trainer">
                                    <div>
                                        <div class="trainer-name">{{ $event->trainer?->name ?? 'Anonim' }}</div>
                                        <div style="font-size: 0.72rem; color:#64748b;">{{ $event->trainer?->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Alasan Penolakan" style="max-width: 250px;">
                                <div class="rejection-note">
                                    <i class="bi bi-chat-text-fill me-1"></i>
                                    {{ Str::limit($rejectionReasons ?: $event->material_rejection_reason, 60) ?: '-' }}
                                </div>
                            </td>
                            <td data-label="Tanggal Ditolak">
                                <div style="font-weight: 600; color: #334155;">
                                    {{ $latestRejectedAt ? \Carbon\Carbon::parse($latestRejectedAt)->format('d M Y') : '-' }}
                                </div>
                                <div style="font-size: 0.72rem; color:#64748b;">
                                    {{ $latestRejectedAt ? \Carbon\Carbon::parse($latestRejectedAt)->diffForHumans() : '' }}
                                </div>
                            </td>
                            <td data-label="Status">
                                <span class="badge-status badge-rejected-status">Revisi</span>
                            </td>
                            <td data-label="Aksi" class="text-end">
                                <a href="{{ route('admin.event-material.show', $event->id) }}" class="btn-action">
                                    Tinjau <i class="bi bi-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state" style="padding: 36px 20px;">
                                    <i class="bi bi-folder2-open"></i>
                                    <h6 class="fw-bold mt-2 mb-1" style="color:#334155;">Belum ada materi event revisi</h6>
                                    <p class="text-muted mb-0">Saat ini tidak ada modul event yang ditolak.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection