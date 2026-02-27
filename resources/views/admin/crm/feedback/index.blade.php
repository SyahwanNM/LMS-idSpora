@extends('layouts.admin')

@section('title', 'Feedback Analysis')

@section('navbar')
    @include('partials.navbar-crm')
@endsection

@section('styles')
<style>
    /* Hero Header */
    .crm-hero {
        background: linear-gradient(135deg, #1A1D1F 0%, #2A2F34 100%);
        border-radius: 24px;
        padding: 32px;
        color: #fff;
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.05);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }

    .hero-label {
        background: rgba(109, 40, 217, 0.2);
        color: #a78bfa;
        padding: 6px 16px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        display: inline-block;
        margin-bottom: 16px;
        border: 1px solid rgba(139, 92, 246, 0.3);
    }

    .hero-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
    }

    /* Sidebar-like Nav */
    .crm-wrapper {
        display: flex;
        min-height: calc(100vh - 72px);
    }

    .crm-sidebar-new {
        width: 260px;
        background: #fff;
        padding: 24px;
        border-right: 1px solid #eee;
        flex-shrink: 0;
    }

    .crm-main {
        flex-grow: 1;
        padding: 32px;
        background-color: #F8F9FA;
    }

    .nav-menu-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 1px;
        margin-bottom: 16px;
        display: block;
        margin-top: 24px;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: #1e293b;
        text-decoration: none;
        border-radius: 12px;
        margin-bottom: 4px;
        font-weight: 600;
        transition: all 0.2s;
        gap: 12px;
    }

    .sidebar-link i {
        font-size: 1.1rem;
        color: #64748b;
    }

    .sidebar-link:hover {
        background-color: #f1f5f9;
        color: #6d28d9;
    }

    .sidebar-link.active {
        background-color: #6d28d9;
        color: #fff;
    }

    .sidebar-link.active i {
        color: #fff;
    }

    .nav-pills-custom .nav-link {
        color: #64748b;
        font-weight: 500;
        padding: 0.6rem 1.2rem;
        border-radius: 8px;
        transition: all 0.2s ease;
    }
    .nav-pills-custom .nav-link.active {
        background: #6d28d9;
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
        color: #64748b;
        text-align: right;
    }
    .feedback-card {
        border-bottom: 1px solid #eee;
        padding: 1.5rem;
    }
    .feedback-card:last-child {
        border-bottom: none;
    }
</style>
@endsection

@section('content')
<div class="crm-wrapper">
    <aside class="crm-sidebar-new d-none d-lg-block" style="position: sticky; top: 72px; height: calc(100vh - 72px);">
        <span class="nav-menu-label mt-0">DASHBOARD</span>
        <a href="{{ route('admin.crm.dashboard') }}" class="sidebar-link">
            <i class="bi bi-speedometer2"></i> Analitik Ringkas
        </a>
        <a href="{{ route('admin.crm.customers.index') }}" class="sidebar-link">
            <i class="bi bi-people"></i> Data Pelanggan
        </a>

        <span class="nav-menu-label">OPERASIONAL</span>
        <a href="{{ route('admin.crm.feedback.index') }}" class="sidebar-link active">
            <i class="bi bi-chat-heart"></i> Analisis Feedback
        </a>
        <a href="{{ route('admin.crm.broadcast.index') }}" class="sidebar-link">
            <i class="bi bi-megaphone"></i> Blast Broadcast
        </a>

        <span class="nav-menu-label">BANTUAN</span>
        <a href="{{ route('admin.crm.support.index') }}" class="sidebar-link">
            <i class="bi bi-headset"></i> Tiket Support
        </a>
    </aside>

    <main class="crm-main">
        <div class="crm-hero d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <span class="hero-label">Insight Center</span>
                <h1 class="hero-title">Analisis Feedback</h1>
                <p class="hero-subtitle mb-0">Evaluasi tingkat kepuasan peserta pada event dan course untuk peningkatan kualitas program.</p>
            </div>
            <div class="mt-4 mt-md-0">
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
    </main>
</div>
@endsection
