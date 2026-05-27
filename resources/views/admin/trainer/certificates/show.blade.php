@extends('layouts.admin-trainer')

@section('title', 'Penerbitan Sertifikat')

@php
    $tab = request('tab', 'items');
    $pendingItems = $pendingItems ?? collect();
    $certificates = $certificates ?? collect();
@endphp

@push('admin-trainer-styles')
<style>
    :root {
        --cert-primary: #2f3fcb;
        --cert-primary-2: #4858db;
        --cert-border: #e6eaf2;
        --cert-muted: #6b7a99;
        --cert-success: #059669;
    }

    .publish-page { width: 100%; }

    .publish-breadcrumb {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
        font-size: 13px;
        color: #74809a;
    }

    .back-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        border: 1px solid var(--cert-border);
        background: #fff;
        color: var(--cert-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .publish-hero {
        background: linear-gradient(135deg, #2935b8 0%, #4858db 58%, #dce3ff 100%);
        border-radius: 20px;
        padding: 34px 36px;
        color: #fff;
        min-height: 185px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(47,63,203,.14);
        margin-bottom: 28px;
    }

    .publish-hero::after {
        content: '';
        position: absolute;
        right: 55px;
        top: 28px;
        width: 250px;
        height: 125px;
        border-radius: 26px;
        background: rgba(255,255,255,.18);
    }

    .hero-content {
        position: relative;
        z-index: 2;
        max-width: 620px;
    }

    .page-eyebrow {
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: rgba(255,255,255,.9);
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 18px;
    }

    .page-eyebrow::before {
        content: '';
        width: 22px;
        height: 2px;
        background: rgba(255,255,255,.9);
        border-radius: 999px;
    }

    .publish-hero h1 {
        font-size: 32px;
        font-weight: 900;
        margin: 0 0 12px;
        letter-spacing: -.6px;
    }

    .publish-hero p {
        margin: 0;
        font-size: 15px;
        line-height: 1.7;
        color: rgba(255,255,255,.95);
    }

    .publish-tabs {
        display: flex;
        gap: 8px;
        margin-bottom: 0;
    }

    .publish-tab-btn {
        min-width: 200px;
        height: 48px;
        border: 1px solid var(--cert-border);
        border-bottom: 0;
        background: #fff;
        color: #64748b;
        border-radius: 14px 14px 0 0;
        font-weight: 900;
        font-size: 14px;
    }

    .publish-tab-btn.active {
        color: var(--cert-primary);
        border-top: 3px solid var(--cert-primary);
    }

    .table-card {
        background: #fff;
        border: 1px solid var(--cert-border);
        border-radius: 0 20px 20px 20px;
        box-shadow: 0 12px 28px rgba(15,23,42,.06);
        padding: 24px;
    }

    .filter-row {
        display: grid;
        grid-template-columns: 1.6fr 1fr 180px;
        gap: 18px;
        margin-bottom: 24px;
    }

    .search-box {
        position: relative;
    }

    .search-box i {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .filter-input {
        width: 100%;
        height: 46px;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        padding: 0 16px;
        background: #fff;
        outline: none;
        color: #0f172a;
        font-size: 14px;
    }

    .search-box .filter-input {
        padding-left: 48px;
    }

    .reset-btn {
        height: 46px;
        border: 1px solid #dbe3ef;
        border-radius: 10px;
        background: #fff;
        color: #334155;
        font-weight: 800;
    }

    .cert-table-wrap {
        border: 1px solid var(--cert-border);
        border-radius: 12px;
        overflow: hidden;
    }

    .cert-table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .cert-table thead th {
        background: #f8fafc;
        color: #6b7a99;
        font-size: 11px;
        font-weight: 900;
        letter-spacing: .04em;
        text-transform: uppercase;
        padding: 14px 16px;
        white-space: nowrap;
    }

    .cert-table tbody td {
        padding: 16px;
        border-top: 1px solid var(--cert-border);
        vertical-align: middle;
        font-size: 13px;
        color: #0f172a;
    }

    .program-cell {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .program-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #eef1ff;
        color: var(--cert-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .program-icon.green {
        background: #dcfce7;
        color: #059669;
    }

    .program-title {
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .program-subtitle {
        color: #6b7a99;
        font-size: 12px;
    }

    .type-badge {
        background: #eef1ff;
        color: var(--cert-primary);
        padding: 5px 10px;
        border-radius: 7px;
        font-size: 11px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 11px;
        font-weight: 900;
    }

    .status-ready,
    .status-done {
        background: #dcfce7;
        color: #15803d;
    }

    .btn-publish {
        height: 34px;
        min-width: 96px;
        border: 0;
        border-radius: 8px;
        background: linear-gradient(135deg,#2f3fcb,#2636bd);
        color: #fff;
        font-size: 12px;
        font-weight: 900;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        padding: 0 12px;
    }

    .btn-publish:hover {
        color: #fff;
        filter: brightness(.96);
    }

    .more-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid #dbe3ef;
        background: #fff;
        color: var(--cert-primary);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .more-btn:hover {
        background: #eef1ff;
        color: var(--cert-primary);
    }

    .pagination-mini {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-top: 22px;
    }

    .page-btn {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid var(--cert-border);
        background: #fff;
        color: #64748b;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
    }

    .page-btn.active {
        background: var(--cert-primary);
        color: #fff;
        border-color: var(--cert-primary);
    }

    .side-panel {
        background: #fff;
        border: 1px solid var(--cert-border);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 12px 28px rgba(15,23,42,.06);
        position: sticky;
        top: 96px;
    }

    .side-content {
        padding: 26px 24px;
    }

    .guide-title {
        font-size: 17px;
        font-weight: 900;
        color: var(--cert-primary);
        margin-bottom: 18px;
    }

    .guide-desc {
        color: var(--cert-muted);
        font-size: 13px;
        line-height: 1.6;
        margin-bottom: 18px;
    }

    .guide-item {
        display: flex;
        gap: 14px;
        padding: 20px 0;
        border-bottom: 1px solid var(--cert-border);
    }

    .guide-icon {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        background: #eef1ff;
        color: var(--cert-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .guide-item h6 {
        font-size: 13px;
        font-weight: 900;
        color: #0f172a;
        margin: 0 0 8px;
    }

    .guide-item p {
        margin: 0;
        color: var(--cert-muted);
        font-size: 12px;
        line-height: 1.6;
    }

    .side-footer {
        padding: 24px;
        border-top: 1px solid var(--cert-border);
        text-align: center;
    }

    .template-link {
        width: 100%;
        height: 48px;
        border-radius: 12px;
        background: #eef1ff;
        color: var(--cert-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        font-weight: 900;
        margin-bottom: 16px;
    }

    .template-link:hover {
        color: var(--cert-primary);
    }

    .empty-state {
        padding: 44px;
        text-align: center;
        color: #64748b;
    }

    .custom-pane { display: none; }
    .custom-pane.active { display: block; }

    @media(max-width:1200px) {
        .filter-row { grid-template-columns: 1fr; }
    }

    @media(max-width:768px) {
        .publish-tabs { flex-direction: column; }

        .publish-tab-btn {
            width: 100%;
            border: 1px solid var(--cert-border);
            border-radius: 12px;
        }

        .table-card {
            border-radius: 20px;
        }
    }
</style>
@endpush

@section('admin-trainer-content')
<div class="publish-page">

    <div class="publish-breadcrumb">
        <a href="{{ route('admin.trainer.certificates.index') }}" class="back-btn">
            <i class="bi bi-chevron-left"></i>
        </a>
        <span>Sertifikat & Penghargaan</span>
        <i class="bi bi-chevron-right"></i>
        <strong class="text-primary">Penerbitan Sertifikat</strong>
    </div>


    <div class="row g-4">
        <div class="col-xl-9">
            <section class="publish-hero">
                <div class="hero-content">
                    <div class="page-eyebrow">Recognition System</div>
                    <h1>Penerbitan Sertifikat</h1>
                    <p>
                        Kelola dan terbitkan sertifikat untuk event dan kursus
                        yang telah selesai dilaksanakan.
                    </p>
                </div>
            </section>

            <div class="publish-tabs">
                <button type="button"
                    class="publish-tab-btn custom-tab-btn {{ $tab === 'items' ? 'active' : '' }}"
                    data-target="items-pane">
                    Event / Course
                </button>

                <button type="button"
                    class="publish-tab-btn custom-tab-btn {{ $tab === 'history' ? 'active' : '' }}"
                    data-target="history-pane">
                    Riwayat Penerbitan
                </button>
            </div>

            <div class="table-card">
                <div class="filter-row">
                    <div class="search-box">
                        <i class="bi bi-search"></i>
                        <input type="text"
                            id="certSearch"
                            class="filter-input"
                            placeholder="Cari berdasarkan nama event atau kursus...">
                    </div>

                    <select id="statusFilter" class="filter-input">
                        <option value="all">Semua Status</option>
                        <option value="ready">Siap Terbit</option>
                        <option value="done">Diterbitkan</option>
                    </select>

                    <button type="button" id="resetFilter" class="reset-btn">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>
                        Reset Filter
                    </button>
                </div>

                <div class="custom-pane {{ $tab === 'items' ? 'active' : '' }}" id="items-pane">
                    <div class="cert-table-wrap">
                        <table class="cert-table">
                            <thead>
                                <tr>
                                    <th>Event / Course</th>
                                    <th>Tipe</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Peserta</th>
                                    <th>Sertifikat</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($pendingItems as $item)
                                    @php
                                        $itemDate = !empty($item['date']) ? \Carbon\Carbon::parse($item['date']) : null;
                                        $context = $item['context'] ?? 'event';
                                        $type = $item['type'] ?? ucfirst($context);
                                        $title = $item['title'] ?? '-';
                                    @endphp

                                    <tr class="cert-row"
                                        data-title="{{ strtolower($title) }}"
                                        data-status="ready">
                                        <td>
                                            <div class="program-cell">
                                                <div class="program-icon {{ $context === 'course' ? 'green' : '' }}">
                                                    <i class="bi {{ $context === 'course' ? 'bi-mortarboard' : 'bi-calendar-event' }}"></i>
                                                </div>

                                                <div>
                                                    <div class="program-title">{{ $title }}</div>
                                                    <div class="program-subtitle">{{ $type }}</div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="type-badge">{{ strtoupper($context) }}</span>
                                        </td>

                                        <td>
                                            {{ $itemDate ? $itemDate->translatedFormat('d M Y') : '-' }}
                                        </td>

                                        <td>
                                            <strong>{{ $item['participants_count'] ?? 0 }}</strong>
                                            <div class="program-subtitle">Peserta</div>
                                        </td>

                                        <td>
                                            <strong>0</strong>
                                            <div class="program-subtitle">Diterbitkan</div>
                                        </td>

                                        <td>
                                            <span class="status-pill status-ready">
                                                <i class="bi bi-circle-fill" style="font-size:7px;"></i>
                                                Siap Terbit
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <form method="POST"
                                                action="{{ route('admin.trainer.certificates.publish', [
                                                    'trainer' => $trainer->id,
                                                    'context' => $context,
                                                    'id' => $item['id'],
                                                ]) }}"
                                                class="d-inline">
                                                @csrf

                                                <button type="submit" class="btn-publish">
                                                    Terbitkan
                                                    <i class="bi bi-chevron-right"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                Belum ada event/course yang siap diterbitkan.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="custom-pane {{ $tab === 'history' ? 'active' : '' }}" id="history-pane">
                    <div class="cert-table-wrap">
                        <table class="cert-table">
                            <thead>
                                <tr>
                                    <th>Program</th>
                                    <th>Tipe</th>
                                    <th>Tanggal Terbit</th>
                                    <th>Total Peserta</th>
                                    <th>Diterbitkan Oleh</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($certificates as $certificate)
                                    @php
                                        $certifiable = $certificate->certifiable;
                                        $programTitle = $certifiable?->title ?? $certifiable?->name ?? '-';
                                        $programType = class_basename($certificate->certifiable_type ?? 'Event');
                                        $issuedDate = $certificate->issued_at ?? $certificate->created_at;
                                        $isCourse = strtolower($programType) === 'course';
                                    @endphp

                                    <tr class="cert-row"
                                        data-title="{{ strtolower($programTitle) }}"
                                        data-status="done">
                                        <td>
                                            <div class="program-cell">
                                                <div class="program-icon {{ $isCourse ? 'green' : '' }}">
                                                    <i class="bi {{ $isCourse ? 'bi-mortarboard' : 'bi-calendar-event' }}"></i>
                                                </div>

                                                <div>
                                                    <div class="program-title">{{ $programTitle }}</div>
                                                    <div class="program-subtitle">
                                                        {{ $isCourse ? 'Kursus' : 'Webinar' }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <span class="type-badge">
                                                {{ strtoupper($programType) }}
                                            </span>
                                        </td>

                                        <td>
                                            {{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->translatedFormat('d M Y') : '-' }}
                                            <div class="program-subtitle">
                                                {{ $issuedDate ? \Carbon\Carbon::parse($issuedDate)->format('H:i') . ' WIB' : '' }}
                                            </div>
                                        </td>

                                        <td>
                                            <strong>{{ $certificate->participants_count ?? $certificate->total_participants ?? '-' }}</strong>
                                            <div class="program-subtitle">Peserta</div>
                                        </td>

                                        <td>
                                            {{ $certificate->issuer?->name ?? 'Admin idSpora' }}
                                        </td>

                                        <td>
                                            <span class="status-pill status-done">
                                                <i class="bi bi-circle-fill" style="font-size:7px;"></i>
                                                Diterbitkan
                                            </span>
                                        </td>

                                        <td class="text-end">
                                            <a href="{{ route('admin.trainer.certificates.detail', [
                                                'certificate' => $certificate->id,
                                            ]) }}"
                                            class="more-btn">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">
                                            <div class="empty-state">
                                                Belum ada riwayat penerbitan sertifikat.
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="pagination-mini">
                    <a href="#" class="page-btn">
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <a href="#" class="page-btn active">1</a>
                    <a href="#" class="page-btn">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-3">
            <aside class="side-panel">
                <div class="side-content">
                    <h5 class="guide-title">Panduan Penerbitan</h5>
                    <p class="guide-desc">
                        Ikuti langkah-langkah berikut untuk menerbitkan sertifikat:
                    </p>

                    <div class="guide-item">
                        <div class="guide-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div>
                            <h6>Pilih Event / Course</h6>
                            <p>Pilih event atau kursus yang sudah selesai.</p>
                        </div>
                    </div>

                    <div class="guide-item">
                        <div class="guide-icon">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h6>Cek Peserta</h6>
                            <p>Pastikan semua peserta telah terdaftar dengan status lulus.</p>
                        </div>
                    </div>

                    <div class="guide-item">
                        <div class="guide-icon">
                            <i class="bi bi-file-earmark-check"></i>
                        </div>
                        <div>
                            <h6>Terbitkan Sertifikat</h6>
                            <p>Klik tombol terbitkan untuk membuat sertifikat secara massal.</p>
                        </div>
                    </div>

                    <div class="guide-item">
                        <div class="guide-icon">
                            <i class="bi bi-download"></i>
                        </div>
                        <div>
                            <h6>Unduh & Kirim</h6>
                            <p>Unduh sertifikat dan kirim ke peserta melalui email atau bagikan link.</p>
                        </div>
                    </div>
                </div>

                <div class="side-footer">
                    <a href="{{ route('admin.trainer.certificates.index') }}" class="template-link">
                        <i class="bi bi-gear"></i>
                        Kelola Template
                    </a>

                    <p class="guide-desc mb-0">
                        Kelola desain template sertifikat yang digunakan.
                    </p>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection

@push('admin-trainer-scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabBtns = document.querySelectorAll('.custom-tab-btn');
        const panes = document.querySelectorAll('.custom-pane');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function () {
                tabBtns.forEach(b => b.classList.remove('active'));
                panes.forEach(p => p.classList.remove('active'));

                this.classList.add('active');

                const target = this.getAttribute('data-target');
                document.getElementById(target)?.classList.add('active');

                const tab = target === 'history-pane' ? 'history' : 'items';
                const url = new URL(window.location);
                url.searchParams.set('tab', tab);
                window.history.pushState({}, '', url);
            });
        });

        const searchInput = document.getElementById('certSearch');
        const statusFilter = document.getElementById('statusFilter');
        const resetFilter = document.getElementById('resetFilter');

        function runFilter() {
            const term = (searchInput?.value || '').toLowerCase().trim();
            const status = statusFilter?.value || 'all';

            document.querySelectorAll('.cert-row').forEach(row => {
                const title = row.getAttribute('data-title') || '';
                const rowStatus = row.getAttribute('data-status') || '';

                const matchSearch = term === '' || title.includes(term);
                const matchStatus = status === 'all' || rowStatus === status;

                row.style.display = matchSearch && matchStatus ? '' : 'none';
            });
        }

        searchInput?.addEventListener('input', runFilter);
        statusFilter?.addEventListener('change', runFilter);

        resetFilter?.addEventListener('click', function () {
            if (searchInput) searchInput.value = '';
            if (statusFilter) statusFilter.value = 'all';
            runFilter();
        });
    });
</script>
@endpush