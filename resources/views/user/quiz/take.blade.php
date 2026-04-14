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
    <div class="box_luar_kuis quiz-take-v2" id="quizTakeRoot">
        <div class="box_kuis_kiri quiz-modules">
            @php
                $modulesList = $course->modules()->orderBy('order_no')->get();
            @endphp

            <div class="quiz-modules-head">
                <div class="quiz-modules-title">Daftar Modul</div>
                <button type="button" class="quiz-modules-close" id="closeModulesBtn" aria-label="Tutup daftar modul">&times;</button>
            </div>

            <div class="accordion-box">
                @foreach($modulesList as $m)
                    @php
                        $isActiveModule = (int) ($m->id ?? 0) === (int) ($module->id ?? 0);
                        $typeLabel = $m->type ? strtoupper((string) $m->type) : 'MATERI';
                        $cleanDesc = trim(strip_tags((string) ($m->description ?? '')));
                        $desc = $cleanDesc !== '' ? $cleanDesc : 'Klik untuk membuka materi.';
                        $desc = mb_strlen($desc) > 120 ? (mb_substr($desc, 0, 120) . '...') : $desc;
                        $learnUrl = route('course.learn', $course->id) . '?module=' . (int) ($m->id ?? 0);
                    @endphp
                    <div class="accordion-item {{ $isActiveModule ? 'selected active' : '' }}">
                        <a href="{{ $learnUrl }}" style="text-decoration:none; color: inherit;">
                            <button class="accordion-header" type="button">
                                <span style="display:flex; align-items:center; gap:10px; min-width:0;">
                                    <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $m->title ?? 'Materi' }}</span>
                                </span>
                                <span style="display:flex; align-items:center; gap:10px; flex:0 0 auto;">
                                    <span style="font-size:12px; font-weight:700; opacity:.75;">{{ $typeLabel }}</span>
                                    <span class="arrow">▲</span>
                                </span>
                            </button>
                        </a>
                        <div class="accordion-content">
                            <p style="margin:0;">{{ $desc }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="box_kuis_kanan">
            <div class="quiz-title-row" style="margin-top: 6px;">
                <div class="quiz-title-left">
                    <button type="button" class="quiz-modules-open d-none" id="openModulesBtn" aria-label="Buka daftar modul">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                            <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                        </svg>
                    </button>
                    <h1 class="quiz-title">Kuis {{ $quizNumber }} : {{ $module->title }}</h1>
                </div>
            </div>

            <div class="box_soal_kuis">
                <div id="quizHeaderBlock" style="margin-bottom:12px;">
                    <p class="waktu_kuis" id="quizTimer">--:--:--</p>
                    <div class="quiz-sidebar-heading" style="margin-top: 8px; margin-bottom:6px; font-weight:600;">Questions</div>
                    <div class="nomor_kuis" style="margin-bottom: 16px; display:flex; gap:10px; flex-wrap:wrap;">
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

                <form id="quizAnswerForm" action="{{ route('user.quiz.answer', [$course, $module, $attempt]) }}" method="POST">
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
                        <button type="button" class="previous_question" onclick="window.location.href='{{ $prevUrl }}'">Previous Question</button>
                        <button type="submit" class="next_question">{{ $isLastQuestion ? 'Send' : 'Send' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Click-to-select answers
        document.querySelectorAll('.quiz-answer-option').forEach(opt => {
            opt.addEventListener('click', () => {
                const radio = opt.querySelector('input[type="radio"]');
                if (radio) radio.checked = true;
                document.querySelectorAll('.quiz-answer-option').forEach(x => x.classList.remove('selected'));
                opt.classList.add('selected');
            });
        });

        // Sidebar accordion
        document.querySelectorAll('.box_kuis_kiri.quiz-modules .accordion-header').forEach(header => {
            header.addEventListener('click', () => {
                header.closest('.accordion-item')?.classList.toggle('active');
            });
        });

        // Sidebar open/close
        const rootEl = document.getElementById('quizTakeRoot');
        const modulesSidebar = document.querySelector('.box_kuis_kiri.quiz-modules');
        const openModulesBtn = document.getElementById('openModulesBtn');
        const closeModulesBtn = document.getElementById('closeModulesBtn');

        function setModulesOpen(isOpen) {
            if (!modulesSidebar) return;
            modulesSidebar.classList.toggle('closed', !isOpen);
            if (openModulesBtn) openModulesBtn.classList.toggle('d-none', isOpen);
            if (rootEl) rootEl.classList.toggle('modules-closed', !isOpen);
        }

        if (closeModulesBtn) {
            closeModulesBtn.addEventListener('click', () => setModulesOpen(false));
        }
        if (openModulesBtn) {
            openModulesBtn.addEventListener('click', () => setModulesOpen(true));
        }

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
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            try {
                await fetch(finishUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });
            } catch (e) {
                // ignore
            }
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
