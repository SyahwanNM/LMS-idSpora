@extends('layouts.crm')

@section('title', 'Konfigurasi Sertifikat Kursus')

@section('styles')
<style>
    .page-eyebrow {
        font-size: 0.68rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.2px; color: var(--crm-primary);
        display: inline-flex; align-items: center; gap: 6px; margin-bottom: 6px;
    }
    .page-eyebrow::before { content: ''; display: inline-block; width: 16px; height: 2px; background: var(--crm-primary); border-radius: 2px; }

    .template-card {
        cursor: pointer; transition: all 0.25s ease;
        border: 2.5px solid var(--crm-border-soft); border-radius: 16px;
        overflow: hidden; position: relative; background: #fff;
    }
    .template-card:hover { transform: translateY(-4px); box-shadow: var(--crm-shadow-md); border-color: var(--crm-border); }
    .template-card.active { border-color: var(--crm-primary); box-shadow: 0 0 0 4px rgba(124,58,237,0.1); }
    .template-card .check-icon {
        position: absolute; top: 12px; right: 12px;
        background: var(--crm-primary); color: #fff;
        width: 22px; height: 22px; border-radius: 50%;
        display: none; align-items: center; justify-content: center; z-index: 2; font-size: 0.75rem;
    }
    .template-card.active .check-icon { display: flex; }
    
    .template-preview {
        height: 110px; display: flex; align-items: center; justify-content: center;
        font-size: 2rem; color: rgba(255,255,255,0.4);
    }

    .form-field-label {
        font-size: 0.7rem; font-weight: 700; text-transform: uppercase;
        letter-spacing: 0.6px; color: var(--crm-text-subtle); margin-bottom: 6px; display: block;
    }
    .form-field {
        width: 100%; border: 1px solid var(--crm-border); border-radius: 9px;
        padding: 0.55rem 0.9rem; font-size: 0.85rem; color: var(--crm-navy);
        background: var(--crm-border-soft); outline: none; transition: all 0.2s;
    }
    .form-field:focus { border-color: var(--crm-primary); background: #fff; box-shadow: 0 0 0 3px rgba(124,58,237,0.08); }

    .asset-item {
        position: relative; border-radius: 10px; border: 1px solid var(--crm-border);
        padding: 8px; background: #fff; width: fit-content;
    }
    .asset-delete {
        position: absolute; top: -8px; right: -8px;
        width: 22px; height: 22px; border-radius: 50%;
        background: #ef4444; color: #fff; border: 2px solid #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem; cursor: pointer; transition: transform 0.2s;
    }
    .asset-delete:hover { transform: scale(1.1); }

    .sig-entry {
        background: var(--crm-border-soft); border: 1px solid var(--crm-border);
        border-radius: 12px; padding: 1.25rem; margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="crm-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center">
    <div>
        <div class="page-eyebrow">Course Recognition</div>
        <h1 style="font-size:1.5rem;font-weight:800;color:var(--crm-navy);letter-spacing:-0.8px;margin:0;">Konfigurasi Sertifikat Kursus</h1>
        <p style="font-size:0.8rem;color:var(--crm-text-subtle);margin:5px 0 0;">Kursus: <span class="fw-700 text-primary">{{ $course->name }}</span></p>
    </div>
    <a href="{{ route('admin.crm.certificates.index', ['tab' => 'courses']) }}" class="btn btn-sm px-3 fw-600 mt-3 mt-md-0"
       style="background:var(--crm-border-soft);color:var(--crm-navy);border-radius:8px;font-size:0.8rem;">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

@if(session('success'))
<div class="alert border-0 alert-dismissible fade show mb-4" style="background:rgba(16,185,129,0.1);color:#059669;border-radius:12px;padding:0.85rem 1rem;font-size:0.85rem;" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert border-0 alert-dismissible fade show mb-4" style="background:rgba(239,68,68,0.08);color:#dc2626;border-radius:12px;padding:0.85rem 1rem;font-size:0.82rem;" role="alert">
    <div class="fw-800 mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan:</div>
    <ul class="mb-0 ps-3">
        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
    </ul>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('admin.crm.certificates.update-course', $course) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Step 1 --}}
            <div class="card-minimal p-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">1</div>
                    <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Pilih Template Desain</h6>
                </div>
                
                <div class="row g-3">
                    @php $tpls = [
                        ['id'=>'template_1','name'=>'Classic Royal','desc'=>'Elegan dengan aksen emas dan navy.','bg'=>'linear-gradient(135deg, #1e1b4b 0%, #312e81 100%)','icon'=>'bi-award'],
                        ['id'=>'template_2','name'=>'Modern Minimal','desc'=>'Bersih, fokus pada tipografi modern.','bg'=>'#f1f5f9','icon'=>'bi-file-earmark-text','color'=>'#1e293b'],
                        ['id'=>'template_3','name'=>'Creative Dynamic','desc'=>'Enerjik dengan gradien dan pola.','bg'=>'linear-gradient(135deg, #6d28d9 0%, #db2777 100%)','icon'=>'bi-palette']
                    ]; @endphp
                    @foreach($tpls as $t)
                    <div class="col-md-4">
                        <div class="template-card {{ ($course->certificate_template ?? 'template_1') == $t['id'] ? 'active' : '' }}" onclick="selectTemplate('{{ $t['id'] }}', this)">
                            <div class="check-icon"><i class="bi bi-check"></i></div>
                            <div class="template-preview" style="background:{{ $t['bg'] }}; color:{{ $t['color'] ?? '#fff' }};">
                                <i class="bi {{ $t['icon'] }}"></i>
                            </div>
                            <div class="p-3">
                                <div style="font-weight:700;font-size:0.85rem;color:var(--crm-navy);">{{ $t['name'] }}</div>
                                <div style="font-size:0.7rem;color:var(--crm-text-subtle);line-height:1.4;margin-top:2px;">{{ $t['desc'] }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" name="certificate_template" id="selected_template" value="{{ $course->certificate_template ?? 'template_1' }}">
            </div>

            {{-- Step 2 --}}
            <div class="card-minimal p-4 mb-4">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div style="width:24px;height:24px;border-radius:6px;background:var(--crm-primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;">2</div>
                    <h6 class="fw-800 mb-0" style="font-size:0.9rem;color:var(--crm-navy);">Kelola Aset Visual</h6>
                </div>

                <div class="row g-4">
                    {{-- Logos --}}
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-field-label mb-0">Logo Partner Tambahan</label>
                            <button type="button" id="addLogoBtn" onclick="addLogoField()" class="btn btn-sm fw-700" style="font-size:0.65rem;color:var(--crm-primary);background:rgba(124,58,237,0.08);border-radius:6px;padding:3px 10px;">
                                <i class="bi bi-plus-lg me-1"></i>Tambah Baris
                            </button>
                        </div>
                        <div id="logoUploadContainer" class="mb-3">
                            <input type="file" name="certificate_logo[]" class="form-field mb-2" accept="image/*" onchange="previewNewAsset(this)">
                        </div>
                        
                        <div id="existingLogos" class="d-flex flex-wrap gap-3">
                            @php $logos = is_array($course->certificate_logo) ? $course->certificate_logo : ($course->certificate_logo ? [$course->certificate_logo] : []); @endphp
                            @foreach($logos as $logo)
                                <div class="asset-item">
                                    <img src="{{ asset('uploads/' . $logo) }}" style="height:40px;object-fit:contain;">
                                    <div class="asset-delete" onclick="markDelete('logo', '{{ $logo }}', this, event)"><i class="bi bi-x"></i></div>
                                    <input type="hidden" name="delete_logos[]" value="">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <hr style="border-color:var(--crm-border-soft);margin:0.5rem 0;">

                    {{-- Signatures --}}
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-field-label mb-0">Tanda Tangan Digital</label>
                            <button type="button" onclick="addSignatureField()" class="btn btn-sm fw-700" style="font-size:0.65rem;color:var(--crm-primary);background:rgba(124,58,237,0.08);border-radius:6px;padding:3px 10px;">
                                <i class="bi bi-plus-lg me-1"></i>Tambah TTD
                            </button>
                        </div>

                        <div id="signaturesContainer">
                            @php
                                $sigs = is_array($course->certificate_signature) ? $course->certificate_signature : ($course->certificate_signature ? [$course->certificate_signature] : []);
                            @endphp
                            @foreach($sigs as $i => $sig)
                                @php
                                    $isObj = is_array($sig);
                                    $sigPath = $isObj ? ($sig['image'] ?? '') : $sig;
                                    $sigName = $isObj ? ($sig['name'] ?? '') : '';
                                    $sigPos  = $isObj ? ($sig['position'] ?? '') : '';
                                @endphp
                                <div class="sig-entry">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-field-label">Gambar TTD <span class="text-danger">*</span></label>
                                            @if($sigPath)
                                                <div class="d-flex align-items-center gap-3 mb-2">
                                                    <img src="{{ asset('uploads/' . $sigPath) }}" style="height:45px;background:#fff;padding:4px;border-radius:6px;border:1px solid var(--crm-border);object-fit:contain;">
                                                    <label style="font-size:0.75rem;font-weight:700;color:var(--crm-primary);cursor:pointer;">
                                                        <input type="checkbox" name="replace_sig_{{ $i }}" value="1" style="display:none;" onchange="toggleSigReplace(this, {{ $i }})">
                                                        Ganti Gambar
                                                    </label>
                                                </div>
                                                <input type="hidden" name="existing_signature_image[{{ $i }}]" value="{{ $sigPath }}">
                                                <div id="sig_file_{{ $i }}" style="display:none;">
                                                    <input type="file" name="certificate_signature_file[{{ $i }}]" class="form-field" accept="image/*">
                                                </div>
                                            @else
                                                <input type="file" name="certificate_signature_file[{{ $i }}]" class="form-field" accept="image/*">
                                            @endif
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-field-label">Nama Penandatangan</label>
                                            <input type="text" name="signature_name[{{ $i }}]" value="{{ $sigName }}" class="form-field" placeholder="cth: Dr. Ahmad Fauzi">
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <label class="form-field-label mb-0">Jabatan</label>
                                                <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-700" style="font-size:0.65rem;" onclick="removeSigEntry(this, '{{ $sigPath }}', event)">Hapus</button>
                                            </div>
                                            <input type="text" name="signature_position[{{ $i }}]" value="{{ $sigPos }}" class="form-field" placeholder="cth: Direktur Utama">
                                            <input type="hidden" name="delete_signatures[]" value="">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <button type="submit" class="btn fw-800 px-5" style="background:var(--crm-navy);color:#fff;border-radius:10px;padding-top:0.75rem;padding-bottom:0.75rem;">Simpan Perubahan</button>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card-minimal p-4 mb-3" style="background:var(--crm-accent-light);border:1px solid rgba(124,58,237,0.12);">
                <h6 style="font-size:0.85rem;font-weight:800;color:var(--crm-primary);margin-bottom:1.25rem;">📝 Panduan Aset</h6>
                <div class="d-flex gap-3 mb-3">
                    <div style="width:30px;height:30px;border-radius:8px;background:#fff;color:var(--crm-primary);display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:var(--crm-shadow-sm);"><i class="bi bi-file-image"></i></div>
                    <div style="font-size:0.75rem;color:var(--crm-navy-soft);line-height:1.5;"><b>Format File:</b> JPG, PNG, WEBP, atau SVG diperbolehkan.</div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div style="width:30px;height:30px;border-radius:8px;background:#fff;color:#ef4444;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:var(--crm-shadow-sm);"><i class="bi bi-hdd"></i></div>
                    <div style="font-size:0.75rem;color:var(--crm-navy-soft);line-height:1.5;"><b>Ukuran:</b> Maksimal 2MB per file.</div>
                </div>
                <div class="d-flex gap-3 mb-3">
                    <div style="width:30px;height:30px;border-radius:8px;background:#fff;color:#f59e0b;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:var(--crm-shadow-sm);"><i class="bi bi-layers"></i></div>
                    <div style="font-size:0.75rem;color:var(--crm-navy-soft);line-height:1.5;"><b>Limit:</b> Maksimal 3 Logo Partner dan 3 Tanda Tangan.</div>
                </div>
            </div>
            <div style="background:rgba(6,182,212,0.05);border:1px solid rgba(6,182,212,0.2);border-radius:12px;padding:1rem;font-size:0.75rem;color:#0891b2;line-height:1.5;">
                <i class="bi bi-info-circle-fill me-1"></i> Sertifikat kursus otomatis tersedia bagi siswa yang sudah menyelesaikan (Completed) kursus.
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function selectTemplate(id, element) {
        document.querySelectorAll('.template-card').forEach(el => el.classList.remove('active'));
        element.classList.add('active');
        document.getElementById('selected_template').value = id;
    }

    function markDelete(type, path, element, event) {
        if(event) { event.preventDefault(); event.stopPropagation(); }
        if(confirm('Hapus aset ini?')) {
            const wrapper = element.closest('.asset-item');
            const input = wrapper.querySelector('input[name="delete_logos[]"]');
            if (input) input.value = path;
            wrapper.style.opacity = '0.3';
            wrapper.style.pointerEvents = 'none';
            wrapper.classList.add('marked-deleted');
            checkLogoCount();
        }
    }

    function previewNewAsset(input) {
        const file = input.files[0];
        if (file) {
            let preview = input.parentElement.querySelector('.new-asset-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.className = 'new-asset-preview mt-2';
                preview.style.cssText = 'height:40px; border-radius:6px; border:1px solid var(--crm-border); object-fit:contain; background:#fff; padding:3px;';
                input.parentElement.appendChild(preview);
            }
            const reader = new FileReader();
            reader.onload = function(e) { preview.src = e.target.result; }
            reader.readAsDataURL(file);
        }
    }

    function addLogoField() {
        const container = document.getElementById('logoUploadContainer');
        const currentInputs = container.querySelectorAll('input[type="file"]').length;
        const existingLogos = document.querySelectorAll('.asset-item:not(.marked-deleted)').length;
        if ((currentInputs + existingLogos) < 3) {
            const div = document.createElement('div');
            div.className = 'd-flex gap-2 mb-2';
            div.innerHTML = `
                <div class="w-100"><input type="file" name="certificate_logo[]" class="form-field" accept="image/*" onchange="previewNewAsset(this)"></div>
                <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="this.parentElement.remove(); checkLogoCount();"><i class="bi bi-trash"></i></button>
            `;
            container.appendChild(div);
            checkLogoCount();
        }
    }

    function checkLogoCount() {
        const currentInputs = document.querySelectorAll('#logoUploadContainer input[type="file"]').length;
        const existingLogos = document.querySelectorAll('.asset-item:not(.marked-deleted)').length;
        const btn = document.getElementById('addLogoBtn');
        if ((currentInputs + existingLogos) >= 3) btn.style.display = 'none';
        else btn.style.display = 'inline-flex';
    }

    let sigIndex = {{ count(is_array($course->certificate_signature) ? $course->certificate_signature : ($course->certificate_signature ? [$course->certificate_signature] : [])) }};

    function addSignatureField() {
        const container = document.getElementById('signaturesContainer');
        const existing = container.querySelectorAll('.sig-entry').length;
        if (existing >= 3) { alert('Maksimal 3 tanda tangan.'); return; }
        const idx = sigIndex++;
        const div = document.createElement('div');
        div.className = 'sig-entry';
        div.innerHTML = `
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-field-label">Gambar TTD <span class="text-danger">*</span></label>
                    <input type="file" name="certificate_signature_file[${idx}]" class="form-field" accept="image/*" onchange="previewNewAsset(this)">
                </div>
                <div class="col-md-4">
                    <label class="form-field-label">Nama Penandatangan</label>
                    <input type="text" name="signature_name[${idx}]" class="form-field" placeholder="cth: Dr. Ahmad Fauzi">
                </div>
                <div class="col-md-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-field-label mb-0">Jabatan</label>
                        <button type="button" class="btn btn-link p-0 text-danger text-decoration-none fw-700" style="font-size:0.65rem;" onclick="this.closest('.sig-entry').remove()">Hapus</button>
                    </div>
                    <input type="text" name="signature_position[${idx}]" class="form-field" placeholder="cth: Direktur Utama">
                </div>
            </div>`;
        container.appendChild(div);
    }

    function toggleSigReplace(checkbox, idx) {
        const fileDiv = document.getElementById('sig_file_' + idx);
        if (fileDiv) fileDiv.style.display = checkbox.checked ? 'block' : 'none';
    }

    function removeSigEntry(btn, path, event) {
        if(event) { event.preventDefault(); event.stopPropagation(); }
        if (!confirm('Hapus tanda tangan ini?')) return;
        const entry = btn.closest('.sig-entry');
        if (path) {
            const hidden = entry.querySelector('input[name="delete_signatures[]"]');
            if (hidden) hidden.value = path;
            entry.style.opacity = '0.3';
            entry.style.pointerEvents = 'none';
        } else { entry.remove(); }
    }

    document.addEventListener('DOMContentLoaded', checkLogoCount);
</script>
@endsection