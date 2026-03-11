@extends('layouts.admin')

@section('title', 'Review Material - ' . $material->name)

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        body {
            background-color: #f8fafc;
        }

        .material-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
        }

        .material-sidebar {
            width: 260px;
            background: #fff;
            padding: 24px 16px;
            border-right: 1px solid #e2e8f0;
            flex-shrink: 0;
            position: sticky;
            top: 72px;
            height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .nav-menu-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #64748b;
            letter-spacing: 1px;
            margin: 24px 0 12px 16px;
            display: block;
        }

        .nav-menu-label:first-child {
            margin-top: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 10px 16px;
            color: #0f172a;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .sidebar-link i {
            font-size: 1.1rem;
            color: #64748b;
        }

        .sidebar-link:hover {
            background-color: #f1f5f9;
            color: #4338ca;
        }

        .sidebar-link.active {
            background-color: #1e1b4b;
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
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .btn-back {
            background: #fff;
            border: 1px solid #cbd5e1;
            color: #475569;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
        }

        .btn-back:hover {
            background: #f1f5f9;
            color: #1e293b;
        }

        /* Card Setup */
        .card-custom {
            background: #fff;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #1e293b;
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

        /* Sidebar Kanan (Action) */
        .trainer-box {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: #f8fafc;
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

        .btn-approve {
            width: 100%;
            background: #16a34a;
            color: white;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 1rem;
        }

        .btn-approve:hover {
            background: #15803d;
        }

        .btn-reject {
            width: 100%;
            background: #fff;
            color: #dc2626;
            border: 2px solid #dc2626;
            padding: 12px;
            border-radius: 10px;
            font-weight: 700;
        }

        .btn-reject:hover {
            background: #fef2f2;
        }
    </style>
@endsection

@section('content')
    <div class="material-wrapper">
        <aside class="material-sidebar d-none d-lg-block">
            <span class="nav-menu-label">TRAINER MANAGEMENT</span>
            <a href="{{ route('admin.trainer.index') }}" class="sidebar-link">
                <i class="bi bi-people"></i> All Trainers
            </a>
            <a href="{{ route('admin.trainer.create') }}" class="sidebar-link">
                <i class="bi bi-person-plus"></i> Add New Trainer
            </a>

            <span class="nav-menu-label">QUICK ACCESS</span>
            <a href="#materialApprovalMenu"
                class="sidebar-link sidebar-parent {{ request()->routeIs('admin.material.*') ? 'active' : '' }}"
                data-bs-toggle="collapse" role="button"
                aria-expanded="{{ request()->routeIs('admin.material.*') ? 'true' : 'false' }}"
                aria-controls="materialApprovalMenu">
                <span><i class="bi bi-clipboard-check"></i> Material Approval</span>
                <i class="bi bi-chevron-down sidebar-chevron"></i>
            </a>
            <div class="collapse sidebar-submenu {{ request()->routeIs('admin.material.*') ? 'show' : '' }}"
                id="materialApprovalMenu">
                <a href="{{ route('admin.material.approvals') }}"
                    class="sidebar-link {{ request()->routeIs('admin.material.approvals') ? 'active' : '' }}">
                    <i class="bi bi-hourglass-split"></i> Pending Review
                </a>
                <a href="{{ route('admin.material.approved') }}"
                    class="sidebar-link {{ request()->routeIs('admin.material.approved') ? 'active' : '' }}">
                    <i class="bi bi-check-circle"></i> Approved
                </a>
                <a href="{{ route('admin.material.rejected') }}"
                    class="sidebar-link {{ request()->routeIs('admin.material.rejected') ? 'active' : '' }}">
                    <i class="bi bi-x-circle"></i> Rejected
                </a>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.courses.index') }}" class="sidebar-link">
                <i class="bi bi-book"></i> Courses
            </a>
            <a href="{{ route('admin.events.history') }}" class="sidebar-link">
                <i class="bi bi-calendar-event"></i> Events
            </a>
        </aside>

        <main class="material-main">
            <div class="page-header">
                <a href="{{ route('admin.material.approvals') }}" class="btn-back"><i
                        class="bi bi-arrow-left me-2"></i>Kembali</a>
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill"><i
                        class="bi bi-hourglass-split me-1"></i>Status: Pending Review</span>
            </div>

            <div class="row">
                <div class="col-xl-8">
                    <div class="card-custom">
                        <h1 class="fw-bold text-dark mb-2 fs-3">{{ $material->name }}</h1>
                        <p class="text-muted mb-4">{{ $material->category->name ?? 'Kategori Umum' }} • Diupload
                            {{ $material->created_at->format('d M Y') }}
                        </p>

                        <div class="video-container mb-4">
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

                        <h5 class="card-title">Deskripsi Kelas</h5>
                        <p style="line-height: 1.7; color: #475569;">
                            {{ $material->description ?? 'Tidak ada deskripsi yang ditulis oleh trainer.' }}
                        </p>
                    </div>

                    <div class="card-custom">
                        <h5 class="card-title">Isi Modul ({{ $material->modules->count() }})</h5>
                        <div class="module-list">
                            @forelse($material->modules as $module)
                                <div class="module-item">
                                    <div class="module-icon">
                                        @if($module->type == 'video') <i class="bi bi-play-fill"></i>
                                        @elseif($module->type == 'pdf') <i class="bi bi-file-pdf-fill"></i>
                                        @else <i class="bi bi-question-circle-fill"></i> @endif
                                    </div>
                                    <div class="module-desc">
                                        <h6>{{ $module->order_no }}. {{ $module->title }}</h6>
                                        <p>Tipe: {{ strtoupper($module->type) }} @if($module->duration) •
                                        {{ $module->duration }} Menit @endif
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">Belum ada modul yang diupload. <strong
                                        class="text-danger">Wajib Reject!</strong></div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-xl-4">
                    <div class="action-box">
                        <div class="card-custom mb-3" style="padding: 20px;">
                            <h6 class="fw-bold text-muted mb-3" style="font-size: 0.8rem; text-transform: uppercase;">Dibuat
                                Oleh:</h6>
                            <div class="trainer-box m-0">
                                <img src="{{ $material->trainer?->avatar_url ?? 'https://ui-avatars.com/api/?name=Trainer' }}"
                                    alt="Trainer">
                                <div>
                                    <h6 class="fw-bold m-0 text-dark">{{ $material->trainer?->name ?? 'Anonim' }}</h6>
                                    <p class="m-0 text-muted" style="font-size: 0.8rem;">Instruktur</p>
                                </div>
                            </div>
                        </div>

                        <div class="card-custom">
                            <h6 class="fw-bold text-muted mb-3" style="font-size: 0.8rem; text-transform: uppercase;">
                                Keputusan Admin:</h6>

                            <form action="{{ route('admin.material.approve', $material) }}" method="POST" class="mb-3">
                                @csrf
                                <button type="submit" class="btn-approve"
                                    onclick="return confirm('Yakin ingin menyetujui dan mem-publish kelas ini?')">
                                    <i class="bi bi-check-circle-fill me-2"></i> Setujui (Publish)
                                </button>
                            </form>

                            <button type="button" class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal">
                                <i class="bi bi-x-circle me-2"></i> Tolak (Minta Revisi)
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

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
@endsection