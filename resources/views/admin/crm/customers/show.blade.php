@extends('layouts.crm')

@section('title', 'Detail Customer')

@section('styles')

<style>
    .profile-header-card {
        background: linear-gradient(135deg, var(--crm-primary), var(--crm-primary-dark));
        color: white;
        border-radius: 24px;
        overflow: hidden;
        border: none;
        position: relative;
    }
    .profile-header-card::before {
        content: '';
        position: absolute;
        top: -50px;
        right: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }
    .profile-avatar-big {
        width: 120px;
        height: 120px;
        border-radius: 20px;
        border: 4px solid rgba(255, 255, 255, 0.2);
        object-fit: cover;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    }
    .info-label {
        color: var(--crm-text-muted);
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 0.25rem;
    }
    .info-value {
        color: var(--crm-navy);
        font-weight: 600;
        font-size: 0.95rem;
    }
    .icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--crm-accent-light);
        color: var(--crm-primary);
        font-size: 1.1rem;
    }
    .timeline-item {
        position: relative;
        padding-left: 30px;
        padding-bottom: 25px;
        border-left: 2px solid var(--crm-border);
    }
    .timeline-item:last-child {
        border-left: 2px solid transparent;
        padding-bottom: 0;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -7px;
        top: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--crm-primary);
        border: 2px solid white;
        box-shadow: 0 0 0 3px rgba(109, 40, 217, 0.1);
    }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1">
                <li class="breadcrumb-item"><a href="{{ route('admin.crm.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.crm.customers.index') }}" class="text-decoration-none">Customers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail</li>
            </ol>
        </nav>
        <h3 class="fw-800 text-navy mb-0">Manajemen Profil Akun</h3>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-white shadow-sm border-0 px-3 fw-600 rounded-3">
            <i class="bi bi-arrow-left me-2"></i> Kembali
        </a>
        <a href="{{ route('admin.crm.customers.edit', $customer) }}" class="btn btn-primary shadow-lg border-0 px-3 fw-600 rounded-3">
            <i class="bi bi-pencil-square me-2"></i> Edit Profil
        </a>
    </div>
</div>

<div class="row g-4 mb-5">
    <div class="col-12">
        <div class="card profile-header-card border-0">
            <div class="card-body p-5">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <img src="{{ $customer->avatar_url }}" class="profile-avatar-big" alt="avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=fff&color=6d28d9&size=128'">
                    </div>
                    <div class="col ms-3">
                        <div class="d-flex align-items-center gap-3 mb-2">
                            <h2 class="fw-800 mb-0">{{ $customer->name }}</h2>
                            @if($customer->role === 'reseller')
                                <span class="badge bg-warning text-dark fw-700 px-3 rounded-pill shadow-sm">RESELLER</span>
                            @elseif($customer->role === 'trainer')
                                <span class="badge bg-info text-dark fw-700 px-3 rounded-pill shadow-sm">TRAINER</span>
                            @else
                                <span class="badge bg-white text-primary fw-700 px-3 rounded-pill shadow-sm">CUSTOMER</span>
                            @endif
                        </div>
                        <p class="mb-0 opacity-75 fs-5 fw-500"><i class="bi bi-envelope me-2"></i>{{ $customer->email }}</p>
                        <div class="d-flex gap-4 mt-4">
                            <div class="text-center">
                                <div class="fw-800 fs-4">{{ $registrations->count() }}</div>
                                <div class="smaller opacity-75 fw-600">Events</div>
                            </div>
                            <div class="vr opacity-25"></div>
                            <div class="text-center">
                                <div class="fw-800 fs-4">{{ $enrollments->count() }}</div>
                                <div class="smaller opacity-75 fw-600">Courses</div>
                            </div>
                            <div class="vr opacity-25"></div>
                            <div class="text-center">
                                <div class="fw-800 fs-4">{{ $customer->created_at->diffForHumans() }}</div>
                                <div class="smaller opacity-75 fw-600">Member Since</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Contact & Personal Info -->
    <div class="col-lg-4">
        <div class="card-minimal border-0 shadow-sm p-4 mb-4">
            <h5 class="fw-800 text-navy mb-4">Kontak & Informasi</h5>
            
            <div class="d-flex align-items-center mb-4">
                <div class="icon-box me-3">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div>
                    <span class="info-label">Nomor Telepon</span>
                    <span class="info-value">{{ $customer->phone ?? 'Belum ditambahkan' }}</span>
                </div>
            </div>

            <div class="d-flex align-items-center mb-4 text-truncate">
                <div class="icon-box me-3">
                    <i class="bi bi-globe"></i>
                </div>
                <div>
                    <span class="info-label">Website / Portfolio</span>
                    @if($customer->website)
                        <a href="{{ $customer->website }}" target="_blank" class="info-value text-primary text-decoration-none">
                            {{ Str::limit(str_replace(['http://', 'https://'], '', $customer->website), 25) }} <i class="bi bi-box-arrow-up-right smaller ms-1"></i>
                        </a>
                    @else
                        <span class="info-value text-muted">Tidak tersedia</span>
                    @endif
                </div>
            </div>

            <div class="d-flex align-items-center mb-4">
                <div class="icon-box me-3">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div>
                    <span class="info-label">Terdaftar Pada</span>
                    <span class="info-value">{{ $customer->created_at->format('d F Y') }}</span>
                </div>
            </div>

            <hr class="my-4 opacity-50">

            <h6 class="fw-700 text-navy mb-2">Biografi Singkat</h6>
            <p class="text-muted small lh-lg">
                {{ $customer->bio ?? 'Customer ini belum menuliskan biografi mereka. Informasi profil yang lengkap memudahkan interaksi dalam komunitas.' }}
            </p>
        </div>

        <!-- Quick Summary Stats -->
        <div class="card-minimal border-0 shadow-sm p-4 text-center" style="background: var(--crm-accent-light);">
            <i class="bi bi-shield-check text-primary display-6 mb-3"></i>
            <h6 class="fw-800 text-navy">Keamanan Akun</h6>
            <p class="smaller text-muted mb-4 px-2">Akun ini memiliki status validasi terpusat dari core system IDSPora.</p>
            <div class="d-grid">
                <button class="btn btn-outline-danger btn-sm rounded-pill fw-700">Audit Log Transaksi</button>
            </div>
        </div>
    </div>

    <!-- Activities & Programs -->
    <div class="col-lg-8">
        <!-- Activity List -->
        <div class="card-minimal border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-800 text-navy mb-0">Jejak Aktivitas Program</h5>
                <ul class="nav nav-pills nav-pills-custom" id="activityTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-700 smaller" id="events-tab" data-bs-toggle="pill" data-bs-target="#events" type="button" role="tab">Event Terdaftar</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-700 smaller" id="courses-tab" data-bs-toggle="pill" data-bs-target="#courses" type="button" role="tab">Materi Kursus</button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-4">
                <div class="tab-content" id="activityTabContent">
                    <!-- Events Tab -->
                    <div class="tab-pane fade show active" id="events" role="tabpanel">
                        @if($registrations->count() > 0)
                            @foreach($registrations as $registration)
                                <div class="timeline-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-800 text-navy">{{ $registration->event->title }}</div>
                                            <div class="text-muted smaller mb-2">
                                                <i class="bi bi-calendar-event me-1"></i> {{ $registration->event->event_date ? \Carbon\Carbon::parse($registration->event->event_date)->format('d M Y') : 'Online/Flexible' }}
                                            </div>
                                            <span class="badge {{ $registration->status === 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-light text-muted' }} fw-700 rounded-pill px-3" style="font-size: 0.65rem;">
                                                STATUS: {{ strtoupper($registration->status) }}
                                            </span>
                                        </div>
                                        <div class="text-end">
                                            <div class="smaller text-muted fw-600">Registrasi pada:</div>
                                            <div class="small fw-700 text-navy">{{ $registration->created_at->format('d M Y') }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/2648/2648554.png" style="width: 80px; opacity: 0.2;" alt="empty">
                                <p class="text-muted mt-3 fw-600">Belum ada history pendaftaran event.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Courses Tab -->
                    <div class="tab-pane fade" id="courses" role="tabpanel">
                        @if($enrollments->count() > 0)
                            <div class="row g-3">
                                @foreach($enrollments as $enrollment)
                                    <div class="col-md-6">
                                        <div class="p-3 bg-light rounded-4 border h-100">
                                            <div class="d-flex align-items-center mb-3">
                                                <div class="bg-white p-2 rounded-3 shadow-sm me-3">
                                                    <i class="bi bi-journal-check text-primary fs-5"></i>
                                                </div>
                                                <div class="fw-800 text-navy fs-6">{{ $enrollment->course->name ?? 'N/A' }}</div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <span class="badge {{ $enrollment->status === 'active' ? 'bg-primary' : ($enrollment->status === 'completed' ? 'bg-success' : 'bg-secondary') }} rounded-pill px-2" style="font-size: 0.6rem;">
                                                    {{ strtoupper($enrollment->status) }}
                                                </span>
                                                <small class="text-muted smaller">Enrolled: {{ $enrollment->created_at->format('d M y') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-journal-x display-1 text-muted opacity-10"></i>
                                <p class="text-muted mt-3 fw-600">Customer belum mengambil kursus apapun.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Custom Support Ticket Section for this specific user -->
        <div class="card-minimal border-0 shadow-sm p-4" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: white;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-800 mb-0">Tiket Bantuan Aktif</h5>
                <span class="badge bg-primary px-3 rounded-pill fw-700 shadow-sm">{{ $activeTickets->count() }} TIKET</span>
            </div>
            
            @forelse($activeTickets as $ticket)
                <div class="p-3 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-10 mb-3 hover-lift transition-all cursor-pointer" data-bs-toggle="modal" data-bs-target="#ticketModal{{ $ticket->id }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge {{ $ticket->status === 'new' ? 'bg-primary' : 'bg-warning' }} rounded-pill smaller fw-700 shadow-sm">
                            {{ strtoupper($ticket->status === 'new' ? 'BARU' : 'DIPROSES') }}
                        </span>
                        <small class="text-white opacity-75 fw-600">{{ $ticket->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="fw-800 small text-white">{{ $ticket->subject }}</div>
                    <div class="smaller text-white opacity-100 fw-400 mt-2 lh-sm">{{ Str::limit($ticket->message, 100) }}</div>
                    <div class="text-primary smaller fw-700 mt-2 d-inline-block bg-white px-2 py-1 rounded-pill" style="font-size: 0.65rem;">KLIK UNTUK DETAIL</div>
                </div>
            @empty
                <div class="p-4 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-10 text-center py-5">
                    <i class="bi bi-chat-left-dots-fill opacity-25 display-4 d-block mb-3"></i>
                    <p class="mb-0 opacity-75 small">Tidak ada laporan kendala atau tiket bantuan yang aktif untuk akun ini.</p>
                </div>
            @endforelse
            
            @if($activeTickets->count() > 0)
                <div class="text-center mt-3">
                    <a href="{{ route('admin.crm.support.index', ['email' => $customer->email]) }}" class="btn btn-link btn-sm text-white text-decoration-none opacity-50 hover-opacity-100 fw-600">
                        Lihat Semua Support History <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Support Ticket Modals relocation for stability -->
@foreach($activeTickets as $ticket)
<div class="modal fade" id="ticketModal{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; color: var(--crm-navy);">
            <div class="modal-header bg-navy text-white border-0 p-4" style="border-radius: 24px 24px 0 0;">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 p-2 rounded-3 me-3">
                        <i class="bi bi-chat-dots-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-800 mb-0">Detail Tiket Bantuan</h5>
                        <span class="smaller opacity-75">ID Tiket: #SPT-{{ $ticket->id }}</span>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="mb-4 bg-white p-3 rounded-4 shadow-sm">
                    <label class="text-muted smaller fw-800 text-uppercase mb-1 d-block opacity-50">Subjek</label>
                    <div class="fw-800 text-navy fs-5">{{ $ticket->subject }}</div>
                    <div class="smaller text-muted mt-1"><i class="bi bi-clock me-1"></i> {{ $ticket->created_at->translatedFormat('d F Y, H:i') }}</div>
                </div>
                <div class="mb-4 bg-white p-3 rounded-4 shadow-sm text-dark">
                    <label class="text-muted smaller fw-800 text-uppercase mb-2 d-block opacity-50">Isi Pesan</label>
                    <div class="smaller lh-lg" style="white-space: pre-wrap;">{{ $ticket->message }}</div>
                </div>
                @if($ticket->attachment)
                <div class="bg-white p-3 rounded-4 shadow-sm">
                    <label class="text-muted smaller fw-800 text-uppercase mb-2 d-block opacity-50">Lampiran</label>
                    <a href="{{ asset('uploads/' . $ticket->attachment) }}" target="_blank" class="d-block mt-2 overflow-hidden rounded-3 border">
                        <img src="{{ asset('uploads/' . $ticket->attachment) }}" class="img-fluid w-100 hover-zoom">
                    </a>
                </div>
                @endif
            </div>
            <div class="modal-footer border-0 p-4 bg-white">
                <div class="w-100">
                    <div class="d-flex flex-wrap gap-2 justify-content-center">
                        @if($ticket->status !== 'processed' && $ticket->status !== 'resolved')
                        <form action="{{ route('admin.crm.support.updateStatus', $ticket) }}" method="POST" class="m-0 flex-grow-1">
                            @csrf
                            <input type="hidden" name="status" value="processed">
                            <button type="submit" class="btn btn-warning text-white w-100 rounded-pill fw-700 smaller shadow-sm py-2">
                                <i class="bi bi-arrow-repeat me-1"></i> Proses
                            </button>
                        </form>
                        @endif

                        @if($ticket->status !== 'resolved')
                        <form action="{{ route('admin.crm.support.updateStatus', $ticket) }}" method="POST" class="m-0 flex-grow-1">
                            @csrf
                            <input type="hidden" name="status" value="resolved">
                            <button type="submit" class="btn btn-success w-100 rounded-pill fw-700 smaller shadow-sm py-2">
                                <i class="bi bi-check-circle me-1"></i> Selesaikan
                            </button>
                        </form>
                        @endif

                        <button type="button" class="btn btn-navy w-100 rounded-pill fw-700 py-2 shadow-sm mt-2" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection



