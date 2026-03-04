@extends('layouts.admin')

@section('title', 'Laporan Keuangan Course')

@section('navbar')
    @include('partials.navbar-finance')
@endsection

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    :root {
        --ids-primary: #FFB703;
        --ids-secondary: #FB8500;
        --ids-bg: #F8F9FA;
        --ids-card-bg: #FFFFFF;
        --ids-text-main: #1A1D1F;
        --ids-text-muted: #6F767E;
        --ids-border: #EFEFEF;
    }

    body {
        background-color: var(--ids-bg) !important;
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    .finance-wrapper {
        display: flex;
        min-height: calc(100vh - 100px);
        margin: 0 -12px;
    }

    .finance-sidebar {
        width: 240px;
        background: #fff;
        padding: 24px;
        border-right: 1px solid var(--ids-border);
        display: none;
    }

    @media (min-width: 992px) {
        .finance-sidebar { display: block; }
    }

    .nav-menu-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        color: var(--ids-text-muted);
        letter-spacing: 1px;
        margin-bottom: 16px;
        display: block;
    }

    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        color: var(--ids-text-main);
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 4px;
        transition: all 0.2s;
    }

    .sidebar-link i {
        font-size: 1.2rem;
        margin-right: 12px;
        color: var(--ids-text-muted);
    }

    .sidebar-link:hover { background: #F4F4F4; color: var(--ids-text-main); }
    .sidebar-link.active { background: #FEF6E6; color: var(--ids-text-main); }
    .sidebar-link.active i { color: var(--ids-secondary); }

    .badge-notif {
        background: #D93F3F;
        color: #fff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        margin-left: auto;
    }

    .finance-main { flex: 1; padding: 24px; }

    .card-premium {
        background: #fff;
        border: 1px solid var(--ids-border);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
    }

    .table thead th {
        background: #F8F9FA;
        border-bottom: 1px solid var(--ids-border);
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        padding: 16px;
        color: var(--ids-text-muted);
    }

    .table tbody td {
        padding: 16px;
        vertical-align: middle;
        font-size: 0.95rem;
        border-bottom: 1px solid var(--ids-border);
    }

    .course-pill { display: flex; align-items: center; }
    .course-img {
        width: 64px;
        height: 36px;
        object-fit: cover;
        border-radius: 6px;
        margin-right: 12px;
    }

    .revenue-text { font-weight: 700; color: #0284c7; }
    .pending-text { color: #856404; font-size: 0.85rem; }

    .finance-hero {
        background: linear-gradient(135deg, #1A1D1F 0%, #33383C 100%);
        border-radius: 24px;
        padding: 32px;
        color: #fff;
        margin-bottom: 32px;
    }
</style>
@endsection

@section('content')
<div class="finance-wrapper" style="margin-top: 0;">
    @include('partials.finance-sidebar')

    <main class="finance-main">
        <div class="finance-hero">
            <h1 class="fw-bold">Laporan Per Course</h1>
            <p class="text-white-50 mb-0">Rincian penjualan dan performa pendaftaran pada setiap materi pelatihan.</p>
        </div>

        <div class="card-premium">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Course</th>
                            <th>Total Enrollment</th>
                            <th>Enrollment Aktif</th>
                            <th>Total Sales</th>
                            <th>Potential (Pending)</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courses as $course)
                            <tr>
                                <td>
                                    <div class="course-pill">
                                        <img src="{{ \Illuminate\Support\Str::startsWith($course->image, 'http') ? $course->image : asset('uploads/' . $course->image) }}" class="course-img">
                                        <div>
                                            <div class="fw-bold">{{ $course->name }}</div>
                                            <div class="text-muted small">ID: #{{ $course->id }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $course->total_enrollments }}</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $course->active_enrollments }}</span>
                                </td>
                                <td>
                                    <div class="revenue-text">Rp {{ number_format($course->revenue, 0, ',', '.') }}</div>
                                </td>
                                <td>
                                    <div class="pending-text text-muted">Rp {{ number_format($course->pending_revenue, 0, ',', '.') }}</div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.finance.course-detail', $course->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">Belum ada data course.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $courses->links() }}
            </div>
        </div>
    </main>
</div>
@endsection
