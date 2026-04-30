<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quiz - {{ $module->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    @include("partials.navbar-after-login")
    @php
        $selectedAnswerId = collect($attempt->answers ?? [])->firstWhere('question_id', $currentQuestion->id)['answer_id'] ?? null;
        $total = $questions->count();
        $prevIndex = max(0, $currentQuestionIndex - 1);
        $prevUrl = route('user.quiz.take', [$course, $module, $attempt, 'q' => $prevIndex]);
        $resultUrl = route('user.quiz.result.short', $attempt);
        $finishUrl = route('user.quiz.finish', [$course, $module, $attempt]);

        $quizNumber = 1;
        if (($course->modules ?? null) && $module) {
            $quizModules = collect($course->modules)->filter(fn($m) => ($m->type ?? null) === 'quiz')->values();
            $foundIndex = $quizModules->search(fn($m) => (int) $m->id === (int) $module->id);
            if ($foundIndex !== false) {
                $quizNumber = (int) $foundIndex + 1;
            }
        }

        // Ambil subtitle modul dari course_units berdasarkan nomor modul
        $quizModuleSubtitle = null;
        if (preg_match('/^Module\s*(\d+)/i', (string) ($module->title ?? ''), $mm)) {
            $unitNum = (int) $mm[1];
            $unit = $course->units->firstWhere('unit_no', $unitNum);
            if ($unit && !empty($unit->title)) {
                $quizModuleSubtitle = $unit->title;
            }
        }
        if (!$quizModuleSubtitle) {
            // Fallback: parse dari title modul (e.g. "Module 1 - Testing" → "Testing")
            if (preg_match('/^Module\s*\d+\s*-\s*(.+)$/i', (string) ($module->title ?? ''), $mm)) {
                $sub = trim($mm[1]);
                if (!preg_match('/^(PDF\s*Material|Video\s*Lesson|Quiz)$/i', $sub)) {
                    $quizModuleSubtitle = $sub;
                }
            }
        }
        $quizPageTitle = 'Quiz ' . $quizNumber . ($quizModuleSubtitle ? ' - ' . $quizModuleSubtitle : '');

        $isLastQuestion = ($currentQuestionIndex + 1) >= $total;
    @endphp
    <div class="box_luar_kuis quiz-take-v2">
        <!-- Sidebar for Quiz Navigation -->
        <div class="box_kuis_kiri quiz-modules" id="quizSidebar">
            <div class="quiz-modules-head">
                <span class="quiz-modules-title">Questions Navigation</span>
                <button type="button" class="quiz-modules-close" id="closeSidebarBtn">&times;</button>
            </div>
            
            <div class="quiz-sidebar-content" style="padding: 10px 0;">
                <div class="timer-section-sidebar" style="margin-bottom: 24px; text-align: center;">
                    <p style="font-size: 13px; color: #6b7280; margin-bottom: 8px; font-weight: 600;">Time Remaining</p>
                    <p class="waktu_kuis" id="quizTimer" style="margin: 0 auto;">--:--</p>
                </div>

                <div class="quiz-sidebar-heading" style="margin-bottom:12px; font-weight:700; color: #111827; font-size: 15px;">Question List</div>
                <div class="nomor_kuis" style="display:grid; grid-template-columns: repeat(5, 1fr); gap:10px;">
                    @foreach($questions as $idx => $q)
                        @php
                            $isAnswered = in_array($q->id, $answeredQuestionIds ?? [], true);
                            $cls = $idx === $currentQuestionIndex ? 'kuis_aktif' : (!$isAnswered ? 'kuis_belum_diisi' : '');
                            $goUrl = route('user.quiz.take', [$course, $module, $attempt, 'q' => $idx]);
                        @endphp
                        <button type="button" class="{{ $cls }}" onclick="window.location.href='{{ $goUrl }}'">{{ $idx + 1 }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="box_kuis_kanan">
            <div class="quiz-title-row" style="margin-top: 24px; display: flex; align-items: center; gap: 15px; justify-content: space-between;">
                <div class="quiz-title-left" style="display: flex; align-items: center; gap: 12px;">
                    <button type="button" class="quiz-modules-open" id="openSidebarBtn" style="display: none;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#374151" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M2.5 12a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5z"/>
                        </svg>
                    </button>
                    <h1 class="quiz-title" style="margin: 0; font-size: 20px;">Kuis {{ $quizNumber }} : {{ $module->title }}</h1>
                </div>
            </div>

            <div class="box_soal_kuis" style="margin: 30px auto 0; max-width: 800px; float: none;">
                <h2 class="pertanyaan_kuis" style="margin-bottom: 24px;">{{ $currentQuestionIndex + 1 }}. {{ $currentQuestion->question }}</h2>

                <form id="quizAnswerForm" action="{{ route('user.quiz.answer', [$course, $module, $attempt]) }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">

                    <div class="options-container" style="display: flex; flex-direction: column; gap: 14px; margin-bottom: 30px;">
                        @foreach($currentQuestion->answers as $answer)
                            @php
                                $letter = chr(65 + $loop->index);
                                $checked = $selectedAnswerId && (int)$selectedAnswerId === (int)$answer->id;
                            @endphp

                            <label class="pilihan_jawaban_kuis quiz-answer-option {{ $checked ? 'selected' : '' }}" data-answer-id="{{ $answer->id }}" style="margin: 0; width: 100%;">
                                <input class="d-none" type="radio" name="answer_id" value="{{ $answer->id }}" {{ $checked ? 'checked' : '' }} required>
                                <span class="quiz-option-letter" aria-hidden="true">{{ $letter }}.</span>
                                <p class="mb-0">{{ $answer->answer_text }}</p>
                            </label>
                        @endforeach
                    </div>

                    <div class="tombol_kuis" style="display: flex; justify-content: space-between; gap: 15px; margin-top: 40px;">
                        <button type="button" class="previous_question" data-prev-url="{{ $prevUrl }}" style="flex: 1; max-width: 200px;">Previous</button>
                        <button type="submit" class="next_question" style="flex: 1; max-width: 200px;">{{ $isLastQuestion ? 'Submit' : 'Next' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Sidebar Toggling Logic
        const sidebar = document.getElementById('quizSidebar');
        const openBtn = document.getElementById('openSidebarBtn');
        const closeBtn = document.getElementById('closeSidebarBtn');
        const mainContainer = document.querySelector('.box_luar_kuis');

        function toggleSidebar(isClosed) {
            if (isClosed) {
                sidebar.classList.add('closed');
                mainContainer.classList.add('modules-closed');
                openBtn.style.display = 'flex';
            } else {
                sidebar.classList.remove('closed');
                mainContainer.classList.remove('modules-closed');
                openBtn.style.display = 'none';
            }
            // Save state
            localStorage.setItem('quiz_sidebar_closed', isClosed ? '1' : '0');
        }

        openBtn?.addEventListener('click', () => toggleSidebar(false));
        closeBtn?.addEventListener('click', () => toggleSidebar(true));

        // Restore sidebar state
        if (localStorage.getItem('quiz_sidebar_closed') === '1') {
            toggleSidebar(true);
        }
    </script>

    <script>
        // ── Answer persistence via sessionStorage ──────────────────────────────
        const QUIZ_STORAGE_KEY = 'quiz_draft_{{ $attempt->id }}_q{{ $currentQuestion->id }}';

        // Restore saved draft answer on page load (before server-selected takes effect)
        (function restoreDraft() {
            try {
                const saved = sessionStorage.getItem(QUIZ_STORAGE_KEY);
                if (!saved) return;
                const savedId = parseInt(saved, 10);
                if (!savedId) return;

                // Only restore if no server-side answer already selected
                const alreadyChecked = document.querySelector('input[name="answer_id"]:checked');
                if (alreadyChecked) return; // server already has an answer, don't override

                const target = document.querySelector('input[name="answer_id"][value="' + savedId + '"]');
                if (target) {
                    target.checked = true;
                    const label = target.closest('.quiz-answer-option');
                    if (label) {
                        document.querySelectorAll('.quiz-answer-option').forEach(x => x.classList.remove('selected'));
                        label.classList.add('selected');
                    }
                }
            } catch (_e) {}
        })();

        // Track whether user has selected an answer that hasn't been submitted yet
        // If server already has an answer for this question, no need to warn
        let _draftDirty = false;
        @if($selectedAnswerId)
        // This question already has a saved answer — no unsaved state
        _draftDirty = false;
        @endif

        // Click-to-select answers
        document.querySelectorAll('.quiz-answer-option').forEach(opt => {
            opt.addEventListener('click', () => {
                const radio = opt.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
                document.querySelectorAll('.quiz-answer-option').forEach(x => x.classList.remove('selected'));
                opt.classList.add('selected');
                
                // Clear any custom validity if previously set
                if (radio) radio.setCustomValidity('');

                // Save draft to sessionStorage
                try {
                    sessionStorage.setItem(QUIZ_STORAGE_KEY, radio ? radio.value : '');
                } catch (_e) {}

                _draftDirty = true;
            });
        });

        // Clear dirty flag and draft on successful submit + validate before submit
        const quizForm = document.getElementById('quizAnswerForm');
        if (quizForm) {
            quizForm.addEventListener('submit', function(e) {
                const checked = document.querySelector('input[name="answer_id"]:checked');
                if (!checked) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Peringatan!',
                            text: 'Anda harus menyelesaikan soal ini terlebih dahulu',
                            icon: 'warning',
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#f4c430'
                        });
                    } else {
                        alert('Anda harus menyelesaikan semua soal kuis ini terlebih dahulu');
                    }
                    return;
                }

                @if($isLastQuestion)
                    const unansweredCount = document.querySelectorAll('.nomor_kuis button.kuis_belum_diisi').length;
                    if (unansweredCount > 0) {
                        e.preventDefault();
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                title: 'Peringatan!',
                                text: 'Anda harus menyelesaikan semua soal kuis terlebih dahulu sebelum lanjut',
                                icon: 'warning',
                                confirmButtonText: 'Tutup',
                                confirmButtonColor: '#f4c430'
                            });
                        } else {
                            alert('Anda harus menyelesaikan kuis terlebih dahulu sebelum lanjut');
                        }
                        return;
                    }
                @endif

                // Clear dirty flag and draft on valid submit
                _draftDirty = false;
                try { sessionStorage.removeItem(QUIZ_STORAGE_KEY); } catch (_e) {}
            });
        }

        // Warn user before leaving if they have an unsaved answer selection
        window.addEventListener('beforeunload', function(e) {
            const checked = document.querySelector('input[name="answer_id"]:checked');
            if (_draftDirty && checked) {
                const msg = 'Jawaban yang sudah kamu pilih belum disimpan. Yakin ingin meninggalkan halaman ini?';
                e.preventDefault();
                e.returnValue = msg;
                return msg;
            }
        });

        // Also warn when clicking "Previous Question" if there's an unsaved selection
        const prevBtn = document.querySelector('.previous_question');
        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                const checked = document.querySelector('input[name="answer_id"]:checked');
                const prevUrl = prevBtn.getAttribute('data-prev-url') || '{{ $prevUrl }}';
                if (_draftDirty && checked) {
                    e.preventDefault();
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Answer Not Saved',
                            text: 'You have selected an answer but haven\'t clicked "Send". This answer will be lost if you navigate away. Continue?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, move on',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#f4c430',
                        }).then(result => {
                            if (result.isConfirmed) {
                                _draftDirty = false;
                                window.location.href = prevUrl;
                            }
                        });
                    } else {
                        if (confirm('Answer not saved. Continue?')) {
                            _draftDirty = false;
                            window.location.href = prevUrl;
                        }
                    }
                } else {
                    window.location.href = prevUrl;
                }
            });
        }

        // Warn when clicking question number buttons if there's an unsaved selection
        document.querySelectorAll('.nomor_kuis button[onclick]').forEach(btn => {
            const originalOnclick = btn.getAttribute('onclick');
            btn.removeAttribute('onclick');
            btn.addEventListener('click', function(e) {
                const checked = document.querySelector('input[name="answer_id"]:checked');
                if (_draftDirty && checked) {
                    e.preventDefault();
                    const targetUrl = originalOnclick ? originalOnclick.replace("window.location.href='", '').replace("'", '') : null;
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Answer Not Saved',
                            text: 'You have selected an answer but haven\'t clicked "Send". This answer will be lost if you navigate away. Continue?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, move on',
                            cancelButtonText: 'Cancel',
                            confirmButtonColor: '#f4c430',
                        }).then(result => {
                            if (result.isConfirmed) {
                                _draftDirty = false;
                                if (targetUrl) window.location.href = targetUrl;
                            }
                        });
                    } else {
                        if (confirm('Answer not saved. Continue?')) {
                            _draftDirty = false;
                            if (targetUrl) window.location.href = targetUrl;
                        }
                    }
                } else {
                    if (originalOnclick) eval(originalOnclick);
                }
            });
        });

        // Timer (module duration in seconds), based on server-provided endsAt
        const endsAtIso = @json($endsAtIso ?? null);
        const timerEl = document.getElementById('quizTimer');
        const finishUrl = @json($finishUrl);
        const resultUrl = @json($resultUrl);

        function formatTime(totalSeconds) {
            const s = Math.max(0, Math.floor(totalSeconds));
            const m = Math.floor(s / 60);
            const r = s % 60;
            return String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
        }

        async function finishAttempt() {
            // Clear dirty flag so beforeunload doesn't block the redirect
            _draftDirty = false;

            // Show time-up overlay before redirecting
            const overlay = document.createElement('div');
            overlay.style.cssText = 'position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;';
            overlay.innerHTML = `
                <div style="background:#fff;border-radius:20px;padding:36px 32px;max-width:340px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,.25);">
                    <div style="font-size:48px;margin-bottom:12px;">⏰</div>
                    <h3 style="font-weight:800;font-size:20px;color:#1f2937;margin:0 0 10px 0;">Waktu Habis!</h3>
                    <p style="color:#6b7280;font-size:14px;margin:0 0 6px 0;">Kuis kamu otomatis dikumpulkan.</p>
                    <p style="color:#9ca3af;font-size:13px;margin:0;">Mengarahkan ke halaman hasil...</p>
                </div>`;
            document.body.appendChild(overlay);

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                // Gunakan keepalive agar fetch tidak di-cancel saat browser navigasi
                await fetch(finishUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    keepalive: true,
                });
            } catch (e) {
                // ignore network errors
            }
            // Tunggu sebentar agar database commit selesai sebelum redirect
            await new Promise(resolve => setTimeout(resolve, 600));
            window.location.href = resultUrl;
        }

        if (timerEl && endsAtIso) {
            // Kurangi 7 menit (420 detik) + 7 detik dari waktu asli
            const endsAt = new Date(endsAtIso).getTime() - (7 * 60 * 1000) - (7 * 1000);
            const tick = () => {
                const now = Date.now();
                const remaining = Math.floor((endsAt - now) / 1000);
                timerEl.textContent = formatTime(remaining);
                if (remaining <= 0) {
                    clearInterval(intv);
                    finishAttempt();
                }
            };
            tick();
            const intv = setInterval(tick, 1000);
        }
    </script>

</body>

</html>