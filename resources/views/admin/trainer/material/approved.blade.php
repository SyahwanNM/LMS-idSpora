@extends('layouts.admin-trainer')
@extends('layouts.admin-trainer')

@section('title', 'Approved Materials')

@push('admin-trainer-styles')
<style>
    :root {
        --admin-primary: #1e1b4b;
        --admin-secondary: #4338ca;
        --admin-card-bg: #ffffff;
        --admin-border: #e2e8f0;
        --admin-text-main: #0f172a;
        --admin-text-muted: #64748b;
    }

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
        border: 1px solid rgba(255, 255, 255, 0.14);
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
        background: radial-gradient(circle, rgba(255,255,255,.2) 0%, rgba(255,255,255,0) 70%);
        pointer-events: none;
    }

    .page-header > div,
    .page-header > a {
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
        color: rgba(255,255,255,.86);
        font-size: .92rem;
    }

    .content-card {
        background: var(--admin-card-bg);
        border-radius: 20px;
        border: 1px solid var(--admin-border);
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.05);
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
        font-size: .9rem;
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
        border-color: #166534;
        outline: none;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(22,101,52,.1);
    }

    .filter-select {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 0 12px;
        height: 44px;
        font-size: .88rem;
        min-width: 180px;
        background: #fff;
        color: #334155;
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
        font-weight: 700;
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

    .course-title {
        font-weight: 700;
        color: var(--admin-text-main);
        margin: 0 0 4px;
        font-size: .88rem;
        line-height: 1.4;
    }

    .badge-cat {
        background: #e2e8f0;
        color: #475569;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: .7rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
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
        object-fit: cover;
    }

    .trainer-name {
        font-weight: 600;
        color: var(--admin-text-main);
        font-size: .84rem;
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: .75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .badge-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
    }

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
        height: 34px;
        padding: 0 10px;
        border-radius: 7px;
        font-size: .78rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all .2s;
        white-space: nowrap;
    }

    .btn-action:hover {
        border-color: var(--admin-secondary);
        color: var(--admin-secondary);
        background: #f8fafc;
    }

    .btn-back-header {
        background: rgba(255,255,255,.18);
        border: 1px solid rgba(255,255,255,.34);
        color: #fff;
        padding: 10px 16px;
        border-radius: 10px;
        font-size: .84rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all .2s;
        backdrop-filter: blur(2px);
    }

    .btn-back-header:hover {
        background: rgba(255,255,255,.28);
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

    @media (max-width: 992px) {
        .toolbar,
        .page-header {
            flex-direction: column;
            align-items: stretch;
        }

        .toolbar-left,
        .toolbar-form,
        .search-box,
        .filter-select,
        .toolbar-right {
            width: 100%;
        }

        .toolbar-right {
            justify-content: flex-start;
            margin-left: 0;
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

    <a href="{{ route('admin.trainer.material.approvals') }}" class="btn-back-header">
        <i class="bi bi-arrow-left"></i>
        Kembali ke Antrean
    </a>
</div>

<div class="content-card">
    <div class="toolbar">
        <div class="toolbar-left">
            <form method="GET" class="toolbar-form">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           name="search"
                           placeholder="Cari materi course yang disetujui..."
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
            <a href="{{ route('admin.trainer.material.rejected') }}"
               class="btn-action"
               style="height:44px;color:#991b1b;border-color:#fecaca;background:#fef2f2;">
                Lihat Revisi
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
                    <th>Tanggal Disetujui</th>
                    <th>Status</th>
                    <th>Tenggat</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($approvedMaterials as $material)
                    <tr>
                        <td>
                            <h6 class="course-title">
                                {{ \Illuminate\Support\Str::limit($material->name ?? 'Course Tanpa Judul', 45) }}
                            </h6>

                            <span class="badge-cat">
                                <i class="bi bi-folder2 me-1"></i>
                                {{ $material->category->name ?? 'Umum' }}
                            </span>
                        </td>

                        <td>
                            <div class="trainer-info">
                                <img src="{{ $material->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($material->trainer?->name ?? 'Trainer') . '&background=3949ab&color=fff&bold=true' }}"
                                     class="trainer-avatar"
                                     alt="Trainer">

                                <div>
                                    <div class="trainer-name">
                                        {{ $material->trainer?->name ?? 'Anonim' }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div style="font-weight:600;color:#334155;">
                                {{ $material->modules_count ?? 0 }} File/Kuis
                            </div>
                        </td>

                        <td>
                            <div style="font-weight:600;color:#334155;">
                                {{ $material->approved_at ? \Carbon\Carbon::parse($material->approved_at)->format('d M Y') : '-' }}
                            </div>

                            <div style="font-size:.75rem;color:#64748b;">
                                {{ $material->approved_at ? \Carbon\Carbon::parse($material->approved_at)->diffForHumans() : '' }}
                            </div>
                        </td>

                        <td>
                            <span class="badge-status badge-approved-status">Live</span>
                        </td>

                        <td>
                            @php
                                $monitor = $deadlineMonitoring[$material->id] ?? null;
                            @endphp

                            <div style="font-weight:600;color:#334155;">
                                {{ $monitor['deadline_text'] ?? 'Belum ditentukan' }}
                            </div>

                            <div style="font-size:.75rem;color:{{ ($monitor['status'] ?? '') === 'late' ? '#b91c1c' : '#64748b' }};">
                                {{ $monitor['status_text'] ?? 'Tanpa deadline' }}
                            </div>
                        </td>

                        <td class="text-end">
                            <a href="{{ route('admin.trainer.material.show', $material->id) }}"
                               class="btn-action"
                               style="color:#166534;border-color:#bbf7d0;background:#f0fdf4;">
                                <i class="bi bi-eye"></i>
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
        <div class="p-3 border-top">
            {{ $approvedMaterials->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<div class="content-card mt-4">
    <div class="toolbar">
        <div class="toolbar-left">
            <form method="GET" class="toolbar-form">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text"
                           name="search"
                           placeholder="Cari materi event yang disetujui..."
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
            <a href="{{ route('admin.trainer.material.rejected') }}"
               class="btn-action"
               style="height:44px;color:#991b1b;border-color:#fecaca;background:#fef2f2;">
                Lihat Revisi
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
                    <th>Tanggal Disetujui</th>
                    <th>Status</th>
                    <th>Tenggat</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse(($approvedEventModules ?? collect()) as $eventModule)
                    @php
                        $eventModel = $eventModule->event ?? null;

                        $eventTitle =
                            $eventModule->display_title
                            ?? $eventModel?->title
                            ?? $eventModel?->name
                            ?? $eventModule->original_name
                            ?? 'Event Tanpa Judul';

                        $eventType =
                            $eventModule->display_type
                            ?? $eventModel?->jenis
                            ?? $eventModel?->type
                            ?? 'Event';

                        $eventDate =
                            $eventModule->display_date
                            ?? $eventModel?->event_date
                            ?? $eventModel?->start_date
                            ?? null;

                        $eventDeadline =
                            $eventModule->material_deadline
                            ?? $eventModel?->material_deadline
                            ?? null;

                        $approvedAt =
                            $eventModule->module_verified_at
                            ?? $eventModule->reviewed_at
                            ?? $eventModule->updated_at
                            ?? $eventModule->created_at;

                        $eventLate = $eventDeadline ? now()->gt(\Carbon\Carbon::parse($eventDeadline)) : false;

                        $eventId = $eventModule->event_id ?? $eventModel?->id;
                    @endphp

                    <tr>
                        <td>
                            <h6 class="course-title">
                                {{ \Illuminate\Support\Str::limit($eventTitle, 48) }}
                            </h6>

                            <div class="text-muted" style="font-size:.72rem;">
                                {{ $eventType }}

                                @if($eventDate)
                                    • {{ \Carbon\Carbon::parse($eventDate)->format('d M Y') }}
                                @endif
                            </div>

                            @if(!empty($eventModule->original_name))
                                <div class="text-muted mt-1" style="font-size:.7rem;">
                                    File: {{ \Illuminate\Support\Str::limit($eventModule->original_name, 38) }}
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="trainer-info">
                                <img src="{{ $eventModule->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($eventModule->trainer?->name ?? 'Trainer') . '&background=3949ab&color=fff&bold=true' }}"
                                     class="trainer-avatar"
                                     alt="Trainer">

                                <div>
                                    <div class="trainer-name">
                                        {{ $eventModule->trainer?->name ?? 'Anonim' }}
                                    </div>

                                    <div style="font-size:.72rem;color:#64748b;">
                                        {{ $eventModule->trainer?->email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td>
                            <div style="font-weight:600;color:#334155;">
                                1 Dokumen Modul
                            </div>
                        </td>

                        <td>
                            <div style="font-weight:600;color:#334155;">
                                {{ $approvedAt ? \Carbon\Carbon::parse($approvedAt)->format('d M Y') : '-' }}
                            </div>

                            <div style="font-size:.72rem;color:#64748b;">
                                {{ $approvedAt ? \Carbon\Carbon::parse($approvedAt)->diffForHumans() : '' }}
                            </div>
                        </td>

                        <td>
                            <span class="badge-status badge-approved-status">Live</span>
                        </td>

                        <td>
                            <div style="font-weight:600;color:#334155;">
                                {{ $eventDeadline ? \Carbon\Carbon::parse($eventDeadline)->format('d M Y H:i') : 'Belum ditentukan' }}
                            </div>

                            <div style="font-size:.72rem;color:{{ $eventLate ? '#b91c1c' : '#64748b' }};">
                                {{ $eventLate ? 'Melewati deadline' : 'Tanpa deadline' }}
                            </div>
                        </td>

                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-2 flex-wrap">
                                @if($eventId)
                                    <a href="{{ route('admin.event-material.stream', $eventId) }}?assignment_id={{ $eventModule->id }}"
                                       target="_blank"
                                       class="btn-action"
                                       style="color:#166534;border-color:#bbf7d0;background:#f0fdf4;">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <a href="{{ route('admin.event-material.show', $eventId) }}"
                                       class="btn-action">
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                @else
                                    <span class="text-muted" style="font-size:.75rem;">Event tidak tersedia</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state" style="padding:36px 20px;">
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