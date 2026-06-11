@extends('layouts.trainer')

@section('title', 'Event Studio - ' . $event->title)

@php
    $pageTitle = 'Event Studio';
    $breadcrumbs = [
        ['label' => 'Beranda', 'url' => route('trainer.dashboard')],
        ['label' => 'Acara', 'url' => route('trainer.events')],
        ['label' => 'Studio'],
    ];

    $materialStatus = (string) ($event->material_status ?? 'draft');
    $submittedModules = ($myModules ?? collect());
    if ($submittedModules->isEmpty() && !empty($event->module_path)) {
        $submittedModules = collect([(object) [
            'original_name' => basename((string) $event->module_path),
            'path' => $event->module_path,
            'status' => $materialStatus,
            'created_at' => $event->module_submitted_at ?? $event->updated_at,
            'survey_link' => null,
            'rejection_reason' => null,
            'is_legacy' => true,
        ]]);
    }
    $hasUploadedModule = $submittedModules->isNotEmpty();
    if ($hasUploadedModule && ($myMaterialStatus ?? 'not_uploaded') !== 'not_uploaded') {
        $displayMaterialStatus = (string) $myMaterialStatus;
    } else {
        $displayMaterialStatus = $hasUploadedModule ? $materialStatus : 'draft';
    }
    $isRevisionWindow = $displayMaterialStatus === 'rejected';
    $deadline = $isRevisionWindow
        ? (!empty($event->material_revision_deadline)
            ? \Carbon\Carbon::parse($event->material_revision_deadline)
            : ($event->start_at ? $event->start_at->copy()->subDays(3) : null))
        : (!empty($event->material_deadline)
            ? \Carbon\Carbon::parse($event->material_deadline)
            : ($event->start_at ? $event->start_at->copy()->subDays(3) : null));
    $deadlinePassed = $deadline ? now()->gt($deadline) : false;
    $deadlineLabel = $isRevisionWindow ? 'Deadline Revisi (H-3)' : 'Deadline Pengumpulan (H-3)';
    $deadlineHint = $deadlinePassed
        ? ($isRevisionWindow ? 'Batas revisi sudah lewat' : 'Batas pengumpulan sudah lewat')
        : ($isRevisionWindow ? 'Masa revisi masih terbuka' : 'Materi masih bisa diunggah');
    $materialStatusLabel = strtoupper(str_replace('_', ' ', $displayMaterialStatus));
    $isRejected = $displayMaterialStatus === 'rejected';
    $uploadModeLabel = $isRejected ? 'Upload Revisi Materi' : 'Upload Materi Event';
    $submitDraftLabel = $isRejected ? 'GANTI FILE REVISI' : 'TAMBAHKAN KE DRAF';
    $eventRejectionReason = trim((string) ($event->material_rejection_reason ?? ''));
    if ($eventRejectionReason === '') {
        $eventRejectionReason = trim((string) ($event->module_rejection_reason ?? ''));
    }
    $showEventRejectionReason = $displayMaterialStatus === 'rejected' && $eventRejectionReason !== '';
    $resolveModuleViewUrl = function ($module) use ($event) {
        $path = (string) ($module->path ?? '');
        if ($path === '') {
            return null;
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        if (!empty($module->is_legacy)) {
            return $event->module_file_url;
        }

        return $module->download_url ?? \Illuminate\Support\Facades\Storage::disk('public')->url($path);
    };
    $resolveModuleLabel = function ($module) {
        $name = (string) ($module->original_name ?? 'Materi');
        if (str_starts_with($name, 'Link: ')) {
            return str_replace('Link: ', '', $name);
        }

        return $name;
    };
    $canUploadMaterials = true;

    $step1State = 'active'; // active, completed
    $step2State = 'inactive'; // inactive, active, completed
    $step3State = 'inactive'; // inactive, active, completed, rejected

    if ($displayMaterialStatus === 'pending_review' || $displayMaterialStatus === 'approved') {
        $step1State = 'completed';
        $step2State = 'completed';
        if ($displayMaterialStatus === 'approved') {
            $step3State = 'completed';
        } else {
            $step3State = 'active';
        }
    } elseif ($displayMaterialStatus === 'rejected') {
        $step1State = 'active';
        $step2State = !empty($draftModules) ? 'active' : 'inactive';
        $step3State = 'rejected';
    } else {
        $step1State = 'active';
        $step2State = !empty($draftModules) ? 'active' : 'inactive';
        $step3State = 'inactive';
    }
@endphp

@push('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css" />
    <style>
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }
        /* Premium SweetAlert Custom Styling */
        .premium-swal-popup {
            border-radius: 28px !important;
            padding: 36px 30px !important;
            font-family: 'Outfit', 'Inter', sans-serif !important;
            box-shadow: 0 25px 50px -12px rgba(81, 55, 108, 0.25) !important;
            border: 1px solid rgba(81, 55, 108, 0.1) !important;
            background: #ffffff !important;
        }

        .premium-swal-title {
            font-size: 24px !important;
            font-weight: 800 !important;
            color: #1e1b4b !important;
            letter-spacing: -0.03em !important;
            margin-top: 15px !important;
            margin-bottom: 10px !important;
        }

        .premium-swal-confirm-btn {
            padding: 14px 32px !important;
            font-size: 14px !important;
            font-weight: 700 !important;
            border-radius: 14px !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            background: linear-gradient(135deg, #51376c 0%, #3f2a54 100%) !important;
            color: #ffffff !important;
            border: none !important;
            cursor: pointer !important;
            margin: 8px !important;
        }

        .premium-swal-confirm-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 20px rgba(81, 55, 108, 0.3) !important;
        }

        .premium-swal-confirm-btn.danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
        }

        .premium-swal-confirm-btn.danger:hover {
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3) !important;
        }

        .premium-swal-cancel-btn {
            padding: 14px 28px !important;
            font-size: 14px !important;
            font-weight: 600 !important;
            border-radius: 14px !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            background: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0 !important;
            cursor: pointer !important;
            margin: 8px !important;
        }

        .premium-swal-cancel-btn:hover {
            background: #e2e8f0 !important;
            color: #334155 !important;
            transform: translateY(-2px) !important;
        }

        /* Sw-custom layouts styles */
        .swal-custom-container {
            text-align: center;
            padding: 10px 0;
        }

        .swal-icon-glow {
            width: 80px;
            height: 80px;
            margin: 0 auto 20px auto;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .swal-icon-glow.success {
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
        }

        .swal-icon-glow.success::after {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 2px dashed rgba(16, 185, 129, 0.3);
            animation: rotateDashed 20s linear infinite;
        }

        .swal-icon-glow.warning {
            background: rgba(81, 55, 108, 0.1);
            color: #51376c;
        }

        .swal-icon-glow.warning::after {
            content: '';
            position: absolute;
            inset: -6px;
            border-radius: 50%;
            border: 2px dashed rgba(81, 55, 108, 0.3);
            animation: rotateDashed 20s linear infinite;
        }

        @keyframes rotateDashed {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .swal-icon-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            background: #ffffff;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.06);
        }

        .swal-icon-glow.success .swal-icon-circle {
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.15);
        }

        .swal-icon-glow.warning .swal-icon-circle {
            box-shadow: 0 8px 20px rgba(81, 55, 108, 0.15);
        }

        .swal-custom-title {
            font-size: 22px;
            font-weight: 800;
            color: #1e1b4b;
            margin: 0 0 6px 0;
            letter-spacing: -0.02em;
        }

        .swal-custom-subtitle {
            font-size: 14px;
            color: #64748b;
            margin: 0 0 24px 0;
            line-height: 1.4;
        }

        .swal-warning-box {
            background: linear-gradient(135deg, #fdfcff 0%, #f9f6fc 100%);
            border: 1px dashed #b497d6;
            border-radius: 20px;
            padding: 20px;
            text-align: left;
            box-shadow: inset 0 2px 4px rgba(81, 55, 108, 0.02);
        }

        .warning-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f3effa;
            color: #51376c;
            padding: 6px 12px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.05em;
            margin-bottom: 12px;
            border: 1px solid rgba(81, 55, 108, 0.15);
        }

        .warning-text {
            font-size: 13.5px;
            color: #3b284e;
            line-height: 1.55;
            margin: 0 0 10px 0;
        }

        .warning-action-info {
            font-size: 12.5px;
            color: #51376c;
            line-height: 1.5;
            background: rgba(255, 255, 255, 0.7);
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid rgba(180, 151, 214, 0.2);
        }

        .badge-submit-action {
            display: inline-block;
            padding: 2px 6px;
            background: #51376c;
            color: #fff;
            border-radius: 6px;
            font-weight: 700;
            font-size: 11px;
        }

        .swal-steps-box {
            background: #fdfcff;
            border: 1px solid #e9e3f4;
            border-radius: 20px;
            padding: 22px 18px;
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .swal-step-item {
            display: grid;
            grid-template-columns: 36px 1fr;
            gap: 14px;
            align-items: flex-start;
        }

        .swal-step-item .step-num {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: #f3effa;
            color: #51376c;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(81, 55, 108, 0.06);
        }

        .swal-step-item .step-content strong {
            display: block;
            font-size: 13.5px;
            color: #1e1b4b;
            margin-bottom: 3px;
        }

        .swal-step-item .step-content p {
            margin: 0;
            font-size: 12px;
            color: #64748b;
            line-height: 1.45;
        }

        main.content-studio-main {
            width: 100%;
            margin: 0;
            padding: 0;
            flex: 1;
            overflow-x: hidden;
        }

        .studio-page {
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        .studio-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            align-items: start;
        }

        .studio-hero {
            background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
            border-radius: 24px;
            padding: 40px;
            position: relative;
            overflow: hidden;
            /* box-shadow removed */
        }

        .studio-hero::before,
        .studio-hero::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .studio-hero::before {
            top: -50%;
            left: -10%;
            width: 60%;
            height: 200%;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.25) 0%, rgba(255, 255, 255, 0) 70%);
            transform: rotate(30deg);
            filter: blur(40px);
        }

        .studio-hero::after {
            bottom: -50%;
            right: -10%;
            width: 50%;
            height: 150%;
            background: radial-gradient(circle, rgba(236, 72, 153, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
            filter: blur(40px);
        }

        .studio-hero-inner {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: var(--spacing-2xl);
        }

        .studio-title {
            display: flex;
            flex-direction: column;
            gap: 12px;
            max-width: 780px;
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
            backdrop-filter: blur(10px);
            width: fit-content;
        }

        .badge-top i {
            color: var(--yellow-clr);
            font-size: 12px;
        }

        .studio-title h1 {
            margin: 0;
            color: var(--white-clr);
            font-size: 40px;
            font-weight: 800;
            line-height: 1.2;
        }

        .studio-title h5 {
            margin: 0;
            color: rgba(255, 255, 255, 0.72);
            font-size: 14px;
            font-weight: 500;
            line-height: 1.6;
            max-width: 640px;
        }

        .studio-hero-badges {
            display: flex;
            flex-direction: column;
            gap: 10px;
            align-items: flex-end;
            min-width: 240px;
        }

        .hero-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 18px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.92);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.04em;
            white-space: nowrap;
            backdrop-filter: blur(10px);
            width: fit-content;
        }

        .hero-chip i {
            color: var(--yellow-clr);
        }

        .status-grid {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .status-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.04);
            display: flex;
            align-items: flex-start;
            gap: 16px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px rgba(15, 23, 42, 0.08);
        }

        .status-card .icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            background: linear-gradient(135deg, #f3effa 0%, #e9e3f4 100%);
            color: #51376c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            /* box-shadow removed */
        }

        .status-card .icon i {
            font-size: 22px;
        }

        .status-card .label {
            margin: 0 0 4px 0;
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .status-card .value {
            margin: 0;
            color: var(--main-navy-clr);
            font-size: 14px;
            font-weight: 700;
            line-height: 1.4;
        }

        .status-card .hint {
            margin: 4px 0 0 0;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.45;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.05em;
            width: fit-content;
            margin-bottom: 8px;
        }

        .status-badge.approved {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge.pending {
            background: #f3effa;
            color: #51376c;
        }

        .status-badge.not-submitted {
            background: #f1f5f9;
            color: #64748b;
        }

        .status-banner {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #fecaca;
            background: #fef2f2;
            color: #991b1b;
        }

        .status-banner .icon {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: #fee2e2;
            color: #b91c1c;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .status-banner .label {
            margin: 0 0 4px 0;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #b91c1c;
        }

        .status-banner .reason {
            margin: 0;
            font-size: 13px;
            line-height: 1.55;
            color: #7f1d1d;
            white-space: pre-line;
        }

        .studio-panel {
            background: var(--white-clr);
            border: 1px solid rgba(227, 233, 242, 0.95);
            border-radius: 24px;
            overflow: hidden;
            /* box-shadow removed */
            height: fit-content;
        }

        .panel-hero {
            padding: 24px 32px;
            background: linear-gradient(135deg, #3f2a54, #51376c);
            color: var(--white-clr);
        }

        .panel-hero h2 {
            margin: 0 0 6px 0;
            font-size: 22px;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }

        .panel-hero p {
            margin: 0;
            color: rgba(226, 232, 240, 0.9);
            font-size: 13px;
            line-height: 1.6;
        }

        .panel-chip {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.92);
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .upload-panel {
            padding: 32px;
        }

        .module-preview {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 24px;
            border-radius: 20px;
            border: 1px solid #e5ebf5;
            background: linear-gradient(180deg, #fbfcfe 0%, #ffffff 100%);
            margin-bottom: 24px;
            /* box-shadow removed */
        }

        .module-preview .meta-label {
            margin: 0 0 6px 0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            font-weight: 700;
        }

        .module-preview h3 {
            margin: 0 0 6px 0;
            color: var(--main-navy-clr);
            font-size: 18px;
            font-weight: 700;
        }

        .module-preview p {
            margin: 0;
            color: #4b5563;
            font-size: 13px;
            line-height: 1.5;
        }

        .module-preview .preview-link {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 16px;
            border-radius: 12px;
            background: #3f2a54;
            color: var(--white-clr);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .module-preview .preview-link i {
            color: var(--yellow-clr);
        }

        .module-preview-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }

        .module-preview-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 14px;
            border: 1px solid #e8edf5;
            background: #fff;
        }

        .module-preview-item .file-meta {
            min-width: 0;
            flex: 1;
        }

        .module-preview-item .file-name {
            margin: 0 0 4px 0;
            color: var(--main-navy-clr);
            font-size: 14px;
            font-weight: 700;
            word-break: break-all;
        }

        .module-preview-item .file-sub {
            margin: 0;
            color: #64748b;
            font-size: 12px;
        }

        .module-preview-item .file-status {
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 6px;
            border: 1px solid;
            white-space: nowrap;
            margin-top: 6px;
            display: inline-block;
        }

        .module-preview-item .file-status.approved {
            background: #dcfce7;
            border-color: #bbf7d0;
            color: #166534;
        }

        .module-preview-item .file-status.rejected {
            background: #fee2e2;
            border-color: #fecaca;
            color: #991b1b;
        }

        .module-preview-item .file-status.pending {
            background: #f3effa;
            border-color: #e9e3f4;
            color: #51376c;
        }

        .upload-lock-note {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 18px 20px;
            border-radius: 18px;
            border: 1px solid #dbe3f0;
            background: #f8fafc;
        }

        .upload-lock-note .icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            align-items: center;
            justify-content: center;
            background: rgba(35, 29, 121, 0.08);
            color: var(--main-navy-clr);
            flex-shrink: 0;
        }

        .upload-lock-note .icon i {
            font-size: 18px;
        }

        .upload-lock-note h3 {
            margin: 0 0 6px 0;
            color: var(--main-navy-clr);
            font-size: 18px;
            font-weight: 700;
        }

        .upload-lock-note p {
            margin: 0;
            color: #4b5563;
            font-size: 13px;
            line-height: 1.5;
        }

        .notification-modal {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 2500;
            align-items: center;
            justify-content: center;
            background: rgba(15, 23, 42, 0.55);
            padding: 20px;
        }

        .notification-modal.is-open {
            display: flex;
        }

        .notification-modal-card {
            width: min(100%, 460px);
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.24);
            overflow: hidden;
        }

        .notification-modal-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 22px 24px 12px;
        }

        .notification-modal-icon {
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            background: rgba(27, 23, 99, 0.08);
            color: var(--main-navy-clr);
        }

        .notification-modal-icon.success {
            background: rgba(22, 163, 74, 0.12);
            color: #166534;
        }

        .notification-modal-icon.error {
            background: rgba(220, 38, 38, 0.12);
            color: #991b1b;
        }

        .notification-modal-icon.warning {
            background: rgba(245, 158, 11, 0.15);
            color: #92400e;
        }

        .notification-modal-title {
            margin: 0;
            font-size: 18px;
            font-weight: 800;
            color: var(--main-navy-clr);
            line-height: 1.3;
        }

        .notification-modal-message {
            margin: 0;
            padding: 0 24px 18px 82px;
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
            white-space: pre-line;
        }

        .notification-modal-footer {
            display: flex;
            justify-content: flex-end;
            padding: 0 24px 24px;
        }

        .notification-modal-close {
            border: none;
            border-radius: 999px;
            padding: 11px 18px;
            background: var(--main-navy-clr);
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }

        .notification-modal-close:hover {
            filter: brightness(1.05);
        }

        .upload-hints {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .hint-card {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px;
            border: 1px solid #e5ebf5;
            border-radius: 18px;
            background: linear-gradient(180deg, #ffffff 0%, #fbfcfe 100%);
        }

        .hint-card .icon {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: rgba(35, 29, 121, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--main-navy-clr);
            flex-shrink: 0;
        }

        .hint-card .icon i {
            font-size: 16px;
        }

        .hint-card .meta-label {
            margin: 0 0 4px 0;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #6b7280;
            font-weight: 700;
        }

        .hint-card .meta-text {
            margin: 0;
            color: var(--main-navy-clr);
            font-size: 13px;
            line-height: 1.5;
            font-weight: 600;
        }

        .module-form {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .dropzone {
            min-height: 300px;
            border-radius: 24px;
            border: 2px dashed #cbd5e1;
            background: rgba(248, 250, 252, 0.8);
            backdrop-filter: blur(10px);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 16px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 40px;
            margin-top: 8px;
        }

        .dropzone:hover {
            border-color: #51376c;
            background: #fbf9fe;
            transform: scale(1.01);
        }

        .dropzone i {
            font-size: 48px;
            color: #b497d6;
            transition: transform 0.3s;
        }

        .dropzone:hover i {
            transform: translateY(-8px);
        }

        .dropzone h2 {
            margin: 0;
            font-size: var(--font-size-xl);
            color: var(--main-navy-clr);
            font-weight: 600;
        }

        .dropzone p {
            margin: 0;
            font-size: var(--font-size-xs);
            letter-spacing: 0.12em;
            color: var(--gray-second-clr);
            font-weight: 600;
        }

        .file-list h3 {
            font-size: var(--font-size-md);
            font-weight: 600;
            color: var(--main-navy-clr);
            margin: 0 0 var(--spacing-md) 0;
        }

        .panel-footer {
            margin-top: var(--spacing-lg);
            padding-top: var(--spacing-md);
            border-top: 1px solid var(--line-clr);
            display: flex;
            justify-content: flex-end;
        }

        .primary-btn {
            border: none;
            background: linear-gradient(135deg, #51376c 0%, #3f2a54 100%);
            color: var(--white-clr);
            padding: 14px 28px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.06em;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            /* box-shadow removed */
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .primary-btn:hover {
            background: linear-gradient(135deg, #634585 0%, #4c3366 100%) !important;
            transform: translateY(-2px);
        }

        .primary-btn i {
            color: #e9d5ff;
        }

        .validation-card {
            background: linear-gradient(180deg, #3f2a54 0%, #3f2a54 100%);
            color: var(--white-clr);
            border-radius: 24px;
            padding: 22px;
            box-shadow: 0 18px 40px rgba(35, 29, 121, 0.22);
            position: sticky;
            top: var(--spacing-lg);
        }

        .validation-card h3 {
            margin: 0 0 var(--spacing-md) 0;
            color: var(--yellow-clr);
            font-size: var(--font-size-md);
            font-weight: 600;
        }

        .validation-card ol {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .validation-card li {
            display: grid;
            grid-template-columns: 32px 1fr;
            gap: var(--spacing-sm);
        }

        .validation-card li span {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-lg);
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--yellow-clr);
            font-weight: 600;
            font-size: var(--font-size-sm);
        }

        .validation-card h4 {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: var(--font-size-sm);
            font-weight: 600;
        }

        .validation-card p {
            margin: 0;
            color: #b8bce2;
            font-size: var(--font-size-xs);
            line-height: 1.5;
        }

        .file-list li {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 12px;
            background: #f8fafc;
            border-radius: 10px;
            margin-bottom: 8px;
            border-left: 3px solid var(--main-navy-clr);
        }

        .file-list li .file-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
            min-width: 0;
        }

        .file-list li .file-meta i {
            font-size: 20px;
            color: var(--main-navy-clr);
            flex-shrink: 0;
        }

        .file-list li .file-meta p {
            margin: 0;
        }

        .file-list li .file-name {
            font-size: 14px;
            font-weight: 600;
            color: var(--main-navy-clr);
            word-break: break-word;
        }

        .file-list li .file-size {
            font-size: 12px;
            color: #8a94a6;
        }

        .delete-file {
            background: #ff6b6b;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 12px;
            flex-shrink: 0;
        }

        @media (max-width: 1024px) {
            main.content-studio-main {
                padding: var(--spacing-lg);
            }

            .studio-hero-inner,
            .studio-layout {
                grid-template-columns: 1fr;
                display: flex;
                flex-direction: column;
            }

            .studio-hero-badges {
                width: 100%;
                flex-direction: row;
                flex-wrap: wrap;
                align-items: flex-start;
            }

            .status-grid,
            .upload-hints {
                grid-template-columns: 1fr;
            }

            .validation-card {
                position: static;
                top: auto;
            }
        }

        @media (max-width: 720px) {
            main.content-studio-main {
                padding: var(--spacing-md);
            }

            .studio-hero {
                padding: var(--spacing-2xl);
                border-radius: 22px;
            }

            .studio-title h1 {
                font-size: 28px;
            }

            .studio-title h5 {
                font-size: 13px;
            }

            .panel-hero {
                padding: 18px;
                flex-direction: column;
            }

            .panel-hero h2 {
                font-size: 19px;
            }

            .upload-panel {
                padding: 18px;
            }

            .notification-modal-card {
                border-radius: 20px;
            }

            .notification-modal-header {
                padding: 18px 18px 10px;
            }

            .notification-modal-message {
                padding: 0 18px 16px 70px;
            }

            .notification-modal-footer {
                padding: 0 18px 18px;
            }

            .validation-card {
                border-radius: 22px;
                padding: 18px;
            }

            .dropzone {
                min-height: 240px;
                padding: 18px;
            }
        }

        /* Premium SweetAlert2 overrides */
        .swal2-popup {
            border-radius: 24px !important;
            padding: 36px 30px !important;
            font-family: inherit !important;
            box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.15) !important;
        }

        .swal2-title {
            color: #1e293b !important;
            font-size: 22px !important;
            font-weight: 800 !important;
            margin-top: 15px !important;
        }

        .swal2-html-container {
            color: #475569 !important;
            font-size: 14.5px !important;
            line-height: 1.6 !important;
        }

        .swal2-icon {
            border-width: 2px !important;
            margin: 0 auto 10px auto !important;
        }

        .swal2-styled.swal2-confirm {
            border-radius: 12px !important;
            padding: 12px 28px !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            letter-spacing: 0.02em !important;
            box-shadow: none !important;
            transition: all 0.2s !important;
        }

        .swal2-styled.swal2-confirm:hover {
            transform: translateY(-1px) !important;
        }

        .swal2-styled.swal2-cancel {
            border-radius: 12px !important;
            padding: 12px 28px !important;
            font-weight: 700 !important;
            font-size: 14px !important;
            letter-spacing: 0.02em !important;
            box-shadow: none !important;
            transition: all 0.2s !important;
        }

        .swal2-styled.swal2-cancel:hover {
            transform: translateY(-1px) !important;
        }
    </style>
@endpush

@section('content')
    <main class="content-studio-main">
        <div class="studio-page">
            <section class="studio-hero">
                <div class="studio-hero-inner">
                    <div class="studio-title">
                        <a class="hero-chip" href="{{ route('trainer.events.show', $event->id) }}"
                            style="text-decoration:none;">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <span class="badge-top">
                            <i class="bi bi-folder2-open"></i>
                            <span>EVENT STUDIO • MATERIAL MANAGEMENT</span>
                        </span>
                        <h1>{{ $event->title }}</h1>
                        <h5>Kelola materi event dalam satu ruang kerja yang rapi. Upload file, pantau status, dan pastikan
                            admin menerima materi yang tepat.</h5>
                    </div>
                </div>
            </section>

            @if($showEventRejectionReason)
                <section class="status-banner" aria-label="Alasan revisi materi event" style="margin-bottom: 24px;">
                    <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
                    <div>
                        <p class="label">Alasan Revisi dari Admin</p>
                        <p class="reason">{{ $eventRejectionReason }}</p>
                    </div>
                </section>
            @endif

            @if(!empty($draftModules))
                <div class="draft-warning-banner" style="margin-bottom: 24px; padding: 16px 20px; background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border: 1px solid #f59e0b; border-radius: 16px; display: flex; align-items: center; justify-content: space-between; gap: 16px; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.08);">
                    <div style="display: flex; align-items: center; gap: 12px; text-align: left;">
                        <span style="font-size: 24px; animation: bounce 1.5s infinite; display: inline-block;">⚠️</span>
                        <div>
                            <h4 style="margin: 0 0 4px 0; color: #78350f; font-size: 15px; font-weight: 700;">Materi Belum Terkirim ke Admin!</h4>
                            <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.4;">
                                Anda memiliki <strong>{{ count($draftModules) }} berkas di draf</strong>. Klik tombol <strong>Kirim ke Admin</strong> di sebelah kanan draf agar materi dapat ditinjau oleh Admin.
                            </p>
                        </div>
                    </div>
                    <button type="button" class="primary-btn btn-trigger-submit-final" style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); color: #fff; padding: 10px 18px; border-radius: 10px; font-size: 12px; font-weight: 700; white-space: nowrap; border: none; cursor: pointer; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.2);">
                        Kirim Sekarang <i class="bi bi-send-fill" style="color:#fff;"></i>
                    </button>
                </div>
            @endif

            <section class="studio-layout">
                <!-- Left Column: Studio Panel -->
                <div class="studio-panel">
                    <div class="panel-hero">
                        <div>
                            <h2>{{ $uploadModeLabel }}</h2>
                            <p>
                                @if($isRejected)
                                    Materi sebelumnya ditolak admin. Silangan unggah file pengganti untuk revisi agar bisa
                                    direview ulang.
                                @else
                                    Gunakan area ini untuk mengirim file materi final yang akan direview admin sebelum event
                                    berjalan.
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- UX STEPPER GUIDE -->
                    <div class="ux-stepper" style="display: flex; justify-content: space-between; align-items: center; padding: 20px 32px; background: #faf8fc; border-bottom: 1px solid #e9e3f4; gap: 12px; flex-wrap: wrap; text-align: left;">
                        <!-- Step 1 -->
                        <div class="step-item" style="display: flex; align-items: center; gap: 8px;">
                            @if($step1State === 'completed')
                                <span style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #10b981; color: #fff; font-size: 14px; font-weight: 700;"><i class="bi bi-check"></i></span>
                                <span style="font-size: 13.5px; font-weight: 700; color: #10b981;">Unggah Materi</span>
                            @else
                                <span style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #51376c; color: #fff; font-size: 12px; font-weight: 700;">1</span>
                                <span style="font-size: 13.5px; font-weight: 700; color: #51376c;">Unggah Materi</span>
                            @endif
                        </div>
                        <div style="height: 2px; background: {{ $step2State === 'completed' || $step2State === 'active' ? '#51376c' : '#e2e8f0' }}; flex: 1; min-width: 20px;" class="step-line"></div>
                        
                        <!-- Step 2 -->
                        <div class="step-item" style="display: flex; align-items: center; gap: 8px;">
                            @if($step2State === 'completed')
                                <span style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #10b981; color: #fff; font-size: 14px; font-weight: 700;"><i class="bi bi-check"></i></span>
                                <span style="font-size: 13.5px; font-weight: 700; color: #10b981;">Simpan di Draf</span>
                            @elseif($step2State === 'active')
                                <span style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #51376c; color: #fff; font-size: 12px; font-weight: 700;">2</span>
                                <span style="font-size: 13.5px; font-weight: 700; color: #51376c;">Simpan di Draf</span>
                            @else
                                <span style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #cbd5e1; color: #fff; font-size: 12px; font-weight: 700;">2</span>
                                <span style="font-size: 13.5px; font-weight: 600; color: #64748b;">Simpan di Draf</span>
                            @endif
                        </div>
                        <div style="height: 2px; background: {{ $step3State === 'completed' || $step3State === 'active' ? '#10b981' : '#e2e8f0' }}; flex: 1; min-width: 20px;" class="step-line"></div>
                        
                        <!-- Step 3 -->
                        <div class="step-item" style="display: flex; align-items: center; gap: 8px;">
                            @if($step3State === 'completed')
                                <span style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #10b981; color: #fff; font-size: 14px; font-weight: 700;"><i class="bi bi-check-all"></i></span>
                                <span style="font-size: 13.5px; font-weight: 700; color: #10b981;">Materi Disetujui</span>
                            @elseif($step3State === 'active')
                                <span style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #f59e0b; color: #fff; font-size: 12px; font-weight: 700; animation: pulse 1.5s infinite;"><i class="bi bi-hourglass-split"></i></span>
                                <span style="font-size: 13.5px; font-weight: 700; color: #d97706;">Menunggu Review</span>
                            @elseif($step3State === 'rejected')
                                <span style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #ef4444; color: #fff; font-size: 14px; font-weight: 700;"><i class="bi bi-x"></i></span>
                                <span style="font-size: 13.5px; font-weight: 700; color: #ef4444;">Perlu Revisi</span>
                            @else
                                <span style="display: flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 50%; background: #cbd5e1; color: #fff; font-size: 12px; font-weight: 700;">3</span>
                                <span style="font-size: 13.5px; font-weight: 600; color: #64748b;">Kirim ke Admin</span>
                            @endif
                        </div>
                    </div>

                    <div class="upload-panel">
                        @if($displayMaterialStatus === 'pending_review' || $displayMaterialStatus === 'approved')
                            <div class="upload-lock-note" style="margin-bottom: 24px;">
                                <div class="icon" style="display: flex; align-items: center; justify-content: center; background: rgba(81, 55, 108, 0.1); color: #51376c;">
                                    <i class="bi {{ $displayMaterialStatus === 'approved' ? 'bi-shield-check' : 'bi-shield-lock' }}"></i>
                                </div>
                                <div>
                                    @if($displayMaterialStatus === 'approved')
                                        <h3>Materi telah disetujui</h3>
                                        <p>Materi Anda telah disetujui oleh admin. Anda masih dapat mengunggah berkas baru atau mengubah materi jika diperlukan.</p>
                                    @else
                                        <h3>Materi sedang direview</h3>
                                        <p>Seluruh materi Anda telah dikirim dan sedang diperiksa oleh admin. Anda masih dapat mengunggah berkas baru atau mengubah materi jika diperlukan.</p>
                                    @endif
                                </div>
                            </div>
                        @endif

{{-- UNIFIED DRAFT CARD (DRAFT ONLY) --}}
                        @if(!empty($draftModules))
                            <div class="unified-draft-card"
                                style="padding: 24px; background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; margin-bottom: 24px; box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05); position: relative; overflow: hidden;">
                                <!-- Top accent border line -->
                                <div
                                    style="position: absolute; top: 0; left: 0; right: 0; height: 4px; background: linear-gradient(90deg, #51376c, #3f2a54); font-size: 0;">
                                </div>

                                <!-- Count / Header -->
                                <div
                                    style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 16px;">
                                    <span
                                        style="font-size: 16px; font-weight: 700; color: #1e293b; display: flex; align-items: center; gap: 8px;">
                                        <i class="bi bi-file-earmark-arrow-up" style="color: #51376c; font-size: 20px;"></i>
                                        {{ count($draftModules) }} File Tersimpan
                                    </span>
                                </div>

                                <!-- Checklist of files -->
                                <div class="draft-checklist"
                                    style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                                    @foreach($draftModules as $index => $draft)
                                        <div class="draft-checklist-item-wrapper"
                                            style="padding: 14px; background: #faf8fc; border: 1px solid #e9e3f4; border-radius: 16px; display: flex; flex-direction: column; gap: 10px;">
                                            <div class="draft-checklist-item"
                                                style="display: flex; align-items: center; justify-content: space-between; gap: 12px;">
                                                <div
                                                    style="display: flex; align-items: center; gap: 10px; min-width: 0; flex: 1; text-align: left;">
                                                    <i class="bi bi-check-lg"
                                                        style="color: #10b981; font-size: 18px; font-weight: 900; flex-shrink: 0;"></i>
                                                    @if(str_starts_with($draft['original_name'], 'Link: '))
                                                        @php
                                                            $linkUrl = str_replace('Link: ', '', $draft['original_name']);
                                                        @endphp
                                                        <span
                                                            style="font-size: 14px; font-weight: 600; color: #2e2050; word-break: break-all;">
                                                            Materi Link: <a href="{{ $linkUrl }}" target="_blank"
                                                                rel="noopener noreferrer"
                                                                style="color: #51376c; text-decoration: underline;">{{ $linkUrl }}</a>
                                                        </span>
                                                    @else
                                                        <span
                                                            style="font-size: 14px; font-weight: 600; color: #2e2050; word-break: break-all;">{{ $draft['original_name'] }}</span>
                                                    @endif
                                                    @if(!empty($draft['size']) && $draft['size'] > 0)
                                                        <span
                                                            style="font-size: 12px; color: #7c6a9f; white-space: nowrap;">({{ round($draft['size'] / 1024, 2) }}
                                                            KB)</span>
                                                    @endif
                                                </div>
                                                @if($canUploadMaterials)
                                                    <button type="button" class="btn-delete-draft" data-index="{{ $index }}"
                                                        style="background: none; border: none; color: #ef4444; font-size: 16px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 6px; border-radius: 8px; transition: all 0.2s;"
                                                        onmouseover="this.style.backgroundColor='#fee2e2'"
                                                        onmouseout="this.style.backgroundColor='transparent'"
                                                        title="Hapus Draf">
                                                        <i class="bi bi-trash3-fill"></i>
                                                    </button>
                                                @endif
                                            </div>

                                            <!-- Survey Link Input for this draft (Optional) -->
                                            <div style="display: flex; flex-direction: column; gap: 4px; text-align: left;">
                                                <label
                                                    style="font-size: 11px; font-weight: 700; color: #7c6a9f; text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 4px;">
                                                    <i class="bi bi-link-45deg" style="font-size: 14px;"></i> Link Survei Kepuasan
                                                    <span
                                                        style="font-weight: 500; color: #94a3b8; text-transform: none; font-style: italic;">(Opsional)</span>
                                                </label>
                                                <input type="text" class="survey-link-input" data-index="{{ $index }}"
                                                    value="{{ $draft['survey_link'] ?? '' }}"
                                                    placeholder="Masukkan URL kuisioner/survei kepuasan (misal: Google Form)"
                                                    style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 12px; color: #1e293b; background: #fff; transition: all 0.2s;"
                                                    onfocus="this.style.borderColor='#51376c'; this.style.boxShadow='0 0 0 3px rgba(81, 55, 108, 0.1)';"
                                                    onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none'; updateDraftSurveyLink({{ $index }}, this.value);" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Status & Info -->
                                <div style="border-top: 1px solid #f1f5f9; padding-top: 16px; margin-bottom: 20px;">
                                    <div
                                        style="font-size: 14px; font-weight: 700; color: #475569; display: flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                                        Status : <span
                                            style="background: #f3effa; color: #51376c; padding: 2px 8px; border-radius: 6px; font-size: 11px; font-weight: 800; border: 1px solid #e9e3f4; letter-spacing: 0.05em;">{{ $isRejected ? 'REVISI' : 'DRAFT' }}</span>
                                    </div>
                                    <p
                                        style="margin: 0; font-size: 13px; color: #64748b; font-style: italic; display: flex; align-items: center; gap: 6px; text-align: left;">
                                        <i class="bi bi-info-circle-fill" style="color: #64748b; font-size: 14px;"></i>
                                        {{ $isRejected ? 'Materi yang ditolak bisa diganti dengan file revisi sebelum dikirim ulang.' : 'Materi dapat ditambah atau dihapus sebelum dikirim.' }}
                                    </p>
                                </div>

                                <!-- Submit Action Button -->
                                <button type="button" id="btnSubmitFinal" class="primary-btn"
                                    style="width: 100%; justify-content: center; background: linear-gradient(135deg, #51376c 0%, #3f2a54 100%); color: #fff; border: none; cursor: pointer; padding: 14px; border-radius: 14px; font-size: 14px; font-weight: 700; letter-spacing: 0.04em; transition: all 0.2s; box-shadow: 0 4px 12px rgba(81, 55, 108, 0.2);"
                                    onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(81, 55, 108, 0.3)';"
                                    onmouseout="this.style.transform='none'; this.style.boxShadow='0 4px 12px rgba(81, 55, 108, 0.2)';">
                                    {{ $isRejected ? 'Kirim File Revisi ke Admin' : 'Submit ke Admin untuk Review' }} <i
                                        class="bi bi-send-fill" style="color: #fff; margin-left: 8px; font-size: 13px;"></i>
                                </button>
                            </div>
                        @endif

                        {{-- SUBMITTED FILES HISTORY (HISTORY ONLY) --}}
                        @if($myModules->isNotEmpty())
                            <div class="submitted-files-history"
                                style="margin-bottom: 24px; padding: 20px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 20px;">
                                <h3
                                    style="font-size: 15px; font-weight: 700; color: #334155; margin: 0 0 14px 0; display: flex; align-items: center; gap: 8px;">
                                    <i class="bi bi-clock-history" style="color: #64748b; font-size: 18px;"></i>
                                    Riwayat Pengiriman
                                </h3>
                                <div style="display: flex; flex-direction: column; gap: 10px;">
                                    @foreach($myModules as $module)
                                        @php
                                            $historyViewUrl = $resolveModuleViewUrl($module);
                                            $historyLabel = $resolveModuleLabel($module);
                                            $historyIsLink = str_starts_with((string) ($module->original_name ?? ''), 'Link: ')
                                                || preg_match('#^https?://#i', (string) ($module->path ?? ''));
                                            $badgeStyle = match ($module->status) {
                                                'approved' => 'background: #dcfce7; border-color: #bbf7d0; color: #166534;',
                                                'rejected' => 'background: #fee2e2; border-color: #fecaca; color: #991b1b;',
                                                default => 'background: #f3effa; border-color: #e9e3f4; color: #51376c;',
                                            };
                                            $statusLabel = match ($module->status) {
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Perlu Revisi',
                                                default => 'Menunggu Review',
                                            };
                                            $icon = match ($module->status) {
                                                'approved' => 'bi-file-earmark-check-fill',
                                                'rejected' => 'bi-file-earmark-x-fill',
                                                default => 'bi-file-earmark-arrow-up-fill',
                                            };
                                            $iconColor = match ($module->status) {
                                                'approved' => '#16a34a',
                                                'rejected' => '#dc2626',
                                                default => '#51376c',
                                            };
                                        @endphp
                                        <div class="file-item-row"
                                            style="display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 12px; gap: 12px;">
                                            <div
                                                style="display: flex; align-items: center; gap: 12px; min-width: 0; flex: 1; text-align: left;">
                                                <i class="bi {{ $icon }}"
                                                    style="font-size: 22px; color: {{ $iconColor }}; flex-shrink: 0;"></i>
                                                <div style="min-width: 0;">
                                                    @if($historyViewUrl)
                                                        <a href="{{ $historyViewUrl }}" target="_blank" rel="noopener noreferrer"
                                                            style="margin: 0; font-size: 14px; font-weight: 600; color: #51376c; word-break: break-all; text-decoration: underline; display: inline-block;">{{ $historyLabel }}</a>
                                                    @else
                                                        <p
                                                            style="margin: 0; font-size: 14px; font-weight: 600; color: var(--main-navy-clr); word-break: break-all;">
                                                            {{ $historyLabel }}</p>
                                                    @endif
                                                    <p style="margin: 0; font-size: 12px; color: #64748b;">
                                                        Terkirim pada {{ $module->created_at->format('d M Y H:i') }}
                                                    </p>
                                                    @if(!empty($module->survey_link))
                                                        <p
                                                            style="margin: 4px 0 0 0; font-size: 12px; color: #51376c; font-weight: 600;">
                                                            <i class="bi bi-link-45deg"></i> Link Survei:
                                                            <a href="{{ $module->survey_link }}" target="_blank"
                                                                rel="noopener noreferrer"
                                                                style="color: #51376c; text-decoration: underline;">{{ $module->survey_link }}</a>
                                                        </p>
                                                    @endif
                                                    @if($module->status === 'rejected' && !empty($module->rejection_reason))
                                                        <p
                                                            style="margin: 4px 0 0 0; font-size: 12px; color: #dc2626; font-style: italic; background: #fff5f5; padding: 6px 10px; border-radius: 6px; border-left: 3px solid #ef4444;">
                                                            Catatan Admin: {{ $module->rejection_reason }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                                <button type="button" class="btn-delete-submitted" data-id="{{ $module->id }}"
                                                    style="background: none; border: none; color: #ef4444; font-size: 16px; cursor: pointer; display: inline-flex; align-items: center; justify-content: center; padding: 6px; border-radius: 8px; transition: all 0.2s;"
                                                    onmouseover="this.style.backgroundColor='#fee2e2'"
                                                    onmouseout="this.style.backgroundColor='transparent'"
                                                    title="Hapus Materi">
                                                    <i class="bi bi-trash3-fill"></i>
                                                </button>
                                                @if($module->status === 'rejected')
                                                    <button type="button" class="btn-replace-rejected primary-btn" data-id="{{ $module->id }}" data-is-link="{{ $historyIsLink ? '1' : '0' }}"
                                                        style="font-size: 11px; font-weight: 700; padding: 6px 10px; border-radius: 8px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important; color: #fff; border: none; cursor: pointer; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s;">
                                                        <i class="bi bi-arrow-left-right"></i> Ganti
                                                    </button>
                                                @endif
                                                @if($historyViewUrl)
                                                    <a href="{{ $historyViewUrl }}" target="_blank" rel="noopener noreferrer"
                                                        style="font-size: 11px; font-weight: 700; padding: 6px 10px; border-radius: 8px; background: #3f2a54; color: #fff; text-decoration: none; white-space: nowrap;">
                                                        <i class="bi bi-{{ $historyIsLink ? 'link-45deg' : 'eye' }}"></i>
                                                        {{ $historyIsLink ? 'Buka' : 'Lihat' }}
                                                    </a>
                                                @endif
                                                <span
                                                    style="font-size: 11px; font-weight: 800; padding: 4px 10px; border-radius: 8px; border: 1px solid; white-space: nowrap; {{ $badgeStyle }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($canUploadMaterials)
                            <div class="upload-hints">
                                <div class="hint-card">
                                    <div class="icon"><i class="bi bi-file-earmark-arrow-up"></i></div>
                                    <div>
                                        <p class="meta-label">Status upload</p>
                                        <p class="meta-text">Tarik file ke area dropzone atau klik untuk pilih dari perangkat.
                                        </p>
                                    </div>
                                </div>
                                <div class="hint-card">
                                    <div class="icon"><i class="bi bi-clock-history"></i></div>
                                    <div>
                                        <p class="meta-label">Alur</p>
                                        <p class="meta-text">File akan masuk ke daftar materi dan dikirim untuk audit admin.</p>
                                    </div>
                                </div>
                                <div class="hint-card">
                                    <div class="icon"><i class="bi bi-lightning-charge"></i></div>
                                    <div>
                                        <p class="meta-label">Catatan</p>
                                        <p class="meta-text">Pastikan nama file jelas agar mudah diverifikasi tim admin.</p>
                                    </div>
                                </div>
                            </div>

                            <input type="file" id="replaceFileInput" accept=".pdf,.mp4,.pptx,.ppt,.docx,.doc" style="display: none;" />

                            <form id="moduleForm" class="module-form"
                                action="{{ route('trainer.events.studio.upload', $event->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="eventId" value="{{ $event->id }}">
                                <input type="file" id="fileInput" accept=".pdf,.mp4,.pptx,.ppt,.docx,.doc" name="files[]"
                                    style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0;" />

                                <div class="dropzone" id="dropzone">
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <h2>Tarik Aset Acara ke Sini</h2>
                                    <p>DUKUNGAN: PDF, MP4, PPTX, DOCX</p>
                                    <p style="font-size: 12px; color: #999; margin-top: 8px">
                                        atau klik untuk memilih 1 file materi
                                    </p>
                                </div>

                                <div style="margin-top: 20px; text-align: left;">
                                    <label for="materialLink"
                                        style="font-size: 13px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Link Materi Lainnya <span
                                            style="font-weight: 500; color: #94a3b8; text-transform: none; font-style: italic;">(Opsional)</span>
                                    </label>
                                    <input type="text" id="materialLink" name="material_link"
                                        placeholder="Contoh: https://drive.google.com/... atau https://youtube.com/..."
                                        style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; color: #1e293b; background: #fff; transition: all 0.2s;"
                                        onfocus="this.style.borderColor='#51376c'; this.style.boxShadow='0 0 0 3px rgba(81, 55, 108, 0.1)';"
                                        onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';" />
                                </div>

                                <div style="margin-top: 12px; text-align: left;">
                                    <label for="materialSurveyLink"
                                        style="font-size: 13px; font-weight: 700; color: #475569; display: block; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.05em;">
                                        Link Survei Kepuasan <span
                                            style="font-weight: 500; color: #94a3b8; text-transform: none; font-style: italic;">(Opsional)</span>
                                    </label>
                                    <input type="text" id="materialSurveyLink" name="material_survey_link"
                                        placeholder="Contoh: https://forms.gle/... (Untuk berkas / link materi di atas)"
                                        style="width: 100%; padding: 12px 16px; border: 1px solid #cbd5e1; border-radius: 12px; font-size: 14px; color: #1e293b; background: #fff; transition: all 0.2s;"
                                        onfocus="this.style.borderColor='#51376c'; this.style.boxShadow='0 0 0 3px rgba(81, 55, 108, 0.1)';"
                                        onblur="this.style.borderColor='#cbd5e1'; this.style.boxShadow='none';" />
                                </div>

                                <div id="fileList" class="file-list" style="display: none">
                                    <h3>Materi yang Diunggah</h3>
                                    <ul id="uploadedFiles" style="list-style: none; padding: 0; margin: 0"></ul>
                                </div>

                                <div class="panel-footer">
                                    <button type="submit" class="primary-btn" id="submitBtn">
                                        {{ $submitDraftLabel }} <i class="bi bi-plus-circle"
                                            style="color: #e9d5ff; margin-left: 8px;"></i>
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Status Grid -->
                <aside class="status-grid" aria-label="Ringkasan studio event">
                    <article class="status-card">
                        <div class="icon"><i class="bi bi-calendar-event"></i></div>
                        <div>
                            <p class="label">{{ $deadlineLabel }}</p>
                            <p class="value">{{ $deadline ? $deadline->format('d M Y H:i') : 'Tidak ada tenggat' }}</p>
                            <p class="hint">{{ $deadlineHint }}</p>
                        </div>
                    </article>

                    <article class="status-card">
                        <div class="icon"
                            style="background: linear-gradient(135deg, #f3effa 0%, #e9e3f4 100%); color: #51376c;"><i
                                class="bi bi-file-earmark-check"></i></div>
                        <div>
                            <p class="label">Status</p>
                            @if($displayMaterialStatus !== 'draft')
                                @php
                                    $badgeClass = match ($displayMaterialStatus) {
                                        'approved' => 'approved',
                                        'rejected' => 'rejected',
                                        'pending_review' => 'pending',
                                        'not_uploaded' => 'not-submitted',
                                        default => 'pending',
                                    };
                                    $statusIcon = match ($displayMaterialStatus) {
                                        'approved' => '✓',
                                        'rejected' => '✕',
                                        'pending_review' => '⏳',
                                        'not_uploaded' => '—',
                                        default => '📋',
                                    };
                                    $badgeLabel = match ($displayMaterialStatus) {
                                        'approved' => 'Materi Disetujui',
                                        'rejected' => 'Perlu Revisi',
                                        'pending_review' => 'Sedang Direview',
                                        'not_uploaded' => 'Belum Dikirim',
                                        default => 'Terkirim',
                                    };
                                @endphp
                                <span class="status-badge {{ $badgeClass }}">
                                    <span>{{ $statusIcon }}</span>
                                    <span>{{ $badgeLabel }}</span>
                                </span>
                            @else
                                <p class="value">{{ $materialStatusLabel }}</p>
                            @endif
                            @if($displayMaterialStatus === 'draft')
                                <p class="hint">Belum upload materi. Status review akan muncul setelah file berhasil dikirim.
                                </p>
                            @elseif($displayMaterialStatus === 'approved')
                                <p class="hint">Materi Anda telah disetujui dan siap ditampilkan di event.</p>
                            @elseif($displayMaterialStatus === 'rejected')
                                <p class="hint">Silakan upload ulang dengan revisi yang diperlukan admin.</p>
                            @elseif($displayMaterialStatus === 'pending_review')
                                <p class="hint">Admin sedang mereview materi Anda. Harap menunggu notifikasi.</p>
                            @endif
                        </div>
                    </article>

                    <article class="status-card">
                        <div class="icon"
                            style="background: linear-gradient(135deg, #f3effa 0%, #e9e3f4 100%); color: #51376c;"><i
                                class="bi bi-info-circle"></i></div>
                        <div>
                            <p class="label">Format</p>
                            <p class="value">PDF, MP4, PPTX, DOCX</p>
                            <p class="hint">Gunakan format ini agar proses audit lebih cepat.</p>
                        </div>
                    </article>
                </aside>
            </section>
        </div>
    </main>

    <div id="notificationModal" class="notification-modal" aria-hidden="true">
        <div class="notification-modal-card" role="dialog" aria-modal="true" aria-labelledby="notificationModalTitle">
            <div class="notification-modal-header">
                <div id="notificationModalIcon" class="notification-modal-icon">
                    <i class="bi bi-info-circle" style="font-size: 22px;"></i>
                </div>
                <div>
                    <h3 id="notificationModalTitle" class="notification-modal-title">Informasi</h3>
                </div>
            </div>
            <p id="notificationModalMessage" class="notification-modal-message"></p>
            <div class="notification-modal-footer">
                <button id="notificationModalCloseBtn" type="button" class="notification-modal-close">Tutup</button>
            </div>
        </div>
    </div>

    @if($canUploadMaterials)
        <script>
            let uploadedFiles = [];

            function showNotificationModal(title, message, type = 'info') {
                const modal = document.getElementById('notificationModal');
                const icon = document.getElementById('notificationModalIcon');
                const titleEl = document.getElementById('notificationModalTitle');
                const messageEl = document.getElementById('notificationModalMessage');

                if (!modal || !icon || !titleEl || !messageEl) {
                    return;
                }

                icon.className = 'notification-modal-icon';
                const iconEl = icon.querySelector('i');
                if (iconEl) {
                    iconEl.className = 'bi';
                }

                if (type === 'success') {
                    icon.classList.add('success');
                    if (iconEl) iconEl.classList.add('bi-check-circle-fill');
                } else if (type === 'error') {
                    icon.classList.add('error');
                    if (iconEl) iconEl.classList.add('bi-x-circle-fill');
                } else if (type === 'warning') {
                    icon.classList.add('warning');
                    if (iconEl) iconEl.classList.add('bi-exclamation-triangle-fill');
                } else {
                    if (iconEl) iconEl.classList.add('bi-info-circle-fill');
                }

                titleEl.textContent = title;
                messageEl.textContent = message;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
            }

            function closeNotificationModal() {
                const modal = document.getElementById('notificationModal');
                if (!modal) return;
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            }

            document.addEventListener("DOMContentLoaded", function () {
                const notificationModal = document.getElementById('notificationModal');
                const notificationModalCloseBtn = document.getElementById('notificationModalCloseBtn');
                const dropzone = document.getElementById("dropzone");
                const fileInput = document.getElementById("fileInput");
                const fileList = document.getElementById("fileList");
                const uploadedFilesList = document.getElementById("uploadedFiles");
                const moduleForm = document.getElementById("moduleForm");
                const submitBtn = document.getElementById("submitBtn");

                if (notificationModalCloseBtn) {
                    notificationModalCloseBtn.addEventListener('click', closeNotificationModal);
                }

                if (notificationModal) {
                    notificationModal.addEventListener('click', (e) => {
                        if (e.target === notificationModal) {
                            closeNotificationModal();
                        }
                    });
                }

                if (!dropzone || !fileInput || !moduleForm || !submitBtn) return;

                dropzone.addEventListener("click", () => {
                    if (!fileInput.disabled) fileInput.click();
                });

                dropzone.addEventListener("dragover", (e) => {
                    e.preventDefault();
                    dropzone.style.borderColor = "#3c2957";
                    dropzone.style.backgroundColor = "#e0e7ff";
                });

                dropzone.addEventListener("dragleave", () => {
                    dropzone.style.borderColor = "#dfe6f2";
                    dropzone.style.backgroundColor = "#f8fafc";
                });

                dropzone.addEventListener("drop", (e) => {
                    e.preventDefault();
                    dropzone.style.borderColor = "#dfe6f2";
                    dropzone.style.backgroundColor = "#f8fafc";
                    handleFiles(e.dataTransfer.files);
                });

                fileInput.addEventListener("change", (e) => handleFiles(e.target.files));

                function handleFiles(files) {
                    const picked = Array.from(files);
                    if (picked.length === 0) return;

                    uploadedFiles = [picked[0]];
                    updateFileList();
                    fileInput.value = '';
                }

                function updateFileList() {
                    if (uploadedFiles.length > 0) {
                        fileList.style.display = "block";
                        uploadedFilesList.innerHTML = uploadedFiles.map((file, index) => `
                                                            <li>
                                                                <div class="file-meta">
                                                                    <i class="bi bi-file-earmark"></i>
                                                                    <div>
                                                                        <p class="file-name">${file.name}</p>
                                                                        <p class="file-size">${(file.size / 1024).toFixed(2)} KB</p>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="delete-file" data-index="${index}">HAPUS</button>
                                                            </li>
                                                        `).join("");

                        uploadedFilesList.querySelectorAll(".delete-file").forEach(btn => {
                            btn.addEventListener("click", (e) => {
                                const index = parseInt(e.currentTarget.dataset.index, 10);
                                uploadedFiles.splice(index, 1);
                                updateFileList();
                            });
                        });
                    } else {
                        fileList.style.display = "none";
                        uploadedFilesList.innerHTML = "";
                    }
                }

                moduleForm.addEventListener("submit", (e) => {
                    e.preventDefault();

                    const linkInput = document.getElementById("materialLink");
                    const hasLink = linkInput && linkInput.value.trim() !== "";

                    if (uploadedFiles.length === 0 && !hasLink) {
                        Swal.fire({
                            icon: 'warning',
                            iconColor: '#51376c',
                            title: 'Perhatian',
                            text: 'Silakan pilih file atau masukkan link materi terlebih dahulu.',
                            customClass: {
                                popup: 'premium-swal-popup',
                                title: 'premium-swal-title',
                                confirmButton: 'premium-swal-confirm-btn'
                            },
                            buttonsStyling: false
                        });
                        return;
                    }

                    // Only validate extensions if a file is uploaded
                    if (uploadedFiles.length > 0) {
                        const allowedExt = ['pdf', 'mp4', 'pptx', 'ppt', 'docx', 'doc'];
                        const invalidFiles = uploadedFiles.filter((file) => {
                            const ext = (file.name.split('.').pop() || '').toLowerCase();
                            return !allowedExt.includes(ext);
                        });

                        if (invalidFiles.length > 0) {
                            const names = invalidFiles.map((f) => f.name).join(', ');
                            Swal.fire({
                                icon: 'error',
                                iconColor: '#ef4444',
                                title: 'Berkas Tidak Valid',
                                text: 'File tidak valid: ' + names + '. Hanya PDF, MP4, PPTX, DOCX yang diizinkan.',
                                customClass: {
                                    popup: 'premium-swal-popup',
                                    title: 'premium-swal-title',
                                    confirmButton: 'premium-swal-confirm-btn'
                                },
                                buttonsStyling: false
                            });
                            return;
                        }
                    }

                    const formData = new FormData(moduleForm);
                    uploadedFiles.forEach(file => {
                        formData.append('files[]', file);
                    });

                    const originalBtnText = submitBtn.innerHTML;
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...';

                    fetch(moduleForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(async (response) => {
                            const text = await response.text();
                            let data = {};
                            try { data = JSON.parse(text); } catch (e) {
                                throw new Error('Server error: ' + text.substring(0, 200));
                            }
                            if (!response.ok || !data.success) {
                                const message = data.error || data.message || 'Gagal mengunggah materi event.';
                                throw new Error(message);
                            }

                            uploadedFiles = [];
                            updateFileList();
                            fileInput.value = '';
                            if (linkInput) linkInput.value = '';
                            const surveyLinkInput = document.getElementById("materialSurveyLink");
                            if (surveyLinkInput) surveyLinkInput.value = '';

                            Swal.fire({
                                html: `
                                            <div class="swal-custom-container">
                                                <div class="swal-icon-glow success">
                                                    <div class="swal-icon-circle">
                                                        <i class="bi bi-cloud-arrow-up-fill" style="color: #10b981;"></i>
                                                    </div>
                                                </div>
                                                <div class="swal-custom-body">
                                                    <h4 class="swal-custom-title">Materi Tersimpan!</h4>
                                                    <p class="swal-custom-subtitle">Materi Anda berhasil ditambahkan ke draf sementara.</p>
                                                    <div class="swal-warning-box">
                                                        <p class="warning-text" style="margin-bottom: 8px;">
                                                            Apakah Anda ingin <strong>langsung mengirimkan</strong> materi ini ke Admin untuk diperiksa?
                                                        </p>
                                                        <p class="warning-text" style="font-size: 12.5px; color: #64748b; margin: 0;">
                                                            Jika Anda masih ingin mengunggah file/link tambahan lainnya, pilih opsi <strong>Simpan sebagai Draf</strong>.
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        `,
                                showCancelButton: true,
                                confirmButtonText: '🚀 Ya, Kirim ke Admin Sekarang',
                                cancelButtonText: '📁 Simpan sebagai Draf (Tambah lagi)',
                                customClass: {
                                    popup: 'premium-swal-popup',
                                    confirmButton: 'premium-swal-confirm-btn',
                                    cancelButton: 'premium-swal-cancel-btn'
                                },
                                buttonsStyling: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    submitFinalDirectly();
                                } else {
                                    window.location.reload();
                                }
                            });
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                iconColor: '#ef4444',
                                title: 'Gagal',
                                text: 'Gagal: ' + (error.message || 'Terjadi kesalahan saat mengunggah materi event.'),
                                customClass: {
                                    popup: 'premium-swal-popup',
                                    title: 'premium-swal-title',
                                    confirmButton: 'premium-swal-confirm-btn'
                                },
                                buttonsStyling: false
                            });
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        });
                });

                // delete draft handler
                document.querySelectorAll('.btn-delete-draft').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const index = e.currentTarget.dataset.index;

                        Swal.fire({
                            title: 'Hapus Draf?',
                            text: 'Apakah Anda yakin ingin menghapus file draf ini?',
                            icon: 'warning',
                            iconColor: '#ef4444',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'premium-swal-popup',
                                title: 'premium-swal-title',
                                confirmButton: 'premium-swal-confirm-btn danger',
                                cancelButton: 'premium-swal-cancel-btn'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Menghapus...',
                                    allowOutsideClick: false,
                                    didOpen: () => { Swal.showLoading(); },
                                    customClass: {
                                        popup: 'premium-swal-popup',
                                        title: 'premium-swal-title'
                                    }
                                });

                                const formData = new FormData();
                                formData.append('_token', '{{ csrf_token() }}');
                                formData.append('action', 'delete_draft');
                                formData.append('index', index);

                                fetch('{{ route('trainer.events.studio.upload', $event->id) }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                iconColor: '#10b981',
                                                title: 'Berhasil',
                                                text: data.message,
                                                showConfirmButton: false,
                                                timer: 1200,
                                                customClass: {
                                                    popup: 'premium-swal-popup',
                                                    title: 'premium-swal-title'
                                                }
                                            });
                                            setTimeout(() => { window.location.reload(); }, 1200);
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                iconColor: '#ef4444',
                                                title: 'Gagal',
                                                text: data.error || 'Gagal menghapus draf.',
                                                customClass: {
                                                    popup: 'premium-swal-popup',
                                                    title: 'premium-swal-title',
                                                    confirmButton: 'premium-swal-confirm-btn'
                                                },
                                                buttonsStyling: false
                                            });
                                        }
                                    })
                                    .catch(err => {
                                        Swal.fire({
                                            icon: 'error',
                                            iconColor: '#ef4444',
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan sistem.',
                                            customClass: {
                                                popup: 'premium-swal-popup',
                                                title: 'premium-swal-title',
                                                confirmButton: 'premium-swal-confirm-btn'
                                            },
                                            buttonsStyling: false
                                        });
                                    });
                            }
                        });
                    });
                });

                // delete submitted handler
                document.querySelectorAll('.btn-delete-submitted').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const moduleId = e.currentTarget.dataset.id;

                        Swal.fire({
                            title: 'Hapus Materi?',
                            text: 'Apakah Anda yakin ingin menghapus materi yang sudah diupload ini? Tindakan ini akan memperbarui status review materi Anda.',
                            icon: 'warning',
                            iconColor: '#ef4444',
                            showCancelButton: true,
                            confirmButtonColor: '#ef4444',
                            cancelButtonColor: '#64748b',
                            confirmButtonText: 'Ya, Hapus',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'premium-swal-popup',
                                title: 'premium-swal-title',
                                confirmButton: 'premium-swal-confirm-btn danger',
                                cancelButton: 'premium-swal-cancel-btn'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Menghapus...',
                                    allowOutsideClick: false,
                                    didOpen: () => { Swal.showLoading(); },
                                    customClass: {
                                        popup: 'premium-swal-popup',
                                        title: 'premium-swal-title'
                                    }
                                });

                                const formData = new FormData();
                                formData.append('_token', '{{ csrf_token() }}');
                                formData.append('action', 'delete_module');
                                formData.append('module_id', moduleId);

                                fetch('{{ route('trainer.events.studio.upload', $event->id) }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                iconColor: '#10b981',
                                                title: 'Berhasil',
                                                text: data.message,
                                                showConfirmButton: false,
                                                timer: 1200,
                                                customClass: {
                                                    popup: 'premium-swal-popup',
                                                    title: 'premium-swal-title'
                                                }
                                            });
                                            setTimeout(() => { window.location.reload(); }, 1200);
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                iconColor: '#ef4444',
                                                title: 'Gagal',
                                                text: data.error || 'Gagal menghapus materi.',
                                                customClass: {
                                                    popup: 'premium-swal-popup',
                                                    title: 'premium-swal-title',
                                                    confirmButton: 'premium-swal-confirm-btn'
                                                },
                                                buttonsStyling: false
                                            });
                                        }
                                    })
                                    .catch(err => {
                                        Swal.fire({
                                            icon: 'error',
                                            iconColor: '#ef4444',
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan sistem.',
                                            customClass: {
                                                popup: 'premium-swal-popup',
                                                title: 'premium-swal-title',
                                                confirmButton: 'premium-swal-confirm-btn'
                                            },
                                            buttonsStyling: false
                                        });
                                    });
                            }
                        });
                    });
                });

                // submit final handler
                const btnSubmitFinal = document.getElementById('btnSubmitFinal');
                if (btnSubmitFinal) {
                    btnSubmitFinal.addEventListener('click', () => {
                        Swal.fire({
                            html: `
                                        <div class="swal-custom-container">
                                            <div class="swal-icon-glow warning">
                                                <div class="swal-icon-circle">
                                                    <i class="bi bi-send-check-fill"></i>
                                                </div>
                                            </div>
                                            <div class="swal-custom-body">
                                                <h4 class="swal-custom-title">Kirim Materi Final?</h4>
                                                <p class="swal-custom-subtitle">Pastikan semua berkas telah lengkap sebelum mengirim ke Admin.</p>
                                                <div class="swal-steps-box">
                                                    <div class="swal-step-item">
                                                        <div class="step-num"><i class="bi bi-file-earmark-lock2"></i></div>
                                                        <div class="step-content">
                                                            <strong>Kunci Akses Unggah</strong>
                                                            <p>Form unggah akan dikunci sementara selama proses review.</p>
                                                        </div>
                                                    </div>
                                                    <div class="swal-step-item">
                                                        <div class="step-num"><i class="bi bi-shield-shaded"></i></div>
                                                        <div class="step-content">
                                                            <strong>Verifikasi Admin</strong>
                                                            <p>Admin Trainer akan meninjau kelayakan dan kelengkapan materi Anda.</p>
                                                        </div>
                                                    </div>
                                                    <div class="swal-step-item">
                                                        <div class="step-num"><i class="bi bi-bell"></i></div>
                                                        <div class="step-content">
                                                            <strong>Notifikasi Status</strong>
                                                            <p>Anda akan mendapat pemberitahuan apakah materi disetujui atau perlu revisi.</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `,
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Kirim Sekarang',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'premium-swal-popup',
                                confirmButton: 'premium-swal-confirm-btn',
                                cancelButton: 'premium-swal-cancel-btn'
                            },
                            buttonsStyling: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Mengirim...',
                                    allowOutsideClick: false,
                                    didOpen: () => { Swal.showLoading(); },
                                    customClass: {
                                        popup: 'premium-swal-popup',
                                        title: 'premium-swal-title'
                                    }
                                });

                                const formData = new FormData();
                                formData.append('_token', '{{ csrf_token() }}');
                                formData.append('action', 'submit_final');

                                fetch('{{ route('trainer.events.studio.upload', $event->id) }}', {
                                    method: 'POST',
                                    body: formData,
                                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                icon: 'success',
                                                iconColor: '#10b981',
                                                title: 'Berhasil',
                                                text: data.message,
                                                showConfirmButton: false,
                                                timer: 1200,
                                                customClass: {
                                                    popup: 'premium-swal-popup',
                                                    title: 'premium-swal-title'
                                                }
                                            });
                                            setTimeout(() => { window.location.reload(); }, 1200);
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                iconColor: '#ef4444',
                                                title: 'Gagal',
                                                text: data.error || 'Gagal mengirim materi.',
                                                customClass: {
                                                    popup: 'premium-swal-popup',
                                                    title: 'premium-swal-title',
                                                    confirmButton: 'premium-swal-confirm-btn'
                                                },
                                                buttonsStyling: false
                                            });
                                        }
                                    })
                                    .catch(err => {
                                        Swal.fire({
                                            icon: 'error',
                                            iconColor: '#ef4444',
                                            title: 'Gagal',
                                            text: 'Terjadi kesalahan sistem.',
                                            customClass: {
                                                popup: 'premium-swal-popup',
                                                title: 'premium-swal-title',
                                                confirmButton: 'premium-swal-confirm-btn'
                                            },
                                            buttonsStyling: false
                                        });
                                    });
                            }
                        });
                    });
                }

                // banner trigger submit final handler
                const btnTriggerBanners = document.querySelectorAll('.btn-trigger-submit-final');
                btnTriggerBanners.forEach(btn => {
                    btn.addEventListener('click', () => {
                        if (btnSubmitFinal) {
                            btnSubmitFinal.click();
                        } else {
                            submitFinalDirectly();
                        }
                    });
                });

                function submitFinalDirectly() {
                    Swal.fire({
                        title: 'Mengirim...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        customClass: {
                            popup: 'premium-swal-popup',
                            title: 'premium-swal-title'
                        }
                    });

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('action', 'submit_final');

                    fetch('{{ route('trainer.events.studio.upload', $event->id) }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    iconColor: '#10b981',
                                    title: 'Berhasil',
                                    text: data.message,
                                    showConfirmButton: false,
                                    timer: 1200,
                                    customClass: {
                                        popup: 'premium-swal-popup',
                                        title: 'premium-swal-title'
                                    }
                                });
                                setTimeout(() => { window.location.reload(); }, 1200);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    iconColor: '#ef4444',
                                    title: 'Gagal',
                                    text: data.error || 'Gagal mengirim materi.',
                                    customClass: {
                                        popup: 'premium-swal-popup',
                                        title: 'premium-swal-title',
                                        confirmButton: 'premium-swal-confirm-btn'
                                    },
                                    buttonsStyling: false
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        })
                        .catch(err => {
                            Swal.fire({
                                icon: 'error',
                                iconColor: '#ef4444',
                                title: 'Gagal',
                                text: 'Terjadi kesalahan sistem.',
                                customClass: {
                                    popup: 'premium-swal-popup',
                                    title: 'premium-swal-title',
                                    confirmButton: 'premium-swal-confirm-btn'
                                },
                                buttonsStyling: false
                            }).then(() => {
                                window.location.reload();
                            });
                        });
                }

                let currentReplacingModuleId = null;
                const replaceFileInput = document.getElementById('replaceFileInput');

                document.querySelectorAll('.btn-replace-rejected').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        const moduleId = e.currentTarget.dataset.id;
                        const isLink = e.currentTarget.dataset.isLink === '1';

                        if (isLink) {
                            Swal.fire({
                                title: 'Ganti Link Revisi',
                                text: 'Masukkan URL materi yang baru:',
                                input: 'url',
                                inputPlaceholder: 'https://...',
                                showCancelButton: true,
                                confirmButtonText: 'Kirim Revisi',
                                cancelButtonText: 'Batal',
                                customClass: {
                                    popup: 'premium-swal-popup',
                                    confirmButton: 'premium-swal-confirm-btn',
                                    cancelButton: 'premium-swal-cancel-btn'
                                },
                                buttonsStyling: false,
                                inputValidator: (value) => {
                                    if (!value) {
                                        return 'URL tidak boleh kosong!';
                                    }
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    uploadRevisionDirectly(moduleId, null, result.value);
                                }
                            });
                        } else {
                            currentReplacingModuleId = moduleId;
                            if (replaceFileInput) replaceFileInput.click();
                        }
                    });
                });

                if (replaceFileInput) {
                    replaceFileInput.addEventListener('change', (e) => {
                        if (e.target.files.length === 0 || !currentReplacingModuleId) return;
                        const file = e.target.files[0];
                        
                        const allowedExt = ['pdf', 'mp4', 'pptx', 'ppt', 'docx', 'doc'];
                        const ext = (file.name.split('.').pop() || '').toLowerCase();
                        if (!allowedExt.includes(ext)) {
                            Swal.fire({
                                icon: 'error',
                                iconColor: '#ef4444',
                                title: 'Format Tidak Diizinkan',
                                text: 'Hanya berkas PDF, MP4, PPTX, DOCX yang diizinkan.',
                                customClass: {
                                    popup: 'premium-swal-popup',
                                    title: 'premium-swal-title',
                                    confirmButton: 'premium-swal-confirm-btn'
                                },
                                buttonsStyling: false
                            });
                            return;
                        }

                        uploadRevisionDirectly(currentReplacingModuleId, file, null);
                        e.target.value = '';
                    });
                }

                function uploadRevisionDirectly(moduleId, file, link) {
                    Swal.fire({
                        title: 'Mengunggah Revisi...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); },
                        customClass: {
                            popup: 'premium-swal-popup',
                            title: 'premium-swal-title'
                        }
                    });

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('action', 'replace_module');
                    formData.append('module_id', moduleId);
                    if (file) {
                        formData.append('file', file);
                    }
                    if (link) {
                        formData.append('material_link', link);
                    }

                    fetch('{{ route('trainer.events.studio.upload', $event->id) }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                iconColor: '#10b981',
                                title: 'Revisi Berhasil Dikirim',
                                text: data.message,
                                showConfirmButton: false,
                                timer: 1500,
                                customClass: {
                                    popup: 'premium-swal-popup',
                                    title: 'premium-swal-title'
                                }
                            });
                            setTimeout(() => { window.location.reload(); }, 1500);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                iconColor: '#ef4444',
                                title: 'Gagal',
                                text: data.error || 'Gagal mengirim revisi.',
                                customClass: {
                                    popup: 'premium-swal-popup',
                                    title: 'premium-swal-title',
                                    confirmButton: 'premium-swal-confirm-btn'
                                },
                                buttonsStyling: false
                            });
                        }
                    })
                    .catch(err => {
                        Swal.fire({
                            icon: 'error',
                            iconColor: '#ef4444',
                            title: 'Gagal',
                            text: 'Terjadi kesalahan sistem.',
                            customClass: {
                                popup: 'premium-swal-popup',
                                title: 'premium-swal-title',
                                confirmButton: 'premium-swal-confirm-btn'
                            },
                            buttonsStyling: false
                        });
                    });
                }

                window.updateDraftSurveyLink = function (index, value) {
                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('action', 'save_draft_survey');
                    formData.append('index', index);
                    formData.append('survey_link', value);

                    const input = document.querySelector(`.survey-link-input[data-index="${index}"]`);
                    if (!input) return;
                    input.style.borderColor = '#d8b4fe';
                    input.style.boxShadow = '0 0 0 3px rgba(216, 180, 254, 0.2)';

                    fetch('{{ route('trainer.events.studio.upload', $event->id) }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                input.style.borderColor = '#10b981';
                                input.style.boxShadow = '0 0 0 3px rgba(16, 185, 129, 0.2)';
                                if (data.survey_link !== undefined) {
                                    input.value = data.survey_link || '';
                                }
                                setTimeout(() => {
                                    input.style.borderColor = '#cbd5e1';
                                    input.style.boxShadow = 'none';
                                }, 1000);
                            } else {
                                input.style.borderColor = '#ef4444';
                                input.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
                            }
                        })
                        .catch(err => {
                            input.style.borderColor = '#ef4444';
                            input.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.2)';
                        });
                };
            });
        </script>
    @endif
@endsection