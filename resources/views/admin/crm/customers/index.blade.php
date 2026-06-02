@extends('layouts.crm')

@section('title', 'Direktori Pelanggan')

@section('styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--crm-primary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before {
        content: ''; display: inline-block; width: 16px; height: 2px;
        background: var(--crm-primary); border-radius: 2px;
    }
    .page-title { font-size: 1.5rem; font-weight: 800; color: var(--crm-navy); letter-spacing: -0.8px; margin: 0; }
    .page-subtitle { font-size: 0.8rem; color: var(--crm-text-subtle); margin: 5px 0 0; }

    .filter-bar {
        background: #fff; border: 1px solid var(--crm-border);
        border-radius: var(--crm-radius-lg); padding: 1rem 1.25rem;
        box-shadow: var(--crm-shadow-sm); margin-bottom: 1.25rem;
    }
    .filter-input {
        border: 1px solid var(--crm-border); border-radius: 8px;
        padding: 0.5rem 0.85rem; font-size: 0.82rem; color: var(--crm-navy);
        background: var(--crm-border-soft); outline: none; width: 100%;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .filter-input:focus {
        border-color: var(--crm-primary-light);
        box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
        background: #fff;
    }
    .avatar-sq {
        width: 38px; height: 38px; border-radius: 10px;
        object-fit: cover; border: 1.5px solid var(--crm-border);
    }
    .role-pill {
        font-size: 0.65rem; font-weight: 700; letter-spacing: 0.4px;
        padding: 3px 9px; border-radius: 100px; display: inline-block;
    }
    .role-pill.user     { background: var(--crm-border-soft); color: var(--crm-text-muted); }
    .role-pill.reseller { background: rgba(245,158,11,0.1); color: #d97706; }
    .role-pill.trainer  { background: rgba(6,182,212,0.1); color: #0891b2; }

    .stat-chip {
        display: inline-flex; flex-direction: column; align-items: center;
        min-width: 42px;
    }
    .stat-chip .val { font-size: 0.9rem; font-weight: 800; color: var(--crm-navy); line-height: 1; }
    .stat-chip .lbl { font-size: 0.62rem; color: var(--crm-text-subtle); font-weight: 600; margin-top: 2px; }

    .action-icon {
        width: 28px; height: 28px; border-radius: 7px; display: inline-flex;
        align-items: center; justify-content: center; font-size: 0.8rem;
        color: var(--crm-text-muted); transition: all 0.2s;
        text-decoration: none; background: transparent; border: none; cursor: pointer;
    }
    .action-icon:hover { background: var(--crm-border-soft); color: var(--crm-primary); }
    .action-icon.danger:hover { background: rgba(239,68,68,0.1); color: #ef4444; }
</style>
@endsection

@section('content')

{{-- Session Alerts --}}
@if(session('success'))
<div class="alert d-flex align-items-center gap-2 mb-4" style="background:rgba(16,185,129,0.08);border:1px solid rgba(16,185,129,0.2);border-radius:12px;padding:0.85rem 1.25rem;color:#065f46;font-size:0.85rem;font-weight:600;">
    <i class="bi bi-check-circle-fill" style="color:#10b981;"></i> {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert d-flex align-items-center gap-2 mb-4" style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:12px;padding:0.85rem 1.25rem;color:#991b1b;font-size:0.85rem;font-weight:600;">
    <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;"></i> {{ session('error') }}
</div>
@endif

{{-- Page Header --}}
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <div class="page-eyebrow">Database Master</div>
        <h1 class="page-title">Direktori Pelanggan</h1>
        <p class="page-subtitle">Kelola profil, peran, dan pantau keterlibatan pengguna IDSPora.</p>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <button class="btn btn-sm px-3 fw-600 hover-scale" style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:8px;font-size:0.8rem;">
            <i class="bi bi-download me-1"></i> Ekspor CSV
        </button>
    </div>
</div>

{{-- Search & Filter --}}
<div class="card-minimal p-4 mb-4">
    <form method="GET" action="{{ route('admin.crm.customers.index') }}" class="row g-3 align-items-center">
        <div class="col-lg-5">
            <div class="position-relative">
                <i class="bi bi-search position-absolute" style="left:14px;top:50%;transform:translateY(-50%);color:var(--crm-text-subtle);font-size:0.85rem;"></i>
                <input type="text" name="search" class="filter-input ps-5" 
                       placeholder="Cari nama, email, atau ID pelanggan..." value="{{ request('search') }}"
                       style="background: #fff; border: 1px solid var(--crm-border); height: 42px;">
            </div>
        </div>
        <div class="col-lg-3">
            <select name="role" class="filter-input" style="cursor:pointer; background: #fff; border: 1px solid var(--crm-border); height: 42px;">
                <option value="">Semua Peran</option>
                <option value="user"     {{ request('role')=='user'     ? 'selected':'' }}>Customer Umum</option>
                <option value="reseller" {{ request('role')=='reseller' ? 'selected':'' }}>Reseller</option>
                <option value="trainer"  {{ request('role')=='trainer'  ? 'selected':'' }}>Trainer</option>
            </select>
        </div>
        <div class="col-lg-4">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-sm fw-700 px-4 hover-scale" style="background:var(--crm-primary);color:#fff;border-radius:10px;height:42px;flex:1;">
                    <i class="bi bi-funnel-fill me-1"></i> Filter Data
                </button>
                @if(request('search') || request('role'))
                <a href="{{ route('admin.crm.customers.index') }}" class="btn btn-sm px-3 hover-scale d-flex align-items-center justify-content-center" style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:10px;height:42px;">
                    <i class="bi bi-x-lg me-1"></i> Reset
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Customers Table --}}
<div class="card-minimal">
    <div class="table-responsive">
        <table class="crm-table">
            <thead>
                <tr>
                    <th style="padding-left:1.25rem;">Pelanggan</th>
                    <th>Kontak</th>
                    <th>Peran</th>
                    <th style="text-align:center;">Keterlibatan</th>
                    <th>Bergabung</th>
                    <th style="padding-right:1.25rem;text-align:right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr onclick="window.location='{{ route('admin.crm.customers.show', $customer) }}'">
                    <td style="padding-left:1.25rem;">
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $customer->avatar_url }}" class="avatar-sq"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=7c3aed&color=fff&bold=true'">
                            <div>
                                <div style="font-weight:700;font-size:0.85rem;color:var(--crm-navy);">{{ $customer->name }}</div>
                                <div style="font-size:0.72rem;color:var(--crm-text-subtle);">#{{ str_pad($customer->id,5,'0',STR_PAD_LEFT) }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:0.82rem;font-weight:600;color:var(--crm-navy-soft);">{{ $customer->email }}</div>
                        <div style="font-size:0.72rem;color:var(--crm-text-subtle);"><i class="bi bi-phone me-1"></i>{{ $customer->phone ?? '-' }}</div>
                    </td>
                    <td>
                        @if($customer->role === 'reseller')
                            <span class="role-pill reseller">Reseller</span>
                        @elseif($customer->role === 'trainer')
                            <span class="role-pill trainer">Trainer</span>
                        @else
                            <span class="role-pill user">Customer</span>
                        @endif
                    </td>
                    <td style="text-align:center;">
                        <div class="d-flex justify-content-center gap-4">
                            <div class="stat-chip">
                                <span class="val">{{ $customer->event_registrations_count ?? 0 }}</span>
                                <span class="lbl">Events</span>
                            </div>
                            <div class="stat-chip">
                                <span class="val">{{ $customer->enrollments_count ?? 0 }}</span>
                                <span class="lbl">Courses</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div style="font-size:0.82rem;font-weight:600;color:var(--crm-navy);">{{ $customer->created_at->translatedFormat('d M Y') }}</div>
                        <div style="font-size:0.72rem;color:var(--crm-text-subtle);">{{ $customer->created_at->diffForHumans() }}</div>
                    </td>
                    <td style="padding-right:1.25rem;text-align:right;" onclick="event.stopPropagation()">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('admin.crm.customers.show', $customer) }}" class="action-icon hover-scale" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.crm.customers.edit', $customer) }}" class="action-icon hover-scale" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button"
                                    class="action-icon danger hover-scale btn-trigger-delete-customer"
                                    title="Hapus Akun"
                                    data-customer-id="{{ $customer->id }}"
                                    data-customer-name="{{ $customer->name }}"
                                    data-customer-email="{{ $customer->email }}"
                                    data-customer-role="{{ ucfirst($customer->role) }}"
                                    data-delete-url="{{ route('admin.crm.customers.destroy', $customer) }}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state-wrapper">
                            <div class="empty-state-icon hover-scale">
                                <i class="bi bi-people"></i>
                            </div>
                            <h6 class="fw-800 text-navy mb-1">Pelanggan Tidak Ditemukan</h6>
                            <p class="text-muted smaller mb-0">Coba gunakan kata kunci lain atau reset filter pencarian Anda.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div style="padding:1rem 1.25rem;border-top:1px solid var(--crm-border-soft);">
        {{ $customers->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection

@push('modals')
{{-- Delete Customer Confirmation Modal --}}
<div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 460px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <div class="modal-body p-4">

                {{-- Icon --}}
                <div class="text-center mb-4">
                    <div style="width:60px;height:60px;border-radius:50%;background:rgba(239,68,68,0.1);display:inline-flex;align-items:center;justify-content:center;color:#ef4444;font-size:1.75rem;">
                        <i class="bi bi-person-x-fill"></i>
                    </div>
                    <h5 class="fw-800 mt-3 mb-1" style="font-size:1.1rem;color:var(--crm-navy);">Hapus Akun User?</h5>
                    <p style="font-size:0.8rem;color:var(--crm-text-subtle);margin:0;">Tindakan ini bersifat permanen dan tidak dapat dibatalkan.</p>
                </div>

                {{-- Customer Info Card --}}
                <div class="mb-4 p-3" style="background:var(--crm-border-soft);border-radius:12px;border:1px dashed var(--crm-border);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-700 text-muted" style="font-size:0.7rem;text-transform:uppercase;">Nama:</span>
                        <span id="del-customer-name" class="fw-800" style="font-size:0.82rem;color:var(--crm-navy);">-</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-700 text-muted" style="font-size:0.7rem;text-transform:uppercase;">Email:</span>
                        <span id="del-customer-email" class="fw-700" style="font-size:0.78rem;color:var(--crm-primary);">-</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-700 text-muted" style="font-size:0.7rem;text-transform:uppercase;">Peran:</span>
                        <span id="del-customer-role" class="fw-700" style="font-size:0.78rem;color:var(--crm-navy);">-</span>
                    </div>
                </div>

                {{-- Warning --}}
                <div style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.15);border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.5rem;">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-exclamation-triangle-fill mt-1" style="color:#ef4444;font-size:0.85rem;flex-shrink:0;"></i>
                        <span style="font-size:0.75rem;color:#dc2626;font-weight:600;line-height:1.5;">Menghapus akun ini akan menghapus semua data terkait user tersebut dari sistem secara permanen.</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-sm fw-700 px-4"
                            style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:10px;font-size:0.85rem;padding:0.6rem 1.5rem;"
                            data-bs-dismiss="modal">Batal</button>
                    <form id="deleteCustomerForm" action="#" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="button" onclick="submitDeleteCustomer(this)" class="btn btn-sm fw-700 px-4"
                                style="background:#ef4444;color:#fff;border-radius:10px;font-size:0.85rem;padding:0.6rem 1.5rem;">
                            <i class="bi bi-trash3-fill me-1"></i> Ya, Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-trigger-delete-customer').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('del-customer-name').textContent  = btn.dataset.customerName  || '-';
            document.getElementById('del-customer-email').textContent = btn.dataset.customerEmail || '-';
            document.getElementById('del-customer-role').textContent  = btn.dataset.customerRole  || '-';
            document.getElementById('deleteCustomerForm').action       = btn.dataset.deleteUrl;

            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('deleteCustomerModal'));
            modal.show();
        });
    });
});

function submitDeleteCustomer(btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status"></span> Menghapus...';
    const cancelBtn = btn.closest('.d-flex').querySelector('[data-bs-dismiss="modal"]');
    if (cancelBtn) { cancelBtn.disabled = true; cancelBtn.classList.add('disabled'); }
    document.getElementById('deleteCustomerForm').submit();
}
</script>
@endpush
