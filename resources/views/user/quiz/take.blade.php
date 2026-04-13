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
    <div class="box_luar_kuis quiz-take-v2">
        <div class="box_kuis_kanan">
            <p class="waktu_kuis" id="quizTimer">--:--:--</p>
            <div class="quiz-sidebar-heading" style="margin-top: 12px;">Questions</div>
            <div class="nomor_kuis" style="margin-bottom: 16px;">
                @foreach($questions as $idx => $q)
                    @php
                        $isAnswered = in_array($q->id, $answeredQuestionIds ?? [], true);
                        $cls = $idx === $currentQuestionIndex ? 'kuis_aktif' : (!$isAnswered ? 'kuis_belum_diisi' : '');
                        $goUrl = route('user.quiz.take', [$course, $module, $attempt, 'q' => $idx]);
                    @endphp
                    <button type="button" class="{{ $cls }}" onclick="window.location.href='{{ $goUrl }}'">{{ $idx + 1 }}</button>
                @endforeach
            </div>

            <div class="quiz-title-row" style="margin-top: 6px;">
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
