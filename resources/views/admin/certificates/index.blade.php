@extends('layouts.crm')

@section('title', 'Generate Sertifikat')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
        <h4 class="mb-0 text-dark fw-semibold"><i class="bi bi-award me-2"></i>Generate Sertifikat</h4>
            <p class="text-muted small mb-0">Generate dan kelola sertifikat untuk semua event. Fokus pada pengaturan logo, tanda tangan, dan generate sertifikat.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small text-muted fw-semibold">Cari Event</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="eventSearch" class="form-control" placeholder="Cari nama event...">
                    </div>
                </div>
                <div class="col-md-5">
                    <label class="form-label small text-muted fw-semibold">Status Sertifikat</label>
                    <select id="certificateStatusFilter" class="form-select">
                        <option value="all">Semua Status</option>
                        <option value="ready">Siap Generate</option>
                        <option value="configured">Sudah Dikonfigurasi</option>
                        <option value="not-configured">Belum Dikonfigurasi</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-semibold">&nbsp;</label>
                    <button type="button" id="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-x-circle me-1"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Events List -->
    <div class="card shadow-sm">
        <div class="card-body">
            @if($events->count() > 0)
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Event</th>
                                <th>Jumlah Peserta</th>
                                <th>Status Konfigurasi</th>
                                <th>Status Generate</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="eventsTableBody">
                            @foreach($events as $event)
                                @php
                                    $registrationsCount = $event->registrations_count ?? $event->registrations()->count();
                                    $hasLogo = !empty($event->certificate_logo);
                                    $hasSignature = !empty($event->certificate_signature);
                                    $isConfigured = $hasLogo || $hasSignature;
                                    $eventDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
                                    $isFinished = false;
                                    $eventStatus = 'unknown';
                                    if($eventDate) {
                                        $certificateReadyDate = $eventDate->copy()->addDays(3);
                                        $isFinished = $certificateReadyDate->isPast();
                                        if($eventDate->isPast()) {
                                            $eventStatus = 'finished';
                                        } elseif($eventDate->isFuture()) {
                                            $eventStatus = 'upcoming';
                                        } else {
                                            $eventStatus = 'ongoing';
                                        }
                                    }
                                    $status = 'not-configured';
                                    if($isConfigured) {
                                        $status = $isFinished ? 'ready' : 'configured';
                                    }
                                @endphp
                                <tr class="event-row" 
                                    data-title="{{ strtolower($event->title) }}"
                                    data-status="{{ $status }}"
                                    data-event-status="{{ $eventStatus }}">
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $event->title }}</div>
                                    </td>
                                    <td>
                                        @if($registrationsCount > 0)
                                            <span class="badge bg-primary">{{ $registrationsCount }} peserta</span>
                                        @else
                                            <span class="badge bg-secondary">0 peserta</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isConfigured)
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                @if($hasLogo && $hasSignature)
                                                    Lengkap
                                                @elseif($hasLogo)
                                                    Logo Saja
                                                @else
                                                    TTD Saja
                                                @endif
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($registrationsCount > 0)
                                            @if($isFinished)
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Siap Generate
                                                </span>
                                            @else
                                                <span class="badge bg-info">
                                                    <i class="bi bi-clock me-1"></i>Menunggu H+3
                                                </span>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-dash-circle me-1"></i>Tidak Ada Peserta
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            @if($registrationsCount > 0 && $isFinished)
                                                <a href="{{ route('admin.crm.certificates.generate-massal', $event) }}" 
                                                   class="btn btn-success generate-cert-btn" 
                                                   data-bs-toggle="tooltip" 
                                                   title="Generate Sertifikat ({{ $registrationsCount }} peserta)"
                                                   data-count="{{ $registrationsCount }}"
                                                   onclick="return handleGenerateClick(this, {{ $registrationsCount }})">
                                                    <i class="bi bi-download me-1"></i> Generate
                                                </a>
                                            @elseif($registrationsCount > 0 && !$isFinished)
                                                <span class="btn btn-outline-secondary btn-sm" 
                                                      data-bs-toggle="tooltip" 
                                                      title="Sertifikat dapat di-generate setelah H+3 dari tanggal event">
                                                    <i class="bi bi-clock me-1"></i> Belum Waktunya
                                                </span>
                                            @else
                                                <span class="btn btn-outline-secondary btn-sm" 
                                                      data-bs-toggle="tooltip" 
                                                      title="Belum ada peserta yang terdaftar">
                                                    <i class="bi bi-info-circle me-1"></i> Tidak Ada Peserta
                                                </span>
                                            @endif
                                            <a href="{{ route('admin.crm.certificates.edit', $event) }}" 
                                               class="btn btn-outline-primary"
                                               data-bs-toggle="tooltip"
                                               title="Pengaturan Logo & Tanda Tangan Sertifikat">
                                                <i class="bi bi-gear me-1"></i> Pengaturan
                                            </a>
                                        </div>
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
                    <i class="bi bi-award" style="font-size: 3rem; color: #ccc;"></i>
                    <p class="text-muted mt-3">Belum ada event untuk dikelola sertifikatnya.</p>
                    <a href="{{ route('admin.add-event') }}" class="btn btn-primary mt-2">
                        <i class="bi bi-plus-circle me-1"></i> Buat Event Baru
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Info Box -->
    <div class="card shadow-sm mt-4 border-info">
        <div class="card-body">
            <h6 class="text-info mb-3"><i class="bi bi-info-circle me-2"></i>Informasi Penting</h6>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-gear-fill text-primary me-2 mt-1"></i>
                        <div>
                            <strong>Pengaturan Logo & TTD</strong>
                            <p class="small text-muted mb-0">Upload logo dan tanda tangan di halaman Edit Event untuk membuat sertifikat lebih profesional.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-clock-fill text-warning me-2 mt-1"></i>
                        <div>
                            <strong>Waktu Generate</strong>
                            <p class="small text-muted mb-0">Sertifikat dapat di-generate setelah H+3 (3 hari) dari tanggal event berlangsung.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start">
                        <i class="bi bi-download-fill text-success me-2 mt-1"></i>
                        <div>
                            <strong>Generate Massal</strong>
                            <p class="small text-muted mb-0">Generate semua sertifikat sekaligus dalam format ZIP untuk kemudahan distribusi.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('eventSearch');
    const certStatusFilter = document.getElementById('certificateStatusFilter');
    const clearBtn = document.getElementById('clearFilters');
    const rows = Array.from(document.querySelectorAll('.event-row'));

    function filterRows() {
        const searchTerm = (searchInput.value || '').toLowerCase().trim();
        const certStatus = certStatusFilter.value;

        rows.forEach(row => {
            const title = (row.getAttribute('data-title') || '').toLowerCase();
            const rowCertStatus = row.getAttribute('data-status');

            const matchSearch = searchTerm === '' || title.includes(searchTerm);
            const matchCertStatus = certStatus === 'all' || rowCertStatus === certStatus;

            row.style.display = (matchSearch && matchCertStatus) ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterRows);
    certStatusFilter?.addEventListener('change', filterRows);
    clearBtn?.addEventListener('click', function() {
        searchInput.value = '';
        certStatusFilter.value = 'all';
        filterRows();
    });

    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Handle generate certificate click with loading state
function handleGenerateClick(btn, count) {
    if(!confirm(`Generate sertifikat untuk ${count} peserta? Proses ini mungkin memakan waktu beberapa saat.`)) {
        return false;
    }
    
    // Show loading state
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Generating...';
    btn.classList.add('disabled');
    
    // If user navigates away, restore button (though this won't execute if navigation happens)
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        btn.classList.remove('disabled');
    }, 30000); // Restore after 30 seconds if still on page
    
    return true;
}
</script>
<style>
.generate-cert-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
</style>
@endsection