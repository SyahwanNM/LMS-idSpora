@extends('layouts.trainer')

@section('title', 'My Courses')

@php
    $pageTitle = 'Courses';
    $breadcrumbs = [
        ['label' => 'Beranda', 'url' => route('trainer.dashboard')],
        ['label' => 'Dashboard', 'url' => route('trainer.dashboard')],
        ['label' => 'Kursus']
    ];
@endphp

@push('styles')
<style>
.courses-page {
    margin: 0;
    padding: 0;
}

.top-page {
    background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
    border-radius: var(--radius-2xl);
    padding: var(--spacing-3xl);
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(27, 23, 99, 0.15);
    margin-bottom: var(--spacing-2xl);
    width: 100%;
}

.glow-circle {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}

.glow-circle-1 {
    top: -80px;
    right: -80px;
    width: 192px;
    height: 192px;
    background: rgba(251, 191, 36, 0.1);
    filter: blur(60px);
}

.glow-circle-2 {
    bottom: -40px;
    left: -40px;
    width: 128px;
    height: 128px;
    background: rgba(99, 102, 241, 0.1);
    filter: blur(50px);
}

.top-page-inner {
    width: 100%;
    position: relative;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: var(--spacing-2xl);
}

.top-page-content {
    display: flex;
    flex-direction: column;
    gap: 24px;
    flex: 1;
}

.badge-top {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 100px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 9px;
    font-weight: 900;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 8px;
    backdrop-filter: blur(10px);
    width: fit-content;
}

.badge-top svg {
    width: 12px;
    height: 12px;
    color: var(--yellow-clr);
    flex-shrink: 0;
}

.title-page {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-width: 600px;
}

.title-page h1 {
    margin: 0;
    color: var(--white-clr);
    font-size: 40px;
    font-weight: 800;
    line-height: 1.2;
}

.title-page h1 span {
    color: #fbb034;
}

.title-page h5 {
    margin: 0;
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    font-weight: 500;
    line-height: 1.6;
    max-width: 500px;
}

.upcoming-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    min-width: 200px;
    backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.upcoming-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    background: #fbb034;
    border-radius: 14px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(251, 176, 52, 0.3);
}

.upcoming-icon svg {
    width: 24px;
    height: 24px;
    color: var(--main-navy-clr);
}

.upcoming-text {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.upcoming-label {
    font-size: 9px;
    font-weight: 900;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1.4px;
}

.upcoming-count {
    font-size: 18px;
    font-weight: 900;
    color: var(--white-clr);
    line-height: 1;
}

.search-filter-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: var(--spacing-sm);
    flex-wrap: nowrap;
    align-self: flex-end;
    width: auto;
    flex-shrink: 0;
}

.search-column {
    margin-top: 0;
    background-color: rgba(255, 255, 255, 0.16);
    padding: 10px 16px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    width: 100%;
    max-width: 280px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: none;
    height: 44px;
    transition: all 0.2s ease;
    backdrop-filter: blur(10px);
}

.search-column:hover,
.search-column:focus-within {
    background-color: rgba(255, 255, 255, 0.22);
    border-color: rgba(255, 255, 255, 0.35);
}

.search-column input {
    border: none;
    outline: none;
    flex: 1;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9);
    background: transparent;
    font-weight: 400;
}

.search-column input::placeholder {
    color: rgba(255, 255, 255, 0.6);
    font-weight: 400;
}

.search-column svg {
    color: rgba(255, 255, 255, 0.7);
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    transition: color 0.2s ease;
}

.search-column:focus-within svg {
    color: rgba(255, 255, 255, 0.9);
}

.filter-bar {
    gap: 8px;
    background-color: rgba(255, 255, 255, 0.16);
    padding: 10px 16px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    width: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: none;
    height: 44px;
    transition: all 0.2s ease;
    backdrop-filter: blur(10px);
}

.filter-bar:hover {
    background-color: rgba(255, 255, 255, 0.22);
    border-color: rgba(255, 255, 255, 0.35);
}

.filter-bar svg {
    color: rgba(255, 255, 255, 0.8);
    width: 16px;
    height: 16px;
    transition: color 0.2s ease;
}

.filter-bar:hover svg {
    color: rgba(255, 255, 255, 0.9);
}

.status-switcher {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #eef2f7;
}

.status-pill {
    border: 1px solid #d7deea;
    background: #fff;
    color: #5f6f85;
    border-radius: 999px;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    white-space: nowrap;
}

.status-pill:hover {
    border-color: #bdc9da;
    color: #2b2350;
    background: #f9fafb;
}

.status-pill.active {
    background: #2e2050;
    border-color: #2e2050;
    color: #fff;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
}

.status-panel {
    display: none;
    animation: fadeIn 0.3s ease;
}

.status-panel.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(4px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.section-empty {
    padding: 48px 32px;
    background: #fff;
    border: 1px dashed #dbe3ef;
    border-radius: 16px;
    color: #7d98b3;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.section-empty i {
    font-size: 36px;
    color: #cbd5e1;
}

.card-course {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    justify-content: stretch;
    gap: 16px;
    padding: 0;
    animation: fadeIn 0.3s ease;
}

.card-item {
    background-color: var(--white-clr);
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid #eef2f7;
    box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
    transition: all 0.25s ease;
    position: relative;
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    width: 100%;
    max-width: none;
    min-height: 284px;
}

.card-item:hover {
    transform: translateY(-6px);
    box-shadow: 0 18px 32px rgba(15, 23, 42, 0.12);
}

.card-media {
    position: relative;
    overflow: hidden;
    width: 100%;
    height: 146px;
    background: #f0f0f0;
}

.card-media.no-image {
    height: 146px;
    background: linear-gradient(135deg, #2b2350 0%, #1b144c 100%);
}

.no-image-placeholder {
    width: 100%;
    height: 146px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #2b2350 0%, #1b144c 100%);
}

.no-image-placeholder i {
    color: rgba(255, 255, 255, 0.75);
    font-size: 20px;
    line-height: 1;
}

.badge-online {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.96);
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 800;
    margin: 0;
    z-index: 10;
    letter-spacing: 0.9px;
    text-transform: uppercase;
    border: 1px solid rgba(255, 255, 255, 0.28);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    box-shadow: none;
}

.rating {
    position: absolute;
    bottom: 10px;
    right: 12px;
    background-color: #2f1f4f;
    padding: 6px 11px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    gap: 6px;
    font-weight: 800;
    font-size: 12px;
    color: var(--white-clr);
    box-shadow: 0 6px 16px rgba(15, 23, 42, 0.2);
    z-index: 20;
}

.rating svg {
    color: #f5c542;
    width: 14px;
    height: 14px;
}

.rating i {
    color: #f5c542;
    font-size: 13px;
    line-height: 1;
}

.rating p {
    margin: 0;
    color: var(--white-clr);
}

.card-image {
    width: 100%;
    height: 146px;
    object-fit: cover;
    display: block;
}

.card-content {
    max-width: none;
    padding: 12px 12px 10px;
    margin: 0;
    background-color: var(--white-clr);
    border-radius: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
}

.course-title {
    margin: 0;
}

.course-title h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 800;
    color: #2b2350;
    line-height: 1.3;
    display: -webkit-box;
    line-clamp: 2;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.course-title p {
    font-size: 13px;
    font-weight: 400;
    color: #7d98b3;
    margin: 6px 0 0 0;
    line-height: 1.45;
    display: -webkit-box;
    line-clamp: 2;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.bottom-card {
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0 0 0;
    margin-top: auto;
    border-top: 1px solid #edf1f7;
    gap: 6px;
}

.total-participant-path {
    display: flex;
    flex-direction: row;
    gap: 12px;
    align-items: center;
    flex: 1;
}

.total-participant {
    display: flex;
    flex-direction: row;
    margin: 0;
    align-items: center;
    gap: 4px;
}

.total-participant p {
    margin: 0;
    font-size: 11px;
    font-weight: 600;
    color: #3d2a5a;
}

.total-participant svg {
    color: #ffb446;
    width: 14px;
    height: 14px;
    flex-shrink: 0;
}

.total-path {
    display: flex;
    flex-direction: row;
    margin: 0;
    align-items: center;
    gap: 4px;
}

.total-path p {
    margin: 0;
    font-size: 11px;
    font-weight: 600;
    color: #3d2a5a;
}

.total-path svg {
    color: #ffb446;
    width: 14px;
    height: 14px;
    flex-shrink: 0;
}

.btn-detail-course {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    border-radius: 6px;
    background-color: transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 1px solid #dce3f0;
    padding: 0;
    flex-shrink: 0;
}

.btn-detail-course:hover {
    background-color: #f0f3f8;
    border-color: #c0cce0;
}

.btn-detail-course:hover svg {
    color: #2e2050;
}

.btn-detail-course svg {
    color: #5f6f85;
    width: 12px;
    height: 12px;
    transition: color 0.3s ease;
}

/* Responsive */
@media (max-width: 768px) {
    .top-page {
        flex-direction: column;
        align-items: stretch;
        gap: var(--spacing-md);
    }

    .top-page-inner {
        flex-direction: column;
        align-items: flex-start;
    }

    .search-filter-bar {
        width: 100%;
        justify-content: space-between;
    }

    .card-course {
        grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
        gap: 12px;
    }
}

@media (max-width: 600px) {
    .search-filter-bar {
        flex-direction: column;
        align-items: stretch;
    }
    .search-column {
        max-width: 100%;
    }
    .card-course {
        grid-template-columns: 1fr;
    }
}

</style>
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css" />
    <style>
        .processing-mini-row {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 10px 0 12px;
        }

        .processing-mini-pill {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 8px;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.03em;
            line-height: 1;
        }

        .processing-mini-pill.assigned {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .processing-mini-pill.uploaded {
            background: #ecfeff;
            color: #0f766e;
        }

        .processing-mini-pill.revision {
            background: #fff7ed;
            color: #9a3412;
        }

        .processing-mini-pill.ready {
            background: #dcfce7;
            color: #166534;
        }
    </style>
@endpush

@section('content')
    <div class="courses-page">
        <section class="top-page">
            <div class="glow-circle glow-circle-1"></div>
            <div class="glow-circle glow-circle-2"></div>

            <div class="top-page-inner">
                <div class="top-page-content">
                    <div class="title-page">
                        <span class="badge-top">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M12 3l1.912 5.813a2 2 0 001.899 1.374h6.098l-4.931 3.582a2 2 0 00-.728 2.236l1.912 5.813-4.931-3.582a2 2 0 00-2.342 0l-4.931 3.582 1.912-5.813a2 2 0 00-.728-2.236L2.091 10.187h6.098a2 2 0 001.899-1.374L12 3z" />
                            </svg>
                            <span>PUSAT KURSUS + KEUNGGULAN AKADEMIK</span>
                        </span>
                        <h1>Menguasai <br /><span>Manajemen Kursus.</span></h1>
                        <h5>Atur komitmen mengajar Anda dengan presisi. Pantau, kelola, dan unggul di setiap kursus.
                        </h5>
                    </div>
                </div>

                <div class="search-filter-bar">
                    <div class="search-column">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-search" viewBox="0 0 16 16">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg>
                        <input type="text" placeholder="Cari Kursus..." />
                    </div>
                    <button class="filter-bar" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-funnel" viewBox="0 0 16 16">
                            <path
                                d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        @php
            $statusData = [
                ['id' => 'courses-ongoing', 'label' => 'Sedang Berlangsung', 'data' => $ongoingCourses ?? collect()],
                ['id' => 'courses-upcoming', 'label' => 'Mendatang', 'data' => $upcomingCourses ?? collect()],
                ['id' => 'courses-finished', 'label' => 'Selesai', 'data' => $finishedCourses ?? collect()],
            ];
        @endphp

        <section id="courses-status-board" class="status-board">
            <div class="status-switcher" role="tablist" aria-label="Filter status course">
                @foreach($statusData as $index => $status)
                    <button class="status-pill {{ $index === 0 ? 'active' : '' }}" type="button"
                        data-target="{{ $status['id'] }}" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                        {{ $status['label'] }}
                    </button>
                @endforeach
            </div>

            @foreach($statusData as $index => $status)
                <section id="{{ $status['id'] }}" class="status-panel {{ $index === 0 ? 'active' : '' }}" role="tabpanel">
                    @if($status['data']->isEmpty())
                        <div class="section-empty">
                            <i class="bi bi-inbox"></i>
                            <p>Belum ada course untuk kategori ini</p>
                        </div>
                    @else
                        <div class="card-course">
                            @foreach($status['data'] as $course)
                                <a class="card-item" href="{{ route('trainer.detail-course', $course->id) }}" style="text-decoration: none; color: inherit;">
                                    <div class="card-media {{ $course->card_thumbnail ? '' : 'no-image' }}">
                                        <p class="badge-online">{{ strtoupper($course->level ?? 'GENERAL') }}</p>
                                        @if(!empty($course->is_locked))
                                            <div class="badge-locked" style="position: absolute; top: 12px; right: 12px; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; background: #f3f0f7; border: 1px solid rgba(46, 32, 80, 0.18); border-radius: 50%; color: #2e2050; font-size: 11px; z-index: 10;" title="Locked">
                                                <i class="bi bi-lock-fill"></i>
                                            </div>
                                        @endif
                                        <div class="rating">
                                            <i class="bi bi-star-fill"></i>
                                            <p>{{ number_format($course->reviews_avg_rating ?? 0, 1) }}</p>
                                        </div>

                                        @php $thumbUrl = $course->card_thumbnail_url; @endphp
                                        @if(!empty($thumbUrl))
                                            <img class="card-image" src="{{ $thumbUrl }}" alt="{{ $course->name }}">
                                        @else
                                            <div class="no-image-placeholder" aria-hidden="true">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="card-content">
                                        <div class="course-title">
                                            <h3>{{ Str::limit($course->name, 44) }}</h3>
                                            <p>{{ Str::limit($course->description ?? 'Deskripsi belum tersedia.', 80) }}</p>
                                        </div>

                                        @php
                                            $processingAssigned = (int) ($course->processing_assigned_count ?? 0);
                                            $processingUploaded = (int) ($course->processing_uploaded_count ?? 0);
                                            $processingRevision = (int) ($course->processing_revision_count ?? 0);
                                            $processingReady = (int) ($course->processing_ready_count ?? 0);
                                            $hasProcessing = ($processingAssigned + $processingUploaded + $processingRevision + $processingReady) > 0;
                                        @endphp

                                        @if($hasProcessing)
                                            <div class="processing-mini-row" aria-label="Status proses materi">
                                                @if($processingAssigned > 0)
                                                    <span class="processing-mini-pill assigned"><i
                                                            class="bi bi-send-check"></i>{{ $processingAssigned }} diserahkan</span>
                                                @endif
                                                @if($processingUploaded > 0)
                                                    <span class="processing-mini-pill uploaded"><i
                                                            class="bi bi-upload"></i>{{ $processingUploaded }} diunggah</span>
                                                @endif
                                                @if($processingRevision > 0)
                                                    <span class="processing-mini-pill revision"><i
                                                            class="bi bi-arrow-counterclockwise"></i>{{ $processingRevision }} revisi</span>
                                                @endif
                                                @if($processingReady > 0)
                                                    <span class="processing-mini-pill ready"><i
                                                            class="bi bi-check2-circle"></i>{{ $processingReady }} siap publikasi</span>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="bottom-card">
                                            <div class="total-participant-path">
                                                <div class="total-participant">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                        class="bi bi-people" viewBox="0 0 16 16">
                                                        <path
                                                            d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                                                    </svg>
                                                    <p>{{ number_format($course->enrollments_count ?? 0) }}</p>
                                                </div>

                                                <div class="total-path">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                        class="bi bi-stack" viewBox="0 0 16 16">
                                                        <path
                                                            d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z" />
                                                        <path
                                                            d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z" />
                                                    </svg>
                                                    <p>{{ number_format($course->modules_count ?? 0) }}</p>
                                                </div>
                                            </div>

                                            <div class="btn-detail-course" aria-label="Lihat detail {{ $course->name }}">
                                                <i class="bi bi-arrow-right"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const board = document.getElementById('courses-status-board');
            if (!board) return;

            const pills = board.querySelectorAll('.status-pill');
            const panels = board.querySelectorAll('.status-panel');

            pills.forEach((pill) => {
                pill.addEventListener('click', function () {
                    const target = this.dataset.target;

                    pills.forEach((item) => item.classList.toggle('active', item === this));
                    panels.forEach((panel) => panel.classList.toggle('active', panel.id === target));
                });
            });
        });
    </script>
@endpush

