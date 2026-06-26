@extends('layouts.admin-trainer')

@section('title', 'Materi Disetujui')

@push('admin-trainer-styles')
<style>
    :root {
        --admin-primary: #1e1b4b;
        --admin-secondary: #1e1b4b;
        --admin-accent: #1e1b4b;
        --admin-card-bg: #ffffff;
        --admin-border: #e2e8f0;
        --admin-text-main: #0f172a;
        --admin-text-muted: #64748b;
    }

    .page-header {
        background-color: var(--admin-secondary);
        border-radius: 24px;
        padding: 36px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.15);
        box-shadow: 0 20px 40px rgba(30, 27, 75, 0.12);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .page-header::after {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(99, 102, 241, 0) 70%);
        filter: blur(20px);
        border-radius: 50%;
        z-index: 1;
        pointer-events: none;
    }

    .page-header::before {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -5%;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.15) 0%, rgba(14, 165, 233, 0) 70%);
        filter: blur(20px);
        border-radius: 50%;
        z-index: 1;
        pointer-events: none;
    }

    .page-header > div,
    .page-header > .page-header-actions {
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
        font-size: 2.2rem;
        font-weight: 800;
        color: #fff;
        margin-bottom: 8px;
        letter-spacing: -0.8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .page-subtitle {
        margin: 0;
        color: #94a3b8;
        font-size: .95rem;
    }

    .content-card {
        background: var(--admin-card-bg);
        border-radius: 20px;
        border: 1px solid var(--admin-border);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
        overflow: hidden;
    }

    .toolbar {
        padding: 20px 24px;
        border-bottom: 1px solid var(--admin-border);
        background: #fff;
        display: flex;
        gap: 14px;
        align-items: center;
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
        gap: 12px;
        flex-wrap: wrap;
    }

    .search-box {
        position: relative;
        flex: 1 1 340px;
        min-width: 220px;
    }

    .search-box input {
        width: 100%;
        padding: 12px 16px 12px 42px;
        height: 46px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.9rem;
        background: #f8fafc;
        color: #1e293b;
        transition: all 0.25s ease;
        outline: none;
    }

    .search-box i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1rem;
        transition: color 0.25s ease;
    }

    .search-box input:focus {
        border-color: var(--admin-secondary);
        background: #fff;
        box-shadow: 0 0 0 3px rgba(30, 27, 75, 0.1);
    }

    .search-box:focus-within i {
        color: var(--admin-secondary);
    }

    .filter-select {
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        padding: 0 16px;
        height: 46px;
        font-size: .88rem;
        min-width: 180px;
        background: #f8fafc;
        color: #334155;
        outline: none;
        transition: all 0.25s ease;
    }

    .filter-select:focus {
        border-color: var(--admin-secondary);
        background-color: #ffffff;
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

    .table-responsive {
        overflow-x: auto;
    }

    .table-responsive::-webkit-scrollbar {
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f5f9;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    .table {
        margin-bottom: 0;
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        background: #f8fafc;
        color: var(--admin-text-muted);
        font-size: .78rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        padding: 18px 24px;
        border-bottom: 1.5px solid var(--admin-border);
        white-space: nowrap;
    }

    .table td {
        padding: 18px 24px;
        vertical-align: middle;
        border-bottom: 1px solid var(--admin-border);
        font-size: .86rem;
    }

    .table tr:hover {
        background-color: #f8fafc;
    }

    .course-title {
        font-weight: 800;
        color: var(--admin-text-main);
        margin: 0 0 4px;
        font-size: .9rem;
        line-height: 1.45;
    }

    .badge-cat {
        background: rgba(30, 27, 75, 0.08);
        color: var(--admin-secondary);
        padding: 4px 10px;
        border-radius: 6px;
        font-size: .72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
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
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.08);
    }

    .trainer-name {
        font-weight: 700;
        color: var(--admin-text-main);
        font-size: .86rem;
    }

    .badge-status {
        padding: 6px 14px;
        border-radius: 100px;
        font-size: .75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .badge-approved-status {
        background: rgba(16, 185, 129, 0.12);
        color: #059669;
    }

    .badge-approved-status::before {
        content: '';
        width: 6px;
        height: 6px;
        background-color: #10b981;
        border-radius: 50%;
    }

    .btn-action-view {
        background: rgba(16, 185, 129, 0.08);
        border: 1px solid rgba(16, 185, 129, 0.2);
        color: #059669;
        height: 36px;
        padding: 0 14px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .btn-action-view:hover {
        background: #10b981;
        color: #ffffff;
        border-color: #10b981;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(16, 185, 129, 0.2);
    }

    .btn-revisions-link {
        height: 44px;
        color: #991b1b;
        border: 1.5px solid #fecaca;
        background: #fef2f2;
        padding: 0 16px;
        border-radius: 10px;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.25s ease;
    }

    .btn-revisions-link:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #991b1b;
        transform: translateY(-1px);
    }

    .btn-back-header {
        background: rgba(255,255,255,.12);
        border: 1px solid rgba(255,255,255,.2);
        color: #fff;
        padding: 10px 18px;
        border-radius: 12px;
        font-size: .86rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all .2s;
        backdrop-filter: blur(4px);
    }

    .btn-back-header:hover {
        background: rgba(255,255,255,.2);
        color: #fff;
        transform: translateY(-1px);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-state i {
        font-size: 3.2rem;
        color: #cbd5e1;
        margin-bottom: 16px;
        display: block;
    }

    .pagination-wrapper {
        padding: 20px 24px;
        border-top: 1px solid var(--admin-border);
        background: #f8fafc;
    }

    @media (max-width: 991.98px) {
        .page-header {
            padding: 30px;
            border-radius: 20px;
        }
        .page-title {
            font-size: 1.8rem;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: stretch !important;
            gap: 16px;
            padding: 24px !important;
        }

        .page-header-actions {
            flex-direction: column;
            width: 100%;
        }

        .page-header-actions .btn-back-header {
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
        }
        .btn-revisions-link {
            width: 100%;
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

        .btn-action-view {
            width: 100%;
            justify-content: center;
            height: 40px;
        }
    }
</style>
@endpush

@section('admin-trainer-content')
<div class="page-header mb-4">
    <div>
        <h1 class="page-title">
            <i class="bi bi-check-circle-fill me-2"></i>
            Materi Disetujui
        </h1>
        <p class="page-subtitle">
            Daftar materi course dan event yang sudah tervalidasi.
        </p>
    </div>

    <div class="page-header-actions">
        <a href="{{ route('admin.trainer.material.approvals') }}" class="btn-back-header">
            <i class="bi bi-arrow-left"></i>
            Kembali ke Antrean
        </a>
        <a href="{{ route('admin.trainer.material.rejected') }}" class="btn-back-header" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.25);">
            Lihat Revisi
            <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</div>

<!-- Unified Toolbar -->
<div class="content-card mb-4">
    @php
        $activeSearch = trim((string) request('search', ''));
        $activeDeadlineFilter = $deadlineFilter ?? 'all';
        $hasActiveFilter = ($activeDeadlineFilter !== 'all') || ($activeSearch !== '');
    @endphp
    <div class="toolbar">
        <div class="toolbar-left">
            <form method="GET" class="toolbar-form">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           name="search"
                           placeholder="Cari materi course atau event yang disetujui..."
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

        @if($hasActiveFilter)
            <div class="toolbar-right">
                <a href="{{ route('admin.trainer.material.approved') }}" class="btn-revisions-link" style="color: #991b1b; border-color: #fecaca; background: #fef2f2; height: 42px;">
                    <i class="bi bi-x-circle"></i> Reset Filter
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Course Materials Content Card -->
<div class="content-card mb-4">


    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Materi Course</th>
                    <th>Trainer</th>
                    <th>Isi Modul</th>
                    <th>Tanggal Disetujui</th>
                    <th>Status</th>
                    <th>Tenggat</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($approvedMaterials as $material)
                    <tr>
                        <td data-label="Materi Course">
                            <h6 class="course-title">
                                {{ \Illuminate\Support\Str::limit($material->name ?? 'Course Tanpa Judul', 45) }}
                            </h6>

                            <span class="badge-cat">
                                <i class="bi bi-folder2 me-1"></i>
                                {{ $material->category->name ?? 'Umum' }}
                            </span>
                        </td>

                        <td data-label="Trainer">
                            <div class="trainer-info">
                                <img src="{{ $material->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($material->trainer?->name ?? 'Trainer') . '&background=3949ab&color=fff&bold=true' }}"
                                     class="trainer-avatar"
                                     alt="Trainer">

                                <div>
                                    <div class="trainer-name">
                                        {{ $material->trainer?->name ?? 'Anonim' }}
                                    </div>
                                    <div style="font-size:.72rem;color:#64748b;">
                                        {{ $material->trainer?->email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td data-label="Isi Modul">
                            <div style="font-weight:700;color:#334155;">
                                {{ $material->modules_count ?? 0 }} File/Kuis
                            </div>
                        </td>

                        <td data-label="Tanggal Disetujui">
                            <div style="font-weight:700;color:#334155;">
                                {{ $material->approved_at ? \Carbon\Carbon::parse($material->approved_at)->format('d M Y') : '-' }}
                            </div>

                            <div style="font-size:.75rem;color:#64748b;">
                                {{ $material->approved_at ? \Carbon\Carbon::parse($material->approved_at)->diffForHumans() : '' }}
                            </div>
                        </td>

                        <td data-label="Status">
                            <span class="badge-status badge-approved-status">Live</span>
                        </td>

                        <td data-label="Tenggat">
                            @php
                                $monitor = $deadlineMonitoring[$material->id] ?? null;
                            @endphp

                            <div style="font-weight:700;color:#334155;">
                                {{ $monitor['deadline_text'] ?? 'Belum ditentukan' }}
                            </div>

                            <div style="font-size:.75rem;color:{{ ($monitor['status'] ?? '') === 'late' ? '#b91c1c' : '#64748b' }};">
                                {{ $monitor['status_text'] ?? 'Tanpa deadline' }}
                            </div>
                        </td>

                        <td data-label="Aksi" class="text-end">
                            <a href="{{ route('admin.trainer.material.show', $material->id) }}"
                               class="btn-action-view"
                               title="Tinjau Modul">
                                <i class="bi bi-eye-fill"></i> Tinjau
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h5 class="fw-bold text-dark">Belum ada materi course</h5>
                                <p class="text-muted mb-0">
                                    Belum ada materi course yang disetujui saat ini.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($approvedMaterials, 'hasPages') && $approvedMaterials->hasPages())
        <div class="pagination-wrapper">
            {{ $approvedMaterials->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<!-- Event Materials Content Card -->
<div class="content-card">


    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Materi Event</th>
                    <th>Trainer</th>
                    <th>Isi Modul</th>
                    <th>Tanggal Disetujui</th>
                    <th>Status</th>
                    <th>Tenggat</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse(($approvedEventModules ?? collect()) as $event)
                    @php
                        $eventDeadline = $event->material_deadline;
                        $approvedAt = $event->material_approved_at ?? $event->updated_at;
                        $eventLate = $eventDeadline ? now()->gt(\Carbon\Carbon::parse($eventDeadline)) : false;
                    @endphp

                    <tr>
                        <td data-label="Materi Event">
                            <h6 class="course-title">
                                {{ \Illuminate\Support\Str::limit($event->title ?? 'Event Tanpa Judul', 48) }}
                            </h6>

                            <div class="text-muted" style="font-size:.72rem;">
                                {{ $event->jenis ?? 'Event' }}

                                @if($event->event_date)
                                    • {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                                @endif
                            </div>
                        </td>

                        <td data-label="Trainer">
                            <div class="trainer-info">
                                <img src="{{ $event->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($event->trainer?->name ?? 'Trainer') . '&background=3949ab&color=fff&bold=true' }}"
                                     class="trainer-avatar"
                                     alt="Trainer">

                                <div>
                                    <div class="trainer-name">
                                        {{ $event->trainer?->name ?? 'Anonim' }}
                                    </div>

                                    <div style="font-size:.72rem;color:#64748b;">
                                        {{ $event->trainer?->email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td data-label="Isi Modul">
                            <div style="font-weight:700;color:#334155;">
                                {{ $event->trainerModules->count() }} Dokumen Modul
                            </div>
                        </td>

                        <td data-label="Tanggal Disetujui">
                            <div style="font-weight:700;color:#334155;">
                                {{ $approvedAt ? \Carbon\Carbon::parse($approvedAt)->format('d M Y') : '-' }}
                            </div>

                            <div style="font-size:.72rem;color:#64748b;">
                                {{ $approvedAt ? \Carbon\Carbon::parse($approvedAt)->diffForHumans() : '' }}
                            </div>
                        </td>

                        <td data-label="Status">
                            <span class="badge-status badge-approved-status">Live</span>
                        </td>

                        <td data-label="Tenggat">
                            <div style="font-weight:700;color:#334155;">
                                {{ $eventDeadline ? \Carbon\Carbon::parse($eventDeadline)->format('d M Y H:i') : 'Belum ditentukan' }}
                            </div>

                            <div style="font-size:.72rem;color:{{ $eventLate ? '#b91c1c' : '#64748b' }};">
                                {{ $eventLate ? 'Melewati deadline' : 'Tanpa deadline' }}
                            </div>
                        </td>

                        <td data-label="Aksi" class="text-end">
                            <a href="{{ route('admin.event-material.show', $event->id) }}"
                               class="btn-action-view"
                               title="Tinjau Modul">
                                <i class="bi bi-eye-fill"></i> Tinjau
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-folder2-open"></i>
                                <h6 class="fw-bold mt-2 mb-1" style="color:#334155;">
                                    Belum ada materi event approved
                                </h6>
                                <p class="text-muted mb-0">
                                    Saat ini belum ada modul event yang disetujui.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection