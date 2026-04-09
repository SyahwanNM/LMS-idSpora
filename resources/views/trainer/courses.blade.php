@extends('layouts.trainer')

@section('title', 'My Courses')

@php
    $pageTitle = 'Courses';
    $breadcrumbs = [
        ['label' => 'Home', 'url' => route('trainer.dashboard')],
        ['label' => 'Dashboard', 'url' => route('trainer.dashboard')],
        ['label' => 'Courses']
    ];
@endphp

@push('styles')
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.2/font/bootstrap-icons.min.css" />
@endpush

@section('content')
    <div class="courses-page">
        <section class="top-page">
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
                        <h5>Orchestrate your teaching commitments with precision. Track, manage, and excel in every session.
                        </h5>
                    </div>
                </div>

                <div class="search-filter-bar">
                    <div class="search-column">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-search" viewBox="0 0 16 16">
                            <path
                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                        </svg>
                        <input type="text" placeholder="Lookup Session..." />
                    </div>
                    <button class="filter-bar" type="button">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                            class="bi bi-funnel" viewBox="0 0 16 16">
                            <path
                                d="M1.5 1.5A.5.5 0 0 1 2 1h12a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-.128.334L10 8.692V13.5a.5.5 0 0 1-.342.474l-3 1A.5.5 0 0 1 6 14.5V8.692L1.628 3.834A.5.5 0 0 1 1.5 3.5zm1 .5v1.308l4.372 4.858A.5.5 0 0 1 7 8.5v5.306l2-.666V8.5a.5.5 0 0 1 .128-.334L13.5 3.308V2z" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>

        @php
            $statusData = [
                ['id' => 'courses-ongoing', 'label' => 'Sedang Berlangsung', 'data' => $ongoingCourses ?? collect()],
                ['id' => 'courses-upcoming', 'label' => 'Mendatang', 'data' => $upcomingCourses ?? collect()],
                ['id' => 'courses-finished', 'label' => 'Selesai', 'data' => $finishedCourses ?? collect()],
            ];
        @endphp

        <section id="courses-status-board" class="status-board">
            <div class="status-switcher" role="tablist" aria-label="Filter status course">
                @foreach($statusData as $index => $status)
                    <button class="status-pill {{ $index === 0 ? 'active' : '' }}" type="button"
                        data-target="{{ $status['id'] }}" role="tab" aria-selected="{{ $index === 0 ? 'true' : 'false' }}">
                        {{ $status['label'] }}
                    </button>
                @endforeach
            </div>

            @foreach($statusData as $index => $status)
                <section id="{{ $status['id'] }}" class="status-panel {{ $index === 0 ? 'active' : '' }}" role="tabpanel">
                    @if($status['data']->isEmpty())
                        <div class="section-empty">
                            <i class="bi bi-inbox"></i>
                            <p>Belum ada course untuk kategori ini</p>
                        </div>
                    @else
                        <div class="card-course">
                            @foreach($status['data'] as $course)
                                <article class="card-item">
                                    <div class="card-media {{ $course->card_thumbnail ? '' : 'no-image' }}">
                                        <p class="badge-online">{{ strtoupper($course->level ?? 'GENERAL') }}</p>
                                        <div class="rating">
                                            <i class="bi bi-star-fill"></i>
                                            <p>{{ number_format($course->reviews_avg_rating ?? 0, 1) }}</p>
                                        </div>

                                        @php $thumbUrl = $course->card_thumbnail_url; @endphp
                                        @if(!empty($thumbUrl))
                                            <img class="card-image" src="{{ $thumbUrl }}" alt="{{ $course->name }}">
                                        @else
                                            <div class="no-image-placeholder" aria-hidden="true">
                                                <i class="bi bi-image"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="card-content">
                                        <div class="course-title">
                                            <h3>{{ Str::limit($course->name, 44) }}</h3>
                                            <p>{{ Str::limit($course->description ?? 'Deskripsi belum tersedia.', 80) }}</p>
                                        </div>

                                        <div class="bottom-card">
                                            <div class="total-participant-path">
                                                <div class="total-participant">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                        class="bi bi-people" viewBox="0 0 16 16">
                                                        <path
                                                            d="M15 14s1 0 1-1-1-4-5-4-5 3-5 4 1 1 1 1zm-7.978-1L7 12.996c.001-.264.167-1.03.76-1.72C8.312 10.629 9.282 10 11 10c1.717 0 2.687.63 3.24 1.276.593.69.758 1.457.76 1.72l-.008.002-.014.002zM11 7a2 2 0 1 0 0-4 2 2 0 0 0 0 4m3-2a3 3 0 1 1-6 0 3 3 0 0 1 6 0M6.936 9.28a6 6 0 0 0-1.23-.247A7 7 0 0 0 5 9c-4 0-5 3-5 4q0 1 1 1h4.216A2.24 2.24 0 0 1 5 13c0-1.01.377-2.042 1.09-2.904.243-.294.526-.569.846-.816M4.92 10A5.5 5.5 0 0 0 4 13H1c0-.26.164-1.03.76-1.724.545-.636 1.492-1.256 3.16-1.275ZM1.5 5.5a3 3 0 1 1 6 0 3 3 0 0 1-6 0m3-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4" />
                                                    </svg>
                                                    <p>{{ number_format($course->enrollments_count ?? 0) }}</p>
                                                </div>

                                                <div class="total-path">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                        class="bi bi-stack" viewBox="0 0 16 16">
                                                        <path
                                                            d="m14.12 10.163 1.715.858c.22.11.22.424 0 .534L8.267 15.34a.6.6 0 0 1-.534 0L.165 11.555a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0l5.317-2.66zM7.733.063a.6.6 0 0 1 .534 0l7.568 3.784a.3.3 0 0 1 0 .535L8.267 8.165a.6.6 0 0 1-.534 0L.165 4.382a.299.299 0 0 1 0-.535z" />
                                                        <path
                                                            d="m14.12 6.576 1.715.858c.22.11.22.424 0 .534l-7.568 3.784a.6.6 0 0 1-.534 0L.165 7.968a.299.299 0 0 1 0-.534l1.716-.858 5.317 2.659c.505.252 1.1.252 1.604 0z" />
                                                    </svg>
                                                    <p>{{ number_format($course->modules_count ?? 0) }}</p>
                                                </div>
                                            </div>

                                            <a class="btn-detail-course" href="{{ route('trainer.detail-course', $course->id) }}"
                                                aria-label="Lihat detail {{ $course->name }}">
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endforeach
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const board = document.getElementById('courses-status-board');
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