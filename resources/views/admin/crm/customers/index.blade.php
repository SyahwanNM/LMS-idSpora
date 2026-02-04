@extends('layouts.crm')

@section('title', 'Kelola Customer')

@section('styles')
<style>
    .search-card {
        background: #ffffff;
        border: 1px solid var(--crm-border);
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .customer-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        object-fit: cover;
    }
    .badge-role {
        padding: 0.35rem 0.65rem;
        font-weight: 600;
        font-size: 0.7rem;
        border-radius: 6px;
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
        <h3 class="fw-bold text-navy mb-1">Database Pelanggan</h3>
        <p class="text-muted small mb-0">Kelola informasi dan peran pengguna platform</p>
    </div>
    <div class="col-auto">
        <button class="btn btn-sm d-flex align-items-center gap-2" style="background: var(--crm-primary); color: white;">
            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            <span>Export CSV</span>
        </button>
    </div>
</div>

<!-- Search and Filter -->
<div class="card-minimal mb-4 overflow-hidden">
    <div class="card-body p-4 bg-light bg-opacity-10">
        <form method="GET" action="{{ route('admin.crm.customers.index') }}" class="row g-3">
            <div class="col-md-5">
                <div class="form-group">
                    <label class="form-label smaller fw-bold text-muted">Cari Pelanggan</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Nama, email, atau nomor telepon..." value="{{ request('search') }}">
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="form-label smaller fw-bold text-muted">Filter Peran</label>
                    <select name="role" class="form-select form-select-sm">
                        <option value="">Semua Peran</option>
                        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User Umum</option>
                        <option value="reseller" {{ request('role') == 'reseller' ? 'selected' : '' }}>Reseller</option>
                        <option value="trainer" {{ request('role') == 'trainer' ? 'selected' : '' }}>Trainer</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button type="submit" class="btn btn-sm flex-grow-1" style="background: var(--crm-navy); color: white;">
                        Terapkan Filter
                    </button>
                    @if(request('search') || request('role'))
                    <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Customers List -->
<div class="card-minimal overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light bg-opacity-50">
                <tr>
                    <th class="ps-4">Profil Pelanggan</th>
                    <th>Detail Kontak</th>
                    <th>Peran</th>
                    <th class="text-center">Aktivitas</th>
                    <th>Bergabung</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center py-1">
                            <img src="{{ $customer->avatar_url }}" class="customer-avatar me-3 border" style="border-color: var(--crm-secondary) !important;" alt="avatar">
                            <div>
                                <div class="fw-bold text-navy small">{{ $customer->name }}</div>
                                <div class="text-muted smaller" style="font-size: 0.7rem;">ID: #{{ str_pad($customer->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-medium">{{ $customer->email }}</div>
                        <div class="text-muted smaller" style="font-size: 0.7rem;">{{ $customer->phone ?? 'Belum ada nomor' }}</div>
                    </td>
                    <td>
                        @if($customer->role === 'reseller')
                            <span class="badge-role" style="background: #fffbeb; color: var(--crm-secondary); border: 1px solid var(--crm-secondary);">RESELLER</span>
                        @elseif($customer->role === 'trainer')
                            <span class="badge-role" style="background: var(--crm-accent-light); color: var(--crm-primary); border: 1px solid var(--crm-primary);">TRAINER</span>
                        @else
                            <span class="badge-role bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10">USER</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="d-flex justify-content-center gap-3">
                            <div class="text-center" title="Registrasi Event">
                                <div class="smaller text-muted" style="font-size: 0.65rem;">Event</div>
                                <div class="small fw-bold">{{ $customer->event_registrations_count ?? 0 }}</div>
                            </div>
                            <div class="text-center" title="Enrollment Course">
                                <div class="smaller text-muted" style="font-size: 0.65rem;">Course</div>
                                <div class="small fw-bold">{{ $customer->enrollments_count ?? 0 }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="small fw-medium">{{ $customer->created_at->format('d M Y') }}</div>
                    </td>
                    <td class="text-end pe-4">
                        <div class="dropdown">
                            <button class="btn btn-link btn-sm text-muted" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius: 10px;">
                                <li><a class="dropdown-item py-2 smaller" href="{{ route('admin.crm.customers.show', $customer) }}"><i class="bi bi-eye me-2"></i> Detail Profil</a></li>
                                <li><a class="dropdown-item py-2 smaller" href="{{ route('admin.crm.customers.edit', $customer) }}"><i class="bi bi-pencil me-2"></i> Edit Data</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2 smaller text-danger" href="#"><i class="bi bi-trash me-2"></i> Hapus Akun</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <div class="py-4">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" style="width: 64px; opacity: 0.2;" class="mb-3">
                            <h6 class="text-muted">Tidak ada pelanggan ditemukan</h6>
                            @if(request('search') || request('role'))
                                <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-link btn-sm text-decoration-none">Bersihkan Filter</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="card-footer bg-white border-0 py-3 px-4">
        {{ $customers->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
