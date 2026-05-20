@extends('layouts.admin-trainer')

@section('title', 'Sertifikat Trainer')

@push('admin-trainer-styles')
<style>
    .certificate-hero {
        background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
        padding: 32px;
        border-radius: 24px;
        color: white;
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 18px 36px rgba(26, 35, 126, 0.18);
    }

    .certificate-hero::after {
        content: '';
        position: absolute;
        top: -80px;
        right: -80px;
        width: 260px;
        height: 260px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(255,255,255,.22) 0%, rgba(255,255,255,0) 70%);
    }

    .certificate-hero > * {
        position: relative;
        z-index: 2;
    }

    .certificate-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .certificate-subtitle {
        margin: 0;
        color: rgba(255,255,255,.86);
    }

    .stat-card {
        background: #fff;
        border-radius: 18px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        height: 100%;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.06);
    }

    .stat-value {
        font-size: 30px;
        font-weight: 800;
        color: #1a237e;
        line-height: 1;
        margin-bottom: 8px;
    }

    .stat-label {
        color: #64748b;
        font-size: 14px;
        font-weight: 600;
    }

    .filter-card {
        background: #fff;
        border-radius: 18px;
        padding: 18px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
        margin-bottom: 24px;
    }

    .certificate-table-card {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.06);
    }

    .certificate-table th {
        background: #f8f9ff;
        color: #1a237e;
        font-size: 13px;
        font-weight: 800;
        padding: 16px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .certificate-table td {
        padding: 16px;
        vertical-align: middle;
        border-color: #eef2f7;
    }

    .trainer-avatar {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .pending-badge {
        background: #fff7ed;
        color: #c2410c;
        border: 1px solid #fed7aa;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .published-badge {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #bbf7d0;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-manage {
        background: #3949ab;
        color: #fff;
        border: 0;
        border-radius: 10px;
        padding: 8px 14px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-manage:hover {
        background: #283593;
        color: #fff;
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
        color: #64748b;
    }

    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
        display: block;
        margin-bottom: 12px;
    }

    @media (max-width: 768px) {
        .certificate-hero {
            padding: 24px;
            border-radius: 18px;
        }

        .certificate-title {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('admin-trainer-content')
@php
    $trainers = $trainers ?? collect();
    $totalTrainers = $totalTrainers ?? 0;
    $totalCertificates = $totalCertificates ?? 0;
    $totalPending = $totalPending ?? 0;
@endphp

<div class="certificate-hero">
    <h1 class="certificate-title">
        <i class="bi bi-award-fill"></i>
        Sertifikat Trainer
    </h1>

    <p class="certificate-subtitle">
        Kelola konfigurasi, penerbitan, dan monitoring sertifikat trainer.
    </p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-value">{{ $totalTrainers }}</div>
            <div class="stat-label">Total Trainer</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-value">{{ $totalCertificates }}</div>
            <div class="stat-label">Sertifikat Terbit</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-value">{{ $totalPending }}</div>
            <div class="stat-label">Menunggu Sertifikat</div>
        </div>
    </div>
</div>

<div class="filter-card">
    <form method="GET">
        <div class="row g-3 align-items-center">
            <div class="col-md-9">
                <input type="text"
                       name="search"
                       class="form-control"
                       placeholder="Cari nama, email, atau nomor HP trainer..."
                       value="{{ request('search') }}">
            </div>

            <div class="col-md-3">
                <button class="btn btn-primary w-100" style="background:#3949ab;border:none;font-weight:700;">
                    <i class="bi bi-search me-1"></i>
                    Cari
                </button>
            </div>
        </div>
    </form>
</div>

<div class="certificate-table-card">
    <div class="table-responsive">
        <table class="table certificate-table mb-0">
            <thead>
                <tr>
                    <th>Trainer</th>
                    <th>Email</th>
                    <th class="text-center">Pending</th>
                    <th class="text-center">Terbit</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($trainers as $trainer)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($trainer->name) }}&background=3949ab&color=fff&bold=true"
                                     class="trainer-avatar"
                                     alt="{{ $trainer->name }}">

                                <div>
                                    <div class="fw-bold" style="color:#1a237e;">
                                        {{ $trainer->name }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $trainer->profession ?? 'Trainer' }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            {{ $trainer->email }}
                        </td>

                        <td class="text-center">
                            <span class="pending-badge">
                                <i class="bi bi-hourglass-split"></i>
                                {{ $trainer->pending_certificates_count ?? 0 }}
                            </span>
                        </td>

                        <td class="text-center">
                            <span class="published-badge">
                                <i class="bi bi-check-circle-fill"></i>
                                {{ $trainer->published_certificates_count ?? 0 }}
                            </span>
                        </td>

                        <td class="text-end">
                            <a href="{{ route('admin.trainer.certificates.show', $trainer) }}"
                               class="btn-manage">
                                <i class="bi bi-gear-fill"></i>
                                Kelola
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <div class="fw-bold mb-1">Belum ada trainer</div>
                                <div>Data trainer akan muncul di halaman ini.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(method_exists($trainers, 'links') && $trainers->hasPages())
        <div class="p-4 border-top">
            {{ $trainers->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection