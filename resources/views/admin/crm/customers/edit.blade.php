@extends('layouts.crm')

@section('title', 'Edit Customer')

@section('styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--crm-primary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before { content: ''; display: inline-block; width: 16px; height: 2px; background: var(--crm-primary); border-radius: 2px; }

    .form-field-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: var(--crm-text-subtle); margin-bottom: 6px; display: block;
    }
    .form-field {
        width: 100%; border: 1px solid var(--crm-border); border-radius: 10px;
        padding: 0.65rem 1rem; font-size: 0.88rem; color: var(--crm-navy);
        background: var(--crm-border-soft); outline: none; transition: all 0.2s;
    }
    .form-field:focus { border-color: var(--crm-primary); background: #fff; box-shadow: 0 0 0 3px rgba(124,58,237,0.08); }
    
    .profile-card-mini {
        background: #fff; border-radius: 20px; border: 1px solid var(--crm-border-soft);
        padding: 2rem; text-align: center;
    }
    .avatar-preview {
        width: 100px; height: 100px; border-radius: 24px;
        object-fit: cover; border: 4px solid #fff; box-shadow: var(--crm-shadow-md);
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
{{-- Page Header --}}
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size:0.75rem;margin-bottom:8px;">
                <li class="breadcrumb-item"><a href="{{ route('admin.crm.dashboard') }}" style="color:var(--crm-primary);text-decoration:none;font-weight:600;">CRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.crm.customers.index') }}" style="color:var(--crm-text-subtle);text-decoration:none;">Customers</a></li>
                <li class="breadcrumb-item active" style="color:var(--crm-navy);font-weight:700;">Edit</li>
            </ol>
        </nav>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--crm-navy);letter-spacing:-0.8px;margin:0;">Ubah Data Customer</h1>
    </div>
    <a href="{{ route('admin.crm.customers.show', $customer) }}" class="btn btn-sm px-3 fw-600 mt-3 mt-md-0" style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:8px;">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

@if($errors->any())
<div class="alert border-0 alert-dismissible fade show mb-4" style="background:rgba(239,68,68,0.08);color:#dc2626;border-radius:12px;padding:0.85rem 1rem;font-size:0.82rem;" role="alert">
    <div class="fw-800 mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan:</div>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form method="POST" action="{{ route('admin.crm.customers.update', $customer) }}">
    @csrf @method('PUT')
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-minimal p-4 mb-4">
                <h6 class="fw-800 text-navy mb-4" style="font-size:0.95rem;">Informasi Dasar</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-field-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-field" id="name" name="name" value="{{ old('name', $customer->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-field-label">Alamat Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-field" id="email" name="email" value="{{ old('email', $customer->email) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-field-label">Nomor Telepon</label>
                        <input type="text" class="form-field" id="phone" name="phone" value="{{ old('phone', $customer->phone) }}" placeholder="Contoh: 08123456789">
                    </div>

                    <div class="col-md-6">
                        <label for="role" class="form-field-label">Role / Peran <span class="text-danger">*</span></label>
                        <select class="form-field" style="cursor:pointer;" id="role" name="role" required>
                            <option value="user" {{ old('role', $customer->role) == 'user' ? 'selected' : '' }}>User Umum</option>
                            <option value="reseller" {{ old('role', $customer->role) == 'reseller' ? 'selected' : '' }}>Reseller</option>
                            <option value="trainer" {{ old('role', $customer->role) == 'trainer' ? 'selected' : '' }}>Trainer</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="website" class="form-field-label">Website / Portfolio</label>
                        <input type="url" class="form-field" id="website" name="website" value="{{ old('website', $customer->website) }}" placeholder="https://example.com">
                    </div>

                    <div class="col-12">
                        <label for="bio" class="form-field-label">Biografi Singkat</label>
                        <textarea class="form-field" id="bio" name="bio" rows="4" placeholder="Tentang customer...">{{ old('bio', $customer->bio) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-5">
                    <a href="{{ route('admin.crm.customers.show', $customer) }}" class="btn btn-sm px-4 fw-600" style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:10px;padding-top:0.65rem;padding-bottom:0.65rem;">Batal</a>
                    <button type="submit" class="btn btn-sm px-5 fw-800" style="background:var(--crm-navy);color:#fff;border-radius:10px;padding-top:0.65rem;padding-bottom:0.65rem;">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="profile-card-mini mb-4">
                <img src="{{ $customer->avatar_url }}" class="avatar-preview" alt="avatar" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($customer->name) }}&background=6d28d9&color=fff&size=128'">
                <h5 style="font-size:1.1rem;font-weight:800;color:var(--crm-navy);margin-bottom:0.25rem;">{{ $customer->name }}</h5>
                <p style="font-size:0.75rem;color:var(--crm-text-subtle);margin-bottom:1.5rem;">{{ $customer->email }}</p>
                
                <div style="background:var(--crm-border-soft);border-radius:12px;padding:1rem;text-align:left;">
                    <div style="font-size:0.7rem;font-weight:700;color:var(--crm-text-subtle);text-transform:uppercase;margin-bottom:4px;">Account ID</div>
                    <div style="font-size:0.8rem;font-weight:700;color:var(--crm-navy);font-family:monospace;">#{{ str_pad($customer->id, 6, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>

            <div style="background:rgba(124,58,237,0.04);border:1px dashed var(--crm-primary-light);border-radius:16px;padding:1.25rem;">
                <div class="d-flex gap-3">
                    <div style="color:var(--crm-primary);font-size:1.2rem;"><i class="bi bi-info-circle"></i></div>
                    <div style="font-size:0.75rem;color:var(--crm-text-subtle);line-height:1.5;">
                        <b style="color:var(--crm-navy);">Penting:</b> Pastikan alamat email valid karena digunakan untuk pengiriman notifikasi sertifikat dan update program.
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
