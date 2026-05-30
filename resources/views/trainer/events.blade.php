@extends('layouts.trainer')

@section('title', 'Events - Trainer')

@push('styles')
<style>
main {
    padding: var(--spacing-2xl);
    background-color: var(--base-clr);
    overflow-y: auto;
    max-width: none;
    width: 100%;
}

.trainer-page main {
    margin: 0;
    padding: var(--spacing-2xl);
}

.top-page {
    background: linear-gradient(
        135deg,
        var(--main-navy-clr) 0%,
        var(--navy-dark) 100%
    );
    border-radius: var(--radius-2xl);
    padding: var(--spacing-3xl);
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(27, 23, 99, 0.15);
    margin-bottom: var(--spacing-2xl);
    width: 100%;
}

/* Decorative glow circles */
.glow-circle {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 0;
}

.glow-circle-1 {
    top: -80px;
    right: -80px;
    width: 192px;
    height: 192px;
    background: rgba(251, 191, 36, 0.1);
    filter: blur(60px);
}

.glow-circle-2 {
    bottom: -40px;
    left: -40px;
    width: 128px;
    height: 128px;
    background: rgba(99, 102, 241, 0.1);
    filter: blur(50px);
}

.top-page-inner {
    position: relative;
    z-index: 10;
    display: flex;
    flex-direction: row;
    justify-content: space-between;
    align-items: center;
    gap: var(--spacing-2xl);
}

.top-page-content {
    display: flex;
    flex-direction: column;
    gap: 24px;
    flex: 1;
}

.badge-top {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 100px;
    color: rgba(255, 255, 255, 0.9);
    font-size: 9px;
    font-weight: 900;
    letter-spacing: 2px;
    text-transform: uppercase;
    margin-bottom: 8px;
    backdrop-filter: blur(10px);
    width: fit-content;
}

.badge-top svg {
    width: 12px;
    height: 12px;
    color: var(--yellow-clr);
    flex-shrink: 0;
}

.title-page {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-width: 720px;
}

.title-page h1 {
    margin: 0;
    color: var(--white-clr);
    font-size: 40px;
    font-weight: 800;
    line-height: 1.2;
}

.title-page h1 span {
    color: var(--accent-yellow);
}

.title-page h5 {
    margin: 0;
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    font-weight: 500;
    line-height: 1.6;
    max-width: 620px;
}

.upcoming-card {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px 24px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.15);
    border-radius: 20px;
    min-width: 200px;
    backdrop-filter: blur(20px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
}

.upcoming-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 52px;
    height: 52px;
    background: var(--accent-yellow);
    border-radius: 14px;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(251, 176, 52, 0.3);
}

.upcoming-icon svg {
    width: 24px;
    height: 24px;
    color: var(--main-navy-clr);
}

.upcoming-text {
    display: flex;
    flex-direction: column;
    gap: var(--spacing-xs);
}

.upcoming-label {
    font-size: 9px;
    font-weight: 900;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    letter-spacing: 1.4px;
}

.upcoming-count {
    font-size: 18px;
    font-weight: 900;
    color: var(--white-clr);
    line-height: 1;
}

.search-filter-bar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: var(--spacing-sm);
    flex-wrap: nowrap;
    align-self: flex-end;
    margin-left: 0;
    width: auto;
    flex-shrink: 0;
}

.search-column {
    margin-top: 0;
    background-color: rgba(255, 255, 255, 0.16);
    padding: 10px 16px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    width: 100%;
    max-width: 280px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: none;
    height: 44px;
    transition: all 0.2s ease;
    backdrop-filter: blur(10px);
}

.search-column:hover,
.search-column:focus-within {
    background-color: rgba(255, 255, 255, 0.22);
    border-color: rgba(255, 255, 255, 0.35);
}

.search-column svg {
    color: rgba(255, 255, 255, 0.7);
    flex-shrink: 0;
    width: 16px;
    height: 16px;
    transition: color 0.2s ease;
}

.search-column:focus-within svg {
    color: rgba(255, 255, 255, 0.9);
}

.search-column input {
    border: none;
    outline: none;
    flex: 1;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.9);
    background: transparent;
    font-weight: 400;
}

.search-column input::placeholder {
    color: rgba(255, 255, 255, 0.6);
    font-weight: 400;
    font-weight: 400;
}

.filter-bar {
    gap: 8px;
    background-color: rgba(255, 255, 255, 0.16);
    padding: 10px 16px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.25);
    width: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: none;
    height: 44px;
    transition: all 0.2s ease;
    backdrop-filter: blur(10px);
}

.filter-bar:hover {
    background-color: rgba(255, 255, 255, 0.22);
    border-color: rgba(255, 255, 255, 0.35);
}

.filter-bar svg {
    color: rgba(255, 255, 255, 0.8);
    width: 16px;
    height: 16px;
    transition: color 0.2s ease;
}

.filter-bar:hover svg {
    color: rgba(255, 255, 255, 0.9);
}

.status-switcher {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #eef2f7;
}

.status-pill {
    border: 1px solid #d7deea;
    background: #fff;
    color: #5f6f85;
    border-radius: 999px;
    padding: 10px 16px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    user-select: none;
    white-space: nowrap;
}

.status-pill:hover {
    border-color: #bdc9da;
    color: #2b2350;
    background: #f9fafb;
}

.status-pill.active {
    background: #1b1763;
    border-color: #1b1763;
    color: #fff;
    box-shadow: 0 4px 12px rgba(27, 23, 99, 0.2);
}

.status-panel {
    display: none;
    animation: fadeIn 0.3s ease;
}

.status-panel.active {
    display: block;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(4px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.section-empty {
    padding: 48px 32px;
    background: #fff;
    border: 1px dashed #dbe3ef;
    border-radius: 16px;
    color: #7d98b3;
    font-size: 14px;
    font-weight: 500;
    text-align: center;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
}

.section-empty i {
    font-size: 36px;
    color: #cbd5e1;
}

/* Card Course - Similar to courses page */

.card-course {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
    padding: 0;
    animation: fadeIn 0.3s ease;
}

.card-item {
    background: var(--white-clr);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-height: 320px;
}

.card-item:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
}

.card-media {
    position: relative;
    width: 100%;
    height: 172px;
    overflow: hidden;
    background: #f0f0f0;
}

.card-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.badge-online {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 9px;
    font-weight: 800;
    color: rgba(255, 255, 255, 0.96);
    letter-spacing: 0.9px;
    text-transform: uppercase;
    z-index: 2;
    margin: 0;
    border: 1px solid rgba(255, 255, 255, 0.28);
}

.rating {
    position: absolute;
    bottom: 12px;
    right: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.95);
    padding: 8px 12px;
    border-radius: 8px;
    z-index: 2;
}

.rating svg {
    width: 16px;
    height: 16px;
    color: var(--yellow-clr);
}

.rating p {
    font-size: 12px;
    font-weight: 700;
    color: var(--main-navy-clr);
    margin: 0;
}

.card-content {
    padding: 14px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    flex-grow: 1;
}

.event-title {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.event-title h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--main-navy-clr);
    margin: 0;
    line-height: 1.3;
}

.event-title p {
    font-size: 12px;
    color: #666;
    margin: 0;
    line-height: 1.35;
}

.bottom-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 6px;
    margin-top: auto;
}

.total-participant-path {
    display: flex;
    gap: 12px;
    flex: 1;
}

.total-participant,
.total-fee {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-shrink: 1;
}

.total-participant svg,
.total-fee svg {
    width: 14px;
    height: 14px;
    color: #ffb446;
    flex-shrink: 0;
}

.total-participant p,
.total-fee p {
    font-size: 11px;
    font-weight: 600;
    color: #3d2a5a;
    margin: 0;
}

.btn-detail-course {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 26px;
    height: 26px;
    background: transparent;
    border: 1px solid #dce3f0;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    color: #5f6f85;
    flex-shrink: 0;
}

.btn-detail-course:hover {
    background: #f0f3f8;
    border-color: #c0cce0;
    color: #1b1763;
}

.btn-detail-course svg {
    width: 12px;
    height: 12px;
}

/* Responsive */
@media (max-width: 768px) {
    main {
        padding: var(--spacing-2xl);
    }

    .top-page {
        flex-direction: column;
        align-items: stretch;
        gap: var(--spacing-md);
    }

    .top-page-inner {
        flex-direction: column;
        align-items: flex-start;
        gap: var(--spacing-md);
    }

    .search-filter-bar {
        margin-right: 0;
        justify-content: space-between;
        width: 100%;
    }

    .card-course {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 14px;
    }
}

@media (max-width: 600px) {
    main {
        padding: var(--spacing-lg);
    }

    .top-page {
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .search-filter-bar {
        flex-direction: column;
        align-items: stretch;
    }

    .search-column {
        max-width: 100%;
    }

    .card-course {
        grid-template-columns: 1fr;
        gap: var(--spacing-md);
    }

    .card-item {
        max-width: 100%;
    }
}

</style>
@endpush

@php
  $pageTitle = 'Events';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Events']
  ];
@endphp



@section('content')
  <div class="top-page">
    <div class="glow-circle glow-circle-1"></div>
    <div class="glow-circle glow-circle-2"></div>

    <div class="top-page-inner">
      <div class="top-page-content">
        <div class="title-page">
          <span class="badge-top">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path
                d="M12 3l1.912 5.813a2 2 0 001.899 1.374h6.098l-4.931 3.582a2 2 0 00-.728 2.236l1.912 5.813-4.931-3.582a2 2 0 00-2.342 0l-4.931 3.582 1.912-5.813a2 2 0 00-.728-2.236L2.091 10.187h6.098a2 2 0 001.899-1.374L12 3z" />
            </svg>
            <span>SCHEDULE HUB + ACADEMIC EXCELLENCE</span>
          </span>
          <h1>Mastering the <br /><span>Session Ledger.</span></h1>
          <h5>
            Orchestrate your teaching commitments with precision. Track,
            manage, and excel in every session.
          </h5>
        </div>
      </div>
      <div class="search-filter-bar">
        <div class="search-column">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search"
            viewBox="0 0 16 16">
            <path
              d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
          </svg>
          <input type="text" placeholder="Lookup Session..." />
        </div>
        <button class="filter-bar">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-funnel"
            viewBox="0 0 16 16">
            <path
              d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
          </svg>
        </button>
      </div>
    </div>
  </div>
  @php
    $statusData = [
      ['id' => 'events-ongoing', 'label' => 'Sedang Berlangsung', 'data' => $ongoingEvents ?? collect()],
      ['id' => 'events-upcoming', 'label' => 'Mendatang', 'data' => $upcomingEvents ?? collect()],
      ['id' => 'events-finished', 'label' => 'Selesai', 'data' => $finishedEvents ?? collect()],
    ];
  @endphp

  <section id="events-status-board" class="status-board">
    <div class="status-switcher" role="tablist" aria-label="Filter status event">
      @foreach($statusData as $index => $status)
        <button class="status-pill {{ $index === 0 ? 'active' : '' }}" type="button" data-target="{{ $status['id'] }}"
          role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
          {{ $status['label'] }}
        </button>
      @endforeach
    </div>

    @foreach($statusData as $index => $status)
      <section id="{{ $status['id'] }}" class="status-panel {{ $index === 0 ? 'active' : '' }}" role="tabpanel">
        @if($status['data']->isEmpty())
          <div class="section-empty">
            <i class="bi bi-inbox"></i>
            <p>Belum ada event untuk kategori ini</p>
          </div>
        @else
          <div class="card-course">
            @foreach($status['data'] as $event)
              <a href="{{ route('trainer.events.show', $event->id) }}" class="card-item">
                <div class="card-media">
                  <p class="badge-online">{{ strtoupper($event->type ?? 'ONLINE SESSION') }}</p>

                  @php $posterUrl = $event->image_url; @endphp
                  @if(!empty($posterUrl))
                    <img src="{{ $posterUrl }}" alt="{{ $event->title }}" class="card-image" />
                  @else
                    <img src="https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=400&h=300&fit=crop"
                      alt="Default Image" class="card-image" />
                  @endif

                  <div class="rating">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill"
                      viewBox="0 0 16 16">
                      <path
                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                    </svg>
                    <p>5.0</p>
                  </div>
                </div>

                <div class="card-content">
                  <div class="event-title">
                    <h3>{{ Str::limit($event->title, 40) }}</h3>
                    <p>
                      {{ \Carbon\Carbon::parse($event->event_date)->format('M d') }} at
                      {{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('h:i A') : 'TBA' }}
                    </p>
                  </div>

                  <div class="bottom-card">
                    <div class="total-participant-path">
                      <div class="total-participant">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-people"
                          viewBox="0 0 16 16">
                          <path
                            d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                        </svg>
                        <p>{{ $event->participants_count ?? 0 }} Learners</p>
                      </div>
                      <div class="total-fee">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 16 16" fill="none"
                          stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round"
                          class="icon-outline">
                          <rect x="0.75" y="3" width="13.5" height="9" rx="2"></rect>
                          <path d="M0.75 6.5h13.5"></path>
                          <rect x="11" y="7.5" width="2" height="1.5" rx="0.5"></rect>
                        </svg>
                        @if((float) ($event->fee_trainer ?? 0) > 0)
                          <p>
                            Rp {{ number_format((float) $event->fee_trainer, 0, ',', '.') }}
                            @if((bool) ($event->is_fallback_to_event_price ?? false))
                            @endif
                          </p>
                        @else
                          <p>Fee belum diatur</p>
                        @endif
                      </div>
                    </div>
                    <button class="btn-detail-course" title="Detail">
                      <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        class="bi bi-arrow-right-short" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                          d="M4 8a.5.5 0 0 1 .5-.5h5.793L8.146 5.354a.5.5 0 1 1 .708-.708l3 3a.5.5 0 0 1 0 .708l-3 3a.5.5 0 0 1-.708-.708L10.293 8.5H4.5A.5.5 0 0 1 4 8" />
                      </svg>
                    </button>
                  </div>
                </div>
              </a>
            @endforeach
          </div>
        @endif
      </section>
    @endforeach
  </section>

@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const board = document.getElementById('events-status-board');
      if (!board) return;

      const pills = board.querySelectorAll('.status-pill');
      const panels = board.querySelectorAll('.status-panel');

      pills.forEach((pill) => {
        pill.addEventListener('click', function () {
          const target = this.dataset.target;

          pills.forEach((item) => item.classList.toggle('active', item === this));
          panels.forEach((panel) => panel.classList.toggle('active', panel.id === target));
        });
      });
    });
  </script>
@endpush
