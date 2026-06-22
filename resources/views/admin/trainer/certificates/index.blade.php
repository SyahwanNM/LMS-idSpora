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

    function certHasCrmTemplate($model): bool
    {
        return trim((string) ($model->certificate_template ?? '')) !== '';
    }

    function certHasCrmLogo($model): bool
    {
        $logos = $model->certificate_logo ?? null;

        if (is_array($logos)) {
            return collect($logos)->contains(fn($logo) => trim((string) $logo) !== '');
        }

        return trim((string) $logos) !== '';
    }

    function certHasCrmSignature($model): bool
    {
        $signatures = $model->certificate_signature ?? null;

        if (!is_array($signatures)) {
            return trim((string) $signatures) !== '';
        }

        return collect($signatures)->contains(function ($signature) {
            if (is_array($signature)) {
                return trim((string) ($signature['image'] ?? '')) !== '';
            }

            return trim((string) $signature) !== '';
        });
    }

    function certHasTemplate($model): bool
    {
        return certAssets($model)->where('type', 'template')->isNotEmpty()
            || certHasCrmTemplate($model);
    }

    function certHasLogo($model): bool
    {
        return certAssets($model)->where('type', 'logo')->isNotEmpty()
            || certHasCrmLogo($model);
    }

    function certHasSignature($model): bool
    {
        return certAssets($model)->where('type', 'signature')->isNotEmpty()
            || certHasCrmSignature($model);
    }

    function certStatus($model)
    {
        $hasTemplate = certHasTemplate($model);
        $hasLogo = certHasLogo($model);
        $hasSignature = certHasSignature($model);

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

    $siapDikirimCount = $unsentItems->filter(fn($item) => certStatus($item) === 'ready')->count();
    $menungguAssetCount = $unsentItems->filter(fn($item) => certStatus($item) === 'not-configured')->count();
    $dalamProsesCount = $unsentItems->filter(fn($item) => certStatus($item) === 'configured')->count();
    $sudahDikirimCount = $sentItems->count();

    $calcPercent = static function (int $count) use ($totalProgram): int {
        return $totalProgram > 0 ? (int) round(($count / $totalProgram) * 100) : 0;
    };

    $siapDikirimPercent = $calcPercent($siapDikirimCount);
    $menungguAssetPercent = $calcPercent($menungguAssetCount);
    $dalamProsesPercent = $calcPercent($dalamProsesCount);
    $sudahDikirimPercent = $calcPercent($sudahDikirimCount);

    $readyCount = $siapDikirimCount;
    $notConfiguredCount = $menungguAssetCount;
@endphp

@push('admin-trainer-styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --cert-primary: #1e1b4b;
            --cert-primary-2: #6366f1;
            --cert-soft: #f5f3ff;
            --cert-border: #f1f5f9;
            --cert-muted: #64748b;
            --cert-warning: #64748b;
            --cert-danger: #64748b;
            --cert-success: #10b981;
            --cert-ready-bg: rgba(16, 185, 129, 0.08);
            --cert-ready-color: #047857;
            --cert-configured-bg: #f1f5f9;
            --cert-configured-color: #475569;
            --cert-missing-bg: #f1f5f9;
            --cert-missing-color: #475569;
            --btn-start: #1e1b4b;
            --btn-end: #1e1b4b;
            --btn-soft-bg: #eef2ff;
        }

        .admin-trainer-main {
            background-color: #f8fafc !important;
        }

        .cert-dashboard {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            width: 100%;
            background: transparent;
        }

        .cert-hero {
            background: #1e1b4b;
            border-radius: 24px;
            padding: 42px 48px;
            color: #fff;
            margin-bottom: 32px;
            position: relative;
            overflow: hidden;
            min-height: 200px;
            box-shadow: 0 20px 40px rgba(79, 70, 229, 0.15);
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
            margin-top: 0;
            flex-wrap: nowrap;
            overflow-x: auto;
            white-space: nowrap;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .cert-tab-wrapper::-webkit-scrollbar {
            display: none;
        }

        .cert-tab-btn {
            border: 1px solid var(--cert-border);
            border-bottom: 0;
            background: #fff;
            color: #64748b;
            padding: 16px 32px;
            border-radius: 16px 16px 0 0;
            font-weight: 700;
            min-width: auto;
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
            display: flex !important;
            flex-wrap: nowrap !important;
            gap: 24px;
            overflow-x: auto;
            padding-bottom: 20px;
            scroll-behavior: smooth;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        .certificate-grid::-webkit-scrollbar {
            height: 8px;
        }
        .certificate-grid::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 99px;
        }
        .certificate-grid::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 99px;
            border: 2px solid #f1f5f9;
        }
        .certificate-grid::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
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
            flex: 0 0 350px;
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
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }

        /* Mini certificate Mockup inside Header */
        .mini-mockup {
            position: absolute;
            right: -8px;
            bottom: -12px;
            width: 82px;
            height: 58px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            transform: rotate(-12deg);
            transition: transform 0.3s ease;
            padding: 5px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .certificate-card:hover .mini-mockup {
            transform: rotate(-5deg) scale(1.08) translateY(-3px);
        }

        .mockup-frame {
            border: 1px dashed #cbd5e1;
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
            background: #fbbf24;
            border-radius: 50%;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .mockup-lines {
            display: flex;
            flex-direction: column;
            gap: 2px;
            width: 65%;
        }

        .mockup-line-long {
            height: 2px;
            background: #e2e8f0;
            border-radius: 1px;
        }

        .mockup-line-short {
            height: 2px;
            background: #f1f5f9;
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
            background: #e2e8f0;
            color: #475569;
            border: 1px solid #cbd5e1;
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
            border: 1px solid transparent;
        }

        .status-badge.ready {
            background: #ecfdf5;
            color: #047857;
            border-color: #a7f3d0;
        }

        .status-badge.configured {
            background: #eef2ff;
            color: #1e1b4b;
            border-color: #cbd5e1;
        }

        .status-badge.missing {
            background: #f1f5f9;
            color: #475569;
            border-color: #cbd5e1;
        }

        /* Pulse Indicator */
        .pulse-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            display: inline-block;
        }

        .pulse-dot.ready {
            background-color: #10b981;
        }

        .pulse-dot.configured {
            background-color: #1e1b4b;
        }

        .pulse-dot.missing {
            background-color: #64748b;
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
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
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
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .check-item.is-valid {
            border-color: #e2e8f0;
            background: #f8fafc;
        }

        .check-item.is-invalid {
            border-color: #e2e8f0;
            background: #f8fafc;
            opacity: 0.65;
        }

        .check-icon {
            font-size: 1rem;
            line-height: 1;
        }

        .check-item.is-valid .check-icon {
            color: #10b981;
        }

        .check-item.is-invalid .check-icon {
            color: #94a3b8;
        }

        .check-label {
            font-size: 0.68rem;
            font-weight: 600;
            color: #475569;
            text-align: center;
        }

        .check-item.is-valid .check-label {
            color: #475569;
        }

        .check-item.is-invalid .check-label {
            color: #94a3b8;
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
            background: #1e1b4b;
            color: #fff;
        }

        .btn-manage-template:not(.soft):not(.warning):hover {
            color: #fff;
            background: #4338ca;
        }

        .btn-manage-template.soft {
            background: #eef2ff;
            color: #1e1b4b;
            border: 1px solid #e0e7ff;
        }

        .btn-manage-template.soft:hover {
            background: #e0e7ff;
            color: #3730a3;
        }

        .btn-manage-template.warning {
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-manage-template.warning:hover {
            background: #cbd5e1;
            color: #1e293b;
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
            color: #1e1b4b;
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
            background: #1e1b4b;
            color: white;
            border: none;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .btn-action-manage-pill:hover {
            background: #4338ca;
            color: white;
            transform: translateY(-1px);
        }

        .tab-count-badge {
            background: #f1f5f9;
            color: #475569;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 2px 8px;
            border-radius: 20px;
            margin-left: 6px;
            transition: all 0.2s ease;
            display: inline-block;
        }
        .cert-tab-btn.active .tab-count-badge {
            background: var(--cert-soft);
            color: var(--cert-primary);
        }
    </style>
@endpush

@section('admin-trainer-content')
    <div class="cert-dashboard">

        <!-- Header Section -->
        <section class="cert-hero">
            <div class="cert-hero-content">
                <div class="page-eyebrow">Sistem Rekognisi</div>
                <h1 style="color: #fff;">Sertifikat & Penghargaan</h1>
                <p>
                    Pantau program yang masih dalam proses pengiriman sertifikat dan lihat yang sudah berhasil diselesaikan.
                </p>
            </div>

            <!-- Floating Graphics (Certificate mockups) -->
            <div style="position: absolute; right: 50px; top: 50%; transform: translateY(-50%); width: 280px; height: 200px; pointer-events: none; opacity: 0.98; display: block;">
                <!-- Back Card (Glassmorphic) -->
                <div style="position: absolute; right: 60px; top: 40px; width: 190px; height: 130px; background: rgba(255, 255, 255, 0.05); border: 1.5px solid rgba(255, 255, 255, 0.12); border-radius: 12px; transform: rotate(12deg); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);"></div>
                <!-- Front Card (Premium White/Gold) -->
                <div style="position: absolute; right: 20px; top: 15px; width: 210px; height: 145px; background: #ffffff; border: 1px solid rgba(255, 255, 255, 0.8); border-radius: 12px; transform: rotate(-8deg); box-shadow: 0 15px 35px rgba(15, 23, 42, 0.25); padding: 10px; display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="border: 1px solid rgba(197, 160, 89, 0.45); padding: 8px 10px; height: 100%; border-radius: 8px; display: flex; flex-direction: column; justify-content: space-between; background: #fafafa;">
                        <div style="font-size: 0.7rem; font-weight: 800; color: #1e1b4b; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 2px; text-align: center;">SERTIFIKAT</div>
                        <div style="height: 1px; background: rgba(197, 160, 89, 0.35); width: 80%; margin: 2px auto 6px;"></div>
                        
                        <!-- Mock text lines -->
                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                            <div style="height: 3px; background: #e2e8f0; width: 75%; border-radius: 1px;"></div>
                            <div style="height: 3px; background: #f1f5f9; width: 55%; border-radius: 1px;"></div>
                        </div>

                        <!-- Seal & Signature section -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 6px; padding: 0 2px;">
                            <!-- Signature line -->
                            <div style="display: flex; flex-direction: column; gap: 2px; width: 35%;">
                                <div style="height: 8px; border-bottom: 1px solid #cbd5e1;"></div>
                                <div style="font-size: 0.35rem; color: #94a3b8; font-weight: 700; text-align: center; text-transform: uppercase; letter-spacing: 0.2px;">Signature</div>
                            </div>
                            <!-- Gold Seal Badge -->
                            <div style="width: 24px; height: 24px; border-radius: 50%; background: #d4af37; display: flex; align-items: center; justify-content: center; box-shadow: 0 3px 8px rgba(212, 175, 55, 0.3); position: relative; flex-shrink: 0;">
                                <i class="bi bi-award-fill" style="font-size: 12px; color: white;"></i>
                                <!-- Ribbon tails -->
                                <div style="position: absolute; bottom: -5px; left: 5px; width: 4px; height: 7px; background: #b89025; transform: rotate(15deg); clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 50% 70%, 0% 100%);"></div>
                                <div style="position: absolute; bottom: -5px; right: 5px; width: 4px; height: 7px; background: #b89025; transform: rotate(-15deg); clip-path: polygon(0% 0%, 100% 0%, 100% 100%, 50% 70%, 0% 100%);"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4 Metrics Progress Cards Section -->
        <div class="row g-4 mb-4">
            <!-- Card 1: Siap Dikirim -->
            <div class="col-md-6 col-xl-3">
                <div class="metric-progress-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); transition: all 0.25s ease;">
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            <i class="bi bi-send-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.68rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Siap Dikirim</div>
                            <div style="display: flex; align-items: baseline; gap: 4px;">
                                <span style="font-size: 1.50rem; font-weight: 800; color: #0f172a;">{{ $siapDikirimCount }}</span>
                                <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">Program</span>
                            </div>
                        </div>
                    </div>
                    <!-- Progress bar -->
                    <div style="height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-bottom: 8px;">
                        <div style="height: 100%; width: {{ $siapDikirimPercent }}%; background: #10b981; border-radius: 10px;"></div>
                    </div>
                    <div style="font-size: 0.72rem; color: #64748b; font-weight: 500;">
                        <span style="font-weight: 700; color: #0f172a;">{{ $siapDikirimPercent }}%</span> dari total program
                    </div>
                </div>
            </div>

            <!-- Card 2: Menunggu Asset -->
            <div class="col-md-6 col-xl-3">
                <div class="metric-progress-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); transition: all 0.25s ease;">
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #fff7ed; color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            <i class="bi bi-folder-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.68rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Menunggu Asset</div>
                            <div style="display: flex; align-items: baseline; gap: 4px;">
                                <span style="font-size: 1.50rem; font-weight: 800; color: #0f172a;">{{ $menungguAssetCount }}</span>
                                <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">Program</span>
                            </div>
                        </div>
                    </div>
                    <!-- Progress bar -->
                    <div style="height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-bottom: 8px;">
                        <div style="height: 100%; width: {{ $menungguAssetPercent }}%; background: #f59e0b; border-radius: 10px;"></div>
                    </div>
                    <div style="font-size: 0.72rem; color: #64748b; font-weight: 500;">
                        <span style="font-weight: 700; color: #0f172a;">{{ $menungguAssetPercent }}%</span> dari total program
                    </div>
                </div>
            </div>

            <!-- Card 3: Dalam Proses -->
            <div class="col-md-6 col-xl-3">
                <div class="metric-progress-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); transition: all 0.25s ease;">
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.68rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Dalam Proses</div>
                            <div style="display: flex; align-items: baseline; gap: 4px;">
                                <span style="font-size: 1.50rem; font-weight: 800; color: #0f172a;">{{ $dalamProsesCount }}</span>
                                <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">Program</span>
                            </div>
                        </div>
                    </div>
                    <!-- Progress bar -->
                    <div style="height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-bottom: 8px;">
                        <div style="height: 100%; width: {{ $dalamProsesPercent }}%; background: #3b82f6; border-radius: 10px;"></div>
                    </div>
                    <div style="font-size: 0.72rem; color: #64748b; font-weight: 500;">
                        <span style="font-weight: 700; color: #0f172a;">{{ $dalamProsesPercent }}%</span> dari total program
                    </div>
                </div>
            </div>

            <!-- Card 4: Sudah Dikirim -->
            <div class="col-md-6 col-xl-3">
                <div class="metric-progress-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); transition: all 0.25s ease;">
                    <div style="display: flex; align-items: center; gap: 14px; margin-bottom: 14px;">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #faf5ff; color: #a855f7; display: flex; align-items: center; justify-content: center; font-size: 18px; flex-shrink: 0;">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.68rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">Sudah Dikirim</div>
                            <div style="display: flex; align-items: baseline; gap: 4px;">
                                <span style="font-size: 1.50rem; font-weight: 800; color: #0f172a;">{{ $sudahDikirimCount }}</span>
                                <span style="font-size: 0.78rem; color: #64748b; font-weight: 600;">Program</span>
                            </div>
                        </div>
                    </div>
                    <!-- Progress bar -->
                    <div style="height: 6px; background: #f1f5f9; border-radius: 10px; overflow: hidden; margin-bottom: 8px;">
                        <div style="height: 100%; width: {{ $sudahDikirimPercent }}%; background: #a855f7; border-radius: 10px;"></div>
                    </div>
                    <div style="font-size: 0.72rem; color: #64748b; font-weight: 500;">
                        <span style="font-weight: 700; color: #0f172a;">{{ $sudahDikirimPercent }}%</span> dari total program
                    </div>
                </div>
            </div>
        </div>

        <!-- Layout Split Columns (Main Section) -->
        <div class="row g-4">
            
            <!-- Left Column: Certificate Tabs, Filters & Grid -->
            <div class="col-xl-9 col-lg-8">
                
                <!-- Tab Buttons Row -->
                <div class="cert-tab-wrapper" style="display: flex; flex-wrap: nowrap; gap: 8px; margin-bottom: 0; overflow-x: auto; white-space: nowrap; -ms-overflow-style: none; scrollbar-width: none;">
                    <button type="button" class="cert-tab-btn filter-tab active" data-tab-value="all" style="border: 1px solid #cbd5e1; background: #fff; color: #475569; padding: 10px 14px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s; flex: 1;">
                        <i class="bi bi-grid-fill"></i> Semua
                    </button>
                    <button type="button" class="cert-tab-btn filter-tab" data-tab-value="ready" style="border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 14px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s; flex: 1;">
                        <i class="bi bi-send-fill"></i> Siap Dikirim
                        <span style="background: #d1fae5; color: #065f46; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 20px; margin-left: 2px;">{{ $siapDikirimCount }}</span>
                    </button>
                    <button type="button" class="cert-tab-btn filter-tab" data-tab-value="not-configured" style="border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 14px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s; flex: 1;">
                        <i class="bi bi-folder-fill"></i> Menunggu Asset
                        <span style="background: #ffedd5; color: #9a3412; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 20px; margin-left: 2px;">{{ $menungguAssetCount }}</span>
                    </button>
                    <button type="button" class="cert-tab-btn filter-tab" data-tab-value="configured" style="border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 14px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s; flex: 1;">
                        <i class="bi bi-check-circle-fill"></i> Dalam Proses
                        <span style="background: #dbeafe; color: #1e40af; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 20px; margin-left: 2px;">{{ $dalamProsesCount }}</span>
                    </button>
                    <button type="button" class="cert-tab-btn filter-tab" data-tab-value="sent" style="border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 14px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; justify-content: center; gap: 8px; cursor: pointer; transition: all 0.2s; flex: 1;">
                        <i class="bi bi-award-fill"></i> Sudah Dikirim
                        <span style="background: #f1f5f9; color: #475569; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 20px; margin-left: 2px;">{{ $sudahDikirimCount }}</span>
                    </button>
                </div>

                <!-- Filter Card Panel -->
                <div class="filter-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 16px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); margin-top: 16px; margin-bottom: 24px;">
                    <div class="row g-3 align-items-center">
                        <!-- Search bar -->
                        <div class="col-lg-7">
                            <div class="search-wrap" style="position: relative;">
                                <i class="bi bi-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 15px;"></i>
                                <input type="text" id="certSearch" class="filter-input" placeholder="Cari program..." style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 12px; padding: 0 16px 0 44px; outline: none; font-size: 0.9rem; font-weight: 500; transition: all 0.2s;">
                            </div>
                        </div>
                        <!-- Tipe Dropdown -->
                        <div class="col-lg-4">
                            <div style="position: relative;">
                                <select id="certType" class="filter-input" style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 12px; padding: 0 36px 0 16px; outline: none; font-size: 0.9rem; font-weight: 600; color: #475569; appearance: none; background: white;">
                                    <option value="all">Semua Tipe</option>
                                    <option value="event">Event</option>
                                    <option value="course">Course</option>
                                </select>
                                <i class="bi bi-chevron-down" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none;"></i>
                            </div>
                        </div>
                        <!-- Reset Button -->
                        <div class="col-lg-1" style="display: flex; justify-content: flex-end;">
                            <button type="button" id="certReset" class="btn-reset-filter" style="height: 42px; width: 42px; border-radius: 12px; border: 1px solid #cbd5e1; background: #fff; font-weight: 700; color: #475569; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; cursor: pointer;" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise" style="font-size: 16px;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section Header with Slide Navigation -->
                <div style="display: flex; align-items: center; justify-content: space-between; margin-top: 32px; margin-bottom: 16px;">
                    <h3 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin: 0; display: flex; align-items: center; gap: 8px;">
                        <i class="bi bi-folder2-open" style="color: #1e1b4b;"></i> Daftar Program & Sertifikat
                    </h3>
                    <div id="slideArrowsContainer" style="display: flex; gap: 8px;">
                        <button type="button" id="slidePrevBtn" class="btn-action-small view" style="width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" title="Slide Left">
                            <i class="bi bi-chevron-left" style="font-size: 16px;"></i>
                        </button>
                        <button type="button" id="slideNextBtn" class="btn-action-small view" style="width: 36px; height: 36px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: all 0.2s;" title="Slide Right">
                            <i class="bi bi-chevron-right" style="font-size: 16px;"></i>
                        </button>
                    </div>
                </div>

                <!-- Certificate Program Cards Grid -->
                <div class="certificate-grid" id="certificateGrid">
                    @php
                        $allPrograms = $unsentItems->concat($sentItems);
                    @endphp
                    @forelse($allPrograms as $item)
                        @php
                            $assets = certAssets($item);
                            $status = certStatus($item);

                            $hasTemplate = certHasTemplate($item);
                            $hasLogo = certHasLogo($item);
                            $hasSignature = certHasSignature($item);

                            $isEvent = $item instanceof \App\Models\Event;
                            $title = $isEvent ? ($item->title ?? '-') : ($item->name ?? '-');
                            $context = $isEvent ? 'event' : 'course';
                            $typeBadge = $isEvent ? 'Event' : 'Course';
                            
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
                            $trainerCount = $itemTrainers->count();

                            $isSent = $sentItems->contains($item);
                            $tabCategory = $isSent ? 'sent' : ($status === 'ready' ? 'ready' : ($status === 'configured' ? 'configured' : 'not-configured'));
                        @endphp

                        <div class="certificate-card cert-row" 
                             data-title="{{ strtolower($title) }}"
                             data-status="{{ $isSent ? 'sent' : $status }}"
                             data-tab-cat="{{ $tabCategory }}"
                             data-type="{{ $context }}">
                             
                             <!-- Top Banner Header -->
                             <div class="card-header-banner {{ $context }}">
                                 @if($isSent)
                                     <span class="status-badge ready">
                                         <span class="pulse-dot ready"></span> Sudah Dikirim
                                     </span>
                                 @elseif($status === 'ready')
                                     <span class="status-badge ready">
                                         <span class="pulse-dot ready"></span> Siap Dikirim
                                     </span>
                                 @elseif($status === 'configured')
                                     <span class="status-badge configured">
                                         <span class="pulse-dot configured"></span> Dalam Proses
                                     </span>
                                 @else
                                     <span class="status-badge missing">
                                         <span class="pulse-dot missing"></span> Menunggu Asset
                                     </span>
                                 @endif

                                 <span class="type-badge">{{ $typeBadge }}</span>

                                 <!-- Mini mockup decorative element -->
                                 <div class="mini-mockup">
                                     <div class="mockup-frame">
                                         <div class="mockup-lines">
                                             <div class="mockup-line-long"></div>
                                             <div class="mockup-line-short"></div>
                                             <div class="mockup-line-long"></div>
                                         </div>
                                         <div class="mockup-seal"></div>
                                     </div>
                                 </div>
                             </div>

                             <!-- Inner card body -->
                             <div class="card-inner">
                                 <!-- Title, Date and stats row -->
                                 <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                                     <div style="display: flex; gap: 12px; min-width: 0;">
                                         <!-- Left Calendar icon card -->
                                         <div class="program-icon-wrapper {{ $context }}">
                                             @if($isEvent)
                                                 <i class="bi bi-calendar3"></i>
                                             @else
                                                 <i class="bi bi-journal-bookmark-fill"></i>
                                             @endif
                                         </div>
                                         <div style="min-width: 0;">
                                             <h5 class="program-title" style="font-size: 0.95rem; font-weight: 800; color: #0f172a; margin: 0 0 2px 0; line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $title }}">{{ $title }}</h5>
                                             <div style="font-size: 0.72rem; color: #64748b; font-weight: 500;">
                                                 @if($isEvent)
                                                     @php
                                                         $eventDate = $item->event_date ? \Carbon\Carbon::parse($item->event_date) : null;
                                                     @endphp
                                                     <span>{{ $eventDate ? $eventDate->translatedFormat('d M Y') : 'Tanpa Tanggal' }}</span>
                                                 @else
                                                     <span>{{ $item->category->name ?? 'Umum' }}</span>
                                                 @endif
                                             </div>
                                         </div>
                                     </div>
                                     <!-- Right stats -->
                                     <div style="display: flex; gap: 10px; flex-shrink: 0; font-size: 0.72rem; color: #64748b; font-weight: 600; margin-top: 4px;">
                                         <span>Peserta <i class="bi bi-people-fill" style="margin-left: 2px; color: #94a3b8;"></i> <strong style="color: #0f172a;">{{ $metaCount }}</strong></span>
                                         <span>Trainer <i class="bi bi-person-badge-fill" style="margin-left: 2px; color: #94a3b8;"></i> <strong style="color: #0f172a;">{{ $trainerCount }}</strong></span>
                                     </div>
                                 </div>

                                 <!-- Kelengkapan Asset Desain checklist -->
                                 <div class="asset-checklist">
                                     <div class="checklist-title">Kelengkapan Asset Desain</div>
                                     <div class="checklist-grid">
                                         <div class="check-item {{ $hasTemplate ? 'is-valid' : 'is-invalid' }}">
                                             <i class="bi {{ $hasTemplate ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} check-icon"></i>
                                             <span class="check-label">Template</span>
                                         </div>
                                         <div class="check-item {{ $hasLogo ? 'is-valid' : 'is-invalid' }}">
                                             <i class="bi {{ $hasLogo ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} check-icon"></i>
                                             <span class="check-label">Logo</span>
                                         </div>
                                         <div class="check-item {{ $hasSignature ? 'is-valid' : 'is-invalid' }}">
                                             <i class="bi {{ $hasSignature ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} check-icon"></i>
                                             <span class="check-label">Ttd Partner</span>
                                         </div>
                                     </div>
                                 </div>

                                 <!-- Trainer & Certificate List -->
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
                                                 
                                                 $bgColors = ['#ec4899', '#3b82f6', '#06b6d4', '#8b5cf6', '#ea580c'];
                                                 $colorIndex = abs(crc32($trn->name)) % count($bgColors);
                                                 $bgColor = $bgColors[$colorIndex];
                                             @endphp
                                             <div class="trainer-status-row">
                                                 <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                                                     <!-- Avatar Image or Initials Circle -->
                                                     @if($trn->avatar_url)
                                                         <img src="{{ $trn->avatar_url }}" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1px solid rgba(0,0,0,0.05); flex-shrink: 0;" alt="Avatar">
                                                     @else
                                                         <div style="width: 28px; height: 28px; border-radius: 50%; background-color: {{ $bgColor }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 10px; flex-shrink: 0; border: 1px solid rgba(255,255,255,0.1);">
                                                             {{ $initials }}
                                                         </div>
                                                     @endif
                                                     <div style="min-width: 0; flex: 1;">
                                                         <span style="font-size: 0.8rem; font-weight: 700; color: #1e293b; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $trn->name }}">{{ $trn->name }}</span>
                                                         <span style="font-size: 0.68rem; display: block; font-weight: 600;">
                                                             @if($trnStatus === 'published' || $trnStatus === 'sent')
                                                                 <span style="color: #10b981;"><span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #10b981; margin-right: 4px;"></span>Terkirim</span>
                                                             @else
                                                                 <span style="color: #f59e0b;"><span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #f59e0b; margin-right: 4px;"></span>Belum Terkirim</span>
                                                             @endif
                                                         </span>
                                                     </div>
                                                 </div>
                                                 
                                                 <div style="flex-shrink: 0; margin-left: 8px;">
                                                     @if($trnStatus === 'published' || $trnStatus === 'sent')
                                                         <div style="display: flex; gap: 4px;">
                                                             <a href="{{ route('admin.trainer.certificates.detail', ['certificate' => $trnCert->id]) }}" 
                                                                class="btn-action-small view"
                                                                style="width: 28px; height: 28px; border-radius: 6px;"
                                                                title="Lihat Sertifikat">
                                                                 <i class="bi bi-eye-fill" style="font-size: 12px;"></i>
                                                             </a>
                                                             <a href="{{ route('admin.trainer.certificates.edit', ['trainer' => $trn->id, 'context' => $context, 'id' => $item->id]) }}" 
                                                                class="btn-action-small manage-icon"
                                                                style="width: 28px; height: 28px; border-radius: 6px;"
                                                                title="Kelola & Kirim Ulang">
                                                                 <i class="bi bi-pencil-square" style="font-size: 12px;"></i>
                                                             </a>
                                                         </div>
                                                     @else
                                                         <a href="{{ route('admin.trainer.certificates.edit', ['trainer' => $trn->id, 'context' => $context, 'id' => $item->id]) }}" 
                                                            class="btn-action-manage-pill"
                                                            style="padding: 4px 10px; border-radius: 6px; font-size: 0.7rem; box-shadow: none;"
                                                            title="Kelola & Kirim">
                                                             <i class="bi bi-gear-fill" style="font-size: 10px;"></i>
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
                        <div class="empty-card" style="grid-column: 1 / -1; padding: 60px 20px;">
                            <i class="bi bi-inbox fs-1 d-block mb-3" style="color: #cbd5e1;"></i>
                            Data program tidak ditemukan.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Sidebar (Recent Activities) -->
            <div class="col-xl-3 col-lg-4">
                <!-- Recent Activities Card -->
                <div class="card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); border-top: none;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="bi bi-clock-history" style="color: #1e1b4b; font-size: 16px;"></i>
                            <h5 style="font-size: 0.90rem; font-weight: 800; color: #0f172a; margin: 0;">Aktivitas Terakhir</h5>
                        </div>
                        <a href="#" style="font-size: 0.75rem; font-weight: 700; color: #1e1b4b; text-decoration: none; transition: color 0.2s;">Lihat Semua</a>
                    </div>

                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        @foreach($recentActivities as $act)
                            <div style="display: flex; gap: 12px; align-items: flex-start;">
                                <div style="width: 28px; height: 28px; border-radius: 8px; background: {{ $act->type === 'success' ? '#ecfdf5' : ($act->type === 'warning' ? '#fff7ed' : '#eff6ff') }}; color: {{ $act->color }}; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; margin-top: 2px;">
                                    <i class="bi {{ $act->icon }}"></i>
                                </div>
                                <div style="min-width: 0; flex: 1;">
                                    <p style="font-size: 0.78rem; color: #475569; margin: 0 0 4px 0; line-height: 1.4; font-weight: 500;">
                                        {!! $act->message !!}
                                    </p>
                                    <span style="font-size: 0.68rem; color: #94a3b8; font-weight: 600;">{{ $act->time }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('admin-trainer-scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('certSearch');
            const typeFilter = document.getElementById('certType');
            const resetBtn = document.getElementById('certReset');
            const tabBtns = document.querySelectorAll('.filter-tab');
            let activeTabValue = 'all';
            let updateArrowButtons = null;

            function runFilter() {
                const term = (searchInput?.value || '').toLowerCase().trim();
                const type = typeFilter?.value || 'all';

                document.querySelectorAll('.cert-row').forEach(row => {
                    const title = row.getAttribute('data-title') || '';
                    const rowType = row.getAttribute('data-type') || '';
                    const rowTabCat = row.getAttribute('data-tab-cat') || '';

                    const matchSearch = term === '' || title.includes(term);
                    
                    // Match tab filter
                    let matchTab = false;
                    if (activeTabValue === 'all') {
                        matchTab = true;
                    } else if (activeTabValue === 'ready' && rowTabCat === 'ready') {
                        matchTab = true;
                    } else if (activeTabValue === 'not-configured' && rowTabCat === 'not-configured') {
                        matchTab = true;
                    } else if (activeTabValue === 'configured' && rowTabCat === 'configured') {
                        matchTab = true;
                    } else if (activeTabValue === 'sent' && rowTabCat === 'sent') {
                        matchTab = true;
                    }

                    // Match dropdown type filter
                    const matchType = type === 'all' || rowType === type;

                    if (matchSearch && matchTab && matchType) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (typeof updateArrowButtons === 'function') {
                    setTimeout(updateArrowButtons, 50);
                }
            }

            // Bind tab buttons
            tabBtns.forEach(btn => {
                btn.addEventListener('click', function () {
                    tabBtns.forEach(b => {
                        b.classList.remove('active');
                        b.style.borderColor = '#e2e8f0';
                        b.style.color = '#64748b';
                        b.style.background = '#fff';
                    });
                    this.classList.add('active');
                    this.style.borderColor = '#cbd5e1';
                    this.style.color = '#475569';
                    this.style.background = '#f8fafc';

                    activeTabValue = this.getAttribute('data-tab-value');
                    runFilter();
                });
            });

            // Bind search and select inputs
            searchInput?.addEventListener('input', runFilter);
            typeFilter?.addEventListener('change', function() {
                // If dropdown changes, reflect it as the search filter
                runFilter();
            });

            // Bind reset button
            resetBtn?.addEventListener('click', function () {
                if (searchInput) searchInput.value = '';
                if (typeFilter) typeFilter.value = 'all';
                
                // Reset active tab to "All"
                tabBtns.forEach((b, index) => {
                    if (index === 0) {
                        b.classList.add('active');
                        b.style.borderColor = '#cbd5e1';
                        b.style.color = '#475569';
                        b.style.background = '#f8fafc';
                    } else {
                        b.classList.remove('active');
                        b.style.borderColor = '#e2e8f0';
                        b.style.color = '#64748b';
                        b.style.background = '#fff';
                    }
                });
                activeTabValue = 'all';
                runFilter();
            });

            // Slide Navigation Logic
            const grid = document.getElementById('certificateGrid');
            const prevBtn = document.getElementById('slidePrevBtn');
            const nextBtn = document.getElementById('slideNextBtn');
            const arrowsContainer = document.getElementById('slideArrowsContainer');

            if (grid && prevBtn && nextBtn) {
                const cardWidth = 374; // 350px card width + 24px gap

                updateArrowButtons = function() {
                    const scrollLeft = grid.scrollLeft;
                    const maxScrollLeft = grid.scrollWidth - grid.clientWidth;

                    // If content is not scrollable, hide the arrow buttons container
                    if (grid.scrollWidth <= grid.clientWidth) {
                        if (arrowsContainer) arrowsContainer.style.display = 'none';
                        return;
                    } else {
                        if (arrowsContainer) arrowsContainer.style.display = 'flex';
                    }

                    // Disable/opacity-fade prev button
                    if (scrollLeft <= 5) {
                        prevBtn.style.opacity = '0.4';
                        prevBtn.style.pointerEvents = 'none';
                    } else {
                        prevBtn.style.opacity = '1';
                        prevBtn.style.pointerEvents = 'auto';
                    }

                    // Disable/opacity-fade next button
                    if (scrollLeft >= maxScrollLeft - 5) {
                        nextBtn.style.opacity = '0.4';
                        nextBtn.style.pointerEvents = 'none';
                    } else {
                        nextBtn.style.opacity = '1';
                        nextBtn.style.pointerEvents = 'auto';
                    }
                }

                prevBtn.addEventListener('click', function () {
                    grid.scrollBy({
                        left: -cardWidth,
                        behavior: 'smooth'
                    });
                });

                nextBtn.addEventListener('click', function () {
                    grid.scrollBy({
                        left: cardWidth,
                        behavior: 'smooth'
                    });
                });

                grid.addEventListener('scroll', updateArrowButtons);
                window.addEventListener('resize', updateArrowButtons);
                
                // Initial check
                setTimeout(updateArrowButtons, 100);
            }
        });
    </script>
@endpush