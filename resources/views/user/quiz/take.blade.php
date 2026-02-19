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
        $resultUrl = route('user.quiz.result', [$course, $module, $attempt]);
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
        <aside class="box_kuis_kiri quiz-modules" id="quizModulesSidebar">
            <div class="quiz-modules-head">
                <div class="quiz-modules-title">Modules</div>
                <button type="button" class="quiz-modules-close" id="quizModulesClose" aria-label="Close modules">×</button>
            </div>

            <div class="accordion-box">
                @foreach(($course->modules ?? collect()) as $m)
                    @php
                        $isCurrent = (int) $m->id === (int) $module->id;
                        $typeLabel = strtoupper($m->type ?? 'materi');
                        $learnUrl = route('course.learn', $course->id) . '?module=' . $m->id;
                    @endphp

                    <div class="accordion-item {{ $isCurrent ? 'active selected' : '' }}">
                        <button class="accordion-header" type="button" onclick="this.parentElement.classList.toggle('active')">
                            {{ $m->title ?? 'Modul' }}
                            <span class="arrow">▲</span>
                        </button>
                        <div class="accordion-content">
                            <p class="mb-2">Tipe: {{ $typeLabel }}</p>
                            @if(!$isCurrent)
                                <a class="btn" href="{{ $learnUrl }}" style="background:#252346; color:#fff; padding:8px 12px; border-radius:10px; font-weight:600; font-size:12px;">
                                    Buka
                                </a>
                            @else
                                <span class="text-muted" style="font-size:12px;">Sedang dikerjakan</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </aside>

        <div class="box_kuis_kanan">
            <div class="quiz-title-row">
                <div class="quiz-title-left">
                    <button type="button" class="quiz-modules-open" id="quizModulesOpen" aria-label="Open modules">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                            <path fill-rule="evenodd" d="M2.5 12.5A.5.5 0 0 1 3 12h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4A.5.5 0 0 1 3 8h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0-4A.5.5 0 0 1 3 4h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                        </svg>
                    </button>
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M1.5 1a.5.5 0 0 0-.5.5V14a1 1 0 0 0 1 1H14.5a.5.5 0 0 0 0-1H2a.5.5 0 0 1-.5-.5V1.5a.5.5 0 0 0-.5-.5"/>
                        <path d="M4 6.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 6.5m0 2a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7A.5.5 0 0 1 4 8.5m0 2a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4A.5.5 0 0 1 4 10.5"/>
                    </svg>
                    <h1 class="quiz-title">Kuis {{ $quizNumber }}: {{ $module->title }}</h1>
                </div>
            </div>

            <div class="box_soal_kuis">
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
                        <button type="submit" class="next_question">{{ $isLastQuestion ? 'Finish' : 'Next Question' }}</button>
                    </div>
                </form>
            </div>
        </div>

        <aside class="quiz-sidebar">
            <p class="waktu_kuis" id="quizTimer">--:--:--</p>
            <div class="quiz-sidebar-heading">Questions</div>
            <div class="nomor_kuis">
                @foreach($questions as $idx => $q)
                    @php
                        $isAnswered = in_array($q->id, $answeredQuestionIds ?? [], true);
                        $cls = $idx === $currentQuestionIndex ? 'kuis_aktif' : (!$isAnswered ? 'kuis_belum_diisi' : '');
                        $goUrl = route('user.quiz.take', [$course, $module, $attempt, 'q' => $idx]);
                    @endphp
                    <button type="button" class="{{ $cls }}" onclick="window.location.href='{{ $goUrl }}'">{{ $idx + 1 }}</button>
                @endforeach
            </div>
        </aside>
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

        // Modules sidebar open/close
        const modulesSidebar = document.getElementById('quizModulesSidebar');
        const modulesOpen = document.getElementById('quizModulesOpen');
        const modulesClose = document.getElementById('quizModulesClose');
        if (modulesSidebar && modulesOpen && modulesClose) {
            const closeSidebar = () => modulesSidebar.classList.add('closed');
            const openSidebar = () => modulesSidebar.classList.remove('closed');

            modulesOpen.addEventListener('click', openSidebar);
            modulesClose.addEventListener('click', closeSidebar);

            // Default closed on smaller screens
            if (window.matchMedia && window.matchMedia('(max-width: 992px)').matches) {
                closeSidebar();
            }
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
