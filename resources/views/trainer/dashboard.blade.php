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
      <div style="margin-top: 12px; display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
        <span
          style="display:inline-flex; align-items:center; gap:6px; padding:8px 12px; border-radius:999px; font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.05em; background: {{ Auth::user()->user_status === 'suspended' ? '#7f1d1d' : (Auth::user()->user_status === 'inactive' ? '#92400e' : '#065f46') }}; color:#fff;">
          Status Akun: {{ Auth::user()->user_status_label }}
        </span>
        @if(Auth::user()->user_status !== 'suspended')
          <form method="POST" action="{{ route('trainer.availability.toggle') }}">
            @csrf
            <button type="submit" class="btn-secondary" style="padding:8px 14px; font-size:12px;">
              {{ Auth::user()->user_status === 'active' ? 'Nonaktifkan Saya' : 'Saya Siap Mengajar' }}
            </button>
          </form>
        @endif
      </div>
      @if((int) data_get($trainerActivity, 'late_uploads', 0) > 0)
        <div
          style="margin-top: 16px; padding: 14px 16px; border-radius: 16px; background: {{ (int) data_get($trainerActivity, 'late_uploads', 0) >= 3 ? '#7f1d1d' : (((int) data_get($trainerActivity, 'late_uploads', 0) === 2) ? '#92400e' : '#1d4ed8') }}; color: #fff; max-width: 760px;">
          <strong>Perhatian:</strong>
          @if((int) data_get($trainerActivity, 'late_uploads', 0) === 1)
            Anda memiliki 1 keterlambatan berturut-turut (SP1 / Kartu Kuning).
          @elseif((int) data_get($trainerActivity, 'late_uploads', 0) === 2)
            Anda memiliki 2 keterlambatan berturut-turut (SP2 / Peringatan Keras). Satu pelanggaran lagi akun akan dibekukan.
          @else
            Anda memiliki {{ (int) data_get($trainerActivity, 'late_uploads', 0) }} keterlambatan berturut-turut. Akun Anda
            berstatus suspended.
          @endif
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
          <p class="metric-label">Tier Saat Ini</p>
          <div class="metric-value-row">
            <h2 class="metric-value" style="font-size: 20px;">{{ Auth::user()->trainer_tier_label }}</h2>
            <span
              class="metric-badge">{{ strtoupper((string) data_get($trainerActivity, 'trainer_tier', 'associate')) }}</span>
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
                  @php $notificationEntityType = data_get($notification->data, 'entity_type'); @endphp
                  @php $notificationAgreementUrl = data_get($notification->data, 'url'); @endphp
                  @if($notificationEntityType === 'event')
                    <button type="button" class="todo-btn accept"
                      onclick="openSchemeSelectionModal({{ $notification->id }}, '{{ addslashes((string) ($notification->title ?? 'Undangan Event')) }}')">
                      Terima
                    </button>
                  @else
                    <form method="POST" action="{{ route('trainer.notifications.respond', $notification->id) }}"
                      class="js-invitation-response-form">
                      @csrf
                      <input type="hidden" name="decision" value="accept">
                      <select name="contribution_scheme" required
                        style="margin: 0 0 8px; width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:10px; background:#fff; font-size:13px;">
                        <option value="" selected disabled>Skema kontribusi</option>
                        @foreach(Auth::user()->available_contribution_schemes as $schemeKey => $scheme)
                          <option value="{{ $schemeKey }}">{{ data_get($scheme, 'percent') }}% -
                            {{ data_get($scheme, 'label') }}
                          </option>
                        @endforeach
                      </select>
                      @if(!empty($notificationAgreementUrl))
                        <a href="{{ route('trainer.notifications.open', $notification->id) }}" target="_blank" rel="noopener"
                          style="display:inline-flex; margin-bottom:8px; font-size:12px; color:#2563eb; text-decoration:underline;">
                          Buka E-Agreement
                        </a>
                      @endif
                      <label
                        style="display:flex; gap:8px; align-items:flex-start; font-size:12px; color:#334155; margin-bottom:8px;">
                        <input type="checkbox" name="e_agreement" value="1" required style="margin-top:2px;">
                        <span>Saya menyetujui E-Agreement penugasan ini.</span>
                      </label>
                      <button type="submit" class="todo-btn accept">Terima</button>
                    </form>
                  @endif
                  <form method="POST" action="{{ route('trainer.notifications.respond', $notification->id) }}"
                    class="js-invitation-response-form">
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
                @elseif($inviteStatus === 'expired')
                  <span class="invitation-pill is-rejected">Expired</span>
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
                  @php $inviteEntityType = data_get($invite->data, 'entity_type'); @endphp
                  @if($inviteEntityType === 'event')
                    {{-- EVENT INVITATION: Show Modal Trigger --}}
                    <button type="button" class="invitation-btn accept"
                      onclick="openSchemeSelectionModal({{ $invite->id }}, '{{ addslashes($invite->title) }}')">
                      Terima
                    </button>
                    <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}"
                      class="js-invitation-response-form">
                      @csrf
                      <input type="hidden" name="decision" value="reject">
                      <button type="submit" class="invitation-btn reject">Tolak</button>
                    </form>
                  @else
                    {{-- COURSE INVITATION: Keep Original Form --}}
                    <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}"
                      class="js-invitation-response-form">
                      @csrf
                      <input type="hidden" name="decision" value="accept">
                      <select name="contribution_scheme" required
                        style="margin: 0 0 8px; width:100%; padding:8px 10px; border:1px solid #d1d5db; border-radius:10px; background:#fff; font-size:13px;">
                        <option value="" selected disabled>Skema kontribusi</option>
                        @foreach(Auth::user()->available_contribution_schemes as $schemeKey => $scheme)
                          <option value="{{ $schemeKey }}">{{ data_get($scheme, 'percent') }}% - {{ data_get($scheme, 'label') }}
                          </option>
                        @endforeach
                      </select>
                      @if(!empty($inviteUrl))
                        <a href="{{ route('trainer.notifications.open', $invite->id) }}" target="_blank" rel="noopener"
                          style="display:inline-flex; margin-bottom:8px; font-size:12px; color:#2563eb; text-decoration:underline;">
                          Buka E-Agreement
                        </a>
                      @endif
                      <label
                        style="display:flex; gap:8px; align-items:flex-start; font-size:12px; color:#334155; margin-bottom:8px;">
                        <input type="checkbox" name="e_agreement" value="1" required style="margin-top:2px;">
                        <span>Saya menyetujui E-Agreement penugasan ini.</span>
                      </label>
                      <button type="submit" class="invitation-btn accept" data-loading-text="Memproses...">Terima</button>
                    </form>
                    <form method="POST" action="{{ route('trainer.notifications.respond', $invite->id) }}"
                      class="js-invitation-response-form">
                      @csrf
                      <input type="hidden" name="decision" value="reject">
                      <button type="submit" class="invitation-btn reject">Tolak</button>
                    </form>
                  @endif
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