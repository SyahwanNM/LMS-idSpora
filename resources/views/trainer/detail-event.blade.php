@extends('layouts.trainer')

@section('title', $event->title . ' - Trainer')

@php
  $pageTitle = 'Event Detail';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Events', 'url' => route('trainer.events')],
    ['label' => 'Detail']
  ];
@endphp

@section('content')
  <div class="hero-section">
    <div class="hero-container">
      <div class="hero-top-row">
        <button
          class="back-button"
          onclick="window.location.href = '{{ route('trainer.events') }}'"
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <path d="M19 12H5M12 19l-7-7 7-7" />
          </svg>
          <span>ALL SESSIONS</span>
        </button>

        <div class="event-status-badges">
          <span class="status-badge">{{ strtoupper($event->jenis ?? 'VIRTUAL STUDIO') }}</span>
          <span class="status-badge">CONFIRMED COMMITMENT</span>
        </div>
      </div>

      <div class="hero-body">
        <div class="hero-left">
          <div class="event-category-badge">
            <span class="badge-icon"></span>
            <span>{{ strtoupper($event->category ?? 'HYBRID MASTERCLASS') }}</span>
            <span class="badge-sep">•</span>
            <span>SESSION LEDGER</span>
          </div>

          <h1 class="event-hero-title">
            {{ $event->title }}
          </h1>

          <div class="event-info-cards">
            <div class="info-card">
              <div class="info-icon-shell">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">DATE</span>
                <span class="info-card-value">
                  {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('D M d Y') : 'TBA' }}
                </span>
              </div>
            </div>

            <div class="info-card">
              <div class="info-icon-shell">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">TIME</span>
                <span class="info-card-value">
                  {{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('h:i A') : 'TBA' }}
                  @if($event->event_time_end)
                    - {{ \Carbon\Carbon::parse($event->event_time_end)->format('h:i A') }}
                  @endif
                </span>
              </div>
            </div>

            <div class="info-card">
              <div class="info-icon-shell">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  fill="none"
                  stroke="currentColor"
                  stroke-width="2"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                >
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">VENUE</span>
                <span class="info-card-value">{{ $event->location ?? 'Tech Hub Hall A' }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="hero-media">
          @if(Str::startsWith($event->image, ['http://', 'https://']))
              <img src="{{ $event->image }}" alt="{{ $event->title }}" class="hero-image" />
          @elseif($event->image)
              <img src="{{ asset('storage/'.$event->image) }}" alt="{{ $event->title }}" class="hero-image" />
          @else
              <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&h=400&fit=crop" alt="Default Event Image" class="hero-image" />
          @endif
        </div>
      </div>
    </div>
  </div>

  <div class="detail-layout">
    <section class="vsa-section">
      <p class="vsa-title">VIRTUAL STUDIO ASSETS</p>
      <div class="vsa-grid">
        <article class="vsa-card">
          <div class="vsa-icon vsa-icon-blue">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              fill="currentColor"
              viewBox="0 0 16 16"
            >
              <path
                fill-rule="evenodd"
                d="M0 5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 4.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 13H2a2 2 0 0 1-2-2zm11.5 5.175 3.5 1.556V4.269l-3.5 1.556zM2 4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h7.5a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1z"
              />
            </svg>
          </div>
          <div class="vsa-meta">
            <p class="vsa-label">MEETING PORTAL</p>
            <h3>Session Conference</h3>
            <p class="vsa-link">{{ $event->zoom_link ?? 'Link belum tersedia' }}</p>
          </div>
          <a href="{{ $event->zoom_link ?? '#' }}" target="_blank" class="vsa-btn vsa-btn-primary" {{ empty($event->zoom_link) ? 'disabled' : '' }}>
            JOIN SESSION
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              fill="currentColor"
              viewBox="0 0 16 16"
            >
              <path
                fill-rule="evenodd"
                d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5"
              />
              <path
                fill-rule="evenodd"
                d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z"
              />
            </svg>
          </a>
        </article>

        <article class="vsa-card">
          <div class="vsa-icon vsa-icon-amber">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="24"
              height="24"
              fill="currentColor"
              viewBox="0 0 16 16"
            >
              <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
              <path
                d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z"
              />
            </svg>
          </div>
          <div class="vsa-meta">
            <p class="vsa-label">BRANDING KIT</p>
            <h3>Virtual Background</h3>
            <p class="vsa-desc">High-Res PNG • Pre-branded</p>
          </div>
          <a href="{{ $event->vbg_path ? $event->vbg_file_url : '#' }}" class="vsa-btn vsa-btn-amber" download>
            DOWNLOAD VBG
            <svg
              xmlns="http://www.w3.org/2000/svg"
              width="16"
              height="16"
              fill="currentColor"
              viewBox="0 0 16 16"
            >
              <path
                d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"
              />
              <path
                d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z"
              />
            </svg>
          </a>
        </article>
      </div>

      <div class="vsa-context">
        <div class="vsa-context-title">
          <span class="context-dot"></span>
          <span>PEDAGOGICAL CONTEXT</span>
        </div>
        <p>{{ $event->description ?? 'Deskripsi detail event belum tersedia.' }}</p>
      </div>

      <p class="vsa-subtitle">SESSION LEDGER</p>
      <section class="rundown-list">
        <h2>Rundown Acara</h2>
        @php
          $items = collect();

          if (isset($event)) {
            // Prefer normalized schedule items table
            try {
              $items = $event->relationLoaded('scheduleItems')
                ? ($event->scheduleItems ?? collect())
                : $event->scheduleItems()->get();
            } catch (\Throwable $e) {
              $items = collect();
            }

            // Fallback to schedule_json (legacy)
            if ($items->isEmpty()) {
              $rawSchedule = $event->schedule_json ?? null;

              $scheduleArr = null;
              if (is_string($rawSchedule) && trim($rawSchedule) !== '') {
                $decoded = json_decode($rawSchedule, true);
                $scheduleArr = (json_last_error() === JSON_ERROR_NONE) ? $decoded : null;
              } elseif (is_array($rawSchedule)) {
                $scheduleArr = $rawSchedule;
              } elseif (is_object($rawSchedule)) {
                $scheduleArr = json_decode(json_encode($rawSchedule), true);
              }

              if (is_array($scheduleArr)) {
                $items = collect($scheduleArr)->map(function ($row) {
                  $row = is_array($row) ? $row : (is_object($row) ? (array) $row : []);
                  return (object) [
                    'start' => $row['start'] ?? ($row['time_start'] ?? ($row['time'] ?? null)),
                    'end' => $row['end'] ?? ($row['time_end'] ?? null),
                    'title' => $row['title'] ?? ($row['activity'] ?? ''),
                    'description' => $row['description'] ?? ($row['desc'] ?? ''),
                  ];
                })->filter(function ($it) {
                  return !empty($it->title) || !empty($it->description) || !empty($it->start) || !empty($it->end);
                })->values();
              }
            }
          }

          $formatTime = function ($t) {
            if (empty($t)) {
              return null;
            }
            try {
              return \Carbon\Carbon::parse($t)->format('H:i');
            } catch (\Throwable $e) {
              return is_string($t) ? $t : null;
            }
          };
        @endphp
        <ul>
          @forelse($items as $it)
            @php
              $start = $formatTime($it->start ?? null);
              $end = $formatTime($it->end ?? null);
              $timeStr = trim(($start ?: '') . ($end ? ' - ' . $end : ''));
              $activity = trim((string) ($it->title ?? ''));
              if ($activity === '') {
                $activity = trim((string) ($it->description ?? ''));
              }
            @endphp
            <li>
              <span class="time-rundown">{{ $timeStr !== '' ? $timeStr : '-' }}</span>
              <span class="activity-rundown">{{ $activity !== '' ? $activity : '-' }}</span>
            </li>
          @empty
            <li>
              <span class="time-rundown">—</span>
              <span class="activity-rundown">Schedule will be announced.</span>
            </li>
          @endforelse
        </ul>
      </section>

    <aside class="hub-card">
      <p class="hub-title">INSTRUCTOR HUB</p>
      <div class="hub-section">
        <p class="hub-section-title">ENGAGEMENT REQUIREMENTS</p>
        <div class="hub-pill-grid">
          <div
            class="hub-pill"
            data-redirect="{{ route('trainer.events.studio', $event->id) }}"
          >
            <p class="hub-pill-label">MATERIALS</p>
            <p class="hub-pill-value">Upload Content</p>
          </div>
          <div
            class="hub-pill"
            data-redirect="#"
          >
            <p class="hub-pill-label">ASSESSMENTS</p>
            <p class="hub-pill-value">Quizzes</p>
          </div>
        </div>
      </div>

      <div class="hub-item">
        <div class="hub-item-icon">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="18"
            fill="currentColor"
            viewBox="0 0 16 16"
          >
            <path
              d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5"
            />
            <path
              d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z"
            />
          </svg>
        </div>
        <div>
          <h4>Submit Assets</h4>
          <p>Pedagogical Materials</p>
        </div>
      </div>

      <div class="hub-item">
        <div class="hub-item-icon">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            width="18"
            height="18"
            fill="currentColor"
            viewBox="0 0 16 16"
          >
            <path
              d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"
            />
            <path
              d="M5.338 5.59a.75.75 0 0 1 .75.75v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 .75-.75zm6 0a.75.75 0 0 1 .75.75v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 .75-.75z"
            />
          </svg>
        </div>
        <div>
          <h4>Learner Ledger</h4>
          <p>Attendance &amp; Profiles</p>
        </div>
      </div>

      <div class="hub-alert">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          width="16"
          height="16"
          fill="currentColor"
          viewBox="0 0 16 16"
        >
          <path
            d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"
          />
          <path
            d="m10.97 4.97-.02.02-3.36 3.36a.75.75 0 1 1-1.06-1.06l3.36-3.36a.75.75 0 1 1 1.06 1.06l-.02.02zm-9.47 8.47H6a.75.75 0 0 1 0 1.5H.539l-.427 2.154a.75.75 0 0 0 .921.921l2.154-.427V16a.75.75 0 0 1 1.5 0v2.039l2.154.427a.75.75 0 0 0 .921-.921l-.427-2.154H6a.75.75 0 0 1 0-1.5H1.5z"
          />
        </svg>
        <p>
          VALIDATION REQUIRED: PLEASE UPLOAD PEDAGOGICAL ASSETS AT LEAST 24H
          PRIOR FOR AUDIT.
        </p>
      </div>
    </aside>
  </div>
@endsection

@push('scripts')
<script>
  document.addEventListener("click", (event) => {
    const pill = event.target.closest(".hub-pill[data-redirect]");
    if (!pill) return;

    event.preventDefault();
    event.stopPropagation();

    const targetPath = pill.getAttribute("data-redirect");
    if (targetPath && targetPath !== '#') {
      window.location.href = targetPath;
    }
  });
</script>
@endpush