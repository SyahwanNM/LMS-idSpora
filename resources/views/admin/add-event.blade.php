@extends('layouts.admin')
@section('title', 'Kelola Event')
@section('content')
<div class="container-fluid py-4">
        {{-- Success Toast Popup --}}
        @if(session('success'))
            <div aria-live="polite" aria-atomic="true" class="position-relative">
                <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
                    <div id="eventUpdatedToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                        <div class="d-flex">
                            <div class="toast-body">
                                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            </div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function(){
                    try{
                        var el = document.getElementById('eventUpdatedToast');
                        if(window.bootstrap && el){
                            var t = new bootstrap.Toast(el);
                            t.show();
                        }
                    }catch(e){}
                });
            </script>
        @endif
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @if(session('statusFilter'))
            <script>
                document.addEventListener('DOMContentLoaded', function(){
                    try{
                        var sel = document.getElementById('statusFilter');
                        if(sel){
                            sel.value = '{{ session('statusFilter') }}';
                            sel.dispatchEvent(new Event('change'));
                        }
                    }catch(e){}
                });
            </script>
        @endif
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="bi bi-calendar3 me-2"></i>Manage Event</h4>
            <div class="btn-group">
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal"><i class="bi bi-plus-lg"></i> Tambah Event</button>
            </div>
        </div>
        <div class="card shadow-sm"><div class="card-body">
            {{-- Month keys removed: using native month picker instead of precomputed list --}}
            <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                <div class="d-flex flex-column" style="max-width:420px">
                    <small class="text-muted fw-semibold mb-1">Cari Nama</small>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="eventSearch" class="form-control" placeholder="Cari nama event..." autocomplete="off">
                    </div>
                </div>
                <div class="d-flex flex-column" style="max-width:240px">
                    <small class="text-muted fw-semibold mb-1">Status Event</small>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-funnel"></i></span>
                        <select id="statusFilter" class="form-select" aria-label="Filter status">
                            <option value="all" selected>Semua Status</option>
                            <option value="upcoming">Segera Hadir</option>
                            <option value="ongoing">Berlangsung</option>
                            <option value="finished">Telah Selesai</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex flex-column" style="max-width:220px">
                    <small class="text-muted fw-semibold mb-1">Tipe Kelola</small>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-diagram-3"></i></span>
                        <select id="manageFilter" class="form-select" aria-label="Filter tipe kelola">
                            <option value="all" selected>Semua Tipe</option>
                            <option value="manage">Manage</option>
                            <option value="create">Create</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex flex-column" style="max-width:260px">
                    <small class="text-muted fw-semibold mb-1">Bulan Event</small>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-calendar2-month"></i></span>
                        <input type="month" id="eventMonthFilter" class="form-control" aria-label="Filter bulan">
                        <button type="button" id="clearMonthFilter" class="btn btn-outline-secondary" title="Reset filter bulan"><i class="bi bi-x-circle"></i></button>
                    </div>
                </div>
            </div>
            @if(isset($events) && $events->count())
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Poster</th>
                                <th>Judul</th>
                                <th>Deskripsi</th>
                                <th>Pembicara</th>
                                <th>Tanggal</th>
                                <th>Lokasi</th>
                                <th>Link</th>
                                <th>Kelengkapan Dokumen</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                            @php $rowPct = $event->documents_completion_percent; @endphp
                            @php
                                // Tentukan status event: upcoming, ongoing, finished berdasarkan tanggal mulai/selesai
                                $start = !empty($event->start_date) ? \Carbon\Carbon::parse($event->start_date) : (!empty($event->event_date) ? \Carbon\Carbon::parse($event->event_date) : null);
                                $end = !empty($event->end_date) ? \Carbon\Carbon::parse($event->end_date) : $start;
                                $now = \Carbon\Carbon::now();
                                $status = 'all';
                                if($start && $end){
                                    if($now->lt($start)) $status = 'upcoming';
                                    elseif($now->between($start, $end)) $status = 'ongoing';
                                    elseif($now->gt($end)) $status = 'finished';
                                }
                            @endphp
                            @php $monthValue = $start ? $start->format('Y-m') : (!empty($event->event_date) ? \Carbon\Carbon::parse($event->event_date)->format('Y-m') : ''); @endphp
                            <tr class="{{ $rowPct < 100 ? 'table-warning-subtle' : '' }} table-row-clickable" data-url="{{ route('admin.events.show',$event) }}" data-title="{{ Str::lower($event->title) }}" data-status="{{ $status }}" data-month="{{ $monthValue }}" data-manage="{{ $event->manage_action ?? 'all' }}">
                                <td style="width:100px;">
                                    <div class="position-relative d-inline-block">
                                        @if(!empty($event->manage_action))
                                            <span class="manage-action-ribbon-thumb manage-action-{{ $event->manage_action }}">{{ strtoupper($event->manage_action) }}</span>
                                        @endif
                                        @if($event->image)
                                            <img src="{{ Storage::url($event->image) }}" alt="{{ $event->title }}" class="img-thumbnail" style="max-width:90px;height:60px;object-fit:cover;">
                                        @else
                                            <span class="badge bg-secondary">No Image</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="fw-semibold">{{ $event->title }}</td>
                                <td class="text-truncate" style="max-width:220px;" title="{{ strip_tags($event->description) }}">{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 60) }}</td>
                                <td>{{ $event->speaker ?? '—' }}</td>
                                <td>
                                    @if(!empty($event->event_date))
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('d F Y') }}
                                    @else
                                        —
                                    @endif
                                </td>
                                
                                <td>{{ $event->location }}</td>
                                <td>
                                    @if(!empty($event->zoom_link))
                                        <a href="{{ $event->zoom_link }}" target="\_blank" class="btn btn-sm btn-outline-primary" title="Buka Zoom"><i class="bi bi-camera-video"></i> Zoom</a>
                                    @elseif(!empty($event->maps_url))
                                        <a href="{{ $event->maps_url }}" target="\_blank" class="btn btn-sm btn-outline-secondary" title="Buka Maps"><i class="bi bi-geo-alt"></i> Maps</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @php 
                                        $pct = $event->documents_completion_percent; 
                                        $completed = $event->documents_completed_count; 
                                        $hasVbg = !empty($event->vbg_path);
                                        $hasCert = !empty($event->certificate_path);
                                        $hasAbs = !empty($event->attendance_path);
                                        $tooltip = 'Virtual Background: '.($hasVbg ? '✔' : '✖').', Sertifikat: '.($hasCert ? '✔' : '✖').', Absensi: '.($hasAbs ? '✔' : '✖');
                                        $pctClass = $pct === 100 ? 'doc-pct chip-success' : 'doc-pct chip-incomplete';
                                    @endphp
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <span class="{{ $pctClass }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Kelengkapan Dokumen">{{ $pct }}%</span>
                                        <div class="d-inline-flex align-items-center doc-info-actions gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal-{{ $event->id }}" title="Kelola Dokumen">
                                                <i class="bi bi-folder2-open"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1">{{ $completed }}/3 selesai</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm action-btn-group" role="group" aria-label="Aksi event {{ $event->title }}">
                                        <a href="{{ route('admin.events.show',$event) }}" class="btn btn-outline-info btn-action-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat">
                                            <i class="bi bi-eye"></i><span class="visually-hidden">Lihat</span>
                                        </a>
                                        <a href="{{ route('admin.events.edit',$event) }}" class="btn btn-outline-warning btn-action-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                            <i class="bi bi-pencil-square"></i><span class="visually-hidden">Edit</span>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-action-icon"
                                            data-bs-toggle="modal" data-bs-target="#deleteEventModal"
                                            title="Hapus"
                                            data-url="{{ route('admin.events.destroy',$event) }}"
                                            data-title="{{ $event->title }}"
                                            data-image="{{ $event->image ? Storage::url($event->image) : '' }}">
                                            <i class="bi bi-trash"></i><span class="visually-hidden">Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                {{-- Modals: Kelengkapan Dokumen per Event --}}
                @foreach($events as $event)
                <div class="modal-upload-operasional modal fade" id="uploadOperasionalModal-{{ $event->id }}" tabindex="-1" aria-labelledby="uploadOperasionalLabel-{{ $event->id }}" aria-hidden="true">

        <script>
        document.addEventListener('DOMContentLoaded', function(){
            // Inline validation for Kelola Event required
            const form = document.getElementById('eventForm');
            const manageSel = document.getElementById('manage_action');
            const manageHelp = document.getElementById('manageActionHelp');
            if(form && manageSel){
                const checkManage = () => {
                    const val = manageSel.value || '';
                    const invalid = (val === '' || val === null);
                    if(manageHelp){ manageHelp.style.display = invalid ? 'block' : 'none'; }
                    return !invalid;
                };
                manageSel.addEventListener('change', checkManage);
                form.addEventListener('submit', function(e){ if(!checkManage()){ e.preventDefault(); manageSel.focus(); } });
            }
            const searchInput = document.getElementById('eventSearch');
            const statusSelect = document.getElementById('statusFilter');
            const manageSelect = document.getElementById('manageFilter');
            const monthInput = document.getElementById('eventMonthFilter');
            const clearMonthBtn = document.getElementById('clearMonthFilter');
            const rows = Array.from(document.querySelectorAll('table tbody tr'));

            let currentStatus = 'all';
            let currentMonth = 'all';
            let searchTerm = '';
            let currentManage = 'all';

            const applyFilters = () => {
                const term = searchTerm.trim();
                rows.forEach(row => {
                    const title = (row.getAttribute('data-title') || '').toLowerCase();
                    const status = row.getAttribute('data-status') || 'all';
                    const month = row.getAttribute('data-month') || '';
                    const manage = row.getAttribute('data-manage') || 'all';
                    const matchSearch = term === '' || title.includes(term);
                    const matchStatus = currentStatus === 'all' || status === currentStatus;
                    const matchMonth = currentMonth === 'all' || month === currentMonth;
                    const matchManage = currentManage === 'all' || manage === currentManage;
                    row.style.display = (matchSearch && matchStatus && matchMonth && matchManage) ? '' : 'none';
                });
            };

            // Debounced search
            let tId;
            searchInput && searchInput.addEventListener('input', () => {
                clearTimeout(tId);
                tId = setTimeout(() => {
                    searchTerm = searchInput.value.toLowerCase();
                    applyFilters();
                }, 150);
            });

            // Status filter select
            statusSelect && statusSelect.addEventListener('change', () => {
                currentStatus = statusSelect.value || 'all';
                applyFilters();
            });

            // Manage/create filter select
            manageSelect && manageSelect.addEventListener('change', () => {
                currentManage = manageSelect.value || 'all';
                applyFilters();
            });

            // Month picker filter
            monthInput && monthInput.addEventListener('change', () => {
                const val = monthInput.value; // format YYYY-MM
                currentMonth = val ? val : 'all';
                applyFilters();
            });
            clearMonthBtn && clearMonthBtn.addEventListener('click', () => {
                if(monthInput){ monthInput.value=''; }
                currentMonth = 'all';
                applyFilters();
            });
        });
        </script>
                    <div class="modal-dialog modal-dialog-scrollable">
                        <div class="content-operasional-view modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="uploadOperasionalLabel-{{ $event->id }}">Status Dokumen Detail</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">Tinjau status semua dokumen terkait acara dan administrasi.</p>
                                @php 
                                    $hasVbg = !empty($event->vbg_path);
                                    $hasCert = !empty($event->certificate_path);
                                    $hasAbs = !empty($event->attendance_path);
                                @endphp
                                <ul class="list-group list-group-flush mb-3 small">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="bi {{ $hasVbg ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i>
                                            Virtual Background
                                        </span>
                                        <span>
                                            @if($hasVbg)
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
                                            @else
                                                <span class="text-muted">Belum ada</span>
                                            @endif
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="bi {{ $hasCert ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i>
                                            Sertifikat
                                        </span>
                                        <span>
                                            @if($hasCert)
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
                                            @else
                                                <span class="text-muted">Belum ada</span>
                                            @endif
                                        </span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="bi {{ $hasAbs ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i>
                                            Absensi
                                        </span>
                                        <span>
                                            @if($hasAbs)
                                                @php $aExt = strtolower(pathinfo($event->attendance_path, PATHINFO_EXTENSION)); @endphp
                                                @if(in_array($aExt, ['jpg','jpeg','png','gif','webp','bmp','svg']))
                                                    <a href="{{ Storage::url($event->attendance_path) }}" target="_blank" class="d-inline-block">
                                                        <img src="{{ Storage::url($event->attendance_path) }}" alt="Absensi" class="rounded border" style="width:56px;height:36px;object-fit:cover;">
                                                    </a>
                                                @elseif($aExt === 'pdf')
                                                    <a href="{{ Storage::url($event->attendance_path) }}" target="_blank" class="link-primary"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
                                                @else
                                                    <a href="{{ Storage::url($event->attendance_path) }}" target="_blank" class="link-primary">Lihat</a>
                                                @endif
                                            @else
                                                <span class="text-muted">Belum ada</span>
                                            @endif
                                        </span>
                                    </li>
                                </ul>
                                <form action="{{ route('admin.events.documents.upload', $event) }}" method="post" enctype="multipart/form-data" id="docForm-{{ $event->id }}">
                                    @csrf
                                    @php $allComplete = $hasVbg && $hasCert && $hasAbs; @endphp
                                    @if($allComplete)
                                        <div class="text-center mb-3">
                                            <button type="button" class="btn btn-outline-primary btn-sm" data-edit-doc-toggle="{{ $event->id }}">
                                                <i class="bi bi-pencil-square me-1"></i>Edit Upload
                                            </button>
                                        </div>
                                        <div class="doc-edit-wrapper d-none" id="docEditWrapper-{{ $event->id }}">
                                            <div class="box-up mb-3">
                                                <label for="vbg-{{ $event->id }}" class="form-label">Virtual Background</label>
                                                <input type="file" class="form-control" id="vbg-{{ $event->id }}" name="virtual_background" accept="image/*" data-preview="#vbg-preview-{{ $event->id }}">
                                                <div id="vbg-preview-{{ $event->id }}" class="mt-2"></div>
                                            </div>
                                            <div class="box-up mb-3">
                                                <label for="sertif-{{ $event->id }}" class="form-label">Sertifikat</label>
                                                <input type="file" class="form-control" id="sertif-{{ $event->id }}" name="certificate" accept="image/*,application/pdf" data-preview="#sertif-preview-{{ $event->id }}">
                                                <div id="sertif-preview-{{ $event->id }}" class="mt-2"></div>
                                            </div>
                                            <div class="box-up mb-3">
                                                <label for="absensi-{{ $event->id }}" class="form-label">Absensi</label>
                                                <input type="file" class="form-control" id="absensi-{{ $event->id }}" name="attendance" accept="image/*,application/pdf" data-preview="#absensi-preview-{{ $event->id }}">
                                                <div id="absensi-preview-{{ $event->id }}" class="mt-2"></div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="box-up mb-3">
                                            <label for="vbg-{{ $event->id }}" class="form-label">Virtual Background</label>
                                            <input type="file" class="form-control" id="vbg-{{ $event->id }}" name="virtual_background" accept="image/*" data-preview="#vbg-preview-{{ $event->id }}">
                                            <div id="vbg-preview-{{ $event->id }}" class="mt-2"></div>
                                        </div>
                                        <div class="box-up mb-3">
                                            <label for="sertif-{{ $event->id }}" class="form-label">Sertifikat</label>
                                            <input type="file" class="form-control" id="sertif-{{ $event->id }}" name="certificate" accept="image/*,application/pdf" data-preview="#sertif-preview-{{ $event->id }}">
                                            <div id="sertif-preview-{{ $event->id }}" class="mt-2"></div>
                                        </div>
                                        <div class="box-up mb-3">
                                            <label for="absensi-{{ $event->id }}" class="form-label">Absensi</label>
                                            <input type="file" class="form-control" id="absensi-{{ $event->id }}" name="attendance" accept="image/*,application/pdf" data-preview="#absensi-preview-{{ $event->id }}">
                                            <div id="absensi-preview-{{ $event->id }}" class="mt-2"></div>
                                        </div>
                                    @endif
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-primary" form="docForm-{{ $event->id }}">Save changes</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="mt-3">{{ $events->links() }}</div>
            @else <div class="text-center py-5">Belum ada event.</div> @endif
        </div></div>
        <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="addEventModalLabel"><i class="bi bi-calendar-plus me-2"></i>Tambah Event Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">@csrf
                        <div class="row g-3">
                            <div class="col-lg-8">
                                <div class="mb-3">
                                    <label for="gambar" class="form-label fw-semibold">Gambar Event <span class="text-danger">*</span></label>
                                    <input type="file" name="image" id="gambar" class="form-control" accept="image/*" required>
                                    <div class="form-text">Format: JPG, PNG. Maksimal 5MB. <span id="imageSizeInfo" class="fw-semibold"></span></div>
                                    <div id="imagePreview" class="mt-3" style="display:none;">
                                        <img id="previewImg" src="#" alt="Preview" class="img-fluid rounded shadow-sm" style="max-height:200px;width:100%;object-fit:cover;">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="nama" class="form-label fw-semibold">Nama Event <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="nama" class="form-control" required value="{{ old('title') }}" placeholder="Masukkan Nama Event">
                                </div>
                                <!-- Pembicara (dynamic, minimal 1 required) -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Pembicara <span class="text-danger">*</span></label>
                                    @php $oldSpeakers = old('speakers', []); @endphp
                                    <div id="speakersContainer" class="d-flex flex-column gap-2">
                                        @if(!empty($oldSpeakers))
                                            @foreach($oldSpeakers as $i => $sp)
                                            <div class="input-group speaker-row">
                                                <input type="text" name="speakers[]" class="form-control" value="{{ $sp }}" placeholder="Nama pembicara" {{ $i === 0 ? 'required' : '' }}>
                                                <button type="button" class="btn btn-outline-danger remove-speaker" {{ $i === 0 ? 'disabled' : '' }} title="Hapus">&times;</button>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="input-group speaker-row">
                                                <input type="text" name="speakers[]" class="form-control" placeholder="Nama pembicara" required>
                                                <button type="button" class="btn btn-outline-danger remove-speaker" disabled title="Hapus">&times;</button>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="addSpeakerRow"><i class="bi bi-plus-circle me-1"></i>Tambah Nama Pembicara</button>
                                    <input type="hidden" name="speaker" id="speakerCombined" value="{{ old('speaker') }}">
                                    <div class="form-text">Minimal 1 pembicara (wajib). Tambahan pembicara bersifat opsional.</div>
                                </div>
                                <!-- Materi (kategori konten) -->
                                <div class="mb-3">
                                    <label for="materi" class="form-label fw-semibold">Materi <span class="text-danger">*</span></label>
                                    <select name="materi" id="materi" class="form-select" required>
                                        @php $materiOpts = [
                                            'Web Programming','Mobile Programming','Fullstack Development','Backend Development','UI / UX','Product Management',
                                            'Quality Assurance','Digital Marketing','Cyber Security','Career Development','Tech Entrepreneur','Freelancer',
                                            'Content Creator','Academic Mentoring','Data','Dev Ops','Game Development','AI','Product Design','N8N','BPMN'
                                        ]; @endphp
                                        <option value="" disabled {{ old('materi') ? '' : 'selected' }}>Pilih materi</option>
                                        @foreach($materiOpts as $opt)
                                            <option value="{{ $opt }}" {{ old('materi') === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Kelola Event: Manage / Create -->
                                <div class="mb-3">
                                    <label for="manage_action" class="form-label fw-semibold">Kelola Event <span class="text-danger">*</span></label>
                                    <select name="manage_action" id="manage_action" class="form-select" required>
                                        <option value="" disabled {{ old('manage_action') ? '' : 'selected' }}>Pilih aksi</option>
                                        <option value="manage" {{ old('manage_action') === 'manage' ? 'selected' : '' }}>Manage</option>
                                        <option value="create" {{ old('manage_action') === 'create' ? 'selected' : '' }}>Create</option>
                                    </select>
                                    <small id="manageActionHelp" class="text-danger" style="display:none">Lengkapi: pilih salah satu (Manage/Create) sebelum menyimpan.</small>
                                </div>
                                <!-- Jenis Acara -->
                                <div class="mb-3">
                                    <label for="jenis" class="form-label fw-semibold">Jenis Acara <span class="text-danger">*</span></label>
                                    <select name="jenis" id="jenis" class="form-select" required>
                                        @php $jenisOpts = ['Webinar','Seminar','Workshop']; @endphp
                                        <option value="" disabled {{ old('jenis') ? '' : 'selected' }}>Pilih jenis acara</option>
                                        @foreach($jenisOpts as $j)
                                            <option value="{{ $j }}" {{ old('jenis') === $j ? 'selected' : '' }}>{{ $j }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <!-- Level field removed per request -->
                                <!-- Penjelasan Singkat (maks 40 kata) -->
                                <div class="mb-3">
                                    <label for="short_desc" class="form-label fw-semibold">Penjelasan Singkat <span class="text-danger">*</span></label>
                                    <textarea name="short_description" id="short_desc" class="form-control" rows="3" required placeholder="Ringkas tujuan atau inti acara (maks 40 kata)">{{ old('short_description') }}</textarea>
                                    <small class="d-block mt-1" id="shortDescHint"><span id="shortDescCount">0</span>/40 kata</small>
                                </div>
                                <div class="mb-3">
                                    <label for="deskripsi" class="form-label fw-semibold">Deskripsi Event <span class="text-danger">*</span></label>
                                    <textarea name="description" id="deskripsi" class="form-control" rows="6" required>{{ old('description') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label fw-semibold">Tanggal Event <span class="text-danger">*</span></label>
                                    <input type="date" name="event_date" id="tanggal" class="form-control date-enhanced js-date-picker" required value="{{ old('event_date') }}" min="{{ date('Y-m-d') }}">
                                    <small class="text-muted d-block mt-1">Format tampilan: Hari, DD Bulan YYYY</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Waktu Mulai & Selesai <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="time" name="event_time" id="masuk1" class="form-control" required value="{{ old('event_time') }}">
                                        <span>s/d</span>
                                        <input type="time" name="event_time_end" id="masuk2" class="form-control" value="{{ old('event_time_end') }}">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label fw-semibold">Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" name="location" id="lokasi" class="form-control" required value="{{ old('location') }}" placeholder="Masukkan Lokasi">
                                </div>
                                <div class="mb-3">
                                    <label for="hargaDisplay" class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                                    <input type="text" id="hargaDisplay" class="form-control currency-input" required inputmode="numeric" placeholder="0" autocomplete="off" value="{{ number_format((int)old('price',0),0,',','.') }}">
                                    <input type="hidden" name="price" id="harga" value="{{ (int)old('price',0) }}">
                                    <small class="text-muted">Gunakan hanya angka. Otomatis diformat: contoh 1.000, 10.000, 100.000.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="diskon" class="form-label fw-semibold">Diskon (%)</label>
                                    <input type="number" name="discount_percentage" id="diskon" class="form-control" min="0" max="100" step="1" value="{{ old('discount_percentage',0) }}" placeholder="0">
                                </div>
                                <div class="mb-3">
                                    <label for="discount_until" class="form-label fw-semibold">Jangka Waktu Diskon</label>
                                    <input type="date" name="discount_until" id="discount_until" class="form-control js-discount-date" value="{{ old('discount_until') }}" disabled>
                                    <small class="text-muted d-block mt-1">Harus sebelum tanggal event (tidak termasuk hari H).</small>
                                </div>
                                <div class="mb-3">
                                    <label for="benefit" class="form-label fw-semibold">Benefit <span class="text-muted">(Opsional)</span></label>
                                    <div id="benefitsContainer" class="d-flex flex-column gap-2">
                                        <!-- Rows will be injected by JS; fallback single row for no-JS -->
                                        <div class="input-group benefit-row d-none">
                                            <span class="input-group-text"><i class="bi bi-check2"></i></span>
                                            <input type="text" class="form-control" name="benefits[]" placeholder="Contoh: Sertifikat peserta">
                                            <button type="button" class="btn btn-outline-danger remove-benefit" title="Hapus">&times;</button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" id="addBenefitRow" class="btn btn-outline-secondary btn-sm">
                                            <i class="bi bi-plus"></i> Tambah Benefit
                                        </button>
                                    </div>
                                    <input type="hidden" name="benefit" id="benefit" value="{{ old('benefit') }}">
                                    <small class="text-muted d-block mt-1">Masukkan benefit per baris. Klik Tambah untuk menambah item.</small>
                                </div>
                                <div class="mb-3">
                                    <label for="maps" class="form-label fw-semibold">Maps Lokasi (Jika Offline)</label>
                                    <div class="input-group">
                                        <input type="text" name="maps_url" id="maps" class="form-control" value="{{ old('maps_url') }}" placeholder="Tempel link Google Maps (bisa short link maps.app.goo.gl)">
                                        <button class="btn btn-outline-secondary" type="button" id="btnResolveMaps">Deteksi</button>
                                    </div>
                                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                    <div id="mapsPreview" class="mt-2 rounded border" style="display:none;height:260px;"></div>
                                    <div class="form-text">Klik "Deteksi" untuk mencoba membaca koordinat dari short link Google Maps.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="zoom" class="form-label fw-semibold">Link Zoom (Jika Online)</label>
                                    <input type="text" name="zoom_link" id="zoom" class="form-control" value="{{ old('zoom_link') }}" placeholder="Masukkan Link Zoom">
                                </div>
                                <div class="mb-3">
                                    <label for="terms" class="form-label fw-semibold">Terms & Condition</label>
                                    <textarea name="terms_and_conditions" id="terms" class="form-control" rows="6">{{ old('terms_and_conditions') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Schedule <span class="text-muted small">(Opsional)</span></label>
                                    <table class="table table-sm align-middle" id="scheduleTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width:180px">Waktu Mulai</th>
                                                <th style="width:180px">Waktu Selesai</th>
                                                <th>Kegiatan</th>
                                                <th>Deskripsi</th>
                                                <th style="width:80px" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="addScheduleRow"><i class="bi bi-plus-circle me-1"></i>Tambah Baris</button>
                                </div>

                                <!-- Pengeluaran -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Pengeluaran <span class="text-muted small">(Opsional)</span></label>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle" id="expensesTable">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Barang</th>
                                                    <th style="width:120px">Kuantitas</th>
                                                    <th style="width:160px">Harga Satuan (Rp)</th>
                                                    <th style="width:180px">Harga Total (Rp)</th>
                                                    <th style="width:80px" class="text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" id="addExpenseRow"><i class="bi bi-plus-circle me-1"></i>Tambah Pengeluaran</button>
                                    <div class="d-flex justify-content-end mt-2">
                                        <span class="me-2 fw-semibold">Total Pengeluaran:</span>
                                        <span id="expensesGrandTotal" class="fw-bold">Rp0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <div class="event-side-sticky">
                                <div class="alert alert-info small">
                                    <strong>Tips:</strong> Pastikan data event sudah benar sebelum disimpan. Gunakan deskripsi yang menarik dan informatif.
                                </div>
                                <ul class="list-group mb-3 small">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">Status Harga
                                        <span class="badge bg-secondary" id="statusHarga">Gratis</span>
                                    </li>
                                    <li class="list-group-item">Diskon akan otomatis dihitung jika persentase > 0.</li>
                                    <li class="list-group-item">Gunakan Maps untuk event offline dan Zoom untuk event online.</li>
                                </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="me-auto small text-muted" id="submitHint" style="display:none;">
                        Lengkapi semua field bertanda * terlebih dahulu untuk mengaktifkan tombol Simpan.
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" form="eventForm" id="submitBtn" disabled>
                        <i class="bi bi-check-circle me-1"></i> Simpan Event
                    </button>
                </div>
            </div></div>
        </div>
    </div>
    @endsection
    @section('styles')
    <style>
        .ck-editor__editable{min-height:260px}
        /* (bar kelengkapan dokumen dihapus) */
        /* subtle row warning background for incomplete */
        .table-warning-subtle{ background-color: #fff8e1; }
        /* Aksi tombol ikon estetis */
        .action-btn-group .btn-action-icon{width:34px;height:34px;padding:0;display:inline-flex;align-items:center;justify-content:center;border-radius:8px;position:relative;}
        .action-btn-group .btn-action-icon i{font-size:1rem;line-height:1;}
        .action-btn-group .btn-action-icon:not(:last-child){margin-right:4px;}
        .action-btn-group .btn-action-icon:hover{box-shadow:0 4px 10px -2px rgba(0,0,0,.15);transform:translateY(-2px);}
        .action-btn-group .btn-action-icon:active{transform:translateY(0);}
        @media (prefers-reduced-motion:reduce){.action-btn-group .btn-action-icon{transition:none}}
        /* Doc percent chips */
        .doc-pct{display:inline-block;font-weight:600;letter-spacing:.3px;padding:4px 10px;border-radius:999px;font-size:.75rem;line-height:1;}
        .chip-success{background:#d1fadf;color:#047857;box-shadow:0 0 0 1px #a7f3d0 inset;}
        .chip-incomplete{background:#eef2ff;color:#1e3a8a;box-shadow:0 0 0 1px #c7d2fe inset;}
        .doc-info-actions .btn{padding:2px 8px;}
        .doc-info-actions .btn i{font-size:.9rem;}
        /* Hidden edit upload wrapper styling */
        .doc-edit-wrapper{border:1px dashed #dee2e6;padding:.75rem .75rem .25rem;border-radius:.5rem;background:#f8f9fa;}
        .doc-edit-wrapper .box-up:last-child{margin-bottom:.25rem;}
        /* Enhanced date input */
        .date-enhanced:focus { box-shadow: 0 0 0 .2rem rgba(13,110,253,.25); }
        .flatpickr-calendar { font-family: inherit; }
        .flatpickr-day.today { border-color:#0d6efd; }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { background:#0d6efd; color:#fff; }
        .flatpickr-day.selected:hover { background:#0b5ed7; }
        .flatpickr-input[disabled], .flatpickr-alt-input[disabled] { background:#f8f9fa; cursor:not-allowed; }
        #tanggalFriendly { font-style: italic; }
        /* clickable row hover */
        .table-row-clickable{ cursor:pointer; transition: background-color .18s ease, box-shadow .18s ease; }
        /* Apply hover color to cells so full width highlights */
        .table-row-clickable:hover > td{ background-color:#eef5ff !important; }
        .table-row-clickable:active > td{ background-color:#e2ecf9 !important; }
        /* Fallback if browser ignores > selector on TR */
        .table-row-clickable.hovering > td{ background-color:#eef5ff !important; }
        /* Leaflet map size override inside modal */
        #mapsPreview { min-height: 240px; }
        .leaflet-container { font: inherit; }
        /* Small doc upload preview thumbnail */
        .doc-thumb { width: 64px; height: 40px; object-fit: cover; border: 1px solid #dee2e6; border-radius: .25rem; }
        /* Removable preview wrapper */
        .preview-removable{position:relative;display:inline-block;}
        .remove-preview-btn{position:absolute;top:-8px;right:-8px;background:#dc3545;color:#fff;border:none;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.9rem;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,.25);}
        .remove-preview-btn:hover{background:#c82333;}
        .remove-preview-btn:active{background:#bd2130;}
        /* Force all modal form text to black */
        #addEventModal label,
        #addEventModal .form-label,
        #addEventModal input[type=text],
        #addEventModal input[type=number],
        #addEventModal input[type=date],
        #addEventModal input[type=time],
        #addEventModal input[type=file],
        #addEventModal textarea,
        #addEventModal th,
        #addEventModal td,
        #addEventModal .modal-title { color:#000 !important; }
        /* CKEditor content area */
        #addEventModal .ck-content { color:#000 !important; }
        /* Placeholder text to black as well */
        #addEventModal input::placeholder,
        #addEventModal textarea::placeholder { color:#000; opacity:1; }
        /* Prevent Bootstrap .text-muted on labels inside this modal */
        #addEventModal .text-muted { color:#000 !important; }
        /* Sticky side panel so Tips/Status follows scroll in modal */
        #addEventModal .event-side-sticky { position: sticky; top: .5rem; }
            /* Draggable modal UX */
            .modal-draggable .modal-header { cursor: move; user-select: none; }
        /* Modern danger modal styling */
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
        /* Manage/Create ribbon for thumbnails */
        .manage-action-ribbon-thumb { position:absolute; top:-6px; left:-6px; padding:3px 8px 3px 10px; background:linear-gradient(135deg,#0d6efd,#3b82f6); color:#fff; font-size:.55rem; font-weight:600; letter-spacing:.5px; text-transform:uppercase; border-radius:0 4px 4px 0; box-shadow:0 2px 6px -2px rgba(0,0,0,.3); z-index:3; }
        .manage-action-ribbon-thumb:before { content:''; position:absolute; left:0; top:100%; width:0; height:0; border-left:6px solid #093d94; border-top:6px solid transparent; }
        .manage-action-create { background:linear-gradient(135deg,#16a34a,#22c55e); }
        .manage-action-create:before { border-left-color:#0f5d2c; }
        .manage-action-manage { background:linear-gradient(135deg,#0d6efd,#3b82f6); }
        .manage-action-manage:before { border-left-color:#093d94; }
        /* Ensure delete confirmation text is black */
        #deleteEventModal .form-check-label { color: #000 !important; }
    </style>
    @endsection
    @section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Bootstrap tooltips for action icon buttons
        if(window.bootstrap){
            const tTriggers = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tTriggers.forEach(el => { try { new bootstrap.Tooltip(el); } catch(_){} });
        }
        // CKEditor init for deskripsi and terms
        ClassicEditor.create(document.querySelector('#deskripsi'), {
            toolbar: ['heading','|','bold','italic','underline','|','bulletedList','numberedList','|','link','blockQuote','insertTable','|','undo','redo','removeFormat']
        }).then(e => {
            // store globally and ensure textarea value stays in sync for required checks
            window.editorDeskripsi = e;
            const ta = document.getElementById('deskripsi');
            if (ta) {
                ta.value = e.getData();
            }
            // if updateSubmitState is available later, this listener will keep the button state correct
            e.model.document.on('change:data', () => {
                if (ta) { ta.value = e.getData(); }
                if (typeof window.updateSubmitState === 'function') {
                    window.updateSubmitState();
                }
            });
        }).catch(console.error);
        ClassicEditor.create(document.querySelector('#terms'), {
            toolbar: ['bold','italic','underline','bulletedList','numberedList','link','undo','redo','removeFormat']
        }).then(e => window.editorTerms = e).catch(console.error);

        // Image preview & size display (max 5MB)
        const imgInp = document.getElementById('gambar');
        const imageSizeInfo = document.getElementById('imageSizeInfo');
        if (imgInp) {
            imgInp.addEventListener('change', ev => {
                const f = ev.target.files[0];
                const preview = document.getElementById('imagePreview');
                if (!f) { preview.style.display = 'none'; if(imageSizeInfo) imageSizeInfo.textContent=''; return; }
                const sizeMB = (f.size / (1024*1024));
                if(imageSizeInfo){ imageSizeInfo.textContent = 'Ukuran: ' + sizeMB.toFixed(2) + 'MB'; }
                if(sizeMB > 5){
                    alert('Ukuran gambar melebihi 5MB. Silakan pilih file yang lebih kecil.');
                    imgInp.value='';
                    preview.style.display='none';
                    if(imageSizeInfo) imageSizeInfo.textContent='';
                    return;
                }
                const r = new FileReader();
                r.onload = e => {
                    document.getElementById('previewImg').src = e.target.result;
                    preview.style.display = 'block';
                };
                r.readAsDataURL(f);
            });
        }

        // Maps preview (Leaflet) from Google Maps link
    let leafletMap = null, leafletMarker = null;
        const mapsInput = document.getElementById('maps');
        const mapsPreview = document.getElementById('mapsPreview');
        const zoomInput = document.getElementById('zoom');
    const btnResolveMaps = document.getElementById('btnResolveMaps');
    const csrfToken = '{{ csrf_token() }}';
    const resolveMapsUrl = '{{ route('admin.maps.resolve') }}';
        function parseLatLngFromUrl(url){
            if(!url) return null;
            try {
                const decoded = decodeURIComponent(url);
                let m;
                // Pattern @lat,lng (standard map share URL)
                m = decoded.match(/@(-?\d+\.\d+),\s*(-?\d+\.\d+)(?:,[0-9a-zA-Z.]+)?/);
                if(m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
                // q=lat,lng
                m = decoded.match(/[?&]q=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/);
                if(m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
                // ll=lat,lng
                m = decoded.match(/[?&]ll=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/);
                if(m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
                // center=lat,lng
                m = decoded.match(/[?&]center=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/);
                if(m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
                // !3dLAT!4dLNG pattern in some Google place links
                const m3d = decoded.match(/!3d(-?\d+\.\d+)/);
                const m4d = decoded.match(/!4d(-?\d+\.\d+)/);
                if(m3d && m4d) return { lat: parseFloat(m3d[1]), lng: parseFloat(m4d[1]) };
                // /place/lat,lng
                m = decoded.match(/\/place\/\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/);
                if(m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
                // raw "lat,lng"
                m = decoded.trim().match(/^\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)\s*$/);
                if(m) return { lat: parseFloat(m[1]), lng: parseFloat(m[2]) };
                // Generic fallback: first two decimal numbers that look like coordinates
                const allNums = decoded.match(/-?\d+\.\d+/g) || [];
                if(allNums.length >= 2){
                    const lat = parseFloat(allNums[0]);
                    const lng = parseFloat(allNums[1]);
                    if(Math.abs(lat) <= 90 && Math.abs(lng) <= 180) return { lat, lng };
                }
            } catch(_) {}
            return null;
        }
        function ensureMap(){
            if(!mapsPreview) return;
            if(!leafletMap){
                leafletMap = L.map(mapsPreview).setView([ -6.200, 106.816 ], 12);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(leafletMap);
            }
            setTimeout(() => leafletMap.invalidateSize(), 50);
        }
        function showMap(lat, lng){
            if(!mapsPreview) return;
            mapsPreview.style.display = 'block';
            ensureMap();
            const pos = [lat, lng];
            leafletMap.setView(pos, 14);
            if(leafletMarker){ leafletMarker.setLatLng(pos); }
            else { leafletMarker = L.marker(pos).addTo(leafletMap); }
            const latInp = document.getElementById('latitude');
            const lngInp = document.getElementById('longitude');
            if(latInp) latInp.value = (+lat).toFixed(7);
            if(lngInp) lngInp.value = (+lng).toFixed(7);
        }
        function tryRenderMap(){
            const v = mapsInput?.value || '';
            const p = parseLatLngFromUrl(v);
            if(p){ showMap(p.lat, p.lng); }
            else if(mapsPreview){ mapsPreview.style.display = 'none'; }
        }
        // Mutually disable Maps vs Zoom link: choosing one disables the other
        function syncOnlineOfflineInputs(){
            const hasMaps = !!(mapsInput && mapsInput.value.trim());
            const hasZoom = !!(zoomInput && zoomInput.value.trim());
            if (hasMaps) {
                if (zoomInput) zoomInput.disabled = true;
                if (mapsInput) mapsInput.disabled = false;
                if (btnResolveMaps) btnResolveMaps.disabled = false;
            } else if (hasZoom) {
                if (mapsInput) mapsInput.disabled = true;
                if (btnResolveMaps) btnResolveMaps.disabled = true;
                if (mapsPreview) mapsPreview.style.display = 'none';
                if (zoomInput) zoomInput.disabled = false;
            } else {
                if (mapsInput) mapsInput.disabled = false;
                if (zoomInput) zoomInput.disabled = false;
                if (btnResolveMaps) btnResolveMaps.disabled = false;
            }
        }
        if(mapsInput){
            mapsInput.addEventListener('input', () => { tryRenderMap(); syncOnlineOfflineInputs(); });
            mapsInput.addEventListener('change', () => { tryRenderMap(); syncOnlineOfflineInputs(); });
            mapsInput.addEventListener('blur', () => { tryRenderMap(); syncOnlineOfflineInputs(); });
            // initial (old() values)
            tryRenderMap();
        }
        if(zoomInput){
            ['input','change','blur'].forEach(ev => zoomInput.addEventListener(ev, syncOnlineOfflineInputs));
        }
        // Initial mutual-disable state
        syncOnlineOfflineInputs();
        if(btnResolveMaps){
            btnResolveMaps.addEventListener('click', async () => {
                const url = mapsInput?.value || '';
                if(!url){ alert('Masukkan link Google Maps terlebih dahulu.'); return; }
                try{
                    btnResolveMaps.disabled = true;
                    const resp = await fetch(resolveMapsUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ url })
                    });
                    const data = await resp.json();
                    if(resp.ok && data.lat && data.lng){
                        showMap(parseFloat(data.lat), parseFloat(data.lng));
                    }else{
                        alert(data.message || 'Koordinat tidak ditemukan dari link.');
                    }
                }catch(err){
                    alert('Gagal mendeteksi koordinat.');
                }finally{
                    btnResolveMaps.disabled = false;
                }
            });
        }
        // fix map size when modal is shown
        const addEventModalEl = document.getElementById('addEventModal');
        if(addEventModalEl){
            addEventModalEl.addEventListener('shown.bs.modal', () => {
                if(leafletMap) setTimeout(() => leafletMap.invalidateSize(), 50);
            });
        }

        // Make modals draggable by mouse (header drag)
        function enableDraggableModal(modalEl){
            if(!modalEl) return;
            const dialog = modalEl.querySelector('.modal-dialog');
            const header = modalEl.querySelector('.modal-header');
            if(!dialog || !header) return;
            modalEl.classList.add('modal-draggable');
            let startX = 0, startY = 0, startLeft = 0, startTop = 0, dragging = false;

            function onMouseDown(e){
                if(e.button !== 0) return; // only left click
                const rect = dialog.getBoundingClientRect();
                startX = e.clientX;
                startY = e.clientY;
                startLeft = rect.left;
                startTop = rect.top;
                dialog.style.position = 'fixed';
                dialog.style.margin = '0';
                dialog.style.width = rect.width + 'px';
                dialog.style.left = startLeft + 'px';
                dialog.style.top = startTop + 'px';
                dragging = true;
                document.addEventListener('mousemove', onMouseMove);
                document.addEventListener('mouseup', onMouseUp);
                e.preventDefault();
            }
            function onMouseMove(e){
                if(!dragging) return;
                let newLeft = startLeft + (e.clientX - startX);
                let newTop = startTop + (e.clientY - startY);
                const maxLeft = Math.max(0, window.innerWidth - dialog.offsetWidth);
                const maxTop = Math.max(0, window.innerHeight - dialog.offsetHeight);
                if(newLeft < 0) newLeft = 0;
                if(newTop < 0) newTop = 0;
                if(newLeft > maxLeft) newLeft = maxLeft;
                if(newTop > maxTop) newTop = maxTop;
                dialog.style.left = newLeft + 'px';
                dialog.style.top = newTop + 'px';
            }
            function onMouseUp(){
                dragging = false;
                document.removeEventListener('mousemove', onMouseMove);
                document.removeEventListener('mouseup', onMouseUp);
            }
            header.addEventListener('mousedown', onMouseDown);

            // Initialize a reasonable position when shown
            modalEl.addEventListener('shown.bs.modal', () => {
                const rect = dialog.getBoundingClientRect();
                dialog.style.position = 'fixed';
                dialog.style.margin = '0';
                dialog.style.width = rect.width + 'px';
                dialog.style.left = Math.max(0, (window.innerWidth - rect.width) / 2) + 'px';
                dialog.style.top = Math.max(10, (window.innerHeight - rect.height) / 4) + 'px';
            });
            // Cleanup style when hidden
            modalEl.addEventListener('hidden.bs.modal', () => {
                dialog.style.position = '';
                dialog.style.margin = '';
                dialog.style.width = '';
                dialog.style.left = '';
                dialog.style.top = '';
            });
        }
        // Apply to Add Event modal
        if(addEventModalEl){ enableDraggableModal(addEventModalEl); }
        // Apply to all per-event document modals
        document.querySelectorAll('.modal-upload-operasional').forEach(m => enableDraggableModal(m));

        // Speakers (dynamic) - first required, others optional
        const speakersContainer = document.getElementById('speakersContainer');
        const addSpeakerBtn = document.getElementById('addSpeakerRow');
        function updateSpeakerRowsState(){
            if(!speakersContainer) return;
            const rows = speakersContainer.querySelectorAll('.speaker-row');
            rows.forEach((row, idx) => {
                const inp = row.querySelector('input[name="speakers[]"]');
                const rm  = row.querySelector('.remove-speaker');
                if(inp){ inp.required = (idx === 0); }
                if(rm){ rm.disabled = (idx === 0); }
            });
        }
        function addSpeakerRow(prefill=''){
            if(!speakersContainer) return;
            const div = document.createElement('div');
            div.className = 'input-group speaker-row';
            const safeVal = prefill ? prefill.replace(/"/g, '&quot;') : '';
            div.innerHTML = `
                <input type="text" name="speakers[]" class="form-control" placeholder="Nama pembicara" value="${safeVal}">
                <button type="button" class="btn btn-outline-danger remove-speaker" title="Hapus">&times;</button>
            `;
            speakersContainer.appendChild(div);
            updateSpeakerRowsState();
        }
        if(speakersContainer){
            speakersContainer.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-speaker');
                if(btn){
                    const row = btn.closest('.speaker-row');
                    if(row){ row.remove(); updateSpeakerRowsState(); }
                }
            });
        }
        if(addSpeakerBtn){ addSpeakerBtn.addEventListener('click', () => addSpeakerRow()); }
        // Ensure initial state correct (server-rendered rows)
        updateSpeakerRowsState();

        // Benefits (dynamic list -> serialized to hidden 'benefit')
        const benefitsContainer = document.getElementById('benefitsContainer');
        const addBenefitBtn = document.getElementById('addBenefitRow');
        const benefitHidden = document.getElementById('benefit');
        function addBenefitRow(prefill = ''){
            if(!benefitsContainer) return;
            const row = document.createElement('div');
            row.className = 'input-group benefit-row';
            const safeVal = prefill ? String(prefill).replace(/"/g, '&quot;') : '';
            row.innerHTML = `
                <span class="input-group-text"><i class="bi bi-check2"></i></span>
                <input type="text" class="form-control" name="benefits[]" placeholder="Contoh: Sertifikat peserta" value="${safeVal}">
                <button type="button" class="btn btn-outline-danger remove-benefit" title="Hapus">&times;</button>
            `;
            benefitsContainer.appendChild(row);
        }
        function parseInitialBenefits(){
            const raw = (benefitHidden?.value || '').trim();
            if(!raw) return [];
            // Prefer '|' separated; fallback to newline
            const parts = raw.includes('|') ? raw.split('|') : raw.split(/\r?\n/);
            return parts.map(p => (p || '').trim()).filter(Boolean);
        }
        if(benefitsContainer){
            benefitsContainer.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-benefit');
                if(btn){
                    const row = btn.closest('.benefit-row');
                    if(row){ row.remove(); }
                }
            });
            if(addBenefitBtn){ addBenefitBtn.addEventListener('click', () => addBenefitRow()); }
            // Initialize from old('benefit') value if present
            const initial = parseInitialBenefits();
            if(initial.length){ initial.forEach(val => addBenefitRow(val)); }
            else { addBenefitRow(''); }
        }

        // Dynamic status harga badge with formatted thousands
        const hargaHidden = document.getElementById('harga'); // hidden numeric
        const hargaDisplay = document.getElementById('hargaDisplay'); // visible formatted
        const statusHarga = document.getElementById('statusHarga');
        function unformatNumber(str){ return parseInt(String(str).replace(/\D/g,'')||'0',10); }
        function formatThousands(num){
            const n = Math.max(0, parseInt(num||0,10));
            return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.');
        }
        if (hargaDisplay && hargaHidden && statusHarga) {
            const updateStatus = () => {
                const val = unformatNumber(hargaDisplay.value);
                hargaHidden.value = val; // keep hidden in sync
                hargaDisplay.value = formatThousands(val); // ensure formatting
                statusHarga.textContent = val === 0 ? 'Gratis' : 'Berbayar';
                statusHarga.className = 'badge ' + (val === 0 ? 'bg-success' : 'bg-primary');

                // Disable discount fields when price is 0
                const diskonInput = document.getElementById('diskon');
                const discountUntilInput = document.getElementById('discount_until');
                if(diskonInput){
                    if(val === 0){
                        diskonInput.value = 0;
                        diskonInput.disabled = true;
                    } else {
                        diskonInput.disabled = false; // re-enable; further logic handled by its own toggle
                    }
                }
                if(discountUntilInput){
                    const fpInst = discountUntilInput._flatpickr;
                    if(val === 0){
                        discountUntilInput.disabled = true;
                        discountUntilInput.value = '';
                        if(fpInst){
                            fpInst.clear();
                            if(fpInst.altInput) fpInst.altInput.disabled = true;
                        }
                    } else {
                        // Re-enable base input; actual enable depends on diskon > 0
                        if(fpInst && fpInst.altInput){
                            // altInput stays disabled until diskon > 0; will be toggled elsewhere
                            fpInst.altInput.disabled = parseInt((diskonInput?.value)||'0',10) === 0;
                        }
                        discountUntilInput.disabled = parseInt((diskonInput?.value)||'0',10) === 0;
                    }
                }
            };
            // Block non digit (except control keys) and live format
            hargaDisplay.addEventListener('keydown', (e) => {
                const allowed = ['Backspace','Tab','ArrowLeft','ArrowRight','Delete','Home','End'];
                if(allowed.includes(e.key)) return;
                if(!/\d/.test(e.key)) e.preventDefault();
            });
            hargaDisplay.addEventListener('input', () => {
                const raw = unformatNumber(hargaDisplay.value);
                hargaDisplay.value = formatThousands(raw);
                updateStatus();
            });
            updateStatus();
        }

        // Enable/disable discount_until based on diskon value
        const diskonInput = document.getElementById('diskon');
        const discountUntilInput = document.getElementById('discount_until');
        let discountUntilFp = null; // flatpickr instance
        if(window.flatpickr){
            discountUntilFp = flatpickr('#discount_until', {
                locale:'id',
                dateFormat:'Y-m-d',
                altInput:true,
                altFormat:'l, j F Y',
                disableMobile:true,
                clickOpens:true
            });
        }
        function updateDiscountUntilBounds(){
            const eventDateStr = document.getElementById('tanggal')?.value;
            if(!eventDateStr || !discountUntilFp) return;
            const eventDate = new Date(eventDateStr + 'T00:00:00');
            if(isNaN(eventDate.getTime())) return;
            // Max is day before event
            const maxDate = new Date(eventDate.getTime() - 24*60*60*1000);
            const today = new Date(); today.setHours(0,0,0,0);
            // If maxDate < today, disable discount field entirely
            if(maxDate < today){
                discountUntilInput.disabled = true;
                if(discountUntilFp && discountUntilFp.altInput){ discountUntilFp.altInput.disabled = true; }
                discountUntilInput.value = '';
                discountUntilFp.clear();
                return;
            }
            discountUntilFp.set('minDate', today);
            discountUntilFp.set('maxDate', maxDate);
            // Clear if current selected >= eventDate or empty
            const current = discountUntilInput.value;
            if(current){
                const curDate = new Date(current + 'T00:00:00');
                if(curDate >= eventDate){
                    discountUntilFp.clear();
                    discountUntilInput.value='';
                }
            }
        }
        const eventDateEl = document.getElementById('tanggal');
        if(eventDateEl){ ['change','input'].forEach(ev=>eventDateEl.addEventListener(ev, updateDiscountUntilBounds)); }
        if (diskonInput && discountUntilInput) {
            const toggleDiscountUntil = () => {
                const perc = parseInt(diskonInput.value || '0', 10);
                const enable = perc > 0;
                discountUntilInput.disabled = !enable;
                if(discountUntilFp && discountUntilFp.altInput){
                    discountUntilFp.altInput.disabled = !enable;
                }
                if (!enable) {
                    // Clear value when disabled to avoid stale dates
                    discountUntilInput.value = '';
                    discountUntilFp && discountUntilFp.clear();
                } else {
                    updateDiscountUntilBounds();
                }
            };
            // Block minus input; clamp value to [0, 100]
            diskonInput.addEventListener('keydown', (e) => {
                if (e.key === '-' || e.key === 'Subtract' || e.keyCode === 189) e.preventDefault();
            });
            diskonInput.addEventListener('input', () => {
                let p = parseInt(diskonInput.value || '0', 10);
                if (isNaN(p) || p < 0) p = 0;
                if (p > 100) p = 100;
                diskonInput.value = p;
                toggleDiscountUntil();
            });
            // Initialize state on load (covers old() values after validation errors)
            toggleDiscountUntil();
            // Initial bounds if already has event date
            updateDiscountUntilBounds();
        }

        // Dynamic Schedule rows
        const scheduleTableBody = document.querySelector('#scheduleTable tbody');
        const addScheduleBtn = document.getElementById('addScheduleRow');
        let scheduleIndex = 0;
        function createScheduleRow(idx) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="time" class="form-control form-control-sm" name="schedule[${idx}][start]" /></td>
                <td><input type="time" class="form-control form-control-sm" name="schedule[${idx}][end]" /></td>
                <td><input type="text" class="form-control form-control-sm" name="schedule[${idx}][title]" placeholder="Nama kegiatan" /></td>
                <td><input type="text" class="form-control form-control-sm" name="schedule[${idx}][description]" placeholder="Deskripsi singkat" /></td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-action="remove" title="Hapus">
                        <i class="bi bi-x"></i>
                    </button>
                </td>`;
            return tr;
        }
        function addScheduleRow() {
            const row = createScheduleRow(scheduleIndex++);
            scheduleTableBody.appendChild(row);
        }
        if (addScheduleBtn && scheduleTableBody) {
            addScheduleBtn.addEventListener('click', addScheduleRow);
            scheduleTableBody.addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-action="remove"]');
                if (btn) {
                    const tr = btn.closest('tr');
                    tr.remove();
                }
            });
            // Initial one row
            addScheduleRow();
        }

        // Pengeluaran (Expenses) dynamic rows and totals
        const expensesTableBody = document.querySelector('#expensesTable tbody');
        const addExpenseBtn = document.getElementById('addExpenseRow');
        const expensesGrandTotalEl = document.getElementById('expensesGrandTotal');
        let expenseIndex = 0;

        function clampNonNegativeNumberInput(input, step = 1) {
            input.addEventListener('keydown', (e) => {
                if (e.key === '-' || e.key === 'Subtract' || e.keyCode === 189) e.preventDefault();
            });
            input.addEventListener('input', () => {
                // allow empty during typing
                if (input.value === '') return;
                let v = parseFloat(input.value);
                if (isNaN(v) || v < 0) v = 0;
                if (step >= 1) v = Math.floor(v);
                input.value = v.toString();
            });
        }

        function formatRupiah(n) {
            const v = Math.max(0, Math.floor(n || 0));
            return 'Rp' + v.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        function recalcExpensesGrandTotal() {
            let total = 0;
            expensesTableBody.querySelectorAll('input[data-expense-total]')?.forEach(inp => {
                const val = parseFloat(inp.value || '0');
                if (!isNaN(val)) total += val;
            });
            if (expensesGrandTotalEl) expensesGrandTotalEl.textContent = formatRupiah(total);
        }

        function recalcExpenseRow(tr) {
            const qty = parseFloat(tr.querySelector('input[data-expense-qty]')?.value || '0');
            const unit = parseFloat(tr.querySelector('input[data-expense-unit]')?.value || '0');
            const totalInput = tr.querySelector('input[data-expense-total]');
            const total = (isNaN(qty) ? 0 : qty) * (isNaN(unit) ? 0 : unit);
            if (totalInput) totalInput.value = Math.max(0, Math.round(total));
            recalcExpensesGrandTotal();
        }

        function createExpenseRow(idx) {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" name="expenses[${idx}][item]" placeholder="Nama barang" /></td>
                <td><input type="number" class="form-control form-control-sm" name="expenses[${idx}][quantity]" data-expense-qty min="0" step="1" value="" /></td>
                <td><input type="number" class="form-control form-control-sm" name="expenses[${idx}][unit_price]" data-expense-unit min="0" step="1" value="" /></td>
                <td><input type="number" class="form-control form-control-sm" name="expenses[${idx}][total]" data-expense-total readonly value="0" /></td>
                <td class="text-center">
                    <button type="button" class="btn btn-outline-danger btn-sm" data-action="remove-expense" title="Hapus">
                        <i class="bi bi-trash3"></i>
                    </button>
                </td>`;

            // attach clamping and recalc listeners
            const qtyInput = tr.querySelector('input[data-expense-qty]');
            const unitInput = tr.querySelector('input[data-expense-unit]');
            if (qtyInput) {
                clampNonNegativeNumberInput(qtyInput, 1);
                qtyInput.addEventListener('input', () => recalcExpenseRow(tr));
            }
            if (unitInput) {
                // Allow any integer price (e.g., 23333, 23500, etc.)
                clampNonNegativeNumberInput(unitInput, 1);
                unitInput.addEventListener('input', () => recalcExpenseRow(tr));
            }

            return tr;
        }

        function addExpenseRow() {
            const row = createExpenseRow(expenseIndex++);
            expensesTableBody.appendChild(row);
            recalcExpenseRow(row);
        }

        if (addExpenseBtn && expensesTableBody) {
            addExpenseBtn.addEventListener('click', addExpenseRow);
            expensesTableBody.addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-action="remove-expense"]');
                if (btn) {
                    const tr = btn.closest('tr');
                    tr.remove();
                    recalcExpensesGrandTotal();
                }
            });
            // initial one row
            addExpenseRow();
        }

        // Sync editors + basic required validation
        const form = document.getElementById('eventForm');
        if (form) {
            form.addEventListener('submit', function(ev) {
                if (window.editorDeskripsi) document.getElementById('deskripsi').value = window.editorDeskripsi.getData();
                if (window.editorTerms) document.getElementById('terms').value = window.editorTerms.getData();
                // Build combined speaker string for backward compatibility
                const speakerCombined = document.getElementById('speakerCombined');
                if (speakerCombined && speakersContainer){
                    const names = Array.from(speakersContainer.querySelectorAll('input[name="speakers[]"]'))
                        .map(i => (i.value || '').trim()).filter(Boolean);
                    speakerCombined.value = names.join(', ');
                }
                // Serialize benefits list to hidden field using ' | ' separator
                if(benefitHidden && benefitsContainer){
                    const items = Array.from(benefitsContainer.querySelectorAll('input[name="benefits[]"]'))
                        .map(i => (i.value || '').trim()).filter(Boolean);
                    benefitHidden.value = items.join(' | ');
                }
                // Ensure hidden price numeric sync before submit
                if(hargaDisplay && hargaHidden){
                    hargaHidden.value = unformatNumber(hargaDisplay.value);
                }
                let ok = true;
                form.querySelectorAll('[required]').forEach(f => {
                    if (!f.value.trim()) { f.classList.add('border-danger'); ok = false; }
                    else { f.classList.remove('border-danger'); }
                });
                // Validate short description max 40 words
                const shortDescEl = document.getElementById('short_desc');
                if(shortDescEl){
                    const words = (shortDescEl.value || '').trim().split(/\s+/).filter(Boolean);
                    if(words.length > 40){
                        ok = false;
                        shortDescEl.classList.add('border-danger');
                        alert('Penjelasan singkat maksimal 40 kata. Saat ini: ' + words.length + ' kata.');
                    } else {
                        shortDescEl.classList.remove('border-danger');
                    }
                }
                if (!ok) {
                    // Build list of missing required fields for clearer feedback
                    const requiredSet = Array.from(form.querySelectorAll('[required]'));
                    const fieldFriendlyName = (el) => {
                        if(!el) return 'Field';
                        const id = el.id || '';
                        const name = el.name || '';
                        if(id === 'gambar' || name === 'image') return 'Gambar Event';
                        if(id === 'nama' || name === 'title') return 'Nama Event';
                        if(name === 'speakers[]') return 'Nama Pembicara (minimal 1)';
                        if(id === 'level' || name === 'level') return 'Level';
                        if(id === 'short_desc' || name === 'short_description') return 'Penjelasan Singkat';
                        if(id === 'deskripsi' || name === 'description') return 'Deskripsi Event';
                        if(id === 'tanggal' || name === 'event_date') return 'Tanggal';
                        if(id === 'masuk1' || name === 'event_time') return 'Waktu Mulai';
                        if(id === 'lokasi' || name === 'location') return 'Lokasi';
                        if(id === 'harga' || name === 'price') return 'Harga';
                        return id || name || 'Field';
                    };
                    const missingList = requiredSet.filter(f => !(f.value || '').trim()).map(fieldFriendlyName);
                    ev.preventDefault();
                    alert('Lengkapi semua field wajib.\nYang belum: ' + missingList.join(', '));
                }
                // ensure expense totals up-to-date before submit
                expensesTableBody?.querySelectorAll('tr').forEach(tr => recalcExpenseRow(tr));
                console.log('[EventForm] Submit attempted. Valid:', ok);
            });

            // Live enable/disable submit button based on required fields
            const submitBtn = document.getElementById('submitBtn');
            const submitHint = document.getElementById('submitHint');
            const requiredFields = Array.from(form.querySelectorAll('[required]'));
            // Friendly name helper (shared with submit alert logic above)
            const fieldFriendlyName = (el) => {
                if(!el) return 'Field';
                const id = el.id || '';
                const name = el.name || '';
                if(id === 'gambar' || name === 'image') return 'Gambar Event';
                if(id === 'nama' || name === 'title') return 'Nama Event';
                if(name === 'speakers[]') return 'Nama Pembicara (minimal 1)';
                if(id === 'level' || name === 'level') return 'Level';
                if(id === 'short_desc' || name === 'short_description') return 'Penjelasan Singkat';
                if(id === 'deskripsi' || name === 'description') return 'Deskripsi Event';
                if(id === 'tanggal' || name === 'event_date') return 'Tanggal';
                if(id === 'masuk1' || name === 'event_time') return 'Waktu Mulai';
                if(id === 'lokasi' || name === 'location') return 'Lokasi';
                if(id === 'harga' || name === 'price') return 'Harga';
                return id || name || 'Field';
            };
            function missingRequired(){
                return requiredFields.filter(f => !(f.value || '').trim());
            }
            function allRequiredFilled(){
                return missingRequired().length === 0;
            }
            // expose globally so CKEditor init can call it even if defined later
            window.updateSubmitState = function updateSubmitState(){
                const filled = allRequiredFilled();
                // Extra rule: short description must be <= 40 words
                const sdEl = document.getElementById('short_desc');
                const sdWords = sdEl ? (sdEl.value || '').trim().split(/\s+/).filter(Boolean).length : 0;
                const overLimit = sdEl ? sdWords > 40 : false;
                if(submitBtn){ submitBtn.disabled = (!filled || overLimit); }
                if(submitHint){
                    if(!filled){
                        const missingList = missingRequired().map(fieldFriendlyName);
                        submitHint.textContent = 'Lengkapi: ' + missingList.join(', ');
                        submitHint.style.display = 'block';
                    } else if(overLimit){
                        submitHint.textContent = 'Penjelasan singkat maksimal 40 kata (saat ini ' + sdWords + ').';
                        submitHint.style.display = 'block';
                    } else {
                        submitHint.style.display = 'none';
                    }
                }
            }
            // Observe input/change events
            requiredFields.forEach(f => {
                ['input','change','blur'].forEach(evName => f.addEventListener(evName, updateSubmitState));
            });
            // If CKEditor was created earlier, bind now; otherwise it will bind in its own init then-callback
            if(window.editorDeskripsi){
                const ta = document.getElementById('deskripsi');
                if (ta) { ta.value = window.editorDeskripsi.getData(); }
                window.editorDeskripsi.model.document.on('change:data', () => {
                    if (ta) { ta.value = window.editorDeskripsi.getData(); }
                    updateSubmitState();
                });
            }
            // Initial state
            updateSubmitState();

            // Live word count for short description (max 40 words)
            const shortDescEl = document.getElementById('short_desc');
            const shortDescCountEl = document.getElementById('shortDescCount');
            function updateShortDescCount(){
                if(!shortDescEl || !shortDescCountEl) return;
                const words = (shortDescEl.value || '').trim().split(/\s+/).filter(Boolean);
                shortDescCountEl.textContent = words.length;
                if(words.length > 40){
                    shortDescCountEl.classList.add('text-danger');
                } else {
                    shortDescCountEl.classList.remove('text-danger');
                }
            }
            ['input','change','blur'].forEach(evName => shortDescEl?.addEventListener(evName, () => { updateShortDescCount(); updateSubmitState(); }));
            updateShortDescCount();
        } else {
            console.warn('[EventForm] Form element not found.');
        }

        @if($errors->any())
            if (window.bootstrap) { new bootstrap.Modal(document.getElementById('addEventModal')).show(); }
        @endif

        // Flatpickr date picker initialization (Indonesian locale)
        if(window.flatpickr){
            flatpickr('#tanggal', {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'l, j F Y',
                minDate: 'today',
                disableMobile: true
            });
        }

        // Live preview for document uploads in each event modal
        function initDocUploadPreviews(){
            const inputs = document.querySelectorAll('.modal-upload-operasional input[type="file"][data-preview]');
            inputs.forEach(inp => {
                const sel = inp.getAttribute('data-preview');
                const container = sel ? document.querySelector(sel) : null;
                if(!container) return;
                inp.addEventListener('change', () => {
                    const f = inp.files && inp.files[0];
                    container.innerHTML = '';
                    if(!f) return;
                    // wrapper
                    const wrap = document.createElement('div');
                    wrap.className = 'preview-removable';
                    // remove button
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-preview-btn';
                    removeBtn.setAttribute('data-bs-toggle','tooltip');
                    removeBtn.title = 'Batalkan pilihan';
                    removeBtn.innerHTML = '<i class="bi bi-x"></i>';
                    removeBtn.addEventListener('click', () => {
                        inp.value = '';
                        container.innerHTML = '';
                    });
                    wrap.appendChild(removeBtn);
                    const isImage = f.type && f.type.startsWith('image/');
                    const isPdf = (f.type === 'application/pdf') || (/\.pdf$/i.test(f.name || ''));
                    if(isImage){
                        const reader = new FileReader();
                        reader.onload = e => {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.alt = f.name || 'preview';
                            img.className = 'doc-thumb';
                            wrap.appendChild(img);
                            container.appendChild(wrap);
                        };
                        reader.readAsDataURL(f);
                    } else if(isPdf){
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-light text-dark border';
                        badge.innerHTML = '<i class="bi bi-filetype-pdf me-1"></i>' + (f.name || 'PDF');
                        wrap.appendChild(badge);
                        container.appendChild(wrap);
                    } else {
                        const txt = document.createElement('span');
                        txt.textContent = f.name || 'File terpilih';
                        wrap.appendChild(txt);
                        container.appendChild(wrap);
                    }
                });
            });
        }
        initDocUploadPreviews();

        // Toggle "Edit Upload" to reveal/hide file inputs when all docs complete
        document.querySelectorAll('[data-edit-doc-toggle]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.getAttribute('data-edit-doc-toggle');
                const wrap = document.getElementById('docEditWrapper-' + id);
                if(!wrap) return;
                const nowHidden = wrap.classList.toggle('d-none');
                // Swap button label/icon
                btn.innerHTML = nowHidden ? '<i class="bi bi-pencil-square me-1"></i>Edit Upload' : '<i class="bi bi-x-circle me-1"></i>Tutup';
            });
        });

        // Delete confirmation (modern modal)
        const deleteModalEl = document.getElementById('deleteEventModal');
        const deleteForm = document.getElementById('deleteEventFormGlobal');
        const deleteName = document.getElementById('deleteEventName');
        const deleteCheckbox = document.getElementById('deleteConfirmCheckbox');
        const deleteBtn = document.getElementById('deleteConfirmBtn');
        if(deleteModalEl){
            deleteModalEl.addEventListener('show.bs.modal', function (event) {
                const btn = event.relatedTarget;
                const url = btn?.getAttribute('data-url') || '';
                const title = btn?.getAttribute('data-title') || 'Event';
                const imgSrc = btn?.getAttribute('data-image') || '';
                const imgWrapper = document.getElementById('deleteEventImageWrapper');
                const imgEl = document.getElementById('deleteEventImage');
                if(deleteForm) deleteForm.setAttribute('action', url);
                if(deleteName) deleteName.textContent = title;
                if(deleteCheckbox) deleteCheckbox.checked = false;
                if(deleteBtn) deleteBtn.disabled = true;
                if(imgWrapper && imgEl){
                    if(imgSrc){
                        imgEl.src = imgSrc;
                        imgWrapper.style.display = 'block';
                    } else {
                        imgEl.src = '';
                        imgWrapper.style.display = 'none';
                    }
                }
            });
        }
        if(deleteCheckbox && deleteBtn){
            deleteCheckbox.addEventListener('change', () => { deleteBtn.disabled = !deleteCheckbox.checked; });
        }

        // Make event listing rows clickable (excluding interactive elements)
        document.querySelectorAll('table.table tbody tr[data-url]').forEach(tr => {
            tr.addEventListener('click', (e) => {
                const interactive = e.target.closest('a,button,input,textarea,select,label,.remove-speaker,[data-action="remove"],[data-action="remove-expense"]');
                if(interactive) return; // don't navigate when clicking interactive controls
                const url = tr.getAttribute('data-url');
                if(url){ window.location.href = url; }
            });
        });
    });
    </script>
    <script>
    // enable tooltips for doc status icons
    document.addEventListener('DOMContentLoaded', function() {
        if (window.bootstrap) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.forEach(el => new bootstrap.Tooltip(el));
        }
    });
    </script>
    @endsection

<!-- Global Delete Event Modal (modern) -->
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
                <div class="p-2 rounded border bg-light"><i class="bi bi-calendar-event me-1"></i> <strong id="deleteEventName">Event</strong></div>
                            <div id="deleteEventImageWrapper" class="mt-3" style="display:none;">
                                <img id="deleteEventImage" src="" alt="Gambar Event" class="img-fluid rounded shadow-sm" style="max-height:180px;object-fit:cover;">
                            </div>
                <div class="form-check mt-3">
                    <input class="form-check-input" type="checkbox" value="1" id="deleteConfirmCheckbox">
                    <label class="form-check-label" for="deleteConfirmCheckbox">Saya paham bahwa penghapusan bersifat permanen.</label>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-danger confirm-danger-btn" id="deleteConfirmBtn" form="deleteEventFormGlobal" disabled>
                    <i class="bi bi-trash me-1"></i> Hapus Permanen
                </button>
            </div>
        </div>
    </div>
</div>
<form id="deleteEventFormGlobal" action="#" method="POST" class="d-none">@csrf @method('DELETE')</form>