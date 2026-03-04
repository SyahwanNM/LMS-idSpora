@extends('layouts.trainer')

@section('title', 'Dashboard Trainer')

@php
  $pageTitle = 'Dashboard';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Dashboard']
  ];
@endphp

@push('styles')
  <style>
    .main-wrapper {
      display: flex;
      flex-direction: column;
      min-height: auto;
      overflow-x: hidden;
    }

    .dashboard-content {
      padding: var(--spacing-2xl);
      overflow-y: auto;
      max-width: none;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: var(--spacing-2xl);
      align-items: start;
    }

    .grid-column {
      display: flex;
      flex-direction: column;
      gap: var(--spacing-2xl);
    }

    /* Hero Section */
    .hero-card {
      background: linear-gradient(135deg,
          var(--main-navy-clr) 0%,
          var(--navy-dark) 100%);
      border-radius: var(--radius-2xl);
      padding: var(--spacing-3xl);
      position: relative;
      overflow: hidden;
      box-shadow: 0 10px 25px rgba(27, 23, 99, 0.15);
      margin-bottom: var(--spacing-2xl);
      width: 100%;
    }

    .hero-decoration-1 {
      position: absolute;
      right: -80px;
      top: -80px;
      width: 350px;
      height: 350px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.04);
    }

    .hero-decoration-2 {
      position: absolute;
      right: 100px;
      bottom: -150px;
      width: 400px;
      height: 400px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.03);
    }

    .hero-content {
      position: relative;
      z-index: 1;
    }

    .hero-breadcrumb {
      font-size: var(--font-size-sm);
      color: rgba(255, 255, 255, 0.7);
      margin-bottom: var(--spacing-lg);
      letter-spacing: 1px;
      text-transform: uppercase;
      font-weight: 600;
    }

    .hero-breadcrumb-active {
      color: var(--yellow-clr);
    }

    .hero-heading {
      margin: 0 0 var(--spacing-md) 0;
      font-size: var(--font-size-4xl);
      font-weight: 800;
      line-height: var(--line-height-tight);
      color: white;
    }

    .hero-heading-name {
      color: var(--yellow-clr);
    }

    .hero-description {
      margin: 0 0 var(--spacing-3xl) 0;
      font-size: var(--font-size-lg);
      color: rgba(255, 255, 255, 0.85);
      line-height: var(--line-height-normal);
      max-width: 650px;
    }

    .hero-description strong {
      color: white;
      font-weight: 700;
    }

    .hero-buttons {
      display: flex;
      gap: var(--spacing-lg);
    }

    /* Buttons */
    .btn-primary,
    .btn-secondary {
      display: inline-flex;
      align-items: center;
      gap: var(--spacing-md);
      padding: var(--spacing-md) var(--spacing-xl);
      border-radius: var(--radius-lg);
      text-decoration: none;
      font-weight: 700;
      font-size: var(--font-size-base);
      letter-spacing: 0.4px;
      transition: all 0.2s ease-in-out;
    }

    .btn-primary {
      background-color: var(--yellow-clr);
      color: var(--main-navy-clr);
      box-shadow: 0 8px 20px rgba(251, 197, 49, 0.3);
      border: 2px solid var(--yellow-clr);
    }

    .btn-primary:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 25px rgba(251, 197, 49, 0.4);
      background-color: #fff176;
      border-color: #fff176;
    }

    .btn-secondary {
      background-color: transparent;
      color: white;
      border: 2px solid rgba(255, 255, 255, 0.3);
    }

    .btn-secondary:hover {
      background-color: rgba(255, 255, 255, 0.15);
      border-color: rgba(255, 255, 255, 0.8);
      transform: translateY(-3px);
    }

    /* Metrics Section */
    .metrics-section {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: var(--spacing-lg);
    }

    .metric-card {
      background: var(--white-clr);
      padding: var(--spacing-lg);
      border-radius: var(--radius-lg);
      border: 1px solid var(--line-clr);
      box-shadow: var(--shadow-sm);
      transition: transform 0.2s;
    }

    .metric-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 15px rgba(0, 0, 0, 0.05);
    }

    .metric-label {
      font-size: var(--font-size-xs);
      font-weight: 600;
      color: var(--text-clr);
      margin: 0 0 var(--spacing-sm) 0;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .metric-value-row {
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
      flex-wrap: wrap;
    }

    .metric-value {
      font-size: var(--font-size-5xl);
      font-weight: 800;
      color: var(--main-text-clr);
      line-height: 1;
      margin: 0;
    }

    .metric-change {
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: var(--success-clr);
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
      background: var(--success-bg);
      padding: var(--spacing-xs) var(--spacing-sm);
      border-radius: 12px;
    }

    .metric-badge {
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: var(--amber-clr);
      background: var(--warning-bg);
      padding: var(--spacing-xs) var(--spacing-sm);
      border-radius: 12px;
    }

    /* Studio Pipeline and Status */
    .widget-container,
    .studio-pipeline {
      background: var(--white-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-xl);
      border: 1px solid var(--line-clr);
      box-shadow: var(--shadow-md);
    }

    .section-header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: var(--spacing-lg);
      padding-bottom: var(--spacing-md);
      border-bottom: 1px solid var(--line-clr);
    }

    .section-title {
      display: flex;
      align-items: center;
      gap: var(--spacing-sm);
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--text-clr);
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 0;
    }

    .section-link {
      font-size: var(--font-size-sm);
      font-weight: 700;
      color: var(--main-navy-clr);
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
      transition: color 0.2s;
    }

    .section-link:hover {
      color: var(--yellow-clr);
    }

    .content-status-list {
      display: flex;
      flex-direction: column;
      gap: var(--spacing-md);
    }

    .status-item {
      background: var(--base-clr);
      padding: var(--spacing-md);
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      gap: var(--spacing-md);
      border: 1px solid var(--line-clr);
      transition: all 0.2s ease;
    }

    .status-item:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
      background: var(--white-clr);
      border-color: var(--line-clr);
    }

    /* Status colors */
    .status-item.requires-attention {
      background: #fef2f2;
      border-color: #fecaca;
    }

    .status-item.academic-audit {
      background: #fffbeb;
      border-color: #fde68a;
    }

    .status-item.published {
      background: #f0fdf4;
      border-color: #bbf7d0;
    }

    .status-icon {
      width: 36px;
      height: 36px;
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .status-icon.red {
      background: #fee2e2;
      color: #dc2626;
    }

    .status-icon.yellow {
      background: #fef3c7;
      color: #d97706;
    }

    .status-icon.green {
      background: #d1fae5;
      color: #059669;
    }

    .status-content {
      flex: 1;
    }

    .status-title {
      font-size: var(--font-size-base);
      font-weight: 700;
      color: var(--main-text-clr);
      margin: 0 0 var(--spacing-xs) 0;
    }

    .status-subtitle {
      font-size: var(--font-size-xs);
      font-weight: 500;
      color: var(--text-clr);
      margin: 0;
    }

    .status-action {
      padding: var(--spacing-xs) var(--spacing-md);
      background: var(--error-clr);
      color: var(--white-clr);
      border: none;
      border-radius: var(--radius-sm);
      font-size: var(--font-size-xs);
      font-weight: 700;
      cursor: pointer;
      text-transform: uppercase;
      transition: background 0.2s;
    }

    .status-action:hover {
      background: #b91c1c;
    }

    .status-chevron {
      color: #9ca3af;
      cursor: pointer;
      transition: color 0.2s;
    }

    .status-chevron:hover {
      color: var(--main-navy-clr);
    }

    /* Right Column Widgets */
    .invitation-card {
      background: var(--white-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-lg);
      border: 1px solid var(--line-clr);
      display: flex;
      flex-direction: column;
      gap: var(--spacing-xl);
      box-shadow: var(--shadow-md);
    }

    .invitation-header {
      display: flex;
      align-items: flex-start;
      gap: var(--spacing-lg);
    }

    .invitation-icon {
      width: 44px;
      height: 44px;
      background: var(--yellow-background-clr);
      border: 1px solid #fde68a;
      border-radius: var(--radius-lg);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .invitation-badge {
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: #d97706;
      text-transform: uppercase;
      margin: 0 0 var(--spacing-sm) 0;
      letter-spacing: 0.4px;
    }

    .invitation-title {
      font-size: var(--font-size-lg);
      font-weight: 700;
      color: var(--main-text-clr);
      margin: 0;
      line-height: var(--line-height-tight);
    }

    .invitation-buttons {
      display: flex;
      gap: var(--spacing-xl);
    }

    .btn-decline,
    .btn-review {
      flex: 1;
      padding: var(--spacing-md);
      border-radius: var(--radius-lg);
      font-size: var(--font-size-xs);
      font-weight: 700;
      cursor: pointer;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: var(--spacing-sm);
      border: none;
      transition: all 0.2s;
    }

    .btn-decline {
      background: var(--line-clr);
      color: var(--text-clr);
    }

    .btn-decline:hover {
      background: #e2e8f0;
      color: var(--main-text-clr);
    }

    .btn-review {
      background: var(--main-navy-clr);
      color: var(--white-clr);
      box-shadow: 0 2px 8px rgba(27, 23, 99, 0.15);
    }

    .btn-review:hover {
      background: #1f1a5a;
      transform: translateY(-1px);
    }

    .recent-activity-card {
      background: var(--white-clr);
      border-radius: var(--radius-xl);
      padding: var(--spacing-xl);
      border: 1px solid var(--line-clr);
      box-shadow: var(--shadow-md);
    }

    .activity-item {
      display: flex;
      align-items: center;
      gap: var(--spacing-lg);
      padding: var(--spacing-lg) 0;
      border-bottom: 1px solid var(--line-clr);
    }

    .activity-item:last-child {
      border-bottom: none;
      padding-bottom: 0;
    }

    .activity-thumbnail {
      width: 42px;
      height: 42px;
      border-radius: var(--radius-lg);
      overflow: hidden;
      border: 1px solid var(--line-clr);
    }

    .activity-thumbnail img {
      width: 100%;
      height: 100%;
      object-fit: cover;
    }

    .activity-info {
      flex: 1;
    }

    .activity-name {
      font-size: var(--font-size-base);
      font-weight: 700;
      color: var(--main-text-clr);
      margin: 0 0 var(--spacing-xs) 0;
    }

    .activity-date {
      display: flex;
      align-items: center;
      gap: var(--spacing-xs);
      font-size: var(--font-size-xs);
      color: var(--text-clr);
      margin: 0;
      font-weight: 500;
    }

    .pro-insight-card {
      background: linear-gradient(135deg,
          var(--main-navy-clr) 0%,
          var(--navy-dark) 100%);
      border-radius: var(--radius-xl);
      padding: var(--spacing-xl);
      position: relative;
      overflow: hidden;
      box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
    }

    .pro-insight-card::before {
      content: "";
      position: absolute;
      right: -50px;
      bottom: -50px;
      width: 200px;
      height: 200px;
      background: rgba(251, 197, 49, 0.08);
      border-radius: 50%;
      pointer-events: none;
    }

    .pro-insight-badge {
      display: flex;
      align-items: center;
      gap: var(--spacing-sm);
      font-size: var(--font-size-xs);
      font-weight: 700;
      color: var(--yellow-clr);
      margin-bottom: var(--spacing-md);
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .pro-insight-text {
      font-size: var(--font-size-base);
      line-height: var(--line-height-normal);
      color: rgba(255, 255, 255, 0.85);
      margin: 0;
      position: relative;
      z-index: 1;
    }

    .pro-insight-text strong {
      color: var(--yellow-clr);
      font-weight: 700;
    }

    /* Responsive */
    @media (max-width: 1024px) {
      .dashboard-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-2xl);
      }

      .metrics-section {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .main-wrapper {
        grid-column: 1 / -1;
      }

      .dashboard-content {
        padding: var(--spacing-2xl);
      }

      .hero-card {
        padding: var(--spacing-lg);
      }

      .metrics-section {
        grid-template-columns: 1fr;
      }

      .hero-heading {
        font-size: var(--font-size-3xl);
      }

      .hero-buttons {
        flex-direction: column;
      }

      .btn-primary,
      .btn-secondary {
        width: 100%;
        justify-content: center;
      }

      .invitation-buttons {
        gap: var(--spacing-md);
      }

      .btn-decline,
      .btn-review {
        font-size: var(--font-size-xs);
        padding: var(--spacing-sm);
      }
    }

    @media (max-width: 600px) {
      .dashboard-content {
        padding: var(--spacing-lg);
      }

      .dashboard-grid {
        grid-template-columns: 1fr;
        gap: var(--spacing-lg);
      }

      .hero-card {
        padding: var(--spacing-md);
        margin-bottom: var(--spacing-lg);
      }

      .hero-heading {
        font-size: var(--font-size-3xl);
      }

      .hero-description {
        font-size: var(--font-size-base);
        margin-bottom: var(--spacing-xl);
      }

      .metric-card {
        padding: var(--spacing-md);
      }

      .metrics-section {
        gap: var(--spacing-md);
      }

      .widget-container,
      .studio-pipeline,
      .invitation-card,
      .recent-activity-card,
      .pro-insight-card {
        padding: var(--spacing-lg);
      }

      .section-header {
        margin-bottom: var(--spacing-md);
        padding-bottom: var(--spacing-sm);
      }

      .status-item {
        padding: var(--spacing-sm);
        gap: var(--spacing-sm);
      }

      .status-icon {
        width: 32px;
        height: 32px;
      }

      .invitation-buttons {
        gap: var(--spacing-sm);
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
        {{ Auth::user()->profession ?? 'Instruktur' }},<br />
        <span class="hero-heading-name">{{ Auth::user()->name }}.</span>
      </h1>
      <p class="hero-description">
        Tahun ajaran ini Anda mengelola
        <strong>{{ $totalCourses }} Kelas Aktif</strong> dengan total jaringan
        <strong>{{ number_format($totalStudents) }} pelajar</strong>.
      </p>
      <div class="hero-buttons">
        <a href="#" class="btn-primary">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
            <path
              d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
          </svg>
          LIHAT JADWAL
        </a>
        <a href="#" class="btn-secondary">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
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
          <p class="metric-label">Total Pelajar</p>
          <div class="metric-value-row">
            <h2 class="metric-value">{{ number_format($totalStudents) }}</h2>
            <span class="metric-change">
              <svg width="16" height="16" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9V3M6 3L3 6M6 3L9 6" />
              </svg>
              Aktif
            </span>
          </div>
        </div>

        <div class="metric-card">
          <p class="metric-label">Kelas Diampu</p>
          <div class="metric-value-row">
            <h2 class="metric-value">{{ $totalCourses }}</h2>
            <span class="metric-badge">Trainer</span>
          </div>
        </div>

        <div class="metric-card">
          <p class="metric-label">Reputasi</p>
          <div class="metric-value-row">
            <h2 class="metric-value">5.0</h2>
            <span class="metric-change">
              <svg width="16" height="16" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9V3M6 3L3 6M6 3L9 6" />
              </svg>
              Perfect
            </span>
          </div>
        </div>
      </div>

      {{-- DAFTAR KELAS (REPLACING STUDIO PIPELINE) --}}
      <div class="studio-pipeline">
        <div class="section-header">
          <h3 class="section-title">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path
                d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
              </path>
              <polyline points="7.5 4.21 12 6.81 16.5 4.21"></polyline>
              <polyline points="7.5 19.79 7.5 14.6 3 12"></polyline>
              <polyline points="21 12 16.5 14.6 16.5 19.79"></polyline>
            </svg>
            Kelas Anda (Assigned Courses)
          </h3>
          <a href="#" class="section-link">
            Lihat Semua
            <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 3L11 8L6 13" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </a>
        </div>

        <div class="content-status-list">
          @forelse($myCourses as $course)
            {{-- Logic warna berdasarkan status dummy --}}
            @php
              $statusClass = 'published'; // Default hijau
              $iconClass = 'green';
              if ($course->status == 'draft') {
                $statusClass = 'academic-audit';
                $iconClass = 'yellow';
              }
              if ($course->status == 'archived') {
                $statusClass = 'requires-attention';
                $iconClass = 'red';
              }
            @endphp

            <div class="status-item {{ $statusClass }}">
              <div class="status-icon {{ $iconClass }}">
                {{-- Icon Buku Generic --}}
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
              </div>
              <div class="status-content">
                <h4 class="status-title">{{ $course->name }}</h4>
                <p class="status-subtitle">{{ $course->enrollments_count }} Siswa Aktif</p>
              </div>
              <a href="#" class="status-action" style="text-decoration:none; background: var(--main-navy-clr);">DETAIL</a>
            </div>
          @empty
            <div class="status-item academic-audit">
              <div class="status-content">
                <h4 class="status-title">Belum ada kelas</h4>
                <p class="status-subtitle">Anda belum ditugaskan di kelas manapun.</p>
              </div>
            </div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- KOLOM KANAN --}}
    <div class="grid-column">

      {{-- INVITATION CARD (Placeholder / Promo) --}}
      <div class="invitation-card">
        <div class="invitation-header">
          <div class="invitation-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M13 2L3 14h8l-1 8 10-12h-8l1-8z" fill="#d97706" />
            </svg>
          </div>
          <div>
            <p class="invitation-badge">Tips Mengajar</p>
            <h3 class="invitation-title">
              Tingkatkan Interaksi Siswa
            </h3>
          </div>
        </div>
        <div class="invitation-buttons">
          <button class="btn-review" style="width:100%">
            BACA PANDUAN
            <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 3L11 8L6 13" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </button>
        </div>
      </div>

      {{-- RECENT ACTIVITY (Updated to Recent Enrollments) --}}
      <div class="recent-activity-card">
        <div class="section-header">
          <h3 class="section-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FDB913" stroke-width="2">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
              <circle cx="9" cy="7" r="4"></circle>
              <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
              <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
            </svg>
            Siswa Terbaru
          </h3>
        </div>

        <div class="activity-content">
          @forelse($students->take(5) as $enrollment)
            <div class="activity-item">
              <div class="activity-thumbnail">
                {{-- Tampilkan Avatar User --}}
                <img src="{{ $enrollment->student->avatar_url }}" alt="Avatar" />
              </div>
              <div class="activity-info">
                <h4 class="activity-name">{{ $enrollment->student->name }}</h4>
                <p class="activity-date">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="#FDB913">
                    <path
                      d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                  </svg>
                  {{ $enrollment->created_at->format('d M') }} • {{ Str::limit($enrollment->course->name, 15) }}
                </p>
              </div>
            </div>
          @empty
            <div class="activity-item">
              <p class="text-muted small">Belum ada siswa baru.</p>
            </div>
          @endforelse
        </div>
      </div>

      <div class="pro-insight-card">
        <div class="pro-insight-badge">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#FDB913" stroke-width="2">
            <path d="M12 2L2 7l10 5 10-5-10-5z" fill="#FDB913" />
            <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke-linecap="round" stroke-linejoin="round" />
          </svg>
          Pro Insight
        </div>
        <p class="pro-insight-text">
          Kuis interaktif meningkatkan retensi siswa sebesar
          <strong>24%</strong>. Sarankan modul baru kepada admin hari ini.
        </p>
      </div>
    </div>
  </div>

@endsection