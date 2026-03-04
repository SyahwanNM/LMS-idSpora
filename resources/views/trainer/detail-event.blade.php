@extends('layouts.trainer')

@section('title', 'Event Detail - Trainer')

@php
  $pageTitle = 'Event Detail';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Events', 'url' => route('trainer.events')],
    ['label' => 'Detail']
  ];
@endphp

@push('styles')
  <link rel="stylesheet" href="/assets/css/detail-event.css" />
@endpush

@section('content')
  <div class="hero-section">
    <div class="hero-container">
      <div class="hero-top-row">
        <button class="back-button" onclick="window.location.href = 'events.html'">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 12H5M12 19l-7-7 7-7" />
          </svg>
          <span>ALL SESSIONS</span>
        </button>

        <div class="event-status-badges">
          <span class="status-badge">VIRTUAL STUDIO</span>
          <span class="status-badge">CONFIRMED COMMITMENT</span>
        </div>
      </div>

      <div class="hero-body">
        <div class="hero-left">
          <div class="event-category-badge">
            <span class="badge-icon"></span>
            <span>HYBRID MASTERCLASS</span>
            <span class="badge-sep">•</span>
            <span>SESSION LEDGER</span>
          </div>

          <h1 class="event-hero-title">
            Visual Branding <span>Architecture</span>
          </h1>

          <div class="event-info-cards">
            <div class="info-card">
              <div class="info-icon-shell">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                  <line x1="16" y1="2" x2="16" y2="6"></line>
                  <line x1="8" y1="2" x2="8" y2="6"></line>
                  <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">DATE</span>
                <span class="info-card-value">Wed Apr 10 2024</span>
              </div>
            </div>

            <div class="info-card">
              <div class="info-icon-shell">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <circle cx="12" cy="12" r="10"></circle>
                  <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">TIME</span>
                <span class="info-card-value">01:00 PM - 04:00 PM</span>
              </div>
            </div>

            <div class="info-card">
              <div class="info-icon-shell">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                  stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                  <circle cx="12" cy="10" r="3"></circle>
                </svg>
              </div>
              <div class="info-card-content">
                <span class="info-card-label">VENUE</span>
                <span class="info-card-value">Tech Hub Hall A</span>
              </div>
            </div>
          </div>
        </div>

        <div class="hero-media">
          <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=600&h=400&fit=crop"
            alt="Visual Branding Architecture" class="hero-image" />
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
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd"
                d="M0 5a2 2 0 0 1 2-2h7.5a2 2 0 0 1 1.983 1.738l3.11-1.382A1 1 0 0 1 16 4.269v7.462a1 1 0 0 1-1.406.913l-3.111-1.382A2 2 0 0 1 9.5 13H2a2 2 0 0 1-2-2zm11.5 5.175 3.5 1.556V4.269l-3.5 1.556zM2 4a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h7.5a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1z" />
            </svg>
          </div>
          <div class="vsa-meta">
            <p class="vsa-label">MEETING PORTAL</p>
            <h3>Session Conference</h3>
            <p class="vsa-link">https://zoom.us/j/123456789</p>
          </div>
          <button class="vsa-btn vsa-btn-primary">
            JOIN SESSION
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path fill-rule="evenodd"
                d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
              <path fill-rule="evenodd"
                d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
            </svg>
          </button>
        </article>

        <article class="vsa-card">
          <div class="vsa-icon vsa-icon-amber">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
              <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0" />
              <path
                d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1z" />
            </svg>
          </div>
          <div class="vsa-meta">
            <p class="vsa-label">BRANDING KIT</p>
            <h3>Virtual Background</h3>
            <p class="vsa-desc">High-Res PNG • Pre-branded</p>
          </div>
          <button class="vsa-btn vsa-btn-amber">
            DOWNLOAD VBG
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
              <path
                d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
              <path
                d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708z" />
            </svg>
          </button>
        </article>
      </div>

      <div class="vsa-context">
        <div class="vsa-context-title">
          <span class="context-dot"></span>
          <span>PEDAGOGICAL CONTEXT</span>
        </div>
        <p>Deep dive into responsive design systems.</p>
      </div>

      <p class="vsa-subtitle">SESSION LEDGER</p>
      <section class="rundown-list">
        <h2>Rundown Acara</h2>
        <ul>
          <li>
            <span class="time-rundown">13:00 - 13:20</span>
            <span class="activity-rundown">Registrasi peserta dan pembukaan sesi</span>
          </li>
          <li>
            <span class="time-rundown">13:20 - 14:10</span>
            <span class="activity-rundown">Materi inti: Visual Branding Architecture</span>
          </li>
          <li>
            <span class="time-rundown">14:10 - 14:30</span>
            <span class="activity-rundown">Studi kasus dan diskusi kelompok</span>
          </li>
          <li>
            <span class="time-rundown">14:30 - 15:00</span>
            <span class="activity-rundown">Hands-on workshop dan review hasil</span>
          </li>
          <li>
            <span class="time-rundown">15:00 - 15:20</span>
            <span class="activity-rundown">Tanya jawab, evaluasi, dan penutupan</span>
          </li>
        </ul>
      </section>
    </section>

    <aside class="hub-card">
      <p class="hub-title">INSTRUCTOR HUB</p>
      <div class="hub-section">
        <p class="hub-section-title">ENGAGEMENT REQUIREMENTS</p>
        <div class="hub-pill-grid">
          <div class="hub-pill" data-redirect="content-studio.html?tab=module">
            <p class="hub-pill-label">MATERIALS</p>
            <p class="hub-pill-value">2 PDF • 1 Video</p>
          </div>
          <div class="hub-pill" data-redirect="content-studio.html?tab=quiz">
            <p class="hub-pill-label">ASSESSMENTS</p>
            <p class="hub-pill-value">Pre-test &amp; Post-test</p>
          </div>
        </div>
      </div>

      <div class="hub-item">
        <div class="hub-item-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
            <path
              d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5" />
            <path
              d="M7.646 1.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 2.707V11.5a.5.5 0 0 1-1 0V2.707L5.354 4.854a.5.5 0 1 1-.708-.708z" />
          </svg>
        </div>
        <div>
          <h4>Submit Assets</h4>
          <p>Pedagogical Materials</p>
        </div>
      </div>

      <div class="hub-item">
        <div class="hub-item-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
            <path
              d="M5.338 5.59a.75.75 0 0 1 .75.75v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 .75-.75zm6 0a.75.75 0 0 1 .75.75v2.5a.75.75 0 0 1-1.5 0v-2.5a.75.75 0 0 1 .75-.75z" />
          </svg>
        </div>
        <div>
          <h4>Learner Ledger</h4>
          <p>Attendance &amp; Profiles</p>
        </div>
      </div>

      <div class="hub-alert">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
          <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z" />
          <path
            d="m10.97 4.97-.02.02-3.36 3.36a.75.75 0 1 1-1.06-1.06l3.36-3.36a.75.75 0 1 1 1.06 1.06l-.02.02zm-9.47 8.47H6a.75.75 0 0 1 0 1.5H.539l-.427 2.154a.75.75 0 0 0 .921.921l2.154-.427V16a.75.75 0 0 1 1.5 0v2.039l2.154.427a.75.75 0 0 0 .921-.921l-.427-2.154H6a.75.75 0 0 1 0-1.5H1.5z" />
        </svg>
        <p>
          VALIDATION REQUIRED: PLEASE UPLOAD PEDAGOGICAL ASSETS AT LEAST 24H
          PRIOR FOR AUDIT.
        </p>
      </div>
    </aside>
  </div>
  </main>
  </div>
  <script>
    document.addEventListener("click", (event) => {
      const pill = event.target.closest(".hub-pill[data-redirect]");
      if (!pill) return;

      event.preventDefault();
      event.stopPropagation();

      const targetPath = pill.getAttribute("data-redirect");
      if (targetPath) {
        window.location.href = targetPath;
      }
    });
  </script>

@endsection