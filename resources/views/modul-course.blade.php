@include("partials.navbar-after-login")

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>quiz</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="box_modul_luar">
        <div class="box_modul_kiri">
            @php
                $modulesList = $modules ?? (isset($course) ? ($course->modules ?? collect()) : collect());
                $modulesList = $modulesList instanceof \Illuminate\Support\Collection ? $modulesList->values() : collect($modulesList)->values();
                $activeModule = $currentModule ?? $modulesList->first();
                $passingPercent = 75;

                $freeAccessMode = $freeAccessMode ?? ((isset($course) && (int)($course->price ?? 0) <= 0) ? (string)($course->free_access_mode ?? 'limit_2') : 'all');
                $freeAccessibleModuleIds = $freeAccessibleModuleIds ?? [];
                if (!is_array($freeAccessibleModuleIds)) {
                    $freeAccessibleModuleIds = [];
                }
                $isFreeLimited = ((int)($course->price ?? 0) <= 0) && ((string)$freeAccessMode === 'limit_2');

                $passedQuizModuleIds = [];
                if (auth()->check() && $modulesList->isNotEmpty()) {
                    $quizModuleIds = $modulesList
                        ->filter(fn($m) => strtolower(trim((string) ($m->type ?? ''))) === 'quiz')
                        ->pluck('id')
                        ->map(fn($id) => (int) $id)
                        ->values()
                        ->all();

                    if (!empty($quizModuleIds)) {
                        $completedAttempts = \App\Models\QuizAttempt::query()
                            ->where('user_id', auth()->id())
                            ->whereIn('course_module_id', $quizModuleIds)
                            ->whereNotNull('completed_at')
                            ->orderByDesc('completed_at')
                            ->limit(200)
                            ->get();

                        $passedQuizModuleIds = $completedAttempts
                            ->filter(fn($a) => $a->isPassed($passingPercent))
                            ->pluck('course_module_id')
                            ->map(fn($id) => (int) $id)
                            ->unique()
                            ->values()
                            ->all();
                    }
                }

                $currentQuizPassed = true;
                if ($activeModule && strtolower(trim((string) ($activeModule->type ?? ''))) === 'quiz') {
                    $currentQuizPassed = in_array((int) $activeModule->id, $passedQuizModuleIds, true);
                }
            @endphp

            {{-- UI tetap seperti sebelumnya, tapi konten dinamis dari backend --}}
            <div class="accordion-box"
                data-learn-base="{{ isset($course) ? route('course.learn', $course->id) : '' }}"
                data-active-module-id="{{ $activeModule?->id }}"
            >
                @forelse($modulesList as $m)
                    @php
                        $isActive = $activeModule && ((int)$activeModule->id === (int)$m->id);
                        $typeLabel = $m->type ? strtoupper($m->type) : 'MATERI';
                        $prevModule = $modulesList->get($loop->index - 1);
                        $isLocked = false;
                        $lockReason = '';

                        if ($isFreeLimited && !in_array((int) $m->id, $freeAccessibleModuleIds, true)) {
                            $isLocked = true;
                            $lockReason = 'free';
                        }
                        if (auth()->check() && $prevModule && strtolower(trim((string) ($prevModule->type ?? ''))) === 'quiz') {
                            if (!$isLocked) {
                                $isLocked = !in_array((int) $prevModule->id, $passedQuizModuleIds, true);
                                if ($isLocked) {
                                    $lockReason = 'quiz';
                                }
                            }
                        }
                        $descLines = [];
                        if (!empty($m->description)) {
                            $cleanDesc = trim(strip_tags((string) $m->description));
                            if ($cleanDesc !== '') {
                                // Split into short lines to preserve the original "list" feel
                                $descLines = array_values(array_filter(preg_split('/\r?\n|\.|\!|\?/u', $cleanDesc)));
                            }
                        }
                        $descLines = array_slice($descLines, 0, 3);
                    @endphp

                    <div class="accordion-item {{ $isActive ? 'selected active' : '' }} {{ $isLocked ? 'is-locked' : '' }}" data-locked="{{ $isLocked ? '1' : '0' }}" data-locked-reason="{{ $lockReason }}">
                        <button class="accordion-header" type="button" data-module-id="{{ $m->id }}">
                            <span style="display:flex; align-items:center; gap:10px; min-width:0;">
                                <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $m->title ?? 'Materi' }}</span>
                            </span>
                            <span style="display:flex; align-items:center; gap:10px; flex:0 0 auto;">
                                @if($isLocked)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#111827" viewBox="0 0 16 16" aria-hidden="true">
                                        <path d="M8 1a3 3 0 0 0-3 3v3H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2h-1V4a3 3 0 0 0-3-3m2 6V4a2 2 0 1 0-4 0v3z"/>
                                    </svg>
                                @endif
                                <span class="arrow">▲</span>
                            </span>
                        </button>
                        <div class="accordion-content">
                            <p>Tipe: {{ $typeLabel }}</p>
                            @if($isLocked)
                                <hr>
                                @if($lockReason === 'free')
                                    <p class="text-muted" style="margin:0; font-size:13px;">Terkunci. Course gratis ini hanya membuka 2 modul pertama.</p>
                                @else
                                    <p class="text-muted" style="margin:0; font-size:13px;">Terkunci. Lulus kuis dulu untuk membuka materi berikutnya.</p>
                                @endif
                            @endif
                            @if(!empty($descLines))
                                <hr>
                                @foreach($descLines as $idx => $line)
                                    <p>{{ trim($line) }}</p>
                                    @if($idx < count($descLines) - 1)
                                        <hr>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-muted" style="padding:12px;">Belum ada modul pada course ini.</div>
                @endforelse
            </div>
        </div>
        <div class="box_modul_kanan">
            @php
                $cm = $activeModule;
                $content = $cm->content_url ?? null;
                $isHttp = is_string($content) && (str_starts_with($content, 'http://') || str_starts_with($content, 'https://'));
                $storageUrl = null;
                if (is_string($content)) {
                    $normalized = ltrim($content, '/');
                    if (\Illuminate\Support\Str::startsWith($normalized, 'uploads/')) {
                        $normalized = substr($normalized, strlen('uploads/'));
                    }
                    $storageUrl = asset('uploads/' . $normalized);
                }
                $streamUrl = (!$isHttp && isset($course) && $cm) ? route('user.modules.stream', [$course, $cm]) : null;
                $videoUrl = $isHttp ? $content : ($storageUrl ?: $streamUrl);
                $moduleType = $cm ? strtolower(trim((string) ($cm->type ?? ''))) : '';
                $isVideo = $cm && ($moduleType === 'video');
                $isPdf = $cm && ($moduleType === 'pdf');
                $isQuiz = $cm && ($moduleType === 'quiz');
            @endphp

            {{-- Hapus area media kosong untuk modul kuis --}}
            @if(!($isQuiz ?? false))
                <div class="modul_media_card">
                    {{-- Tampilan tetap: iframe/video area di atas --}}
                    @if($isVideo)
                        @if($isHttp)
                            <iframe class="video_course" width="560" height="315" src="{{ $content }}" title="YouTube video player" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        @else
                            <video class="video_course" width="560" height="315" controls preload="auto" playsinline>
                                <source src="{{ $videoUrl }}" type="{{ $cm->mime_type ?: 'video/mp4' }}">
                            </video>
                        @endif
                    @elseif($isPdf)
                        <iframe class="video_course" width="560" height="315" src="{{ $isHttp ? $content : ($streamUrl ?: $storageUrl) }}" title="PDF" frameborder="0"></iframe>
                    @else
                        <iframe class="video_course" width="560" height="315" src="" title="Content" frameborder="0"></iframe>
                    @endif
                </div>
            @endif
            <h2 class="judul_modul">{{ $cm->title ?? 'Gambaran Umum' }}</h2>

            @if(!($isQuiz ?? false))
                <div class="box_luar_deskripsi_modul">
                    <div class="box_deskripsi_modul">
                        <p class="deskripsi_modul">{{ $cm->description ?? 'Deskripsi modul belum tersedia.' }}</p>
                    </div>
                </div>
            @else
                @php
                    $questionCount = $cm?->quizQuestions?->count() ?? 0;
                    $passingPercent = 75;
                    $durationSeconds = (int) ($cm->duration ?? 0);
                    $durationMinutes = $durationSeconds > 0 ? (int) ceil($durationSeconds / 60) : 0;
                    $durationText = $durationMinutes > 0 ? ($durationMinutes.' menit') : '5 menit';

                    $beforeQuizTitle = null;
                    if ($cm && isset($modulesList) && $modulesList instanceof \Illuminate\Support\Collection) {
                        $idx = $modulesList->search(fn($x) => (int) ($x->id ?? 0) === (int) $cm->id);
                        if ($idx !== false && $idx > 0) {
                            $prev = $modulesList->get($idx - 1);
                            $beforeQuizTitle = $prev?->title ?? null;
                        }
                    }
                    $beforeQuizTitle = $beforeQuizTitle ?: ($cm->title ?? 'materi sebelumnya');

                    $attempts = collect();
                    if (auth()->check() && $cm) {
                        $attempts = \App\Models\QuizAttempt::query()
                            ->where('user_id', auth()->id())
                            ->where('course_module_id', $cm->id)
                            ->whereNotNull('completed_at')
                            ->orderByDesc('completed_at')
                            ->limit(10)
                            ->get();
                    }

                    $startUrl = (isset($course) && $cm) ? route('user.quiz.start', [$course, $cm]) : '#';
                @endphp

                <div class="box_luar_deskripsi_modul">
                    <div class="box_deskripsi_modul">
                        <h4 style="font-weight:700; margin:0 0 12px 0;">Aturan Kuis</h4>
                        <p style="margin:0 0 10px 0;">Kuis ini bertujuan untuk mengukur pemahaman Anda terhadap materi {{ $beforeQuizTitle }}.</p>
                        <p style="margin:0 0 10px 0;">Silakan perhatikan ketentuan berikut sebelum memulai:</p>
                        <ol style="padding-left:18px; margin:0 0 14px 0; line-height:1.7;">
                            <li><strong>Jumlah Soal:</strong> {{ $questionCount }} pertanyaan pilihan ganda.</li>
                            <li><strong>Durasi Pengerjaan:</strong> {{ $durationText }}.</li>
                            <li><strong>Nilai Kelulusan:</strong> Minimum {{ $passingPercent }}% untuk dinyatakan lulus.</li>
                            <li>Jika belum mencapai nilai kelulusan, Anda dapat mengulang kuis setelah 2 menit. Gunakan waktu tersebut untuk mempelajari kembali materi sebelumnya.</li>
                            <li>Pastikan Anda menjawab semua pertanyaan sebelum waktu habis.</li>
                        </ol>
                        <p style="margin:0;">Selamat mengerjakan dan semoga sukses!</p>

                        <div style="display:flex; justify-content:flex-end; margin-top:14px;">
                            @if($currentQuizPassed)
                                <button type="button" class="btn" disabled
                                    style="background:#eafff3; color:#16a34a; border-radius:999px; padding:10px 18px; font-weight:800; cursor:not-allowed;">
                                    Anda telah lulus kuis ini
                                </button>
                            @else
                                <a href="{{ $startUrl }}" class="btn" style="background:#f4c430; color:#1f2937; border-radius:999px; padding:10px 18px; font-weight:700;">
                                    Start
                                    <span style="margin-left:8px;">›</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="box_luar_deskripsi_modul" style="margin-top:18px;">
                    <div class="box_deskripsi_modul">
                        <h4 style="font-weight:700; margin:0 0 14px 0;">Riwayat</h4>
                        <div style="overflow:auto;">
                            <table class="table" style="margin:0;">
                                <thead>
                                    <tr style="border-bottom:1px solid rgba(0,0,0,.08);">
                                        <th style="font-size:13px; color:#374151;">Tanggal</th>
                                        <th style="font-size:13px; color:#374151;">Persentase</th>
                                        <th style="font-size:13px; color:#374151;">Status</th>
                                        <th style="font-size:13px; color:#374151;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($attempts as $att)
                                        @php
                                            $passed = $att->isPassed($passingPercent);
                                            $statusStyle = $passed
                                                ? 'background:#eafff3; color:#16a34a;'
                                                : 'background:#ffecec; color:#ef4444;';
                                            $detailsUrl = (isset($course) && $cm) ? route('user.quiz.result', [$course, $cm, $att]) : '#';
                                        @endphp
                                        <tr style="border-bottom:1px solid rgba(0,0,0,.06);">
                                            <td style="font-size:13px; color:#111827;">
                                                {{ optional($att->completed_at)->format('d M Y H:i') }}
                                            </td>
                                            <td style="font-size:13px; color:#111827;">{{ $att->percentage }}%</td>
                                            <td>
                                                <span style="display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; font-weight:700; {{ $statusStyle }}">
                                                    {{ $passed ? 'Passed' : 'Not Pass' }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ $detailsUrl }}" class="btn" style="background:#eef2f7; color:#374151; border-radius:8px; padding:6px 12px; font-weight:600; font-size:12px;">
                                                    see details
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-muted">Belum ada riwayat kuis.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @php
                $nextModule = null;
                if ($cm && $modulesList && $modulesList->count() > 0) {
                    $idx = $modulesList->search(function ($x) use ($cm) { return (int)$x->id === (int)$cm->id; });
                    if ($idx !== false) {
                        $nextModule = $modulesList->get($idx + 1);
                    }
                }

                $lockNext = ($cm && ($moduleType === 'quiz') && auth()->check() && !$currentQuizPassed);

                $lockNextByFree = false;
                if (isset($course) && (int)($course->price ?? 0) <= 0 && ((string)($freeAccessMode ?? 'all') === 'limit_2') && $nextModule) {
                    $lockNextByFree = !in_array((int) $nextModule->id, (array) $freeAccessibleModuleIds, true);
                }
                $lockNext = $lockNext || $lockNextByFree;
            @endphp

            {{-- Tombol Next tetap seperti sebelumnya (button), tapi arahnya dinamis --}}
            <button class="next_kanan_modul" type="button"
                @if(isset($course) && $nextModule && !$lockNext)
                    data-next-url="{{ route('course.learn', ['course' => $course->id, 'module' => $nextModule->id]) }}"
                @else
                    disabled style="opacity:.6; cursor:not-allowed;"
                @endif
            >
                <p>
                    @if($lockNext)
                        Terkunci
                    @else
                        Next
                    @endif
                </p>
                @if($lockNext)
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="white" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M8 1a3 3 0 0 0-3 3v3H4a2 2 0 0 0-2 2v2a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-2a2 2 0 0 0-2-2h-1V4a3 3 0 0 0-3-3m2 6V4a2 2 0 1 0-4 0v3z"/>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi-right bi-caret-right-fill" viewBox="0 0 16 16">
                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z" />
                    </svg>
                @endif
            </button>
        </div>

    </div>

    <script>
        // Original accordion interaction + backend integration (navigate to selected module)
        const accItems = document.querySelectorAll('.accordion-item');
        const accordionBox = document.querySelector('.accordion-box');
        const learnBase = accordionBox?.getAttribute('data-learn-base') || '';
        const activeModuleId = accordionBox?.getAttribute('data-active-module-id') || '';

        accItems.forEach(item => {
            const header = item.querySelector('.accordion-header');
            if (!header) return;
            header.addEventListener('click', () => {
                const moduleId = header.getAttribute('data-module-id');

                // 1) First click: just expand/collapse preview (no navigation)
                const isOpen = item.classList.contains('active');
                if (!isOpen) {
                    // Close others for a true accordion feel
                    accItems.forEach(other => {
                        if (other !== item) other.classList.remove('active');
                    });
                    item.classList.add('active');
                    return;
                }

                // 2) If it's the currently selected module, allow closing without navigation
                const isSelected = item.classList.contains('selected') || (moduleId && activeModuleId && String(moduleId) === String(activeModuleId));
                if (isSelected) {
                    item.classList.remove('active');
                    return;
                }

                // 3) Second click on a non-selected open item: navigate to load it
                const locked = item.getAttribute('data-locked') === '1';
                if (locked) {
                    const reason = item.getAttribute('data-locked-reason') || '';
                    if (reason === 'free') {
                        alert('Materi ini terkunci. Course gratis ini hanya membuka 2 modul pertama.');
                    } else {
                        alert('Materi ini terkunci. Kamu harus lulus kuis terlebih dahulu.');
                    }
                    return;
                }

                if (learnBase && moduleId) {
                    window.location.href = learnBase + '?module=' + encodeURIComponent(moduleId);
                }
            });
        });

        const nextBtn = document.querySelector('.next_kanan_modul[data-next-url]');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const url = nextBtn.getAttribute('data-next-url');
                if (url) window.location.href = url;
            });
        }

        const videoEl = document.querySelector('.video_course');
        if (videoEl && videoEl.tagName === 'VIDEO') {
            videoEl.load();
        }
    </script>

    <script>
        // Realtime learning-time heartbeat
        (function () {
            const heartbeatUrl = "{{ route('learning-time.heartbeat') }}";
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const courseId = {{ isset($course) ? (int) $course->id : 'null' }};

            if (!heartbeatUrl || !csrfToken || !courseId) return;

            let lastInteractionAt = Date.now();
            const bump = () => { lastInteractionAt = Date.now(); };

            ['mousemove', 'keydown', 'scroll', 'touchstart', 'click'].forEach((evt) => {
                window.addEventListener(evt, bump, { passive: true });
            });

            const sendHeartbeat = async (seconds) => {
                try {
                    await fetch(heartbeatUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({ seconds, course_id: courseId }),
                    });
                } catch (e) {
                    // ignore
                }
            };

            const HEARTBEAT_SECONDS = 15;
            const ACTIVE_WINDOW_MS = 60_000;

            window.setInterval(() => {
                const now = Date.now();
                const isRecentlyActive = (now - lastInteractionAt) <= ACTIVE_WINDOW_MS;
                if (document.hidden || !isRecentlyActive) return;
                sendHeartbeat(HEARTBEAT_SECONDS);
            }, HEARTBEAT_SECONDS * 1000);
        })();
    </script>
</body>

</html>