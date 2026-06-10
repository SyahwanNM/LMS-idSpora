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

        .admin-trainer-main {
            background-color: #f8fafc !important;
        }

        .cert-dashboard {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            width: 100%;
            background: transparent;
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

        <!-- Header Section -->
        <div style="display: flex; flex-direction: column; gap: 20px; margin-bottom: 32px;">
            <div class="row align-items-center g-4">
                <!-- Left Title Block -->
                <div class="col-xl-6">
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 8px;">
                        <!-- Ribbon Icon Circle -->
                        <div style="width: 50px; height: 50px; border-radius: 50%; background: #eef2ff; display: flex; align-items: center; justify-content: center; color: #4f46e5; font-size: 24px; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.08); flex-shrink: 0;">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div>
                            <div style="font-size: 0.72rem; font-weight: 800; color: #4f46e5; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 2px;">Sistem Rekognisi</div>
                            <h1 style="font-size: 2.1rem; font-weight: 800; color: #0f172a; margin: 0; letter-spacing: -0.5px;">Sertifikat & Penghargaan</h1>
                        </div>
                    </div>
                    <p style="font-size: 0.95rem; color: #64748b; line-height: 1.5; margin: 0; max-width: 560px;">Pantau program yang masih dalam proses pengiriman sertifikat dan lihat yang sudah berhasil diselesaikan.</p>
                </div>

                <!-- Right Dark Blue Card Banner -->
                <div class="col-xl-6">
                    <div class="header-banner-card" style="background: linear-gradient(135deg, #1e1b4b 0%, #2e2a72 40%, #3b359c 100%); border-radius: 24px; padding: 24px; color: white; display: flex; position: relative; overflow: hidden; min-height: 140px; align-items: center; box-shadow: 0 12px 30px rgba(30, 27, 75, 0.2);">
                        <!-- Stats Items -->
                        <div style="display: flex; flex-wrap: wrap; gap: 20px 32px; z-index: 2; flex: 1; justify-content: flex-start; padding-right: 140px;">
                            <!-- Stat 1 -->
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 100px;">
                                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(20, 184, 166, 0.15); display: flex; align-items: center; justify-content: center; color: #2dd4bf; font-size: 18px; flex-shrink: 0;">
                                    <i class="bi bi-send-fill"></i>
                                </div>
                                <div>
                                    <div style="font-size: 1.4rem; font-weight: 800; line-height: 1.1;">{{ $siapDikirimCount }}</div>
                                    <div style="font-size: 0.72rem; color: #e2e8f0; font-weight: 500;">Siap Dikirim</div>
                                    <div style="font-size: 0.65rem; color: #94a3b8;">Program</div>
                                </div>
                            </div>
                            <!-- Stat 2 -->
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 100px;">
                                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(245, 158, 11, 0.15); display: flex; align-items: center; justify-content: center; color: #fb923c; font-size: 18px; flex-shrink: 0;">
                                    <i class="bi bi-folder-fill"></i>
                                </div>
                                <div>
                                    <div style="font-size: 1.4rem; font-weight: 800; line-height: 1.1;">{{ $menungguAssetCount }}</div>
                                    <div style="font-size: 0.72rem; color: #e2e8f0; font-weight: 500;">Menunggu Asset</div>
                                    <div style="font-size: 0.65rem; color: #94a3b8;">Program</div>
                                </div>
                            </div>
                            <!-- Stat 3 -->
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 100px;">
                                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(59, 130, 246, 0.15); display: flex; align-items: center; justify-content: center; color: #60a5fa; font-size: 18px; flex-shrink: 0;">
                                    <i class="bi bi-check-circle-fill"></i>
                                </div>
                                <div>
                                    <div style="font-size: 1.4rem; font-weight: 800; line-height: 1.1;">{{ $dalamProsesCount }}</div>
                                    <div style="font-size: 0.72rem; color: #e2e8f0; font-weight: 500;">Dalam Proses</div>
                                    <div style="font-size: 0.65rem; color: #94a3b8;">Program</div>
                                </div>
                            </div>
                            <!-- Stat 4 -->
                            <div style="display: flex; align-items: center; gap: 12px; min-width: 100px;">
                                <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(168, 85, 247, 0.15); display: flex; align-items: center; justify-content: center; color: #c084fc; font-size: 18px; flex-shrink: 0;">
                                    <i class="bi bi-award-fill"></i>
                                </div>
                                <div>
                                    <div style="font-size: 1.4rem; font-weight: 800; line-height: 1.1;">{{ $terkirimHariIni }}</div>
                                    <div style="font-size: 0.72rem; color: #e2e8f0; font-weight: 500;">Terkirim Hari Ini</div>
                                    <div style="font-size: 0.65rem; color: #94a3b8;">Program</div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Graphics (Certificate mockups) -->
                        <div style="position: absolute; right: -20px; top: -10px; width: 180px; height: 160px; z-index: 1; pointer-events: none; opacity: 0.95;">
                            <!-- Back Card -->
                            <div style="position: absolute; right: 35px; top: 30px; width: 120px; height: 85px; background: linear-gradient(135deg, rgba(168, 85, 247, 0.35), rgba(79, 70, 229, 0.35)); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; transform: rotate(15deg); backdrop-filter: blur(6px);"></div>
                            <!-- Front Card -->
                            <div style="position: absolute; right: 20px; top: 20px; width: 130px; height: 90px; background: rgba(255, 255, 255, 0.96); border-radius: 10px; transform: rotate(-5deg); box-shadow: 0 10px 25px rgba(0,0,0,0.25); padding: 10px; display: flex; flex-direction: column; justify-content: space-between; border: 1.5px solid white;">
                                <div style="font-size: 0.62rem; font-weight: 900; color: #4338ca; text-transform: uppercase; letter-spacing: 0.5px;">Sertifikat</div>
                                <div style="height: 2px; background: #e0e7ff; width: 60%; border-radius: 1px;"></div>
                                <div style="height: 2px; background: #e0e7ff; width: 45%; border-radius: 1px;"></div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 4px;">
                                    <div style="font-size: 0.48rem; font-weight: 700; color: #94a3b8; font-family: 'Georgia', serif; font-style: italic;">idSpora</div>
                                    <div style="width: 14px; height: 14px; border-radius: 50%; background: #fbbf24; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                                        <i class="bi bi-award-fill" style="font-size: 8px; color: white;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

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
                <div class="cert-tab-wrapper" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 0;">
                    <button type="button" class="cert-tab-btn filter-tab active" data-tab-value="all" style="border: 1px solid #cbd5e1; background: #fff; color: #475569; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-grid-fill"></i> Semua
                    </button>
                    <button type="button" class="cert-tab-btn filter-tab" data-tab-value="ready" style="border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-send-fill"></i> Siap Dikirim
                        <span style="background: #d1fae5; color: #065f46; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 20px; margin-left: 2px;">{{ $siapDikirimCount }}</span>
                    </button>
                    <button type="button" class="cert-tab-btn filter-tab" data-tab-value="not-configured" style="border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-folder-fill"></i> Menunggu Asset
                        <span style="background: #ffedd5; color: #9a3412; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 20px; margin-left: 2px;">{{ $menungguAssetCount }}</span>
                    </button>
                    <button type="button" class="cert-tab-btn filter-tab" data-tab-value="configured" style="border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-check-circle-fill"></i> Dalam Proses
                        <span style="background: #dbeafe; color: #1e40af; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 20px; margin-left: 2px;">{{ $dalamProsesCount }}</span>
                    </button>
                    <button type="button" class="cert-tab-btn filter-tab" data-tab-value="sent" style="border: 1px solid #e2e8f0; background: #fff; color: #64748b; padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 8px; cursor: pointer; transition: all 0.2s;">
                        <i class="bi bi-award-fill"></i> Sudah Dikirim
                        <span style="background: #f1f5f9; color: #475569; font-size: 0.7rem; font-weight: 800; padding: 2px 8px; border-radius: 20px; margin-left: 2px;">{{ $sudahDikirimCount }}</span>
                    </button>
                </div>

                <!-- Filter Card Panel -->
                <div class="filter-card" style="background: #fff; border: 1px solid #e2e8f0; border-radius: 24px; padding: 16px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); margin-top: 16px; margin-bottom: 24px;">
                    <div class="row g-3 align-items-center">
                        <!-- Search bar -->
                        <div class="col-lg-5">
                            <div class="search-wrap" style="position: relative;">
                                <i class="bi bi-search" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 15px;"></i>
                                <input type="text" id="certSearch" class="filter-input" placeholder="Cari program..." style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 12px; padding: 0 16px 0 44px; outline: none; font-size: 0.9rem; font-weight: 500; transition: all 0.2s;">
                            </div>
                        </div>
                        <!-- Status Dropdown -->
                        <div class="col-lg-4">
                            <div style="position: relative;">
                                <select id="certStatus" class="filter-input" style="width: 100%; height: 42px; border: 1px solid #cbd5e1; border-radius: 12px; padding: 0 36px 0 16px; outline: none; font-size: 0.9rem; font-weight: 600; color: #475569; appearance: none; background: white;">
                                    <option value="all">Semua Status</option>
                                    <option value="ready">Siap Terbit</option>
                                    <option value="configured">Dikonfigurasi</option>
                                    <option value="not-configured">Belum Dikonfigurasi</option>
                                    <option value="sent">Sudah Terbit</option>
                                </select>
                                <i class="bi bi-chevron-down" style="position: absolute; right: 16px; top: 50%; transform: translateY(-50%); color: #94a3b8; pointer-events: none;"></i>
                            </div>
                        </div>
                        <!-- Filter & Reset Button -->
                        <div class="col-lg-3" style="display: flex; gap: 8px;">
                            <button type="button" id="certFilterBtn" class="btn-reset-filter" style="height: 42px; flex: 1; border-radius: 12px; border: 1px solid #cbd5e1; background: #fff; font-weight: 700; font-size: 0.85rem; color: #475569; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s; cursor: pointer;">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                            <button type="button" id="certReset" class="btn-reset-filter" style="height: 42px; width: 42px; border-radius: 12px; border: 1px solid #cbd5e1; background: #fff; font-weight: 700; color: #475569; display: inline-flex; align-items: center; justify-content: center; transition: all 0.2s; cursor: pointer;" title="Reset Filter">
                                <i class="bi bi-arrow-counterclockwise" style="font-size: 16px;"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Certificate Program Cards Grid -->
                <div class="certificate-grid" id="certificateGrid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(340px, 1fr)); gap: 24px;">
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
                            $typeBadge = $isEvent ? 'ACARA' : 'KURSUS';
                            
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
                             style="background: #fff; border: 1px solid #e2e8f0; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); transition: all 0.3s ease; display: flex; flex-direction: column; overflow: hidden;">
                             
                             <!-- Top Badge Header -->
                             <div style="padding: 16px 20px 10px; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0;">
                                 <!-- Status badge -->
                                 @if($isSent)
                                     <span style="font-size: 0.68rem; font-weight: 800; background: #faf5ff; color: #a855f7; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 4px;">
                                         <i class="bi bi-check-circle-fill"></i> Sudah Dikirim
                                     </span>
                                 @elseif($status === 'ready')
                                     <span style="font-size: 0.68rem; font-weight: 800; background: #ecfdf5; color: #10b981; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 4px;">
                                         <i class="bi bi-check-circle-fill"></i> Siap Dikirim
                                     </span>
                                 @elseif($status === 'configured')
                                     <span style="font-size: 0.68rem; font-weight: 800; background: #eff6ff; color: #3b82f6; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 4px;">
                                         <i class="bi bi-check-circle-fill"></i> Dalam Proses
                                     </span>
                                 @else
                                     <span style="font-size: 0.68rem; font-weight: 800; background: #fff7ed; color: #f59e0b; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px; display: inline-flex; align-items: center; gap: 4px;">
                                         <i class="bi bi-exclamation-circle-fill"></i> Menunggu Asset
                                     </span>
                                 @endif

                                 <!-- Type badge (ACARA/KURSUS) -->
                                 <span style="font-size: 0.68rem; font-weight: 800; background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 6px; text-transform: uppercase; letter-spacing: 0.5px;">
                                     {{ $typeBadge }}
                                 </span>
                             </div>

                             <!-- Inner card body -->
                             <div class="card-inner" style="padding: 0 20px 20px; display: flex; flex-direction: column; gap: 14px; flex: 1;">
                                 <!-- Title, Date and stats row -->
                                 <div style="display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;">
                                     <div style="display: flex; gap: 12px; min-width: 0;">
                                         <!-- Left Calendar icon card -->
                                         <div style="width: 42px; height: 42px; border-radius: 10px; background: #eef2ff; color: #4338ca; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0;">
                                             <i class="bi bi-calendar3"></i>
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
                                 <div class="asset-checklist" style="border-top: 1px dashed #e2e8f0; padding-top: 12px; margin-top: 4px;">
                                     <div class="checklist-title" style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 8px;">Kelengkapan Asset Desain</div>
                                     <div class="checklist-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;">
                                         <div class="check-item {{ $hasTemplate ? 'is-valid' : 'is-invalid' }}" style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 4px; border-radius: 8px; border: 1px solid {{ $hasTemplate ? '#d1fae5' : '#ffe4e6' }}; background: {{ $hasTemplate ? '#f0fdf4' : '#fff1f2' }}; transition: all 0.2s;">
                                             <i class="bi {{ $hasTemplate ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" style="color: {{ $hasTemplate ? '#10b981' : '#f43f5e' }}; font-size: 13px; display: inline-flex;"></i>
                                             <span style="font-size: 0.72rem; font-weight: 700; color: {{ $hasTemplate ? '#065f46' : '#9f1239' }};">Template</span>
                                         </div>
                                         <div class="check-item {{ $hasLogo ? 'is-valid' : 'is-invalid' }}" style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 4px; border-radius: 8px; border: 1px solid {{ $hasLogo ? '#d1fae5' : '#ffe4e6' }}; background: {{ $hasLogo ? '#f0fdf4' : '#fff1f2' }}; transition: all 0.2s;">
                                             <i class="bi {{ $hasLogo ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" style="color: {{ $hasLogo ? '#10b981' : '#f43f5e' }}; font-size: 13px; display: inline-flex;"></i>
                                             <span style="font-size: 0.72rem; font-weight: 700; color: {{ $hasLogo ? '#065f46' : '#9f1239' }};">Logo</span>
                                         </div>
                                         <div class="check-item {{ $hasSignature ? 'is-valid' : 'is-invalid' }}" style="display: flex; align-items: center; justify-content: center; gap: 6px; padding: 6px 4px; border-radius: 8px; border: 1px solid {{ $hasSignature ? '#d1fae5' : '#ffe4e6' }}; background: {{ $hasSignature ? '#f0fdf4' : '#fff1f2' }}; transition: all 0.2s;">
                                             <i class="bi {{ $hasSignature ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}" style="color: {{ $hasSignature ? '#10b981' : '#f43f5e' }}; font-size: 13px; display: inline-flex;"></i>
                                             <span style="font-size: 0.72rem; font-weight: 700; color: {{ $hasSignature ? '#065f46' : '#9f1239' }}; text-align: center; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">Ttd Partner</span>
                                         </div>
                                     </div>
                                 </div>

                                 <!-- Trainer & Certificate List -->
                                 <div class="trainer-status-list" style="margin-top: 2px;">
                                     <div class="checklist-title" style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 8px;">Daftar Trainer & Sertifikat</div>
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
                                             <div class="trainer-status-row" style="display: flex; align-items: center; justify-content: space-between; padding: 6px 10px; background: #f8fafc; border: 1px solid #f1f5f9; border-radius: 12px; transition: all 0.2s;">
                                                 <div style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1;">
                                                     <!-- Initials Circle -->
                                                     <div style="width: 28px; height: 28px; border-radius: 50%; background-color: {{ $bgColor }}; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 10px; flex-shrink: 0; border: 1px solid rgba(255,255,255,0.1);">
                                                         {{ $initials }}
                                                     </div>
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

            <!-- Right Column: Sidebar (Quick Summary & Recent Activities) -->
            <div class="col-xl-3 col-lg-4">
                
                <!-- Quick Summary Card -->
                <div class="card" style="background: #white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); margin-bottom: 24px; border-top: none;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <i class="bi bi-bar-chart-line-fill" style="color: #4f46e5; font-size: 16px;"></i>
                        <h5 style="font-size: 0.90rem; font-weight: 800; color: #0f172a; margin: 0;">Ringkasan Cepat</h5>
                    </div>
                    
                    <div style="display: flex; flex-direction: column; gap: 16px;">
                        <!-- Item 1 -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: #ecfdf5; color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                    <i class="bi bi-send-fill"></i>
                                </div>
                                <span style="font-size: 0.82rem; font-weight: 700; color: #475569;">Program Siap Dikirim</span>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 0.9rem; font-weight: 800; color: #0f172a;">{{ $siapDikirimCount }}</span>
                                <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 600; margin-left: 2px;">{{ $siapDikirimPercent }}%</span>
                            </div>
                        </div>

                        <!-- Item 2 -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: #fff7ed; color: #f59e0b; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                    <i class="bi bi-folder-fill"></i>
                                </div>
                                <span style="font-size: 0.82rem; font-weight: 700; color: #475569;">Menunggu Asset</span>
                            </div>
                            <div style="text-align: right;">
                                <span style="font-size: 0.9rem; font-weight: 800; color: #0f172a;">{{ $menungguAssetCount }}</span>
                                <span style="font-size: 0.7rem; color: #94a3b8; font-weight: 600; margin-left: 2px;">{{ $menungguAssetPercent }}%</span>
                            </div>
                        </div>

                        <!-- Item 3 -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: #eff6ff; color: #3b82f6; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div>
                                    <span style="font-size: 0.82rem; font-weight: 700; color: #475569; display: block;">Total Peserta</span>
                                    <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 500; display: block; margin-top: -2px;">Seluruh program</span>
                                </div>
                            </div>
                            <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">
                                {{ $totalParticipants }}
                            </div>
                        </div>

                        <!-- Item 4 -->
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div style="display: flex; align-items: center; gap: 12px;">
                                <div style="width: 32px; height: 32px; border-radius: 8px; background: #f5f3ff; color: #7c3aed; display: flex; align-items: center; justify-content: center; font-size: 14px; flex-shrink: 0;">
                                    <i class="bi bi-person-badge-fill"></i>
                                </div>
                                <div>
                                    <span style="font-size: 0.82rem; font-weight: 700; color: #475569; display: block;">Total Trainer</span>
                                    <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 500; display: block; margin-top: -2px;">Aktif terlibat</span>
                                </div>
                            </div>
                            <div style="font-size: 0.95rem; font-weight: 800; color: #0f172a;">
                                {{ $totalActiveTrainers }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities Card -->
                <div class="card" style="background: #white; border: 1px solid #e2e8f0; border-radius: 20px; padding: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.01); border-top: none;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 18px; border-bottom: 1px solid #f1f5f9; padding-bottom: 12px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="bi bi-clock-history" style="color: #4f46e5; font-size: 16px;"></i>
                            <h5 style="font-size: 0.90rem; font-weight: 800; color: #0f172a; margin: 0;">Aktivitas Terakhir</h5>
                        </div>
                        <a href="#" style="font-size: 0.75rem; font-weight: 700; color: #4f46e5; text-decoration: none; transition: color 0.2s;">Lihat Semua</a>
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
            const statusFilter = document.getElementById('certStatus');
            const resetBtn = document.getElementById('certReset');
            const tabBtns = document.querySelectorAll('.filter-tab');
            let activeTabValue = 'all';

            function runFilter() {
                const term = (searchInput?.value || '').toLowerCase().trim();
                const status = statusFilter?.value || 'all';

                document.querySelectorAll('.cert-row').forEach(row => {
                    const title = row.getAttribute('data-title') || '';
                    const rowStatus = row.getAttribute('data-status') || '';
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

                    // Match dropdown status filter
                    const matchStatus = status === 'all' || rowStatus === status;

                    if (matchSearch && matchTab && matchStatus) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
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
            statusFilter?.addEventListener('change', function() {
                // If dropdown changes, reflect it as the search filter
                runFilter();
            });

            // Bind reset button
            resetBtn?.addEventListener('click', function () {
                if (searchInput) searchInput.value = '';
                if (statusFilter) statusFilter.value = 'all';
                
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
        });
    </script>
@endpush