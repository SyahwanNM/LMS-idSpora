@extends('layouts.admin')
@section('title','Edit Event')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="bi bi-calendar-check me-2"></i>Edit Event</h4>
        <a href="{{ url('admin/add-event') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
    </div>
    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <div class="card shadow-sm"><div class="card-body">
        <form action="{{ route('admin.events.update',$event) }}" method="POST" enctype="multipart/form-data" id="eventForm">@csrf @method('PUT')
            <div class="row g-3">
                <div class="col-lg-8">
                    <div class="mb-3">
                        <label for="image" class="form-label fw-semibold">Gambar Event</label>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <div class="form-text">Kosongkan jika tidak ingin mengganti gambar. Maks 5MB. <span id="imageSizeInfo" class="fw-semibold"></span></div>
                        @if($event->image)
                        <div class="mt-2 border rounded p-2 bg-light text-center">
                            <img src="{{ Storage::url($event->image) }}" alt="Current Image" class="img-thumbnail rounded" style="max-width:260px;height:160px;object-fit:cover;">
                        </div>
                        @endif
                        <div id="imagePreview" class="mt-2" style="display:none;">
                            <img id="previewImg" src="#" alt="Preview" class="img-fluid rounded shadow-sm" style="max-height:180px;object-fit:cover;width:100%;">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="nama" class="form-label fw-semibold">Nama Event <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="nama" class="form-control" required value="{{ old('title',$event->title) }}" placeholder="Masukkan Nama Event">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Pembicara <span class="text-danger">*</span></label>
                        @php $speakerList = collect(explode(',', old('speaker',$event->speaker)))->map(fn($s)=>trim($s))->filter()->values(); @endphp
                        <div id="speakersContainer" class="d-flex flex-column gap-2">
                            @if($speakerList->count())
                                @foreach($speakerList as $i => $sp)
                                <div class="input-group speaker-row">
                                    <input type="text" name="speakers[]" class="form-control" value="{{ $sp }}" placeholder="Nama pembicara" {{ $i===0 ? 'required' : '' }}>
                                    <button type="button" class="btn btn-outline-danger remove-speaker" {{ $i===0 ? 'disabled' : '' }} title="Hapus">&times;</button>
                                </div>
                                @endforeach
                            @else
                                <div class="input-group speaker-row">
                                    <input type="text" name="speakers[]" class="form-control" placeholder="Nama pembicara" required>
                                    <button type="button" class="btn btn-outline-danger remove-speaker" disabled title="Hapus">&times;</button>
                                </div>
                            @endif
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="addSpeakerRow"><i class="bi bi-plus-circle me-1"></i>Tambah Nama Pembicara</button>
                        <input type="hidden" name="speaker" id="speakerCombined" value="{{ old('speaker',$event->speaker) }}">
                        <div class="form-text">Minimal 1 pembicara (wajib). Tambahan pembicara opsional.</div>
                    </div>
                    <div class="mb-3">
                        <!-- Level field removed per request -->
                    </div>
                    <!-- Penjelasan Singkat (maks 40 kata) -->
                    <div class="mb-3">
                        <label for="short_desc" class="form-label fw-semibold">Penjelasan Singkat <span class="text-danger">*</span></label>
                        <textarea name="short_description" id="short_desc" class="form-control" rows="3" required placeholder="Ringkas tujuan atau inti acara (maks 40 kata)">{{ old('short_description', $event->short_description ?? '') }}</textarea>
                        <small class="d-block mt-1" id="shortDescHint"><span id="shortDescCount">0</span>/40 kata</small>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsi" class="form-label fw-semibold">Deskripsi Event <span class="text-danger">*</span></label>
                        <textarea name="description" id="deskripsi" class="form-control" rows="6" required>{{ old('description',$event->description) }}</textarea>
                    </div>
                    <!-- Kelola Event: Manage / Create -->
                    <div class="mb-3">
                        <label for="manage_action" class="form-label fw-semibold">Kelola Event <span class="text-danger">*</span></label>
                        <select name="manage_action" id="manage_action" class="form-select" required>
                            @php $currentManage = old('manage_action', $event->manage_action ?? null); @endphp
                            <option value="" disabled {{ $currentManage ? '' : 'selected' }}>Pilih aksi</option>
                            <option value="manage" {{ $currentManage === 'manage' ? 'selected' : '' }}>Manage</option>
                            <option value="create" {{ $currentManage === 'create' ? 'selected' : '' }}>Create</option>
                        </select>
                        <small class="text-muted">Pilih apakah event ini dikelola (Manage) atau dibuat baru (Create).</small>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal" class="form-label fw-semibold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" name="event_date" id="tanggal" class="form-control" required value="{{ old('event_date',$event->event_date) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Waktu Mulai & Selesai <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="time" name="event_time" id="masuk1" class="form-control" required value="{{ old('event_time',$event->event_time) }}">
                            <span>s/d</span>
                            <input type="time" name="event_time_end" id="masuk2" class="form-control" value="{{ old('event_time_end',$event->event_time_end) }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="lokasi" class="form-label fw-semibold">Lokasi <span class="text-danger">*</span></label>
                        <input type="text" name="location" id="lokasi" class="form-control" required value="{{ old('location',$event->location) }}" placeholder="Masukkan Lokasi">
                    </div>
                    <div class="mb-3">
                        <label for="hargaDisplay" class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="text" id="hargaDisplay" class="form-control" required placeholder="0" value="{{ number_format(old('price',$event->price),0,',','.') }}">
                            <input type="hidden" name="price" id="harga" value="{{ old('price',$event->price) }}">
                        </div>
                        <small class="form-text">Masukkan angka saja; otomatis diformat dengan titik ribuan. Isi 0 untuk gratis.</small>
                    </div>
                    <div class="mb-3">
                        <label for="diskon" class="form-label fw-semibold">Diskon (%)</label>
                        <input type="number" name="discount_percentage" id="diskon" class="form-control" min="0" max="100" step="1" value="{{ old('discount_percentage',$event->discount_percentage) }}" placeholder="0">
                    </div>
                    <div class="mb-3">
                        <label for="discount_until" class="form-label fw-semibold">Jangka Waktu Diskon</label>
                        <input type="date" name="discount_until" id="discount_until" class="form-control" value="{{ old('discount_until',$event->discount_until) }}" {{ old('discount_percentage',$event->discount_percentage) > 0 ? '' : 'disabled' }}>
                        <small class="form-text">Tanggal terakhir diskon (maksimal sehari sebelum hari acara).</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Benefit <span class="text-muted">(Opsional)</span></label>
                        <div id="benefitsContainer"></div>
                        <button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="addBenefitRow"><i class="bi bi-plus-circle me-1"></i>Tambah Benefit</button>
                        <input type="hidden" name="benefit" id="benefitHidden" value="{{ old('benefit',$event->benefit) }}">
                        <div class="form-text">Tambah satu per satu; akan digabung otomatis saat disimpan.</div>
                    </div>
                    <div class="mb-3">
                        <label for="maps" class="form-label fw-semibold">Maps Lokasi (Jika Offline)</label>
                        <div class="input-group">
                            <input type="text" name="maps_url" id="maps" class="form-control" value="{{ old('maps_url',$event->maps_url) }}" placeholder="Tempel link Google Maps (bisa short link maps.app.goo.gl)">
                            <button class="btn btn-outline-secondary" type="button" id="btnResolveMaps">Deteksi</button>
                        </div>
                        <div id="mapsPreview" class="mt-2 rounded border" style="display:none;height:260px;"></div>
                        <div class="form-text">Klik "Deteksi" untuk membaca koordinat dari short link Google Maps.</div>
                    </div>
                    <div class="mb-3">
                        <label for="zoom" class="form-label fw-semibold">Link Zoom (Jika Online)</label>
                        <input type="text" name="zoom_link" id="zoom" class="form-control" value="{{ old('zoom_link',$event->zoom_link) }}" placeholder="Masukkan Link Zoom">
                    </div>
                    <div class="mb-3">
                        <label for="terms" class="form-label fw-semibold">Terms & Condition</label>
                        <textarea name="terms_and_conditions" id="terms" class="form-control" rows="6">{{ old('terms_and_conditions',$event->terms_and_conditions) }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Schedule <span class="text-muted small">(Opsional)</span></label>
                        <table class="table table-sm align-middle" id="scheduleTable">
                            <thead class="table-light"><tr><th style="width:180px">Waktu Mulai</th><th style="width:180px">Waktu Selesai</th><th>Kegiatan</th><th>Deskripsi</th><th style="width:80px" class="text-center">Aksi</th></tr></thead>
                            <tbody>
                                @php $existingSchedule = old('schedule', $event->schedule_json ?? []); @endphp
                                @if(is_array($existingSchedule) && count($existingSchedule))
                                    @foreach($existingSchedule as $i => $row)
                                    <tr>
                                        <td><input type="time" class="form-control form-control-sm" name="schedule[{{ $i }}][start]" value="{{ $row['start'] ?? '' }}"></td>
                                        <td><input type="time" class="form-control form-control-sm" name="schedule[{{ $i }}][end]" value="{{ $row['end'] ?? '' }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="schedule[{{ $i }}][title]" placeholder="Nama kegiatan" value="{{ $row['title'] ?? '' }}"></td>
                                        <td><input type="text" class="form-control form-control-sm" name="schedule[{{ $i }}][description]" placeholder="Deskripsi singkat" value="{{ $row['description'] ?? '' }}"></td>
                                        <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" data-action="remove" title="Hapus"><i class="bi bi-x"></i></button></td>
                                    </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="addScheduleRow"><i class="bi bi-plus-circle me-1"></i>Tambah Baris</button>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pengeluaran <span class="text-muted small">(Opsional)</span></label>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle" id="expensesTable">
                                <thead class="table-light"><tr><th>Barang</th><th style="width:120px">Kuantitas</th><th style="width:160px">Harga Satuan (Rp)</th><th style="width:180px">Harga Total (Rp)</th><th style="width:80px" class="text-center">Aksi</th></tr></thead>
                                <tbody>
                                    @php $existingExpenses = old('expenses', $event->expenses_json ?? []); @endphp
                                    @if(is_array($existingExpenses) && count($existingExpenses))
                                        @foreach($existingExpenses as $i => $row)
                                        <tr>
                                            <td><input type="text" class="form-control form-control-sm" name="expenses[{{ $i }}][item]" placeholder="Nama barang" value="{{ $row['item'] ?? '' }}"></td>
                                            <td><input type="number" class="form-control form-control-sm" name="expenses[{{ $i }}][quantity]" data-expense-qty min="0" step="1" value="{{ $row['quantity'] ?? 0 }}"></td>
                                            <td><input type="number" class="form-control form-control-sm" name="expenses[{{ $i }}][unit_price]" data-expense-unit min="0" step="1000" value="{{ $row['unit_price'] ?? 0 }}"></td>
                                            <td><input type="number" class="form-control form-control-sm" name="expenses[{{ $i }}][total]" data-expense-total readonly value="{{ $row['total'] ?? 0 }}"></td>
                                            <td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" data-action="remove-expense" title="Hapus"><i class="bi bi-trash3"></i></button></td>
                                        </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="addExpenseRow"><i class="bi bi-plus-circle me-1"></i>Tambah Pengeluaran</button>
                        <div class="d-flex justify-content-end mt-2"><span class="me-2 fw-semibold">Total Pengeluaran:</span><span id="expensesGrandTotal" class="fw-bold">Rp0</span></div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="alert alert-info small"><strong>Tips:</strong> Pastikan data event sudah benar sebelum disimpan.</div>
                    <ul class="list-group mb-3 small">
                        <li class="list-group-item d-flex justify-content-between align-items-center">Status Harga <span class="badge bg-secondary" id="statusHarga">Berbayar</span></li>
                        <li class="list-group-item">Diskon aktif jika persentase > 0.</li>
                        <li class="list-group-item">Gunakan Maps untuk offline dan Zoom untuk online.</li>
                    </ul>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3 gap-2">
                <a href="{{ url('admin/add-event') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle me-1"></i> Batal</a>
                <button type="submit" class="btn btn-primary" id="submitBtn" disabled><i class="bi bi-check-circle me-1"></i> Update Event</button>
            </div>
            <div class="small text-muted mt-2" id="submitHint" style="display:none;">Lengkapi semua field wajib untuk mengaktifkan tombol Update.</div>
        </form>
    </div></div>
</div>
@endsection

@section('styles')
<style>
    .ck-editor__editable{min-height:260px}
    #eventForm label,#eventForm .form-label,#eventForm input,#eventForm textarea,#eventForm select{color:#000}
    #eventForm input::placeholder,#eventForm textarea::placeholder{color:#666}
    .speaker-row .remove-speaker{min-width:52px}
    #mapsPreview{min-height:240px}
</style>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>
<!-- Flatpickr Date Picker -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/l10n/id.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // CKEditor init
    ClassicEditor.create(document.querySelector('#deskripsi'), {toolbar:['heading','|','bold','italic','underline','|','bulletedList','numberedList','|','link','blockQuote','insertTable','|','undo','redo','removeFormat']}).then(e=>{window.editorDeskripsi=e;const ta=document.getElementById('deskripsi');if(ta) ta.value=e.getData();e.model.document.on('change:data',()=>{if(ta) ta.value=e.getData(); if(typeof window.updateSubmitState==='function'){window.updateSubmitState();}});}).catch(console.error);
    ClassicEditor.create(document.querySelector('#terms'), {toolbar:['bold','italic','underline','bulletedList','numberedList','link','undo','redo','removeFormat']}).then(e=>window.editorTerms=e).catch(console.error);

    // Image preview
    const imgInp=document.getElementById('image');
    // Image preview + size (max 5MB)
    imgInp?.addEventListener('change',ev=>{const f=ev.target.files[0];const wrap=document.getElementById('imagePreview');const sizeInfo=document.getElementById('imageSizeInfo');if(!f){wrap.style.display='none'; if(sizeInfo) sizeInfo.textContent=''; return;} const sizeMB=f.size/(1024*1024); if(sizeInfo) sizeInfo.textContent='Ukuran: '+sizeMB.toFixed(2)+'MB'; if(sizeMB>5){ alert('Ukuran gambar melebihi 5MB. Pilih file lain.'); imgInp.value=''; wrap.style.display='none'; if(sizeInfo) sizeInfo.textContent=''; return;} const r=new FileReader(); r.onload=e=>{document.getElementById('previewImg').src=e.target.result; wrap.style.display='block';}; r.readAsDataURL(f);});


    // Maps logic
    let leafletMap=null, leafletMarker=null; const mapsInput=document.getElementById('maps'); const mapsPreview=document.getElementById('mapsPreview'); const btnResolveMaps=document.getElementById('btnResolveMaps'); const csrfToken='{{ csrf_token() }}'; const resolveMapsUrl='{{ route('admin.maps.resolve') }}';
    function parseLatLngFromUrl(url){
        if(!url) return null;
        try {
            const d = decodeURIComponent(url);
            let m = d.match(/@(-?\d+\.\d+),\s*(-?\d+\.\rd+)/); if(m) return {lat:parseFloat(m[1]),lng:parseFloat(m[2])};
            m = d.match(/[?&]q=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/); if(m) return {lat:parseFloat(m[1]),lng:parseFloat(m[2])};
            m = d.match(/[?&]ll=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/); if(m) return {lat:parseFloat(m[1]),lng:parseFloat(m[2])};
            m = d.match(/[?&]center=\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/); if(m) return {lat:parseFloat(m[1]),lng:parseFloat(m[2])};
            const m3d = d.match(/!3d(-?\d+\.\d+)/); const m4d = d.match(/!4d(-?\d+\.\d+)/); if(m3d && m4d) return {lat:parseFloat(m3d[1]),lng:parseFloat(m4d[1])};
            m = d.match(/\/place\/\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)/); if(m) return {lat:parseFloat(m[1]),lng:parseFloat(m[2])};
            m = d.trim().match(/^\s*(-?\d+\.\d+)\s*,\s*(-?\d+\.\d+)\s*$/); if(m) return {lat:parseFloat(m[1]),lng:parseFloat(m[2])};
            const nums = d.match(/-?\d+\.\d+/g) || []; if(nums.length>=2){ const lat=parseFloat(nums[0]); const lng=parseFloat(nums[1]); if(Math.abs(lat)<=90 && Math.abs(lng)<=180) return {lat,lng}; }
        } catch(_) {}
        return null;
    }
    function ensureMap(){ if(!mapsPreview) return; if(!leafletMap){ leafletMap=L.map(mapsPreview).setView([-6.200,106.816],12); L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'&copy; OpenStreetMap contributors'}).addTo(leafletMap);} setTimeout(()=>leafletMap.invalidateSize(),50); }
    function showMap(lat,lng){ if(!mapsPreview) return; mapsPreview.style.display='block'; ensureMap(); const pos=[lat,lng]; leafletMap.setView(pos,14); if(leafletMarker){leafletMarker.setLatLng(pos);} else {leafletMarker=L.marker(pos).addTo(leafletMap);} }
    function tryRenderMap(){ const v=mapsInput?.value||''; const p=parseLatLngFromUrl(v); if(p) showMap(p.lat,p.lng); else if(mapsPreview) mapsPreview.style.display='none'; }
    mapsInput?.addEventListener('change',tryRenderMap); mapsInput?.addEventListener('blur',tryRenderMap); tryRenderMap();
    btnResolveMaps?.addEventListener('click',async()=>{const url=mapsInput?.value||''; if(!url){alert('Masukkan link terlebih dahulu'); return;} try{ btnResolveMaps.disabled=true; const resp=await fetch(resolveMapsUrl,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken},body:JSON.stringify({url})}); const data=await resp.json(); if(resp.ok && data.lat && data.lng){ showMap(data.lat,data.lng);} else alert(data.message||'Koordinat tidak ditemukan.'); }catch(e){ alert('Gagal mendeteksi koordinat.'); } finally{ btnResolveMaps.disabled=false; }});

    // Speakers dynamic
    const speakersContainer=document.getElementById('speakersContainer'); const addSpeakerBtn=document.getElementById('addSpeakerRow');
    function updateSpeakerRowsState(){ speakersContainer?.querySelectorAll('.speaker-row').forEach((row,idx)=>{ const inp=row.querySelector('input[name="speakers[]"]'); const rm=row.querySelector('.remove-speaker'); if(inp) inp.required=(idx===0); if(rm) rm.disabled=(idx===0); }); }
    function addSpeakerRow(prefill=''){ if(!speakersContainer) return; const div=document.createElement('div'); div.className='input-group speaker-row'; const safe=prefill.replace(/"/g,'&quot;'); div.innerHTML=`<input type="text" name="speakers[]" class="form-control" placeholder="Nama pembicara" value="${safe}"><button type="button" class="btn btn-outline-danger remove-speaker" title="Hapus">&times;</button>`; speakersContainer.appendChild(div); updateSpeakerRowsState(); }
    speakersContainer?.addEventListener('click',e=>{ const btn=e.target.closest('.remove-speaker'); if(btn){ const row=btn.closest('.speaker-row'); row.remove(); updateSpeakerRowsState(); } });
    addSpeakerBtn?.addEventListener('click',()=>addSpeakerRow()); updateSpeakerRowsState();

    // Harga dengan format ribuan + status + kontrol diskon
    const hargaHidden = document.getElementById('harga');
    const hargaDisplay = document.getElementById('hargaDisplay');
    const statusHarga = document.getElementById('statusHarga');
    const diskonInput = document.getElementById('diskon');
    const discountUntilInput = document.getElementById('discount_until');
    function unformatNumber(str){ return parseInt(String(str).replace(/\D/g,'')||'0',10); }
    function formatThousands(num){ const n=Math.max(0,parseInt(num||0,10)); return n.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); }
    function updateHargaState(){
        const val = unformatNumber(hargaDisplay.value);
        hargaHidden.value = val;
        hargaDisplay.value = formatThousands(val);
        if(statusHarga){
            statusHarga.textContent = val === 0 ? 'Gratis' : 'Berbayar';
            statusHarga.className = 'badge '+(val===0?'bg-success':'bg-primary');
        }
        // disable diskon when free
        if(diskonInput){
            if(val===0){ diskonInput.value=0; diskonInput.disabled=true; }
            else { diskonInput.disabled=false; }
        }
        if(discountUntilInput){
            if(val===0){ discountUntilInput.value=''; discountUntilInput.disabled=true; if(discountUntilInput._flatpickr){ discountUntilInput._flatpickr.clear(); discountUntilInput._flatpickr.altInput.disabled=true; } }
            else {
                const perc = parseInt(diskonInput?.value||'0',10);
                discountUntilInput.disabled = perc<=0;
                if(discountUntilInput._flatpickr){ discountUntilInput._flatpickr.altInput.disabled = perc<=0; }
            }
        }
    }
    if(hargaDisplay){
        hargaDisplay.addEventListener('keydown',e=>{ const allowed=['Backspace','Tab','ArrowLeft','ArrowRight','Delete','Home','End']; if(allowed.includes(e.key)) return; if(!/\d/.test(e.key)) e.preventDefault(); });
        hargaDisplay.addEventListener('input',()=>{ const raw=unformatNumber(hargaDisplay.value); hargaDisplay.value=formatThousands(raw); updateHargaState(); });
        updateHargaState();
    }
    // Diskon input clamp + toggle discount_until
    function toggleDiscountUntil(){
        if(!discountUntilInput) return;
        const perc = parseInt(diskonInput?.value||'0',10);
        const enable = perc>0 && (unformatNumber(hargaDisplay?.value||'0')>0);
        discountUntilInput.disabled = !enable;
        if(discountUntilInput._flatpickr){ discountUntilInput._flatpickr.altInput.disabled = !enable; }
        if(!enable){ discountUntilInput.value=''; discountUntilInput._flatpickr && discountUntilInput._flatpickr.clear(); }
        else { updateDiscountUntilBounds(); }
    }
    diskonInput?.addEventListener('keydown',e=>{ if(['-','Subtract'].includes(e.key)||e.keyCode===189) e.preventDefault(); });
    diskonInput?.addEventListener('input',()=>{ let p=parseInt(diskonInput.value||'0',10); if(isNaN(p)||p<0) p=0; if(p>100) p=100; diskonInput.value=p; toggleDiscountUntil(); });

    // Flatpickr init for tanggal & discount_until
    let eventDateFp=null, discountUntilFp=null;
    if(window.flatpickr){
        eventDateFp = flatpickr('#tanggal', { locale:'id', dateFormat:'Y-m-d', altInput:true, altFormat:'l, j F Y', minDate:'today', disableMobile:true });
        discountUntilFp = flatpickr('#discount_until', { locale:'id', dateFormat:'Y-m-d', altInput:true, altFormat:'l, j F Y', disableMobile:true, clickOpens:true });
    }
    function updateDiscountUntilBounds(){
        if(!discountUntilFp) return;
        const dateStr = document.getElementById('tanggal')?.value;
        if(!dateStr) return;
        const eventDate = new Date(dateStr+'T00:00:00');
        if(isNaN(eventDate.getTime())) return;
        const maxDate = new Date(eventDate.getTime() - 24*60*60*1000);
        const today = new Date(); today.setHours(0,0,0,0);
        if(maxDate < today){
            discountUntilInput.disabled = true;
            discountUntilFp.altInput.disabled = true;
            discountUntilInput.value='';
            discountUntilFp.clear();
            return;
        }
        discountUntilFp.set('minDate', today);
        discountUntilFp.set('maxDate', maxDate);
        const current = discountUntilInput.value;
        if(current){ const curDate = new Date(current+'T00:00:00'); if(curDate >= eventDate){ discountUntilFp.clear(); discountUntilInput.value=''; } }
    }
    const eventDateEl=document.getElementById('tanggal');
    eventDateEl && ['change','input'].forEach(ev=>eventDateEl.addEventListener(ev,updateDiscountUntilBounds));
    // Initial states
    toggleDiscountUntil();
    updateDiscountUntilBounds();

    // Schedule dynamic
    const scheduleTableBody=document.querySelector('#scheduleTable tbody'); const addScheduleBtn=document.getElementById('addScheduleRow'); let scheduleIndex=0;
    function createScheduleRow(idx){ const tr=document.createElement('tr'); tr.innerHTML=`<td><input type="time" class="form-control form-control-sm" name="schedule[${idx}][start]"></td><td><input type="time" class="form-control form-control-sm" name="schedule[${idx}][end]"></td><td><input type="text" class="form-control form-control-sm" name="schedule[${idx}][title]" placeholder="Nama kegiatan"></td><td><input type="text" class="form-control form-control-sm" name="schedule[${idx}][description]" placeholder="Deskripsi singkat"></td><td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" data-action="remove" title="Hapus"><i class="bi bi-x"></i></button></td>`; return tr; }
    function addScheduleRow(){ scheduleTableBody.appendChild(createScheduleRow(scheduleIndex++)); }
    addScheduleBtn?.addEventListener('click',addScheduleRow); scheduleTableBody?.addEventListener('click',e=>{ const b=e.target.closest('button[data-action="remove"]'); if(b){ b.closest('tr').remove(); }}); addScheduleRow();

    // Expenses dynamic
    const expensesTableBody=document.querySelector('#expensesTable tbody'); const addExpenseBtn=document.getElementById('addExpenseRow'); const expensesGrandTotalEl=document.getElementById('expensesGrandTotal'); let expenseIndex=0;
    function clampNonNeg(input,step=1){ input.addEventListener('keydown',e=>{ if(['-','Subtract'].includes(e.key)||e.keyCode===189) e.preventDefault();}); input.addEventListener('input',()=>{ if(input.value==='') return; let v=parseFloat(input.value); if(isNaN(v)||v<0) v=0; if(step>=1) v=Math.floor(v); input.value=v; }); }
    function formatRupiah(n){ const v=Math.max(0,Math.floor(n||0)); return 'Rp'+v.toString().replace(/\B(?=(\d{3})+(?!\d))/g,'.'); }
    function recalcExpensesGrandTotal(){ let total=0; expensesTableBody.querySelectorAll('input[data-expense-total]').forEach(inp=>{ const val=parseFloat(inp.value||'0'); if(!isNaN(val)) total+=val; }); expensesGrandTotalEl.textContent=formatRupiah(total); }
    function recalcExpenseRow(tr){ const qty=parseFloat(tr.querySelector('input[data-expense-qty]')?.value||'0'); const unit=parseFloat(tr.querySelector('input[data-expense-unit]')?.value||'0'); const tot=tr.querySelector('input[data-expense-total]'); const total=(isNaN(qty)?0:qty)*(isNaN(unit)?0:unit); if(tot) tot.value=Math.max(0,Math.round(total)); recalcExpensesGrandTotal(); }
    function createExpenseRow(idx){ const tr=document.createElement('tr'); tr.innerHTML=`<td><input type="text" class="form-control form-control-sm" name="expenses[${idx}][item]" placeholder="Nama barang"></td><td><input type="number" class="form-control form-control-sm" name="expenses[${idx}][quantity]" data-expense-qty min="0" step="1"></td><td><input type="number" class="form-control form-control-sm" name="expenses[${idx}][unit_price]" data-expense-unit min="0" step="1000"></td><td><input type="number" class="form-control form-control-sm" name="expenses[${idx}][total]" data-expense-total readonly value="0"></td><td class="text-center"><button type="button" class="btn btn-outline-danger btn-sm" data-action="remove-expense" title="Hapus"><i class="bi bi-trash3"></i></button></td>`; const q=tr.querySelector('input[data-expense-qty]'); const u=tr.querySelector('input[data-expense-unit]'); clampNonNeg(q,1); clampNonNeg(u,100); q.addEventListener('input',()=>recalcExpenseRow(tr)); u.addEventListener('input',()=>recalcExpenseRow(tr)); return tr; }
    function addExpenseRow(){ const row=createExpenseRow(expenseIndex++); expensesTableBody.appendChild(row); recalcExpenseRow(row); }
    addExpenseBtn?.addEventListener('click',addExpenseRow); expensesTableBody?.addEventListener('click',e=>{ const b=e.target.closest('button[data-action="remove-expense"]'); if(b){ b.closest('tr').remove(); recalcExpensesGrandTotal(); }}); addExpenseRow();

    // Benefits dynamic (serialize to hidden field)
    const benefitsContainer=document.getElementById('benefitsContainer');
    const addBenefitBtn=document.getElementById('addBenefitRow');
    const benefitHidden=document.getElementById('benefitHidden');
    function addBenefitRow(prefill=''){
        if(!benefitsContainer) return;
        const row=document.createElement('div');
        row.className='input-group mb-2 benefit-row';
        const safe=(prefill||'').replace(/"/g,'&quot;');
        row.innerHTML=`<input type="text" class="form-control" name="benefits[]" placeholder="Tuliskan benefit" value="${safe}"><button type="button" class="btn btn-outline-danger" data-action="remove-benefit" title="Hapus"><i class="bi bi-x"></i></button>`;
        benefitsContainer.appendChild(row);
    }
    benefitsContainer?.addEventListener('click',e=>{
        const btn=e.target.closest('button[data-action="remove-benefit"]');
        if(btn){ btn.closest('.benefit-row')?.remove(); }
    });
    // Prefill from hidden value (pipe or newline separated)
    (function prefillBenefits(){
        if(!benefitsContainer) return;
        const raw=benefitHidden?.value||'';
        const parts=(raw.includes('|')?raw.split('|'):raw.split(/\r?\n/)).map(s=>s.trim()).filter(Boolean);
        if(parts.length){ parts.forEach(p=>addBenefitRow(p)); }
        else { addBenefitRow(''); }
    })();

    // Validation & submit state
    const form=document.getElementById('eventForm'); if(form){
        const submitBtn=document.getElementById('submitBtn'); const submitHint=document.getElementById('submitHint'); const requiredFields=Array.from(form.querySelectorAll('[required]'));
        function fieldFriendlyName(el){ if(!el) return 'Field'; const id=el.id||''; const name=el.name||''; if(id==='image'||name==='image') return 'Gambar Event'; if(id==='nama'||name==='title') return 'Nama Event'; if(name==='speakers[]') return 'Nama Pembicara'; if(id==='short_desc'||name==='short_description') return 'Penjelasan Singkat'; if(id==='deskripsi'||name==='description') return 'Deskripsi'; if(id==='tanggal'||name==='event_date') return 'Tanggal'; if(id==='masuk1'||name==='event_time') return 'Waktu Mulai'; if(id==='lokasi'||name==='location') return 'Lokasi'; if(id==='harga'||name==='price') return 'Harga'; return id||name||'Field'; }
        function missingRequired(){ return requiredFields.filter(f=>!(f.value||'').trim()); }
        window.updateSubmitState=function(){
            const filled=missingRequired().length===0;
            const sdEl=document.getElementById('short_desc');
            const sdWords=sdEl ? (sdEl.value||'').trim().split(/\s+/).filter(Boolean).length : 0;
            const overLimit=sdEl ? sdWords>40 : false;
            if(submitBtn) submitBtn.disabled = (!filled || overLimit);
            if(submitHint){
                if(!filled){
                    submitHint.textContent='Lengkapi: '+missingRequired().map(fieldFriendlyName).join(', ');
                    submitHint.style.display='block';
                } else if(overLimit){
                    submitHint.textContent='Penjelasan singkat maksimal 40 kata (saat ini '+sdWords+').';
                    submitHint.style.display='block';
                } else {
                    submitHint.style.display='none';
                }
            }
        };
        requiredFields.forEach(f=>['input','change','blur'].forEach(ev=>f.addEventListener(ev,window.updateSubmitState)));
        window.updateSubmitState();
        form.addEventListener('submit',ev=>{ if(window.editorDeskripsi) document.getElementById('deskripsi').value=window.editorDeskripsi.getData(); if(window.editorTerms) document.getElementById('terms').value=window.editorTerms.getData(); const speakerCombined=document.getElementById('speakerCombined'); if(speakerCombined && speakersContainer){ const names=Array.from(speakersContainer.querySelectorAll('input[name="speakers[]"]')).map(i=>(i.value||'').trim()).filter(Boolean); speakerCombined.value=names.join(', ');} // sync hidden harga
            if(hargaHidden && hargaDisplay){ hargaHidden.value = unformatNumber(hargaDisplay.value); }
            // serialize benefits to hidden field (pipe-separated)
            if(benefitHidden && benefitsContainer){
                const items=Array.from(benefitsContainer.querySelectorAll('input[name="benefits[]"]')).map(i=>(i.value||'').trim()).filter(Boolean);
                benefitHidden.value = items.join('|');
            }
            // Validate short description <= 40 words
            const shortDescEl = document.getElementById('short_desc');
            if(shortDescEl){
                const words = (shortDescEl.value||'').trim().split(/\s+/).filter(Boolean);
                if(words.length>40){
                    ev.preventDefault();
                    shortDescEl.classList.add('border-danger');
                    alert('Penjelasan singkat maksimal 40 kata. Saat ini: '+words.length+' kata.');
                    return;
                } else {
                    shortDescEl.classList.remove('border-danger');
                }
            }
            let ok=true; requiredFields.forEach(f=>{ if(!f.value.trim()){ f.classList.add('border-danger'); ok=false; } else f.classList.remove('border-danger'); }); if(!ok){ ev.preventDefault(); alert('Lengkapi semua field wajib.\nYang belum: '+missingRequired().map(fieldFriendlyName).join(', ')); return; } expensesTableBody?.querySelectorAll('tr').forEach(tr=>recalcExpenseRow(tr)); });

        // Live word count update for short description
        const shortDescCountEl = document.getElementById('shortDescCount');
        const shortDescEl2 = document.getElementById('short_desc');
        function updateShortDescCount(){
            if(!shortDescEl2 || !shortDescCountEl) return;
            const words=(shortDescEl2.value||'').trim().split(/\s+/).filter(Boolean);
            shortDescCountEl.textContent = words.length;
            if(words.length>40){ shortDescCountEl.classList.add('text-danger'); } else { shortDescCountEl.classList.remove('text-danger'); }
        }
        ['input','change','blur'].forEach(ev=> shortDescEl2?.addEventListener(ev,()=>{ updateShortDescCount(); window.updateSubmitState(); }));
        updateShortDescCount();
    }
});
</script>
@endsection