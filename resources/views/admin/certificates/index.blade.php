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

<!-- Tab Navigation -->
<ul class="nav nav-tabs nav-tabs-custom mb-4 border-bottom-0" id="certificateTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $tab === 'events' ? 'active' : '' }} fw-bold px-4 py-3" id="events-tab" data-bs-toggle="tab" data-bs-target="#events-content" type="button" role="tab" aria-controls="events-content" aria-selected="{{ $tab === 'events' ? 'true' : 'false' }}">
            <i class="bi bi-calendar-event me-2"></i>Sertifikat Event
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link {{ $tab === 'courses' ? 'active' : '' }} fw-bold px-4 py-3" id="courses-tab" data-bs-toggle="tab" data-bs-target="#courses-content" type="button" role="tab" aria-controls="courses-content" aria-selected="{{ $tab === 'courses' ? 'true' : 'false' }}">
            <i class="bi bi-mortarboard me-2"></i>Sertifikat Kursus
        </button>
    </li>
</ul>

<div class="tab-content" id="certificateTabsContent">
    <!-- Events Tab Pane -->
    <div class="tab-pane fade {{ $tab === 'events' ? 'show active' : '' }}" id="events-content" role="tabpanel" aria-labelledby="events-tab">
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
                                $registrationsCount = $event->registrations_count;
                                $hasLogo = !empty($event->certificate_logo);
                                $hasSignature = !empty($event->certificate_signature);
                                $isConfigured = $hasLogo || $hasSignature;
                                $eventDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
                                $isFinished = false;
                                if($eventDate) {
                                    $certificateReadyDate = $eventDate->copy()->addDays(3);
                                    $isFinished = $certificateReadyDate->isPast();
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
                            <tr><td colspan="5" class="text-center py-5"><p class="text-muted mb-0">Data event belum tersedia</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Courses Tab Pane -->
    <div class="tab-pane fade {{ $tab === 'courses' ? 'show active' : '' }}" id="courses-content" role="tabpanel" aria-labelledby="courses-tab">
        <div class="card-minimal overflow-hidden mb-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr>
                            <th class="ps-4">Nama Kursus</th>
                            <th class="text-center">Siswa</th>
                            <th class="text-center">Lulus (Dapat Sertif)</th>
                            <th>Konfigurasi Aset</th>
                            <th class="text-end pe-4">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            @php
                                $enrollmentsCount = $course->enrollments_count;
                                $completedCount = $course->enrollments()->where('status', 'completed')->count();
                                $hasLogo = !empty($course->certificate_logo);
                                $hasSignature = !empty($course->certificate_signature);
                                $isConfigured = $hasLogo || $hasSignature;
                            @endphp
                            <tr class="course-row" data-title="{{ strtolower($course->name) }}">
                                <td class="ps-4">
                                    <div class="fw-bold text-navy small">{{ $course->name }}</div>
                                    <div class="text-muted smaller" style="font-size: 0.7rem;">{{ $course->category->name ?? 'General' }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="small fw-semibold text-muted">{{ $enrollmentsCount }} Siswa</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1" style="font-size: 0.65rem;">{{ $completedCount }} Selesai</span>
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
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if($completedCount > 0)
                                            <a href="{{ route('admin.crm.certificates.generate-massal-course', $course) }}" class="btn btn-sm px-3" style="background: var(--crm-primary); color: white;" onclick="return handleGenerateClick(this, {{ $completedCount }})">
                                                Unduh Semua (ZIP)
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.crm.certificates.edit-course', $course) }}" class="btn btn-sm rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border: 1px solid var(--crm-primary); color: var(--crm-primary);" title="Konfigurasi">
                                            <i class="bi bi-gear-fill" style="font-size: 0.8rem;"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-5"><p class="text-muted mb-0">Data kursus belum tersedia</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
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
    
    function filterRows() {
        const searchTerm = (searchInput.value || '').toLowerCase().trim();
        const certStatus = certStatusFilter.value;
        
        // Filter Events
        document.querySelectorAll('.event-row').forEach(row => {
            const title = (row.getAttribute('data-title') || '').toLowerCase();
            const rowCertStatus = row.getAttribute('data-status');
            const matchSearch = searchTerm === '' || title.includes(searchTerm);
            const matchCertStatus = certStatus === 'all' || rowCertStatus === certStatus;
            row.style.display = (matchSearch && matchCertStatus) ? '' : 'none';
        });

        // Filter Courses
        document.querySelectorAll('.course-row').forEach(row => {
            const title = (row.getAttribute('data-title') || '').toLowerCase();
            const matchSearch = searchTerm === '' || title.includes(searchTerm);
            // Courses don't have H+3 logic in this filter yet, but could be added if needed
            row.style.display = matchSearch ? '' : 'none';
        });
    }

    searchInput?.addEventListener('input', filterRows);
    certStatusFilter?.addEventListener('change', filterRows);
    clearBtn?.addEventListener('click', function() {
        searchInput.value = '';
        certStatusFilter.value = 'all';
        filterRows();
    });

    // Handle Tab Persistence
    const triggerTabList = [].slice.call(document.querySelectorAll('#certificateTabs button'))
    triggerTabList.forEach(function (triggerEl) {
        triggerEl.addEventListener('click', function (event) {
            const tabName = event.target.id.replace('-tab', '');
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        })
    })
});

function handleGenerateClick(btn, count) {
    if(!confirm(`Terbitkan sertifikat massal untuk ${count} peserta?`)) return false;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    setTimeout(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    }, 15000);
    return true;
}
</script>
@endsection