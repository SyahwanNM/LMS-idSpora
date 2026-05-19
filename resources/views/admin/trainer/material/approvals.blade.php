@extends('layouts.admin-trainer')

@section('title', 'Manage Trainers')

@push('admin-trainer-styles')
<style>
    .trainer-hero {
        background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
        border-radius: 24px;
        padding: 32px 36px;
        color: #fff;
        margin-bottom: 28px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .trainer-hero::after {
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

    .trainer-hero > * {
        position: relative;
        z-index: 2;
    }

    .hero-title {
        font-size: 2.15rem;
        font-weight: 800;
        margin-bottom: 6px;
        letter-spacing: -0.6px;
        display: flex;
        align-items: center;
        line-height: 1.1;
    }

    .hero-title i {
        font-size: 1.6rem;
        flex-shrink: 0;
    }

    .hero-subtitle {
        color: rgba(255, 255, 255, 0.85);
        font-size: 16px;
        margin-bottom: 0;
        line-height: 1.5;
        max-width: 720px;
    }

    .stat-card {
        display: flex;
        align-items: center;
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        gap: 16px;
        height: 100%;
    }

    .stat-card:hover {
        border-color: #3949ab;
        box-shadow: 0 8px 24px rgba(57, 73, 171, 0.12);
        transform: translateY(-4px);
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
    }

    .stat-primary .stat-icon {
        background: linear-gradient(135deg, #3949ab 0%, #5c6bc0 100%);
        color: #fff;
    }

    .stat-success .stat-icon {
        background: linear-gradient(135deg, #2e7d32 0%, #43a047 100%);
        color: #fff;
    }

    .stat-info .stat-icon {
        background: linear-gradient(135deg, #0288d1 0%, #039be5 100%);
        color: #fff;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #1a237e;
        line-height: 1.1;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 14px;
        color: #64748b;
        font-weight: 600;
        line-height: 1.2;
    }

    .toolbar-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
    }

    .search-input,
    .filter-select {
        border-radius: 12px;
        border: 1.5px solid #e9ecef;
        font-size: 14px;
        height: 44px;
    }

    .search-input:focus,
    .filter-select:focus {
        border-color: #3949ab;
        box-shadow: 0 0 0 0.2rem rgba(57, 73, 171, 0.12);
        background-color: #f8f9ff;
    }

    .trainer-table-card {
        border: 0;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .table-header-row th {
        color: #1a237e;
        font-weight: 700;
        font-size: 13px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 16px;
        background: #f8f9ff;
        white-space: nowrap;
    }

    .trainer-table-card tbody td {
        padding: 16px;
        vertical-align: middle;
        border-color: #eef2f7;
        white-space: nowrap;
    }

    .trainer-table-card tbody tr:hover {
        background-color: #f8f9ff;
    }

    .trainer-avatar {
        width: 44px;
        height: 44px;
        object-fit: cover;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .trainer-skill-cell {
        white-space: normal !important;
        min-width: 180px;
        max-width: 260px;
    }

    .status-badge {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .status-badge::before {
        content: '';
        width: 8px;
        height: 8px;
        border-radius: 50%;
    }

    .status-active {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .status-active::before {
        background: #2e7d32;
    }

    .status-inactive {
        background: #ffebee;
        color: #c62828;
    }

    .status-inactive::before {
        background: #c62828;
    }

    .badge-course {
        background: #e3f2fd;
        color: #1a237e;
        border: 1.5px solid #bbdefb;
        font-weight: 700;
        padding: 8px 14px;
        border-radius: 999px;
    }

    .btn-action {
        border-radius: 10px;
        padding: 7px 11px;
        font-size: 14px;
        transition: all 0.2s ease;
        border: 1.5px solid transparent;
    }

    .btn-action-view {
        color: #1976d2;
        background-color: #e3f2fd;
    }

    .btn-action-edit {
        color: #1a237e;
        background-color: #e8eaf6;
    }

    .btn-action-delete {
        color: #c62828;
        background-color: #ffebee;
    }

    .empty-state {
        padding: 60px 20px;
        text-align: center;
    }

    .empty-state i {
        font-size: 64px;
        color: #cbd5e1;
        margin-bottom: 20px;
    }

    @media (max-width: 576px) {
        .trainer-hero {
            padding: 24px;
            border-radius: 18px;
        }

        .hero-title {
            font-size: 1.45rem;
        }

        .toolbar-search-form,
        .toolbar-actions {
            flex-direction: column;
        }

        .toolbar-search-form button,
        .toolbar-actions a {
            width: 100%;
        }
    }
</style>
@endpush

@section('admin-trainer-content')
    <div class="trainer-hero" style="margin-left:0;">
        <h1 class="hero-title">
            <i class="bi bi-person-badge-fill me-3"></i>
            Trainer Management
        </h1>
        <p class="hero-subtitle">
            Kelola akun instruktur, monitor penugasan kelas, dan track performa trainer secara real-time.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="stat-card stat-primary">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $totalTrainers }}</div>
                    <div class="stat-label">Total Trainer</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card stat-success">
                <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $activeTrainers }}</div>
                    <div class="stat-label">Trainer Aktif (30 Hari)</div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="stat-card stat-info">
                <div class="stat-icon"><i class="bi bi-easel-fill"></i></div>
                <div>
                    <div class="stat-value">{{ $teachingTrainers }}</div>
                    <div class="stat-label">Sedang Mengajar</div>
                </div>
            </div>
        </div>
    </div>

    <div class="toolbar-card mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-lg-5">
                <form action="{{ route('admin.trainer.index') }}" method="GET" class="d-flex gap-2 toolbar-search-form">
                    <input type="hidden" name="sort" value="{{ request('sort') }}">

                    <input type="text"
                           name="search"
                           class="form-control search-input"
                           placeholder="Cari nama, email, atau nomor HP..."
                           value="{{ request('search') }}">

                    <button type="submit" class="btn btn-primary rounded-3 px-4"
                            style="background:#3949ab;border:none;font-weight:700;height:44px;">
                        <i class="bi bi-search"></i>
                        <span class="d-sm-none ms-1">Cari</span>
                    </button>
                </form>
            </div>

            <div class="col-12 col-lg-4">
                <form action="{{ route('admin.trainer.index') }}" method="GET">
                    <input type="hidden" name="search" value="{{ request('search') }}">

                    <select name="sort" class="form-select filter-select" onchange="this.form.submit()">
                        <option value="">Urutkan Berdasarkan...</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru Bergabung</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama Bergabung</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                    </select>
                </form>
            </div>

            <div class="col-12 col-lg-3">
                <div class="d-flex justify-content-lg-end gap-2 toolbar-actions">
                    @if(request('search') || request('sort'))
                        <a href="{{ route('admin.trainer.index') }}" class="btn btn-outline-secondary rounded-3" style="height:44px;">
                            <i class="bi bi-x-circle me-1"></i>Reset
                        </a>
                    @endif

                    <a href="{{ route('admin.trainer.create') }}" class="btn btn-primary rounded-3"
                       style="background:#3949ab;border:none;font-weight:700;height:44px;">
                        <i class="bi bi-person-plus me-1"></i>Tambah
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card trainer-table-card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-header-row">
                    <tr>
                        <th class="ps-4" style="min-width:220px;">Trainer</th>
                        <th style="min-width:200px;">Kontak</th>
                        <th style="min-width:180px;">Keahlian</th>
                        <th class="text-center" style="width:110px;">Status</th>
                        <th class="text-center" style="width:100px;">Kelas</th>
                        <th style="width:130px;">Bergabung</th>
                        <th class="text-center pe-4" style="width:160px;">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($trainers as $trainer)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($trainer->name) }}&background=3949ab&color=fff&bold=true"
                                         class="trainer-avatar"
                                         alt="{{ $trainer->name }}">

                                    <div>
                                        <h6 class="mb-0 fw-bold" style="color:#1a237e;">{{ $trainer->name }}</h6>
                                        <small class="text-muted">{{ $trainer->profession ?? 'Instruktur' }}</small>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="small mb-1">
                                    <i class="bi bi-envelope text-muted me-1"></i>
                                    <span style="color:#424242;">{{ $trainer->email }}</span>
                                </div>

                                <div class="small text-muted">
                                    <i class="bi bi-telephone text-muted me-1"></i>
                                    {{ $trainer->phone ?? '—' }}
                                </div>
                            </td>

                            <td class="trainer-skill-cell">
                                <div class="small" style="color:#424242;">
                                    @if($trainer->bio)
                                        {{ \Illuminate\Support\Str::limit($trainer->bio, 50) }}
                                    @else
                                        <span class="text-muted">Belum ada keahlian</span>
                                    @endif
                                </div>
                            </td>

                            <td class="text-center">
                                @php
                                    $isActive = $trainer->created_at >= now()->subDays(30);
                                @endphp

                                <span class="status-badge {{ $isActive ? 'status-active' : 'status-inactive' }}">
                                    {{ $isActive ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>

                            <td class="text-center">
                                <span class="badge-course">
                                    {{ $trainer->courses_as_trainer_count ?? 0 }}
                                </span>
                            </td>

                            <td>
                                <small class="text-muted">
                                    {{ $trainer->created_at?->format('d M Y') }}
                                </small>
                            </td>

                            <td class="text-center pe-4">
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.trainer.show', $trainer) }}"
                                       class="btn btn-action btn-action-view"
                                       title="Lihat Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>

                                    <a href="{{ route('admin.trainer.edit', $trainer) }}"
                                       class="btn btn-action btn-action-edit"
                                       title="Edit Trainer">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <form action="{{ route('admin.trainer.destroy', $trainer) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus trainer {{ $trainer->name }}?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-action btn-action-delete"
                                                title="Hapus Trainer">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-inbox"></i>
                                    <h6 class="fw-bold mt-3" style="color:#1a237e;">Belum Ada Data Trainer</h6>
                                    <p class="text-muted mb-0">
                                        Silakan
                                        <a href="{{ route('admin.trainer.create') }}" style="color:#3949ab;font-weight:700;">
                                            tambah trainer baru
                                        </a>
                                        untuk mulai menugaskan kelas.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($trainers->hasPages())
            <div class="card-footer bg-light border-top p-4">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <small class="text-muted">
                        Menampilkan <strong>{{ $trainers->firstItem() }}</strong> sampai
                        <strong>{{ $trainers->lastItem() }}</strong> dari
                        <strong>{{ $trainers->total() }}</strong> trainer
                    </small>

                    <div>
                        {{ $trainers->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@push('admin-trainer-scripts')
<script>
    setTimeout(function () {
        document.querySelectorAll('.alert').forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
</script>
@endpush