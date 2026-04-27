@extends('layouts.trainer')

@section('title', 'Events - Trainer')

@php
  $pageTitle = 'Events';
  $breadcrumbs = [
    ['label' => 'Home', 'url' => route('trainer.dashboard')],
    ['label' => 'Events']
  ];
@endphp

@push('styles')
  @vite(['resources/css/trainer/events.css'])
@endpush

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
    $allEvents = ($events ?? collect())->values();
    $statusData = [
      ['id' => 'events-all', 'label' => 'Semua', 'data' => $allEvents],
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
              @php
                $eventCardImage = $event->image_url;
              @endphp
              <a href="{{ route('trainer.events.show', $event->id) }}" class="card-item">
                <div class="card-media">
                  <p class="badge-online">{{ strtoupper($event->type ?? 'ONLINE SESSION') }}</p>

                  @if(!empty($eventCardImage))
                    <img src="{{ $eventCardImage }}" alt="{{ $event->title }}" class="card-image" />
                  @else
                    <div class="card-image-empty">Gambar event belum diupload admin</div>
                  @endif

                  <div class="rating">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill"
                      viewBox="0 0 16 16">
                      <path
                        d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z" />
                    </svg>
                    <p>{{ number_format($event->feedbacks_avg_rating ?? 0, 1) }}</p>
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