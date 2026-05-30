@extends('layouts.trainer')

@section('title', $event->title . ' - Trainer')

@php
  $pageTitle = 'Event Detail';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Events', 'url' => route('trainer.events')],
    ['label' => 'Detail']
  ];
@endphp

@push('styles')
<style>
    /* Detail Event Page Specific Styles - Using main.css variables */
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
        background: linear-gradient(
            135deg,
            var(--main-navy-clr) 0%,
            var(--navy-dark) 100%
        );
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
        background: var(--yellow-clr);
        box-shadow: 0 0 0 3px rgba(251, 197, 49, 0.2);
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
        color: var(--yellow-clr);
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
        width: 130px;
        height: 130px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.3);
    }

    .hero-image-placeholder {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--yellow-clr) 0%, #f1b700 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 3px solid rgba(255, 255, 255, 0.25);
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
        gap: 8px;
        width: fit-content;
        max-width: 100%;
        margin-top: 6px;
    }

    .info-card {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        padding: 6px 0;
        background: transparent;
        border: none;
        border-radius: 0;
        backdrop-filter: none;
        transition: none;
    }

    .info-card:hover {
        transform: none;
    }

    .info-icon-shell {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(10px);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .info-card svg {
        flex-shrink: 0;
        color: var(--yellow-clr);
        width: 18px;
        height: 18px;
    }

    .info-card-content {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .info-card-label {
        font-size: 10px;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.65);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-card-value {
        font-size: 14px;
        font-weight: 500;
        color: var(--white-clr);
        white-space: nowrap;
    }

    /* Content Wrapper */
    .content-wrapper {
        display: flex;
        gap: var(--spacing-xl);
        padding: 0;
    }

    .left-content {
        width: 100%;
        padding: 0;
    }

    .right-content {
        display: none;
    }

    h1 {
        padding: var(--spacing-xs);
        color: var(--main-navy-clr);
    }

    .info-detail {
        display: flex;
        gap: var(--spacing-xl);
        margin: 0;
        padding: var(--spacing-xl);
        background-color: var(--white-clr);
        border: 2px solid var(--line-clr);
        border-radius: var(--radius-2xl);
        justify-content: space-around;
    }

    .detail-list {
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        flex: 0 1 auto;
    }

    .detail-list svg {
        fill: var(--main-navy-clr);
        background-color: var(--base-clr);
        padding: var(--spacing-sm);
        border-radius: var(--radius-lg);
        border: 1px solid var(--line-clr);
        flex-shrink: 0;
    }

    .list-description {
        padding: 0;
    }

    .list-description h6 {
        margin: 0;
        font-size: var(--font-size-xs);
        color: var(--main-text-clr);
    }

    .list-description p {
        margin: 0;
        color: var(--main-navy-clr);
        font-weight: 600;
        white-space: nowrap;
        font-size: var(--font-size-base);
    }

    .event-description {
        margin-top: var(--spacing-xl);
        padding: var(--spacing-xl);
        background-color: var(--white-clr);
        border: 2px solid var(--line-clr);
        border-radius: var(--radius-2xl);
    }

    .event-description h2 {
        padding: var(--spacing-xs);
        color: var(--main-navy-clr);
    }

    .event-description p {
        margin: 0;
        padding: var(--spacing-xs);
        color: var(--main-text-clr);
        text-align: justify;
    }

    .virtual-assets {
        margin-top: var(--spacing-xl);
        padding: var(--spacing-xl) var(--spacing-3xl);
        background-color: var(--white-clr);
        border: 2px solid var(--line-clr);
        border-radius: var(--radius-2xl);
    }

    .zoom-vbg {
        display: flex;
        margin: var(--spacing-lg) 0;
        gap: var(--spacing-xl);
        flex-wrap: wrap;
    }

    .virtual-assets h2 {
        color: var(--main-navy-clr);
        margin: 0 0 var(--spacing-sm) 0;
    }

    .zoom-vbg p {
        margin: 0;
        color: var(--main-text-clr);
        text-align: justify;
    }

    .link-zoom {
        background-color: var(--blue-background-clr);
        padding: var(--spacing-3xl);
        border-radius: var(--radius-2xl);
        border: 1px solid rgb(79 70 229 / 0.05);
        flex: 1;
        min-width: 250px;
    }

    .link-zoom h4 {
        margin: var(--spacing-lg) 0 var(--spacing-xs) 0;
        color: var(--main-navy-clr);
        font-size: var(--font-size-lg);
    }

    .link-zoom p {
        margin: 0 0 var(--spacing-lg) 0;
        color: var(--main-text-clr);
        font-size: var(--font-size-base);
        line-height: var(--line-height-tight);
    }

    .link-zoom svg {
        color: var(--indigo-clr);
        background-color: rgb(224 231 255);
        padding: var(--spacing-sm);
        border-radius: var(--radius-lg);
        border: 1px solid rgb(79 70 229 / 0.1);
    }

    .link-zoom button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-sm);
        width: 100%;
        margin-top: var(--spacing-lg);
        background-color: var(--main-navy-clr);
        color: var(--white-clr);
        border: none;
        border-radius: var(--radius-xl);
        font-weight: 600;
        cursor: pointer;
        font-size: var(--font-size-base);
    }

    .link-zoom button svg {
        background-color: transparent;
        border: none;
        width: 20px;
        height: 20px;
        fill: var(--white-clr);
    }

    .vbg {
        background-color: var(--yellow-background-clr);
        padding: var(--spacing-3xl);
        border-radius: var(--radius-2xl);
        border: 1px solid rgb(251 197 49 / 0.05);
        flex: 1;
        min-width: 250px;
    }

    .vbg h4 {
        margin: var(--spacing-lg) 0 var(--spacing-xs) 0;
        color: var(--main-navy-clr);
        font-size: var(--font-size-lg);
    }

    .vbg p {
        margin: 0 0 var(--spacing-lg) 0;
        color: var(--main-text-clr);
        font-size: var(--font-size-base);
        line-height: var(--line-height-tight);
    }

    .vbg svg {
        color: #e1b12c;
        background-color: var(--yellow-background-clr);
        padding: var(--spacing-sm);
        border-radius: var(--radius-lg);
        border: 1px solid rgb(251 197 49 / 0.1);
    }

    .vbg button {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-sm);
        width: 100%;
        margin-top: var(--spacing-lg);
        background-color: var(--main-navy-clr);
        color: var(--white-clr);
        border: none;
        border-radius: var(--radius-xl);
        font-weight: 600;
        cursor: pointer;
        font-size: var(--font-size-base);
    }

    .vbg button svg {
        background-color: transparent;
        border: none;
        width: 20px;
        height: 20px;
        fill: var(--white-clr);
    }

    /* Virtual Studio Assets Section */
    .virtual-studio-assets {
        margin-top: var(--spacing-2xl);
        margin-bottom: var(--spacing-2xl);
    }

    .section-title {
        font-size: 12px;
        font-weight: 700;
        color: rgba(108, 108, 108, 0.6);
        letter-spacing: 1px;
        text-transform: uppercase;
        margin: 0 0 var(--spacing-lg) 0;
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
    }

    .vsa-section {
        background: transparent;
        min-width: 0;
        flex: 1 1 auto;
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .vsa-title {
        margin: 0 0 16px 0;
        font-size: 20px;
        font-weight: 700;
        color: var(--detail-title);
        letter-spacing: -0.2px;
        text-transform: none;
        line-height: 1.3;
    }

    .vsa-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
        margin-bottom: 18px;
    }

    .vsa-grid.is-hybrid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .vsa-card {
        background: var(--detail-panel-bg);
        border: 1px solid var(--detail-border);
        border-radius: 18px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 14px;
        box-shadow: 0 14px 32px rgba(15, 23, 42, 0.07);
        transition:
            transform 0.25s ease,
            box-shadow 0.25s ease,
            border-color 0.25s ease;
    }

    .vsa-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 20px 36px rgba(15, 23, 42, 0.11);
        border-color: rgba(29, 78, 216, 0.34);
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

    .vsa-icon-blue {
        background: linear-gradient(
            145deg,
            rgba(31, 26, 90, 0.15),
            rgba(54, 89, 170, 0.12)
        );
        color: var(--detail-accent);
    }

    .vsa-icon-amber {
        background: linear-gradient(
            145deg,
            rgba(251, 197, 49, 0.28),
            rgba(251, 197, 49, 0.12)
        );
        color: #8a6200;
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

    .vsa-btn-primary {
        background: linear-gradient(135deg, var(--main-navy-clr) 0%, #3659aa 100%);
        color: var(--white-clr);
        box-shadow: 0 10px 20px rgba(31, 26, 90, 0.25);
    }

    .vsa-btn-amber {
        background: linear-gradient(135deg, #fbc531 0%, #f0b320 100%);
        color: #3d2a00;
        box-shadow: 0 10px 20px rgba(251, 197, 49, 0.32);
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
        padding: 0 0 18px 0;
        margin-bottom: 18px;
        box-shadow: none;
    }

    .vsa-context-title {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
        font-weight: 700;
        color: var(--detail-muted);
        letter-spacing: 0.8px;
        text-transform: uppercase;
        margin-bottom: 12px;
    }

    .context-dot {
        width: 11px;
        height: 11px;
        border-radius: 50%;
        background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%);
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.16);
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

    .vsa-subtitle {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: var(--detail-title);
        letter-spacing: -0.2px;
        text-transform: none;
        line-height: 1.3;
    }

    .hub-card {
        background: var(--detail-panel-bg);
        border: 1px solid var(--detail-border);
        border-radius: 20px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 18px;
        height: fit-content;
        box-shadow: var(--detail-shadow);
        position: sticky;
        top: 24px;
        flex: 0 0 340px;
        width: 340px;
        margin-left: auto;
    }

    .hub-title {
        margin: 0 0 4px 0;
        font-size: 20px;
        font-weight: 700;
        color: var(--detail-title);
        letter-spacing: -0.2px;
        text-transform: none;
        line-height: 1.3;
    }

    .hub-actions-column {
        display: flex;
        flex-direction: column;
        gap: 18px;
        flex: 0 0 340px;
        width: 340px;
        margin-left: auto;
    }

    .hub-actions-column .hub-section {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .hub-section-title {
        margin: 0 0 12px 0;
        font-size: 12px;
        font-weight: 700;
        color: #7b8798;
        letter-spacing: 0.8px;
        text-transform: uppercase;
    }

    .hub-pill-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
    }

    .hub-pill {
        background: linear-gradient(180deg, #f8fbff 0%, #f1f6ff 100%);
        border: 1px solid rgba(148, 163, 184, 0.24);
        border-radius: 12px;
        padding: 13px 14px;
        min-height: 84px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .hub-pill:hover {
        background: linear-gradient(180deg, #ffffff 0%, #eff6ff 100%);
        transform: translateY(-2px);
        border-color: rgba(31, 26, 90, 0.32);
        box-shadow: 0 8px 16px rgba(31, 26, 90, 0.12);
    }

    .hub-pill-label {
        margin: 0 0 6px 0;
        font-size: 10px;
        font-weight: 700;
        color: #7b8798;
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }

    .hub-pill-value {
        margin: 0;
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.3;
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

    .hub-item h4,
    .hub-pill-value {
        color: #0f172a;
    }

    .hub-item:hover {
        background: #ffffff;
        border-color: rgba(31, 26, 90, 0.32);
        box-shadow: 0 10px 20px rgba(15, 23, 42, 0.08);
    }

    .hub-item-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(
            140deg,
            rgba(31, 26, 90, 0.16),
            rgba(54, 89, 170, 0.14)
        );
        color: var(--main-navy-clr);
        border: 1px solid rgba(31, 26, 90, 0.16);
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

    .hub-alert {
        background: rgba(34, 197, 94, 0.05);
        border: 1px solid rgba(34, 197, 94, 0.2);
        border-radius: 12px;
        padding: 12px 14px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .hub-alert svg {
        color: rgb(34, 197, 94);
        flex-shrink: 0;
        width: 16px;
        height: 16px;
    }

    .hub-alert p {
        margin: 0;
        font-size: 11px;
        font-weight: 600;
        color: rgb(34, 197, 94);
        letter-spacing: 0.2px;
        line-height: 1.4;
    }

    /* Rundown List */
    .rundown-list {
        margin-top: 0;
        padding: var(--spacing-lg) 0 0 0;
        background-color: transparent;
        border: none;
        border-radius: 0;
    }

    .rundown-list h2 {
        color: var(--detail-title);
        font-size: 20px;
        font-weight: 700;
        margin: 0 0 var(--spacing-lg) 0;
        letter-spacing: -0.2px;
    }

    .rundown-list ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .rundown-list li {
        display: flex;
        align-items: flex-start;
        gap: var(--spacing-lg);
        padding: 16px 18px;
        margin-bottom: var(--spacing-sm);
        background: linear-gradient(180deg, #f8fbff 0%, #ffffff 100%);
        border-radius: var(--radius-xl);
        border: 1px solid rgba(148, 163, 184, 0.22);
        box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
        position: relative;
    }

    .rundown-list li::before {
        content: "";
        width: 8px;
        height: 8px;
        border-radius: 999px;
        background: linear-gradient(135deg, #facc15 0%, #f59e0b 100%);
        box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.16);
        margin-top: 7px;
        flex-shrink: 0;
    }

    .rundown-list li:last-child {
        margin-bottom: 0;
    }

    .time-rundown {
        color: var(--main-navy-clr);
        font-weight: 700;
        font-size: 13px;
        white-space: nowrap;
        min-width: 140px;
        letter-spacing: 0.2px;
    }

    .activity-rundown {
        color: #334155;
        font-size: 14px;
        line-height: 1.6;
        flex: 1;
    }

    @media (max-width: 1100px) {
        .detail-layout-top {
            flex-direction: column;
            gap: 18px;
        }

        .hub-actions-column {
            width: 100%;
            flex: 1 1 auto;
            margin-left: 0;
        }

        .hub-card {
            position: static;
            top: auto;
            width: 100%;
            flex: 1 1 auto;
            margin-left: 0;
        }

        .hub-pill-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .vsa-grid {
            grid-template-columns: 1fr;
        }

        .hub-pill-grid {
            grid-template-columns: 1fr;
        }

        .vsa-title,
        .vsa-subtitle,
        .hub-title,
        .rundown-list h2 {
            font-size: 18px;
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
        }

        .hero-image {
            height: 200px;
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
            padding: var(--spacing-sm);
        }

        .event-info-cards {
            grid-template-columns: 1fr;
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

        .hub-pill-grid {
            grid-template-columns: 1fr;
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
            min-width: 120px;
            font-size: var(--font-size-sm);
        }

        .activity-rundown {
            font-size: var(--font-size-sm);
        }

        .rundown-list li {
            padding: var(--spacing-md);
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

        .hero-image {
            height: 180px;
        }

        .time-rundown {
            min-width: 100px;
            font-size: var(--font-size-sm);
        }

        .activity-rundown {
            font-size: var(--font-size-sm);
        }

        .rundown-list li {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--spacing-xs);
            padding: var(--spacing-sm);
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
                  {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('D M d Y') : 'TBA' }}
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
                  {{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('h:i A') : 'TBA' }}
                  @if($event->event_time_end)
                    - {{ \Carbon\Carbon::parse($event->event_time_end)->format('h:i A') }}
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
                @if((float) ($eventCompensation['fee_trainer'] ?? 0) > 0)
                  <span class="info-card-value">
                    Rp {{ number_format((float) ($eventCompensation['fee_trainer'] ?? 0), 0, ',', '.') }}/peserta
                  </span>
                @else
                  <span class="info-card-value">Belum diatur admin</span>
                @endif
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
        <p class="vsa-title">Aset Acara</p>
        <div class="vsa-grid {{ $hasHybridAssets ? 'is-hybrid' : '' }}">
          @if($hasMapLink)
            <article class="vsa-card">
              <div class="vsa-icon vsa-icon-blue">
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
              <a href="{{ $mapLink ?: '#' }}" target="_blank" class="vsa-btn vsa-btn-primary" {{ empty($mapLink) ? 'disabled' : '' }}>
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
              <div class="vsa-icon vsa-icon-blue">
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
              <a href="{{ $zoomLink ?: '#' }}" target="_blank" class="vsa-btn vsa-btn-primary" {{ empty($zoomLink) ? 'disabled' : '' }}>
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
              <div class="vsa-icon vsa-icon-amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                  <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
                  <path
                    d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z" />
                </svg>
              </div>
              <div class="vsa-meta">
                <p class="vsa-label">LATAR VIRTUAL</p>
                <h3>Latar Virtual</h3>
                <p class="vsa-desc">PNG Resolusi Tinggi   Berlogo</p>
              </div>
              <a href="{{ $vbgUrl }}" class="vsa-btn vsa-btn-primary" download>
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

        <p class="vsa-title">Deskripsi Event</p>
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

        <section class="rundown-list">
          <h2>Rundown Acara</h2>
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
          <ul>
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
              <li>
                <span class="time-rundown">{{ $timeStr !== '' ? $timeStr : '-' }}</span>
                <span class="activity-rundown">{{ $activity !== '' ? $activity : '-' }}</span>
              </li>
            @empty
              <li>
                <span class="time-rundown">—</span>
                <span class="activity-rundown">Schedule will be announced.</span>
              </li>
            @endforelse
          </ul>
        </section>
      </section>

      <div class="hub-actions-column">
        <p class="hub-title">Materi</p>
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
          $statusColor = match($myMaterialStatus ?? 'not_uploaded') {
            'approved'     => '#166534',
            'pending_review' => '#92400e',
            'rejected'     => '#991b1b',
            default        => '#64748b',
          };
          $statusBg = match($myMaterialStatus ?? 'not_uploaded') {
            'approved'     => '#dcfce7',
            'pending_review' => '#fef3c7',
            'rejected'     => '#fee2e2',
            default        => '#f1f5f9',
          };
          $statusLabel = match($myMaterialStatus ?? 'not_uploaded') {
            'approved'     => '✓ Materi Disetujui',
            'pending_review' => '⏳ Menunggu Review',
            'rejected'     => '✕ Perlu Revisi',
            default        => '— Belum Upload',
          };
        @endphp

        <div style="margin-top:12px; padding:10px 14px; background:{{ $statusBg }}; border-radius:10px; font-size:13px; font-weight:600; color:{{ $statusColor }};">
          {{ $statusLabel }}
        </div>

        @if(($myModules ?? collect())->isNotEmpty())
          <div style="margin-top:10px;">
            @foreach($myModules as $mod)
              <div style="display:flex; align-items:center; justify-content:space-between; padding:8px 10px; background:#f8fafc; border-radius:8px; margin-bottom:6px; border:1px solid #e2e8f0; font-size:12px;">
                <div style="display:flex; align-items:center; gap:8px; overflow:hidden;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5z"/><path d="M9.5 0V3a1.5 1.5 0 0 0 1.5 1.5H14"/></svg>
                  <span style="overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:140px;">{{ $mod->original_name }}</span>
                </div>
                <span style="font-size:11px; padding:2px 7px; border-radius:20px; font-weight:700;
                  background:{{ $mod->status === 'approved' ? '#dcfce7' : ($mod->status === 'rejected' ? '#fee2e2' : '#fef3c7') }};
                  color:{{ $mod->status === 'approved' ? '#166534' : ($mod->status === 'rejected' ? '#991b1b' : '#92400e') }};">
                  {{ $mod->status === 'approved' ? 'Approved' : ($mod->status === 'rejected' ? 'Ditolak' : 'Pending') }}
                </span>
              </div>
            @endforeach
          </div>
        @endif
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