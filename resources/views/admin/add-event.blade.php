@extends('layouts.admin')
@section('title', 'Kelola Event')
@section('content')
<div class="container-fluid py-4">
        @if($errors->any())
            <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @if(session('statusFilter'))
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    try {
                        var sel = document.getElementById('statusFilter');
                        if (sel) {
                            sel.value = @json(session('statusFilter'));
                            sel.dispatchEvent(new Event('change'));
                        }
                    } catch (e) {}
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
            <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-md-start mb-3 gap-3">
            <div class="d-flex flex-wrap align-items-center gap-2">
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
                    <small class="text-muted fw-semibold mb-1">
                        Tipe Kelola
                        <i class="bi bi-info-circle-fill ms-1" role="button" tabindex="0"
                            aria-label="Info tipe kelola"
                            style="color: var(--bs-warning);"
                            data-bs-toggle="tooltip"
                            data-bs-placement="top"
                            data-bs-custom-class="tooltip-hint-yellow"
                            title="Manage: event yang dikelola (lanjutan/operasional). Create: event baru yang dibuat dari awal. Filter ini hanya untuk menyaring daftar event."></i>
                    </small>
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
            <div class="d-flex align-items-end gap-2 flex-shrink-0 ms-auto mt-2 mt-md-0">
                 <button type="button" class="export-event btn btn-danger" style="height:38px; min-width:110px;">
                     <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                 </button>
                 <button type="button" class="export-event btn btn-success" style="height:38px; min-width:120px;">
                     <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
                 </button>
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
                                <th>Reseller</th>
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
                                            <img src="{{ $event->image_url }}" alt="{{ $event->title }}" class="img-thumbnail" style="max-width:90px;height:60px;object-fit:cover;">
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
                                    @php
                                        $hasMapsLink = !empty($event->maps_url);
                                        $hasZoomLink = !empty($event->zoom_link);
                                    @endphp
                                    @if($hasMapsLink || $hasZoomLink)
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($hasMapsLink)
                                                <a href="{{ $event->maps_url }}" target="\_blank" class="btn btn-sm btn-outline-secondary" title="Buka Maps"><i class="bi bi-geo-alt"></i> Maps</a>
                                            @endif
                                            @if($hasZoomLink)
                                                <a href="{{ $event->zoom_link }}" target="\_blank" class="btn btn-sm btn-outline-primary" title="Buka Zoom"><i class="bi bi-camera-video"></i> Zoom</a>
                                            @endif
                                        </div>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @if((bool) ($event->is_reseller_event ?? false))
                                        <span class="badge bg-success">Ya</span>
                                    @else
                                        <span class="badge bg-secondary">Tidak</span>
                                    @endif
                                </td>
                                <td>
                                    @php 
                                        $pct = $event->documents_completion_percent; 
                                        $hasMapsLink = !empty($event->maps_url);
                                        $hasZoomLink = !empty($event->zoom_link);
                                        $isOfflineOnly = $hasMapsLink && !$hasZoomLink;
                                        $hasVbg = !empty($event->vbg_path);
                                        $eventTrainerModulesApproved = $event->approvedTrainerModules ?? collect();
                                        $hasModule = $eventTrainerModulesApproved->isNotEmpty() || !empty($event->module_path);
                                        // Absensi dianggap selesai jika ada file atau QR sudah aktif
                                        $hasAbsFile = !empty($event->attendance_path);
                                        $hasAbsQrImg = !empty($event->attendance_qr_image);
                                        $hasAbsQrToken = !empty($event->attendance_qr_token);
                                        $hasAbs = $hasAbsFile || $hasAbsQrImg || $hasAbsQrToken;
                                        // Tooltip ringkas
                                        $tooltip = $isOfflineOnly
                                            ? 'Module (Trainer): '.($hasModule ? '✔' : '✖').', Absensi (QR/File): '.($hasAbs ? '✔' : '✖')
                                            : 'Virtual Background: '.($hasVbg ? '✔' : '✖').', Module (Trainer): '.($hasModule ? '✔' : '✖').', Absensi (QR/File): '.($hasAbs ? '✔' : '✖');
                                        $pctClass = $pct === 100 ? 'doc-pct chip-success' : 'doc-pct chip-incomplete';
                                        $totalDisplay = $isOfflineOnly ? 2 : 3;
                                        // Tampilkan count UI (offline: tanpa VBG)
                                        $completedDisplay = ($isOfflineOnly ? 0 : ($hasVbg ? 1 : 0)) + ($hasModule ? 1 : 0) + ($hasAbs ? 1 : 0);
                                        // Determine missing items for publish confirmation
                                        $missing = [];
                                        if (!$isOfflineOnly && !$hasVbg) $missing[] = 'Virtual Background';
                                        if (!$hasModule) $missing[] = 'Module (Trainer)';
                                        if (!$hasAbs) $missing[] = 'Absensi';
                                    @endphp
                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                        <span class="{{ $pctClass }}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $tooltip }}">{{ $pct }}%</span>
                                        <div class="d-inline-flex align-items-center doc-info-actions gap-1">
                                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadOperasionalModal-{{ $event->id }}" title="Kelola Dokumen">
                                                <i class="bi bi-folder2-open"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1">{{ $completedDisplay }}/{{ $totalDisplay }} selesai</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm action-btn-group" role="group" aria-label="Aksi event {{ $event->title }}">
                                        @if(!(bool)($event->is_published ?? false))
                                            <form action="{{ route('admin.events.publish', $event) }}" method="POST" class="d-inline publish-form">
                                                @csrf
                                                <button type="button" class="btn btn-outline-success btn-action-icon publish-event-btn" data-doc-pct="{{ $pct }}" data-missing='@json($missing)' data-bs-toggle="tooltip" data-bs-placement="top" title="Terbitkan">
                                                    <i class="bi bi-megaphone"></i><span class="visually-hidden">Terbitkan</span>
                                                </button>
                                            </form>
                                        @else
                                            <form action="{{ route('admin.events.unpublish', $event) }}" method="POST" class="d-inline unpublish-form">
                                                @csrf
                                                <button type="button" class="btn btn-success btn-action-icon unpublish-event-btn" data-bs-toggle="tooltip" data-bs-placement="top" title="Batal Terbitkan">
                                                    <i class="bi bi-check2-circle"></i><span class="visually-hidden">Batal Terbitkan</span>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('admin.events.show',$event) }}" class="btn btn-outline-info btn-action-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="Lihat">
                                            <i class="bi bi-eye"></i><span class="visually-hidden">Lihat</span>
                                        </a>
                                        <a href="{{ route('admin.events.edit',$event) }}" class="btn btn-outline-warning btn-action-icon edit-event-btn" data-edit-url="{{ route('admin.events.edit',$event) }}" data-id="{{ $event->id }}" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit">
                                            <i class="bi bi-pencil-square"></i><span class="visually-hidden">Edit</span>
                                        </a>
                                        <button type="button" class="btn btn-outline-danger btn-action-icon"
                                            data-bs-toggle="modal" data-bs-target="#deleteEventModal"
                                            title="Hapus"
                                            data-url="{{ route('admin.events.destroy',$event) }}"
                                            data-title="{{ $event->title }}"
                                            data-image="{{ $event->image_url ?? '' }}">
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
                <div class="modal-upload-operasional modal fade" id="uploadOperasionalModal-{{ $event->id }}" tabindex="-1" aria-labelledby="uploadOperasionalLabel-{{ $event->id }}" aria-hidden="true" data-draggable="false">

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
                    return !invalid;                };
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

            // ===== EXPORT PDF & EXCEL =====
            // Guard: hanya inisialisasi sekali meski script dirender berkali-kali
            if (!window.__manageEventExportInitialized) {
                window.__manageEventExportInitialized = true;

            const exportBtns = document.querySelectorAll('.export-event');
            const exportPdfBtn  = exportBtns[0] ?? null;
            const exportExcelBtn = exportBtns[1] ?? null;

            function getFilterLabel(){
                const parts = [];
                const s = document.getElementById('eventSearch')?.value?.trim();
                if(s) parts.push('Cari: "' + s + '"');
                const st = document.getElementById('statusFilter')?.value;
                if(st && st !== 'all') parts.push('Status: ' + st);
                const mg = document.getElementById('manageFilter')?.value;
                if(mg && mg !== 'all') parts.push('Tipe: ' + mg);
                const mo = document.getElementById('eventMonthFilter')?.value;
                if(mo) {
                    const [y,m] = mo.split('-');
                    const label = new Date(+y, +m-1, 1).toLocaleDateString('id-ID',{month:'long',year:'numeric'});
                    parts.push('Bulan: ' + label);
                }
                return parts.length ? parts.join(' · ') : 'Semua Event';
            }

            function buildExportTable(){
                // Ambil hanya baris yang terlihat
                const visibleRows = rows.filter(r => r.style.display !== 'none');
                const headers = ['No', 'Judul', 'Pembicara', 'Tanggal', 'Lokasi', 'Tipe Kelola', 'Reseller', 'Kelengkapan'];

                const table = document.createElement('table');
                table.style.cssText = 'border-collapse:collapse; width:100%; font-size:10px; font-family:Arial,sans-serif; table-layout:fixed;';

                // Lebar kolom proporsional agar tidak overflow
                const colgroup = document.createElement('colgroup');
                ['4%','18%','14%','12%','14%','10%','10%','18%'].forEach(w => {
                    const col = document.createElement('col');
                    col.style.width = w;
                    colgroup.appendChild(col);
                });
                table.appendChild(colgroup);

                // Header
                const thead = table.createTHead();
                const hRow = thead.insertRow();
                headers.forEach(h => {
                    const th = document.createElement('th');
                    th.textContent = h;
                    th.style.cssText = 'background:#1e3a5f; color:#fff; padding:6px 7px; border:1px solid #1e3a5f; font-weight:600; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;';
                    hRow.appendChild(th);
                });

                // Body
                const tbody = table.createTBody();
                const tdBase = 'padding:5px 7px; border:1px solid #d1d5db; vertical-align:middle; overflow:hidden;';

                visibleRows.forEach((row, idx) => {
                    const cells = row.querySelectorAll('td');
                    const tr = tbody.insertRow();
                    tr.style.backgroundColor = idx % 2 === 0 ? '#ffffff' : '#f3f4f6';

                    // No
                    const tdNo = tr.insertCell();
                    tdNo.textContent = idx + 1;
                    tdNo.style.cssText = tdBase + 'text-align:center; white-space:nowrap;';

                    // Judul
                    const tdJudul = tr.insertCell();
                    tdJudul.textContent = cells[1]?.textContent?.trim() ?? '-';
                    tdJudul.style.cssText = tdBase + 'font-weight:600; word-break:break-word;';

                    // Pembicara
                    const tdPembicara = tr.insertCell();
                    tdPembicara.textContent = cells[3]?.textContent?.trim() ?? '-';
                    tdPembicara.style.cssText = tdBase + 'word-break:break-word;';

                    // Tanggal
                    const tdTanggal = tr.insertCell();
                    tdTanggal.textContent = cells[4]?.textContent?.trim() ?? '-';
                    tdTanggal.style.cssText = tdBase + 'white-space:nowrap;';

                    // Lokasi
                    const tdLokasi = tr.insertCell();
                    tdLokasi.textContent = cells[5]?.textContent?.trim() ?? '-';
                    tdLokasi.style.cssText = tdBase + 'word-break:break-word;';

                    // Tipe Kelola
                    const tdManage = tr.insertCell();
                    const manageVal = row.getAttribute('data-manage') || '-';
                    tdManage.textContent = manageVal === 'manage' ? 'Manage' : manageVal === 'create' ? 'Create' : manageVal;
                    tdManage.style.cssText = tdBase + 'text-align:center; white-space:nowrap;';

                    // Reseller
                    const tdReseller = tr.insertCell();
                    tdReseller.textContent = cells[7]?.textContent?.trim() ?? '-';
                    tdReseller.style.cssText = tdBase + 'text-align:center; white-space:nowrap;';

                    // Kelengkapan
                    const tdDoc = tr.insertCell();
                    const docSpan = cells[8]?.querySelector('.doc-pct');
                    const docSmall = cells[8]?.querySelector('small');
                    tdDoc.textContent = (docSpan?.textContent?.trim() ?? '') + ' ' + (docSmall?.textContent?.trim() ?? '');
                    tdDoc.style.cssText = tdBase + 'text-align:center; white-space:nowrap;';
                });

                return table;
            }

            // Export PDF
            exportPdfBtn && exportPdfBtn.addEventListener('click', function(){
                if(typeof window.html2pdf !== 'function'){
                    alert('Library html2pdf belum dimuat. Coba refresh halaman.');
                    return;
                }
                const filterLabel = getFilterLabel();
                const printDate = new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'});
                const table = buildExportTable();

                const printable = document.createElement('div');
                printable.style.cssText = 'width:880px; padding:16px 24px; background:#fff; color:#111827; font-family:Arial,sans-serif; box-sizing:border-box; font-size:10px; margin:0 auto;';
                printable.innerHTML = `
                    <div style="display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:14px; padding-bottom:10px; border-bottom:3px solid #1e3a5f;">
                        <div>
                            <div style="font-size:16px; font-weight:700; color:#1e3a5f;">Laporan Manage Event</div>
                            <div style="font-size:11px; color:#6b7280; margin-top:3px;">Filter: <strong style="color:#111827;">${filterLabel}</strong></div>
                        </div>
                        <div style="text-align:right; font-size:10px; color:#9ca3af; line-height:1.6;">
                            <div style="font-weight:600; color:#374151; font-size:12px;">LMS IdSpora</div>
                            <div>Dicetak: ${printDate}</div>
                        </div>
                    </div>`;
                printable.appendChild(table);

                const footer = document.createElement('div');
                footer.style.cssText = 'margin-top:12px; padding-top:8px; border-top:1px solid #e5e7eb; font-size:9px; color:#9ca3af; display:flex; justify-content:space-between;';
                footer.innerHTML = `<span>LMS IdSpora — Manage Event</span><span>${printDate}</span>`;
                printable.appendChild(footer);

                const offscreen = document.createElement('div');
                offscreen.style.cssText = 'position:fixed; left:0; top:-9999px; z-index:-1;';
                offscreen.appendChild(printable);
                document.body.appendChild(offscreen);

                window.html2pdf().set({
                    margin: [8, 14, 8, 14],
                    filename: 'manage-event-' + new Date().toISOString().slice(0,10) + '.pdf',
                    image: { type: 'jpeg', quality: 0.98 },
                    html2canvas: { scale: 2, windowWidth: 920, useCORS: true, logging: false, x: 0, y: 0 },
                    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
                }).from(printable).save().then(() => offscreen.remove()).catch(() => offscreen.remove());
            });

            // Export Excel
            exportExcelBtn && exportExcelBtn.addEventListener('click', function(){
                if(!window.XLSX){
                    alert('Library XLSX belum dimuat. Coba refresh halaman.');
                    return;
                }
                const filterLabel = getFilterLabel();
                const printDate = new Date().toLocaleDateString('id-ID',{day:'2-digit',month:'long',year:'numeric'});
                const visibleRows = rows.filter(r => r.style.display !== 'none');

                const headers = ['No', 'Judul', 'Pembicara', 'Tanggal', 'Lokasi', 'Tipe Kelola', 'Reseller', 'Kelengkapan'];
                const data = [
                    ['Laporan Manage Event'],
                    ['Filter: ' + filterLabel],
                    ['Dicetak: ' + printDate],
                    [],
                    headers,
                ];

                visibleRows.forEach((row, idx) => {
                    const cells = row.querySelectorAll('td');
                    const manageVal = row.getAttribute('data-manage') || '-';
                    const docSpan = cells[8]?.querySelector('.doc-pct');
                    const docSmall = cells[8]?.querySelector('small');
                    data.push([
                        idx + 1,
                        cells[1]?.textContent?.trim() ?? '-',
                        cells[3]?.textContent?.trim() ?? '-',
                        cells[4]?.textContent?.trim() ?? '-',
                        cells[5]?.textContent?.trim() ?? '-',
                        manageVal === 'manage' ? 'Manage' : manageVal === 'create' ? 'Create' : manageVal,
                        cells[7]?.textContent?.trim() ?? '-',
                        (docSpan?.textContent?.trim() ?? '') + ' ' + (docSmall?.textContent?.trim() ?? ''),
                    ]);
                });

                const wb = window.XLSX.utils.book_new();
                const ws = window.XLSX.utils.aoa_to_sheet(data);

                // Merge judul di baris pertama
                if(!ws['!merges']) ws['!merges'] = [];
                ws['!merges'].push({ s:{r:0,c:0}, e:{r:0,c:7} });

                // Lebar kolom
                ws['!cols'] = [
                    {wch:5}, {wch:30}, {wch:20}, {wch:15}, {wch:20},
                    {wch:12}, {wch:10}, {wch:15}
                ];

                window.XLSX.utils.book_append_sheet(wb, ws, 'Manage Event');
                window.XLSX.writeFile(wb, 'manage-event-' + new Date().toISOString().slice(0,10) + '.xlsx');
            });

            } // end guard __manageEventExportInitialized
        });
        </script>
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="content-operasional-view modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="uploadOperasionalLabel-{{ $event->id }}">Status Dokumen Detail</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-2">Tinjau status semua dokumen terkait acara dan administrasi.</p>
                                @php 
                                    $hasMapsLink = !empty($event->maps_url);
                                    $hasZoomLink = !empty($event->zoom_link);
                                    $isOfflineOnly = $hasMapsLink && !$hasZoomLink;
                                    $requiresVbg = !$isOfflineOnly;
                                    $hasVbg = !empty($event->vbg_path);
                                    $eventTrainerModulesApproved = $event->approvedTrainerModules()->with('trainer')->get();
                                    $hasModule = $eventTrainerModulesApproved->isNotEmpty() || !empty($event->module_path);
                                    // Absensi: selesai jika file atau QR aktif
                                    $hasAbsFile = !empty($event->attendance_path);
                                    $hasAbsQrImg = !empty($event->attendance_qr_image);
                                    $hasAbsQrToken = !empty($event->attendance_qr_token);
                                    $hasAbs = $hasAbsFile || $hasAbsQrImg || $hasAbsQrToken;
                                    $totalDisplay = $isOfflineOnly ? 2 : 3;
                                    $completedDisplay = ($isOfflineOnly ? 0 : ($hasVbg ? 1 : 0)) + ($hasModule ? 1 : 0) + ($hasAbs ? 1 : 0);
                                    $pct = $event->documents_completion_percent;
                                    $pctClass = $pct === 100 ? 'doc-pct chip-success' : 'doc-pct chip-incomplete';
                                @endphp
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="{{ $pctClass }}" title="Kelengkapan Dokumen">{{ $pct }}%</span>
                                    <small class="text-muted">{{ $completedDisplay }}/{{ $totalDisplay }} selesai</small>
                                </div>
                                <ul class="list-group list-group-flush mb-3 small">
                                    @if($requiresVbg)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi {{ $hasVbg ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i>
                                                Virtual Background
                                            </span>
                                            <span>
                                                @if($hasVbg)
                                                    @php $vExt = strtolower(pathinfo($event->vbg_path, PATHINFO_EXTENSION)); @endphp
                                                    @if(in_array($vExt, ['jpg','jpeg','png','gif','webp','bmp','svg']))
                                                        <a href="{{ $event->vbg_file_url }}" target="_blank" class="d-inline-block">
                                                            <img src="{{ $event->vbg_file_url }}" alt="VBG" class="rounded border" style="width:56px;height:36px;object-fit:cover;">
                                                        </a>
                                                    @elseif($vExt === 'pdf')
                                                        <a href="{{ $event->vbg_file_url }}" target="_blank" class="link-primary"><i class="bi bi-filetype-pdf me-1"></i>PDF</a>
                                                    @else
                                                        <a href="{{ $event->vbg_file_url }}" target="_blank" class="link-primary">Lihat</a>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Belum ada</span>
                                                @endif
                                            </span>
                                        </li>
                                    @endif
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <span>
                                            <i class="bi {{ $hasModule ? 'bi-check-circle text-success' : 'bi-x-circle text-danger' }} me-2"></i>
                                            Module (Trainer)
                                        </span>
                                        <span class="d-flex flex-column align-items-end gap-1">
                                            @if($eventTrainerModulesApproved->isNotEmpty())
                                                @foreach($eventTrainerModulesApproved as $etm)
                                                    <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($etm->path) }}" target="_blank" class="link-primary" style="font-size:0.82rem;">
                                                        <i class="bi bi-file-earmark-arrow-down me-1"></i>{{ \Illuminate\Support\Str::limit($etm->original_name, 25) }}
                                                        @if($etm->trainer)<span class="text-muted">({{ $etm->trainer->name }})</span>@endif
                                                    </a>
                                                @endforeach
                                            @elseif($hasModule)
                                                <a href="{{ $event->module_file_url }}" target="_blank" class="link-primary"><i class="bi bi-file-earmark-arrow-down me-1"></i>Unduh</a>
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
                                                @if($hasAbsFile)
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
                                                @elseif($hasAbsQrImg)
                                                    <a href="{{ $event->attendance_qr_image_url }}" target="_blank" class="d-inline-block">
                                                        <img src="{{ $event->attendance_qr_image_url }}" alt="QR Absensi" class="rounded border" style="width:56px;height:56px;object-fit:cover;">
                                                    </a>
                                                @else
                                                    <span class="badge bg-success">QR Absensi Aktif</span>
                                                @endif
                                            @else
                                                <span class="text-muted">Belum ada</span>
                                            @endif
                                        </span>
                                    </li>
                                </ul>

                                <form action="{{ route('admin.events.documents.upload', $event) }}" method="post" enctype="multipart/form-data" id="docForm-{{ $event->id }}">
                                    @csrf
                                    @php $adminDocsComplete = $isOfflineOnly ? ($hasAbs) : ($hasVbg && $hasAbs); @endphp
                                    @if($adminDocsComplete)
                                      
                                        <div class="doc-edit-wrapper d-none" id="docEditWrapper-{{ $event->id }}">
                                            @if($requiresVbg)
                                                <div class="box-up mb-3">
                                                    <label for="vbg-{{ $event->id }}" class="form-label">Virtual Background</label>
                                                    <input type="file" class="form-control" id="vbg-{{ $event->id }}" name="virtual_background" accept="image/*" data-preview="#vbg-preview-{{ $event->id }}">
                                                    <div id="vbg-preview-{{ $event->id }}" class="mt-2"></div>
                                                </div>
                                            @endif
                                            <!-- Absensi upload dihilangkan karena sudah gunakan QR -->
                                        </div>
                                    @else
                                        @if($requiresVbg)
                                            <div class="box-up mb-3">
                                                <label for="vbg-{{ $event->id }}" class="form-label">Virtual Background</label>
                                                <input type="file" class="form-control" id="vbg-{{ $event->id }}" name="virtual_background" accept="image/*" data-preview="#vbg-preview-{{ $event->id }}">
                                                <div id="vbg-preview-{{ $event->id }}" class="mt-2"></div>
                                            </div>
                                        @endif
                                        <!-- Absensi upload dihilangkan karena sudah gunakan QR -->
                                    @endif
                                </form>
                            </div>
                            <div class="modal-footer">
                                <div class="w-100 d-grid gap-2 d-sm-flex justify-content-end">
                                     <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary px-4" form="docForm-{{ $event->id }}">
                                        <span class="me-1">Save changes</span>
                                        <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="mt-3">{{ $events->links() }}</div>
            @else <div class="text-center py-5">Belum ada event.</div> @endif
        </div></div>
        <div class="modal fade" id="addEventModal" tabindex="-1" aria-labelledby="addEventModalLabel" aria-hidden="true" data-bs-focus="false" data-draggable="false" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-xl modal-dialog-scrollable"><div class="modal-content">
                <div class="modal-header"><h5 class="modal-title" id="addEventModalLabel"><i class="bi bi-calendar-plus me-2"></i>Tambah Event Baru</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">@csrf
                        <div class="text-danger small mb-3">
                            <strong>*</strong> Wajib diisi.
                        </div>
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
                                    <div class="form-text">Gunakan judul yang jelas dan spesifik (contoh: "Webinar Laravel Dasar").</div>
                                </div>
                                <!-- Pembicara (dynamic, minimal 1 required) -->
                                <div class="box-trainer-event">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nama Pembicara/Trainer <span class="text-danger">*</span></label>
                                    @php $oldSpeakers = old('speakers', []); $oldSalaries = old('speaker_salaries', []); @endphp
                                    <div id="speakersContainer" class="d-flex flex-column gap-2">
                                        @if(!empty($oldSpeakers))
                                            @foreach($oldSpeakers as $i => $sp)
                                            <div class="speaker-row border rounded p-2" style="background:#f8fafc;">
                                                <div class="d-flex gap-2 align-items-center">
                                                    <select name="speakers[]" class="form-select speaker-select" data-selected="{{ $sp }}" {{ $i === 0 ? 'required' : '' }}>
                                                        <option value="" disabled>Memuat pembicara...</option>
                                                        <option value="{{ $sp }}" selected>{{ $sp }}</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-danger remove-speaker" title="Hapus"><i class="bi bi-trash"></i></button>
                                                </div>
                                                <div class="mt-2">
                                                    <input type="number" name="speaker_salaries[]" class="form-control form-control-sm"
                                                        placeholder="Gaji Pembicara/Trainer (Rp)"
                                                        value="{{ $oldSalaries[$i] ?? '' }}" min="0" step="1000">
                                                </div>
                                            </div>
                                            @endforeach
                                        @else
                                            <div class="speaker-row border rounded p-2" style="background:#f8fafc;">
                                                <div class="d-flex gap-2 align-items-center">
                                                    <select name="speakers[]" class="form-select speaker-select" data-selected="" required>
                                                        <option value="" selected disabled>Pilih pembicara</option>
                                                    </select>
                                                    <button type="button" class="btn btn-outline-danger remove-speaker" title="Hapus"><i class="bi bi-trash"></i></button>
                                                </div>
                                                <div class="mt-2">
                                                    <input type="number" name="speaker_salaries[]" class="form-control form-control-sm"
                                                        placeholder="Masukkan Gaji Pembicara/Trainer" min="0" step="1000">
                                                    <div class="form-text">Isikan Gaji untuk Nama Pembicara/Trainer</div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="addSpeakerRow"><i class="bi bi-plus-circle me-1"></i>Tambah Nama Pembicara</button>
                                    <input type="hidden" name="speaker" id="speakerCombined" value="{{ old('speaker') }}">
                                    <div class="form-text">Minimal 1 pembicara (wajib). Tambahan pembicara bersifat opsional.</div>
                                </div>
                                </div>
                                <!-- Materi (kategori konten) -->
                                <div class="mb-3">
                                    <label for="materi" class="form-label fw-semibold">Materi <span class="text-danger">*</span></label>
                                    @php
                                        $materiDefaults = [
                                            'Web Programming','Mobile Programming','Fullstack Development','Backend Development','UI / UX','Product Management',
                                            'Frontend Development',
                                            'Quality Assurance','Digital Marketing','Cyber Security','Career Development','Tech Entrepreneur','Freelancer',
                                            'Content Creator','Academic Mentoring','Data','Dev Ops','Game Development','AI','Product Design','N8N','BPMN'
                                        ];
                                        $materiFromDb = isset($materiOptions) ? collect($materiOptions)->map(fn($v) => trim((string)$v))->filter()->all() : [];
                                        $materiMerged = collect(array_merge($materiDefaults, $materiFromDb))
                                            ->map(fn($v) => trim((string)$v))
                                            ->filter()
                                            ->unique(fn($v) => mb_strtolower($v))
                                            ->sortBy(fn($v) => mb_strtolower($v))
                                            ->values()
                                            ->all();
                                        $currentMateri = trim((string) old('materi'));
                                    @endphp
                                    <div class="position-relative">
                                        <input type="text" name="materi" id="materi" class="form-control" required
                                               value="{{ $currentMateri }}"
                                               placeholder="Klik untuk lihat daftar, lalu ketik untuk mencari"
                                               autocomplete="off">
                                        <div id="materiSuggestions"
                                             class="list-group position-absolute w-100 shadow-sm"
                                             style="display:none; z-index: 1066; max-height: 240px; overflow-y:auto;"></div>
                                    </div>
                                    <div class="form-text">Klik kolom untuk melihat daftar. Ketik untuk mencari.</div>
                                    <div id="materiInvalidText" class="text-danger small mt-1" style="display:none;">Tidak ada materi</div>
                                </div>
                                <!-- Kelola Event: Manage / Create -->
                                <div class="mb-3">
                                    <label for="manage_action" class="form-label fw-semibold">
                                        Kelola Event
                                        <i class="bi bi-info-circle-fill ms-1" role="button" tabindex="0"
                                            aria-label="Info kelola event"
                                            style="color: var(--bs-warning);"
                                            data-bs-toggle="tooltip"
                                            data-bs-placement="top"
                                            data-bs-custom-class="tooltip-hint-yellow"
                                            title="Manage: pilih jika event ini masuk kategori dikelola (operasional/lanjutan). Create: pilih jika event ini dibuat sebagai event baru dari awal."></i>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="manage_action" id="manage_action" class="form-select" required>
                                        <option value="" disabled {{ old('manage_action') ? '' : 'selected' }}>Pilih aksi</option>
                                        <option value="manage" {{ old('manage_action') === 'manage' ? 'selected' : '' }}>Manage</option>
                                        <option value="create" {{ old('manage_action') === 'create' ? 'selected' : '' }}>Create</option>
                                    </select>
                                    <div class="form-text">Manage: event operasional/lanjutan. Create: event baru dari awal.</div>
                                    <small id="manageActionHelp" class="text-danger" style="display:none">Lengkapi: pilih salah satu (Manage/Create) sebelum menyimpan.</small>
                                </div>

                                <!-- Reseller Event -->
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Reseller Event</label>
                                    
                                        <div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="is_reseller_event" id="reseller-event-yes" value="1" {{ old('is_reseller_event', 0) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="reseller-event-yes">Ya</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="is_reseller_event" id="reseller-event-no" value="0" {{ old('is_reseller_event', 0) ? '' : 'checked' }}>
                                                <label class="form-check-label" for="reseller-event-no">Tidak</label>
                                            </div>
                                        </div>
                                    <div class="form-text">Jika Ya, event ini akan muncul di Produk Komisi Reseller.</div>
                                </div>
                                <!-- Jenis Acara -->
                                <div class="mb-3">
                                    <label for="jenis" class="form-label fw-semibold">Jenis Acara <span class="text-danger">*</span></label>
                                    <div class="position-relative">
                                        @php
                                            $jenisDefaults = ['Webinar','Seminar','Workshop'];
                                            $jenisFromDb = isset($jenisOptions) ? collect($jenisOptions)->map(fn($v) => trim((string)$v))->filter()->all() : [];
                                            $jenisMerged = collect(array_merge($jenisDefaults, $jenisFromDb))->map(fn($v) => trim((string)$v))->filter()->unique(fn($v) => mb_strtolower($v))->values()->all();

                                            $currentJenis = trim((string) old('jenis', ''));
                                        @endphp
                                        <select name="jenis" id="jenis" class="form-select" required>
                                            <option value="" disabled {{ $currentJenis !== '' ? '' : 'selected' }}>Pilih jenis acara</option>
                                            @foreach($jenisMerged as $opt)
                                                @php $optStr = (string) $opt; @endphp
                                                <option value="{{ $optStr }}" {{ $currentJenis === $optStr ? 'selected' : '' }}>{{ $optStr }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-text">Pilih jenis acara untuk event ini.</div>
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
                                    <div class="form-text">Jelaskan detail event: topik, target peserta, agenda singkat, dan benefit.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="tanggal" class="form-label fw-semibold">Tanggal Pelaksanaan Event <span class="text-danger">*</span></label>
                                     <input type="date" name="event_date" id="tanggal" class="form-control" required
                                         value="{{ old('event_date') }}">
                                     <div class="form-text">Pilih tanggal pelaksanaan event.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Waktu Mulai & Selesai <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center gap-2">
                                        <input type="time" name="event_time" id="masuk1" class="form-control" required value="{{ old('event_time') }}">
                                        <span>s/d</span>
                                        <input type="time" name="event_time_end" id="masuk2" class="form-control" value="{{ old('event_time_end') }}">
                                    </div>
                                    <div class="form-text">Isi jam mulai (wajib). Jam selesai opsional.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="lokasi" class="form-label fw-semibold">Tipe Pelaksanaan <span class="text-danger">*</span></label>
                                    @php
                                        $oldMode = old('location_mode');
                                        $oldMaps = trim((string) old('maps_url', ''));
                                        $oldZoom = trim((string) old('zoom_link', ''));
                                        $defaultMode = 'offline';
                                        if ($oldMode) {
                                            $defaultMode = $oldMode;
                                        } elseif ($oldMaps !== '' && $oldZoom !== '') {
                                            $defaultMode = 'hybrid';
                                        } elseif ($oldZoom !== '') {
                                            $defaultMode = 'online';
                                        } elseif ($oldMaps !== '') {
                                            $defaultMode = 'offline';
                                        }
                                    @endphp
                                    <select name="location_mode" id="lokasi" class="form-select" required>
                                        <option value="" disabled {{ $defaultMode ? '' : 'selected' }}>Pilih lokasi</option>
                                        <option value="offline" {{ $defaultMode === 'offline' ? 'selected' : '' }}>Offline</option>
                                        <option value="online" {{ $defaultMode === 'online' ? 'selected' : '' }}>Online</option>
                                        <option value="hybrid" {{ $defaultMode === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                                    </select>
                                    <div class="form-text">Pilih tipe lokasi event. Field Maps/Zoom akan menyesuaikan.</div>
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
                                    <div class="form-text">Isi 0 jika tidak ada diskon.</div>
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
                                <div class="mb-3 d-none" id="placeNameGroup">
                                    <label for="place_name" class="form-label fw-semibold">Nama Tempat <span class="text-danger" id="placeNameRequiredStar" style="display:none">*</span></label>
                                    <input type="text" name="place_name" id="place_name" class="form-control" value="{{ old('place_name') }}" placeholder="Contoh: Hotel ABC / Aula Kampus / Gedung Serbaguna">
                                    <div class="form-text">Muncul setelah klik "Deteksi" untuk offline/hybrid.</div>
                                </div>
                                <div class="mb-3" id="mapsGroup">
                                    <label for="maps" class="form-label fw-semibold">Maps Lokasi (Offline/Hybrid) <span class="text-danger" id="mapsRequiredStar" style="display:none">*</span></label>
                                    <div class="input-group">
                                        <input type="text" name="maps_url" id="maps" class="form-control" value="{{ old('maps_url') }}" placeholder="Tempel link Google Maps (bisa short link maps.app.goo.gl)">
                                        <button class="btn btn-outline-secondary" type="button" id="btnResolveMaps">Deteksi</button>
                                    </div>
                                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
                                    <div id="mapsPreview" class="mt-2 rounded border" style="display:none;height:260px;"></div>
                                    <div class="form-text">Klik "Deteksi" untuk mencoba membaca koordinat dari short link Google Maps.</div>
                                </div>
                                <div class="mb-3" id="zoomGroup">
                                    <label for="zoom" class="form-label fw-semibold">Link Zoom (Online/Hybrid) <span class="text-danger" id="zoomRequiredStar" style="display:none">*</span></label>
                                    <input type="text" name="zoom_link" id="zoom" class="form-control" value="{{ old('zoom_link') }}" placeholder="Masukkan Link Zoom">
                                    <div class="form-text">Isi link meeting jika online/hybrid. Pastikan link bisa diakses.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="terms" class="form-label fw-semibold">Terms & Condition</label>
                                    <textarea name="terms_and_conditions" id="terms" class="form-control" rows="6">{{ old('terms_and_conditions') }}</textarea>
                                    <div class="form-text">Opsional. Tulis aturan/persyaratan peserta (refund, ketentuan sertifikat, dll).</div>
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
                                    <div class="form-text">Opsional. Tambahkan rundown/acara per sesi jika diperlukan.</div>
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
                                    <div class="form-text">Opsional. Catat biaya untuk kebutuhan laporan/operasional.</div>
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
                                    <li class="list-group-item">Jika event hybrid, isi Maps dan Zoom sekaligus.</li>
                                </ul>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <div class="me-auto small text-danger fw-semibold" id="submitHint" style="display:none;" aria-live="polite">
                        Lengkapi semua field bertanda * terlebih dahulu untuk mengaktifkan tombol Simpan.
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" form="eventForm" id="submitBtn">
                        <i class="bi bi-check-circle me-1"></i> Simpan Event
                    </button>
                </div>
            </div></div>
        </div>
        </div>

        <!-- Publish confirmation modal (global) -->
        <div class="modal fade" id="publishConfirmModal" tabindex="-1" aria-labelledby="publishConfirmModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="publishConfirmModalLabel">Konfirmasi Terbitkan Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body" id="publishConfirmModalBody"></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="publishConfirmBtn">Terbitkan</button>
              </div>
            </div>
          </div>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var publishModalEl = document.getElementById('publishConfirmModal');
            var publishModal = (publishModalEl && window.bootstrap && typeof bootstrap.Modal === 'function') ? new bootstrap.Modal(publishModalEl) : null;
            var pendingPublishForm = null;
            document.querySelectorAll('.publish-event-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var pct = parseInt(btn.getAttribute('data-doc-pct') || '0', 10);
                    var missingJson = btn.getAttribute('data-missing') || '[]';
                    var missing = [];
                    try { missing = JSON.parse(missingJson); } catch(e) { missing = []; }
                    var form = btn.closest('form');
                    var body = document.getElementById('publishConfirmModalBody');
                    var title = document.getElementById('publishConfirmModalLabel');
                    var confirmBtn = document.getElementById('publishConfirmBtn');
                    
                    if (title) title.textContent = 'Konfirmasi Terbitkan Event';
                    if (confirmBtn) {
                        confirmBtn.textContent = 'Terbitkan';
                        confirmBtn.classList.remove('btn-danger');
                        confirmBtn.classList.add('btn-primary');
                    }

                    if (pct >= 100) {
                        if (body) body.innerHTML = '<p>Apakah anda yakin ingin publish event ini? Dokumen sudah lengkap dan event akan segera tampil untuk publik.</p>';
                    } else {
                        if (body) {
                            var html = '<p>Kelengkapan dokumen event ini belum lengkap:</p><ul>';
                            if (Array.isArray(missing) && missing.length) {
                                missing.forEach(function(it){ html += '<li>' + it + '</li>'; });
                            } else {
                                html += '<li>Beberapa dokumen belum lengkap</li>';
                            }
                            html += '</ul><p>Apakah anda yakin ingin publish event ini?</p>';
                            body.innerHTML = html;
                        }
                    }
                    pendingPublishForm = form;
                    if (publishModal) publishModal.show();
                });
            });
            document.querySelectorAll('.unpublish-event-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    var form = btn.closest('form');
                    var body = document.getElementById('publishConfirmModalBody');
                    var title = document.getElementById('publishConfirmModalLabel');
                    var confirmBtn = document.getElementById('publishConfirmBtn');
                    
                    if (title) title.textContent = 'Konfirmasi Batal Terbitkan';
                    if (confirmBtn) {
                        confirmBtn.textContent = 'Batal Terbitkan';
                        confirmBtn.classList.remove('btn-primary');
                        confirmBtn.classList.add('btn-danger');
                    }
                    if (body) body.innerHTML = '<p>Apakah Anda yakin ingin membatalkan publikasi event ini? Event tidak akan terlihat lagi oleh publik.</p>';
                    
                    pendingPublishForm = form;
                    if (publishModal) publishModal.show();
                });
            });
            var confirmBtn = document.getElementById('publishConfirmBtn');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    if (pendingPublishForm) pendingPublishForm.submit();
                });
            }
        });
        </script>

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
        /* Flatpickr inside Bootstrap modal: keep calendar above modal */
        .flatpickr-calendar.open { z-index: 1065 !important; }
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

        /* Yellow hint tooltip (Bootstrap tooltip custom class) */
        .tooltip-hint-yellow .tooltip-inner{
            background-color: var(--bs-warning-bg-subtle);
            color: var(--bs-warning-text-emphasis);
            border: 1px solid var(--bs-warning-border-subtle);
            max-width: 320px;
            text-align: left;
        }
        .tooltip-hint-yellow.bs-tooltip-top .tooltip-arrow::before{ border-top-color: var(--bs-warning-bg-subtle); }
        .tooltip-hint-yellow.bs-tooltip-bottom .tooltip-arrow::before{ border-bottom-color: var(--bs-warning-bg-subtle); }
        .tooltip-hint-yellow.bs-tooltip-start .tooltip-arrow::before{ border-left-color: var(--bs-warning-bg-subtle); }
        .tooltip-hint-yellow.bs-tooltip-end .tooltip-arrow::before{ border-right-color: var(--bs-warning-bg-subtle); }
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

        /* Modal footer (Add Event): prevent global full-width .btn-secondary overrides */
        #addEventModal .modal-footer {
            border-top: 1px solid rgba(0,0,0,.08);
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-wrap: wrap;
        }
        #addEventModal .modal-footer .btn {
            width: auto !important;
            padding: 10px 14px;
            border-radius: 10px;
            font-weight: 600;
            font-size: .95rem;
            box-shadow: none !important;
            transform: none !important;
        }
        #addEventModal .modal-footer .btn-secondary {
            background: #fff !important;
            color: #000 !important;
            border: 1px solid rgba(0,0,0,.15) !important;
        }
        #addEventModal .modal-footer .btn-secondary:hover,
        #addEventModal .modal-footer .btn-secondary:active {
            background: #f8f9fa !important;
            color: #000 !important;
            filter: none !important;
        }
        #addEventModal .modal-footer #submitHint { margin-right: auto; }

        /* Force addEventModal dialog to sit centered vertically */
        #addEventModal .modal-dialog {
            margin-top: 55px !important;
            margin-bottom: 30px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }

        /* Modal (Upload Dokumen per Event): remove scrollbar + match logout modal look */
        .modal-upload-operasional.modal{ overflow-y: hidden; }
        .modal-upload-operasional .modal-content{ border-radius: 18px; overflow: hidden; }
        .modal-upload-operasional .modal-header{ padding: 1.1rem 1.1rem .75rem; }
        .modal-upload-operasional .modal-body{ padding: 0 1.1rem 1rem; }
        .modal-upload-operasional .modal-footer{ padding: .25rem 1.1rem 1.1rem; border-top: 0; }
        .modal-upload-operasional .btn{ border-radius: 12px; padding-top: .6rem; padding-bottom: .6rem; }
        .modal-upload-operasional .modal-footer .btn{ width: auto !important; flex: 0 0 auto; }

        /* Publish confirmation modal: compact footer/buttons like logout/feedback confirm modal */
        #publishConfirmModal .modal-content{ border-radius: 18px; overflow: hidden; }
        #publishConfirmModal .modal-header{ padding: 1.1rem 1.1rem .75rem; }
        #publishConfirmModal .modal-body{ padding: 0 1.1rem 1rem; }
        #publishConfirmModal .modal-footer{ padding: .25rem 1.1rem 1.1rem; border-top: 0; }
        #publishConfirmModal .btn{ border-radius: 12px; padding-top: .6rem; padding-bottom: .6rem; }
        #publishConfirmModal .modal-footer .btn{ width: auto !important; flex: 0 0 auto; }

        /* Slightly shift centered doc modal upward */
        .modal-upload-operasional.show .modal-dialog {
            transform: translate(0, -24px) !important;
        }

        /* Prevent modal from sticking to the top header/navbar */
        #addEventModal .modal-dialog,
        #editEventModal .modal-dialog {
            margin-top: 8px;
        }
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
        /* Delete modal footer buttons: keep compact like logout/doc modal */
        #deleteEventModal .modal-footer .btn{ width: auto !important; }
    </style>
    @endsection
    @section('scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
    <!-- Flatpickr Date Picker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>
    <!-- Export libraries -->
    <script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Init Bootstrap tooltips for action icon buttons
        if(window.bootstrap){
            const tTriggers = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tTriggers.forEach(el => { try { new bootstrap.Tooltip(el); } catch(_){} });
        }

        // Materi autocomplete (show after 2 chars) + validation
        function setupGenericAutocomplete(inputEl, boxEl, options, invalidEl) {
            if(!inputEl || !boxEl) return;
            const norm = (s) => String(s || '').trim();
            const lower = (s) => norm(s).toLowerCase();
            const optionSet = new Set((options || []).map(v => lower(v)).filter(Boolean));

            function hide(){ boxEl.style.display = 'none'; boxEl.innerHTML = ''; }
            function show(items){
                if(!items.length){ hide(); return; }
                boxEl.innerHTML = items.map(v => '<button type="button" class="list-group-item list-group-item-action" data-value="' + String(v).replace(/"/g,'&quot;') + '">' + String(v) + '</button>').join('');
                boxEl.style.display = 'block';
            }
            function filter(q){
                const query = lower(q);
                const base = (options || []).map(norm).filter(Boolean);
                if(!query) return base.slice(0, 30);
                return base.filter(v => lower(v).includes(query)).slice(0, 30);
            }
            function applyValidity(){
                const raw = norm(inputEl.value);
                // Validasi dihilangkan: user boleh input materi bebas (tidak harus dari daftar)
                inputEl.setCustomValidity('');
                if(invalidEl) invalidEl.style.display = 'none';
            }

            inputEl.addEventListener('input', () => { show(filter(inputEl.value)); applyValidity(); });
            inputEl.addEventListener('focus', () => { show(filter(inputEl.value)); });
            inputEl.addEventListener('click', () => { show(filter(inputEl.value)); });
            inputEl.addEventListener('blur', () => setTimeout(() => { hide(); applyValidity(); }, 150));

            boxEl.addEventListener('mousedown', (e) => {
                const btn = e.target?.closest('[data-value]');
                if(!btn) return;
                e.preventDefault();
                inputEl.value = btn.getAttribute('data-value') || '';
                hide();
                applyValidity();
                if (typeof window.updateSubmitState === 'function') window.updateSubmitState();
            });
            document.addEventListener('click', (e) => {
                if (e.target === inputEl) return;
                if (boxEl.contains(e.target)) return;
                hide();
            });
            applyValidity();
        }

        (function setupMateriAutocomplete(){
            const inp = document.getElementById('materi');
            const box = document.getElementById('materiSuggestions');
            const inv = document.getElementById('materiInvalidText');
            if(!inp || !box) return;
            setupGenericAutocomplete(inp, box, @json($materiMerged ?? []), inv);
        })();

        (function setupJenisAutocomplete(){
            const inp = document.getElementById('jenis');
            const box = document.getElementById('jenisSuggestions');
            if(!inp || !box) return;
            setupGenericAutocomplete(inp, box, @json($jenisMerged ?? []));
        })();
        // CKEditor init for deskripsi and terms
        // IMPORTANT: guard ClassicEditor so a missing asset doesn't break the whole page (incl. submit enable/disable).
        if (typeof ClassicEditor !== 'undefined') {
            const deskripsiEl = document.querySelector('#deskripsi');
            if (deskripsiEl) {
                ClassicEditor.create(deskripsiEl, {
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
            }

            const termsEl = document.querySelector('#terms');
            if (termsEl) {
                ClassicEditor.create(termsEl, {
                    toolbar: ['bold','italic','underline','bulletedList','numberedList','link','undo','redo','removeFormat']
                }).then(e => window.editorTerms = e).catch(console.error);
            }
        } else {
            console.warn('[EventForm] ClassicEditor is not available; skipping rich text init.');
        }

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
        const locationModeEl = document.getElementById('lokasi');
        const placeNameGroup = document.getElementById('placeNameGroup');
        const placeNameInput = document.getElementById('place_name');
        const mapsGroup = document.getElementById('mapsGroup');
        const zoomGroup = document.getElementById('zoomGroup');

        const mapsInput = document.getElementById('maps');
        const mapsPreview = document.getElementById('mapsPreview');
        const zoomInput = document.getElementById('zoom');
        const mapsRequiredStar = document.getElementById('mapsRequiredStar');
        const zoomRequiredStar = document.getElementById('zoomRequiredStar');
        const placeNameRequiredStar = document.getElementById('placeNameRequiredStar');
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
        function syncMapsUI(){
            const hasMaps = !!(mapsInput && String(mapsInput.value || '').trim());
            if (btnResolveMaps) btnResolveMaps.disabled = !hasMaps;
            if (!hasMaps && mapsPreview) mapsPreview.style.display = 'none';
        }

        function setResolveMapsLoading(isLoading){
            if(!btnResolveMaps) return;
            if(isLoading){
                if(!btnResolveMaps.dataset.originalHtml){
                    btnResolveMaps.dataset.originalHtml = btnResolveMaps.innerHTML;
                }
                btnResolveMaps.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memuat...';
                btnResolveMaps.setAttribute('aria-busy', 'true');
            }else{
                btnResolveMaps.innerHTML = btnResolveMaps.dataset.originalHtml || 'Deteksi';
                btnResolveMaps.removeAttribute('aria-busy');
            }
        }

        function showPlaceNameIfNeeded(forceShow = false){
            if(!placeNameGroup || !placeNameInput) return;
            const mode = String(locationModeEl?.value || '').toLowerCase();
            const isOfflineHybrid = (mode === 'offline' || mode === 'hybrid');
            const hasValue = String(placeNameInput.value || '').trim() !== '';
            const shouldShow = isOfflineHybrid && (forceShow || hasValue);
            placeNameGroup.classList.toggle('d-none', !shouldShow);
            placeNameInput.required = shouldShow;
            if (placeNameRequiredStar) placeNameRequiredStar.style.display = shouldShow ? '' : 'none';
        }

        function syncLocationModeUI(){
            const mode = String(locationModeEl?.value || '').toLowerCase();
            const isOffline = mode === 'offline';
            const isOnline = mode === 'online';
            const isHybrid = mode === 'hybrid';

            if(mapsGroup) mapsGroup.classList.toggle('d-none', isOnline);
            if(zoomGroup) zoomGroup.classList.toggle('d-none', isOffline);

            if(mapsInput) mapsInput.required = (isOffline || isHybrid);
            if(zoomInput) zoomInput.required = (isOnline || isHybrid);

            // Visual '*' must match the required logic.
            if (mapsRequiredStar) mapsRequiredStar.style.display = (isOffline || isHybrid) ? '' : 'none';
            if (zoomRequiredStar) zoomRequiredStar.style.display = (isOnline || isHybrid) ? '' : 'none';

            // Hide place name by default; shown after Deteksi or if already filled
            showPlaceNameIfNeeded(false);

            // If switching to Online, clear maps + coords + preview
            if(isOnline){
                if(mapsInput) mapsInput.value = '';
                const latInp = document.getElementById('latitude');
                const lngInp = document.getElementById('longitude');
                if(latInp) latInp.value = '';
                if(lngInp) lngInp.value = '';
                if(mapsPreview) mapsPreview.style.display = 'none';
                if(btnResolveMaps) btnResolveMaps.disabled = true;
            }
            // If switching to Offline, clear zoom
            if(isOffline){
                if(zoomInput) zoomInput.value = '';
            }
            syncMapsUI();
            if (typeof window.updateSubmitState === 'function') window.updateSubmitState();
        }
        if(mapsInput){
            mapsInput.addEventListener('input', () => { tryRenderMap(); syncMapsUI(); });
            mapsInput.addEventListener('change', () => { tryRenderMap(); syncMapsUI(); });
            mapsInput.addEventListener('blur', () => { tryRenderMap(); syncMapsUI(); });
            // initial (old() values)
            tryRenderMap();
        }
        // Initial state
        syncMapsUI();
        if(btnResolveMaps){
            btnResolveMaps.addEventListener('click', async () => {
                // When Deteksi clicked for offline/hybrid, reveal place name field
                showPlaceNameIfNeeded(true);
                if(placeNameInput && !placeNameGroup?.classList.contains('d-none')){
                    placeNameInput.focus();
                }

                const url = mapsInput?.value || '';
                if(!url){ alert('Masukkan link Google Maps terlebih dahulu.'); return; }
                try{
                    setResolveMapsLoading(true);
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
                    setResolveMapsLoading(false);
                    btnResolveMaps.disabled = false;
                    syncMapsUI();
                }
            });
        }

        if(locationModeEl){
            locationModeEl.addEventListener('change', syncLocationModeUI);
            // initial
            syncLocationModeUI();
        }
        // fix map size when modal is shown
        const addEventModalEl = document.getElementById('addEventModal');
        if(addEventModalEl){
            addEventModalEl.addEventListener('shown.bs.modal', () => {
                if(leafletMap) setTimeout(() => leafletMap.invalidateSize(), 50);
                if (typeof window.updateSubmitState === 'function') window.updateSubmitState();
            });
        }

        // Make modals draggable by mouse (header drag)
        function enableDraggableModal(modalEl){
            if(!modalEl) return;
            const draggableAttr = String(modalEl.getAttribute('data-draggable') || '').toLowerCase();
            if(draggableAttr === 'false') return;
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
                const autoPosAttr = String(modalEl.getAttribute('data-draggable-auto-position') || '').toLowerCase();
                if(autoPosAttr === 'false') return;
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

        // Reseller Event: radios submit as `is_reseller_event` directly; no JS sync required.

        // Speakers (dynamic) - sourced from Trainer API; first required, others optional
        const speakersContainer = document.getElementById('speakersContainer');
        const addSpeakerBtn = document.getElementById('addSpeakerRow');
        const speakerCombined = document.getElementById('speakerCombined');
        const trainersUrl = @json(route('admin.api.trainers'));
        let trainersCache = null;

        async function fetchTrainers(){
            if (trainersCache !== null) return trainersCache;
            try {
                const res = await fetch(trainersUrl, { headers: { 'Accept': 'application/json' } });
                const json = await res.json();
                const list = (json && Array.isArray(json.data)) ? json.data : [];
                trainersCache = list.map(t => ({ id: t.id, name: String(t.name || '').trim() })).filter(t => t.name !== '');
            } catch (e) {
                trainersCache = [];
            }
            return trainersCache;
        }

        function updateSpeakerRowsState(){
            if(!speakersContainer) return;
            const rows = speakersContainer.querySelectorAll('.speaker-row');
            rows.forEach((row, idx) => {
                const sel = row.querySelector('select[name="speakers[]"]');
                const rm  = row.querySelector('.remove-speaker');
                if(sel){ sel.required = (idx === 0); }
                if(rm){ rm.disabled = (rows.length <= 1); }
            });
        }

        function updateSpeakerCombined(){
            if(!speakerCombined || !speakersContainer) return;
            const names = Array.from(speakersContainer.querySelectorAll('select[name="speakers[]"]'))
                .map(s => String(s.value || '').trim())
                .filter(Boolean);
            speakerCombined.value = names.join(', ');
        }

        function populateSpeakerSelect(selectEl, selectedName, trainers){
            if(!selectEl) return;
            const selected = String(selectedName || '').trim();
            const options = [];
            options.push('<option value="" disabled ' + (selected ? '' : 'selected') + '>Pilih pembicara</option>');
            const names = new Set();
            (trainers || []).forEach(t => {
                const name = String(t.name || '').trim();
                if(!name || names.has(name)) return;
                names.add(name);
                const isSel = selected && name === selected;
                options.push('<option value="' + name.replace(/"/g,'&quot;') + '" ' + (isSel ? 'selected' : '') + '>' + name + '</option>');
            });
            if(selected && !names.has(selected)){
                options.push('<option value="' + selected.replace(/"/g,'&quot;') + '" selected>' + selected + ' (tidak ditemukan)</option>');
            }
            selectEl.innerHTML = options.join('');
            if(selected){ selectEl.value = selected; }
        }

        async function refreshSpeakerSelects(){
            if(!speakersContainer) return;
            const trainers = await fetchTrainers();
            speakersContainer.querySelectorAll('select[name="speakers[]"]').forEach(sel => {
                const selected = sel.getAttribute('data-selected') || sel.value || '';
                populateSpeakerSelect(sel, selected, trainers);
                sel.setAttribute('data-selected', sel.value || '');
            });
            updateSpeakerCombined();
        }

        function addSpeakerRow(prefill=''){
            if(!speakersContainer) return;
            const div = document.createElement('div');
            div.className = 'speaker-row border rounded p-2';
            div.style.background = '#f8fafc';
            const safeVal = prefill ? String(prefill).replace(/"/g, '&quot;') : '';
            div.innerHTML = `
                <div class="d-flex gap-2 align-items-center">
                    <select name="speakers[]" class="form-select speaker-select" data-selected="${safeVal}">
                        <option value="" selected disabled>Pilih narasumber</option>
                    </select>
                    <button type="button" class="btn btn-outline-danger remove-speaker" title="Hapus"><i class="bi bi-trash"></i></button>
                </div>
                <div class="mt-2">
                    <input type="number" name="speaker_salaries[]" class="form-control form-control-sm"
                        placeholder="Gaji Pembicara/Trainer (Rp)" min="0" step="1000">
                </div>`;
            speakersContainer.appendChild(div);
            updateSpeakerRowsState();
            refreshSpeakerSelects();
        }

        if(speakersContainer){
            speakersContainer.addEventListener('click', (e) => {
                const btn = e.target.closest('.remove-speaker');
                if(btn){
                    const row = btn.closest('.speaker-row');
                    if(row){ row.remove(); updateSpeakerRowsState(); updateSpeakerCombined(); }
                }
            });
            speakersContainer.addEventListener('change', (e) => {
                const sel = e.target.closest('select[name="speakers[]"]');
                if(sel){ sel.setAttribute('data-selected', sel.value || ''); updateSpeakerCombined(); }
            });
        }
        if(addSpeakerBtn){ addSpeakerBtn.addEventListener('click', () => addSpeakerRow()); }
        updateSpeakerRowsState();
        refreshSpeakerSelects();

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
                altFormat:'l, d F Y',
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
            const inlineErrorClass = 'js-inline-error';

            function fieldKey(el){
                if(!el) return '';
                return String(el.id || el.name || '').trim();
            }

            function getFieldBlock(el){
                if(!el) return null;
                if((el.name || '') === 'speakers[]'){
                    const container = document.getElementById('speakersContainer');
                    return container?.closest('.mb-3') || el.closest('.mb-3') || el.parentElement;
                }
                return el.closest('.mb-3') || el.closest('.input-group') || el.parentElement;
            }

            function getInlineErrorEl(block, key){
                if(!block || !key) return null;
                const existing = Array.from(block.querySelectorAll('.' + inlineErrorClass))
                    .find((node) => (node.dataset.for || '') === key);
                if(existing) return existing;
                const el = document.createElement('small');
                el.className = 'text-danger d-block mt-1 ' + inlineErrorClass;
                el.dataset.for = key;
                el.style.display = 'none';
                block.appendChild(el);
                return el;
            }

            function setInlineError(targetEl, message){
                const key = fieldKey(targetEl);
                const block = getFieldBlock(targetEl);
                if(!block || !key) return;
                const errEl = getInlineErrorEl(block, key);
                if(!errEl) return;
                const msg = String(message || '').trim();
                errEl.textContent = msg;
                errEl.style.display = msg ? 'block' : 'none';
            }

            function clearInlineError(targetEl){
                const key = fieldKey(targetEl);
                const block = getFieldBlock(targetEl);
                if(!block || !key) return;
                const errEl = Array.from(block.querySelectorAll('.' + inlineErrorClass))
                    .find((node) => (node.dataset.for || '') === key);
                if(errEl){
                    errEl.textContent = '';
                    errEl.style.display = 'none';
                }
            }

            function clearAllInlineErrors(){
                form.querySelectorAll('.' + inlineErrorClass).forEach((el) => {
                    el.textContent = '';
                    el.style.display = 'none';
                });
            }

            form.addEventListener('submit', function(ev) {
                if (window.editorDeskripsi) document.getElementById('deskripsi').value = window.editorDeskripsi.getData();
                if (window.editorTerms) document.getElementById('terms').value = window.editorTerms.getData();
                // Build combined speaker string for backward compatibility
                updateSpeakerCombined();
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
                    if (!String(f.value || '').trim()) {
                        f.classList.add('border-danger');
                        ok = false;
                    } else {
                        f.classList.remove('border-danger');
                    }
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
                    ev.preventDefault();

                    // Render inline errors and focus first invalid field
                    if (typeof window.updateSubmitState === 'function') {
                        window.updateSubmitState();
                    }
                    const firstInvalid = form.querySelector('.border-danger');
                    if (firstInvalid && typeof firstInvalid.focus === 'function') {
                        firstInvalid.focus();
                    }
                }
                // ensure expense totals up-to-date before submit
                expensesTableBody?.querySelectorAll('tr').forEach(tr => recalcExpenseRow(tr));
                console.log('[EventForm] Submit attempted. Valid:', ok);
            });

            // Live enable/disable submit button based on required fields
            const submitBtn = document.getElementById('submitBtn');
            const submitHint = document.getElementById('submitHint');
            const isElementVisible = (el) => {
                if (!el) return false;
                if (el.closest('.d-none')) return false;
                // More reliable than offsetParent (offsetParent can be null for some positioned elements)
                return !!(el.getClientRects && el.getClientRects().length);
            };
            const getRequiredFields = () => Array.from(form.querySelectorAll('[required]'))
                .filter(el => !el.disabled && isElementVisible(el));

            const isRequiredFieldEmpty = (fieldEl) => {
                if (!fieldEl) return true;
                const type = String(fieldEl.type || '').toLowerCase();
                if (type === 'file') {
                    return !(fieldEl.files && fieldEl.files.length > 0);
                }
                if (type === 'checkbox' || type === 'radio') {
                    return !fieldEl.checked;
                }
                return !String(fieldEl.value || '').trim();
            };
            // Friendly name helper (shared with submit alert logic above)
            const fieldFriendlyName = (el) => {
                if(!el) return 'Field';
                const id = el.id || '';
                const name = el.name || '';
                if(id === 'gambar' || name === 'image') return 'Gambar Event';
                if(id === 'nama' || name === 'title') return 'Nama Event';
                if(name === 'speakers[]') return 'Nama Pembicara (minimal 1)';
                if(id === 'manage_action' || name === 'manage_action') return 'Kelola Event';
                if(id === 'level' || name === 'level') return 'Level';
                if(id === 'short_desc' || name === 'short_description') return 'Penjelasan Singkat';
                if(id === 'deskripsi' || name === 'description') return 'Deskripsi Event';
                if(id === 'tanggal' || name === 'event_date') return 'Tanggal';
                if(id === 'masuk1' || name === 'event_time') return 'Waktu Mulai';
                if(id === 'lokasi' || name === 'location_mode') return 'Lokasi';
                if(id === 'place_name' || name === 'place_name') return 'Nama Tempat';
                if(id === 'maps' || name === 'maps_url') return 'Link Google Maps';
                if(id === 'zoom' || name === 'zoom_link') return 'Link Zoom';
                if(id === 'hargaDisplay' || id === 'harga' || name === 'price') return 'Harga';
                return id || name || 'Field';
            };
            function missingRequired(){
                return getRequiredFields().filter(isRequiredFieldEmpty);
            }
            function allRequiredFilled(){
                return missingRequired().length === 0;
            }
            // expose globally so CKEditor init can call it even if defined later
            window.updateSubmitState = function updateSubmitState(){
                clearAllInlineErrors();

                const filled = allRequiredFilled();
                // Extra rule: short description must be <= 40 words
                const sdEl = document.getElementById('short_desc');
                const sdWords = sdEl ? (sdEl.value || '').trim().split(/\s+/).filter(Boolean).length : 0;
                const overLimit = sdEl ? sdWords > 40 : false;
                const shouldDisable = (!filled || overLimit);
                if(submitBtn){
                    submitBtn.disabled = shouldDisable;
                    if (shouldDisable) {
                        submitBtn.setAttribute('disabled', 'disabled');
                        submitBtn.setAttribute('aria-disabled', 'true');
                    } else {
                        submitBtn.removeAttribute('disabled');
                        submitBtn.setAttribute('aria-disabled', 'false');
                    }
                }

                // Show a compact hint near the button so users know what's missing.
                if(submitHint){
                    if(!filled){
                        const missingNames = missingRequired().map(fieldFriendlyName);
                        submitHint.textContent = 'Lengkapi: ' + missingNames.join(', ') + '.';
                        submitHint.style.display = 'block';
                    } else if(overLimit){
                        submitHint.textContent = 'Penjelasan singkat maksimal 40 kata (saat ini ' + sdWords + ' kata).';
                        submitHint.style.display = 'block';
                    } else {
                        submitHint.style.display = 'none';
                    }
                }

                // Required fields: show inline error under each missing field
                missingRequired().forEach((fieldEl) => {
                    fieldEl.classList.add('border-danger');
                    setInlineError(fieldEl, fieldFriendlyName(fieldEl) + ' wajib diisi.');
                });

                // Clear errors for currently filled required fields
                getRequiredFields()
                    .filter((fieldEl) => String(fieldEl.value || '').trim())
                    .forEach((fieldEl) => {
                        fieldEl.classList.remove('border-danger');
                        clearInlineError(fieldEl);
                    });

                // Short description max 40 words
                if(sdEl){
                    if(overLimit){
                        sdEl.classList.add('border-danger');
                        setInlineError(sdEl, 'Penjelasan singkat maksimal 40 kata (saat ini ' + sdWords + ' kata).');
                    } else {
                        clearInlineError(sdEl);
                    }
                }

            }
            // Observe input/change events
            ['input','change','blur'].forEach(evName => form.addEventListener(evName, updateSubmitState));
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
            // Direct listeners (keep) + delegated fallback for robustness.
            ['input','change','blur'].forEach(evName => shortDescEl?.addEventListener(evName, () => { updateShortDescCount(); updateSubmitState(); }));
            document.addEventListener('input', function(e){
                const t = e.target;
                if(!(t instanceof HTMLElement)) return;
                if(t.id === 'short_desc' || (t.tagName === 'TEXTAREA' && t.getAttribute('name') === 'short_description')){
                    updateShortDescCount();
                    if (typeof window.updateSubmitState === 'function') window.updateSubmitState();
                }
            }, true);
            updateShortDescCount();
        } else {
            console.warn('[EventForm] Form element not found.');
        }

        // NOTE: Add Event uses native <input type="date"> for #tanggal (no Flatpickr).

        @if($errors->any())
            if (window.bootstrap) { new bootstrap.Modal(document.getElementById('addEventModal')).show(); }
        @endif

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

    <script>
        // Robust live word counter for Penjelasan Singkat (max 40 words)
        (function() {
            function countWords(text) {
                return String(text || '').trim().split(/\s+/).filter(Boolean).length;
            }

            function updateCounter(textarea) {
                if (!textarea) return;
                const block = textarea.closest('.mb-3') || textarea.parentElement;
                const countEl = block ? block.querySelector('#shortDescCount') : document.getElementById('shortDescCount');
                if (!countEl) return;

                const n = countWords(textarea.value);
                countEl.textContent = String(n);
                countEl.classList.toggle('text-danger', n > 40);
            }

            document.addEventListener('input', function(e) {
                const t = e.target;
                if (!(t instanceof HTMLTextAreaElement)) return;
                if (t.id === 'short_desc' || t.name === 'short_description') {
                    updateCounter(t);
                }
            }, true);

            document.addEventListener('DOMContentLoaded', function() {
                const ta = document.querySelector('textarea#short_desc, textarea[name="short_description"]');
                if (ta) updateCounter(ta);
            });
        })();
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
                <div class="w-100 d-grid gap-2 d-sm-flex justify-content-end">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger confirm-danger-btn px-4" id="deleteConfirmBtn" form="deleteEventFormGlobal" disabled>
                        <span class="me-1">Hapus Permanen</span>
                        <i class="bi bi-arrow-right-short" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="deleteEventFormGlobal" action="#" method="POST" class="d-none">@csrf @method('DELETE')</form>

<script>
// Isolated binding for Delete Event modal (defensive against other script errors)
document.addEventListener('DOMContentLoaded', function(){
    try {
        var modalEl = document.getElementById('deleteEventModal');
        var formEl = document.getElementById('deleteEventFormGlobal');
        var nameEl = document.getElementById('deleteEventName');
        var checkboxEl = document.getElementById('deleteConfirmCheckbox');
        var btnEl = document.getElementById('deleteConfirmBtn');

        if(!modalEl || !formEl || !btnEl) return;
        if(btnEl.dataset.boundDeleteModal === '1') return;
        btnEl.dataset.boundDeleteModal = '1';

        function syncBtn(){
            btnEl.disabled = !(checkboxEl && checkboxEl.checked);
        }

        if(checkboxEl){
            checkboxEl.addEventListener('change', syncBtn);
        }

        // Set action/title when modal is opened
        modalEl.addEventListener('show.bs.modal', function(ev){
            var trigger = ev.relatedTarget;
            var url = trigger && trigger.getAttribute ? (trigger.getAttribute('data-url') || '') : '';
            var title = trigger && trigger.getAttribute ? (trigger.getAttribute('data-title') || 'Event') : 'Event';

            if(url) formEl.setAttribute('action', url);
            if(nameEl) nameEl.textContent = title;
            if(checkboxEl) checkboxEl.checked = false;
            syncBtn();
        });

        // Submit programmatically for maximum browser compatibility
        btnEl.addEventListener('click', function(e){
            if(btnEl.disabled) return;
            e.preventDefault();
            btnEl.disabled = true;
            if(typeof formEl.requestSubmit === 'function') formEl.requestSubmit();
            else formEl.submit();
        });

        syncBtn();
    } catch(e) {
        // swallow - never block the page
    }
});
</script>
<script>
function initEditEventLocationAndBenefits(modalEl){
    if(!modalEl || modalEl.dataset.locationBenefitsInitialized === '1') return;
    modalEl.dataset.locationBenefitsInitialized = '1';

    const eventDateInput = modalEl.querySelector('#tanggal');
    const discountUntilInput = modalEl.querySelector('#discount_until');
    const locationModeEl = modalEl.querySelector('#lokasi');
    const placeNameGroup = modalEl.querySelector('#placeNameGroup');
    const placeNameInput = modalEl.querySelector('#place_name');
    const mapsGroup = modalEl.querySelector('#mapsGroup');
    const zoomGroup = modalEl.querySelector('#zoomGroup');
    const mapsInput = modalEl.querySelector('#maps');
    const mapsPreview = modalEl.querySelector('#mapsPreview');
    const btnResolveMaps = modalEl.querySelector('#btnResolveMaps');
    const zoomInput = modalEl.querySelector('#zoom');
    const mapsRequiredStar = modalEl.querySelector('#mapsRequiredStar');
    const zoomRequiredStar = modalEl.querySelector('#zoomRequiredStar');
    const placeNameRequiredStar = modalEl.querySelector('#placeNameRequiredStar');
    const latitudeInput = modalEl.querySelector('#latitude');
    const longitudeInput = modalEl.querySelector('#longitude');
    const form = modalEl.querySelector('#editEventForm');
    const submitBtn = modalEl.querySelector('#editSubmitBtn');
    const benefitsContainer = modalEl.querySelector('#benefitsContainer');
    const addBenefitBtn = modalEl.querySelector('#addBenefitRow');
    const benefitHidden = modalEl.querySelector('#benefit');
    const resolveMapsUrl = @json(route('admin.maps.resolve'));
    const csrfToken = @json(csrf_token());
    let leafletMap = null;
    let leafletMarker = null;
    let eventDateFp = null;
    let discountUntilFp = null;

    if(eventDateInput){
        eventDateInput.removeAttribute('min');
    }

    if(window.flatpickr){
        if(eventDateInput && !eventDateInput._flatpickr){
            eventDateFp = flatpickr(eventDateInput, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'l, d F Y',
                disableMobile: true,
                allowInput: true,
                defaultDate: eventDateInput.value || null
            });
        }else if(eventDateInput?._flatpickr){
            eventDateFp = eventDateInput._flatpickr;
        }

        if(discountUntilInput && !discountUntilInput._flatpickr){
            discountUntilFp = flatpickr(discountUntilInput, {
                locale: 'id',
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'l, d F Y',
                disableMobile: true,
                clickOpens: true
            });
        }else if(discountUntilInput?._flatpickr){
            discountUntilFp = discountUntilInput._flatpickr;
        }
    }

    function updateDiscountUntilBounds(){
        if(!discountUntilInput || !discountUntilFp) return;
        const dateStr = eventDateInput?.value || '';
        if(!dateStr) return;

        const eventDate = new Date(dateStr + 'T00:00:00');
        if(isNaN(eventDate.getTime())) return;

        const maxDate = new Date(eventDate.getTime() - 24 * 60 * 60 * 1000);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if(maxDate < today){
            discountUntilInput.disabled = true;
            if(discountUntilFp.altInput) discountUntilFp.altInput.disabled = true;
            discountUntilInput.value = '';
            discountUntilFp.clear();
            return;
        }

        discountUntilFp.set('minDate', today);
        discountUntilFp.set('maxDate', maxDate);

        const current = discountUntilInput.value;
        if(current){
            const currentDate = new Date(current + 'T00:00:00');
            if(currentDate >= eventDate){
                discountUntilFp.clear();
                discountUntilInput.value = '';
            }
        }
    }

    function parseLatLngFromUrl(url){
        if(!url) return null;
        try{
            const decoded = decodeURIComponent(url);
            let match = decoded.match(/@(-?\d+\.\d+),\s*(-?\d+\.\d+)/);
            if(match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
            match = decoded.match(/[?&]q=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/);
            if(match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
            match = decoded.match(/[?&]ll=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/);
            if(match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
            match = decoded.match(/[?&]center=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/);
            if(match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
            const m3d = decoded.match(/!3d(-?\d+\.\d+)/);
            const m4d = decoded.match(/!4d(-?\d+\.\d+)/);
            if(m3d && m4d) return { lat: parseFloat(m3d[1]), lng: parseFloat(m4d[1]) };
            match = decoded.trim().match(/^\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)\s*$/);
            if(match) return { lat: parseFloat(match[1]), lng: parseFloat(match[2]) };
        }catch(_){}
        return null;
    }

    function ensureMap(){
        if(!mapsPreview || !window.L) return;
        if(!leafletMap){
            leafletMap = L.map(mapsPreview).setView([-6.200, 106.816], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(leafletMap);
        }
        setTimeout(function(){ leafletMap.invalidateSize(); }, 50);
    }

    function showMap(lat, lng){
        if(latitudeInput) latitudeInput.value = (+lat).toFixed(7);
        if(longitudeInput) longitudeInput.value = (+lng).toFixed(7);
        if(!mapsPreview || !window.L) return;
        mapsPreview.style.display = 'block';
        ensureMap();
        if(!leafletMap) return;
        const position = [lat, lng];
        leafletMap.setView(position, 14);
        if(leafletMarker){
            leafletMarker.setLatLng(position);
        }else{
            leafletMarker = L.marker(position).addTo(leafletMap);
        }
    }

    function tryRenderMap(){
        const value = mapsInput?.value || '';
        const parsed = parseLatLngFromUrl(value);
        if(parsed){
            showMap(parsed.lat, parsed.lng);
            return;
        }
        const lat = latitudeInput ? parseFloat(String(latitudeInput.value || '')) : NaN;
        const lng = longitudeInput ? parseFloat(String(longitudeInput.value || '')) : NaN;
        if(Number.isFinite(lat) && Number.isFinite(lng)){
            showMap(lat, lng);
        }else if(mapsPreview){
            mapsPreview.style.display = 'none';
        }
    }

    function syncMapsUI(){
        const hasMaps = !!String(mapsInput?.value || '').trim();
        if(btnResolveMaps) btnResolveMaps.disabled = !hasMaps;
        if(!hasMaps && mapsPreview) mapsPreview.style.display = 'none';
    }

    function setResolveMapsLoading(isLoading){
        if(!btnResolveMaps) return;
        if(isLoading){
            if(!btnResolveMaps.dataset.originalHtml){
                btnResolveMaps.dataset.originalHtml = btnResolveMaps.innerHTML;
            }
            btnResolveMaps.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memuat...';
            btnResolveMaps.setAttribute('aria-busy', 'true');
        }else{
            btnResolveMaps.innerHTML = btnResolveMaps.dataset.originalHtml || 'Deteksi';
            btnResolveMaps.removeAttribute('aria-busy');
        }
    }

    function showPlaceNameIfNeeded(forceShow = false){
        if(!placeNameGroup || !placeNameInput) return;
        const mode = String(locationModeEl?.value || '').toLowerCase();
        const isOfflineHybrid = (mode === 'offline' || mode === 'hybrid');
        const hasValue = String(placeNameInput.value || '').trim() !== '';
        const shouldShow = isOfflineHybrid && (forceShow || hasValue);
        placeNameGroup.classList.toggle('d-none', !shouldShow);
        placeNameInput.required = shouldShow;
        if(placeNameRequiredStar) placeNameRequiredStar.style.display = shouldShow ? '' : 'none';
    }

    function syncLocationModeUI(){
        const mode = String(locationModeEl?.value || '').toLowerCase();
        const isOffline = mode === 'offline';
        const isOnline = mode === 'online';
        const isHybrid = mode === 'hybrid';

        if(mapsGroup) mapsGroup.classList.toggle('d-none', isOnline);
        if(zoomGroup) zoomGroup.classList.toggle('d-none', isOffline);

        if(mapsInput) mapsInput.required = (isOffline || isHybrid);
        if(zoomInput) zoomInput.required = (isOnline || isHybrid);
        if(mapsRequiredStar) mapsRequiredStar.style.display = (isOffline || isHybrid) ? '' : 'none';
        if(zoomRequiredStar) zoomRequiredStar.style.display = (isOnline || isHybrid) ? '' : 'none';

        showPlaceNameIfNeeded(false);

        if(isOnline){
            if(mapsInput) mapsInput.value = '';
            if(latitudeInput) latitudeInput.value = '';
            if(longitudeInput) longitudeInput.value = '';
            if(mapsPreview) mapsPreview.style.display = 'none';
            if(btnResolveMaps) btnResolveMaps.disabled = true;
        }
        if(isOffline && zoomInput){
            zoomInput.value = '';
        }

        syncMapsUI();
        if(typeof window.updateSubmitState === 'function') window.updateSubmitState();
    }

    mapsInput?.addEventListener('input', function(){ tryRenderMap(); syncMapsUI(); });
    mapsInput?.addEventListener('change', function(){ tryRenderMap(); syncMapsUI(); });
    mapsInput?.addEventListener('blur', function(){ tryRenderMap(); syncMapsUI(); });
    eventDateInput?.addEventListener('input', updateDiscountUntilBounds);
    eventDateInput?.addEventListener('change', updateDiscountUntilBounds);
    locationModeEl?.addEventListener('change', syncLocationModeUI);
    btnResolveMaps?.addEventListener('click', async function(){
        showPlaceNameIfNeeded(true);
        if(placeNameInput && !placeNameGroup?.classList.contains('d-none')){
            placeNameInput.focus();
        }

        const url = mapsInput?.value || '';
        if(!url){
            alert('Masukkan link Google Maps terlebih dahulu.');
            return;
        }

        try{
            setResolveMapsLoading(true);
            btnResolveMaps.disabled = true;
            const response = await fetch(resolveMapsUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ url })
            });
            const data = await response.json();
            if(response.ok && data.lat && data.lng){
                showMap(data.lat, data.lng);
            }else{
                alert(data.message || 'Koordinat tidak ditemukan.');
            }
        }catch(_){
            alert('Gagal mendeteksi koordinat.');
        }finally{
            setResolveMapsLoading(false);
            btnResolveMaps.disabled = false;
            syncMapsUI();
        }
    });

    function renderBenefitRow(prefill = ''){
        if(!benefitsContainer) return;
        const row = document.createElement('div');
        row.className = 'input-group mb-2 benefit-row';
        const safeValue = String(prefill || '').replace(/"/g, '&quot;');
        row.innerHTML = `<input type="text" class="form-control" name="benefits[]" placeholder="Tuliskan benefit" value="${safeValue}"><button type="button" class="btn btn-outline-danger" data-action="remove-benefit" title="Hapus"><i class="bi bi-x"></i></button>`;
        benefitsContainer.appendChild(row);
    }

    benefitsContainer?.addEventListener('click', function(e){
        const btn = e.target.closest('button[data-action="remove-benefit"]');
        if(btn){
            btn.closest('.benefit-row')?.remove();
        }
    });

    addBenefitBtn?.addEventListener('click', function(){
        renderBenefitRow();
    });

    if(benefitsContainer){
        const raw = (benefitHidden?.value || '').trim();
        const parts = raw ? (raw.includes('|') ? raw.split('|') : raw.split(/\r?\n/)).map(function(item){
            return (item || '').trim();
        }).filter(Boolean) : [];
        if(parts.length){
            parts.forEach(function(item){ renderBenefitRow(item); });
        }else{
            renderBenefitRow();
        }
    }

    form?.addEventListener('submit', function(){
        if(benefitHidden && benefitsContainer){
            const items = Array.from(benefitsContainer.querySelectorAll('input[name="benefits[]"]'))
                .map(function(input){ return (input.value || '').trim(); })
                .filter(Boolean);
            benefitHidden.value = items.join(' | ');
        }
    });

    if(submitBtn && form && submitBtn.dataset.boundEditSubmit !== '1'){
        submitBtn.dataset.boundEditSubmit = '1';
        submitBtn.addEventListener('click', function(e){
            e.preventDefault();
            if(typeof form.requestSubmit === 'function'){
                form.requestSubmit();
            }else{
                form.submit();
            }
        });
    }

    tryRenderMap();
    syncMapsUI();
    syncLocationModeUI();
    updateDiscountUntilBounds();
}

function initEditEventDynamicTables(modalEl){
    if(!modalEl || modalEl.dataset.dynamicTablesInitialized === '1') return;
    modalEl.dataset.dynamicTablesInitialized = '1';

    const scheduleTableBody = modalEl.querySelector('#scheduleTable tbody');
    const addScheduleBtn = modalEl.querySelector('#addScheduleRow');
    let scheduleIndex = scheduleTableBody ? scheduleTableBody.querySelectorAll('tr').length : 0;

    function createScheduleRow(idx){
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="time" class="form-control form-control-sm" name="schedule[${idx}][start]"></td>
            <td><input type="time" class="form-control form-control-sm" name="schedule[${idx}][end]"></td>
            <td><input type="text" class="form-control form-control-sm" name="schedule[${idx}][title]" placeholder="Nama kegiatan"></td>
            <td><input type="text" class="form-control form-control-sm" name="schedule[${idx}][description]" placeholder="Deskripsi singkat"></td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm" data-action="remove" title="Hapus">
                    <i class="bi bi-x"></i>
                </button>
            </td>`;
        return tr;
    }

    function addScheduleRow(){
        if(!scheduleTableBody) return;
        scheduleTableBody.appendChild(createScheduleRow(scheduleIndex++));
    }

    addScheduleBtn?.addEventListener('click', function(e){
        e.preventDefault();
        addScheduleRow();
    });

    scheduleTableBody?.addEventListener('click', function(e){
        const btn = e.target.closest('button[data-action="remove"]');
        if(btn){
            btn.closest('tr')?.remove();
        }
    });

    if(scheduleTableBody && scheduleTableBody.querySelectorAll('tr').length === 0){
        addScheduleRow();
    }

    const expensesTableBody = modalEl.querySelector('#expensesTable tbody');
    const addExpenseBtn = modalEl.querySelector('#addExpenseRow');
    const expensesGrandTotalEl = modalEl.querySelector('#expensesGrandTotal');
    let expenseIndex = expensesTableBody ? expensesTableBody.querySelectorAll('tr').length : 0;

    function formatRupiah(value){
        const n = Math.max(0, Math.floor(Number(value) || 0));
        return 'Rp' + n.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    function clampNonNegativeNumberInput(input){
        if(!input) return;
        input.addEventListener('keydown', function(e){
            if(e.key === '-' || e.key === 'Subtract' || e.keyCode === 189){
                e.preventDefault();
            }
        });
        input.addEventListener('input', function(){
            if(input.value === '') return;
            let v = parseFloat(input.value);
            if(isNaN(v) || v < 0) v = 0;
            input.value = Math.floor(v).toString();
        });
    }

    function recalcExpensesGrandTotal(){
        if(!expensesTableBody || !expensesGrandTotalEl) return;
        let total = 0;
        expensesTableBody.querySelectorAll('input[data-expense-total]').forEach(function(input){
            const value = parseFloat(input.value || '0');
            if(!isNaN(value)) total += value;
        });
        expensesGrandTotalEl.textContent = formatRupiah(total);
    }

    function recalcExpenseRow(tr){
        if(!tr) return;
        const qty = parseFloat(tr.querySelector('input[data-expense-qty]')?.value || '0');
        const unit = parseFloat(tr.querySelector('input[data-expense-unit]')?.value || '0');
        const totalInput = tr.querySelector('input[data-expense-total]');
        const total = (isNaN(qty) ? 0 : qty) * (isNaN(unit) ? 0 : unit);
        if(totalInput) totalInput.value = Math.max(0, Math.round(total));
        recalcExpensesGrandTotal();
    }

    function wireExpenseRow(tr){
        if(!tr || tr.dataset.expenseRowInitialized === '1') return;
        tr.dataset.expenseRowInitialized = '1';

        const qtyInput = tr.querySelector('input[data-expense-qty]');
        const unitInput = tr.querySelector('input[data-expense-unit]');

        clampNonNegativeNumberInput(qtyInput);
        clampNonNegativeNumberInput(unitInput);

        qtyInput?.addEventListener('input', function(){ recalcExpenseRow(tr); });
        unitInput?.addEventListener('input', function(){ recalcExpenseRow(tr); });
    }

    function createExpenseRow(idx){
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" name="expenses[${idx}][item]" placeholder="Nama barang"></td>
            <td><input type="number" class="form-control form-control-sm" name="expenses[${idx}][quantity]" data-expense-qty min="0" step="1"></td>
            <td><input type="number" class="form-control form-control-sm" name="expenses[${idx}][unit_price]" data-expense-unit min="0" step="1"></td>
            <td><input type="number" class="form-control form-control-sm" name="expenses[${idx}][total]" data-expense-total readonly value="0"></td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger btn-sm" data-action="remove-expense" title="Hapus">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>`;
        wireExpenseRow(tr);
        return tr;
    }

    function addExpenseRow(){
        if(!expensesTableBody) return;
        const row = createExpenseRow(expenseIndex++);
        expensesTableBody.appendChild(row);
        recalcExpenseRow(row);
    }

    addExpenseBtn?.addEventListener('click', function(e){
        e.preventDefault();
        addExpenseRow();
    });

    expensesTableBody?.addEventListener('click', function(e){
        const btn = e.target.closest('button[data-action="remove-expense"]');
        if(btn){
            btn.closest('tr')?.remove();
            recalcExpensesGrandTotal();
        }
    });

    if(expensesTableBody){
        const rows = Array.from(expensesTableBody.querySelectorAll('tr'));
        if(rows.length === 0){
            addExpenseRow();
        }else{
            rows.forEach(function(row){
                wireExpenseRow(row);
                recalcExpenseRow(row);
            });
            recalcExpensesGrandTotal();
        }
    }
}

function initEditEventSpeakers(modalEl){
    if(!modalEl || modalEl.dataset.speakersInitialized === '1') return;
    modalEl.dataset.speakersInitialized = '1';

    const speakersContainer = modalEl.querySelector('#speakersContainer');
    const addSpeakerBtn = modalEl.querySelector('#addSpeakerRow');
    const speakerCombined = modalEl.querySelector('#speakerCombined');
    const trainersUrl = @json(route('admin.api.trainers'));
    let trainersCache = null;

    async function fetchTrainers(){
        if (trainersCache !== null) return trainersCache;
        try {
            const res = await fetch(trainersUrl, { headers: { 'Accept': 'application/json' } });
            const json = await res.json();
            const list = (json && Array.isArray(json.data)) ? json.data : [];
            trainersCache = list.map(t => ({ id: t.id, name: String(t.name || '').trim() })).filter(t => t.name !== '');
        } catch (e) { trainersCache = []; }
        return trainersCache;
    }

    function updateSpeakerRowsState() {
        if (!speakersContainer) return;
        const rows = speakersContainer.querySelectorAll('.speaker-row');
        rows.forEach((row, idx) => {
            const sel = row.querySelector('select[name="speakers[]"]');
            const rm = row.querySelector('.remove-speaker');
            if (sel) sel.required = (idx === 0);
            if (rm) rm.disabled = (rows.length <= 1);
        });
    }

    function updateSpeakerCombined() {
        if (!speakerCombined || !speakersContainer) return;
        const names = Array.from(speakersContainer.querySelectorAll('select[name="speakers[]"]'))
            .map(s => String(s.value || '').trim())
            .filter(Boolean);
        speakerCombined.value = names.join(', ');
    }

    function populateSpeakerSelect(selectEl, selectedName, trainers) {
        if (!selectEl) return;
        const selected = String(selectedName || '').trim();
        const options = [];
        options.push('<option value="" disabled ' + (selected ? '' : 'selected') + '>Pilih narasumber</option>');
        const names = new Set();
        (trainers || []).forEach(t => {
            const name = String(t.name || '').trim();
            if (!name || names.has(name)) return;
            names.add(name);
            const isSel = selected && name === selected;
            options.push('<option value="' + name.replace(/"/g, '&quot;') + '" ' + (isSel ? 'selected' : '') + '>' + name + '</option>');
        });
        if (selected && !names.has(selected)) {
            options.push('<option value="' + selected.replace(/"/g, '&quot;') + '" selected>' + selected + ' (tidak ditemukan)</option>');
        }
        selectEl.innerHTML = options.join('');
        if (selected) { selectEl.value = selected; }
    }

    async function refreshSpeakerSelects() {
        if (!speakersContainer) return;
        const trainers = await fetchTrainers();
        speakersContainer.querySelectorAll('select[name="speakers[]"]').forEach(sel => {
            const selected = sel.getAttribute('data-selected') || sel.value || '';
            populateSpeakerSelect(sel, selected, trainers);
            sel.setAttribute('data-selected', sel.value || '');
        });
        updateSpeakerCombined();
    }

    function addSpeakerRow(prefill = '') {
        if (!speakersContainer) return;
        const div = document.createElement('div');
        div.className = 'speaker-row border rounded p-2';
        div.style.background = '#f8fafc';
        const safe = prefill ? String(prefill).replace(/"/g, '&quot;') : '';
        div.innerHTML = `
            <div class="d-flex gap-2 align-items-center">
                <select name="speakers[]" class="form-select speaker-select" data-selected="${safe}">
                    <option value="" selected disabled>Pilih narasumber</option>
                </select>
                <button type="button" class="btn btn-outline-danger remove-speaker" title="Hapus"><i class="bi bi-trash"></i></button>
            </div>
            <div class="mt-2">
                <input type="number" name="speaker_salaries[]" class="form-control form-control-sm"
                    placeholder="Gaji Pembicara/Trainer (Rp)" min="0" step="1000">
            </div>`;
        speakersContainer.appendChild(div);
        updateSpeakerRowsState();
        refreshSpeakerSelects();
    }

    if(speakersContainer){
        speakersContainer.addEventListener('click', e => {
            const btn = e.target.closest('.remove-speaker');
            if (btn) {
                const row = btn.closest('.speaker-row');
                if(row) {
                    row.remove();
                    updateSpeakerRowsState();
                    updateSpeakerCombined();
                }
            }
        });
        speakersContainer.addEventListener('change', e => {
            const sel = e.target.closest('select[name="speakers[]"]');
            if(sel){
                sel.setAttribute('data-selected', sel.value || '');
                updateSpeakerCombined();
            }
        });
    }

    if(addSpeakerBtn){
        addSpeakerBtn.addEventListener('click', (e) => {
            e.preventDefault();
            addSpeakerRow();
        });
    }

    updateSpeakerRowsState();
    refreshSpeakerSelects();
}

function initEditEventAutocomplete(modalEl) {
    if(!modalEl || modalEl.dataset.autocompleteInitialized === '1') return;
    modalEl.dataset.autocompleteInitialized = '1';

    const materiInp = modalEl.querySelector('#materi');
    const materiBox = modalEl.querySelector('#materiSuggestions');
    const materiInv = modalEl.querySelector('#materiInvalidText');
    
    if(materiInp && materiBox) {
        let options = [];
        try {
            const raw = materiInp.getAttribute('data-options');
            options = raw ? JSON.parse(raw) : [];
        } catch(e) { options = []; }
        setupGenericAutocomplete(materiInp, materiBox, options, materiInv);
    }
}

// Edit button: load edit modal via AJAX, fallback to full navigation
document.addEventListener('click', async function(e){
    const btn = e.target.closest('.edit-event-btn');
    if(!btn) return;
    const url = btn.getAttribute('data-edit-url') || btn.href;
    // allow open-in-new-tab
    if (e.button === 1 || e.ctrlKey || e.metaKey || e.shiftKey) return;
    e.preventDefault();
    if(!url) return;
    try{
        const existing = document.getElementById('editEventModal');
        if(existing) existing.remove();
        const res = await fetch(url, { headers: { 'X-Requested-With':'XMLHttpRequest' } });
        if(!res.ok){ window.location.href = url; return; }
        const text = await res.text();
        const tmp = document.createElement('div'); tmp.innerHTML = text;
        const modalEl = tmp.querySelector('#editEventModal');
        if(!modalEl){ window.location.href = url; return; }
        // Inject forced label color styles into the modal (AJAX responses may lack page-level styles)
        try{
            const css = `#editEventModal #editEventForm label,#editEventModal #editEventForm .form-label,#editEventModal #editEventForm small,#editEventModal #editEventForm .form-text,#editEventModal #editEventForm .input-group-text{color:#000!important} #editEventModal ::placeholder{color:#000!important;opacity:1!important} #editEventModal .modal-dialog{margin-top:8px!important;}`;
            const styleEl = document.createElement('style'); styleEl.type = 'text/css'; styleEl.appendChild(document.createTextNode(css));
            modalEl.insertBefore(styleEl, modalEl.firstChild);
        }catch(e){}
        document.body.appendChild(modalEl);
        initEditEventLocationAndBenefits(modalEl);
        initEditEventDynamicTables(modalEl);
        initEditEventSpeakers(modalEl);
        initEditEventAutocomplete(modalEl);
        if(typeof enableDraggableModal === 'function') try{ enableDraggableModal(modalEl); }catch(_){}
        if(window.bootstrap && typeof bootstrap.Modal === 'function'){
            const m = new bootstrap.Modal(modalEl, { backdrop: 'static', keyboard: false });
            m.show();
        } else {
            modalEl.classList.add('show'); modalEl.style.display = 'block';
        }
    } catch(err){
        console.error('Edit modal load failed', err);
        window.location.href = url;
    }
});
</script>
