@extends('layouts.admin')

@section('title', 'Review Material - ' . $material->name)

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        :root {
            --admin-primary: #1e1b4b;
            --admin-secondary: #4338ca;
            --admin-bg: #f8fafc;
            --admin-card-bg: #ffffff;
            --admin-border: #e2e8f0;
            --admin-text-main: #0f172a;
            --admin-text-muted: #64748b;
        }

        body {
            background-color: var(--admin-bg);
        }

        html {
            scrollbar-gutter: stable;
        }

        .material-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
        }

        .trainer-sidebar {
            width: 260px;
            background: var(--admin-card-bg);
            padding: 24px 16px;
            border-right: 1px solid #eee;
            flex-shrink: 0;
            position: sticky;
            top: 72px;
            height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .nav-menu-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: var(--admin-text-muted);
            letter-spacing: 1px;
            margin-bottom: 12px;
            margin-top: 24px;
            display: block;
            padding-left: 16px;
        }

        .nav-menu-label:first-child {
            margin-top: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 11px 16px;
            color: #1e293b;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .sidebar-link i {
            font-size: 18px;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: #f8fafc;
            color: #3949ab;
        }

        .sidebar-link:hover i {
            color: #3949ab;
        }

        .sidebar-link.active {
            background-color: #3949ab;
            color: #fff;
        }

        .sidebar-link.active i {
            color: #fff;
        }

        .sidebar-parent {
            justify-content: space-between;
        }

        .sidebar-parent .sidebar-chevron {
            font-size: 0.8rem;
            transition: transform 0.2s ease;
        }

        .sidebar-parent[aria-expanded='true'] .sidebar-chevron {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            margin: 4px 0 8px;
        }

        .sidebar-submenu .sidebar-link {
            margin-left: 14px;
            padding: 7px 10px;
            font-size: 0.82rem;
            border-radius: 8px;
        }

        .sidebar-submenu .sidebar-link i {
            font-size: 0.95rem;
        }

        .material-main {
            flex-grow: 1;
            padding: 32px;
            overflow-x: hidden;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            gap: 12px;
            flex-wrap: wrap;
        }

        .status-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            height: 40px;
            padding: 0 14px;
            border-radius: 999px;
            border: 1px solid #c7d2fe;
            background: #eef2ff;
            color: #312e81;
            font-size: 0.84rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .btn-back {
            background: #fff;
            border: 1px solid #cbd5e1;
            color: #334155;
            height: 44px;
            padding: 0 16px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        .btn-back:hover {
            background: #f8fafc;
            color: #1e293b;
        }

        /* Card Setup */
        .card-custom {
            background: var(--admin-card-bg);
            border-radius: 16px;
            border: 1px solid var(--admin-border);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--admin-text-main);
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        /* Video Player */
        .video-container {
            background: #0f172a;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 16/9;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .video-container iframe,
        .video-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .video-container.is-quiz {
            aspect-ratio: auto;
            min-height: 360px;
            max-height: 520px;
            overflow: auto;
            background: #fff;
            color: #0f172a;
            display: block;
            padding: 18px;
        }

        .quiz-preview-head {
            font-size: 0.82rem;
            color: #64748b;
            margin-bottom: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .quiz-preview-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .quiz-preview-item {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px;
            background: #f8fafc;
        }

        .quiz-preview-q {
            margin: 0 0 8px;
            font-size: 0.9rem;
            font-weight: 700;
            color: #1e293b;
        }

        .quiz-preview-answers {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .quiz-preview-answer {
            border-radius: 8px;
            padding: 7px 10px;
            font-size: 0.82rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            color: #334155;
        }

        .quiz-preview-answer.is-correct {
            border-color: #86efac;
            background: #f0fdf4;
            color: #166534;
            font-weight: 700;
        }

        /* Module List */
        .module-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-height: 68vh;
            overflow-y: auto;
            padding-right: 4px;
        }

        .status-board {
            margin-top: 4px;
        }

        .status-switcher {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 14px;
        }

        .status-pill {
            border: 1px solid #dbe3f2;
            background: #f8fafc;
            color: #334155;
            border-radius: 999px;
            height: 36px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            gap: 7px;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .status-pill:hover {
            border-color: #c7d2fe;
            background: #eef2ff;
            color: #312e81;
        }

        .status-pill.active {
            background: #312e81;
            border-color: #312e81;
            color: #fff;
        }

        .status-pill-count {
            min-width: 22px;
            height: 22px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            padding: 0 6px;
            background: rgba(15, 23, 42, 0.08);
            color: inherit;
        }

        .status-pill.active .status-pill-count {
            background: rgba(255, 255, 255, 0.2);
        }

        .status-panel {
            display: none;
        }

        .status-panel.active {
            display: block;
        }

        .module-item {
            border: 1px solid #e5ebf5;
            border-radius: 14px;
            padding: 14px;
            display: flex;
            gap: 14px;
            background: #ffffff;
            align-items: flex-start;
            transition: all 0.2s ease;
        }

        .module-item:hover {
            border-color: #c7d2fe;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
        }

        .module-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: #eef2ff;
            color: #4338ca;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .module-desc h6 {
            margin: 0 0 4px 0;
            font-weight: 700;
            color: #1e293b;
        }

        .module-head {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
        }

        .module-head-left {
            min-width: 0;
        }

        .module-quick-actions {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .module-icon-btn {
            width: 30px;
            height: 30px;
            border-radius: 8px;
            border: 1px solid transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
        }

        .module-icon-btn.preview {
            color: #2b2470;
            background: #f1f4ff;
            border-color: #d6dcff;
        }

        .module-icon-btn.download {
            color: #166534;
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .module-desc p {
            margin: 0;
            font-size: 0.85rem;
            color: #64748b;
        }

        .module-desc {
            flex: 1;
        }

        .module-meta {
            margin-top: 6px;
            font-size: 0.8rem;
            color: #64748b;
        }

        .module-actions {
            display: flex;
            gap: 6px;
            margin-top: 8px;
            flex-wrap: wrap;
        }

        .module-decision-stack {
            display: flex;
            flex-direction: row;
            align-items: center;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .module-action-form {
            margin: 0;
        }

        .module-btn-approve,
        .module-btn-reject {
            border: 1px solid transparent;
            border-radius: 8px;
            height: 34px;
            padding: 0 11px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.77rem;
            font-weight: 700;
            cursor: pointer;
        }

        .module-btn-approve {
            color: #166534;
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .module-btn-approve:hover {
            background: #dcfce7;
        }

        .module-btn-reject {
            color: #991b1b;
            background: #fef2f2;
            border-color: #fecaca;
        }

        .module-btn-reject:hover {
            background: #fee2e2;
        }

        .module-reject-form {
            margin-top: 10px;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #fee2e2;
            background: #fff7f7;
        }

        .module-reject-form textarea {
            width: 100%;
            min-height: 78px;
            border-radius: 8px;
            border: 1px solid #fecaca;
            padding: 8px 10px;
            font-size: 0.82rem;
            resize: vertical;
            margin-bottom: 8px;
        }

        .module-review-badge {
            margin-top: 8px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 3px 9px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .module-review-badge.approved {
            background: #dcfce7;
            color: #166534;
        }

        .module-review-badge.rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .module-review-badge.pending {
            background: #fef3c7;
            color: #92400e;
        }

        .module-review-trigger {
            appearance: none;
            display: inline-flex;
            border: 0;
            background: transparent;
            padding: 0;
        }

        .module-review-trigger:hover {
            background: transparent;
        }

        .module-btn {
            text-decoration: none;
            border-radius: 8px;
            height: 34px;
            padding: 0 11px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.77rem;
            font-weight: 700;
            border: 1px solid transparent;
        }

        .module-btn-open {
            color: #1e1b4b;
            background: #eef2ff;
            border-color: #c7d2fe;
        }

        .module-btn-open:hover {
            background: #e0e7ff;
        }

        .module-btn-download {
            color: #166534;
            background: #f0fdf4;
            border-color: #bbf7d0;
        }

        .module-btn-download:hover {
            background: #dcfce7;
        }

        .module-tag {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.2px;
            margin-top: 8px;
            width: fit-content;
        }

        .module-tag-ready {
            color: #166534;
            background: #dcfce7;
        }

        .module-tag-missing {
            color: #991b1b;
            background: #fee2e2;
        }

        .review-state {
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            flex-wrap: wrap;
        }

        .review-state-title {
            font-size: 0.88rem;
            color: #334155;
            font-weight: 700;
        }

        .review-state-meta {
            font-size: 0.82rem;
            color: #64748b;
        }

        /* Sidebar Kanan (Action) */
        .trainer-box {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .trainer-box img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
        }

        .action-box {
            position: sticky;
            top: 32px;
        }

        .side-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
        }

        .action-box .side-card {
            margin-bottom: 14px;
        }

        .side-card-title {
            color: #475569;
            font-size: 0.88rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 12px;
        }

        .btn-approve {
            width: 100%;
            background: linear-gradient(135deg, #3949ab 0%, #1e1b4b 100%);
            color: white;
            height: 52px;
            padding: 0 14px;
            border: 1px solid transparent;
            border-radius: 12px;
            font-weight: 700;
            margin-bottom: 10px;
            font-size: 0.95rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 10px 20px rgba(57, 73, 171, 0.24);
            transition: all 0.2s ease;
        }

        .btn-approve:hover {
            transform: translateY(-1px);
            background: linear-gradient(135deg, #3949ab 0%, #1e1b4b 100%);
            ;
            box-shadow: 0 14px 24px rgba(30, 27, 75, 0.28);
        }

        .btn-approve:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-reject {
            width: 100%;
            background: #f8fafc;
            color: #334155;
            border: 1px solid #cbd5e1;
            height: 50px;
            padding: 0 12px;
            border-radius: 12px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
        }

        .btn-reject:hover {
            background: #f1f5f9;
            border-color: #94a3b8;
        }

        .reject-modal .modal-content {
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 38px rgba(15, 23, 42, 0.16);
        }

        .reject-modal .modal-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            padding: 18px 20px;
        }

        .reject-modal .modal-title {
            color: #334155;
            font-size: 1.02rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .reject-modal .btn-close {
            filter: none;
            opacity: 0.6;
            background-color: transparent;
            border-radius: 8px;
            padding: 0.45rem;
            box-shadow: none;
            outline: none;
        }

        .reject-modal .btn-close:hover,
        .reject-modal .btn-close:focus,
        .reject-modal .btn-close:focus-visible {
            background-color: #e2e8f0;
            opacity: 1;
            box-shadow: none;
            outline: none;
        }

        .reject-modal .btn-close:active {
            background-color: #e2e8f0;
            opacity: 1;
            filter: brightness(0);
            box-shadow: none;
            outline: none;
        }

        .reject-modal .modal-body {
            padding: 18px 20px;
            background: #ffffff;
        }

        .reject-modal .form-label {
            color: #334155;
            font-size: 0.9rem;
            letter-spacing: 0.1px;
            margin-bottom: 8px;
        }

        .reject-modal .form-control {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.9rem;
            line-height: 1.45;
            color: #1e293b;
            padding: 12px 14px;
            min-height: 132px;
            resize: vertical;
        }

        .reject-modal .form-control::placeholder {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .reject-modal .form-control:focus {
            border-color: #3949ab;
            box-shadow: 0 0 0 0.2rem rgba(57, 73, 171, 0.14);
        }

        .reject-modal .help-text {
            color: #64748b;
            font-size: 0.8rem;
            margin-top: 8px;
            display: block;
        }

        .reject-modal .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 14px 20px 18px;
            gap: 8px;
        }

        .reject-modal .btn-cancel {
            border: 1px solid #cbd5e1;
            background: #ffffff;
            color: #334155;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 700;
            height: 40px;
            min-width: 92px;
            padding: 0 14px;
        }

        .reject-modal .btn-cancel:hover {
            background: #f8fafc;
        }

        .reject-modal .btn-submit-reject {
            border: 1px solid #334155;
            background: #334155;
            color: #fff;
            border-radius: 10px;
            font-size: 0.86rem;
            font-weight: 800;
            height: 40px;
            min-width: 128px;
            padding: 0 14px;
        }

        .reject-modal .btn-submit-reject:hover {
            background: #1e293b;
            border-color: #1e293b;
        }

        @media (max-width: 768px) {
            .module-item {
                flex-direction: column;
            }

            .page-header {
                align-items: flex-start;
            }

            .action-box {
                position: static;
                top: auto;
            }
        }
    </style>
@endsection

@section('content')
    <div class="material-wrapper">
        @include('admin.partials.trainer-sidebar')

        <main class="material-main">
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            <div class="page-header">
                <a href="{{ route('admin.material.' . ($material->status === 'approved' ? 'approved' : 'approvals')) }}"
                    class="btn-back"><i class="bi bi-arrow-left me-2"></i>Kembali</a>
                @if($material->status === 'approved')
                    <span class="status-chip" style="border:1px solid #bbf7d0; background:#dcfce7; color:#166534;"><i
                            class="bi bi-check-circle"></i>Status: Disetujui</span>
                @elseif($material->status === 'rejected')
                    <span class="status-chip" style="border:1px solid #fecaca; background:#fee2e2; color:#991b1b;"><i
                            class="bi bi-x-circle"></i>Status: Ditolak</span>
                @else
                    <span class="status-chip"><i class="bi bi-hourglass-split"></i>Status: Pending Review</span>
                @endif
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card-custom">
                        <h1 class="fw-bold text-dark mb-2 fs-3">{{ $material->name }}</h1>
                        <p class="text-muted mb-4">{{ $material->category->name ?? 'Kategori Umum' }} • Diupload
                            {{ $material->created_at->format('d M Y') }}
                        </p>

                        <div class="video-container mb-3" id="topReviewViewer">
                            @if($material->media && str_contains($material->media, 'mp4'))
                                <video controls controlsList="nodownload">
                                    <source src="{{ asset('storage/' . $material->media) }}" type="video/mp4">
                                </video>
                            @elseif($material->card_thumbnail)
                                <img src="{{ $material->card_thumbnail }}"
                                    style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div class="text-center opacity-50">
                                    <i class="bi bi-camera-video" style="font-size: 4rem;"></i>
                                    <p class="mt-2">Trailer Preview Tidak Tersedia</p>
                                </div>
                            @endif
                        </div>
                        @if($material->status === 'pending_review')
                            <div class="review-state">
                                <div class="review-state-title" id="topReviewTitle">Preview awal materi course</div>
                            </div>
                        @elseif($material->status === 'rejected')
                            <div class="review-state">
                                <div class="review-state-title" id="topReviewTitle">Preview materi course yang ditolak</div>
                                <div class="review-state-meta" id="topReviewMeta">Materi ini telah ditolak oleh admin. Tidak
                                    dapat direview ulang di sini.</div>
                            </div>
                        @endif
                    </div>

                    <div class="card-custom">
                        <h5 class="card-title">Isi Modul ({{ $uploadedModulesCount ?? 0 }})</h5>
                        @php
                            $allUploadedModules = collect($uploadedModules ?? [])->values();
                            $moduleStatusTabs = [
                                [
                                    'id' => 'modules-pending',
                                    'label' => 'Pending',
                                    'icon' => 'bi-hourglass-split',
                                    'status' => 'pending_review',
                                    'data' => $allUploadedModules->filter(fn($m) => (($m->review_status ?? 'pending_review') === 'pending_review'))->values(),
                                ],
                                [
                                    'id' => 'modules-rejected',
                                    'label' => 'Rejected',
                                    'icon' => 'bi-x-circle',
                                    'status' => 'rejected',
                                    'data' => $allUploadedModules->filter(fn($m) => (($m->review_status ?? 'pending_review') === 'rejected'))->values(),
                                ],
                                [
                                    'id' => 'modules-approved',
                                    'label' => 'Approved',
                                    'icon' => 'bi-check-circle',
                                    'status' => 'approved',
                                    'data' => $allUploadedModules->filter(fn($m) => (($m->review_status ?? 'pending_review') === 'approved'))->values(),
                                ],
                            ];
                        @endphp

                        <section id="module-review-board" class="status-board" aria-label="Filter review modul">
                            <div class="status-switcher" role="tablist" aria-label="Tab status review modul">
                                @foreach($moduleStatusTabs as $index => $tab)
                                    <button class="status-pill {{ $index === 0 ? 'active' : '' }}" type="button"
                                        data-target="{{ $tab['id'] }}" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                                        <i class="bi {{ $tab['icon'] }}"></i>
                                        <span>{{ $tab['label'] }}</span>
                                        <span class="status-pill-count">{{ $tab['data']->count() }}</span>
                                    </button>
                                @endforeach
                            </div>

                            @foreach($moduleStatusTabs as $index => $tab)
                                <section id="{{ $tab['id'] }}" class="status-panel {{ $index === 0 ? 'active' : '' }}" role="tabpanel">
                                    @if($tab['data']->isEmpty())
                                        <div class="text-center py-4 text-muted border rounded-3 bg-light-subtle">
                                            Tidak ada modul {{ strtolower($tab['label']) }}.
                                        </div>
                                    @else
                                        <div class="module-list">
                                            @foreach($tab['data'] as $module)
                                                @php
                                                    $rawContent = trim((string) ($module->content_url ?? ''));
                                                    $isHttp = str_starts_with($rawContent, 'http://') || str_starts_with($rawContent, 'https://');
                                                    $normalizedContent = ltrim((string) preg_replace('#^/?storage/#', '', $rawContent), '/');
                                                    $contentUrl = null;
                                                    $ext = strtolower(pathinfo($normalizedContent !== '' ? $normalizedContent : $rawContent, PATHINFO_EXTENSION));
                                                    $mime = strtolower((string) ($module->mime_type ?? ''));

                                                    $previewKind = 'file';
                                                    if ($module->isQuiz()) {
                                                        $previewKind = 'quiz';
                                                    } elseif ($module->isVideo() || str_starts_with($mime, 'video/') || in_array($ext, ['mp4', 'mov', 'avi', 'mkv', 'webm'], true)) {
                                                        $previewKind = 'video';
                                                    } elseif ($module->isPdf() || str_contains($mime, 'pdf') || $ext === 'pdf') {
                                                        $previewKind = 'pdf';
                                                    }

                                                    if ($isHttp) {
                                                        $contentUrl = $rawContent;
                                                    } elseif ($normalizedContent !== '' && $rawContent !== 'quiz_submitted') {
                                                        $contentUrl = route('admin.material.module.stream', [$material, $module]);
                                                    }

                                                    $canOpenFile = !$module->isQuiz() && !empty($contentUrl);
                                                    $canReviewInline = $canOpenFile || $module->isQuiz();
                                                    $moduleReviewStatus = in_array(($module->review_status ?? ''), ['approved', 'rejected', 'pending_review'], true)
                                                        ? $module->review_status
                                                        : 'pending_review';
                                                @endphp
                                                <div class="module-item">
                                                    <div class="module-icon">
                                                        @if($module->type == 'video') <i class="bi bi-play-fill"></i>
                                                        @elseif($module->type == 'pdf') <i class="bi bi-file-pdf-fill"></i>
                                                        @elseif($module->type == 'quiz') <i class="bi bi-question-circle-fill"></i>
                                                        @else <i class="bi bi-file-earmark-arrow-down-fill"></i> @endif
                                                    </div>
                                                    <div class="module-desc">
                                                        <div class="module-head">
                                                            <div class="module-head-left">
                                                                <h6>{{ $module->order_no }}. {{ $module->title }}</h6>
                                                            </div>
                                                            <div class="module-quick-actions">
                                                                @if($canReviewInline)
                                                                    <button type="button" class="module-review-trigger"
                                                                        data-review-module-id="{{ $module->id }}"
                                                                        data-review-title="{{ e($module->title) }}" data-review-url="{{ $contentUrl }}"
                                                                        data-review-kind="{{ $previewKind }}"
                                                                        data-review-file="{{ e($module->file_name ?: basename((string) $module->content_url)) }}"
                                                                        title="Preview">
                                                                        <span class="module-icon-btn preview"><i class="bi bi-eye"></i></span>
                                                                    </button>
                                                                @endif
                                                                @if($canOpenFile)
                                                                    <a href="{{ route('admin.material.module.stream', [$material, $module]) }}?download=1"
                                                                        class="module-icon-btn download" title="Unduh">
                                                                        <i class="bi bi-download"></i>
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <p>Tipe: {{ strtoupper($module->type) }} @if($module->duration) •
                                                        {{ $module->duration }} Menit @endif
                                                        </p>
                                                        @if(!empty($module->file_name) || !empty($module->mime_type))
                                                            <div class="module-meta">
                                                                {{ $module->file_name ?: basename((string) $module->content_url) }}
                                                                @if(!empty($module->mime_type))
                                                                    • {{ $module->mime_type }}
                                                                @endif
                                                            </div>
                                                        @endif

                                                        @if($material->status === 'pending_review')
                                                            @if($canReviewInline)
                                                                @if($moduleReviewStatus !== 'approved')
                                                                    <div class="module-decision-stack">
                                                                        <form method="POST"
                                                                            action="{{ route('admin.material.module.approve', [$material, $module]) }}"
                                                                            class="module-action-form">
                                                                            @csrf
                                                                            <button type="submit" class="module-btn-approve">
                                                                                <i class="bi bi-check2-circle"></i> Approve Modul
                                                                            </button>
                                                                        </form>

                                                                        <button type="button" class="module-btn-reject"
                                                                            data-bs-toggle="collapse"
                                                                            data-bs-target="#rejectModuleForm-{{ $module->id }}"
                                                                            aria-expanded="false">
                                                                            <i class="bi bi-x-circle"></i> Reject Modul
                                                                        </button>
                                                                    </div>

                                                                    <div class="collapse module-reject-form" id="rejectModuleForm-{{ $module->id }}">
                                                                        <form method="POST"
                                                                            action="{{ route('admin.material.module.reject', [$material, $module]) }}">
                                                                            @csrf
                                                                            <textarea name="rejection_reason" required minlength="10"
                                                                                placeholder="Tulis alasan revisi untuk modul ini..."></textarea>
                                                                            <button type="submit" class="module-btn-reject">
                                                                                <i class="bi bi-send"></i> Kirim Revisi Modul
                                                                            </button>
                                                                        </form>
                                                                    </div>
                                                                @endif

                                                                <span class="module-review-badge {{ $moduleReviewStatus === 'approved' ? 'approved' : ($moduleReviewStatus === 'rejected' ? 'rejected' : 'pending') }}">
                                                                    @if($moduleReviewStatus === 'approved')
                                                                        <i class="bi bi-check-circle"></i> Modul approved
                                                                    @elseif($moduleReviewStatus === 'rejected')
                                                                        <i class="bi bi-x-circle"></i> Modul rejected
                                                                    @else
                                                                        <i class="bi bi-hourglass-split"></i> Menunggu review modul
                                                                    @endif
                                                                </span>
                                                                <span
                                                                    class="module-tag module-tag-ready">{{ $module->isQuiz() ? 'Kuis tersedia' : 'File tersedia' }}</span>
                                                            @elseif($module->isQuiz())
                                                                <span class="module-tag module-tag-ready">Kuis tersedia</span>
                                                            @else
                                                                <span class="module-tag module-tag-missing">File belum tersedia</span>
                                                            @endif
                                                        @elseif($material->status === 'approved')
                                                            @if($canOpenFile)
                                                                <span
                                                                    class="module-tag module-tag-ready">{{ $module->isQuiz() ? 'Kuis tersedia' : 'File tersedia' }}</span>
                                                            @elseif($module->isQuiz())
                                                                <span class="module-tag module-tag-ready">Kuis tersedia</span>
                                                            @else
                                                                <span class="module-tag module-tag-missing">File belum tersedia</span>
                                                            @endif
                                                        @elseif($material->status === 'rejected')
                                                            @if($canOpenFile)
                                                                <span
                                                                    class="module-tag module-tag-ready">{{ $module->isQuiz() ? 'Kuis tersedia' : 'File tersedia' }}</span>
                                                            @elseif($module->isQuiz())
                                                                <span class="module-tag module-tag-ready">Kuis tersedia</span>
                                                            @else
                                                                <span class="module-tag module-tag-missing">File belum tersedia</span>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </section>
                            @endforeach
                        </section>
                    </div>

                </div>

                <div class="col-xl-4">
                    <div class="action-box">
                        <div class="card-custom side-card mb-3" style="padding: 20px;">
                            <h6 class="side-card-title">Dibuat Oleh:</h6>
                            <div class="trainer-box m-0">
                                <img src="{{ $material->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=Trainer' }}"
                                    alt="Trainer">
                                <div>
                                    <h6 class="fw-bold m-0 text-dark">{{ $material->trainer?->name ?? 'Anonim' }}</h6>
                                    <p class="m-0 text-muted" style="font-size: 0.8rem;">Instruktur</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @php
        $quizMapForJs = collect($uploadedModules ?? [])
            ->filter(fn($m) => $m->isQuiz())
            ->mapWithKeys(function ($m) {
                return [
                    (string) $m->id => $m->quizQuestions
                        ->map(function ($q) {
                            return [
                                'question' => (string) ($q->question ?? ''),
                                'points' => (int) ($q->points ?? 0),
                                'answers' => $q->answers
                                    ->sortBy('order_no')
                                    ->map(function ($a) {
                                        return [
                                            'text' => (string) ($a->answer_text ?? ''),
                                            'is_correct' => (bool) ($a->is_correct ?? false),
                                        ];
                                    })
                                    ->values()
                                    ->all(),
                            ];
                        })
                        ->values()
                        ->all(),
                ];
            })
            ->all();
    @endphp

    <script>
        (function () {
            const board = document.getElementById('module-review-board');
            const quizMap = @json($quizMapForJs);
            const viewer = document.getElementById('topReviewViewer');
            const viewerTitle = document.getElementById('topReviewTitle');
            const viewerMeta = document.getElementById('topReviewMeta');

            function escapeHtml(value) {
                return String(value ?? '')
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function showPreviewContent(html, title, meta, isQuiz = false) {
                if (!viewer) {
                    return;
                }

                viewer.classList.toggle('is-quiz', isQuiz);
                viewer.innerHTML = html;

                if (viewerTitle) {
                    viewerTitle.textContent = title || 'Preview Modul';
                }

                if (viewerMeta) {
                    viewerMeta.textContent = meta || '-';
                }

                viewer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }

            function renderQuiz(moduleId) {
                const questions = quizMap[String(moduleId)] || [];

                if (!questions.length) {
                    return '<div class="text-muted">Belum ada soal pada modul kuis ini.</div>';
                }

                const items = questions.map((q, idx) => {
                    const answers = (q.answers || []).map((a) => {
                        return `<div class="quiz-preview-answer ${a.is_correct ? 'is-correct' : ''}">${escapeHtml(a.text || '-')}</div>`;
                    }).join('');

                    return `
                        <div class="quiz-preview-item">
                            <p class="quiz-preview-q">${idx + 1}. ${escapeHtml(q.question || 'Tanpa pertanyaan')} ${q.points ? `(${q.points} poin)` : ''}</p>
                            <div class="quiz-preview-answers">${answers || '<div class="quiz-preview-answer">Belum ada opsi jawaban</div>'}</div>
                        </div>
                    `;
                }).join('');

                return `<div class="quiz-preview-head">Review Soal Kuis</div><div class="quiz-preview-list">${items}</div>`;
            }

            function renderPreview(url, kind) {
                if (!url) {
                    return '<div class="text-center opacity-50"><i class="bi bi-file-earmark-x" style="font-size: 4rem;"></i><p class="mt-2 mb-0">File tidak tersedia</p></div>';
                }

                if (kind === 'video') {
                    return `<video controls controlsList="nodownload"><source src="${url}"></video>`;
                }

                if (kind === 'pdf') {
                    return `<iframe src="${url}#toolbar=1&navpanes=0"></iframe>`;
                }

                return `<iframe src="${url}"></iframe>`;
            }

            if (board) {
                const pills = board.querySelectorAll('.status-pill');
                const panels = board.querySelectorAll('.status-panel');

                pills.forEach((pill) => {
                    pill.addEventListener('click', function () {
                        const target = this.dataset.target;
                        pills.forEach((item) => {
                            const isActive = item === this;
                            item.classList.toggle('active', isActive);
                            item.setAttribute('aria-selected', isActive ? 'true' : 'false');
                        });
                        panels.forEach((panel) => panel.classList.toggle('active', panel.id === target));
                    });
                });

                board.addEventListener('click', function (event) {
                    const trigger = event.target.closest('.module-review-trigger');
                    if (!trigger) {
                        return;
                    }

                    const fileUrl = trigger.getAttribute('data-review-url') || '';
                    const fileKind = trigger.getAttribute('data-review-kind') || 'file';
                    const moduleTitle = trigger.getAttribute('data-review-title') || 'Materi';
                    const fileName = trigger.getAttribute('data-review-file') || 'File';
                    const moduleId = trigger.getAttribute('data-review-module-id') || '';

                    if (fileKind === 'quiz') {
                        showPreviewContent(renderQuiz(moduleId), 'Preview: ' + moduleTitle, 'Menampilkan daftar soal dan opsi jawaban modul kuis', true);
                        return;
                    }

                    showPreviewContent(renderPreview(fileUrl, fileKind), 'Preview: ' + moduleTitle, 'Menampilkan: ' + fileName, false);
                });
            }

        })();
    </script>
@endsection