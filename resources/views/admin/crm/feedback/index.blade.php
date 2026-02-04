@extends('layouts.crm')

@section('title', 'Feedback Analysis')

@section('styles')
<style>
    .nav-pills-custom .nav-link {
        color: var(--crm-text-muted);
        font-weight: 500;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .nav-pills-custom .nav-link.active {
        background: var(--crm-primary);
        color: white;
    }
    .rating-bar-container {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 0.75rem;
    }
    .rating-label {
        width: 30px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .progress-minimal {
        height: 10px;
        border-radius: 10px;
        background: #f1f5f9;
        flex-grow: 1;
    }
    .progress-minimal .progress-bar {
        border-radius: 10px;
    }
    .rating-count {
        width: 45px;
        font-size: 0.75rem;
        color: var(--crm-text-muted);
        text-align: right;
    }
    .feedback-card {
        border-bottom: 1px solid var(--crm-border);
        padding: 1.5rem;
    }
    .feedback-card:last-child {
        border-bottom: none;
    }
</style>
@endsection

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h3 class="fw-bold text-navy mb-1">Analisis Insight Pelanggan</h3>
        <p class="text-muted small mb-0">Evaluasi performa event dan kepuasan materi pembelajaran</p>
    </div>
    <div class="col-auto">
        <div class="nav nav-pills nav-pills-custom bg-white p-1 rounded-3 border shadow-sm">
            <a class="nav-link {{ ($type ?? 'event') == 'event' ? 'active' : '' }}" 
               href="{{ route('admin.crm.feedback.index', ['type' => 'event'] + request()->except('type', 'course_id')) }}">
               <i class="bi bi-calendar-event me-1"></i> Event
            </a>
            <a class="nav-link {{ ($type ?? 'event') == 'course' ? 'active' : '' }}" 
               href="{{ route('admin.crm.feedback.index', ['type' => 'course'] + request()->except('type', 'event_id')) }}">
               <i class="bi bi-journal-text me-1"></i> Course
            </a>
        </div>
    </div>
</div>

<!-- Filter Box -->
<div class="card-minimal mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.crm.feedback.index') }}" class="row g-3">
            <input type="hidden" name="type" value="{{ $type ?? 'event' }}">
            <div class="col-md-4">
                <label class="form-label smaller fw-bold text-muted">Pilih {{ ($type ?? 'event') == 'event' ? 'Event' : 'Course' }}</label>
                <select name="{{ ($type ?? 'event') == 'event' ? 'event_id' : 'course_id' }}" class="form-select form-select-sm">
                    <option value="">Semua {{ ($type ?? 'event') == 'event' ? 'Event' : 'Course' }}</option>
                    @if(($type ?? 'event') == 'event')
                        @foreach($allEvents as $item)
                            <option value="{{ $item->id }}" {{ ($eventId ?? null) == $item->id ? 'selected' : '' }}>{{ $item->title }}</option>
                        @endforeach
                    @else
                        @foreach($allCourses as $item)
                            <option value="{{ $item->id }}" {{ ($courseId ?? null) == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label smaller fw-bold text-muted">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label smaller fw-bold text-muted">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-navy btn-sm w-100" style="background: var(--crm-navy); color: white;">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Analysis Summary -->
    <div class="col-lg-4">
        <div class="card-minimal p-4 mb-4">
            <h6 class="fw-bold mb-4">Ringkasan Rating</h6>
            <div class="text-center mb-4">
                <div class="display-4 fw-bold text-navy">{{ number_format(($type ?? 'event') == 'event' ? ($avgRating ?? 0) : ($avgCourseRating ?? 0), 1) }}</div>
                <div class="text-warning fs-5" style="color: var(--crm-secondary) !important;">
                    @php $rating = ($type ?? 'event') == 'event' ? ($avgRating ?? 0) : ($avgCourseRating ?? 0); @endphp
                    @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= round($rating) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
                <p class="text-muted small mt-2">Berdasarkan {{ ($type ?? 'event') == 'event' ? $totalFeedback : $totalReviews }} ulasan masuk</p>
            </div>

            <hr class="my-4">

            @php 
                $dist = ($type ?? 'event') == 'event' ? $ratingDistribution : $courseRatingDistribution; 
                $total = ($type ?? 'event') == 'event' ? $totalFeedback : $totalReviews;
            @endphp
            
            @for($i = 5; $i >= 1; $i--)
                @php
                    $rowData = $dist->firstWhere('rating', $i);
                    $count = $rowData ? $rowData->count : 0;
                    $percent = $total > 0 ? ($count / $total) * 100 : 0;
                @endphp
                <div class="rating-bar-container">
                    <div class="rating-label">{{ $i }} <i class="bi bi-star-fill" style="font-size: 0.7rem; color: var(--crm-secondary);"></i></div>
                    <div class="progress progress-minimal">
                        <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%; background: var(--crm-primary);"></div>
                    </div>
                    <div class="rating-count">{{ $count }}</div>
                </div>
            @endfor
        </div>

        @if(($type ?? 'event') == 'event' && $avgSpeakerRating !== null)
        <div class="card-minimal p-4 border-opacity-25" style="background: var(--crm-accent-light); border: 1px solid rgba(109, 40, 217, 0.2);">
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 bg-white rounded-3 me-3 shadow-sm" style="color: var(--crm-primary);"><i class="bi bi-person-badge fs-4"></i></div>
                <div>
                    <h6 class="fw-bold mb-0">Rating Pemateri</h6>
                    <small class="text-muted">Rata-rata performa</small>
                </div>
            </div>
            <div class="h3 fw-bold mb-0" style="color: var(--crm-primary);">{{ number_format($avgSpeakerRating, 1) }} <span class="fs-6 fw-normal text-muted">/ 5.0</span></div>
        </div>
        @endif
    </div>

    <!-- Feedback List -->
    <div class="col-lg-8">
        @php
            $analysis = ($type ?? 'event') == 'event' ? $eventAnalysis : $courseAnalysis;
            $items = ($type ?? 'event') == 'event' ? ($analysis ? $analysis['feedbacks'] : $recentFeedbacks) : ($analysis ? $analysis['reviews'] : $recentReviews);
            $selectedTitle = ($type ?? 'event') == 'event' ? ($analysis ? $analysis['event']->title : 'Feedback Terbaru') : ($analysis ? $analysis['course']->name : 'Review Terbaru');
        @endphp

        <div class="card-minimal overflow-hidden">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="fw-bold mb-0">{{ $selectedTitle }}</h5>
            </div>
            <div class="card-body p-0">
                @forelse($items as $feedback)
                <div class="feedback-card">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <img src="{{ $feedback->user->avatar_url }}" class="customer-avatar me-3 border" style="width: 42px; height: 42px; border-radius: 50%;">
                            <div>
                                <div class="fw-bold text-navy small">{{ $feedback->user->name }}</div>
                                <div class="text-muted smaller" style="font-size: 0.7rem;">{{ $feedback->created_at->diffForHumans() }} 
                                    @if(!($analysis)) 
                                        &bull; {{ ($type ?? 'event') == 'event' ? ($feedback->event->title ?? 'N/A') : ($feedback->course->name ?? 'N/A') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="" style="font-size: 0.8rem; color: var(--crm-secondary);">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi bi-star{{ $i <= $feedback->rating ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                            @if(isset($feedback->speaker_rating) && $feedback->speaker_rating)
                                <span class="badge" style="background: var(--crm-accent-light); color: var(--crm-primary); border: 1px solid rgba(109, 40, 217, 0.2); font-size: 0.6rem;">SPEAKER: {{ $feedback->speaker_rating }}</span>
                            @endif
                        </div>
                    </div>
                    @if($feedback->comment ?? (isset($feedback->comment) ? $feedback->comment : null))
                        <p class="mb-0 text-dark small lh-base">
                            "{{ $feedback->comment ?? $feedback->comment }}"
                        </p>
                    @elseif(isset($feedback->review) || isset($feedback->comment))
                        <p class="mb-0 text-dark small lh-base">
                            "{{ $feedback->comment ?? ($feedback->review ?? '') }}"
                        </p>
                    @else
                        <span class="text-muted smaller"><i>Tidak ada komentar tertulis</i></span>
                    @endif
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-chat-square-dots text-muted opacity-25" style="font-size: 3rem;"></i>
                    <p class="text-muted small mt-2">Belum ada feedback yang dapat dianalisis</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
