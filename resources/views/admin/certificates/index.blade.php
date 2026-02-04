@extends('layouts.crm')

@section('title', 'Sertifikat & Penghargaan')

@section('styles')
<style>
    .status-indicator {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
    }
    .info-card {
        background: #f8fafc;
        border: 1px dashed var(--crm-border);
        border-radius: 12px;
        padding: 1.25rem;
    }
    .table-custom thead th {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: var(--crm-text-muted);
        letter-spacing: 0.5px;
        padding: 1rem;
        background: #f8fafc;
        border-bottom: 1px solid var(--crm-border);
    }
</style>
@endsection

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h3 class="fw-bold text-navy mb-1">Sertifikat & Penghargaan</h3>
        <p class="text-muted small mb-0">Kelola distribusi sertifikat digital untuk peserta event</p>
    </div>
    <div class="col-auto">
        <button class="btn btn-outline-secondary btn-sm bg-white" data-bs-toggle="modal" data-bs-target="#helpModal">
            <i class="bi bi-question-circle me-1"></i> Bantuan
        </button>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success border-0 shadow-sm alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Filter Section -->
<div class="card-minimal mb-4">
    <div class="card-body p-4 bg-light bg-opacity-10">
        <div class="row g-3">
            <div class="col-md-5">
                <label class="form-label smaller fw-bold text-muted">Cari Nama Program</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="eventSearch" class="form-control border-start-0 ps-0" placeholder="Ketik judul event...">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label smaller fw-bold text-muted">Status Kesiapan</label>
                <select id="certificateStatusFilter" class="form-select form-select-sm">
                    <option value="all">Semua Status</option>
                    <option value="ready">Siap Generate (H+3)</option>
                    <option value="configured">Dikonfigurasi</option>
                    <option value="not-configured">Belum Ada Aset</option>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="button" id="clearFilters" class="btn btn-outline-secondary btn-sm w-100">Reset Filter</button>
            </div>
        </div>
    </div>
</div>

<!-- Events List -->
<div class="card-minimal overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light bg-opacity-50">
                <tr>
                    <th class="ps-4">Nama Program / Event</th>
                    <th class="text-center">Peserta</th>
                    <th>Konfigurasi Aset</th>
                    <th>Akses Sertifikat</th>
                    <th class="text-end pe-4">Tindakan</th>
                </tr>
            </thead>
            <tbody id="eventsTableBody">
                @forelse($events as $event)
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
                            if($eventDate->isPast()) { $eventStatus = 'finished'; } 
                            elseif($eventDate->isFuture()) { $eventStatus = 'upcoming'; } 
                            else { $eventStatus = 'ongoing'; }
                        }
                        $status = 'not-configured';
                        if($isConfigured) { $status = $isFinished ? 'ready' : 'configured'; }
                    @endphp
                    <tr class="event-row" data-title="{{ strtolower($event->title) }}" data-status="{{ $status }}">
                        <td class="ps-4">
                            <div class="fw-bold text-navy small">{{ $event->title }}</div>
                            <div class="text-muted smaller" style="font-size: 0.7rem;">{{ $event->event_date ? $eventDate->format('d M Y') : 'Tanpa Tanggal' }}</div>
                        </td>
                        <td class="text-center">
                            <span class="small fw-semibold text-muted">{{ $registrationsCount }} Jiwa</span>
                        </td>
                        <td>
                            @if($isConfigured)
                                <div class="d-flex align-items-center">
                                    <span class="status-indicator" style="background: var(--crm-primary);"></span>
                                    <span class="smaller fw-medium" style="font-size: 0.75rem; color: var(--crm-primary);">ASET LENGKAP</span>
                                </div>
                            @else
                                <div class="d-flex align-items-center">
                                    <span class="status-indicator" style="background: var(--crm-secondary);"></span>
                                    <span class="smaller fw-medium" style="font-size: 0.75rem; color: var(--crm-secondary);">ASET KOSONG</span>
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($registrationsCount > 0)
                                @if($isFinished)
                                    <span class="badge rounded-pill px-2 py-1" style="font-size: 0.65rem; background: var(--crm-accent-light); color: var(--crm-primary); border: 1px solid rgba(109, 40, 217, 0.2);">SIAP DITERBITKAN</span>
                                @else
                                    <span class="badge bg-light text-muted border border-secondary border-opacity-10 rounded-pill px-2 py-1" style="font-size: 0.65rem;">MENUNGGU H+3</span>
                                @endif
                            @else
                                <span class="text-muted smaller">---</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                @if($registrationsCount > 0 && $isFinished)
                                    <a href="{{ route('admin.crm.certificates.generate-massal', $event) }}" class="btn btn-sm px-3" style="background: var(--crm-primary); color: white;" onclick="return handleGenerateClick(this, {{ $registrationsCount }})">
                                        Unduh Semua (ZIP)
                                    </a>
                                @endif
                                <a href="{{ route('admin.crm.certificates.edit', $event) }}" class="btn btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border: 1px solid var(--crm-primary); color: var(--crm-primary);" title="Konfigurasi">
                                    <i class="bi bi-gear-fill" style="font-size: 0.8rem;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <p class="text-muted mb-0">Data event belum tersedia</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="info-card h-100" style="border-left: 4px solid var(--crm-primary);">
            <h6 class="fw-bold text-navy mb-3">Ketentuan Penerbitan</h6>
            <ul class="smaller text-muted ps-3 mb-0">
                <li class="mb-2">Sertifikat hanya dapat diterbitkan <b>3 hari setelah</b> tanggal pelaksanaan event berakhir untuk memastikan sinkronisasi absensi.</li>
                <li class="mb-2">Pastikan <b>Logo Partner</b> dan <b>Tanda Tangan Authorized</b> telah diupload pada menu pengaturan masing-masing event.</li>
                <li>Gunakan fitur "Generate" untuk memproses sertifikat secara massal dalam format arsip terkompresi.</li>
            </ul>
        </div>
    </div>
    <div class="col-md-6">
        <div class="info-card h-100" style="border-left: 4px solid var(--crm-secondary);">
            <h6 class="fw-bold text-navy mb-3">Bantuan Cepat</h6>
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 bg-white rounded border me-3"><i class="bi bi-file-earmark-pdf text-danger fs-4"></i></div>
                <div>
                    <div class="small fw-bold">Manual Book Sertifikat</div>
                    <a href="#" class="smaller text-primary text-decoration-none">Unduh Panduan (PDF)</a>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <div class="p-2 bg-white rounded border me-3"><i class="bi bi-envelope text-info fs-4"></i></div>
                <div>
                    <div class="small fw-bold">Hubungi Support IT</div>
                    <a href="mailto:support@idspora.com" class="smaller text-primary text-decoration-none">Kirim Email Bantuan</a>
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
});

function handleGenerateClick(btn, count) {
    if(!confirm(`Terbitkan sertifikat massal untuk ${count} peserta?`)) return false;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }, 10000);
    return true;
}
</script>
@endsection