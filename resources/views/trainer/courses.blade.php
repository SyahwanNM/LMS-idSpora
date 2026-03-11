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
    <main>
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

                    <div class="upcoming-card">
                        <div class="upcoming-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                class="bi bi-journal-bookmark-fill" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M6 8V1h1v6.117L8.447 5.67l.553.553L6.5 8.723zm8 5V2.5A1.5 1.5 0 0 0 12.5 1h-1A1.5 1.5 0 0 0 10 2.5V13l2-1 2 1z" />
                                <path
                                    d="M0 2a2 2 0 0 1 2-2h9.5A2.5 2.5 0 0 1 14 2.5V14a1 1 0 0 1-1.447.894L12 14.618l-.553.276A1 1 0 0 1 10 14V2.5a.5.5 0 0 0-.5-.5H2a1 1 0 0 0-1 1v11.5a.5.5 0 0 0 .5.5H9v1H1.5A1.5 1.5 0 0 1 0 14.5z" />
                            </svg>
                        </div>
                        <div class="upcoming-text">
                            <span class="upcoming-label">TOTAL COURSES</span>
                            <span class="upcoming-count">{{ $courses->total() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="card-course">
            @forelse($courses as $course)
                <article class="card-item">
                    <div class="card-media {{ $course->card_thumbnail ? '' : 'no-image' }}">
                        <p class="badge-online">{{ strtoupper($course->level ?? 'GENERAL') }}</p>
                        <div class="rating">
                            <i class="bi bi-star-fill"></i>
                            <p>{{ number_format($course->reviews_avg_rating ?? 0, 1) }}</p>
                        </div>

                        @if($course->card_thumbnail)
                            <img class="card-image" src="{{ asset('storage/' . $course->card_thumbnail) }}"
                                alt="{{ $course->name }}">
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
            @empty
                <div
                    style="grid-column: 1 / -1; text-align: center; padding: 40px; background: white; border-radius: 16px; border: 1px dashed #cbd5e1;">
                    <i class="bi bi-inbox text-muted" style="font-size: 2.5rem; margin-bottom: 12px; display:block;"></i>
                    <h4 style="color: #1a237e; font-weight: 800; margin-bottom: 8px;">Belum Ada Course</h4>
                    <p style="color: #64748b; font-size: 14px; margin: 0;">Anda belum memiliki kelas aktif saat ini.</p>
                </div>
            @endforelse
        </section>

        @if($courses->hasPages())
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $courses->links() }}
            </div>
        @endif
    </main>
@endsection