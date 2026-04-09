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
    <style>
        .box_luar_course_builder { padding-top: 60px; }
        .judul_course_builder { margin-top: 0; margin-bottom: 6px; line-height: 1.2; }
        .deskripsi_course_builder { margin-top: 0; }
    </style>
</head>

<body>
    @include('partials.navbar-admin-course')
    <style>
        /* New global notification component (replaces legacy Bootstrap toasts on this page) */
        .global-notification { position: fixed; top: 14px; right: 14px; display:flex; flex-direction:column; gap:10px; align-items:flex-end; z-index:12050; pointer-events:none; }
        .notification { min-width: 300px; max-width:420px; pointer-events:auto; display:flex; align-items:center; gap:12px; padding:12px 14px; border-radius:12px; box-shadow: 0 8px 30px rgba(2,6,23,0.12); color:#fff; transform: translateY(-6px) scale(.99); opacity:0; transition: transform .22s cubic-bezier(.2,.9,.2,1), opacity .22s ease; }
        .notification.show { transform: translateY(0) scale(1); opacity:1; }
        .notification.success { background: linear-gradient(90deg,#16a34a,#34d399); }
        .notification.error { background: linear-gradient(90deg,#dc2626,#f43f5e); }
        .notification .notif-message{ flex:1; font-weight:600; font-size:0.95rem; }
        .notification .notif-close { background:transparent; border:0; color:rgba(255,255,255,.95); }
    </style>

    @if(session('success') || session('login_success') || session('error') || session('publish_warning') || session('already_published'))
        <div id="globalNotifications" class="global-notification" aria-live="polite" aria-atomic="true">
            @if(session('login_success'))
                <div class="notification success" role="status" data-timeout="4200">
                    <div class="notif-message">{{ session('login_success') }}</div>
                    <button class="notif-close" aria-label="Close" type="button">&times;</button>
                </div>
            @elseif(session('success'))
                <div class="notification success" role="status" data-timeout="3800">
                    <div class="notif-message">{{ session('success') }}</div>
                    <button class="notif-close" aria-label="Close" type="button">&times;</button>
                </div>
            @endif

            @php
                $pw = session('publish_warning');
                $pwList = is_array($pw) ? array_values(array_filter($pw)) : [];
            @endphp
            @if(!empty($pwList))
                <div class="notification error" role="status" data-timeout="6000">
                    <div class="notif-message">Oops, modul course belum lengkap: {{ implode(', ', $pwList) }} belum ada. Segera hubungi trainer.</div>
                    <button class="notif-close" aria-label="Close" type="button">&times;</button>
                </div>
            @elseif(session('already_published'))
                <div class="notification error" role="status" data-timeout="5000">
                    <div class="notif-message">Course ini sudah diterbitkan</div>
                    <button class="notif-close" aria-label="Close" type="button">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="notification error" role="status" data-timeout="6000">
                    <div class="notif-message">{{ session('error') }}</div>
                    <button class="notif-close" aria-label="Close" type="button">&times;</button>
                </div>
            @endif
        </div>
    @endif

    <script>
        (function(){
            function wireBanner(){
                try {
                    const wrap = document.getElementById('globalNotifications');
                    if(!wrap) return;
                    wrap.querySelectorAll('.notification').forEach(function(n){
                        setTimeout(function(){ n.classList.add('show'); }, 20);
                        const timeout = parseInt(n.getAttribute('data-timeout') || 4000, 10);
                        const closeBtn = n.querySelector('.notif-close');
                        const hide = function(){ n.classList.remove('show'); setTimeout(()=> n.remove(), 260); };
                        if(closeBtn) closeBtn.addEventListener('click', hide);
                        setTimeout(hide, timeout);
                    });
                } catch(e){}
            }

            document.addEventListener('DOMContentLoaded', wireBanner);

            window.adminNotify = function(type, message, timeout){
                try {
                    const kind = (type === 'error') ? 'error' : 'success';
                    const text = (message == null) ? '' : String(message);
                    const ms = Number.isFinite(Number(timeout)) ? Math.max(800, Number(timeout)) : 3800;

                    let wrap = document.getElementById('globalNotifications');
                    if(!wrap){
                        wrap = document.createElement('div');
                        wrap.id = 'globalNotifications';
                        wrap.className = 'global-notification';
                        wrap.setAttribute('aria-live', 'polite');
                        wrap.setAttribute('aria-atomic', 'true');
                        document.body.appendChild(wrap);
                    }

                    const n = document.createElement('div');
                    n.className = 'notification ' + kind;
                    n.setAttribute('role', 'status');
                    n.setAttribute('data-timeout', String(ms));

                    const msg = document.createElement('div');
                    msg.className = 'notif-message';
                    msg.textContent = text;

                    const close = document.createElement('button');
                    close.className = 'notif-close';
                    close.setAttribute('aria-label', 'Close');
                    close.type = 'button';
                    close.innerHTML = '&times;';

                    n.appendChild(msg);
                    n.appendChild(close);
                    wrap.appendChild(n);

                    const hide = function(){ n.classList.remove('show'); setTimeout(()=> n.remove(), 260); };
                    close.addEventListener('click', hide);
                    setTimeout(function(){ n.classList.add('show'); }, 20);
                    setTimeout(hide, ms);
                } catch(e){}
            };
        })();
    </script>

    <!-- Publish confirmation modal (UI, no browser alert/confirm) -->
    <div class="modal fade" id="publishConfirmModal" tabindex="-1" aria-labelledby="publishConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="publishConfirmModalLabel">Course belum lengkap</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div style="font-weight:600;">Oops, modul course belum lengkap.</div>
                    <div class="mt-2">Berikut yang belum ada:</div>
                    <ul id="publishMissingList" class="mt-2 mb-3" style="padding-left: 18px;"></ul>
                    <div class="text-muted" style="font-size: 0.9rem;">Segera hubungi trainer untuk melengkapi modul, video, dan kuis.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id="publishConfirmProceedBtn">Tetap Publish</button>
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
            <div class="box_cari_course_builder">
                <div class="box_filter_cari">
                    <div class="cari_pendapatan">
                        <div class="box_pendapatan_per_course">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                            </svg>
                            <input class="cari_course_builder" type="text" placeholder="Cari Course" value="{{ request('q') }}">
                        </div>
                    </div>
                    <div class="box_filter_course_builder">
                        <p class="mulai_course">Bulan</p>
                        <input class="tanggal_course" type="month" value="{{ request('month') }}">
                        <button class="btn_terapkan" id="applyRevenueFilter">Terapkan</button>
                    </div>
                    <div class="box_unduh_course">
                        <a class="btn_unduh" id="exportPdfBtn" href="{{ route('admin.courses.export', ['format' => 'pdf']) }}" style="text-decoration:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-download" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
                                <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
                            </svg>
                            <p>Export PDF</p>
                        </a>
                        <a class="btn_unduh" id="exportExcelBtn" href="{{ route('admin.courses.export', ['format' => 'excel']) }}" style="text-decoration:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-arrow-down-fill" viewBox="0 0 16 16">
                                <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0M9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1m-1 4v3.793l1.146-1.147a.5.5 0 0 1 .708.708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708L7.5 11.293V7.5a.5.5 0 0 1 1 0" />
                            </svg>
                            <p>Export Excel</p>
                        </a>
                    </div>


                </div>
            </div>
            <table class="tabel_daftar_course">
                <thead>
                    <tr>
                        <th>Nama Course</th>
                        <th>Level</th>
                        <th>Harga</th>
                        <th>Status Kelengkapan</th>
                        <th>Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                    @php
                    $hasModules = ($course->modules && $course->modules->count() > 0);
                    $isPublished = ($course->status === 'active');

                    $modulesCol = $course->modules ?? collect();
                    $totalModules = $modulesCol->count();
                    $pdfSlots = $modulesCol->where('type', 'pdf');
                    $videoSlots = $modulesCol->where('type', 'video');
                    $quizSlots = $modulesCol->where('type', 'quiz');

                    $pdfCount = $pdfSlots->count();
                    $videoCount = $videoSlots->count();
                    $quizCount = $quizSlots->count();

                    $hasMissingPdf = ($pdfCount <= 0) || ($pdfSlots->filter(fn($m) => empty($m->content_url))->count() > 0);
                    $hasMissingVideo = ($videoCount <= 0) || ($videoSlots->filter(fn($m) => empty($m->content_url))->count() > 0);
                    $hasMissingQuiz = false;
                    if ($quizCount <= 0) {
                        $hasMissingQuiz = true;
                    } else {
                        $hasMissingQuiz = $quizSlots->filter(function ($m) {
                            $cnt = null;
                            if (isset($m->quiz_questions_count)) {
                                $cnt = (int) $m->quiz_questions_count;
                            } elseif (method_exists($m, 'relationLoaded') && $m->relationLoaded('quizQuestions')) {
                                $cnt = $m->quizQuestions ? (int) $m->quizQuestions->count() : 0;
                            }
                            $cnt = (int) ($cnt ?? 0);
                            return $cnt <= 0;
                        })->count() > 0;
                    }

                    $missingForPublish = [];
                    if ($totalModules <= 0) { $missingForPublish[] = 'Modul'; }
                    if ($hasMissingPdf) { $missingForPublish[] = 'Modul (PDF)'; }
                    if ($hasMissingVideo) { $missingForPublish[] = 'Video'; }
                    if ($hasMissingQuiz) { $missingForPublish[] = 'Kuis'; }

                    $hasMissingMaterial = !empty($missingForPublish);
                    @endphp
                    <tr>
                        <td>{{ $course->name }}</td>
                        <td>{{ ucfirst($course->level) }}</td>
                        <td>Rp. {{ number_format($course->price, 0, ',', '.') }}</td>
                        <td>
                            @if($isPublished)
                            <button class="status_kelengkapan_complete">Complete</button>
                            @elseif($hasMissingMaterial)
                            <button class="status_kelengkapan_miss">Missing Material</button>
                            @else
                            <button class="status_kelengkapan_inprogress">In Progress</button>
                            @endif
                        </td>
                        <td>
                            @php
                            $coursePayments = $course->manualPayments ?? collect();
                            $countPending = $coursePayments->where('status', 'pending')->count();
                            $countApproved = $coursePayments->where('status', 'settled')->count();
                            $countRejected = $coursePayments->where('status', 'rejected')->count();
                            @endphp

                            <div class="d-flex flex-wrap gap-1 align-items-center">
                                <span class="badge text-bg-warning">Pending: {{ $countPending }}</span>
                                <span class="badge text-bg-success">Approved: {{ $countApproved }}</span>
                                <span class="badge text-bg-danger">Rejected: {{ $countRejected }}</span>
                            </div>

                            @if($coursePayments->count() > 0)
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#coursePaymentsModal-{{ $course->id }}">
                                Lihat user
                            </button>

                            <div class="modal fade" id="coursePaymentsModal-{{ $course->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Pembayaran Manual - {{ $course->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>User</th>
                                                            <th>WhatsApp</th>
                                                            <th>Referral</th>
                                                            <th>Status</th>
                                                            <th>Bukti</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($coursePayments->sortByDesc('created_at') as $payment)
                                                        @php
                                                        $proof = $payment->proofs->sortByDesc('created_at')->first();
                                                        $status = $payment->status;
                                                        $statusLabel = $status === 'settled' ? 'Approved' : ucfirst($status);
                                                        $statusClass = $status === 'settled' ? 'text-bg-success' : ($status === 'rejected' ? 'text-bg-danger' : 'text-bg-warning');
                                                        @endphp
                                                        <tr>
                                                            <td>
                                                                <div class="fw-semibold">{{ $payment->user->name ?? 'User' }}</div>
                                                                <div class="text-muted" style="font-size:12px">{{ $payment->user->email ?? '' }}</div>
                                                            </td>
                                                            <td>{{ $payment->whatsapp_number ?? '-' }}</td>
                                                            <td>{{ $payment->referral_code ?: '-' }}</td>
                                                            <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                                                            <td>
                                                                @if($proof)
                                                                @php
                                                                $proofPath = ltrim((string) ($proof->file_path ?? ''), '/');
                                                                if (\Illuminate\Support\Str::startsWith($proofPath, 'uploads/')) {
                                                                $proofPath = substr($proofPath, strlen('uploads/'));
                                                                }
                                                                $proofUrl = $proofPath !== '' ? asset('uploads/' . $proofPath) : '#';
                                                                @endphp
                                                                <a class="btn btn-sm btn-outline-secondary" target="_blank" href="{{ $proofUrl }}">Lihat</a>
                                                                @else
                                                                <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                @if($status === 'pending')
                                                                    <form method="POST" class="d-flex flex-wrap gap-1 m-0">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-sm btn-success"
                                                                            formaction="{{ route('admin.courses.manual-payments.approve', [$course, $payment]) }}"
                                                                            onclick="return confirm('Approve pembayaran ini?')">
                                                                            Approve
                                                                        </button>
                                                                        <button type="button" class="btn btn-sm btn-danger js-reject-course-payment"
                                                                            data-action="{{ route('admin.courses.manual-payments.reject', [$course, $payment]) }}"
                                                                            data-user="{{ $payment->user->name ?? 'User' }}"
                                                                            data-course="{{ $course->name }}">
                                                                            Reject
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    <span class="text-muted">-</span>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="text-muted mt-2" style="font-size:12px">Belum ada pembayaran.</div>
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
                                'id' => $course->id,
                                'title' => $course->name,
                                'image' => $course->card_thumbnail ? Storage::disk('public')->url($course->card_thumbnail) : '',
                                'description' => trim($course->description),
                                'enroll_count' => (int)($course->enrollments_count ?? 0),
                                'edit_url' => route('admin.courses.edit', $course),
                                'has_trainer' => !empty($course->trainer_id),
                                'modules' => $course->modules->map(function($m) {
                                return [
                                'type' => $m->type, // pdf, video, quiz
                                'title' => $m->title,
                                'subtitle' => $m->description ?? '',
                                'duration' => $m->formatted_duration ?? '',
                                // Extra fields for Quiz if needed
                                'question_count' => $m->type === 'quiz' ? $m->quizQuestions->count() : 0,
                                // Content completeness marker for preview
                                'has_content' => $m->type === 'quiz'
                                    ? (($m->quizQuestions->count() ?? 0) > 0)
                                    : !empty($m->content_url),
                                ];
                                })->values()->toArray(),
                                'published' => $isPublished ? '1' : '0',
                                'level' => ucfirst($course->level),
                                'price' => 'Rp. ' . number_format($course->price, 0, ',', '.'),
                                'duration' => $course->duration . ' jam',
                                'status_text' => $isPublished ? 'Published' : ($hasModules ? 'Draft' : 'Incomplete'),
                                'participants_url' => route('admin.courses.participants', $course),
                                ];
                                @endphp
                                <button type="button" class="btn_view_course btn p-0 preview-course" title="Preview" data-course="{{ base64_encode(json_encode($previewData)) }}">
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
                                <form method="POST" action="{{ route('admin.courses.publish', $course) }}" class="m-0 publish-course-form">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-sm {{ $isPublished ? 'btn-success' : 'btn-outline-success' }} btn_publish_course js-publish-course"
                                        data-published="{{ $isPublished ? '1' : '0' }}"
                                        data-missing='@json($missingForPublish)'>
                                        {{ $isPublished ? 'Published' : 'Publish' }}
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">Belum ada course.</td>
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
            <div class="modal-dialog ">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 id="coursePreviewLabel" class="modal-title">Modal title</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="option">
                        <div class="list_box_preview">
                            <div class="list-option">
                                <button class="tab-btn active" data-target="tab-ringkasan">Ringkasan</button>
                                <button class="tab-btn" data-target="tab-pdf">Modul PDF</button>
                                <button class="tab-btn" data-target="tab-video">Video</button>
                                <button class="tab-btn" data-target="tab-kuis">Kuis</button>
                                <button class="tab-btn" data-target="tab-participant">Participant</button>
                            </div>

                            <div>
                                <button id="coursePreviewEditBtn" type="button" class="button_edit_preview">Edit</button>
                            </div>
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
                                <div class="list-info info-green">
                                    <h5>STUDENT ENROLL</h5>
                                    <h4 id="cp-enroll-count">0 Peserta</h4>
                                </div>
                                <div class="list-info info-yellow">
                                    <h5>Benefit</h5>
                                    <h4>
                                        <ol>
                                            <li>Sertifikat</li>
                                            <li>Materi</li>
                                            <li>Video</li>
                                        </ol>
                                    </h4>
                                </div>
                            </div>
                            <div class="ringkasan-konten">
                                <h3>Deskripsi Konten</h3>
                                <div class="info-ringkasan">
                                    <p id="cp-content-description">-</p>
                                </div>
                            </div>
                            <div class="ringkasan-konten">
                                <h3>Syllabus</h3>
                                <div class="syllabus-ringkasan">
                                    <ol id="cp-syllabus-list"></ol>
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
                                            <h5 id="count-video">0</h5>
                                            <p>Video Pembelajaran</p>
                                        </div>
                                    </div>
                                    <div class="list-ringkasan">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />

                                            <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                                        </svg>
                                        <div class="detail-ringkasan">
                                            <h5 id="count-quiz">0</h5>
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
                        <div id="tab-participant" class="tab-content">

                            <div class="participant-table">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Nama Peserta</th>
                                            <th>Email</th>
                                            <th>Progress</th>
                                            <th>Status</th>
                                            <th>Tanggal Aktif</th>
                                        </tr>
                                    </thead>

                                    <tbody id="participantTableBody">
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Pilih course untuk melihat participant.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
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

            // Keep Edit as a real button; redirect using data attribute
            (function bindEditButton(){
                var editBtn = document.getElementById('coursePreviewEditBtn');
                if (!editBtn) return;
                if (editBtn.dataset.bound === '1') return;
                editBtn.dataset.bound = '1';
                editBtn.addEventListener('click', function() {
                    var url = this.dataset.editUrl || '';
                    if (!url) return;
                    window.location.href = url;
                });
            })();

            // --- 3. Content Setter Functions ---
            function setText(id, val) {
                var el = document.getElementById(id);
                if (el) el.textContent = (val === null || typeof val === 'undefined') ? '' : String(val);
            }

            function setImage(id, url) {
                var el = document.getElementById(id);
                if (el) {
                    el.src = url || '';
                    el.style.display = url ? 'block' : 'none';
                }
            }

            function escapeHtml(value) {
                var s = String(value ?? '');
                return s
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function moduleHasContent(m) {
                if (!m) return false;
                if (typeof m.has_content !== 'undefined') return !!m.has_content;
                if (String(m.type || '') === 'quiz') return Number(m.question_count || 0) > 0;
                return !!(m.content_url || '');
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
                setText('modal-course-name', data.title || '-');
                setText('modal-course-desc', data.description || 'Tidak ada deskripsi.');

                // Edit button (keep as <button> to preserve shape)
                (function(){
                    var editBtn = document.getElementById('coursePreviewEditBtn');
                    if (!editBtn) return;
                    editBtn.dataset.editUrl = (data && data.edit_url) ? String(data.edit_url) : '';
                })();

                setText('cp-level', data.level || '-');
                setText('cp-price', data.price || 'Rp0');

                // Student enroll (jumlah user yang enroll course)
                (function(){
                    var enrollEl = document.getElementById('cp-enroll-count');
                    if (!enrollEl) return;
                    var n = 0;
                    try { n = parseInt(data.enroll_count || '0', 10); } catch (e) { n = 0; }
                    if (!Number.isFinite(n) || n < 0) n = 0;
                    enrollEl.textContent = n + ' Peserta';
                })();

                // Status Color
                var statusEl = document.getElementById('cp-status');
                if (statusEl) {
                    statusEl.textContent = data.status_text || '-';
                    // Reset colors
                    statusEl.parentElement.className = 'list-info'; // base
                    if (data.published === '1') statusEl.parentElement.classList.add('info-green'); // Active
                    else if (data.status_text === 'Incomplete') statusEl.parentElement.classList.add('info-yellow'); // Warning
                    else statusEl.parentElement.classList.add('info-blue'); // Draft
                }

                // --- MODULES PARSING ---
                var modules = data.modules || [];
                var visibleModules = Array.isArray(modules)
                    ? modules.filter(function(m) { return moduleHasContent(m); })
                    : [];

                // --- CONTENT DESCRIPTION ---
                setText('cp-content-description', data.description || 'Tidak ada deskripsi.');

                // --- SYLLABUS (judul bab modul) ---
                (function renderSyllabus(){
                    var ol = document.getElementById('cp-syllabus-list');
                    if (!ol) return;
                    if (!Array.isArray(visibleModules) || visibleModules.length === 0) {
                        ol.innerHTML = '<li class="text-muted">Belum ada materi yang disetujui.</li>';
                        return;
                    }
                    ol.innerHTML = visibleModules
                        .map(function(m){ return '<li>' + escapeHtml(m && m.title ? m.title : '-') + '</li>'; })
                        .join('');
                })();

                // --- PARTICIPANTS ---
                (function loadParticipants() {
                    var tbody = document.getElementById('participantTableBody');
                    if (!tbody) return;

                    tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Memuat participant...</td></tr>';

                    var url = data.participants_url;
                    if (!url) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Data participant tidak tersedia.</td></tr>';
                        return;
                    }

                    fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(res) {
                        if (!res.ok) throw new Error('HTTP ' + res.status);
                        return res.json();
                    })
                    .then(function(json) {
                        var participants = (json && json.participants) ? json.participants : [];
                        if (!participants.length) {
                            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Belum ada peserta yang enroll course ini.</td></tr>';
                            return;
                        }

                        tbody.innerHTML = participants.map(function(p) {
                            var name = (p && p.name) ? p.name : '-';
                            var email = (p && p.email) ? p.email : '-';
                            var progress = (p && typeof p.progress_percent !== 'undefined') ? (String(p.progress_percent) + '%') : '0%';
                            var status = (p && p.status_label) ? p.status_label : (p && p.status ? p.status : '-');
                            var enrolledAt = (p && p.enrolled_at) ? p.enrolled_at : '-';
                            return (
                                '<tr>' +
                                    '<td>' + escapeHtml(name) + '</td>' +
                                    '<td>' + escapeHtml(email) + '</td>' +
                                    '<td>' + escapeHtml(progress) + '</td>' +
                                    '<td>' + escapeHtml(status) + '</td>' +
                                    '<td>' + escapeHtml(enrolledAt) + '</td>' +
                                '</tr>'
                            );
                        }).join('');
                    })
                    .catch(function() {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Gagal memuat participant.</td></tr>';
                    });
                })();

                // 1. Hitung Ringkasan (hanya yang sudah ada konten/diapprove)
                var countPdf = visibleModules.filter(m => m.type === 'pdf').length;
                var countVideo = visibleModules.filter(m => m.type === 'video').length;
                var countQuiz = visibleModules.filter(m => m.type === 'quiz').length;

                setText('count-pdf', countPdf);
                setText('count-video', countVideo); // Asumsi ID elemen ringkasan video adalah 'count-video' (perlu ditambahkan di HTML jika belum ada)
                setText('count-quiz', countQuiz); // Asumsi ID elemen ringkasan kuis adalah 'count-quiz'

                // --- RENDER TAB CONTENTS ---

                // 1. PDF Tab
                var pdfContainer = document.getElementById('list-pdf-container');
                if (pdfContainer) {
                    var pdfsAll = Array.isArray(modules) ? modules.filter(m => m.type === 'pdf') : [];
                    var pdfs = visibleModules.filter(m => m.type === 'pdf');
                    var missingPdfCount = pdfsAll.filter(m => !moduleHasContent(m)).length;
                    if (pdfs.length === 0) {
                        pdfContainer.innerHTML = '<p class="text-center text-muted my-4">Belum ada modul PDF yang disetujui.</p>';
                    } else {
                        pdfContainer.innerHTML = pdfs.map(m => `
                             <div class="list-pdf">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5z" />
                                </svg>
                                <div class="detail-pdf">
                                    <h4>${m.title}</h4>
                                    <p>${m.subtitle || 'Dokumen Materi'}</p>
                                </div>
                            </div>
                        `).join('');
                    }
                }

                // 2. Video Tab
                var vidContainer = document.getElementById('list-video-container');
                if (vidContainer) {
                    var vidsAll = Array.isArray(modules) ? modules.filter(m => m.type === 'video') : [];
                    var vids = visibleModules.filter(m => m.type === 'video');
                    var missingVideoCount = vidsAll.filter(m => !moduleHasContent(m)).length;
                    if (vids.length === 0) {
                        vidContainer.innerHTML = '<p class="text-center text-muted my-4">Belum ada video yang disetujui.</p>';
                    } else {
                        vidContainer.innerHTML = vids.map(m => `
                            <div class="list-video">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-camera-video-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M0 5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 4.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 13H2a2 2 0 0 1-2-2z" />
                                </svg>
                                <div class="detail-video">
                                    <h4>${m.title}</h4>
                                    <p>${m.subtitle || 'Video Lesson'}</p>
                                </div>
                            </div>
                        `).join('');
                    }
                }

                // 3. Quiz Tab
                var quizContainer = document.getElementById('list-kuis-container');
                if (quizContainer) {
                    var quizzesAll = Array.isArray(modules) ? modules.filter(m => m.type === 'quiz') : [];
                    var quizzes = visibleModules.filter(m => m.type === 'quiz');
                    var missingQuizCount = quizzesAll.filter(m => Number(m.question_count || 0) <= 0).length;
                    if (quizzes.length === 0) {
                        quizContainer.innerHTML = '<p class="text-center text-muted my-4">Belum ada kuis yang disetujui.</p>';
                    } else {
                        quizContainer.innerHTML = quizzes.map(m => `
                             <div class="list-kuis">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-check-circle" viewBox="0 0 16 16">
                                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16" />
                                    <path d="m10.97 4.97-.02.022-3.473 4.425-2.093-2.094a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05" />
                                </svg>
                                <div class="detail-kuis">
                                    <h4>${m.title}</h4>
                                    <div class="soal-passing">
                                        <div class="info-item">
                                            <p>Jumlah Soal</p>
                                            <h5>${m.question_count || 0} Soal</h5>
                                        </div>
                                        <div class="info-item passing-score">
                                            <p>Passing Score</p>
                                            <h5>75% (${Math.ceil((m.question_count || 0) * 0.75)} Soal)</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }
                }

                showCourseModal();
            });
        });
    </script>

    <script>
        (function() {
            function buildExportUrl(anchorEl, format) {
                if (!anchorEl) return null;

                var url = new URL(anchorEl.getAttribute('href'), window.location.origin);
                url.searchParams.set('format', format);

                var qEl = document.querySelector('.cari_course_builder');
                var monthEl = document.querySelector('.tanggal_course');

                var q = qEl ? String(qEl.value || '').trim() : '';
                var month = monthEl ? String(monthEl.value || '').trim() : '';

                if (q !== '') url.searchParams.set('q', q);
                else url.searchParams.delete('q');

                if (month !== '') url.searchParams.set('month', month);
                else url.searchParams.delete('month');

                return url.toString();
            }

            var pdfBtn = document.getElementById('exportPdfBtn');
            var excelBtn = document.getElementById('exportExcelBtn');

            if (pdfBtn) {
                pdfBtn.addEventListener('click', function(ev) {
                    try {
                        var u = buildExportUrl(pdfBtn, 'pdf');
                        if (u) pdfBtn.setAttribute('href', u);
                    } catch (e) {}
                });
            }

            if (excelBtn) {
                excelBtn.addEventListener('click', function(ev) {
                    try {
                        var u = buildExportUrl(excelBtn, 'excel');
                        if (u) excelBtn.setAttribute('href', u);
                    } catch (e) {}
                });
            }
        })();
    </script>

    <script>
        (function() {
            var applyBtn = document.getElementById('applyRevenueFilter');
            var qEl = document.querySelector('.cari_course_builder');
            var monthEl = document.querySelector('.tanggal_course');
            var baseUrl = @json(route('admin.courses.index'));

            function applyFilters() {
                try {
                    var url = new URL(baseUrl, window.location.origin);
                    var q = qEl ? String(qEl.value || '').trim() : '';
                    var month = monthEl ? String(monthEl.value || '').trim() : '';

                    if (q !== '') url.searchParams.set('q', q);
                    if (month !== '') url.searchParams.set('month', month);

                    window.location.href = url.toString();
                } catch (e) {
                    // fallback
                    window.location.href = baseUrl;
                }
            }

            if (applyBtn) {
                applyBtn.addEventListener('click', function(ev) {
                    ev.preventDefault();
                    applyFilters();
                });
            }

            if (qEl) {
                qEl.addEventListener('keydown', function(ev) {
                    if (ev.key === 'Enter') {
                        ev.preventDefault();
                        applyFilters();
                    }
                });
            }
        })();
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function notifyError(message, ms) {
                if (typeof window.adminNotify === 'function') {
                    window.adminNotify('error', message, ms || 5000);
                }
            }

            // Pre-publish warning (client-side): inform admin before publishing incomplete content
            var pendingPublishForm = null;
            var publishModalEl = document.getElementById('publishConfirmModal');
            var publishProceedBtn = document.getElementById('publishConfirmProceedBtn');
            var publishMissingList = document.getElementById('publishMissingList');

            function escapeHtml(s) {
                return String(s)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function openPublishModal(missing, formEl) {
                pendingPublishForm = formEl || null;

                if (publishMissingList) {
                    publishMissingList.innerHTML = (missing || []).map(function(x) {
                        return '<li>' + escapeHtml(x) + ' belum ada</li>';
                    }).join('');
                }

                try {
                    if (window.bootstrap && window.bootstrap.Modal && publishModalEl) {
                        window.bootstrap.Modal.getOrCreateInstance(publishModalEl).show();
                        return;
                    }
                } catch (e) {}

                // If bootstrap modal is unavailable, fall back to showing warning toast only (no browser popup)
                notifyError('Oops, modul course belum lengkap. Segera hubungi trainer untuk melengkapi modul, video, dan kuis.', 6000);
                pendingPublishForm = null;
            }

            if (publishProceedBtn) {
                publishProceedBtn.addEventListener('click', function() {
                    try {
                        if (pendingPublishForm) {
                            pendingPublishForm.submit();
                        }
                    } catch (e) {}
                });
            }

            if (publishModalEl) {
                publishModalEl.addEventListener('hidden.bs.modal', function() {
                    pendingPublishForm = null;
                });
            }

            document.addEventListener('click', function(ev) {
                var btn = ev.target.closest('.js-publish-course');
                if (!btn) return;

                if ((btn.dataset.published || '') === '1') {
                    ev.preventDefault();
                    notifyError('Course ini sudah diterbitkan', 4500);
                    return;
                }

                var missing = [];
                try {
                    missing = JSON.parse(btn.getAttribute('data-missing') || '[]');
                    if (!Array.isArray(missing)) missing = [];
                } catch (e) {
                    missing = [];
                }

                if (missing.length > 0) {
                    ev.preventDefault();
                    openPublishModal(missing, btn.closest('form'));
                    return;
                }
            });

            // Reject course manual payment: show modal with rejection reason options (same as event)
            var rejectModalEl = document.getElementById('rejectCoursePaymentModal');
            var rejectFormEl = document.getElementById('rejectCoursePaymentForm');
            var rejectReasonEl = document.getElementById('rejectCoursePaymentReason');
            var rejectMetaEl = document.getElementById('rejectCoursePaymentMeta');

            function openRejectCoursePaymentModal(btnEl) {
                if (!btnEl || !rejectModalEl || !rejectFormEl) return;

                var action = btnEl.getAttribute('data-action') || '';
                rejectFormEl.setAttribute('action', action);

                if (rejectReasonEl) {
                    rejectReasonEl.value = '';
                }

                if (rejectMetaEl) {
                    var user = btnEl.getAttribute('data-user') || 'User';
                    var course = btnEl.getAttribute('data-course') || '';
                    rejectMetaEl.textContent = course ? (user + ' • ' + course) : user;
                }

                try {
                    if (window.bootstrap && window.bootstrap.Modal) {
                        window.bootstrap.Modal.getOrCreateInstance(rejectModalEl).show();
                    }
                } catch (e) {}
            }

            document.addEventListener('click', function(ev) {
                var btn = ev.target.closest('.js-reject-course-payment');
                if (!btn) return;
                ev.preventDefault();
                openRejectCoursePaymentModal(btn);
            });
        });
    </script>

    <!-- Reject Course Manual Payment Modal (UI, no browser alert/confirm) -->
    <div class="modal fade" id="rejectCoursePaymentModal" tabindex="-1" aria-labelledby="rejectCoursePaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectCoursePaymentModalLabel">Tolak Pembayaran Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectCoursePaymentForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="text-muted" style="font-size: 0.9rem;" id="rejectCoursePaymentMeta"></div>
                        <p class="mt-2 mb-2">Pilih alasan penolakan:</p>
                        <div class="mb-3">
                            <label for="rejectCoursePaymentReason" class="form-label text-danger">Alasan Penolakan <span class="text-danger">*</span></label>
                            <select class="form-select" id="rejectCoursePaymentReason" name="reason" required>
                                <option value="" selected disabled>Pilih alasan penolakan</option>
                                <option value="Nominal pembayaran kurang">Nominal pembayaran kurang</option>
                                <option value="Nominal pembayaran lebih">Nominal pembayaran lebih</option>
                                <option value="Gambar bukti pembayaran blur/buram. Silahkan kirim ulang">Gambar bukti pembayaran blur/buram. Silahkan kirim ulang</option>
                                <option value="Pembayaran dinyatakan tidak valid">Pembayaran dinyatakan tidak valid</option>
                            </select>
                            <div class="form-text">Alasan ini akan digunakan sebagai keterangan penolakan.</div>
                        </div>
                    </div>
                    <div class="modal-footer d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary btn-sm px-3 w-auto" style="flex: 0 0 auto;" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger btn-sm px-3 w-auto" style="flex: 0 0 auto;">Tolak Pembayaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>