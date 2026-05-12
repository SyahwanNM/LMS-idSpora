@extends('layouts.crm')

@section('title', 'Buat Broadcast Baru')

@section('styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--crm-primary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before { content: ''; display: inline-block; width: 16px; height: 2px; background: var(--crm-primary); border-radius: 2px; }
    .form-field-label {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: var(--crm-text-subtle); margin-bottom: 6px; display: block;
    }
    .form-field {
        width: 100%; border: 1px solid var(--crm-border); border-radius: 10px;
        padding: 0.65rem 1rem; font-size: 0.875rem; color: var(--crm-navy);
        background: var(--crm-border-soft); outline: none;
        transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        font-family: 'Poppins', sans-serif;
    }
    .form-field:focus {
        border-color: var(--crm-primary); box-shadow: 0 0 0 3px rgba(124,58,237,0.08);
        background: #fff;
    }
    .tip-item { display: flex; gap: 10px; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid var(--crm-border-soft); }
    .tip-item:last-child { border-bottom: none; padding-bottom: 0; }
    .tip-icon { width: 28px; height: 28px; border-radius: 7px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 0.85rem; }
</style>
@endsection

@section('content')
{{-- Header --}}
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <div class="page-eyebrow">Broadcasting Module</div>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--crm-navy);letter-spacing:-0.8px;margin:0;">Buat Broadcast Baru</h1>
        <p style="font-size:0.8rem;color:var(--crm-text-subtle);margin:5px 0 0;">Kirim pesan massal via Email dan/atau WhatsApp ke segmen pengguna tertentu.</p>
    </div>
    <a href="{{ route('admin.crm.broadcast.index') }}" class="btn btn-sm px-3 fw-600 mt-3 mt-md-0 hover-scale"
       style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:8px;font-size:0.8rem;">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row g-4">
    {{-- Form --}}
    <div class="col-lg-8">
        <div class="card-minimal p-4">
            <form action="{{ route('admin.crm.broadcast.send') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-12">
                        <label class="form-field-label">Judul / Subjek Pesan</label>
                        <input type="text" name="title" class="form-field"
                               placeholder="Contoh: Promo Flash Sale Event Maret!" required>
                        <p style="font-size:0.72rem;color:var(--crm-text-subtle);margin:6px 0 0;">Digunakan untuk pencatatan internal dan subjek jika dikirim via Email.</p>
                    </div>

                    <div class="col-md-6">
                        <label class="form-field-label">Target Segmen</label>
                        <select name="segment" class="form-field" style="cursor:pointer;" required>
                            <option value="all">Semua Pengguna</option>
                            <option value="reseller">Hanya Reseller</option>
                            <option value="trainer">Hanya Trainer / Pemateri</option>
                            <option value="no_event">Belum Pernah Ikut Event</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-field-label">Platform Pengiriman</label>
                        <select name="platform" class="form-field" style="cursor:pointer;" required>
                            <option value="email">📧 Hanya Email</option>
                            <option value="whatsapp">💬 Hanya WhatsApp</option>
                            <option value="both">✨ Keduanya (Email & WhatsApp)</option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-field-label">Isi Pesan Broadcast</label>
                        <textarea name="message" class="form-field" rows="9"
                                  placeholder="Tuliskan isi pesan Anda di sini..." required></textarea>
                        <div class="d-flex justify-content-between mt-2">
                            <p style="font-size:0.72rem;color:var(--crm-text-subtle);margin:0;">Gunakan bahasa yang menarik dan informatif.</p>
                            <span id="charCount" style="font-size:0.72rem;font-weight:700;color:var(--crm-primary);">0 karakter</span>
                        </div>
                    </div>

                    <div class="col-12 pt-2">
                        <div style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.15);border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.25rem;">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;font-size:0.9rem;"></i>
                                <span style="font-size:0.78rem;color:#dc2626;font-weight:600;">Pesan broadcast tidak dapat dibatalkan setelah dikirim. Pastikan konten sudah benar.</span>
                            </div>
                        </div>
                        <button type="submit"
                                onclick="return confirm('Apakah Anda yakin ingin mengirim broadcast ini sekarang?')"
                                class="btn fw-700 px-5 hover-scale" style="background:var(--crm-primary);color:#fff;border-radius:10px;padding-top:0.7rem;padding-bottom:0.7rem;font-size:0.875rem;box-shadow:0 4px 12px rgba(124,58,237,0.2);">
                            <i class="bi bi-send-fill me-2"></i> Kirim Broadcast Sekarang
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Sidebar Tips --}}
    <div class="col-lg-4">
        <div class="card-minimal p-4 mb-3" style="background:var(--crm-accent-light);border:1px solid rgba(124,58,237,0.12);">
            <h6 style="font-size:0.85rem;font-weight:800;color:var(--crm-primary);margin-bottom:1rem;">💡 Tips & Panduan</h6>
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(124,58,237,0.1);color:var(--crm-primary);">
                    <i class="bi bi-person-heart"></i>
                </div>
                <div>
                    <div style="font-size:0.78rem;font-weight:700;color:var(--crm-navy);">Personalisasi</div>
                    <div style="font-size:0.72rem;color:var(--crm-text-muted);margin-top:2px;">Gunakan sapaan yang ramah namun tetap profesional.</div>
                </div>
            </div>
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(16,185,129,0.1);color:#059669;">
                    <i class="bi bi-cursor-fill"></i>
                </div>
                <div>
                    <div style="font-size:0.78rem;font-weight:700;color:var(--crm-navy);">Call to Action</div>
                    <div style="font-size:0.72rem;color:var(--crm-text-muted);margin-top:2px;">Sertakan link pendaftaran atau kontak WhatsApp admin.</div>
                </div>
            </div>
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(245,158,11,0.1);color:#d97706;">
                    <i class="bi bi-emoji-smile"></i>
                </div>
                <div>
                    <div style="font-size:0.78rem;font-weight:700;color:var(--crm-navy);">Gunakan Emoji</div>
                    <div style="font-size:0.72rem;color:var(--crm-text-muted);margin-top:2px;">Untuk WhatsApp, emoji meningkatkan engagement secara signifikan.</div>
                </div>
            </div>
            <div class="tip-item">
                <div class="tip-icon" style="background:rgba(6,182,212,0.1);color:#0891b2;">
                    <i class="bi bi-cpu"></i>
                </div>
                <div>
                    <div style="font-size:0.78rem;font-weight:700;color:var(--crm-navy);">Background Queue</div>
                    <div style="font-size:0.72rem;color:var(--crm-text-muted);margin-top:2px;">Pengiriman massal diproses otomatis di background job.</div>
                </div>
            </div>
        </div>

        <div class="card-minimal p-4">
            <h6 style="font-size:0.85rem;font-weight:800;color:var(--crm-navy);margin-bottom:0.75rem;">Estimasi Penerima</h6>
            <div id="estimateBox">
                <p style="font-size:0.78rem;color:var(--crm-text-subtle);">Pilih segmen untuk melihat estimasi jumlah penerima.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.querySelector('textarea[name="message"]').addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length + ' karakter';
    });
</script>
@endsection
