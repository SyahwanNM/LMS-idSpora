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
  /* ─── Certificate Page ─── */
  .cert-page {
    margin: 0;
    padding: 0;
  }

  /* ─── Stats Strip ─── */
  .cert-stats-strip {
    display: flex;
    gap: 16px;
    margin-bottom: 28px;
    flex-wrap: wrap;
  }

  .cert-stat-card {
    flex: 1;
    min-width: 150px;
    background: #fff;
    border: 1px solid #eef2f7;
    border-radius: 16px;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 2px 8px rgba(27, 23, 99, 0.06);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }

  .cert-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(27, 23, 99, 0.1);
  }

  .cert-stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .cert-stat-icon.yellow { background: rgba(251, 176, 52, 0.12); }
  .cert-stat-icon.green  { background: rgba(32, 179, 134, 0.12); }
  .cert-stat-icon.indigo { background: rgba(99, 102, 241, 0.12); }

  .cert-stat-icon i {
    font-size: 20px;
  }

  .cert-stat-icon.yellow i { color: #fbb034; }
  .cert-stat-icon.green  i { color: #20b386; }
  .cert-stat-icon.indigo i { color: #6366f1; }

  .cert-stat-info { display: flex; flex-direction: column; gap: 2px; }

  .cert-stat-label {
    font-size: 11px;
    font-weight: 700;
    color: #9aa8bd;
    text-transform: uppercase;
    letter-spacing: 0.8px;
  }

  .cert-stat-value {
    font-size: 22px;
    font-weight: 900;
    color: #1b1763;
    line-height: 1;
  }

  /* ─── Filter Bar ─── */
  .cert-filter-bar {
    display: flex;
    gap: 8px;
    margin-bottom: 24px;
    flex-wrap: wrap;
  }

  .cert-filter-pill {
    padding: 8px 18px;
    border-radius: 999px;
    border: 1px solid #dbe3ea;
    background: #fff;
    color: #5f6f85;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
  }

  .cert-filter-pill:hover {
    border-color: #b0bec8;
    color: #1b1763;
    background: #f4f6fb;
  }

  .cert-filter-pill.active {
    background: #1b1763;
    border-color: #1b1763;
    color: #fff;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.22);
  }

  /* ─── Empty State ─── */
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
    color: #1b1763;
    margin: 0;
  }

  .cert-empty p {
    font-size: 14px;
    color: #64748b;
    margin: 0;
    max-width: 320px;
    line-height: 1.6;
  }

  /* ─── Cards Grid ─── */
  .cert-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
  }

  /* ─── Certificate Card ─── */
  .certificate-card {
    background: #fff;
    border-radius: 20px;
    border: 1px solid #eef2f7;
    box-shadow: 0 4px 16px rgba(27, 23, 99, 0.07);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    position: relative;
  }

  .certificate-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 32px rgba(27, 23, 99, 0.13);
  }

  .certificate-card.is-highlight {
    border-color: #fbb034;
    box-shadow: 0 4px 16px rgba(251, 176, 52, 0.18);
  }

  /* Card accent bar */
  .cert-card-accent {
    height: 5px;
    width: 100%;
    background: linear-gradient(90deg, #1b1763 0%, #6366f1 100%);
    flex-shrink: 0;
  }

  .cert-card-accent.event {
    background: linear-gradient(90deg, #d59a10 0%, #fbb034 100%);
  }

  /* Card body */
  .cert-card-body {
    padding: 20px 22px 16px;
    display: flex;
    flex-direction: column;
    gap: 14px;
    flex: 1;
  }

  /* Card header row */
  .cert-card-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 12px;
  }

  .cert-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 800;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    flex-shrink: 0;
  }

  .cert-type-badge.course {
    background: rgba(99, 102, 241, 0.10);
    color: #4f46e5;
    border: 1px solid rgba(99, 102, 241, 0.18);
  }

  .cert-type-badge.event {
    background: rgba(251, 176, 52, 0.12);
    color: #b45309;
    border: 1px solid rgba(251, 176, 52, 0.25);
  }

  .cert-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
  }

  .cert-status-badge.done {
    background: rgba(32, 179, 134, 0.10);
    color: #199a5b;
    border: 1px solid rgba(32, 179, 134, 0.22);
  }

  .cert-status-badge.pending {
    background: rgba(251, 176, 52, 0.10);
    color: #b45309;
    border: 1px solid rgba(251, 176, 52, 0.22);
  }

  /* Card title */
  .cert-card-title {
    font-size: 15px;
    font-weight: 800;
    color: #1a1335;
    line-height: 1.4;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
  }

  /* Card meta */
  .cert-card-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #7d98b3;
    font-weight: 500;
  }

  .cert-card-meta i {
    font-size: 13px;
    color: #9aa8bd;
  }

  /* New label */
  .cert-new-label {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 800;
    color: #b45309;
    background: rgba(251, 176, 52, 0.13);
    border: 1px solid rgba(251, 176, 52, 0.28);
    border-radius: 999px;
    padding: 2px 9px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
  }

  /* Card footer */
  .cert-card-footer {
    padding: 14px 22px 18px;
    border-top: 1px solid #eef2f7;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    background: #fafbff;
  }

  .cert-number {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
  }

  .cert-number-label {
    font-size: 9px;
    font-weight: 800;
    color: #9aa8bd;
    text-transform: uppercase;
    letter-spacing: 0.8px;
  }

  .cert-number-code {
    font-size: 11px;
    font-weight: 700;
    color: #4f46e5;
    font-family: 'Fira Code', monospace;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 180px;
  }

  /* Download Button */
  .btn-cert-download {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 9px 18px;
    border-radius: 12px;
    background: #1b1763;
    color: #fff;
    font-size: 12px;
    font-weight: 800;
    text-decoration: none;
    transition: all 0.2s ease;
    border: none;
    cursor: pointer;
    flex-shrink: 0;
    letter-spacing: 0.2px;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.20);
  }

  .btn-cert-download:hover {
    background: #252590;
    color: #fff;
    box-shadow: 0 6px 18px rgba(27, 23, 99, 0.30);
    transform: translateY(-1px);
  }

  .btn-cert-download i {
    font-size: 13px;
  }

  /* Unavailable button */
  .btn-cert-unavailable {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 16px;
    border-radius: 12px;
    background: #f1f5f9;
    color: #b0b3c1;
    font-size: 12px;
    font-weight: 700;
    border: none;
    cursor: default;
    flex-shrink: 0;
  }

  .btn-cert-unavailable i {
    font-size: 13px;
  }

  /* ─── Responsive ─── */
  @media (max-width: 768px) {
    .cert-cards-grid {
      grid-template-columns: 1fr;
    }

    .cert-stats-strip {
      gap: 12px;
    }

    .cert-stat-card {
      min-width: 120px;
      padding: 16px 18px;
    }
  }

  @media (max-width: 500px) {
    .cert-stats-strip {
      flex-direction: column;
    }
  }
</style>
@endpush

@section('content')
  <div class="courses-page cert-page">

    {{-- ─── Hero Header ─── --}}
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

        {{-- Stats di hero --}}
        @php
          $allItems    = $historyItems ?? collect();
          $totalItems  = $allItems->count();
          $totalDone   = $allItems->where('statusLabel', 'Selesai')->count();
          $totalCerts  = $allItems->filter(fn($i) => !empty($i['certificate']) && !empty($i['certificate']->file_path))->count();
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

    {{-- ─── Filter Pills ─── --}}
    @if(!$allItems->isEmpty())
    <div class="cert-filter-bar" id="certFilterBar">
      <button class="cert-filter-pill active" data-filter="all">Semua</button>
      @if($allItems->where('type','course')->count() > 0)
        <button class="cert-filter-pill" data-filter="course">
          <i class="bi bi-journal-richtext" style="margin-right:4px;"></i>Kursus
        </button>
      @endif
      @if($allItems->where('type','event')->count() > 0)
        <button class="cert-filter-pill" data-filter="event">
          <i class="bi bi-calendar-event" style="margin-right:4px;"></i>Event
        </button>
      @endif
      @if($totalCerts > 0)
        <button class="cert-filter-pill" data-filter="has-cert">
          <i class="bi bi-award" style="margin-right:4px;"></i>Punya Sertifikat
        </button>
      @endif
    </div>
    @endif

    {{-- ─── Content ─── --}}
    @if($allItems->isEmpty())
      <div class="cert-empty">
        <div class="cert-empty-icon">
          <i class="bi bi-journal-x"></i>
        </div>
        <h4>Belum Ada Kegiatan</h4>
        <p>Anda belum memiliki riwayat kegiatan yang selesai. Kegiatan yang telah diselesaikan akan muncul di sini.</p>
      </div>
    @else
      <div class="cert-cards-grid" id="certCardsGrid">
        @foreach($allItems as $item)
          @php
            $cert       = $item['certificate'] ?? null;
            $hasCert    = !empty($cert) && !empty($cert->certificate_number) && !empty($cert->file_path);
            $isHighlight = !empty($item['highlight']);
            $dateText   = $item['date'] ? \Carbon\Carbon::parse($item['date'])->format('d M Y') : '-';
            $type       = strtolower($item['type'] ?? 'course');
            $isDone     = ($item['statusLabel'] ?? '') === 'Selesai';
          @endphp

          <div class="certificate-card {{ $isHighlight ? 'is-highlight' : '' }}"
               data-type="{{ $type }}"
               data-has-cert="{{ $hasCert ? 'yes' : 'no' }}">

            {{-- Accent bar --}}
            <div class="cert-card-accent {{ $type === 'event' ? 'event' : '' }}"></div>

            {{-- Card body --}}
            <div class="cert-card-body">

              {{-- Header: type badge + status badge --}}
              <div class="cert-card-header">
                <span class="cert-type-badge {{ $type }}">
                  @if($type === 'event')
                    <i class="bi bi-calendar-event"></i>
                  @else
                    <i class="bi bi-journal-richtext"></i>
                  @endif
                  {{ strtoupper($type) }}
                </span>

                <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
                  @if($isHighlight)
                    <span class="cert-new-label">
                      <i class="bi bi-bell-fill"></i> Baru
                    </span>
                  @endif
                  @if($isDone)
                    <span class="cert-status-badge done">
                      <i class="bi bi-check-circle-fill"></i> Selesai
                    </span>
                  @else
                    <span class="cert-status-badge pending">
                      <i class="bi bi-clock-fill"></i> {{ $item['statusLabel'] ?? '-' }}
                    </span>
                  @endif
                </div>
              </div>

              {{-- Title --}}
              <p class="cert-card-title">{{ $item['title'] ?? '-' }}</p>

              {{-- Date meta --}}
              <div class="cert-card-meta">
                <i class="bi bi-calendar3"></i>
                <span>{{ $dateText }}</span>
              </div>

            </div>

            {{-- Card footer --}}
            <div class="cert-card-footer">
              @if($hasCert)
                <div class="cert-number">
                  <span class="cert-number-label">No. Sertifikat</span>
                  <span class="cert-number-code" title="{{ $cert->certificate_number }}">{{ $cert->certificate_number }}</span>
                </div>
                <a class="btn-cert-download"
                   href="{{ $item['downloadUrl'] }}"
                   target="_blank"
                   title="Unduh Sertifikat">
                  <i class="bi bi-download"></i>
                  Unduh
                </a>
              @else
                <div class="cert-number">
                  <span class="cert-number-label">Sertifikat</span>
                  <span style="font-size:12px; color:#b0b3c1; font-weight:600;">Belum tersedia</span>
                </div>
                <button class="btn-cert-unavailable" disabled>
                  <i class="bi bi-hourglass-split"></i>
                  Menunggu
                </button>
              @endif
            </div>

          </div>
        @endforeach
      </div>
    @endif

  </div>
@endsection

@push('scripts')
<script>
  (function () {
    const pills = document.querySelectorAll('.cert-filter-pill');
    const cards = document.querySelectorAll('.certificate-card');

    pills.forEach(pill => {
      pill.addEventListener('click', function () {
        pills.forEach(p => p.classList.remove('active'));
        this.classList.add('active');

        const filter = this.dataset.filter;

        cards.forEach(card => {
          let show = false;
          if (filter === 'all') {
            show = true;
          } else if (filter === 'has-cert') {
            show = card.dataset.hasCert === 'yes';
          } else {
            show = card.dataset.type === filter;
          }
          card.style.display = show ? '' : 'none';
        });
      });
    });
  })();
</script>
@endpush