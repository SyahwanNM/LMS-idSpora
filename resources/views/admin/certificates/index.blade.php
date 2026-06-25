@extends('layouts.crm')

@section('title', 'Sertifikat & Penghargaan')

@section('styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--crm-primary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before { content: ''; display: inline-block; width: 16px; height: 2px; background: var(--crm-primary); border-radius: 2px; }
    
    /* Custom Tab Switcher (No Bootstrap JS Dependency) */
    .crm-tab-switcher { 
        display: inline-flex; 
        background: var(--crm-border-soft); 
        padding: 4px; 
        border-radius: 12px;
        gap: 4px;
        list-style: none;
        margin: 0;
    }
    .crm-tab-switcher button {
        font-size: 0.78rem; 
        font-weight: 700; 
        padding: 8px 18px;
        border-radius: 9px; 
        border: none;
        color: var(--crm-text-muted);
        background: transparent;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
    }
    .crm-tab-switcher button.active {
        background-color: #fff;
        color: var(--crm-primary);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .status-pill {
        font-size: 0.65rem; font-weight: 700; padding: 3px 9px;
        border-radius: 100px; display: inline-flex; align-items: center; gap: 4px;
    }
    .status-pill.ready { background: rgba(16,185,129,0.1); color: #059669; }
    .status-pill.configured { background: rgba(59,130,246,0.1); color: #2563eb; }
    .status-pill.missing { background: var(--crm-border-soft); color: var(--crm-text-muted); }

    .filter-input {
        border: 1px solid var(--crm-border); border-radius: 10px;
        padding: 0.45rem 1rem; font-size: 0.85rem; color: var(--crm-navy);
        background: var(--crm-border-soft); outline: none; width: 100%;
        transition: all 0.2s; height: 44px;
    }
    .filter-input:focus { border-color: var(--crm-primary-light); box-shadow: 0 0 0 3px rgba(124,58,237,0.1); background: #fff; }

    /* Custom Pane Visibility */
    .custom-tab-pane {
        display: none;
        animation: fadeIn 0.3s ease;
    }
    .custom-tab-pane.active {
        display: block;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <div class="page-eyebrow">Recognition System</div>
        <h1 style="font-size:1.75rem;font-weight:800;color:var(--crm-navy);letter-spacing:-0.8px;margin:0;">Sertifikat & Penghargaan</h1>
        <p style="font-size:0.85rem;color:var(--crm-text-subtle);margin:5px 0 0;">Kelola aset sertifikat untuk event dan kursus yang sudah dibuat.</p>
    </div>
    <div class="mt-3 mt-md-0">
        <ul class="crm-tab-switcher">
            <li>
                <button type="button" class="custom-tab-btn {{ $tab === 'events' ? 'active' : '' }}" data-target="events-pane">
                    <i class="bi bi-calendar-event me-2"></i>Sertifikat Event
                    <span class="badge rounded-pill bg-light text-muted border ms-2" style="font-size: 0.65rem;">{{ $events->count() }}</span>
                </button>
            </li>
            <li>
                <button type="button" class="custom-tab-btn {{ $tab === 'courses' ? 'active' : '' }}" data-target="courses-pane">
                    <i class="bi bi-mortarboard me-2"></i>Sertifikat Kursus
                    <span class="badge rounded-pill bg-light text-muted border ms-2" style="font-size: 0.65rem;">{{ $courses->count() }}</span>
                </button>
            </li>
        </ul>
    </div>
</div>

{{-- Search & Filter Section --}}
<div class="card-minimal p-4 mb-4 border-0 shadow-sm" style="border-radius: 16px;">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="position-relative">
                <i class="bi bi-search position-absolute" style="left:16px;top:50%;transform:translateY(-50%);color:var(--crm-text-subtle);"></i>
                <input type="text" id="certSearch" class="filter-input ps-5" placeholder="Cari berdasarkan nama program atau kursus..." style="background: #fff;">
            </div>
        </div>
        <div class="col-md-4">
            <select id="certStatus" class="filter-input" style="background: #fff; cursor: pointer;">
                <option value="all">Semua Status Kesiapan</option>
                <option value="ready">Siap Terbit (Event Selesai + Aset Ada)</option>
                <option value="configured">Dikonfigurasi (Aset Ada)</option>
                <option value="not-configured">Belum Dikonfigurasi</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" id="certReset" class="btn btn-light fw-700 w-100 h-100" style="border-radius: 10px; border: 1px solid var(--crm-border);">
                Reset
            </button>
        </div>
    </div>
</div>

<div class="tab-content-container">
    {{-- Events Tab Pane --}}
    <div class="custom-tab-pane {{ $tab === 'events' ? 'active' : '' }}" id="events-pane">
        <div class="card-minimal border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="table-responsive">
                <table class="crm-table w-100">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem;">Nama Event</th>
                            <th style="text-align:center;">Peserta</th>
                            <th>Status Konfigurasi</th>
                            <th style="padding-right:1.5rem;text-align:right;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                            @php
                                $isConfigured = !empty($event->certificate_logo) || !empty($event->certificate_signature);
                                $eventDate = $event->event_date ? \Carbon\Carbon::parse($event->event_date) : null;
                                $isFinished = $eventDate ? ($eventDate->isPast() || $eventDate->isToday()) : false;
                                $status = $isConfigured ? ($isFinished ? 'ready' : 'configured') : 'not-configured';
                            @endphp
                            <tr class="cert-row" data-title="{{ strtolower($event->title) }}" data-status="{{ $status }}">
                                <td style="padding-left:1.5rem;">
                                    <div style="font-weight:800;font-size:0.95rem;color:var(--crm-navy);">{{ $event->title }}</div>
                                    <div style="font-size:0.75rem;color:var(--crm-text-subtle);">{{ $eventDate ? $eventDate->translatedFormat('d M Y') : 'Tanpa Tanggal' }}</div>
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge-soft" style="font-weight:700;">{{ $event->registrations_count }}</span>
                                </td>
                                <td>
                                    @if($isConfigured)
                                        <span class="status-pill ready"><i class="bi bi-check-circle-fill"></i> ASET LENGKAP</span>
                                    @else
                                        <span class="status-pill missing"><i class="bi bi-dash-circle"></i> ASET KOSONG</span>
                                    @endif
                                </td>
                                <td style="padding-right:1.5rem;text-align:right;">
                                    <div class="d-inline-flex gap-2 align-items-center justify-content-end" style="min-width: 250px;">
                                        @if($event->registrations_count > 0)
                                            <a href="{{ route('admin.crm.certificates.generate-massal', $event) }}" class="btn btn-sm px-3 fw-700" style="background:rgba(16,185,129,0.1);color:#059669;border-radius:8px;transition:all 0.2s;" onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">
                                                <i class="bi bi-download me-1"></i> Unduh Massal
                                            </a>
                                        @else
                                            <button class="btn btn-sm px-3 fw-700" disabled style="background:var(--crm-border-soft);color:var(--crm-text-subtle);border-radius:8px;cursor:not-allowed;border:none;">
                                                <i class="bi bi-download me-1"></i> Unduh Massal
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.crm.certificates.edit', $event) }}" class="btn btn-sm px-3 fw-700" style="background:var(--crm-primary-bg);color:var(--crm-primary);border-radius:8px;transition:all 0.2s;" onmouseover="this.style.background='rgba(124,58,237,0.12)'" onmouseout="this.style.background='var(--crm-primary-bg)'">Kelola Aset</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">Data event tidak ditemukan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Courses Tab Pane --}}
    <div class="custom-tab-pane {{ $tab === 'courses' ? 'active' : '' }}" id="courses-pane">
        <div class="card-minimal border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="table-responsive">
                <table class="crm-table w-100">
                    <thead>
                        <tr>
                            <th style="padding-left:1.5rem;">Nama Kursus</th>
                            <th style="text-align:center;">Siswa</th>
                            <th>Status Konfigurasi</th>
                            <th style="padding-right:1.5rem;text-align:right;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            @php
                                $isConfigured = !empty($course->certificate_logo) || !empty($course->certificate_signature);
                            @endphp
                            <tr class="cert-row" data-title="{{ strtolower($course->name) }}" data-status="{{ $isConfigured ? 'ready' : 'not-configured' }}">
                                <td style="padding-left:1.5rem;">
                                    <div style="font-weight:800;font-size:0.95rem;color:var(--crm-navy);">{{ $course->name }}</div>
                                    <div style="font-size:0.75rem;color:var(--crm-text-subtle);">{{ $course->category->name ?? 'General' }}</div>
                                </td>
                                <td style="text-align:center;">
                                    <span class="badge-soft">{{ $course->enrollments_count }}</span>
                                </td>
                                <td>
                                    @if($isConfigured)
                                        <span class="status-pill ready"><i class="bi bi-check-circle-fill"></i> ASET LENGKAP</span>
                                    @else
                                        <span class="status-pill missing"><i class="bi bi-dash-circle"></i> ASET KOSONG</span>
                                    @endif
                                </td>
                                <td style="padding-right:1.5rem;text-align:right;">
                                    <div class="d-inline-flex gap-2 align-items-center justify-content-end" style="min-width: 250px;">
                                        @if($course->completed_enrollments_count > 0)
                                            <a href="{{ route('admin.crm.certificates.generate-massal-course', $course) }}" class="btn btn-sm px-3 fw-700" style="background:rgba(16,185,129,0.1);color:#059669;border-radius:8px;transition:all 0.2s;" onmouseover="this.style.background='rgba(16,185,129,0.2)'" onmouseout="this.style.background='rgba(16,185,129,0.1)'">
                                                <i class="bi bi-download me-1"></i> Unduh Massal
                                            </a>
                                        @else
                                            <button class="btn btn-sm px-3 fw-700" disabled style="background:var(--crm-border-soft);color:var(--crm-text-subtle);border-radius:8px;cursor:not-allowed;border:none;">
                                                <i class="bi bi-download me-1"></i> Unduh Massal
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.crm.certificates.edit-course', $course) }}" class="btn btn-sm px-3 fw-700" style="background:var(--crm-primary-bg);color:var(--crm-primary);border-radius:8px;transition:all 0.2s;" onmouseover="this.style.background='rgba(124,58,237,0.12)'" onmouseout="this.style.background='var(--crm-primary-bg)'">Kelola Aset</a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">Data kursus tidak ditemukan.</td></tr>
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
    // --- Custom Tab Switching Logic ---
    const tabBtns = document.querySelectorAll('.custom-tab-btn');
    const tabPanes = document.querySelectorAll('.custom-tab-pane');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons and panes
            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            // Add active class to clicked button and target pane
            this.classList.add('active');
            const targetId = this.getAttribute('data-target');
            document.getElementById(targetId).classList.add('active');

            // Update URL without reloading
            const tabName = targetId.replace('-pane', '');
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        });
    });

    // --- Filtering Logic ---
    const searchInput = document.getElementById('certSearch');
    const statusFilter = document.getElementById('certStatus');
    const resetBtn = document.getElementById('certReset');
    
    // Force reset filters on load to prevent browser cache from hiding rows accidentally
    if (searchInput) searchInput.value = '';
    if (statusFilter) statusFilter.value = 'all';
    
    function runFilter() {
        if(!searchInput || !statusFilter) return;
        const term = searchInput.value.toLowerCase().trim();
        const status = statusFilter.value;
        
        document.querySelectorAll('.cert-row').forEach(row => {
            const title = row.getAttribute('data-title') || '';
            const rowStatus = row.getAttribute('data-status');
            const matchSearch = term === '' || title.includes(term);
            const matchStatus = status === 'all' || rowStatus === status;
            
            // Explicitly set display without !important first to be safe
            if (matchSearch && matchStatus) {
                row.style.display = 'table-row';
            } else {
                row.style.display = 'none';
            }
        });
    }

    searchInput?.addEventListener('input', runFilter);
    statusFilter?.addEventListener('change', runFilter);
    resetBtn?.addEventListener('click', () => {
        if(searchInput) searchInput.value = '';
        if(statusFilter) statusFilter.value = 'all';
        runFilter();
    });

    console.log('CRM Certificate: Custom Tabs & Filtering initialized.');
});
</script>
@endsection