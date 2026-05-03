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
            <div style="background-color: #f4dc21ea; width: 10%; height: 5%; padding: 10px; margin-top: 35px; margin-bottom: 10px; border-radius: 4px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16" style="margin-top: -20px;">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
            </div>
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
                $isFreeLimited = ((string)($freeAccessMode ?? 'all') === 'limit_2');

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

                // Completed material module IDs (pdf/video) from Progress table
                $completedMaterialModuleIds = [];
                if (auth()->check() && isset($course)) {
                    $enrollment = \App\Models\Enrollment::where('user_id', auth()->id())
                        ->where('course_id', $course->id)
                        ->whereIn('status', ['active', 'completed', 'expired'])
                        ->first();
                    if ($enrollment) {
                        // For video modules: require BOTH completed=1 AND video_watched=1
                        // This prevents old stale records (completed=1, video_watched=0) from showing checkmarks
                        $videoModuleIds = $modulesList
                            ->filter(fn($m) => strtolower(trim((string)($m->type ?? ''))) === 'video')
                            ->pluck('id')->map(fn($id) => (int)$id)->all();

                        $completedMaterialModuleIds = \App\Models\Progress::where('enrollment_id', $enrollment->id)
                            ->where('completed', true)
                            ->get(['course_module_id', 'video_watched'])
                            ->filter(function ($p) use ($videoModuleIds) {
                                $mid = (int) $p->course_module_id;
                                if (in_array($mid, $videoModuleIds, true)) {
                                    return (bool) $p->video_watched; // video: need video_watched=1 too
                                }
                                return true; // pdf/other: completed=1 is enough
                            })
                            ->pluck('course_module_id')
                            ->map(fn($id) => (int) $id)
                            ->values()
                            ->all();
                    }
                }

                $currentQuizPassed = true;
                if ($activeModule && strtolower(trim((string) ($activeModule->type ?? ''))) === 'quiz') {
                    $currentQuizPassed = in_array((int) $activeModule->id, $passedQuizModuleIds, true);
                }

                $totalModules = $modulesList->count();
                $filteredModules = $modulesList->filter(fn($m) => strtolower(trim((string)($m->type ?? ''))) !== 'pdf')->values();
                $pdfCount = $modulesList->where('type', 'pdf')->count();
                $videoCount = $modulesList->where('type', 'video')->count();
                $quizCount = $modulesList->where('type', 'quiz')->count();
                $missingMaterials = [];
                if ($totalModules <= 0) { $missingMaterials[] = 'Modul'; }
                if ($pdfCount <= 0) { $missingMaterials[] = 'Modul (PDF)'; }
                if ($videoCount <= 0) { $missingMaterials[] = 'Video'; }
                if ($quizCount <= 0) { $missingMaterials[] = 'Kuis'; }
            @endphp

            {{-- UI tetap seperti sebelumnya, tapi konten dinamis dari backend --}}
            @if(!empty($missingMaterials))
                <div class="alert alert-warning" role="alert" style="margin: 0 0 12px 0;">
                    <div style="font-weight:600;">Oops, course modules are incomplete.</div>
                    <div style="margin-top:6px;">{{ implode(', ', $missingMaterials) }} belum ada. Segera hubungi trainer.</div>
                </div>
            @endif

            <div class="accordion-box"
                data-learn-base="{{ isset($course) ? route('course.learn', $course->id) : '' }}"
                data-active-module-id="{{ $activeModule?->id }}"
            >
                @forelse($modulesList as $m)
                    @php
                        $isActive = $activeModule && ((int)$activeModule->id === (int)$m->id);
                        $typeLabel = $m->type ? strtoupper($m->type) : 'MATERI';
                        $prevModule = $modulesList->get($loop->index - 1);
                        $mId = (int) $m->id;
                        $isDone = false;
                        $mType = strtolower(trim((string)($m->type ?? '')));
                        if (auth()->check()) {
                            if ($mType === 'quiz') {
                                $isDone = in_array($mId, $passedQuizModuleIds, true);
                            } else {
                                $isDone = in_array($mId, $completedMaterialModuleIds, true);
                            }
                        }

                        // SEQUENTIAL GATING LOGIC:
                        // A module is locked if ANY preceding QUIZ has not been passed.
                        // We use $filteredModules (which skips PDFs) to determine the sequence.
                        $isLocked = false;
                        $lockReason = '';

                        // Get all modules that appear BEFORE the current one in the sidebar
                        $currentIdxInFiltered = $filteredModules->search(fn($fm) => (int)$fm->id === $mId);
                        if ($currentIdxInFiltered !== false) {
                            $precedingModules = $filteredModules->take($currentIdxInFiltered);
                            foreach ($precedingModules as $pm) {
                                if (strtolower(trim((string)($pm->type ?? ''))) === 'quiz') {
                                    if (!in_array((int)$pm->id, $passedQuizModuleIds, true)) {
                                        $isLocked = true;
                                        $lockReason = 'quiz';
                                        break;
                                    }
                                }
                                // Optional: also lock if previous video is not completed
                                /*
                                if (strtolower(trim((string)($pm->type ?? ''))) === 'video') {
                                    if (!in_array((int)$pm->id, $completedMaterialModuleIds, true)) {
                                        $isLocked = true;
                                        $lockReason = 'video';
                                        break;
                                    }
                                }
                                */
                            }
                        }

                        if ($isFreeLimited && !in_array((int) $m->id, $freeAccessibleModuleIds, true)) {
                            $isLocked = true;
                            $lockReason = 'free';
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

                        // Unified UI: Skip all PDF modules in sidebar as they are now integrated into video lessons
                        $skipInSidebar = false;
                        if (strtolower(trim((string)($m->type ?? ''))) === 'pdf') {
                            $skipInSidebar = true;
                        }
                    @endphp

                    @if(!$skipInSidebar)

                    <div class="accordion-item {{ $isActive ? 'selected active' : '' }} {{ $isLocked ? 'is-locked' : '' }}" data-locked="{{ $isLocked ? '1' : '0' }}" data-locked-reason="{{ $lockReason }}">
                        <button class="accordion-header" type="button" data-module-id="{{ $m->id }}">
                            <span style="display:flex; align-items:center; gap:10px; min-width:0;">
                                <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $m->title ?? 'Materi' }}</span>
                            </span>
                            <span style="display:flex; align-items:center; gap:8px; flex:0 0 auto;">
                                <span style="width:20px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                    @if($isDone)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#16a34a" class="bi bi-check-circle-fill" viewBox="0 0 16 16" aria-label="Completed">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                        </svg>
                                    @elseif($isLocked)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#111827" class="bi bi-lock-fill" viewBox="0 0 16 16" aria-hidden="true">
                                            <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                                        </svg>
                                    @endif
                                </span>
                                <span class="arrow" style="width:16px; text-align:center;">▲</span>
                            </span>
                        </button>
                        <div class="accordion-content">
                            <p>Tipe: {{ $typeLabel }}</p>
                            @if($isLocked)
                                <hr>
                                @if($lockReason === 'free')
                                    <p class="text-muted" style="margin:0; font-size:13px;">Locked. Enroll or purchase this course to unlock this module.</p>
                                @else
                                    <p class="text-muted" style="margin:0; font-size:13px;">Locked. Pass the quiz first to unlock the next material.</p>
                                @endif
                            @endif
                            {{-- HIDE DESKRIPSI DI ACCORDION MENU BAWAH --}}
                            {{-- @if(!empty($descLines))
                                <hr>
                                @foreach($descLines as $idx => $line)
                                    <p>{{ trim($line) }}</p>
                                    @if($idx < count($descLines) - 1)
                                        <hr>
                                    @endif
                                @endforeach
                            @endif --}}
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="text-muted" style="padding:12px;">No modules available for this course.</div>
                @endforelse
            </div>
        </div>

        <!-- RIGHT AREA: Split into Main Content and Quiz Navigation -->
        <div class="box_modul_kanan_quiz">
            <!-- MIDDLE: Question Content -->
            <div class="quiz-main-content">
                <div style="width: 100%; max-width: 800px; box-sizing: border-box;">
                    <div style="margin-bottom: 32px;">
                        <span style="display: inline-block; padding: 6px 12px; background: #fef3c7; color: #d97706; border-radius: 6px; font-weight: 700; font-size: 11px; text-transform: uppercase; margin-bottom: 12px; margin-top: 35px;">Quiz Module</span>
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