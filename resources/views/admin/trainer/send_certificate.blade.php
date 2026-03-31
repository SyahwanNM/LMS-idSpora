@extends('layouts.admin')

@section('title', 'Kirim Sertifikat Trainer')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        .trainer-wrapper {
            display: flex;
            min-height: calc(100vh - 72px);
            overflow-x: hidden;
        }

        .trainer-sidebar {
            width: 260px;
            background: #fff;
            padding: 24px 16px;
            border-right: 1px solid #eee;
            flex-shrink: 0;
            position: sticky;
            top: 72px;
            height: calc(100vh - 72px);
            overflow-y: auto;
        }

        .trainer-main {
            flex-grow: 1;
            min-width: 0;
            padding: 32px;
            background-color: #F8F9FA;
            overflow-x: auto;
        }

        .nav-menu-label {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 1px;
            margin-bottom: 12px;
            margin-top: 24px;
            display: block;
            padding-left: 16px;
        }

        .nav-menu-label:first-child {
            margin-top: 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 11px 16px;
            color: #1e293b;
            text-decoration: none;
            border-radius: 10px;
            margin-bottom: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            gap: 12px;
        }

        .sidebar-link i {
            font-size: 1.15rem;
            color: #64748b;
            transition: color 0.2s ease;
        }

        .sidebar-link:hover {
            background-color: #f8fafc;
            color: #3949ab;
        }

        .sidebar-link:hover i {
            color: #3949ab;
        }

        .sidebar-link.active {
            background-color: #3949ab;
            color: #fff;
        }

        .sidebar-link.active i {
            color: #fff;
        }

        .sidebar-parent {
            justify-content: space-between;
        }

        .sidebar-parent .sidebar-chevron {
            font-size: 0.8rem;
            transition: transform 0.2s ease;
        }

        .sidebar-parent[aria-expanded='true'] .sidebar-chevron {
            transform: rotate(180deg);
        }

        .sidebar-submenu {
            margin: 4px 0 8px;
        }

        .sidebar-submenu .sidebar-link {
            margin-left: 14px;
            padding: 7px 10px;
            font-size: 0.82rem;
            border-radius: 8px;
        }

        .sidebar-submenu .sidebar-link i {
            font-size: 0.95rem;
        }

        .send-hero {
            background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
            border-radius: 24px;
            padding: 36px;
            color: #fff;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .send-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 360px;
            height: 360px;
            background: radial-gradient(circle, rgba(138, 43, 226, 0.22) 0%, rgba(138, 43, 226, 0) 70%);
            border-radius: 50%;
            z-index: 1;
        }

        .panel-card {
            border: 0;
            border-radius: 18px;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        }

        .panel-card .card-header {
            background: #f8f9ff;
            border-bottom: 1px solid #e9ecef;
            color: #1a237e;
            font-weight: 800;
        }

        .send-table thead th {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #334155;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 16px;
            white-space: nowrap;
            text-align: center;
        }

        .send-table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-color: #eef2f7;
        }

        .inline-control {
            min-width: 90px;
            border-radius: 10px;
            border: 1px solid #dbe2ea;
            padding: 6px 10px;
            font-size: 12px;
            background: #fff;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #dbe2ea;
            background: #fff;
            color: #334155;
        }

        .icon-btn:hover {
            border-color: #9aa8bc;
            background: #f8fafc;
            color: #0f172a;
        }

        .mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-size: 12px;
        }
    </style>
    @include('admin.trainer._top-text-color')
@endsection

@section('content')
    <div class="trainer-wrapper">
        @include('admin.trainer._sidebar')

        <main class="trainer-main">
            <div class="send-hero">
                <div style="position:relative; z-index:2;">
                    <h1 class="mb-2" style="font-size:2rem; font-weight:900;">
                        <i class="bi bi-award-fill me-2"></i>Kirim Sertifikat - {{ $trainer->name }}
                    </h1>
                    <p class="mb-0" style="opacity:.88;">
                        Gunakan template sertifikat yang sama dengan CRM. Aksi utama di halaman ini: <b>Kirim</b> dan <b>Lihat sertifikat terkirim</b>.
                    </p>
                </div>
            </div>

            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                </div>
            @endif
            @if(session('info'))
                <div class="alert alert-info border-0 shadow-sm">
                    <i class="bi bi-info-circle-fill me-2"></i>{{ session('info') }}
                </div>
            @endif

            <div class="row g-4">
                <div class="col-12">
                    <div class="card panel-card">
                        <div class="card-header">Daftar Kelas Selesai - Siap Dikirim</div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0 send-table">
                                    <thead>
                                        <tr>
                                            <th style="width:90px;">Tipe</th>
                                            <th style="width:120px;">Tanggal</th>
                                            <th>Nama Kelas / Event</th>
                                            <th style="width:110px;">Kegiatan</th>
                                            <th style="width:100px;">Jenis</th>
                                            <th style="width:90px;">Urut</th>
                                            <th style="width:130px;">Terbit</th>
                                            <th style="width:110px;">Status</th>
                                            <th class="text-end" style="width:120px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($pendingItems ?? collect()) as $item)
                                            @php
                                                $seq = str_pad((string) ($loop->iteration), 3, '0', STR_PAD_LEFT);
                                                $dateText = !empty($item['date']) ? \Carbon\Carbon::parse($item['date'])->format('d M Y') : '-';
                                                $rowId = 'row_' . $item['context'] . '_' . $item['context_id'];
                                                $issuedAt = now()->format('Y-m-d');
                                            @endphp
                                            <tr class="cert-row" data-row="{{ $rowId }}">
                                                <td class="fw-semibold">{{ strtoupper($item['context']) }}</td>
                                                <td>{{ $dateText }}</td>
                                                <td>{{ $item['title'] }}</td>
                                                <td>
                                                    <select class="inline-control cert-activity">
                                                        <option value="WBN" {{ $item['activity_code']==='WBN' ? 'selected' : '' }}>WBN</option>
                                                        <option value="SMN" {{ $item['activity_code']==='SMN' ? 'selected' : '' }}>SMN</option>
                                                        <option value="WRT" {{ $item['activity_code']==='WRT' ? 'selected' : '' }}>WRT</option>
                                                        <option value="VDP" {{ $item['activity_code']==='VDP' ? 'selected' : '' }}>VDP</option>
                                                        <option value="ELR" {{ $item['activity_code']==='ELR' ? 'selected' : '' }}>ELR</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="inline-control cert-type">
                                                        <option value="TRN">TRN</option>
                                                        <option value="MC">MC</option>
                                                        <option value="MOD">MOD</option>
                                                        <option value="PNT">PNT</option>
                                                        <option value="CLB">CLB</option>
                                                        <option value="SRT">SRT</option>
                                                        <option value="GRD">GRD</option>
                                                        <option value="SPV">SPV</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input value="{{ $seq }}" class="inline-control cert-seq" />
                                                </td>
                                                <td>
                                                    <input type="date" value="{{ $issuedAt }}" class="inline-control cert-issued" />
                                                </td>
                                                <td><span class="badge bg-success-subtle text-success">Selesai</span></td>
                                                <td class="text-end">
                                                    <div class="d-inline-flex align-items-center">
                                                        <button type="button"
                                                            class="icon-btn me-2 preview-btn"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Preview Data"
                                                            data-title="{{ $item['title'] }}"
                                                            data-context="{{ strtoupper($item['context']) }}">
                                                            <i class="bi bi-eye"></i>
                                                        </button>

                                                        <form method="POST" action="{{ route('admin.trainer.certificates.issue', $trainer) }}" class="d-inline cert-send-form">
                                                            @csrf
                                                            <input type="hidden" name="context" value="{{ $item['context'] }}">
                                                            <input type="hidden" name="context_id" value="{{ $item['context_id'] }}">
                                                            <input type="hidden" name="activity_code" value="{{ $item['activity_code'] }}" class="hidden-activity">
                                                            <input type="hidden" name="type_code" value="TRN" class="hidden-type">
                                                            <input type="hidden" name="sequence" value="{{ $seq }}" class="hidden-seq">
                                                            <input type="hidden" name="issued_at" value="{{ $issuedAt }}" class="hidden-issued">
                                                            <button id="{{ $rowId }}" type="submit" class="icon-btn send-btn" disabled
                                                                data-bs-toggle="tooltip"
                                                                data-bs-placement="top"
                                                                title="Kirim Sertifikat">
                                                                <i class="bi bi-send-check"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="9" class="text-center text-muted py-4">Semua kelas selesai sudah dikirim sertifikat.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card panel-card">
                        <div class="card-header">Sertifikat Berhasil Dikirim</div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0 send-table">
                                    <thead>
                                        <tr>
                                            <th>No Sertifikat</th>
                                            <th>Konteks</th>
                                            <th>Tanggal Kirim</th>
                                            <th class="text-end">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse(($sentCertificates ?? collect()) as $cert)
                                            @php
                                                $label = $cert->certifiable instanceof \App\Models\Event
                                                    ? ('Event: ' . ($cert->certifiable->title ?? '#'.$cert->certifiable_id))
                                                    : ($cert->certifiable instanceof \App\Models\Course
                                                        ? ('Course: ' . ($cert->certifiable->name ?? '#'.$cert->certifiable_id))
                                                        : 'Manual Upload');
                                            @endphp
                                            <tr>
                                                <td class="mono">{{ $cert->certificate_number }}</td>
                                                <td>{{ $label }}</td>
                                                <td>{{ $cert->issued_at?->format('d M Y H:i') ?? '-' }}</td>
                                                <td class="text-end">
                                                    @if(!empty($cert->file_path))
                                                        <a href="{{ route('admin.trainer.certificates.view', $cert) }}" target="_blank"
                                                            class="icon-btn me-1"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Lihat Sertifikat">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                    @endif
                                                    <form action="{{ route('admin.trainer.certificates.revoke', $cert) }}" method="POST" class="d-inline"
                                                        onsubmit="return confirm('Cabut sertifikat ini?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="icon-btn"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Cabut Sertifikat">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-4">Belum ada sertifikat terkirim.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection

@section('scripts')
<script>
(function () {
    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    [...tooltipTriggerList].forEach(el => new bootstrap.Tooltip(el));

    const modalEl = document.getElementById('previewDataModal');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    const modalBody = modalEl ? modalEl.querySelector('.modal-body') : null;

    const previewUrl = '{{ route('admin.trainer.certificates.preview') }}';
    const trainerId = '{{ $trainer->id }}';

    const padSeq = (value) => String(value || '').replace(/\D/g, '').slice(-3).padStart(3, '0');

    const syncHiddenFields = (row) => {
        const activity = row.querySelector('.cert-activity')?.value || '';
        const type = row.querySelector('.cert-type')?.value || '';
        const seq = padSeq(row.querySelector('.cert-seq')?.value || '');
        const issued = row.querySelector('.cert-issued')?.value || '';

        const hiddenActivity = row.querySelector('.hidden-activity');
        const hiddenType = row.querySelector('.hidden-type');
        const hiddenSeq = row.querySelector('.hidden-seq');
        const hiddenIssued = row.querySelector('.hidden-issued');

        if (hiddenActivity) hiddenActivity.value = activity;
        if (hiddenType) hiddenType.value = type;
        if (hiddenSeq) hiddenSeq.value = seq;
        if (hiddenIssued) hiddenIssued.value = issued;
    };

    document.querySelectorAll('.cert-row').forEach((row) => {
        const previewBtn = row.querySelector('.preview-btn');
        if (!previewBtn) return;

        previewBtn.addEventListener('click', async () => {
            syncHiddenFields(row);
            const activity = row.querySelector('.cert-activity')?.value || '';
            const type = row.querySelector('.cert-type')?.value || '';
            const seq = padSeq(row.querySelector('.cert-seq')?.value || '');
            const issued = row.querySelector('.cert-issued')?.value || '';
            const context = row.querySelector('input[name="context"]')?.value || previewBtn.getAttribute('data-context')?.toLowerCase();
            const contextId = row.querySelector('input[name="context_id"]')?.value || '';

            const form = new FormData();
            form.append('_token', '{{ csrf_token() }}');
            form.append('trainer_id', trainerId);
            form.append('context', context || 'event');
            form.append('context_id', contextId || '0');
            form.append('activity_code', activity || 'WBN');
            form.append('type_code', type || 'TRN');
            form.append('sequence', seq || '001');
            form.append('issued_at', issued || '');

            try {
                const res = await fetch(previewUrl, { method: 'POST', body: form, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error('Preview gagal');
                const html = await res.text();
                if (modalBody) modalBody.innerHTML = html;
                modal?.show();

                // enable send button after preview
                const sendBtn = row.querySelector('.send-btn');
                if (sendBtn) sendBtn.disabled = false;
            } catch (err) {
                alert('Gagal memuat preview sertifikat.');
            }
        });

        row.querySelectorAll('.cert-activity, .cert-type, .cert-seq, .cert-issued').forEach((field) => {
            field.addEventListener('change', () => syncHiddenFields(row));
            field.addEventListener('input', () => syncHiddenFields(row));
        });

        const sendForm = row.querySelector('.cert-send-form');
        if (sendForm) {
            sendForm.addEventListener('submit', () => syncHiddenFields(row));
        }

        syncHiddenFields(row);
    });
})();
</script>

<div class="modal fade" id="previewDataModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius:16px;">
      <div class="modal-header">
        <h5 class="modal-title"><i class="bi bi-eye me-2"></i>Preview Data Sertifikat</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="small text-muted mb-2">Pastikan data sudah benar sebelum klik kirim.</div>
        <table class="table table-sm mb-0">
          <tr><th style="width:120px;">Konteks</th><td id="pvContext"></td></tr>
          <tr><th>Judul</th><td id="pvTitle"></td></tr>
          <tr><th>Kegiatan</th><td id="pvActivity"></td></tr>
          <tr><th>Jenis</th><td id="pvType"></td></tr>
          <tr><th>Urut</th><td id="pvSeq"></td></tr>
          <tr><th>Terbit</th><td id="pvIssued"></td></tr>
          <tr><th>No Sertif</th><td class="mono" id="pvNumber"></td></tr>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection