@extends('layouts.admin')

@section('title', 'Detail Event')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-3">
                            @if(auth()->user()?->role === 'admin')
                            <a href="{{ route('admin.add-event') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Back
                            </a>
                            @endif
                            <h4 class="mb-0 text-dark d-flex align-items-center">
                                <i class="bi bi-calendar-event me-2"></i>
                                Detail Event
                            </h4>
                        </div>
                        @if(auth()->user()?->role === 'admin')
                        <div class="d-flex gap-2">
                            @if((bool)($event->is_published ?? false))
                                <form action="{{ route('admin.events.unpublish', $event) }}" method="POST" class="d-inline" id="unpublishEventFormShow">
                                    @csrf
                                    <button type="button" class="btn btn-outline-danger" id="unpublishBtnShow">
                                        <i class="bi bi-megaphone me-1"></i> Batal Terbitkan
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.events.publish', $event) }}" method="POST" class="d-inline" id="publishEventFormShow">
                                    @csrf
                                    <button type="button" class="btn btn-success" id="publishBtnShow">
                                        <i class="bi bi-megaphone me-1"></i> Publish
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteEventModal">
                                <i class="bi bi-trash me-1"></i> Delete
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="row">
                        <!-- Event Image -->
                        <div class="col-lg-4 mb-4">
                            <div class="position-relative event-preview-wrapper">
                                @if(!empty($event->manage_action))
                                    <div class="manage-action-ribbon manage-action-{{ $event->manage_action }}">
                                        <span class="ribbon-text">{{ strtoupper($event->manage_action) }}</span>
                                    </div>
                                @endif
                                @if($event->image_url)
                                    <figure class="event-image-figure mb-0" data-bs-toggle="modal" data-bs-target="#imagePreviewModal" style="cursor:zoom-in;">
                                        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" 
                                             class="img-fluid rounded shadow-sm event-main-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <figcaption class="event-image-overlay small">
                                            <i class="bi bi-arrows-fullscreen me-1"></i> Klik untuk perbesar
                                        </figcaption>
                                    </figure>
                                @endif
                                @if(!$event->image_url)
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center no-image-block">
                                        <div class="text-center text-muted">
                                            <i class="bi bi-image" style="font-size: 3rem;"></i>
                                            <p class="mt-2 mb-0">No Image</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                            <!-- Event Details -->
                        <div class="col-lg-8">
                            <div class="mb-4">
                                <h2 class="text-dark mb-2">{{ $event->title }}</h2>
                                @if(!empty($event->short_description))
                                <p class="text-muted mb-3">{{ $event->short_description }}</p>
                                @endif
                                
                                <div class="row g-3 mb-4">
                                    @if($event->jenis !== 'Lomba')
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-fill text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Speaker</small>
                                                <strong>{{ $event->speaker }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-journal-text text-secondary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Material</small>
                                                <strong>{{ $event->materi ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt-fill text-success me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Location</small>
                                                <strong>{{ $event->location }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-diagram-3 text-dark me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Category</small>
                                                <strong>{{ $event->jenis ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-date text-info me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Date</small>
                                                @php
                                                    $startDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
                                                    $untilDate = !empty($event->event_until_date) ? \Carbon\Carbon::parse($event->event_until_date) : null;
                                                @endphp
                                                <strong>
                                                    @if($startDate)
                                                        @if($untilDate && $untilDate->ne($startDate))
                                                            @if($startDate->format('F Y') === $untilDate->format('F Y'))
                                                                {{ $startDate->format('d') }} – {{ $untilDate->format('d F Y') }}
                                                            @else
                                                                {{ $startDate->format('d F Y') }} – {{ $untilDate->format('d F Y') }}
                                                            @endif
                                                        @else
                                                            {{ $startDate->format('d F Y') }}
                                                        @endif
                                                    @else
                                                        TBA
                                                    @endif
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock text-warning me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Time</small>
                                                @php
                                                    $startTime = $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : null;
                                                    $endTime = !empty($event->event_until_time) 
                                                        ? \Carbon\Carbon::parse($event->event_until_time)->format('H:i') 
                                                        : (!empty($event->event_time_end) ? \Carbon\Carbon::parse($event->event_time_end)->format('H:i') : null);
                                                @endphp
                                                <strong>
                                                    @if($startTime)
                                                        {{ $startTime }}
                                                        @if($endTime) - {{ $endTime }} @endif
                                                        WIB
                                                    @else
                                                        TBA
                                                    @endif
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Old Harga Tiket section removed -->
                            </div>
                        </div>
                    </div>

                    @php
                        $dailyQrs = \App\Models\EventDailyQr::where('event_id', $event->id)->orderBy('qr_date')->get();
                        $isMultiDay = !empty($event->event_until_date)
                            && \Carbon\Carbon::parse($event->event_until_date)->gt(\Carbon\Carbon::parse($event->event_date));
                    @endphp

                    <!-- Tab Navigation Menu -->
                    <div class="mt-4 mb-4">
                        <ul class="nav nav-tabs nav-tabs-custom border-bottom pb-0 gap-1" id="eventDetailTabs" role="tablist" style="border-bottom: 2px solid #dee2e6 !important;">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active px-4 py-2.5 fw-semibold d-flex align-items-center gap-2" id="dashboard-tab" data-bs-toggle="tab" data-bs-target="#dashboard-pane" type="button" role="tab" aria-controls="dashboard-pane" aria-selected="true">
                                    <i class="bi bi-broadcast text-primary"></i>Dasbor Kehadiran & Peserta
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-2.5 fw-semibold d-flex align-items-center gap-2" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab" aria-controls="info-pane" aria-selected="false">
                                    <i class="bi bi-info-circle text-success"></i>Informasi Event & Rundown
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link px-4 py-2.5 fw-semibold d-flex align-items-center gap-2" id="docs-tab" data-bs-toggle="tab" data-bs-target="#docs-pane" type="button" role="tab" aria-controls="docs-pane" aria-selected="false">
                                    <i class="bi bi-folder2-open text-warning"></i>Dokumen & QR Absensi
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="eventDetailTabsContent">
                        <!-- TAB 1: Dasbor Kehadiran & Peserta -->
                        <div class="tab-pane fade show active" id="dashboard-pane" role="tabpanel" aria-labelledby="dashboard-tab" tabindex="0">
                            <!-- Real-time Attendance Dashboard -->
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="card border shadow-sm rounded-3">
                                        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="live-dot-ring me-1">
                                                    <span class="live-dot"></span>
                                                </div>
                                                @if($event->jenis === 'Lomba')
                                                    <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-broadcast text-primary me-2"></i>Monitoring pendaftaran per hari</h5>
                                                @else
                                                    <h5 class="mb-0 text-dark fw-bold"><i class="bi bi-broadcast text-primary me-2"></i>Live Monitoring Kehadiran (Real-time)</h5>
                                                @endif
                                            </div>
                                            <span class="badge bg-light text-dark border small" id="lastUpdatedBadge">Terakhir diupdate: -</span>
                                        </div>
                                        <div class="card-body p-4">
                                            <div class="row g-4">
                                                <!-- Stats and Chart Column -->
                                                <div class="col-xl-8">
                                                    <!-- Stats Cards -->
                                                    <div class="row g-3 mb-4" id="dailyStatsGrid">
                                                        <div class="col-md-4 static-card">
                                                            <div class="p-3 bg-white border rounded h-100 d-flex flex-column justify-content-center shadow-sm stats-card stats-card-primary" style="border-left: 4px solid #0d6efd !important;">
                                                                <small class="text-muted d-block mb-1">Total Pendaftar Aktif</small>
                                                                <h3 class="mb-0 fw-bold text-dark" id="activeParticipantsCount">-</h3>
                                                            </div>
                                                        </div>
                                                        @if($event->jenis === 'Lomba')
                                                        <div class="col-md-4 static-card">
                                                            <div class="p-3 bg-white border rounded h-100 d-flex flex-column justify-content-center shadow-sm" style="border-left: 4px solid #198754 !important;">
                                                                <small class="text-muted d-block mb-1">Total Pendaftar Team</small>
                                                                <h3 class="mb-0 fw-bold text-dark" id="teamParticipantsCount">-</h3>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4 static-card">
                                                            <div class="p-3 bg-white border rounded h-100 d-flex flex-column justify-content-center shadow-sm" style="border-left: 4px solid #fd7e14 !important;">
                                                                <small class="text-muted d-block mb-1">Total Pendaftar Individu</small>
                                                                <h3 class="mb-0 fw-bold text-dark" id="individualParticipantsCount">-</h3>
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                    <!-- Chart Container -->
                                                    <div class="bg-light p-3 border rounded shadow-sm">
                                                        @if($event->jenis === 'Lomba')
                                                            <h6 class="text-dark fw-semibold mb-3"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Grafik Pendaftaran per Hari</h6>
                                                        @else
                                                            <h6 class="text-dark fw-semibold mb-3"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Grafik Persentase Kehadiran per Hari</h6>
                                                        @endif
                                                        <div style="position: relative; height: 260px;">
                                                            <canvas id="attendanceChart"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Live Check-in Feed Column -->
                                                <div class="col-xl-4">
                                                    <div class="border rounded p-3 h-100 bg-white d-flex flex-column shadow-sm" style="max-height: 400px;">
                                                        @if($event->jenis === 'Lomba')
                                                            <h6 class="text-dark fw-semibold mb-3 border-bottom pb-2"><i class="bi bi-activity me-2 text-success"></i>Pendaftaran Terbaru</h6>
                                                        @else
                                                            <h6 class="text-dark fw-semibold mb-3 border-bottom pb-2"><i class="bi bi-activity me-2 text-success"></i>Feed Check-in Terbaru</h6>
                                                        @endif
                                                        <div class="overflow-y-auto flex-grow-1" id="liveCheckinFeed" style="max-height: 320px;">
                                                            <div class="text-center text-muted py-5 small">Memuat aktivitas...</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Registered Participants -->
                            @php
                                // Eager-load users for participant list
                                try {
                                    $registrations = $event->registrations()
                                        ->whereHas('user', function ($query) {
                                            $query->where('role', '!=', 'admin');
                                        })
                                        ->with(['user', 'paymentProofs', 'dailyAttendances', 'team.leader', 'team.registrations.user'])
                                        ->latest()
                                        ->get();
                                } catch (\Throwable $e) {
                                    $registrations = collect();
                                }
                            @endphp
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="border rounded p-4 {{ $registrations->count() ? 'bg-light' : 'bg-warning-subtle' }}">
                                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <h6 class="text-dark mb-0"><i class="bi bi-people me-2"></i>Registered Participants</h6>
                                                <span id="participantsCountBadge" class="badge {{ $registrations->count() ? 'bg-primary' : 'bg-secondary' }}">Total: {{ $registrations->count() }}</span>
                                                <a href="{{ route('admin.events.export-participants', $event) }}" class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 ms-2 shadow-sm" id="exportParticipantsExcelBtn" title="Export Excel Registered Participants">
                                                    <i class="bi bi-file-earmark-excel-fill"></i> Export Excel
                                                </a>
                                            </div>
                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                @if(strtolower(trim($event->jenis ?? '')) === 'lomba' && in_array($event->lomba_kategori, ['team', 'both']))
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="small text-muted fw-semibold" style="font-size: 11.5px;">Filter:</span>
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Participant Type Filter">
                                                        <button type="button" class="btn btn-outline-primary active filter-btn" data-filter="all" style="border-radius: 6px 0 0 6px;">All/Both</button>
                                                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="team">Team Only</button>
                                                        <button type="button" class="btn btn-outline-primary filter-btn" data-filter="individual" style="border-radius: 0 6px 6px 0;">Individual Only</button>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="input-group input-group-sm" style="max-width: 320px;">
                                                    <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                                    <input type="text" id="participantSearch" class="form-control" placeholder="Search participants (name/email)">
                                                </div>
                                            </div>
                                        </div>
                                        @if($registrations->count())
                                        <div id="participantsTableWrapper" class="table-responsive">
                                            <table id="participantsTable" class="table table-sm table-striped align-middle mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width:48px;">No</th>
                                                        <th style="width:200px;">Name</th>
                                                        <th style="width:200px;">Email</th>
                                                        <th style="width:140px;">Phone</th>
                                                        <th style="width:120px;">Status</th>
                                                        @if($event->jenis === 'Lomba')
<th style="width:250px;">Lomba Submission</th>
                                                        @endif
                                                        <th style="width:160px;">Registered</th>
                                                        <th style="width:160px;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($registrations as $i => $reg)
                                                    @php
                                                        $subReg = $reg;
                                                        if ($reg->team_id && !$reg->is_team_leader) {
                                                            $leaderReg = \App\Models\EventRegistration::where('team_id', $reg->team_id)
                                                                ->where('is_team_leader', true)
                                                                ->first();
                                                            if ($leaderReg) {
                                                                $subReg = $leaderReg;
                                                            }
                                                        }
                                                        $pendingStage2Payment = \App\Models\ManualPayment::where('event_registration_id', $subReg->id)
                                                            ->where('status', 'pending')
                                                            ->where(function($q) {
                                                                $q->whereJsonContains('metadata->stage', 2)
                                                                  ->orWhere('order_id', 'like', 'STG2-%');
                                                            })
                                                            ->first();
                                                        $stage2ProofPath = $pendingStage2Payment ? optional($pendingStage2Payment->proofs()->latest()->first())->file_path : null;
                                                    @endphp
                                                    <tr data-reg-code="{{ $reg->registration_code ?? '' }}"
                                                         data-registration-type="{{ $reg->team_id ? 'team' : 'individual' }}"
                                                         data-team-info="{{ $reg->team ? ($reg->team->name . ' ' . $reg->team->code . ' ' . ($reg->team->leader->name ?? '')) : ($reg->team_name ?? '') }}">
                                                        <td>{{ $i+1 }}</td>
                                                        <td class="fw-semibold">
                                                             <div>{{ $reg->user->name ?? '-' }}</div>
                                                             @if($reg->team_id && $reg->team)
                                                                 <div class="mt-1">
                                                                     <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold py-0.5 px-2" style="font-size: 10px; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
                                                                         <i class="bi bi-people-fill"></i> Team: {{ $reg->team->name }}
                                                                         @if($reg->is_team_leader)
                                                                             <span class="badge bg-success text-white py-0.5 px-1 ms-1" style="font-size: 9px; border-radius: 3px;">Leader</span>
                                                                         @else
                                                                             <span class="badge bg-secondary text-white py-0.5 px-1 ms-1" style="font-size: 9px; border-radius: 3px;">Member</span>
                                                                         @endif
                                                                     </span>
                                                                     <div class="mt-1 p-2 rounded border bg-white small text-muted font-monospace" style="font-size: 10px; line-height: 1.4; max-width: 280px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                                                                         <div class="d-flex justify-content-between mb-0.5">
                                                                             <span class="text-secondary">Code:</span>
                                                                             <span class="fw-bold text-dark">{{ $reg->team->code }}</span>
                                                                         </div>
                                                                         <div class="d-flex justify-content-between mb-0.5">
                                                                             <span class="text-secondary">Leader:</span>
                                                                             <span class="text-dark">{{ $reg->team->leader->name ?? '-' }}</span>
                                                                         </div>
                                                                         <div class="text-start mt-1 pt-1 border-top">
                                                                             <span class="text-secondary d-block mb-0.5">Members:</span>
                                                                             <div class="text-dark" style="white-space: normal; word-break: break-word;">
                                                                                 {{ $reg->team->registrations->where('is_team_leader', false)->map(fn($r) => $r->user->name ?? '')->filter()->implode(', ') ?: '-' }}
                                                                             </div>
                                                                         </div>
                                                                     </div>
                                                                 </div>
                                                             @elseif(!$reg->team_id && strtolower(trim($event->jenis ?? '')) === 'lomba' && in_array($event->lomba_kategori, ['team', 'both']))
                                                                 <div class="mt-1">
                                                                     <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle fw-semibold py-0.5 px-2" style="font-size: 10px; border-radius: 4px;">
                                                                         <i class="bi bi-person-fill me-1"></i> Individual
                                                                     </span>
                                                                     @if($reg->team_name)
                                                                         <div class="mt-1 text-muted small" style="font-size: 10px;">
                                                                             Team Name (Indiv): <strong class="text-dark">{{ $reg->team_name }}</strong>
                                                                         </div>
                                                                     @endif
                                                                 </div>
                                                             @endif
                                                         </td>
                                                        <td class="text-muted">{{ $reg->user->email ?? '-' }}</td>
                                                         <td class="text-muted">
                                                             <div class="fw-semibold text-dark">{{ $reg->user->phone ?? '-' }}</div>
                                                             @if(!empty($reg->university_origin) || !empty($reg->study_program) || !empty($reg->position) || !empty($reg->educational_background) || !empty($reg->user->profession))
                                                                 <div class="mt-1" style="font-size: 10.5px; line-height: 1.35; max-width: 220px;">
                                                                     @if(!empty($reg->university_origin))
                                                                         <div class="text-dark"><i class="bi bi-bank me-1 text-muted"></i>{{ $reg->university_origin }}</div>
                                                                     @endif
                                                                     @if(!empty($reg->study_program))
                                                                         <div class="text-secondary"><i class="bi bi-journal-text me-1 text-muted"></i>{{ $reg->study_program }}</div>
                                                                     @endif
                                                                     @if(!empty($reg->position))
                                                                         <div class="text-muted"><i class="bi bi-briefcase me-1 text-muted"></i>{{ $reg->position }}</div>
                                                                     @elseif(!empty($reg->educational_background))
                                                                         <div class="text-muted"><i class="bi bi-briefcase me-1 text-muted"></i>{{ $reg->educational_background }}</div>
                                                                     @elseif(!empty($reg->user->profession))
                                                                         <div class="text-muted"><i class="bi bi-briefcase me-1 text-muted"></i>{{ $reg->user->profession }}</div>
                                                                     @endif
                                                                 </div>
                                                             @endif
                                                         </td>
                                                        <td>
                                                            @php
                                                                $st = strtolower((string)$reg->status);
                                                                $eventFinished = method_exists($event, 'isFinished') && $event->isFinished();
                                                                $isAlpha = $eventFinished && $st === 'active' && empty($reg->attended_at);
                                                                $displayStatus = $isAlpha ? 'alpha' : $st;
                                                            @endphp
                                                            <span class="badge {{
                                                                $displayStatus === 'active' ? 'bg-success' :
                                                                ($displayStatus === 'alpha' ? 'bg-warning text-dark' :
                                                                ($displayStatus === 'rejected' ? 'bg-danger' : 'bg-secondary'))
                                                            }}">{{ strtoupper($displayStatus) }}</span>
                                                            @if($st === 'pending' && !empty($reg->payment_proof))
                                                                <span class="badge bg-info text-dark ms-1" style="font-size:0.65rem;">PROOF</span>
                                                            @endif
                                                            @if($pendingStage2Payment && $stage2ProofPath)
                                                                <span class="badge bg-info text-dark ms-1" style="font-size:0.65rem;">PROOF TAHAP 2</span>
                                                            @endif
                                                        </td>
                                                        @if($event->jenis === 'Lomba')
                                                          <td>
                                                              @if($subReg->submission_path)
                                                                  <div class="mb-2">
                                                                      <span class="small text-muted d-block fw-bold">Tahap 1:</span>
                                                                      <a href="{{ Storage::disk('public')->url($subReg->submission_path) }}" target="_blank" class="btn btn-xs btn-outline-primary py-0 px-2 my-1" style="font-size: 11px;">
                                                                          <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> File Awal
                                                                      </a>
                                                                      <div class="mt-1">
                                                                          @if($subReg->submission_status === 'lolos')
                                                                              <span class="badge bg-success" style="font-size: 10px;">Lolos</span>
                                                                          @elseif($subReg->submission_status === 'tidak_lolos')
                                                                              <span class="badge bg-danger" style="font-size: 10px;">Tidak Lolos</span>
                                                                          @else
                                                                              <span class="badge bg-warning text-dark" style="font-size: 10px;">Pending</span>
                                                                          @endif
                                                                      </div>

                                                                      @if($subReg->submission_notes)
                                                                          <div class="mt-1 small text-muted text-start" style="font-size: 10px; max-width: 150px; white-space: normal; word-break: break-word;">
                                                                              <strong>Catatan:</strong> {{ Str::limit($subReg->submission_notes, 50) }}
                                                                          </div>
                                                                      @endif
                                                                      
                                                                      {{-- Status Setting Buttons --}}
                                                                      @if($st === 'active' && (!$reg->team_id || $reg->is_team_leader))
                                                                      <div class="mt-2 d-flex gap-1">
                                                                          {{-- Trigger Modal for Lolos --}}
                                                                          <button type="button" class="btn btn-xs btn-success py-0 px-2" style="font-size: 11px;" data-bs-toggle="modal" data-bs-target="#reviewModalLolos-{{ $subReg->id }}">
                                                                              <i class="bi bi-check2-circle me-1"></i>Lolos
                                                                          </button>

                                                                          <!-- Modal Lolos for $subReg->id -->
                                                                          <div class="modal fade shadow-sm" id="reviewModalLolos-{{ $subReg->id }}" tabindex="-1" aria-labelledby="reviewModalLolosLabel-{{ $subReg->id }}" aria-hidden="true">
                                                                              <div class="modal-dialog modal-dialog-centered">
                                                                                  <div class="modal-content">
                                                                                      <div class="modal-header">
                                                                                          <h5 class="modal-title fw-bold" id="reviewModalLolosLabel-{{ $subReg->id }}">
                                                                                              <i class="bi bi-check2-circle text-success me-2"></i>Nyatakan Peserta Lolos & Beri Catatan
                                                                                          </h5>
                                                                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                                      </div>
                                                                                      <form method="POST" action="{{ route('admin.events.submissions.review', [$event, $subReg]) }}">
                                                                                          @csrf
                                                                                          <input type="hidden" name="status" value="lolos">
                                                                                          <div class="modal-body text-start">
                                                                                              <p class="text-muted small">Anda akan menyatakan bahwa submission dari <strong>{{ $subReg->user->name }}</strong> dinyatakan <strong>Lolos</strong>. Berikan catatan atau revisi yang harus dibaca oleh peserta (opsional).</p>
                                                                                              <div class="mb-3">
                                                                                                  <label for="submission_notes-{{ $subReg->id }}" class="form-label fw-bold">Catatan / Revisi:</label>
                                                                                                  <textarea class="form-control text-start" id="submission_notes-{{ $subReg->id }}" name="submission_notes" rows="4" placeholder="Tulis catatan revisi atau instruksi selanjutnya untuk peserta...">{{ $subReg->submission_notes }}</textarea>
                                                                                              </div>
                                                                                          </div>
                                                                                          <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-2">
                                                                                              <button type="button" class="btn btn-outline-secondary btn-sm px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                                                                                              <button type="submit" class="btn btn-success btn-sm px-4 fw-bold" style="border-radius: 10px;">Simpan & Loloskan</button>
                                                                                          </div>
                                                                                      </form>
                                                                                  </div>
                                                                              </div>
                                                                          </div>

                                                                          <form method="POST" action="{{ route('admin.events.submissions.review', [$event, $subReg]) }}" class="d-inline">
                                                                              @csrf
                                                                              <input type="hidden" name="status" value="tidak_lolos">
                                                                              <button type="submit" class="btn btn-xs btn-danger py-0 px-2" style="font-size: 11px;" onclick="return confirm('Nyatakan peserta ini TIDAK LOLOS?')">
                                                                                  <i class="bi bi-x-circle me-1"></i>Tidak
                                                                              </button>
                                                                          </form>

                                                                          @if(in_array($subReg->submission_status, ['lolos', 'tidak_lolos']))
                                                                          <form method="POST" action="{{ route('admin.events.submissions.review', [$event, $subReg]) }}" class="d-inline">
                                                                              @csrf
                                                                              <input type="hidden" name="status" value="pending">
                                                                              <button type="submit" class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size: 11px;" onclick="return confirm('Kembalikan status review ke Pending?')">
                                                                                  <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                                                              </button>
                                                                          </form>
                                                                          @endif
                                                                      </div>
                                                                      @endif
                                                                  </div>
                                                              @else
                                                                  <span class="text-muted small">Belum unggah file awal</span>
                                                              @endif

                                                              @if($subReg->submission_path_2)
                                                                  <div class="mt-2 pt-2 border-top">
                                                                      <span class="small text-muted d-block fw-bold">Tahap 2:</span>
                                                                      <a href="{{ Storage::disk('public')->url($subReg->submission_path_2) }}" target="_blank" class="btn btn-xs btn-outline-success py-0 px-2 my-1" style="font-size: 11px;">
                                                                          <i class="bi bi-file-earmark-arrow-down-fill me-1"></i> File Akhir
                                                                      </a>
                                                                      <div class="text-muted small mt-1" style="font-size: 9px; line-height: 1.2;">Diunggah: {{ $subReg->submission_2_uploaded_at ? $subReg->submission_2_uploaded_at->format('d M H:i') : '' }}</div>
                                                                  </div>
                                                              @endif
                                                          </td>
                                                          @endif
                                                        <td class="text-muted">{{ optional($reg->created_at)->format('d M Y H:i') }}</td>
                                                        <td>
                                                            <div class="d-flex flex-column gap-2" style="width:fit-content;">
                                                                @if(!empty($reg->payment_proof))
                                                                    <a href="{{ Storage::disk('public')->url($reg->payment_proof) }}"
                                                                       target="_blank"
                                                                       class="btn btn-xs btn-outline-secondary py-0 px-2" style="font-size:11px;width:fit-content;">
                                                                        <i class="bi bi-image me-1"></i>Bukti
                                                                    </a>
                                                                @endif

                                                                @if($st === 'pending' && !empty($reg->payment_proof))
                                                                    <div class="d-flex gap-1">
                                                                        <button type="button" class="btn btn-xs btn-success py-0 px-2" style="font-size:11px;"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#approveModal"
                                                                            data-reg-id="{{ $reg->id }}"
                                                                            data-event-id="{{ $event->id }}"
                                                                            data-user-name="{{ $reg->user->name ?? '-' }}">
                                                                            <i class="bi bi-check2"></i> OK
                                                                        </button>
                                                                        <button type="button" class="btn btn-xs btn-danger py-0 px-2" style="font-size:11px;"
                                                                            data-bs-toggle="modal"
                                                                            data-bs-target="#rejectModal"
                                                                            data-reg-id="{{ $reg->id }}"
                                                                            data-event-id="{{ $event->id }}"
                                                                            data-user-name="{{ $reg->user->name ?? '-' }}">
                                                                            <i class="bi bi-x"></i> Reject
                                                                        </button>
                                                                    </div>
                                                                @elseif($st === 'active')
                                                                     <div class="d-flex flex-column gap-2" style="width:fit-content;">
                                                                         @if($pendingStage2Payment && $stage2ProofPath)
                                                                             <div class="d-flex flex-column gap-1 p-2 border border-info rounded bg-info-subtle" style="min-width: 140px;">
                                                                                 <span class="small text-dark fw-bold d-block" style="font-size: 10px;">Tahap 2 Manual:</span>
                                                                                 <a href="{{ Storage::disk('public')->url($stage2ProofPath) }}"
                                                                                    target="_blank"
                                                                                    class="btn btn-xs btn-outline-secondary py-0 px-2 mb-1 bg-white" style="font-size:11px;width:fit-content;">
                                                                                     <i class="bi bi-image me-1"></i>Bukti T2
                                                                                 </a>
                                                                                 <div class="d-flex gap-1">
                                                                                     <button type="button" class="btn btn-xs btn-success py-0 px-2" style="font-size:11px;"
                                                                                         data-bs-toggle="modal"
                                                                                         data-bs-target="#approveModal"
                                                                                         data-reg-id="{{ $reg->id }}"
                                                                                         data-event-id="{{ $event->id }}"
                                                                                         data-user-name="{{ $reg->user->name ?? '-' }}">
                                                                                         <i class="bi bi-check2"></i> OK
                                                                                     </button>
                                                                                     <button type="button" class="btn btn-xs btn-danger py-0 px-2" style="font-size:11px;"
                                                                                         data-bs-toggle="modal"
                                                                                         data-bs-target="#rejectModal"
                                                                                         data-reg-id="{{ $reg->id }}"
                                                                                         data-event-id="{{ $event->id }}"
                                                                                         data-user-name="{{ $reg->user->name ?? '-' }}">
                                                                                         <i class="bi bi-x"></i> Reject
                                                                                     </button>
                                                                                 </div>
                                                                             </div>
                                                                         @endif
                                                                         <div class="d-flex align-items-center gap-1">
                                                                             @php
                                                                                 $attendedDays = $reg->dailyAttendances->pluck('day_number')->toArray();
                                                                                 $totalEventDays = max(1, count($dailyQrs));
                                                                             @endphp
                                                                             @if(strtolower(trim($event->jenis ?? '')) !== 'lomba')
                                                                             <button class="btn btn-xs btn-outline-primary py-0 px-2 d-flex align-items-center gap-1"
                                                                                     type="button"
                                                                                     data-bs-toggle="modal"
                                                                                     data-bs-target="#presenceModal"
                                                                                     data-reg-id="{{ $reg->id }}"
                                                                                     data-user-name="{{ $reg->user->name ?? '-' }}"
                                                                                     data-attended-days="{{ json_encode($attendedDays) }}"
                                                                                     data-total-days="{{ $totalEventDays }}"
                                                                                     data-reg-status-yes="{{ $reg->attendance_status === 'yes' ? 1 : 0 }}"
                                                                                     style="font-size:11px; height: 22px; line-height: 22px;">
                                                                                 <i class="bi bi-calendar-check"></i> Presensi
                                                                             </button>
                                                                             @endif
                                                                             <form method="POST" action="{{ route('admin.events.registrations.cancel', [$event, $reg]) }}" class="d-inline m-0">
                                                                                 @csrf
                                                                                 @method('PATCH')
                                                                                 <button type="submit" class="btn btn-xs btn-danger text-white py-0 px-2 fw-semibold" style="font-size:11px; height: 22px; line-height: 22px;"
                                                                                     onclick="return confirm('Batalkan approval ini? Status akan kembali ke pending.')">
                                                                                     <i class="bi bi-arrow-counterclockwise"></i> Batal ACC
                                                                                 </button>
                                                                             </form>
                                                                         </div>
                                                                     </div>
                                                                @elseif(empty($reg->payment_proof))
                                                                     <span class="text-muted small">-</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                            <div id="participantsEmpty" class="alert alert-light border small mb-0 d-none">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi bi-emoji-frown fs-4 text-muted" aria-hidden="true"></i>
                                                    <div>
                                                        <div id="participantsEmptyMessage">Oopss, data peserta tidak ada.</div>
                                                        <div id="participantsEmptyQuery" class="small text-muted"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                        <div class="alert alert-light border small mb-0">Belum ada peserta terdaftar.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 2: Informasi Event & Rundown -->
                        <div class="tab-pane fade" id="info-pane" role="tabpanel" aria-labelledby="info-tab" tabindex="0">
                            <!-- Event Description -->
                            <div class="row mt-3">
                                <div class="col-12 mb-4">
                                    <h5 class="text-dark mb-3">
                                        <i class="bi bi-file-text me-2 text-success"></i>Description
                                    </h5>
                                    <div class="bg-light rounded p-4 border bg-white">
                                        <div class="event-description">
                                            {!! $event->description !!}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Jadwal Event (Schedule) -->
                            @php
                                // Kumpulkan jadwal dari relasi atau legacy JSON
                                $scheduleRows = collect();
                                if($event->relationLoaded('scheduleItems')) {
                                    $scheduleRows = $event->scheduleItems->sortBy('start');
                                } elseif(is_array($event->schedule_json) && count($event->schedule_json)) {
                                    $scheduleRows = collect($event->schedule_json)->sortBy('start');
                                } else {
                                    try { $scheduleRows = $event->scheduleItems()->orderBy('start')->get(); } catch(\Throwable $e) { $scheduleRows = collect(); }
                                }
                            @endphp
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-dark mb-3"><i class="bi bi-clock-history me-2 text-success"></i>Schedule</h5>
                                    @if($scheduleRows->count())
                                    <div class="table-responsive border rounded bg-white">
                                        <table class="table table-sm table-striped align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width:110px;">Start</th>
                                                    <th style="width:110px;">End</th>
                                                    <th style="width:110px;">Duration</th>
                                                    <th style="width:240px;">Event</th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($scheduleRows as $row)
                                                    @php
                                                        $start = is_object($row) ? ($row->start ?? null) : ($row['start'] ?? null);
                                                        $end = is_object($row) ? ($row->end ?? null) : ($row['end'] ?? null);
                                                        $title = is_object($row) ? ($row->title ?? null) : ($row['title'] ?? null);
                                                        $desc = is_object($row) ? ($row->description ?? null) : ($row['description'] ?? null);
                                                        $durationLabel = '-';
                                                        if($start && $end) {
                                                            // Normalisasi ke HH:MM
                                                            $fmt = function($t){ return preg_replace('/^(\d{2}:\d{2})(:\d{2})$/','$1',$t); };
                                                            $sNorm = $fmt((string)$start);
                                                            $eNorm = $fmt((string)$end);
                                                            try {
                                                                $sC = \Carbon\Carbon::createFromFormat('H:i', $sNorm);
                                                                $eC = \Carbon\Carbon::createFromFormat('H:i', $eNorm);
                                                                if($eC->lessThan($sC)) { // jika end < start, asumsi lewat tengah malam
                                                                    $eC = $eC->addDay();
                                                                }
                                                                $mins = $sC->diffInMinutes($eC);
                                                                if($mins >= 60) {
                                                                    $hours = intdiv($mins,60); $rem = $mins % 60;
                                                                    $durationLabel = $hours.' jam'.($rem>0?' '.$rem.' menit':'');
                                                                } else {
                                                                    $durationLabel = $mins.' menit';
                                                                }
                                                            } catch(\Throwable $ex) {
                                                                $durationLabel = '-';
                                                            }
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="fw-semibold">{{ $start ?: '-' }}</td>
                                                        <td class="text-muted">{{ $end ?: '-' }}</td>
                                                        <td>{{ $durationLabel }}</td>
                                                        <td>{{ $title ?: '-' }}</td>
                                                        <td class="small">{{ $desc ?: '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="alert alert-light border small mb-0">No schedule added yet.</div>
                                    @endif
                                </div>
                            </div>

                            <!-- Pengeluaran (Expenses) -->
                            @if(isset($event->expenses) && count($event->expenses))
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-dark mb-3"><i class="bi bi-cash-stack me-2 text-success"></i>Expenses</h5>
                                    <div class="table-responsive border rounded bg-white">
                                        <table class="table table-bordered align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Jumlah</th>
                                                    <th>Harga Satuan</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $totalExpenses = 0; @endphp
                                                @foreach($event->expenses as $exp)
                                                <tr>
                                                    <td>{{ $exp->item }}</td>
                                                    <td>{{ $exp->quantity }}</td>
                                                    <td>Rp{{ number_format($exp->unit_price,0,',','.') }}</td>
                                                    <td>Rp{{ number_format($exp->total,0,',','.') }}</td>
                                                </tr>
                                                @php $totalExpenses += $exp->total; @endphp
                                                @endforeach
                                                <tr class="fw-bold">
                                                    <td colspan="3" class="text-end">Total</td>
                                                    <td>Rp{{ number_format($totalExpenses,0,',','.') }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            @endif

                            <!-- Location map and Benefit -->
                            <div class="row g-3 mb-4">
                                @if(!empty($event->latitude) && !empty($event->longitude))
                                <div class="col-lg-6">
                                    <div class="border rounded p-3 h-100 bg-white shadow-sm">
                                        <h6 class="text-dark mb-3"><i class="bi bi-geo-alt me-2 text-success"></i>Lokasi Peta</h6>
                                        <div id="eventMap" style="height:260px; border-radius:12px; overflow:hidden;"></div>
                                        @if(!empty($event->maps_url))
                                            <a href="{{ $event->maps_url }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-2"><i class="bi bi-box-arrow-up-right me-1"></i>Open Google Maps</a>
                                        @endif
                                    </div>
                                </div>
                                @elseif(!empty($event->maps_url))
                                <div class="col-lg-6">
                                    <div class="border rounded p-3 h-100 bg-white shadow-sm">
                                        <h6 class="text-dark mb-2"><i class="bi bi-geo-alt me-2 text-success"></i>Lokasi</h6>
                                        <a href="{{ $event->maps_url }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Open Google Maps</a>
                                    </div>
                                </div>
                                @endif
                                @php
                                    $benefitItems = is_array($event->benefit)
                                        ? $event->benefit
                                        : array_values(array_filter(array_map('trim', preg_split('/\|\s*|\r\n|\n/', (string)($event->benefit ?? ''))), fn($s) => $s !== ''));
                                @endphp
                                @if(!empty($benefitItems))
                                <div class="col-lg-6">
                                    <div class="border rounded p-3 h-100 bg-white shadow-sm">
                                        <h6 class="text-dark mb-2"><i class="bi bi-gift me-2 text-success"></i>Benefit</h6>
                                        <ul class="mb-0 ps-3 small">
                                            @foreach($benefitItems as $b)
                                                <li>{{ $b }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                                @endif
                            </div>

                            @if(!empty($event->terms_and_conditions))
                            <!-- Terms & Conditions -->
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="text-dark mb-3">
                                        <i class="bi bi-shield-check me-2 text-success"></i>Terms & Conditions
                                    </h5>
                                    <div class="bg-light rounded p-4 border bg-white">
                                        <div class="event-description">
                                            {!! $event->terms_and_conditions !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- TAB 3: Dokumen & QR Absensi -->
                        <div class="tab-pane fade" id="docs-pane" role="tabpanel" aria-labelledby="docs-tab" tabindex="0">
                            <!-- Extra Details and Documents -->
                            <div class="row mt-3 g-3">
                                <div class="col-lg-6">
                                    <div class="border rounded p-3 h-100 bg-white shadow-sm">
                                        <h6 class="text-dark mb-3"><i class="bi bi-tag me-2 text-warning"></i>Price</h6>
                                        <div class="mb-3">
                                            @php $isFree = (int)$event->price === 0; @endphp
                                            @if($isFree)
                                                <h3 class="text-success mb-0 fw-bold">Free</h3>
                                            @elseif($event->hasDiscount())
                                                <div class="d-flex flex-column">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <h3 class="text-success mb-0 fw-bold">Rp{{ number_format($event->discounted_price, 0, ',', '.') }}</h3>
                                                        <span class="badge bg-danger">-{{ $event->discount_percentage }}%</span>
                                                    </div>
                                                    <small class="text-muted text-decoration-line-through">Rp{{ number_format($event->price, 0, ',', '.') }}</small>
                                                </div>
                                            @else
                                                <h3 class="text-success mb-0 fw-bold">Rp{{ number_format($event->price, 0, ',', '.') }}</h3>
                                            @endif
                                        </div>
                                        <hr class="my-3 text-muted opacity-25">
                                        <ul class="list-unstyled mb-0">
                                            @if(!empty($event->discount_until))
                                            <li class="mb-2 d-flex align-items-center"><i class="bi bi-calendar-check text-success me-2"></i> <span><strong>Discount until:</strong> {{ \Carbon\Carbon::parse($event->discount_until)->format('d F Y') }}</span></li>
                                            @endif
                                            @if(!empty($event->zoom_link))
                                            <li class="mb-2 d-flex align-items-center"><i class="bi bi-camera-video text-primary me-2"></i> <a href="{{ $event->zoom_link }}" target="_blank" class="link-primary">Open Zoom Link</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="border rounded p-3 h-100 bg-white shadow-sm">
                                        <h6 class="text-dark mb-3"><i class="bi bi-folder2-open me-2 text-warning"></i>Documents</h6>
                                        <ul class="list-group list-group-flush small">
                                            @if(!empty($event->zoom_link) || empty($event->maps_url))
                                                <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                                    <span><i class="bi {{ !empty($event->vbg_path) ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i> Virtual Background</span>
                                                    <span>
                                                        @if(!empty($event->vbg_path))
                                                            @php $vExt = strtolower(pathinfo($event->vbg_path, PATHINFO_EXTENSION)); @endphp
                                                            @if(in_array($vExt, ['jpg','jpeg','png','gif','webp','bmp','svg']))
                                                                <a href="{{ $event->vbg_file_url }}" target="_blank" class="d-inline-block">
                                                                    <img src="{{ $event->vbg_file_url }}" alt="VBG" class="rounded border" style="width:56px;height:36px;object-fit:cover;">
                                                                </a>
                                                            @elseif($vExt === 'pdf')
                                                                <a href="{{ $event->vbg_file_url }}" target="_blank" class="link-primary"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
                                                            @else
                                                                <a href="{{ $event->vbg_file_url }}" target="_blank" class="link-primary">View</a>
                                                            @endif
                                                        @else <span class="text-muted">Empty</span> @endif
                                                    </span>
                                                </li>
                                            @endif
                                            @if($event->jenis !== 'Lomba')
                                            <li class="list-group-item px-0">
                                                @php
                                                    $trainerModules = $event->trainerModules()->with('trainer')->orderByDesc('created_at')->get();
                                                    $hasModuleItems = $trainerModules->isNotEmpty();
                                                    $moduleApproved = $event->has_approved_modules;
                                                    $moduleRejected = $trainerModules->isNotEmpty() && $trainerModules->every(fn($m) => $m->status === 'rejected');
                                                    $modulePending  = $trainerModules->contains('status', 'pending_review');
                                                    $moduleIcon = $moduleApproved
                                                        ? 'bi-check-circle text-success'
                                                        : ($moduleRejected ? 'bi-x-circle text-danger'
                                                            : ($modulePending ? 'bi-hourglass-split text-warning' : 'bi-x-circle text-danger'));
                                                @endphp
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span><i class="bi {{ $moduleIcon }} me-2"></i> Module (Trainer)</span>
                                                    @if(!$hasModuleItems)
                                                        <span class="text-muted small">Belum ada</span>
                                                    @endif
                                                </div>

                                                @if($hasModuleItems)
                                                    <div class="mt-2">
                                                        @foreach($trainerModules as $tm)
                                                            @php
                                                                $borderClass = $tm->status === 'approved' ? 'border-success' : ($tm->status === 'rejected' ? 'border-danger' : 'border-warning');
                                                                $isLink = preg_match('#^https?://#i', $tm->path);
                                                            @endphp
                                                            <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded mb-2 border-start border-4 {{ $borderClass }}">
                                                                <div class="d-flex align-items-center gap-2 truncate" style="max-width: 65%;">
                                                                    <i class="bi {{ $isLink ? 'bi-link-45deg text-primary' : 'bi-file-earmark-text text-secondary' }}" style="font-size: 1.1rem;"></i>
                                                                    <div class="truncate">
                                                                        <div class="fw-bold text-truncate" style="font-size:0.75rem;" title="{{ $tm->original_name }}">{{ $tm->original_name }}</div>
                                                                        <div class="text-muted text-truncate" style="font-size:0.7rem;">
                                                                            {{ $tm->trainer?->name ?? 'Trainer' }} &bull; {{ $tm->created_at?->format('d/m/Y H:i') }}
                                                                        </div>
                                                                        @if(!empty($tm->survey_link ?: $tm->feedback_link))
                                                                            <div class="text-warning text-truncate mt-1" style="font-size:0.7rem;">
                                                                                <i class="bi bi-chat-left-text me-1"></i> Feedback: <a href="{{ $tm->survey_link ?: $tm->feedback_link }}" target="_blank" class="text-warning text-decoration-underline">{{ $tm->survey_link ?: $tm->feedback_link }}</a>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="d-flex align-items-center gap-1">
                                                                    <a href="{{ $tm->download_url }}" target="_blank" class="btn btn-xs btn-outline-secondary py-0 px-2" title="{{ $isLink ? 'Buka Link' : 'Download File' }}">
                                                                        <i class="bi {{ $isLink ? 'bi-box-arrow-up-right' : 'bi-download' }}"></i>
                                                                    </a>
                                                                    @if($tm->status === 'approved')
                                                                        <span class="badge bg-success" style="font-size:0.65rem;">Approved</span>
                                                                    @elseif($tm->status === 'rejected')
                                                                        <span class="badge bg-danger" style="font-size:0.65rem;">Ditolak</span>
                                                                    @else
                                                                        <span class="badge bg-warning text-dark" style="font-size:0.65rem;">Pending</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif


                                            </li>
                                            @if($modulePending ?? false)
                                                <li class="list-group-item px-0">
                                                    <div class="mt-2 small text-warning">
                                                        <i class="bi bi-info-circle me-1"></i>Ada modul yang menunggu review. Approve di halaman <a href="{{ route('admin.trainer.show', $event->trainer ?? 1) }}">Admin Trainer</a>.
                                                    </div>
                                                </li>
                                            @endif
                                            @endif
                                            @if($event->jenis !== 'Lomba')
                                            <li class="list-group-item px-0">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span>
                                                        <i class="bi bi-qr-code {{ $dailyQrs->isNotEmpty() ? 'text-success' : 'text-muted' }} me-2"></i>
                                                        QR Attendance
                                                        @if($isMultiDay)
                                                            <span class="badge bg-info ms-1" style="font-size:0.65rem;">Multi-Hari</span>
                                                        @endif
                                                    </span>
                                                    <form action="{{ route('admin.events.qr.generate', $event) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm {{ $dailyQrs->isNotEmpty() ? 'btn-outline-warning' : 'btn-outline-success' }}">
                                                            <i class="bi bi-{{ $dailyQrs->isNotEmpty() ? 'arrow-repeat' : 'qr-code' }} me-1"></i>
                                                            {{ $dailyQrs->isNotEmpty() ? 'Generate Ulang Semua' : 'Generate QR' }}
                                                        </button>
                                                    </form>
                                                </div>

                                                @if($dailyQrs->isNotEmpty())
                                                    <div class="row g-2">
                                                        @foreach($dailyQrs as $dqr)
                                                            @php
                                                                $qrUrl = $dqr->qr_image_url;
                                                                $qExt  = $dqr->qr_image ? strtolower(pathinfo($dqr->qr_image, PATHINFO_EXTENSION)) : 'png';
                                                                $today = \Carbon\Carbon::now(config('app.timezone'))->format('Y-m-d');
                                                                $isToday = ($dqr->qr_date instanceof \Carbon\Carbon ? $dqr->qr_date->format('Y-m-d') : (string)$dqr->qr_date) === $today;
                                                            @endphp
                                                            <div class="col-auto">
                                                                <div class="border rounded p-2 text-center {{ $isToday ? 'border-success border-2' : '' }}" style="min-width:120px;">
                                                                    <div class="fw-bold small mb-1">
                                                                        Hari {{ $dqr->day_number }}
                                                                        @if($isToday)
                                                                            <span class="badge bg-success ms-1" style="font-size:0.6rem;">Hari Ini</span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="text-muted" style="font-size:0.7rem;">
                                                                        {{ \Carbon\Carbon::parse($dqr->qr_date)->format('d M Y') }}
                                                                    </div>
                                                                    @if($qrUrl)
                                                                        <a href="{{ $qrUrl }}" target="_blank" class="d-block my-1">
                                                                            <img src="{{ $qrUrl }}" alt="QR Hari {{ $dqr->day_number }}"
                                                                                 class="rounded border" style="width:80px;height:80px;object-fit:cover;">
                                                                        </a>
                                                                        <div class="d-flex flex-wrap gap-1 justify-content-center mt-1">
                                                                            <button type="button"
                                                                                class="btn btn-xs btn-outline-success py-0 px-1 btn-dl-qr-png"
                                                                                style="font-size:10px;"
                                                                                data-qr-src="{{ $qrUrl }}"
                                                                                data-filename="event-{{ $event->id }}-day{{ $dqr->day_number }}-{{ \Carbon\Carbon::parse($dqr->qr_date)->format('Y-m-d') }}.png">
                                                                                <i class="bi bi-download"></i> PNG
                                                                            </button>
                                                                            <form action="{{ route('admin.events.qr.generate', $event) }}" method="POST" class="d-inline">
                                                                                @csrf
                                                                                <input type="hidden" name="day_id" value="{{ $dqr->id }}">
                                                                                <button type="submit" class="btn btn-xs btn-outline-warning py-0 px-1" style="font-size:10px;">
                                                                                    <i class="bi bi-arrow-repeat"></i>
                                                                                </button>
                                                                            </form>
                                                                        </div>
                                                                    @else
                                                                        <div class="text-muted small my-2">Belum ada gambar</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-muted small">
                                                        Belum ada QR. Klik "Generate QR" untuk membuat QR per hari.
                                                    </div>
                                                @endif
                                            </li>
                                            @endif
                                        </ul>

                                    </div>
                                </div>
                            </div>

                            <!-- Link Zoom -->
                            @if(!empty($event->zoom_link))
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="border rounded p-3 bg-white shadow-sm">
                                        <h6 class="text-dark mb-3"><i class="bi bi-camera-video me-2 text-warning"></i>Link Zoom Meeting</h6>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="p-3 bg-primary-subtle text-primary rounded-3" style="width: 56px; height: 56px; display: flex; align-items: center; justify-content: center;">
                                                <i class="bi bi-camera-video-fill fs-4 text-primary"></i>
                                            </div>
                                            <div>
                                                <p class="mb-1 text-muted small">Link zoom untuk meeting online/hybrid event ini:</p>
                                                <a href="{{ $event->zoom_link }}" target="_blank" class="btn btn-primary btn-sm"><i class="bi bi-box-arrow-up-right me-1"></i> Buka Link Zoom</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Certificate Generation Section -->
                  

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.add-event') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Back to List
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-lg px-4" data-bs-toggle="modal" data-bs-target="#deleteEventModal">
                                    <i class="bi bi-trash me-1"></i> Delete
                                </button>
                                <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-primary btn-lg px-4">
                                    <i class="bi bi-pencil me-1"></i> Edit Event
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.btn-close.btn-close-sm { background-size: .75em .75em; }

/* Bootstrap tabs: pastikan kontainer tab selalu terlihat (visibility diatur oleh .tab-pane) */
#eventDetailTabsContent {
    display: block !important;
}

/* Custom premium style for Tabs */
.nav-tabs-custom {
    border-bottom: 2px solid #e5e7eb !important;
}
.nav-tabs-custom .nav-link {
    color: #4b5563;
    background: transparent;
    border: none;
    border-radius: 0;
    border-bottom: 3px solid transparent;
    padding: 0.75rem 1.25rem;
    transition: all 0.25s ease;
    margin-bottom: -2px;
}
.nav-tabs-custom .nav-link:hover {
    color: #111827;
    border-bottom-color: #d1d5db;
    background-color: rgba(243, 244, 246, 0.4);
}
.nav-tabs-custom .nav-link.active {
    color: #4f46e5;
    background: transparent;
    border-bottom-color: #4f46e5;
    font-weight: 600;
}

.event-description {
    line-height: 1.6;
    color: #333;
}

.event-description h1,
.event-description h2,
.event-description h3,
.event-description h4,
.event-description h5,
.event-description h6 {
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: #2c3e50;
}

.event-description p {
    margin-bottom: 1rem;
}

.event-description ul,
.event-description ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.event-description blockquote {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #6c757d;
}

.event-description img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1rem 0;
}

.event-description table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.event-description table th,
.event-description table td {
    border: 1px solid #dee2e6;
    padding: 0.75rem;
    text-align: left;
}

.event-description table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

/* Modern delete modal shared styles */
.modal-modern {
    border: 0;
    border-radius: 18px;
    background: rgba(255,255,255,0.9);
    backdrop-filter: saturate(180%) blur(10px);
    -webkit-backdrop-filter: saturate(180%) blur(10px);
    box-shadow: 0 20px 40px rgba(0,0,0,.18), 0 8px 18px rgba(0,0,0,.08);
    overflow: hidden;
}
.modal-modern .modal-header { border: 0; padding-bottom: 0.25rem; }
.modal-modern .modal-body { padding-top: 0.75rem; }
.gradient-ring {
    position: absolute; inset: -2px; border-radius: 20px; padding: 2px;
    background: linear-gradient(135deg,#ef4444, #f59e0b, #ef4444);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor; mask-composite: exclude;
    pointer-events: none; /* allow clicks to pass through */
}
.icon-pill {
    width:56px; height:56px; border-radius:14px; display:flex; align-items:center; justify-content:center;
    background: linear-gradient(135deg, #fee2e2, #fff5f5);
    color:#dc2626; box-shadow: inset 0 0 0 1px rgba(220,38,38,.25);
}
.confirm-danger-btn { background: #dc2626; border-color:#dc2626; }
.confirm-danger-btn:hover { background:#b91c1c; border-color:#b91c1c; }
/* Registration approve/reject modal buttons: equal size */
.registration-action-footer .btn { flex: 0 0 140px; }
/* Reject registration modal buttons: equal size */
.reject-registration-footer .btn { flex: 0 0 140px; width: 140px; }
/* Manage/Create action ribbon */
.manage-action-ribbon { position:absolute; top:12px; left:-6px; padding:6px 14px 6px 18px; background:linear-gradient(135deg,#0d6efd,#3b82f6); color:#fff; font-size:.75rem; font-weight:600; letter-spacing:.5px; text-transform:uppercase; border-radius:0 6px 6px 0; box-shadow:0 4px 12px -3px rgba(0,0,0,.25); display:flex; align-items:center; z-index:5; }
.manage-action-ribbon:before { content:''; position:absolute; left:0; top:100%; width:0; height:0; border-left:6px solid #093d94; border-top:6px solid transparent; }
.manage-action-ribbon .ribbon-text { position:relative; }
.manage-action-create { background:linear-gradient(135deg,#16a34a,#22c55e); }
.manage-action-create:before { border-left-color:#0f5d2c; }
.manage-action-manage { background:linear-gradient(135deg,#0d6efd,#3b82f6); }
.manage-action-manage:before { border-left-color:#093d94; }

#publishEventModalShow .modal-content{ border-radius: 18px; overflow: hidden; }
#publishEventModalShow .modal-header{ padding: 1.1rem 1.1rem .75rem; border-bottom:0; }
#publishEventModalShow .modal-body{ padding: 0 1.1rem 1rem; }
#publishEventModalShow .modal-footer{ padding: .25rem 1.1rem 1.1rem; border-top: 0; }
#publishEventModalShow .btn{ border-radius: 12px; padding: .6rem 1.25rem; }
</style>
@endsection

@section('scripts')
@if($event->image)
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg image-preview-modal">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title small text-muted" id="imagePreviewLabel">Preview Gambar Event</h6>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="image-preview-container">
                        <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="preview-full-image" id="previewFullImage" onerror="this.src='{{ asset('aset/poster.png') }}'">
                </div>
            </div>
            <div class="modal-footer justify-content-between py-2 border-0">
                <div class="d-flex gap-2 align-items-center small text-muted flex-wrap">
                        <span><i class="bi bi-image me-1"></i>Resolusi asli ditampilkan proporsional</span>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnZoomIn"><i class="bi bi-zoom-in"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnZoomOut"><i class="bi bi-zoom-out"></i></button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="btnResetZoom"><i class="bi bi-aspect-ratio"></i></button>
                        <a href="{{ $event->image_url }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-up-right"></i> Buka Tab</a>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Tutup</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function(){
        const img = document.getElementById('previewFullImage');
        if(!img) return;
        let scale = 1;
        const step = 0.15;
        const maxScale = 3;
        const minScale = 0.4;
        const zoomInBtn = document.getElementById('btnZoomIn');
        const zoomOutBtn = document.getElementById('btnZoomOut');
        const resetBtn = document.getElementById('btnResetZoom');
        function apply(){ img.style.transform = `scale(${scale})`; }
        zoomInBtn.addEventListener('click', ()=>{ if(scale < maxScale){ scale += step; apply(); }});
        zoomOutBtn.addEventListener('click', ()=>{ if(scale > minScale){ scale -= step; apply(); }});
        resetBtn.addEventListener('click', ()=>{ scale = 1; apply(); });
        // Drag to pan when zoomed
        let isDown = false, startX, startY, scrollLeft, scrollTop;
        const container = document.querySelector('.image-preview-container');
        container.addEventListener('mousedown', (e)=>{ if(scale<=1) return; isDown=true; container.classList.add('dragging'); startX=e.pageX - container.offsetLeft; startY=e.pageY - container.offsetTop; scrollLeft=container.scrollLeft; scrollTop=container.scrollTop; });
        container.addEventListener('mouseleave', ()=>{ isDown=false; container.classList.remove('dragging'); });
        container.addEventListener('mouseup', ()=>{ isDown=false; container.classList.remove('dragging'); });
        container.addEventListener('mousemove', (e)=>{ if(!isDown) return; e.preventDefault(); const x = e.pageX - container.offsetLeft; const y = e.pageY - container.offsetTop; const walkX = (x - startX); const walkY = (y - startY); container.scrollLeft = scrollLeft - walkX; container.scrollTop = scrollTop - walkY; });
        // Wheel zoom (Ctrl + wheel)
        container.addEventListener('wheel', (e)=>{ if(!e.ctrlKey) return; e.preventDefault(); if(e.deltaY < 0 && scale < maxScale){ scale += step; } else if(e.deltaY > 0 && scale > minScale){ scale -= step; } apply(); }, { passive:false });
        // Reset zoom each time modal opens
        const modalEl = document.getElementById('imagePreviewModal');
        modalEl.addEventListener('show.bs.modal', ()=>{ scale=1; apply(); container.scrollTo({top:0,left:0}); });
});
</script>
<style>
/* Image preview enhancements */
.event-preview-wrapper .event-main-image { width:100%; height:300px; object-fit:cover; border-radius:14px; }
@media (max-width:575.98px){ .event-preview-wrapper .event-main-image { height:240px; } }
.event-image-figure { position:relative; }
.event-image-overlay { position:absolute; inset:0; display:flex; align-items:flex-end; justify-content:flex-start; padding:10px 14px; background:linear-gradient(to top,rgba(0,0,0,.55),rgba(0,0,0,0)); color:#f1f5f9; opacity:0; transition:opacity .35s; border-radius:14px; font-size:.75rem; letter-spacing:.5px; font-weight:500; }
.event-image-figure:hover .event-image-overlay { opacity:1; }
.image-preview-modal .modal-content { border-radius:20px; }
.image-preview-container { max-height:70vh; overflow:auto; background:#0f172a; border-radius:14px; padding:12px; display:flex; align-items:center; justify-content:center; }
.image-preview-container.dragging { cursor:grabbing; }
.preview-full-image { max-width:100%; height:auto; transition:transform .25s ease; transform-origin:center center; user-select:none; }
.image-preview-container::-webkit-scrollbar { width:10px; height:10px; }
.image-preview-container::-webkit-scrollbar-thumb { background:#334155; border-radius:20px; }
.image-preview-container::-webkit-scrollbar-track { background:transparent; }

/* Live pulse indicator */
.live-dot-ring {
    position: relative;
    display: inline-block;
    width: 10px;
    height: 10px;
}
.live-dot {
    position: absolute;
    width: 10px;
    height: 10px;
    background-color: #10b981;
    border-radius: 50%;
}
.live-dot::after {
    content: '';
    position: absolute;
    width: 10px;
    height: 10px;
    background-color: #10b981;
    border-radius: 50%;
    animation: live-pulse 1.6s infinite ease-in-out;
}
@keyframes live-pulse {
    0% { transform: scale(1); opacity: 1; }
    100% { transform: scale(2.8); opacity: 0; }
}
.btn-batal-hadir:hover {
    background-color: #fee2e2 !important;
    color: #dc2626 !important;
    border-color: #fca5a5 !important;
}
</style>
@endif
@if(!empty($event->latitude) && !empty($event->longitude))
<!-- Leaflet map for event location -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
let eventMapInstance = null;
document.addEventListener('DOMContentLoaded', function(){
    try{
        var lat = {{ (float) $event->latitude }};
        var lng = {{ (float) $event->longitude }};
        eventMapInstance = L.map('eventMap').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(eventMapInstance);
        L.marker([lat, lng]).addTo(eventMapInstance).bindPopup(`{{ addslashes($event->title) }}`);
    }catch(e){ console.error(e); }

    // Fix for Leaflet map inside initially hidden Bootstrap tab
    const infoTabButton = document.getElementById('info-tab');
    if (infoTabButton) {
        infoTabButton.addEventListener('shown.bs.tab', function () {
            if (eventMapInstance) {
                setTimeout(function() {
                    eventMapInstance.invalidateSize();
                }, 150);
            }
        });
    }
});
</script>
@endif
<script>
// Client-side filter for participants table (always rendered)
document.addEventListener('DOMContentLoaded', function(){
    var input = document.getElementById('participantSearch');
    var table = document.getElementById('participantsTable');
    if (!input || !table) return;
    var tbody = table.querySelector('tbody');
    var wrapper = document.getElementById('participantsTableWrapper');
    var emptyEl = document.getElementById('participantsEmpty');
    var badge = document.getElementById('participantsCountBadge');
    function refreshCounts(){
        var visible = 0;
        Array.prototype.forEach.call(tbody.querySelectorAll('tr'), function(row){
            if(row.style.display !== 'none') visible++;
        });
        if(badge) {
            badge.textContent = 'Total: ' + visible;
            badge.classList.toggle('bg-primary', visible > 0);
            badge.classList.toggle('bg-secondary', visible === 0);
        }
        if(visible === 0){
            if(wrapper) wrapper.style.display = 'none';
            if(emptyEl) emptyEl.classList.remove('d-none');
        } else {
            if(wrapper) wrapper.style.display = '';
            if(emptyEl) emptyEl.classList.add('d-none');
        }
    }

    var currentFilter = 'all';

    function applyFilters() {
        var raw = (input.value || '').trim();
        var q = raw.toLowerCase();
        
        Array.prototype.forEach.call(tbody.querySelectorAll('tr'), function(row){
            var name = (row.children[1]?.textContent || '').toLowerCase();
            var email = (row.children[2]?.textContent || '').toLowerCase();
            var phone = (row.children[3]?.textContent || '').toLowerCase();
            var code = (row.dataset.regCode || '').toLowerCase();
            var teamInfo = (row.dataset.teamInfo || '').toLowerCase();
            
            var searchMatch = !q 
                || name.includes(q) 
                || email.includes(q) 
                || phone.includes(q) 
                || code.includes(q)
                || teamInfo.includes(q);
                
            var typeMatch = true;
            var regType = row.getAttribute('data-registration-type');
            if (currentFilter === 'team' && regType !== 'team') {
                typeMatch = false;
            } else if (currentFilter === 'individual' && regType !== 'individual') {
                typeMatch = false;
            }
            
            row.style.display = (searchMatch && typeMatch) ? '' : 'none';
        });
        
        var msgEl = document.getElementById('participantsEmptyMessage');
        var qEl = document.getElementById('participantsEmptyQuery');
        if(msgEl && qEl){
            if(raw){
                msgEl.textContent = 'Oopss, data peserta tidak ditemukan.';
                qEl.textContent = '"' + raw + '"';
            } else {
                msgEl.textContent = 'Oopss, data peserta tidak ditemukan.';
                qEl.textContent = '';
            }
        }

        var exportBtn = document.getElementById('exportParticipantsExcelBtn');
        if (exportBtn) {
            var baseUrl = '{{ route("admin.events.export-participants", $event) }}';
            var params = new URLSearchParams();
            if (currentFilter && currentFilter !== 'all') {
                params.append('filter', currentFilter);
            }
            if (raw) {
                params.append('q', raw);
            }
            var queryString = params.toString();
            exportBtn.href = queryString ? baseUrl + '?' + queryString : baseUrl;
        }

        refreshCounts();
    }

    input.addEventListener('input', applyFilters);

    document.querySelectorAll('.filter-btn').forEach(function(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            document.querySelectorAll('.filter-btn').forEach(function(b){ b.classList.remove('active'); });
            this.classList.add('active');
            currentFilter = this.getAttribute('data-filter') || 'all';
            applyFilters();
        });
    });
    // initialize counts on load
    refreshCounts();
});
</script>
<script>
// PNG/JPG download for QR (supports SVG and raster sources)
document.addEventListener('DOMContentLoaded', function(){
    function loadImageFromSvgText(svgText){
        return new Promise((resolve, reject)=>{
            try {
                const blob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' });
                const url = URL.createObjectURL(blob);
                const img = new Image();
                img.onload = function(){ URL.revokeObjectURL(url); resolve(img); };
                img.onerror = function(e){ URL.revokeObjectURL(url); reject(e); };
                img.src = url;
            } catch (err) { reject(err); }
        });
    }
    function loadImageFromUrl(src){
        return new Promise((resolve, reject)=>{
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = ()=> resolve(img);
            img.onerror = (e)=> reject(e);
            img.src = src;
        });
    }
    function drawToCanvas(img, type='image/png', targetWidth=600, targetHeight=600){
        const canvas = document.createElement('canvas');
        const w = targetWidth, h = targetHeight;
        canvas.width = w; canvas.height = h;
        const ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff'; // white bg for JPG
        ctx.fillRect(0,0,w,h);
        const scale = Math.min(w / img.width, h / img.height);
        const dw = img.width * scale; const dh = img.height * scale;
        const dx = (w - dw) / 2; const dy = (h - dh) / 2;
        ctx.drawImage(img, dx, dy, dw, dh);
        return canvas.toDataURL(type);
    }
    async function handleDownload(format){
        const btn = format === 'png' ? document.getElementById('btnDownloadQrPng') : document.getElementById('btnDownloadQrJpg');
        if(!btn) return;
        const src = btn.getAttribute('data-qr-src');
        const ext = (btn.getAttribute('data-qr-ext') || '').toLowerCase();
        const baseName = btn.getAttribute('data-qr-name') || 'qr';
        const mime = format === 'png' ? 'image/png' : 'image/jpeg';
        try {
            let img;
            if(ext === 'svg'){
                const res = await fetch(src, { cache: 'no-store' });
                const svgText = await res.text();
                img = await loadImageFromSvgText(svgText);
            } else {
                img = await loadImageFromUrl(src);
            }
            const dataUrl = drawToCanvas(img, mime);
            const a = document.createElement('a');
            a.href = dataUrl;
            a.download = `${baseName}.${format}`;
            document.body.appendChild(a);
            a.click();
            a.remove();
        } catch (err) {
            console.error('Gagal mengunduh QR sebagai', format, err);
            alert('Maaf, gagal mengunduh dalam format ' + format.toUpperCase() + '.');
        }
    }
    const btnPng = document.getElementById('btnDownloadQrPng');
    const btnJpg = document.getElementById('btnDownloadQrJpg');
    if(btnPng) btnPng.addEventListener('click', ()=> handleDownload('png'));
    if(btnJpg) btnJpg.addEventListener('click', ()=> handleDownload('jpg'));

    // Per-day QR download buttons (.btn-dl-qr-png)
    document.querySelectorAll('.btn-dl-qr-png').forEach(function(btn){
        btn.addEventListener('click', async function(){
            const src      = this.getAttribute('data-qr-src');
            const filename = this.getAttribute('data-filename') || 'qr.png';
            const ext      = (src || '').split('.').pop().toLowerCase();
            const origText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:.75rem;height:.75rem;"></span>';
            try {
                let img;
                if (ext === 'svg') {
                    const res    = await fetch(src, { cache: 'no-store' });
                    const svgTxt = await res.text();
                    img = await loadImageFromSvgText(svgTxt);
                } else {
                    img = await loadImageFromUrl(src);
                }
                const dataUrl = drawToCanvas(img, 'image/png', 800, 800);
                const a = document.createElement('a');
                a.href     = dataUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                a.remove();
            } catch(err) {
                console.error('Download QR PNG failed', err);
                alert('Gagal mengunduh QR sebagai PNG.');
            } finally {
                this.disabled  = false;
                this.innerHTML = origText;
            }
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Delete event modal: ensure submit works even if data-api is flaky
    var deleteModalEl = document.getElementById('deleteEventModal');
    var deleteForm = document.getElementById('deleteEventFormShow');
    var deleteBtn = document.getElementById('deleteConfirmBtnShow');

    // Publish/Unpublish show page logic
    var pubModEl = document.getElementById('publishEventModalShow');
    var pubMod = (pubModEl && window.bootstrap && typeof bootstrap.Modal === 'function') ? new bootstrap.Modal(pubModEl) : null;
    var pubModTitle = document.getElementById('publishEventModalShowLabel');
    var pubModMsg = document.getElementById('publishEventModalShowMessage');
    var pubModBtn = document.getElementById('publishEventModalShowConfirmBtn');
    var activeForm = null;

    var pubBtnShow = document.getElementById('publishBtnShow');
    if(pubBtnShow){
        pubBtnShow.addEventListener('click', function(){
            if(pubModTitle) pubModTitle.textContent = 'Publish Event';
            if(pubModMsg) pubModMsg.textContent = 'Are you sure you want to publish this event? The event will be visible to the public.';
            if(pubModBtn) {
                pubModBtn.textContent = 'Publish';
                pubModBtn.className = 'btn btn-primary btn-sm';
            }
            activeForm = document.getElementById('publishEventFormShow');
            if(pubMod) pubMod.show();
        });
    }

    var unpubBtnShow = document.getElementById('unpublishBtnShow');
    if(unpubBtnShow){
        unpubBtnShow.addEventListener('click', function(){
            if(pubModTitle) pubModTitle.textContent = 'Konfirmasi Batal Terbitkan';
            if(pubModMsg) pubModMsg.textContent = 'Apakah Anda yakin ingin membatalkan publikasi event ini? Event tidak akan terlihat lagi oleh publik.';
            if(pubModBtn) {
                pubModBtn.textContent = 'Unpublish';
                pubModBtn.className = 'btn btn-danger btn-sm';
            }
            activeForm = document.getElementById('unpublishEventFormShow');
            if(pubMod) pubMod.show();
        });
    }

    if(pubModBtn){
        pubModBtn.addEventListener('click', function(){
            if(activeForm) activeForm.submit();
        });
    }

    // Fallback: open modal programmatically (covers cases where Bootstrap data-api is not bound)
    document.querySelectorAll('[data-bs-target="#deleteEventModal"]').forEach(function(btn){
        btn.addEventListener('click', function(){
            try {
                if(deleteModalEl && window.bootstrap && bootstrap.Modal){
                    bootstrap.Modal.getOrCreateInstance(deleteModalEl).show();
                }
            } catch(e) {}
        });
    });

    if(deleteBtn && deleteForm && deleteBtn.dataset.boundSubmit !== '1'){
        deleteBtn.dataset.boundSubmit = '1';
        deleteBtn.addEventListener('click', function(e){
            e.preventDefault();
            deleteBtn.disabled = true;
            try {
                if(typeof deleteForm.requestSubmit === 'function'){
                    deleteForm.requestSubmit();
                } else {
                    deleteForm.submit();
                }
            } catch(err) {
                deleteBtn.disabled = false;
                throw err;
            }
        });
    }
});
</script>

<!-- Approve Registration Modal -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0" style="background:#f0fdf4;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width:36px;height:36px;border-radius:50%;background:#dcfce7;display:flex;align-items:center;justify-content:center;">
                        <i class="bi bi-check-circle-fill text-success"></i>
                    </div>
                    <h5 class="modal-title fw-semibold mb-0" id="approveModalLabel">Konfirmasi Pembayaran</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2 pb-1">
                <p class="text-muted small mb-1">Anda akan mengonfirmasi pembayaran dari:</p>
                <p class="fw-semibold mb-3" id="approveUserName" style="color:#15803d;"></p>
                <p class="text-muted small">Peserta akan otomatis mendapatkan status <strong>Active</strong> dan notifikasi konfirmasi.</p>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                <form id="approveForm" method="POST" action="" class="d-inline m-0">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="bi bi-check2 me-1"></i>Konfirmasi
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const approveModal = document.getElementById('approveModal');
    if (!approveModal) return;
    approveModal.addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        if (!btn) return;
        const regId   = btn.getAttribute('data-reg-id');
        const eventId = btn.getAttribute('data-event-id');
        const name    = btn.getAttribute('data-user-name') || '-';
        document.getElementById('approveUserName').textContent = name;
        document.getElementById('approveForm').action =
            '/admin/events/' + eventId + '/registrations/' + regId + '/approve';
    });
});
</script>

<!-- Reject Registration Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-semibold" id="rejectModalLabel">
                    <i class="bi bi-x-circle me-2 text-danger"></i>Reject Registration
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST" action="">
                @csrf
                <div class="modal-body pt-0">
                    <p class="text-muted small mb-3">Rejecting registration of <strong id="rejectUserName"></strong>.</p>
                    <label class="form-label fw-semibold small">Rejection Reason</label>
                    <textarea name="rejection_reason" class="form-control" rows="3"
                        placeholder="e.g. Bukti transfer tidak jelas / tidak sesuai nominal"
                        required>Bukti pembayaran tidak valid.</textarea>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i>Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rejectModal = document.getElementById('rejectModal');
    if (!rejectModal) return;
    rejectModal.addEventListener('show.bs.modal', function(e) {
        const btn = e.relatedTarget;
        if (!btn) return;
        const regId   = btn.getAttribute('data-reg-id');
        const eventId = btn.getAttribute('data-event-id');
        const name    = btn.getAttribute('data-user-name') || '-';
        document.getElementById('rejectUserName').textContent = name;
        document.getElementById('rejectForm').action =
            '/admin/events/' + eventId + '/registrations/' + regId + '/reject';
    });
});
</script>

<!-- Delete Confirmation Modal (modern) -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-labelledby="deleteEventLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-modern position-relative">
            <span class="gradient-ring" aria-hidden="true"></span>
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-pill"><i class="bi bi-trash-fill fs-4"></i></div>
                    <div>
                        <h5 class="modal-title mb-0" id="deleteEventLabel">Delete Event</h5>
                        <small class="text-muted">This action cannot be undone.</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">You are about to delete the event:</p>
                <div class="p-2 rounded border bg-light"><i class="bi bi-calendar-event me-1"></i> <strong>{{ $event->title }}</strong></div>
                <div class="alert alert-warning small mt-3 mb-0">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    This action cannot be undone.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger confirm-danger-btn" id="deleteConfirmBtnShow" form="deleteEventFormShow">
                    <i class="bi bi-trash me-1"></i> Hapus Permanen
                </button>
            </div>
        </div>
    </div>
    <form id="deleteEventFormShow" action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
</div>

    <!-- Publish/Unpublish Confirmation Modal (Global for this page) -->
    <div class="modal fade" id="publishEventModalShow" tabindex="-1" aria-labelledby="publishEventModalShowLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="publishEventModalShowLabel">Konfirmasi</h5>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="publishEventModalShowMessage">Are you sure?</p>
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="publishEventModalShowConfirmBtn" style="border-radius: 8px;">Konfirmasi</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Presence Modal (Modern & Interactive - Compact size) -->
    <div class="modal fade" id="presenceModal" tabindex="-1" aria-labelledby="presenceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 440px;">
            <div class="modal-content border-0 shadow-lg position-relative" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header bg-white border-bottom py-2.5 px-3">
                    <div class="d-flex align-items-center gap-2.5">
                        <div class="p-2 rounded bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:36px; height:36px; border-radius: 10px !important;">
                            <i class="bi bi-calendar-check fs-5"></i>
                        </div>
                        <div>
                            <h6 class="modal-title fw-bold text-dark mb-0" id="presenceModalLabel">Presensi Harian</h6>
                            <small class="text-muted" id="presenceModalSubTitle" style="font-size: 11px;">Kelola kehadiran peserta secara manual</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-muted small text-uppercase tracking-wider mb-1" style="font-size: 10.5px;">Nama Peserta</label>
                        <div class="p-2.5 rounded border bg-light d-flex align-items-center gap-2" style="border-radius: 8px !important;">
                            <i class="bi bi-person-fill text-muted fs-6"></i>
                            <strong id="presenceModalUserName" class="text-dark" style="font-size: 13.5px;"></strong>
                        </div>
                    </div>
                    <label class="form-label fw-semibold text-muted small text-uppercase tracking-wider mb-1.5" style="font-size: 10.5px;">Daftar Kehadiran</label>
                    <div class="d-flex flex-column gap-2" id="presenceDaysList">
                        <!-- Dynamic daily presence items will be generated here -->
                    </div>
                </div>
                <div class="modal-footer border-top bg-light py-2.5 px-4 d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-secondary px-4 fw-semibold" data-bs-dismiss="modal" style="border-radius: 10px;">Tutup</button>
                </div>
            </div>
        </div>
    </div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let attendanceChart = null;
    const isLomba = @json($event->jenis === 'Lomba');

    function initChart(labels, data) {
        const canvas = document.getElementById('attendanceChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (attendanceChart) {
            attendanceChart.destroy();
        }
        attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: isLomba ? 'Jumlah Pendaftar' : 'Persentase Kehadiran (%)',
                    data: data,
                    backgroundColor: 'rgba(99, 102, 241, 0.85)',
                    borderColor: 'rgba(99, 102, 241, 1)',
                    borderWidth: 1,
                    borderRadius: 6,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: isLomba ? undefined : 100,
                        ticks: {
                            callback: function(value) { return isLomba ? value : value + "%"; }
                        }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) { return isLomba ? context.parsed.y + " Pendaftar" : context.parsed.y + "% Kehadiran"; }
                        }
                    }
                }
            }
        });
    }

    function fetchAttendanceStats() {
        fetch('{{ route("admin.events.attendance.stats", $event) }}')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update Active Participants count
                    const activeParticipantsCount = document.getElementById('activeParticipantsCount');
                    if (activeParticipantsCount) {
                        activeParticipantsCount.textContent = data.total_active_participants;
                    }
                    const teamParticipantsCount = document.getElementById('teamParticipantsCount');
                    if (teamParticipantsCount) {
                        teamParticipantsCount.textContent = data.total_team_participants !== undefined ? data.total_team_participants : 0;
                    }
                    const individualParticipantsCount = document.getElementById('individualParticipantsCount');
                    if (individualParticipantsCount) {
                        individualParticipantsCount.textContent = data.total_individual_participants !== undefined ? data.total_individual_participants : 0;
                    }
                    
                    // Update Stats Grid
                    const statsGrid = document.getElementById('dailyStatsGrid');
                    if (statsGrid) {
                        const staticCards = Array.from(statsGrid.querySelectorAll('.static-card')).map(el => el.cloneNode(true));
                        statsGrid.innerHTML = '';
                        staticCards.forEach(card => statsGrid.appendChild(card));
                        
                        const labels = [];
                        const chartData = [];
                        
                        data.days.forEach(day => {
                            labels.push('Hari ' + day.day_number);
                            chartData.push(day.percent);
                            
                            const col = document.createElement('div');
                            col.className = 'col-md-4';
                            
                            if (isLomba) {
                                col.innerHTML = `
                                    <div class="p-3 bg-white border rounded h-100 d-flex flex-column justify-content-center shadow-sm">
                                        <small class="text-muted d-block mb-1">Pendaftar Hari ${day.day_number}</small>
                                        <div class="d-flex align-items-baseline gap-2">
                                            <h3 class="mb-0 fw-bold text-dark">${day.checked_in}</h3>
                                            <span class="small text-muted">pendaftar baru</span>
                                        </div>
                                        <small class="text-muted mt-1" style="font-size: 11px;">Tanggal: ${day.date}</small>
                                    </div>
                                `;
                            } else {
                                col.innerHTML = `
                                    <div class="p-3 bg-white border rounded h-100 d-flex flex-column justify-content-center shadow-sm">
                                        <small class="text-muted d-block mb-1">Kehadiran Hari ${day.day_number}</small>
                                        <div class="d-flex align-items-baseline gap-2">
                                            <h3 class="mb-0 fw-bold text-dark">${day.checked_in}/${day.total}</h3>
                                            <span class="badge bg-success-subtle text-success small">${day.percent}%</span>
                                        </div>
                                        <small class="text-muted mt-1" style="font-size: 11px;">Tanggal: ${day.date}</small>
                                    </div>
                                `;
                            }
                            statsGrid.appendChild(col);
                        });
                        
                        // Draw / Update Chart
                        initChart(labels, chartData);
                    }
                    
                    // Update Live Feed
                    const feed = document.getElementById('liveCheckinFeed');
                    if (feed) {
                        if (data.logs.length === 0) {
                            feed.innerHTML = isLomba 
                                ? '<div class="text-center text-muted py-5 small">Belum ada riwayat pendaftaran.</div>'
                                : '<div class="text-center text-muted py-5 small">Belum ada riwayat check-in.</div>';
                        } else {
                            feed.innerHTML = '';
                            data.logs.forEach(log => {
                                const item = document.createElement('div');
                                item.className = 'd-flex align-items-start gap-2 mb-3 border-bottom pb-2';
                                
                                const iconClass = isLomba ? 'bi-person-plus-fill' : 'bi-person-check-fill';
                                const badgeHtml = isLomba ? '' : `<span class="badge bg-primary-subtle text-primary" style="font-size:8.5px;">Hari ${log.day_number}</span> &bull; `;
                                
                                item.innerHTML = `
                                    <div class="p-2 bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width:32px; height:32px; flex-shrink:0;">
                                        <i class="bi ${iconClass}" style="font-size:14px;"></i>
                                    </div>
                                    <div class="flex-grow-1 min-w-0" style="line-height:1.35;">
                                        <div class="fw-semibold text-dark text-truncate small">${log.name}</div>
                                        <div class="text-muted text-truncate" style="font-size:10.5px;">${log.email}</div>
                                        <div class="text-muted mt-1" style="font-size:10px;">
                                            ${badgeHtml}${log.date} ${log.scanned_at}
                                        </div>
                                    </div>
                                `;
                                feed.appendChild(item);
                            });
                        }
                    }
                    
                    // Update Last Updated time
                    const lastUpdatedBadge = document.getElementById('lastUpdatedBadge');
                    if (lastUpdatedBadge) {
                        const now = new Date();
                        lastUpdatedBadge.textContent = 'Terakhir diupdate: ' + now.toLocaleTimeString();
                    }
                }
            })
            .catch(err => console.error('Gagal mengambil statistik:', err));
    }

    // Trigger on load
    fetchAttendanceStats();

    // Poll every 10 seconds
    setInterval(fetchAttendanceStats, 10000);

    // Initialize Presence Modal setup
    const presenceModal = document.getElementById('presenceModal');
    if (presenceModal) {
        presenceModal.addEventListener('show.bs.modal', function(e) {
            const btn = e.relatedTarget;
            if (!btn) return;
            
            const regId = btn.getAttribute('data-reg-id');
            const userName = btn.getAttribute('data-user-name') || '-';
            const totalDays = parseInt(btn.getAttribute('data-total-days')) || 1;
            const attendedDays = JSON.parse(btn.getAttribute('data-attended-days') || '[]');
            const regStatusYes = parseInt(btn.getAttribute('data-reg-status-yes')) || 0;
            
            document.getElementById('presenceModalUserName').textContent = userName;
            
            const listContainer = document.getElementById('presenceDaysList');
            listContainer.innerHTML = '';
            
            for (let d = 1; d <= totalDays; d++) {
                const hasAttended = attendedDays.includes(d) || (totalDays === 1 && regStatusYes === 1);
                const action = hasAttended ? 'cancel' : 'checkin';
                
                const item = document.createElement('div');
                item.className = 'd-flex align-items-center justify-content-between py-2 px-3 border bg-white shadow-sm';
                item.style.borderRadius = '10px';
                
                let statusBadge = '';
                let actionBtn = '';
                
                if (hasAttended) {
                    statusBadge = '<span class="badge bg-success-subtle text-success border border-success fw-semibold py-1 px-2.5 d-inline-flex align-items-center gap-1" style="font-size: 10.5px; border-radius: 15px;"><i class="bi bi-check-circle-fill"></i> Hadir</span>';
                    actionBtn = `
                        <button class="btn btn-sm btn-outline-danger btn-batal-hadir d-inline-flex align-items-center gap-1 py-1 px-2.5 fw-semibold text-danger" 
                                style="border-radius: 6px; font-size: 10.5px;" 
                                onclick="toggleDailyPresenceModal(${regId}, ${d}, 'cancel', this)">
                            <i class="bi bi-x-circle"></i> Batal Hadir
                        </button>
                    `;
                } else {
                    statusBadge = '<span class="badge bg-secondary-subtle text-secondary border border-secondary fw-semibold py-1 px-2.5 d-inline-flex align-items-center gap-1" style="font-size: 10.5px; border-radius: 15px;"><i class="bi bi-dash-circle"></i> Absen</span>';
                    actionBtn = `
                        <button class="btn btn-sm btn-success d-inline-flex align-items-center gap-1 py-1 px-2.5 fw-semibold text-white" 
                                style="border-radius: 6px; font-size: 10.5px; background-color: #198754; border-color: #198754;" 
                                onclick="toggleDailyPresenceModal(${regId}, ${d}, 'checkin', this)">
                            <i class="bi bi-check-lg"></i> Set Hadir
                        </button>
                    `;
                }
                
                item.innerHTML = `
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold text-dark fs-6" style="min-width: 60px;">Hari ${d}</span>
                        ${statusBadge}
                    </div>
                    <div>
                        ${actionBtn}
                    </div>
                `;
                listContainer.appendChild(item);
            }
        });
    }
});

function toggleDailyPresenceModal(regId, dayNumber, action, btnEl) {
    btnEl.disabled = true;
    const originalText = btnEl.innerHTML;
    btnEl.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';

    const url = action === 'checkin' 
        ? `/admin/events/{{ $event->id }}/registrations/${regId}/check-in`
        : `/admin/events/{{ $event->id }}/registrations/${regId}/cancel-day`;
        
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrf,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ day_number: dayNumber })
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (window.adminNotify) {
                window.adminNotify('success', data.message);
            } else {
                alert(data.message);
            }
            setTimeout(() => { window.location.reload(); }, 800);
        } else {
            if (window.adminNotify) {
                window.adminNotify('error', data.message || 'Gagal mengubah status presensi.');
            } else {
                alert(data.message || 'Gagal mengubah status presensi.');
            }
            btnEl.disabled = false;
            btnEl.innerHTML = originalText;
        }
    })
    .catch(err => {
        console.error(err);
        alert('Terjadi kesalahan koneksi.');
        btnEl.disabled = false;
        btnEl.innerHTML = originalText;
    });
}
</script>

@endsection