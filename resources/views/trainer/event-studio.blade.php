@extends('layouts.trainer')

@section('title', 'Event Studio - ' . $event->title)

@php
    $pageTitle = 'Event Studio';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Events', 'url' => route('trainer.events')],
        ['label' => 'Studio'],
    ];

    $materialStatus = (string) ($myMaterialStatus ?? 'not_uploaded');
    $isRevisionWindow = $materialStatus === 'rejected';

    // Deadline: prefer invitation upload_due_at, then event material_deadline, else no deadline
    $invitationForDeadline = \App\Models\TrainerNotification::where('trainer_id', auth()->id())
        ->where('type', 'event_invitation')
        ->where('data', 'like', '%"entity_id":' . $event->id . '%')
        ->latest('id')->first();
    $invDueAt = data_get($invitationForDeadline?->data ?? [], 'upload_due_at', '');
    $deadline = $invDueAt !== ''
        ? \Carbon\Carbon::parse($invDueAt)
        : (!empty($event->material_deadline) ? \Carbon\Carbon::parse($event->material_deadline) : null);

    $deadlinePassed = $deadline ? now()->gt($deadline) : false;
    $deadlineLabel = $isRevisionWindow ? 'Deadline Revisi (H-3)' : 'Deadline Pengumpulan (H-7)';
    $deadlineHint = $deadlinePassed
        ? ($isRevisionWindow ? 'Batas revisi sudah lewat' : 'Batas pengumpulan sudah lewat')
        : ($isRevisionWindow ? 'Masa revisi masih terbuka' : 'Materi masih bisa diunggah');
    $materialStatusLabel = strtoupper(str_replace('_', ' ', $materialStatus));

    // Per-trainer rejection reason (from latest rejected module)
    $eventRejectionReason = '';
    $latestRejected = ($myModules ?? collect())->where('status', 'rejected')->sortByDesc('updated_at')->first();
    if ($latestRejected) {
        $eventRejectionReason = trim((string) ($latestRejected->rejection_reason ?? ''));
    }
    $showEventRejectionReason = $materialStatus === 'rejected' && $eventRejectionReason !== '';

    // Per-trainer uploaded module info
    $latestModule = ($myModules ?? collect())->sortByDesc('created_at')->first();
    $hasUploadedModule = $latestModule !== null;
    $uploadedModuleName = $hasUploadedModule ? $latestModule->original_name : null;
    $moduleFileUrl = $hasUploadedModule ? \Illuminate\Support\Facades\Storage::disk('public')->url($latestModule->path) : null;
    $canUploadMaterials = $materialStatus !== 'approved';
@endphp

@push('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css" />
    <style>
        main.content-studio-main {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0;
            flex: 1;
            width: 100%;
            overflow-x: hidden;
        }

        .studio-page {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-xl);
        }

        .studio-hero {
            background: linear-gradient(135deg, var(--main-navy-clr) 0%, var(--navy-dark) 100%);
            border-radius: var(--radius-2xl);
            padding: var(--spacing-3xl);
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(27, 23, 99, 0.15);
        }

        .studio-hero::before,
        .studio-hero::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .studio-hero::before {
            top: -80px;
            right: -80px;
            width: 192px;
            height: 192px;
            background: rgba(251, 191, 36, 0.1);
            filter: blur(60px);
        }

        .studio-hero::after {
            bottom: -40px;
            left: -40px;
            width: 128px;
            height: 128px;
            background: rgba(99, 102, 241, 0.1);
            filter: blur(50px);
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
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: var(--spacing-md);
        }

        .status-card {
            background: var(--white-clr);
            border: 1px solid rgba(227, 233, 242, 0.95);
            border-radius: 18px;
            padding: 16px 18px;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.05);
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .status-card .icon {
            width: 40px;
            height: 40px;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(35, 29, 121, 0.08), rgba(212, 166, 47, 0.12));
            color: var(--main-navy-clr);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .status-card .icon i {
            font-size: 18px;
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
            background: #fef3c7;
            color: #92400e;
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
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.07);
        }

        .panel-hero {
            padding: 22px 24px;
            background: linear-gradient(135deg, rgba(35, 29, 121, 0.98), rgba(42, 36, 140, 0.95));
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
            padding: 22px 24px 24px;
        }

        .module-preview {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 20px;
            border-radius: 18px;
            border: 1px solid #e5ebf5;
            background: linear-gradient(180deg, #fbfcfe 0%, #ffffff 100%);
            margin-bottom: 18px;
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
            background: #231d79;
            color: var(--white-clr);
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;
        }

        .module-preview .preview-link i {
            color: var(--yellow-clr);
        }

        .upload-lock-note {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            padding: 18px 20px;

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

            @media (max-width: 576px) {
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
            }

            border-radius: 18px;
            border: 1px solid #dbe3f0;
            background: #f8fafc;
        }

        .upload-lock-note .icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;

            <div id="notificationModal" class="notification-modal" aria-hidden="true"><div class="notification-modal-card" role="dialog" aria-modal="true" aria-labelledby="notificationModalTitle"><div class="notification-modal-header"><div id="notificationModalIcon" class="notification-modal-icon"><i class="bi bi-info-circle" style="font-size: 22px;"></i></div><div><h3 id="notificationModalTitle" class="notification-modal-title">Informasi</h3></div></div><p id="notificationModalMessage" class="notification-modal-message"></p><div class="notification-modal-footer"><button id="notificationModalCloseBtn" type="button" class="notification-modal-close">Tutup</button></div></div></div>display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(35, 29, 121, 0.08);

            function showNotificationModal(title, message, type='info') {
                const modal=document.getElementById('notificationModal');
                const icon=document.getElementById('notificationModalIcon');
                const titleEl=document.getElementById('notificationModalTitle');
                const messageEl=document.getElementById('notificationModalMessage');

                if ( !modal || !icon || !titleEl || !messageEl) {
                    return;
                }

                icon.className='notification-modal-icon';
                const iconEl=icon.querySelector('i');

                if (iconEl) {
                    iconEl.className='bi';
                }

                if (type==='success') {
                    icon.classList.add('success');
                    if (iconEl) iconEl.classList.add('bi-check-circle-fill');
                }

                else if (type==='error') {
                    icon.classList.add('error');
                    if (iconEl) iconEl.classList.add('bi-x-circle-fill');
                }

                else if (type==='warning') {
                    icon.classList.add('warning');
                    if (iconEl) iconEl.classList.add('bi-exclamation-triangle-fill');
                }

                else {
                    if (iconEl) iconEl.classList.add('bi-info-circle-fill');
                }

                titleEl.textContent=title;
                messageEl.textContent=message;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
            }

            function closeNotificationModal() {
                const modal=document.getElementById('notificationModal');
                if ( !modal) return;
                modal.classList.remove('is-open');
                modal.setAttribute('aria-hidden', 'true');
            }

            color: var(--main-navy-clr);
            flex-shrink: 0;
        }

        .upload-lock-note .icon i {
            font-size: 18px;
        }

        const notificationModal=document.getElementById('notificationModal');
        const notificationModalCloseBtn=document.getElementById('notificationModalCloseBtn');

        .upload-lock-note h3 {
            margin: 0 0 6px 0;

            if (notificationModalCloseBtn) {
                notificationModalCloseBtn.addEventListener('click', closeNotificationModal);
            }

            if (notificationModal) {
                notificationModal.addEventListener('click', (e)=> {
                        if (e.target===notificationModal) {
                            closeNotificationModal();
                        }
                    });
            }

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
            min-height: 280px;
            border-radius: 20px;
            border: 2px dashed #dfe6f2;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: var(--spacing-sm);
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 24px;
        }

        .dropzone:hover {
            border-color: var(--main-navy-clr);
            background: #f0f5fc;
        }

        .dropzone i {
            font-size: 40px;
            color: #c8d0de;
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
            background: #231d79;
            color: var(--white-clr);
            padding: var(--spacing-sm) var(--spacing-lg);
            border-radius: 12px;
            font-size: var(--font-size-sm);
            font-weight: 600;
            letter-spacing: 0.06em;
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .primary-btn i {
            color: var(--yellow-clr);
        }

        .validation-card {
            background: linear-gradient(180deg, #231d79 0%, #1c175f 100%);
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

            .validation-card {
                border-radius: 22px;
                padding: 18px;
            }

            .dropzone {
                min-height: 240px;
                padding: 18px;
            }
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
                <section class="status-banner" aria-label="Alasan revisi materi event">
                    <div class="icon"><i class="bi bi-exclamation-triangle"></i></div>
                    <div>
                        <p class="label">Alasan Revisi dari Admin</p>
                        <p class="reason">{{ $eventRejectionReason }}</p>
                    </div>
                </section>
            @endif

            <section class="status-grid" aria-label="Ringkasan studio event">
                <article class="status-card">
                    <div class="icon"><i class="bi bi-calendar-event"></i></div>
                    <div>
                        <p class="label">{{ $deadlineLabel }}</p>
                        <p class="value">{{ $deadline ? $deadline->format('d M Y H:i') : 'Tidak ada tenggat' }}</p>
                        <p class="hint">{{ $deadlineHint }}</p>
                    </div>
                </article>

                <article class="status-card">
                    <div class="icon"><i class="bi bi-file-earmark-check"></i></div>
                    <div>
                        <p class="label">Status</p>
                        @if($materialStatus !== 'draft')
                            @php
                                $badgeClass = match ($materialStatus) {
                                    'approved'     => 'approved',
                                    'rejected'     => 'rejected',
                                    'pending_review' => 'pending',
                                    'not_uploaded' => 'not-submitted',
                                    default        => 'pending',
                                };
                                $statusIcon = match ($materialStatus) {
                                    'approved'     => '✓',
                                    'rejected'     => '✕',
                                    'pending_review' => '⏳',
                                    'not_uploaded' => '—',
                                    default        => '📋',
                                };
                                $badgeLabel = match ($materialStatus) {
                                    'approved'     => 'Material Approved',
                                    'rejected'     => 'Revision Needed',
                                    'pending_review' => 'In Review',
                                    'not_uploaded' => 'Not Submitted',
                                    default        => 'Submitted',
                                };
                            @endphp
                            <span class="status-badge {{ $badgeClass }}">
                                <span>{{ $statusIcon }}</span>
                                <span>{{ $badgeLabel }}</span>
                            </span>
                        @else
                            <p class="value">{{ $materialStatusLabel }}</p>
                        @endif
                        @if($materialStatus === 'not_uploaded')
                            <p class="hint">Belum ada modul yang dikirim. Silakan upload materi Anda.</p>
                        @elseif($materialStatus === 'draft')
                            <p class="hint">Status review akan berubah setelah admin memeriksa file.</p>
                        @elseif($materialStatus === 'approved')
                            <p class="hint">Materi Anda telah disetujui dan siap ditampilkan di event.</p>
                        @elseif($materialStatus === 'rejected')
                            <p class="hint">Silakan upload ulang dengan revisi yang diperlukan admin.</p>
                        @elseif($materialStatus === 'pending_review')
                            <p class="hint">Admin sedang mereview materi Anda. Harap menunggu notifikasi.</p>
                        @endif
                    </div>
                </article>

                <article class="status-card">
                    <div class="icon"><i class="bi bi-info-circle"></i></div>
                    <div>
                        <p class="label">Format</p>
                        <p class="value">PDF, MP4, PPTX, DOCX</p>
                        <p class="hint">Gunakan format ini agar proses audit lebih cepat.</p>
                    </div>
                </article>
            </section>

            <section class="studio-layout">
                <div class="studio-panel">
                    <div class="panel-hero">
                        <div>
                            <h2>Upload Materi Event</h2>
                            <p>Gunakan area ini untuk mengirim file materi final yang akan direview admin sebelum event
                                berjalan.</p>
                        </div>
                    </div>

                    <div class="upload-panel">
                        <div class="module-preview">
                            <div>
                                <p class="meta-label">Materi saat ini</p>
                                @if($hasUploadedModule)
                                    <h3>{{ $uploadedModuleName }}</h3>
                                    <p>
                                        {{ $materialStatus === 'approved' ? 'Materi ini sudah dikunci setelah disetujui admin.' : 'Materi ini masih tersimpan untuk status ' . strtolower(str_replace('_', ' ', $materialStatus)) . '.' }}
                                    </p>
                                @else
                                    <h3>Belum ada materi yang diunggah</h3>
                                    <p>File yang Anda kirim akan tampil di sini sebagai referensi status terakhir.</p>
                                @endif
                            </div>

                            @if($hasUploadedModule && $moduleFileUrl)
                                <a href="{{ $moduleFileUrl }}" class="preview-link" target="_blank" rel="noopener noreferrer">
                                    <i class="bi bi-box-arrow-up-right"></i>
                                    Lihat File
                                </a>
                            @endif
                        </div>

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

                            <form id="moduleForm" class="module-form"
                                action="{{ route('trainer.events.studio.upload', $event->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="eventId" value="{{ $event->id }}">

                                <div class="dropzone" id="dropzone">
                                    <input type="file" id="fileInput" accept=".pdf,.mp4,.pptx,.ppt,.docx,.doc" name="files[]"
                                        style="display: none" {{ $deadlinePassed ? 'disabled' : '' }} />
                                    <i class="bi bi-cloud-arrow-up"></i>
                                    <h2>Drop Event Assets Here</h2>
                                    <p>SUPPORT: PDF, MP4, PPTX, DOCX</p>
                                    <p style="font-size: 12px; color: #999; margin-top: 8px">
                                        atau klik untuk memilih 1 file materi
                                    </p>
                                </div>

                                <div id="fileList" class="file-list" style="display: none">
                                    <h3>Materi yang Diunggah</h3>
                                    <ul id="uploadedFiles" style="list-style: none; padding: 0; margin: 0"></ul>
                                </div>

                                <div class="panel-footer">
                                    <button type="submit" class="primary-btn" id="submitBtn" {{ $deadlinePassed ? 'disabled' : '' }}>
                                        SUBMIT FOR AUDIT <i class="bi bi-send"></i>
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="upload-lock-note">
                                <div class="icon"><i class="bi bi-shield-lock"></i></div>
                                <div>
                                    <h3>Area upload dikunci</h3>
                                    <p>Materi sudah berstatus approved, jadi file baru tidak bisa dikirim lagi. Jika perlu
                                        revisi, hubungi admin untuk alur berikutnya.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </section>
        </div>
    </main>

    @if($canUploadMaterials)
        <script>
            let uploadedFiles = [];

            document.addEventListener("DOMContentLoaded", function () {
                const dropzone = document.getElementById("dropzone");
                const fileInput = document.getElementById("fileInput");
                const fileList = document.getElementById("fileList");
                const uploadedFilesList = document.getElementById("uploadedFiles");
                const moduleForm = document.getElementById("moduleForm");
                const submitBtn = document.getElementById("submitBtn");

                if (!dropzone || !fileInput || !moduleForm || !submitBtn) return;

                dropzone.addEventListener("click", () => {
                    if (!fileInput.disabled) fileInput.click();
                });

                dropzone.addEventListener("dragover", (e) => {
                    e.preventDefault();
                    dropzone.style.borderColor = "#1a237e";
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

                    if (uploadedFiles.length === 0) {
                        alert('Silakan pilih file terlebih dahulu sebelum submit.');
                        return;
                    }

                    const allowedExt = ['pdf', 'mp4', 'pptx', 'ppt', 'docx', 'doc'];
                    const invalidFiles = uploadedFiles.filter((file) => {
                        const ext = (file.name.split('.').pop() || '').toLowerCase();
                        return !allowedExt.includes(ext);
                    });

                    if (invalidFiles.length > 0) {
                        const names = invalidFiles.map((f) => f.name).join(', ');
                        alert('File tidak valid: ' + names + '. Hanya PDF, MP4, PPTX, DOCX yang diizinkan.');
                        return;
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
                            try { data = JSON.parse(text); } catch(e) {
                                throw new Error('Server error: ' + text.substring(0, 200));
                            }
                            if (!response.ok || !data.success) {
                                const message = data.error || data.message || 'Gagal mengunggah materi event.';
                                throw new Error(message);
                            }

                            uploadedFiles = [];
                            updateFileList();
                            fileInput.value = '';
                            alert(data.message || 'Materi event berhasil diunggah dan dikirim ke admin.');
                            location.reload();
                        })
                        .catch(error => {
                            alert('Gagal: ' + (error.message || 'Terjadi kesalahan saat mengunggah materi event.'));
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalBtnText;
                        });
                });
            });
        </script>
    @endif
@endsection