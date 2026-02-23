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
                            <a href="{{ route('admin.add-event') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                            <h4 class="mb-0 text-dark d-flex align-items-center">
                                <i class="bi bi-calendar-event me-2"></i>
                                Detail Event
                            </h4>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-warning">
                                <i class="bi bi-pencil me-1"></i> Edit
                            </a>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteEventModal">
                                <i class="bi bi-trash me-1"></i> Hapus
                            </button>
                        </div>
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
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-fill text-primary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Pembicara</small>
                                                <strong>{{ $event->speaker }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-journal-text text-secondary me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Materi</small>
                                                <strong>{{ $event->materi ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt-fill text-success me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Lokasi</small>
                                                <strong>{{ $event->location }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-diagram-3 text-dark me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Jenis</small>
                                                <strong>{{ $event->jenis ?? '-' }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-calendar-date text-info me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Tanggal</small>
                                                <strong>{{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-clock text-warning me-2"></i>
                                            <div>
                                                <small class="text-muted d-block">Waktu</small>
                                                <strong>
                                                    {{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }}
                                                    @if(!empty($event->event_time_end)) - {{ \Carbon\Carbon::parse($event->event_time_end)->format('H:i') }} @endif WIB
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Old Harga Tiket section removed -->
                            </div>
                        </div>
                    </div>

                    <!-- Extra Details and Documents -->
                    <div class="row mt-3 g-3">
                        <div class="col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-dark mb-3"><i class="bi bi-tag me-2"></i>Harga</h6>
                                <div class="mb-3">
                                    @php $isFree = (int)$event->price === 0; @endphp
                                    @if($isFree)
                                        <h3 class="text-success mb-0 fw-bold">Gratis</h3>
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
                                    {{-- Level removed per request --}}
                                    @if(!empty($event->discount_until))
                                    <li class="mb-2 d-flex align-items-center"><i class="bi bi-calendar-check text-success me-2"></i> <span><strong>Diskon s/d:</strong> {{ \Carbon\Carbon::parse($event->discount_until)->format('d F Y') }}</span></li>
                                    @endif
                                    @if(!empty($event->zoom_link))
                                    <li class="mb-2 d-flex align-items-center"><i class="bi bi-camera-video text-primary me-2"></i> <a href="{{ $event->zoom_link }}" target="_blank" class="link-primary">Buka Link Zoom</a></li>
                                    @endif
                                    
                                </ul>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-dark mb-3"><i class="bi bi-folder2-open me-2"></i>Dokumen Operasional</h6>
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="bi {{ !empty($event->vbg_path) ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i> Virtual Background</span>
                                        <span>
                                            @if(!empty($event->vbg_path))
                                                @php $vExt = strtolower(pathinfo($event->vbg_path, PATHINFO_EXTENSION)); @endphp
                                                @if(in_array($vExt, ['jpg','jpeg','png','gif','webp','bmp','svg']))
                                                    <a href="{{ Storage::url($event->vbg_path) }}" target="_blank" class="d-inline-block">
                                                        <img src="{{ Storage::url($event->vbg_path) }}" alt="VBG" class="rounded border" style="width:56px;height:36px;object-fit:cover;">
                                                    </a>
                                                @elseif($vExt === 'pdf')
                                                    <a href="{{ Storage::url($event->vbg_path) }}" target="_blank" class="link-primary"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
                                                @else
                                                    <a href="{{ Storage::url($event->vbg_path) }}" target="_blank" class="link-primary">Lihat</a>
                                                @endif
                                            @else <span class="text-muted">Belum ada</span> @endif
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="bi {{ !empty($event->certificate_path) ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i> Sertifikat</span>
                                        <span>
                                            @if(!empty($event->certificate_path))
                                                @php $cExt = strtolower(pathinfo($event->certificate_path, PATHINFO_EXTENSION)); @endphp
                                                @if(in_array($cExt, ['jpg','jpeg','png','gif','webp','bmp','svg']))
                                                    <a href="{{ Storage::url($event->certificate_path) }}" target="_blank" class="d-inline-block">
                                                        <img src="{{ Storage::url($event->certificate_path) }}" alt="Sertifikat" class="rounded border" style="width:56px;height:36px;object-fit:cover;">
                                                    </a>
                                                @elseif($cExt === 'pdf')
                                                    <a href="{{ Storage::url($event->certificate_path) }}" target="_blank" class="link-primary"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
                                                @else
                                                    <a href="{{ Storage::url($event->certificate_path) }}" target="_blank" class="link-primary">Lihat</a>
                                                @endif
                                            @else <span class="text-muted">Belum ada</span> @endif
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span><i class="bi {{ !empty($event->attendance_qr_image) ? 'bi-qr-code text-success' : 'bi-qr-code text-muted' }} me-2"></i> QR Absensi</span>
                                        <span class="d-flex align-items-center gap-2">
                                            @if(!empty($event->attendance_qr_image))
                                                @php $qExt = strtolower(pathinfo($event->attendance_qr_image, PATHINFO_EXTENSION)); $qrUrl = Storage::url($event->attendance_qr_image); @endphp
                                                <a href="{{ $qrUrl }}" target="_blank" class="d-inline-block">
                                                    <img src="{{ $qrUrl }}" alt="QR Absensi" class="rounded border" style="width:56px;height:56px;object-fit:cover;">
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-success" data-qr-src="{{ $qrUrl }}" data-qr-ext="{{ $qExt }}" data-qr-name="event-{{ $event->id }}-qr" id="btnDownloadQrPng"><i class="bi bi-filetype-png me-1"></i>Unduh PNG</button>
                                                <button type="button" class="btn btn-sm btn-outline-info" data-qr-src="{{ $qrUrl }}" data-qr-ext="{{ $qExt }}" data-qr-name="event-{{ $event->id }}-qr" id="btnDownloadQrJpg"><i class="bi bi-filetype-jpg me-1"></i>Unduh JPG</button>
                                                <form action="{{ route('admin.events.qr.generate', $event) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning"><i class="bi bi-arrow-repeat me-1"></i>Regenerate</button>
                                                </form>
                                            @else
                                                <span class="text-muted">Belum tersedia</span>
                                                <form action="{{ route('admin.events.qr.generate', $event) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success"><i class="bi bi-qr-code me-1"></i>Generate</button>
                                                </form>
                                            @endif
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Registered Participants -->
                    @php
                        // Eager-load users for participant list
                        try {
                            $registrations = $event->registrations()->with('user')->latest()->get();
                        } catch (\Throwable $e) {
                            $registrations = collect();
                        }
                    @endphp
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="border rounded p-4 {{ $registrations->count() ? 'bg-light' : 'bg-warning-subtle' }}">
                                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                                        <div class="d-flex align-items-center gap-2">
                                        <h6 class="text-dark mb-0"><i class="bi bi-people me-2"></i>Peserta Terdaftar</h6>
                                        <span id="participantsCountBadge" class="badge {{ $registrations->count() ? 'bg-primary' : 'bg-secondary' }}">Total: {{ $registrations->count() }}</span>
                                    </div>
                                    <div class="input-group input-group-sm" style="max-width: 320px;">
                                        <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                        <input type="text" id="participantSearch" class="form-control" placeholder="Cari peserta (nama/email)">
                                    </div>
                                </div>
                                @if($registrations->count())
                                <div id="participantsTableWrapper" class="table-responsive">
                                    <table id="participantsTable" class="table table-sm table-striped align-middle mb-0">
                                        <thead class="table-light">
                                            <tr data-reg-code="{{ $reg->registration_code ?? '' }}">
                                                <th style="width:48px;">No</th>
                                                <th style="width:220px;">Nama</th>
                                                <th style="width:240px;">Email</th>
                                                <th style="width:120px;">Status</th>
                                                <th style="width:160px;">Terdaftar</th>
                                                <th style="width:160px;">Bukti Pembayaran</th>
                                                <th style="width:160px;">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($registrations as $i => $reg)
                                            <tr>
                                                <td>{{ $i+1 }}</td>
                                                <td class="fw-semibold">{{ $reg->user->name ?? '-' }}</td>
                                                <td class="text-muted">{{ $reg->user->email ?? '-' }}</td>
                                                <td>
                                                    @php $st = strtolower((string)$reg->status); @endphp
                                                <span class="badge {{ $st === 'active' ? 'bg-success' : ($st === 'rejected' ? 'bg-danger' : 'bg-secondary') }}">{{ strtoupper($reg->status ?? '-') }}</span>
                                                </td>
                                                <td class="text-muted">{{ optional($reg->created_at)->format('d M Y H:i') }}</td>
                                                <td>
                                                    @if(!empty($reg->payment_proof))
                                                        @php $ppExt = strtolower(pathinfo($reg->payment_proof, PATHINFO_EXTENSION)); $ppUrl = Storage::url($reg->payment_proof); @endphp
                                                        <a href="{{ $ppUrl }}" target="_blank" class="d-inline-block">
                                                            @if(in_array($ppExt, ['jpg','jpeg','png','gif','webp','bmp','svg']))
                                                                <img src="{{ $ppUrl }}" alt="Bukti" class="rounded border" style="width:80px;height:48px;object-fit:cover;">
                                                            @else
                                                                <i class="bi bi-file-earmark-text"></i> Lihat
                                                            @endif
                                                        </a>
                                                    @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($st === 'pending')
                                                            <button type="button" class="btn btn-sm btn-success btn-action" 
                                                                data-action="{{ route('admin.events.registrations.approve', ['event'=>$event->id, 'registration'=>$reg->id]) }}" 
                                                                data-title="Setujui Pendaftaran" data-message="Setujui pendaftaran ini?" data-variant="approve">
                                                                Acc
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-danger ms-1 btn-action" 
                                                                data-action="{{ route('admin.events.registrations.reject', ['event'=>$event->id, 'registration'=>$reg->id]) }}" 
                                                                data-title="Tolak Pendaftaran" data-message="Tolak pendaftaran ini?" data-variant="reject">
                                                                Tolak
                                                            </button>
                                                        @else
                                                        <span class="text-muted small">-</span>
                                                    @endif
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

                    <!-- Location map and Benefit -->
                    <div class="row mt-3 g-3">
                        @if(!empty($event->latitude) && !empty($event->longitude))
                        <div class="col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-dark mb-3"><i class="bi bi-geo-alt me-2"></i>Lokasi Peta</h6>
                                <div id="eventMap" style="height:260px; border-radius:12px; overflow:hidden;"></div>
                                @if(!empty($event->maps_url))
                                    <a href="{{ $event->maps_url }}" target="_blank" class="btn btn-sm btn-outline-secondary mt-2"><i class="bi bi-box-arrow-up-right me-1"></i>Buka di Google Maps</a>
                                @endif
                            </div>
                        </div>
                        @elseif(!empty($event->maps_url))
                        <div class="col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-dark mb-2"><i class="bi bi-geo-alt me-2"></i>Lokasi</h6>
                                <a href="{{ $event->maps_url }}" target="_blank" class="btn btn-outline-secondary"><i class="bi bi-box-arrow-up-right me-1"></i>Buka di Google Maps</a>
                            </div>
                        </div>
                        @endif
                        @if(!empty($event->benefit))
                        <div class="col-lg-6">
                            <div class="border rounded p-3 h-100">
                                <h6 class="text-dark mb-2"><i class="bi bi-gift me-2"></i>Benefit</h6>
                                @php
                                    $raw = $event->benefit ?? '';
                                    $parts = preg_split('/\|\s*|\r\n|\n/', $raw);
                                    $items = array_values(array_filter(array_map('trim', (array)$parts), function($s){ return $s !== ''; }));
                                @endphp
                                @if(count($items))
                                    <ul class="mb-0 ps-3 small">
                                        @foreach($items as $b)
                                            <li>{{ $b }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="small">{!! nl2br(e($event->benefit)) !!}</div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Event Description -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="border-top pt-4">
                                <h5 class="text-dark mb-3">
                                    <i class="bi bi-file-text me-2"></i>Deskripsi Event
                                </h5>
                                <div class="bg-light rounded p-4">
                                    <div class="event-description">
                                        {!! $event->description !!}
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
                                @if($scheduleRows->count())
                                <div class="mt-4">
                                    <h5 class="text-dark mb-3"><i class="bi bi-clock-history me-2"></i>Jadwal Kegiatan</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width:110px;">Mulai</th>
                                                    <th style="width:110px;">Selesai</th>
                                                    <th style="width:110px;">Durasi</th>
                                                    <th style="width:240px;">Kegiatan</th>
                                                    <th>Deskripsi</th>
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
                                </div>
                                @else
                                <div class="mt-4">
                                    <h5 class="text-dark mb-2"><i class="bi bi-clock-history me-2"></i>Jadwal Kegiatan</h5>
                                    <div class="alert alert-light border small mb-0">Belum ada jadwal ditambahkan.</div>
                                </div>
                                @endif
                                <!-- Link Zoom -->
                                @if(!empty($event->zoom_link))
                                <div class="mt-4">
                                    <h5 class="text-dark mb-3"><i class="bi bi-camera-video me-2"></i>Link Zoom</h5>
                                    <a href="{{ $event->zoom_link }}" target="_blank" class="btn btn-outline-primary"><i class="bi bi-box-arrow-up-right me-1"></i> Buka Link Zoom</a>
                                </div>
                                @endif
                                <!-- Pengeluaran (Expenses) -->
                                @if(isset($event->expenses) && count($event->expenses))
                                <div class="mt-4">
                                    <h5 class="text-dark mb-3"><i class="bi bi-cash-stack me-2"></i>Pengeluaran</h5>
                                    <div class="table-responsive">
                                        <table class="table table-bordered align-middle">
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
                                @endif
                            </div>
                        </div>
                    </div>

                    @if(!empty($event->terms_and_conditions))
                    <!-- Terms & Conditions -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="border-top pt-4">
                                <h5 class="text-dark mb-3">
                                    <i class="bi bi-shield-check me-2"></i>Syarat & Ketentuan
                                </h5>
                                <div class="bg-light rounded p-4">
                                    <div class="event-description">
                                        {!! $event->terms_and_conditions !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Certificate Generation Section -->
                  

                    <!-- Action Buttons -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.add-event') }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-lg px-4" data-bs-toggle="modal" data-bs-target="#deleteEventModal">
                                    <i class="bi bi-trash me-1"></i> Hapus
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
/* Manage/Create action ribbon */
.manage-action-ribbon { position:absolute; top:12px; left:-6px; padding:6px 14px 6px 18px; background:linear-gradient(135deg,#0d6efd,#3b82f6); color:#fff; font-size:.75rem; font-weight:600; letter-spacing:.5px; text-transform:uppercase; border-radius:0 6px 6px 0; box-shadow:0 4px 12px -3px rgba(0,0,0,.25); display:flex; align-items:center; z-index:5; }
.manage-action-ribbon:before { content:''; position:absolute; left:0; top:100%; width:0; height:0; border-left:6px solid #093d94; border-top:6px solid transparent; }
.manage-action-ribbon .ribbon-text { position:relative; }
.manage-action-create { background:linear-gradient(135deg,#16a34a,#22c55e); }
.manage-action-create:before { border-left-color:#0f5d2c; }
.manage-action-manage { background:linear-gradient(135deg,#0d6efd,#3b82f6); }
.manage-action-manage:before { border-left-color:#093d94; }
</style>
@endsection

@section('scripts')
@if($event->image)
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content border-0 shadow-lg image-preview-modal">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title small text-muted" id="imagePreviewLabel">Preview Gambar Event</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
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
</style>
@endif
@if(!empty($event->latitude) && !empty($event->longitude))
<!-- Leaflet map for event location -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    try{
        var lat = {{ (float) $event->latitude }};
        var lng = {{ (float) $event->longitude }};
        var map = L.map('eventMap').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup(`{{ addslashes($event->title) }}`);
    }catch(e){ console.error(e); }
});
</script>
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

    input.addEventListener('input', function(){
        var raw = (this.value || '').trim();
        var q = raw.toLowerCase();
        Array.prototype.forEach.call(tbody.querySelectorAll('tr'), function(row){
            var name = (row.children[1]?.textContent || '').toLowerCase();
            var email = (row.children[2]?.textContent || '').toLowerCase();
            var code = (row.dataset.regCode || '').toLowerCase();
            var match = !q || name.includes(q) || email.includes(q) || code.includes(q);
            row.style.display = match ? '' : 'none';
        });
        // Update empty message to include the searched query when present
        var msgEl = document.getElementById('participantsEmptyMessage');
        var qEl = document.getElementById('participantsEmptyQuery');
        if(msgEl && qEl){
            if(raw){
                msgEl.textContent = 'Oopss, nama/email yang kamu cari tidak ada.';
                qEl.textContent = '"' + raw + '"';
            } else {
                msgEl.textContent = 'Oopss, data peserta tidak ada.';
                qEl.textContent = '';
            }
        }
        refreshCounts();
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
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    // Generic action modal
    var actionModalEl = document.getElementById('registrationActionModal');
    var actionModal = actionModalEl ? new bootstrap.Modal(actionModalEl) : null;
    var actionForm = document.getElementById('registrationActionForm');
    var actionMessage = document.getElementById('registrationActionMessage');
    var actionLabel = document.getElementById('registrationActionLabel');

    // Reject reason modal
    var rejectModalEl = document.getElementById('rejectRegistrationModal');
    var rejectModal = rejectModalEl ? new bootstrap.Modal(rejectModalEl) : null;
    var rejectForm = document.getElementById('rejectRegistrationForm');
    var rejectReasonHtml = document.getElementById('rejectionReason');

    document.querySelectorAll('.btn-action').forEach(function(btn){
        btn.addEventListener('click', function(){
            var variant = btn.getAttribute('data-variant'); // 'approve' or 'reject'
            var action = btn.getAttribute('data-action');

            if(variant === 'reject') {
                // Open rejection modal
                if(rejectForm) rejectForm.setAttribute('action', action);
                if(rejectReasonHtml) rejectReasonHtml.value = ''; // clear previous text
                if(rejectModal) rejectModal.show();
            } else {
                // Open standard confirmation modal
                var title = btn.getAttribute('data-title') || 'Konfirmasi';
                var message = btn.getAttribute('data-message') || 'Lanjutkan tindakan ini?';
                
                if(actionForm) actionForm.setAttribute('action', action);
                if(actionLabel) actionLabel.textContent = title;
                if(actionMessage) actionMessage.textContent = message;
                if(actionModal) actionModal.show();
            }
        });
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
                        <h5 class="modal-title mb-0" id="deleteEventLabel">Hapus Event</h5>
                        <small class="text-muted">Tindakan ini tidak dapat dibatalkan</small>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Anda akan menghapus event:</p>
                <div class="p-2 rounded border bg-light"><i class="bi bi-calendar-event me-1"></i> <strong>{{ $event->title }}</strong></div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" value="1" id="deleteConfirmCheckboxShow">
                    <label class="form-check-label" for="deleteConfirmCheckboxShow">Saya paham bahwa penghapusan bersifat permanen.</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger confirm-danger-btn" id="deleteConfirmBtnShow" form="deleteEventFormShow" disabled>
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
    <!-- Approve/Reject confirmation modal (reusable) -->
    <div class="modal fade" id="registrationActionModal" tabindex="-1" aria-labelledby="registrationActionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registrationActionLabel">Confirm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="registrationActionMessage">Are you sure?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="registrationActionForm" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary" id="registrationActionConfirmBtn">Konfirmasi</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div class="modal fade" id="rejectRegistrationModal" tabindex="-1" aria-labelledby="rejectRegistrationLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectRegistrationLabel">Tolak Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="rejectRegistrationForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Apakah Anda yakin ingin menolak pendaftaran ini?</p>
                        <div class="mb-3">
                            <label for="rejectionReason" class="form-label text-danger">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="rejectionReason" name="reason" rows="3" required placeholder="Contoh: Bukti transfer tidak terbaca"></textarea>
                            <div class="form-text">Alasan ini akan dikirimkan kepada pendaftar melalui notifikasi.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak Pendaftaran</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection