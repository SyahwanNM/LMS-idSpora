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
        text-decoration: none; background: transparent;
    }
    .action-icon:hover { background: var(--crm-border-soft); color: var(--crm-primary); }
</style>
@endsection

@section('content')
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
                            <a href="{{ route('admin.crm.customers.show', $customer) }}" class="action-icon row-action-btn hover-scale" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('admin.crm.customers.edit', $customer) }}" class="action-icon row-action-btn hover-scale" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
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
