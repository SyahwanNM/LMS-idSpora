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
    <div class="box_luar_kuis quiz-take-v2" id="quizTakeRoot">
        <div class="box_kuis_kiri quiz-modules">
            @php
                $modulesList = $course->modules()->orderBy('order_no')->get();
                $passingPercent = 75;

                $normalizeGroupKey = function ($title, $fallbackIndex = 1) {
                    $t = trim((string) $title);
                    if ($t === '') {
                        return 'UNIT_'.$fallbackIndex;
                    }

                    if (preg_match('/^(Module\s*\d+)/i', $t, $m)) {
                        return mb_strtoupper(trim($m[1]));
                    }

                    $t2 = preg_replace('/\s*-\s*(PDF\s*Material|Video\s*Lesson|Quiz)\s*$/i', '', $t);
                    $t2 = trim((string) $t2);
                    return $t2 !== '' ? mb_strtoupper($t2) : ('UNIT_'.$fallbackIndex);
                };

                // Extract the human-readable subtitle after "Module X" (e.g. "Module 1 - Testing" → "Testing")
                $extractModuleSubtitle = function ($title) {
                    $t = trim((string) $title);
                    if (preg_match('/^Module\s*\d+\s*-\s*(.+)$/i', $t, $m)) {
                        $sub = trim($m[1]);
                        if (!preg_match('/^(PDF\s*Material|Video\s*Lesson|Quiz)$/i', $sub)) {
                            return $sub;
                        }
                    }
                    return null;
                };

                $groupSubtitles = [];

                // Build unit title map from course_units table
                $unitTitleMap = [];
                if (isset($course)) {
                    foreach (($course->units ?? collect()) as $u) {
                        $unitTitleMap[(int) $u->unit_no] = (string) ($u->title ?? '');
                    }
                }

                $formatDisplayTitle = function (string $groupKey, string $kind) use (&$groupSubtitles) {
                    $isModuleKey = \Illuminate\Support\Str::startsWith($groupKey, 'MODULE');
                    $isUnitKey = \Illuminate\Support\Str::startsWith($groupKey, 'UNIT_');

                    $prefix = '';
                    if ($isModuleKey) {
                        $prefix = ucwords(strtolower($groupKey));
                    } elseif ($isUnitKey) {
                        $num = str_replace('UNIT_', '', $groupKey);
                        $prefix = 'Module ' . $num;
                    }

                    $hasPrefix = $isModuleKey || $isUnitKey;

                    $subtitle = $groupSubtitles[$groupKey] ?? null;
                    if ($subtitle && $hasPrefix) {
                        $prefix = $prefix . ' - ' . $subtitle;
                    }

                    // Hanya tampilkan nama modul, type label sudah ditampilkan terpisah
                    if ($kind === 'material') {
                        return $hasPrefix ? $prefix : 'Materi';
                    }
                    if ($kind === 'quiz') {
                        return $hasPrefix ? $prefix : 'Quiz';
                    }
                    return $hasPrefix ? $prefix : $groupKey;
                };

                // Build display list: per group show (Materi = PDF+Video combined) + Quiz
                $grouped = [];
                $groupOrder = [];
                $unitCounter = 1;
                $currentGenericKey = null;
                $isGenericUnitTitle = function ($title) {
                    $t = trim((string) $title);
                    return (bool) preg_match('/^(PDF\s*Material|Video\s*Lesson|Quiz)$/i', $t);
                };
                foreach ($modulesList as $m) {
                    $titleStr = (string) ($m->title ?? '');
                    $type = strtolower(trim((string) ($m->type ?? '')));

                    if ($isGenericUnitTitle($titleStr)) {
                        if (!$currentGenericKey) {
                            $currentGenericKey = 'UNIT_'.$unitCounter;
                            $unitCounter++;
                        }
                        $key = $currentGenericKey;
                        if ($type === 'quiz') {
                            $currentGenericKey = null;
                        }
                    } else {
                        $currentGenericKey = null;
                        $key = $normalizeGroupKey($titleStr, $unitCounter);
                        if (\Illuminate\Support\Str::startsWith($key, 'UNIT_')) {
                            $unitCounter++;
                        }
                    }

                    if (!array_key_exists($key, $grouped)) {
                        $grouped[$key] = [
                            'key' => $key,
                            'pdf' => null,
                            'video' => null,
                            'quiz' => null,
                        ];
                        $groupOrder[] = $key;
                    }

                    if (!isset($groupSubtitles[$key])) {
                        $unitNum = null;
                        if (preg_match('/MODULE\s*(\d+)/i', $key, $km)) {
                            $unitNum = (int) $km[1];
                        } elseif (preg_match('/UNIT_(\d+)/i', $key, $km)) {
                            $unitNum = (int) $km[1];
                        }
                        if ($unitNum && !empty($unitTitleMap[$unitNum])) {
                            $groupSubtitles[$key] = $unitTitleMap[$unitNum];
                        } else {
                            $sub = $extractModuleSubtitle($titleStr);
                            if ($sub) {
                                $groupSubtitles[$key] = $sub;
                            }
                        }
                    }

                    if ($type === 'pdf' && !$grouped[$key]['pdf']) $grouped[$key]['pdf'] = $m;
                    if ($type === 'video' && !$grouped[$key]['video']) $grouped[$key]['video'] = $m;
                    if ($type === 'quiz' && !$grouped[$key]['quiz']) $grouped[$key]['quiz'] = $m;
                }

                $displayItems = collect();
                $quizCounter = 0;
                foreach ($groupOrder as $key) {
                    $g = $grouped[$key];

                    if ($g['pdf'] || $g['video']) {
                        $rep = $g['pdf'] ?: $g['video'];
                        $displayItems->push([
                            'kind' => 'material',
                            'key' => $key,
                            'title' => $formatDisplayTitle($key, 'material'),
                            'rep' => $rep,
                            'pdf' => $g['pdf'],
                            'video' => $g['video'],
                            'quiz' => $g['quiz'],
                        ]);
                    }

                    if ($g['quiz']) {
                        $quizCounter++;
                        $subtitle = $groupSubtitles[$key] ?? null;
                        $quizTitle = 'Quiz ' . $quizCounter . ($subtitle ? ' - ' . $subtitle : '');
                        $displayItems->push([
                            'kind' => 'quiz',
                            'key' => $key,
                            'title' => $quizTitle,
                            'rep' => $g['quiz'],
                            'pdf' => $g['pdf'],
                            'video' => $g['video'],
                            'quiz' => $g['quiz'],
                        ]);
                    }
                }

                $freeAccessMode = $freeAccessMode ?? ((isset($course) && (int)($course->price ?? 0) <= 0) ? (string)($course->free_access_mode ?? 'limit_2') : 'all');
                
                $freeAccessibleModuleIds = [];
                if ($freeAccessMode === 'limit_2') {
                    $freeAccessibleModuleIds = $modulesList->take(2)->pluck('id')->map(fn($id) => (int) $id)->values()->all();
                }

                $isFreeLimited = ($freeAccessMode === 'limit_2');

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

                // Completed material module IDs from Progress table
                $completedMaterialModuleIds = [];
                if (auth()->check()) {
                    $enrollment = \App\Models\Enrollment::where('user_id', auth()->id())
                        ->where('course_id', $course->id)
                        ->whereIn('status', ['active', 'completed', 'expired'])
                        ->first();
                    if ($enrollment) {
                        $videoModuleIds = $modulesList
                            ->filter(fn($m) => strtolower(trim((string)($m->type ?? ''))) === 'video')
                            ->pluck('id')->map(fn($id) => (int)$id)->all();

                        $completedMaterialModuleIds = \App\Models\Progress::where('enrollment_id', $enrollment->id)
                            ->where('completed', true)
                            ->get(['course_module_id', 'video_watched'])
                            ->filter(function ($p) use ($videoModuleIds) {
                                $mid = (int) $p->course_module_id;
                                if (in_array($mid, $videoModuleIds, true)) {
                                    return (bool) $p->video_watched;
                                }
                                return true;
                            })
                            ->pluck('course_module_id')
                            ->map(fn($id) => (int) $id)
                            ->values()
                            ->all();
                    }
                }
            @endphp

            <div class="quiz-modules-head">
                <div class="quiz-modules-title">List Module</div>
                <button type="button" class="quiz-modules-close" id="closeModulesBtn" aria-label="Tutup daftar modul">&times;</button>
            </div>

            <div class="accordion-box">
                @php $hasFailedPrevQuiz = false; @endphp
                @forelse($displayItems as $it)
                    @php
                        $rep = $it['rep'];
                        $isActive = ((int) ($module->id ?? 0) === (int) ($rep->id ?? 0))
                            && (($it['kind'] ?? '') === 'quiz'); 
                        $typeLabel = ($it['kind'] ?? '') === 'quiz' ? 'QUIZ' : 'MATERI';
                        
                        $isLocked = $hasFailedPrevQuiz;
                        $lockReason = $hasFailedPrevQuiz ? 'quiz' : '';

                        if ($isFreeLimited && !$isLocked) {
                            $candidateIds = [];
                            if (($it['kind'] ?? '') === 'quiz') {
                                $candidateIds[] = (int) ($rep->id ?? 0);
                            } else {
                                if (!empty($it['pdf']?->id)) $candidateIds[] = (int) $it['pdf']->id;
                                if (!empty($it['video']?->id)) $candidateIds[] = (int) $it['video']->id;
                                if (empty($candidateIds)) $candidateIds[] = (int) ($rep->id ?? 0);
                            }

                            $allowedAny = false;
                            foreach ($candidateIds as $cid) {
                                if ($cid > 0 && in_array($cid, $freeAccessibleModuleIds, true)) { $allowedAny = true; break; }
                            }

                            if (!$allowedAny) {
                                $isLocked = true;
                                $lockReason = 'free';
                            }
                        }

                        // Cascading quiz check: if this is a quiz and not passed, ALL subsequent items will be locked.
                        if (($it['kind'] ?? '') === 'quiz') {
                            $quizId = (int) ($it['quiz']?->id ?? $rep->id ?? 0);
                            if (auth()->check() && !in_array($quizId, $passedQuizModuleIds, true)) {
                                $hasFailedPrevQuiz = true;
                            }
                        }

                        // Check completion
                        $isDone = false;
                        if (($it['kind'] ?? '') === 'quiz') {
                            $isDone = in_array((int) ($it['quiz']?->id ?? 0), $passedQuizModuleIds, true);
                        } else {
                            $videoId = (int) ($it['video']?->id ?? 0);
                            $pdfId   = (int) ($it['pdf']?->id ?? 0);
                            if ($videoId > 0) {
                                $isDone = in_array($videoId, $completedMaterialModuleIds, true);
                            } elseif ($pdfId > 0) {
                                $isDone = in_array($pdfId, $completedMaterialModuleIds, true);
                            }
                        }

                        $learnUrl = route('course.learn', $course->id) . '?module=' . (int) ($rep->id ?? 0);
                    @endphp
                    <div class="accordion-item {{ $isActive ? 'selected active' : '' }} {{ $isLocked ? 'is-locked' : '' }}" data-locked="{{ $isLocked ? '1' : '0' }}" data-locked-reason="{{ $lockReason }}" data-learn-url="{{ $learnUrl }}">
                        <button class="accordion-header" type="button">
                            <span style="display:flex; align-items:center; gap:10px; min-width:0;">
                                <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $it['title'] ?? ($rep->title ?? 'Materi') }}</span>
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
                                <span style="font-size:12px; font-weight:700; opacity:.75; min-width:44px; text-align:right;">{{ $typeLabel }}</span>
                                <span class="arrow" style="width:16px; text-align:center;">▲</span>
                            </span>
                        </button>
                        <div class="accordion-content">
                            @if($isLocked)
                                @if($lockReason === 'free')
                                    <p class="text-muted" style="margin:0; font-size:13px;">Locked. Register or purchase a course to unlock this module.</p>
                                @else
                                    <p class="text-muted" style="margin:0; font-size:13px;">Locked. Pass the quiz first to unlock the next material.</p>
                                @endif
                            @else
                                <p class="text-muted" style="margin:0; font-size:13px;">Click to review the material.</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-muted" style="padding:12px;">Belum ada modul pada course ini.</div>
                @endforelse
            </div>
        </div>

        <div class="box_kuis_kanan">
            <div class="quiz-title-row" style="margin-top: 24px;">
                <div class="quiz-title-left">
                    <button type="button" class="quiz-modules-open" id="openModulesBtn" aria-label="Buka daftar modul">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16" aria-hidden="true">
                            <path d="M2.5 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5m0 4a.5.5 0 0 1 .5-.5h10a.5.5 0 0 1 0 1H3a.5.5 0 0 1-.5-.5"/>
                        </svg>
                    </button>
                    <h1 class="quiz-title">{{ $quizPageTitle }}</h1>
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
                        <button type="submit" class="next_question">{{ $isLastQuestion ? 'Submit' : 'Next' }}</button>
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

        // Sidebar accordion
        const accItems = document.querySelectorAll('.box_kuis_kiri.quiz-modules .accordion-item');
        accItems.forEach(item => {
            const header = item.querySelector('.accordion-header');
            if (!header) return;
            header.addEventListener('click', (e) => {
                e.preventDefault();
                const isOpen = item.classList.contains('active');
                if (!isOpen) {
                    accItems.forEach(other => {
                        if (other !== item) other.classList.remove('active');
                    });
                    item.classList.add('active');
                    return;
                }

                const locked = item.getAttribute('data-locked') === '1';
                if (locked) {
                    const reason = item.getAttribute('data-locked-reason') || '';
                    if (reason === 'free') {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'Materi ini terkunci. Silakan beli atau daftar course ini untuk membuka seluruh materi.',
                            icon: 'warning',
                            confirmButtonColor: '#f4c430',
                        });
                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'Anda harus menyelesaikan kuis terlebih dahulu baru bisa lanjut ke tahap selanjutnya.',
                            icon: 'warning',
                            confirmButtonColor: '#f4c430',
                        });
                    }
                    return;
                }

                const url = item.getAttribute('data-learn-url');
                if (url) {
                    window.location.href = url;
                }
            });
        });

        // Sidebar open/close with persistence
        const rootEl = document.getElementById('quizTakeRoot');
        const modulesSidebar = document.querySelector('.box_kuis_kiri.quiz-modules');
        const openModulesBtn = document.getElementById('openModulesBtn');
        const closeModulesBtn = document.getElementById('closeModulesBtn');
        const SIDEBAR_STATE_KEY = 'quiz_sidebar_open_{{ $attempt->id }}';

        function setModulesOpen(isOpen) {
            if (!modulesSidebar) return;
            modulesSidebar.classList.toggle('closed', !isOpen);
            if (openModulesBtn) openModulesBtn.classList.toggle('d-none', isOpen);
            if (rootEl) rootEl.classList.toggle('modules-closed', !isOpen);
            
            // Save state to sessionStorage
            try {
                sessionStorage.setItem(SIDEBAR_STATE_KEY, isOpen ? '1' : '0');
            } catch (_e) {}
        }

        if (closeModulesBtn) {
            closeModulesBtn.addEventListener('click', () => setModulesOpen(false));
        }
        if (openModulesBtn) {
            openModulesBtn.addEventListener('click', () => setModulesOpen(true));
        }

        // Restore sidebar state from sessionStorage
        try {
            const savedState = sessionStorage.getItem(SIDEBAR_STATE_KEY);
            if (savedState === '0') {
                setModulesOpen(false);
            } else {
                // Default: sidebar terbuka (termasuk saat pertama kali buka quiz)
                setModulesOpen(true);
            }
        } catch (_e) {
            setModulesOpen(true);
        }

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