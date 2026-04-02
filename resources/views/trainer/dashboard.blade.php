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
          <p class="metric-label">Sertifikat Terkumpul</p>
          <div class="metric-value-row">
            <h2 class="metric-value">{{ number_format($totalCertificates) }}</h2>
            <span class="metric-change">
              <svg width="16" height="16" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9V3M6 3L3 6M6 3L9 6" />
              </svg>
              idSpora
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
            <span class="metric-change is-positive">
              <svg width="16" height="16" viewBox="0 0 12 12" fill="none" stroke="currentColor" stroke-width="2.5"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 9V3M6 3L3 6M6 3L9 6" />
              </svg>
              Excellent
            </span>
          </div>
        </div>
      </div>

      {{-- AKSI DIBUTUHKAN --}}
      <div class="studio-pipeline">
        <div class="section-header">
          <h3 class="section-title">
            Aksi Dibutuhkan
          </h3>
          <a href="{{ route('trainer.events') }}" class="section-link">Kelola Jadwal</a>
        </div>

        <p class="section-summary">
          Fokus tindakan admin: konfirmasi penugasan dan lengkapi materi event yang masih menunggu.
        </p>

        <div class="todo-board">
          <div class="todo-column">
            <div class="todo-column-head">
              <h4>Menunggu Konfirmasi</h4>
              <span>{{ $todoConfirmations->count() }}</span>
            </div>
            @forelse($todoConfirmations as $notification)
              <div class="todo-row">
                <div class="todo-row-content">
                  <h5>{{ Str::limit($notification->title, 42) }}</h5>
                  <p>{{ Str::limit($notification->message, 62) }}</p>
                </div>
                <div class="todo-actions-inline">
                  <form method="POST" action="{{ route('trainer.notifications.respond', $notification->id) }}"
                    class="js-invitation-response-form">
                    @csrf
                    <input type="hidden" name="decision" value="accept">
                    <button type="submit" class="todo-btn accept">Terima</button>
                  </form>
                  <form method="POST" action="{{ route('trainer.notifications.respond', $notification->id) }}"
                    class="js-invitation-response-form" data-confirm="Yakin ingin menolak undangan ini?">
                    @csrf
                    <input type="hidden" name="decision" value="reject">
                    <button type="submit" class="todo-btn reject">Tolak</button>
                  </form>
                </div>
              </div>
            @empty
              <div class="focus-empty">Tidak ada undangan yang menunggu konfirmasi.</div>
            @endforelse
          </div>

          <div class="todo-column">
            <div class="todo-column-head">
              <h4>Menunggu Materi</h4>
              <span>{{ $todoMaterials->count() }}</span>
            </div>
            @forelse($todoMaterials as $event)
              <div class="todo-row">
                <div class="todo-row-content">
                  <h5>{{ Str::limit($event->title, 44) }}</h5>
                  <p>{{ optional($event->event_date)->format('d M Y') }} • Materi presentasi belum diunggah</p>
                </div>
                <a href="{{ route('trainer.events.studio', $event->id) }}" class="todo-upload-link">Upload</a>
              </div>
            @empty
              <div class="focus-empty">Semua event aktif sudah memiliki materi.</div>
            @endforelse
          </div>
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
            <p class="invitation-badge">Undangan Trainer</p>
            <h3 class="invitation-title">
              Tugas Baru dari Admin
            </h3>
            <p class="invitation-meta">
              {{ $unreadInvitationCount }} undangan belum dibaca
            </p>
          </div>
        </div>
        <div class="invitation-list">
          @forelse($dashboardInvitations as $invite)
            @php
              $inviteUrl = data_get($invite->data, 'url');
              $inviteStatus = data_get($invite->data, 'invitation_status', 'pending');
              $inviteDueAt = data_get($invite->data, 'due_at');
              $dueDate = $inviteDueAt ? \Illuminate\Support\Carbon::parse($inviteDueAt) : null;
              $isOverdue = $dueDate ? $dueDate->isPast() : false;
            @endphp
            <div class="invitation-item {{ is_null($invite->read_at) ? 'is-unread' : '' }}">
              <div class="invitation-item-top">
                <h4 class="invitation-item-title">{{ $invite->title }}</h4>
                @if($inviteStatus === 'accepted')
                  <span class="invitation-pill is-accepted">Diterima</span>
                @elseif($inviteStatus === 'rejected')
                  <span class="invitation-pill is-rejected">Ditolak</span>
                @elseif(is_null($invite->read_at))
                  <span class="invitation-pill">Baru</span>
                @endif
              </div>
              <p class="invitation-item-message">{{ $invite->message }}</p>
              @if($dueDate)
                <p class="invitation-deadline {{ $isOverdue && $inviteStatus === 'pending' ? 'is-overdue' : '' }}">
                  Deadline: {{ $dueDate->format('d M Y H:i') }}
                  @if($isOverdue && $inviteStatus === 'pending')
                    (Terlambat)
                  @endif
                </p>
              @endif
              <div class="invitation-item-footer">
                <span class="invitation-item-time">{{ $invite->created_at?->diffForHumans() }}</span>
                @if(!empty($inviteUrl))
                  <a href="{{ route('trainer.notifications.open', $invite->id) }}" class="invitation-item-link">Buka</a>
                @endif
              </div>
              @if($inviteStatus === 'pending')
                <div class="invitation-actions">
                  <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}"
                    class="js-invitation-response-form">
                    @csrf
                    <input type="hidden" name="decision" value="accept">
                    <button type="submit" class="invitation-btn accept" data-loading-text="Memproses...">Terima</button>
                  </form>
                  <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}"
                    class="js-invitation-response-form" data-confirm="Yakin ingin menolak undangan ini?">
                    @csrf
                    <input type="hidden" name="decision" value="reject">
                    <button type="submit" class="invitation-btn reject" data-loading-text="Memproses...">Tolak</button>
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

    </div>
  </div>

@endsection