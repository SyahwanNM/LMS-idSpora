@extends('layouts.trainer')

@section('title', 'Riwayat Kegiatan - Trainer')

@php
  $pageTitle = 'Riwayat Kegiatan';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Riwayat Kegiatan']
  ];
@endphp

@push('styles')
<style>
  /* Import cursive fonts for certificate preview name */
  @import url('https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Great+Vibes&display=swap');

  .courses-page { margin: 0; padding: 0; }
  .top-page { background: linear-gradient(135deg, #2e2050 0%, #51376c 100%); border-radius: var(--radius-2xl); padding: var(--spacing-3xl); position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(27, 23, 99, 0.15); margin-bottom: var(--spacing-2xl); width: 100%; }
  .glow-circle { position: absolute; border-radius: 50%; pointer-events: none; z-index: 0; }
  .glow-circle-1 { top: -80px; right: -80px; width: 192px; height: 192px; background: rgba(251, 191, 36, 0.1); filter: blur(60px); }
  .glow-circle-2 { bottom: -40px; left: -40px; width: 128px; height: 128px; background: rgba(99, 102, 241, 0.1); filter: blur(50px); }
  .top-page-inner { width: 100%; position: relative; display: flex; flex-direction: row; justify-content: space-between; align-items: center; gap: var(--spacing-2xl); }
  .top-page-content { display: flex; flex-direction: column; gap: 24px; flex: 1; }
  .badge-top { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 100px; color: rgba(255, 255, 255, 0.9); font-size: 9px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 8px; backdrop-filter: blur(10px); width: fit-content; }
  .badge-top svg { width: 12px; height: 12px; color: var(--yellow-clr); flex-shrink: 0; }
  .title-page { display: flex; flex-direction: column; gap: 12px; max-width: 600px; }
  .title-page h1 { margin: 0; color: var(--white-clr); font-size: 40px; font-weight: 800; line-height: 1.2; }
  .title-page h1 span { color: #fbb034; }
  .title-page h5 { margin: 0; color: rgba(255, 255, 255, 0.7); font-size: 14px; font-weight: 500; line-height: 1.6; max-width: 500px; }
  .upcoming-card { display: flex; align-items: center; gap: 16px; padding: 20px 24px; background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 20px; min-width: 200px; backdrop-filter: blur(20px); box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); }
  .upcoming-icon { display: flex; align-items: center; justify-content: center; width: 52px; height: 52px; background: #fbb034; border-radius: 14px; flex-shrink: 0; box-shadow: 0 4px 12px rgba(251, 176, 52, 0.3); }
  .upcoming-icon svg { width: 24px; height: 24px; color: var(--main-navy-clr); }
  .upcoming-text { display: flex; flex-direction: column; gap: var(--spacing-xs); }
  .upcoming-label { font-size: 9px; font-weight: 900; color: rgba(255, 255, 255, 0.6); text-transform: uppercase; letter-spacing: 1.4px; }
  .upcoming-count { font-size: 18px; font-weight: 900; color: var(--white-clr); line-height: 1; }
  .search-filter-bar { display: flex; justify-content: flex-end; align-items: center; gap: var(--spacing-sm); flex-wrap: nowrap; align-self: flex-end; width: auto; flex-shrink: 0; }

  .cert-page {
    margin: 0;
    padding: 0;
  }

  /* â”€â”€â”€ Tabs & Filters Container â”€â”€â”€ */
  .cert-tabs-filter-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1.5px solid #e2e8f0;
    padding-bottom: 0;
    margin-top: 16px;
    margin-bottom: 32px;
    gap: 16px;
    flex-wrap: wrap;
  }

  .cert-nav-tabs {
    display: flex;
    gap: 28px;
  }

  .cert-tab-btn {
    background: none;
    border: none;
    padding: 12px 4px;
    font-size: 15px;
    font-weight: 700;
    color: #94a3b8;
    cursor: pointer;
    position: relative;
    transition: color 0.2s ease;
  }

  .cert-tab-btn:hover {
    color: #2e2050;
    background: transparent !important;
  }

  .cert-tab-btn.active {
    color: #2e2050;
    background: transparent !important;
  }

  .cert-tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -1.5px;
    left: 0;
    right: 0;
    height: 3px;
    background-color: #2e2050;
    border-radius: 99px;
  }

  .cert-filter-widgets {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .cert-search-wrapper {
    position: relative;
    min-width: 220px;
  }

  .cert-search-input {
    width: 100%;
    padding: 9px 36px 9px 16px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    font-size: 13px;
    color: #334155;
    outline: none;
    transition: all 0.2s ease;
    background-color: #fff;
  }

  .cert-search-input:focus {
    border-color: #cbd5e1;
    box-shadow: 0 0 0 3px rgba(27, 23, 99, 0.08);
  }

  .cert-search-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 14px;
  }

  .cert-select-wrapper {
    position: relative;
  }

  .cert-select {
    padding: 9px 36px 9px 16px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    font-size: 13px;
    font-weight: 700;
    color: #334155;
    background-color: #fff;
    cursor: pointer;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    transition: all 0.2s ease;
  }

  .cert-select:focus {
    border-color: #cbd5e1;
    box-shadow: 0 0 0 3px rgba(27, 23, 99, 0.08);
  }

  .cert-select-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
    font-size: 11px;
    pointer-events: none;
  }

  .cert-toggle-filter-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    background-color: #fff;
    color: #64748b;
    cursor: pointer;
    transition: all 0.2s ease;
  }

  .cert-toggle-filter-btn:hover, .cert-toggle-filter-btn.active {
    border-color: #cbd5e1;
    background-color: #f8fafc;
    color: #2e2050;
  }

  /* â”€â”€â”€ Cards Grid â”€â”€â”€ */
  .cert-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(230px, 1fr));
    gap: 20px;
  }

  /* â”€â”€â”€ Certificate Card â”€â”€â”€ */
  .certificate-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #eef2f7;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }

  .certificate-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(27, 23, 99, 0.08);
  }

  /* â”€â”€â”€ Mini Certificate Preview Box â”€â”€â”€ */
  .mini-cert-preview {
    position: relative;
    aspect-ratio: 1.414 / 1;
    width: 100%;
    overflow: hidden;
    background: #fff;
    border-bottom: 1px solid #f1f5f9;
    border-radius: 20px 20px 0 0;
  }

  .mini-cert-scaled {
    width: 29.7cm;
    height: 21cm;
    position: absolute;
    top: 0;
    left: 0;
    transform-origin: top left;
    pointer-events: none;
    background: #fff;
  }

  .cert-locked-overlay {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(2px);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #fff;
    gap: 8px;
    z-index: 10;
    border-radius: 20px 20px 0 0;
  }

  .cert-locked-overlay i {
    font-size: 24px;
    color: #fff;
  }

  .cert-locked-overlay span {
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #fff;
  }

  .cert-type-pill {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 11;
    font-size: 8px;
    font-weight: 800;
    padding: 2px 8px;
    border-radius: 99px;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .cert-type-pill.event {
    background-color: #6366f1;
  }

  .cert-type-pill.course {
    background-color: #10b981;
  }

  /* â”€â”€â”€ Card Body â”€â”€â”€ */
  .cert-card-body {
    padding: 16px 20px 0 20px;
    display: flex;
    flex-direction: column;
    gap: 8px;
    flex: 1;
  }

  .cert-card-title {
    font-size: 15px;
    font-weight: 700;
    color: #0f172a;
    line-height: 1.4;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .cert-card-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #64748b;
    font-weight: 500;
  }

  .cert-card-meta i {
    font-size: 13px;
    color: #94a3b8;
  }

  /* â”€â”€â”€ Card Footer Buttons â”€â”€â”€ */
  .cert-card-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 20px 20px;
    background: transparent;
    border-top: none;
    gap: 12px;
  }

  .btn-cert-download {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 9px 20px;
    border-radius: 99px;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    flex-grow: 1;
    background: #fff;
  }

  .btn-cert-view-circle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    border: none;
    flex-shrink: 0;
  }

  /* Unified Navy Theme Buttons */
  .certificate-card .btn-cert-download {
    border: 1.5px solid #2e2050;
    color: #2e2050;
  }
  .certificate-card .btn-cert-download:hover {
    background: #2e2050;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(27, 23, 99, 0.15);
  }
  .certificate-card .btn-cert-view-circle {
    background: #f8fafc;
    color: #2e2050;
    border: 1.5px solid #e2e8f0;
  }
  .certificate-card .btn-cert-view-circle:hover {
    background: #2e2050;
    color: #fff;
    border-color: #2e2050;
    transform: translateY(-2px);
    box-shadow: 0 4px 10px rgba(27, 23, 99, 0.15);
  }

  /* Disabled State Buttons */
  .btn-cert-download.disabled, .btn-cert-download:disabled {
    border: 1.5px solid #e2e8f0 !important;
    background: #f8fafc !important;
    color: #cbd5e1 !important;
    cursor: not-allowed;
  }

  .btn-cert-view-circle.disabled, .btn-cert-view-circle:disabled {
    background: #f8fafc !important;
    color: #cbd5e1 !important;
    cursor: not-allowed;
  }

  /* â”€â”€â”€ Client Pagination â”€â”€â”€ */
  .cert-pagination-container {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    margin-top: 40px;
    margin-bottom: 20px;
  }

  .cert-page-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 1px solid #e2e8f0;
    background-color: #fff;
    color: #64748b;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
    text-decoration: none;
  }

  .cert-page-btn:hover {
    border-color: #cbd5e1;
    background-color: #f8fafc;
    color: #2e2050;
  }

  .cert-page-btn.active {
    background-color: #2e2050;
    border-color: #2e2050;
    color: #fff;
    box-shadow: 0 4px 10px rgba(27, 23, 99, 0.3);
  }

  .cert-page-btn.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background-color: #f8fafc;
    color: #cbd5e1;
    border-color: #e2e8f0;
  }

  /* â”€â”€â”€ Empty State â”€â”€â”€ */
  .cert-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    padding: 64px 32px;
    background: #fff;
    border: 1.5px dashed #dbe3ef;
    border-radius: 20px;
    text-align: center;
  }

  .cert-empty-icon {
    width: 72px;
    height: 72px;
    border-radius: 20px;
    background: linear-gradient(135deg, #eef2ff 0%, #f0fdf4 100%);
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .cert-empty-icon i {
    font-size: 32px;
    color: #a5b4d0;
  }

  .cert-empty h4 {
    font-size: 18px;
    font-weight: 800;
    color: #2e2050;
    margin: 0;
  }

  .cert-empty p {
    font-size: 14px;
    color: #64748b;
    margin: 0;
    max-width: 320px;
    line-height: 1.6;
  }

  /* Responsive Design */
  @media (max-width: 1024px) {
    .cert-cards-grid {
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    }
  }

  @media (max-width: 768px) {
    .cert-tabs-filter-container {
      flex-direction: column;
      align-items: stretch;
      gap: 20px;
    }
    .cert-nav-tabs {
      flex-wrap: wrap;
      justify-content: center;
    }
    .cert-filter-widgets {
      flex-direction: column;
      align-items: stretch;
    }
    .cert-search-wrapper, .cert-select-wrapper {
      width: 100%;
    }
    .cert-toggle-filter-btn {
      width: 100%;
      height: 40px;
    }
    .cert-cards-grid {
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    }
  }

  @media (max-width: 600px) {
    .cert-cards-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush

@section('content')
  <div class="courses-page cert-page">

    {{-- â”€â”€â”€ Hero Header â”€â”€â”€ --}}
    <section class="top-page">
      <div class="glow-circle glow-circle-1"></div>
      <div class="glow-circle glow-circle-2"></div>
      <div class="top-page-inner">
        <div class="top-page-content">
          <div class="title-page">
            <span class="badge-top">
              <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 3l1.912 5.813a2 2 0 001.899 1.374h6.098l-4.931 3.582a2 2 0 00-.728 2.236l1.912 5.813-4.931-3.582a2 2 0 00-2.342 0l-4.931 3.582 1.912-5.813a2 2 0 00-.728-2.236L2.091 10.187h6.098a2 2 0 001.899-1.374L12 3z" />
              </svg>
              <span>RIWAYAT KEGIATAN</span>
            </span>
            <h1>Riwayat <span>Kegiatan</span></h1>
            <h5>Semua aktivitas, kelas, dan event yang pernah Anda ikuti atau ajar, lengkap dengan status dan sertifikat.</h5>
          </div>
        </div>

        {{-- Stats di hero â”€â”€â”€ --}}
        @php
          $allItems = $historyItems ?? collect();
          $totalItems = $allItems->count();
          $totalDone = $allItems->where('statusLabel', 'Selesai')->count();
          $totalCerts = $allItems->filter(fn($i) => !empty($i['certificate']) && !empty($i['certificate']->certificate_number))->count();
          $years = $allItems->map(function($item) {
              return $item['date'] ? \Carbon\Carbon::parse($item['date'])->year : null;
          })->filter()->unique()->sortDesc();
        @endphp
        <div style="display:flex; flex-direction:column; gap:12px; flex-shrink:0;">
          <div class="upcoming-card">
            <div class="upcoming-icon">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
              </svg>
            </div>
            <div class="upcoming-text">
              <span class="upcoming-label">Total Kegiatan</span>
              <span class="upcoming-count">{{ $totalItems }}</span>
            </div>
          </div>
          <div class="upcoming-card">
            <div class="upcoming-icon" style="background:#20b386; box-shadow:0 4px 12px rgba(32,179,134,0.3);">
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="upcoming-text">
              <span class="upcoming-label">Sertifikat</span>
              <span class="upcoming-count">{{ $totalCerts }}</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    {{-- â”€â”€â”€ Tabs & Filters Header â”€â”€â”€ --}}
    @if(!$allItems->isEmpty())
    <div class="cert-tabs-filter-container">
      <!-- Left Side: Tabs Navigation -->
      <div class="cert-nav-tabs">
        <button class="cert-tab-btn active" data-filter="all">Semua Sertifikat</button>
        <button class="cert-tab-btn" data-filter="event">Event</button>
        <button class="cert-tab-btn" data-filter="course">Course</button>
      </div>

      <!-- Right Side: Filters & Search -->
      <div class="cert-filter-widgets">
        <div class="cert-search-wrapper">
          <input type="text" id="certSearchInput" placeholder="Cari sertifikat..." class="cert-search-input">
          <i class="bi bi-search cert-search-icon"></i>
        </div>

        <div class="cert-select-wrapper">
          <select id="certYearSelect" class="cert-select">
            <option value="all">Semua Tahun</option>
            @foreach($years as $y)
              <option value="{{ $y }}">{{ $y }}</option>
            @endforeach
          </select>
          <i class="bi bi-chevron-down cert-select-icon"></i>
        </div>

        <div class="cert-select-wrapper">
          <select id="certSortSelect" class="cert-select">
            <option value="newest">Terbaru</option>
            <option value="oldest">Terlama</option>
          </select>
          <i class="bi bi-chevron-down cert-select-icon"></i>
        </div>

        <button class="cert-toggle-filter-btn" id="certToggleHasCert" title="Tampilkan Hanya yang Memiliki Sertifikat">
          <i class="bi bi-sliders"></i>
        </button>
      </div>
    </div>
    @endif

    {{-- â”€â”€â”€ Content â”€â”€â”€ --}}
    @if($allItems->isEmpty())
      <div class="cert-empty">
        <div class="cert-empty-icon">
          <i class="bi bi-journal-x"></i>
        </div>
        <h4>Belum Ada Kegiatan</h4>
        <p>Anda belum memiliki riwayat kegiatan yang selesai. Kegiatan yang telah diselesaikan akan muncul di sini.</p>
      </div>
    @else
      @php
        $eventIndex = 0;
        $courseIndex = 0;
      @endphp
      <div class="cert-cards-grid" id="certCardsGrid">
        @foreach($allItems as $index => $item)
          @php
            $cert       = $item['certificate'] ?? null;
            $hasCert    = !empty($cert) && !empty($cert->certificate_number);
            $certIssued = $hasCert && in_array($cert->status ?? '', ['sent', 'published']);
            $isHighlight = !empty($item['highlight']);
            $dateText   = $item['date'] ? \Carbon\Carbon::parse($item['date'])->translatedFormat('d M Y') : '-';
            $type       = strtolower($item['type'] ?? 'course');

            // Role label map
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
            $roleCode = $cert ? strtoupper(trim($cert->type_code)) : 'TRN';
            $roleLabel = $roleMap[$roleCode] ?? 'Instruktur';
          @endphp

          <div class="certificate-card {{ $isHighlight ? 'is-highlight' : '' }}"
               data-type="{{ $type }}"
               data-has-cert="{{ $hasCert ? 'yes' : 'no' }}"
               data-year="{{ $item['date'] ? \Carbon\Carbon::parse($item['date'])->year : '' }}"
               data-timestamp="{{ $item['date'] ? \Carbon\Carbon::parse($item['date'])->timestamp : 0 }}"
               data-title="{{ strtolower($item['title'] ?? '') }}">

            {{-- 1. Mini Certificate Preview Box --}}
            <div class="mini-cert-preview">
              <span class="cert-type-pill {{ $type }}">
                {{ $type }}
              </span>

              @if(!$hasCert)
                <div class="cert-locked-overlay">
                  <i class="bi bi-lock-fill"></i>
                  <span>Belum Diterbitkan</span>
                </div>
              @endif

              <div class="mini-cert-scaled">
                @include('trainer.certificates.certificate-pdf', [
                    'is_preview' => true,
                    'template' => $item['template'],
                    'context' => $type,
                    'event' => $type === 'event' ? $item['model'] : null,
                    'course' => $type === 'course' ? $item['model'] : null,
                    'user' => Auth::user(),
                    'issuedAt' => $cert?->issued_at ?? now(),
                    'certificateNumber' => $cert?->certificate_number ?? 'DRAFT-CERT',
                    'logosBase64' => $item['logosBase64'] ?? [],
                    'signaturesBase64' => $item['signaturesBase64'] ?? [],
                    'signaturesData' => $item['signaturesData'] ?? [],
                    'roleLabel' => $roleLabel
                ])
              </div>
            </div>

            {{-- 2. Card Content â”€â”€â”€ --}}
            <div class="cert-card-body">
              <h4 class="cert-card-title">{{ $item['title'] ?? '-' }}</h4>
              <div class="cert-card-meta">
                <i class="bi bi-calendar3"></i>
                <span>{{ $dateText }}</span>
              </div>
            </div>

            {{-- 3. Card Footer â”€â”€â”€ --}}
            <div class="cert-card-footer">
              @if($hasCert)
                <a class="btn-cert-download"
                   href="{{ $item['downloadUrl'] }}"
                   target="_blank"
                   title="Unduh Sertifikat">
                  <i class="bi bi-download"></i>
                  Unduh
                </a>
                <a class="btn-cert-view-circle"
                   href="{{ $item['showUrl'] }}"
                   title="Lihat Detail Sertifikat">
                  <i class="bi bi-eye"></i>
                </a>
              @else
                <button class="btn-cert-download disabled" disabled>
                  <i class="bi bi-hourglass-split"></i>
                  Menunggu
                </button>
                <button class="btn-cert-view-circle disabled" disabled>
                  <i class="bi bi-eye-slash"></i>
                </button>
              @endif
            </div>

          </div>
        @endforeach
      </div>

      <!-- Client-side Pagination Container -->
      <div class="cert-pagination-container" id="certPagination"></div>
    @endif

  </div>
@endsection

@push('scripts')
<script>
  (function () {
    const cardsGrid = document.getElementById('certCardsGrid');
    if (!cardsGrid) return;

    const cards = Array.from(document.querySelectorAll('.certificate-card'));
    const tabBtns = document.querySelectorAll('.cert-tab-btn');
    const searchInput = document.getElementById('certSearchInput');
    const yearSelect = document.getElementById('certYearSelect');
    const sortSelect = document.getElementById('certSortSelect');
    const toggleHasCertBtn = document.getElementById('certToggleHasCert');
    const paginationContainer = document.getElementById('certPagination');

    let currentFilter = 'all';
    let currentSearch = '';
    let currentYear = 'all';
    let currentSort = 'newest';
    let onlyHasCert = false;

    let currentPage = 1;
    const itemsPerPage = 8;
    let filteredCards = [];

    // Initialize
    init();

    function init() {
      // Event listeners for tabs
      tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
          tabBtns.forEach(b => b.classList.remove('active'));
          this.classList.add('active');
          currentFilter = this.dataset.filter;
          currentPage = 1;
          applyFiltersAndRender();
        });
      });

      // Event listener for search input
      if (searchInput) {
        searchInput.addEventListener('input', function () {
          currentSearch = this.value.trim().toLowerCase();
          currentPage = 1;
          applyFiltersAndRender();
        });
      }

      // Event listener for year select
      if (yearSelect) {
        yearSelect.addEventListener('change', function () {
          currentYear = this.value;
          currentPage = 1;
          applyFiltersAndRender();
        });
      }

      // Event listener for sort select
      if (sortSelect) {
        sortSelect.addEventListener('change', function () {
          currentSort = this.value;
          currentPage = 1;
          applyFiltersAndRender();
        });
      }

      // Event listener for onlyHasCert toggle
      if (toggleHasCertBtn) {
        toggleHasCertBtn.addEventListener('click', function () {
          onlyHasCert = !onlyHasCert;
          this.classList.toggle('active', onlyHasCert);
          currentPage = 1;
          applyFiltersAndRender();
        });
      }

      // Initial filter run
      applyFiltersAndRender();
    }

    function applyFiltersAndRender() {
      // 1. Filter
      filteredCards = cards.filter(card => {
        // Tab type filter
        const typeMatch = (currentFilter === 'all') || (card.dataset.type === currentFilter);
        
        // Search query filter
        const searchMatch = (currentSearch === '') || (card.dataset.title.includes(currentSearch));
        
        // Year filter
        const yearMatch = (currentYear === 'all') || (card.dataset.year === currentYear);
        
        // Toggle certificate filter
        const certMatch = !onlyHasCert || (card.dataset.hasCert === 'yes');

        return typeMatch && searchMatch && yearMatch && certMatch;
      });

      // 2. Sort
      filteredCards.sort((a, b) => {
        const timeA = parseInt(a.dataset.timestamp) || 0;
        const timeB = parseInt(b.dataset.timestamp) || 0;
        
        if (currentSort === 'newest') {
          return timeB - timeA;
        } else {
          return timeA - timeB;
        }
      });

      // 3. Update DOM order for sorted array
      filteredCards.forEach(card => cardsGrid.appendChild(card));

      // 4. Render current page & hide others
      renderPageItems();

      // 5. Render Pagination controls
      renderPaginationControls();

      // Trigger certificate preview resizing
      setTimeout(resizeMiniCertificates, 50);
    }

    function renderPageItems() {
      // Hide all cards first
      cards.forEach(card => card.style.display = 'none');

      // Calculate bounds
      const startIdx = (currentPage - 1) * itemsPerPage;
      const endIdx = startIdx + itemsPerPage;

      // Show matching cards for this page
      const pageItems = filteredCards.slice(startIdx, endIdx);
      pageItems.forEach(card => card.style.display = '');

      // Handle empty filtered results
      let emptyMsg = document.getElementById('certNoResultsMsg');
      if (filteredCards.length === 0) {
        if (!emptyMsg) {
          emptyMsg = document.createElement('div');
          emptyMsg.id = 'certNoResultsMsg';
          emptyMsg.className = 'cert-empty';
          emptyMsg.style.width = '100%';
          emptyMsg.style.gridColumn = '1 / -1';
          emptyMsg.innerHTML = `
            <div class="cert-empty-icon"><i class="bi bi-search"></i></div>
            <h4>Sertifikat Tidak Ditemukan</h4>
            <p>Tidak ada sertifikat yang cocok dengan pencarian atau filter Anda.</p>
          `;
          cardsGrid.appendChild(emptyMsg);
        } else {
          emptyMsg.style.display = '';
        }
      } else if (emptyMsg) {
        emptyMsg.style.display = 'none';
      }
    }

    function renderPaginationControls() {
      if (!paginationContainer) return;
      paginationContainer.innerHTML = '';

      const totalPages = Math.ceil(filteredCards.length / itemsPerPage);
      if (totalPages <= 1) {
        paginationContainer.style.display = 'none';
        return;
      }
      paginationContainer.style.display = 'flex';

      // Left Chevron
      const prevBtn = document.createElement('button');
      prevBtn.className = 'cert-page-btn' + (currentPage === 1 ? ' disabled' : '');
      prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
      prevBtn.disabled = (currentPage === 1);
      prevBtn.addEventListener('click', () => {
        if (currentPage > 1) {
          currentPage--;
          applyFiltersAndRender();
          scrollToGrid();
        }
      });
      paginationContainer.appendChild(prevBtn);

      // Page numbers
      for (let i = 1; i <= totalPages; i++) {
        const pageBtn = document.createElement('button');
        pageBtn.className = 'cert-page-btn' + (i === currentPage ? ' active' : '');
        pageBtn.innerText = i;
        pageBtn.addEventListener('click', () => {
          currentPage = i;
          applyFiltersAndRender();
          scrollToGrid();
        });
        paginationContainer.appendChild(pageBtn);
      }

      // Right Chevron
      const nextBtn = document.createElement('button');
      nextBtn.className = 'cert-page-btn' + (currentPage === totalPages ? ' disabled' : '');
      nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
      nextBtn.disabled = (currentPage === totalPages);
      nextBtn.addEventListener('click', () => {
        if (currentPage < totalPages) {
          currentPage++;
          applyFiltersAndRender();
          scrollToGrid();
        }
      });
      paginationContainer.appendChild(nextBtn);
    }

    function scrollToGrid() {
      const rect = cardsGrid.getBoundingClientRect();
      const elemTop = rect.top + window.scrollY - 100;
      window.scrollTo({
        top: elemTop,
        behavior: 'smooth'
      });
    }

    function resizeMiniCertificates() {
      document.querySelectorAll('.mini-cert-preview').forEach(container => {
        const rect = container.getBoundingClientRect();
        if (rect.width === 0) return; // Skip if hidden
        const scale = rect.width / 1122.5; // A4 standard width is 1122.5px
        const scaledDiv = container.querySelector('.mini-cert-scaled');
        if (scaledDiv) {
          scaledDiv.style.transform = `scale(${scale})`;
        }
      });
    }

    window.addEventListener('resize', resizeMiniCertificates);
  })();
</script>
@endpush

