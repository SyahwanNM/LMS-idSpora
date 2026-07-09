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
    /* Global styles and variables */
    :root {
        --purple-brand: #51376c;
        --purple-hover: #3f2a54;
        --purple-light: #f5ecf7;
        --purple-border: #e6d5ec;
        --gray-text: #475467;
        --dark-text: #101828;
    }

    main {
        padding: 32px;
        background-color: #F8F9FC;
        overflow-y: auto;
        max-width: none;
        margin: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        gap: 0;
        font-family: "Plus Jakarta Sans", "Inter", "Segoe UI", sans-serif;
    }

    /* Hero Section */
    .hero-section {
        width: 100%;
        margin: 0 0 24px 0;
        padding: 32px;
        background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
        border-radius: 16px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 10px 25px rgba(27, 23, 99, 0.15);
    }

    .hero-container {
        max-width: none;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .hero-top-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 0;
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.85);
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .back-button:hover {
        color: #ffffff;
        transform: translateX(-3px);
    }

    .hero-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 40px;
        z-index: 2;
    }

    .hero-left {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .event-hero-title {
        font-size: 40px;
        font-weight: 800;
        color: #ffffff;
        margin: 0;
        letter-spacing: -1px;
        line-height: 1.2;
    }

    .event-hero-subtitle {
        font-size: 16px;
        color: rgba(255, 255, 255, 0.75);
        margin: 0;
        line-height: 1.6;
        max-width: 650px;
    }

    .hero-tags {
        display: flex;
        gap: 8px;
        margin-top: 4px;
    }

    .tag-purple {
        background: rgba(255, 255, 255, 0.15);
        color: #FFFFFF;
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(255,255,255,0.2);
    }

    .tag-outline {
        border: 1px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.05);
        color: #ffffff;
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .hero-media {
        width: 320px;
        height: 180px;
        flex-shrink: 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border: 2px solid rgba(255,255,255,0.2);
    }

    .hero-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .hero-image-placeholder {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #51376c 0%, #2e2050 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-size: 48px;
        font-weight: 800;
    }

    /* Metadata cards row */
    .hero-meta-row {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 12px;
        width: 100%;
        margin-top: 12px;
    }

    @media (max-width: 992px) {
        .hero-meta-row {
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        }
        .hero-body {
            flex-direction: column;
            align-items: flex-start;
            gap: 20px;
        }
        .hero-media {
            width: 100%;
            height: 200px;
        }
    }

    .meta-card {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 12px;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        box-shadow: none;
        backdrop-filter: blur(4px);
        transition: all 0.2s ease;
    }

    .meta-card:hover {
        background: rgba(255, 255, 255, 0.12);
        border-color: rgba(255, 255, 255, 0.25);
    }

    .meta-icon-wrapper {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.15);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .meta-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .meta-label {
        font-size: 9px;
        font-weight: 700;
        color: rgba(255, 255, 255, 0.6);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .meta-value {
        font-size: 12px;
        font-weight: 700;
        color: #ffffff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Tab switcher */
    .course-tabs {
        display: flex;
        background: #FFFFFF;
        border: 1px solid #EAECF0;
        border-radius: 12px;
        padding: 0;
        overflow: hidden;
        margin: 24px 0 0 0;
        width: 100%;
    }

    .tab-pill {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 16px 20px;
        background: transparent;
        border: none;
        font-size: 14px;
        font-weight: 700;
        color: #475467;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 3px solid transparent;
    }

    .tab-pill:hover {
        color: var(--purple-hover);
        background: #F9FAFB;
    }

    .tab-pill.active {
        color: var(--purple-brand);
        border-bottom-color: var(--purple-brand);
        background: var(--purple-light);
    }

    .tab-pill i {
        font-size: 16px;
    }

    /* Tab content container */
    .tab-content {
        display: none;
        animation: fadeInTab 0.35s ease;
        width: 100%;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeInTab {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* 2 Column Layout */
    .modern-detail-grid {
        display: grid;
        grid-template-columns: 1fr 360px;
        gap: 24px;
        width: 100%;
        align-items: flex-start;
    }

    @media (max-width: 992px) {
        .modern-detail-grid {
            grid-template-columns: 1fr;
        }
    }

    .detail-main-content {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .detail-sidebar {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    /* Left Card content wrapper */
    .detail-card-wrapper {
        background: #FFFFFF;
        border: 1px solid #EAECF0;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .section-divider {
        border: 0;
        border-top: 1px solid #EAECF0;
        margin: 0;
    }

    /* About section */
    .about-header-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--purple-brand);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 4px;
        display: block;
    }

    .about-title {
        font-size: 24px;
        font-weight: 800;
        color: #101828;
        margin: 0 0 16px 0;
    }

    .about-body-cols {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: 24px;
        margin-top: 12px;
    }

    @media (max-width: 768px) {
        .about-body-cols {
            grid-template-columns: 1fr;
        }
    }

    .about-text-content {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .about-p {
        font-size: 14px;
        color: #475467;
        line-height: 1.6;
        margin: 0;
    }

    .event-description-text {
        font-size: 14px;
        color: #475467;
        line-height: 1.6;
    }
    .event-description-text p {
        font-size: 14px;
        color: #475467;
        line-height: 1.6;
        margin-bottom: 12px;
    }
    .event-description-text p:last-child {
        margin-bottom: 0;
    }
    .event-description-text h1,
    .event-description-text h2,
    .event-description-text h3 {
        color: #101828;
        font-weight: 700;
        margin-top: 16px;
        margin-bottom: 8px;
    }
    .event-description-text h1 { font-size: 18px; }
    .event-description-text h2 { font-size: 16px; }
    .event-description-text h3 { font-size: 14px; }
    .event-description-text ul,
    .event-description-text ol {
        margin-bottom: 12px;
        padding-left: 20px;
    }
    .event-description-text li {
        margin-bottom: 4px;
    }

    /* Features list */
    .features-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 8px;
    }

    .feature-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
    }

    .feature-icon-shell {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: var(--purple-light);
        color: var(--purple-brand);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }

    .feature-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .feature-title {
        font-size: 13px;
        font-weight: 700;
        color: #344054;
    }

    .feature-sub {
        font-size: 12px;
        color: #667085;
    }

    
    /* Rundown Timeline */
    .timeline-header-label {
        font-size: 11px;
        font-weight: 700;
        color: var(--purple-brand);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        display: block;
    }

    .timeline-track {
        position: relative;
        padding-left: 120px;
        margin-left: 20px;
        border-left: 2px solid var(--purple-border);
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .timeline-row {
        position: relative;
        min-height: 24px;
    }

    .timeline-time-label {
        position: absolute;
        left: -130px;
        width: 100px;
        text-align: right;
        font-size: 13px;
        font-weight: 700;
        color: var(--purple-hover);
    }

    .timeline-row-dot {
        position: absolute;
        left: -6px;
        top: 4px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: var(--purple-brand);
        box-shadow: 0 0 0 4px #FFFFFF, 0 0 0 6px var(--purple-light);
    }

    .timeline-row-content {
        font-size: 13px;
        font-weight: 600;
        color: #344054;
        padding-left: 16px;
        line-height: 1.4;
    }

    .timeline-empty {
        text-align: center;
        padding: 24px;
        color: #667085;
        font-size: 13px;
    }

    /* Sidebar Cards */
    .sidebar-card {
        background: #FFFFFF;
        border: 1px solid #EAECF0;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .sidebar-header {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        color: #344054;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-bottom: 1px solid #F2F4F7;
        padding-bottom: 12px;
        margin-bottom: 4px;
    }

    .sidebar-header i {
        color: var(--purple-brand);
        font-size: 16px;
    }

    /* Assets & Materials rows */
    .sidebar-row-item {
        border: 1px solid #EAECF0;
        border-radius: 12px;
        padding: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #FFFFFF;
    }

    .row-item-left {
        display: flex;
        gap: 12px;
        align-items: center;
        min-width: 0;
    }

    .row-item-icon-shell {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .row-item-icon-shell.purple { background: var(--purple-light); color: var(--purple-brand); }
    .row-item-icon-shell.red { background: #FEF3F2; color: #D92D20; }
    .row-item-icon-shell.green { background: #D1FADF; color: #039855; }

    .row-item-info {
        display: flex;
        flex-direction: column;
        gap: 2px;
        min-width: 0;
    }

    .row-item-title {
        font-size: 13px;
        font-weight: 700;
        color: #344054;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .row-item-sub {
        font-size: 11px;
        color: #667085;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Action Buttons inside rows */
    .row-btn-solid {
        background: var(--purple-brand);
        color: #FFFFFF;
        border: none;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .row-btn-solid:hover {
        background: var(--purple-hover);
    }

    .row-btn-outline {
        border: 1px solid #D0D5DD;
        background: #FFFFFF;
        color: #344054;
        padding: 8px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .row-btn-outline:hover {
        background: #F9FAFB;
        border-color: #CBD5E1;
    }

    .materials-action {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .materials-action:hover {
        border-color: var(--purple-border);
        background: var(--purple-light);
    }

    /* Green status banner */
    .materi-uploaded-banner {
        background: #ECFDF5;
        border: 1px solid #D1FADF;
        color: #027A48;
        padding: 10px 14px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Files listing */
    .file-items-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .file-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        border: 1px solid #F2F4F7;
        background: #FAFAFA;
        border-radius: 8px;
        gap: 12px;
    }

    .file-row-left {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 0;
    }

    .file-row-left i {
        color: #667085;
        font-size: 16px;
    }

    .file-row-name {
        font-size: 12px;
        color: #344054;
        font-weight: 600;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Status badge capsules */
    .status-capsule {
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 9px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .status-capsule.approved {
        background: #D1FADF;
        color: #027A48;
    }

    .status-capsule.rejected {
        background: #FEE4E2;
        color: #B42318;
    }

    .status-capsule.pending {
        background: #FEF0C7;
        color: #B54708;
    }

    /* Tab 2: Participants design styling */
    .event-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
        width: 100%;
    }

    .stat-card-mini {
        background: #FFFFFF;
        border: 1px solid #EAECF0;
        border-radius: 16px;
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card-mini:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 24, 40, 0.08);
        border-color: var(--purple-border);
    }

    .stat-icon-wrapper {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }

    .stat-icon-wrapper.blue { background: rgba(37, 99, 235, 0.08); color: #2563eb; }
    .stat-icon-wrapper.green { background: rgba(16, 185, 129, 0.08); color: #10b981; }
    .stat-icon-wrapper.amber { background: rgba(245, 158, 11, 0.08); color: #f59e0b; }
    .stat-icon-wrapper.purple { background: rgba(81, 55, 108, 0.08); color: var(--purple-brand); }

    .stat-info-mini {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .stat-label-mini {
        font-size: 10px;
        font-weight: 700;
        color: #667085;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-val-mini {
        font-size: 15px;
        font-weight: 800;
        color: #101828;
    }

    .participant-filters {
        display: flex;
        gap: 8px;
        margin-bottom: 24px;
        border-bottom: 1px solid #EAECF0;
        padding-bottom: 16px;
    }

    .filter-pill {
        padding: 8px 16px;
        border-radius: 99px;
        border: 1px solid #EAECF0;
        background: #FFFFFF;
        color: #475467;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .filter-pill:hover {
        background: var(--purple-light);
        color: var(--purple-brand);
        border-color: var(--purple-border);
    }

    .filter-pill.active {
        background: var(--purple-light);
        color: var(--purple-brand);
        border-color: var(--purple-border);
        box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05);
    }

    .enrollment-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        background: #FFFFFF;
        border: 1px solid #EAECF0;
        border-radius: 12px;
        padding: 14px 20px;
        box-shadow: 0 1px 3px rgba(16, 24, 40, 0.05);
    }

    .enrollment-header h3 {
        margin: 0;
        font-size: 14px;
        font-weight: 800;
        color: #344054;
        text-transform: uppercase;
    }

    .total-badge {
        background: var(--purple-brand);
        color: #FFFFFF;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 700;
    }

    .participant-table-wrapper {
        background: #FFFFFF;
        border: 1px solid #EAECF0;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(16, 24, 40, 0.03);
        margin-top: 16px;
    }

    .participant-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
    }

    .participant-table th {
        background: #F9FAFB;
        padding: 14px 20px;
        font-size: 12px;
        font-weight: 700;
        color: #475467;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 1px solid #EAECF0;
    }

    .participant-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #EAECF0;
        font-size: 14px;
        vertical-align: middle;
    }

    .participant-row {
        transition: all 0.2s ease;
    }

    .participant-row:hover {
        background: #F9FAFB;
    }

    .participant-user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .participant-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #F2F4F7;
    }

    .participant-name-wrapper {
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .participant-name {
        font-weight: 700;
        color: #101828;
        font-size: 14px;
    }

    .participant-email {
        font-size: 12px;
        color: #667085;
    }

    .whatsapp-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 99px;
        background: #E8F5E9;
        color: #2E7D32;
        font-size: 12px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
        border: 1px solid #C8E6C9;
    }

    .whatsapp-badge:hover {
        background: #C8E6C9;
        transform: translateY(-1px);
    }

    .no-contact {
        color: #98A2B3;
        font-size: 13px;
    }

    .participant-reg-date {
        font-size: 13px;
        color: #475467;
        font-weight: 500;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.2px;
    }

    .status-pill.status-present {
        background: #ECFDF5;
        color: #027A48;
        border: 1px solid #D1FADF;
    }

    .status-pill.status-absent {
        background: #FFF9E6;
        color: #B45309;
        border: 1px solid #FFE5A3;
    }

    .attendance-timestamp {
        font-size: 13px;
        color: #027A48;
        font-weight: 600;
    }

    .no-attendance-timestamp {
        color: #98A2B3;
        font-size: 13px;
    }

    .empty-table-state {
        text-align: center;
        padding: 48px !important;
        color: #667085;
    }

    .empty-table-state .empty-icon {
        font-size: 40px;
        color: #D0D5DD;
        margin-bottom: 12px;
        display: block;
    }

    .empty-table-state p {
        margin: 0;
        font-size: 14px;
        font-weight: 500;
    }

    /* ==========================================================================
       TRAINER EVENT DETAIL STYLING (EXACT COHESION WITH FINANCE.BLADE.PHP DESIGN)
       ========================================================================== */

    /* Import Google Font 'Outfit' (Identical to Finance page) */
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap');

    /* 1. Global Page Font & Background */
    main {
        font-family: 'Outfit', sans-serif !important;
        background-color: #f8fafc !important;
        padding: 32px !important;
        color: #334155 !important;
    }

    main * {
        box-sizing: border-box !important;
        font-family: 'Outfit', sans-serif !important;
    }

    /* 2. Hero Section (Identical to realized earnings navy card) */
    .hero-section {
        background: linear-gradient(135deg, #2e2050 0%, #624388 100%) !important;
        border-radius: 24px !important;
        padding: 32px !important;
        position: relative !important;
        overflow: hidden !important;
        color: white !important;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1) !important;
        margin-bottom: 24px !important;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s ease !important;
    }
    
    /* Decorative circles identical to finance.blade.php */
    .hero-section::before, .hero-section::after {
        content: '' !important;
        position: absolute !important;
        border-radius: 50% !important;
        background: rgba(255, 255, 255, 0.1) !important;
        transition: transform 0.5s ease !important;
        pointer-events: none !important;
    }
    .hero-section::before {
        width: 150px !important;
        height: 150px !important;
        top: -40px !important;
        right: -40px !important;
    }
    .hero-section::after {
        width: 250px !important;
        height: 250px !important;
        bottom: -100px !important;
        right: 10% !important;
    }
    .hero-section:hover::before {
        transform: scale(1.1) translate(-10px, 10px) !important;
    }
    .hero-section:hover::after {
        transform: scale(1.05) translate(10px, -10px) !important;
    }

    /* Back Navigation & Titles */
    .back-button {
        color: rgba(255, 255, 255, 0.85) !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        transition: all 0.2s ease !important;
        background: transparent !important;
        border: none !important;
        cursor: pointer !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .back-button:hover {
        color: #ffffff !important;
        transform: translateX(-3px) !important;
    }
    .event-hero-title {
        font-size: 38px !important;
        font-weight: 800 !important;
        color: #ffffff !important;
        letter-spacing: -1px !important;
        line-height: 1.1 !important;
        margin: 12px 0 12px 0 !important;
    }
    .event-hero-subtitle {
        font-size: 14px !important;
        color: rgba(255, 255, 255, 0.75) !important;
        line-height: 1.5 !important;
        max-width: 750px !important;
        margin: 0 !important;
    }
    .tag-outline {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        padding: 5px 14px !important;
        border-radius: 99px !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        letter-spacing: 0.5px !important;
    }

    /* 3. Cards & Container Boxes (Identical to bd-card / table-section) */
    .detail-card-wrapper, .sidebar-card, .participant-table-wrapper {
        background: #ffffff !important;
        border-radius: 20px !important;
        padding: 24px !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03) !important;
        border: 1px solid #f1f5f9 !important;
        transition: box-shadow 0.3s ease !important;
    }
    .detail-card-wrapper:hover, .sidebar-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06) !important;
    }

    .about-body-cols {
        grid-template-columns: 1fr !important;
        gap: 0 !important;
    }

    .about-header-label {
        font-size: 16px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .about-header-label::before {
        content: '' !important;
        width: 12px !important;
        height: 12px !important;
        border-radius: 50% !important;
        background: #fbbf24 !important;
        display: inline-block !important;
    }
    .event-description-text p {
        font-size: 14.5px !important;
        color: #334155 !important;
        line-height: 1.65 !important;
    }

    .feature-icon-shell {
        background: #f1f5f9 !important;
        color: #2e2050 !important;
        border-radius: 8px !important;
        width: 32px !important;
        height: 32px !important;
    }
    .feature-title {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
    }

    /* Rundown Timeline */
    .timeline-header-label {
        font-size: 16px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .timeline-header-label::before {
        content: '' !important;
        width: 12px !important;
        height: 12px !important;
        border-radius: 50% !important;
        background: #fbbf24 !important;
        display: inline-block !important;
    }
    .timeline-track {
        padding-left: 120px !important;
        border-left: 2px solid #f1f5f9 !important;
        gap: 24px !important;
        margin-left: 20px !important;
        margin-top: 16px !important;
    }
    .timeline-time-label {
        left: -130px !important;
        width: 100px !important;
        color: #64748b !important;
        font-weight: 700 !important;
        font-size: 13px !important;
    }
    .timeline-row-dot {
        background: #fbbf24 !important;
        box-shadow: 0 0 0 4px #FFFFFF, 0 0 0 5px #f1f5f9 !important;
        width: 12px !important;
        height: 12px !important;
        top: 3px !important;
        left: -7px !important;
    }
    .timeline-row-content {
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #334155 !important;
    }

    .detail-main-content {
        gap: 12px !important;
    }

    /* 4. Tabs Switching Control (Segmented nav style) */
    .course-tabs {
        background: #f8fafc !important;
        border: 1px solid #f1f5f9 !important;
        border-radius: 16px !important;
        padding: 6px !important;
        display: inline-flex !important;
        width: fit-content !important;
        gap: 6px !important;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02) !important;
        margin: 0 0 10px 0 !important;
    }
    .tab-pill {
        flex: initial !important;
        padding: 10px 24px !important;
        border-radius: 12px !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        color: #64748b !important;
        border: none !important;
        background: transparent !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        border-bottom: none !important;
    }
    .tab-pill:hover {
        color: #0f172a !important;
        background: rgba(255, 255, 255, 0.6) !important;
    }
    .tab-pill.active {
        color: #ffffff !important;
        background: linear-gradient(135deg, #2e2050 0%, #51376c 100%) !important;
        box-shadow: 0 4px 10px rgba(27, 23, 99, 0.2) !important;
    }

    /* 5. Inner Grid Boxes & Sidebar Items (Identical to bd-item style) */
    .info-grid-box {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 12px !important;
        padding-top: 14px !important;
    }
    .info-grid-item, .sidebar-row-item, .file-row {
        padding: 16px !important;
        background: #f8fafc !important;
        border-radius: 12px !important;
        border: 1px solid #f1f5f9 !important;
        transition: transform 0.2s ease, background 0.2s ease, border-color 0.2s ease !important;
        width: 100% !important;
    }
    .info-grid-item {
        display: flex !important;
        flex-direction: column !important;
        gap: 4px !important;
    }
    .info-grid-item:hover, .sidebar-row-item:hover, .file-row:hover {
        transform: translateX(4px) !important;
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
    }
    .info-grid-item.full-width {
        grid-column: span 2 !important;
    }
    .info-grid-label {
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #64748b !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    .info-grid-label i {
        color: #2e2050 !important;
        font-size: 13px !important;
    }
    .info-grid-val {
        font-size: 14px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
    }

    /* 6. Plain Mini Badge Stats Row */
    .participant-simple-stats-row {
        display: flex !important;
        align-items: center !important;
        flex-wrap: wrap !important;
        gap: 12px !important;
        margin-bottom: 24px !important;
        width: 100% !important;
    }
    .stat-mini-pill {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        padding: 8px 14px !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 13px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02) !important;
    }
    .stat-mini-label {
        color: #64748b !important;
        font-weight: 500 !important;
    }
    .stat-mini-value {
        color: #0f172a !important;
        font-weight: 800 !important;
    }
    /* 7. Participant Table Header & Dropdown Layout */
    .enrollment-header {
        background: #ffffff !important;
        border-radius: 12px !important;
        padding: 12px 20px !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.01) !important;
        border: 1px solid #f1f5f9 !important;
        margin-bottom: 16px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 16px !important;
    }
    .enrollment-header h3 {
        font-size: 14px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        margin: 0 !important;
    }
    .enrollment-header-left {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }
    .total-badge {
        background: #2e2050 !important;
        font-size: 10px !important;
        font-weight: 700 !important;
        margin: 0 !important;
    }
    .participant-filter-dropdown {
        font-family: 'Outfit', sans-serif !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #475569 !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        padding: 6px 32px 6px 12px !important;
        background: #ffffff url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23475569' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 12px center/10px auto !important;
        -webkit-appearance: none !important;
        -moz-appearance: none !important;
        appearance: none !important;
        outline: none !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }
    .participant-filter-dropdown:hover {
        border-color: #cbd5e1 !important;
        background-color: #f8fafc !important;
    }
    .participant-filter-dropdown:focus {
        border-color: #2e2050 !important;
        box-shadow: 0 0 0 3px rgba(46, 32, 80, 0.1) !important;
    }

    .participant-table-wrapper {
        background: #ffffff !important;
        border-radius: 20px !important;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03) !important;
        border: 1px solid #f1f5f9 !important;
        padding: 0 !important;
        overflow: hidden !important;
        max-height: 520px !important;
        overflow-y: auto !important;
    }
    .participant-table th {
        background: #f8fafc !important;
        color: #64748b !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        border-bottom: 2px solid #f1f5f9 !important;
        padding: 16px 20px !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
    }
    .participant-table td {
        padding: 16px 20px !important;
        border-bottom: 1px solid #f1f5f9 !important;
        color: #334155 !important;
        font-size: 14px !important;
    }
    .participant-row {
        transition: background 0.2s ease !important;
    }
    .participant-row:hover {
        background: #f8fafc !important;
    }
    .participant-avatar {
        width: 40px !important;
        height: 40px !important;
        border: 2px solid #f1f5f9 !important;
    }
    .participant-name {
        font-weight: 700 !important;
        color: #0f172a !important;
    }
    .participant-email {
        color: #64748b !important;
    }
    
    /* WhatsApp Badge Outlines */
    .whatsapp-badge {
        background: #e8f5e9 !important;
        color: #2e7d32 !important;
        border: 1px solid #c8e6c9 !important;
        padding: 6px 12px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        border-radius: 99px !important;
    }
    .whatsapp-badge:hover {
        background: #c8e6c9 !important;
        transform: translateY(-1px) !important;
    }
    .status-pill.status-present {
        background: #dcfce7 !important;
        color: #166534 !important;
        border: 1px solid #bbf7d0 !important;
        font-weight: 700 !important;
    }
    .status-pill.status-absent {
        background: #fef3c7 !important;
        color: #92400e !important;
        border: 1px solid #fde68a !important;
        font-weight: 700 !important;
    }

    /* 8. Sticky Sidebar Card */
    .detail-sidebar {
        position: sticky !important;
        top: 100px !important;
        z-index: 10 !important;
    }
    .sidebar-header {
        font-size: 13px !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding-bottom: 12px !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
    }
    .sidebar-header i {
        color: #2e2050 !important;
    }
    .row-item-title {
        font-weight: 700 !important;
        color: #0f172a !important;
    }
    .row-item-sub {
        color: #64748b !important;
    }

    /* Action Buttons (Consistent with btn-invoice / finance page style) */
    .row-btn-solid {
        background: linear-gradient(135deg, #2e2050 0%, #51376c 100%) !important;
        color: white !important;
        border: none !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 10px rgba(27, 23, 99, 0.2) !important;
        transition: all 0.2s ease !important;
        cursor: pointer !important;
    }
    .row-btn-solid:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 6px 15px rgba(27, 23, 99, 0.3) !important;
    }
    .row-btn-outline {
        border: 1px solid #d0d5dd !important;
        background: #ffffff !important;
        color: #344054 !important;
        padding: 8px 16px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        transition: all 0.2s ease !important;
    }
    .row-btn-outline:hover {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        transform: translateY(-1px) !important;
    }

    .materi-uploaded-banner {
        background: #dcfce7 !important;
        border: 1px solid #bbf7d0 !important;
        color: #166534 !important;
        padding: 10px 14px !important;
        border-radius: 8px !important;
        font-size: 12px !important;
        font-weight: 700 !important;
    }
    .file-row {
        padding: 12px 14px !important;
        border: 1px solid #f1f5f9 !important;
        background: #f8fafc !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    .file-row-name {
        font-size: 12.5px !important;
        color: #334155 !important;
        font-weight: 600 !important;
    }
    .status-capsule.approved { background: #dcfce7 !important; color: #166534 !important; }
    .status-capsule.rejected { background: #fee2e2 !important; color: #991b1b !important; }
    .status-capsule.pending { background: #fef3c7 !important; color: #92400e !important; }

    /* 10. Plain Event Summary List (No Box, No Icon, No Bold, Natural Font) */
    .event-meta-plain-list {
        margin-top: 16px !important;
        display: flex !important;
        flex-direction: column !important;
        gap: 6px !important;
        width: 100% !important;
    }
    .plain-list-item {
        font-size: 14.5px !important;
        color: #334155 !important;
        line-height: 1.65 !important;
        font-weight: 400 !important;
    }

    /* Media overrides */
    @media (max-width: 992px) {
        .detail-sidebar {
            position: static !important;
        }
        .course-tabs {
            display: flex !important;
            width: 100% !important;
        }
        .tab-pill {
            flex: 1 !important;
            padding: 12px 10px !important;
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
          <i class="bi bi-arrow-left"></i>
          <span>Kembali ke Semua Event</span>
        </button>
      </div>

      <div class="hero-body">
        <div class="hero-left">
          <h1 class="event-hero-title">
            {{ $event->title }}
          </h1>
          
          <p class="event-hero-subtitle">
            {{ $event->short_description ?: 'Webinar interaktif dengan berbagai sesi inspiratif, materi berkualitas, dan diskusi mendalam bersama para narasumber.' }}
          </p>

          <div class="hero-tags">
            <span class="tag-outline"><i class="bi bi-globe"></i> {{ $event->category ?? 'Webinar' }}</span>
          </div>

          @php
            $startDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
            $untilDate = !empty($event->event_until_date) ? \Carbon\Carbon::parse($event->event_until_date) : null;
            
            $startTime = $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : null;
            $endTime = !empty($event->event_until_time) 
              ? \Carbon\Carbon::parse($event->event_until_time)->format('H:i') 
              : (!empty($event->event_time_end) ? \Carbon\Carbon::parse($event->event_time_end)->format('H:i') : null);

            $titleLower = strtolower($event->title ?? '');
            if (str_contains($titleLower, 'dosen') || str_contains($titleLower, 'guru') || str_contains($titleLower, 'pendidik')) {
                $targetAudience = 'Dosen & Pendidik';
            } elseif (str_contains($titleLower, 'lomba') || str_contains($titleLower, 'mahasiswa') || str_contains($titleLower, 'siswa')) {
                $targetAudience = 'Mahasiswa & Siswa';
            } else {
                $targetAudience = 'Mahasiswa, Dosen, & Umum';
            }
          @endphp


        </div>


      </div>
    </div>
  </div>
  <div class="detail-layout">
    <div class="modern-detail-grid">
      <!-- Left Column: Tabs & Tab Content -->
      <div class="detail-main-content">
        
        <!-- Tab navigation inside Left Column -->
        <div class="course-tabs" style="margin-top: 0;">
            <button class="tab-pill active" type="button" data-target="event-detail-info">
                <i class="bi bi-info-circle"></i>
                <span>Detail Informasi Event</span>
            </button>
            <button class="tab-pill" type="button" data-target="event-participants">
                <i class="bi bi-people"></i>
                <span>Daftar Peserta</span>
            </button>
        </div>

        <!-- Tab 1: Detail Informasi Event -->
        <section id="event-detail-info" class="tab-content active">
          <div class="detail-card-wrapper">
            <div>
              <span class="about-header-label">TENTANG EVENT</span>
              
              @php
                $eventDescription = trim((string) ($event->description ?? ''));
                if ($eventDescription === '') {
                  $eventDescription = trim((string) ($event->short_description ?? ''));
                }
                if ($eventDescription === '') {
                  $eventDescription = trim((string) ($event->materi ?? ''));
                }
              @endphp

              <div class="about-body-cols">
                <!-- Left: Text and Features -->
                <div class="about-text-content">
                  <div class="event-description-text">
                    {!! $eventDescription !== '' ? $eventDescription : '<p class="about-p">Deskripsi event belum tersedia.</p>' !!}
                  </div>

                  <!-- Highlighted Event Summary Callout List (Plain List, directly under description) -->
                  <div class="event-meta-plain-list">
                    <div class="plain-list-item">Tanggal: {{ $startDate ? $startDate->format('d M Y') : 'TBA' }}</div>
                    <div class="plain-list-item">Waktu: {{ $startTime ? $startTime . ($endTime ? ' - ' . $endTime : '') : 'TBA' }} WIB</div>
                    <div class="plain-list-item">Lokasi: {{ $event->location ?? ($event->zoom_link ? 'Online' : 'Offline') }}</div>
                    <div class="plain-list-item">Biaya: Rp{{ number_format((float) ($event->price ?? 0), 0, ',', '.') }}</div>
                    <div class="plain-list-item">Target Peserta: {{ $targetAudience }}</div>
                  </div>

                  @php
                    $benefits = [];
                    if (!empty($event->benefit)) {
                      if (is_array($event->benefit)) {
                        $benefits = $event->benefit;
                      } elseif (is_string($event->benefit)) {
                        $benefits = collect(explode('|', $event->benefit))->map(fn($b) => trim($b))->filter()->all();
                      }
                    }
                  @endphp
                  @if(!empty($benefits))
                    <div class="features-list">
                      @foreach($benefits as $ben)
                        @if(trim((string)$ben) !== '')
                          <div class="feature-item">
                            <div class="feature-icon-shell"><i class="bi bi-shield-check"></i></div>
                            <div class="feature-info">
                              <span class="feature-title">{{ $ben }}</span>
                            </div>
                          </div>
                        @endif
                      @endforeach
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <hr class="section-divider" />
            <div>
              <span class="timeline-header-label">RUNDOWN ACARA</span>
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
              <div class="timeline-track">
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
                  <div class="timeline-row">
                    <span class="timeline-time-label">{{ $timeStr ?: '-' }}</span>
                    <span class="timeline-row-dot"></span>
                    <div class="timeline-row-content">{{ $activity ?: '-' }}</div>
                  </div>
                @empty
                  <div class="timeline-empty">
                    <i class="bi bi-calendar-x"></i> Jadwal detail belum diumumkan.
                  </div>
                @endforelse
              </div>
            </div>
          </div>
        </section>

        <!-- Tab 2: Daftar Peserta -->
        <section id="event-participants" class="tab-content">
          @php
              $totalRegistered = $activeStudents->count();
              $attendedCount = $activeStudents->filter(function ($s) {
                  return !empty($s->attended_at) || in_array(strtolower((string) $s->attendance_status), ['yes', 'present', 'attended']);
              })->count();
              $notAttendedCount = $totalRegistered - $attendedCount;
              $attendanceRate = $totalRegistered > 0 ? round(($attendedCount / $totalRegistered) * 100) : 0;
          @endphp

          <!-- Stats overview panel -->
          <div class="participant-simple-stats-row">
              <div class="stat-mini-pill">
                  <span class="stat-mini-label">Total Terdaftar</span>
                  <span class="stat-mini-value">{{ $totalRegistered }}</span>
              </div>
              <div class="stat-mini-pill">
                  <span class="stat-mini-label">Sudah Absen</span>
                  <span class="stat-mini-value">{{ $attendedCount }}</span>
              </div>
              <div class="stat-mini-pill">
                  <span class="stat-mini-label">Belum Absen</span>
                  <span class="stat-mini-value">{{ $notAttendedCount }}</span>
              </div>
              <div class="stat-mini-pill">
                  <span class="stat-mini-label">Kehadiran</span>
                  <span class="stat-mini-value">{{ $attendanceRate }}%</span>
              </div>
          </div>


          <div class="enrollment-header">
              <div class="enrollment-header-left">
                  <h3>PESERTA TERDAFTAR</h3>
                  <span class="total-badge">{{ $totalRegistered }} TOTAL</span>
              </div>
              <div class="enrollment-header-right">
                  <select id="participant-filter-select" class="participant-filter-dropdown">
                      <option value="all">Semua Peserta</option>
                      <option value="present">Sudah Absen</option>
                      <option value="absent">Belum Absen</option>
                  </select>
              </div>
          </div>

          <div class="participant-table-wrapper">
              <table class="participant-table">
                  <thead>
                      <tr>
                          <th>Peserta</th>
                          <th>Tanggal Daftar</th>
                          <th>Status Absensi</th>
                          <th>Waktu Hadir</th>
                      </tr>
                  </thead>
                  <tbody>
                      @forelse($activeStudents as $enrollment)
                          @php
                              $hasAttended = !empty($enrollment->attended_at) || in_array(strtolower((string) $enrollment->attendance_status), ['yes', 'present', 'attended']);
                          @endphp
                          <tr class="participant-row" data-attended="{{ $hasAttended ? 'true' : 'false' }}">
                              <td>
                                  <div class="participant-user-info">
                                      <img src="{{ $enrollment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($enrollment->full_name ?: ($enrollment->user->name ?? 'User')) . '&background=f5ecf7&color=51376c' }}"
                                          alt="{{ $enrollment->full_name ?: ($enrollment->user->name ?? 'User') }}" class="participant-avatar" />
                                      <div class="participant-name-wrapper">
                                          <span class="participant-name">{{ $enrollment->full_name ?: ($enrollment->user->name ?? 'Anonim') }}</span>
                                          <span class="participant-email">{{ strtolower($enrollment->user->email ?? '') }}</span>
                                      </div>
                                  </div>
                              </td>
                              <td>
                                  <span class="participant-reg-date">
                                      <i class="bi bi-calendar3 me-1"></i> {{ $enrollment->created_at->format('d M Y') }}
                                  </span>
                              </td>
                              <td>
                                  @if($hasAttended)
                                      <span class="status-pill status-present">
                                          <i class="bi bi-check-circle-fill"></i> Hadir
                                      </span>
                                  @else
                                      <span class="status-pill status-absent">
                                          <i class="bi bi-dash-circle-fill"></i> Belum Absen
                                      </span>
                                  @endif
                              </td>
                              <td>
                                  @if($hasAttended && $enrollment->attended_at)
                                      <span class="attendance-timestamp">
                                          <i class="bi bi-clock-fill me-1"></i> {{ $enrollment->attended_at->format('H:i') }} WIB
                                      </span>
                                  @else
                                      <span class="no-attendance-timestamp">-</span>
                                  @endif
                              </td>
                          </tr>
                      @empty
                          <tr>
                              <td colspan="4" class="empty-table-state">
                                  <i class="bi bi-people-fill empty-icon"></i>
                                  <p>Belum ada peserta terdaftar untuk event ini.</p>
                              </td>
                          </tr>
                      @endforelse
                  </tbody>
              </table>
          </div>
        </section>
      </div>

      <!-- Right Column: Sidebar (Assets & Materials) -->
      <div class="detail-sidebar">
        <!-- Akses & Materi Card (Consolidated) -->
        <div class="sidebar-card assets-materials-card">
          <div class="sidebar-header">
            <i class="bi bi-folder-fill"></i>
            <span>AKSES & MATERI</span>
          </div>

          <div class="sidebar-content-box" style="display: flex; flex-direction: column; gap: 14px; margin-top: 14px;">
            <!-- Zoom Link -->
            @if($hasZoomLink)
              <div class="sidebar-row-item">
                <div class="row-item-left">
                  <div class="row-item-icon-shell purple"><i class="bi bi-camera-video"></i></div>
                  <div class="row-item-info">
                    <span class="row-item-title">Rapat Virtual</span>
                    <span class="row-item-sub">Gabung ke sesi video online</span>
                  </div>
                </div>
                <a href="{{ $zoomLink }}" target="_blank" class="row-btn-solid" style="text-decoration: none;">
                  Zoom <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
                </a>
              </div>
            @endif

            <!-- Virtual Background -->
            @if(!empty($vbgUrl))
              <div class="sidebar-row-item">
                <div class="row-item-left">
                  <div class="row-item-icon-shell purple"><i class="bi bi-image"></i></div>
                  <div class="row-item-info">
                    <span class="row-item-title">Latar Virtual</span>
                    <span class="row-item-sub">Background resmi event</span>
                  </div>
                </div>
                <a href="{{ $vbgUrl }}" class="row-btn-outline" download style="text-decoration: none; padding: 6px 12px; font-size: 11px;">
                  Unduh <i class="bi bi-download"></i>
                </a>
              </div>
            @endif

            <!-- Kirim / Edit Materi Button (Action row) -->
            <div class="sidebar-row-item materials-action" onclick="window.location.href='{{ route('trainer.events.studio', $event->id) }}'" style="cursor: pointer;">
              <div class="row-item-left">
                <div class="row-item-icon-shell red"><i class="bi bi-cloud-arrow-up"></i></div>
                <div class="row-item-info">
                  <span class="row-item-title">Kirim / Edit Materi</span>
                  <span class="row-item-sub">Unggah modul & presentasi Anda</span>
                </div>
              </div>
              <i class="bi bi-chevron-right" style="color: #98A2B3; font-size: 14px;"></i>
            </div>

            <!-- Uploaded Modules -->
            @if(($myModules ?? collect())->isNotEmpty())
              <div class="file-items-list" style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($myModules as $mod)
                  <div class="file-row">
                    <div class="file-row-left">
                      <i class="bi bi-file-earmark-text"></i>
                      <span class="file-row-name" title="{{ $mod->original_name }}">{{ $mod->original_name }}</span>
                    </div>
                    @php
                      $capsuleClass = match($mod->status) {
                        'approved' => 'approved',
                        'rejected' => 'rejected',
                        default => 'pending',
                      };
                      $capsuleLabel = match($mod->status) {
                        'approved' => 'Approved',
                        'rejected' => 'Ditolak',
                        default => 'Pending',
                      };
                    @endphp
                    <span class="status-capsule {{ $capsuleClass }}">{{ $capsuleLabel }}</span>
                  </div>
                @endforeach
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener("DOMContentLoaded", function () {
        // Tab switching logic
        const tabButtons = document.querySelectorAll(".tab-pill");
        const tabContents = document.querySelectorAll(".tab-content");

        tabButtons.forEach((button) => {
            button.addEventListener("click", () => {
                tabButtons.forEach((btn) => btn.classList.remove("active"));
                tabContents.forEach((content) => content.classList.remove("active"));

                button.classList.add("active");
                const targetId = button.getAttribute("data-target");
                const target = document.getElementById(targetId);
                if (target) target.classList.add("active");
            });
        });

        // Participant filtering logic
        const filterSelect = document.getElementById("participant-filter-select");
        const rows = document.querySelectorAll(".participant-row");

        if (filterSelect) {
            filterSelect.addEventListener("change", () => {
                const filter = filterSelect.value;
                rows.forEach(row => {
                    const isPresent = row.getAttribute("data-attended") === "true";
                    if (filter === "all") {
                        row.style.display = "";
                    } else if (filter === "present") {
                        row.style.display = isPresent ? "" : "none";
                    } else if (filter === "absent") {
                        row.style.display = !isPresent ? "" : "none";
                    }
                });
            });
        }
    });

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
