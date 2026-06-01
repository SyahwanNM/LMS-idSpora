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
    1 => 'âš ï¸ Peringatan: Anda memiliki 1x riwayat keterlambatan. Harap tepat waktu di kelas berikutnya agar rapor kembali bersih.',
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
  $courseReviews = \App\Models\Review::whereHas('course', function ($q) use ($trainer) {
    $q->where('trainer_id', $trainer->id);
  })->get(['rating']);
  
  $eventFeedbacks = \App\Models\Feedback::whereHas('event', function ($q) use ($trainer) {
    $q->where('trainer_id', $trainer->id);
  })->get(['rating']);
  
  $totalRatings = $courseReviews->count() + $eventFeedbacks->count();
  
  $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
  foreach($courseReviews as $r) { 
      $val = (int) round($r->rating);
      if($val >= 1 && $val <= 5) $ratingCounts[$val]++; 
  }
  foreach($eventFeedbacks as $f) { 
      $val = (int) round($f->rating);
      if($val >= 1 && $val <= 5) $ratingCounts[$val]++; 
  }
  
  $ratingPercentages = [];
  foreach($ratingCounts as $star => $count) {
      $ratingPercentages[$star] = $totalRatings > 0 ? round(($count / $totalRatings) * 100) : 0;
  }

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

  // 3b. Fetch 5-Month Revenue Data
  $revenueData = [];
  $maxRevenue = 0;
  $monthsLabel = [];

  for ($i = 4; $i >= 0; $i--) {
      $dt = now()->subMonths($i);
      $monthStart = $dt->copy()->startOfMonth();
      $monthEnd = $dt->copy()->endOfMonth();

      $amount = \App\Models\TrainerPayment::where('user_id', $trainer->id)
          ->where('status', 'approved')
          ->whereBetween('payment_date', [$monthStart, $monthEnd])
          ->sum('amount');

      $revenueData[] = $amount;
      $monthsLabel[] = $dt->translatedFormat('M Y');
      if ($amount > $maxRevenue) $maxRevenue = $amount;
  }

  // Calculate SVG Coordinates (viewBox 360 x 150)
  $svgPoints = [];
  $xStep = 320 / 4; 
  // Make the curve occupy only 80px vertically (from y=40 to y=120) to leave plenty of padding
  $yScale = $maxRevenue > 0 ? 80 / $maxRevenue : 0;

  foreach ($revenueData as $index => $amount) {
      $x = 20 + ($index * $xStep);
      $y = 120 - ($amount * $yScale); // Baseline is 120 (so it never hits bottom 150)
      $svgPoints[] = [
          'x' => $x, 'y' => $y, 'amount' => $amount, 'label' => $monthsLabel[$index]
      ];
  }

  $pathD = "M {$svgPoints[0]['x']} {$svgPoints[0]['y']} ";
  for ($i = 1; $i < 5; $i++) {
      $cpX = ($svgPoints[$i-1]['x'] + $svgPoints[$i]['x']) / 2;
      $pathD .= "C {$cpX} {$svgPoints[$i-1]['y']}, {$cpX} {$svgPoints[$i]['y']}, {$svgPoints[$i]['x']} {$svgPoints[$i]['y']} ";
  }
  $areaD = $pathD . " L {$svgPoints[4]['x']} 150 L {$svgPoints[0]['x']} 150 Z";

  $thisMonthRevenue = $revenueData[4] ?? 0;
  $lastMonthRevenue = $revenueData[3] ?? 0;
  $revenueGrowth = 0;
  if ($lastMonthRevenue > 0) {
      $revenueGrowth = (($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100;
  } elseif ($thisMonthRevenue > 0) {
      $revenueGrowth = 100;
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
      $daysLeft = $deadline ? round(now()->diffInDays($deadline, false)) : null;

      $statusLabel = 'JATUH TEMPO';
      $statusClass = 'status-due';

      $title = $materialStatus === 'rejected' ? 'Revisi materi ' . $row['event_title'] : 'Upload materi ' . $row['event_title'];
      $desc = 'SLA Pengunggahan Materi';

      $tugasItems[] = [
        'title' => $title,
        'desc' => $desc,
        'status_label' => $statusLabel,
        'status_class' => $statusClass,
        'time_label' => 'Jadwal menyusul',
        'url' => $assignment->event ? route('trainer.events.studio', $assignment->event->id) : route('trainer.events'),
        'icon_class' => 'bi-cloud-arrow-up-fill',
        'days_left' => $daysLeft !== null ? max(0, $daysLeft) : '-',
        'raw_days_left' => $daysLeft,
        'date_str' => $deadline ? $deadline->format('d M Y') : 'Jadwal menyusul'
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
    
    $moduleDeadline = $module->updated_at ? $module->updated_at->addDays(7) : now()->addDays(7);
    $daysLeft = round(now()->diffInDays($moduleDeadline, false));

    $tugasItems[] = [
      'title' => ($isRevision ? 'Revisi ' : 'Lengkapi ') . $module->title,
      'desc' => 'SLA Pengunggahan Materi',
      'status_label' => $statusLabel,
      'status_class' => $statusClass,
      'time_label' => 'Jadwal menyusul',
      'url' => route('trainer.courses.studio', $module->course_id),
      'icon_class' => 'bi-cloud-arrow-up-fill',
      'days_left' => max(0, $daysLeft),
      'raw_days_left' => $daysLeft,
      'date_str' => $moduleDeadline->format('d M Y')
    ];
  }

  // 6. Calculate "Tugas Menunggu" total count
  $tugasMenungguCount = count($tugasItems);

  // 7. Calendar variables
  $today = now();
  $calDate = request('cal_date') ? \Carbon\Carbon::parse(request('cal_date')) : now();
  $year = $calDate->year;
  $month = $calDate->month;
  $daysInMonth = $calDate->daysInMonth;
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
  $prevMonth = $calDate->copy()->subMonth();
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

  // Pre-fetch all event detail links for the Javascript Calendar
  $allTrainerEvents = \App\Models\Event::where('trainer_id', $trainer->id)->whereNotNull('event_date')->get();
  $eventDetailLinks = [];
  foreach($allTrainerEvents as $ev) {
      $dateStr = \Carbon\Carbon::parse($ev->event_date)->format('Y-m-d');
      $eventDetailLinks[$dateStr] = route('trainer.events.show', $ev->id);
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
<style>
/* EXACT MATCH TO MOCKUP */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

body {
    background-color: #f8fafc;
    font-family: 'Inter', sans-serif;
    color: #334155;
    -webkit-font-smoothing: antialiased;
}

.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 32px 24px;
    display: flex;
    flex-direction: column;
    gap: 32px;
}

/* Header */
.header-section {
    background: linear-gradient(135deg, #2e2050 0%, #51376c 100%);
    border: none;
    border-radius: 24px;
    padding: 32px 40px;
    margin-bottom: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 15px 35px rgba(27, 23, 99, 0.15);
    position: relative;
    overflow: hidden;
}
.header-section::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
    border-radius: 50%;
}
.header-content {
    position: relative;
    z-index: 2;
}
.welcome-greeting { font-size: 32px; font-weight: 800; color: white; margin: 0 0 12px 0; display: flex; align-items: center; gap: 12px; letter-spacing: -0.5px; }
.welcome-subtitle { font-size: 16px; color: rgba(255, 255, 255, 0.8); margin: 0; font-weight: 500; }
.status-toggle-wrapper { display: flex; align-items: center; gap: 16px; position: relative; z-index: 2; margin-top: 24px; }
.status-badge { display: inline-flex; align-items: center; gap: 8px; padding: 6px 16px; border-radius: 99px; font-size: 13px; font-weight: 600; }
.status-badge.is-active { background-color: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); }
.status-badge.is-passive { background-color: rgba(255, 255, 255, 0.1); color: rgba(255, 255, 255, 0.7); border: 1px solid rgba(255, 255, 255, 0.15); }
.status-badge.is-suspended { background-color: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.3); }

.status-switch { display: inline-flex; align-items: center; gap: 12px; cursor: pointer; user-select: none; }
.status-switch input { position: absolute; opacity: 0; width: 0; height: 0; }
.status-switch-track { width: 48px; height: 26px; background-color: #cbd5e1; border-radius: 99px; position: relative; transition: all 0.3s; }
.status-switch-track::before { content: ""; position: absolute; height: 20px; width: 20px; left: 3px; bottom: 3px; background-color: #fff; border-radius: 50%; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.status-switch input:checked + .status-switch-track { background-color: #10b981; }
.status-switch input:checked + .status-switch-track::before { transform: translateX(22px); }
.status-switch:hover .status-switch-track { box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15); }

/* Grids */
.grid-3-col { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.grid-2-col-wide { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; }

/* Cards */
.dash-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 20px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02), 0 2px 4px -1px rgba(0,0,0,0.02); display: flex; flex-direction: column; }
.dash-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
.dash-card-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0; }
.dash-card-link { font-size: 13px; font-weight: 600; color: #624388; text-decoration: none; display: flex; align-items: center; gap: 4px; }
.dash-card-link:hover { color: #51376c; text-decoration: underline; }

/* Pendapatan Total */
.dash-revenue-card { background: linear-gradient(135deg, #624388 0%, #8562b3 100%); color: white; border: none; padding: 24px 24px 0 24px; position: relative; overflow: hidden; box-shadow: 0 10px 25px -5px rgba(98, 67, 136, 0.4); }
.revenue-title { font-size: 13px; font-weight: 600; color: rgba(255,255,255,0.9); text-transform: uppercase; letter-spacing: 0.5px; display: flex; align-items: center; }
.revenue-amount { font-size: 36px; font-weight: 800; margin: 16px 0 12px 0; letter-spacing: -1px; }
.revenue-growth { display: flex; align-items: center; gap: 10px; font-size: 12px; font-weight: 500; margin-bottom: 0; }
.revenue-growth .badge-up { padding: 4px 10px; border-radius: 99px; font-weight: 700; display: flex; align-items: center; gap: 6px; }
.chart-container { width: calc(100% + 48px); flex: 1; min-height: 160px; margin-top: 24px; position: relative; margin-left: -24px; margin-bottom: -15px; }
.revenue-btn-wrapper { padding: 0 24px 24px 24px; position: relative; z-index: 10; margin-left: -24px; width: calc(100% + 48px); }
.revenue-btn { display: block; width: 100%; background: #ffffff; color: #1e3a8a; padding: 14px; border-radius: 12px; text-align: center; font-weight: 700; font-size: 14px; text-decoration: none; transition: all 0.2s ease; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
.revenue-btn:hover { background: #f8fafc; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2); }

/* Graph Tooltip Styles */
.graph-tooltip { position: absolute; transform: translate(-50%, -100%); margin-top: -15px; opacity: 0; visibility: hidden; transition: all 0.2s ease; z-index: 20; pointer-events: none; }
.graph-tooltip::after { content: ''; position: absolute; bottom: -5px; left: 50%; transform: translateX(-50%); border-width: 5px 5px 0; border-style: solid; border-color: #0f172a transparent transparent transparent; }
.graph-tooltip.tooltip-first { transform: translate(-25px, -100%); }
.graph-tooltip.tooltip-first::after { left: 25px; }
.graph-tooltip.tooltip-last { transform: translate(calc(-100% + 25px), -100%); }
.graph-tooltip.tooltip-last::after { left: calc(100% - 25px); }
.tooltip-content { background: #0f172a; color: white; padding: 6px 12px; border-radius: 8px; font-size: 11px; white-space: nowrap; text-align: center; box-shadow: 0 4px 6px rgba(0,0,0,0.2); }
.hover-target { position: absolute; transform: translate(-50%, -50%); width: 30px; height: 30px; cursor: pointer; z-index: 15; }
.hover-target:hover + .graph-tooltip { opacity: 1; visibility: visible; margin-top: -20px; }

/* Ringkasan Aktivitas */
.activity-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; flex: 1; margin-bottom: 24px; }
.activity-item { display: flex; flex-direction: column; background: #ffffff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 16px; text-align: left; }
.activity-icon { width: 40px; height: 40px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
.icon-purple { background: #f7f5fa; color: #624388; }
.icon-green { background: #d1fae5; color: #10b981; }
.icon-orange { background: #ffedd5; color: #f97316; }
.icon-blue { background: #f7f5fa; color: #624388; }
.activity-val { font-size: 24px; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 6px; }
.activity-lbl { font-size: 13px; color: #475569; font-weight: 600; }
.activity-sub { font-size: 11px; color: #94a3b8; margin-top: 4px; }

/* E-Sertifikat Saya */
.cert-count-row { margin-bottom: 24px; }
.cert-count { font-size: 32px; font-weight: 800; color: #0f172a; line-height: 1; margin-bottom: 4px; }
.cert-subtitle { font-size: 13px; color: #64748b; font-weight: 500; }
.cert-list { display: flex; flex-direction: column; gap: 12px; flex: 1; margin-bottom: 24px; }
.cert-item { display: flex; align-items: center; gap: 16px; padding: 12px; border: 1px solid #f1f5f9; border-radius: 12px; background: #ffffff; }
.cert-img { width: 60px; height: 40px; border: 1px solid #e2e8f0; border-radius: 4px; background: #f8fafc; object-fit: contain; }
.cert-info h5 { margin: 0 0 4px 0; font-size: 13px; font-weight: 700; color: #0f172a; }
.cert-info p { margin: 0; font-size: 11px; color: #64748b; }
.btn-arrow-icon { width: 36px; height: 36px; border: 1px solid #e2e8f0; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #64748b; text-decoration: none; transition: 0.2s; }
.btn-arrow-icon:hover { border-color: #cbd5e1; background: #f8fafc; }

/* Undangan Diterima */
.card-green-top {
    border: none; position: relative; overflow: hidden; background: linear-gradient(145deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0;
}
.card-green-top::before { content: ''; position: absolute; top: 0; right: 0; width: 160px; height: 160px; background: radial-gradient(circle, rgba(98,67,136,0.06) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; transform: translate(30%, -30%); z-index: 1; pointer-events: none; }
.card-green-top > * { position: relative; z-index: 2; }
.badge-header-green { font-size: 12px; font-weight: 700; color: #51376c; }
.invite-item { display: flex; align-items: flex-start; gap: 16px; padding-bottom: 16px; border-bottom: 1px solid #f1f5f9; margin-bottom: 16px; }
.invite-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.invite-icon-box { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; flex-shrink: 0; }
.box-purple { background: #f7f5fa; color: #624388; }
.box-green { background: #f7f5fa; color: #624388; }
.invite-content { flex: 1; }
.tag-badge { font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 6px; text-transform: uppercase; display: inline-block; margin-bottom: 8px; letter-spacing: 0.5px; }
.tag-event { background: #f7f5fa; color: #624388; }
.tag-course { background: #f7f5fa; color: #624388; }
.invite-title { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 6px 0; line-height: 1.4; }
.invite-meta-row { font-size: 12px; color: #475569; display: flex; justify-content: space-between; margin-bottom: 8px; }
.invite-meta-icons { font-size: 12px; color: #64748b; display: flex; align-items: center; gap: 16px; margin-bottom: 12px; }
.invite-desc { font-size: 12px; color: #64748b; line-height: 1.5; }

/* Deadline Materi */
.card-orange-top {
    border: none; position: relative; overflow: hidden; background: linear-gradient(145deg, #ffffff, #f8fafc); border: 1px solid #e2e8f0;
}
.card-orange-top::before { content: ''; position: absolute; top: 0; right: 0; width: 160px; height: 160px; background: radial-gradient(circle, rgba(98,67,136,0.06) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; transform: translate(30%, -30%); z-index: 1; pointer-events: none; }
.card-orange-top > * { position: relative; z-index: 2; }
.badge-header-orange { font-size: 12px; font-weight: 700; color: #51376c; }
.deadline-item { display: flex; align-items: flex-start; gap: 16px; padding-bottom: 12px; border-bottom: 1px solid #f1f5f9; margin-bottom: 12px; transition: background-color 0.2s ease, border-radius 0.2s ease; padding: 12px; margin-left: -12px; width: calc(100% + 24px); cursor: pointer; }
.deadline-item:hover { background-color: #f8fafc; border-radius: 12px; }
.deadline-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 12px; }
.deadline-icon { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: white; background: linear-gradient(135deg, #624388 0%, #8562b3 100%); flex-shrink: 0; }
.tag-deadline { font-size: 10px; font-weight: 800; padding: 4px 8px; border-radius: 6px; margin-bottom: 8px; display: inline-block; transition: all 0.2s; }
.tag-deadline-danger { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
.tag-deadline-warning { background: #fef3c7; color: #d97706; border: 1px solid #fcd34d; }
.tag-deadline-normal { background: #f7f5fa; color: #624388; border: 1px solid #e9d5ff; }
.deadline-title { font-size: 14px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
.deadline-date { font-size: 11px; color: #64748b; font-weight: 600; margin-left: auto; }
.deadline-desc { font-size: 12px; color: #475569; margin-top: 6px; }

/* Rating & Ulasan */
.rating-big-row { display: flex; align-items: baseline; gap: 4px; margin-bottom: 4px; }
.rating-big { font-size: 40px; font-weight: 800; color: #0f172a; line-height: 1; }
.rating-max { font-size: 18px; color: #64748b; font-weight: 600; }
.stars { color: #f59e0b; font-size: 18px; letter-spacing: 2px; margin-bottom: 4px; }
.rating-count { font-size: 12px; color: #64748b; margin-bottom: 24px; }
.rating-bars { display: flex; flex-direction: column; gap: 8px; margin-bottom: 24px; }
.rating-bar-row { display: flex; align-items: center; gap: 12px; font-size: 12px; color: #475569; font-weight: 600; }
.rating-bar-row span:first-child { width: 20px; }
.rating-bar-row span:last-child { width: 40px; text-align: right; color: #94a3b8; font-weight: 500; }
.bar-track { flex: 1; height: 6px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
.bar-fill { height: 100%; background: #624388; border-radius: 4px; }

.review-box { display: flex; gap: 12px; align-items: flex-start; }
.reviewer-avatar { width: 36px; height: 36px; border-radius: 50%; object-fit: cover; }
.review-content { flex: 1; }
.review-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px; }
.reviewer-name { font-size: 13px; font-weight: 700; color: #0f172a; }
.review-stars { color: #f59e0b; font-size: 11px; }
.review-text { font-size: 12px; color: #475569; line-height: 1.5; margin: 0 0 12px 0; }
.review-footer { display: flex; justify-content: space-between; align-items: center; }
.review-tag { background: #f7f5fa; color: #624388; font-size: 10px; font-weight: 600; padding: 4px 8px; border-radius: 4px; }
.review-date { font-size: 10px; color: #94a3b8; }

/* Kelas & Event Berjalan Grid */
.ongoing-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
.ongoing-card { position: relative; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 20px; padding: 24px; display: flex; flex-direction: column; background: linear-gradient(145deg, #ffffff, #f8fafc); transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.3s ease; cursor: pointer; min-height: 190px; }
.ongoing-card:hover { transform: translateY(-6px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.02); border-color: #cbd5e1; }
.ongoing-card::before { content: ''; position: absolute; top: 0; right: 0; width: 160px; height: 160px; background: radial-gradient(circle, rgba(98,67,136,0.06) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; transform: translate(30%, -30%); z-index: 1; pointer-events: none; }
.ongoing-card .tag-badge { margin-bottom: 0; position: relative; z-index: 2; }
.ongoing-icon-small { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; color: white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); position: relative; z-index: 2; }
.btn-kelola-new { background: #3c2957; color: white; padding: 8px 18px; border-radius: 10px; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 6px; transition: background 0.2s ease, transform 0.2s ease; position: relative; z-index: 2; }
.ongoing-card:hover .btn-kelola-new { background: #624388; }
.icon-gradient-green { background: linear-gradient(135deg, #34d399 0%, #059669 100%); }
.icon-gradient-purple { background: linear-gradient(135deg, #a78bfa 0%, #7c3aed 100%); }
.icon-gradient-blue { background: linear-gradient(135deg, #60a5fa 0%, #2563eb 100%); }

/* Kalender */
.cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.cal-nav { background: none; border: none; font-size: 16px; color: #64748b; cursor: pointer; text-decoration: none; transition: all 0.2s ease; display: inline-flex; align-items: center; justify-content: center; width: 28px; height: 28px; border-radius: 6px; }
.cal-nav:hover { color: #624388; background: #f7f5fa; }
.cal-month { font-size: 15px; font-weight: 700; color: #0f172a; }
.cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px 4px; text-align: center; margin-bottom: 24px; }
.cal-day-name { font-size: 11px; font-weight: 700; color: #0f172a; padding-bottom: 8px; }
.cal-date { width: 32px; height: 32px; margin: 0 auto; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 600; color: #334155; border-radius: 50%; cursor: pointer; transition: all 0.2s ease; text-decoration: none; }
.cal-date:hover { background-color: #f7f5fa; color: #624388; transform: scale(1.1); }
.cal-date.muted { color: #cbd5e1; }
.cal-date.muted:hover { background-color: #f1f5f9; color: #94a3b8; transform: scale(1.05); }
.cal-date.selected-orange { border: 2px solid #f97316; color: #f97316; }
.cal-date.selected-blue { border: 2px solid #3b82f6; color: #3b82f6; }
.cal-date.selected-green { border: 2px solid #10b981; color: #10b981; }
.cal-date.today-fill { background: #624388; color: white; }
.cal-legend { display: flex; justify-content: center; gap: 24px; font-size: 12px; font-weight: 600; color: #475569; }
.legend-item { display: flex; align-items: center; gap: 8px; }
.dot { width: 8px; height: 8px; border-radius: 50%; }
.dot-green { background: #10b981; }
.dot-purple { background: #624388; }
.dot-orange { background: #f97316; }

/* Announcement */
.announcement-card { background: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 24px; display: flex; align-items: center; gap: 20px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02); }
.announcement-icon { width: 56px; height: 56px; background: #f7f5fa; color: #624388; border-radius: 16px; display: flex; align-items: center; justify-content: center; font-size: 24px; flex-shrink: 0; }
.announcement-content { flex: 1; }
.announcement-title { font-size: 16px; font-weight: 700; color: #0f172a; margin: 0 0 4px 0; }
.announcement-desc { font-size: 13px; color: #64748b; margin: 0; }
.announcement-date { font-size: 12px; font-weight: 500; color: #94a3b8; display: flex; align-items: center; gap: 16px; }

@media (max-width: 1200px) {
    .grid-3-col { grid-template-columns: repeat(2, 1fr); }
    .grid-2-col-wide { grid-template-columns: 1fr; }
    .ongoing-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 768px) {
    .grid-3-col { grid-template-columns: 1fr; }
    .ongoing-grid { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="dashboard-container">

    {{-- Warning Late Banner --}}
    @if($lateBanner)
      <div class="dash-card mb-4" style="background: #fef2f2; border-color: #fca5a5; padding: 16px; flex-direction: row; gap: 16px; align-items: center;">
        <div style="font-size: 24px;">{{ $lateUploads === 2 ? '🚨' : '⚠️' }}</div>
        <div>
          <strong style="color: #9f1239; font-size: 15px;">Notifikasi Pelanggaran Rapor</strong><br>
          <span style="color: #be123c; font-size: 14px;">{{ $lateBanner }}</span>
        </div>
      </div>
    @endif

    {{-- HEADER --}}
    <div class="header-section">
        <div class="header-content">
            <h1 class="welcome-greeting">{{ $greeting }}, {{ Auth::user()->name }} 👋</h1>
            <p class="welcome-subtitle">Kelola kegiatan mengajar, buat materi terbaik, dan inspirasi peserta Anda.</p>
            
            <div class="status-toggle-wrapper">
                <span class="status-badge {{ $trainerStatusTone }}">
                    <i class="bi bi-circle-fill" style="font-size: 8px;"></i> Status: {{ $trainerStatusLabel }}
                </span>
                @if($trainerStatus !== 'suspended')
                  <form method="POST" id="statusToggleForm" action="{{ route('trainer.availability.toggle') }}" style="margin: 0;">
                    @csrf
                    <label class="status-switch" title="Geser untuk mengubah status keaktifan">
                      <input type="checkbox" {{ $trainerStatus === 'active' ? 'checked' : '' }} onchange="handleStatusToggle(this)">
                      <span class="status-switch-track" aria-hidden="true"></span>
                    </label>
                  </form>
                @endif
            </div>
        </div>
        <div style="position: relative; z-index: 2; opacity: 0.2;">
            <i class="bi bi-stars" style="font-size: 80px; color: #ffffff;"></i>
        </div>
    </div>

    {{-- ROW 1: 3 Columns --}}
    <div class="grid-3-col">
        {{-- Pendapatan Total --}}
        <div class="dash-card dash-revenue-card" style="display: flex; flex-direction: column;">
            <div style="position: absolute; top: 0; right: 0; width: 220px; height: 220px; background: radial-gradient(circle, rgba(255,255,255,0.12) 0%, rgba(255,255,255,0) 70%); border-radius: 50%; transform: translate(30%, -30%); z-index: 1;"></div>
            
            <div style="display: flex; justify-content: space-between; align-items: center; position: relative; z-index: 2;">
                <div class="revenue-title"><i class="bi bi-wallet2" style="margin-right: 8px; font-size: 16px;"></i> Pendapatan Total</div>
                <div style="background: rgba(255,255,255,0.15); padding: 4px 10px; border-radius: 20px; font-size: 10px; font-weight: 700; backdrop-filter: blur(4px); letter-spacing: 0.5px; border: 1px solid rgba(255,255,255,0.1);">BULAN INI</div>
            </div>
            
            <div class="revenue-amount" style="position: relative; z-index: 2;">Rp {{ number_format($thisMonthRevenue, 0, ',', '.') }}</div>
            
            <div class="revenue-growth" style="position: relative; z-index: 2;">
                @if($revenueGrowth >= 0)
                <span class="badge-up" style="background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border: 1px solid rgba(16, 185, 129, 0.3);"><i class="bi bi-graph-up-arrow"></i> +{{ number_format($revenueGrowth, 1) }}%</span> 
                @else
                <span class="badge-up" style="background: rgba(239, 68, 68, 0.2); color: #fca5a5; border: 1px solid rgba(239, 68, 68, 0.3);"><i class="bi bi-graph-down-arrow"></i> {{ number_format($revenueGrowth, 1) }}%</span>
                @endif
                <span style="opacity: 0.7; font-size: 11px;">vs bulan lalu</span>
            </div>
            
            <div class="chart-container">
                <svg viewBox="0 0 360 150" preserveAspectRatio="none" style="width:100%; height:100%; display:block;">
                    <defs>
                        <linearGradient id="chartArea" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="rgba(255,255,255,0.25)" />
                            <stop offset="100%" stop-color="rgba(255,255,255,0)" />
                        </linearGradient>
                    </defs>
                    
                    <!-- Vertical dashed lines -->
                    @foreach($svgPoints as $pt)
                    <line x1="{{ $pt['x'] }}" y1="{{ $pt['y'] }}" x2="{{ $pt['x'] }}" y2="150" stroke="rgba(255,255,255,0.15)" stroke-width="1.5" stroke-dasharray="3,4" />
                    @endforeach
                    
                    <!-- Area fill -->
                    <path d="{{ $areaD }}" fill="url(#chartArea)"/>
                    
                    <!-- Line stroke -->
                    <path d="{{ $pathD }}" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round"/>
                    
                    <!-- Nodes -->
                    @foreach($svgPoints as $pt)
                    <circle cx="{{ $pt['x'] }}" cy="{{ $pt['y'] }}" r="5" fill="#ffffff" />
                    @endforeach
                </svg>

                <!-- Tooltips -->
                @foreach($svgPoints as $pt)
                <div class="hover-target" style="left: {{ ($pt['x']/360)*100 }}%; top: {{ ($pt['y']/150)*100 }}%;"></div>
                <div class="graph-tooltip {{ $loop->first ? 'tooltip-first' : '' }} {{ $loop->last ? 'tooltip-last' : '' }}" style="left: {{ ($pt['x']/360)*100 }}%; top: {{ ($pt['y']/150)*100 }}%;">
                    <div class="tooltip-content">
                        <strong style="color:#cbd5e1;">{{ $pt['label'] }}</strong><br>
                        <span style="font-size:13px; font-weight:700;">Rp {{ number_format($pt['amount'], 0, ',', '.') }}</span>
                    </div>
                </div>
                @endforeach
            </div>
            
            <div class="revenue-btn-wrapper">
                <a href="{{ route('trainer.finance') }}" class="revenue-btn">Kelola Pendapatan</a>
            </div>
        </div>

        {{-- Ringkasan Aktivitas --}}
        <div class="dash-card">
            <div class="dash-card-header">
                <h3 class="dash-card-title">Ringkasan Aktivitas</h3>
            </div>
            <div class="activity-grid">
                <div class="activity-item">
                    <div class="activity-icon icon-purple"><i class="bi bi-calendar-event"></i></div>
                    <div class="activity-val">{{ $activeCourseCount + $activeEventCount }}</div>
                    <div class="activity-lbl">Kelas Berjalan</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon icon-green"><i class="bi bi-people"></i></div>
                    <div class="activity-val">{{ number_format($totalStudents) }}</div>
                    <div class="activity-lbl">Peserta Aktif</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon icon-orange"><i class="bi bi-file-earmark-text"></i></div>
                    <div class="activity-val">{{ $tugasMenungguCount }}</div>
                    <div class="activity-lbl">Tugas Menunggu</div>
                    <div class="activity-sub">Perlu diselesaikan</div>
                </div>
                <div class="activity-item">
                    <div class="activity-icon icon-blue"><i class="bi bi-star"></i></div>
                    <div class="activity-val">{{ number_format($averageRating, 1) }}</div>
                    <div class="activity-lbl">Rating Trainer</div>
                    <div class="activity-sub">Dari {{ number_format($totalRatings) }} ulasan</div>
                </div>
            </div>
            <div style="text-align: center;">
                <a href="{{ route('trainer.courses') }}" class="dash-card-link" style="justify-content: center;">Lihat Semua <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>

        {{-- E-Sertifikat Saya --}}
        <div class="dash-card" style="display: flex; align-items: center; justify-content: space-between; cursor: pointer; transition: transform 0.2s ease, box-shadow 0.2s ease;" onclick="window.location.href='{{ route('trainer.certificates.index') }}'" onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 10px 20px -5px rgba(0, 0, 0, 0.1)';" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='none';">
            <div style="width: 100%;">
                <div class="dash-card-header" style="margin-bottom: 16px;">
                    <h3 class="dash-card-title">E-Sertifikat Saya</h3>
                    <a href="{{ route('trainer.certificates.index') }}" class="btn-arrow-icon"><i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="cert-count-row">
                    <div class="cert-count">{{ $totalCertificates }}</div>
                    <div class="cert-subtitle">Sertifikat Diperoleh</div>
                </div>
                <div class="cert-list">
                    @forelse($teachingHistory as $history)
                    @php
                        $isCourseCert = $history->certifiable_type === \App\Models\Course::class;
                        $certifiable = $history->certifiable;
                        $activityTitle = $isCourseCert ? (optional($certifiable)->name ?? 'Kelas') : (optional($certifiable)->title ?? 'Event');
                        $roleMap = [
                            'SRT' => 'Peserta',
                            'MC' => 'MC',
                            'TRN' => 'Narasumber',
                            'PNT' => 'Panitia',
                            'CLB' => 'Kolaborator',
                            'MOD' => 'Moderator',
                            'GRD' => 'Kelulusan',
                            'SPV' => 'Supervisor/penilai',
                        ];
                        $roleCode = strtoupper(trim($history->type_code));
                        $roleLabel = $roleMap[$roleCode] ?? 'Instruktur';
                        $contextType = $isCourseCert ? 'course' : 'event';
                    @endphp
                    <div class="cert-item" onclick="window.location.href='{{ route('trainer.certificates.index') }}'">
                        <div style="width: 60px; height: 42.4px; position: relative; overflow: hidden; border: 1px solid #e2e8f0; border-radius: 4px; flex-shrink: 0; background: #fff;">
                            <div style="width: 29.7cm; height: 21cm; position: absolute; top: 0; left: 0; transform-origin: top left; transform: scale(0.05345); pointer-events: none; background: #fff;">
                                @include('trainer.certificates.certificate-pdf', [
                                    'is_preview' => true,
                                    'template' => $history->template ?? null,
                                    'context' => $contextType,
                                    'event' => !$isCourseCert ? $certifiable : null,
                                    'course' => $isCourseCert ? $certifiable : null,
                                    'user' => Auth::user(),
                                    'issuedAt' => $history->issued_at ?? now(),
                                    'certificateNumber' => $history->certificate_number ?? 'DRAFT',
                                    'logosBase64' => $history->logosBase64 ?? [],
                                    'signaturesBase64' => $history->signaturesBase64 ?? [],
                                    'signaturesData' => $history->signaturesData ?? [],
                                    'roleLabel' => $roleLabel
                                ])
                            </div>
                        </div>
                        <div class="cert-info">
                            <h5>{{ Str::limit($activityTitle, 25) }}</h5>
                            <p>Diterbitkan: {{ optional($history->issued_at)->format('d M Y') ?? '-' }}</p>
                        </div>
                    </div>
                    @empty
                    <div style="text-align: center; padding: 20px; color: #64748b; font-size: 13px;">Belum ada e-sertifikat.</div>
                    @endforelse
                </div>
            </div>
            <div style="text-align: center; margin-top: auto;">
                <a href="{{ route('trainer.certificates.index') }}" class="dash-card-link" style="justify-content: center;">Lihat Semua Sertifikat <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    {{-- ROW 2: 3 Columns --}}
    <div class="grid-3-col">
        {{-- Undangan Diterima --}}
        <div class="dash-card card-green-top">
            <div class="dash-card-header">
                <h3 class="dash-card-title">Undangan Diterima</h3>
                @if($pendingInvitationItems->count() > 0)
                <span class="badge-header-green">{{ $pendingInvitationItems->count() }} Baru</span>
                @endif
            </div>
            
            <div style="display: flex; flex-direction: column; flex: 1;">
                @forelse($pendingInvitationItems->take(2) as $invite)
                @php
                    $inviteEntityType = method_exists($invite, 'effectiveEntityType') ? $invite->effectiveEntityType() : data_get($invite->data, 'entity_type', 'course');
                    $inviteTypeLabel = $inviteEntityType === 'event' ? 'Event' : 'Course';
                    $entityId = (int) data_get($invite->data, 'entity_id', 0);
                    $entityDate = '-';
                    $entityTime = '-';
                    $entityLocation = '-';
                    
                    if ($inviteEntityType === 'event') {
                        $eventObj = \App\Models\Event::find($entityId);
                        if ($eventObj) {
                            $entityDate = $eventObj->event_date ? $eventObj->event_date->format('d M Y') : 'Jadwal Menyusul';
                            $entityTime = $eventObj->event_time ? \Carbon\Carbon::parse($eventObj->event_time)->format('H:i') : '-';
                            $entityLocation = $eventObj->location ?: ($eventObj->is_online ? 'Online (Virtual)' : '-');
                        }
                    } else {
                        $courseObj = \App\Models\Course::find($entityId);
                        if ($courseObj) {
                             $entityDate = $courseObj->created_at ? $courseObj->created_at->format('d M Y') : '-';
                             $entityLocation = 'Platform LMS';
                        }
                    }
                @endphp
                <div class="invite-item" onclick="openSchemeSelectionModal({{ $invite->id }}, '{{ addslashes($invite->title) }}', '{{ $inviteEntityType }}')" style="cursor:pointer;">
                    <div class="invite-icon-box {{ $inviteEntityType === 'event' ? 'box-purple' : 'box-green' }}">
                        <i class="bi {{ $inviteEntityType === 'event' ? 'bi-person-video3' : 'bi-file-text' }}"></i>
                    </div>
                    <div class="invite-content">
                        <span class="tag-badge {{ $inviteEntityType === 'event' ? 'tag-event' : 'tag-course' }}">{{ $inviteTypeLabel }}</span>
                        <h4 class="invite-title">{{ Str::limit($invite->title, 35) }}</h4>
                        <div class="invite-meta-row">
                            <span>Penyelenggara: idSpora</span>
                            <span>{{ $entityDate }}</span>
                        </div>
                        <div class="invite-meta-icons">
                            @if($entityTime !== '-')
                            <span><i class="bi bi-clock"></i> {{ $entityTime }}</span>
                            @endif
                            @if($entityLocation !== '-')
                            <span><i class="bi bi-geo-alt"></i> {{ Str::limit($entityLocation, 20) }}</span>
                            @endif
                        </div>
                        <div class="invite-desc">
                            {{ $inviteEntityType === 'event' ? 'Anda diundang sebagai pembicara untuk sesi ' . Str::limit($invite->title, 20) . '.' : 'Anda diundang sebagai pengajar untuk menyampaikan materi dalam kelas ini.' }}
                        </div>
                        <div style="display: flex; gap: 8px; margin-top: 12px;">
                            <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}" style="flex:1;">
                                @csrf
                                <input type="hidden" name="action" value="accept">
                                <button type="button" class="btn btn-sm" style="width:100%; font-size:12px; font-weight:600; border-radius:8px; background-color:#624388; border:none; color:white; padding:8px;" onclick="event.stopPropagation(); openSchemeSelectionModal({{ $invite->id }}, '{{ addslashes($invite->title) }}', '{{ $inviteEntityType }}')">Terima</button>
                            </form>
                            <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}" style="flex:1;">
                                @csrf
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-sm btn-outline-danger" style="width:100%; font-size:12px; font-weight:600; border-radius:8px; border:1px solid #ef4444; color:#ef4444; background:white; padding:8px;" onclick="event.stopPropagation(); return confirm('Apakah Anda yakin ingin menolak undangan ini?');">Tolak</button>
                            </form>
                        </div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 20px; color: #64748b; font-size: 13px;">Tidak ada undangan baru.</div>
                @endforelse
            </div>
            
            <div style="text-align: center; margin-top: auto; padding-top: 16px;">
                <a href="{{ route('trainer.notifications.index') }}" class="dash-card-link" style="justify-content: center; color: #624388;">Lihat Semua Undangan <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>

        {{-- Deadline Materi --}}
        <div class="dash-card card-orange-top">
            <div class="dash-card-header">
                <h3 class="dash-card-title">Deadline Materi</h3>
                @if($tugasMenungguCount > 0)
                <span class="badge-header-orange">{{ $tugasMenungguCount }} Akan Datang</span>
                @endif
            </div>
            
            <div style="display: flex; flex-direction: column; flex: 1;">
                @forelse(array_slice($tugasItems, 0, 3) as $task)
                <div class="deadline-item" onclick="window.location.href='{{ $task['url'] }}'">
                    <div class="deadline-icon"><i class="bi {{ $task['icon_class'] }}"></i></div>
                    <div style="flex: 1;">
                        @php
                            $rdl = $task['raw_days_left'] ?? null;
                            if ($rdl === null) {
                                $tagClass = 'tag-deadline-normal';
                                $tagText = 'TENGGAT BELUM DITENTUKAN';
                            } elseif ($rdl < 0) {
                                $tagClass = 'tag-deadline-danger';
                                $tagText = 'TERLAMBAT ' . abs($rdl) . ' HARI';
                            } elseif ($rdl == 0) {
                                $tagClass = 'tag-deadline-danger';
                                $tagText = 'HARI INI (SEGERA)';
                            } elseif ($rdl <= 3) {
                                $tagClass = 'tag-deadline-warning';
                                $tagText = $rdl . ' HARI LAGI';
                            } else {
                                $tagClass = 'tag-deadline-normal';
                                $tagText = $rdl . ' HARI LAGI';
                            }
                        @endphp
                        <span class="tag-deadline {{ $tagClass }}">{{ $tagText }}</span>
                        <div style="display: flex; justify-content: space-between; align-items: baseline;">
                            <h4 class="deadline-title">{{ Str::limit($task['title'], 25) }}</h4>
                            <span class="deadline-date">{{ $task['date_str'] }}</span>
                        </div>
                        <div class="deadline-desc">{{ $task['desc'] }}: {{ Str::limit($task['title'], 30) }}</div>
                    </div>
                </div>
                @empty
                <div style="text-align: center; padding: 20px; color: #64748b; font-size: 13px;">Tidak ada deadline.</div>
                @endforelse
            </div>
            
            <div style="text-align: center; margin-top: auto; padding-top: 16px;">
                <a href="{{ route('trainer.courses') }}" class="dash-card-link" style="justify-content: center; color: #f97316;">Lihat Semua Deadline <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>

        {{-- Rating & Ulasan --}}
        <div class="dash-card">
            <div class="dash-card-header">
                <h3 class="dash-card-title">Rating & Ulasan Peserta</h3>
                <a href="{{ route('trainer.feedback') }}" class="dash-card-link" style="font-size: 11px;">Lihat Semua</a>
            </div>
            
            <div class="rating-big-row">
                <span class="rating-big">{{ number_format($averageRating, 1) }}</span>
                <span class="rating-max">/ 5.0</span>
            </div>
            <div class="stars">★★★★★</div>
            <div class="rating-count">Dari {{ number_format($totalRatings) }} ulasan</div>
            
            <div class="rating-bars">
                <div class="rating-bar-row"><span>5 ★</span><div class="bar-track"><div class="bar-fill" style="width: {{ $ratingPercentages[5] ?? 0 }}%;"></div></div><span>{{ $ratingPercentages[5] ?? 0 }}% ({{ $ratingCounts[5] ?? 0 }})</span></div>
                <div class="rating-bar-row"><span>4 ★</span><div class="bar-track"><div class="bar-fill" style="width: {{ $ratingPercentages[4] ?? 0 }}%;"></div></div><span>{{ $ratingPercentages[4] ?? 0 }}% ({{ $ratingCounts[4] ?? 0 }})</span></div>
                <div class="rating-bar-row"><span>3 ★</span><div class="bar-track"><div class="bar-fill" style="width: {{ $ratingPercentages[3] ?? 0 }}%;"></div></div><span>{{ $ratingPercentages[3] ?? 0 }}% ({{ $ratingCounts[3] ?? 0 }})</span></div>
                <div class="rating-bar-row"><span>2 ★</span><div class="bar-track"><div class="bar-fill" style="width: {{ $ratingPercentages[2] ?? 0 }}%;"></div></div><span>{{ $ratingPercentages[2] ?? 0 }}% ({{ $ratingCounts[2] ?? 0 }})</span></div>
                <div class="rating-bar-row"><span>1 ★</span><div class="bar-track"><div class="bar-fill" style="width: {{ $ratingPercentages[1] ?? 0 }}%;"></div></div><span>{{ $ratingPercentages[1] ?? 0 }}% ({{ $ratingCounts[1] ?? 0 }})</span></div>
            </div>

            @forelse($feedbackItems->take(2) as $fb)
            @php
                $fbRating = (float)($fb->rating ?? 5);
                $fbName = $fb->user->name ?? ($fb->participant_name ?? 'Peserta');
                $fbComment = $fb->comment ?? ($fb->feedback ?? '-');
                $fbEvent = $fb->event->title ?? ($fb->event_title ?? 'Materi');
                $fbDate = $fb->created_at ? $fb->created_at->format('d M Y') : '-';
            @endphp
            <div class="review-box mt-2" style="margin-bottom:12px;">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($fbName) }}&background=f1f5f9" class="reviewer-avatar" alt="Avatar">
                <div class="review-content">
                    <div class="review-header">
                        <span class="reviewer-name">{{ $fbName }}</span>
                        <span class="review-stars">
                            @for($i=1; $i<=5; $i++)
                                {!! $i <= $fbRating ? '★' : '<span style="color:#cbd5e1">★</span>' !!}
                            @endfor
                            <strong style="color: #0f172a; margin-left: 4px;">{{ number_format($fbRating, 1) }}</strong>
                        </span>
                    </div>
                    <p class="review-text">{{ Str::limit($fbComment, 80) }}</p>
                    <div class="review-footer">
                        <span class="review-tag">{{ Str::limit($fbEvent, 15) }}</span>
                        <span class="review-date">{{ $fbDate }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div style="text-align: center; color: #64748b; font-size: 13px; padding: 20px;">Belum ada ulasan peserta.</div>
            @endforelse
            
            <div style="text-align: center; margin-top: auto; padding-top: 16px;">
                <a href="{{ route('trainer.feedback') }}" class="dash-card-link" style="justify-content: center;">Lihat Semua Ulasan <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>

    {{-- ROW 3: Kelas & Kalender --}}
    <div class="grid-2-col-wide">
        {{-- Kelas & Event Berjalan --}}
        <div>
            <div class="dash-card-header" style="margin-bottom: 16px;">
                <h3 class="dash-card-title">Kelas & Event Berjalan</h3>
                <a href="{{ route('trainer.courses') }}" class="dash-card-link">Lihat Semua</a>
            </div>
            
            <div class="ongoing-grid">
                @forelse(array_slice($kegiatanBerjalan, 0, 3) as $idx => $item)
                @php
                    $isCourse = $item['type'] === 'course';
                    $tagClass = $isCourse ? 'tag-course' : 'tag-event';
                    $icon = $isCourse ? 'bi-journal-richtext' : 'bi-camera-video';
                    $bg = $isCourse ? 'icon-gradient-blue' : 'icon-gradient-purple';
                @endphp
                <div class="ongoing-card" onclick="window.location.href='{{ $item['url'] }}'">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <span class="tag-badge {{ $tagClass }}">{{ $item['badge_label'] }}</span>
                        <div class="ongoing-icon-small {{ $bg }}"><i class="bi {{ $icon }}"></i></div>
                    </div>
                    
                    <h4 style="font-size: 16px; font-weight: 800; color: #0f172a; margin: 0 0 20px 0; line-height: 1.4; position: relative; z-index: 2; flex: 1;">{{ Str::limit($item['title'], 40) }}</h4>
                    
                    <div style="display: flex; align-items: flex-end; justify-content: space-between; position: relative; z-index: 2; margin-top: auto;">
                        <div style="display: flex; flex-direction: column; gap: 4px;">
                            <span style="font-size: 11px; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">Peserta Aktif</span>
                            <span style="font-size: 20px; font-weight: 800; color: #0f172a; display: flex; align-items: center; gap: 6px;"><i class="bi bi-people-fill" style="color: #94a3b8; font-size: 16px;"></i> {{ $item['count_value'] }}</span>
                        </div>
                        
                        <div class="btn-kelola-new">Kelola <i class="bi bi-arrow-right"></i></div>
                    </div>
                </div>
                @empty
                <div style="grid-column: span 3; text-align: center; padding: 40px; color: #64748b; background: #ffffff; border-radius: 16px; border: 1px solid #f1f5f9;">Belum ada kelas atau event.</div>
                @endforelse
            </div>
        </div>

        {{-- Kalender Kegiatan --}}
        <div id="calendar-widget" style="scroll-margin-top: 100px;">
            <div class="dash-card-header" style="margin-bottom: 16px;">
                <h3 class="dash-card-title">Kalender Kegiatan</h3>
                <a href="{{ route('trainer.events') }}" class="dash-card-link">Lihat Kalender</a>
            </div>
            
            <div class="dash-card" style="padding: 24px;">
                <div class="cal-header">
                    <button type="button" class="cal-nav" id="btn-prev-month"><i class="bi bi-chevron-left"></i></button>
                    <div class="cal-month" id="cal-month-title">Mei 2026</div>
                    <button type="button" class="cal-nav" id="btn-next-month"><i class="bi bi-chevron-right"></i></button>
                </div>
                
                <div class="cal-grid" id="cal-grid-dates">
                    <!-- Javascript will render the calendar grid here instantly -->
                </div>
                
                <div class="cal-legend">
                    <div class="legend-item"><div class="dot dot-green"></div> Kelas</div>
                    <div class="legend-item"><div class="dot dot-purple"></div> Event</div>
                    <div class="legend-item"><div class="dot dot-orange"></div> Deadline</div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
function handleStatusToggle(checkbox) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Konfirmasi Perubahan Status',
            text: checkbox.checked 
                ? "Apakah Anda yakin ingin mengubah status menjadi 'Siap Mengajar'? Anda akan mulai menerima penugasan atau tawaran kelas baru."
                : "Apakah Anda yakin ingin mengubah status menjadi 'Sedang Cuti / Pasif'? Anda tidak akan mendapatkan penawaran kelas baru selama status ini aktif.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: checkbox.checked ? '#10b981' : '#f97316',
            cancelButtonColor: '#cbd5e1',
            confirmButtonText: checkbox.checked ? 'Ya, Siap Mengajar!' : 'Ya, Istirahat Dulu',
            cancelButtonText: 'Batal',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Memproses...',
                    text: 'Mohon tunggu sebentar',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => { Swal.showLoading(); }
                });
                document.getElementById('statusToggleForm').submit();
            } else {
                checkbox.checked = !checkbox.checked;
            }
        });
    } else {
        if(confirm('Apakah Anda yakin ingin mengubah status keaktifan?')) {
            document.getElementById('statusToggleForm').submit();
        } else {
            checkbox.checked = !checkbox.checked;
        }
    }
}
</script>
@endpush
@endsection


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

      // Pure Client-side Calendar Navigation (Instant)
      const calMonthTitle = document.getElementById('cal-month-title');
      const calGridDates = document.getElementById('cal-grid-dates');
      const btnPrevMonth = document.getElementById('btn-prev-month');
      const btnNextMonth = document.getElementById('btn-next-month');
      
      const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      
      let currentCalDate = new Date();
      const globalEventLinks = @json($eventDetailLinks);
      
      function renderLocalCalendar(dateObj) {
          const year = dateObj.getFullYear();
          const month = dateObj.getMonth();
          
          calMonthTitle.innerText = monthNames[month] + ' ' + year;
          
          const firstDay = new Date(year, month, 1).getDay();
          const startOffset = (firstDay + 6) % 7; 
          
          const daysInMonth = new Date(year, month + 1, 0).getDate();
          const prevMonthDays = new Date(year, month, 0).getDate();
          
          let html = `
              <div class="cal-day-name">Sen</div><div class="cal-day-name">Sel</div><div class="cal-day-name">Rab</div>
              <div class="cal-day-name">Kam</div><div class="cal-day-name">Jum</div><div class="cal-day-name">Sab</div><div class="cal-day-name">Min</div>
          `;
          
          let dayCount = 1;
          let nextMonthDay = 1;
          const today = new Date();
          
          for (let i = 0; i < 35; i++) {
              if (i < startOffset) {
                  let prevDay = prevMonthDays - startOffset + i + 1;
                  html += `<a href="#" class="cal-date muted" onclick="event.preventDefault()">${prevDay}</a>`;
              } else if (dayCount <= daysInMonth) {
                  let classes = 'cal-date';
                  
                  let formattedDate = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(dayCount).padStart(2, '0');
                  
                  if (globalEventLinks[formattedDate]) {
                      classes += ' selected-blue';
                  }
                  
                  if (year === today.getFullYear() && month === today.getMonth() && dayCount === today.getDate()) {
                      classes += ' today-fill';
                  }
                  
                  let targetUrl = globalEventLinks[formattedDate] ? globalEventLinks[formattedDate] : `{{ route('trainer.events') }}?date=${formattedDate}`;
                  
                  html += `<a href="${targetUrl}" class="${classes}">${dayCount}</a>`;
                  dayCount++;
              } else {
                  html += `<a href="#" class="cal-date muted" onclick="event.preventDefault()">${nextMonthDay++}</a>`;
              }
          }
          calGridDates.innerHTML = html;
      }
      
      btnPrevMonth.addEventListener('click', function() {
          currentCalDate.setMonth(currentCalDate.getMonth() - 1);
          renderLocalCalendar(currentCalDate);
      });
      
      btnNextMonth.addEventListener('click', function() {
          currentCalDate.setMonth(currentCalDate.getMonth() + 1);
          renderLocalCalendar(currentCalDate);
      });
      
      renderLocalCalendar(currentCalDate);
    </script>
  @endpush
@include('trainer.partials.scheme-selection-modal')
