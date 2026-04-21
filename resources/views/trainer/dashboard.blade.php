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
  $pendingInvitationItems = collect($dashboardInvitations ?? [])->values();
  $activeAssignmentItems = collect($activeAssignments ?? [])->values();
  $revenueCourseItems = collect($revenueCourses ?? [])->values();
  $completedCourseItems = collect($completedCourses ?? [])->values();
  $feedbackItems = collect($recentEventFeedbacks ?? [])->values();
  $walletBalance = (float) ($trainer->wallet_balance ?? 0);
  $totalCompletedCourses = (int) data_get($trainerActivity, 'total_courses_completed', 0);
  $averageRating = (float) data_get($trainerActivity, 'average_rating', 0);
@endphp

@push('styles')
  @vite(['resources/css/trainer/dashboard.css'])
  <style>
    .dashboard-top-stack {
      display: flex;
      flex-direction: column;
      gap: var(--spacing-lg);
      margin-bottom: var(--spacing-xl);
    }

    .trainer-status-strip {
      position: static;
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      justify-content: space-between;
      gap: 12px 16px;
      margin-top: 12px;
      padding-top: 12px;
      border-top: 1px solid rgba(255, 255, 255, 0.16);
      border-bottom: 0;
      background: transparent;
    }

    .status-strip-left,
    .status-strip-right {
      display: flex;
      flex-wrap: wrap;
      align-items: center;
      gap: 10px;
    }

    .status-strip-right {
      margin-left: auto;
      justify-content: flex-end;
    }

    .status-chip {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: .01em;
      color: rgba(255, 255, 255, 0.88);
      background: rgba(0, 0, 0, 0.22);
      border: 1px solid rgba(255, 255, 255, 0.12);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.06);
    }

    .status-chip.is-active,
    .status-chip.is-passive,
    .status-chip.is-suspended,
    .status-chip.is-blue,
    .status-chip.is-green {
      background: rgba(0, 0, 0, 0.22);
      border-color: rgba(255, 255, 255, 0.12);
      color: rgba(255, 255, 255, 0.88);
    }

    .status-toggle-form {
      display: inline-flex;
      align-items: center;
      gap: 10px;
    }

    .status-switch {
      position: relative;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      user-select: none;
    }

    .status-switch input {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }

    .status-switch-track {
      width: 50px;
      height: 28px;
      border-radius: 999px;
      background: rgba(255, 255, 255, 0.24);
      border: 1px solid rgba(255, 255, 255, 0.2);
      position: relative;
      transition: background .2s ease;
    }

    .status-switch-track::after {
      content: '';
      position: absolute;
      top: 2px;
      left: 2px;
      width: 22px;
      height: 22px;
      border-radius: 50%;
      background: #fff;
      box-shadow: 0 2px 6px rgba(15, 23, 42, .2);
      transition: transform .2s ease;
    }

    .status-switch input:checked+.status-switch-track {
      background: rgba(250, 204, 21, 0.42);
      border-color: rgba(253, 224, 71, 0.48);
    }

    .status-switch input:checked+.status-switch-track::after {
      transform: translateX(22px);
    }

    .status-switch-label {
      font-size: 11px;
      font-weight: 600;
      color: rgba(255, 255, 255, 0.85);
    }

    .status-hint {
      margin: 0;
      font-size: 12px;
      color: #64748b;
    }

    .late-banner {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 14px 16px;
      border-radius: 18px;
      border: 1px solid transparent;
      line-height: 1.55;
    }

    .late-banner.level-1 {
      background: #fffbeb;
      border-color: #facc15;
      color: #854d0e;
    }

    .late-banner.level-2 {
      background: #fef2f2;
      border-color: #ef4444;
      color: #7f1d1d;
    }

    .late-banner-icon {
      flex-shrink: 0;
      width: 34px;
      height: 34px;
      border-radius: 50%;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      background: rgba(255, 255, 255, .55);
      font-size: 18px;
    }

    .panel-card {
      background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
      border: 1px solid rgba(221, 230, 242, 0.9);
      border-radius: 22px;
      box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
      overflow: hidden;
    }

    .panel-card-header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 16px;
      padding: 18px 20px 12px;
      border-bottom: 1px solid rgba(229, 237, 246, 0.9);
      background: transparent;
    }

    .panel-card-title {
      margin: 0;
      font-size: 15px;
      font-weight: 800;
      color: #0f172a;
      text-transform: uppercase;
      letter-spacing: .04em;
    }

    .panel-card-subtitle {
      margin: 4px 0 0;
      font-size: 12px;
      color: #64748b;
      line-height: 1.5;
    }

    .countdown-timer {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 7px 12px;
      border-radius: 999px;
      font-size: 12px;
      font-weight: 800;
      background: #eef2ff;
      color: #1b1763;
      border: 1px solid #dbe4ff;
    }

    .countdown-timer.is-urgent {
      background: #fef2f2;
      color: #991b1b;
      border-color: #fecaca;
    }

    .panel-card-body {
      padding: 18px 20px 20px;
    }

    .invitation-list,
    .assignment-list,
    .feedback-list,
    .history-list {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    .invitation-item,
    .assignment-item,
    .feedback-item,
    .history-item {
      border: 1px solid rgba(219, 229, 241, 0.8);
      border-radius: 18px;
      padding: 16px;
      background: rgba(255, 255, 255, 0.82);
      box-shadow: 0 8px 16px rgba(15, 23, 42, 0.04);
    }

    .assignment-item {
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto;
      gap: 14px 18px;
      align-items: center;
      padding: 16px 0 16px;
      background: transparent;
      border: 0;
      border-bottom: 1px solid rgba(229, 237, 246, 0.9);
      border-radius: 0;
      box-shadow: none;
    }

    .assignment-item:first-child {
      padding-top: 6px;
    }

    .assignment-item:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .assignment-main {
      display: flex;
      gap: 12px;
      align-items: flex-start;
      min-width: 0;
    }

    .assignment-mark {
      width: 36px;
      height: 36px;
      border-radius: 12px;
      background: linear-gradient(135deg, #1b1763 0%, #3f3cbb 100%);
      color: #fff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      box-shadow: 0 8px 16px rgba(27, 23, 99, 0.12);
      font-size: 16px;
    }

    .assignment-copy {
      min-width: 0;
      flex: 1;
    }

    .assignment-title {
      margin: 0;
      font-size: 16px;
      line-height: 1.3;
      letter-spacing: -0.02em;
      color: #0f172a;
      font-weight: 800;
    }

    .assignment-meta {
      margin: 4px 0 0;
      font-size: 12px;
      line-height: 1.45;
      color: #526078;
    }

    .assignment-side {
      display: flex;
      flex-direction: column;
      align-items: flex-end;
      gap: 8px;
      min-width: 210px;
    }

    .assignment-topline {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 8px;
      flex-wrap: wrap;
    }

    .assignment-side .countdown-timer {
      justify-content: center;
      align-self: flex-end;
      padding: 6px 10px;
      font-size: 11px;
    }

    .assignment-side .assignment-status-line {
      justify-content: flex-end;
      margin-top: 0;
      max-width: 220px;
    }

    .assignment-status-line {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
    }

    .assignment-status-line .mini-pill {
      padding: 6px 10px;
      font-size: 10px;
    }

    .assignment-actions {
      grid-column: auto;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
      padding-top: 2px;
    }

    .assignment-actions .action-chip {
      border-radius: 999px;
      padding: 10px 16px;
      font-size: 12px;
      letter-spacing: .02em;
    }

    .assignment-actions .action-chip.primary {
      box-shadow: 0 8px 16px rgba(27, 23, 99, 0.10);
    }

    .assignment-actions .action-chip.success,
    .assignment-actions .action-chip.warning {
      background: #fff;
    }

    .invitation-item.is-unread {
      border-color: #c7d7ff;
      box-shadow: 0 12px 22px rgba(37, 99, 235, 0.08);
    }

    .entry-head {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      align-items: flex-start;
      margin-bottom: 10px;
    }

    .entry-title {
      margin: 0;
      font-size: 15px;
      font-weight: 800;
      color: #0f172a;
      line-height: 1.4;
    }

    .entry-meta {
      margin: 6px 0 0;
      font-size: 12px;
      color: #64748b;
      line-height: 1.5;
    }

    .entry-tags {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-bottom: 10px;
    }

    .mini-pill {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 6px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 800;
      border: 1px solid transparent;
    }

    .mini-pill.is-blue {
      background: #eff6ff;
      color: #1d4ed8;
      border-color: #bfdbfe;
    }

    .mini-pill.is-green {
      background: #ecfdf5;
      color: #047857;
      border-color: #a7f3d0;
    }

    .mini-pill.is-yellow {
      background: #fffbeb;
      color: #a16207;
      border-color: #fde68a;
    }

    .mini-pill.is-red {
      background: #fef2f2;
      color: #991b1b;
      border-color: #fecaca;
    }

    .entry-footer {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
      justify-content: space-between;
      margin-top: 12px;
    }

    .entry-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      margin-top: 12px;
    }

    .action-chip,
    .reply-btn,
    .secondary-link {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 8px 12px;
      border-radius: 12px;
      border: 1px solid #dbe5f1;
      background: #fff;
      color: #1b1763;
      font-size: 12px;
      font-weight: 800;
      text-decoration: none;
      cursor: pointer;
    }

    .action-chip.primary,
    .reply-btn.primary {
      background: #1b1763;
      color: #fff;
      border-color: #1b1763;
    }

    .action-chip.warning {
      background: #fff7ed;
      color: #9a3412;
      border-color: #fed7aa;
    }

    .action-chip.success {
      background: #ecfdf5;
      color: #047857;
      border-color: #a7f3d0;
    }

    .action-chip:hover,
    .reply-btn:hover,
    .secondary-link:hover {
      transform: translateY(-1px);
      box-shadow: 0 8px 16px rgba(15, 23, 42, 0.08);
    }

    .table-wrap {
      overflow-x: auto;
    }

    .revenue-table {
      width: 100%;
      border-collapse: collapse;
    }

    .revenue-table th {
      text-align: left;
      font-size: 11px;
      letter-spacing: .06em;
      text-transform: uppercase;
      color: #64748b;
      padding: 0 12px 10px;
    }

    .revenue-table td {
      padding: 14px 12px;
      border-top: 1px solid #edf2f7;
      border-bottom: 1px solid #edf2f7;
      background: transparent;
      font-size: 13px;
      color: #0f172a;
      vertical-align: top;
    }

    .revenue-table tr td:first-child {
      border-left: 1px solid #edf2f7;
    }

    .revenue-table tr td:last-child {
      border-right: 1px solid #edf2f7;
    }

    .money-stat {
      display: inline-flex;
      align-items: baseline;
      gap: 8px;
      flex-wrap: wrap;
    }

    .money-stat strong {
      font-size: 30px;
      color: #1b1763;
      line-height: 1;
    }

    .money-stat span {
      font-size: 12px;
      color: #64748b;
      font-weight: 700;
    }

    .reply-modal {
      position: fixed;
      inset: 0;
      z-index: 2000;
      display: none;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: rgba(2, 6, 23, 0.55);
    }

    .reply-modal.is-open {
      display: flex;
    }

    .reply-modal-card {
      width: min(620px, 100%);
      border-radius: 20px;
      background: #fff;
      overflow: hidden;
      box-shadow: 0 24px 64px rgba(15, 23, 42, 0.28);
    }

    .reply-modal-header,
    .reply-modal-footer {
      padding: 16px 18px;
      background: #f8fbff;
    }

    .reply-modal-header {
      border-bottom: 1px solid #e5edf6;
    }

    .reply-modal-footer {
      border-top: 1px solid #e5edf6;
      display: flex;
      justify-content: flex-end;
      gap: 10px;
    }

    .reply-modal-body {
      padding: 18px;
    }

    .reply-modal-textarea {
      width: 100%;
      min-height: 150px;
      border-radius: 14px;
      border: 1px solid #cbd5e1;
      padding: 12px 14px;
      font-size: 14px;
      line-height: 1.6;
      resize: vertical;
      outline: none;
    }

    .reply-modal-textarea:focus {
      border-color: #1b1763;
      box-shadow: 0 0 0 3px rgba(27, 23, 99, 0.12);
    }

    .reply-modal-title {
      margin: 0;
      font-size: 17px;
      font-weight: 800;
      color: #0f172a;
    }

    .reply-modal-context {
      margin: 6px 0 0;
      font-size: 12px;
      color: #64748b;
      line-height: 1.5;
    }

    .empty-note {
      padding: 18px;
      border-radius: 16px;
      border: 1px dashed #cbd5e1;
      background: #f8fafc;
      color: #64748b;
      font-size: 13px;
      line-height: 1.55;
    }

    .hero-card {
      border-radius: 28px;
      box-shadow: 0 14px 30px rgba(15, 23, 42, 0.10);
    }

    .metrics-section {
      display: grid;
      grid-template-columns: repeat(4, minmax(0, 1fr));
      align-items: stretch;
      gap: 14px;
    }

    .metric-card {
      min-width: 0;
    }

    .metric-card,
    .studio-pipeline,
    .invitation-card,
    .teaching-history-card {
      border-radius: 20px;
      box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
    }

    .section-header {
      margin-bottom: 14px;
      padding-bottom: 10px;
    }

    .section-title {
      font-size: 13px;
      letter-spacing: .08em;
      color: #0f172a;
    }

    .section-summary {
      color: #526078;
      font-size: 13px;
    }

    .hero-description {
      max-width: 720px;
      font-size: 15px;
    }

    .hero-heading {
      letter-spacing: -0.03em;
    }

    .hero-buttons .btn-primary,
    .hero-buttons .btn-secondary {
      border-radius: 14px;
    }

    @media (max-width: 1024px) {
      .dashboard-grid {
        grid-template-columns: 1fr;
      }

      .metrics-section {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 720px) {

      .entry-head,
      .panel-card-header {
        flex-direction: column;
      }

      .assignment-item {
        grid-template-columns: 1fr;
      }

      .assignment-side {
        align-items: flex-start;
        min-width: 0;
      }

      .assignment-topline,
      .assignment-side .assignment-status-line {
        justify-content: flex-start;
      }

      .assignment-side .countdown-timer {
        align-self: flex-start;
      }

      .revenue-table th,
      .revenue-table td {
        padding: 10px;
      }
    }

    @media (max-width: 560px) {
      .metrics-section {
        grid-template-columns: 1fr;
      }

      .status-strip-left,
      .status-strip-right {
        width: 100%;
      }

      .status-toggle-form {
        width: 100%;
        justify-content: space-between;
      }
    }
  </style>
@endpush

@section('content')

  {{-- HERO SECTION --}}
  <div class="hero-card">
    <div class="hero-decoration-1"></div>
    <div class="hero-decoration-2"></div>
    <div class="hero-content">
      <div class="hero-breadcrumb">
        Home -
        <span class="hero-breadcrumb-active">Trainer Dashboard</span>
      </div>
      <h1 class="hero-heading">
        {{ Auth::user()->profession ?? 'Instruktur' }},
        <span class="hero-heading-name">{{ Auth::user()->name }}.</span>
      </h1>
      <p class="hero-description">
        Tahun ajaran ini Anda mengelola
        <strong>{{ $totalCourses }} Kelas Aktif</strong> dengan total jaringan
        <strong>{{ number_format($totalStudents) }} pelajar</strong>.
      </p>
      <div class="trainer-status-strip">
        <div class="status-strip-left">
          <span class="status-chip {{ $trainerStatusTone }}">
            Status Akun: {{ $trainerStatusLabel }}
          </span>
          @if($trainerStatus === 'suspended')
            <span class="status-chip is-suspended">Hanya admin yang dapat mengaktifkan kembali</span>
          @else
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

        <div class="status-strip-right">
          <span class="status-chip is-blue">{{ $unreadInvitationCount }} Undangan Baru</span>
          <span class="status-chip is-green">Rapor Telat: {{ $lateUploads }}</span>
        </div>
      </div>

      @if($lateBanner)
        <div class="late-banner {{ $lateBannerClass }}">
          <div class="late-banner-icon">{{ $lateUploads === 2 ? '🚨' : '⚠️' }}</div>
          <div>
            <strong>Notifikasi Pelanggaran</strong><br>
            {{ $lateBanner }}
          </div>
        </div>
      @endif

      <div class="hero-buttons">
        <a href="{{ route('trainer.events') }}" class="btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
            <path
              d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
          </svg>
          LIHAT JADWAL
        </a>
        <a href="{{ route('trainer.courses') }}" class="btn-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
            <path
              d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783" />
          </svg>
          DAFTAR KELAS
        </a>
      </div>
    </div>
  </div>

  <div class="dashboard-grid">

    {{-- KOLOM KIRI --}}
    <div class="grid-column">

      {{-- METRICS SECTION --}}
      <div class="metrics-section">
        <div class="metric-card">
          <p class="metric-label">Course/Event Selesai</p>
          <div class="metric-value-row">
            <h2 class="metric-value">{{ number_format((int) data_get($trainerActivity, 'total_courses_completed', 0)) }}
            </h2>
            <span class="metric-badge">Final</span>
          </div>
        </div>

        <div class="metric-card">
          <p class="metric-label">Rata-rata Rating</p>
          <div class="metric-value-row">
            <h2 class="metric-value">{{ number_format((float) data_get($trainerActivity, 'average_rating', 0), 1) }}</h2>
            <span class="metric-badge">Bintang</span>
          </div>
        </div>

        <div class="metric-card">
          <p class="metric-label">Pelanggaran Telat</p>
          <div class="metric-value-row">
            <h2 class="metric-value">{{ number_format((int) data_get($trainerActivity, 'late_uploads', 0)) }}</h2>
            <span
              class="metric-change {{ (int) data_get($trainerActivity, 'late_uploads', 0) > 0 ? 'is-negative' : 'is-positive' }}">
              {{ (int) data_get($trainerActivity, 'late_uploads', 0) > 0 ? 'Strike' : 'Aman' }}
            </span>
          </div>
        </div>

        <div class="metric-card">
          <p class="metric-label">Undangan Kedaluwarsa</p>
          <div class="metric-value-row">
            <h2 class="metric-value">
              {{ number_format((int) data_get($trainerActivity, 'consecutive_expired_invitations', 0)) }}
            </h2>
            <span
              class="metric-change {{ (int) data_get($trainerActivity, 'consecutive_expired_invitations', 0) > 0 ? 'is-negative' : 'is-positive' }}">
              {{ (int) data_get($trainerActivity, 'consecutive_expired_invitations', 0) > 0 ? 'Perlu Perhatian' : 'Terkendali' }}
            </span>
          </div>
        </div>
      </div>

      {{-- KELAS BERJALAN --}}
      <div class="studio-pipeline">
        <div class="section-header">
          <h3 class="section-title">
            Kelas Berjalan
          </h3>
          <a href="{{ route('trainer.events') }}" class="section-link">Lihat Semua Event</a>
        </div>

        <p class="section-summary">
          Penugasan aktif yang perlu Anda kerjakan sekarang.
        </p>

        <div class="assignment-list">
          @forelse($activeAssignmentItems->take(3) as $assignmentRow)
            @php
              $assignment = $assignmentRow['assignment'];
              $event = $assignment->event;
              $eventTitle = (string) ($assignmentRow['event_title'] ?: 'Event Tanpa Judul');
              $eventDate = (string) ($assignmentRow['event_date'] ?: 'Jadwal menyusul');
              $studioUrl = $event ? route('trainer.events.studio', $event->id) : route('trainer.events');
              $deadline = $assignment->sla_upload_deadline;
              $deadlineIso = $deadline ? $deadline->toIso8601String() : null;
              $materialStatus = strtolower((string) ($assignment->material_status ?? 'pending'));
              $approvalLabel = match ($materialStatus) {
                'approved' => 'Disetujui',
                'rejected' => 'Revisi',
                'pending_review' => 'Menunggu Review Admin',
                default => 'Belum Upload',
              };
              $approvalClass = match ($materialStatus) {
                'approved' => 'is-green',
                'rejected' => 'is-red',
                default => 'is-yellow',
              };
              $schemePercent = (int) ($assignmentRow['scheme_percent'] ?? 0);
              $activeParticipantsCount = (int) ($assignmentRow['active_participants_count'] ?? 0);
              $feePerParticipant = (float) ($assignmentRow['fee_per_participant'] ?? 0);
              $estimatedFee = (float) ($assignmentRow['estimated_fee'] ?? 0);
              $assignmentIcon = match ($schemePercent) {
                35 => 'bi-journal-text',
                25 => 'bi-camera-video',
                default => 'bi-play-circle',
              };
            @endphp
            <div class="assignment-item">
              <div class="assignment-main">
                <div class="assignment-mark">
                  <i class="bi {{ $assignmentIcon }}"></i>
                </div>
                <div class="assignment-copy">
                  <h4 class="assignment-title">{{ $eventTitle }}</h4>
                  <p class="assignment-meta">
                    {{ $eventDate }} • {{ $assignmentRow['scheme_label'] }}
                  </p>
                  <p class="assignment-meta">
                    Fee/Peserta Rp {{ number_format($feePerParticipant, 0, ',', '.') }}
                    • Peserta Aktif {{ number_format($activeParticipantsCount) }}
                    • Estimasi Rp {{ number_format($estimatedFee, 0, ',', '.') }}
                  </p>
                </div>
              </div>

              <div class="assignment-side">
                <div class="assignment-topline">
                  <div class="assignment-status-line">
                    <span class="mini-pill is-blue">{{ $assignmentRow['scheme_percent'] }}%</span>
                    <span class="mini-pill {{ $approvalClass }}">{{ $approvalLabel }}</span>
                    @if($materialStatus === 'rejected' && !empty($assignment->material_rejection_reason))
                      <span
                        class="mini-pill is-red">{{ Str::limit((string) $assignment->material_rejection_reason, 56) }}</span>
                    @endif
                  </div>

                  @if($deadlineIso)
                    <span class="countdown-timer js-countdown" data-deadline="{{ $deadlineIso }}"
                      data-mode="72h">Menghitung...</span>
                  @endif
                </div>

                <div class="assignment-actions">
                  <a href="{{ $studioUrl }}" class="action-chip primary">Buka Studio</a>
                </div>
              </div>
            </div>
          @empty
            <div class="empty-note">
              Belum ada kelas berjalan. Setelah Anda menerima undangan dan menyetujui e-agreement, penugasan aktif akan
              muncul di sini.
            </div>
          @endforelse
        </div>
      </div>

      <div class="teaching-history-card">
        <div class="section-header teaching-history-header">
          <h3 class="section-title">
            Riwayat Mengajar & E-Sertifikat
          </h3>
          <a href="{{ route('trainer.certificates.index') }}" class="section-link">Lihat Semua</a>
        </div>

        <div class="teaching-history-list">
          @forelse($teachingHistory as $history)
            @php
              $isCourseCert = $history->certifiable_type === \App\Models\Course::class;
              $certifiable = $history->certifiable;
              $activityTitle = $isCourseCert
                ? (optional($certifiable)->name ?? 'Kelas')
                : (optional($certifiable)->title ?? 'Event');
            @endphp
            <div class="teaching-history-item">
              <div class="teaching-history-content">
                <h4 class="teaching-history-title">{{ Str::limit($activityTitle, 52) }}</h4>
                <p class="teaching-history-meta">
                  {{ $isCourseCert ? 'Course Selesai' : 'Event Selesai' }}
                  • Sertifikat terbit {{ optional($history->issued_at)->format('d M Y') ?? '-' }}
                </p>
              </div>

              @if(!empty($history->file_path) && $certifiable)
                    <a href="{{ $isCourseCert
                ? route('trainer.certificates.courses.download', $certifiable)
                : route('trainer.certificates.events.download', $certifiable) }}" class="history-download-btn">
                      Download Sertifikat
                    </a>
              @endif
            </div>
          @empty
            <div class="focus-empty">Belum ada riwayat sertifikat mengajar yang diterbitkan.</div>
          @endforelse
        </div>

        <div class="focus-grid" style="margin-top: 18px;">
          <div class="focus-panel focus-panel-courses">
            <div class="focus-panel-header">
              <div>
                <h4 class="focus-panel-title">Riwayat Kelas Selesai</h4>
                <p class="focus-panel-subtitle">Kelas yang sudah beres dan siap Anda jadikan portofolio.</p>
              </div>
              <span class="focus-panel-count">{{ $completedCourseItems->count() }}</span>
            </div>

            <div class="focus-list">
              @forelse($completedCourseItems as $course)
                <div class="focus-item">
                  <div>
                    <h5 class="focus-item-title" style="margin:0; font-size:14px; color:#0f172a; font-weight:800;">
                      {{ Str::limit($course->name, 42) }}
                    </h5>
                    <p class="focus-item-desc" style="margin:4px 0 0; font-size:12px; color:#64748b; line-height:1.45;">
                      {{ ucfirst((string) ($course->status ?? 'approved')) }} • Peserta aktif
                      {{ number_format((int) ($course->active_students_count ?? 0)) }} • Rating
                      {{ number_format((float) ($course->reviews_avg_rating ?? 0), 1) }}
                    </p>
                  </div>
                  <span class="mini-pill is-green">Selesai</span>
                </div>
              @empty
                <div class="empty-note">Belum ada kelas selesai yang tercatat di dashboard ini.</div>
              @endforelse
            </div>
          </div>

          <div class="focus-panel focus-panel-events">
            <div class="focus-panel-header">
              <div>
                <h4 class="focus-panel-title">Ulasan Peserta</h4>
                <p class="focus-panel-subtitle">Baca komentar peserta dan balas langsung dari dashboard.</p>
              </div>
              <span class="focus-panel-count">{{ $feedbackItems->count() }}</span>
            </div>

            <div class="feedback-list">
              @forelse($feedbackItems as $feedback)
                <div class="feedback-item">
                  <div class="entry-head">
                    <div>
                      <h4 class="entry-title">{{ $feedback->user->name ?? 'Peserta' }}</h4>
                      <p class="entry-meta">
                        {{ optional($feedback->event)->title ?? 'Event' }} •
                        {{ optional($feedback->created_at)->format('d M Y H:i') }}
                      </p>
                    </div>
                    <span class="mini-pill is-yellow">{{ number_format((float) ($feedback->rating ?? 0), 1) }} ★</span>
                  </div>

                  <p class="entry-meta" style="font-size:13px; color:#334155; margin-top:0;">
                    {{ $feedback->comment ?: 'Tidak ada komentar tertulis.' }}
                  </p>

                  @if($feedback->replies->isNotEmpty())
                    <div class="empty-note" style="margin-top: 12px; padding: 12px 14px;">
                      Balasan terakhir: {{ Str::limit((string) data_get($feedback->replies->last(), 'response'), 120) }}
                    </div>
                  @endif

                  <div class="entry-actions">
                    <button type="button" class="reply-btn primary"
                      onclick="openFeedbackReplyModal({{ $feedback->id }}, @js($feedback->user->name ?? 'Peserta'), @js(optional($feedback->event)->title ?? 'Event'))">
                      Balas Komentar
                    </button>
                  </div>
                </div>
              @empty
                <div class="empty-note">Belum ada ulasan peserta yang masuk.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>

    </div>

    {{-- KOLOM KANAN --}}
    <div class="grid-column">

      {{-- INVITATION CARD --}}
      <div class="invitation-card">
        <div class="invitation-header">
          <div class="invitation-icon">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M13 2L3 14h8l-1 8 10-12h-8l1-8z" fill="currentColor" />
            </svg>
          </div>
          <div>
            <p class="invitation-badge">Pending Invitations</p>
            <h3 class="invitation-title">Tugas Baru dari Admin</h3>
            <p class="invitation-meta">
              {{ $pendingInvitationItems->count() }} undangan aktif • {{ $unreadInvitationCount }} belum dibaca
            </p>
          </div>
        </div>
        <div class="invitation-list">
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
              $inviteStatusLabel = match ($inviteStatus) {
                'accepted' => 'Diterima',
                'rejected' => 'Ditolak',
                'expired' => 'Expired',
                default => 'Menunggu Tindakan Anda',
              };
            @endphp
            <div
              class="invitation-item {{ is_null($invite->read_at) ? 'is-unread' : '' }} {{ $inviteEntityType === 'event' ? 'is-event' : 'is-course' }}">
              <div class="invitation-item-top">
                <h4 class="invitation-item-title">{{ $invite->title }}</h4>
                <span
                  class="invitation-type-label {{ $inviteEntityType === 'event' ? 'is-event' : 'is-course' }}">{{ $inviteTypeLabel }}</span>
              </div>
              <div class="invitation-item-summary">
                <span class="invitation-summary-text {{ $isOverdue && $inviteStatus === 'pending' ? 'is-overdue' : '' }}">
                  {{ $inviteStatusLabel }}
                </span>
              </div>
              @if($dueDate)
                <p class="invitation-deadline {{ $isOverdue && $inviteStatus === 'pending' ? 'is-overdue' : '' }}">
                  Deadline: {{ $dueDate->format('d M Y H:i') }}
                  @if($isOverdue && $inviteStatus === 'pending')
                    • SLA terlewat
                  @endif
                </p>
              @endif
              <div class="invitation-item-footer">
                <span class="invitation-item-time">{{ $invite->created_at?->diffForHumans() }}</span>
                @if(!empty($inviteUrl))
                  <a href="{{ route('trainer.notifications.open', $invite->id) }}" class="invitation-item-link">Buka</a>
                @endif
              </div>
              @if($inviteStatus === 'pending' && $invite->type !== 'event_invitation')
                <div class="invitation-actions">
                  @if($inviteEntityType === 'course')
                    <button type="button" class="action-chip primary"
                      onclick="openSchemeSelectionModal({{ $invite->id }}, '{{ addslashes($invite->title) }}', '{{ $inviteEntityType }}')">
                      Terima
                    </button>
                  @else
                    <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}"
                      class="js-invitation-response-form">
                      @csrf
                      <input type="hidden" name="decision" value="accept">
                      <input type="hidden" name="e_agreement" value="1">
                      <button type="submit" class="action-chip primary">Terima</button>
                    </form>
                  @endif
                  <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}"
                    class="js-invitation-response-form">
                    @csrf
                    <input type="hidden" name="decision" value="reject">
                    <button type="submit" class="action-chip warning">Tolak</button>
                  </form>
                </div>
              @endif
            </div>
          @empty
            <div class="invitation-empty">
              Belum ada undangan trainer saat ini.
            </div>
          @endforelse
        </div>
      </div>

      <div class="panel-card">
        <div class="panel-card-header">
          <div>
            <h3 class="panel-card-title">Revenue Sharing</h3>
            <p class="panel-card-subtitle">
              Transparansi pendapatan berdasarkan peserta aktif bulan ini dan skema bagi hasil yang Anda pilih.
            </p>
          </div>
          <span class="mini-pill is-green">Saldo Saat Ini</span>
        </div>

        <div class="panel-card-body">
          <div class="money-stat" style="margin-bottom: 16px;">
            <strong>Rp {{ number_format($walletBalance, 0, ',', '.') }}</strong>
            <span>Belum ditarik</span>
          </div>

          <div class="table-wrap">
            <table class="revenue-table">
              <thead>
                <tr>
                  <th>Nama Course</th>
                  <th>Peserta Aktif Bulan Ini</th>
                  <th>Harga Course</th>
                  <th>Skema</th>
                  <th>Estimasi Pendapatan</th>
                </tr>
              </thead>
              <tbody>
                @forelse($revenueCourseItems as $row)
                  <tr>
                    <td>
                      <strong>{{ Str::limit($row['course_name'], 34) }}</strong><br>
                      <span style="font-size: 12px; color: #64748b;">Rating
                        {{ number_format((float) ($row['rating'] ?? 0), 1) }}</span>
                    </td>
                    <td>{{ number_format((int) ($row['active_students_count'] ?? 0)) }}</td>
                    <td>Rp {{ number_format((float) ($row['price'] ?? 0), 0, ',', '.') }}</td>
                    <td>{{ (int) ($row['scheme_percent'] ?? 0) }}%</td>
                    <td>Rp {{ number_format((float) ($row['estimated_revenue'] ?? 0), 0, ',', '.') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5">
                      <div class="empty-note">Belum ada data revenue course yang dapat ditampilkan.</div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

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
        const countdownElements = document.querySelectorAll('.js-countdown');
        const replyModal = document.getElementById('feedbackReplyModal');
        const replyForm = document.getElementById('feedbackReplyForm');
        const replyInput = document.getElementById('replyResponseInput');
        const replyContext = document.getElementById('replyModalContext');
        const replyFeedbackId = document.getElementById('replyFeedbackId');
        const replyResponseField = document.getElementById('replyResponseField');
        const replyCancel = document.getElementById('replyModalCancel');
        const replySubmit = document.getElementById('replyModalSubmit');
        const feedbackReplyStoreUrl = @json(route('trainer.feedback.reply.store'));

        function formatRemaining(deadline, mode) {
          const now = new Date().getTime();
          const diff = deadline.getTime() - now;
          const totalSeconds = Math.max(0, Math.floor(diff / 1000));
          const hours = Math.floor(totalSeconds / 3600);
          const minutes = Math.floor((totalSeconds % 3600) / 60);
          const seconds = totalSeconds % 60;

          const urgent = mode === '24h' ? hours < 3 : hours < 6;
          const text = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

          return { text, urgent };
        }

        function refreshCountdowns() {
          countdownElements.forEach((element) => {
            const deadline = element.dataset.deadline ? new Date(element.dataset.deadline) : null;
            if (!deadline || Number.isNaN(deadline.getTime())) {
              return;
            }

            const mode = element.dataset.mode || '24h';
            const remaining = formatRemaining(deadline, mode);
            element.textContent = mode === '72h' ? `SLA ${remaining.text}` : `Sisa ${remaining.text}`;
            element.classList.toggle('is-urgent', remaining.urgent || deadline.getTime() <= Date.now());
          });
        }

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

        refreshCountdowns();
        setInterval(refreshCountdowns, 1000);
      });
    </script>
  @endpush

@endsection