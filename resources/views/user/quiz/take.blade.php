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
    <style>
        html, body { overflow: hidden; height: 100%; margin: 0; padding: 0; width: 100vw; }
        .quiz-take-v3 {
            display: flex;
            width: 100%;
            height: calc(100vh - 64px);
            overflow: hidden;
            background: #fdfdfd;
            padding-top: 50px;
        }
        .box_modul_kiri {
            width: 320px;
            background: #fff;
            border-right: 1px solid #e5e7eb;
            overflow-y: auto;
            overflow-x: hidden;
            flex-shrink: 0;
            margin-top: 15px;
        }
        .box_modul_kanan_quiz {
            flex: 1;
            display: flex;
            min-width: 0;
            overflow: hidden;
            margin-top: 15px;
        }
        .quiz-main-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 0 40px 60px 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 0;
        }
        .quiz-nav-right {
            width: 320px;
            background: #fff;
            border-left: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 24px 15px;
            flex-shrink: 0;
            margin-top: 15px;
        }

        /* Quiz Navigation: Flex wrap is safer than grid for preventing cut-off */
        .nomor_kuis {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            width: 100%;
            justify-content: flex-start;
        }
        .nomor_kuis button {
            width: 42px; /* Standard size */
            height: 42px;
            border-radius: 10px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            border: 1px solid #e5e7eb;
            font-weight: 600;
            flex-shrink: 0;
            padding: 0;
        }
        .kuis_aktif { background: #f4c430 !important; color: #111827 !important; font-weight: 700; border: none !important; }
        .kuis_belum_diisi { background: #f3f4f6; color: #6b7280; border: 1px solid #e5e7eb; }
        .nomor_kuis button:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        
        /* Responsive Breakpoints */
        @media (max-width: 1200px) {
            .box_modul_kiri { width: 250px; }
            .quiz-nav-right { width: 250px; }
        }

        @media (max-width: 992px) {
            .quiz-take-v3 {
                flex-direction: column;
                height: auto;
                overflow: visible;
                padding-top: 20px;
            }
            .box_modul_kiri {
                width: 100%;
                height: 300px;
                border-right: none;
                border-bottom: 1px solid #e5e7eb;
            }
            .box_modul_kanan_quiz {
                flex-direction: column;
                width: 100%;
                height: auto;
                overflow: visible;
            }
            .quiz-main-content {
                width: 100%;
                padding: 30px 20px;
                overflow: visible;
            }
            .quiz-nav-right {
                width: 100%;
                height: auto;
                border-left: none;
                border-top: 1px solid #e5e7eb;
                padding-bottom: 50px;
            }
            .nomor_kuis { justify-content: center; }
        }
        
        /* Sidebar Utama Styles */
        .accordion-header { width: 100%; padding: 16px; display: flex; justify-content: space-between; align-items: center; background: #fff; border: none; border-bottom: 1px solid #f3f4f6; text-align: left; transition: background 0.2s; }
        .accordion-header:hover:not(.is-locked) { background: #f9fafb; }
        .accordion-item.active .accordion-header { background: #fefce8; border-left: 4px solid #f4c430; }
        .accordion-item.is-locked .accordion-header { 
            opacity: 0.5; 
            cursor: not-allowed !important; 
            pointer-events: none !important; /* Disable all clicks */
            background: #f9fafb;
        }
        .accordion-item.is-locked .accordion-header * {
            pointer-events: none !important;
        }
        
        .quiz-answer-option.selected {
            border-color: #f4c430 !important;
            background: #fefce8 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .quiz-answer-option.selected .quiz-option-letter {
            background: #f4c430 !important;
            color: #111827 !important;
        }
        .quiz-answer-option.selected p {
            color: #111827 !important;
            font-weight: 700 !important;
        }
        
        /* Choice styles to fix overflow */
        .quiz-answer-option {
            width: 100%;
            box-sizing: border-box;
            display: flex !important;
            align-items: flex-start !important;
            gap: 15px;
            padding: 18px 20px;
            border: 1.5px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.2s;
            background: #fff;
            white-space: normal !important; /* Force wrap */
        }
        .quiz-answer-option p {
            flex: 1;
            min-width: 0;
            margin: 0 !important;
            font-size: 15px;
            color: #374151;
            font-weight: 500;
            line-height: 1.5;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: normal !important;
        }
        .quiz-option-letter {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f3f4f6;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            color: #6b7280;
            flex-shrink: 0;
            margin-top: -2px;
        }
    </style>
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
        
        // Logic for Sidebar Utama (Course Navigation)
        $modulesList = $course->modules ?? collect();
        
        // Remove PDFs from the list first to match the sidebar display
        $filteredModules = $modulesList->filter(fn($m) => strtolower(trim((string)($m->type ?? ''))) !== 'pdf')->values();
        
        $activeModule = $module;
        $activeModuleIndex = $filteredModules->search(fn($m) => (int)$m->id === (int)$activeModule->id);
        if ($activeModuleIndex === false) {
            // Fallback to order_no if search fails
            $activeModuleIndex = 0;
        }
        
        $passingPercent = 75;

        // Fetch passed quizzes for checkmarks
        $passedQuizModuleIds = [];
        if (auth()->check() && $modulesList->isNotEmpty()) {
            $quizModuleIds = $modulesList
                ->filter(fn($m) => strtolower(trim((string) ($m->type ?? ''))) === 'quiz')
                ->pluck('id')
                ->map(fn($id) => (int) $id)
                ->all();

            if (!empty($quizModuleIds)) {
                $passedQuizModuleIds = \App\Models\QuizAttempt::where('user_id', auth()->id())
                    ->whereIn('course_module_id', $quizModuleIds)
                    ->whereNotNull('completed_at')
                    ->get()
                    ->filter(fn($a) => $a->isPassed($passingPercent))
                    ->pluck('course_module_id')
                    ->map(fn($id) => (int) $id)
                    ->unique()
                    ->all();
            }
        }

        // Fetch completed materials for checkmarks
        $completedMaterialModuleIds = [];
        if (auth()->check()) {
            $enrollment = \App\Models\Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->first();
            if ($enrollment) {
                $completedMaterialModuleIds = \App\Models\Progress::where('enrollment_id', $enrollment->id)
                    ->where('completed', true)
                    ->pluck('course_module_id')
                    ->map(fn($id) => (int) $id)
                    ->all();
            }
        }

        $isLastQuestion = ($currentQuestionIndex + 1) >= $total;
    @endphp

    <div class="box_modul_luar quiz-take-v3">
        <!-- LEFT: Sidebar Utama (Course Navigation) -->
        <div class="box_modul_kiri">
            <div style="padding: 24px; border-bottom: 1px solid #f3f4f6;">
                <h5 style="font-weight: 700; margin: 0; font-size: 16px; color: #111827;">Course Content</h5>
                <p style="margin: 4px 0 0 0; font-size: 12px; color: #6b7280; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $course->name }}</p>
            </div>
            <div class="accordion-box">
                @foreach($filteredModules as $mIdx => $m)
                    @php
                        $isActive = (int)$activeModule->id === (int)$m->id;
                        $mType = strtolower(trim((string)($m->type ?? '')));

                        // SEQUENTIAL GATING LOGIC:
                        // A module is locked if ANY preceding QUIZ has not been passed.
                        // However, we ALWAYS allow the current active module so the user can finish it.
                        $isLocked = false;
                        if (!$isActive) {
                            $precedingModules = $filteredModules->take($mIdx);
                            foreach ($precedingModules as $pm) {
                                if (strtolower(trim((string)($pm->type ?? ''))) === 'quiz') {
                                    if (!in_array((int)$pm->id, $passedQuizModuleIds, true)) {
                                        $isLocked = true;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        $isDone = $mType === 'quiz' 
                            ? in_array((int)$m->id, $passedQuizModuleIds) 
                            : in_array((int)$m->id, $completedMaterialModuleIds);
                    @endphp
                    <div class="accordion-item {{ $isActive ? 'active' : '' }} {{ $isLocked ? 'is-locked' : '' }}">
                        <button class="accordion-header" type="button" 
                                @if(!$isLocked) onclick="window.location.href='{{ route('course.learn', [$course->id, 'module' => $m->id]) }}'" @endif
                                style="{{ $isLocked ? 'cursor: not-allowed;' : '' }}">
                            <span style="display:flex; align-items:center; gap:10px; min-width:0; flex: 1;">
                                <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis; font-size: 13px; font-weight: 500;">{{ $m->title }}</span>
                            </span>
                            <span style="display: flex; align-items: center; gap: 8px;">
                                @if($isDone)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#16a34a" viewBox="0 0 16 16">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                @elseif($isLocked)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#9ca3af" viewBox="0 0 16 16">
                                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                    </svg>
                                @endif
                            </span>
                        </button>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- RIGHT AREA: Split into Main Content and Quiz Navigation -->
        <div class="box_modul_kanan_quiz">
            <!-- MIDDLE: Question Content -->
            <div class="quiz-main-content">
                <div style="width: 100%; max-width: 800px; box-sizing: border-box;">
                    <div style="margin-bottom: 32px;">
                        <span style="display: inline-block; padding: 6px 12px; background: #fef3c7; color: #d97706; border-radius: 6px; font-weight: 700; font-size: 11px; text-transform: uppercase; margin-bottom: 12px;">Quiz Module</span>
                        <h1 style="font-size: 22px; font-weight: 800; color: #111827; margin: 0; word-break: break-word;">{{ $module->title }}</h1>
                    </div>

                    <div class="box_soal_kuis" style="background: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05); border: 1px solid #f3f4f6; width: 100%; box-sizing: border-box;">
                        <h2 class="pertanyaan_kuis" style="font-size: 18px; font-weight: 600; line-height: 1.6; margin-bottom: 32px; color: #374151; word-break: break-word; white-space: normal !important;">
                            {{ $currentQuestionIndex + 1 }}. {{ $currentQuestion->question }}
                        </h2>

                        <form id="quizAnswerForm" action="{{ route('user.quiz.answer', [$course, $module, $attempt]) }}" method="POST" novalidate style="width: 100%;">
                            @csrf
                            <input type="hidden" name="question_id" value="{{ $currentQuestion->id }}">

                            <div class="options-container" style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 40px; width: 100%;">
                                @foreach($currentQuestion->answers as $answer)
                                    @php
                                        $letter = chr(65 + $loop->index);
                                        $checked = $selectedAnswerId && (int)$selectedAnswerId === (int)$answer->id;
                                    @endphp

                                    <label class="quiz-answer-option {{ $checked ? 'selected' : '' }}" data-answer-id="{{ $answer->id }}" style="position: relative;">
                                        <input type="radio" name="answer_id" value="{{ $answer->id }}" {{ $checked ? 'checked' : '' }} required style="position: absolute; opacity: 0; pointer-events: none;">
                                        <span class="quiz-option-letter">{{ $letter }}</span>
                                        <p>{{ $answer->answer_text }}</p>
                                    </label>
                                @endforeach
                            </div>

                            <div style="display: flex; justify-content: space-between; gap: 20px;">
                                <button type="button" class="previous_question btn btn-light py-3 px-4 fw-bold" data-prev-url="{{ $prevUrl }}" style="border-radius: 12px; flex: 1;">Previous</button>
                                <button type="submit" class="next_question btn btn-warning py-3 px-4 fw-bold" style="border-radius: 12px; flex: 1; background: #f4c430; border: none;">{{ $isLastQuestion ? 'Submit' : 'Next' }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Quiz Navigation (Question Numbers) -->
            <div class="quiz-nav-right">
                <div style="margin-bottom: 32px; text-align: center; padding: 20px; background: #f9fafb; border-radius: 16px;">
                    <p style="font-size: 13px; color: #6b7280; margin-bottom: 8px; font-weight: 600;">Time Remaining</p>
                    <p id="quizTimer" style="margin: 0; font-size: 28px; font-weight: 800; color: #111827; font-family: monospace;">--:--</p>
                </div>

                <h5 style="font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 16px;">Question Navigation</h5>
                <div class="nomor_kuis" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 10px;">
                    @foreach($questions as $idx => $q)
                        @php
                            $isAnswered = in_array($q->id, $answeredQuestionIds ?? [], true);
                            $cls = $idx === $currentQuestionIndex ? 'kuis_aktif' : (!$isAnswered ? 'kuis_belum_diisi' : '');
                            $goUrl = route('user.quiz.take', [$course, $module, $attempt, 'q' => $idx]);
                        @endphp
                        <button type="button" class="{{ $cls }}" onclick="window.location.href='{{ $goUrl }}'">{{ $idx + 1 }}</button>
                    @endforeach
                </div>

                <div style="margin-top: auto; padding-top: 24px; border-top: 1px solid #f3f4f6;">
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                        <div style="width: 12px; height: 12px; background: #f4c430; border-radius: 3px;"></div>
                        <span style="font-size: 12px; color: #6b7280;">Current Position</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 12px; height: 12px; background: #f3f4f6; border: 1px solid #e5e7eb; border-radius: 3px;"></div>
                        <span style="font-size: 12px; color: #6b7280;">Not Answered</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ── Answer persistence via sessionStorage ──────────────────────────────
            const QUIZ_STORAGE_KEY = 'quiz_draft_{{ $attempt->id }}_q{{ $currentQuestion->id }}';

            // Restore saved draft answer on page load (before server-selected takes effect)
            (function restoreDraft() {
                try {
                    const saved = sessionStorage.getItem(QUIZ_STORAGE_KEY);
                    if (!saved) return;
                    const savedId = parseInt(saved, 10);
                    if (!savedId) return;

                    const alreadyChecked = document.querySelector('input[name="answer_id"]:checked');
                    if (alreadyChecked) return;

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

            let _draftDirty = false;
            @if($selectedAnswerId)
            _draftDirty = false;
            @endif

            // Click-to-select answers
            document.querySelectorAll('.quiz-answer-option').forEach(opt => {
                opt.addEventListener('click', function(e) {
                    const radio = this.querySelector('input[type="radio"]');
                    if (radio) {
                        radio.checked = true;
                    }
                    document.querySelectorAll('.quiz-answer-option').forEach(x => x.classList.remove('selected'));
                    this.classList.add('selected');
                    if (radio) radio.setCustomValidity('');

                    try {
                        sessionStorage.setItem(QUIZ_STORAGE_KEY, radio ? radio.value : '');
                    } catch (_e) {}
                    _draftDirty = true;
                });
            });

            // Form validation
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
                            alert('Anda harus menyelesaikan soal ini terlebih dahulu');
                        }
                        return;
                    }

                    @if($isLastQuestion)
                        const unansweredCount = document.querySelectorAll('.nomor_kuis button.kuis_belum_diisi').length;
                        if (unansweredCount > 0) {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Peringatan!',
                                text: 'Anda harus menyelesaikan semua soal kuis terlebih dahulu',
                                icon: 'warning',
                                confirmButtonText: 'Tutup',
                                confirmButtonColor: '#f4c430'
                            });
                            return;
                        }
                    @endif

                    _draftDirty = false;
                    try { sessionStorage.removeItem(QUIZ_STORAGE_KEY); } catch (_e) {}
                });
            }

            // Navigation warnings
            const prevBtn = document.querySelector('.previous_question');
            if (prevBtn) {
                prevBtn.addEventListener('click', function(e) {
                    const checked = document.querySelector('input[name="answer_id"]:checked');
                    const prevUrl = this.getAttribute('data-prev-url');
                    if (_draftDirty && checked) {
                        e.preventDefault();
                        Swal.fire({
                            title: 'Answer Not Saved',
                            text: 'Continue without saving?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes',
                            confirmButtonColor: '#f4c430',
                        }).then(result => {
                            if (result.isConfirmed) {
                                _draftDirty = false;
                                window.location.href = prevUrl;
                            }
                        });
                    } else {
                        window.location.href = prevUrl;
                    }
                });
            }

            // Timer Logic
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
                _draftDirty = false;
                const overlay = document.createElement('div');
                overlay.style.cssText = 'position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.55);display:flex;align-items:center;justify-content:center;';
                overlay.innerHTML = '<div style="background:#fff;border-radius:20px;padding:32px;text-align:center;"><h3>Waktu Habis!</h3><p>Mengumpulkan kuis...</p></div>';
                document.body.appendChild(overlay);

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                try {
                    await fetch(finishUrl, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token || '' },
                        keepalive: true,
                    });
                } catch (e) {}
                setTimeout(() => { window.location.href = resultUrl; }, 1000);
            }

            if (timerEl && endsAtIso) {
                const endsAt = new Date(endsAtIso).getTime() - (7 * 60 * 1000) - (7 * 1000);
                const intv = setInterval(() => {
                    const remaining = Math.floor((endsAt - Date.now()) / 1000);
                    timerEl.textContent = formatTime(remaining);
                    if (remaining <= 0) {
                        clearInterval(intv);
                        finishAttempt();
                    }
                }, 1000);
            }
        });
    </script>

</body>

</html>