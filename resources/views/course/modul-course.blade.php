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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body { overflow: hidden; height: 100%; }
        .wysiwyg-output {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        .wysiwyg-output p,
        .wysiwyg-output div,
        .wysiwyg-output h1,
        .wysiwyg-output h2,
        .wysiwyg-output h3,
        .wysiwyg-output h4,
        .wysiwyg-output ul,
        .wysiwyg-output ol,
        .wysiwyg-output li,
        .wysiwyg-output blockquote,
        .wysiwyg-output pre {
            max-width: 100% !important;
            width: 100% !important;
            box-sizing: border-box !important;
        }
        .box_luar_deskripsi_modul,
        .trainer-html-content {
            width: 100% !important;
            box-sizing: border-box !important;
        }
    </style>
</head>

<body>
    @include("partials.navbar-after-login")
    <div class="box_modul_luar">
        <div class="box_modul_kiri">
            @php
                $modulesList = $modules ?? (isset($course) ? ($course->modules ?? collect()) : collect());
                $modulesList = $modulesList instanceof \Illuminate\Support\Collection ? $modulesList->values() : collect($modulesList)->values();
                $modulesList = $modulesList->sortBy('order_no')->values();
                $activeModule = $currentModule ?? $modulesList->first();
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
                        // Ignore generic suffixes
                        if (!preg_match('/^(PDF\s*Material|Video\s*Lesson|Quiz)$/i', $sub)) {
                            return $sub;
                        }
                    }
                    return null;
                };

                // Build unit title map from course_units table (unit_no → title), same source as syllabus in detail page
                $unitTitleMap = [];
                if (isset($course) && $course->relationLoaded('units')) {
                    foreach ($course->units as $u) {
                        $unitTitleMap[(int) $u->unit_no] = (string) ($u->title ?? '');
                    }
                } elseif (isset($course)) {
                    foreach (($course->units ?? collect()) as $u) {
                        $unitTitleMap[(int) $u->unit_no] = (string) ($u->title ?? '');
                    }
                }

                // groupSubtitles: keyed by group key (e.g. "MODULE 1"), value = human title
                $groupSubtitles = [];

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

                    // Append subtitle if available (e.g. "Module 1 - Testing")
                    $subtitle = $groupSubtitles[$groupKey] ?? null;
                    if ($subtitle && $hasPrefix) {
                        $prefix = $prefix . ' - ' . $subtitle;
                    }

                    if ($kind === 'material') {
                        return $hasPrefix ? ($prefix) : 'Material';
                    }
                    if ($kind === 'quiz') {
                        return $hasPrefix ? ($prefix) : 'Quiz';
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

                    // Store subtitle for this group if not yet set
                    if (!isset($groupSubtitles[$key])) {
                        // Priority 1: from course_units table (unit_no matches group index)
                        // Extract unit number from key: "MODULE 1" → 1, "UNIT_2" → 2
                        $unitNum = null;
                        if (preg_match('/MODULE\s*(\d+)/i', $key, $km)) {
                            $unitNum = (int) $km[1];
                        } elseif (preg_match('/UNIT_(\d+)/i', $key, $km)) {
                            $unitNum = (int) $km[1];
                        }
                        if ($unitNum && !empty($unitTitleMap[$unitNum])) {
                            $groupSubtitles[$key] = $unitTitleMap[$unitNum];
                        } else {
                            // Priority 2: parse from module title (e.g. "Module 1 - Testing")
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

                $activeGroupKey = null;
                if ($activeModule) {
                    foreach ($grouped as $gk => $g) {
                        foreach (['pdf', 'video', 'quiz'] as $k) {
                            if (!empty($g[$k]?->id) && (int) $g[$k]->id === (int) $activeModule->id) {
                                $activeGroupKey = $gk;
                                break 2;
                            }
                        }
                    }
                    $activeGroupKey = $activeGroupKey ?: $normalizeGroupKey($activeModule->title ?? '', 1);
                }
                $activeKind = ($activeModule && strtolower(trim((string) ($activeModule->type ?? ''))) === 'quiz') ? 'quiz' : 'material';
                $activeDisplay = $displayItems->first(function ($it) use ($activeGroupKey, $activeKind) {
                    return ($it['key'] ?? null) === $activeGroupKey && ($it['kind'] ?? null) === $activeKind;
                });
                $activeDisplay = $activeDisplay ?: $displayItems->first();
                $activeDisplay = $activeDisplay ?: [
                    'kind' => null,
                    'key' => null,
                    'title' => null,
                    'rep' => null,
                    'pdf' => null,
                    'video' => null,
                    'quiz' => null,
                ];
                $activeDisplayModuleId = !empty($activeDisplay['rep']?->id)
                    ? (int) $activeDisplay['rep']->id
                    : ($activeModule?->id);

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
                    <div style="font-weight:600;">Oops, course modules are incompletep.</div>
                    <div style="margin-top:6px;">{{ implode(', ', $missingMaterials) }} belum ada. Segera hubungi trainer.</div>
                </div>
            @endif

            <div class="accordion-box"
                data-learn-base="{{ isset($course) ? route('course.learn', $course->id) : '' }}"
                data-active-module-id="{{ $activeDisplayModuleId }}"
            >
                @php $hasFailedPrevQuiz = false; @endphp
                @forelse($displayItems as $it)
                    @php
                        $rep = $it['rep'];
                        $isActive = ((int) ($activeDisplayModuleId ?? 0) === (int) ($rep->id ?? 0))
                            && (($activeDisplay['kind'] ?? '') === ($it['kind'] ?? ''));
                        $typeLabel = ($it['kind'] ?? '') === 'quiz' ? 'QUIZ' : 'MATERIAL';
                        
                        $isLocked = $hasFailedPrevQuiz;
                        $lockReason = $hasFailedPrevQuiz ? 'quiz' : '';

                        // Check if this item is completed
                        $isDone = false;
                        if (($it['kind'] ?? '') === 'quiz') {
                            $isDone = in_array((int) ($it['quiz']?->id ?? 0), $passedQuizModuleIds, true);
                        } else {
                            // If group has a video, only video completion counts (must be watched to end)
                            // If group has only PDF (no video), PDF completion counts
                            $videoId = (int) ($it['video']?->id ?? 0);
                            $pdfId   = (int) ($it['pdf']?->id ?? 0);
                            if ($videoId > 0) {
                                $isDone = in_array($videoId, $completedMaterialModuleIds, true);
                            } elseif ($pdfId > 0) {
                                $isDone = in_array($pdfId, $completedMaterialModuleIds, true);
                            }
                        }

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

                        $descLines = [];
                        $descSource = ($it['kind'] ?? '') === 'quiz'
                            ? ($it['quiz']?->description ?? '')
                            : (($it['pdf']?->description ?? '') ?: ($it['video']?->description ?? '') ?: ($rep->description ?? ''));

                        if (!empty($descSource)) {
                            $cleanDesc = trim(strip_tags((string) $descSource));
                            if ($cleanDesc !== '') {
                                // If it's a trainer-edited HTML content, just show a snippet or nothing in sub-items
                                // to avoid cluttering the sidebar with split sentences.
                                $isRich = str_contains((string)$descSource, '<') && str_contains((string)$descSource, '>');
                                if ($isRich) {
                                    $descLines = [\Illuminate\Support\Str::limit($cleanDesc, 80)];
                                } else {
                                    // Split into short lines to preserve the original "list" feel for legacy content
                                    $descLines = array_values(array_filter(preg_split('/\r?\n|\.|\!|\?/u', $cleanDesc)));
                                    $descLines = array_slice($descLines, 0, 3);
                                }
                            }
                        }
                    @endphp

                    <div class="accordion-item {{ $isActive ? 'selected active' : '' }} {{ $isLocked ? 'is-locked' : '' }}" data-locked="{{ $isLocked ? '1' : '0' }}" data-locked-reason="{{ $lockReason }}">
                        <button class="accordion-header" type="button" data-module-id="{{ $rep->id }}">
                            <span style="display:flex; align-items:center; gap:10px; min-width:0;">
                                <span style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $it['title'] ?? ($rep->title ?? 'Material') }}</span>
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
                @empty
                    <div class="text-muted" style="padding:12px;">No modules available for this course.</div>
                @endforelse
            </div>
        </div>
        <div class="scroll-modul-box">
        <div class="box_modul_kanan">
            @php
                $activeKind = $activeDisplay['kind'] ?? (($activeModule && strtolower(trim((string) ($activeModule->type ?? ''))) === 'quiz') ? 'quiz' : 'material');

                $pdfModule = $activeDisplay['pdf'] ?? null;
                $videoModule = $activeDisplay['video'] ?? null;
                $quizModule = $activeDisplay['quiz'] ?? null;

                $cm = $activeKind === 'quiz'
                    ? ($quizModule ?: $activeModule)
                    : ($activeDisplay['rep'] ?? $activeModule);

                $moduleType = $cm ? strtolower(trim((string) ($cm->type ?? ''))) : '';
                $isQuiz = ($activeKind === 'quiz');

                $resolveMediaUrl = function ($module) use ($course) {
                    if (!$module) return [null, false, null, null];
                    $content = trim((string) ($module->content_url ?? ''));
                    // If content is empty or just a slash, it's not a real file
                    if ($content === '' || $content === '/') return [null, false, null, null];

                    $isHttp = str_starts_with($content, 'http://') || str_starts_with($content, 'https://');
                    $storageUrl = null;
                    if (!$isHttp) {
                        $normalized = ltrim($content, '/');
                        if (\Illuminate\Support\Str::startsWith($normalized, 'uploads/')) {
                            $normalized = substr($normalized, strlen('uploads/'));
                        }
                        $storageUrl = asset('uploads/' . $normalized);
                    }
                    $streamUrl = (!$isHttp && isset($course) && $module) ? route('user.modules.stream', [$course, $module]) : null;
                    $url = $isHttp ? $content : ($storageUrl ?: $streamUrl);
                    return [$url, $isHttp, $storageUrl, $streamUrl];
                };

                [$pdfUrl] = $resolveMediaUrl($pdfModule);
                [$vidUrl, $vidIsHttp] = $resolveMediaUrl($videoModule);
            @endphp

            @php
                // Detect if the PDF module has rich HTML content from trainer editor
                $pdfDescription = trim((string) ($pdfModule?->description ?? ''));
                $videoDescription = trim((string) ($videoModule?->description ?? ''));
                $cmDescription = trim((string) ($cm->description ?? ''));
                $materialDescription = $pdfDescription ?: $videoDescription ?: $cmDescription;
                
                // Content counts as HTML if it contains tags
                $hasRichHtml = $materialDescription !== '' && (
                    str_contains($materialDescription, '<') && str_contains($materialDescription, '>')
                );
                
                $hasPdfFile = !empty($pdfUrl);
                
                // Prioritize showing HTML content if a file doesn't exist OR if it's explicitly HTML content
                $showHtmlContent = $hasRichHtml && !$hasPdfFile;
            @endphp

            {{-- Materi: PDF + Video jadi satu kesatuan. Kuis: tidak menampilkan media kosong. --}}
            @if(!$isQuiz)
                <div class="modul_media_card">
                    @if($videoModule && $vidUrl)
                        @if($vidIsHttp)
                            <iframe class="video_course" width="560" height="315" src="{{ $videoModule->content_url }}" title="Video" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                        @else
                            <video class="video_course" width="560" height="315" controls preload="auto" playsinline>
                                <source src="{{ $vidUrl }}" type="{{ $videoModule->mime_type ?: 'video/mp4' }}">
                            </video>
                        @endif
                    @endif

                    @if($hasPdfFile)
                        @if($videoModule && $vidUrl)
                            <div style="margin-top:12px;"></div>
                        @endif
                        <iframe class="video_course" width="560" height="315" src="{{ $pdfUrl }}" title="PDF" frameborder="0"></iframe>
                    @endif

                    @if(!$showHtmlContent && !$hasPdfFile && (!$videoModule || !$vidUrl))
                        <iframe class="video_course" width="560" height="315" src="" title="Content" frameborder="0"></iframe>
                    @endif
                </div>
            @endif

            <h2 class="judul_modul">{{ $activeDisplay['title'] ?? ($cm->title ?? 'Gambaran Umum') }}</h2>

            @if(!$isQuiz)
                <div class="box_luar_deskripsi_modul">
                    <div class="box_deskripsi_modul modul_course" style="padding: 2px; max-height: none; overflow-y: visible; height: auto;">
                        @if($showHtmlContent)
                            {{-- Trainer rich text HTML content --}}
                            <div class="trainer-html-content" style="padding: 20px 20px 20px 4px; background: #fff; border-radius: 12px; border: none; width: 100%; box-sizing: border-box; max-height: none; overflow-y: visible; height: auto;">
                                <div class="wysiwyg-output" style="width:100%; text-align:left;">
                                    {!! $materialDescription !!}
                                </div>
                            </div>
                        @else
                            <div style="padding: 20px 20px 20px 4px; width: 100%; box-sizing: border-box; max-height: none; overflow-y: visible; height: auto;">
                                @if($hasRichHtml)
                                    <div class="deskripsi_modul wysiwyg-output" style="font-size: 14px; color: #374151; width: 100%; text-align:left;">
                                        {!! $materialDescription !!}
                                    </div>
                                @else
                                    <p class="deskripsi_modul" style="font-size: 14px; color: #374151; width: 100%; margin: 0; text-align:left;">
                                        {{ $materialDescription ?: 'Module description not available.' }}
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @else
                @php
                    $questionCount = $cm?->quizQuestions?->count() ?? 0;
                    $passingPercent = 75;
                    $durationSeconds = (int) ($cm->duration ?? 0);
                    $durationMinutes = $durationSeconds > 0 ? (int) ceil($durationSeconds / 60) : 0;

                    // Jika durasi tidak di-set, gunakan default: 15 menit untuk kuis terakhir, 10 menit lainnya
                    if ($durationMinutes <= 0 && $cm) {
                        $isLastQuiz = !$course->modules()
                            ->where('type', 'quiz')
                            ->where('order_no', '>', $cm->order_no)
                            ->exists();
                        $durationMinutes = $isLastQuiz ? 15 : 10;
                    }

                    $durationText = $durationMinutes > 0 ? ($durationMinutes.' minutes') : '10 menit';

                    $beforeQuizTitle = null;
                    if ($activeDisplay && (($activeDisplay['kind'] ?? '') === 'quiz')) {
                        $beforeQuizTitle = $formatDisplayTitle($activeDisplay['key'], 'material');
                    }
                    if (!$beforeQuizTitle && $cm && isset($modulesList) && $modulesList instanceof \Illuminate\Support\Collection) {
                        $idx = $modulesList->search(fn($x) => (int) ($x->id ?? 0) === (int) $cm->id);
                        if ($idx !== false && $idx > 0) {
                            $prev = $modulesList->get($idx - 1);
                            $beforeQuizTitle = $prev?->title ?? null;
                        }
                    }
                    $beforeQuizTitle = $beforeQuizTitle ?: ($cm->title ?? 'materi sebelumnya');

                    $attempts = collect();
                    $ongoingAttempt = null;
                    if (auth()->check() && $cm) {
                        // Selesaikan ongoing attempt yang sudah expired sebelum query attempts
                        $rawOngoing = \App\Models\QuizAttempt::query()
                            ->where('user_id', auth()->id())
                            ->where('course_module_id', $cm->id)
                            ->whereNull('completed_at')
                            ->latest()
                            ->first();

                        if ($rawOngoing) {
                            $isLastQuizCheck = !$course->modules()
                                ->where('type', 'quiz')
                                ->where('order_no', '>', $cm->order_no)
                                ->exists();
                            $ongoingDurSec = ($isLastQuizCheck ? 15 : 10) * 60;
                            $ongoingStarted = $rawOngoing->started_at ?? $rawOngoing->created_at;
                            $ongoingExpired = $ongoingStarted ? $ongoingStarted->copy()->addSeconds($ongoingDurSec) : null;

                            if ($ongoingExpired && $ongoingExpired->isFuture()) {
                                // Masih berjalan — tampilkan tombol Lanjutkan
                                $ongoingAttempt = $rawOngoing;
                            } else {
                                // Sudah expired — complete dengan waktu sekarang (WIB)
                                $rawOngoing->update(['completed_at' => \Carbon\Carbon::now('Asia/Jakarta')]);
                                $rawOngoing->refresh();
                            }
                        }

                        $attempts = \App\Models\QuizAttempt::query()
                            ->where('user_id', auth()->id())
                            ->where('course_module_id', $cm->id)
                            ->whereNotNull('completed_at')
                            ->orderByDesc('completed_at')
                            ->limit(10)
                            ->get();
                    }

                    $startUrl = (isset($course) && $cm) ? route('user.quiz.start', [$course, $cm]) : '#';
                    $continueUrl = ($ongoingAttempt && isset($course) && $cm)
                        ? route('user.quiz.take', [$course, $cm, $ongoingAttempt])
                        : null;

                    // Cooldown: 1 menit setelah attempt terbaru yang tidak lulus
                    $cooldownSeconds = 60;
                    $latestCompletedAttempt = $attempts->first(); // sudah orderByDesc completed_at
                    $lastFailedAttempt = ($latestCompletedAttempt && !$latestCompletedAttempt->isPassed($passingPercent))
                        ? $latestCompletedAttempt
                        : null;
                    $cooldownEndsAt = null;
                    $inCooldown = false;
                    if (!$currentQuizPassed && !$ongoingAttempt && $lastFailedAttempt && $lastFailedAttempt->completed_at) {
                        $cooldownEndsAt = $lastFailedAttempt->completed_at->copy()->addSeconds($cooldownSeconds);
                        $inCooldown = $cooldownEndsAt->isFuture();
                    }
                   $remainingCooldownSec = 0;
                if ($inCooldown && $cooldownEndsAt) {
            // Hitung sisa detik murni di sisi server agar aman dari masalah zona waktu/jam PC lambat
            $remainingCooldownSec = max(0, $cooldownEndsAt->timestamp - \Carbon\Carbon::now('Asia/Jakarta')->timestamp);
            }
                @endphp
                

                <div class="box_luar_deskripsi_modul">
                    <div class="box_deskripsi_modul">
                        <h4 style="font-weight:700; margin:0 0 12px 0;">Quiz Rules</h4>
                        <p style="margin:0 0 10px 0;">This quiz is designed to measure your understanding of the {{ $beforeQuizTitle }} material.</p>
                        <p style="margin:0 0 10px 0;">Please pay attention to the following conditions before starting:</p>
                        <ol style="padding-left:18px; margin:0 0 14px 0; line-height:1.7;">
                            <li><strong>Number of Questions:</strong> {{ $questionCount }} multiple choice questions.</li>
                            <li><strong>Duration of Work:</strong> {{ $durationText }}.</li>
                            <li><strong>Passing Grade:</strong> Minimum {{ $passingPercent }}% to be declared passed.</li>
                            <li>If you have not reached the passing grade, you can retake the quiz after 1 minute.</li>
                            <li>Please make sure you answer all questions before time runs out.</li>
                        </ol>
                        <p style="margin:0;">Good luck and have fun!</p>

                        <div style="display:flex; justify-content:flex-end; margin-top:14px;">
                            @if($currentQuizPassed)
                                <button type="button" class="btn" disabled
                                    style="background:#eafff3; color:#16a34a; border-radius:999px; padding:10px 18px; font-weight:800; cursor:not-allowed;">
                                   You have passed this quiz
                                </button>
                            @elseif($ongoingAttempt)
                                <a href="{{ $continueUrl }}" class="btn" style="background:#252346; color:#f4c430; border-radius:999px; padding:10px 18px; font-weight:700;">
                                    Resume
                                    <span style="margin-left:8px;">›</span>
                                </a>
                            @elseif($inCooldown)
                                <button type="button" class="btn" id="startQuizBtn" disabled
                        style="background:#f1f5f9; color:#64748b; border-radius:999px; padding:10px 18px; font-weight:700; cursor:not-allowed;"
                        data-start-url="{{ $startUrl }}"
                        data-cooldown-remaining="{{ $remainingCooldownSec }}">
                        Wait <span id="quizCooldownTimer">...</span>
                        </button>
                            @else
                                <a href="#" id="startQuizBtn" data-start-url="{{ $startUrl }}" class="btn" style="background:#f4c430; color:#1f2937; border-radius:999px; padding:10px 18px; font-weight:700;">
                                    Start
                                    <span style="margin-left:8px;">›</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="box_luar_deskripsi_modul" style="margin-top:18px;">
                    <div class="box_deskripsi_modul">
                        <h4 style="font-weight:700; margin:0 0 14px 0;">History</h4>
                        <div style="overflow:auto;">
                            <table class="table" style="margin:0;">
                                <thead>
                                    <tr style="border-bottom:1px solid rgba(0,0,0,.08);">
                                        <th style="font-size:13px; color:#374151;">Date</th>
                                        <th style="font-size:13px; color:#374151;">Percentage</th>
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
                                            $detailsUrl = $att ? route('user.quiz.result.short', $att) : '#';
                                            $tanggalText = $att->completed_at ? $att->completed_at->copy()->subMinutes(7)->format('d M Y') . ' at ' . $att->completed_at->copy()->subMinutes(7)->format('H:i:s') : '-';
                                        @endphp
                                        <tr style="border-bottom:1px solid rgba(0,0,0,.06);">
                                            <td style="font-size:13px; color:#111827;">
                                                {{ $tanggalText }}
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
                                            <td colspan="4" class="text-muted">There is no quiz history yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
            @php
                // Find current item's index in the display list
                $activeIndexInDisplay = -1;
                 
                foreach($displayItems as $idx => $it) {
                    if (($it['key'] ?? null) === ($activeDisplay['key'] ?? null) && ($it['kind'] ?? null) === ($activeDisplay['kind'] ?? null)) {
                        $activeIndexInDisplay = $idx;
                        break;
                    }
                }
                
                $nextDisplayItem = ($activeIndexInDisplay !== -1) ? $displayItems->get($activeIndexInDisplay + 1) : null;
                $nextTargetRep = $nextDisplayItem ? ($nextDisplayItem['rep'] ?? null) : null;

                // Lock logic
                // 1. If currently on a quiz and haven't passed it yet, lock the next step.
                $lockNext = ($activeKind === 'quiz' && auth()->check() && !$currentQuizPassed);

                // 2. Handle free access (limit_2) logic for the next item
                $lockNextByFree = false;
                if (((string)($freeAccessMode ?? 'all') === 'limit_2') && $nextTargetRep) {
                    $candidateIds = [];
                    if (($nextDisplayItem['kind'] ?? '') === 'quiz') {
                        $candidateIds[] = (int) ($nextTargetRep->id ?? 0);
                    } else {
                        if (!empty($nextDisplayItem['pdf']?->id)) $candidateIds[] = (int) $nextDisplayItem['pdf']->id;
                        if (!empty($nextDisplayItem['video']?->id)) $candidateIds[] = (int) $nextDisplayItem['video']->id;
                    }

                    $allowedAny = false;
                    foreach ($candidateIds as $cid) {
                        if ($cid > 0 && in_array($cid, $freeAccessibleModuleIds, true)) { $allowedAny = true; break; }
                    }
                    if (!$allowedAny) {
                        $lockNextByFree = true;
                    }
                }

                $lockNext = $lockNext || $lockNextByFree;
            @endphp

            {{-- Tombol Next tetap seperti sebelumnya (button), tapi arahnya dinamis --}}
            <button class="next_kanan_modul" type="button"
                @if(isset($course) && $nextTargetRep && !$lockNext)
                    data-next-url="{{ route('course.learn', ['course' => $course->id, 'module' => $nextTargetRep->id]) }}"
                @elseif(isset($course) && !$nextTargetRep && !$lockNext)
                    data-next-url="{{ route('course.rating', ['course' => $course->id]) }}"
                @elseif($lockNext && $lockNextByFree)
                    data-is-locked-free="1"
                    data-buy-url="{{ route('course.payment', $course->id) }}"
                    data-course-name="{{ $course->name }}"
                @elseif($lockNext)
                    data-is-locked-quiz="1"
                @else
                    disabled style="opacity:.6; cursor:not-allowed;"
                @endif
            >
                <p>
                    @if($lockNext && $lockNextByFree)
                        Buy Course
                    @elseif($lockNext)
                        Locked
                    @elseif(!$nextTargetRep)
                        Finish & Leave a Review
                    @else
                        Next
                    @endif
                </p>
                @if($lockNext)
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="white" class="bi bi-lock-fill" viewBox="0 0 16 16" aria-hidden="true">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2z"/>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="white" class="bi-right bi-caret-right-fill" viewBox="0 0 16 16">
                        <path d="m12.14 8.753-5.482 4.796c-.646.566-1.658.106-1.658-.753V3.204a1 1 0 0 1 1.659-.753l5.48 4.796a1 1 0 0 1 0 1.506z" />
                    </svg>
                @endif
            </button>
        </div>
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
                        Swal.fire({
                            title: 'Oops!',
                            text: 'This content is locked. Please purchase or enroll in this course to unlock all materials.',
                            icon: 'warning',
                            confirmButtonColor: '#f4c430',
                        });
                    } else {
                        Swal.fire({
                            title: 'Oops!',
                            text: 'You must complete the quiz before proceeding to the next step.',
                            icon: 'warning',
                            confirmButtonColor: '#f4c430',
                        });
                    }
                    return;
                }

                if (learnBase && moduleId) {
                    window.location.href = learnBase + '?module=' + encodeURIComponent(moduleId);
                }
            });
        });

        const nextBtn = document.querySelector('.next_kanan_modul');
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                const url = nextBtn.getAttribute('data-next-url');
                const isLockedFree = nextBtn.getAttribute('data-is-locked-free') === '1';

                if (isLockedFree) {
                    const buyUrl = nextBtn.getAttribute('data-buy-url');
                    const courseName = nextBtn.getAttribute('data-course-name') || 'course';
                    
                    Swal.fire({
                        title: 'Oops!',
                        text: `Free access for \ has ended. Tap the button to purchase and enjoy full access.`,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Buy Now',
                        cancelButtonText: 'Maybe Later',
                        confirmButtonColor: '#f4c430',
                        cancelButtonColor: '#d33',
                    }).then((result) => {
                        if (result.isConfirmed && buyUrl) {
                            window.location.href = buyUrl;
                        }
                    });
                    return;
                }

                const isLockedQuiz = nextBtn.getAttribute('data-is-locked-quiz') === '1';
                if (isLockedQuiz) {
                    Swal.fire({
                        title: 'Oops!',
                        text: 'You must complete the quiz before proceeding to the next step.',
                        icon: 'warning',
                        confirmButtonColor: '#f4c430',
                    });
                    return;
                }

                if (url) {
                    window.location.href = url;
                }
            });
        }

        document.querySelectorAll('video.video_course').forEach((v) => {
            try { v.load(); } catch (e) {}
        });

        // Mark video module complete when video finishes playing
        (function () {
            const completeUrlBase = "{{ isset($course) ? url('/courses/' . $course->id . '/modules') : '' }}";
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            // Use the actual video module ID (not the representative display ID)
            const videoModuleId = {{ !empty($videoModule?->id) ? (int)$videoModule->id : ($activeDisplayModuleId ?? 'null') }};

            if (!completeUrlBase || !csrfToken || !videoModuleId) return;
            console.log('[video-complete] ready, moduleId=', videoModuleId, 'url=', completeUrlBase + '/' + videoModuleId + '/complete');

            document.querySelectorAll('video.video_course').forEach((v) => {
                // Mark video_watched=true when user clicks play
                v.addEventListener('play', async function onFirstPlay() {
                    v.removeEventListener('play', onFirstPlay); // only once
                    try {
                        await fetch(`${completeUrlBase}/${videoModuleId}/watching`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        });
                    } catch (e) { console.error('[video-watching] error:', e); }
                });

                // Mark completed=true when video ends
                v.addEventListener('ended', async function () {
                    try {
                        const resp = await fetch(`${completeUrlBase}/${videoModuleId}/complete`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                            },
                        });
                        if (!resp.ok) {
                            console.error('[video-complete] HTTP', resp.status, await resp.text());
                            return;
                        }
                        // Update the accordion checkmark without reload
                        const btn = document.querySelector(`.accordion-header[data-module-id="${videoModuleId}"]`);
                        if (btn) {
                            const iconSlot = btn.querySelector('span[style*="width:20px"]');
                            if (iconSlot && !iconSlot.querySelector('.bi-check-circle-fill')) {
                                iconSlot.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="#16a34a" class="bi bi-check-circle-fill" viewBox="0 0 16 16"><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/></svg>`;
                            }
                        }
                    } catch (e) {
                        console.error('[video-complete] fetch error:', e);
                    }
                });
            });
        })();
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('error'))
                Swal.fire({
                    title: 'Oops!',
                    text: '{{ session('error') }}',
                    icon: 'warning',
                    confirmButtonColor: '#f4c430',
                });
            @endif
            @if(session('success'))
                Swal.fire({
                    title: 'Success',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonColor: '#16a34a',
                });
            @endif
        });
    </script>

    <!-- Quiz Start Confirmation Modal -->
    <div id="quizStartModal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.45); align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:20px; padding:36px 32px; max-width:380px; width:90%; text-align:center; box-shadow:0 20px 60px rgba(0,0,0,0.2); animation:quizModalIn .2s ease;">
            <div style="font-size:48px; margin-bottom:12px;">🎯</div>
            <h3 style="font-weight:800; font-size:20px; color:#1f2937; margin:0 0 10px 0;">Good luck with your work!</h3>
            <p style="color:#6b7280; font-size:14px; margin:0 0 24px 0;">Good luck and get your best grades! 💪</p>
            <div style="display:flex; gap:10px; justify-content:center;">
                <button id="quizStartCancelBtn" type="button"
                    style="flex:1; padding:10px 0; border-radius:999px; border:1.5px solid #e5e7eb; background:#fff; color:#374151; font-weight:600; font-size:14px; cursor:pointer;">
                    Cancelled
                </button>
                <a id="quizStartConfirmBtn" href="#"
                    style="flex:1; padding:10px 0; border-radius:999px; background:#f4c430; color:#1f2937; font-weight:700; font-size:14px; text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">
                    Start Quiz
                </a>
            </div>
        </div>
    </div>
    <style>
        @keyframes quizModalIn {
            from { transform: scale(.92); opacity: 0; }
            to   { transform: scale(1);  opacity: 1; }
        }
    </style>
    <script>
        (function() {
            const startBtn = document.getElementById('startQuizBtn');
            const modal = document.getElementById('quizStartModal');
            const cancelBtn = document.getElementById('quizStartCancelBtn');
            const confirmBtn = document.getElementById('quizStartConfirmBtn');

            // Cooldown countdown timer (1 menit)
           // Cooldown countdown timer (1 menit)
const cooldownTimerEl = document.getElementById('quizCooldownTimer');
if (cooldownTimerEl && startBtn && startBtn.hasAttribute('data-cooldown-remaining')) {
    let remaining = parseInt(startBtn.getAttribute('data-cooldown-remaining')) || 0;
    let cooldownDone = false;
    
    function tickCooldown() {
        if (cooldownDone) return;
        
        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        cooldownTimerEl.textContent = String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0');
        
        if (remaining <= 0) {
            cooldownDone = true;
            clearInterval(cooldownInterval);
            startBtn.disabled = false;
            startBtn.style.background = '#f4c430';
            startBtn.style.color = '#1f2937';
            startBtn.style.cursor = 'pointer';
            startBtn.innerHTML = 'Start <span style="margin-left:8px;">›</span>';
            startBtn.addEventListener('click', openQuizModal);
        }
        remaining--; // kurangi 1 detik setiap interval berjalan
    }
    
    const cooldownInterval = setInterval(tickCooldown, 1000);
    tickCooldown(); // panggil sekali untuk inisialisasi awal
}

            function openQuizModal(e) {
                e.preventDefault();
                const url = startBtn.getAttribute('data-start-url') || '#';
                confirmBtn.href = url;
                modal.style.display = 'flex';
            }

            if (!startBtn || !modal) return;
            if (!startBtn.disabled) {
                startBtn.addEventListener('click', openQuizModal);
            }

            cancelBtn.addEventListener('click', function() {
                modal.style.display = 'none';
            });

            modal.addEventListener('click', function(e) {
                if (e.target === modal) modal.style.display = 'none';
            });
        })();
    </script>
</body>

</html>

