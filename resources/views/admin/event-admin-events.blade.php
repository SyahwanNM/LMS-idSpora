@extends('layouts.admin')

@section('title', 'My Events')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex align-items-center gap-3">
                        <h4 class="mb-0 text-dark d-flex align-items-center">
                            <i class="bi bi-calendar-event me-2"></i>
                            My Assigned Events
                        </h4>
                        <span class="badge bg-primary">{{ $events->count() }} Event</span>
                    </div>
                </div>

                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($events->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x" style="font-size:3rem;"></i>
                            <p class="mt-3">Tidak ada event yang di-assign ke akun Anda.</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($events as $event)
                                @php
                                    $now = \Carbon\Carbon::now(config('app.timezone'));
                                    $startAt = $event->start_at;
                                    $endAt   = $event->end_at;
                                    $isOngoing  = $startAt && $endAt && $now->between($startAt, $endAt);
                                    $isUpcoming = $startAt && $now->lt($startAt);
                                    $isFinished = $endAt && $now->gt($endAt);
                                    $statusLabel = $isOngoing ? 'Berlangsung' : ($isUpcoming ? 'Akan Datang' : ($isFinished ? 'Selesai' : '-'));
                                    $statusClass = $isOngoing ? 'bg-success' : ($isUpcoming ? 'bg-info text-dark' : 'bg-secondary');
                                @endphp
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border shadow-sm">
                                        @if($event->image_url)
                                            <img src="{{ $event->image_url }}"
                                                 class="card-img-top"
                                                 style="height:160px;object-fit:cover;"
                                                 alt="{{ $event->title }}"
                                                 onerror="this.style.display='none'">
                                        @endif
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex align-items-start justify-content-between mb-2">
                                                <h6 class="card-title mb-0 fw-bold" style="font-size:0.95rem;">{{ $event->title }}</h6>
                                                <span class="badge {{ $statusClass }} ms-2 flex-shrink-0" style="font-size:0.7rem;">
                                                    {{ $statusLabel }}
                                                </span>
                                            </div>

                                            <div class="text-muted small mb-3">
                                                <div class="d-flex align-items-center gap-1 mb-1">
                                                    <i class="bi bi-calendar3"></i>
                                                    <span>{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d M Y') : '-' }}</span>
                                                    @if($event->event_until_date && $event->event_until_date != $event->event_date)
                                                        <span>– {{ \Carbon\Carbon::parse($event->event_until_date)->format('d M Y') }}</span>
                                                    @endif
                                                </div>
                                                <div class="d-flex align-items-center gap-1 mb-1">
                                                    <i class="bi bi-geo-alt"></i>
                                                    <span>{{ $event->location ?? '-' }}</span>
                                                </div>
                                                @php
                                                    $activeRegs = $event->registrations()->where('status', 'active')->count();
                                                @endphp
                                                <div class="d-flex align-items-center gap-1">
                                                    <i class="bi bi-people"></i>
                                                    <span>{{ $activeRegs }} peserta aktif</span>
                                                </div>
                                            </div>

                                            <div class="mt-auto">
                                                <a href="{{ route('admin.events.show', $event) }}"
                                                   class="btn btn-primary btn-sm w-100">
                                                    <i class="bi bi-eye me-1"></i> Lihat Detail Event
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
