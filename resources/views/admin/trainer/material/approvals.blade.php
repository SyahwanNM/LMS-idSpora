@extends('layouts.admin-trainer')

@section('title', 'Persetujuan Materi - Menunggu')

@push('admin-trainer-styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');

    :root {
        --admin-primary: #1e1b4b;
        --admin-secondary: #1e1b4b;
        --admin-accent: #1e1b4b;
        --admin-card-bg: #ffffff;
        --admin-border: #e2e8f0;
        --admin-text-main: #0f172a;
        --admin-text-muted: #64748b;
        --status-pending-bg: #f1f5f9;
        --status-pending-text: #475569;
    }

    .material-page {
        font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        animation: fadeIn 0.4s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .page-header {
        background-color: var(--admin-secondary);
        border-radius: 20px;
        padding: 30px 36px;
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

    .page-title {
        font-size: 2rem;
        font-weight: 800;
        letter-spacing: -0.8px;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff;
    }

    .page-subtitle {
        margin: 0;
        color: #94a3b8;
        font-size: .92rem;
        font-weight: 500;
    }

    .page-header-actions {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
        position: relative;
        z-index: 2;
    }

    .btn-header-action {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        color: #fff;
        height: 40px;
        padding: 0 18px;
        border-radius: 12px;
        font-size: .84rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        backdrop-filter: blur(8px);
        transition: all 0.25s ease;
    }

    .btn-header-action:hover {
        background: rgba(255, 255, 255, 0.16);
        border-color: rgba(255, 255, 255, 0.25);
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-header-action i {
        font-size: 1rem;
    }

    /* Stats Grid Simplification */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--admin-card-bg);
        border-radius: 20px;
        padding: 24px;
        border: 1px solid var(--admin-border);
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.01);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.06);
        border-color: #cbd5e1;
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .stat-card.pending .stat-icon {
        background: #f1f5f9;
        color: #475569;
    }
    .stat-card.approved .stat-icon {
        background: rgba(16, 185, 129, 0.08);
        color: #059669;
    }
    .stat-card.rejected .stat-icon {
        background: rgba(239, 68, 68, 0.08);
        color: #dc2626;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.05);
    }

    .stat-info h3 {
        font-size: 1.8rem;
        font-weight: 800;
        margin: 0 0 4px;
        line-height: 1;
        letter-spacing: -0.5px;
        color: var(--admin-text-main);
    }

    .stat-info p {
        margin: 0;
        color: var(--admin-text-muted);
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: .5px;
        text-transform: uppercase;
    }

    /* Content Card */
    .content-card {
        background: var(--admin-card-bg);
        border-radius: 20px;
        border: 1px solid var(--admin-border);
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.01);
        overflow: hidden;
    }

    /* Custom Card Header styling */
    .card-header-custom {
        padding: 24px 24px 8px;
        background: transparent;
        border-bottom: none;
    }

    .header-icon-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .header-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .header-icon.course {
        background: rgba(30, 27, 75, 0.06);
        color: var(--admin-secondary);
    }

    .header-icon.event {
        background: rgba(30, 27, 75, 0.06);
        color: var(--admin-secondary);
    }

    .header-title {
        font-size: 1.05rem;
        font-weight: 800;
        color: var(--admin-text-main);
        margin: 0;
        letter-spacing: -0.2px;
    }

    .header-subtitle {
        font-size: 0.76rem;
        color: var(--admin-text-muted);
        margin: 2px 0 0;
        font-weight: 500;
    }

    /* Toolbar styling */
    .toolbar {
        padding: 16px 24px;
        border-bottom: 1px solid var(--admin-border);
        background: #fff;
        display: flex;
        gap: 12px;
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
        padding: 10px 16px 10px 38px;
        height: 42px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.88rem;
        background: #f8fafc;
        color: #1e293b;
        transition: all 0.25s ease;
        outline: none;
        font-weight: 500;
    }

    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 0.95rem;
        transition: all 0.25s ease;
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
        padding: 0 14px;
        height: 42px;
        font-size: .86rem;
        min-width: 170px;
        background: #f8fafc;
        color: #334155;
        outline: none;
        transition: all 0.25s ease;
        font-weight: 600;
    }

    .filter-select:focus {
        border-color: var(--admin-secondary);
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(30, 27, 75, 0.1);
    }

    .toolbar-right {
        display: flex;
        gap: 8px;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: wrap;
        margin-left: auto;
    }

    .btn-approved-link {
        height: 42px;
        color: #475569;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        padding: 0 16px;
        border-radius: 12px;
        font-size: 0.84rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.25s ease;
    }

    .btn-approved-link:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
        transform: translateY(-1px);
    }

    .btn-approved-link i {
        font-size: 0.9rem;
        transition: transform 0.2s ease;
    }

    .btn-approved-link:hover i {
        transform: translateX(3px);
    }

    /* Table styles */
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
        font-size: .76rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .8px;
        padding: 16px 24px;
        border-bottom: 1.5px solid var(--admin-border);
        white-space: nowrap;
    }

    .table td {
        padding: 16px 24px;
        vertical-align: middle;
        border-bottom: 1px solid var(--admin-border);
        font-size: .84rem;
    }

    .table tr {
        transition: background-color 0.2s ease;
    }

    .table tr:hover {
        background-color: rgba(248, 250, 252, 0.7);
    }

    .course-title {
        font-weight: 800;
        color: var(--admin-text-main);
        margin: 0 0 4px;
        font-size: .88rem;
        line-height: 1.45;
    }

    .badge-cat {
        background: #f1f5f9;
        color: #475569;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: .7rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        border: 1px solid #e2e8f0;
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
        border: 2px solid #ffffff;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05);
    }

    .trainer-name {
        font-weight: 700;
        color: var(--admin-text-main);
        font-size: .84rem;
    }

    /* Module Pill */
    .module-pill {
        background: #f8fafc;
        color: #475569;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: .78rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid #e2e8f0;
    }

    .module-pill i {
        font-size: 0.84rem;
        color: #64748b;
    }

    /* Date and Deadline Block */
    .date-block {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .date-main {
        font-weight: 700;
        color: #334155;
    }

    .date-sub {
        font-size: .72rem;
        color: #64748b;
        font-weight: 500;
    }

    .badge-status {
        padding: 5px 10px;
        border-radius: 6px;
        font-size: .72rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .badge-pending {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .badge-pending::before {
        content: '';
        width: 6px;
        height: 6px;
        background-color: #475569;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .deadline-block {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .deadline-date {
        font-size: 0.8rem;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
    }

    .deadline-status {
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .deadline-status.late {
        color: #dc2626;
    }

    .deadline-status.on-time {
        color: #475569;
    }

    /* Tinjau Action Button styling */
    .btn-action-tinjau {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #475569;
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
        transition: all 0.25s ease;
        white-space: nowrap;
    }

    .btn-action-tinjau:hover {
        background: var(--admin-secondary);
        color: #ffffff;
        border-color: var(--admin-secondary);
        transform: translateY(-1px);
    }

    .btn-action-tinjau i {
        font-size: 0.9rem;
    }

    /* Empty States Styling */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 48px 24px;
    }

    .empty-state-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 16px;
    }

    .pagination-wrapper {
        padding: 16px 24px;
        border-top: 1px solid var(--admin-border);
        background: #f8fafc;
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

        .btn-action-tinjau {
            width: 100%;
            justify-content: center;
            height: 40px;
        }
    }

    @media (max-width: 1200px) {
        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
    }

    @media (max-width: 991.98px) {
        .page-header {
            padding: 24px;
            border-radius: 16px;
        }
        .page-title {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 768px) {
        .stat-grid {
            grid-template-columns: 1fr;
            gap: 14px;
        }

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

        .page-header-actions .btn-header-action {
            width: 100% !important;
            justify-content: center;
        }

        .toolbar {
            padding: 16px;
            gap: 10px;
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
        .btn-approved-link {
            width: 100%;
            justify-content: center;
        }
        .btn-header-action {
            width: 100%;
        }
    }

    @media (max-width: 575.98px) {
        .page-header {
            padding: 24px;
            border-radius: 16px;
        }
        .page-title {
            font-size: 1.5rem;
            gap: 8px;
        }
        .page-subtitle {
            font-size: 0.84rem;
        }
        .stat-card {
            padding: 16px;
            border-radius: 16px;
            gap: 12px;
        }
        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            font-size: 1.15rem;
        }
        .stat-info h3 {
            font-size: 1.5rem;
        }
        .stat-info p {
            font-size: 0.68rem;
        }
        .card-header-custom {
            padding: 20px 20px 8px;
        }
        .toolbar {
            padding: 12px 20px;
        }
    }
</style>
@endpush

@section('admin-trainer-content')
<div class="material-page">

    <!-- Page Header -->
    <div class="page-header mb-4">
        <div>
            <h1 class="page-title">
                <i class="bi bi-hourglass-split"></i>
                Persetujuan Materi
            </h1>
            <p class="page-subtitle">
                Review materi course dan event dari trainer sebelum ditayangkan ke publik.
            </p>
        </div>

        <div class="page-header-actions">
            <button class="btn-header-action" onclick="window.location.reload();">
                <i class="bi bi-arrow-clockwise"></i>
                Segarkan
            </button>
            <a href="{{ route('admin.trainer.material.approved') }}" class="btn-header-action">
                Lihat Disetujui
                <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>

    <!-- Stats Section -->
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
                        <input type="text" name="search" placeholder="Cari materi course, event, atau nama trainer..."
                            value="{{ request('search') }}">
                    </div>

                    <select class="filter-select" name="deadline_filter" onchange="this.form.submit()">
                        <option value="all" {{ ($deadlineFilter ?? 'all') === 'all' ? 'selected' : '' }}>
                            Semua Deadline
                        </option>
                        <option value="overdue" {{ ($deadlineFilter ?? 'all') === 'all' || ($deadlineFilter ?? 'all') === 'overdue' ? 'selected' : '' }}>
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

            @if($hasActiveFilter)
                <div class="toolbar-right">
                    <a href="{{ route('admin.trainer.material.approvals') }}" class="btn-approved-link" style="color: #991b1b; border-color: #fecaca; background: #fef2f2;">
                        <i class="bi bi-x-circle"></i> Reset Filter
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Course Materials Card -->
    <div class="content-card mb-5">
        <div class="card-header-custom">
            <div class="header-icon-title">
                <div class="header-icon course"><i class="bi bi-journal-bookmark-fill"></i></div>
                <div>
                    <h5 class="header-title">Materi Course</h5>
                    <p class="header-subtitle">Daftar materi course yang diajukan oleh trainer</p>
                </div>
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
                            <td data-label="Materi Course">
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

                            <td data-label="Trainer">
                                <div class="trainer-info">
                                    <img src="{{ $material->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($material->trainer?->name ?? 'Trainer') . '&background=3949ab&color=fff&bold=true' }}"
                                        class="trainer-avatar" alt="Trainer">

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
                                <span class="module-pill">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                    {{ $material->modules_count ?? 0 }} File/Kuis
                                </span>
                            </td>

                            <td data-label="Tanggal Submit">
                                <div class="date-block">
                                    <span class="date-main">{{ $material->updated_at?->format('d M Y') ?? $material->created_at?->format('d M Y') }}</span>
                                    <span class="date-sub">{{ $material->updated_at?->diffForHumans() ?? $material->created_at?->diffForHumans() }}</span>
                                </div>
                            </td>

                            <td data-label="Status">
                                <span class="badge-status badge-pending">
                                    Menunggu Tinjauan
                                </span>
                            </td>

                            <td data-label="Tenggat">
                                @php $monitor = $deadlineMonitoring[$material->id] ?? null; @endphp

                                <div class="deadline-block">
                                    @if($monitor)
                                        <span class="deadline-date"><i class="bi bi-calendar-event me-1"></i> {{ $monitor['deadline_text'] ?? 'Belum ditentukan' }}</span>
                                        @if(($monitor['status'] ?? '') === 'late')
                                            <span class="deadline-status late"><i class="bi bi-exclamation-circle-fill"></i> {{ $monitor['status_text'] }}</span>
                                        @else
                                            <span class="deadline-status on-time"><i class="bi bi-check-circle-fill"></i> {{ $monitor['status_text'] }}</span>
                                        @endif
                                    @else
                                        <span class="deadline-date text-muted">Belum ditentukan</span>
                                        <span class="deadline-status text-muted">Tanpa deadline</span>
                                    @endif
                                </div>
                            </td>

                            <td data-label="Aksi" class="text-end">
                                <a href="{{ route('admin.trainer.material.show', $material->id) }}"
                                    class="btn-action-tinjau"
                                    title="Tinjau Modul">
                                    Tinjau <i class="bi bi-play-fill"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        @if(($pendingEventModules ?? collect())->isEmpty())
                            <tr>
                                <td colspan="7">
                                    <div class="empty-state">
                                        <div class="empty-state-icon">
                                            <i class="bi bi-inbox"></i>
                                        </div>
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
            <div class="pagination-wrapper">
                {{ $pendingMaterials->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- Event Materials Card -->
    <div class="content-card">
        <div class="card-header-custom">
            <div class="header-icon-title">
                <div class="header-icon event"><i class="bi bi-calendar-event-fill"></i></div>
                <div>
                    <h5 class="header-title">Materi Event</h5>
                    <p class="header-subtitle">Daftar materi event yang diajukan oleh trainer</p>
                </div>
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
                    @forelse(($pendingEventModules ?? collect()) as $event)
                        <tr>
                            <td data-label="Materi Event">
                                <div class="course-info">
                                    <div>
                                        <h6 class="course-title">
                                            {{ \Illuminate\Support\Str::limit($event->event->title ?? '-', 48) }}
                                        </h6>
                                        <div class="text-muted" style="font-size:.75rem;">
                                            {{ $event->event->jenis ?? 'Event' }}
                                            @if($event->event->event_date)
                                                • {{ $event->event->event_date->format('d M Y') }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td data-label="Trainer">
                                <div class="trainer-info">
                                    <img src="{{ $event->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($event->trainer?->name ?? 'Trainer') . '&background=3949ab&color=fff&bold=true' }}"
                                        class="trainer-avatar" alt="Trainer">

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
                                <span class="module-pill">
                                    <i class="bi bi-file-earmark-text-fill"></i>
                                    {{ $event->event->trainerModules->where('trainer_id', $event->trainer_id)->count() }} Dokumen Modul
                                </span>
                            </td>

                            <td data-label="Tanggal Submit">
                                @php
                                    $latestSubmit = $event->event->trainerModules->where('trainer_id', $event->trainer_id)->max('created_at');
                                @endphp
                                <div class="date-block">
                                    <span class="date-main">{{ $latestSubmit ? \Carbon\Carbon::parse($latestSubmit)->format('d M Y') : ($event->updated_at?->format('d M Y') ?? '-') }}</span>
                                    @if($latestSubmit)
                                        <span class="date-sub">{{ \Carbon\Carbon::parse($latestSubmit)->diffForHumans() }}</span>
                                    @endif
                                </div>
                            </td>

                            <td data-label="Status">
                                <span class="badge-status badge-pending">
                                    Menunggu Tinjauan
                                </span>
                            </td>

                            <td data-label="Tenggat">
                                @php
                                    $eventDeadline = $event->event->material_deadline;
                                    $eventLate = $eventDeadline ? now()->gt($eventDeadline) : false;
                                @endphp

                                <div class="deadline-block">
                                    @if($eventDeadline)
                                        <span class="deadline-date"><i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($eventDeadline)->format('d M Y H:i') }}</span>
                                        @if($eventLate)
                                            <span class="deadline-status late"><i class="bi bi-exclamation-circle-fill"></i> Melewati deadline</span>
                                        @else
                                            <span class="deadline-status on-time"><i class="bi bi-check-circle-fill"></i> Tepat Waktu</span>
                                        @endif
                                    @else
                                        <span class="deadline-date text-muted">Belum ditentukan</span>
                                        <span class="deadline-status text-muted">Tanpa deadline</span>
                                    @endif
                                </div>
                            </td>

                            <td data-label="Aksi" class="text-end">
                                <a href="{{ route('admin.event-material.show', $event->event_id) }}?assignment_id={{ $event->id }}"
                                    class="btn-action-tinjau"
                                    title="Tinjau Modul">
                                    Tinjau <i class="bi bi-play-fill"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <i class="bi bi-folder2-open"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">Belum Ada Materi Event Pending</h5>
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