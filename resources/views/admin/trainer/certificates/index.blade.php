@extends('layouts.admin-trainer')

@section('title', 'Sertifikat & Penghargaan')

@php
    use App\Models\Event;
    use App\Models\Course;
    use App\Models\TrainerCertificateAsset;

    $tab = $tab ?? request('tab', 'unsentItems');

    $unsentItems = $unsentItems ?? collect();
    $sentItems = $sentItems ?? collect();
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

    $allPrograms = $unsentItems->concat($sentItems);

    $totalProgram = $allPrograms->count();

    $totalPublished = method_exists($trainers, 'sum')
        ? $trainers->sum('published_certificates_count')
        : 0;

    $readyCount = $allPrograms->filter(fn($item) => certStatus($item) === 'ready')->count();

    $notConfiguredCount = $allPrograms->filter(fn($item) => certStatus($item) === 'not-configured')->count();
@endphp

@push('admin-trainer-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --cert-primary: #4f46e5;
            --cert-primary-2: #6366f1;
            --cert-soft: #f5f3ff;
            --cert-border: #f1f5f9;
            --cert-muted: #64748b;
            --cert-warning: #f59e0b;
            --cert-danger: #ef4444;
            --cert-success: #10b981;
            --cert-ready-bg: rgba(16, 185, 129, 0.1);
            --cert-ready-color: #065f46;
            --cert-configured-bg: rgba(59, 130, 246, 0.1);
            --cert-configured-color: #1e40af;
            --cert-missing-bg: rgba(239, 68, 68, 0.1);
            --cert-missing-color: #9f1239;
            --btn-start: #4f46e5;
            --btn-end: #6366f1;
            --btn-soft-bg: #eef2ff;
        }

        .cert-dashboard {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            width: 100%;
        }

        .cert-hero {
            background: linear-gradient(135deg, #312e81 0%, #4f46e5 55%, #818cf8 100%);
            border-radius: 24px;
            padding: 42px 48px;
            color: #fff;
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
            min-height: 200px;
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.15);
        }

        .cert-hero::after {
            content: '';
            position: absolute;
            right: 78px;
            top: 28px;
            width: 255px;
            height: 145px;
            background: rgba(255, 255, 255, .1);
            border-radius: 28px;
            backdrop-filter: blur(4px);
        }

        .cert-hero::before {
            content: '✦';
            position: absolute;
            right: 112px;
            top: 82px;
            color: rgba(255, 255, 255, .6);
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
            font-size: 2.50rem;
            font-weight: 800;
            margin: 0 0 12px;
            letter-spacing: -1px;
            line-height: 1.2;
        }

        .cert-hero p {
            color: rgba(255, 255, 255, .9);
            font-size: 1.05rem;
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
            box-shadow: 0 10px 28px rgba(15, 23, 42, .03);
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 30px rgba(79, 70, 229, 0.08);
        }

        .metric-icon {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            flex-shrink: 0;
        }

        .metric-icon.blue {
            background: #eff6ff;
            color: #2563eb;
        }

        .metric-icon.purple {
            background: #faf5ff;
            color: #7c3aed;
        }

        .metric-icon.green {
            background: #ecfdf5;
            color: #059669;
        }

        .metric-icon.yellow {
            background: #fefbeb;
            color: #d97706;
        }

        .metric-icon.orange {
            background: #fff7ed;
            color: #ea580c;
        }

        .metric-icon.emerald {
            background: #f0fdf4;
            color: #16a34a;
        }

        .metric-label {
            font-size: .84rem;
            color: var(--cert-muted);
            margin-bottom: 4px;
            font-weight: 600;
        }

        .metric-value {
            font-size: 1.85rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .metric-desc {
            margin-top: 4px;
            color: #94a3b8;
            font-size: .80rem;
        }

        .status-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 8px 0 24px;
        }

        .status-legend-item {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid var(--cert-border);
            background: #fff;
            border-radius: 999px;
            padding: 8px 16px;
            color: #475569;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-dot.ready {
            background: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .status-dot.configured {
            background: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .status-dot.missing {
            background: #ef4444;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2);
        }

        .cert-tab-wrapper {
            display: flex;
            gap: 8px;
            margin-top: 32px;
        }

        .cert-tab-btn {
            border: 1px solid var(--cert-border);
            border-bottom: 0;
            background: #fff;
            color: #64748b;
            padding: 16px 32px;
            border-radius: 16px 16px 0 0;
            font-weight: 700;
            min-width: 220px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .cert-tab-btn.active {
            color: var(--cert-primary);
            border-top: 3px solid var(--cert-primary);
            background: #fff;
            font-weight: 800;
        }

        .cert-tab-btn:hover:not(.active) {
            background: #fafafa;
            color: #1e293b;
        }

        .tab-count {
            background: #f1f5f9;
            color: #475569;
            font-size: .75rem;
            border-radius: 999px;
            padding: 3px 9px;
            font-weight: 700;
            transition: all 0.2s ease;
        }

        .cert-tab-btn.active .tab-count {
            background: var(--cert-soft);
            color: var(--cert-primary);
        }

        .filter-card {
            background: #fff;
            border: 1px solid var(--cert-border);
            border-radius: 0 24px 24px 24px;
            padding: 24px;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .03);
            margin-bottom: 28px;
        }

        .filter-input {
            width: 100%;
            height: 48px;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            background: #fff;
            padding: 0 16px;
            font-size: .92rem;
            outline: none;
            transition: all 0.2s ease;
            color: #1e293b;
            font-weight: 500;
        }

        .filter-input:focus {
            border-color: var(--cert-primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, .08);
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
            border: 1px solid #e2e8f0;
            background: #fff;
            font-weight: 700;
            color: #475569;
            width: 100%;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-reset-filter:hover {
            background: #f8fafc;
            color: #1e293b;
            border-color: #cbd5e1;
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
            gap: 24px;
        }

        /* ── Certificate Card ── */
        .certificate-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease, border-color 0.3s ease;
        }

        .certificate-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 35px rgba(79, 70, 229, 0.12);
            border-color: #cbd5e1;
        }

        /* Card Header Banner */
        .card-header-banner {
            position: relative;
            padding: 20px;
            height: 90px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            overflow: hidden;
        }

        .card-header-banner.course {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
        }

        .card-header-banner.event {
            background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        }

        /* Mini certificate Mockup inside Header */
        .mini-mockup {
            position: absolute;
            right: -8px;
            bottom: -12px;
            width: 82px;
            height: 58px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            transform: rotate(-12deg);
            transition: transform 0.3s ease;
            padding: 5px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .certificate-card:hover .mini-mockup {
            transform: rotate(-5deg) scale(1.08) translateY(-3px);
        }

        .mockup-frame {
            border: 1px dashed rgba(255, 255, 255, 0.4);
            height: 100%;
            border-radius: 3px;
            padding: 3px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
        }

        .mockup-seal {
            position: absolute;
            right: 3px;
            bottom: 3px;
            width: 8px;
            height: 8px;
            background: radial-gradient(circle, #fbbf24 60%, #d97706 100%);
            border-radius: 50%;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .mockup-lines {
            display: flex;
            flex-direction: column;
            gap: 2px;
            width: 65%;
        }

        .mockup-line-long {
            height: 2px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 1px;
        }

        .mockup-line-short {
            height: 2px;
            background: rgba(255, 255, 255, 0.35);
            border-radius: 1px;
            width: 60%;
        }

        /* Type badge inside banner */
        .type-badge {
            font-size: .65rem;
            font-weight: 800;
            border-radius: 6px;
            padding: 4px 10px;
            text-transform: uppercase;
            letter-spacing: .8px;
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(4px);
        }

        /* Status badge */
        .status-badge {
            font-size: .7rem;
            font-weight: 700;
            border-radius: 999px;
            padding: 4px 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            backdrop-filter: blur(4px);
        }

        .status-badge.ready {
            background: rgba(16, 185, 129, 0.3);
            border-color: rgba(16, 185, 129, 0.4);
        }

        .status-badge.configured {
            background: rgba(59, 130, 246, 0.3);
            border-color: rgba(59, 130, 246, 0.4);
        }

        .status-badge.missing {
            background: rgba(239, 68, 68, 0.3);
            border-color: rgba(239, 68, 68, 0.4);
        }

        /* Pulse Indicator */
        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
            position: relative;
        }

        .pulse-dot.ready {
            background-color: #10b981;
        }

        .pulse-dot.ready::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #10b981;
            animation: pulse-ring 1.8s infinite ease-in-out;
        }

        .pulse-dot.configured {
            background-color: #3b82f6;
        }

        .pulse-dot.configured::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #3b82f6;
            animation: pulse-ring 1.8s infinite ease-in-out;
        }

        .pulse-dot.missing {
            background-color: #ef4444;
        }

        .pulse-dot.missing::after {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: #ef4444;
            animation: pulse-ring 1.8s infinite ease-in-out;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(1);
                opacity: 0.85;
            }
            100% {
                transform: scale(2.8);
                opacity: 0;
            }
        }

        /* Card inner padding wrapper */
        .card-inner {
            padding: 22px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            flex: 1;
        }

        /* Program info block */
        .program-main {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .program-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            flex-shrink: 0;
            transition: transform 0.25s ease;
        }

        .program-icon-wrapper.course {
            background: #f5f3ff;
            color: #4f46e5;
            border: 1px solid #e0e7ff;
        }

        .program-icon-wrapper.event {
            background: #f0f9ff;
            color: #0284c7;
            border: 1px solid #e0f2fe;
        }

        .certificate-card:hover .program-icon-wrapper {
            transform: scale(1.08);
        }

        .program-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
            min-width: 0;
        }

        .program-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .program-date {
            font-size: .78rem;
            color: var(--cert-muted);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Slate layout for meta details and asset checklists */
        .meta-and-assets {
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 14px;
            padding: 14px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .meta-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .meta-label {
            font-size: 0.78rem;
            color: var(--cert-muted);
            font-weight: 600;
        }

        .meta-val {
            font-size: 0.9rem;
            font-weight: 700;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Asset section */
        .asset-checklist {
            border-top: 1px dashed #e2e8f0;
            padding-top: 12px;
        }

        .checklist-title {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            margin-bottom: 8px;
        }

        .checklist-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .check-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 8px 4px;
            border-radius: 8px;
            background: #fff;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .check-item.is-valid {
            border-color: #d1fae5;
            background: #f0fdf4;
        }

        .check-item.is-invalid {
            border-color: #ffe4e6;
            background: #fff1f2;
        }

        .check-icon {
            font-size: 1rem;
            line-height: 1;
        }

        .check-item.is-valid .check-icon {
            color: #10b981;
        }

        .check-item.is-invalid .check-icon {
            color: #f43f5e;
        }

        .check-label {
            font-size: 0.68rem;
            font-weight: 600;
            color: #64748b;
            text-align: center;
        }

        .check-item.is-valid .check-label {
            color: #065f46;
        }

        .check-item.is-invalid .check-label {
            color: #9f1239;
        }

        /* Manage button — full width */
        .btn-manage-template {
            margin: 0 22px 22px;
            height: 44px;
            border-radius: 12px;
            border: none;
            font-weight: 700;
            font-size: .86rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 0 18px;
            text-decoration: none;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            flex-shrink: 0;
            cursor: pointer;
        }

        .btn-manage-template:hover {
            transform: translateY(-1px);
        }

        .card-actions-row {
            display: flex;
            gap: 10px;
            padding: 0 22px 22px;
            margin: 0;
        }

        .card-actions-row .btn-manage-template {
            margin: 0;
            flex: 1;
            height: 44px;
        }

        .btn-manage-template.btn-outline {
            border: 1px solid #cbd5e1;
            background: transparent;
            color: #475569;
        }

        .btn-manage-template.btn-outline:hover {
            background: #f8fafc;
            color: #1e293b;
            border-color: #94a3b8;
        }

        /* Button styles based on config status */
        .btn-manage-template:not(.soft):not(.warning) {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #fff;
            box-shadow: 0 4px 12px rgba(79, 70, 229, 0.2);
        }

        .btn-manage-template:not(.soft):not(.warning):hover {
            color: #fff;
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.35);
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
        }

        .btn-manage-template.soft {
            background: #eef2ff;
            color: #4f46e5;
            border: 1px solid #e0e7ff;
        }

        .btn-manage-template.soft:hover {
            background: #e0e7ff;
            color: #3730a3;
        }

        .btn-manage-template.warning {
            background: #fff1f2;
            color: #e11d48;
            border: 1px solid #ffe4e6;
        }

        .btn-manage-template.warning:hover {
            background: #ffe4e6;
            color: #be123c;
        }

        /* Empty state */
        .empty-card {
            background: #fff;
            border: 2px dashed #e2e8f0;
            border-radius: 20px;
            padding: 64px 24px;
            text-align: center;
            grid-column: 1 / -1;
            color: #64748b;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }

        .empty-card i {
            font-size: 2.8rem;
            color: #cbd5e1;
        }

        /* ── Responsive ── */
        @media (max-width: 1400px) {
            .certificate-grid {
                grid-template-columns: repeat(2, minmax(300px, 1fr));
                gap: 20px;
            }
        }

        @media (max-width: 768px) {
            .cert-hero {
                padding: 32px;
            }

            .cert-hero h1 {
                font-size: 2.0rem;
            }

            .certificate-grid {
                grid-template-columns: 1fr;
            }

            .cert-tab-wrapper {
                flex-direction: column;
                gap: 0;
            }

            .cert-tab-btn {
                width: 100%;
                border-radius: 16px;
                border: 1px solid var(--cert-border);
                margin-bottom: 8px;
            }

            .filter-card {
                border-radius: 24px;
            }
        }

        /* Trainer Status List styling */
        .trainer-status-list {
            margin-top: 14px;
            border-top: 1px solid #f1f5f9;
            padding-top: 14px;
        }
        .trainer-status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            background: #f8fafc;
            border: 1px solid #f1f5f9;
            border-radius: 12px;
            transition: all 0.2s ease;
            margin-bottom: 6px;
        }
        .trainer-status-row:last-child {
            margin-bottom: 0;
        }
        .trainer-status-row:hover {
            background: #f1f5f9;
            border-color: #cbd5e1;
        }
        .btn-action-small {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-action-small.view {
            background: #eef2ff;
            color: #4f46e5;
            border: 1px solid #e0e7ff;
        }
        .btn-action-small.view:hover {
            background: #e0e7ff;
            color: #3730a3;
            transform: scale(1.05);
        }
        .btn-action-small.manage-icon {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }
        .btn-action-small.manage-icon:hover {
            background: #e2e8f0;
            color: #1e293b;
            transform: scale(1.05);
        }
        .btn-action-manage-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 8px;
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: white;
            border: none;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 4px 10px rgba(79, 70, 229, 0.15);
        }
        .btn-action-manage-pill:hover {
            box-shadow: 0 6px 14px rgba(79, 70, 229, 0.25);
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
            color: white;
            transform: translateY(-1px);
        }
    </style>
@endpush

@section('admin-trainer-content')
    <div class="cert-dashboard">

        <section class="cert-hero">
            <div class="cert-hero-content">
                <div class="page-eyebrow">Sistem Rekognisi</div>
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
            <button type="button" class="cert-tab-btn custom-tab-btn {{ $tab === 'unsentItems' ? 'active' : '' }}"
                data-target="unsentItems-pane">
                <i class="bi bi-clock"></i>
                Belum Terkirim
                <span class="tab-count">{{ $unsentItems->count() }}</span>
            </button>

            <button type="button" class="cert-tab-btn custom-tab-btn {{ $tab === 'sentItems' ? 'active' : '' }}"
                data-target="sentItems-pane">
                <i class="bi bi-send-check"></i>
                Sudah Terkirim
                <span class="tab-count">{{ $sentItems->count() }}</span>
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
               <div class="tab-content-container">
            <div class="custom-tab-pane {{ $tab === 'unsentItems' ? 'active' : '' }}" id="unsentItems-pane">
                <div class="certificate-grid">
                    @forelse($unsentItems as $item)
                        @php
                            $assets = certAssets($item);
                            $status = certStatus($item);

                            $hasTemplate = $assets->where('type', 'template')->isNotEmpty();
                            $hasLogo = $assets->where('type', 'logo')->isNotEmpty();
                            $hasSignature = $assets->where('type', 'signature')->isNotEmpty();

                            $isEvent = $item instanceof \App\Models\Event;
                            $title = $isEvent ? ($item->title ?? '-') : ($item->name ?? '-');
                            $context = $isEvent ? 'event' : 'course';
                            $typeBadge = $isEvent ? 'ACARA' : 'KURSUS';
                            $iconClass = $isEvent ? 'bi-calendar-event' : 'bi-mortarboard';
                            
                            $metaLabel = $isEvent ? 'Peserta Terdaftar' : 'Siswa Terdaftar';
                            $metaCount = $isEvent ? ($item->registrations_count ?? 0) : ($item->enrollments_count ?? 0);

                            // Extract all trainers associated with this program
                            $itemTrainers = collect();
                            if ($item->trainer) {
                                $itemTrainers->push($item->trainer);
                            }
                            if ($isEvent && isset($item->speakers)) {
                                foreach ($item->speakers as $speaker) {
                                    if ($speaker->trainer) {
                                        $itemTrainers->push($speaker->trainer);
                                    }
                                }
                            }
                            $itemTrainers = $itemTrainers->unique('id');
                        @endphp

                        <div class="certificate-card cert-row" data-title="{{ strtolower($title) }}"
                            data-status="{{ $status }}">

                            {{-- Card Header Banner with gradient --}}
                            <div class="card-header-banner {{ $context }}">
                                {{-- Mini certificate Mockup --}}
                                <div class="mini-mockup">
                                    <div class="mockup-frame">
                                        <div class="mockup-seal"></div>
                                        <div class="mockup-lines">
                                            <div class="mockup-line-long"></div>
                                            <div class="mockup-line-short"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Header Content --}}
                                <span class="type-badge">{{ $typeBadge }}</span>
                                
                                @if($status === 'ready')
                                    <span class="status-badge ready">
                                        <span class="pulse-dot ready"></span> Siap Dikirim
                                    </span>
                                @elseif($status === 'configured')
                                    <span class="status-badge configured">
                                        <span class="pulse-dot configured"></span> Perlu Dilengkapi
                                    </span>
                                @else
                                    <span class="status-badge missing">
                                        <span class="pulse-dot missing"></span> Belum Siap
                                    </span>
                                @endif
                            </div>

                            <div class="card-inner" style="padding-bottom: 22px;">
                                {{-- Program info --}}
                                <div class="program-main">
                                    <div class="program-icon-wrapper {{ $context }}">
                                        <i class="bi {{ $iconClass }}"></i>
                                    </div>
                                    <div class="program-info">
                                        <h5 class="program-title" title="{{ $title }}">{{ $title }}</h5>
                                        <div class="program-date">
                                            @if($isEvent)
                                                @php
                                                    $eventDate = $item->event_date ? \Carbon\Carbon::parse($item->event_date) : null;
                                                @endphp
                                                <i class="bi bi-calendar3"></i>
                                                <span>{{ $eventDate ? $eventDate->translatedFormat('d M Y') : 'Tanpa Tanggal' }}</span>
                                            @else
                                                <i class="bi bi-folder2-open"></i>
                                                <span>{{ $item->category->name ?? 'Umum' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Slate Box: Meta & Assets --}}
                                <div class="meta-and-assets">
                                    <div class="meta-item">
                                        <span class="meta-label">{{ $metaLabel }}</span>
                                        <span class="meta-val">
                                            <i class="bi bi-people"></i>
                                            {{ $metaCount }}
                                        </span>
                                    </div>

                                    {{-- Asset Checklist --}}
                                    <div class="asset-checklist">
                                        <div class="checklist-title">Kelengkapan Aset Desain</div>
                                        <div class="checklist-grid">
                                            <div class="check-item {{ $hasTemplate ? 'is-valid' : 'is-invalid' }}">
                                                <div class="check-icon">
                                                    <i class="bi {{ $hasTemplate ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                </div>
                                                <span class="check-label">Template</span>
                                            </div>
                                            <div class="check-item {{ $hasLogo ? 'is-valid' : 'is-invalid' }}">
                                                <div class="check-icon">
                                                    <i class="bi {{ $hasLogo ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                </div>
                                                <span class="check-label">Logo</span>
                                            </div>
                                            <div class="check-item {{ $hasSignature ? 'is-valid' : 'is-invalid' }}">
                                                <div class="check-icon">
                                                    <i class="bi {{ $hasSignature ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                </div>
                                                <span class="check-label">Ttd Partner</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Trainer & Certificate Status List --}}
                                <div class="trainer-status-list">
                                    <div class="checklist-title">Daftar Trainer & Sertifikat</div>
                                    <div style="display: flex; flex-direction: column; gap: 8px;">
                                        @foreach($itemTrainers as $trn)
                                            @php
                                                $certKey = get_class($item) . ':' . $item->id . ':' . $trn->id;
                                                $trnCert = $allCertificates->has($certKey) ? $allCertificates->get($certKey)->first() : null;
                                                $trnStatus = $trnCert ? $trnCert->status : 'draft';
                                                
                                                // Get initials for avatar
                                                $words = explode(' ', $trn->name);
                                                $initials = '';
                                                if (count($words) >= 2) {
                                                    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                                } else {
                                                    $initials = strtoupper(substr($trn->name, 0, 2));
                                                }
                                                
                                                $bgColors = ['#4f46e5', '#7c3aed', '#2563eb', '#06b6d4', '#ec4899'];
                                                $colorIndex = abs(crc32($trn->name)) % count($bgColors);
                                                $bgColor = $bgColors[$colorIndex];
                                            @endphp
                                            <div class="trainer-status-row">
                                                <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: {{ $bgColor }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; flex-shrink: 0; border: 1px solid rgba(255,255,255,0.1);">
                                                        {{ $initials }}
                                                    </div>
                                                    <div style="min-width: 0; flex: 1;">
                                                        <span style="font-size: 0.82rem; font-weight: 700; color: #1e293b; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $trn->name }}">{{ $trn->name }}</span>
                                                        <span style="font-size: 0.72rem; display: block;">
                                                            @if($trnStatus === 'published' || $trnStatus === 'sent')
                                                                <span style="color: #10b981; font-weight: 600;"><i class="bi bi-patch-check-fill me-1"></i>Terkirim</span>
                                                            @else
                                                                <span style="color: #f59e0b; font-weight: 600;"><i class="bi bi-clock-history me-1"></i>Belum Terkirim</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div style="flex-shrink: 0; margin-left: 8px;">
                                                    @if($trnStatus === 'published' || $trnStatus === 'sent')
                                                        <div style="display: flex; gap: 4px;">
                                                            <a href="{{ route('admin.trainer.certificates.detail', ['certificate' => $trnCert->id]) }}" 
                                                               class="btn-action-small view"
                                                               title="Lihat Sertifikat">
                                                                <i class="bi bi-eye-fill"></i>
                                                            </a>
                                                            <a href="{{ route('admin.trainer.certificates.edit', ['trainer' => $trn->id, 'context' => $context, 'id' => $item->id]) }}" 
                                                               class="btn-action-small manage-icon"
                                                               title="Kelola & Kirim Ulang">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </a>
                                                        </div>
                                                    @else
                                                        <a href="{{ route('admin.trainer.certificates.edit', ['trainer' => $trn->id, 'context' => $context, 'id' => $item->id]) }}" 
                                                           class="btn-action-manage-pill"
                                                           title="Kelola & Kirim">
                                                            <i class="bi bi-gear-fill"></i>
                                                            <span>Kelola</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-card">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            Data program belum terkirim tidak ditemukan.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="custom-tab-pane {{ $tab === 'sentItems' ? 'active' : '' }}" id="sentItems-pane">
                <div class="certificate-grid">
                    @forelse($sentItems as $item)
                        @php
                            $assets = certAssets($item);
                            $status = certStatus($item);

                            $hasTemplate = $assets->where('type', 'template')->isNotEmpty();
                            $hasLogo = $assets->where('type', 'logo')->isNotEmpty();
                            $hasSignature = $assets->where('type', 'signature')->isNotEmpty();

                            $isEvent = $item instanceof \App\Models\Event;
                            $title = $isEvent ? ($item->title ?? '-') : ($item->name ?? '-');
                            $context = $isEvent ? 'event' : 'course';
                            $typeBadge = $isEvent ? 'ACARA' : 'KURSUS';
                            $iconClass = $isEvent ? 'bi-calendar-event' : 'bi-mortarboard';
                            
                            $metaLabel = $isEvent ? 'Peserta Terdaftar' : 'Siswa Terdaftar';
                            $metaCount = $isEvent ? ($item->registrations_count ?? 0) : ($item->enrollments_count ?? 0);

                            // Extract all trainers associated with this program
                            $itemTrainers = collect();
                            if ($item->trainer) {
                                $itemTrainers->push($item->trainer);
                            }
                            if ($isEvent && isset($item->speakers)) {
                                foreach ($item->speakers as $speaker) {
                                    if ($speaker->trainer) {
                                        $itemTrainers->push($speaker->trainer);
                                    }
                                }
                            }
                            $itemTrainers = $itemTrainers->unique('id');
                        @endphp

                        <div class="certificate-card cert-row" data-title="{{ strtolower($title) }}"
                            data-status="{{ $status }}">

                            {{-- Card Header Banner with gradient --}}
                            <div class="card-header-banner {{ $context }}">
                                {{-- Mini certificate Mockup --}}
                                <div class="mini-mockup">
                                    <div class="mockup-frame">
                                        <div class="mockup-seal"></div>
                                        <div class="mockup-lines">
                                            <div class="mockup-line-long"></div>
                                            <div class="mockup-line-short"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Header Content --}}
                                <span class="type-badge">{{ $typeBadge }}</span>
                                
                                <span class="status-badge ready">
                                    <span class="pulse-dot ready"></span> Terkirim
                                </span>
                            </div>

                            <div class="card-inner" style="padding-bottom: 22px;">
                                {{-- Program info --}}
                                <div class="program-main">
                                    <div class="program-icon-wrapper {{ $context }}">
                                        <i class="bi {{ $iconClass }}"></i>
                                    </div>
                                    <div class="program-info">
                                        <h5 class="program-title" title="{{ $title }}">{{ $title }}</h5>
                                        <div class="program-date">
                                            @if($isEvent)
                                                @php
                                                    $eventDate = $item->event_date ? \Carbon\Carbon::parse($item->event_date) : null;
                                                @endphp
                                                <i class="bi bi-calendar3"></i>
                                                <span>{{ $eventDate ? $eventDate->translatedFormat('d M Y') : 'Tanpa Tanggal' }}</span>
                                            @else
                                                <i class="bi bi-folder2-open"></i>
                                                <span>{{ $item->category->name ?? 'Umum' }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Slate Box: Meta & Assets --}}
                                <div class="meta-and-assets">
                                    <div class="meta-item">
                                        <span class="meta-label">{{ $metaLabel }}</span>
                                        <span class="meta-val">
                                            <i class="bi bi-people"></i>
                                            {{ $metaCount }}
                                        </span>
                                    </div>

                                    {{-- Asset Checklist --}}
                                    <div class="asset-checklist">
                                        <div class="checklist-title">Kelengkapan Aset Desain</div>
                                        <div class="checklist-grid">
                                            <div class="check-item {{ $hasTemplate ? 'is-valid' : 'is-invalid' }}">
                                                <div class="check-icon">
                                                    <i class="bi {{ $hasTemplate ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                </div>
                                                <span class="check-label">Template</span>
                                            </div>
                                            <div class="check-item {{ $hasLogo ? 'is-valid' : 'is-invalid' }}">
                                                <div class="check-icon">
                                                    <i class="bi {{ $hasLogo ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                </div>
                                                <span class="check-label">Logo</span>
                                            </div>
                                            <div class="check-item {{ $hasSignature ? 'is-valid' : 'is-invalid' }}">
                                                <div class="check-icon">
                                                    <i class="bi {{ $hasSignature ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                                </div>
                                                <span class="check-label">Ttd Partner</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Trainer & Certificate Status List --}}
                                <div class="trainer-status-list">
                                    <div class="checklist-title">Daftar Trainer & Sertifikat</div>
                                    <div style="display: flex; flex-direction: column; gap: 8px;">
                                        @foreach($itemTrainers as $trn)
                                            @php
                                                $certKey = get_class($item) . ':' . $item->id . ':' . $trn->id;
                                                $trnCert = $allCertificates->has($certKey) ? $allCertificates->get($certKey)->first() : null;
                                                $trnStatus = $trnCert ? $trnCert->status : 'draft';
                                                
                                                // Get initials for avatar
                                                $words = explode(' ', $trn->name);
                                                $initials = '';
                                                if (count($words) >= 2) {
                                                    $initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
                                                } else {
                                                    $initials = strtoupper(substr($trn->name, 0, 2));
                                                }
                                                
                                                $bgColors = ['#4f46e5', '#7c3aed', '#2563eb', '#06b6d4', '#ec4899'];
                                                $colorIndex = abs(crc32($trn->name)) % count($bgColors);
                                                $bgColor = $bgColors[$colorIndex];
                                            @endphp
                                            <div class="trainer-status-row">
                                                <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                                                    <div style="width: 32px; height: 32px; border-radius: 50%; background-color: {{ $bgColor }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; flex-shrink: 0; border: 1px solid rgba(255,255,255,0.1);">
                                                        {{ $initials }}
                                                    </div>
                                                    <div style="min-width: 0; flex: 1;">
                                                        <span style="font-size: 0.82rem; font-weight: 700; color: #1e293b; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $trn->name }}">{{ $trn->name }}</span>
                                                        <span style="font-size: 0.72rem; display: block;">
                                                            @if($trnStatus === 'published' || $trnStatus === 'sent')
                                                                <span style="color: #10b981; font-weight: 600;"><i class="bi bi-patch-check-fill me-1"></i>Terkirim</span>
                                                            @else
                                                                <span style="color: #f59e0b; font-weight: 600;"><i class="bi bi-clock-history me-1"></i>Belum Terkirim</span>
                                                            @endif
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div style="flex-shrink: 0; margin-left: 8px;">
                                                    @if($trnStatus === 'published' || $trnStatus === 'sent')
                                                        <div style="display: flex; gap: 4px;">
                                                            <a href="{{ route('admin.trainer.certificates.detail', ['certificate' => $trnCert->id]) }}" 
                                                               class="btn-action-small view"
                                                               title="Lihat Sertifikat">
                                                                <i class="bi bi-eye-fill"></i>
                                                            </a>
                                                            <a href="{{ route('admin.trainer.certificates.edit', ['trainer' => $trn->id, 'context' => $context, 'id' => $item->id]) }}" 
                                                               class="btn-action-small manage-icon"
                                                               title="Kelola & Kirim Ulang">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </a>
                                                        </div>
                                                    @else
                                                        <a href="{{ route('admin.trainer.certificates.edit', ['trainer' => $trn->id, 'context' => $context, 'id' => $item->id]) }}" 
                                                           class="btn-action-manage-pill"
                                                           title="Kelola & Kirim">
                                                            <i class="bi bi-gear-fill"></i>
                                                            <span>Kelola</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-card">
                            <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                            Data program terkirim tidak ditemukan.
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