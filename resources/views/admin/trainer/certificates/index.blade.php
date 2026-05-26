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

    function certAssets($model)
    {
        if (!$model) {
            return collect();
        }

        return TrainerCertificateAsset::query()
            ->where('certifiable_type', get_class($model))
            ->where('certifiable_id', $model->id)
            ->get();
    }

    function certStatus($model)
    {
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

    $readyCount = $allPrograms->filter(fn($item) => certStatus($item) === 'ready')->count();

    $notConfiguredCount = $allPrograms->filter(fn($item) => certStatus($item) === 'not-configured')->count();
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
            color: #2f3fcb;
        }

        .metric-icon.purple {
            background: #dde4ff;
            color: #3730a3;
        }

        .metric-icon.green {
            background: #e0e7ff;
            color: #4338ca;
        }

        .metric-icon.yellow {
            background: #c7d2fe;
            color: #1e1b8e;
        }

        .metric-icon.orange {
            background: #fff7ed;
            color: #c2410c;
        }

        .metric-icon.emerald {
            background: #ecfdf5;
            color: #059669;
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

        .status-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 8px 0 22px;
        }

        .status-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--cert-border);
            background: #fff;
            border-radius: 999px;
            padding: 8px 12px;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-dot.ready {
            background: #059669;
        }

        .status-dot.configured {
            background: #4858db;
        }

        .status-dot.missing {
            background: #ef4444;
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
            background: rgba(255, 255, 255, .18);
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

        /* ── Certificate Grid ── */
        .certificate-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(300px, 1fr));
            gap: 20px;
        }

        /* ── Certificate Card ── */
        .certificate-card {
            background: #fff;
            border: 1px solid #dde4f5;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(47, 63, 203, .07);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform .22s ease, box-shadow .22s ease;
        }

        .certificate-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(47, 63, 203, .14);
        }

        /* Card inner padding wrapper */
        .card-inner {
            padding: 20px 20px 16px;
            display: flex;
            flex-direction: column;
            gap: 14px;
            flex: 1;
        }

        /* Card top row: type badge + status badge */
        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        /* Type badge — indigo muda ringan (sesuai foto) */
        .type-badge {
            font-size: .7rem;
            font-weight: 800;
            border-radius: 8px;
            padding: 5px 13px;
            text-transform: uppercase;
            letter-spacing: .7px;
            flex-shrink: 0;
            background: #e8eaf6;
            color: #3949ab;
            border: 1px solid #c5cae9;
        }

        /* Status badge */
        .status-badge {
            font-size: .75rem;
            font-weight: 800;
            border-radius: 999px;
            padding: 5px 14px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ready → outline putih bertepi biru (persis foto) */
        .status-badge.ready {
            background: #ecfdf5;
            color: #047857;
            border: 1.5px solid #a7f3d0;
        }

        /* configured → biru medium filled */
        .status-badge.configured {
            background: #eef3ff;
            color: #4858db;
            border: 1.5px solid #c7d2fe;
        }

        /* missing → biru tua gelap */
        .status-badge.missing {
            background: #fff1f2;
            color: #b91c1c;
            border: 1.5px solid #fecdd3;
        }

        /* Program info block */
        .program-main {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Icon indigo muda (sesuai foto) */
        .program-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: #e8eaf6;
            color: #3949ab;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .program-icon.ready {
            background: #e8eaf6;
            color: #3949ab;
        }

        .program-icon.configured {
            background: #e8eaf6;
            color: #3949ab;
        }

        .program-icon.missing {
            background: #c5cae9;
            color: #1a237e;
        }

        .program-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .program-title {
            font-size: 1rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
            line-height: 1.35;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .program-date {
            font-size: .8rem;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Peserta Terdaftar — vertikal */
        .program-meta-wrap {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .program-meta-label {
            font-size: .8rem;
            color: #64748b;
            font-weight: 500;
        }

        .program-meta-count {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 1.1rem;
            font-weight: 900;
            color: #3949ab;
        }

        .program-meta-count i {
            font-size: 1.1rem;
        }

        /* Asset section — flat */
        .asset-section {
            border-top: 1px solid #e8eeff;
            padding-top: 14px;
        }

        .asset-title {
            font-size: .9rem;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 12px;
        }

        .asset-flat-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0;
        }

        .asset-flat-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .asset-flat-label {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: .82rem;
            font-weight: 600;
            color: #334155;
        }

        .asset-flat-label i {
            color: #7986cb;
            font-size: .9rem;
        }

        .asset-flat-status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: .82rem;
            font-weight: 700;
        }

        /* ok → biru cerah sesuai foto */
        .asset-flat-status.ok {
            color: #3949ab;
        }

        /* no → biru tua */
        .asset-flat-status.no {
            color: #1a237e;
        }

        /* neutral → biru abu */
        .asset-flat-status.neutral {
            color: #90a4c8;
        }

        /* Manage button — full width */
        .btn-manage-template {
            margin: 0 20px 20px;
            height: 48px;
            border-radius: 12px;
            border: none;
            font-weight: 800;
            font-size: .9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 0 18px;
            text-decoration: none;
            transition: all .2s ease;
            flex-shrink: 0;
        }

        .btn-manage-template .bi-chevron-right {
            margin-left: auto;
            font-size: .85rem;
        }

        /* default → indigo-violet gradien (sesuai foto) */
        .btn-manage-template:not(.soft):not(.warning) {
            background: linear-gradient(90deg, #3949ab 0%, #5c6bc0 100%);
            color: #fff;
            box-shadow: 0 6px 20px rgba(57, 73, 171, .30);
        }

        .btn-manage-template:not(.soft):not(.warning):hover {
            color: #fff;
            box-shadow: 0 8px 26px rgba(57, 73, 171, .42);
            filter: brightness(1.07);
        }

        /* configured → biru muda */
        .btn-manage-template.soft {
            background: #eef1ff;
            color: #2f3fcb;
            border: 1px solid #c7d2fe;
        }

        .btn-manage-template.soft:hover {
            background: #e0e7ff;
            color: #1e1b8e;
        }

        /* missing → biru navy transparan */
        .btn-manage-template.warning {
            background: #dde4ff;
            color: #1e1b8e;
            border: 1px solid #a5b4fc;
        }

        .btn-manage-template.warning:hover {
            background: #c7d2fe;
            color: #1e1b8e;
        }

        /* Empty state */
        .empty-card {
            background: #fff;
            border: 1.5px dashed #c7d2fe;
            border-radius: 18px;
            padding: 60px 20px;
            text-align: center;
            grid-column: 1 / -1;
            color: #64748b;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .empty-card i {
            font-size: 2.5rem;
            color: #a5b4fc;
        }

        /* ── Responsive ── */
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
        }
    </style>
@endpush

@section('admin-trainer-content')
    <div class="cert-dashboard">

        <section class="cert-hero">
            <div class="cert-hero-content">
                <div class="page-eyebrow">Recognition System</div>
                <h1>Sertifikat & Penghargaan</h1>
                <p>Pantau program yang masih harus dikirim sertifikatnya dan lihat mana yang sudah selesai dikirim.</p>
            </div>
        </section>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-icon orange">
                        <i class="bi bi-send"></i>
                    </div>
                    <div>
                        <div class="metric-label">Harus Dikirim</div>
                        <div class="metric-value">{{ $totalPending }}</div>
                        <div class="metric-desc">Belum diproses</div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-icon emerald">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                    <div>
                        <div class="metric-label">Sudah Dikirim</div>
                        <div class="metric-value">{{ $totalPublished }}</div>
                        <div class="metric-desc">Selesai diproses</div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-icon blue">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div>
                        <div class="metric-label">Siap Dikirim</div>
                        <div class="metric-value">{{ $readyCount }}</div>
                        <div class="metric-desc">Tinggal kirim</div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="metric-card">
                    <div class="metric-icon yellow">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div>
                        <div class="metric-label">Belum Siap</div>
                        <div class="metric-value">{{ $notConfiguredCount }}</div>
                        <div class="metric-desc">Masih perlu aset</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="status-legend">
            <div class="status-legend-item"><span class="status-dot ready"></span> Siap Dikirim</div>
            <div class="status-legend-item"><span class="status-dot configured"></span> Perlu Dilengkapi</div>
            <div class="status-legend-item"><span class="status-dot missing"></span> Belum Siap</div>
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
                                        $assets = certAssets($event);
                                        $status = certStatus($event);

                                        $hasTemplate = $assets->where('type', 'template')->isNotEmpty();
                                        $hasLogo = $assets->where('type', 'logo')->isNotEmpty();
                                        $hasSignature = $assets->where('type', 'signature')->isNotEmpty();

                                        $eventDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
                                    @endphp

                                    <div class="certificate-card cert-row" data-title="{{ strtolower($event->title ?? '') }}"
                                        data-status="{{ $status }}">

                                        <div class="card-inner">
                                            {{-- Top: type badge + status badge --}}
                                            <div class="card-top">
                                                <span class="type-badge">EVENT</span>

                                                @if($status === 'ready')
                                                    <span class="status-badge ready">
                                                        <i class="bi bi-send-check-fill"></i> Siap Dikirim
                                                    </span>
                                                @elseif($status === 'configured')
                                                    <span class="status-badge configured">
                                                        <i class="bi bi-gear-wide-connected"></i> Perlu Dilengkapi
                                                    </span>
                                                @else
                                                    <span class="status-badge missing">
                                                        <i class="bi bi-exclamation-triangle-fill"></i> Belum Siap
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Program info --}}
                                            <div class="program-main">
                                                <div
                                                    class="program-icon {{ $status === 'ready' ? 'ready' : ($status === 'configured' ? 'configured' : 'missing') }}">
                                                    <i class="bi bi-calendar-event"></i>
                                                </div>
                                                <div class="program-info">
                                                    <h5 class="program-title">{{ $event->title ?? '-' }}</h5>
                                                    <div class="program-date">
                                                        <i class="bi bi-calendar3"></i>
                                                        {{ $eventDate ? $eventDate->translatedFormat('d M Y') : 'Tanpa Tanggal' }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Peserta Terdaftar — vertikal --}}
                                            <div class="program-meta-wrap">
                                                <span class="program-meta-label">Peserta Terdaftar</span>
                                                <span class="program-meta-count">
                                                    <i class="bi bi-people-fill"></i>
                                                    {{ $event->registrations_count ?? 0 }}
                                                </span>
                                            </div>

                                            {{-- Aset Sertifikat — flat row --}}
                                            <div class="asset-section">
                                                <div class="asset-title">Aset Sertifikat</div>
                                                <div class="asset-flat-row">
                                                    <div class="asset-flat-item">
                                                        <div class="asset-flat-label">
                                                            <i class="bi bi-file-earmark-text"></i> Template
                                                        </div>
                                                        <div class="asset-flat-status {{ $hasTemplate ? 'ok' : 'neutral' }}">
                                                            <i
                                                                class="bi {{ $hasTemplate ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                                            {{ $hasTemplate ? 'Tersedia' : 'Belum Ada' }}
                                                        </div>
                                                    </div>
                                                    <div class="asset-flat-item">
                                                        <div class="asset-flat-label">
                                                            <i class="bi bi-image"></i> Logo
                                                        </div>
                                                        <div class="asset-flat-status {{ $hasLogo ? 'ok' : 'no' }}">
                                                            <i class="bi {{ $hasLogo ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                            {{ $hasLogo ? 'Tersedia' : 'Belum Ada' }}
                                                        </div>
                                                    </div>
                                                    <div class="asset-flat-item">
                                                        <div class="asset-flat-label">
                                                            <i class="bi bi-pencil"></i> Tanda Tangan
                                                        </div>
                                                        <div class="asset-flat-status {{ $hasSignature ? 'ok' : 'no' }}">
                                                            <i
                                                                class="bi {{ $hasSignature ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                            {{ $hasSignature ? 'Tersedia' : 'Belum Ada' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Tombol Kelola Template — full width --}}
                                        <a href="{{ route('admin.trainer.certificates.edit', [
                            'trainer' => $event->trainer_id,
                            'context' => 'event',
                            'id' => $event->id,
                        ]) }}"
                                            class="btn-manage-template {{ $status === 'not-configured' ? 'warning' : ($status === 'configured' ? 'soft' : '') }}">
                                            <i class="bi bi-gear-fill"></i>
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
                                        $assets = certAssets($course);
                                        $status = certStatus($course);

                                        $hasTemplate = $assets->where('type', 'template')->isNotEmpty();
                                        $hasLogo = $assets->where('type', 'logo')->isNotEmpty();
                                        $hasSignature = $assets->where('type', 'signature')->isNotEmpty();
                                    @endphp

                                    <div class="certificate-card cert-row" data-title="{{ strtolower($course->name ?? '') }}"
                                        data-status="{{ $status }}">

                                        <div class="card-inner">
                                            {{-- Top: type badge + status badge --}}
                                            <div class="card-top">
                                                <span class="type-badge">COURSE</span>

                                                @if($status === 'ready')
                                                    <span class="status-badge ready">
                                                        <i class="bi bi-send-check-fill"></i> Siap Dikirim
                                                    </span>
                                                @elseif($status === 'configured')
                                                    <span class="status-badge configured">
                                                        <i class="bi bi-gear-wide-connected"></i> Perlu Dilengkapi
                                                    </span>
                                                @else
                                                    <span class="status-badge missing">
                                                        <i class="bi bi-exclamation-triangle-fill"></i> Belum Siap
                                                    </span>
                                                @endif
                                            </div>

                                            {{-- Program info --}}
                                            <div class="program-main">
                                                <div
                                                    class="program-icon {{ $status === 'ready' ? 'ready' : ($status === 'configured' ? 'configured' : 'missing') }}">
                                                    <i class="bi bi-mortarboard"></i>
                                                </div>
                                                <div class="program-info">
                                                    <h5 class="program-title">{{ $course->name ?? '-' }}</h5>
                                                    <div class="program-date">
                                                        <i class="bi bi-folder2-open"></i>
                                                        {{ $course->category->name ?? 'General' }}
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- Siswa Terdaftar — vertikal --}}
                                            <div class="program-meta-wrap">
                                                <span class="program-meta-label">Siswa Terdaftar</span>
                                                <span class="program-meta-count">
                                                    <i class="bi bi-people-fill"></i>
                                                    {{ $course->enrollments_count ?? 0 }}
                                                </span>
                                            </div>

                                            {{-- Aset Sertifikat — flat row --}}
                                            <div class="asset-section">
                                                <div class="asset-title">Aset Sertifikat</div>
                                                <div class="asset-flat-row">
                                                    <div class="asset-flat-item">
                                                        <div class="asset-flat-label">
                                                            <i class="bi bi-file-earmark-text"></i> Template
                                                        </div>
                                                        <div class="asset-flat-status {{ $hasTemplate ? 'ok' : 'neutral' }}">
                                                            <i
                                                                class="bi {{ $hasTemplate ? 'bi-check-circle-fill' : 'bi-dash-circle-fill' }}"></i>
                                                            {{ $hasTemplate ? 'Tersedia' : 'Belum Ada' }}
                                                        </div>
                                                    </div>
                                                    <div class="asset-flat-item">
                                                        <div class="asset-flat-label">
                                                            <i class="bi bi-image"></i> Logo
                                                        </div>
                                                        <div class="asset-flat-status {{ $hasLogo ? 'ok' : 'no' }}">
                                                            <i class="bi {{ $hasLogo ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                            {{ $hasLogo ? 'Tersedia' : 'Belum Ada' }}
                                                        </div>
                                                    </div>
                                                    <div class="asset-flat-item">
                                                        <div class="asset-flat-label">
                                                            <i class="bi bi-pencil"></i> Tanda Tangan
                                                        </div>
                                                        <div class="asset-flat-status {{ $hasSignature ? 'ok' : 'no' }}">
                                                            <i
                                                                class="bi {{ $hasSignature ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                            {{ $hasSignature ? 'Tersedia' : 'Belum Ada' }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Tombol Kelola Template — full width --}}
                                        <a href="{{ route('admin.trainer.certificates.edit', [
                            'trainer' => $course->trainer_id,
                            'context' => 'course',
                            'id' => $course->id,
                        ]) }}"
                                            class="btn-manage-template {{ $status === 'not-configured' ? 'warning' : ($status === 'configured' ? 'soft' : '') }}">
                                            <i class="bi bi-gear-fill"></i>
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

            resetBtn?.addEventListener('click', function () {
                if (searchInput) searchInput.value = '';
                if (statusFilter) statusFilter.value = 'all';
                runFilter();
            });
        });
    </script>
@endpush