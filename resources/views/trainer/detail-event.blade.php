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
        margin: 24px 0;
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
            <span class="tag-purple"><i class="bi bi-plus-lg"></i> Daftar Event</span>
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

          <div class="hero-meta-row">
            <!-- Tanggal -->
            <div class="meta-card">
              <div class="meta-icon-wrapper"><i class="bi bi-calendar3"></i></div>
              <div class="meta-info">
                <span class="meta-label">TANGGAL</span>
                <span class="meta-value">{{ $startDate ? $startDate->format('d M Y') : 'TBA' }}</span>
              </div>
            </div>
            <!-- Waktu -->
            <div class="meta-card">
              <div class="meta-icon-wrapper"><i class="bi bi-clock"></i></div>
              <div class="meta-info">
                <span class="meta-label">WAKTU</span>
                <span class="meta-value">
                  @if($startTime)
                    {{ $startTime }} @if($endTime) - {{ $endTime }} @endif WIB
                  @else
                    TBA
                  @endif
                </span>
              </div>
            </div>
            <!-- Lokasi -->
            <div class="meta-card">
              <div class="meta-icon-wrapper"><i class="bi bi-laptop"></i></div>
              <div class="meta-info">
                <span class="meta-label">LOKASI / MODE</span>
                <span class="meta-value">{{ $event->zoom_link ? 'Online' : 'Offline' }}</span>
              </div>
            </div>
            <!-- Biaya / Fee -->
            <div class="meta-card">
              <div class="meta-icon-wrapper"><i class="bi bi-tags"></i></div>
              <div class="meta-info">
                <span class="meta-label">BIAYA</span>
                <span class="meta-value">
                  @if((float) ($eventCompensation['speaker_salary'] ?? 0) > 0)
                    Rp {{ number_format((float) $eventCompensation['speaker_salary'], 0, ',', '.') }}
                  @elseif((float) ($eventCompensation['fee_per_participant'] ?? 0) > 0)
                    Rp {{ number_format((float) $eventCompensation['fee_per_participant'], 0, ',', '.') }}/pax
                  @else
                    Rp 100.000
                  @endif
                </span>
              </div>
            </div>
            <!-- Target -->
            <div class="meta-card">
              <div class="meta-icon-wrapper"><i class="bi bi-people"></i></div>
              <div class="meta-info">
                <span class="meta-label">TARGET PESERTA</span>
                <span class="meta-value">{{ $targetAudience }}</span>
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
          <div class="event-stats-grid">
              <div class="stat-card-mini">
                  <span class="stat-icon-wrapper blue"><i class="bi bi-people-fill"></i></span>
                  <div class="stat-info-mini">
                      <span class="stat-label-mini">Total Terdaftar</span>
                      <strong class="stat-val-mini">{{ $totalRegistered }} Peserta</strong>
                  </div>
              </div>
              <div class="stat-card-mini">
                  <span class="stat-icon-wrapper green"><i class="bi bi-check-circle-fill"></i></span>
                  <div class="stat-info-mini">
                      <span class="stat-label-mini">Sudah Absen</span>
                      <strong class="stat-val-mini">{{ $attendedCount }} Peserta</strong>
                  </div>
              </div>
              <div class="stat-card-mini">
                  <span class="stat-icon-wrapper amber"><i class="bi bi-clock-history"></i></span>
                  <div class="stat-info-mini">
                      <span class="stat-label-mini">Belum Absen</span>
                      <strong class="stat-val-mini">{{ $notAttendedCount }} Peserta</strong>
                  </div>
              </div>
              <div class="stat-card-mini">
                  <span class="stat-icon-wrapper purple"><i class="bi bi-graph-up"></i></span>
                  <div class="stat-info-mini">
                      <span class="stat-label-mini">Tingkat Kehadiran</span>
                      <strong class="stat-val-mini">{{ $attendanceRate }}%</strong>
                  </div>
              </div>
          </div>

          <!-- Interactive Filters -->
          <div class="participant-filters">
              <button class="filter-pill active" type="button" data-filter="all">
                  <i class="bi bi-grid-fill"></i> Semua
              </button>
              <button class="filter-pill" type="button" data-filter="present">
                  <i class="bi bi-check-circle-fill" style="color: #10b981;"></i> Sudah Absen
              </button>
              <button class="filter-pill" type="button" data-filter="absent">
                  <i class="bi bi-dash-circle-fill" style="color: #f59e0b;"></i> Belum Absen
              </button>
          </div>

          <div class="enrollment-header">
              <h3>PESERTA TERDAFTAR</h3>
              <span class="total-badge">{{ $totalRegistered }} TOTAL</span>
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
        <!-- Quick Assets Card (Links & Maps) -->
        <div class="sidebar-card assets-card">
          <div class="sidebar-header">
            <i class="bi bi-folder-fill"></i>
            <span>ASET ACARA</span>
          </div>
          
          @if($hasZoomLink)
            <div class="sidebar-row-item">
              <div class="row-item-left">
                <div class="row-item-icon-shell purple"><i class="bi bi-camera-video"></i></div>
                <div class="row-item-info">
                  <span class="row-item-title">Rapat Virtual / Sesi Video Online</span>
                  <span class="row-item-sub">Akses ruang meeting untuk mengikuti acara</span>
                </div>
              </div>
              <a href="{{ $zoomLink }}" target="_blank" class="row-btn-solid" style="text-decoration: none;">
                Gabung Zoom <i class="bi bi-chevron-right" style="font-size: 10px;"></i>
              </a>
            </div>
          @endif

          @if(!empty($vbgUrl))
            <div class="sidebar-row-item">
              <div class="row-item-left">
                <div class="row-item-icon-shell purple"><i class="bi bi-image"></i></div>
                <div class="row-item-info">
                  <span class="row-item-title">Latar Virtual Event</span>
                  <span class="row-item-sub">Unduh background resmi acara</span>
                </div>
              </div>
              <a href="{{ $vbgUrl }}" class="row-btn-outline" download style="text-decoration: none;">
                Unduh <i class="bi bi-download"></i>
              </a>
            </div>
          @endif

          @if(!$hasZoomLink && empty($vbgUrl))
            <p class="muted-text text-center" style="font-size: 12px; margin: 0; color: #667085;">Akses link belum tersedia.</p>
          @endif
        </div>

        <!-- Materials Card -->
        <div class="sidebar-card materials-card">
          <div class="sidebar-header">
            <i class="bi bi-folder-fill"></i>
            <span>MATERI ACARA</span>
          </div>

          <div class="sidebar-row-item materials-action" onclick="window.location.href='{{ route('trainer.events.studio', $event->id) }}'">
            <div class="row-item-left">
              <div class="row-item-icon-shell red"><i class="bi bi-cloud-arrow-up"></i></div>
              <div class="row-item-info">
                <span class="row-item-title">Kirim / Edit Materi</span>
                <span class="row-item-sub">Unggah materi dan izin materi Anda</span>
              </div>
            </div>
            <i class="bi bi-chevron-right" style="color: #98A2B3; font-size: 14px;"></i>
          </div>

          @if(($myModules ?? collect())->isNotEmpty())
            <div class="materi-uploaded-banner" style="margin-top: 12px;">
              <i class="bi bi-check-circle-fill"></i>
              <span>Materi Diunggah</span>
            </div>

            <div class="file-items-list" style="margin-top: 12px;">
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
        const filterButtons = document.querySelectorAll(".filter-pill");
        const rows = document.querySelectorAll(".participant-row");

        filterButtons.forEach(btn => {
            btn.addEventListener("click", () => {
                filterButtons.forEach(b => b.classList.remove("active"));
                btn.classList.add("active");

                const filter = btn.getAttribute("data-filter");
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
        });
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
