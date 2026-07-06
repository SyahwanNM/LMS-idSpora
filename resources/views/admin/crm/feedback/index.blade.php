@extends('layouts.crm')

@section('title', 'Analisis Feedback')

@section('styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--crm-primary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before { content: ''; display: inline-block; width: 16px; height: 2px; background: var(--crm-primary); border-radius: 2px; }
    .page-title { font-size: 1.5rem; font-weight: 800; color: var(--crm-navy); letter-spacing: -0.8px; margin: 0; }
    .page-subtitle { font-size: 0.8rem; color: var(--crm-text-subtle); margin: 5px 0 0; }

    .tab-switcher { display: flex; gap: 4px; background: var(--crm-border-soft); border-radius: 10px; padding: 4px; }
    .tab-switcher a {
        font-size: 0.78rem; font-weight: 600; padding: 6px 16px;
        border-radius: 7px; text-decoration: none; color: var(--crm-text-muted);
        transition: all 0.2s;
    }
    .tab-switcher a.active { background: #fff; color: var(--crm-primary); box-shadow: 0 1px 3px rgba(0,0,0,0.08); }

    .rating-row { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
    .rating-row .lbl { font-size: 0.75rem; font-weight: 700; color: var(--crm-navy); width: 28px; }
    .rating-row .bar { flex: 1; height: 6px; background: var(--crm-border-soft); border-radius: 100px; overflow: hidden; }
    .rating-row .bar-fill { height: 100%; border-radius: 100px; background: var(--crm-primary); }
    .rating-row .cnt { font-size: 0.72rem; color: var(--crm-text-subtle); font-weight: 600; width: 28px; text-align: right; }

    .feedback-item {
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid var(--crm-border-soft);
        transition: background 0.15s;
    }
    .feedback-item:last-child { border-bottom: none; }
    .feedback-item:hover { background: #fafafa; }

    .star-rating { color: #f59e0b; font-size: 0.78rem; }

    .filter-input {
        border: 1px solid var(--crm-border); border-radius: 8px;
        padding: 0.45rem 0.85rem; font-size: 0.82rem; color: var(--crm-navy);
        background: var(--crm-border-soft); outline: none; width: 100%;
        transition: border-color 0.2s;
    }
    .filter-input:focus { border-color: var(--crm-primary-light); box-shadow: 0 0 0 3px rgba(124,58,237,0.1); background: #fff; }
</style>
@endsection

@section('content')
{{-- Page Header --}}
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <div class="page-eyebrow">Insight Center</div>
        <h1 class="page-title">Analisis Feedback</h1>
        <p class="page-subtitle">Evaluasi kepuasan peserta pada event dan course untuk peningkatan kualitas program.</p>
    </div>
    <div class="tab-switcher mt-3 mt-md-0">
        <a class="{{ ($type ?? 'event') == 'event' ? 'active' : '' }}"
           href="{{ route('admin.crm.feedback.index', ['type'=>'event'] + request()->except('type','course_id')) }}">
           <i class="bi bi-calendar-event me-1"></i> Event
        </a>
        <a class="{{ ($type ?? 'event') == 'course' ? 'active' : '' }}"
           href="{{ route('admin.crm.feedback.index', ['type'=>'course'] + request()->except('type','event_id')) }}">
           <i class="bi bi-journal-text me-1"></i> Course
        </a>
    </div>
</div>

{{-- Filter Bar --}}
<div class="card-minimal p-3 mb-4">
    <form method="GET" action="{{ route('admin.crm.feedback.index') }}" class="row g-3 align-items-end">
        <input type="hidden" name="type" value="{{ $type ?? 'event' }}">
        <div class="col-md-4">
            <label style="font-size:0.7rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;display:block;">Pilih {{ ($type ?? 'event') == 'event' ? 'Event' : 'Course' }}</label>
            <select name="{{ ($type ?? 'event') == 'event' ? 'event_id' : 'course_id' }}" class="filter-input" style="cursor:pointer;">
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
            <label style="font-size:0.7rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;display:block;">Dari Tanggal</label>
            <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
        </div>
        <div class="col-md-3">
            <label style="font-size:0.7rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;letter-spacing:0.5px;margin-bottom:6px;display:block;">Sampai Tanggal</label>
            <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-sm fw-700 w-100 px-3 hover-scale" style="background:var(--crm-primary);color:#fff;border-radius:8px;font-size:0.8rem;padding-top:0.5rem;padding-bottom:0.5rem;">
                <i class="bi bi-funnel-fill me-1"></i> Filter
            </button>
        </div>
    </form>
</div>

<div class="row g-4">
    {{-- Left: Rating Summary --}}
    <div class="col-lg-4">
        <div class="card-minimal p-4 mb-3">
            @php
                $rating = ($type ?? 'event') == 'event' ? ($avgRating ?? 0) : ($avgCourseRating ?? 0);
                $totalCount = ($type ?? 'event') == 'event' ? $totalFeedback : $totalReviews;
                $dist = ($type ?? 'event') == 'event' ? $ratingDistribution : $courseRatingDistribution;
                $total = $totalCount;
            @endphp
            <h6 class="fw-800 mb-4" style="font-size:0.9rem;color:var(--crm-navy);">Ringkasan Rating</h6>
            <div class="text-center mb-4">
                <div style="font-size:3rem;font-weight:800;color:var(--crm-navy);letter-spacing:-2px;line-height:1;">{{ number_format($rating, 1) }}</div>
                <div class="star-rating my-2">
                    @for($i=1; $i<=5; $i++)
                        <i class="bi bi-star{{ $i <= round($rating) ? '-fill' : '' }}"></i>
                    @endfor
                </div>
                <p style="font-size:0.75rem;color:var(--crm-text-subtle);">Dari {{ number_format($totalCount) }} ulasan masuk</p>
            </div>
            <hr style="border-color:var(--crm-border-soft);margin:1rem 0;">
            @for($i = 5; $i >= 1; $i--)
                @php
                    $rowData = $dist->firstWhere('rating', $i);
                    $count = $rowData ? $rowData->count : 0;
                    $percent = $total > 0 ? ($count / $total) * 100 : 0;
                @endphp
                <div class="rating-row">
                    <div class="lbl">{{ $i }}<i class="bi bi-star-fill" style="font-size:0.55rem;color:#f59e0b;margin-left:2px;"></i></div>
                    <div class="bar"><div class="bar-fill" style="width:{{ $percent }}%;"></div></div>
                    <div class="cnt">{{ $count }}</div>
                </div>
            @endfor
        </div>

        @if(($type ?? 'event') == 'event' && $avgSpeakerRating !== null)
        <div class="card-minimal p-4" style="background:var(--crm-accent-light);border:1px solid rgba(124,58,237,0.15);">
            <div class="d-flex align-items-center gap-3 mb-3">
                <div style="width:38px;height:38px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;color:var(--crm-primary);box-shadow:var(--crm-shadow-sm);">
                    <i class="bi bi-person-badge fs-5"></i>
                </div>
                <div>
                    <div style="font-weight:700;font-size:0.85rem;color:var(--crm-navy);">Rating Pemateri</div>
                    <div style="font-size:0.72rem;color:var(--crm-text-subtle);">Rata-rata performa speaker</div>
                </div>
            </div>
            <div style="font-size:2rem;font-weight:800;color:var(--crm-primary);letter-spacing:-1px;">{{ number_format($avgSpeakerRating, 1) }}
                <span style="font-size:0.9rem;font-weight:500;color:var(--crm-text-subtle);">/ 5.0</span>
            </div>
        </div>
        @endif
    </div>

    {{-- Right: List --}}
    <div class="col-lg-8">
        @php
            $analysis = ($type ?? 'event') == 'event' ? $eventAnalysis : $courseAnalysis;
            $isFiltered = ($type ?? 'event') == 'event' ? $eventId : $courseId;
            $items = ($type ?? 'event') == 'event'
                ? ($analysis ? $analysis['feedbacks'] : $recentFeedbacks)
                : ($analysis ? $analysis['reviews'] : $recentReviews);
            $selectedTitle = ($type ?? 'event') == 'event'
                ? ($analysis ? $analysis['event']->title : 'Semua Event')
                : ($analysis ? $analysis['course']->name : 'Semua Course');
        @endphp

        {{-- Per-Event/Course Summary List --}}
        @if(!$isFiltered)
        <div class="card-minimal mb-4">
            <div class="d-flex justify-content-between align-items-center p-4" style="border-bottom:1px solid var(--crm-border-soft);">
                <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Daftar Analisis Per {{ ($type ?? 'event') == 'event' ? 'Event' : 'Course' }}</h6>
            </div>
            <div class="table-responsive">
                <table class="crm-table">
                    <thead>
                        <tr>
                            <th style="padding-left:1.25rem;">{{ ($type ?? 'event') == 'event' ? 'Event' : 'Course' }}</th>
                            <th style="text-align:center;">Ulasan</th>
                            <th style="text-align:center;">Rating</th>
                            @if(($type ?? 'event') == 'event')
                            <th style="text-align:center;">Speaker</th>
                            @endif
                            <th style="padding-right:1.25rem;text-align:right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($type ?? 'event') == 'event' ? $events : $courses as $item)
                        <tr>
                            <td style="padding-left:1.25rem;">
                                <div style="font-weight:700;font-size:0.82rem;color:var(--crm-navy);">{{ ($type ?? 'event') == 'event' ? $item->title : $item->name }}</div>
                                @if(($type ?? 'event') == 'event')
                                <div style="font-size:0.72rem;color:var(--crm-text-subtle);"><i class="bi bi-calendar3 me-1"></i>{{ $item->event_date ? $item->event_date->format('d M Y') : '-' }}</div>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span class="badge-soft" style="background:var(--crm-border-soft);color:var(--crm-text-muted);">{{ ($type ?? 'event') == 'event' ? $item->feedbacks_count : $item->reviews_count }}</span>
                            </td>
                            <td style="text-align:center;">
                                <div class="d-flex align-items-center justify-content-center gap-1">
                                    <span style="font-weight:800;font-size:0.85rem;color:var(--crm-navy);">{{ number_format(($type ?? 'event') == 'event' ? $item->feedbacks_avg_rating : $item->reviews_avg_rating, 1) }}</span>
                                    <i class="bi bi-star-fill" style="font-size:0.65rem;color:#f59e0b;"></i>
                                </div>
                            </td>
                            @if(($type ?? 'event') == 'event')
                            <td style="text-align:center;">
                                <span style="font-size:0.82rem;color:var(--crm-text-muted);font-weight:600;">{{ number_format($item->feedbacks_avg_speaker_rating, 1) ?: '-' }}</span>
                            </td>
                            @endif
                            <td style="padding-right:1.25rem;text-align:right;">
                                <a href="{{ route('admin.crm.feedback.index', ['type'=>$type, (($type ?? 'event')=='event' ? 'event_id':'course_id')=>$item->id]) }}"
                                   class="btn btn-sm fw-700 px-3" style="background:var(--crm-navy);color:#fff;border-radius:8px;font-size:0.72rem;">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:2rem;color:var(--crm-text-subtle);font-size:0.82rem;">Tidak ada data untuk dianalisis</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding:1rem 1.25rem;border-top:1px solid var(--crm-border-soft);">
                {{ (($type ?? 'event') == 'event' ? $events : $courses)->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif

        {{-- Individual Feedback List --}}
        @if($isFiltered || $recentFeedbacks->isNotEmpty() || $recentReviews->isNotEmpty())
        <div class="card-minimal">
            <div class="d-flex justify-content-between align-items-center p-4" style="border-bottom:1px solid var(--crm-border-soft);">
                <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">
                    {{ $isFiltered ? 'Feedback: '.$selectedTitle : 'Ulasan Terbaru' }}
                </h6>
                @if($isFiltered)
                <a href="{{ route('admin.crm.feedback.index', ['type'=>$type]) }}"
                   style="font-size:0.78rem;color:var(--crm-text-muted);font-weight:600;text-decoration:none;">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                @endif
            </div>
            @forelse($items as $feedback)
            @php
                $userName = $feedback->user?->name ?? 'Pengguna (Dihapus)';
                $userAvatar = $feedback->user?->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=7c3aed&color=fff&bold=true';
            @endphp
            <div class="feedback-item">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <img src="{{ $userAvatar }}"
                             style="width:36px;height:36px;border-radius:9px;object-fit:cover;border:1.5px solid var(--crm-border);"
                             onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=7c3aed&color=fff&bold=true'">
                        <div>
                            <div style="font-weight:700;font-size:0.82rem;color:var(--crm-navy);">{{ $userName }}</div>
                            <div style="font-size:0.7rem;color:var(--crm-text-subtle);">{{ $feedback->created_at->diffForHumans() }}
                                @if(!$analysis) &bull; {{ ($type ?? 'event') == 'event' ? ($feedback->event?->title ?? 'N/A') : ($feedback->course?->name ?? 'N/A') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="star-rating">
                            @for($i=1; $i<=5; $i++)<i class="bi bi-star{{ $i <= $feedback->rating ? '-fill' : '' }}"></i>@endfor
                        </div>
                        @if(isset($feedback->speaker_rating) && $feedback->speaker_rating)
                        <span class="badge-soft mt-1" style="background:rgba(124,58,237,0.08);color:var(--crm-primary);font-size:0.62rem;">Speaker: {{ $feedback->speaker_rating }}/5</span>
                        @endif
                    </div>
                </div>
                @if($feedback->comment)
                <p style="font-size:0.82rem;color:var(--crm-navy-soft);margin:0;line-height:1.6;padding-left:44px;">"{{ $feedback->comment }}"</p>
                @else
                <p style="font-size:0.78rem;color:var(--crm-text-subtle);margin:0;font-style:italic;padding-left:44px;">Tidak ada komentar tertulis</p>
                @endif
            </div>
            @empty
            <div class="empty-state-wrapper">
                <div class="empty-state-icon hover-scale">
                    <i class="bi bi-chat-square-dots"></i>
                </div>
                <h6 class="fw-800 text-navy mb-1">Belum Ada Feedback</h6>
                <p class="text-muted smaller mb-0">Belum ada ulasan atau komentar yang masuk untuk kategori ini.</p>
            </div>
            @endforelse
        </div>
        @endif
    </div>
</div>
@endsection
