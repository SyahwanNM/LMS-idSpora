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
  @vite(['resources/css/trainer/dashboard.css'])
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
            <h2 class="metric-value">4.8</h2>
            <span class="metric-change" style="background:#d1fae5; color:#059669;">
              <svg width="16" height="16" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9V3M6 3L3 6M6 3L9 6" />
              </svg>
              Excellent
            </span>
          </div>
        </div>
      </div>

      {{-- DAFTAR KELAS --}}
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
          <a href="{{ route('trainer.courses') }}" class="section-link">
            Lihat Semua
            <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 3L11 8L6 13" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </a>
        </div>

        <div class="content-status-list">
          @forelse($myCourses as $course)
            @php
              $statusClass = 'published'; // Default hijau
              $iconClass = 'green';
              if ($course->status == 'draft') {
                $statusClass = 'academic-audit';
                $iconClass = 'yellow';
              }
              if ($course->status == 'archive') {
                $statusClass = 'requires-attention';
                $iconClass = 'red';
              }
            @endphp

            <div class="status-item {{ $statusClass }}">
              <div class="status-icon {{ $iconClass }}">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                  <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                </svg>
              </div>
              <div class="status-content">
                <h4 class="status-title">{{ Str::limit($course->name, 40) }}</h4>
                <p class="status-subtitle">{{ $course->enrollments_count ?? 0 }} Siswa Aktif</p>
              </div>
              <a href="{{ route('trainer.detail-course', $course->id) }}" class="status-action"
                style="text-decoration:none; background: var(--main-navy-clr);">DETAIL</a>
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

      {{-- INVITATION CARD --}}
      <div class="invitation-card">
        <div class="invitation-header">
          <div class="invitation-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"
              stroke-linecap="round" stroke-linejoin="round">
              <path d="M13 2L3 14h8l-1 8 10-12h-8l1-8z" fill="#d97706" />
            </svg>
          </div>
          <div>
            <p class="invitation-badge">Insight Feedback</p>
            <h3 class="invitation-title">
              Pantau Kepuasan Siswa Anda
            </h3>
          </div>
        </div>
        <div class="invitation-buttons">
          <a href="{{ route('trainer.feedback') }}" class="btn-review" style="width:100%">
            LIHAT FEEDBACK
            <svg width="18" height="18" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M6 3L11 8L6 13" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          </a>
        </div>
      </div>

      {{-- RECENT ACTIVITY (Siswa Terbaru) --}}
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
                {{-- Fallback ke default avatar jika user terhapus/kosong --}}
                <img
                  src="{{ $enrollment->student->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($enrollment->student->name ?? 'User') }}"
                  alt="Avatar" />
              </div>
              <div class="activity-info">
                <h4 class="activity-name">{{ $enrollment->student->name ?? 'Anonim' }}</h4>
                <p class="activity-date">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="#FDB913">
                    <path
                      d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z" />
                  </svg>
                  {{ $enrollment->created_at->format('d M') }} • {{ Str::limit($enrollment->course->name ?? 'Course', 15) }}
                </p>
              </div>
            </div>
          @empty
            <div class="activity-item">
              <p class="text-muted small" style="margin:0;">Belum ada siswa yang mendaftar di kelas Anda.</p>
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
          <strong>24%</strong>. Jangan lupa menambahkan soal pre-test di modul Anda!
        </p>
      </div>
    </div>
  </div>

@endsection