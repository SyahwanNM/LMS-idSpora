@extends('layouts.admin')

@section('title', 'Review Material - ' . $material->name)

@section('styles')
    <style>
        /* Custom Navbar */
        .material-navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 152, 0, 0.1);
            padding: 16px 32px;
            box-shadow: 0 2px 12px rgba(255, 152, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .material-navbar h4 {
            font-weight: 800;
            color: #e65100;
            margin: 0;
            font-size: 1.5rem;
        }

        .material-navbar .breadcrumb {
            margin: 0;
            background: transparent;
            padding: 0;
            font-size: 0.85rem;
        }

        /* Main Layout */
        .material-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
        }

        .material-sidebar {
            width: 260px;
            background: #fff;
            padding: 24px 16px;
            border-right: 1px solid #eee;
            flex-shrink: 0;
            position: sticky;
            top: 72px;
            height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .material-main {
            flex-grow: 1;
            padding: 32px;
            background-color: #F8F9FA;
        }

        /* Sidebar Links */
        .nav-menu-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
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
            font-size: 0.9rem;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .sidebar-link i {
            font-size: 1.15rem;
            color: #64748b;
        }

        .sidebar-link:hover {
            background-color: #fff3e0;
            color: #e65100;
        }

        .sidebar-link:hover i {
            color: #e65100;
        }

        /* Hero Section */
        .material-hero {
            background: linear-gradient(135deg, #e65100 0%, #f57c00 50%, #ff9800 100%);
            border-radius: 24px;
            padding: 48px;
            color: #fff;
            margin-bottom: 32px;
            box-shadow: 0 20px 40px rgba(230, 81, 0, 0.2);
        }

        .material-hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 16px;
        }

        .material-hero .meta-info {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.2);
            padding: 8px 16px;
            border-radius: 10px;
            font-size: 0.9rem;
        }

        /* Content Cards */
        .content-card {
            background: #fff;
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
            border: 1px solid #f0f0f0;
        }

        .content-card h5 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #212529;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f5f5f5;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .content-card h5 i {
            color: #e65100;
        }

        /* Video/Media Player */
        .media-player {
            position: relative;
            width: 100%;
            padding-bottom: 56.25%; /* 16:9 aspect ratio */
            border-radius: 16px;
            overflow: hidden;
            background: #000;
        }

        .media-player iframe,
        .media-player video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .media-placeholder {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 16px;
            color: #fff;
            background: linear-gradient(135deg, #424242 0%, #616161 100%);
        }

        .media-placeholder i {
            font-size: 4rem;
            opacity: 0.6;
        }

        /* Module List */
        .module-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            margin-bottom: 12px;
            transition: all 0.2s ease;
        }

        .module-item:hover {
            border-color: #e65100;
            background: #fffbf5;
        }

        .module-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #e65100 0%, #f57c00 100%);
            color: #fff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .module-info {
            flex-grow: 1;
        }

        .module-title {
            font-weight: 700;
            color: #212529;
            margin-bottom: 4px;
        }

        .module-meta {
            font-size: 0.85rem;
            color: #6c757d;
            display: flex;
            gap: 16px;
        }

        .module-type {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            background: #e3f2fd;
            color: #1565c0;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Quiz Preview */
        .quiz-item {
            padding: 16px;
            background: #f8f9fa;
            border-radius: 12px;
            margin-bottom: 12px;
        }

        .quiz-question {
            font-weight: 600;
            color: #212529;
            margin-bottom: 8px;
        }

        .quiz-answers {
            padding-left: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }

        /* Action Buttons */
        .action-buttons {
            position: sticky;
            bottom: 20px;
            background: #fff;
            padding: 24px;
            border-radius: 20px;
            box-shadow: 0 -4px 24px rgba(0, 0, 0, 0.15);
            display: flex;
            gap: 16px;
            justify-content: center;
        }

        .btn-approve {
            background: linear-gradient(135deg, #2e7d32 0%, #388e3c 100%);
            color: #fff;
            padding: 16px 40px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            border: 0;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-approve:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(46, 125, 50, 0.4);
        }

        .btn-reject {
            background: #fff;
            color: #d32f2f;
            padding: 16px 40px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 1rem;
            border: 2px solid #d32f2f;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-reject:hover {
            background: #d32f2f;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(211, 47, 47, 0.4);
        }

        .btn-back {
            background: #fff;
            color: #6c757d;
            padding: 16px 32px;
            border-radius: 14px;
            font-weight: 700;
            border: 2px solid #dee2e6;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-back:hover {
            border-color: #adb5bd;
            color: #495057;
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: 0;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #d32f2f 0%, #f44336 100%);
            color: #fff;
            border-radius: 20px 20px 0 0;
            padding: 24px 32px;
        }

        .modal-header h5 {
            font-weight: 800;
            margin: 0;
        }

        .modal-header .btn-close {
            filter: brightness(0) invert(1);
        }

        .modal-body {
            padding: 32px;
        }

        .modal-footer {
            border-top: 0;
            padding: 0 32px 32px;
        }

        /* Info Box */
        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #1976d2;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 16px;
        }

        .info-box i {
            color: #1976d2;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .material-sidebar {
                display: none;
            }

            .material-main {
                padding: 20px;
            }

            .material-hero {
                padding: 32px 24px;
            }

            .material-hero h1 {
                font-size: 1.8rem;
            }

            .action-buttons {
                flex-direction: column;
            }
        }
    </style>
@endsection

@section('content')
    <!-- Custom Navbar -->
    <nav class="material-navbar">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4>🔍 Review Material</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.material.approvals') }}">Material Approval</a></li>
                        <li class="breadcrumb-item active">Review</li>
                    </ol>
                </nav>
            </div>
        </div>
    </nav>

    <div class="material-wrapper">
        <!-- Sidebar Navigation -->
        <aside class="material-sidebar d-none d-lg-block">
            <span class="nav-menu-label">APPROVAL STATUS</span>
            <a href="{{ route('admin.material.approvals') }}" class="sidebar-link">
                <i class="bi bi-hourglass-split"></i> Pending Review
            </a>
            <a href="{{ route('admin.material.approved') }}" class="sidebar-link">
                <i class="bi bi-check-circle"></i> Approved
            </a>
            <a href="{{ route('admin.material.rejected') }}" class="sidebar-link">
                <i class="bi bi-x-circle"></i> Rejected
            </a>

            <span class="nav-menu-label">QUICK ACCESS</span>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="{{ route('admin.trainer.index') }}" class="sidebar-link">
                <i class="bi bi-people"></i> Trainers
            </a>
        </aside>

        <main class="material-main">
            <!-- Hero Section -->
            <div class="material-hero">
                <span class="badge bg-warning text-dark mb-3" style="padding: 10px 16px; font-size: 0.85rem;">
                    ⏳ PENDING REVIEW
                </span>
                <h1>{{ $material->name }}</h1>
                <div class="meta-info">
                    <div class="meta-item">
                        <i class="bi bi-person-fill"></i>
                        <span>{{ $material->trainer->name ?? 'Unknown Trainer' }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-folder-fill"></i>
                        <span>{{ $material->category->name ?? 'Uncategorized' }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-calendar-fill"></i>
                        <span>{{ $material->created_at->format('d F Y') }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="bi bi-files"></i>
                        <span>{{ $material->modules->count() }} Modul</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Video/Media Preview -->
                    <div class="content-card">
                        <h5>
                            <i class="bi bi-play-circle-fill"></i>
                            Media Preview
                        </h5>
                        <div class="media-player">
                            @if($material->media && $material->media_type === 'video')
                                <iframe src="{{ $material->media }}" frameborder="0" allowfullscreen></iframe>
                            @elseif($material->media_type === 'image')
                                <img src="{{ $material->media }}" alt="{{ $material->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            @else
                                <div class="media-placeholder">
                                    <i class="bi bi-file-earmark-play"></i>
                                    <p>No media preview available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="content-card">
                        <h5>
                            <i class="bi bi-file-text-fill"></i>
                            Deskripsi Materi
                        </h5>
                        @if($material->description)
                            <p style="color: #495057; line-height: 1.8; white-space: pre-line;">{{ $material->description }}</p>
                        @else
                            <p class="text-muted"><em>Tidak ada deskripsi.</em></p>
                        @endif
                    </div>

                    <!-- Modules List -->
                    <div class="content-card">
                        <h5>
                            <i class="bi bi-collection-fill"></i>
                            Daftar Modul ({{ $material->modules->count() }})
                        </h5>
                        @if($material->modules->count() > 0)
                            @foreach($material->modules as $module)
                                <div class="module-item">
                                    <div class="module-number">{{ $module->order_no }}</div>
                                    <div class="module-info">
                                        <div class="module-title">{{ $module->title }}</div>
                                        <div class="module-meta">
                                            <span class="module-type">
                                                @if($module->type === 'video')
                                                    <i class="bi bi-play-circle-fill"></i> Video
                                                @elseif($module->type === 'pdf')
                                                    <i class="bi bi-file-pdf-fill"></i> PDF
                                                @elseif($module->type === 'quiz')
                                                    <i class="bi bi-question-circle-fill"></i> Kuis
                                                @else
                                                    <i class="bi bi-file-earmark-fill"></i> {{ ucfirst($module->type) }}
                                                @endif
                                            </span>
                                            @if($module->duration)
                                                <span><i class="bi bi-clock me-1"></i>{{ $module->duration }} menit</span>
                                            @endif
                                            @if($module->is_free)
                                                <span class="text-success"><i class="bi bi-unlock-fill me-1"></i>Gratis</span>
                                            @endif
                                        </div>
                                        @if($module->description)
                                            <small class="text-muted mt-2 d-block">{{ Str::limit($module->description, 100) }}</small>
                                        @endif
                                    </div>
                                </div>

                                <!-- Quiz Questions Preview -->
                                @if($module->type === 'quiz' && $module->quizQuestions->count() > 0)
                                    <div class="ms-5 mb-3">
                                        <small class="text-muted d-block mb-2">
                                            <i class="bi bi-question-circle me-1"></i>
                                            {{ $module->quizQuestions->count() }} Pertanyaan Kuis
                                        </small>
                                        @foreach($module->quizQuestions->take(3) as $question)
                                            <div class="quiz-item">
                                                <div class="quiz-question">{{ $loop->iteration }}. {{ $question->question }}</div>
                                                <div class="quiz-answers">
                                                    @if($question->option_a) • {{ $question->option_a }}<br>@endif
                                                    @if($question->option_b) • {{ $question->option_b }}<br>@endif
                                                    @if($question->option_c) • {{ $question->option_c }}<br>@endif
                                                    @if($question->option_d) • {{ $question->option_d }}@endif
                                                </div>
                                            </div>
                                        @endforeach
                                        @if($module->quizQuestions->count() > 3)
                                            <small class="text-muted">... dan {{ $module->quizQuestions->count() - 3 }} pertanyaan lainnya</small>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p class="text-muted"><em>Belum ada modul yang ditambahkan.</em></p>
                        @endif
                    </div>
                </div>

                <div class="col-lg-4">
                    <!-- Trainer Info -->
                    <div class="content-card">
                        <h5>
                            <i class="bi bi-person-badge-fill"></i>
                            Trainer
                        </h5>
                        <div class="text-center mb-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($material->trainer->name ?? 'Unknown') }}&background=e65100&color=fff&bold=true&size=200"
                                alt="{{ $material->trainer->name ?? 'Unknown' }}"
                                style="width: 100px; height: 100px; border-radius: 50%; border: 4px solid #f0f0f0;">
                        </div>
                        <h6 class="text-center mb-2" style="font-weight: 700;">{{ $material->trainer->name ?? 'Unknown' }}</h6>
                        <p class="text-center text-muted mb-3" style="font-size: 14px;">{{ $material->trainer->email ?? '-' }}</p>
                        @if($material->trainer->bio)
                            <p style="font-size: 14px; color: #6c757d; line-height: 1.6;">{{ $material->trainer->bio }}</p>
                        @endif
                        <a href="{{ route('admin.trainer.show', $material->trainer) }}" class="btn btn-outline-primary w-100 mt-2">
                            <i class="bi bi-eye me-2"></i>Lihat Profile
                        </a>
                    </div>

                    <!-- Material Info -->
                    <div class="content-card">
                        <h5>
                            <i class="bi bi-info-circle-fill"></i>
                            Detail Materi
                        </h5>
                        <table style="width: 100%; font-size: 0.9rem;">
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 12px 0; color: #6c757d;">Level</td>
                                <td style="padding: 12px 0; font-weight: 600; text-align: right;">{{ $material->level ?? 'Semua Level' }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 12px 0; color: #6c757d;">Durasi</td>
                                <td style="padding: 12px 0; font-weight: 600; text-align: right;">{{ $material->duration ?? 0 }} menit</td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 12px 0; color: #6c757d;">Harga</td>
                                <td style="padding: 12px 0; font-weight: 600; text-align: right;">
                                    @if($material->price > 0)
                                        Rp {{ number_format($material->price, 0, ',', '.') }}
                                    @else
                                        <span class="text-success">GRATIS</span>
                                    @endif
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid #f0f0f0;">
                                <td style="padding: 12px 0; color: #6c757d;">Jumlah Modul</td>
                                <td style="padding: 12px 0; font-weight: 600; text-align: right;">{{ $material->modules->count() }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 12px 0; color: #6c757d;">Upload Date</td>
                                <td style="padding: 12px 0; font-weight: 600; text-align: right;">{{ $material->created_at->format('d M Y') }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- Review Guidelines -->
                    <div class="info-box">
                        <h6 style="font-weight: 700; margin-bottom: 12px;">
                            <i class="bi bi-lightbulb-fill me-2"></i>Panduan Review
                        </h6>
                        <ul style="font-size: 0.85rem; line-height: 1.8; margin-bottom: 0; padding-left: 20px;">
                            <li>Pastikan konten video/media jelas</li>
                            <li>Periksa kelengkapan modul pembelajaran</li>
                            <li>Verifikasi kuis dan jawaban yang benar</li>
                            <li>Cek kesesuaian dengan kategori</li>
                            <li>Pastikan tidak ada konten berbahaya</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="{{ route('admin.material.approvals') }}" class="btn-back">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
                <button type="button" class="btn-reject" data-bs-toggle="modal" data-bs-target="#rejectModal">
                    <i class="bi bi-x-circle-fill"></i>
                    Minta Perbaikan
                </button>
                <form action="{{ route('admin.material.approve', $material) }}" method="POST" style="margin: 0;">
                    @csrf
                    <button type="submit" class="btn-approve"
                        onclick="return confirm('✅ Apakah Anda yakin ingin menyetujui materi ini?\n\nMateri akan langsung dipublikasikan dan bisa diakses peserta.')">
                        <i class="bi bi-check-circle-fill"></i>
                        Setujui & Publikasikan
                    </button>
                </form>
            </div>
        </main>
    </div>

    <!-- Rejection Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Minta Perbaikan Materi
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.material.reject', $material) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="alert alert-warning" role="alert">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Catatan penting:</strong> Jelaskan secara spesifik bagian mana yang perlu diperbaiki agar trainer bisa melakukan revisi dengan tepat.
                        </div>

                        <div class="mb-3">
                            <label for="rejection_reason" class="form-label" style="font-weight: 700;">
                                Alasan Penolakan / Catatan Revisi <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="8"
                                required minlength="10" maxlength="1000"
                                placeholder="Contoh:&#10;• Video di modul 2 suaranya kurang jelas di menit ke-2:30&#10;• PDF modul 3 tidak bisa dibuka, mohon upload ulang&#10;• Kuis modul 5 ada jawaban yang salah di soal nomor 3&#10;• Deskripsi materi perlu ditambahkan informasi lebih detail"
                                style="border: 2px solid #e9ecef; border-radius: 12px; padding: 16px; font-size: 0.9rem;"></textarea>
                            <small class="text-muted">Minimal 10 karakter, maksimal 1000 karakter</small>
                        </div>

                        <div class="info-box" style="background: #fff3cd; border-left-color: #ffc107;">
                            <i class="bi bi-bell-fill me-2" style="color: #ffc107;"></i>
                            <small>Trainer akan menerima notifikasi dan bisa melihat catatan ini di dashboard mereka.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                            style="border-radius: 10px; padding: 12px 24px; font-weight: 600;">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-danger"
                            style="border-radius: 10px; padding: 12px 32px; font-weight: 700;">
                            <i class="bi bi-send-fill me-2"></i>
                            Kirim Catatan Revisi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
