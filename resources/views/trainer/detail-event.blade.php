@extends('layouts.trainer')

@section('title', $event->title . ' - Trainer')

@php
  $pageTitle = 'Event Detail';
  $breadcrumbs = [
    ['label' => 'Dasbor', 'url' => route('trainer.dashboard')],
    ['label' => 'Events', 'url' => route('trainer.events')],
    ['label' => 'Detail Event']
  ];
@endphp

@push('styles')
<style>
    /* Detail Event Page Specific Styles */
    main {
        padding: var(--spacing-4xl);
        background-color: var(--base-clr);
        overflow-y: auto;
        max-width: none;
        margin: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .hero-section {
        width: 100%;
        margin: 0;
        padding: var(--spacing-3xl) var(--spacing-4xl);
        background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.12);
    }

    .hero-container {
        max-width: none;
        margin: 0;
    }

    .hero-top-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: var(--spacing-2xl);
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-xs);
        padding: 12px 20px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: var(--radius-lg);
        color: var(--white-clr);
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        cursor: pointer;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .back-button:hover {
        background: rgba(255, 255, 255, 0.15);
        border-color: rgba(255, 255, 255, 0.3);
        transform: translateX(-4px);
    }

    .back-button svg {
        width: 16px;
        height: 16px;
    }

    .event-status-badges {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 8px 16px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        color: rgba(224, 224, 242, 0.95);
        font-size: 11px;
        font-weight: 500;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        backdrop-filter: blur(10px);
    }

    .event-category-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 999px;
        color: rgba(224, 224, 242, 0.95);
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        margin-bottom: var(--spacing-lg);
        width: fit-content;
    }

    .event-category-badge .badge-icon {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #a2388c;
        box-shadow: 0 0 0 3px rgba(162, 56, 140, 0.2);
    }

    .event-category-badge .badge-sep {
        color: rgba(224, 224, 242, 0.65);
    }

    .event-hero-title {
        color: var(--white-clr);
        font-size: 44px;
        font-weight: 700;
        line-height: 1.2;
        margin: 0 0 var(--spacing-2xl) 0;
        padding: 0;
        letter-spacing: -0.5px;
    }

    .event-hero-title span {
        color: #c052aa;
        font-style: italic;
        font-weight: 600;
    }

    .hero-body {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: var(--spacing-3xl);
    }

    .hero-left {
        flex: 1;
        min-width: 0;
    }

    .hero-media {
        width: 240px;
        height: 150px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 16px;
        border: 2px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
    }

    .hero-image-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 16px;
        background: linear-gradient(135deg, var(--main-navy-clr) 0%, #3659aa 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid rgba(255, 255, 255, 0.25);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.2);
    }

    .hero-image-placeholder span {
        font-size: 48px;
        font-weight: 800;
        color: var(--main-navy-clr);
        font-family: 'Sora', sans-serif;
    }

    .event-info-cards {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        width: 100%;
        margin-top: var(--spacing-xl);
    }

    .info-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 14px;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        transition: all 0.25s ease;
    }

    .info-card:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }

    .info-icon-shell {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.08);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-card svg {
        flex-shrink: 0;
        color: var(--white-clr);
        width: 16px;
        height: 16px;
    }

    .info-card-content {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .info-card-label {
        font-size: 9px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .info-card-value {
        font-size: 13px;
        font-weight: 600;
        color: var(--white-clr);
        white-space: nowrap;
    }

    .detail-layout {
        --detail-panel-bg: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        --detail-border: rgba(148, 163, 184, 0.24);
        --detail-shadow: 0 18px 42px rgba(15, 23, 42, 0.08);
        --detail-title: var(--main-navy-clr);
        --detail-muted: #667085;
        --detail-accent: var(--main-navy-clr);
        --detail-accent-soft: rgba(31, 26, 90, 0.14);
        display: flex;
        flex-direction: column;
        gap: 24px;
        margin-top: var(--spacing-3xl);
        width: 100%;
        font-family: "Plus Jakarta Sans", "Manrope", "Segoe UI", sans-serif;
    }

    .detail-layout-top {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        align-items: flex-start;
        gap: 28px;
        width: 100%;
    }

    .detail-box {
        width: 100%;
        background: var(--detail-panel-bg);
        border: 1px solid var(--detail-border);
        border-radius: 20px;
        padding: 22px;
        box-shadow: var(--detail-shadow);
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .detail-box-context {
        position: relative;
        border-left: 4px solid #8b1e77 !important;
    }

    .vsa-section {
        background: transparent;
        min-width: 0;
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        gap: 28px;
    }

    .detail-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
        width: 100%;
    }

    .vsa-title {
        font-size: 18px;
        font-weight: 800;
        color: var(--main-navy-clr);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        letter-spacing: -0.3px;
    }

    .vsa-title::before {
        content: "";
        display: inline-block;
        width: 4px;
        height: 18px;
        background: linear-gradient(180deg, #8b1e77 0%, #4a0e4e 100%);
        border-radius: 99px;
    }

    .vsa-grid {
        display: flex;
        flex-direction: row;
        gap: 18px;
        width: 100%;
    }

    .vsa-card {
        flex: 1;
        min-width: 0;
        background: #f8fafc;
        border: 1px solid var(--detail-border);
        border-radius: 18px;
        padding: 20px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: all 0.25s ease;
    }

    .vsa-card:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
        border-color: rgba(139, 30, 119, 0.25);
    }

    .vsa-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.45);
    }

    .vsa-icon-plum {
        background: linear-gradient(
            145deg,
            rgba(139, 30, 119, 0.15),
            rgba(139, 30, 119, 0.08)
        );
        color: #8b1e77;
    }

    .vsa-icon svg {
        width: 22px;
        height: 22px;
    }

    .vsa-meta h3 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
        color: var(--detail-title);
        line-height: 1.35;
    }

    .vsa-label {
        margin: 0 0 6px 0;
        font-size: 11px;
        font-weight: 700;
        color: #7b8798;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    .vsa-link,
    .vsa-desc {
        margin: 6px 0 0 0;
        font-size: 13px;
        color: #5f6b7d;
        line-height: 1.5;
        word-break: break-all;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .vsa-btn {
        margin-top: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 16px;
        border-radius: 12px;
        border: none;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.8px;
        text-transform: uppercase;
        cursor: pointer;
        text-decoration: none;
        transition:
            transform 0.2s ease,
            box-shadow 0.2s ease,
            filter 0.2s ease;
    }

    .vsa-btn[disabled] {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .vsa-btn-plum {
        background: linear-gradient(135deg, #8b1e77 0%, #4a0e4e 100%);
        color: var(--white-clr);
        box-shadow: 0 10px 20px rgba(139, 30, 119, 0.25);
    }

    .vsa-btn:hover {
        transform: translateY(-1px);
        filter: saturate(1.08);
    }

    .vsa-btn:active {
        transform: translateY(0);
    }

    .vsa-context {
        background: transparent;
        border: none;
        border-radius: 0;
        padding: 0;
        box-shadow: none;
    }

    .vsa-context-body {
        margin: 0;
        font-size: 15px;
        color: #1f2937;
        line-height: 1.75;
    }

    .vsa-context-body p {
        margin: 0 0 10px 0;
    }

    .vsa-context-body p:last-child {
        margin-bottom: 0;
    }

    .vsa-context-body ul,
    .vsa-context-body ol {
        margin: 8px 0 12px 20px;
        padding: 0;
    }

    .vsa-context-body li {
        margin-bottom: 6px;
    }

    .hub-sidebar-card {
        background: var(--white-clr);
        border: 1px solid var(--detail-border);
        border-radius: 20px;
        padding: 22px;
        box-shadow: var(--detail-shadow);
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .hub-sidebar-title {
        font-size: 16px;
        font-weight: 800;
        color: var(--main-navy-clr);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        letter-spacing: -0.2px;
    }

    .hub-sidebar-title::before {
        content: "";
        display: inline-block;
        width: 3px;
        height: 16px;
        background: linear-gradient(180deg, #8b1e77 0%, #4a0e4e 100%);
        border-radius: 99px;
    }

    .hub-actions-column {
        display: flex;
        flex-direction: column;
        gap: 18px;
        flex: 0 0 340px;
        width: 340px;
        margin-left: auto;
    }

    .hub-actions-column .detail-group {
        position: sticky;
        top: 24px;
    }

    .hub-actions-column .hub-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .material-status-banner {
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .material-status-banner.status-approved {
        background: #dcfce7;
        color: #166534;
        border: 1px solid rgba(22, 101, 52, 0.15);
    }

    .material-status-banner.status-pending_review {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid rgba(146, 64, 14, 0.15);
    }

    .material-status-banner.status-rejected {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid rgba(153, 27, 27, 0.15);
    }

    .material-status-banner.status-not_uploaded {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid rgba(100, 116, 139, 0.15);
    }

    .uploaded-modules-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin-top: 8px;
    }

    .module-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 12px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        font-size: 12px;
        transition: all 0.2s ease;
    }

    .module-item-row:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .module-item-left {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow: hidden;
        color: var(--main-navy-clr);
    }

    .module-item-left svg {
        flex-shrink: 0;
        color: var(--text-clr);
    }

    .module-item-name {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        max-width: 150px;
        font-weight: 600;
    }

    .module-status-badge {
        font-size: 11px;
        padding: 3px 8px;
        border-radius: 20px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .module-status-badge.status-approved {
        background: #dcfce7;
        color: #166534;
    }

    .module-status-badge.status-rejected {
        background: #fee2e2;
        color: #991b1b;
    }

    .module-status-badge.status-pending_review,
    .module-status-badge.status-pending {
        background: #fef3c7;
        color: #92400e;
    }

    .hub-item {
        display: flex;
        gap: 14px;
        align-items: center;
        padding: 14px;
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 12px;
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        transition: all 0.25s ease;
    }

    .hub-item > div:last-child {
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .hub-item h4 {
        color: #0f172a;
    }

    .hub-item:hover {
        background: #ffffff;
        border-color: rgba(139, 30, 119, 0.25);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    .hub-item-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(
            140deg,
            rgba(139, 30, 119, 0.15),
            rgba(139, 30, 119, 0.08)
        );
        color: #8b1e77;
        border: 1px solid rgba(139, 30, 119, 0.16);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .hub-item-icon svg {
        width: 20px;
        height: 20px;
        stroke: currentColor;
    }

    .hub-item h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
    }

    .hub-item p {
        margin: 0;
        font-size: 12px;
        font-weight: 600;
        color: #5f6b7d;
        letter-spacing: 0;
        text-transform: none;
        line-height: 1.45;
        word-break: break-word;
    }

    /* Rundown List Timeline Design */
    .rundown-timeline {
        position: relative;
        padding-left: 28px;
        margin: 0;
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .rundown-timeline::before {
        content: "";
        position: absolute;
        top: 8px;
        bottom: 8px;
        left: 9px;
        width: 2px;
        background: rgba(139, 30, 119, 0.12);
    }

    .rundown-timeline-item {
        position: relative;
        width: 100%;
    }

    .rundown-timeline-dot {
        position: absolute;
        left: -24px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: linear-gradient(135deg, #8b1e77 0%, #4a0e4e 100%);
        box-shadow: 0 0 0 4px var(--base-clr), 0 0 0 6px rgba(139, 30, 119, 0.25);
        z-index: 2;
    }

    .rundown-timeline-card {
        background: #f8fafc;
        border: 1px solid var(--detail-border);
        border-radius: var(--radius-xl);
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 20px;
        width: 100%;
        transition: all 0.25s ease;
    }

    .rundown-timeline-card:hover {
        background: #f1f5f9;
        transform: translateX(4px);
        border-color: rgba(139, 30, 119, 0.25);
    }

    .time-rundown {
        color: var(--main-navy-clr);
        font-weight: 700;
        font-size: 13px;
        white-space: nowrap;
        min-width: 110px;
        letter-spacing: 0.2px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .activity-rundown {
        color: #334155;
        font-size: 14px;
        line-height: 1.6;
        flex: 1;
    }

    @media (max-width: 1200px) {
        .detail-layout-top {
            flex-direction: column;
            gap: 18px;
        }

        .hub-actions-column {
            width: 100%;
            flex: 1 1 auto;
            margin-left: 0;
        }

        .hub-actions-column .detail-group {
            position: static;
            top: auto;
        }
    }

    @media (max-width: 992px) {
        main {
            flex-direction: column;
            margin-left: var(--spacing-sm);
            padding: var(--spacing-lg);
            gap: var(--spacing-lg);
        }

        .hero-top-row {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-xl);
        }

        .event-status-badges {
            width: 100%;
            justify-content: flex-start;
        }

        .hero-body {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--spacing-xl);
        }

        .hero-left {
            width: 100%;
        }

        .hero-media {
            width: 100%;
            max-width: 420px;
            height: auto;
        }

        .hero-image,
        .hero-image-placeholder {
            height: 200px;
            border-radius: 16px;
        }

        .event-info-cards {
            width: 100%;
            gap: 12px;
            max-width: 100%;
        }

        .event-hero-title {
            font-size: 38px;
            margin-bottom: var(--spacing-xl);
        }
    }

    @media (max-width: 768px) {
        main {
            margin-left: 0;
            padding: var(--spacing-lg) var(--spacing-md) 100px var(--spacing-md);
        }

        .vsa-grid {
            flex-direction: column;
        }

        .vsa-title,
        .hub-sidebar-title {
            font-size: 18px;
        }

        .hero-section {
            padding: var(--spacing-2xl) var(--spacing-lg);
        }

        .event-hero-title {
            font-size: 32px;
            line-height: 1.25;
            word-break: break-word;
        }

        .back-button {
            width: 100%;
            justify-content: center;
        }

        .status-badge {
            font-size: 10px;
            padding: 7px 12px;
        }

        .event-info-cards {
            flex-direction: column;
            align-items: stretch;
        }

        .info-card {
            width: 100%;
        }

        .info-card-value {
            white-space: normal;
        }

        .time-rundown {
            min-width: 100px;
            font-size: var(--font-size-sm);
        }

        .activity-rundown {
            font-size: var(--font-size-sm);
        }

        .rundown-timeline-card {
            padding: var(--spacing-md) var(--spacing-lg);
        }

        .vsa-card {
            padding: var(--spacing-md) var(--spacing-lg);
        }
    }

    @media (max-width: 480px) {
        .hero-section {
            padding: var(--spacing-xl) var(--spacing-md);
            border-radius: 16px;
        }

        .event-hero-title {
            font-size: 28px;
        }

        .hero-image,
        .hero-image-placeholder {
            height: 180px;
        }

        .time-rundown {
            min-width: 0;
            font-size: var(--font-size-sm);
        }

        .activity-rundown {
            font-size: var(--font-size-sm);
        }

        .rundown-timeline-card {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
            padding: 12px 14px;
        }

        .vsa-card {
            padding: var(--spacing-md);
        }
    }
</style>
@endpush

@php
  $mapLink = '';
  $mapsUrl = trim((string) ($event->maps_url ?? ''));
  $zoomLink = trim((string) ($event->zoom_link ?? ''));
  $isOfflineEvent = false;
  $hasMapLink = false;
  $hasZoomLink = $zoomLink !== '';

  if ($mapsUrl !== '') {
    $maps = $mapsUrl;
    if (
      \Illuminate\Support\Str::startsWith($maps, ['http://', 'https://', '//'])
    ) {
      $mapLink = $maps;
    } else {
      try {
        $mapLink = \Illuminate\Support\Facades\Storage::url($maps);
      } catch (\Throwable $e) {
        $mapLink = $maps;
      }
    }
    $isOfflineEvent = true;
    $hasMapLink = true;
  } elseif (!empty($event->latitude) && !empty($event->longitude)) {
    $mapLink = 'https://www.google.com/maps?q=' . urlencode($event->latitude . ',' . $event->longitude);
    $isOfflineEvent = true;
    $hasMapLink = true;
  }

  // Legacy compatibility: some old online events stored VBG in image field.
  $hasHybridAssets = $hasMapLink && $hasZoomLink;
  $hasVbgAsset = !empty($event->vbg_path) || ($hasZoomLink && !empty($event->image));
  $vbgUrl = $hasVbgAsset ? route('trainer.events.vbg.download', $event->id) : '';
@endphp

@section('content')
  <div class="hero-section">
    <div class="hero-container">
      <div class="hero-top-row">
        <button class="back-button" onclick="window.location.href = '{{ route('trainer.events') }}'">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7" />
          </svg>
          <span>ALL SESSIONS</span>
        </button>
      </div>

      <div class="hero-body">
        <div class="hero-left">
          <h1 class="event-hero-title">
            {{ $event->title }}
          </h1>
          <div class="event-info-cards">
            <div class="info-card">
              <div class="info-icon-shell">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">DATE</span>
                <span class="info-card-value">
                  @php
                    $startDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
                    $untilDate = !empty($event->event_until_date) ? \Carbon\Carbon::parse($event->event_until_date) : null;
                  @endphp
                  @if($startDate)
                    @if($untilDate && $untilDate->ne($startDate))
                      @if($startDate->format('M Y') === $untilDate->format('M Y'))
                        {{ $startDate->format('d') }} – {{ $untilDate->format('d M Y') }}
                      @else
                        {{ $startDate->format('d M Y') }} – {{ $untilDate->format('d M Y') }}
                      @endif
                    @else
                      {{ $startDate->format('d M Y') }}
                    @endif
                  @else
                    TBA
                  @endif
                </span>
              </div>
            </div>

            <div class="info-card">
              <div class="info-icon-shell">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">TIME</span>
                <span class="info-card-value">
                  @php
                    $startTime = $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : null;
                    $endTime = !empty($event->event_until_time) 
                      ? \Carbon\Carbon::parse($event->event_until_time)->format('H:i') 
                      : (!empty($event->event_time_end) ? \Carbon\Carbon::parse($event->event_time_end)->format('H:i') : null);
                  @endphp
                  @if($startTime)
                    {{ $startTime }}
                    @if($endTime) - {{ $endTime }} @endif
                    WIB
                  @else
                    TBA
                  @endif
                </span>
              </div>
            </div>

            <div class="info-card">
              <div class="info-icon-shell">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">VENUE</span>
                <span class="info-card-value">{{ $event->location ?? 'Tech Hub Hall A' }}</span>
              </div>
            </div>

            <div class="info-card">
              <div class="info-icon-shell">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <path d="M16 8h-5a2 2 0 0 0 0 4h2a2 2 0 0 1 0 4H8"></path>
                  <path d="M12 6v2"></path>
                  <path d="M12 16v2"></path>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">FEE TRAINER</span>
                @if((float) ($eventCompensation['speaker_salary'] ?? 0) > 0)
                  <span class="info-card-value">
                    Rp {{ number_format((float) $eventCompensation['speaker_salary'], 0, ',', '.') }}
                  </span>
                @elseif((float) ($eventCompensation['fee_per_participant'] ?? 0) > 0)
                  <span class="info-card-value">
                    Rp {{ number_format((float) $eventCompensation['fee_per_participant'], 0, ',', '.') }}/peserta
                  </span>
                @else
                  <span class="info-card-value">Belum diatur admin</span>
                @endif
              </div>
            </div>

            <div class="info-card">
              <div class="info-icon-shell">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                  <circle cx="9" cy="7" r="4"></circle>
                  <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                  <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">TARGET PESERTA</span>
                <span class="info-card-value">
                  @php
                    $titleLower = strtolower($event->title ?? '');
                    if (str_contains($titleLower, 'dosen') || str_contains($titleLower, 'guru') || str_contains($titleLower, 'pendidik')) {
                        $targetAudience = 'Dosen & Pendidik';
                    } elseif (str_contains($titleLower, 'lomba') || str_contains($titleLower, 'mahasiswa') || str_contains($titleLower, 'siswa')) {
                        $targetAudience = 'Mahasiswa & Siswa';
                    } else {
                        $targetAudience = 'Mahasiswa, Dosen, & Umum';
                    }
                  @endphp
                  {{ $targetAudience }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="hero-media">
          @if($event->image_url)
            <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="hero-image" />
          @else
            <div class="hero-image-placeholder">
              <span>{{ strtoupper(substr($event->title, 0, 1)) }}</span>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="detail-layout">
    <div class="detail-layout-top">
      <section class="vsa-section">
        
        <!-- Aset Acara Group -->
        <div class="detail-group">
          <h2 class="vsa-title">Aset Acara</h2>
          <div class="detail-box">
            <div class="vsa-grid">
              @if($hasMapLink)
                <article class="vsa-card">
                  <div class="vsa-icon vsa-icon-plum">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                      <path
                        d="M8 0a5 5 0 0 0-5 5c0 1.676 1.3 4.02 3.163 6.275A24.7 24.7 0 0 0 8 15c.837-1.08 1.837-2.36 2.837-3.725C12.7 9.02 14 6.676 14 5a5 5 0 0 0-5-5zm0 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4z" />
                    </svg>
                  </div>
                  <div class="vsa-meta">
                    <p class="vsa-label">PETA LOKASI</p>
                    <h3>Lokasi Event</h3>
                    <p class="vsa-link">{{ $event->location ?? 'Lokasi belum tersedia' }}</p>
                  </div>
                  <a href="{{ $mapLink ?: '#' }}" target="_blank" class="vsa-btn vsa-btn-plum" {{ empty($mapLink) ? 'disabled' : '' }}>
                    BUKA PETA
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path fill-rule="evenodd"
                        d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                      <path fill-rule="evenodd"
                        d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                    </svg>
                  </a>
                </article>
              @endif

              @if($hasZoomLink)
                <article class="vsa-card">
                  <div class="vsa-icon vsa-icon-plum">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                      <path fill-rule="evenodd"
                        d="M0 5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 4.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 13H2a2 2 0 0 1-2-2zm11.5 5.175 3.5 1.556V4.269l-3.5 1.556zM2 4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h7.5a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1z" />
                    </svg>
                  </div>
                  <div class="vsa-meta">
                    <p class="vsa-label">SESI ONLINE</p>
                    <h3>Rapat Virtual</h3>
                    <p class="vsa-link">{{ $zoomLink ?: 'Link belum tersedia' }}</p>
                  </div>
                  <a href="{{ $zoomLink ?: '#' }}" target="_blank" class="vsa-btn vsa-btn-plum" {{ empty($zoomLink) ? 'disabled' : '' }}>
                    GABUNG SESI
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path fill-rule="evenodd"
                        d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                      <path fill-rule="evenodd"
                        d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                    </svg>
                  </a>
                </article>
              @endif

              @if(!empty($vbgUrl))
                <article class="vsa-card">
                  <div class="vsa-icon vsa-icon-plum">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                      <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                      <path
                        d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z" />
                    </svg>
                  </div>
                  <div class="vsa-meta">
                    <p class="vsa-label">LATAR VIRTUAL</p>
                    <h3>Latar Virtual</h3>
                    <p class="vsa-desc">PNG Resolusi Tinggi Berlogo</p>
                  </div>
                  <a href="{{ $vbgUrl }}" class="vsa-btn vsa-btn-plum" download>
                    UNDUH VBG
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                      <path
                        d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                      <path
                        d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                    </svg>
                  </a>
                </article>
              @endif
            </div>
          </div>
        </div>

        <!-- Deskripsi Event Group -->
        <div class="detail-group">
          <h2 class="vsa-title">Deskripsi Event</h2>
          <div class="detail-box detail-box-context">
            <div class="vsa-context">
              @php
                $eventDescription = trim((string) ($event->description ?? ''));
                if ($eventDescription === '') {
                  $eventDescription = trim((string) ($event->short_description ?? ''));
                }
                if ($eventDescription === '') {
                  $eventDescription = trim((string) ($event->materi ?? ''));
                }
              @endphp
              <div class="vsa-context-body">
                {!! $eventDescription !== '' ? $eventDescription : '<p>Deskripsi event belum tersedia.</p>' !!}
              </div>
            </div>
          </div>
        </div>

        <!-- Rundown Acara Group -->
        <div class="detail-group">
          <h2 class="vsa-title">Rundown Acara</h2>
          <div class="detail-box">
            @php
              $items = collect();

              if (isset($event)) {
                try {
                  $items = $event->relationLoaded('scheduleItems')
                    ? ($event->scheduleItems ?? collect())
                    : $event->scheduleItems()->get();
                } catch (\Throwable $e) {
                  $items = collect();
                }

                if ($items->isEmpty()) {
                  $rawSchedule = $event->schedule_json ?? null;

                  $scheduleArr = null;
                  if (is_string($rawSchedule) && trim($rawSchedule) !== '') {
                    $decoded = json_decode($rawSchedule, true);
                    $scheduleArr = (json_last_error() === JSON_ERROR_NONE) ? $decoded : null;
                  } elseif (is_array($rawSchedule)) {
                    $scheduleArr = $rawSchedule;
                  } elseif (is_object($rawSchedule)) {
                    $scheduleArr = json_decode(json_encode($rawSchedule), true);
                  }

                  if (is_array($scheduleArr)) {
                    $items = collect($scheduleArr)->map(function ($row) {
                      $row = is_array($row) ? $row : (is_object($row) ? (array) $row : []);
                      return (object) [
                        'start' => $row['start'] ?? ($row['time_start'] ?? ($row['time'] ?? null)),
                        'end' => $row['end'] ?? ($row['time_end'] ?? null),
                        'title' => $row['title'] ?? ($row['activity'] ?? ''),
                        'description' => $row['description'] ?? ($row['desc'] ?? ''),
                      ];
                    })->filter(function ($it) {
                      return !empty($it->title) || !empty($it->description) || !empty($it->start) || !empty($it->end);
                    })->values();
                  }
                }
              }

              $formatTime = function ($t) {
                if (empty($t)) {
                  return null;
                }
                try {
                  return \Carbon\Carbon::parse($t)->format('H:i');
                } catch (\Throwable $e) {
                  return is_string($t) ? $t : null;
                }
              };
            @endphp
            <ul class="rundown-timeline">
              @forelse($items as $it)
                @php
                  $start = $formatTime($it->start ?? null);
                  $end = $formatTime($it->end ?? null);
                  $timeStr = trim(($start ?: '') . ($end ? ' - ' . $end : ''));
                  $activity = trim((string) ($it->title ?? ''));
                  if ($activity === '') {
                    $activity = trim((string) ($it->description ?? ''));
                  }
                @endphp
                <li class="rundown-timeline-item">
                  <div class="rundown-timeline-dot"></div>
                  <div class="rundown-timeline-card">
                    <span class="time-rundown">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock" viewBox="0 0 24 24" style="margin-right: 4px; color: var(--text-clr); width: 14px; height: 14px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                      {{ $timeStr !== '' ? $timeStr : '-' }}
                    </span>
                    <span class="activity-rundown">{{ $activity !== '' ? $activity : '-' }}</span>
                  </div>
                </li>
              @empty
                <li class="rundown-timeline-item is-empty">
                  <div class="rundown-timeline-dot" style="background: #cbd5e1; box-shadow: none;"></div>
                  <div class="rundown-timeline-card" style="background: #f8fafc; border-style: dashed; box-shadow: none;">
                    <span class="time-rundown">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-clock" viewBox="0 0 24 24" style="margin-right: 4px; color: var(--text-clr); width: 14px; height: 14px;"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                      —
                    </span>
                    <span class="activity-rundown" style="color: var(--text-clr);">Schedule will be announced.</span>
                  </div>
                </li>
              @endforelse
            </ul>
          </div>
        </div>
      </section>

      <!-- Sidebar Column -->
      <div class="hub-actions-column">
        <div class="detail-group">
          <h2 class="hub-sidebar-title">Materi Acara</h2>
          <aside class="hub-sidebar-card">
            <div class="hub-section">
              <div class="hub-item" data-redirect="{{ route('trainer.events.studio', $event->id) }}">
                <div class="hub-item-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"
                    aria-hidden="true">
                    <path
                      d="M3 7.5A2.5 2.5 0 0 1 5.5 5h4l2 2h7A2.5 2.5 0 0 1 21 9.5v9A2.5 2.5 0 0 1 18.5 21h-13A2.5 2.5 0 0 1 3 18.5z" />
                    <path d="M12 11v7" />
                    <path d="m8.8 14.2 3.2-3.2 3.2 3.2" />
                  </svg>
                </div>
                <div>
                  <h4>Kirim Materi</h4>
                </div>
              </div>
            </div>

            @php
              $statusLabel = match($myMaterialStatus ?? 'not_uploaded') {
                'approved'     => '✓ Materi Disetujui',
                'pending_review' => '⏳ Menunggu Review',
                'rejected'     => '✕ Perlu Revisi',
                default        => '— Belum Upload',
              };
            @endphp

            <div class="material-status-banner status-{{ $myMaterialStatus ?? 'not_uploaded' }}">
              {{ $statusLabel }}
            </div>

            @if(($myModules ?? collect())->isNotEmpty())
              <div class="uploaded-modules-list">
                @foreach($myModules as $mod)
                  <div class="module-item-row">
                    <div class="module-item-left">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/>
                        <path d="M9.5 0V3a1.5 1.5 0 0 0 1.5 1.5H14"/>
                      </svg>
                      <span class="module-item-name" title="{{ $mod->original_name }}">{{ $mod->original_name }}</span>
                    </div>
                    <span class="module-status-badge status-{{ $mod->status }}">
                      {{ $mod->status === 'approved' ? 'Approved' : ($mod->status === 'rejected' ? 'Ditolak' : 'Pending') }}
                    </span>
                  </div>
                @endforeach
              </div>
            @endif
          </aside>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener("click", (event) => {
      const item = event.target.closest(".hub-item[data-redirect]");
      if (!item) return;

      event.preventDefault();
      event.stopPropagation();

      const targetPath = item.getAttribute("data-redirect");
      if (targetPath && targetPath !== '#') {
        window.location.href = targetPath;
      }
    });
  </script>
@endpush
