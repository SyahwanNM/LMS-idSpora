@extends('layouts.admin')

@section('title', 'Antrian Sertifikat Trainer')

@section('navbar')
    @include('partials.navbar-admin-trainer')
@endsection

@section('styles')
    <style>
        /* Match admin trainer layout */
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

        /* Hero: keep gradient, fix font contrast */
        .queue-hero {
            background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
            border-radius: 24px;
            padding: 40px;
            color: #fff;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
        }

        .queue-hero::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(138, 43, 226, 0.25) 0%, rgba(138, 43, 226, 0) 70%);
            border-radius: 50%;
            z-index: 1;
        }

        .queue-hero::before {
            content: '';
            position: absolute;
            bottom: -20%;
            left: -5%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(65, 105, 225, 0.2) 0%, rgba(65, 105, 225, 0) 70%);
            border-radius: 50%;
            z-index: 1;
        }

        .queue-card {
            border: 0;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .queue-table thead th {
            background: #f8f9ff;
            color: #1a237e;
            font-weight: 700;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: .04em;
            padding: 14px 16px;
            border-bottom: 1px solid #e9ecef;
        }

        .queue-table tbody td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        .badge-pending {
            background: rgba(251, 191, 36, 0.18);
            color: #a16207;
            border: 1px solid rgba(251, 191, 36, 0.35);
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
        }
    </style>
    @include('admin.trainer._top-text-color')
@endsection

@section('content')
    <div class="trainer-wrapper">
        @include('admin.trainer._sidebar')

        <main class="trainer-main">
            <div class="queue-hero">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3" style="position: relative; z-index: 2;">
                    <div>
                        <h1 class="mb-2" style="font-size: 2.2rem; font-weight: 900;">
                            <i class="bi bi-award-fill me-2"></i>Antrian Sertifikat Trainer
                        </h1>
                        <p class="mb-0" style="opacity: .88; max-width: 760px;">
                            Daftar trainer yang punya kelas/event <b>selesai</b> tetapi sertifikatnya belum diterbitkan/dikirim.
                            Pilih trainer untuk lanjut ke halaman penerbitan/kirim sertifikat.
                        </p>
                    </div>
                    <form method="GET" class="d-flex gap-2" style="min-width: 320px;">
                        <input name="search" value="{{ $search ?? '' }}" class="form-control"
                            placeholder="Cari trainer (nama/email/wa)..." style="border-radius: 14px;">
                        <button class="btn btn-light" style="border-radius: 14px; font-weight: 700;">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="card queue-card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table queue-table mb-0">
                            <thead>
                                <tr>
                                    <th>Trainer</th>
                                    <th style="width: 160px;">Pending (Event)</th>
                                    <th style="width: 170px;">Pending (Course)</th>
                                    <th style="width: 160px;">Total Pending</th>
                                    <th class="text-end" style="width: 180px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($trainers as $t)
                                    <tr>
                                        <td>
                                            <div class="fw-bold" style="color:#1a237e;">{{ $t->name }}</div>
                                            <div class="small text-muted">{{ $t->email }}</div>
                                        </td>
                                        <td>
                                            <span class="badge-pending">{{ (int) ($t->pending_events_certificates ?? 0) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-pending">{{ (int) ($t->pending_courses_certificates ?? 0) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge-pending">{{ (int) ($t->pending_certificates_count ?? 0) }}</span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.trainer.certificates.send.form', $t) }}"
                                                class="btn btn-primary btn-sm"
                                                style="border-radius: 12px; font-weight: 700;">
                                                Kirim Sertif
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Tidak ada antrian sertifikat saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if(method_exists($trainers, 'hasPages') && $trainers->hasPages())
                <div class="mt-3">
                    {{ $trainers->links() }}
                </div>
            @endif
        </main>
    </div>
@endsection

