@extends('layouts.trainer')

@section('title', 'Course Detail - Trainer')

@php
    $pageTitle = 'Course Detail';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Courses', 'url' => route('trainer.courses')],
        ['label' => 'Detail']
    ];

    $courseModules = $course->modules ?? collect();
    $moduleChunks = $courseModules->values()->chunk(3);
@endphp

@push('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css" />

@endpush

@section('content')
    <main class="detail-course">
        <section class="course-hero">
            <div class="hero-head">
                <a href="{{ route('trainer.courses') }}" class="hero-back" style="text-decoration:none;">
                    <i class="bi bi-chevron-left back-icon"></i>
                    REPOSITORY LEDGER
                </a>

                <div class="hero-badges">
                    <span class="hero-pill-accent">{{ strtoupper($course->level ?? 'GENERAL') }} TIER</span>
                    <span class="hero-pill-outline">CUR-ID: C{{ $course->id }}</span>
                </div>
            </div>

            <div class="hero-body">
                <div class="hero-copy">
                    <p class="hero-kicker">
                        <i class="bi bi-star-fill kicker-icon"></i>ACADEMIC CURRICULUM • DETAIL
                    </p>
                    <h1>{{ Str::limit($course->name, 50) }}</h1>
                    <p class="text-white opacity-75 mt-2 mb-4" style="font-size: 14px; line-height: 1.6;">
                        {{ Str::limit($course->description ?? 'Deskripsi tidak tersedia.', 120) }}
                    </p>
                    <div class="hero-stats">
                        <div class="stat-chip">
                            <i class="bi bi-people"></i>
                            <div>
                                <p class="stat-label">ENROLLMENT</p>
                                <p class="stat-value">{{ number_format($enrollmentCount ?? 0) }} Learners</p>
                            </div>
                        </div>
                        <div class="stat-chip">
                            <i class="bi bi-folder"></i>
                            <div>
                                <p class="stat-label">STRUCTURE</p>
                                <p class="stat-value">{{ $moduleChunks->count() }} Academic Units</p>
                            </div>
                        </div>
                        <div class="stat-chip">
                            <i class="bi bi-star"></i>
                            <div>
                                <p class="stat-label">RATING</p>
                                <p class="stat-value">{{ $averageRating ?? '0.0' }} / 5.0</p>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="hero-media">
                    <div class="hero-image-wrap">
                        @if($course->card_thumbnail)
                            @php
                                $rawThumb = str_replace('\\', '/', trim((string) $course->card_thumbnail));
                                $thumbUrl = null;

                                if (str_starts_with($rawThumb, 'http://') || str_starts_with($rawThumb, 'https://')) {
                                    $thumbUrl = $rawThumb;
                                } elseif (str_starts_with($rawThumb, 'uploads/')) {
                                    $thumbUrl = asset($rawThumb);
                                } else {
                                    $rel = $rawThumb;
                                    $markerPos = stripos($rel, 'storage/app/public/');
                                    if ($markerPos !== false) {
                                        $rel = substr($rel, $markerPos + strlen('storage/app/public/'));
                                    }
                                    if (str_starts_with($rel, 'storage/')) {
                                        $rel = ltrim(substr($rel, 8), '/');
                                    }
                                    if (str_starts_with($rel, 'public/')) {
                                        $rel = ltrim(substr($rel, 7), '/');
                                    }
                                    $rel = ltrim($rel, '/');
                                    if ($rel !== '' && !str_contains($rel, '/')) {
                                        $rel = 'courses/card_thumbnails/' . $rel;
                                    }
                                    $thumbUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($rel);
                                }
                            @endphp
                            <img src="{{ $thumbUrl }}" alt="{{ $course->name }}" />
                        @else
                            <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=360&fit=crop"
                                alt="Default Thumbnail" />
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <div class="course-tabs">
            <button class="tab-pill active" type="button" data-target="curriculum-map">
                <i class="bi bi-clipboard-check"></i>
                <span>Curriculum Map</span>
            </button>
            <button class="tab-pill" type="button" data-target="quiz-recap">
                <i class="bi bi-file-earmark-text"></i>
                <span>Quiz Recap</span>
            </button>
            <button class="tab-pill" type="button" data-target="enrollment">
                <i class="bi bi-people"></i>
                <span>Enrollment</span>
            </button>
        </div>

        <div class="course-layout">
            <section id="curriculum-map" class="tab-content active">
                <div class="unit-header">
                    <p>ACADEMIC UNITS (ADMIN MANAGED)</p>
                </div>

                @if($moduleChunks->count() > 0)
                    @foreach($moduleChunks as $idx => $chunk)
                        <div class="unit-card {{ $idx > 0 ? 'compact' : '' }}">
                            <div class="unit-top">
                                <div class="unit-index {{ $idx > 0 ? 'muted' : '' }}">{{ str_pad($idx + 1, 2, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="unit-title">
                                    <h3>Academic Unit: Module {{ $idx + 1 }}</h3>
                                    <div class="unit-meta">
                                        <span><i class="bi bi-folder"></i> {{ $chunk->count() }} OPERATIONAL ASSETS</span>
                                    </div>
                                </div>
                                <button class="unit-toggle" type="button"><i class="bi bi-chevron-down"></i></button>
                            </div>

                            <div class="unit-assets" style="{{ $idx > 0 ? 'display:none;' : '' }}">
                                @foreach($chunk as $module)
                                    @php
                                        $icon = $module->type === 'video' ? 'bi-film' : ($module->type === 'quiz' ? 'bi-check-circle' : 'bi-file-earmark-pdf');
                                        $label = $module->type === 'video' ? 'Video Asset' : ($module->type === 'quiz' ? 'Quiz Engine' : 'PDF Material');
                                        $assetTab = $module->type === 'quiz' ? 'quiz' : ($module->type === 'video' ? 'video' : 'module');

                                      @endphp
                                    <div class="asset-mini"
                                        data-redirect="{{ route('trainer.courses.studio', $course->id) }}?unit={{ $idx }}&tab={{ $assetTab }}">
                                        <i class="bi {{ $icon }}"></i>
                                        <div>
                                            <h4>{{ Str::limit($module->title, 25) }}</h4>
                                            <p>{{ $label }}</p>

                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <div
                        style="text-align: center; padding: 40px; background: white; border-radius: 16px; border: 1px dashed var(--line-clr);">
                        <p style="color: var(--gray-second-clr); font-weight:600; margin:0;">Silabus/modul belum disusun oleh
                            Admin Course.</p>
                        <p style="color: var(--gray-second-clr); font-size: 14px; margin: 10px 0 0 0;">Trainer hanya dapat
                            upload materi setelah struktur modul tersedia.</p>
                    </div>
                @endif
            </section>

            <section id="quiz-recap" class="tab-content">
                <div class="recap-stats">
                    <div class="stat-box">
                        <p class="stat-box-label">CLASS AVERAGE</p>
                        <div class="stat-box-content">
                            <h2>{{ $classAverage ?? '--' }}%</h2>
                            <div class="stat-box-icon green"><i class="bi bi-graph-up-arrow"></i></div>
                        </div>
                        <p class="stat-box-sub"><i class="bi bi-check-circle-fill"></i> AUTOMATED CALCULATION</p>
                    </div>
                    <div class="stat-box">
                        <p class="stat-box-label">TOTAL SUBMISSIONS</p>
                        <div class="stat-box-content">
                            <h2>{{ $totalSubmissions ?? 0 }}</h2>
                            <div class="stat-box-icon purple"><i class="bi bi-bar-chart-line"></i></div>
                        </div>
                        <p class="stat-box-sub"><i class="bi bi-dot"></i> LIVE FEED REGISTRY</p>
                    </div>
                </div>

                <div class="grading-registry">
                    <div class="registry-header">
                        <h3>AUTOMATIC GRADING REGISTRY</h3>
                        <button class="export-btn" type="button"><i class="bi bi-download"></i> EXPORT LEDGER</button>
                    </div>
                    <div class="registry-table">
                        <div class="table-header">
                            <div class="col-learner">LEARNER</div>
                            <div class="col-submission">SUBMISSION DATE</div>
                            <div class="col-score">SYSTEM SCORE</div>
                            <div class="col-certificate">STATUS</div>
                        </div>

                        @if(isset($quizAttempts) && $quizAttempts->count() > 0)
                            @foreach($quizAttempts as $attempt)
                                <div class="table-row">
                                    <div class="col-learner">
                                        <img src="{{ $attempt->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($attempt->user->name ?? 'User') }}"
                                            alt="{{ $attempt->user->name ?? 'User' }}" />
                                        <div>
                                            <h4>{{ Str::limit($attempt->user->name ?? 'Anonim', 15) }}</h4>
                                            <p>Modul: {{ Str::limit($attempt->courseModule->title ?? 'Unknown', 15) }}</p>
                                        </div>
                                    </div>
                                    <div class="col-submission">
                                        <p>{{ $attempt->completed_at ? $attempt->completed_at->format('Y-m-d') : 'In Progress' }}
                                        </p>
                                        <span>{{ $attempt->completed_at ? $attempt->completed_at->format('H:i') : '--:--' }}</span>
                                    </div>
                                    <div class="col-score">
                                        <span
                                            class="score-bullet {{ ($attempt->percentage ?? 0) >= 75 ? 'green' : 'orange' }}"></span>
                                        <strong>{{ $attempt->percentage ?? 0 }}%</strong>
                                    </div>
                                    <div class="col-certificate">
                                        @if(($attempt->percentage ?? 0) >= 75)
                                            <span class="badge-issued"><i class="bi bi-check-circle"></i> PASSED</span>
                                        @else
                                            <span class="badge-pending">FAILED</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div
                                style="padding: 30px; text-align: center; color: var(--gray-second-clr); font-weight: 600; border-bottom: 1px solid var(--line-clr);">
                                Belum ada data pengerjaan kuis dari siswa.</div>
                        @endif
                    </div>
                </div>
            </section>

            <section id="enrollment" class="tab-content">
                <div class="enrollment-header">
                    <h3>ENROLLED LEARNERS</h3>
                    <span class="total-badge">{{ $enrollmentCount ?? 0 }} TOTAL</span>
                </div>
                <div class="learner-grid">
                    @forelse($activeStudents as $enrollment)
                        <div class="learner-card">
                            <img src="{{ $enrollment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($enrollment->user->name) }}"
                                alt="{{ $enrollment->user->name }}" />
                            <div class="learner-info">
                                <h4>{{ $enrollment->user->name ?? 'Anonim' }}</h4>
                                <p>{{ strtoupper($enrollment->user->email ?? 'NO EMAIL') }}</p>
                                <span class="learner-date">Joined: {{ $enrollment->created_at->format('Y-m-d') }}</span>
                            </div>
                        </div>
                    @empty
                        <div
                            style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 16px; border: 1px solid var(--line-clr);">
                            <p style="color: var(--gray-second-clr); font-weight:600; margin:0;">Belum ada siswa yang mendaftar
                                di kelas ini.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tabButtons = document.querySelectorAll(".tab-pill");
            const tabContents = document.querySelectorAll(".tab-content");

            tabButtons.forEach((button) => {
                button.addEventListener("click", () => {
                    tabButtons.forEach((btn) => btn.classList.remove("active"));
                    tabContents.forEach((content) => content.classList.remove("active"));

                    button.classList.add("active");
                    const targetId = button.getAttribute("data-target");
                    const target = document.getElementById(targetId);
                    if (target) target.classList.add("active");
                });
            });

            const unitCards = document.querySelectorAll(".unit-card");
            unitCards.forEach((unitCard) => {
                const toggleBtn = unitCard.querySelector(".unit-toggle");
                const topArea = unitCard.querySelector(".unit-top");
                const content = unitCard.querySelector(".unit-assets");

                const toggleCard = () => {
                    unitCard.classList.toggle("collapsed");
                    if (content) {
                        content.style.display = unitCard.classList.contains("collapsed") ? "none" : "grid";
                    }
                };

                if (toggleBtn) {
                    toggleBtn.addEventListener("click", (e) => {
                        e.stopPropagation();
                        toggleCard();
                    });
                }

                if (topArea) {
                    topArea.addEventListener("click", toggleCard);
                }
            });

            document.addEventListener("click", (event) => {
                const assetCard = event.target.closest(".asset-mini[data-redirect]");
                if (!assetCard) return;

                const targetPath = assetCard.getAttribute("data-redirect");
                if (targetPath) {
                    window.location.href = targetPath;
                }
            });
        });
    </script>
@endpush