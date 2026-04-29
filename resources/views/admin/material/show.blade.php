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
            gap: 12px;
        }

        .module-item {
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            display: flex;
            gap: 16px;
            background: #f8fafc;
            align-items: flex-start;
        }

        .module-icon {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            background: #e0e7ff;
            color: #4338ca;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .module-desc h6 {
            margin: 0 0 4px 0;
            font-weight: 700;
            color: #1e293b;
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
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .module-review-trigger {
            appearance: none;
            border: 1px solid #c7d2fe;
            background: #eef2ff;
            color: #1e1b4b;
            border-radius: 8px;
            height: 36px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
            font-weight: 700;
            cursor: pointer;
        }

        .module-review-trigger:hover {
            background: #e0e7ff;
        }

        .module-btn {
            text-decoration: none;
            border-radius: 8px;
            height: 36px;
            padding: 0 12px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.8rem;
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
            gap: 16px;
            padding: 16px;
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
            top: 100px;
        }

        .side-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
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
            background: #3949ab;
            color: white;
            height: 58px;
            padding: 0 14px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-approve:hover {
            background: #1e1b4b;
        }

        .btn-approve:disabled {
            background: #9ca3af;
            cursor: not-allowed;
            opacity: 0.7;
        }

        .btn-reject {
            width: 100%;
            background: #fff;
            color: #ef4444;
            border: 2px solid #ef4444;
            height: 56px;
            padding: 0 12px;
            border-radius: 10px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-reject:hover {
            background: #fef2f2;
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
                        @if($material->status !== 'rejected')
                            <div class="review-state">
                                <div class="review-state-title" id="topReviewTitle">Preview awal materi course</div>
                                <div class="review-state-meta" id="topReviewMeta">Klik "Review di sini" pada daftar modul untuk
                                    mengganti preview.</div>
                            </div>
                        @elseif($material->status === 'rejected')
                            <div class="review-state">
                                <div class="review-state-title" id="topReviewTitle">Preview materi course yang ditolak</div>
                                <div class="review-state-meta" id="topReviewMeta">Materi ini telah ditolak oleh admin. Tidak
                                    dapat direview ulang di sini.</div>
                            </div>
                        @endif

                        <h5 class="card-title">Deskripsi Kelas</h5>
                        <p style="line-height: 1.7; color: #475569;">
                            {{ $material->description ?? 'Tidak ada deskripsi yang ditulis oleh trainer.' }}
                        </p>
                    </div>

                    <div class="card-custom">
                        <h5 class="card-title">Isi Modul ({{ $material->modules->count() }})</h5>
                        <div class="module-list">
                            @php $unitCounter = 1; @endphp
                            @forelse($material->modules->chunk(3) as $unitModules)
                                <div class="unit-group mb-5 p-4 border rounded-4 bg-white shadow-sm">
                                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                                        <div>
                                            <h4 class="fw-bold text-navy m-0">Bab {{ $unitCounter }}</h4>
                                            <p class="text-muted small m-0">Grup: 1 Modul + 1 Video + 1 Kuis</p>
                                        </div>
                                        @if($material->status !== 'rejected')
                                            @php
                                                $allInUnitApproved = $unitModules->every(fn($m) => $m->review_status === 'approved');
                                            @endphp
                                            <form action="{{ route('admin.material.unit.approve', $material) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="unit_no" value="{{ $unitCounter }}">
                                                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4" {{ $allInUnitApproved ? 'disabled' : '' }}>
                                                    <i class="bi bi-check-all me-1"></i> {{ $allInUnitApproved ? 'Sudah Disetujui' : 'Setujui Bab ' . $unitCounter }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    @foreach($unitModules as $module)
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
                                        @endphp
                                        <div class="module-item mb-3 {{ $module->review_status === 'approved' ? 'border-success' : '' }}" style="{{ $module->review_status === 'approved' ? 'border-left: 4px solid #14b8a6; background: #f0fdfa;' : '' }}">
                                            <div class="module-icon" style="{{ $module->review_status === 'approved' ? 'background: #ccfbf1; color: #0d9488;' : '' }}">
                                                @if($module->type == 'video') <i class="bi bi-play-fill"></i>
                                                @elseif($module->type == 'pdf') <i class="bi bi-file-pdf-fill"></i>
                                                @elseif($module->type == 'quiz') <i class="bi bi-question-circle-fill"></i>
                                                @else <i class="bi bi-file-earmark-arrow-down-fill"></i> @endif
                                            </div>
                                            <div class="module-desc">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="m-0 fw-bold">{{ $module->order_no }}. {{ $module->title }}</h6>
                                                    @if($module->review_status === 'approved')
                                                        <span class="badge bg-success rounded-pill" style="font-size: 10px;"><i class="bi bi-check-circle-fill me-1"></i>Approved</span>
                                                    @endif
                                                </div>
                                                <p class="m-0 small text-muted">Tipe: {{ strtoupper($module->type) }} @if($module->duration) •
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

                                                @if($material->status === 'pending_review' || $material->status === 'approved')
                                                    <div class="module-actions mt-2">
                                                        @if($canReviewInline)
                                                            <button type="button" class="module-review-trigger"
                                                                data-review-module-id="{{ $module->id }}"
                                                                data-review-title="{{ e($module->title) }}" data-review-url="{{ $contentUrl }}"
                                                                data-review-kind="{{ $previewKind }}"
                                                                data-review-file="{{ e($module->file_name ?: basename((string) $module->content_url)) }}">
                                                                <i class="bi bi-eye"></i> Review
                                                            </button>
                                                        @endif
                                                        
                                                        @if($canOpenFile)
                                                            <a href="{{ route('admin.material.module.stream', [$material, $module]) }}?download=1"
                                                                class="module-btn module-btn-download">
                                                                <i class="bi bi-download"></i> Unduh
                                                            </a>
                                                        @endif

                                                        @if($material->status === 'pending_review' && $module->review_status !== 'approved')
                                                            <form action="{{ route('admin.material.module.approve', [$material, $module]) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="module-btn bg-success text-white border-0">
                                                                    <i class="bi bi-check-circle"></i> Approve
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @php $unitCounter++; @endphp
                            @empty
                                <div class="text-center py-4 text-muted">Belum ada modul yang diupload.</div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <div class="col-xl-4">
                    <div class="action-box">
                        @if(isset($structureCompleteness))
                            <div class="card-custom side-card mb-3" style="padding: 20px;">
                                <h6 class="side-card-title">Status Upload Materi</h6>
                                @if(($structureCompleteness['is_complete'] ?? false) === true)
                                    <div class="alert alert-success mb-0 py-2 px-3" style="font-size: 0.88rem;">
                                        <i class="bi bi-check-circle-fill me-1"></i>
                                        Semua slot modul sudah terisi.
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-2 py-2 px-3" style="font-size: 0.88rem;">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                        Baru {{ $material->modules->count() }} modul diupload trainer.
                                    </div>
                                    @if(!empty($structureCompleteness['missing_items']))
                                        <ul style="margin: 0; padding-left: 18px; font-size: 0.82rem; color: #7c2d12;">
                                            @foreach($structureCompleteness['missing_items'] as $missingItem)
                                                <li>{{ $missingItem }}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="mt-2 small text-muted">
                                        Admin dapat menyetujui materi yang sudah diupload tanpa menunggu semua slot penuh.
                                    </div>
                                @endif
                            </div>
                        @endif

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
                        @if($material->status !== 'rejected')
                            <div class="card-custom side-card">
                                <h6 class="side-card-title">
                                    Keputusan Admin:</h6>
                                <form action="{{ route('admin.material.approve', $material) }}" method="POST" class="mb-3">
                                    @csrf
                                    <button type="submit" class="btn-approve"
                                        onclick="return confirm('Yakin ingin menyetujui materi yang sudah diupload trainer?')">
                                        <i class="bi bi-check-circle-fill me-2"></i> Setujui Materi yang Diupload
                                    </button>
                                </form>
                                <button type="button" class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                    <i class="bi bi-x-circle me-2"></i> Tolak (Minta Revisi)
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </main>
    </div>

    @if($material->status !== 'rejected')
        <div class="modal fade" id="rejectModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius: 16px; overflow:hidden;">
                    <div class="modal-header bg-danger text-white border-0 p-4">
                        <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle-fill me-2"></i> Alasan Penolakan
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.material.reject', $material) }}" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Catatan untuk Trainer <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control" name="rejection_reason" rows="5" required minlength="10"
                                    placeholder="Jelaskan secara detail bagian mana dari video/slide yang melanggar aturan atau perlu diperbaiki..."></textarea>
                                <small class="text-muted mt-2 d-block">Pesan ini akan muncul di dashboard trainer.</small>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger px-4 fw-bold">Kirim Revisi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @php
        $quizMapForJs = $material->modules
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
            const viewer = document.getElementById('topReviewViewer');
            const title = document.getElementById('topReviewTitle');
            const hint = document.getElementById('topReviewMeta');
            const triggers = document.querySelectorAll('.module-review-trigger');
            const quizMap = @json($quizMapForJs);

            if (!viewer || !title || !hint || triggers.length === 0) {
                return;
            }

            function renderQuiz(moduleId) {
                const questions = quizMap[String(moduleId)] || [];
                viewer.classList.add('is-quiz');

                if (!questions.length) {
                    viewer.innerHTML = '<div class="text-muted">Belum ada soal pada modul kuis ini.</div>';
                    return;
                }

                const items = questions.map((q, idx) => {
                    const answers = (q.answers || []).map((a) => {
                        return `<div class="quiz-preview-answer ${a.is_correct ? 'is-correct' : ''}">${a.text || '-'}</div>`;
                    }).join('');

                    return `
                                        <div class="quiz-preview-item">
                                            <p class="quiz-preview-q">${idx + 1}. ${q.question || 'Tanpa pertanyaan'} ${q.points ? `(${q.points} poin)` : ''}</p>
                                            <div class="quiz-preview-answers">${answers || '<div class="quiz-preview-answer">Belum ada opsi jawaban</div>'}</div>
                                        </div>
                                    `;
                }).join('');

                viewer.innerHTML = `<div class="quiz-preview-head">Review Soal Kuis</div><div class="quiz-preview-list">${items}</div>`;
            }

            function renderPreview(url, kind) {
                viewer.classList.remove('is-quiz');
                if (!url) {
                    viewer.innerHTML = `<div class="text-center opacity-50"><i class="bi bi-file-earmark-x" style="font-size: 4rem;"></i><p class="mt-2">File tidak tersedia</p></div>`;
                    return;
                }

                if (kind === 'video') {
                    viewer.innerHTML = `<video controls controlsList="nodownload"><source src="${url}"></video>`;
                    return;
                }

                if (kind === 'pdf') {
                    viewer.innerHTML = `<iframe src="${url}#toolbar=1&navpanes=0"></iframe>`;
                    return;
                }

                viewer.innerHTML = `<iframe src="${url}"></iframe>`;
            }

            triggers.forEach((btn) => {
                btn.addEventListener('click', function () {
                    const fileUrl = this.getAttribute('data-review-url') || '';
                    const fileKind = this.getAttribute('data-review-kind') || 'file';
                    const moduleTitle = this.getAttribute('data-review-title') || 'Materi';
                    const fileName = this.getAttribute('data-review-file') || 'File';
                    const moduleId = this.getAttribute('data-review-module-id') || '';

                    title.textContent = 'Preview: ' + moduleTitle;
                    hint.textContent = 'Menampilkan: ' + fileName;

                    if (fileKind === 'quiz') {
                        hint.textContent = 'Menampilkan daftar soal dan opsi jawaban modul kuis';
                        renderQuiz(moduleId);
                    } else {
                        renderPreview(fileUrl, fileKind);
                    }

                    viewer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            });
        })();
    </script>
@endsection