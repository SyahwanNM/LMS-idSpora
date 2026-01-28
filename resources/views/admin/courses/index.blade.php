<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.navbar-admin-course')
    @if(session('success'))
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div id="courseUpdatedToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            try {
                var el = document.getElementById('courseUpdatedToast');
                if (window.bootstrap && el) {
                    var t = new bootstrap.Toast(el);
                    t.show();
                }
            } catch (e) {}
        });
    </script>
    @endif
    <!-- Publish warning toast (shown when course has missing material) -->
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div id="publishWarningToast" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4500">
                <div class="d-flex">
                    <div class="toast-body">
                        <span class="me-2" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.982 1.767h13.706c.89 0 1.438-.99.982-1.767L8.982 1.566zM8 5.5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 8 5.5Zm0 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" />
                            </svg>
                        </span>
                        Lengkapkan Material Course Modules terlebih dahulu sebelum menerbitkan Course
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
    <!-- Already published toast (shown when course is already active) -->
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div id="alreadyPublishedToast" class="toast align-items-center text-bg-info border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body">
                        Course ini sudah diterbitkan
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
    <div class="box_luar_course_builder">
        <h1 class="judul_course_builder">Daftar Course</h1>
        <p class="deskripsi_course_builder">Atur detail course sebelum dipublikasi</p>
        <a href="{{ route('admin.add-course') }}" class="tambah_course" style="text-decoration: none;">
            <svg style="margin-top: 7px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
            </svg>
            <p style="margin-top: 2px;">Tambah Course</p>
        </a>
        <div class="box_daftar_course">
            <h4 class="judul_daftar_course">Daftar Course yang Ada</h4>
            <table class="tabel_daftar_course">
                <thead>
                    <tr>
                        <th>Nama Course</th>
                        <th>Level</th>
                        <th>Harga</th>
                        <th>Status Kelengkapan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    @php
                    $hasModules = ($course->modules && $course->modules->count() > 0);
                    $isPublished = ($course->status === 'active');
                    @endphp
                    <tr>
                        <td>{{ $course->name }}</td>
                        <td>{{ ucfirst($course->level) }}</td>
                        <td>Rp. {{ number_format($course->price, 0, ',', '.') }}</td>
                        <td>
                            @if($isPublished)
                            <button class="status_kelengkapan_complete">Complete</button>
                            @elseif(!$hasModules)
                            <button class="status_kelengkapan_miss">Missing Material</button>
                            @else
                            <button class="status_kelengkapan_inprogress">In Progress</button>
                            @endif
                        </td>
                        <td>
                            <div class="aksi_daftar_course d-flex gap-2">
                                <a href="{{ route('admin.courses.edit', $course) }}" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                        <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                        <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                    </svg>
                                </a>
                                @php
                                $previewData = [
                                'title' => $course->name,
                                'image' => $course->card_thumbnail ? Storage::url($course->card_thumbnail) : '',
                                'description' => trim($course->description),
                                'modules' => ($course->modules && $course->modules->count() > 0) ? $course->modules->implode('title', '||') : '',
                                'published' => $isPublished ? '1' : '0',
                                'level' => ucfirst($course->level),
                                'price' => 'Rp. ' . number_format($course->price, 0, ',', '.'),
                                'duration' => $course->duration . ' jam',
                                ];
                                @endphp
                                <button type="button" class="btn p-0 preview-course" title="Preview" data-course="{{ base64_encode(json_encode($previewData)) }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                        <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                        <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                    </svg>
                                </button>
                                <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Hapus course ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn p-0" title="Delete">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                            <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Belum ada course.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
    <div class="preview">
        <div class="modal" id="coursePreviewModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="coursePreviewLabel" class="modal-title">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="option">
                        <div class="list-option">
                            <button class="tab-btn active" data-target="tab-ringkasan">Ringkasan</button>
                            <button class="tab-btn" data-target="tab-pdf">Modul PDF</button>
                            <button class="tab-btn" data-target="tab-video">Video</button>
                            <button class="tab-btn" data-target="tab-kuis">Kuis</button>
                        </div>
                    </div>
                    <div class="modal-body">
                        <div id="tab-ringkasan" class="tab-content active">
                            <h3 id="modal-course-name">Nama Course</h3>
                            <p id="modal-course-desc">Deskripsi singkat course akan muncul di sini.</p>
                            <div class="info-detail">
                                <div class="list-info info-purple">
                                    <h5>ID Course</h5>
                                    <h4>#1</h4>
                                </div>
                                <div class="list-info info-blue">
                                    <h5>LEVEL</h5>
                                    <h4 id="cp-level">Beginner</h4>
                                </div>
                                <div class="list-info info-green">
                                    <h5>HARGA</h5>
                                    <h4 id="cp-price">Rp250.000</h4>
                                </div>
                                <div class="list-info info-yellow">
                                    <h5>Status</h5>
                                    <h4 id="cp-status">Selesai</h4>
                                </div>
                            </div>
                            <div class="ringkasan-konten">
                                <h3>Ringkasan Konten</h3>
                                <div class="info-ringkasan">
                                    <div class="list-ringkasan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark" viewBox="0 0 16 16">
                                            <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z" />
                                        </svg>
                                        <div class="detail-ringkasan">
                                            <h5 id="count-pdf">0</h5>
                                            <p>Modul PDF</p>
                                        </div>
                                    </div>
                                    <div class="list-ringkasan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-camera-video-fill" viewBox="0 0 16 16">
                                            <path fill-rule="evenodd" d="M0 5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 4.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 13H2a2 2 0 0 1-2-2z" />
                                        </svg>
                                        <div class="detail-ringkasan">
                                            <h5>2</h5>
                                            <p>Video Pembelajaran</p>
                                        </div>
                                    </div>
                                    <div class="list-ringkasan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />

                                            <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                                        </svg>
                                        <div class="detail-ringkasan">
                                            <h5>2</h5>
                                            <p>Kuis</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tab-pdf" class="tab-content">
                            <div class="pdf-content" id="list-pdf-container">
                                <div class="list-pdf">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z" />
                                    </svg>
                                    <div class="detail-pdf">
                                        <h4>Pengenalan UI/UX Dasar</h4>
                                        <p>Materi dasar tentang UI dan UX design</p>
                                    </div>
                                </div>

                                <div class="list-pdf">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z" />
                                    </svg>
                                    <div class="detail-pdf">
                                        <h4>Prinsip Desain</h4>
                                        <p>Pelajari prinsip-prinsip desain yang fundamental</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tab-video" class="tab-content">
                            <div class="video-content" id="list-video-container">
                                <div class="list-video">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-camera-video-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M0 5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 4.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 13H2a2 2 0 0 1-2-2z" />
                                    </svg>
                                    <div class="detail-video">
                                        <h4>Pengenalan UI/UX Dasar</h4>
                                        <p>Materi dasar tentang UI dan UX design</p>
                                    </div>
                                </div>

                                <div class="list-video">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-camera-video-fill" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M0 5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 4.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 13H2a2 2 0 0 1-2-2z" />
                                    </svg>
                                    <div class="detail-video">
                                        <h4>Prinsip Desain</h4>
                                        <p>Pelajari prinsip-prinsip desain yang fundamental</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tab-kuis" class="tab-content">
                            <div class="kuis-content" id="list-kuis-container">
                                <div class="list-kuis">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                                    </svg>
                                    <div class="detail-kuis">
                                        <h4>Kuis Evaluasi: Fundamental Design</h4>
                                        <div class="soal-passing">
                                            <div class="info-item">
                                                <p>Jumlah Soal</p>
                                                <h5>15 Soal</h5>
                                            </div>
                                            <div class="info-item passing-score">
                                                <p>Passing Score</p>
                                                <h5>80%</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="list-kuis">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                        <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                        <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                                    </svg>
                                    <div class="detail-kuis">
                                        <h4>Final Test UI/UX</h4>
                                        <div class="soal-passing">
                                            <div class="info-item">
                                                <p>Jumlah Soal</p>
                                                <h5>20 Soal</h5>
                                            </div>
                                            <div class="info-item passing-score">
                                                <p>Passing Score</p>
                                                <h5>75%</h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var modalEl = document.getElementById('coursePreviewModal');
            var modal = null;

            // --- 1. Logic Tab Switching ---
            const tabButtons = document.querySelectorAll('.tab-btn');
            const tabContents = document.querySelectorAll('.tab-content');

            tabButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const target = this.getAttribute('data-target');

                    // Update Button Active State
                    tabButtons.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');

                    // Update Content Visibility
                    tabContents.forEach(content => {
                        content.style.display = 'none';
                        content.classList.remove('active');
                    });

                    const targetEl = document.getElementById(target);
                    if (targetEl) {
                        targetEl.style.display = 'block';
                        targetEl.classList.add('active');
                    }
                });
            });

            // --- 2. Modal Helper Functions (Bootstrap & Fallback) ---
            if (window.bootstrap && modalEl) {
                try {
                    modal = new window.bootstrap.Modal(modalEl);
                } catch (e) {}
            }
            var _fallbackBackdrop = null;

            function showCourseModal() {
                // Reset ke tab Ringkasan setiap kali modal dibuka
                if (tabButtons.length > 0) {
                    tabButtons.forEach(b => b.classList.remove('active'));
                    tabButtons[0].classList.add('active');
                    tabContents.forEach(c => {
                        c.style.display = 'none';
                        c.classList.remove('active');
                    });
                    tabContents[0].style.display = 'block';
                    tabContents[0].classList.add('active');
                }

                if (window.bootstrap && modal) {
                    try {
                        modal.show();
                        return;
                    } catch (e) {}
                }
                if (!modalEl) return;
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                document.body.classList.add('modal-open');
                if (!_fallbackBackdrop) {
                    _fallbackBackdrop = document.createElement('div');
                    _fallbackBackdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(_fallbackBackdrop);
                }
            }

            function hideCourseModal() {
                if (window.bootstrap && modal) {
                    try {
                        modal.hide();
                        return;
                    } catch (e) {}
                }
                if (!modalEl) return;
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                document.body.classList.remove('modal-open');
                if (_fallbackBackdrop) {
                    _fallbackBackdrop.remove();
                    _fallbackBackdrop = null;
                }
            }

            if (modalEl) {
                modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(btn => {
                    btn.addEventListener('click', hideCourseModal);
                });
                modalEl.addEventListener('click', ev => {
                    if (ev.target === modalEl) hideCourseModal();
                });
            }

            // --- 3. Content Setter Functions ---
            function setText(id, val) {
                var el = document.getElementById(id);
                if (el) el.textContent = val || '';
            }

            function setImage(id, url) {
                var el = document.getElementById(id);
                if (el) {
                    el.src = url || '';
                    el.style.display = url ? 'block' : 'none';
                }
            }

            // --- 4. Event Delegation for Preview Click ---
            document.addEventListener('click', function(ev) {
                var btn = ev.target.closest('.preview-course');
                if (!btn) return;

                var raw = btn.getAttribute('data-course') || '';
                var data = {};
                try {
                    data = JSON.parse(atob(raw));
                } catch (e) {
                    try {
                        data = JSON.parse(raw);
                    } catch (e2) {
                        data = {};
                    }
                }

                // Update UI Dasar
                setText('coursePreviewLabel', 'Preview Course: ' + (data.title || ''));
                // Gunakan ID yang ada di modal-body kamu
                var mainTitle = modalEl.querySelector('.modal-body h3');
                var mainDesc = modalEl.querySelector('.modal-body p');
                if (mainTitle) mainTitle.textContent = data.title;
                if (mainDesc) mainDesc.textContent = data.description;

                setText('cp-level', data.level);
                setText('cp-price', data.price);

                // Logic menghitung modul untuk Ringkasan
                var modsCount = 0;
                if (data.modules) {
                    var modsArray = data.modules.split('||').filter(Boolean);
                    modsCount = modsArray.length;

                    // Jika ingin mengisi list PDF secara dinamis di Tab PDF:
                    var pdfListContainer = document.getElementById('list-pdf-content');
                    if (pdfListContainer) {
                        pdfListContainer.innerHTML = modsArray.map(m => `<li class="list-group-item">${m}</li>`).join('');
                    }
                }
                // Update angka ringkasan
                setText('count-pdf', modsCount);

                showCourseModal();
            });
        });
    </script>
</body>

</html>