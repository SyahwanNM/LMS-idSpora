@extends('layouts.crm')

@section('title', 'Buat Voucher')

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
</style>
@endsection

@section('content')
{{-- Page Header --}}
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size:0.75rem;margin-bottom:8px;">
                <li class="breadcrumb-item"><a href="{{ route('admin.crm.dashboard') }}" style="color:var(--crm-primary);text-decoration:none;font-weight:600;">CRM</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.crm.vouchers.index') }}" style="color:var(--crm-text-subtle);text-decoration:none;">Voucher</a></li>
                <li class="breadcrumb-item active" style="color:var(--crm-navy);font-weight:700;">Buat Baru</li>
            </ol>
        </nav>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--crm-navy);letter-spacing:-0.8px;margin:0;">Buat Voucher Baru</h1>
    </div>
    <a href="{{ route('admin.crm.vouchers.index') }}" class="btn btn-sm px-3 fw-600 mt-3 mt-md-0" style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:8px;">
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

<form method="POST" action="{{ route('admin.crm.vouchers.store') }}">
    @csrf
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card-minimal p-4 mb-4">
                <h6 class="fw-800 text-navy mb-4" style="font-size:0.95rem;">Parameter Voucher</h6>
                
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="code" class="form-field-label">Kode Voucher <span class="text-danger">*</span></label>
                        <input type="text" class="form-field" id="code" name="code" value="{{ old('code') }}" placeholder="Contoh: CASHBACK10" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Harus unik. Contoh: PROMO50K, DISKON10PCT</small>
                    </div>

                    <div class="col-md-6">
                        <label for="name" class="form-field-label">Nama Voucher <span class="text-danger">*</span></label>
                        <input type="text" class="form-field" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Voucher Diskon 10%" required>
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-field-label">Deskripsi</label>
                        <textarea class="form-field" id="description" name="description" rows="3" placeholder="Jelaskan mengenai kegunaan voucher ini...">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-md-6">
                        <label for="discount_type" class="form-field-label">Tipe Potongan <span class="text-danger">*</span></label>
                        <select class="form-field" style="cursor:pointer;" id="discount_type" name="discount_type" required>
                            <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Nominal Tetap (Rupiah)</option>
                            <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Persentase (%)</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="discount_value" class="form-field-label">Nilai Potongan <span class="text-danger">*</span></label>
                        <input type="number" class="form-field" id="discount_value" name="discount_value" value="{{ old('discount_value') }}" min="1" placeholder="Contoh: 50000 atau 10" required>
                    </div>

                    <div class="col-md-6">
                        <label for="points_required" class="form-field-label">Poin Yang Dibutuhkan <span class="text-danger">*</span></label>
                        <input type="number" class="form-field" id="points_required" name="points_required" value="{{ old('points_required', 0) }}" min="0" required>
                        <small class="text-muted" style="font-size: 0.72rem;">Jumlah poin pelanggan yang ditukar untuk mengklaim voucher.</small>
                    </div>

                    <div class="col-md-6">
                        <label for="min_purchase" class="form-field-label">Minimal Pembelian (Rupiah) <span class="text-danger">*</span></label>
                        <input type="number" class="form-field" id="min_purchase" name="min_purchase" value="{{ old('min_purchase', 0) }}" min="0" required>
                    </div>

                    <div class="col-md-6">
                        <label for="expires_at" class="form-field-label">Tanggal Kedaluwarsa Voucher Master</label>
                        <input type="datetime-local" class="form-field" id="expires_at" name="expires_at" value="{{ old('expires_at') }}">
                        <small class="text-muted" style="font-size: 0.72rem;">Batas waktu voucher master dapat ditukarkan. Kosongkan jika tanpa batas.</small>
                    </div>

                    <div class="col-md-6">
                        <label for="usage_limit" class="form-field-label">Batas Total Klaim (Usage Limit)</label>
                        <input type="number" class="form-field" id="usage_limit" name="usage_limit" value="{{ old('usage_limit') }}" min="1" placeholder="Contoh: 100">
                        <small class="text-muted" style="font-size: 0.72rem;">Maksimal total berapa kali voucher master ini bisa diklaim. Kosongkan jika tanpa batas.</small>
                    </div>

                    <div class="col-md-6">
                        <label for="active" class="form-field-label">Status Voucher <span class="text-danger">*</span></label>
                        <select class="form-field" style="cursor:pointer;" id="active" name="active" required>
                            <option value="1" {{ old('active', '1') == '1' ? 'selected' : '' }}>Aktif (Tampilkan untuk Pelanggan)</option>
                            <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>Nonaktif (Sembunyikan)</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-5">
                    <a href="{{ route('admin.crm.vouchers.index') }}" class="btn btn-sm px-4 fw-600" style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:10px;padding-top:0.65rem;padding-bottom:0.65rem;">Batal</a>
                    <button type="submit" class="btn btn-sm px-5 fw-800 text-white" style="background:var(--crm-navy);border-radius:10px;padding-top:0.65rem;padding-bottom:0.65rem;">
                        <i class="bi bi-check-lg me-1"></i> Buat Voucher
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div style="background:rgba(124,58,237,0.04);border:1px dashed var(--crm-primary-light);border-radius:16px;padding:1.25rem;">
                <div class="d-flex gap-3">
                    <div style="color:var(--crm-primary);font-size:1.2rem;"><i class="bi bi-info-circle"></i></div>
                    <div style="font-size:0.75rem;color:var(--crm-text-subtle);line-height:1.5;">
                        <b style="color:var(--crm-navy);">Panduan Pengisian:</b>
                        <ul class="ps-3 mt-2 mb-0">
                            <li>Kode voucher harus ditulis dengan HURUF KAPITAL tanpa spasi.</li>
                            <li>Tipe potongan "Nominal Tetap" akan mengurangi total tagihan dengan nominal rupiah (misal Rp50.000).</li>
                            <li>Tipe potongan "Persentase" akan memotong sekian persen dari total tagihan (misal 10%).</li>
                            <li>Voucher hasil penukaran oleh pelanggan akan otomatis memiliki masa berlaku 30 hari sejak ditukarkan oleh mereka.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
