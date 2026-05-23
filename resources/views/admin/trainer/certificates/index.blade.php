@extends('layouts.admin-trainer')

@section('title', 'Sertifikat & Penghargaan')

@php
    $tab = $tab ?? request('tab', 'pendingItems');

    $pendingItems = $pendingItems ?? collect();
    $certificates = $certificates ?? collect();

    $totalProgram = $pendingItems->count() + $certificates->count();
    $totalPublished = $certificates->count();
    $readyCount = $pendingItems->count();
    $notConfiguredCount = $pendingItems->filter(function ($event) {
        return empty($event->certificate_logo) && empty($event->certificate_signature);
    })->count();
@endphp

@push('admin-trainer-styles')
    <style>
        :root {
            --cert-primary: #2f3fcb;
            --cert-primary-2: #4858db;
            --cert-soft: #eef4ff;
            --cert-border: #e7eef8;
            --cert-muted: #607089;
            --cert-bg: #f8fafc;
            --cert-warning: #f59e0b;
            --cert-danger: #ef4444;
            --cert-success: #059669;

            --cert-ready-bg: #ecfdf5;
            --cert-ready-color: var(--cert-success);
            --cert-configured-bg: #eef3ff;
            --cert-configured-color: var(--cert-primary-2);
            --cert-missing-bg: #fff1f2;
            --cert-missing-color: var(--cert-danger);

            --btn-start: var(--cert-primary);
            --btn-end: var(--cert-primary-2);
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
            font-size: 0.76rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            color: rgba(255, 255, 255, .88);
        }

        .page-eyebrow::before {
            content: '';
            width: 22px;
            height: 2px;
            background: rgba(255, 255, 255, .9);
            border-radius: 4px;
        }

        .cert-hero h1 {
            font-size: 2.35rem;
            font-weight: 900;
            margin: 0 0 10px;
            letter-spacing: -1px;
        }

        .cert-hero p {
            color: rgba(255, 255, 255, .92);
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
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
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
            font-size: 0.86rem;
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
            font-size: 0.86rem;
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

        /* hover should use blue gradient (not yellow) */
        .cert-tab-btn:hover {
            background: linear-gradient(90deg, var(--cert-primary), var(--cert-primary-2));
            color: #ffffff;
            border-color: var(--cert-primary-2);
        }

        .cert-tab-btn:hover .tab-count {
            background: rgba(255, 255, 255, 0.18);
            color: #ffffff;
        }

        .tab-count {
            background: #eef1ff;
            color: var(--cert-primary);
            font-size: 0.75rem;
            border-radius: 999px;
            padding: 3px 9px;
            font-weight: 900;
        }

        .filter-card {
            background: #fff;
            border: 1px solid var(--cert-border);
            border-radius: 0 20px 20px 20px;
            padding: 22px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
            margin-bottom: 18px;
        }

        .filter-input {
            width: 100%;
            height: 48px;
            border: 1px solid #dbe3ef;
            border-radius: 12px;
            background: #fff;
            padding: 0 16px;
            font-size: 0.92rem;
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
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.06);
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
            color: #2f3fcb;
            font-size: 0.78rem;
            font-weight: 900;
            border-radius: 10px;
            padding: 6px 12px;
            text-transform: uppercase;
            letter-spacing: .6px;
        }

        .status-badge {
            font-size: 0.78rem;
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
            font-size: 0.86rem;
            color: #607089;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .program-meta {
            margin-top: 6px;
            margin-bottom: 14px;
            color: var(--cert-muted);
            font-size: 0.86rem;
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
            font-size: 0.9rem;
            font-weight: 900;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .asset-list {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .asset-item {
            display: flex;
            gap: 10px;
            align-items: center;
            color: var(--cert-muted);
            font-weight: 800;
            font-size: 0.86rem;
        }

        .asset-item+.asset-item {
            padding-left: 8px;
            border-left: 1px solid #eef6ff;
        }

        .asset-label i {
            color: #94a3b8;
        }

        .asset-status {
            font-size: 0.82rem;
            font-weight: 800;
        }

        .asset-status.ok {
            color: #059669;
        }

        .asset-status.no {
            color: #ef4444;
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
            box-shadow: 0 10px 24px rgba(72, 88, 219, 0.14);
        }

        .btn-manage-template i.bi-gear {
            color: #fff;
            opacity: .95;
        }

        .btn-manage-template .bi-chevron-right {
            margin-left: auto;
            color: #fff;
            opacity: .95;
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

        @media (max-width: 1400px) {
            .certificate-grid {
                grid-template-columns: repeat(2, minmax(320px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .certificate-grid {
                grid-template-columns: 1fr;
            }

            .certificate-card {
                padding: 22px;
            }

            .asset-list {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .asset-item {
                border-right: 0;
                border-bottom: 1px solid #e7ebf3;
                padding: 0 0 12px;
            }

            .asset-item:last-child {
                border-bottom: 0;
                padding-bottom: 0;
            }
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

        @media (max-width: 1200px) {
            .certificate-grid {
                grid-template-columns: repeat(2, minmax(280px, 1fr));
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
        }
    </style>
@endpush

@section('admin-trainer-content')
    <div class="cert-dashboard">

        <section class="cert-hero">
            <div class="cert-hero-content">
                <div class="page-eyebrow">Recognition System</div>
                <h1>Sertifikat & Penghargaan</h1>
                <p>
                    Kelola aset sertifikat untuk event dan kursus yang sudah dibuat.
                </p>
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
                        <div class="metric-desc">Event / Course siap terbit</div>
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
            <button type="button" class="cert-tab-btn custom-tab-btn {{ $tab === 'pendingItems' ? 'active' : '' }}"
                data-target="pendingItems-pane">
                <i class="bi bi-calendar-event"></i>
                Sertifikat Event
                <span class="tab-count">{{ $pendingItems->count() }}</span>
            </button>

            <button type="button" class="cert-tab-btn custom-tab-btn {{ $tab === 'certificates' ? 'active' : '' }}"
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
                        <input type="text" id="certSearch" class="filter-input"
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
                                        $isConfigured = !empty($event->certificate_logo) || !empty($event->certificate_signature);
                                        $eventDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
                                        $isFinished = $eventDate ? ($eventDate->isPast() || $eventDate->isToday()) : false;
                                        $status = $isConfigured ? ($isFinished ? 'ready' : 'configured') : 'not-configured';
                                    @endphp

                                    <div class="certificate-card cert-row" data-title="{{ strtolower($event->title) }}"
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
                                            <div
                                                class="program-icon {{ $status === 'configured' ? 'purple' : ($status === 'not-configured' ? 'red' : '') }}">
                                                <i class="bi bi-calendar-event"></i>
                                            </div>

                                            <div>
                                                <h5 class="program-title">{{ $event->title }}</h5>

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
                                                    <div class="asset-status {{ $isConfigured ? 'ok' : 'neutral' }}">
                                                        <i
                                                            class="bi {{ $isConfigured ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                                        {{ $isConfigured ? 'Tersedia' : 'Belum Ada' }}
                                                    </div>
                                                </div>

                                                <div class="asset-item">
                                                    <div class="asset-label">
                                                        <i class="bi bi-image"></i>
                                                        Logo
                                                    </div>
                                                    <div class="asset-status {{ !empty($event->certificate_logo) ? 'ok' : 'no' }}">
                                                        <i
                                                            class="bi {{ !empty($event->certificate_logo) ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                                        {{ !empty($event->certificate_logo) ? 'Tersedia' : 'Belum Ada' }}
                                                    </div>
                                                </div>

                                                <div class="asset-item">
                                                    <div class="asset-label">
                                                        <i class="bi bi-pen"></i>
                                                        Tanda Tangan
                                                    </div>
                                                    <div class="asset-status {{ !empty($event->certificate_signature) ? 'ok' : 'no' }}">
                                                        <i
                                                            class="bi {{ !empty($event->certificate_signature) ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                                        {{ !empty($event->certificate_signature) ? 'Tersedia' : 'Belum Ada' }}
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
                                            <i class="bi bi-chevron-right ms-auto"></i>
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
                                        $isConfigured = !empty($course->certificate_logo) || !empty($course->certificate_signature);
                                        $status = $isConfigured ? 'ready' : 'not-configured';
                                    @endphp

                                    <div class="certificate-card cert-row" data-title="{{ strtolower($course->name) }}"
                                        data-status="{{ $status }}">

                                        <div class="card-top">
                                            <span class="type-badge">Course</span>

                                            @if($isConfigured)
                                                <span class="status-badge ready">
                                                    <i class="bi bi-check-circle-fill"></i>
                                                    Siap Terbit
                                                </span>
                                            @else
                                                <span class="status-badge missing">
                                                    <i class="bi bi-exclamation-lg"></i>
                                                    Belum Konfigurasi
                                                </span>
                                            @endif
                                        </div>

                                        <div class="program-main">
                                            <div class="program-icon {{ $isConfigured ? '' : 'red' }}">
                                                <i class="bi bi-mortarboard"></i>
                                            </div>

                                            <div>
                                                <h5 class="program-title">{{ $course->name }}</h5>

                                                <div class="program-date">
                                                    <i class="bi bi-folder2-open"></i>
                                                    {{ $course->category->name ?? 'General' }}
                                                </div>

                                                <div class="program-meta">
                                                    <i class="bi bi-people"></i>
                                                    Siswa:
                                                    <strong>{{ $course->enrollments_count ?? 0 }}</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="asset-section">
                                            <div class="asset-title">Aset Sertifikat</div>

                                            <div class="asset-list">
                                                <div class="asset-item">
                                                    <div class="asset-label">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                        Template
                                                    </div>

                                                    <div class="asset-status {{ $isConfigured ? 'ok' : 'no' }}">
                                                        <i
                                                            class="bi {{ $isConfigured ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                                        {{ $isConfigured ? 'Tersedia' : 'Belum Ada' }}
                                                    </div>
                                                </div>

                                                <div class="asset-item">
                                                    <div class="asset-label">
                                                        <i class="bi bi-image"></i>
                                                        Logo
                                                    </div>

                                                    <div class="asset-status {{ !empty($course->certificate_logo) ? 'ok' : 'no' }}">
                                                        <i
                                                            class="bi {{ !empty($course->certificate_logo) ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                                        {{ !empty($course->certificate_logo) ? 'Tersedia' : 'Belum Ada' }}
                                                    </div>
                                                </div>

                                                <div class="asset-item">
                                                    <div class="asset-label">
                                                        <i class="bi bi-pen"></i>
                                                        Tanda Tangan
                                                    </div>

                                                    <div class="asset-status {{ !empty($course->certificate_signature) ? 'ok' : 'no' }}">
                                                        <i
                                                            class="bi {{ !empty($course->certificate_signature) ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                                        {{ !empty($course->certificate_signature) ? 'Tersedia' : 'Belum Ada' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <a href="{{ route('admin.trainer.certificates.edit', [
                            'trainer' => $course->trainer_id,
                            'context' => 'course',
                            'id' => $course->id,
                        ]) }}" class="btn-manage-template {{ !$isConfigured ? 'warning' : '' }}">
                                            <i class="bi bi-gear"></i>
                                            Kelola Template
                                            <i class="bi bi-chevron-right ms-auto"></i>
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

            resetBtn?.addEventListener('click', () => {
                if (searchInput) searchInput.value = '';
                if (statusFilter) statusFilter.value = 'all';
                runFilter();
            });
        });
    </script>
@endpush