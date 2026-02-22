@extends('layouts.admin')
@section('title','Edit Admin')
@section('content')
<h5 class="mb-3">Edit Akun Admin</h5>
@if($errors->any())<div class="alert alert-danger py-2"><ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('admin.users.update',$user) }}" class="card p-3" enctype="multipart/form-data">
    @csrf @method('PUT')
    <div class="mb-3">
        <label class="form-label text-dark">Foto Profil</label>
        <div class="d-flex align-items-center gap-3">
            <div class="avatar-circle" style="width:56px;height:56px;border-radius:50%;overflow:hidden;border:2px solid #EBBC01;background:#6b7280;display:flex;align-items:center;justify-content:center;">
                <img id="avatarEditPreview" src="{{ $user->avatar_url ?? asset('aset/default-avatar.png') }}" alt="avatar" style="width:100%;height:100%;object-fit:cover;display:block;">
            </div>
            <div class="flex-grow-1">
                <input type="file" name="avatar" accept="image/*" class="form-control" id="avatarEditInput">
                <input type="hidden" name="avatar_base64" id="avatarBase64">
                <input type="hidden" name="remove_avatar" id="removeAvatarFlag" value="0">
                <small class="text-muted d-block">Opsional. Format: JPG/PNG, ukuran maks 2MB.</small>
                <div class="d-flex gap-2 mt-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm" id="toggleAvatarCrop">Atur Foto Profil</button>
                    <button type="button" class="btn btn-outline-danger btn-sm" id="removeAvatarBtn">Hapus Foto</button>
                </div>
            </div>
        </div>
        <div id="avatarCropSection" class="mt-3" style="display:none;">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="avatar-cropper border rounded position-relative" style="width:100%;max-width:320px;height:320px;overflow:hidden;background:#111827;">
                        <div class="avatar-crop-mask" style="position:absolute;inset:0;pointer-events:none;">
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;">
                                <div style="width:240px;height:240px;border-radius:50%;box-shadow:0 0 0 2px #EBBC01, 0 0 0 9999px rgba(0,0,0,0.55);"></div>
                            </div>
                        </div>
                        <img id="avatarCropImage" alt="crop" style="position:absolute;top:0;left:0;transform-origin:top left;display:none;">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-column gap-2">
                        <label class="form-label small mb-1">Zoom</label>
                        <input type="range" min="0.5" max="3" step="0.01" value="1" id="avatarZoomRange" style="max-width:360px;">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="avatarZoomOut"><i class="bi bi-zoom-out"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="avatarZoomIn"><i class="bi bi-zoom-in"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="avatarReset"><i class="bi bi-aspect-ratio"></i> Reset</button>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-2">
                            <label class="form-label small mb-0">Ukuran Output</label>
                            <select id="avatarOutputSize" class="form-select form-select-sm" style="max-width:120px;">
                                <option value="256">256×256</option>
                                <option value="512" selected>512×512</option>
                                <option value="1024">1024×1024</option>
                            </select>
                        </div>
                        <small class="text-muted">Geser gambar untuk mengubah posisi. Preview menampilkan masker lingkaran, gambar disimpan persegi.</small>
                        <div>
                            <button type="button" class="btn btn-success btn-sm" id="applyAvatarCrop"><i class="bi bi-check-circle me-1"></i>Gunakan Hasil Crop</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Nama</label>
        <input type="text" name="name" value="{{ old('name',$user->name) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Email</label>
        <input type="email" name="email" value="{{ old('email',$user->email) }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Password (kosongkan jika tidak diubah)</label>
        <input type="password" name="password" class="form-control">
    </div>
    <div class="mb-3">
        <label class="form-label text-dark">Role</label>
        <select name="role" class="form-select" required disabled>
            <option value="admin" selected>Admin</option>
        </select>
        <input type="hidden" name="role" value="admin">
        <small class="text-muted">Role tidak dapat diubah. Hanya akun admin yang dapat dikelola melalui menu ini.</small>
    </div>
    <div class="d-flex justify-content-between">
        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Kembali</a>
        <button class="btn btn-primary" type="submit">Update</button>
    </div>
</form>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var input = document.getElementById('avatarEditInput');
    var preview = document.getElementById('avatarEditPreview');
    var toggleBtn = document.getElementById('toggleAvatarCrop');
    var section = document.getElementById('avatarCropSection');
    var cropImg = document.getElementById('avatarCropImage');
    var zoomRange = document.getElementById('avatarZoomRange');
    var zoomInBtn = document.getElementById('avatarZoomIn');
    var zoomOutBtn = document.getElementById('avatarZoomOut');
    var resetBtn = document.getElementById('avatarReset');
    var applyBtn = document.getElementById('applyAvatarCrop');
    var removeBtn = document.getElementById('removeAvatarBtn');
    var removeFlag = document.getElementById('removeAvatarFlag');
    var cropper = {
        scale: 1,
        x: 0,
        y: 0,
        imgW: 0,
        imgH: 0,
        boxW: 0,
        boxH: 0
    };

    function updateTransform(){
        cropImg.style.transform = `translate(${cropper.x}px, ${cropper.y}px) scale(${cropper.scale})`;
    }
    function resetCrop(){
        cropper.scale = 1;
        zoomRange.value = 1;
        cropper.x = 0; cropper.y = 0;
        updateTransform();
    }
    function ensureBoxSize(){
        var box = cropImg.parentElement.getBoundingClientRect();
        cropper.boxW = box.width; cropper.boxH = box.height;
    }
    function loadCropImage(file){
        var reader = new FileReader();
        reader.onload = function(e){
            cropImg.src = e.target.result;
            cropImg.style.display = 'block';
            cropImg.onload = function(){
                cropper.imgW = cropImg.naturalWidth; cropper.imgH = cropImg.naturalHeight;
                ensureBoxSize();
                resetCrop();
            };
        };
        reader.readAsDataURL(file);
    }
    // Drag to pan
    var dragging = false, startX = 0, startY = 0, originX = 0, originY = 0;
    cropImg.addEventListener('mousedown', function(e){ dragging = true; startX = e.pageX; startY = e.pageY; originX = cropper.x; originY = cropper.y; e.preventDefault(); });
    document.addEventListener('mouseup', function(){ dragging = false; });
    document.addEventListener('mousemove', function(e){ if(!dragging) return; cropper.x = originX + (e.pageX - startX); cropper.y = originY + (e.pageY - startY); updateTransform(); });

    // Controls
    zoomRange.addEventListener('input', function(){ cropper.scale = parseFloat(this.value); updateTransform(); });
    zoomInBtn.addEventListener('click', function(){ cropper.scale = Math.min(3, cropper.scale + 0.1); zoomRange.value = cropper.scale.toFixed(2); updateTransform(); });
    zoomOutBtn.addEventListener('click', function(){ cropper.scale = Math.max(0.5, cropper.scale - 0.1); zoomRange.value = cropper.scale.toFixed(2); updateTransform(); });
    resetBtn.addEventListener('click', resetCrop);

    // Export cropped square to blob
    function exportCropped(size){
        size = size || 512;
        var canvas = document.createElement('canvas');
        canvas.width = size; canvas.height = size;
        var ctx = canvas.getContext('2d');
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0,0,size,size);
        // compute draw based on current transform
        ensureBoxSize();
        // scale factors from image natural to current displayed size
        var displayW = cropper.imgW * cropper.scale;
        var displayH = cropper.imgH * cropper.scale;
        // The image top-left position within crop box
        var imgX = cropper.x;
        var imgY = cropper.y;
        // Map box coords to canvas coords
        var scaleToCanvasX = size / cropper.boxW;
        var scaleToCanvasY = size / cropper.boxH;
        var dx = imgX * scaleToCanvasX;
        var dy = imgY * scaleToCanvasY;
        var dw = displayW * scaleToCanvasX;
        var dh = displayH * scaleToCanvasY;
        ctx.drawImage(cropImg, dx, dy, dw, dh);
        return new Promise(function(resolve){ canvas.toBlob(function(blob){ resolve(blob); }, 'image/png', 0.92); });
    }

    applyBtn.addEventListener('click', async function(){
        if(!cropImg.src) return;
        var outSel = document.getElementById('avatarOutputSize');
        var targetSize = parseInt(outSel?.value || '512', 10);
        // produce data URL to ensure compatibility across browsers
        var canvas = document.createElement('canvas');
        canvas.width = targetSize; canvas.height = targetSize;
        var ctx = canvas.getContext('2d');
        // redraw using current transform
        ensureBoxSize();
        var displayW = cropper.imgW * cropper.scale;
        var displayH = cropper.imgH * cropper.scale;
        var imgX = cropper.x;
        var imgY = cropper.y;
        var scaleToCanvasX = targetSize / cropper.boxW;
        var scaleToCanvasY = targetSize / cropper.boxH;
        var dx = imgX * scaleToCanvasX;
        var dy = imgY * scaleToCanvasY;
        var dw = displayW * scaleToCanvasX;
        var dh = displayH * scaleToCanvasY;
        ctx.fillStyle = '#ffffff'; ctx.fillRect(0,0,targetSize,targetSize);
        ctx.drawImage(cropImg, dx, dy, dw, dh);
        var dataUrl = canvas.toDataURL('image/png', 0.92);
        var hidden = document.getElementById('avatarBase64');
        if(hidden) hidden.value = dataUrl;
        // clear file input to avoid conflicts
        input.value = '';
        // Update small preview
        preview.src = dataUrl;
        // Collapse section
        section.style.display = 'none';
    });

    if(toggleBtn){ toggleBtn.addEventListener('click', function(){ section.style.display = section.style.display==='none' ? 'block' : 'none'; ensureBoxSize(); }); }
    if(input && preview){
        input.addEventListener('change', function(){
            var file = input.files && input.files[0];
            if(!file) return;
            if(!file.type.startsWith('image/')) return;
            // If user selects a file, ensure remove flag is reset
            if(removeFlag){ removeFlag.value = '0'; }
            // Show preview
            var reader = new FileReader();
            reader.onload = function(e){ preview.src = e.target.result; };
            reader.readAsDataURL(file);
            // Prepare crop UI
            loadCropImage(file);
            section.style.display = 'block';
        });
    }

    // Handle remove avatar action
    if(removeBtn){
        removeBtn.addEventListener('click', function(){
            if(removeFlag){ removeFlag.value = '1'; }
            // Clear inputs and crop state
            if(input){ input.value = ''; }
            var hiddenB64 = document.getElementById('avatarBase64');
            if(hiddenB64){ hiddenB64.value = ''; }
            cropImg.src = '';
            cropImg.style.display = 'none';
            resetCrop();
            // Set preview to default placeholder
            preview.src = '{{ asset('aset/default-avatar.png') }}';
            // Hide crop section if open
            section.style.display = 'none';
        });
    }
});
</script>
@endsection