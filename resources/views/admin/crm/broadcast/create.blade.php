@extends('layouts.crm')

@section('title', 'Buat Broadcast Baru')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col">
        <h3 class="fw-bold text-navy mb-1">Buat Broadcast Baru</h3>
        <p class="text-muted small mb-0">Kirim pengumuman atau promo langsung ke WhatsApp/Email pengguna</p>
    </div>
    <div class="col-auto">
        <a href="{{ route('admin.crm.broadcast.index') }}" class="btn btn-outline-secondary btn-sm px-3" style="border-radius: 8px;">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card-minimal p-4 mb-4">
            <form action="{{ route('admin.crm.broadcast.send') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-label fw-bold text-navy">Judul / Subjek Pesan</label>
                        <input type="text" name="title" class="form-control" placeholder="Contoh: Promo Flash Sale Event Maret!" required>
                        <div class="form-text smaller">Hanya untuk pencatatan dan subjek Email.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-navy">Target Segmen</label>
                        <select name="segment" class="form-select" required>
                            <option value="all">Semua Pengguna</option>
                            <option value="reseller">Hanya Reseller</option>
                            <option value="trainer">Hanya Trainer / Pemateri</option>
                            <option value="no_event">Pengguna yang Belum Pernah Ikut Event</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-navy">Platform Pengiriman</label>
                        <select name="platform" class="form-select" required>
                            <option value="email">Hanya Email</option>
                            <option value="whatsapp">Hanya WhatsApp (Blast)</option>
                            <option value="both">Keduanya (Email & WhatsApp)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-bold text-navy">Isi Pesan</label>
                        <textarea name="message" class="form-control" rows="8" placeholder="Tuliskan pesan Anda di sini..." required></textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <div class="form-text smaller">Gunakan bahasa yang menarik dan informatif.</div>
                            <div id="charCount" class="form-text smaller text-muted">0 karakter</div>
                        </div>
                    </div>

                    <div class="col-12 text-end pt-3">
                        <button type="submit" class="btn btn-lg px-5 shadow-sm" style="background: var(--crm-primary); color: white; border-radius: 12px;" onclick="return confirm('Apakah Anda yakin ingin mengirim broadcast ini sekarang?')">
                            <i class="bi bi-send-fill me-2"></i> Kirim Broadcast Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-minimal p-4 mb-4" style="background: var(--crm-accent-light); border: 1px solid rgba(109, 40, 217, 0.1);">
            <h6 class="fw-bold mb-3" style="color: var(--crm-primary);">Tips & Panduan</h6>
            <ul class="list-unstyled smaller text-muted mb-0">
                <li class="mb-3 d-flex gap-2">
                    <i class="bi bi-check2-circle text-primary"></i>
                    <span><strong>Personalisasi:</strong> Gunakan sapaan yang ramah namun profesional.</span>
                </li>
                <li class="mb-3 d-flex gap-2">
                    <i class="bi bi-check2-circle text-primary"></i>
                    <span><strong>Call to Action:</strong> Pastikan menyertakan link pendaftaran atau link WhatsApp admin.</span>
                </li>
                <li class="mb-3 d-flex gap-2">
                    <i class="bi bi-check2-circle text-primary"></i>
                    <span><strong>Gunakan Emojis:</strong> Untuk WhatsApp, penggunaan emoji dapat meningkatkan CTR (Click-Through Rate).</span>
                </li>
                <li class="d-flex gap-2">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span>Sistem akan memproses pengiriman dalam antrian (Background Job) jika jumlah penerima sangat banyak.</span>
                </li>
            </ul>
        </div>

        <div class="card-minimal p-4">
            <h6 class="fw-bold mb-3 text-navy">Preview Estimasi</h6>
            <div id="estimateBox">
                <p class="smaller text-muted">Pilih segmen untuk melihat estimasi jumlah penerima.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .smaller { font-size: 0.85rem; }
    .text-navy { color: var(--crm-navy); }
    .form-control, .form-select {
        border-radius: 10px;
        border: 1px solid var(--crm-border);
        padding: 0.75rem 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--crm-primary);
        box-shadow: 0 0 0 0.25rem rgba(109, 40, 217, 0.1);
    }
</style>
@endsection

@section('scripts')
<script>
    document.querySelector('textarea[name="message"]').addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length + ' karakter';
    });
</script>
@endsection
