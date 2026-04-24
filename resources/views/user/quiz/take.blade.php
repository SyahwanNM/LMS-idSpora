@include("partials.navbar-after-login")

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

        $isLastQuestion = ($currentQuestionIndex + 1) >= $total;
    @endphp
    <div class="box_luar_kuis quiz-take-v2">
        <div class="box_kuis_kanan">
            <div class="quiz-title-row" style="margin-top: 24px;">
                <div class="quiz-title-left">
                    <span class="quiz-title-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2 2.5a.5.5 0 0 1 .5-.5H14a1 1 0 0 1 1 1v9a1 1 0 0 1-1 1H2.5a.5.5 0 0 1-.5-.5z" opacity=".15"/>
                            <path d="M2.5 2A1.5 1.5 0 0 0 1 3.5v9A1.5 1.5 0 0 0 2.5 14H14a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zM2 3.5A.5.5 0 0 1 2.5 3H14v10H2.5a.5.5 0 0 1-.5-.5z"/>
                            <path d="M4 5.25a.75.75 0 0 1 .75-.75h6.5a.75.75 0 0 1 0 1.5h-6.5A.75.75 0 0 1 4 5.25m0 2.5a.75.75 0 0 1 .75-.75h6.5a.75.75 0 0 1 0 1.5h-6.5A.75.75 0 0 1 4 7.75m0 2.5a.75.75 0 0 1 .75-.75h4a.75.75 0 0 1 0 1.5h-4A.75.75 0 0 1 4 10.25"/>
                        </svg>
                    </span>
                    <h1 class="quiz-title">Kuis {{ $quizNumber }} : {{ $module->title }}</h1>
                </div>
            </div>

            <div class="box_soal_kuis">
                <div id="quizHeaderBlock" style="margin-bottom:12px; margin-top: 30px;">
                    <p class="waktu_kuis" id="quizTimer">--:--:--</p>
                    <div class="quiz-sidebar-heading" style="margin-top: 8px; margin-bottom:6px; font-weight:600;">Questions</div>
                    <div class="nomor_kuis" style="margin-bottom: 16px;margin-top: 10px; display:flex; gap:10px; flex-wrap:wrap;">
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
                <h2 class="pertanyaan_kuis">{{ $currentQuestionIndex + 1 }}. {{ $currentQuestion->question }}</h2>

                <form id="quizAnswerForm" action="{{ route('user.quiz.answer', [$course, $module, $attempt]) }}" method="POST" novalidate>
                    @csrf
                    <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">

                    @foreach($currentQuestion->answers as $answer)
                        @php
                            $letter = chr(65 + $loop->index);
                            $checked = $selectedAnswerId && (int)$selectedAnswerId === (int)$answer->id;
                        @endphp

                        <label class="pilihan_jawaban_kuis quiz-answer-option {{ $checked ? 'selected' : '' }}" data-answer-id="{{ $answer->id }}">
                            <input class="d-none" type="radio" name="answer_id" value="{{ $answer->id }}" {{ $checked ? 'checked' : '' }} required>
                            <span class="quiz-option-letter" aria-hidden="true">{{ $letter }}.</span>
                            <p class="mb-0">{{ $answer->answer_text }}</p>
                        </label>
                    @endforeach

                    <div class="tombol_kuis">
                        <button type="button" class="previous_question" data-prev-url="{{ $prevUrl }}">Previous Question</button>
                        <button type="submit" class="next_question">{{ $isLastQuestion ? 'Send' : 'Send' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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
                            title: 'Jawaban Belum Disimpan',
                            text: 'Kamu sudah memilih jawaban tapi belum menekan "Send". Jawaban ini akan hilang jika kamu pindah soal. Lanjutkan?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, pindah soal',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#f4c430',
                        }).then(result => {
                            if (result.isConfirmed) {
                                _draftDirty = false;
                                window.location.href = prevUrl;
                            }
                        });
                    } else {
                        if (confirm('Jawaban belum disimpan. Lanjutkan?')) {
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
                            title: 'Jawaban Belum Disimpan',
                            text: 'Kamu sudah memilih jawaban tapi belum menekan "Send". Jawaban ini akan hilang jika kamu pindah soal. Lanjutkan?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, pindah soal',
                            cancelButtonText: 'Batal',
                            confirmButtonColor: '#f4c430',
                        }).then(result => {
                            if (result.isConfirmed) {
                                _draftDirty = false;
                                if (targetUrl) window.location.href = targetUrl;
                            }
                        });
                    } else {
                        if (confirm('Jawaban belum disimpan. Lanjutkan?')) {
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
            const h = Math.floor(s / 3600);
            const m = Math.floor((s % 3600) / 60);
            const r = s % 60;
            return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ':' + String(r).padStart(2, '0');
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
            const endsAt = new Date(endsAtIso).getTime();
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
