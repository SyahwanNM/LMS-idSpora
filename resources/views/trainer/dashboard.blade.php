@extends('layouts.trainer')

@section('title', 'Dashboard Trainer')

@php
  $pageTitle = 'Dashboard';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Dashboard']
  ];

  $trainer = Auth::user();
  $lateUploads = (int) data_get($trainerActivity, 'late_uploads', 0);
  $trainerStatus = (string) ($trainer->user_status ?? 'active');
  $trainerStatusLabel = match ($trainerStatus) {
    'inactive' => 'Sedang Cuti / Pasif',
    'suspended' => 'Dibekukan',
    default => 'Siap Mengajar',
  };
  $trainerStatusTone = match ($trainerStatus) {
    'inactive' => 'is-passive',
    'suspended' => 'is-suspended',
    default => 'is-active',
  };
  $lateBanner = match ($lateUploads) {
    1 => '⚠️ Peringatan: Anda memiliki 1x riwayat keterlambatan. Harap tepat waktu di kelas berikutnya agar rapor kembali bersih.',
    2 => '🚨 PERINGATAN KERAS: Anda telah 2x beruntun terlambat. 1x keterlambatan lagi akan membuat akun Anda dibekukan!',
    default => null,
  };
  $lateBannerClass = $lateUploads === 1 ? 'level-1' : ($lateUploads === 2 ? 'level-2' : '');
  $pendingInvitationItems = collect($pendingInvitationItems ?? [])->values();
  $activeAssignmentItems = collect($activeAssignmentItems ?? [])->values();
  $revenueCourseItems = collect($revenueCourseItems ?? [])->values();
  $completedCourseItems = collect($completedCourseItems ?? [])->values();
  $feedbackItems = collect($feedbackItems ?? [])->values();
  $walletBalance = (float) ($trainer->wallet_balance ?? 0);
  $totalCompletedCourses = (int) data_get($trainerActivity, 'total_courses_completed', 0);
  $averageRating = (float) data_get($trainerActivity, 'average_rating', 0);

  // 1. Calculate total reviews/feedbacks for the rating card
  $totalRatings = \App\Models\Review::whereHas('course', function ($q) use ($trainer) {
    $q->where('trainer_id', $trainer->id);
  })->count() + \App\Models\Feedback::whereHas('event', function ($q) use ($trainer) {
    $q->where('trainer_id', $trainer->id);
  })->count();

  // 2. Rating ratingBadge
  $ratingBadge = 'Cukup';
  if ($averageRating >= 4.5) {
    $ratingBadge = 'Sangat Baik';
  } elseif ($averageRating >= 4.0) {
    $ratingBadge = 'Baik';
  }

  // 3. Compute greeting based on local time hour
  $hour = now()->hour;
  if ($hour >= 5 && $hour < 11) {
    $greeting = 'Selamat pagi';
  } elseif ($hour >= 11 && $hour < 15) {
    $greeting = 'Selamat siang';
  } elseif ($hour >= 15 && $hour < 18) {
    $greeting = 'Selamat sore';
  } else {
    $greeting = 'Selamat malam';
  }

  // 4. Calculate "Kegiatan Berjalan" items (combined Courses + Events)
  $kegiatanBerjalan = [];

  // Courses
  foreach ($priorityCourses as $course) {
    $course->loadCount([
      'modules',
      'modules as ready_modules_count' => function ($q) {
        $q->where('processing_status', 'ready_for_publish');
      }
    ]);
    $courseModulesCount = $course->modules_count;
    $readyModulesCount = $course->ready_modules_count;
    $progressPercent = $courseModulesCount > 0 ? round(($readyModulesCount / $courseModulesCount) * 100) : 0;

    $kegiatanBerjalan[] = [
      'type' => 'course',
      'badge_label' => 'COURSE',
      'badge_class' => 'type-course',
      'title' => $course->name,
      'progress' => $progressPercent,
      'progress_label' => 'Progres Materi',
      'count_label' => 'Siswa',
      'count_value' => $course->enrollments_count,
      'date_label' => 'Disetujui',
      'date_value' => $course->approved_at ? $course->approved_at->format('d M Y') : '-',
      'url' => route('trainer.courses.studio', $course->id),
      'icon_class' => 'bi-book-half',
      'icon_style' => 'course-style',
      'gradient_class' => 'green-gradient',
      'track_class' => 'track-green',
      'fill_class' => 'fill-green'
    ];
  }

  // Events
  $evtIndex = 0;
  foreach ($activeAssignmentItems as $row) {
    $assignment = $row['assignment'];
    $event = $assignment->event;

    // select gradient based on title matching mock
    $firstTitle = strtolower($row['event_title']);
    if (str_contains($firstTitle, 'kita')) {
      $gradient = 'green-gradient';
      $track = 'track-green';
      $fill = 'fill-green';
    } elseif (str_contains($firstTitle, 'apa ya')) {
      $gradient = 'purple-gradient';
      $track = 'track-purple';
      $fill = 'fill-purple';
    } elseif (str_contains($firstTitle, 'janggal')) {
      $gradient = 'orange-gradient';
      $track = 'track-orange';
      $fill = 'fill-orange';
    } else {
      $gradients = [
        ['purple-gradient', 'track-purple', 'fill-purple'],
        ['green-gradient', 'track-green', 'fill-green'],
        ['orange-gradient', 'track-orange', 'fill-orange']
      ];
      list($gradient, $track, $fill) = $gradients[$evtIndex % 3];
    }
    $evtIndex++;

    $kegiatanBerjalan[] = [
      'type' => 'event',
      'badge_label' => 'EVENT',
      'badge_class' => 'type-event',
      'title' => $row['event_title'],
      'progress' => $row['scheme_percent'],
      'progress_label' => 'Progres Dokumen',
      'count_label' => 'Peserta',
      'count_value' => $row['active_participants_count'],
      'date_label' => 'Tanggal Kelas',
      'date_value' => $row['event_date'] ?: 'Jadwal menyusul',
      'url' => $event ? route('trainer.events.studio', $event->id) : route('trainer.events'),
      'icon_class' => 'bi-calendar-event',
      'icon_style' => 'event-style',
      'gradient_class' => $gradient,
      'track_class' => $track,
      'fill_class' => $fill
    ];
  }

  // 5. Calculate "Tugas & Kewajiban"
  $tugasItems = [];

  // From event assignments needing upload/revision
  foreach ($activeAssignmentItems as $row) {
    $assignment = $row['assignment'];
    $materialStatus = strtolower((string) ($assignment->material_status ?? 'pending'));

    if ($materialStatus !== 'approved') {
      $deadline = $assignment->sla_upload_deadline;
      $isOverdue = $deadline ? $deadline->isPast() : false;

      $statusLabel = 'JATUH TEMPO';
      $statusClass = 'status-due';

      $title = $materialStatus === 'rejected' ? 'Revisi materi ' . $row['event_title'] : 'Upload materi ' . $row['event_title'];
      $desc = 'SLA Pengunggahan Materi';

      $tugasItems[] = [
        'title' => $title,
        'desc' => $desc,
        'status_label' => $statusLabel,
        'status_class' => $statusClass,
        'time_label' => 'Jadwal menyusual',
        'url' => $assignment->event ? route('trainer.events.studio', $assignment->event->id) : route('trainer.events'),
        'icon_class' => 'bi-cloud-arrow-up-fill'
      ];
    }
  }

  // From course modules needing upload/revision
  $unfinishedCourseModules = \App\Models\CourseModule::whereHas('course', function ($q) use ($trainer) {
    $q->where('trainer_id', $trainer->id);
  })->whereIn('processing_status', ['assigned_to_admin_course', 'revision_requested'])
    ->with('course')
    ->get();

  foreach ($unfinishedCourseModules as $module) {
    $isRevision = $module->processing_status === 'revision_requested';
    $statusLabel = 'JATUH TEMPO';
    $statusClass = 'status-due';

    $tugasItems[] = [
      'title' => ($isRevision ? 'Revisi ' : 'Lengkapi ') . $module->title,
      'desc' => 'SLA Pengunggahan Materi',
      'status_label' => $statusLabel,
      'status_class' => $statusClass,
      'time_label' => 'Jadwal menyusual',
      'url' => route('trainer.courses.studio', $module->course_id),
      'icon_class' => 'bi-cloud-arrow-up-fill'
    ];
  }

  // 6. Calculate "Tugas Menunggu" total count
  $tugasMenungguCount = count($tugasItems);

  // 7. Calendar variables
  $today = now();
  $year = $today->year;
  $month = $today->month;
  $daysInMonth = $today->daysInMonth;
  $firstDayOfMonth = \Carbon\Carbon::create($year, $month, 1);
  $startOfWeek = $firstDayOfMonth->dayOfWeek; // 0 (Sun) to 6 (Sat)
  $startOfWeekIndex = ($startOfWeek + 6) % 7; // Convert to Monday-start (0 = Mon, 6 = Sun)

  $eventsThisMonth = \App\Models\Event::where('trainer_id', $trainer->id)
    ->whereNotNull('event_date')
    ->whereYear('event_date', $year)
    ->whereMonth('event_date', $month)
    ->get()
    ->groupBy(function ($event) {
      return $event->event_date->day;
    });

  // Weeks mapping with adjacent month days
  $prevMonth = $today->copy()->subMonth();
  $prevMonthDays = $prevMonth->daysInMonth;

  $weeks = [];
  $currentWeek = [];

  // Fill first week with previous month days
  for ($i = 0; $i < 7; $i++) {
    if ($i < $startOfWeekIndex) {
      $prevDay = $prevMonthDays - ($startOfWeekIndex - 1 - $i);
      $currentWeek[$i] = [
        'day' => $prevDay,
        'is_current' => false
      ];
    } else {
      $currentWeek[$i] = [
        'day' => $i - $startOfWeekIndex + 1,
        'is_current' => true
      ];
    }
  }
  $weeks[] = $currentWeek;

  $day = 7 - $startOfWeekIndex + 1;
  $nextDayVal = 1;
  while ($day <= $daysInMonth) {
    $currentWeek = array_fill(0, 7, null);
    for ($i = 0; $i < 7; $i++) {
      if ($day <= $daysInMonth) {
        $currentWeek[$i] = [
          'day' => $day,
          'is_current' => true
        ];
        $day++;
      } else {
        // Fill with next month days
        $currentWeek[$i] = [
          'day' => $nextDayVal++,
          'is_current' => false
        ];
      }
    }
    $weeks[] = $currentWeek;
  }

  // 8. Agenda Items (today's events + fallback to upcoming)
  $agendaEvents = \App\Models\Event::where('trainer_id', $trainer->id)
    ->whereNotNull('event_date')
    ->whereDate('event_date', $today->toDateString())
    ->orderBy('event_time', 'asc')
    ->get();

  if ($agendaEvents->isEmpty()) {
    $agendaEvents = \App\Models\Event::where('trainer_id', $trainer->id)
      ->whereNotNull('event_date')
      ->whereDate('event_date', '>=', $today->toDateString())
      ->orderBy('event_date', 'asc')
      ->orderBy('event_time', 'asc')
      ->limit(3)
      ->get();
  }
@endphp

@push('styles')
  @vite(['resources/css/trainer/dashboard.css'])
  <style>
    /* Inline styles for modal specifically to ensure zero style leaks and exact functionality */
    .reply-modal {
      position: fixed;
      inset: 0;
      z-index: 2000;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: rgba(2, 6, 23, 0.55);
      backdrop-filter: blur(4px);
    }

    .reply-modal.is-open {
      display: flex;
    }

    .reply-modal-card {
      width: min(620px, 100%);
      border-radius: var(--radius-lg);
      background: #fff;
      overflow: hidden;
      box-shadow: var(--shadow-premium);
      border: 1px solid var(--clr-border);
    }

    .reply-modal-header,
    .reply-modal-footer {
      padding: 18px 24px;
      background: #f8fafc;
    }

    .reply-modal-header {
      border-bottom: 1px solid var(--clr-border);
    }

    .reply-modal-footer {
      border-top: 1px solid var(--clr-border);
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .reply-modal-body {
      padding: 24px;
    }

    .reply-modal-textarea {
      width: 100%;
      min-height: 150px;
      border-radius: var(--radius-md);
      border: 1px solid var(--clr-border);
      padding: 14px;
      font-size: 14px;
      line-height: 1.6;
      resize: vertical;
      outline: none;
      transition: border-color 0.2s, box-shadow 0.2s;
    }

    .reply-modal-textarea:focus {
      border-color: var(--clr-primary);
      box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }

    .reply-modal-title {
      margin: 0;
      font-family: var(--font-accent);
      font-size: 18px;
      font-weight: 700;
      color: var(--clr-navy);
    }

    .reply-modal-context {
      margin: 6px 0 0;
      font-size: 12px;
      color: var(--clr-text-muted);
      line-height: 1.5;
    }

    .reply-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 10px 18px;
      border-radius: var(--radius-sm);
      font-size: 12px;
      font-weight: 700;
      cursor: pointer;
      border: 1px solid transparent;
      transition: all 0.2s;
      background-color: white;
      border-color: #cbd5e1;
      color: var(--clr-text-main);
    }

    .reply-btn:hover {
      background-color: #f1f5f9;
    }

    .reply-btn.primary {
      background-color: var(--clr-primary);
      color: white;
      border-color: var(--clr-primary);
    }

    .reply-btn.primary:hover {
      background-color: var(--clr-primary-hover);
    }

    .reply-btn:disabled {
      opacity: 0.6;
      cursor: not-allowed;
    }

    .empty-note {
      padding: 24px;
      border-radius: var(--radius-md);
      border: 1px dashed var(--clr-border);
      background: var(--clr-bg);
      color: var(--clr-text-muted);
      font-size: 13px;
      line-height: 1.6;
      text-align: center;
    }

    /* Warning late uploads styling */
    .late-banner {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 16px;
      border-radius: var(--radius-lg);
      border: 1px solid transparent;
      line-height: 1.55;
      font-size: 13px;
    }

    .late-banner.level-1 {
      background: #fffbeb;
      border-color: #fde047;
      color: #854d0e;
    }

    .late-banner.level-2 {
      background: #fef2f2;
      border-color: #fca5a5;
      color: #991b1b;
    }

    .late-banner-icon {
      flex-shrink: 0;
      font-size: 18px;
    }
  </style>
@endpush

@section('content')
  <div class="bg-glow-container" aria-hidden="true">
    <div class="bg-glow-1"></div>
    <div class="bg-glow-2"></div>
  </div>

  <div class="dashboard-container">

    {{-- Warning Late Banner --}}
    @if($lateBanner)
      <div class="late-banner {{ $lateBannerClass }} mb-4">
        <div class="late-banner-icon">{{ $lateUploads === 2 ? '🚨' : '⚠️' }}</div>
        <div>
          <strong>Notifikasi Pelanggaran Rapor</strong><br>
          {{ $lateBanner }}
        </div>
      </div>
    @endif

    {{-- HERO & RATING ROW --}}
    <div class="hero-rating-section">
      {{-- Welcome Hero --}}
      <div class="welcome-hero-card">
        <div class="welcome-text-side">
          <span class="welcome-greeting">{{ $greeting }},</span>
          <h1 class="welcome-title">{{ Auth::user()->name }} 👋</h1>
          <p class="welcome-subtitle">
            Kelola kegiatan mengajar, buat materi terbaik, dan inspirasi peserta Anda.
          </p>

          {{-- Availability Toggle --}}
          <div class="welcome-status-row">
            <span class="account-status-badge {{ $trainerStatusTone }}">
              <i class="bi bi-circle-fill" style="font-size: 8px;"></i>
              Status: {{ $trainerStatusLabel }}
            </span>

            @if($trainerStatus !== 'suspended')
              <form method="POST" action="{{ route('trainer.availability.toggle') }}" class="status-toggle-form">
                @csrf
                <label class="status-switch" title="Geser untuk mengubah status keaktifan">
                  <input type="checkbox" {{ $trainerStatus === 'active' ? 'checked' : '' }} onchange="this.form.submit()">
                  <span class="status-switch-track" aria-hidden="true"></span>
                  <span
                    class="status-switch-label">{{ $trainerStatus === 'active' ? 'Siap Mengajar' : 'Sedang Cuti / Pasif' }}</span>
                </label>
              </form>
            @endif
          </div>
        </div>

        <div class="welcome-img-side">
          <img src="{{ asset('aset/trainer_welcome.png') }}" alt="Trainer Welcome Illustration">
        </div>
      </div>

      {{-- Rating Box --}}
      <div class="rating-premium-card">
        <div class="rating-header">
          <span class="rating-title">
            Rating Trainer Anda
            <i class="bi bi-info-circle rating-info-icon"
              title="Berdasarkan feedback ulasan dari peserta Anda pada course dan event"></i>
          </span>
          <div class="rating-ornament">
            <i class="bi bi-patch-check-fill text-white opacity-25" style="font-size: 28px;"></i>
          </div>
        </div>

        <div class="rating-body">
          <div class="rating-score-row">
            <span class="rating-score">{{ number_format($averageRating, 1) }}</span>
            <div class="rating-stars">
              @for ($i = 1; $i <= 5; $i++)
                @if ($i <= round($averageRating))
                  <i class="bi bi-star-fill"></i>
                @else
                  <i class="bi bi-star"></i>
                @endif
              @endfor
            </div>
          </div>
          <div>
            <span class="rating-badge">{{ $ratingBadge }}</span>
          </div>
        </div>

        <div class="rating-footer">
          <p class="rating-count-text">Berdasarkan {{ number_format($totalRatings) }} penilaian peserta</p>
          <a href="{{ route('trainer.feedback') }}" class="rating-link">
            Lihat Detail Rating
            <i class="bi bi-arrow-right"></i>
          </a>
        </div>
      </div>
    </div>

    {{-- TOP ROW METRIC CARDS --}}
    <div class="top-metrics-row">
      {{-- Undangan Aktif --}}
      <div class="top-metric-card">
        <div class="top-metric-icon-box envelope-box">
          <i class="bi bi-envelope-open-fill"></i>
        </div>
        <div class="top-metric-info">
          <span class="top-metric-label">Undangan Aktif</span>
          <span class="top-metric-value">{{ $pendingInvitationItems->count() }}</span>
          <span class="top-metric-sublabel">Event / Course</span>
        </div>
      </div>

      {{-- Kegiatan Berjalan --}}
      <div class="top-metric-card">
        <div class="top-metric-icon-box clipboard-box">
          <i class="bi bi-clipboard-data-fill"></i>
        </div>
        <div class="top-metric-info">
          <span class="top-metric-label">Kegiatan Berjalan</span>
          <span class="top-metric-value">{{ $activeCourseCount + $activeEventCount }}</span>
          <span class="top-metric-sublabel">Event / Course</span>
        </div>
      </div>

      {{-- Tugas Menunggu --}}
      <div class="top-metric-card">
        <div class="top-metric-icon-box check-box">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <div class="top-metric-info">
          <span class="top-metric-label">Tugas Menunggu</span>
          <span class="top-metric-value">{{ $tugasMenungguCount }}</span>
          <span class="top-metric-sublabel">Perlu diselesaikan</span>
        </div>
      </div>
    </div>

    {{-- MAIN GRID LAYOUT (Matches Screenshot) --}}
    <div class="dashboard-grid-layout">

      {{-- COLUMN LEFT --}}
      <div class="grid-col-left">

        {{-- CARD: UNDANGAN TERBARU --}}
        <div class="dashboard-card shadow-accent">
          <div class="dashboard-card-header">
            <div class="dashboard-card-title-wrap">
              <i class="bi bi-envelope-open-fill dashboard-card-icon" style="color: #6366f1;"></i>
              <h3 class="dashboard-card-title">Undangan Terbaru</h3>
            </div>
            <a href="{{ route('trainer.notifications.index') }}" class="dashboard-card-link">Lihat Semua</a>
          </div>

          <div class="dashboard-card-body" style="display: flex; flex-direction: column; gap: 14px;">
            @forelse($pendingInvitationItems as $invite)
              @php
                $inviteUrl = data_get($invite->data, 'url');
                $inviteStatus = method_exists($invite, 'effectiveInvitationStatus')
                  ? $invite->effectiveInvitationStatus()
                  : data_get($invite->data, 'invitation_status', 'pending');
                $inviteDueAt = data_get($invite->data, 'due_at');
                $dueDate = $inviteDueAt ? \Illuminate\Support\Carbon::parse($inviteDueAt) : null;
                $isOverdue = $dueDate ? $dueDate->isPast() : false;
                $inviteEntityType = method_exists($invite, 'effectiveEntityType')
                  ? $invite->effectiveEntityType()
                  : data_get($invite->data, 'entity_type', 'course');
                $inviteTypeLabel = $inviteEntityType === 'event' ? 'Event' : 'Course';

                $entityId = (int) data_get($invite->data, 'entity_id', 0);
                $entityDate = null;
                $entityCategory = null;
                if ($inviteEntityType === 'event') {
                  $eventObj = \App\Models\Event::find($entityId);
                  $entityDate = $eventObj && $eventObj->event_date ? $eventObj->event_date->format('d M Y') : 'Jadwal menyusul';
                } else {
                  $courseObj = \App\Models\Course::with('category')->find($entityId);
                  $entityCategory = $courseObj && $courseObj->category ? $courseObj->category->name : 'Umum';
                }
              @endphp

              <div class="invite-item-box {{ is_null($invite->read_at) ? 'is-unread' : '' }}">
                <div class="invite-icon-container {{ $inviteEntityType === 'event' ? 'is-event' : 'is-course' }}">
                  <i class="bi {{ $inviteEntityType === 'event' ? 'bi-calendar-date' : 'bi-book' }}"></i>
                </div>

                <div class="invite-details">
                  <div class="invite-meta-badges">
                    <span class="invite-badge-tag {{ $inviteEntityType === 'event' ? 'type-event' : 'type-course' }}">
                      {{ $inviteTypeLabel }}
                    </span>
                    @if(is_null($invite->read_at))
                      <span class="invite-badge-tag status-new">Baru</span>
                    @endif
                  </div>
                  <h4 class="invite-title">{{ $invite->title }}</h4>
                  <div class="invite-meta-text">
                    @if($inviteEntityType === 'event')
                      <span>Tanggal: <strong>{{ $entityDate }}</strong></span>
                    @else
                      <span>Kategori: <strong>{{ $entityCategory }}</strong></span>
                    @endif
                    <span class="mx-2">•</span>
                    <span>Peran: <strong>Instruktur</strong></span>
                  </div>
                </div>

                <div class="invite-actions-wrap">
                  @if($dueDate)
                    <span class="invite-date-badge {{ $isOverdue ? 'is-urgent' : '' }}">
                      Diterima hingga: {{ $dueDate->format('d M Y') }}
                    </span>
                  @endif

                  <div class="invite-buttons-row">
                    @if(!empty($inviteUrl))
                      <a href="{{ route('trainer.notifications.open', $invite->id) }}" class="invite-btn btn-detail">Lihat
                        Detail</a>
                    @endif

                    @if($inviteStatus === 'pending')
                      @if($inviteEntityType === 'course')
                        <button type="button" class="invite-btn btn-accept"
                          onclick="openSchemeSelectionModal({{ $invite->id }}, '{{ addslashes($invite->title) }}', '{{ $inviteEntityType }}')">
                          Terima Undangan
                        </button>
                      @else
                        <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}"
                          style="margin: 0;">
                          @csrf
                          <input type="hidden" name="decision" value="accept">
                          <input type="hidden" name="e_agreement" value="1">
                          <button type="submit" class="invite-btn btn-accept">Terima Undangan</button>
                        </form>
                      @endif

                      <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}"
                        style="margin: 0;">
                        @csrf
                        <input type="hidden" name="decision" value="reject">
                        <button type="submit" class="invite-btn btn-reject">Tolak</button>
                      </form>
                    @endif
                  </div>
                </div>
              </div>
            @empty
              {{-- Envelope Flying Illustration Empty State --}}
              <div class="text-center py-4">
                <img src="{{ asset('aset/trainer_envelope.png') }}" alt="Belum ada undangan"
                  style="width: 260px; max-width: 100%; margin-bottom: 24px;">
                <h4 style="font-size: 16px; font-weight: 800; color: var(--main-navy-clr); margin-bottom: 8px;">Belum ada
                  undangan trainer baru saat ini.</h4>
                <p style="font-size: 13px; color: var(--text-clr); margin: 0;">Undangan untuk event atau course akan muncul
                  di sini.</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- CARD: KEGIATAN BERJALAN --}}
        <div id="kegiatan-berjalan" class="dashboard-card shadow-accent">
          <div class="dashboard-card-header">
            <div class="dashboard-card-title-wrap">
              <i class="bi bi-search dashboard-card-icon" style="color: #6366f1;"></i>
              <h3 class="dashboard-card-title">Kegiatan Berjalan</h3>
            </div>
            <a href="{{ route('trainer.courses') }}" class="dashboard-card-link">Lihat Semua</a>
          </div>

          <div class="dashboard-card-body">
            <div class="progress-list-wrapper">
              @forelse($kegiatanBerjalan as $item)
                <a href="{{ $item['url'] }}" class="progress-item-box">
                  <div class="progress-item-icon-box {{ $item['gradient_class'] }}">
                    <i class="bi {{ $item['icon_class'] }}"></i>
                  </div>

                  <div class="progress-item-info">
                    <div class="progress-item-title-row">
                      <h4 class="progress-item-title">{{ $item['title'] }}</h4>
                      <span class="invite-badge-tag {{ $item['badge_class'] }}">{{ $item['badge_label'] }}</span>
                    </div>

                    <div class="progress-bar-container">
                      <span class="progress-bar-label">{{ $item['progress_label'] }}</span>
                      <div class="progress-bar-bg {{ $item['track_class'] }}">
                        <div class="progress-bar-fill {{ $item['fill_class'] }}" style="width: {{ $item['progress'] }}%">
                        </div>
                      </div>
                      <span class="progress-bar-percent">{{ $item['progress'] }}%</span>
                    </div>
                  </div>

                  <div class="progress-item-stats">
                    <div class="progress-stat-col">
                      <span class="progress-stat-lbl">{{ $item['count_label'] }}</span>
                      <span class="progress-stat-val">{{ number_format($item['count_value']) }}</span>
                    </div>

                    <div class="progress-stat-col" style="min-width: 100px;">
                      <span class="progress-stat-lbl">{{ $item['date_label'] }}</span>
                      <span class="progress-stat-val">{{ $item['date_value'] }}</span>
                    </div>
                  </div>

                  <div class="chevron-right-arrow">
                    <i class="bi bi-chevron-right"></i>
                  </div>
                </a>
              @empty
                <div class="empty-note">
                  Belum ada kelas atau event yang sedang berjalan aktif.
                </div>
              @endforelse
            </div>
          </div>
        </div>

      </div>

      {{-- COLUMN RIGHT --}}
      <div class="grid-col-right">

        {{-- CARD: TUGAS & KEWAJIBAN --}}
        <div class="dashboard-card shadow-accent">
          <div class="dashboard-card-header">
            <div class="dashboard-card-title-wrap">
              <i class="bi bi-calendar-check-fill dashboard-card-icon" style="color: #f59e0b;"></i>
              <h3 class="dashboard-card-title">Tugas & Kewajiban</h3>
            </div>
            <a href="{{ route('trainer.courses') }}" class="dashboard-card-link">Lihat Semua</a>
          </div>

          <div class="dashboard-card-body">
            <div class="tasks-list-wrapper">
              @forelse(collect($tugasItems)->take(3) as $task)
                <a href="{{ $task['url'] }}" class="task-card-row">
                  <div class="task-icon-container {{ $task['status_class'] }}">
                    <i class="bi {{ $task['icon_class'] }}"></i>
                  </div>

                  <div class="task-details">
                    <h4 class="task-title">{{ $task['title'] }}</h4>
                    <span class="task-subtitle">{{ $task['desc'] }}</span>
                  </div>

                  <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 4px;">
                    <span class="task-status-badge {{ $task['status_class'] }}">{{ $task['status_label'] }}</span>
                    <span class="task-time-text">{{ $task['time_label'] }}</span>
                  </div>

                  <div class="chevron-right-arrow">
                    <i class="bi bi-chevron-right" style="font-size: 14px;"></i>
                  </div>
                </a>
              @empty
                <div class="empty-note">
                  ✨ Luar biasa! Semua tugas dan kewajiban Anda telah selesai diselesaikan.
                </div>
              @endforelse
            </div>
          </div>
        </div>
        {{-- CARD: KALENDER KEGIATAN & AGENDA (COMBINED SINGLE CARD) --}}
        <div class="calendar-agenda-card shadow-accent">

          {{-- Card Header --}}
          <div class="dashboard-card-header">
            <div class="dashboard-card-title-wrap">
              <i class="bi bi-calendar3 dashboard-card-icon" style="color: var(--main-navy-clr);"></i>
              <h3 class="dashboard-card-title">Kalender Kegiatan</h3>
            </div>
            <a href="{{ route('trainer.events') }}" class="dashboard-card-link">Lihat Kalender</a>
          </div>

          {{-- Card Body --}}
          <div class="calendar-agenda-body">

            {{-- Left column: Monthly Calendar view --}}
            <div class="calendar-panel">
              <div class="calendar-nav-row">
                <span class="calendar-nav-btn"><i class="bi bi-chevron-left"></i></span>
                <span class="calendar-month-name">{{ $today->translatedFormat('F Y') }}</span>
                <span class="calendar-nav-btn"><i class="bi bi-chevron-right"></i></span>
              </div>

              <div class="calendar-days-grid">
                @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $name)
                  <div class="calendar-day-name">{{ $name }}</div>
                @endforeach

                @foreach($weeks as $week)
                  @foreach($week as $index => $dayData)
                    @php
                      $day = $dayData['day'];
                      $isCurrent = $dayData['is_current'];

                      $hasEvent = $isCurrent && isset($eventsThisMonth[$day]);
                      $isToday = $isCurrent && $today->day === $day;
                      $dateClass = '';

                      if ($isToday) {
                        $dateClass .= ' is-today';
                      }
                      if (!$isCurrent) {
                        $dateClass .= ' is-not-current';
                      }

                      $eventColorClass = '';
                      if ($hasEvent) {
                        $dayEvents = $eventsThisMonth[$day];
                        $firstEventTitle = strtolower($dayEvents->first()->title ?? '');
                        if (str_contains($firstEventTitle, 'kita')) {
                          $eventColorClass = ' color-green';
                        } elseif (str_contains($firstEventTitle, 'apa ya')) {
                          $eventColorClass = ' color-purple';
                        } elseif (str_contains($firstEventTitle, 'janggal')) {
                          $eventColorClass = ' color-orange';
                        } else {
                          $eventColorClass = ' color-purple';
                        }
                        $dateClass .= ' has-event' . $eventColorClass;
                      }
                    @endphp
                    <div class="calendar-date-number{{ $dateClass }}" @if($hasEvent)
                      title="{{ $eventsThisMonth[$day]->pluck('title')->implode(', ') }}"
                      onclick="window.location.href='{{ route('trainer.events.show', $eventsThisMonth[$day]->first()->id) }}'"
                    @endif>
                      {{ $day }}
                      @if($hasEvent)
                        <span class="calendar-date-dot"></span>
                      @endif
                    </div>
                  @endforeach
                @endforeach
              </div>
            </div>

            {{-- Vertical Divider Line --}}
            <div class="calendar-vertical-divider"></div>

            {{-- Right column: Agenda Timeline list --}}
            <div class="agenda-panel">
              <h4 class="agenda-date-header">{{ $today->translatedFormat('l, d F Y') }}</h4>

              <div class="timeline-agenda-list">
                @forelse($agendaEvents as $evt)
                  @php
                    $evtTime = $evt->event_time ? $evt->event_time->format('H:i') : '00:00';

                    // Color dots & lines based on event title
                    $firstTitle = strtolower($evt->title);
                    $agendaColor = 'color-purple'; // default
                    $agendaDesc = 'Persiapan Materi';
                    $agendaMeta = 'Online';

                    if (str_contains($firstTitle, 'kita')) {
                      $agendaColor = 'color-green';
                      $agendaDesc = 'Briefing dengan Admin';
                      $agendaMeta = 'Online';
                    } elseif (str_contains($firstTitle, 'janggal')) {
                      $agendaColor = 'color-orange';
                      $agendaDesc = 'Deadline Upload Materi';
                      $agendaMeta = 'Deadline';
                    } elseif (str_contains($firstTitle, 'marketing')) {
                      $agendaColor = 'color-green';
                      $agendaDesc = 'Sesi 2: Market Research';
                      $agendaMeta = 'Online';
                    } elseif (str_contains($firstTitle, 'leadership')) {
                      $agendaColor = 'color-purple';
                      $agendaDesc = 'Briefing Trainer';
                      $agendaMeta = 'Online';
                    } elseif (str_contains($firstTitle, 'buat materi')) {
                      $agendaColor = 'color-orange';
                      $agendaDesc = 'Modul Pembukaan';
                      $agendaMeta = 'Deadline';
                    }
                  @endphp
                  <div class="timeline-agenda-item">
                    <span class="timeline-time">{{ $evtTime }}</span>
                    <div class="timeline-indicator">
                      <div class="timeline-dot {{ $agendaColor }}"></div>
                    </div>
                    <div class="timeline-details">
                      <a href="{{ route('trainer.events.show', $evt->id) }}" style="text-decoration: none; color: inherit;">
                        <strong class="timeline-title" title="{{ $evt->title }}" style="transition: color 0.2s;"
                          onmouseover="this.style.color='var(--accent-blue)'"
                          onmouseout="this.style.color='var(--main-navy-clr)'">{{ $evt->title }}</strong>
                      </a>
                      <span class="timeline-subtitle">{{ $agendaDesc }}</span>
                      <span class="timeline-meta">{{ $agendaMeta }}</span>
                    </div>
                  </div>
                @empty
                  <div class="empty-note" style="padding: 16px; font-size: 12px;">
                    Belum ada agenda terdekat hari ini.
                  </div>
                @endforelse
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

    {{-- BOTTOM GRIDS: REVENUE SHARING & TEACHING HISTORY --}}
    <div class="dashboard-grid-layout mt-4" style="grid-template-columns: 1fr 1.2fr;">

      {{-- REVENUE SHARING CARD --}}
      <div class="dashboard-card shadow-accent">
        <div class="dashboard-card-header">
          <div class="dashboard-card-title-wrap">
            <div class="header-icon-circle green-theme">
              <i class="bi bi-wallet2"></i>
            </div>
            <div>
              <h3 class="dashboard-card-title">Revenue Sharing</h3>
              <p style="font-size: 11px; color: var(--text-clr); margin: 2px 0 0 0;">Pendapatan dari setiap course yang
                Anda ajar.</p>
            </div>
          </div>
          <div class="saldo-badge-box">
            <span class="saldo-badge-title">Saldo Saat Ini</span>
            <span class="saldo-badge-value">Rp {{ number_format($walletBalance, 0, ',', '.') }}</span>
          </div>
        </div>

        <div class="dashboard-card-body">
          {{-- Total Pendapatan Card --}}
          <div class="total-revenue-box">
            <div>
              <span style="font-size: 12px; color: var(--text-clr); font-weight: 600;">Total Pendapatan</span>
              <div class="total-revenue-value">
                Rp {{ number_format($walletBalance, 0, ',', '.') }}
                <i class="bi bi-info-circle" title="Akumulasi pendapatan saat ini yang belum ditarik"
                  style="font-size: 13px;"></i>
              </div>
              <span style="font-size: 11px; color: var(--text-clr);">Belum ditarik</span>
            </div>
            <div class="total-revenue-illustration">
              <img src="{{ asset('aset/trainer_wallet.png') }}" alt="Wallet illustration">
            </div>
          </div>

          {{-- Revenue sharing table wrapper --}}
          <div class="revenue-table-wrapper">
            <div class="revenue-header-row">
              <div>Nama Course</div>
              <div style="text-align: center;">Peserta Aktif</div>
              <div style="text-align: right;">Harga</div>
              <div style="text-align: center;">Bagi Hasil</div>
              <div style="text-align: right;">Estimasi</div>
            </div>
            @forelse($revenueCourseItems as $row)
              <div class="revenue-data-row">
                <div>
                  <a href="{{ route('trainer.courses.studio', $row['course_id']) }}"
                    style="text-decoration: none; color: inherit;">
                    <strong style="color: var(--main-navy-clr); transition: color 0.2s;"
                      onmouseover="this.style.color='var(--accent-blue)'"
                      onmouseout="this.style.color='var(--main-navy-clr)'">{{ Str::limit($row['course_name'], 30) }}</strong>
                  </a><br>
                  <small class="text-muted">★ {{ number_format((float) ($row['rating'] ?? 0), 1) }}</small>
                </div>
                <div style="text-align: center;">{{ number_format((int) ($row['active_students_count'] ?? 0)) }}</div>
                <div style="text-align: right;">Rp{{ number_format((float) ($row['price'] ?? 0), 0, ',', '.') }}</div>
                <div style="text-align: center;">{{ (int) ($row['scheme_percent'] ?? 0) }}%</div>
                <div style="text-align: right;"><strong
                    style="color: var(--main-navy-clr);">Rp{{ number_format((float) ($row['estimated_revenue'] ?? 0), 0, ',', '.') }}</strong>
                </div>
              </div>
            @empty
              <div class="card-empty-state">
                <img src="{{ asset('aset/trainer_document_search.png') }}" alt="Belum ada data revenue course"
                  class="card-empty-img" style="max-width: 140px;">
                <h4 class="card-empty-title">Belum ada data revenue course.</h4>
                <p class="card-empty-desc">Data pendapatan akan muncul setelah ada peserta aktif.</p>
              </div>
            @endforelse
          </div>

          {{-- Footer Hint Box --}}
          <div class="revenue-footer-hint">
            <i class="bi bi-info-circle-fill"></i>
            <span>Pendapatan akan dihitung berdasarkan jumlah peserta aktif dan kebijakan bagi hasil.</span>
          </div>
        </div>
      </div>

      {{-- E-CERTIFICATES & TEACHING HISTORY CARD --}}
      <div class="dashboard-card shadow-accent">
        <div class="dashboard-card-header">
          <div class="dashboard-card-title-wrap">
            <div class="header-icon-circle purple-theme">
              <i class="bi bi-award-fill"></i>
            </div>
            <div>
              <h3 class="dashboard-card-title">Riwayat Mengajar & E-Sertifikat</h3>
              <p style="font-size: 11px; color: var(--text-clr); margin: 2px 0 0 0;">Lihat perjalanan mengajar dan
                sertifikat yang Anda dapatkan.</p>
            </div>
          </div>
          <a href="{{ route('trainer.certificates.index') }}" class="btn-outline-purple">Lihat Semua <i
              class="bi bi-chevron-right" style="font-size: 10px;"></i></a>
        </div>

        <div class="dashboard-card-body" style="padding: 24px;">
          <div style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
            @forelse($teachingHistory as $history)
              @php
                $isCourseCert = $history->certifiable_type === \App\Models\Course::class;
                $certifiable = $history->certifiable;
                $activityTitle = $isCourseCert
                  ? (optional($certifiable)->name ?? 'Kelas')
                  : (optional($certifiable)->title ?? 'Event');
              @endphp
              <div
                style="display: flex; justify-content: space-between; align-items: center; border: 1px solid var(--line-clr); border-radius: var(--radius-xl); padding: 14px 18px; background-color: #fafbfe; transition: all 0.2s ease;">
                <div>
                  @if($certifiable)
                    <a href="{{ $isCourseCert ? route('trainer.courses.studio', $certifiable->id) : route('trainer.events.studio', $certifiable->id) }}"
                      style="text-decoration: none; color: inherit;">
                      <strong
                        style="color: var(--main-navy-clr); font-size: 13.5px; font-weight: 700; transition: color 0.2s;"
                        onmouseover="this.style.color='var(--accent-blue)'"
                        onmouseout="this.style.color='var(--main-navy-clr)'">{{ Str::limit($activityTitle, 46) }}</strong>
                    </a>
                  @else
                    <strong
                      style="color: var(--main-navy-clr); font-size: 13.5px; font-weight: 700;">{{ Str::limit($activityTitle, 46) }}</strong>
                  @endif<br>
                  <span class="text-muted" style="font-size: 11.5px;">
                    {{ $isCourseCert ? 'Course Selesai' : 'Event Selesai' }} • Terbit
                    {{ optional($history->issued_at)->format('d M Y') ?? '-' }}
                  </span>
                </div>

                @if(!empty($history->file_path) && $certifiable)
                        <a href="{{ $isCourseCert
                  ? route('trainer.certificates.courses.download', $certifiable)
                  : route('trainer.certificates.events.download', $certifiable) }}" class="btn-outline-purple"
                          style="font-size: 11px; padding: 6px 12px;">
                          Download
                        </a>
                @endif
              </div>
            @empty
              <div class="card-empty-state" style="padding: 16px;">
                <img src="{{ asset('aset/trainer_cert_empty.png') }}" alt="Belum ada riwayat" class="card-empty-img"
                  style="max-width: 180px;">
                <h4 class="card-empty-title">Belum ada riwayat sertifikat mengajar.</h4>
                <p class="card-empty-desc">Sertifikat akan muncul setelah Anda menyelesaikan kelas.</p>
              </div>
            @endforelse
          </div>

          {{-- USER FEEDBACKS/REVIEWS NESTED CARD --}}
          <div class="review-section-box">
            <div class="review-section-header">
              <div style="display: flex; align-items: center; gap: 10px;">
                <div class="header-icon-circle purple-theme"
                  style="width: 34px; height: 34px; font-size: var(--font-size-md);">
                  <i class="bi bi-chat-left-text-fill"></i>
                </div>
                <h4 class="dashboard-card-title" style="font-size: 14px; margin: 0;">Ulasan Peserta Terbaru</h4>
              </div>
              <a href="{{ route('trainer.feedback') }}" class="btn-outline-purple"
                style="font-size: 11px; padding: 6px 12px;">Lihat Semua Ulasan</a>
            </div>

            <div style="display: flex; flex-direction: column; gap: 12px;">
              @forelse($feedbackItems as $feedback)
                <div class="review-item-box">
                  <div class="review-item-header">
                    <div>
                      <h5 style="font-size: 13.5px; font-weight: 700; color: var(--main-navy-clr); margin: 0 0 2px 0;">
                        {{ $feedback->user->name ?? 'Peserta' }}</h5>
                      <small class="text-muted" style="font-size: 11px;">
                        @if($feedback->event)
                          <a href="{{ route('trainer.events.studio', $feedback->event->id) }}"
                            style="text-decoration: none; color: inherit; font-weight: 600; transition: color 0.2s;"
                            onmouseover="this.style.color='var(--accent-blue)'" onmouseout="this.style.color=''">
                            {{ $feedback->event->title }}
                          </a>
                        @else
                          Event
                        @endif
                        • {{ optional($feedback->created_at)->format('d M Y') }}
                      </small>
                    </div>
                    <span class="review-badge-rating">★ {{ number_format((float) ($feedback->rating ?? 0), 1) }}</span>
                  </div>

                  <p class="review-comment">{{ $feedback->comment ?: 'Tidak ada komentar tertulis.' }}</p>

                  @if($feedback->replies->isNotEmpty())
                    <div class="review-reply-box">
                      <strong>Balasan Anda:</strong>
                      {{ Str::limit((string) data_get($feedback->replies->last(), 'response'), 120) }}
                    </div>
                  @endif

                  <button type="button" class="review-reply-btn"
                    onclick="openFeedbackReplyModal({{ $feedback->id }}, @js($feedback->user->name ?? 'Peserta'), @js(optional($feedback->event)->title ?? 'Event'))">
                    <i class="bi bi-reply-fill"></i> Balas Ulasan
                  </button>
                </div>
              @empty
                <div class="card-empty-state"
                  style="padding: 16px; background-color: var(--white-clr); border-radius: var(--radius-xl); border: 1px dashed var(--line-clr);">
                  <img src="{{ asset('aset/trainer_review_empty.png') }}" alt="Belum ada ulasan" class="card-empty-img"
                    style="max-width: 140px; margin-bottom: 12px;">
                  <h4 class="card-empty-title">Belum ada ulasan peserta yang masuk.</h4>
                  <p class="card-empty-desc">Ulasan dari peserta akan ditampilkan di sini.</p>
                </div>
              @endforelse
            </div>
          </div>

        </div>
      </div>

    </div>

  </div>

  {{-- MODAL FOR FEEDBACK REPLY --}}
  <div class="reply-modal" id="feedbackReplyModal" aria-hidden="true">
    <div class="reply-modal-card">
      <div class="reply-modal-header">
        <h3 class="reply-modal-title">Balas Komentar Peserta</h3>
        <p class="reply-modal-context" id="replyModalContext">Tulis balasan Anda di bawah.</p>
      </div>
      <form id="feedbackReplyForm">
        @csrf
        <input type="hidden" name="feedback_id" id="replyFeedbackId">
        <input type="hidden" name="response" id="replyResponseField">
        <div class="reply-modal-body">
          <textarea class="reply-modal-textarea" id="replyResponseInput"
            placeholder="Tulis balasan Anda kepada peserta..."></textarea>
        </div>
        <div class="reply-modal-footer">
          <button type="button" class="reply-btn" id="replyModalCancel">Batal</button>
          <button type="submit" class="reply-btn primary" id="replyModalSubmit">Kirim Balasan</button>
        </div>
      </form>
    </div>
  </div>

  @push('scripts')
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const replyModal = document.getElementById('feedbackReplyModal');
        const replyForm = document.getElementById('feedbackReplyForm');
        const replyInput = document.getElementById('replyResponseInput');
        const replyContext = document.getElementById('replyModalContext');
        const replyFeedbackId = document.getElementById('replyFeedbackId');
        const replyResponseField = document.getElementById('replyResponseField');
        const replyCancel = document.getElementById('replyModalCancel');
        const replySubmit = document.getElementById('replyModalSubmit');
        const feedbackReplyStoreUrl = @json(route('trainer.feedback.reply.store'));

        function openReplyModal(feedbackId, participantName, eventTitle) {
          if (!replyModal) return;
          replyFeedbackId.value = String(feedbackId);
          replyResponseField.value = '';
          replyInput.value = '';
          replyContext.textContent = `Peserta: ${participantName} • Event: ${eventTitle}`;
          replyModal.classList.add('is-open');
          replyModal.setAttribute('aria-hidden', 'false');
          setTimeout(() => replyInput.focus(), 80);
        }

        function closeReplyModal() {
          if (!replyModal) return;
          replyModal.classList.remove('is-open');
          replyModal.setAttribute('aria-hidden', 'true');
        }

        window.openFeedbackReplyModal = openReplyModal;

        if (replyCancel) {
          replyCancel.addEventListener('click', closeReplyModal);
        }

        if (replyModal) {
          replyModal.addEventListener('click', function (event) {
            if (event.target === replyModal) {
              closeReplyModal();
            }
          });
        }

        if (replyForm) {
          replyForm.addEventListener('submit', function (event) {
            event.preventDefault();

            const response = replyInput.value.trim();
            if (!response) {
              replyInput.focus();
              return;
            }

            replySubmit.disabled = true;
            replySubmit.textContent = 'Mengirim...';
            replyResponseField.value = response;

            fetch(feedbackReplyStoreUrl, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': replyForm.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
              },
              body: JSON.stringify({
                feedback_id: replyFeedbackId.value,
                response: response
              })
            })
              .then(async (res) => {
                let payload = {};
                try {
                  payload = await res.json();
                } catch (_) {
                  payload = {};
                }

                if (!res.ok || !payload.success) {
                  throw new Error(payload.message || 'Gagal menyimpan balasan.');
                }

                return payload;
              })
              .then(() => {
                closeReplyModal();
                window.location.reload();
              })
              .catch((error) => {
                alert(error.message || 'Gagal menyimpan balasan.');
              })
              .finally(() => {
                replySubmit.disabled = false;
                replySubmit.textContent = 'Kirim Balasan';
              });
          });
        }
      });
    </script>
  @endpush

  @include('trainer.partials.scheme-selection-modal')
@endsection