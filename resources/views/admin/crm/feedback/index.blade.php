@extends('layouts.crm')

@section('title', 'Feedback Analysis')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-2 text-dark fw-bold">
            <i class="bi bi-chat-left-text me-2 text-primary"></i>Feedback Analysis
        </h2>
        <p class="text-muted mb-0">Analisis feedback dari peserta event dan course</p>
    </div>
</div>

<!-- Tab Navigation -->
<ul class="nav nav-tabs mb-4" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ ($type ?? 'event') == 'event' ? 'active' : '' }}" 
           href="{{ route('admin.crm.feedback.index', ['type' => 'event'] + request()->except('type', 'course_id')) }}">
            <i class="bi bi-calendar-event me-2"></i>Event Feedback
        </a>
    </li>
    <li class="nav-item" role="presentation">
        <a class="nav-link {{ ($type ?? 'event') == 'course' ? 'active' : '' }}" 
           href="{{ route('admin.crm.feedback.index', ['type' => 'course'] + request()->except('type', 'event_id')) }}">
            <i class="bi bi-book me-2"></i>Course Feedback
        </a>
    </li>
</ul>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(($type ?? 'event') == 'event')
<!-- ========== EVENT FEEDBACK SECTION ========== -->
<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3 bg-primary">
                    <i class="bi bi-chat-dots"></i>
                </div>
                <div>
                    <div class="text-muted small mb-1">Total Feedback</div>
                    <div class="h3 mb-0 fw-bold text-dark">{{ number_format($totalFeedback) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3 bg-success">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div>
                    <div class="text-muted small mb-1">Rating Rata-rata</div>
                    <div class="h3 mb-0 fw-bold text-dark">{{ number_format($avgRating ?? 0, 1) }}<small class="fs-6 text-muted">/5</small></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3 bg-info">
                    <i class="bi bi-person-badge"></i>
                </div>
                <div>
                    <div class="text-muted small mb-1">Rating Speaker</div>
                    @if($avgSpeakerRating !== null)
                        <div class="h3 mb-0 fw-bold text-dark">{{ number_format($avgSpeakerRating, 1) }}<small class="fs-6 text-muted">/5</small></div>
                        <small class="text-muted">({{ $speakerFeedbackCount ?? 0 }} feedback)</small>
                    @else
                        <div class="h3 mb-0 fw-bold text-muted">-</div>
                        <small class="text-muted">Belum ada data</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card-3d mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.crm.feedback.index') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label small text-muted fw-semibold">Filter Event</label>
                <select name="event_id" class="form-select">
                    <option value="">Semua Event</option>
                    @foreach($allEvents as $event)
                        <option value="{{ $event->id }}" {{ $eventId == $event->id ? 'selected' : '' }}>
                            {{ $event->title }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label small text-muted fw-semibold">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <input type="hidden" name="type" value="event">
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Rating Distribution Chart -->
    <div class="col-12 col-lg-6">
        <div class="card-3d">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title mb-0 fw-semibold text-dark">Distribusi Rating</h5>
            </div>
            <div class="card-body p-4">
                @if($ratingDistribution->count() > 0)
                    <div class="rating-chart">
                        @for($i = 5; $i >= 1; $i--)
                            @php
                                $ratingData = $ratingDistribution->firstWhere('rating', $i);
                                $count = $ratingData ? $ratingData->count : 0;
                                $percentage = $totalFeedback > 0 ? ($count / $totalFeedback) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-end me-3" style="width: 40px;">
                                    <span class="fw-bold text-dark">{{ $i }}</span>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 30px;">
                                        <div class="progress-bar 
                                            @if($i >= 4) bg-success
                                            @elseif($i == 3) bg-warning
                                            @else bg-danger
                                            @endif" 
                                            role="progressbar" 
                                            style="width: {{ $percentage }}%"
                                            aria-valuenow="{{ $percentage }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            <span class="fw-semibold text-white">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">Belum ada data rating</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Rated Events -->
    <div class="col-12 col-lg-6">
        <div class="card-3d h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title mb-0 fw-semibold text-dark">Event Terbaik</h5>
            </div>
            <div class="card-body p-4">
                @if($topRatedEvents->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($topRatedEvents as $event)
                            <div class="list-group-item px-0 border-0 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-dark">{{ $event->title }}</div>
                                        <small class="text-muted">{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d M Y') : '-' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary">{{ number_format($event->feedbacks_avg_rating ?? 0, 1) }}</div>
                                        <small class="text-muted">{{ $event->feedbacks_count }} feedback</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-x" style="font-size: 2rem; color: #ccc;"></i>
                        <p class="text-muted mt-3 small">Belum ada event dengan rating</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Events List with Feedback -->
<div class="card-3d mt-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h5 class="card-title mb-0 fw-semibold text-dark">Daftar Event dengan Feedback</h5>
    </div>
    <div class="card-body p-4">
        @if($events->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Event</th>
                            <th>Tanggal Event</th>
                            <th>Jumlah Feedback</th>
                            <th>Rating Rata-rata</th>
                            <th>Rating Speaker</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                            @php
                                $eventAvgRating = \App\Models\Feedback::where('event_id', $event->id)->avg('rating');
                                $eventAvgSpeaker = \App\Models\Feedback::where('event_id', $event->id)->whereNotNull('speaker_rating')->avg('speaker_rating');
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $event->title }}</div>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d M Y') : '-' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $event->feedbacks_count }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold text-dark me-2">{{ number_format($eventAvgRating ?? 0, 1) }}</span>
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= round($eventAvgRating ?? 0) ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($eventAvgSpeaker)
                                        <span class="badge bg-info">{{ number_format($eventAvgSpeaker, 1) }}</span>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.crm.feedback.index', ['type' => 'event', 'event_id' => $event->id]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $events->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-chat-left-text" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Belum ada event dengan feedback</p>
            </div>
        @endif
    </div>
</div>

<!-- Event Detail Analysis (if event selected) -->
@if($eventAnalysis)
<div class="card-3d mt-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-semibold text-dark">Analisis Detail: {{ $eventAnalysis['event']->title }}</h5>
        <a href="{{ route('admin.crm.feedback.index', ['type' => 'event']) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x-circle me-1"></i> Tutup
        </a>
    </div>
    <div class="card-body p-4">
        <!-- Event Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <div class="text-center p-3 bg-light rounded">
                    <div class="h4 mb-0 fw-bold text-primary">{{ number_format($eventAnalysis['event']->feedbacks_avg_rating ?? 0, 1) }}</div>
                    <small class="text-muted">Rating Rata-rata</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="text-center p-3 bg-light rounded">
                    <div class="h4 mb-0 fw-bold text-info">{{ number_format($eventAnalysis['event']->feedbacks_avg_speaker_rating ?? 0, 1) }}</div>
                    <small class="text-muted">Rating Speaker</small>
                </div>
            </div>
        </div>

        <!-- Rating Distribution for Event -->
        @if($eventAnalysis['ratingDistribution']->count() > 0)
        <div class="mb-4">
            <h6 class="fw-semibold mb-3">Distribusi Rating Event Ini</h6>
            <div class="rating-chart">
                @for($i = 5; $i >= 1; $i--)
                    @php
                        $ratingData = $eventAnalysis['ratingDistribution']->firstWhere('rating', $i);
                        $count = $ratingData ? $ratingData->count : 0;
                        $total = $eventAnalysis['feedbacks']->count();
                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                    @endphp
                    <div class="d-flex align-items-center mb-3">
                        <div class="text-end me-3" style="width: 40px;">
                            <span class="fw-bold text-dark">{{ $i }}</span>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar 
                                    @if($i >= 4) bg-success
                                    @elseif($i == 3) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    role="progressbar" 
                                    style="width: {{ $percentage }}%"
                                    aria-valuenow="{{ $percentage }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    <span class="fw-semibold text-white">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
        @endif

        <!-- Feedback List -->
        <div>
            <h6 class="fw-semibold mb-3">Daftar Feedback</h6>
            @if($eventAnalysis['feedbacks']->count() > 0)
                <div class="list-group">
                    @foreach($eventAnalysis['feedbacks'] as $feedback)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3" style="width:40px;height:40px;">
                                        <img src="{{ $feedback->user->avatar_url }}" alt="avatar" referrerpolicy="no-referrer">
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $feedback->user->name }}</div>
                                        <small class="text-muted">{{ $feedback->created_at->format('d M Y H:i') }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $feedback->rating ? '-fill text-warning' : ' text-muted' }}"></i>
                                        @endfor
                                        <span class="fw-bold ms-2">{{ $feedback->rating }}</span>
                                    </div>
                                    @if($feedback->speaker_rating)
                                        <div class="small">
                                            <span class="badge bg-info">Speaker: {{ $feedback->speaker_rating }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="mb-0 text-dark">{{ $feedback->comment }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                    <p class="text-muted mt-3 small">Belum ada feedback untuk event ini</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Recent Feedbacks -->
<div class="card-3d mt-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h5 class="card-title mb-0 fw-semibold text-dark">Feedback Terbaru</h5>
    </div>
    <div class="card-body p-4">
        @if($recentFeedbacks->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($recentFeedbacks as $feedback)
                    <div class="list-group-item px-0 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar-circle me-2" style="width:32px;height:32px;">
                                        <img src="{{ $feedback->user->avatar_url }}" alt="avatar" referrerpolicy="no-referrer">
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $feedback->user->name }}</div>
                                        <small class="text-muted">{{ $feedback->event->title }}</small>
                                    </div>
                                </div>
                                <p class="mb-0 text-muted small">{{ \Illuminate\Support\Str::limit($feedback->comment, 100) }}</p>
                            </div>
                            <div class="text-end ms-3">
                                <div class="mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $feedback->rating ? '-fill text-warning' : ' text-muted' }}"></i>
                                    @endfor
                                </div>
                                <small class="text-muted">{{ $feedback->created_at->format('d M Y') }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                <p class="text-muted mt-3 small">Belum ada feedback</p>
            </div>
        @endif
    </div>
</div>

@else
<!-- ========== COURSE FEEDBACK SECTION ========== -->
<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3 bg-primary">
                    <i class="bi bi-chat-dots"></i>
                </div>
                <div>
                    <div class="text-muted small mb-1">Total Review</div>
                    <div class="h3 mb-0 fw-bold text-dark">{{ number_format($totalReviews ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon me-3 bg-success">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div>
                    <div class="text-muted small mb-1">Rating Rata-rata</div>
                    <div class="h3 mb-0 fw-bold text-dark">{{ number_format($avgCourseRating ?? 0, 1) }}<small class="fs-6 text-muted">/5</small></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Section -->
<div class="card-3d mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.crm.feedback.index') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label small text-muted fw-semibold">Filter Course</label>
                <select name="course_id" class="form-select">
                    <option value="">Semua Course</option>
                    @foreach($allCourses ?? [] as $course)
                        <option value="{{ $course->id }}" {{ ($courseId ?? null) == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">Dari Tanggal</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">Sampai Tanggal</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-md-1">
                <label class="form-label small text-muted fw-semibold">&nbsp;</label>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <input type="hidden" name="type" value="course">
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Rating Distribution Chart -->
    <div class="col-12 col-lg-6">
        <div class="card-3d">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title mb-0 fw-semibold text-dark">Distribusi Rating</h5>
            </div>
            <div class="card-body p-4">
                @if(($courseRatingDistribution ?? collect())->count() > 0)
                    <div class="rating-chart">
                        @for($i = 5; $i >= 1; $i--)
                            @php
                                $ratingData = $courseRatingDistribution->firstWhere('rating', $i);
                                $count = $ratingData ? $ratingData->count : 0;
                                $percentage = ($totalReviews ?? 0) > 0 ? ($count / $totalReviews) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center mb-3">
                                <div class="text-end me-3" style="width: 40px;">
                                    <span class="fw-bold text-dark">{{ $i }}</span>
                                    <i class="bi bi-star-fill text-warning"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="progress" style="height: 30px;">
                                        <div class="progress-bar 
                                            @if($i >= 4) bg-success
                                            @elseif($i == 3) bg-warning
                                            @else bg-danger
                                            @endif" 
                                            role="progressbar" 
                                            style="width: {{ $percentage }}%"
                                            aria-valuenow="{{ $percentage }}" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                            <span class="fw-semibold text-white">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                        <p class="text-muted mt-3">Belum ada data rating</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Top Rated Courses -->
    <div class="col-12 col-lg-6">
        <div class="card-3d h-100">
            <div class="card-header bg-transparent border-0 pt-4 px-4">
                <h5 class="card-title mb-0 fw-semibold text-dark">Course Terbaik</h5>
            </div>
            <div class="card-body p-4">
                @if(($topRatedCourses ?? collect())->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($topRatedCourses as $course)
                            <div class="list-group-item px-0 border-0 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-dark">{{ $course->name }}</div>
                                        <small class="text-muted">{{ $course->level ?? '-' }}</small>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-primary">{{ number_format($course->reviews_avg_rating ?? 0, 1) }}</div>
                                        <small class="text-muted">{{ $course->reviews_count }} review</small>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-book-x" style="font-size: 2rem; color: #ccc;"></i>
                        <p class="text-muted mt-3 small">Belum ada course dengan rating</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Courses List with Reviews -->
<div class="card-3d mt-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h5 class="card-title mb-0 fw-semibold text-dark">Daftar Course dengan Review</h5>
    </div>
    <div class="card-body p-4">
        @if(($courses ?? collect())->count() > 0)
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Course</th>
                            <th>Level</th>
                            <th>Jumlah Review</th>
                            <th>Rating Rata-rata</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                            @php
                                $courseAvgRating = \App\Models\Review::where('course_id', $course->id)->avg('rating');
                            @endphp
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $course->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $course->level ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $course->reviews_count }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold text-dark me-2">{{ number_format($courseAvgRating ?? 0, 1) }}</span>
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= round($courseAvgRating ?? 0) ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.crm.feedback.index', ['type' => 'course', 'course_id' => $course->id]) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $courses->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-book" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Belum ada course dengan review</p>
            </div>
        @endif
    </div>
</div>

<!-- Course Detail Analysis (if course selected) -->
@if($courseAnalysis ?? null)
<div class="card-3d mt-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-semibold text-dark">Analisis Detail: {{ $courseAnalysis['course']->name }}</h5>
        <a href="{{ route('admin.crm.feedback.index', ['type' => 'course']) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-x-circle me-1"></i> Tutup
        </a>
    </div>
    <div class="card-body p-4">
        <!-- Course Statistics -->
        <div class="row g-3 mb-4">
            <div class="col-md-12">
                <div class="text-center p-3 bg-light rounded">
                    <div class="h4 mb-0 fw-bold text-primary">{{ number_format($courseAnalysis['course']->reviews_avg_rating ?? 0, 1) }}</div>
                    <small class="text-muted">Rating Rata-rata</small>
                </div>
            </div>
        </div>

        <!-- Rating Distribution for Course -->
        @if($courseAnalysis['ratingDistribution']->count() > 0)
        <div class="mb-4">
            <h6 class="fw-semibold mb-3">Distribusi Rating Course Ini</h6>
            <div class="rating-chart">
                @for($i = 5; $i >= 1; $i--)
                    @php
                        $ratingData = $courseAnalysis['ratingDistribution']->firstWhere('rating', $i);
                        $count = $ratingData ? $ratingData->count : 0;
                        $total = $courseAnalysis['reviews']->count();
                        $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                    @endphp
                    <div class="d-flex align-items-center mb-3">
                        <div class="text-end me-3" style="width: 40px;">
                            <span class="fw-bold text-dark">{{ $i }}</span>
                            <i class="bi bi-star-fill text-warning"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar 
                                    @if($i >= 4) bg-success
                                    @elseif($i == 3) bg-warning
                                    @else bg-danger
                                    @endif" 
                                    role="progressbar" 
                                    style="width: {{ $percentage }}%"
                                    aria-valuenow="{{ $percentage }}" 
                                    aria-valuemin="0" 
                                    aria-valuemax="100">
                                    <span class="fw-semibold text-white">{{ $count }} ({{ number_format($percentage, 1) }}%)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
        @endif

        <!-- Review List -->
        <div>
            <h6 class="fw-semibold mb-3">Daftar Review</h6>
            @if($courseAnalysis['reviews']->count() > 0)
                <div class="list-group">
                    @foreach($courseAnalysis['reviews'] as $review)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle me-3" style="width:40px;height:40px;">
                                        <img src="{{ $review->user->avatar_url }}" alt="avatar" referrerpolicy="no-referrer">
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $review->user->name }}</div>
                                        <small class="text-muted">{{ $review->created_at->format('d M Y H:i') }}</small>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <div class="mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : ' text-muted' }}"></i>
                                        @endfor
                                        <span class="fw-bold ms-2">{{ $review->rating }}</span>
                                    </div>
                                </div>
                            </div>
                            @if($review->comment)
                            <div class="mt-2">
                                <p class="mb-0 text-dark">{{ $review->comment }}</p>
                            </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                    <p class="text-muted mt-3 small">Belum ada review untuk course ini</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endif

<!-- Recent Reviews -->
<div class="card-3d mt-4">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h5 class="card-title mb-0 fw-semibold text-dark">Review Terbaru</h5>
    </div>
    <div class="card-body p-4">
        @if(($recentReviews ?? collect())->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($recentReviews as $review)
                    <div class="list-group-item px-0 border-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="avatar-circle me-2" style="width:32px;height:32px;">
                                        <img src="{{ $review->user->avatar_url }}" alt="avatar" referrerpolicy="no-referrer">
                                    </div>
                                    <div>
                                        <div class="fw-semibold text-dark">{{ $review->user->name }}</div>
                                        <small class="text-muted">{{ $review->course->name }}</small>
                                    </div>
                                </div>
                                @if($review->comment)
                                <p class="mb-0 text-muted small">{{ \Illuminate\Support\Str::limit($review->comment, 100) }}</p>
                                @endif
                            </div>
                            <div class="text-end ms-3">
                                <div class="mb-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $review->rating ? '-fill text-warning' : ' text-muted' }}"></i>
                                    @endfor
                                </div>
                                <small class="text-muted">{{ $review->created_at->format('d M Y') }}</small>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-4">
                <i class="bi bi-inbox" style="font-size: 2rem; color: #ccc;"></i>
                <p class="text-muted mt-3 small">Belum ada review</p>
            </div>
        @endif
    </div>
</div>
@endif

@endsection

@section('styles')
<style>
.rating-chart .progress {
    border-radius: 8px;
    overflow: hidden;
}
.rating-chart .progress-bar {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
}
</style>
@endsection

