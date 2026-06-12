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
            <form id="broadcastForm" action="{{ route('admin.crm.broadcast.send') }}" method="POST" enctype="multipart/form-data">
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
                            <option value="manual">✍️ Input Manual</option>
                        </select>
                    </div>

                    <div class="col-12" id="manualTargetsField" style="display: none;">
                        <label class="form-field-label">Target Pengiriman Manual (Email / No. HP)</label>
                        <textarea name="manual_targets" class="form-field" rows="4"
                                  placeholder="Masukkan email dan/atau nomor HP. Pisahkan dengan koma atau baris baru.&#10;Contoh:&#10;email1@example.com&#10;+628123456789&#10;email2@example.com"></textarea>
                        <p style="font-size:0.72rem;color:var(--crm-text-subtle);margin:6px 0 0;">
                            Sistem akan secara otomatis menyaring email (berisi '@') untuk pengiriman Email, dan nomor HP (8-20 digit angka) untuk pengiriman WhatsApp.
                        </p>
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
                        <label class="form-field-label">Link / URL Tujuan (Opsional)</label>
                        <input type="text" name="link" class="form-field"
                               placeholder="Contoh: https://idspora.com/courses/detail-course/1">
                        <p style="font-size:0.72rem;color:var(--crm-text-subtle);margin:6px 0 0;">Link halaman terkait broadcast. Jika dikosongkan, tombol di email otomatis mengarah ke Landing Page idSpora.</p>
                    </div>

                    <div class="col-12">
                        <label class="form-field-label">File Lampiran (Opsional, Bisa Lebih dari 1)</label>
                        <input type="file" name="attachments[]" id="attachmentInput" class="form-field" style="padding: 0.5rem 1rem;" multiple>
                        <p style="font-size:0.72rem;color:var(--crm-text-subtle);margin:6px 0 0;">Format file bebas (PDF, Gambar, Doc, dll). Anda dapat memilih beberapa file sekaligus. Maksimal ukuran per file adalah 10MB.</p>
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
                        <button type="button"
                                onclick="confirmBroadcast(event)"
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

@push('modals')
<div class="modal fade" id="confirmSendModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;">
            <div class="modal-body p-4">
                <div class="text-center mb-3">
                    <div class="mb-3" style="width:55px;height:55px;border-radius:50%;background:rgba(124,58,237,0.1);display:inline-flex;align-items:center;justify-content:center;color:var(--crm-primary);font-size:1.6rem;animation: pulse 2s infinite;">
                        <i class="bi bi-send-check-fill"></i>
                    </div>
                    <h5 class="fw-800 text-navy mb-1" style="font-size:1.15rem; color: var(--crm-navy);">Konfirmasi Pengiriman</h5>
                    <p class="text-muted smaller mb-3" style="font-size:0.8rem;">Silakan periksa kembali detail broadcast Anda sebelum mengirim.</p>
                </div>
                
                <div class="card-minimal p-3 mb-4" style="background:var(--crm-border-soft); border-radius: 12px; border: 1px dashed var(--crm-border);">
                    <div class="mb-2 pb-2 border-bottom d-flex justify-content-between align-items-start" style="border-color: rgba(0,0,0,0.05) !important;">
                        <span class="fw-700 text-muted" style="font-size:0.72rem; text-transform: uppercase;">Subjek / Judul:</span>
                        <span id="confirmTitle" class="fw-800 text-navy text-end" style="font-size:0.75rem; max-width: 70%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">-</span>
                    </div>
                    <div class="mb-2 pb-2 border-bottom d-flex justify-content-between align-items-center" style="border-color: rgba(0,0,0,0.05) !important;">
                        <span class="fw-700 text-muted" style="font-size:0.72rem; text-transform: uppercase;">Target Segmen:</span>
                        <span id="confirmSegment" class="badge bg-primary-subtle text-primary fw-700 px-2.5 py-1" style="font-size:0.72rem; border-radius: 6px; background: rgba(124,58,237,0.1) !important; color: var(--crm-primary) !important;">-</span>
                    </div>
                    <div class="mb-2 pb-2 border-bottom d-flex justify-content-between align-items-center" style="border-color: rgba(0,0,0,0.05) !important;">
                        <span class="fw-700 text-muted" style="font-size:0.72rem; text-transform: uppercase;">Platform:</span>
                        <span id="confirmPlatform" class="badge bg-secondary-subtle text-dark fw-700 px-2.5 py-1" style="font-size:0.72rem; border-radius: 6px; background: rgba(108, 117, 125, 0.1) !important; color: var(--crm-navy) !important;">-</span>
                    </div>
                    <div class="mb-2 pb-2 border-bottom d-flex justify-content-between align-items-start" style="border-color: rgba(0,0,0,0.05) !important;">
                        <span class="fw-700 text-muted" style="font-size:0.72rem; text-transform: uppercase;">Link Tujuan:</span>
                        <span id="confirmLink" class="fw-700 text-primary text-end" style="font-size:0.75rem; max-width: 70%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: var(--crm-primary);">-</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-700 text-muted" style="font-size:0.72rem; text-transform: uppercase;">Lampiran File:</span>
                        <span id="confirmAttachment" class="fw-700 text-navy text-end" style="font-size:0.75rem; max-width: 70%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">-</span>
                    </div>
                </div>

                <div style="background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.15);border-radius:10px;padding:0.75rem 1rem;margin-bottom:1.5rem;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;font-size:0.9rem;"></i>
                        <span style="font-size:0.75rem;color:#dc2626;font-weight:600;">Tindakan ini tidak dapat dibatalkan dan broadcast akan langsung diproses.</span>
                    </div>
                </div>
                
                <div class="d-flex gap-3 justify-content-center">
                    <button type="button" class="btn btn-sm fw-700 px-4" style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:10px;font-size:0.85rem;padding: 0.6rem 1.5rem;" data-bs-dismiss="modal">Batal</button>
                    <button type="button" onclick="submitBroadcastForm(this)" class="btn btn-sm fw-700 px-4 text-white hover-scale" style="background:var(--crm-primary);border-radius:10px;font-size:0.85rem;padding: 0.6rem 1.5rem;box-shadow:0 4px 12px rgba(124,58,237,0.2);">
                        <i class="bi bi-send-fill me-1"></i> Ya, Kirim Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(124,58,237,0.4);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(124,58,237,0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(124,58,237,0);
    }
}
</style>
@endpush

@section('scripts')
<script>
    document.querySelector('textarea[name="message"]').addEventListener('input', function() {
        document.getElementById('charCount').textContent = this.value.length + ' karakter';
    });

    const segmentSelect = document.querySelector('select[name="segment"]');
    const manualTargetsField = document.getElementById('manualTargetsField');
    const manualTargetsTextarea = document.querySelector('textarea[name="manual_targets"]');
    const estimateBox = document.getElementById('estimateBox');

    function updateEstimate() {
        const segment = segmentSelect.value;
        if (segment === 'manual') {
            const val = manualTargetsTextarea.value || '';
            const targets = val.split(/[\n,]+/).map(t => t.trim()).filter(t => t.length > 0);
            const emails = targets.filter(t => t.includes('@'));
            // Phone regex: optional +, then 8 to 20 digits, spaces/hyphens allowed
            const phones = targets.filter(t => !t.includes('@') && /^\+?[0-9\-\s]{8,20}$/.test(t));
            
            estimateBox.innerHTML = `
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span style="font-size:0.78rem;color:var(--crm-text-subtle);">Target Email Manual:</span>
                    <strong style="font-size:0.85rem;color:var(--crm-navy);">${emails.length}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span style="font-size:0.78rem;color:var(--crm-text-subtle);">Target WhatsApp Manual:</span>
                    <strong style="font-size:0.85rem;color:var(--crm-navy);">${phones.length}</strong>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                    <span style="font-size:0.78rem;color:var(--crm-navy);font-weight:700;">Total Target:</span>
                    <strong style="font-size:0.95rem;color:var(--crm-primary);">${emails.length + phones.length}</strong>
                </div>
            `;
        } else {
            estimateBox.innerHTML = `<p style="font-size:0.78rem;color:var(--crm-text-subtle);"><span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menghitung estimasi...</p>`;
            
            fetch(`{{ route('admin.crm.broadcast.estimate-count') }}?segment=${segment}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        estimateBox.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:0.78rem;color:var(--crm-text-subtle);">Total Penerima:</span>
                                <strong style="font-size:0.95rem;color:var(--crm-primary);">${data.count} pengguna</strong>
                            </div>
                        `;
                    } else {
                        estimateBox.innerHTML = `<p style="font-size:0.78rem;color:red;">Gagal memuat estimasi.</p>`;
                    }
                })
                .catch(err => {
                    estimateBox.innerHTML = `<p style="font-size:0.78rem;color:red;">Gagal memuat estimasi.</p>`;
                });
        }
    }

    segmentSelect.addEventListener('change', function() {
        if (this.value === 'manual') {
            manualTargetsField.style.display = 'block';
            manualTargetsTextarea.setAttribute('required', 'required');
        } else {
            manualTargetsField.style.display = 'none';
            manualTargetsTextarea.removeAttribute('required');
        }
        updateEstimate();
    });

    manualTargetsTextarea.addEventListener('input', updateEstimate);

    // Initial load
    updateEstimate();

    function confirmBroadcast(event) {
        event.preventDefault();
        const form = document.getElementById('broadcastForm');
        if (form.checkValidity()) {
            // Read form values
            const titleVal = form.querySelector('input[name="title"]').value;
            const segmentSelect = form.querySelector('select[name="segment"]');
            const segmentText = segmentSelect.options[segmentSelect.selectedIndex].text;
            
            const platformSelect = form.querySelector('select[name="platform"]');
            const platformText = platformSelect.options[platformSelect.selectedIndex].text;
            
            const linkVal = form.querySelector('input[name="link"]').value || '-';
            
            let targetCountText = '-';
            if (segmentSelect.value === 'manual') {
                const val = manualTargetsTextarea.value || '';
                const targets = val.split(/[\n,]+/).map(t => t.trim()).filter(t => t.length > 0);
                const emails = targets.filter(t => t.includes('@'));
                const phones = targets.filter(t => !t.includes('@') && /^\+?[0-9\-\s]{8,20}$/.test(t));
                const total = emails.length + phones.length;
                targetCountText = `${total} target manual (${emails.length} Email, ${phones.length} WhatsApp)`;
            } else {
                targetCountText = segmentText;
            }
            
            // Get attachment file names
            const attachmentInput = document.getElementById('attachmentInput');
            let attachmentName = '-';
            if (attachmentInput.files.length > 0) {
                const names = Array.from(attachmentInput.files).map(f => f.name);
                attachmentName = names.join(', ');
            }
            
            // Update confirmation modal details
            document.getElementById('confirmTitle').textContent = titleVal;
            document.getElementById('confirmSegment').textContent = targetCountText;
            document.getElementById('confirmPlatform').textContent = platformText;
            document.getElementById('confirmLink').textContent = linkVal;
            document.getElementById('confirmAttachment').textContent = attachmentName;
            
            // Show modal
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmSendModal'));
            confirmModal.show();
        } else {
            form.reportValidity();
        }
    }

    function submitBroadcastForm(btn) {
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';
            // Disable cancel button
            const cancelBtn = btn.previousElementSibling;
            if (cancelBtn) {
                cancelBtn.setAttribute('disabled', 'disabled');
                cancelBtn.classList.add('disabled');
            }
        }
        document.getElementById('broadcastForm').submit();
    }
</script>
@endsection
