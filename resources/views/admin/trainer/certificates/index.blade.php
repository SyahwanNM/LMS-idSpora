@extends('layouts.admin-trainer')

@section('title', 'Sertifikat & Penghargaan')

@php
    use App\Models\Event;
    use App\Models\Course;
    use App\Models\TrainerCertificateAsset;

    $tab = $tab ?? request('tab', 'pendingItems');

    $pendingItems = $pendingItems ?? collect();
    $certificates = $certificates ?? collect();
    $trainers = $trainers ?? collect();

    function certAssets($model) {
        if (!$model) {
            return collect();
        }

        return TrainerCertificateAsset::query()
            ->where('certifiable_type', get_class($model))
            ->where('certifiable_id', $model->id)
            ->get();
    }

    function certStatus($model) {
        $assets = certAssets($model);

        $hasTemplate = $assets->where('type', 'template')->isNotEmpty();
        $hasLogo = $assets->where('type', 'logo')->isNotEmpty();
        $hasSignature = $assets->where('type', 'signature')->isNotEmpty();

        if ($hasTemplate && $hasLogo && $hasSignature) {
            return 'ready';
        }

        if ($hasTemplate || $hasLogo || $hasSignature) {
            return 'configured';
        }

        return 'not-configured';
    }

    $allPrograms = $pendingItems->concat($certificates);

    $totalProgram = $allPrograms->count();

    $totalPublished = method_exists($trainers, 'sum')
        ? $trainers->sum('published_certificates_count')
        : 0;

    $readyCount = $allPrograms->filter(fn ($item) => certStatus($item) === 'ready')->count();

    $notConfiguredCount = $allPrograms->filter(fn ($item) => certStatus($item) === 'not-configured')->count();
@endphp

@push('admin-trainer-styles')
<style>
    :root {
        --cert-primary: #2f3fcb;
        --cert-primary-2: #4858db;
        --cert-soft: #eef4ff;
        --cert-border: #e7eef8;
        --cert-muted: #607089;
        --cert-warning: #f59e0b;
        --cert-danger: #ef4444;
        --cert-success: #059669;
        --cert-ready-bg: #ecfdf5;
        --cert-ready-color: #059669;
        --cert-configured-bg: #eef3ff;
        --cert-configured-color: #4858db;
        --cert-missing-bg: #fff1f2;
        --cert-missing-color: #ef4444;
        --btn-start: #2f3fcb;
        --btn-end: #4858db;
        --btn-soft-bg: #eef4ff;
        --btn-warning-bg: #fff5f6;
    }

    .cert-dashboard {
        width: 100%;
    }

    .cert-hero {
        background: linear-gradient(135deg, #2935b8 0%, #4858db 55%, #dce3ff 100%);
        border-radius: 22px;
        padding: 38px 42px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        min-height: 190px;
        box-shadow: 0 18px 40px rgba(47, 63, 203, 0.16);
    }

    .cert-hero::after {
        content: '';
        position: absolute;
        right: 78px;
        top: 28px;
        width: 255px;
        height: 145px;
        background: rgba(255, 255, 255, .20);
        border-radius: 28px;
    }

    .cert-hero::before {
        content: '✦';
        position: absolute;
        right: 112px;
        top: 82px;
        color: rgba(255, 255, 255, .78);
        font-size: 52px;
        z-index: 2;
    }

    .cert-hero-content {
        position: relative;
        z-index: 4;
        max-width: 640px;
    }

    .page-eyebrow {
        font-size: .76rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
        color: rgba(255,255,255,.88);
    }

    .page-eyebrow::before {
        content: '';
        width: 22px;
        height: 2px;
        background: rgba(255,255,255,.9);
        border-radius: 4px;
    }

    .cert-hero h1 {
        font-size: 2.35rem;
        font-weight: 900;
        margin: 0 0 10px;
        letter-spacing: -1px;
    }

    .cert-hero p {
        color: rgba(255,255,255,.92);
        font-size: 1rem;
        line-height: 1.65;
        margin: 0;
    }

    .metric-card {
        background: #fff;
        border: 1px solid var(--cert-border);
        border-radius: 20px;
        padding: 22px;
        display: flex;
        align-items: center;
        gap: 18px;
        min-height: 118px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
    }

    .metric-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }

    .metric-icon.blue {
        background: #eef1ff;
        color: var(--cert-primary);
    }

    .metric-icon.purple {
        background: #f1e8ff;
        color: #7c3aed;
    }

    .metric-icon.green {
        background: #dff8ed;
        color: var(--cert-success);
    }

    .metric-icon.yellow {
        background: #fff2cc;
        color: var(--cert-warning);
    }

    .metric-label {
        font-size: .86rem;
        color: #0f172a;
        margin-bottom: 6px;
    }

    .metric-value {
        font-size: 1.85rem;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
    }

    .metric-desc {
        margin-top: 6px;
        color: var(--cert-muted);
        font-size: .86rem;
    }

    .cert-tab-wrapper {
        display: flex;
        gap: 6px;
        margin-top: 26px;
    }

    .cert-tab-btn {
        border: 1px solid var(--cert-border);
        border-bottom: 0;
        background: #fff;
        color: #475569;
        padding: 14px 28px;
        border-radius: 14px 14px 0 0;
        font-weight: 800;
        min-width: 220px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .cert-tab-btn.active {
        color: var(--cert-primary);
        border-top: 3px solid var(--cert-primary);
        background: #fff;
    }

    .cert-tab-btn:hover {
        background: linear-gradient(90deg, var(--cert-primary), var(--cert-primary-2));
        color: #fff;
        border-color: var(--cert-primary-2);
    }

    .cert-tab-btn:hover .tab-count {
        background: rgba(255,255,255,.18);
        color: #fff;
    }

    .tab-count {
        background: #eef1ff;
        color: var(--cert-primary);
        font-size: .75rem;
        border-radius: 999px;
        padding: 3px 9px;
        font-weight: 900;
    }

    .filter-card {
        background: #fff;
        border: 1px solid var(--cert-border);
        border-radius: 0 20px 20px 20px;
        padding: 22px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
        margin-bottom: 18px;
    }

    .filter-input {
        width: 100%;
        height: 48px;
        border: 1px solid #dbe3ef;
        border-radius: 12px;
        background: #fff;
        padding: 0 16px;
        font-size: .92rem;
        outline: none;
    }

    .filter-input:focus {
        border-color: var(--cert-primary);
        box-shadow: 0 0 0 4px rgba(47, 63, 203, .08);
    }

    .search-wrap {
        position: relative;
    }

    .search-wrap i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .search-wrap .filter-input {
        padding-left: 48px;
    }

    .btn-reset-filter {
        height: 48px;
        border-radius: 12px;
        border: 1px solid #dbe3ef;
        background: #fff;
        font-weight: 700;
        color: #334155;
        width: 100%;
    }

    .custom-tab-pane {
        display: none;
    }

    .custom-tab-pane.active {
        display: block;
    }

    .certificate-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(300px, 1fr));
        gap: 20px;
    }

    .certificate-card {
        background: #fff;
        border: 1px solid #f1f5f9;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 18px 36px rgba(15, 23, 42, .06);
        min-height: 300px;
        display: flex;
        flex-direction: column;
    }

    .card-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        margin-bottom: 18px;
    }

    .type-badge {
        background: #eef4ff;
        color: var(--cert-primary);
        font-size: .78rem;
        font-weight: 900;
        border-radius: 10px;
        padding: 6px 12px;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .status-badge {
        font-size: .78rem;
        font-weight: 900;
        border-radius: 999px;
        padding: 8px 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        text-transform: uppercase;
    }

    .status-badge.ready {
        background: var(--cert-ready-bg);
        color: var(--cert-ready-color);
    }

    .status-badge.configured {
        background: var(--cert-configured-bg);
        color: var(--cert-configured-color);
    }

    .status-badge.missing {
        background: var(--cert-missing-bg);
        color: var(--cert-missing-color);
    }

    .program-main {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 12px;
    }

    .program-icon {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        background: var(--btn-warning-bg);
        color: var(--cert-missing-color);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .program-icon.ready {
        background: #ecfdf5;
        color: var(--cert-success);
    }

    .program-icon.purple {
        background: #f6f3ff;
        color: #7c3aed;
    }

    .program-icon.red {
        background: var(--btn-warning-bg);
        color: var(--cert-missing-color);
    }

    .program-title {
        font-size: 1.02rem;
        font-weight: 900;
        color: #0b1220;
        margin: 0 0 6px;
        line-height: 1.25;
    }

    .program-date {
        font-size: .86rem;
        color: #607089;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .program-meta {
        margin-top: 6px;
        margin-bottom: 14px;
        color: var(--cert-muted);
        font-size: .86rem;
    }

    .program-meta strong {
        color: var(--cert-primary);
        font-weight: 900;
        margin-left: 6px;
        display: inline-block;
    }

    .asset-section {
        border-top: 1px solid #eef6ff;
        padding-top: 14px;
        margin-top: auto;
    }

    .asset-title {
        font-size: .9rem;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 12px;
    }

    .asset-list {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .asset-item {
        display: flex;
        gap: 8px;
        align-items: center;
        color: var(--cert-muted);
        font-weight: 800;
        font-size: .82rem;
    }

    .asset-item + .asset-item {
        padding-left: 8px;
        border-left: 1px solid #eef6ff;
    }

    .asset-label i {
        color: #94a3b8;
    }

    .asset-status {
        font-size: .8rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .asset-status.ok {
        color: var(--cert-success);
    }

    .asset-status.no {
        color: var(--cert-danger);
    }

    .asset-status.neutral {
        color: #94a3b8;
    }

    .btn-manage-template {
        margin-top: 12px;
        width: 100%;
        height: 48px;
        border-radius: 12px;
        border: 0;
        background: linear-gradient(90deg, var(--btn-start), var(--btn-end));
        color: #fff;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 12px;
        padding: 0 18px;
        text-decoration: none;
        box-shadow: 0 10px 24px rgba(72, 88, 219, .14);
    }

    .btn-manage-template:hover {
        color: #fff;
        filter: brightness(.97);
    }

    .btn-manage-template .bi-chevron-right {
        margin-left: auto;
    }

    .btn-manage-template.soft {
        background: var(--btn-soft-bg);
        color: var(--cert-primary);
        box-shadow: none;
    }

    .btn-manage-template.warning {
        background: var(--btn-warning-bg);
        color: var(--cert-missing-color);
        border: 1px solid #ffdce0;
        box-shadow: none;
    }

    .empty-card {
        background: #fff;
        border: 1px solid var(--cert-border);
        border-radius: 18px;
        padding: 60px 20px;
        text-align: center;
        grid-column: 1 / -1;
        color: var(--cert-muted);
    }

    @media (max-width: 1400px) {
        .certificate-grid {
            grid-template-columns: repeat(2, minmax(300px, 1fr));
        }
    }

    @media (max-width: 768px) {
        .cert-hero {
            padding: 28px;
        }

        .cert-hero h1 {
            font-size: 1.8rem;
        }

        .certificate-grid {
            grid-template-columns: 1fr;
        }

        .cert-tab-wrapper {
            flex-direction: column;
        }

        .cert-tab-btn {
            width: 100%;
            border-radius: 14px;
            border: 1px solid var(--cert-border);
        }

        .filter-card {
            border-radius: 20px;
        }

        .asset-list {
            flex-direction: column;
            align-items: flex-start;
        }

        .asset-item + .asset-item {
            padding-left: 0;
            border-left: 0;
        }
    }
</style>
@endpush

@section('admin-trainer-content')
<div class="cert-dashboard">

    <section class="cert-hero">
        <div class="cert-hero-content">
            <div class="page-eyebrow">Recognition System</div>
            <h1>Sertifikat & Penghargaan</h1>
            <p>Kelola aset sertifikat untuk event dan kursus yang sudah dibuat.</p>
        </div>
    </section>

    <div class="row g-4 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-icon blue">
                    <i class="bi bi-calendar4-week"></i>
                </div>
                <div>
                    <div class="metric-label">Event / Course</div>
                    <div class="metric-value">{{ $totalProgram }}</div>
                    <div class="metric-desc">Total program</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-icon purple">
                    <i class="bi bi-award"></i>
                </div>
                <div>
                    <div class="metric-label">Sertifikat Terbit</div>
                    <div class="metric-value">{{ $totalPublished }}</div>
                    <div class="metric-desc">Sertifikat sudah diterbitkan</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-icon green">
                    <i class="bi bi-check-lg"></i>
                </div>
                <div>
                    <div class="metric-label">Siap Terbit</div>
                    <div class="metric-value">{{ $readyCount }}</div>
                    <div class="metric-desc">Aset lengkap</div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="metric-card">
                <div class="metric-icon yellow">
                    <i class="bi bi-clock"></i>
                </div>
                <div>
                    <div class="metric-label">Belum Konfigurasi</div>
                    <div class="metric-value">{{ $notConfiguredCount }}</div>
                    <div class="metric-desc">Perlu pengaturan aset</div>
                </div>
            </div>
        </div>
    </div>

    <div class="cert-tab-wrapper">
        <button type="button"
            class="cert-tab-btn custom-tab-btn {{ $tab === 'pendingItems' ? 'active' : '' }}"
            data-target="pendingItems-pane">
            <i class="bi bi-calendar-event"></i>
            Sertifikat Event
            <span class="tab-count">{{ $pendingItems->count() }}</span>
        </button>

        <button type="button"
            class="cert-tab-btn custom-tab-btn {{ $tab === 'certificates' ? 'active' : '' }}"
            data-target="certificates-pane">
            <i class="bi bi-mortarboard"></i>
            Sertifikat Kursus
            <span class="tab-count">{{ $certificates->count() }}</span>
        </button>
    </div>

    <div class="filter-card">
        <div class="row g-3 align-items-center">
            <div class="col-lg-5">
                <div class="search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text"
                        id="certSearch"
                        class="filter-input"
                        placeholder="Cari berdasarkan nama program...">
                </div>
            </div>

            <div class="col-lg-4">
                <select id="certStatus" class="filter-input">
                    <option value="all">Semua Status Kesiapan</option>
                    <option value="ready">Siap Terbit</option>
                    <option value="configured">Dikonfigurasi</option>
                    <option value="not-configured">Belum Dikonfigurasi</option>
                </select>
            </div>

            <div class="col-lg-3">
                <button type="button" id="certReset" class="btn-reset-filter">
                    <i class="bi bi-arrow-counterclockwise me-2"></i>
                    Reset Filter
                </button>
            </div>
        </div>
    </div>

    <div class="tab-content-container">
        <div class="custom-tab-pane {{ $tab === 'pendingItems' ? 'active' : '' }}" id="pendingItems-pane">
            <div class="certificate-grid">
                @forelse($pendingItems as $event)
                    @php
                        $assets = certAssets($event);
                        $status = certStatus($event);

                        $hasTemplate = $assets->where('type', 'template')->isNotEmpty();
                        $hasLogo = $assets->where('type', 'logo')->isNotEmpty();
                        $hasSignature = $assets->where('type', 'signature')->isNotEmpty();

                        $eventDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
                    @endphp

                    <div class="certificate-card cert-row"
                        data-title="{{ strtolower($event->title ?? '') }}"
                        data-status="{{ $status }}">

                        <div class="card-top">
                            <span class="type-badge">Event</span>

                            @if($status === 'ready')
                                <span class="status-badge ready">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Siap Terbit
                                </span>
                            @elseif($status === 'configured')
                                <span class="status-badge configured">
                                    <i class="bi bi-circle-fill"></i>
                                    Dikonfigurasi
                                </span>
                            @else
                                <span class="status-badge missing">
                                    <i class="bi bi-exclamation-lg"></i>
                                    Belum Konfigurasi
                                </span>
                            @endif
                        </div>

                        <div class="program-main">
                            <div class="program-icon {{ $status === 'ready' ? 'ready' : ($status === 'configured' ? 'purple' : 'red') }}">
                                <i class="bi bi-calendar-event"></i>
                            </div>

                            <div>
                                <h5 class="program-title">{{ $event->title ?? '-' }}</h5>

                                <div class="program-date">
                                    <i class="bi bi-calendar2-week"></i>
                                    {{ $eventDate ? $eventDate->translatedFormat('d M Y') : 'Tanpa Tanggal' }}
                                </div>
                            </div>
                        </div>

                        <div class="program-meta">
                            Peserta Terdaftar
                            <strong>
                                <i class="bi bi-people me-1"></i>
                                {{ $event->registrations_count ?? 0 }}
                            </strong>
                        </div>

                        <div class="asset-section">
                            <div class="asset-title">Aset Sertifikat</div>

                            <div class="asset-list">
                                <div class="asset-item">
                                    <div class="asset-label">
                                        <i class="bi bi-file-earmark-text"></i>
                                        Template
                                    </div>
                                    <div class="asset-status {{ $hasTemplate ? 'ok' : 'neutral' }}">
                                        <i class="bi {{ $hasTemplate ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                        {{ $hasTemplate ? 'Tersedia' : 'Belum Ada' }}
                                    </div>
                                </div>

                                <div class="asset-item">
                                    <div class="asset-label">
                                        <i class="bi bi-image"></i>
                                        Logo
                                    </div>
                                    <div class="asset-status {{ $hasLogo ? 'ok' : 'no' }}">
                                        <i class="bi {{ $hasLogo ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                        {{ $hasLogo ? 'Tersedia' : 'Belum Ada' }}
                                    </div>
                                </div>

                                <div class="asset-item">
                                    <div class="asset-label">
                                        <i class="bi bi-pen"></i>
                                        Tanda Tangan
                                    </div>
                                    <div class="asset-status {{ $hasSignature ? 'ok' : 'no' }}">
                                        <i class="bi {{ $hasSignature ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                        {{ $hasSignature ? 'Tersedia' : 'Belum Ada' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.trainer.certificates.edit', [
                            'trainer' => $event->trainer_id,
                            'context' => 'event',
                            'id' => $event->id,
                        ]) }}"
                        class="btn-manage-template {{ $status === 'not-configured' ? 'warning' : ($status === 'configured' ? 'soft' : '') }}">
                            <i class="bi bi-gear"></i>
                            Kelola Template
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="empty-card">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Data event tidak ditemukan.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="custom-tab-pane {{ $tab === 'certificates' ? 'active' : '' }}" id="certificates-pane">
            <div class="certificate-grid">
                @forelse($certificates as $course)
                    @php
                        $assets = certAssets($course);
                        $status = certStatus($course);

                        $hasTemplate = $assets->where('type', 'template')->isNotEmpty();
                        $hasLogo = $assets->where('type', 'logo')->isNotEmpty();
                        $hasSignature = $assets->where('type', 'signature')->isNotEmpty();
                    @endphp

                    <div class="certificate-card cert-row"
                        data-title="{{ strtolower($course->name ?? '') }}"
                        data-status="{{ $status }}">

                        <div class="card-top">
                            <span class="type-badge">Course</span>

                            @if($status === 'ready')
                                <span class="status-badge ready">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Siap Terbit
                                </span>
                            @elseif($status === 'configured')
                                <span class="status-badge configured">
                                    <i class="bi bi-circle-fill"></i>
                                    Dikonfigurasi
                                </span>
                            @else
                                <span class="status-badge missing">
                                    <i class="bi bi-exclamation-lg"></i>
                                    Belum Konfigurasi
                                </span>
                            @endif
                        </div>

                        <div class="program-main">
                            <div class="program-icon {{ $status === 'ready' ? 'ready' : ($status === 'configured' ? 'purple' : 'red') }}">
                                <i class="bi bi-mortarboard"></i>
                            </div>

                            <div>
                                <h5 class="program-title">{{ $course->name ?? '-' }}</h5>

                                <div class="program-date">
                                    <i class="bi bi-folder2-open"></i>
                                    {{ $course->category->name ?? 'General' }}
                                </div>
                            </div>
                        </div>

                        <div class="program-meta">
                            Siswa Terdaftar
                            <strong>
                                <i class="bi bi-people me-1"></i>
                                {{ $course->enrollments_count ?? 0 }}
                            </strong>
                        </div>

                        <div class="asset-section">
                            <div class="asset-title">Aset Sertifikat</div>

                            <div class="asset-list">
                                <div class="asset-item">
                                    <div class="asset-label">
                                        <i class="bi bi-file-earmark-text"></i>
                                        Template
                                    </div>
                                    <div class="asset-status {{ $hasTemplate ? 'ok' : 'neutral' }}">
                                        <i class="bi {{ $hasTemplate ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                        {{ $hasTemplate ? 'Tersedia' : 'Belum Ada' }}
                                    </div>
                                </div>

                                <div class="asset-item">
                                    <div class="asset-label">
                                        <i class="bi bi-image"></i>
                                        Logo
                                    </div>
                                    <div class="asset-status {{ $hasLogo ? 'ok' : 'no' }}">
                                        <i class="bi {{ $hasLogo ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                        {{ $hasLogo ? 'Tersedia' : 'Belum Ada' }}
                                    </div>
                                </div>

                                <div class="asset-item">
                                    <div class="asset-label">
                                        <i class="bi bi-pen"></i>
                                        Tanda Tangan
                                    </div>
                                    <div class="asset-status {{ $hasSignature ? 'ok' : 'no' }}">
                                        <i class="bi {{ $hasSignature ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                        {{ $hasSignature ? 'Tersedia' : 'Belum Ada' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <a href="{{ route('admin.trainer.certificates.edit', [
                            'trainer' => $course->trainer_id,
                            'context' => 'course',
                            'id' => $course->id,
                        ]) }}"
                        class="btn-manage-template {{ $status === 'not-configured' ? 'warning' : ($status === 'configured' ? 'soft' : '') }}">
                            <i class="bi bi-gear"></i>
                            Kelola Template
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </div>
                @empty
                    <div class="empty-card">
                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                        Data kursus tidak ditemukan.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('admin-trainer-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabBtns = document.querySelectorAll('.custom-tab-btn');
        const tabPanes = document.querySelectorAll('.custom-tab-pane');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanes.forEach(p => p.classList.remove('active'));

                this.classList.add('active');

                const targetId = this.getAttribute('data-target');
                document.getElementById(targetId)?.classList.add('active');

                const tabName = targetId.replace('-pane', '');
                const url = new URL(window.location);
                url.searchParams.set('tab', tabName);
                window.history.pushState({}, '', url);
            });
        });

        const searchInput = document.getElementById('certSearch');
        const statusFilter = document.getElementById('certStatus');
        const resetBtn = document.getElementById('certReset');

        function runFilter() {
            const term = (searchInput?.value || '').toLowerCase().trim();
            const status = statusFilter?.value || 'all';

            document.querySelectorAll('.cert-row').forEach(row => {
                const title = row.getAttribute('data-title') || '';
                const rowStatus = row.getAttribute('data-status') || '';

                const matchSearch = term === '' || title.includes(term);
                const matchStatus = status === 'all' || rowStatus === status;

                row.style.display = matchSearch && matchStatus ? '' : 'none';
            });
        }

        searchInput?.addEventListener('input', runFilter);
        statusFilter?.addEventListener('change', runFilter);

        resetBtn?.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = 'all';
            runFilter();
        });
    });
</script>
@endpush