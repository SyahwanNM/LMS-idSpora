@extends('layouts.trainer')

@section('title', 'Course Detail - Trainer')

@php
  $pageTitle = 'Course Detail';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Courses', 'url' => route('trainer.courses')],
    ['label' => 'Detail']
  ];
@endphp

@push('styles')
  @vite(['resources/css/trainer/detail-course.css'])
@endpush

@extends('layouts.trainer')

@section('title', 'Course Detail - Trainer')

@php
  $pageTitle = 'Course Detail';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Courses', 'url' => route('trainer.courses')],
    ['label' => 'Detail']
  ];
@endphp

@push('styles')
  @vite(['resources/css/trainer/detail-course.css'])
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css" />
  {{-- Semua style CSS spesifik halaman ini sudah ada di dalam detail-course.css yang Anda buat sebelumnya --}}
@endpush

@section('content')
  <main class="detail-course">
    {{-- HERO SECTION --}}
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
            {{ Str::limit($course->description, 120) }}
          </p>
          <div class="hero-stats">
            <div class="stat-chip">
              <i class="bi bi-people"></i>
              <div>
                <p class="stat-label">ENROLLMENT</p>
                <p class="stat-value">{{ number_format($enrollmentCount) }} Learners</p>
              </div>
            </div>
            <div class="stat-chip">
              <i class="bi bi-folder"></i>
              <div>
                <p class="stat-label">STRUCTURE</p>
                <p class="stat-value">{{ $moduleCount }} Active Assets</p>
              </div>
            </div>
            <div class="stat-chip">
              <i class="bi bi-star"></i>
              <div>
                <p class="stat-label">RATING</p>
                <p class="stat-value">{{ $averageRating }} / 5.0</p>
              </div>
            </div>
          </div>
        </div>
        <div class="hero-media">
          <div class="hero-image-wrap">
            @if($course->card_thumbnail)
                <img src="{{ asset('storage/' . $course->card_thumbnail) }}" alt="{{ $course->name }}" />
            @else
                <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?w=600&h=360&fit=crop" alt="Default Thumbnail" />
            @endif
          </div>
        </div>
      </div>
    </section>

    {{-- TABS NAVIGATION --}}
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
          <p>ACADEMIC PATH</p>
          <div style="display: flex; gap: var(--spacing-sm); align-items: center;">
            <a href="{{ route('trainer.courses.studio', $course->id) }}" class="btn-propose"
              style="text-decoration: none; color: var(--main-navy-clr); display: inline-flex; align-items: center; gap: var(--spacing-sm);">
              <i class="bi bi-file-earmark-arrow-up"></i> UPLOAD MATERIALS
            </a>
            <a href="{{ route('trainer.courses.studio', $course->id) }}" class="btn-propose" style="text-decoration:none;">
              <i class="bi bi-plus"></i> NEW SECTION
            </a>
          </div>
        </div>

        @php 
            $chapterCount = 0; 
            $hasModules = $course->modules->count() > 0;
        @endphp

        @if($hasModules)
            @forelse($course->modules as $module)
                
                {{-- Jika module ini adalah sebuah "Bab" (Section/Chapter) --}}
                @if($module->type === 'section' || $module->type === 'chapter')
                    @php $chapterCount++; @endphp
                    
                    {{-- Tutup container Bab sebelumnya JIKA ini BUKAN bab pertama --}}
                    @if($chapterCount > 1)
                            </div> {{-- Tutup .unit-assets --}}
                        </div> {{-- Tutup .unit-card --}}
                    @endif

                    {{-- Buka Container Bab Baru --}}
                    <div class="unit-card {{ $chapterCount > 1 ? 'compact' : '' }}">
                        <div class="unit-top">
                            <div class="unit-index">
                                {{ str_pad($chapterCount, 2, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="unit-title">
                                <h3>{{ $module->title }}</h3>
                                <div class="unit-meta">
                                    <span><i class="bi bi-folder"></i> LEARNING PATH</span>
                                </div>
                            </div>
                            <button class="unit-toggle" type="button">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                        
                        {{-- Buka kontainer untuk Anak-anak modul di dalam bab ini --}}
                        <div class="unit-assets">
                
                {{-- Jika ini adalah Modul Biasa (Video/PDF/Quiz) --}}
                @else
                    {{-- Jika ada modul yang tidak masuk ke dalam section apapun (orphan), buatkan section default secara otomatis --}}
                    @if($chapterCount === 0)
                        @php $chapterCount++; @endphp
                        <div class="unit-card">
                            <div class="unit-top">
                                <div class="unit-index">01</div>
                                <div class="unit-title">
                                    <h3>General Materials</h3>
                                    <div class="unit-meta">
                                        <span><i class="bi bi-folder"></i> LEARNING PATH</span>
                                    </div>
                                </div>
                                <button class="unit-toggle" type="button">
                                    <i class="bi bi-chevron-down"></i>
                                </button>
                            </div>
                            <div class="unit-assets">
                    @endif

                    {{-- Render Asset/Isi Modul --}}
                    <div class="asset-mini" onclick="window.open('{{ $module->content_url ?? asset('storage/' . $module->file_name) }}', '_blank')">
                        <i class="bi {{ $module->type == 'video' ? 'bi-film' : ($module->type == 'quiz' ? 'bi-check-circle' : 'bi-file-earmark-pdf') }}"></i>
                        <div>
                            <h4>{{ Str::limit($module->title, 25) }}</h4>
                            <p>{{ strtoupper($module->type) }} ASSET</p>
                        </div>
                    </div>
                @endif

                {{-- Jika ini adalah iterasi TERAKHIR, pastikan div ditutup dengan benar --}}
                @if($loop->last && $chapterCount > 0)
                        </div> {{-- Tutup .unit-assets terakhir --}}
                    </div> {{-- Tutup .unit-card terakhir --}}
                @endif

            @empty
                @endforelse
        @else
            {{-- Tampilan Jika Course Kosong --}}
            <div style="text-align: center; padding: 40px; background: white; border-radius: 16px; border: 1px dashed var(--line-clr);">
                <p style="color: var(--gray-second-clr); font-weight:600; margin:0;">Belum ada silabus / modul untuk kelas ini.</p>
                <a href="{{ route('trainer.content-studio', ['courseId' => $course->id]) }}" style="color: var(--main-navy-clr); font-size: 14px; text-decoration:underline; display:inline-block; margin-top: 10px;">
                Mulai Susun Silabus
                </a>
            </div>
        @endif
      </section>

      <section id="quiz-recap" class="tab-content">
        <div class="recap-stats">
          <div class="stat-box">
            <p class="stat-box-label">CLASS AVERAGE</p>
            <div class="stat-box-content">
              <h2>{{ $classAverage ?? '--' }}%</h2>
              <div class="stat-box-icon green">
                <i class="bi bi-graph-up-arrow"></i>
              </div>
            </div>
            <p class="stat-box-sub">
              <i class="bi bi-check-circle-fill"></i> AUTOMATED CALCULATION
            </p>
          </div>
          <div class="stat-box">
            <p class="stat-box-label">TOTAL SUBMISSIONS</p>
            <div class="stat-box-content">
              <h2>{{ $totalSubmissions ?? 0 }}</h2>
              <div class="stat-box-icon purple">
                <i class="bi bi-bar-chart-line"></i>
              </div>
            </div>
            <p class="stat-box-sub">
              <i class="bi bi-dot"></i> LIVE FEED REGISTRY
            </p>
          </div>
        </div>

        <div class="grading-registry">
          <div class="registry-header">
            <h3>AUTOMATIC GRADING REGISTRY</h3>
            <button class="export-btn" type="button" onclick="alert('Exporting Ledger to CSV...')">
              <i class="bi bi-download"></i> EXPORT LEDGER
            </button>
          </div>
          <div class="registry-table">
            <div class="table-header">
              <div class="col-learner">LEARNER</div>
              <div class="col-submission">SUBMISSION DATE</div>
              <div class="col-score">SYSTEM SCORE</div>
              <div class="col-certificate">STATUS</div>
            </div>
            
            {{-- Looping Data Nilai Kuis Siswa --}}
            @if(isset($quizAttempts) && $quizAttempts->count() > 0)
                @foreach($quizAttempts as $attempt)
                <div class="table-row">
                    <div class="col-learner">
                    <img src="{{ $attempt->user->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($attempt->user->name ?? 'User') }}" alt="{{ $attempt->user->name }}" />
                    <div>
                        <h4>{{ Str::limit($attempt->user->name ?? 'Anonim', 15) }}</h4>
                        <p>Modul: {{ Str::limit($attempt->courseModule->title ?? 'Unknown', 15) }}</p>
                    </div>
                    </div>
                    <div class="col-submission">
                    <p>{{ $attempt->completed_at ? $attempt->completed_at->format('Y-m-d') : 'In Progress' }}</p>
                    <span>{{ $attempt->completed_at ? $attempt->completed_at->format('H:i') : '--:--' }}</span>
                    </div>
                    <div class="col-score">
                    <span class="score-bullet {{ $attempt->percentage >= 75 ? 'green' : 'orange' }}"></span>
                    <strong>{{ $attempt->percentage }}%</strong>
                    </div>
                    <div class="col-certificate">
                    @if($attempt->percentage >= 75)
                        <span class="badge-issued"><i class="bi bi-check-circle"></i> PASSED</span>
                    @else
                        <span class="badge-pending">FAILED</span>
                    @endif
                    </div>
                </div>
                @endforeach
            @else
                <div style="padding: 30px; text-align: center; color: var(--gray-second-clr); font-weight: 600; border-bottom: 1px solid var(--line-clr);">
                    Belum ada data pengerjaan kuis dari siswa.
                </div>
            @endif
          </div>
        </div>
      </section>

      <section id="enrollment" class="tab-content">
        <div class="enrollment-header">
          <h3>ENROLLED LEARNERS</h3>
          <span class="total-badge">{{ $enrollmentCount }} TOTAL</span>
        </div>
        <div class="learner-grid">
          @forelse($activeStudents as $enrollment)
            <div class="learner-card">
              <img src="{{ $enrollment->student->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode($enrollment->student->name) }}" alt="{{ $enrollment->student->name }}" />
              <div class="learner-info">
                <h4>{{ $enrollment->student->name ?? 'Anonim' }}</h4>
                <p>{{ strtoupper($enrollment->student->email ?? 'NO EMAIL') }}</p>
                <span class="learner-date">Joined: {{ $enrollment->created_at->format('Y-m-d') }}</span>
              </div>
            </div>
          @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 16px; border: 1px solid var(--line-clr);">
              <p style="color: var(--gray-second-clr); font-weight:600; margin:0;">Belum ada siswa yang mendaftar di kelas ini.</p>
            </div>
          @endforelse
        </div>
      </section>

      <aside class="course-right">
        <div class="grading-card">
          <div class="grading-head">
            <i class="bi bi-lightning-fill grading-icon"></i>
            <p>GRADING PROTOCOL</p>
          </div>
          <div class="grading-status">
            <p>Oversight Status</p>
            <h4>Active Automated</h4>
          </div>
          <ul class="grading-notes">
            <li>System automatically calculates percentage scores.</li>
            <li>Manual overrides are disabled for audit compliance.</li>
          </ul>
          <button class="grading-btn" type="button">View Audit Ledger</button>
        </div>

        <div class="instructor-card">
          <p class="instructor-title">INSTRUCTOR HUB</p>
          <div class="instructor-item">
            <span class="dot"></span>
            <div>
              <h4>Submit Assets</h4>
              <p>Pedagogical Materials</p>
            </div>
          </div>
        </div>
      </aside>
    </div>
  </main>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      
      // 1. Tab Switching Logic
      const tabButtons = document.querySelectorAll(".tab-pill");
      const tabContents = document.querySelectorAll(".tab-content");

      tabButtons.forEach((button) => {
        button.addEventListener("click", () => {
          tabButtons.forEach((btn) => btn.classList.remove("active"));
          tabContents.forEach((content) => content.classList.remove("active"));

          button.classList.add("active");
          const targetId = button.getAttribute("data-target");
          document.getElementById(targetId).classList.add("active");
        });
      });

      // 2. Unit Card Expand/Collapse Logic
      const unitCards = document.querySelectorAll(".unit-card");
      const unitToggles = document.querySelectorAll(".unit-toggle");

      const toggleUnitCard = (unitCard) => {
        const unitAssets = unitCard.querySelector(".unit-assets");
        const icon = unitCard.querySelector(".unit-toggle i");

        if (unitAssets && !unitCard.classList.contains("compact")) {
          unitCard.classList.toggle("collapsed");

          if (unitCard.classList.contains("collapsed")) {
            unitAssets.style.display = "none";
            icon.style.transform = "rotate(0deg)";
          } else {
            unitAssets.style.display = "grid";
            icon.style.transform = "rotate(180deg)";
          }
        } else {
          unitCard.classList.toggle("expanded");

          if (unitCard.classList.contains("expanded")) {
            if (unitAssets) {
               unitAssets.style.display = "grid";
            }
            icon.style.transform = "rotate(180deg)";
          } else {
            if (unitAssets) {
               unitAssets.style.display = "none";
            }
            icon.style.transform = "rotate(0deg)";
          }
        }
      };

      // Listener untuk Tombol Chevron (Panah Bawah/Atas)
      unitToggles.forEach((toggle) => {
        toggle.addEventListener("click", (event) => {
          event.stopPropagation(); // Mencegah trigger ganda dari unitCard click
          const unitCard = toggle.closest(".unit-card");
          toggleUnitCard(unitCard);
        });
      });

      // Listener klik pada area Card (Kecuali jika yang diklik adalah link/tombol/asset di dalamnya)
      unitCards.forEach((unitCard) => {
        unitCard.addEventListener("click", (event) => {
          // Hanya bereaksi jika klik berada di area header unit (.unit-top)
          if (event.target.closest(".unit-top") && !event.target.closest(".unit-toggle")) {
             toggleUnitCard(unitCard);
          }
        });
      });
      
      // Initial Setup: Sembunyikan isi modul yang class-nya 'compact' atau 'collapsed'
      unitCards.forEach((card) => {
          const assets = card.querySelector(".unit-assets");
          if(assets && (card.classList.contains('compact') || card.classList.contains('collapsed'))) {
              assets.style.display = 'none';
          }
      });

    });
  </script>
@endsection