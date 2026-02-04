    @include("partials.navbar-after-login")
    <!DOCTYPE html>
    <html lang="en">

    <head>
                <style>
                    .stars-bi {
                        transition: color 0.2s, transform 0.18s;
                        color: gray;
                    }
                    .stars-bi.active {
                        color: #FFD600 !important;
                    }
                    .stars-bi.hovered {
                        transform: scale(1.22) rotate(-8deg) !important;
                        filter: drop-shadow(0 2px 6px #FFD600cc) !important;
                        z-index: 2 !important;
                    }
                </style>
            <style>
                .stars-bi {
                    transition: color 0.2s, transform 0.18s;
                }
                .add-stars .stars-bi.hovered,
                .add-stars .stars-bi.hovered:focus {
                    transform: scale(1.22) rotate(-8deg) !important;
                    color: #FFD600 !important;
                    filter: drop-shadow(0 2px 6px #FFD600cc) !important;
                    z-index: 2 !important;
                    transition: color 0.2s !important;
                    transition-property: color, transform, filter !important;
                }
            </style>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Detail Event</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
            rel="stylesheet">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            /* Make the top navy section flush to the very top */
           /* --- Reset & Layout Dasar --- */
html, body { 
    margin: 0; 
    padding: 0; 
}

.container-ungu { 
    margin-top: 0 !important; 
}

/* Nudge breadcrumb down agar tidak tertutup navbar */
.container-ungu .link-box { 
    padding-top: 80px; 
}

@media (max-width: 576px) {
    .container-ungu .link-box { padding-top: 64px; }
}

/* --- Tab Content & Panes --- */
.desc-box .tab-content .tab-pane { 
    padding: 16px 20px 24px; 
}

@media (max-width: 576px) {
    .desc-box .tab-content .tab-pane { padding: 12px 14px 18px; }
}

/* Spesifik untuk Tab Terms & Condition */
.desc-box .tab-content #nav-contact,
.desc-box .terms-box { 
    padding: 4px 8px 0 !important; 
    margin: 0 !important; 
}

.desc-box .tab-content #nav-contact .terms-content { 
    margin-top: 4px !important; 
    margin-bottom: 0 !important; 
    padding-bottom: 0 !important; 
}

.desc-box .tab-content #nav-contact h6 { 
    margin-top: 4px !important; 
    margin-bottom: 6px !important; 
}

/* --- Social Media Icons --- */
.share .share-list .bi-facebook { 
    position: relative; 
    top: -1px; 
}

/* --- Resource Cards (Locked State) --- */
.resource-card.locked { 
    opacity: 0.6; 
}

.resource-card.locked .img-resource svg,
.resource-card.locked .link-share { 
    opacity: 0.6; 
}

.resource-card.locked .resource-value { 
    color: #6c757d; 
}

.resource-card.locked .link-share { 
    pointer-events: none; 
}

/* Icon gembok pada resource yang dikunci */
.resource-card .img-resource { 
    position: relative; 
}

.resource-card.locked .img-resource::after {
    content: '';
    position: absolute;
    right: -6px;
    bottom: -6px;
    width: 18px;
    height: 18px;
    opacity: .8;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,.15));
    background-repeat: no-repeat;
    background-size: 18px 18px;
    background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236b7280' class='bi bi-lock' viewBox='0 0 16 16'><path d='M8 1a3 3 0 0 0-3 3v3H4a2 2 0 0 0-2 2v4a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-1V4a3 3 0 0 0-3-3m0 4a1 1 0 0 1 1 1v2H7V6a1 1 0 0 1 1-1'/></svg>");
}

/* --- Feedback & Rating Card --- */
.add-rating { 
    border: none !important; 
    box-shadow: none !important; 
}

.add-rating .scroll-review-box { 
    border: none !important; 
}

/* Overlay untuk Feedback Terkunci */
.add-rating.locked { 
    opacity: 1 !important; 
    filter: none !important; 
    position: relative; 
}

.add-rating.locked .locked-overlay {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(255,255,255,0.55);
    backdrop-filter: blur(6px);
    -webkit-backdrop-filter: blur(6px);
    font-weight: 600;
    color: #6c757d;
    text-align: center;
    padding: 12px;
    pointer-events: none;
    z-index: 2;
}

.add-rating.locked > h5 {
    position: relative;
    z-index: 3;
    filter: blur(2px);
    color: #6c757d;
    user-select: none;
    pointer-events: none;
}

.feedback-locked-msg { 
    margin-top: -8px; 
}

/* --- Modal & Scrollbar Styling --- */
#feedbackModal .modal-content {
    border-radius: 12px;
    overflow: hidden;
}

#participant-ratings-list::-webkit-scrollbar { width: 6px; }
#participant-ratings-list::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 3px; }
#participant-ratings-list::-webkit-scrollbar-thumb { background: #888; border-radius: 3px; }
#participant-ratings-list::-webkit-scrollbar-thumb:hover { background: #555; }

.rating-card { transition: box-shadow 0.2s; }
.rating-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.1); }

/* --- Tombol Book & Save --- */
.booksave-row { 
    display: flex; 
    flex-direction: column; 
    gap: 12px; 
    align-items: stretch; 
}

.bookseat, .save {
    display: block; 
    width: 100%; 
    text-align: center; 
    padding: 12px 16px; 
    border-radius: 6px; 
    font-weight: 600; 
    text-decoration: none; 
    box-sizing: border-box;
    border: none;
}

.bookseat { 
    background: #f5c400; 
    color: #111; 
    order: 1; 
}

.bookseat:disabled, .bookseat.disabled { 
    background: #ddd; 
    color: #666; 
}

.save { 
    background: #1f2235; 
    color: #ffd400; 
    order: 2; 
    cursor: pointer; 
    position: relative; 
    z-index: 2; 
    pointer-events: auto !important; 
}

/* --- Info & Price Detail --- */
.price-box > span { 
    color: #6b7280; 
    text-decoration: line-through; 
    display: inline-block; 
    min-height: 20px; 
}

.price-free { 
    color: #16a34a; 
    font-weight: 700; 
    letter-spacing: .3px; 
}

.info-item .label-event { 
    display: block; 
    font-weight: 600; 
    color: #6b7280; 
    margin-bottom: 2px; 
}

.info-item .isi-event { 
    display: block; 
    color: #111827; 
    font-weight: 600; 
}

/* Ticket Info Grid (Right Card) */
.detail-box-right .info-boxluar .info-item {
    display: grid;
    grid-template-columns: 1fr auto;
    column-gap: 12px;
    align-items: start;
    width: 100%;
}

.detail-box-right .info-boxluar .info-left {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    min-width: 0;
}

.detail-box-right .info-boxluar .info-left svg {
    margin: 3px 0 0 0 !important;
    flex: 0 0 20px;
}

.detail-box-right .info-boxluar .label-event {
    display: inline !important;
    margin: 0;
    white-space: nowrap;
}

.detail-box-right .info-boxluar .isi-event {
    display: inline !important;
    justify-self: end;
    text-align: right;
    white-space: nowrap;
}

@media (max-width: 576px) {
    .detail-box-right .info-boxluar .info-item { grid-template-columns: 1fr; row-gap: 4px; }
    .detail-box-right .info-boxluar .isi-event { justify-self: start; text-align: left; white-space: normal; }
}

/* --- Star Ratings --- */
.stars { user-select: none; }
.stars span { 
    cursor: pointer; 
    font-size: 20px; 
    color: #c9c9c9; 
    margin-right: 2px; 
}
.stars span.selected { color: #FFD600; }

.stars-rating-input span {
    display: inline-block;
    font-size: 1.75rem;
    line-height: 1;
}
        </style>

    </head>

    <body>
        <div class="container-ungu">
            <div class="link-box">
                <a href="{{ route('dashboard') }}">Home</a>
                <p>></p>
                <a href="{{ route('events.index') }}">Events</a>
                <p>></p>
                <a href="#">{{ isset($event) ? $event->title : 'Event' }}</a>
            </div>
            <div class="box-event-creator">
                <div class="event-creator">
                    <p><span class="highlite-yellow">Event</span> by idSpora</p>
                </div>
                <div class="add-calender">
                    @php
                        // Build Google Calendar URL with event info
                        $tz = config('app.timezone', 'Asia/Jakarta');
                        $titleCal = isset($event) && !empty($event->title) ? $event->title : 'Event';
                        $descRaw = isset($event) && !empty($event->description) ? strip_tags($event->description) : 'idSpora Event';
                        $eventPageUrl = url()->current();
                        $detailsCal = trim($descRaw . "\n\nMore info: " . $eventPageUrl);
                        // Compute local start/end times safely (similar to logic used below)
                        $eventDateTop = isset($event) && !empty($event->event_date) ? (\Carbon\Carbon::parse($event->event_date)) : null;
                        $parseEvtTimeTop = function($date, $raw) {
                            if (empty($raw)) return null;
                            if ($raw instanceof \Carbon\Carbon) return $raw;
                            $rawStr = trim((string)$raw);
                            if (preg_match('/\d{4}-\d{2}-\d{2}/', $rawStr)) {
                                try { return \Carbon\Carbon::parse($rawStr); } catch (\Throwable $e) { return null; }
                            }
                            if ($date) {
                                $dateStr = $date instanceof \Carbon\Carbon ? $date->format('Y-m-d') : (string)$date;
                                try { return \Carbon\Carbon::parse($dateStr.' '.$rawStr); } catch (\Throwable $e) { return null; }
                            }
                            try { return \Carbon\Carbon::parse($rawStr); } catch (\Throwable $e) { return null; }
                        };
                        $startTop = isset($event) ? $parseEvtTimeTop($eventDateTop, $event->event_time ?? null) : null;
                        $endTop = isset($event) ? $parseEvtTimeTop($eventDateTop, $event->event_time_end ?? null) : null;
                        if (!$startTop && $eventDateTop) { $startTop = $eventDateTop->copy()->startOfDay(); }
                        if (!$endTop && $eventDateTop) { $endTop = $eventDateTop->copy()->endOfDay(); }
                        // Prepare UTC date range for Google Calendar
                        $startUtcStr = $startTop ? $startTop->copy()->utc()->format('Ymd\THis\Z') : null;
                        $endUtcStr = $endTop ? $endTop->copy()->utc()->format('Ymd\THis\Z') : ($startTop ? $startTop->copy()->utc()->addHour()->format('Ymd\THis\Z') : null);
                        $datesParam = ($startUtcStr && $endUtcStr) ? ($startUtcStr . '/' . $endUtcStr) : null;
                        $gcalBase = 'https://calendar.google.com/calendar/render?action=TEMPLATE';
                        $gcalParams = [
                            'text' => $titleCal,
                            'dates' => $datesParam,
                            'details' => $detailsCal,
                            'location' => isset($event) && !empty($event->location) ? $event->location : '',
                            'ctz' => $tz,
                        ];
                        $gcalQuery = collect($gcalParams)
                            ->filter(fn($v) => !is_null($v) && $v !== '')
                            ->map(function($v, $k){ return $k . '=' . urlencode($v); })
                            ->implode('&');
                        $gcalUrl = $gcalBase . '&' . $gcalQuery;
                        // Determine registration state for top buttons
                        $authUserTop = Auth::user();
                        $registrationTop = isset($event) && $authUserTop ? $event->registrations()->where('user_id',$authUserTop->id)->first() : null;
                        $isRegisteredTop = $registrationTop && $registrationTop->status === 'active';
                    @endphp
                    @if(!empty($datesParam))
                        <button class="" type="button" onclick="window.open('{{ $gcalUrl }}','_blank')" title="Add to Google Calendar">
                            <svg class="ikon-calender-event" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-calendar-plus" viewBox="0 0 16 16">
                                <path d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7" />
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                            </svg>
                            <p>Add To Calender</p>
                        </button>
                        {{-- Tombol "Lihat Detail Registrasi" dihapus sesuai permintaan --}}
                    @else
                        <button class="" disabled title="Tanggal/waktu belum tersedia" style="opacity:.6;cursor:not-allowed;">
                            <svg class="ikon-calender-event" xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-calendar-plus" viewBox="0 0 16 16">
                                <path d="M8 7a.5.5 0 0 1 .5.5V9H10a.5.5 0 0 1 0 1H8.5v1.5a.5.5 0 0 1-1 0V10H6a.5.5 0 0 1 0-1h1.5V7.5A.5.5 0 0 1 8 7" />
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                            </svg>
                            <p>Add To Calender</p>
                        </button>
                    @endif
                </div>
            </div>
            <div class="event-title">
                <h4 class="mb-2">{{ isset($event) ? $event->title : 'Event' }}</h4>
                @php
                    // Label: Kelola Event (manage/create), Status Event (upcoming/ongoing/finished), Tanggal
                    $manageAction = isset($event) && !empty($event->manage_action) ? strtolower($event->manage_action) : null;
                    $startDate = isset($event) && !empty($event->event_date) ? (\Carbon\Carbon::parse($event->event_date)) : null;
                    $endDate = $startDate; // if no explicit end, use start
                    $nowLbl = \Carbon\Carbon::now();
                    $statusLbl = null;
                    if($startDate){
                        if($nowLbl->lt($startDate)) $statusLbl = 'Segera Hadir';
                        elseif($nowLbl->isSameDay($startDate)) $statusLbl = 'Berlangsung';
                        elseif($nowLbl->gt($startDate)) $statusLbl = 'Telah Selesai';
                    }
                    $tanggalLbl = $startDate ? $startDate->format('d F Y') : null;
                @endphp
                <p class="small text-white mb-0">{{ isset($event) && !empty($event->short_description) ? $event->short_description : '' }}</p>
            </div>
        </div>
        <div class="detail-box">
            <div class="detail-box-left">
                <img src="{{ isset($event) && $event->image_url ? $event->image_url : asset('aset/event.png') }}" alt="{{ isset($event) ? $event->title : 'Gambar Event' }}" onerror="this.src='{{ asset('aset/event.png') }}'">
                @php
                    $authUser = Auth::user();
                    $registration = isset($event) && $authUser ? $event->registrations()->where('user_id',$authUser->id)->first() : null;
                    $isRegistered = $registration && $registration->status === 'active';
                    $eventDate = isset($event) && $event->event_date ? ($event->event_date instanceof \Carbon\Carbon ? $event->event_date : \Carbon\Carbon::parse($event->event_date)) : null;
                    $parseEventTime = function($date, $raw) {
                        if (empty($raw)) return null;
                        if ($raw instanceof \Carbon\Carbon) return $raw;
                        $rawStr = trim((string)$raw);
                        // Normalize local notations like "14.30" -> "14:30" and trim timezone labels
                        $norm = preg_replace('/\s*(WIB|WITA|WIT)\s*$/i', '', $rawStr);
                        if (preg_match('/^\d{1,2}\.\d{2}$/', $norm)) {
                            $norm = str_replace('.', ':', $norm);
                        }
                        // If includes date part already, parse directly
                        if (preg_match('/\d{4}-\d{2}-\d{2}/', $norm)) {
                            try { return \Carbon\Carbon::parse($norm); } catch (\Throwable $e) { return null; }
                        }
                        // Combine with date when available
                        if ($date) {
                            $dateStr = $date instanceof \Carbon\Carbon ? $date->format('Y-m-d') : (string)$date;
                            try { return \Carbon\Carbon::parse($dateStr.' '.$norm); } catch (\Throwable $e) { return null; }
                        }
                        // Fallback parse
                        try { return \Carbon\Carbon::parse($norm); } catch (\Throwable $e) { return null; }
                    };
                    $startTime = isset($event) ? $parseEventTime($eventDate, $event->event_time) : null;
                    $endTime = isset($event) ? $parseEventTime($eventDate, $event->event_time_end) : null;
                    if(!$startTime && $eventDate) $startTime = $eventDate->copy()->startOfDay();
                    if(!$endTime && $eventDate) $endTime = $eventDate->copy()->endOfDay();
                    // Ensure parsed times align with the event date to avoid mis-parsing
                    if ($startTime && $eventDate && !$startTime->isSameDay($eventDate)) {
                        $startTime = $eventDate->copy()->startOfDay();
                    }
                    if ($endTime && $eventDate && !$endTime->isSameDay($eventDate)) {
                        $endTime = $eventDate->copy()->endOfDay();
                    }
                    $nowTs = \Carbon\Carbon::now(config('app.timezone'));
                    // Event status flags
                    $eventStarted = false;
                    $eventFinished = false;
                    if ($eventDate) {
                        $eventStarted = $startTime ? $nowTs->gte($startTime) : $nowTs->isSameDay($eventDate);
                        $eventFinished = $nowTs->gt($endTime ? $endTime : $eventDate->copy()->endOfDay());
                    }
                    // Attendance via QR verification (check-in)
                    $hasFeedback = $registration && ((isset($registration->feedback_submitted_at) && $registration->feedback_submitted_at) || $registration->certificate_issued_at);
                    $hasCertificate = $registration && $registration->certificate_issued_at;
                    $attendanceSubmitted = false;
                    if ($registration) {
                        $status = strtolower((string)($registration->attendance_status ?? ''));
                        $attendanceSubmitted = (
                            in_array($status, ['present','attended','checked-in'], true)
                            || !empty($registration->attended_at)
                            || !empty($registration->attendance_code_used_at)
                        );
                    }
                    $stepStates = [
                        'Registered' => $isRegistered,
                        'Attendance' => $attendanceSubmitted,
                        'Feedback' => $hasFeedback,
                        'Certificate' => $hasCertificate,
                    ];
                @endphp
                <div class="progress-box">
                    <h5>Your Progress</h5>
                    <div class="progress-line">&nbsp;</div>
                    <div class="progress-steps">
                        @foreach($stepStates as $label => $done)
                            @php
                                // Determine if this is the current step (first not done after previous done ones)
                                $previousAllDone = collect($stepStates)->takeWhile(fn($v,$k) => $k !== $label)->every(fn($v) => $v);
                                $current = !$done && $previousAllDone;
                            @endphp
                            <div class="step {{ $done ? 'active' : ($current ? 'current' : 'disabled') }}">
                                <div class="circle">
                                    @if($done)
                                        <p>✔</p>
                                    @elseif($current)
                                        <p>●</p>
                                    @else
                                        <p>○</p>
                                    @endif
                                </div>
                                <p>{{ $label }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="detail-box-right">
                @php
                    $eventObj = isset($event) ? $event : null;
                    $nowTs = \Carbon\Carbon::now();
                    // Event states
                    // Finished if now is after the event end time (or end of event day if time end missing)
                    $eventFinished = false;
                    // Started if now is equal/after start time
                    $eventStarted = false;
                    if ($eventDate) {
                        $eventFinished = $nowTs->gt($endTime ? $endTime : $eventDate->copy()->endOfDay());
                        $eventStarted = $nowTs->gte($startTime ? $startTime : $eventDate->copy()->startOfDay());
                    }
                    // Pricing state
                    $hasActiveDiscount = false;
                    $basePrice = $eventObj ? (float) ($eventObj->price ?? 0) : 0.0;
                    $finalPrice = $basePrice;
                    $discountMsg = null;
                    if ($eventObj && !empty($eventObj->discount_percentage) && !empty($eventObj->discount_until)) {
                        $discountUntil = \Carbon\Carbon::parse($eventObj->discount_until)->endOfDay();
                        $hasActiveDiscount = ($eventObj->discount_percentage > 0) && $nowTs->lte($discountUntil);
                        if ($hasActiveDiscount) {
                            $finalPrice = (float) ($eventObj->discounted_price ?? $basePrice);
                            $startOfToday = $nowTs->copy()->startOfDay();
                            $diffDaysInt = (int) $startOfToday->diffInDays($discountUntil, false);
                            if ($diffDaysInt > 1) {
                                $discountMsg = $diffDaysInt . ' Days left at this price!';
                            } elseif ($diffDaysInt === 1) {
                                $discountMsg = '1 Day left at this price!';
                            } else {
                                $discountMsg = null;
                            }
                        }
                    }
                @endphp
                <div class="info-price-box">
                    @php $isFreeNow = ((int) $finalPrice) === 0; @endphp
                    <div class="price-box">
                        <span>
                            @if($hasActiveDiscount && $basePrice > 0)
                                Rp.{{ number_format($basePrice, 0, ',', '.') }}
                            @endif
                        </span>
                        @if($isFreeNow)
                            <h5 class="price-free">GRATIS!</h5>
                        @else
                            <h5>Rp.{{ number_format($finalPrice, 0, ',', '.') }}</h5>
                        @endif
                        @if($hasActiveDiscount && $discountMsg)
                        <div class="diskon-time">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-alarm" viewBox="0 0 16 16">
                                <path d="M8.5 5.5a.5.5 0 0 0-1 0v3.362l-1.429 2.38a.5.5 0 1 0 .858.515l1.5-2.5A.5.5 0 0 0 8.5 9z" />
                                <path d="M6.5 0a.5.5 0 0 0 0 1H7v1.07a7.001 7.001 0 0 0-3.273 12.474l-.602.602a.5.5 0 0 0 .707.708l.746-.746A6.97 6.97 0 0 0 8 16a6.97 6.97 0 0 0 3.422-.892l.746.746a.5.5 0 0 0 .707-.708l-.601-.602A7.001 7.001 0 0 0 9 2.07V1h.5a.5.5 0 0 0 0-1zm1.038 3.018a6 6 0 0 1 .924 0 6 6 0 1 1-.924 0M0 3.5c0 .753.333 1.429.86 1.887A8.04 8.04 0 0 1 4.387 1.86 2.5 2.5 0 0 0 0 3.5M13.5 1c-.753 0-1.429.333-1.887.86a8.04 8.04 0 0 1 3.527 3.527A2.5 2.5 0 0 0 13.5 1" />
                            </svg>
                            <p>{{ $discountMsg }}</p>
                        </div>
                        @endif
                    </div>
                    @if($hasActiveDiscount)
                    <div class="diskon-event">
                        <p>{{ $eventObj->discount_percentage }}% OFF</p>
                    </div>
                    @endif
                </div>
                <hr class="line-info">
                <div class="info-boxluar">
                    <div class="info-item">
                        <div class="info-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-calendar-date" viewBox="0 0 16 16">
                                <path d="M6.445 11.688V6.354h-.633A13 13 0 0 0 4.5 7.16v.695c.375-.257.969-.62 1.258-.777h.012v4.61zm1.188-1.305c.047.64.594 1.406 1.703 1.406 1.258 0 2-1.066 2-2.871 0-1.934-.781-2.668-1.953-2.668-.926 0-1.797.672-1.797 1.809 0 1.16.824 1.77 1.676 1.77.746 0 1.23-.376 1.383-.79h.027c-.004 1.316-.461 2.164-1.305 2.164-.664 0-1.008-.45-1.05-.82zm2.953-2.317c0 .696-.559 1.18-1.184 1.18-.601 0-1.144-.383-1.144-1.2 0-.823.582-1.21 1.168-1.21.633 0 1.16.398 1.16 1.23" />
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4z" />
                            </svg>
                            <span class="label-event">Date</span>
                        </div>
                        <span class="isi-event">{{ isset($event) && $event->event_date ? \Carbon\Carbon::parse($event->event_date)->translatedFormat('d F Y') : '-' }}</span>
                    </div>
                    <div class="info-item">
                        <div class="info-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-clock" viewBox="0 0 16 16">
                                <path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71z" />
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0" />
                            </svg>
                            <span class="label-event">Time</span>
                        </div>
                            @php
                                $formatTimeOnly = function($raw){
                                    if(empty($raw)) return null;
                                    if($raw instanceof \Carbon\Carbon) return $raw->format('H.i');
                                    $s = trim((string)$raw);
                                    try { return \Carbon\Carbon::parse($s)->format('H.i'); } catch (\Throwable $e) {
                                        if(preg_match('/^(\d{1,2}):(\d{2})$/',$s)) return str_replace(':','.', $s);
                                        return null;
                                    }
                                };
                                $startT = isset($event) ? $formatTimeOnly($event->event_time) : null;
                                $endT = isset($event) ? $formatTimeOnly($event->event_time_end) : null;
                                if($startT && $endT){
                                    $timeRange = $startT . ' – ' . $endT . ' WIB';
                                } elseif($startT){
                                    $timeRange = $startT . ' WIB';
                                } else {
                                    $timeRange = '-';
                                }
                            @endphp
                        <span class="isi-event">{{ $timeRange }}</span>
                    </div>
                    <div class="info-item ">
                        <div class="info-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" fill="currentColor" class="bi bi-bar-chart" viewBox="0 0 16 16">
                                <path d="M4 11H2v3h2zm5-4H7v7h2zm5-5v12h-2V2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z" />
                            </svg>
                            <span class="label-event">Location</span>
                        </div>
                        <span class="isi-event">{{ $event->location ?? '-' }}</span>
                    </div>
                    <div class="info-item">
                        <div class="info-left">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-people" viewBox="0 0 16 16">
                                <path d="M7 14s-1 0-1-1 1-4 5-4 5 3 5 4-1 1-1 1zm4-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6m-5.784 6A2.24 2.24 0 0 1 5 13c0-1.355.68-2.75 1.936-3.72A6.3 6.3 0 0 0 5 9c-4 0-5 3-5 4s1 1 1 1zM4.5 8a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5" />
                            </svg>
                            <span class="label-event">Student Enrolled</span>
                        </div>
                        <span class="isi-event">{{ isset($event) ? $event->registrations()->where('status','active')->count() : 0 }}</span>
                    </div>
                </div>
                <hr>
                @php
                    $isFree = false;
                    if(isset($event)){
                        $hasDiscountLocal = method_exists($event,'hasDiscount') ? $event->hasDiscount() : false;
                        $finalPriceLocal = $hasDiscountLocal ? ($event->discounted_price ?? $event->price) : $event->price;
                        $isFree = ((int)($finalPriceLocal ?? 0)) === 0;
                    }
                @endphp
                <div class="booksave-row">
                        @php
                            // Registration rules
                            // - FREE events: allow booking until event finished (ignore started state)
                            // - PAID events: allow booking only before start time when available; else until end-of-day
                            $hasStartTime = isset($event) && !empty($event->event_time);
                                                        $canRegister = (!$isRegistered) && (
                                                                $eventDate
                                                                        ? (
                                                                                // For FREE events: block booking once started or finished
                                                                                ($isFree ? ((!$eventStarted) && (!$eventFinished)) : ($hasStartTime ? (!$eventStarted) : (!$eventFinished)))
                                                                            )
                                                                        : true
                                                        );
                            // Pre-compute saved state for current user to render initial label
                            $isSaved = false;
                            if(isset($event) && auth()->check()){
                                try {
                                    $isSaved = auth()->user()->savedEvents()->where('event_id', $event->id)->exists();
                                } catch (\Throwable $e) { $isSaved = false; }
                            }
                        @endphp
                        @if($canRegister)
                            @if($isFree)
                                <button type="button" id="bookFreeBtn" class="bookseat">Book Seat</button>
                            @else
                                <a class="bookseat text-white text-center" href="{{ route('payment', $event) }}" style="text-decoration:none;">Book Seat</a>
                            @endif
                        @else
                            @if(!$isRegistered && $eventFinished)
                                <button class="bookseat" disabled>Event Telah Selesai</button>
                            @elseif(!$isRegistered && $eventStarted)
                                <button class="bookseat" disabled>Event Sudah Dimulai</button>
                            @elseif($isRegistered)
                                <button class="bookseat" disabled>Seat Booked</button>
                            @else
                                <button class="bookseat" disabled>Unavailable</button>
                            @endif
                        @endif
                        <button type="button" class="save" id="saveEventBtn" data-event-id="{{ $event->id }}" style="cursor:pointer; position:relative; z-index:10;">{{ $isSaved ? 'Saved' : 'Save' }}</button>
                    </div>
                <hr>
                <div class="include-box">
                    <div class="include-title">
                        <h6 style="color:#000; margin: 0 0 8px 2px;">Benefit Event</h6>
                        <ul class="event-benefit-list" style="margin-bottom:0;">
                            @if(!empty($event->benefit))
                                @foreach(explode('|', $event->benefit) as $benefit)
                                    <li>{{ trim($benefit) }}</li>
                                @endforeach
                            @else
                                <li>No benefits listed.</li>
                            @endif
                        </ul>


                        
                    </div>
                </div>
                <hr>
                <div class="share">
                    <h6 class="share-title">Share this event:</h6>
                    <div class="share-list">
                        @if($isRegistered && $eventFinished && !$hasFeedback)
                            
                        @elseif($hasFeedback)
                        
                        @else
                            <span class="link-share" style="opacity:.4; cursor:not-allowed;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="share-bi bi-lock" viewBox="0 0 16 16">
                                    <path d="M8 1a2 2 0 0 0-2 2v2H5a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H10V3a2 2 0 0 0-2-2" />
                                </svg>
                            </span>
                        @endif

                        <a id="fbShare" class="share-item" aria-label="Share on Facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener" title="Share on Facebook">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4E5566" class="bi bi-facebook" viewBox="0 0 16 16" aria-hidden="true">
                                <path d="M12 2.04V.5H9.75C8.26.5 7.5 1.5 7.5 2.83V4H6v2h1.5v6H10V6h1.5l.5-2H10V2.83C10 2.2 10.4 2 11 2H13v.04z"/>
                            </svg>
                        </a>

                        <a id="xShare" class="share-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4E5566" class="bi bi-twitter-x" viewBox="0 0 16 16">
                                <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z" />
                            </svg>
                        </a>

                        <a id="emailShare" href="#" class="share-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4E5566" class="bi bi-envelope" viewBox="0 0 16 16">
                                <path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v.217l-8 4.8-8-4.8z"/>
                                <path d="M0 4.697v7.104l5.803-3.482z"/>
                                <path d="M6.761 8.83 0 12.803V14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-1.197l-6.761-3.973-1.239.744z"/>
                                <path d="M10.197 8.32 16 4.697v7.104z"/>
                            </svg>
                        </a>

                        <a id="waShare" href="#" class="share-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#4E5566" class="bi bi-whatsapp" viewBox="0 0 16 16">
                                <path d="M13.601 2.326A7.85 7.85 0 0 0 7.994 0C3.627 0 .068 3.558.064 7.926c0 1.399.366 2.76 1.057 3.965L0 16l4.204-1.102a7.9 7.9 0 0 0 3.79.965h.004c4.368 0 7.926-3.558 7.93-7.93A7.9 7.9 0 0 0 13.6 2.326zM7.994 14.521a6.6 6.6 0 0 1-3.356-.92l-.24-.144-2.494.654.666-2.433-.156-.251a6.56 6.56 0 0 1-1.007-3.505c0-3.626 2.957-6.584 6.591-6.584a6.56 6.56 0 0 1 4.66 1.931 6.56 6.56 0 0 1 1.928 4.66c-.004 3.639-2.961 6.592-6.592 6.592m3.615-4.934c-.197-.099-1.17-.578-1.353-.646-.182-.065-.315-.099-.445.099-.133.197-.513.646-.627.775-.114.133-.232.148-.43.05-.197-.1-.836-.308-1.592-.985-.59-.525-.985-1.175-1.103-1.372-.114-.198-.011-.304.088-.403.087-.088.197-.232.296-.346.1-.114.133-.198.198-.33.065-.134.034-.248-.015-.347-.05-.099-.445-1.076-.612-1.47-.16-.389-.323-.335-.445-.34-.114-.007-.247-.007-.38-.007a.73.73 0 0 0-.529.247c-.182.198-.691.677-.691 1.654s.71 1.916.81 2.049c.098.133 1.394 2.132 3.383 2.992.47.205.84.326 1.129.418.475.152.904.129 1.246.08.38-.058 1.171-.48 1.338-.943.164-.464.164-.86.114-.943-.049-.084-.182-.133-.38-.232" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Registration Modal -->
        <div class="modal fade" id="registrationModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Registration Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if($isRegistered && $authUser)
                            <form>
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" value="{{ $authUser->name }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="text" class="form-control" value="{{ $authUser->email }}" disabled>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Registration Code</label>
                                    <input type="text" class="form-control" value="{{ $registration->registration_code ?? 'Pending assignment' }}" disabled>
                                </div>
                                
                            </form>
                        @else
                            <p class="text-muted">You are not registered yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scan QR Modal -->
        <div class="modal fade" id="scanQrModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Scan QR Event</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="qr-reader" style="width:100%; max-width:520px; margin:0 auto;"></div>
                        <div id="qr-success" class="text-center d-none" style="padding: 16px 8px;">
                            <div style="display:flex;justify-content:center;margin:16px 0;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="84" height="84" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                    <polyline points="22 4 12 14.01 9 11.01"/>
                                </svg>
                            </div>
                            <p class="mb-1 fw-semibold text-success">Absensi Berhasil Dilakukan</p>
                            <p class="text-muted" style="margin-bottom:16px;">{{ $event->title }}<br>{{ optional($eventDate)->translatedFormat('l, d F Y') }}</p>
                            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                        <div class="mt-3">
                            <div id="qr-status" class="alert alert-info small">Arahkan kamera ke QR event untuk memindai.</div>
                            <div class="d-flex justify-content-center mt-2 gap-2 flex-wrap">
                                <button id="qr-permission-btn" type="button" class="btn btn-sm btn-primary d-none">Izinkan Kamera</button>
                                <button id="qr-test-btn" type="button" class="btn btn-sm btn-outline-primary d-none">Test Kamera</button>
                                <label class="btn btn-sm btn-outline-secondary d-none" id="qr-upload-btn">
                                    Unggah QR
                                    <input type="file" id="qr-file-input" accept="image/*" class="d-none">
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="mapModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Location Map</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if($isRegistered)
                            @if(!empty($event->maps_url))
                                <div class="text-center">
                                    <img src="{{ Storage::url($event->maps_url) }}" alt="Map" style="max-width:100%; height:auto; border:1px solid #eee; border-radius:8px;" />
                                </div>
                            @elseif(!empty($event->latitude) && !empty($event->longitude))
                                <iframe width="100%" height="400" style="border:0" loading="lazy" allowfullscreen
                                    referrerpolicy="no-referrer-when-downgrade"
                                    src="https://www.google.com/maps/embed/v1/view?key=YOUR_GOOGLE_MAPS_KEY&center={{ $event->latitude }},{{ $event->longitude }}&zoom=15">
                                </iframe>
                            @else
                                <p class="text-muted">Map not available.</p>
                            @endif
                        @else
                            <p class="text-muted">Register to view the map.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="resource-box">
            <h5>Participant Resources</h5>
            <div class="participant-resources">

                {{-- Virtual Background Resource --}}
                <div class="resource-card {{ (!empty($event->vbg_path) && $isRegistered) ? '' : 'locked' }}">
                    <div class="img-resource">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-image" viewBox="0 0 16 16">
                            <path d="M14.002 3H2c-.55 0-1 .45-1 1v8c0 .55.45 1 1 1h12c.55 0 1-.45 1-1V4c0-.55-.45-1-1-1zm0 1v.217l-3.106 3.106a.5.5 0 0 1-.707 0L7.5 5.207l-4.5 4.5V4h11zm-12 8V9.707l3.646-3.647a.5.5 0 0 1 .708 0l2.647 2.646 3.646-3.646a.5.5 0 0 1 .708 0L15 8.293V12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1z"/>
                        </svg>
                    </div>
                    <div class="resource-value">
                        <h6>Virtual Background</h6>
                        <p>@if(!empty($event->vbg_path) && $isRegistered) Download your event background @else Not available @endif</p>
                    </div>
                    @if(!empty($event->vbg_path) && $isRegistered)
                        <a class="link-share" href="{{ Storage::url($event->vbg_path) }}" download>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="share-bi bi-download" viewBox="0 0 16 16">
                                <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5A1.5 1.5 0 0 0 2.5 14h11a1.5 1.5 0 0 0 1.5-1.5V10.4a.5.5 0 0 1 1 0v2.1A2.5 2.5 0 0 1 13.5 15h-11A2.5 2.5 0 0 1 0 12.5V10.4a.5.5 0 0 1 .5-.5z"/>
                                <path d="M7.646 10.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 9.293V1.5a.5.5 0 0 0-1 0v7.793L5.354 7.146a.5.5 0 1 0-.708.708z"/>
                            </svg>
                        </a>
                    @else
                        <span class="link-share d-flex align-items-center" style="opacity:.4; cursor:not-allowed;">
                            
                        </span>
                    @endif
                </div>

                <div class="resource-card {{ (isset($isRegistered) && $isRegistered && ((isset($eventStarted) && $eventStarted) || (isset($attendanceSubmitted) && $attendanceSubmitted))) ? '' : 'locked' }}" style="position:relative;">
                    <div class="img-resource">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-qr-code-scan" viewBox="0 0 16 16"><path d="M2 2h2v2H2V2Z"/><path d="M6 0H0v6h6V0ZM2 4V2h2v2H2Z"/><path d="M12 2h2v2h-2V2Z"/><path d="M16 0h-6v6h6V0Zm-4 4V2h2v2h-2Z"/><path d="M2 12h2v2H2v-2Z"/><path d="M6 10H0v6h6v-6Zm-4 4v-2h2v2H2Z"/><path d="M7 2h1v1H7V2Z"/><path d="M8 4h1v1H8V4Z"/><path d="M2 7h1v1H2V7Z"/><path d="M4 8h1v1H4V8Z"/><path d="M12 7h1v1h-1V7Z"/><path d="M7 12h1v1H7v-1Z"/><path d="M8 13h1v1H8v-1Z"/><path d="M9 7h1v1H9V7Z"/><path d="M10 2h1v1h-1V2Z"/><path d="M10 11h1v1h-1v-1Z"/><path d="M11 10h1v1h-1v-1Z"/><path d="M12 9h1v1h-1V9Z"/><path d="M13 8h1v1h-1V8Z"/><path d="M14 7h1v1h-1V7Z"/><path d="M15 6h1v1h-1V6Z"/><path d="M12 12h1v1h-1v-1Z"/><path d="M13 13h1v1h-1v-1Z"/><path d="M14 12h1v1h-1v-1Z"/></svg>
                    </div>
                    <div class="resource-value">
                        <h6>Attendance QR Event</h6>
                        @if(isset($attendanceSubmitted) && $attendanceSubmitted)
                            <p class="text-success" style="font-weight:600;">Absensi Berhasil Dilakukan</p>
                        @else
                            <p>Scan QR Event to mark your attendance.</p>
                        @endif
                    </div>
                    @if(isset($isRegistered) && $isRegistered && isset($attendanceSubmitted) && $attendanceSubmitted)
                        <span class="d-inline-flex align-items-center" style="position:absolute; top:24px; right:10px;" title="Absensi Berhasil Dilakukan">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#22c55e" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-label="Absensi Berhasil">
                                <circle cx="12" cy="12" r="9"></circle>
                                <polyline points="8 12 11 15 16 10"></polyline>
                            </svg>
                        </span>
                    @elseif(isset($isRegistered) && $isRegistered && isset($eventStarted) && $eventStarted)
                        <a class="link-share" href="{{ route('events.scan', $event) }}" title="Buka Halaman Scan" style="position:absolute; top:24px; right:10px; text-decoration:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-label="Buka Halaman Scan">
                                <path d="M18 13v6a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                <polyline points="15 3 21 3 21 9" />
                                <line x1="10" y1="14" x2="21" y2="3" />
                            </svg>
                        </a>
                    @else
                        <span class="link-share d-inline-flex align-items-center" style="position:absolute; top:24px; right:10px; opacity:.4; cursor:not-allowed;" title="Scan tersedia saat acara dimulai"></span>
                    @endif
                </div>

                <div class="resource-card {{ (isset($isRegistered) && $isRegistered && isset($hasFeedback) && $hasFeedback) ? '' : 'locked' }}">
                    <div class="img-resource">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-award" viewBox="0 0 16 16">
                            <path d="M9.669.864 8 0 6.331.864l-1.858.282-.842 1.68-1.337 1.32L2.6 6l-.306 1.854 1.337 1.32.842 1.68 1.858.282L8 12l1.669-.864 1.858-.282.842-1.68 1.337-1.32L13.4 6l.306-1.854-1.337-1.32-.842-1.68zm1.196 1.193.684 1.365 1.086 1.072L12.387 6l.248 1.506-1.086 1.072-.684 1.365-1.51.229L8 10.874l-1.355-.702-1.51-.229-.684-1.365-1.086-1.072L3.614 6l-.25-1.506 1.087-1.072.684-1.365 1.51-.229L8 1.126l1.356.702z" />
                            <path d="M4 11.794V16l4-1 4 1v-4.206l-2.018.306L8 13.126 6.018 12.1z" />
                        </svg>
                    </div>
                    
                    <div class="resource-value">
                        <h6>Certificate</h6>
                        @php
                            // Show certificate availability only when user registered and has submitted feedback (post-event)
                            // $isRegistered and $hasFeedback are computed earlier in the view
                        @endphp
                        @if(isset($isRegistered) && $isRegistered)
                            @if(isset($hasFeedback) && $hasFeedback)
                                @if(!empty($event->certificate_path))
                                    <p>Certificate available — <a href="{{ Storage::url($event->certificate_path) }}" target="_blank">Download</a></p>
                                @else
                                    <p>Your certificate will be available soon. Thank you for submitting feedback.</p>
                                @endif
                            @else
                                <p>Available after you submit feedback for this event.</p>
                            @endif
                        @else
                            <p>Available after event completion.</p>
                        @endif
                    </div>
                    @if(isset($isRegistered) && $isRegistered && isset($hasFeedback) && $hasFeedback && !empty($event->certificate_path))
                        <a class="link-share" href="{{ Storage::url($event->certificate_path) }}" target="_blank">Download</a>
                    @else
                        <span class="link-share d-flex align-items-center" style="opacity:.6; cursor:not-allowed;"></span>
                    @endif
                </div>
            
            <div class="resource-card{{ !$isRegistered ? ' locked' : '' }}">
                    @if(isset($event) && $event->type === 'online' && !empty($event->zoom_link))
                        <div class="img-resource">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-camera-video" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M0 5a2 2 0 0 1 2-2h7a2 2 0 0 1 2 2v.5l3.553-2.132A.5.5 0 0 1 16 3.5v9a.5.5 0 0 1-.447.5.5.5 0 0 1-.276-.083L11 10.5V11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V5zm2-1a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1H2zm12 2.5-3 1.8v2.4l3 1.8V6.5z"/>
                            </svg>
                        </div>
                        <div class="resource-value">
                            <h6>Link Zoom</h6>
                            <p>Available for registered participants</p>
                        </div>
                        <a class="link-share" href="{{ $event->zoom_link }}" target="_blank" rel="noopener">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="share-bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                                <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                            </svg>
                        </a>
                    @else
                        <div class="img-resource">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-geo-alt" viewBox="0 0 16 16">
                                <path d="M12.166 8.94c-.524 1.062-1.234 2.12-1.96 3.07A32 32 0 0 1 8 14.58a32 32 0 0 1-2.206-2.57c-.726-.95-1.436-2.008-1.96-3.07C3.304 7.867 3 6.862 3 6a5 5 0 0 1 10 0c0 .862-.305 1.867-.834 2.94M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10" />
                                <path d="M8 8a2 2 0 1 1 0-4 2 2 0 0 1 0 4m0 1a3 3 0 1 0 0-6 3 3 0 0 0 0 6" />
                            </svg>
                        </div>
                        <div class="resource-value">
                            <h6>{{ (!empty($event->zoom_link) ? 'Link Zoom' : 'Location Map') }}</h6>
                            <p>{{ $isRegistered ? 'Available for registered participants' : 'Available upon registration' }}</p>
                        </div>
                        @php
                            $mapLink = '';
                            if(isset($event)){
                                if(!empty($event->maps_url)){
                                    $maps = trim($event->maps_url);
                                    if (\Illuminate\Support\Str::startsWith($maps, ['http://','https://','//'])) {
                                        $mapLink = $maps;
                                    } else {
                                        try { $mapLink = Storage::url($maps); } catch (\Throwable $e) { $mapLink = $maps; }
                                    }
                                } elseif(!empty($event->latitude) && !empty($event->longitude)) {
                                    $mapLink = 'https://www.google.com/maps?q=' . $event->latitude . ',' . $event->longitude;
                                }
                            }
                        @endphp
                        @if($isRegistered)
                            <a class="link-share" href="{{ (!empty($event->zoom_link) ? $event->zoom_link : ($mapLink ?: '#')) }}" target="_blank" rel="noopener">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="share-bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                                    <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                                </svg>
                            </a>
                        @else
                            
                        @endif
                    @endif
                </div>
            <div class="resource-card {{ ($isRegistered && $attendanceSubmitted) ? '' : 'locked' }}">
                <div class="img-resource">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                    </svg>
                </div>
                <div class="resource-value">
                    <h6>Feedback and Ratings</h6>
                    <p>Please fill out your feedback for this event</p>
                </div>

                @if($isRegistered && $attendanceSubmitted)
                    <button type="button" class="link-share" onclick="toggleFeedbackSection()" title="Open" style="border: none; background: transparent; padding: 0; margin: 0; cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="share-bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                            <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                        </svg>
                    </button>
                @else
                    <span class="link-share d-flex align-items-center" style="opacity:.6; cursor:not-allowed;"></span>
                @endif
                </div>
            </div>
        </div>
        
        <!-- Feedback & Reviews Section (Hidden by default) -->
        @if($isRegistered && $attendanceSubmitted)
        <div id="feedbackSection" style="display: none; background-color: white; box-shadow: 0px 0px 10px 10px rgba(0, 0, 0, 0.08); padding: 20px; margin-top: 50px; margin-left: 70px; border-radius: 20px; width: 90%; overflow: hidden;">
            <div class="d-flex justify-content-between align-items-center" style="margin-top: 20px; margin-left: 25px; margin-bottom: 10px;">
                <h5 class="mb-0 fw-bold" style="font-size: 1.1rem; color: #333;">Feedback & Reviews</h5>
                <button type="button" onclick="toggleFeedbackSection()" aria-label="Close" style="background: none; border: none; font-size: 1.3rem; color: #666; cursor: pointer; padding: 0; width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 4px; transition: background-color 0.2s; margin-right: 25px;" onmouseover="this.style.backgroundColor='#e9ecef'" onmouseout="this.style.backgroundColor='transparent'">
                    <span style="line-height: 1;">&times;</span>
                </button>
            </div>
            <div class="row g-0" style="margin-top: 20px; min-height: 300px;">
                <!-- Left Column: Participant Ratings -->
                <div class="col-md-6" style="background-color: #f8f9fa; padding: 1rem; border-right: 1px solid #e9ecef;">
                    <h6 class="fw-bold mb-3" style="font-size: 1rem; color: #333;">Participant Ratings</h6>
                    <div id="participant-ratings-list" style="max-height: 250px; overflow-y: auto;">
                        @if(isset($feedbacks) && $feedbacks->count() > 0)
                            @foreach($feedbacks as $feedback)
                                <div class="rating-card mb-2" style="background: white; border: 1px solid #e9ecef; border-radius: 8px; padding: 0.75rem;">
                                    <div class="stars-rating mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="{{ $i <= ($feedback->rating ?? 0) ? '#FFD600' : '#e0e0e0' }}" viewBox="0 0 16 16" style="margin-right: 2px;">
                                                <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                            </svg>
                                        @endfor
                                    </div>
                                    <p class="mb-1" style="color: #333; font-size: 0.85rem; line-height: 1.4;">{{ $feedback->comment }}</p>
                                    <p class="mb-0" style="color: #999; font-size: 0.8rem;">-{{ $feedback->user->name ?? 'Anonymous' }}</p>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-3">
                                <p style="font-size: 0.85rem;">Belum ada rating dari peserta</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Share Your Feedback -->
                <div class="col-md-6" style="background-color: white; padding: 1rem;">
                    <h6 class="fw-bold mb-3" style="font-size: 1rem; color: #333;">Share your feedback</h6>
                    <form action="#" method="POST" id="feedback-form">
                        @csrf
                        
                        <!-- Event Rating -->
                        <div class="mb-2">
                            <label class="form-label mb-1" style="font-weight: 500; color: #333; font-size: 0.9rem;">Rating Event</label>
                            <div class="stars-rating-input" data-target="eventRating" style="font-size: 1.5rem; letter-spacing: 4px; cursor: pointer; user-select: none;">
                                <span data-rating="1" style="color: #ccc; transition: color 0.2s;">☆</span>
                                <span data-rating="2" style="color: #ccc; transition: color 0.2s;">☆</span>
                                <span data-rating="3" style="color: #ccc; transition: color 0.2s;">☆</span>
                                <span data-rating="4" style="color: #ccc; transition: color 0.2s;">☆</span>
                                <span data-rating="5" style="color: #ccc; transition: color 0.2s;">☆</span>
                            </div>
                        </div>

                        <!-- Speaker Rating -->
                        <div class="mb-3">
                            <label class="form-label mb-1" style="font-weight: 500; color: #333; font-size: 0.9rem;">Rating Speaker</label>
                            <div class="stars-rating-input" data-target="speakerRating" style="font-size: 1.5rem; letter-spacing: 4px; cursor: pointer; user-select: none;">
                                <span data-rating="1" style="color: #ccc; transition: color 0.2s;">☆</span>
                                <span data-rating="2" style="color: #ccc; transition: color 0.2s;">☆</span>
                                <span data-rating="3" style="color: #ccc; transition: color 0.2s;">☆</span>
                                <span data-rating="4" style="color: #ccc; transition: color 0.2s;">☆</span>
                                <span data-rating="5" style="color: #ccc; transition: color 0.2s;">☆</span>
                            </div>
                        </div>

                        <!-- Feedback Text -->
                        <div class="mb-3">
                            <textarea id="feedback-text" name="feedback" class="form-control" rows="4" placeholder="Write your thoughts..." required style="border: 1px solid #ccc; border-radius: 8px; padding: 10px; font-size: 0.85rem; resize: none;"></textarea>
                        </div>

                        <!-- Submit Button -->
                        <button type="button" id="submit-feedback-btn" class="btn w-100 fw-semibold" style="background-color: #FFD600; color: #000; border: none; border-radius: 8px; padding: 0.6rem; font-size: 0.9rem;">
                            Submit Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <div class="desc-box">
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-event nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home" aria-selected="true">Overview</button>
                    <button class="nav-event nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Schedule</button>
                    <button class="nav-event nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact" aria-selected="false">Terms & Condition</button>
                    <span class="ms-auto d-flex align-items-center" style="gap:8px; font-size:12px;">
                        @if($hasCertificate && $event->certificate_path)
                            <a class="link-share" href="{{ Storage::url($event->certificate_path) }}" target="_blank">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="share-bi bi-box-arrow-up-right" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8.636 3.5a.5.5 0 0 0-.5-.5H1.5A1.5 1.5 0 0 0 0 4.5v10A1.5 1.5 0 0 0 1.5 16h10a1.5 1.5 0 0 0 1.5-1.5V7.864a.5.5 0 0 0-1 0V14.5a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h6.636a.5.5 0 0 0 .5-.5" />
                                    <path fill-rule="evenodd" d="M16 .5a.5.5 0 0 0-.5-.5h-5a.5.5 0 0 0 0 1h3.793L6.146 9.146a.5.5 0 1 0 .708.708L15 1.707V5.5a.5.5 0 0 0 1 0z" />
                                </svg>
                            </a>
                        @endif
                    </span>
                </div>
                <div class="tab-content" id="nav-tabContent">
                    <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab" tabindex="0">
                        {!! $event->description ?? '' !!}
                    </div>
                    <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                        <div class="scroll-schedule-box">
                            <div class="schedule-box">
                            <h6 class="title-schedule">Event Schedule</h6>
                        

                        <!-- QR scanning library -->
                        <script src="https://unpkg.com/html5-qrcode@2.3.8/minified/html5-qrcode.min.js"></script>
                        <script>
                        document.addEventListener('DOMContentLoaded', function(){
                            var scanner = null;
                            var modalEl = document.getElementById('scanQrModal');
                            if(!modalEl) return;
                            // Helper to detect secure origin acceptance (https or localhost)
                            function isSecureEnough(){
                                var isLocalhost = ['localhost','127.0.0.1','::1'].indexOf(location.hostname) !== -1;
                                return window.isSecureContext || isLocalhost;
                            }

                            // Attempt to request camera permission directly (user-gesture)
                            function requestCameraPermission(statusEl, onDone){
                                var constraintsAttempts = [
                                    { video: { facingMode: { exact: 'environment' } } },
                                    { video: { facingMode: { ideal: 'environment' } } },
                                    { video: true }
                                ];
                                var p = Promise.resolve();
                                constraintsAttempts.forEach(function(constraints){
                                    p = p.catch(function(){
                                        return navigator.mediaDevices.getUserMedia(constraints).then(function(stream){
                                            try { stream.getTracks().forEach(function(t){ t.stop(); }); } catch(_e){}
                                            if (typeof onDone === 'function') onDone(true);
                                            throw '__BREAK__';
                                        });
                                    });
                                });
                                return p.catch(function(e){ if(e==='__BREAK__') return; if (typeof onDone === 'function') onDone(false, e); });
                            }

                            // Try to read Permissions API when available
                            function checkPermissionState(){
                                if (navigator.permissions && navigator.permissions.query) {
                                    try { return navigator.permissions.query({ name: 'camera' }); } catch(_e) { return Promise.resolve(null); }
                                }
                                return Promise.resolve(null);
                            }

                            modalEl.addEventListener('shown.bs.modal', function(){
                                try {
                                    var el = document.getElementById('qr-reader');
                                    if (!el) return;
                                    if (scanner) { try { scanner.stop(); } catch(e){} }
                                    // Ensure Html5Qrcode library is available; load fallback if missing
                                    if (!window.Html5Qrcode) {
                                        if (statusEl) {
                                            statusEl.className = 'alert alert-warning small';
                                            statusEl.textContent = 'Memuat library pemindaian QR...';
                                        }
                                        var fallback = document.createElement('script');
                                        fallback.src = 'https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/minified/html5-qrcode.min.js';
                                        fallback.async = true;
                                        fallback.onload = function(){
                                            try {
                                                scanner = new Html5Qrcode('qr-reader');
                                            } catch (err) {
                                                console.error('Init Html5Qrcode gagal setelah fallback', err);
                                                if (statusEl) { statusEl.className = 'alert alert-danger small'; statusEl.textContent = 'Gagal memuat library QR. Gunakan unggah gambar sebagai alternatif.'; }
                                                if (uploadBtn) { uploadBtn.classList.remove('d-none'); }
                                                return;
                                            }
                                            // Lanjut ke alur normal di bawah
                                        };
                                        fallback.onerror = function(){
                                            console.error('Gagal memuat library Html5Qrcode dari CDN');
                                            if (statusEl) { statusEl.className = 'alert alert-danger small'; statusEl.textContent = 'Library QR tidak termuat. Silakan gunakan unggah gambar.'; }
                                            if (uploadBtn) { uploadBtn.classList.remove('d-none'); }
                                        };
                                        document.head.appendChild(fallback);
                                        // Jangan lanjutkan hingga library termuat
                                        return;
                                    }
                                    scanner = new Html5Qrcode("qr-reader");
                                    var statusEl = document.getElementById('qr-status');
                                    var successEl = document.getElementById('qr-success');
                                    var permBtn = document.getElementById('qr-permission-btn');
                                    var testBtn = document.getElementById('qr-test-btn');
                                    var uploadBtn = document.getElementById('qr-upload-btn');
                                    var fileInput = document.getElementById('qr-file-input');
                                    var eventStartedFlag = {{ (isset($eventStarted) && $eventStarted) ? 'true' : 'false' }};
                                    // Reset views each time modal opens
                                    if (successEl) successEl.classList.add('d-none');
                                    if (el) el.style.display = '';
                                    var currentEventId = {{ (int) ($event->id ?? 0) }};
                                    var eventQrToken = @json($event->attendance_qr_token ?? null);
                                    // Gate scanning if event hasn't started
                                    if (!eventStartedFlag) {
                                        if (statusEl) {
                                            statusEl.className = 'alert alert-warning small';
                                            statusEl.textContent = 'Scan tersedia saat acara dimulai.';
                                        }
                                        if (permBtn) { permBtn.classList.add('d-none'); }
                                        if (uploadBtn) { uploadBtn.classList.add('d-none'); }
                                        return;
                                    }
                                    // If insecure origin and not localhost, camera will be blocked
                                    if (!isSecureEnough()) {
                                        if (statusEl) {
                                            statusEl.className = 'alert alert-warning small';
                                            statusEl.innerHTML = 'Kamera diblokir pada koneksi non-HTTPS.<br>Gunakan HTTPS atau localhost.<br><small>Opsi solusi cepat:<br>- Aktifkan SSL di Laragon lalu akses: https://domain.test<br>- atau gunakan ngrok/cloudflared untuk URL https publik.</small>';
                                        }
                                        if (permBtn) { permBtn.classList.add('d-none'); }
                                        if (testBtn) { testBtn.classList.remove('d-none'); }
                                        if (uploadBtn) { uploadBtn.classList.remove('d-none'); }
                                        return;
                                    }

                                    function beginScan(cameraIdOrFacingMode){
                                        return scanner.start(cameraIdOrFacingMode, { fps: 10, qrbox: { width: 250, height: 250 } }, function(decodedText) {
                                        // Validate QR: must contain event URL with matching token
                                        var ok = false;
                                        try {
                                            var url = new URL(decodedText);
                                            ok = url.pathname.indexOf('/events/' + currentEventId) !== -1 && (!!eventQrToken ? url.searchParams.get('t') === eventQrToken : true);
                                        } catch(_e) {
                                            // Fallback string check
                                            ok = decodedText && decodedText.indexOf('/events/' + currentEventId) !== -1 && (!eventQrToken || decodedText.indexOf('t=' + eventQrToken) !== -1);
                                        }
                                        if (ok) {
                                            if (statusEl){ statusEl.className = 'alert alert-success small'; statusEl.textContent = 'Scan berhasil! Menyimpan absensi...'; }
                                            // Stop camera and show success UI
                                            try { scanner.stop(); } catch(e){}
                                            if (el) el.style.display = 'none';
                                            if (successEl) successEl.classList.remove('d-none');
                                            // NOTE: If you want to persist attendance here via AJAX, call a route.
                                            // Currently no attendance route is exposed in routes/web.php. If added, perform fetch here.
                                        } else {
                                            statusEl.className = 'alert alert-warning small';
                                            statusEl.textContent = 'QR tidak cocok dengan event ini.';
                                        }
                                        }, function(err){ /* per-frame failure, ignore */ });
                                    }

                                    // Helper: robust start attempts sequence
                                    function tryStartSequence(){
                                        // Attempt environment, then user, then enumerate
                                        var tried = false;
                                        return beginScan({ facingMode: 'environment' }).catch(function(e1){
                                            tried = true;
                                            // If permission denied, show controls
                                            if (permBtn && e1 && (e1.name === 'NotAllowedError' || e1.message?.toLowerCase().includes('permission'))) {
                                                permBtn.classList.remove('d-none'); permBtn.disabled = false;
                                            }
                                            return beginScan({ facingMode: 'user' });
                                        }).catch(function(e2){
                                            if (Html5Qrcode && Html5Qrcode.getCameras) {
                                                return Html5Qrcode.getCameras().then(function(cameras){
                                                    if (!cameras || !cameras.length) { throw new Error('Tidak ada kamera terdeteksi'); }
                                                    var back = cameras.find(function(c){ return /back|rear|environment/i.test(c.label); });
                                                    var chosen = (back || cameras[0]).id;
                                                    return beginScan(chosen);
                                                });
                                            }
                                            throw e2;
                                        }).catch(function(eFinal){
                                            console.warn('Gagal memulai kamera', eFinal);
                                            if (statusEl) { statusEl.className = 'alert alert-danger small'; statusEl.textContent = 'Gagal memulai kamera: ' + (eFinal && eFinal.message ? eFinal.message : eFinal); }
                                            if (permBtn) { permBtn.classList.remove('d-none'); permBtn.disabled = false; }
                                            if (uploadBtn) { uploadBtn.classList.remove('d-none'); }
                                            return Promise.reject(eFinal);
                                        });
                                    }

                                    // Preflight permission to improve camera availability (especially iOS)
                                    var preflight = function(){
                                        if (statusEl) { statusEl.className = 'alert alert-info small'; statusEl.textContent = 'Meminta izin kamera...'; }
                                        if (!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)) { return Promise.resolve(); }
                                        return checkPermissionState().then(function(state){
                                            if (state && state.state === 'denied') {
                                                if (statusEl) {
                                                    statusEl.className = 'alert alert-danger small';
                                                    statusEl.innerHTML = 'Izin kamera ditolak oleh browser. Buka pengaturan situs dan izinkan kamera, lalu coba lagi.';
                                                }
                                                if (permBtn) {
                                                    permBtn.classList.remove('d-none');
                                                    permBtn.disabled = false;
                                                }
                                                return Promise.resolve();
                                            }
                                            // Attempt a quick silent preflight; if it fails, we’ll show the manual button
                                            return navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } } })
                                                .then(function(stream){ try { stream.getTracks().forEach(function(t){ t.stop(); }); } catch(_e){} })
                                                .catch(function(e){ console.warn('Preflight getUserMedia failed', e); });
                                        });
                                    };

                                    preflight().then(function(){
                                        // If permission still not granted, show manual button to request
                                        if (navigator.permissions && navigator.permissions.query) {
                                            navigator.permissions.query({name: 'camera'}).then(function(ps){
                                                if (ps && ps.state !== 'granted' && permBtn) {
                                                    permBtn.classList.remove('d-none');
                                                    permBtn.disabled = false;
                                                    if (testBtn) { testBtn.classList.remove('d-none'); }
                                                }
                                            }).catch(function(){});
                                        } else {
                                            if (permBtn) { permBtn.classList.remove('d-none'); permBtn.disabled = false; }
                                            if (testBtn) { testBtn.classList.remove('d-none'); }
                                        }
                                        // Try robust start
                                        return tryStartSequence();
                                    }).catch(function(){ /* already handled in tryStartSequence */ });

                                    // Hook manual permission button
                                    // Test camera button (diagnostic)
                                    if (testBtn) {
                                        testBtn.onclick = function(){
                                            if (!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)) {
                                                statusEl.className = 'alert alert-danger small';
                                                statusEl.textContent = 'Browser tidak mendukung kamera.';
                                                return;
                                            }
                                            statusEl.className = 'alert alert-info small';
                                            statusEl.textContent = 'Menguji akses kamera...';
                                            navigator.mediaDevices.getUserMedia({ video: { facingMode: { ideal: 'environment' } } })
                                                .then(function(stream){
                                                    try { stream.getTracks().forEach(function(t){ t.stop(); }); } catch(_e){}
                                                    statusEl.className = 'alert alert-success small';
                                                    statusEl.textContent = 'Tes berhasil: kamera dapat diakses.';
                                                    testBtn.classList.add('d-none');
                                                    // Attempt start after successful test
                                                    tryStartSequence();
                                                })
                                                .catch(function(err){
                                                    statusEl.className = 'alert alert-danger small';
                                                    statusEl.textContent = 'Tes gagal: ' + (err && err.message ? err.message : err);
                                                    uploadBtn && uploadBtn.classList.remove('d-none');
                                                    permBtn && (permBtn.classList.remove('d-none'), permBtn.disabled = false);
                                                });
                                        };
                                    }
                                    if (permBtn) {
                                        permBtn.onclick = function(){
                                            permBtn.disabled = true;
                                            if (statusEl) { statusEl.className = 'alert alert-info small'; statusEl.textContent = 'Meminta izin kamera...'; }
                                            if (!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)) {
                                                if (statusEl) { statusEl.className = 'alert alert-danger small'; statusEl.textContent = 'Browser tidak mendukung kamera.'; }
                                                return;
                                            }
                                            requestCameraPermission(statusEl, function(granted, err){
                                                if (!granted) {
                                                    if (statusEl) {
                                                        statusEl.className = 'alert alert-danger small';
                                                        statusEl.textContent = 'Izin kamera gagal: ' + (err && err.message ? err.message : 'Unknown error');
                                                    }
                                                    permBtn.disabled = false;
                                                    return;
                                                }
                                                // After permission granted, try to start scan again
                                                if (statusEl) { statusEl.className = 'alert alert-info small'; statusEl.textContent = 'Memulai kamera...'; }
                                                if (Html5Qrcode && Html5Qrcode.getCameras) {
                                                    Html5Qrcode.getCameras().then(function(cameras){
                                                        var back = cameras && cameras.find(function(c){ return /back|rear|environment/i.test(c.label); });
                                                        var chosen = (back || (cameras && cameras[0]) || {}).id || { facingMode: 'environment' };
                                                        beginScan(chosen).then(function(){
                                                            permBtn.classList.add('d-none');
                                                        }).catch(function(e){
                                                            console.error('start error', e);
                                                            if (statusEl) { statusEl.className = 'alert alert-danger small'; statusEl.textContent = 'Gagal memulai kamera: ' + (e && e.message ? e.message : e); }
                                                            permBtn.disabled = false;
                                                        });
                                                    }).catch(function(){
                                                        beginScan({ facingMode: 'environment' }).then(function(){
                                                            permBtn.classList.add('d-none');
                                                        }).catch(function(e){
                                                            console.error('start error', e);
                                                            if (statusEl) { statusEl.className = 'alert alert-danger small'; statusEl.textContent = 'Gagal memulai kamera: ' + (e && e.message ? e.message : e); }
                                                            permBtn.disabled = false;
                                                        });
                                                    });
                                                } else {
                                                    tryStartSequence().then(function(){ permBtn.classList.add('d-none'); }).catch(function(e){
                                                        console.error('start error', e);
                                                        if (statusEl) { statusEl.className = 'alert alert-danger small'; statusEl.textContent = 'Gagal memulai kamera: ' + (e && e.message ? e.message : e); }
                                                        permBtn.disabled = false;
                                                    });
                                                }
                                            });
                                        };
                                    }
                                    // File upload fallback
                                    if (fileInput) {
                                        fileInput.onchange = function(){
                                            var f = fileInput.files && fileInput.files[0];
                                            if (!f) return;
                                            statusEl.className = 'alert alert-info small'; statusEl.textContent = 'Memindai gambar...';
                                            scanner.scanFile(f, true).then(function(decodedText){
                                                var ok = false;
                                                try {
                                                    var url = new URL(decodedText);
                                                    ok = url.pathname.indexOf('/events/' + currentEventId) !== -1 && (!!eventQrToken ? url.searchParams.get('t') === eventQrToken : true);
                                                } catch(_e) {
                                                    ok = decodedText && decodedText.indexOf('/events/' + currentEventId) !== -1 && (!eventQrToken || decodedText.indexOf('t=' + eventQrToken) !== -1);
                                                }
                                                if (ok) {
                                                    if (statusEl){ statusEl.className = 'alert alert-success small'; statusEl.textContent = 'Scan berhasil dari gambar! Menyimpan absensi...'; }
                                                    try { scanner.stop(); } catch(e){}
                                                    if (el) el.style.display = 'none';
                                                    if (successEl) successEl.classList.remove('d-none');
                                                } else {
                                                    statusEl.className = 'alert alert-warning small';
                                                    statusEl.textContent = 'Gambar tidak cocok dengan event ini.';
                                                }
                                            }).catch(function(err){
                                                console.error('scanFile error', err);
                                                statusEl.className = 'alert alert-danger small';
                                                statusEl.textContent = 'Gagal memindai gambar QR.';
                                            });
                                        };
                                        if (uploadBtn) {
                                            uploadBtn.onclick = function(){ fileInput && fileInput.click(); };
                                        }
                                    }
                                } catch (e) {
                                    console.error(e);
                                }
                            });
                            modalEl.addEventListener('hidden.bs.modal', function(){
                                if (scanner) {
                                    scanner.stop().catch(function(){/* ignore */});
                                    scanner.clear();
                                    scanner = null;
                                }
                                // Reset UI for next open
                                var el = document.getElementById('qr-reader');
                                var successEl = document.getElementById('qr-success');
                                var permBtn = document.getElementById('qr-permission-btn');
                                if (el) el.style.display = '';
                                if (successEl) successEl.classList.add('d-none');
                                if (permBtn) permBtn.classList.add('d-none');
                            });
                        });
                        </script>
                        @php
                                $items = collect();
                                if(isset($event)){
                                    // Schedule MUST come from schedule_json
                                    $rawSchedule = $event->schedule_json ?? null;

                                    // Normalize to array (supports casted array, JSON string, or stdClass)
                                    $scheduleArr = null;
                                    if (is_string($rawSchedule) && trim($rawSchedule) !== '') {
                                        $decoded = json_decode($rawSchedule, true);
                                        $scheduleArr = (json_last_error() === JSON_ERROR_NONE) ? $decoded : null;
                                    } elseif (is_array($rawSchedule)) {
                                        $scheduleArr = $rawSchedule;
                                    } elseif (is_object($rawSchedule)) {
                                        $scheduleArr = json_decode(json_encode($rawSchedule), true);
                                    }

                                    if (is_array($scheduleArr)) {
                                        $items = collect($scheduleArr)->map(function($row){
                                            $row = is_array($row) ? $row : (is_object($row) ? (array) $row : []);
                                            return (object) [
                                                'start' => $row['start'] ?? ($row['time_start'] ?? ($row['time'] ?? null)),
                                                'end' => $row['end'] ?? ($row['time_end'] ?? null),
                                                'title' => $row['title'] ?? ($row['activity'] ?? ''),
                                                'description' => $row['description'] ?? ($row['desc'] ?? ''),
                                            ];
                                        })->filter(function($it){
                                            return !empty($it->title) || !empty($it->description) || !empty($it->start) || !empty($it->end);
                                        })->values();
                                    }
                                }
                                $formatTime = function($t){
                                    if(empty($t)) return null;
                                    try { return \Carbon\Carbon::parse($t)->format('H.i'); } catch (\Throwable $e) { return is_string($t) ? $t : null; }
                                };
                            @endphp
                            @forelse($items as $idx => $it)
                                @php
                                    $start = $formatTime($it->start ?? null);
                                    $end = $formatTime($it->end ?? null);
                                    $timeStr = trim(($start ?: '') . ($end ? ' - '.$end : ''));
                                    if($timeStr) $timeStr .= ' WIB';
                                @endphp
                                <div class="schedule-item-box">
                                    <div class="schedule-line"></div>
                                    <div class="schedule-item">
                                        <p class="time">{{ $timeStr ?: '-' }}</p>
                                        <p class="activity">{{ $it->title ?? '' }}</p>
                                        <p class="desc">{{ $it->description ?? '' }}</p>
                                    </div>
                                </div>
                                <br>
                            @empty
                                <p class="text-muted" style="margin-left:30px;">Schedule will be announced.</p>
                            @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="terms-box tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" tabindex="0">
                        <h6 class="mb-3 mt-2">Terms & Condition</h6>
                        <div class="terms-content" style="margin-top: 10px;">
                            @php
                                $termsHtml = isset($event) ? ($event->terms_and_condition ?? ($event->terms_and_conditions ?? '')) : '';
                                $termsText = trim(preg_replace('/\xC2\xA0|\s+/', ' ', strip_tags((string) $termsHtml)));
                            @endphp

                            @if($termsText === '')
                                <p class="text-muted" style="margin:0;">Terms and Condition akan segera diumumkan</p>
                            @else
                                {!! $termsHtml !!}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        
        <script>
            // Toggle feedback section visibility
            function toggleFeedbackSection() {
                const section = document.getElementById('feedbackSection');
                if (section) {
                    if (section.style.display === 'none' || section.style.display === '') {
                        section.style.display = 'block';
                        // Smooth scroll to section
                        setTimeout(() => {
                            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 100);
                    } else {
                        section.style.display = 'none';
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', () => {
                // Check if feedback section should be open after reload
                if (sessionStorage.getItem('feedbackSectionOpen') === 'true') {
                    const section = document.getElementById('feedbackSection');
                    if (section) {
                        section.style.display = 'block';
                        setTimeout(() => {
                            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }, 300);
                    }
                    sessionStorage.removeItem('feedbackSectionOpen');
                }
                
                // Initialize star rating inputs (outline stars that fill on click)
                document.querySelectorAll('.stars-rating-input').forEach((starContainer) => {
                    const target = starContainer.getAttribute('data-target');
                    let selectedRating = 0;
                    
                    starContainer.querySelectorAll('span').forEach((star, index) => {
                        const rating = index + 1;
                        star.dataset.rating = rating;
                        
                        // Hover effect
                        star.addEventListener('mouseenter', () => {
                            starContainer.querySelectorAll('span').forEach((s, idx) => {
                                s.textContent = idx < rating ? '★' : '☆';
                                s.style.color = idx < rating ? '#FFD600' : '#ccc';
                            });
                        });
                        
                        star.addEventListener('mouseleave', () => {
                            starContainer.querySelectorAll('span').forEach((s, idx) => {
                                if (selectedRating > 0) {
                                    s.textContent = idx < selectedRating ? '★' : '☆';
                                    s.style.color = idx < selectedRating ? '#FFD600' : '#ccc';
                                } else {
                                    s.textContent = '☆';
                                    s.style.color = '#ccc';
                                }
                            });
                        });
                        
                        // Click to select
                        star.addEventListener('click', () => {
                            selectedRating = rating;
                            starContainer.dataset.selectedRating = selectedRating;
                            starContainer.querySelectorAll('span').forEach((s, idx) => {
                                s.textContent = idx < rating ? '★' : '☆';
                                s.style.color = idx < rating ? '#FFD600' : '#ccc';
                            });
                        });
                    });
                });

                // --- Feedback dynamic submit ---
                const submitBtn = document.getElementById('submit-feedback-btn');
                const feedbackText = document.getElementById('feedback-text');
                
                // Helpers to read selected ratings from the UI
                function getRatingByTarget(targetName){
                    const cont = document.querySelector(`.stars-rating-input[data-target="${targetName}"]`);
                    if(!cont) return 0;
                    return parseInt(cont.dataset.selectedRating || '0', 10);
                }

                function updateSubmitState() {
                    const hasText = feedbackText && feedbackText.value && feedbackText.value.trim();
                    submitBtn.disabled = !hasText;
                }

                feedbackText.addEventListener('input', function() {
                    updateSubmitState();
                });
                // Ensure initial state
                updateSubmitState();
                // Note: star hover/active styles were removed earlier per design; selection is indicated via 'selected' class only.
                // Reset after submit: show confirmation modal with checkbox
                const modalHtml = `
                    <div class="modal fade" id="feedback-confirm-modal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Konfirmasi Feedback</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                                                <div class="modal-body">
                                                    <p class="text-center mb-2">
                                                        Mohon pastikan feedback Anda sesuai pedoman. Feedback tidak boleh mengandung SARA, pornografi, ancaman, ujaran kebencian, atau konten ilegal lainnya.
                                                    </p>
                                                    <div class="d-flex align-items-center mb-3" style="justify-content: flex-start;">
                                                        <input class="form-check-input me-2" type="checkbox" value="" id="confirm-eval">
                                                        <label class="form-check-label fw-semibold text-dark mb-0" for="confirm-eval">Saya yakin bahwa feedback ini digunakan untuk keperluan evaluasi IdSpora. Lanjutkan?</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="button" class="btn btn-primary" id="confirm-submit-feedback" disabled>Kirim Feedback</button>
                                                </div>
                        </div>
                    </div>
                    </div>`;

                // append modal to body once
                const tmpDiv = document.createElement('div');
                tmpDiv.innerHTML = modalHtml;
                document.body.appendChild(tmpDiv.firstElementChild);

                const modalEl = document.getElementById('feedback-confirm-modal');
                let feedbackConfirmModal = null;
                function ensureModalInitialized() {
                    if (feedbackConfirmModal) return;
                    if (modalEl && window.bootstrap && typeof bootstrap.Modal === 'function') {
                        feedbackConfirmModal = new bootstrap.Modal(modalEl);
                        // ensure confirm button disabled until checkbox checked
                        const modalBtn = modalEl.querySelector('#confirm-submit-feedback');
                        const modalCheckbox = modalEl.querySelector('#confirm-eval');
                        if (modalBtn) modalBtn.disabled = !(modalCheckbox && modalCheckbox.checked);
                        if (modalCheckbox && modalBtn) {
                            modalCheckbox.addEventListener('change', function() { modalBtn.disabled = !this.checked; });
                        }
                    }
                }

                submitBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const text = feedbackText.value.trim();
                    if (!text) {
                        alert('Isi feedback terlebih dahulu.');
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Submit Feedback';
                        return;
                    }
                    // ensure user picked at least an event rating
                    const eventRatingNow = getRatingByTarget('eventRating');
                    if (eventRatingNow < 1) {
                        alert('Silakan pilih rating untuk acara.');
                        return;
                    }
                    // ensure confirmation modal is initialized, then show
                    ensureModalInitialized();
                    if (feedbackConfirmModal) {
                        // reset checkbox
                        const cb = modalEl.querySelector('#confirm-eval');
                        if (cb) cb.checked = false;
                        // ensure modal confirm button is disabled when modal opens
                        const modalBtnEl = modalEl.querySelector('#confirm-submit-feedback');
                        if (modalBtnEl) modalBtnEl.disabled = true;
                        feedbackConfirmModal.show();
                        return;
                    }
                    // fallback: if bootstrap not available, use simple confirm
                    const fallback = window.confirm('Saya yakin bahwa feedback ini digunakan untuk keperluan evaluasi IdSpora. Lanjutkan?');
                    if (!fallback) return;
                    // perform submit directly if fallback confirmed
                    performFeedbackSubmit(true);
                });

                // Modal confirm button handler
                const modalConfirmBtn = document.getElementById('confirm-submit-feedback');
                if (modalConfirmBtn) {
                    const modalCheckbox = modalEl.querySelector('#confirm-eval');
                    // start disabled
                    modalConfirmBtn.disabled = true;
                    if (modalCheckbox) {
                        modalCheckbox.addEventListener('change', function() {
                            modalConfirmBtn.disabled = !this.checked;
                        });
                    }
                    modalConfirmBtn.addEventListener('click', function() {
                        const cb = modalEl.querySelector('#confirm-eval');
                        if (!cb || !cb.checked) {
                            alert('Silakan centang kotak "Saya yakin bahwa feedback ini digunakan untuk keperluan evaluasi IdSpora." untuk melanjutkan.');
                            return;
                        }
                        // close modal
                        try { feedbackConfirmModal.hide(); } catch (e) {}
                        performFeedbackSubmit(true);
                    });
                }

                function performFeedbackSubmit(agreed) {
                    const text = feedbackText.value.trim();
                    const eventRatingNow = getRatingByTarget('eventRating');
                    const speakerRatingNow = getRatingByTarget('speakerRating');
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Saving...';
                    fetch('/feedback/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            event_id: @json($event->id),
                            rating: eventRatingNow,
                            speaker_rating: speakerRatingNow,
                            comment: text,
                            agreed_guidelines: !!agreed
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Submit Feedback';
                        if (data.success) {
                            // Show success message
                            alert('Feedback berhasil dikirim! Terima kasih atas feedback Anda.');
                            
                            // Reset form
                            feedbackText.value = '';
                            document.querySelectorAll('.stars-rating-input').forEach(container => {
                                container.dataset.selectedRating = '0';
                                container.querySelectorAll('span').forEach(s => {
                                    s.textContent = '☆';
                                    s.style.color = '#ccc';
                                });
                            });
                            
                            // Reload page to show new feedback (section will remain open)
                            // Store state before reload
                            sessionStorage.setItem('feedbackSectionOpen', 'true');
                            setTimeout(() => {
                                window.location.reload();
                            }, 500);
                        } else {
                            alert(data.message || 'Gagal menyimpan feedback.');
                        }
                    })
                    .catch(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Submit Feedback';
                        alert('Gagal menyimpan feedback.');
                    });
                }
            });
            // NOTE: Removed duplicate generic rating handlers.
            // The specific handlers for `#event-rating` and `#speaker-rating`
            // (defined inside DOMContentLoaded) are used to manage hover/click
            // and maintain separate `eventRating` and `speakerRating` state.
        </script>
        <!-- Ensure Bootstrap JS is available for modals (lazy-load if missing) -->
        <script>
            (function(){
                try {
                    if (!window.bootstrap || typeof window.bootstrap.Modal !== 'function') {
                        var s = document.createElement('script');
                        s.src = 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js';
                        s.defer = true; s.crossOrigin = 'anonymous';
                        document.body.appendChild(s);
                    }
                } catch (_e) {}
            })();
        </script>
        <script>
            // Save/Unsave event handler (supports ID and fallback by class)
            (function(){
                const nodeList = document.querySelectorAll('#saveEventBtn, .booksave-row .save');
                // Deduplicate elements in case the selector matches the same node twice
                const buttons = Array.from(new Set(Array.from(nodeList)));
                if(!buttons.length) return;

                function getCsrfToken(){
                    const meta = document.querySelector('meta[name="csrf-token"]');
                    if(meta && meta.content) return meta.content;
                    // Fallback to blade-injected token
                    return '{{ csrf_token() }}';
                }

                function onClick(e){
                    try { e.preventDefault(); e.stopPropagation(); } catch(_) {}
                    if (this.disabled) return; // guard against double-clicks

                    const original = (this.textContent || '').trim();
                    const wasSaved = original.toLowerCase() === 'saved';
                    this.disabled = true;
                    this.setAttribute('aria-busy', 'true');
                    this.textContent = 'Saving...';

                    fetch('{{ route('events.save', $event) }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json, text/html;q=0.9,*/*;q=0.8',
                            'X-CSRF-TOKEN': getCsrfToken(),
                            'X-Requested-With':'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    })
                    .then(async (r)=>{
                        if (r.redirected && r.url) {
                            if (r.url.includes('/sign-in') || r.url.includes('/login')) {
                                // Preserve return URL so user returns to this event after login
                                const ret = encodeURIComponent(window.location.href);
                                const loginUrl = r.url.includes('?') ? `${r.url}&return=${ret}` : `${r.url}?return=${ret}`;
                                window.location.href = loginUrl;
                                return { success: false, saved: wasSaved };
                            }
                        }
                        if (r.status === 401 || r.status === 419) {
                            const ret = encodeURIComponent(window.location.href);
                            window.location.href = '{{ route('login') }}' + `?return=${ret}`;
                            return { success: false, saved: wasSaved };
                        }
                        const ct = (r.headers.get('content-type')||'').toLowerCase();
                        if (ct.includes('application/json')) {
                            try {
                                const data = await r.json();
                                // Normalize payload
                                return {
                                    success: !!data.success,
                                    saved: typeof data.saved === 'boolean' ? data.saved : (!wasSaved)
                                };
                            } catch(_) {
                                return { success:false, saved: wasSaved };
                            }
                        }
                        return { success: r.ok, saved: r.ok ? (!wasSaved) : wasSaved };
                    })
                    .then(({success, saved})=>{
                        if(success){
                            this.textContent = saved ? 'Saved' : 'Save';
                            this.dataset.state = saved ? 'saved' : 'unsaved';
                            this.setAttribute('aria-pressed', saved ? 'true' : 'false');
                        } else {
                            this.textContent = original;
                        }
                    })
                    .catch(()=>{
                        // Network or fetch error: fallback to form POST
                        try {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '{{ route('events.save', $event) }}';
                            const csrf = document.createElement('input');
                            csrf.type = 'hidden'; csrf.name = '_token'; csrf.value = getCsrfToken();
                            form.appendChild(csrf);
                            document.body.appendChild(form);
                            form.submit();
                            return;
                        } catch(_e) {
                            // Visual feedback if even fallback fails
                            this.textContent = original;
                            try { this.classList.add('shake'); setTimeout(()=>this.classList.remove('shake'), 500); } catch(_){ }
                        }
                    })
                    .finally(()=>{
                        this.disabled = false;
                        this.removeAttribute('aria-busy');
                    });
                }

                buttons.forEach(function(b){ b.addEventListener('click', onClick); });
            })();
        </script>
        <script>
            // Free event booking via AJAX: redirect to ticket page
                (function(){
                    const btn = document.getElementById('bookFreeBtn');
                if(!btn) return;
                function submitFallbackForm(){
                    try {
                        const form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '{{ route('events.register.form', $event) }}';
                        form.style.display = 'none';
                        const csrf = document.createElement('input');
                        csrf.type = 'hidden';
                        csrf.name = '_token';
                        csrf.value = '{{ csrf_token() }}';
                        form.appendChild(csrf);
                        document.body.appendChild(form);
                        form.submit();
                    } catch (_e) {}
                }
                btn.addEventListener('click', function(){
                    const original = this.textContent;
                    this.disabled = true;
                    this.textContent = 'Processing...';
                    fetch('{{ route('events.register', $event) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json, text/html;q=0.9,*/*;q=0.8',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({})
                    })
                    .then(async (r) => {
                        // Handle auth/CSRF issues explicitly
                        if (r.status === 401 || r.status === 419) {
                            // Redirect to login so the user can re-authenticate
                            window.location.href = '{{ route('login') }}';
                            return;
                        }
                        const ct = (r.headers.get('content-type') || '').toLowerCase();
                        if (!r.ok) {
                            // Read body for message when possible
                            const t = await r.text();
                            this.textContent = original;
                            this.disabled = false;
                            // Fallback to form-based registration
                            submitFallbackForm();
                            return;
                        }
                        if (r.redirected && r.url) {
                            window.location.href = r.url; return;
                        }
                        if (ct.includes('application/json')) {
                            const data = await r.json();
                            if (data && data.redirect) { window.location.href = data.redirect; return; }
                            if (data && (data.status === 'ok' || data.status === 'already')) {
                                this.textContent = data.button_text || 'Seat Booked';
                                this.disabled = true;
                                // Trigger notification refresh immediately
                                try { if (typeof loadNotifications === 'function') setTimeout(() => loadNotifications(), 50); } catch(_e){}
                                // Ensure UI reflects registered state
                                setTimeout(()=> window.location.reload(), 300);
                                return;
                            }
                            // Unknown JSON shape: just reload to update state
                            window.location.reload();
                            return;
                        }
                        // Non-JSON success: reload to reflect state
                        window.location.reload();
                    })
                    .catch(() => {
                        this.textContent = original;
                        // Fallback to form-based registration on network errors
                        submitFallbackForm();
                    })
                    .finally(() => {
                        if (this.textContent === original) this.disabled = false;
                    });
                });
            })();
        </script>
         @include('partials.footer-before-login')
    </body>

    </html>