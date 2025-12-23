@extends('layouts.crm')

@section('title', 'Kelola Customer')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-2 text-dark fw-bold">
            <i class="bi bi-people me-2 text-primary"></i>Kelola Customer
        </h2>
        <p class="text-muted mb-0">Daftar semua customer yang terdaftar di sistem</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Search and Filter -->
<div class="card-3d mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.crm.customers.index') }}" class="row g-3">
            <div class="col-md-5">
                <label class="form-label small text-muted fw-semibold">Cari Customer</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Nama, email, atau telepon..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted fw-semibold">Filter Role</label>
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="reseller" {{ request('role') == 'reseller' ? 'selected' : '' }}>Reseller</option>
                    <option value="trainer" {{ request('role') == 'trainer' ? 'selected' : '' }}>Trainer</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted fw-semibold">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn w-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #dc2626 100%); border: none; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.25), 0 2px 8px rgba(220, 38, 38, 0.2); color: white;">
                        <i class="bi bi-search me-1"></i> Cari
                    </button>
                    <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Customers List -->
<div class="card-3d">
    <div class="card-body p-4">
        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Customer</th>
                            <th>Kontak</th>
                            <th>Role</th>
                            <th>Registrasi</th>
                            <th>Enrollment</th>
                            <th>Bergabung</th>
                            <th class="text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3" style="width:45px;height:45px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                                            <img src="{{ $customer->avatar_url }}" alt="avatar" referrerpolicy="no-referrer">
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $customer->name }}</div>
                                            <small class="text-muted">{{ $customer->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($customer->phone)
                                        <div class="small"><i class="bi bi-telephone me-1"></i>{{ $customer->phone }}</div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($customer->role === 'reseller')
                                        <span class="badge bg-primary">Reseller</span>
                                    @elseif($customer->role === 'trainer')
                                        <span class="badge bg-info">Trainer</span>
                                    @else
                                        <span class="badge bg-secondary">User</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $customer->event_registrations_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">{{ $customer->enrollments_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $customer->created_at->format('d M Y') }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.crm.customers.show', $customer) }}" class="btn btn-outline-primary btn-sm" title="Detail">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.crm.customers.edit', $customer) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $customers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people" style="font-size: 3rem; color: #ccc;"></i>
                <p class="text-muted mt-3">Tidak ada customer ditemukan</p>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-outline-primary mt-2">
                        <i class="bi bi-arrow-left me-1"></i> Reset Filter
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection

