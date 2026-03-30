<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Hasil Kuis - {{ $module->title }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include("partials.navbar-after-login")

    @php
        $passingPercent = 75;
        $totalSoal = (int) ($attempt->total_questions ?? 0);
        if ($totalSoal <= 0) {
            $totalSoal = isset($questions) ? (int) $questions->count() : 0;
        }

        $completedAt = $attempt->completed_at ? \Carbon\Carbon::parse($attempt->completed_at) : null;
        $tanggalText = $completedAt
            ? $completedAt->format('d M Y') . ' pukul ' . $completedAt->format('H:i:s')
            : '-';

        $passed = $attempt->isPassed($passingPercent);
        $scoreValue = (int) round((float) ($attempt->percentage ?? 0));
        $scoreValue = max(0, min(100, $scoreValue));
        $answersArr = collect($attempt->answers ?? []);
        $backToCourseUrl = route('course.learn', $course->id) . '?module=' . $module->id;
        $letters = range('a', 'z');
    @endphp

    <main class="quiz-result-page">
        <div class="box_luar_hasil">
            <div class="box_kiri_hasil">
            <h5>Tanggal Ujian : {{ $tanggalText }}</h5>
            <div class="informasi_hasil">
                <div class="score_hasil">
                    <p>Total Soal</p>
                    <h3 class="deactive">{{ $totalSoal }}</h3>
                </div>
                <div class="score_hasil">
                    <p>Score</p>
                    <h3>{{ $scoreValue }}</h3>
                </div>
            </div>

            @if ($passed)
                <p class="batas_minimum_nilai">Score anda sudah memenuhi batas minimum yang ditentukan pada ujian ini: {{ $passingPercent }}.</p>
            @else
                <p class="batas_minimum_nilai">Score anda belum memenuhi batas minimum yang ditentukan pada ujian ini: {{ $passingPercent }}.</p>
                <p class="batas_minimum_nilai">Mohon untuk mempelajari kembali modul-modul terkait: {{ $module->title ?? $course->name }}.</p>
            @endif
            </div>

            <div class="box_kanan_hasil">
            @foreach ($questions as $index => $question)
                @php
                    $userAnswer = $answersArr->firstWhere('question_id', $question->id);
                    $selectedAnswerId = (int) ($userAnswer['answer_id'] ?? 0);
                    $isCorrect = (bool) ($userAnswer['is_correct'] ?? false);
                    $indicatorClass = $isCorrect ? 'true' : 'false';
                    $indicatorValue = $isCorrect ? '1' : '0';
                @endphp

                <div class="qr-card">
                    <div class="qr-qhead">
                        <div class="qr-qtext">
                            <span class="qr-qnum">{{ $index + 1 }}.</span>
                            <span>{{ $question->question }}</span>
                        </div>
                        <div class="qr-indicator {{ $isCorrect ? 'ok' : 'bad' }}">{{ $indicatorValue }}</div>
                    </div>

                    <div class="qr-options">
                        @foreach (($question->answers ?? collect()) as $aIndex => $answer)
                            @php
                                $isAnswerCorrect = (bool) ($answer->is_correct ?? false);
                                $isSelected = $selectedAnswerId > 0 && ((int) $answer->id === $selectedAnswerId);
                                $letter = $letters[$aIndex] ?? chr(97 + $aIndex);

                                $optClass = 'qr-option';
                                if ($isAnswerCorrect) {
                                    $optClass .= ' is-correct';
                                }
                                if ($isSelected) {
                                    $optClass .= ' is-selected';
                                    if (!$isAnswerCorrect) {
                                        $optClass .= ' is-wrong';
                                    }
                                }
                            @endphp

                            <div class="{{ $optClass }}">
                                <span class="qr-radio" aria-hidden="true"></span>
                                <span class="qr-option-text">{{ $letter }}. {{ $answer->answer_text }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach

                <div class="quiz-result-actions">
                    <a class="kembali_course" href="{{ $backToCourseUrl }}" style="text-decoration:none; color:inherit;">
                        <span>Back To Course</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-caret-right" viewBox="0 0 16 16" aria-hidden="true">
                            <path d="M6 12.796V3.204L11.481 8zm.659.753 5.48-4.796a1 1 0 0 0 0-1.506L6.66 2.451C6.011 1.885 5 2.345 5 3.204v9.592a1 1 0 0 0 1.659.753" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </main>
</body>

</html>

