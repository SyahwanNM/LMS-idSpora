@extends('layouts.crm')

@section('title', 'Template Builder Sertifikat')

@section('styles')
<style>
    /* Override CRM layout parent wrappers to prevent screen overflow */
    .crm-main-content {
        min-height: 0 !important;
        height: calc(100vh - 64px) !important;
        overflow: hidden !important;
    }
    
    .crm-page-container {
        padding: 0 !important;
        margin: 0 !important;
        max-width: 100% !important;
        height: 100% !important;
    }
    
    body {
        background-color: #f1f5f9;
        overflow: hidden !important;
        height: 100vh !important;
    }
    
    .builder-container {
        display: flex;
        height: calc(100% - 64px); /* 100% parent height minus Builder Nav (64px) */
        background-color: #f8fafc;
        overflow: hidden;
    }
    
    /* Left sidebar: Elements & tools */
    .sidebar-left {
        width: 300px;
        background: #ffffff;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        z-index: 10;
        height: 100%;
    }
    

    
    /* Canvas workspace */
    .workspace {
        flex-grow: 1;
        padding: 1.5rem;
        overflow: hidden !important;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e2e8f0;
        height: 100%;
    }
    
    /* Right sidebar: Properties */
    .sidebar-right {
        width: 320px;
        background: #ffffff;
        border-left: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
        z-index: 10;
        height: 100%;
    }
    
    .sidebar-title {
        font-size: 0.8rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #475569;
        padding: 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        margin: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
    }
    
    .sidebar-content {
        padding: 1.25rem;
        overflow-y: auto;
        flex-grow: 1;
    }
    
    /* Canvas Container */
    .cert-canvas-container {
        position: relative;
        width: 1000px;
        height: 706px;
        background-color: #ffffff;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        border: 2px solid #cbd5e1;
        overflow: hidden;
        user-select: none;
        flex-shrink: 0;
        transform-origin: center center;
    }
    
    /* Elements on canvas */
    .canvas-element {
        position: absolute;
        cursor: move;
        border: 1px dashed transparent;
        box-sizing: border-box;
    }
    .canvas-element:hover {
        border-color: #94a3b8;
    }
    .canvas-element.selected {
        border: 1.5px solid #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.15);
    }
    
    .canvas-element .resize-handle {
        position: absolute;
        width: 8px;
        height: 8px;
        background-color: #6366f1;
        border: 1px solid #ffffff;
        border-radius: 50%;
        display: none;
    }
    .canvas-element.selected .resize-handle {
        display: block;
    }
    .canvas-element .handle-se { right: -4px; bottom: -4px; cursor: se-resize; }
    
    /* Tools lists */
    .tool-btn {
        width: 100%;
        padding: 0.75rem 1rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        color: #334155;
        font-weight: 600;
        font-size: 0.78rem !important;
        text-align: left;
        margin-bottom: 0.75rem;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-start !important;
        gap: 10px !important;
        transition: all 0.2s;
    }
    .tool-btn:hover {
        background: #eff6ff;
        border-color: #bfdbfe;
        color: #1e3a8a;
    }
    .tool-btn.btn-red-outline {
        border-color: #f87171 !important;
        color: #ef4444 !important;
        background: #fef2f2 !important;
    }
    .tool-btn.btn-red-outline:hover {
        background: #fee2e2 !important;
        border-color: #ef4444 !important;
        color: #b91c1c !important;
    }
    
    .placeholder-pill {
        display: inline-block;
        padding: 4px 10px;
        background-color: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1e40af;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-right: 5px;
        margin-bottom: 5px;
        cursor: pointer;
    }
    .placeholder-pill:hover {
        background-color: #dbeafe;
    }
    
    .bg-color-option {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        cursor: pointer;
        display: inline-block;
        margin-right: 5px;
    }
    .bg-color-option.active {
        box-shadow: 0 0 0 3px rgba(99,102,241,0.4);
        border-color: #6366f1;
    }
    
    /* Top Navbar builder */
    .builder-nav {
        height: 64px;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 0.75rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        z-index: 100;
        box-sizing: border-box;
    }
</style>
@endsection

@section('content')
<div class="builder-nav shadow-sm mb-0">
    <div class="d-flex align-items-center gap-3">
        <a href="{{ $event ? route('admin.crm.certificates.edit', $event) : route('admin.crm.certificates.edit-course', $course) }}" 
           class="btn btn-sm btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
        <div>
            <h5 class="fw-800 text-dark mb-0">Visual Certificate Builder</h5>
            <small class="text-muted">{{ $event ? 'Event: ' . $event->title : 'Course: ' . $course->name }}</small>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="resetTemplate()">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset
        </button>
        <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold" onclick="saveTemplate()">
            <i class="bi bi-cloud-check-fill me-1"></i> Simpan Template
        </button>
    </div>
</div>

<div class="builder-container">
    <!-- Sidebar Left: Elements Panel -->
    <aside class="sidebar-left">
        <h6 class="sidebar-title">
            <span><i class="bi bi-plus-circle me-1 text-primary"></i> Tambah Elemen</span>
        </h6>
        <div class="sidebar-content">
            <button class="tool-btn" onclick="addTextElement('Judul Sertifikat', 'Georgia', 36, '#1e1b4b', true)">
                <i class="bi bi-type-h1 text-primary"></i> Tambah Teks Besar
            </button>
            <button class="tool-btn" onclick="addTextElement('Diberikan kepada peserta atas partisipasinya.', 'Helvetica', 14, '#475569')">
                <i class="bi bi-text-left text-success"></i> Tambah Deskripsi Teks
            </button>
            
            <div class="mt-4 mb-3">
                <div class="small fw-800 text-muted uppercase mb-2">Variabel Dinamis (Placeholders)</div>
                <div class="placeholder-pill" onclick="addVariableElement('@{{nama}}', 'Great Vibes', 32, '#7f1d1d')">Nama Peserta</div>
                <div class="placeholder-pill" onclick="addVariableElement('@{{event}}', 'Georgia', 18, '#1e1b4b', true)">Judul Acara</div>
                <div class="placeholder-pill" onclick="addVariableElement('@{{tanggal}}', 'Helvetica', 12, '#475569')">Tanggal Terbit</div>
                <div class="placeholder-pill" onclick="addVariableElement('@{{nomor_sertifikat}}', 'Courier New', 11, '#94a3b8')">No. Sertifikat</div>
            </div>
            
            <div class="mt-4 mb-3">
                <div class="small fw-800 text-muted uppercase mb-2">Aset Gambar (Logo / TTD)</div>
                <!-- Logos list -->
                @if(!empty($existingLogos))
                    <div class="small text-muted mb-1 fw-bold">Logo Partner Event:</div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach($existingLogos as $l)
                            <img src="{{ $l['base64'] }}" style="height:32px; width:auto; border:1px solid #e2e8f0; border-radius:4px; cursor:pointer;" 
                                 onclick="addLogoElement('{{ $l['path'] }}', '{{ $l['base64'] }}')" title="Klik untuk tambah ke canvas">
                        @endforeach
                    </div>
                @endif
                
                <!-- Signatures list -->
                @if(!empty($existingSigs))
                    <div class="small text-muted mb-1 fw-bold">Tanda Tangan Event:</div>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach($existingSigs as $s)
                            <img src="{{ $s['base64'] }}" style="height:32px; width:auto; border:1px solid #e2e8f0; border-radius:4px; cursor:pointer;" 
                                 onclick="addSignatureElement('{{ $s['path'] }}', '{{ $s['base64'] }}', '{{ $s['name'] }}', '{{ $s['position'] }}')" 
                                 title="Klik untuk tambah ke canvas">
                        @endforeach
                    </div>
                @endif

                <div class="mt-2">
                    <label class="form-label small text-muted fw-bold mb-1">Upload File Baru</label>
                    <input type="file" id="builder-file-upload" class="form-control form-control-sm" accept="image/*" onchange="uploadNewAsset(this)">
                </div>
            </div>

            <div class="mt-4 mb-3">
                <div class="small fw-800 text-muted uppercase mb-2">Bentuk & Shape (Editable)</div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-2 px-2" onclick="addShapePreset('rect')">
                        ⬜ Persegi
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-2 px-2" onclick="addShapePreset('circle')">
                        🔴 Lingkaran
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-2 px-2" onclick="addShapePreset('triangle')">
                        🔺 Segitiga
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-2 px-2" onclick="addShapePreset('diamond')">
                        🔷 Belah Ketupat
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-2 px-2" onclick="addShapePreset('star')">
                        ⭐ Bintang
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-2 px-2" onclick="addShapePreset('medal_seal')">
                        🏅 Lencana Medali
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-2 px-2" onclick="addShapePreset('ribbon_tail')">
                        🎗️ Pita Medal
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-2 px-2" onclick="addShapePreset('corner_poly')">
                        📐 Ornamen Sudut
                    </button>
                    <button class="btn btn-sm btn-outline-secondary d-flex align-items-center gap-1 py-2 px-2" onclick="addShapePreset('ornate_line')">
                        ➖ Garis Bermotif
                    </button>
                </div>
            </div>

            <div class="mt-4 mb-3">
                <div class="small fw-800 text-muted uppercase mb-2">Ornamen Hiasan (Draggable)</div>
                <div class="d-flex flex-column gap-1">
                    <button class="tool-btn py-2 text-start" onclick="addOrnamentElement('gold_badge')">
                        🏅 Medali Emas Award
                    </button>
                    <button class="tool-btn py-2 text-start" onclick="addOrnamentElement('laurel_wreath')">
                        🌿 Daun Laurel Kemenangan
                    </button>
                    <button class="tool-btn py-2 text-start" onclick="addOrnamentElement('gold_corner')">
                        📐 Siku Ornamen Pojok
                    </button>
                    <button class="tool-btn py-2 text-start" onclick="addOrnamentElement('red_ribbon')">
                        🎗️ Pita Merah Prestasi
                    </button>
                    <button class="tool-btn py-2 text-start" onclick="addOrnamentElement('gold_star')">
                        ⭐ Bintang Kejuaraan
                    </button>
                    <button class="tool-btn py-2 text-start" onclick="addOrnamentElement('gold_divider')">
                        ➖ Garis Pembatas Mewah
                    </button>
                </div>
            </div>

            <div class="mt-4 mb-3">
                <div class="small fw-800 text-muted uppercase mb-2">Bingkai & Pola Latar</div>
                <div class="d-flex flex-column gap-1" style="max-height: 250px; overflow-y: auto; padding-right: 2px;">
                    <button class="tool-btn py-2" onclick="setBgPattern('classic_gold')">
                        <i class="bi bi-border-outer text-warning"></i>
                        <span>Bingkai Emas Klasik</span>
                    </button>
                    <button class="tool-btn py-2" onclick="setBgPattern('red_gold_corners')">
                        <i class="bi bi-border-style" style="color: #ef4444 !important;"></i>
                        <span>Sudut Merah Emas (Image 1)</span>
                    </button>
                    <button class="tool-btn py-2" onclick="setBgPattern('teal_gold_ribbons')">
                        <i class="bi bi-intersect text-success"></i>
                        <span>Pita Teal Emas (Image 2)</span>
                    </button>
                    <button class="tool-btn py-2" onclick="setBgPattern('black_gold_waves')">
                        <i class="bi bi-water text-warning"></i>
                        <span>Gelombang Hitam Emas (Image 3)</span>
                    </button>
                    <button class="tool-btn py-2" onclick="setBgPattern('vintage_guilloche')">
                        <i class="bi bi-journal-code text-secondary"></i>
                        <span>Vintage Guilloche Frame</span>
                    </button>
                    <button class="tool-btn py-2 btn-red-outline" onclick="setBgPattern(null)">
                        <i class="bi bi-x-circle" style="color: #ef4444 !important;"></i>
                        <span>Hapus Pola / Bingkai</span>
                    </button>
                </div>
            </div>
        </div>
    </aside>

    <!-- Canvas Area -->
    <main class="workspace" id="builder-workspace">
        <div class="cert-canvas-container" id="cert-canvas" style="background-color: #ffffff;">
            <!-- Render Elements here -->
        </div>
    </main>

    <!-- Sidebar Right: Properties Panel -->
    <aside class="sidebar-right">
        <h6 class="sidebar-title">
            <span><i class="bi bi-sliders me-1 text-primary"></i> Properti Elemen</span>
        </h6>
        <div class="sidebar-content" id="properties-panel">
            <div class="text-center py-5 text-muted" id="properties-empty">
                <i class="bi bi-mouse3" style="font-size:2rem; display:block; margin-bottom:8px;"></i>
                Pilih elemen pada canvas untuk melihat atau mengubah properti.
            </div>
            
            <div id="properties-content" style="display:none;">
                <!-- Background properties -->
                <div id="prop-bg-section" style="display:none;">
                    <div class="mb-3">
                        <label class="form-field-label small text-muted fw-bold">Warna Latar</label>
                        <input type="color" id="prop-bg-color" class="form-control form-control-color w-100" oninput="updateCanvasBackground()">
                    </div>
                    <div class="mb-3">
                        <label class="form-field-label small text-muted fw-bold">Gradasi Cepat</label>
                        <div class="d-flex flex-wrap gap-2 mt-1">
                            <span class="bg-color-option" style="background:#ffffff;" onclick="setCanvasBackground('#ffffff')"></span>
                            <span class="bg-color-option" style="background:linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);" onclick="setCanvasBackground(null, 'linear-gradient(135deg, #1e1b4b 0%, #312e81 100%)')"></span>
                            <span class="bg-color-option" style="background:linear-gradient(135deg, #f8fafc 0%, #cbd5e1 100%);" onclick="setCanvasBackground(null, 'linear-gradient(135deg, #f8fafc 0%, #cbd5e1 100%)')"></span>
                            <span class="bg-color-option" style="background:linear-gradient(135deg, #6d28d9 0%, #db2777 100%);" onclick="setCanvasBackground(null, 'linear-gradient(135deg, #6d28d9 0%, #db2777 100%)')"></span>
                            <span class="bg-color-option" style="background:linear-gradient(155deg, #001060 0%, #0033cc 60%, #0050ff 100%);" onclick="setCanvasBackground(null, 'linear-gradient(155deg, #001060 0%, #0033cc 60%, #0050ff 100%)')"></span>
                        </div>
                    </div>
                    <hr>
                </div>

                <!-- Position & Size properties -->
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="small text-muted fw-bold">Posisi Kiri (X)</label>
                        <input type="number" id="prop-x" class="form-control form-control-sm" oninput="updateActiveElementProperty('x', this.value)">
                    </div>
                    <div class="col-6">
                        <label class="small text-muted fw-bold">Posisi Atas (Y)</label>
                        <input type="number" id="prop-y" class="form-control form-control-sm" oninput="updateActiveElementProperty('y', this.value)">
                    </div>
                </div>
                
                <div class="row g-2 mb-3" id="prop-dimensions">
                    <div class="col-6">
                        <label class="small text-muted fw-bold">Lebar (px)</label>
                        <input type="number" id="prop-width" class="form-control form-control-sm" oninput="updateActiveElementProperty('width', this.value)">
                    </div>
                    <div class="col-6" id="prop-height-container">
                        <label class="small text-muted fw-bold">Tinggi (px)</label>
                        <input type="number" id="prop-height" class="form-control form-control-sm" oninput="updateActiveElementProperty('height', this.value)">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="small text-muted fw-bold">Z-Index (Urutan Layering)</label>
                    <input type="number" id="prop-zindex" class="form-control form-control-sm" min="1" max="100" oninput="updateActiveElementProperty('zIndex', this.value)">
                </div>

                <!-- Text specific properties -->
                <div id="prop-text-section" style="display:none;">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">Konten Teks</label>
                        <textarea id="prop-content" class="form-control form-control-sm" rows="3" oninput="updateActiveElementProperty('content', this.value)"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">Jenis Font</label>
                        <select id="prop-font-family" class="form-select form-select-sm" onchange="updateActiveElementProperty('fontFamily', this.value)">
                            <option value="Helvetica">Helvetica / Arial (Modern)</option>
                            <option value="Georgia">Georgia (Classic)</option>
                            <option value="Great Vibes">Great Vibes (Signature Script)</option>
                            <option value="Courier New">Courier New (Monospace)</option>
                        </select>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Ukuran Font</label>
                            <input type="number" id="prop-font-size" class="form-control form-control-sm" oninput="updateActiveElementProperty('fontSize', this.value)">
                        </div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Warna</label>
                            <input type="color" id="prop-color" class="form-control form-control-color w-100" oninput="updateActiveElementProperty('color', this.value)">
                        </div>
                    </div>
                    
                    <div class="d-flex gap-2 mb-3 align-items-center">
                        <button class="btn btn-sm btn-outline-secondary" id="btn-bold" onclick="toggleTextDecoration('bold')">B</button>
                        <button class="btn btn-sm btn-outline-secondary" id="btn-italic" onclick="toggleTextDecoration('italic')">I</button>
                        <button class="btn btn-sm btn-outline-secondary" id="btn-underline" onclick="toggleTextDecoration('underline')">U</button>
                        
                        <div class="ms-auto btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary" id="btn-align-left" onclick="setTextAlignment('left')"><i class="bi bi-align-left"></i></button>
                            <button class="btn btn-outline-secondary" id="btn-align-center" onclick="setTextAlignment('center')"><i class="bi bi-align-center"></i></button>
                            <button class="btn btn-outline-secondary" id="btn-align-right" onclick="setTextAlignment('right')"><i class="bi bi-align-right"></i></button>
                        </div>
                    </div>
                </div>

                <!-- Signature specific properties -->
                <div id="prop-sig-section" style="display:none;">
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">Nama Penandatangan</label>
                        <input type="text" id="prop-sig-name" class="form-control form-control-sm" oninput="updateActiveElementProperty('name', this.value)">
                    </div>
                    <div class="mb-3">
                        <label class="small text-muted fw-bold">Jabatan</label>
                        <input type="text" id="prop-sig-pos" class="form-control form-control-sm" oninput="updateActiveElementProperty('position', this.value)">
                    </div>
                </div>

                <!-- Box specific properties -->
                <div id="prop-box-section" style="display:none;">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Warna Isian</label>
                            <input type="color" id="prop-box-bgcolor" class="form-control form-control-color w-100" oninput="updateActiveElementProperty('bgColor', this.value)">
                        </div>
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Warna Border</label>
                            <input type="color" id="prop-box-bordercolor" class="form-control form-control-color w-100" oninput="updateActiveElementProperty('borderColor', this.value)">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="small text-muted fw-bold">Tebal Border</label>
                            <input type="number" id="prop-box-borderwidth" class="form-control form-control-sm" oninput="updateActiveElementProperty('borderWidth', this.value)">
                        </div>
                        <div class="col-6" id="prop-box-radius-container">
                            <label class="small text-muted fw-bold">Radius Sudut</label>
                            <input type="number" id="prop-box-borderradius" class="form-control form-control-sm" oninput="updateActiveElementProperty('borderRadius', this.value)">
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Pinned Footer for Action Button -->
        <div class="p-3 border-top bg-light" id="properties-footer" style="display:none;">
            <button class="btn btn-sm btn-danger w-100 fw-bold py-2 rounded-3 shadow-sm" onclick="deleteActiveElement()">
                <i class="bi bi-trash me-1"></i> Hapus Elemen
            </button>
        </div>
    </aside>
</div>

<!-- Forms & metadata -->
<form id="save-template-form" action="{{ $event ? route('admin.crm.certificates.save-custom-template', $event) : route('admin.crm.certificates.save-custom-template-course', $course) }}" method="POST" style="display:none;">
    @csrf
    <input type="hidden" name="template_json" id="template_json_field">
</form>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // State of elements
    let templateState = {
        background: {
            color: '#ffffff',
            gradient: null
        },
        elements: []
    };
    
    // Parse existing custom template if set
    const initialTemplate = @json($customTemplate);
    if (initialTemplate && initialTemplate.elements) {
        templateState = initialTemplate;
    }
    
    let activeElementId = null;
    let isDragging = false;
    let isResizing = false;
    let dragStartX = 0;
    let dragStartY = 0;
    let elementStartX = 0;
    let elementStartY = 0;
    let elementStartWidth = 0;
    let elementStartHeight = 0;

    const canvas = document.getElementById('cert-canvas');

    // Rendering template color state on load
    function initCanvas() {
        if (templateState.background.gradient) {
            canvas.style.background = templateState.background.gradient;
        } else {
            canvas.style.background = templateState.background.color;
        }
        
        // Clear canvas
        canvas.innerHTML = '';

        // Render background pattern image if set
        if (templateState.background.image) {
            const bgImg = document.createElement('img');
            bgImg.id = 'canvas-bg-pattern';
            bgImg.src = templateState.background.image;
            bgImg.style.position = 'absolute';
            bgImg.style.left = '0';
            bgImg.style.top = '0';
            bgImg.style.width = '100%';
            bgImg.style.height = '100%';
            bgImg.style.zIndex = '1';
            bgImg.style.pointerEvents = 'none';
            canvas.appendChild(bgImg);
        }
        
        // Render elements
        templateState.elements.forEach(el => renderElementOnCanvas(el));
        
        // Redraw scale
        adjustCanvasScale();
    }

    function renderElementOnCanvas(el) {
        const div = document.createElement('div');
        div.className = 'canvas-element';
        div.id = 'el-' + el.id;
        div.style.left = el.x + 'px';
        div.style.top = el.y + 'px';
        div.style.zIndex = el.zIndex || 1;
        
        if (el.width) div.style.width = el.width + 'px';
        if (el.height) div.style.height = el.height + 'px';
        
        if (activeElementId === el.id) {
            div.classList.add('selected');
        }

        // Render type specific HTML
        if (el.type === 'text' || el.type === 'variable') {
            div.style.fontFamily = el.fontFamily || 'Helvetica';
            div.style.fontSize = el.fontSize + 'px';
            div.style.color = el.color || '#1e293b';
            div.style.textAlign = el.align || 'left';
            div.style.fontWeight = el.bold ? 'bold' : 'normal';
            div.style.fontStyle = el.italic ? 'italic' : 'normal';
            div.style.textDecoration = el.underline ? 'underline' : 'none';
            div.style.whiteSpace = 'pre-wrap';
            
            // Format variables for visual aids
            let content = el.content || '';
            if (el.type === 'variable') {
                content = el.content;
            }
            div.innerHTML = content;
        } 
        else if (el.type === 'logo' || el.type === 'shape') {
            div.innerHTML = `<img src="${el.base64 || el.src}" style="width:100%; height:100%; pointer-events:none; display:block;">`;
        } 
        else if (el.type === 'signature') {
            div.style.fontFamily = 'Helvetica';
            div.style.textAlign = 'center';
            let imgHtml = `<div style="height:55px;"></div>`;
            if (el.base64 || el.src) {
                imgHtml = `<img src="${el.base64 || el.src}" style="height:55px; width:auto; pointer-events:none; display:block; margin:0 auto 2px;">`;
            }
            div.innerHTML = `
                ${imgHtml}
                <div style="width:90%; border-bottom:1px solid #000; margin:2px auto;"></div>
                <div style="font-size:11px; font-weight:bold; color:#0f172a; margin-top:2px;">${el.name || 'Authorized Signee'}</div>
                <div style="font-size:9px; color:#64748b; font-style:italic;">${el.position || 'Authorized Position'}</div>
            `;
        }
        else if (el.type === 'box') {
            div.style.background = el.bgColor || 'transparent';
            div.style.border = `${el.borderWidth || 0}px ${el.borderStyle || 'solid'} ${el.borderColor || '#000'}`;
            div.style.borderRadius = `${el.borderRadius || 0}px`;
        }

        // Add resizing handles
        const resizeHandle = document.createElement('div');
        resizeHandle.className = 'resize-handle handle-se';
        div.appendChild(resizeHandle);

        // Bind Drag Event
        div.addEventListener('mousedown', (e) => startDrag(e, el.id));
        resizeHandle.addEventListener('mousedown', (e) => startResize(e, el.id));

        canvas.appendChild(div);
    }

    function selectElement(id) {
        activeElementId = id;
        
        // Remove active class from all
        document.querySelectorAll('.canvas-element').forEach(el => el.classList.remove('selected'));
        
        // Add to selected
        const activeDiv = document.getElementById('el-' + id);
        if (activeDiv) activeDiv.classList.add('selected');
        
        // Show properties
        showPropertiesPanel(id);
    }

    function deselectAll() {
        activeElementId = null;
        document.querySelectorAll('.canvas-element').forEach(el => el.classList.remove('selected'));
        showPropertiesPanel(null);
    }

    // Drag-and-drop mechanics
    function startDrag(e, id) {
        if (e.target.classList.contains('resize-handle')) return; // Handle resizing separately
        
        e.preventDefault();
        selectElement(id);
        
        isDragging = true;
        const el = templateState.elements.find(item => item.id === id);
        
        // Standardize mouse offsets with zoom scaling factored in
        const scale = getCanvasScale();
        dragStartX = e.clientX;
        dragStartY = e.clientY;
        elementStartX = el.x;
        elementStartY = el.y;
        
        document.addEventListener('mousemove', dragMove);
        document.addEventListener('mouseup', stopDrag);
    }

    function dragMove(e) {
        if (!isDragging) return;
        
        const scale = getCanvasScale();
        const dx = (e.clientX - dragStartX) / scale;
        const dy = (e.clientY - dragStartY) / scale;
        
        const el = templateState.elements.find(item => item.id === activeElementId);
        
        // Smooth positioning within bounds
        el.x = Math.round(elementStartX + dx);
        el.y = Math.round(elementStartY + dy);
        
        // Snap boundary check
        el.x = Math.max(0, Math.min(el.x, 1000 - (el.width || 50)));
        el.y = Math.max(0, Math.min(el.y, 706 - (el.height || 20)));

        const div = document.getElementById('el-' + activeElementId);
        if (div) {
            div.style.left = el.x + 'px';
            div.style.top = el.y + 'px';
        }
        
        // Update input field indicators
        document.getElementById('prop-x').value = el.x;
        document.getElementById('prop-y').value = el.y;
    }

    function stopDrag() {
        isDragging = false;
        document.removeEventListener('mousemove', dragMove);
        document.removeEventListener('mouseup', stopDrag);
    }

    // Resizing mechanics
    function startResize(e, id) {
        e.preventDefault();
        e.stopPropagation();
        
        isResizing = true;
        selectElement(id);
        
        const el = templateState.elements.find(item => item.id === id);
        
        dragStartX = e.clientX;
        dragStartY = e.clientY;
        elementStartWidth = el.width || 150;
        elementStartHeight = el.height || 80;
        
        document.addEventListener('mousemove', resizeMove);
        document.addEventListener('mouseup', stopResize);
    }

    function resizeMove(e) {
        if (!isResizing) return;
        
        const scale = getCanvasScale();
        const dx = (e.clientX - dragStartX) / scale;
        const dy = (e.clientY - dragStartY) / scale;
        
        const el = templateState.elements.find(item => item.id === activeElementId);
        
        el.width = Math.max(30, Math.round(elementStartWidth + dx));
        el.height = Math.max(20, Math.round(elementStartHeight + dy));

        const div = document.getElementById('el-' + activeElementId);
        if (div) {
            div.style.width = el.width + 'px';
            div.style.height = el.height + 'px';
        }
        
        document.getElementById('prop-width').value = el.width;
        document.getElementById('prop-height').value = el.height;
    }

    function stopResize() {
        isResizing = false;
        document.removeEventListener('mousemove', resizeMove);
        document.removeEventListener('mouseup', stopResize);
    }

    // Properties sidebar binding
    function showPropertiesPanel(id) {
        const emptyPanel = document.getElementById('properties-empty');
        const contentPanel = document.getElementById('properties-content');
        const footerPanel = document.getElementById('properties-footer');
        
        if (!id) {
            emptyPanel.style.display = 'block';
            contentPanel.style.display = 'none';
            if (footerPanel) footerPanel.style.display = 'none';
            return;
        }
        
        emptyPanel.style.display = 'none';
        contentPanel.style.display = 'block';
        if (footerPanel) footerPanel.style.display = 'block';
        
        const el = templateState.elements.find(item => item.id === id);
        
        // Common dimensions
        document.getElementById('prop-x').value = el.x;
        document.getElementById('prop-y').value = el.y;
        document.getElementById('prop-width').value = el.width || 0;
        document.getElementById('prop-height').value = el.height || 0;
        document.getElementById('prop-zindex').value = el.zIndex || 1;

        // Hide specific sections by default
        document.getElementById('prop-text-section').style.display = 'none';
        document.getElementById('prop-sig-section').style.display = 'none';
        document.getElementById('prop-box-section').style.display = 'none';
        document.getElementById('prop-dimensions').style.display = 'flex';
        document.getElementById('prop-height-container').style.display = 'block';

        if (el.type === 'text' || el.type === 'variable') {
            document.getElementById('prop-text-section').style.display = 'block';
            document.getElementById('prop-content').value = el.content || '';
            document.getElementById('prop-font-family').value = el.fontFamily || 'Helvetica';
            document.getElementById('prop-font-size').value = el.fontSize || 14;
            document.getElementById('prop-color').value = el.color || '#1e293b';
            
            // Format state buttons
            toggleActiveButtonState('btn-bold', el.bold);
            toggleActiveButtonState('btn-italic', el.italic);
            toggleActiveButtonState('btn-underline', el.underline);
            
            // Text align state
            const align = el.align || 'left';
            toggleActiveButtonState('btn-align-left', align === 'left');
            toggleActiveButtonState('btn-align-center', align === 'center');
            toggleActiveButtonState('btn-align-right', align === 'right');
            
            if (el.type === 'variable') {
                // Readonly text content for variables
                document.getElementById('prop-content').disabled = true;
            } else {
                document.getElementById('prop-content').disabled = false;
            }
        } 
        else if (el.type === 'logo') {
            document.getElementById('prop-height-container').style.display = 'none'; // Width controls height proportionally
        }
        else if (el.type === 'signature') {
            document.getElementById('prop-sig-section').style.display = 'block';
            document.getElementById('prop-sig-name').value = el.name || '';
            document.getElementById('prop-sig-pos').value = el.position || '';
        }
        else if (el.type === 'box' || el.type === 'shape') {
            document.getElementById('prop-box-section').style.display = 'block';
            document.getElementById('prop-box-bgcolor').value = el.bgColor || '#ffffff';
            document.getElementById('prop-box-bordercolor').value = el.borderColor || '#000000';
            document.getElementById('prop-box-borderwidth').value = el.borderWidth || 1;
            
            const radiusContainer = document.getElementById('prop-box-radius-container');
            if (el.type === 'shape') {
                if (radiusContainer) radiusContainer.style.display = 'none';
            } else {
                if (radiusContainer) radiusContainer.style.display = 'block';
                document.getElementById('prop-box-borderradius').value = el.borderRadius || 0;
            }
        }
    }

    function toggleActiveButtonState(btnId, isActive) {
        const btn = document.getElementById(btnId);
        if (!btn) return;
        if (isActive) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-primary');
        } else {
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-secondary');
        }
    }

    function updateActiveElementProperty(property, value) {
        if (!activeElementId) return;
        const el = templateState.elements.find(item => item.id === activeElementId);
        
        if (property === 'x' || property === 'y' || property === 'width' || property === 'height' || property === 'fontSize' || property === 'borderWidth' || property === 'borderRadius' || property === 'zIndex') {
            value = parseInt(value) || 0;
        }
        
        el[property] = value;
        
        // Re-render element
        const div = document.getElementById('el-' + el.id);
        if (div) {
            // Apply coordinates
            if (property === 'x') div.style.left = value + 'px';
            if (property === 'y') div.style.top = value + 'px';
            if (property === 'width') div.style.width = value + 'px';
            if (property === 'height') div.style.height = value + 'px';
            if (property === 'zIndex') div.style.zIndex = value;
            
            // Re-draw internals to match changes
            if (el.type === 'text' || el.type === 'variable') {
                if (property === 'fontFamily') div.style.fontFamily = value;
                if (property === 'fontSize') div.style.fontSize = value + 'px';
                if (property === 'color') div.style.color = value;
                if (property === 'content') div.innerHTML = value;
                if (property === 'align') div.style.textAlign = value;
            }
            else if (el.type === 'signature') {
                const nameDiv = div.querySelector('div:nth-of-type(1)');
                const posDiv = div.querySelector('div:nth-of-type(2)');
                // Complete re-render signature to match layout
                let imgHtml = `<div style="height:55px;"></div>`;
                if (el.base64 || el.src) {
                    imgHtml = `<img src="${el.base64 || el.src}" style="height:55px; width:auto; pointer-events:none; display:block; margin:0 auto 2px;">`;
                }
                div.innerHTML = `
                    ${imgHtml}
                    <div style="width:90%; border-bottom:1px solid #000; margin:2px auto;"></div>
                    <div style="font-size:11px; font-weight:bold; color:#0f172a; margin-top:2px;">${el.name || 'Authorized Signee'}</div>
                    <div style="font-size:9px; color:#64748b; font-style:italic;">${el.position || 'Authorized Position'}</div>
                `;
            }
            else if (el.type === 'box') {
                if (property === 'bgColor') div.style.background = value;
                if (property === 'borderWidth' || property === 'borderColor') {
                    div.style.border = `${el.borderWidth || 0}px solid ${el.borderColor || '#000'}`;
                }
                if (property === 'borderRadius') div.style.borderRadius = value + 'px';
            }
            else if (el.type === 'shape') {
                // If fill color, stroke color, or stroke width is changed, regenerate the base64 SVG string
                if (property === 'bgColor' || property === 'borderColor' || property === 'borderWidth') {
                    el.base64 = generateShapeSvg(el.shapeType, el.bgColor || '#3b82f6', el.borderColor || '#1d4ed8', el.borderWidth || 0);
                    const img = div.querySelector('img');
                    if (img) img.src = el.base64;
                }
            }
        }
    }

    function toggleTextDecoration(style) {
        if (!activeElementId) return;
        const el = templateState.elements.find(item => item.id === activeElementId);
        
        if (style === 'bold') el.bold = !el.bold;
        if (style === 'italic') el.italic = !el.italic;
        if (style === 'underline') el.underline = !el.underline;
        
        // Reflect on DOM
        const div = document.getElementById('el-' + el.id);
        if (div) {
            if (style === 'bold') div.style.fontWeight = el.bold ? 'bold' : 'normal';
            if (style === 'italic') div.style.fontStyle = el.italic ? 'italic' : 'normal';
            if (style === 'underline') div.style.textDecoration = el.underline ? 'underline' : 'none';
        }
        
        // Update Property state
        showPropertiesPanel(activeElementId);
    }

    function setTextAlignment(align) {
        if (!activeElementId) return;
        updateActiveElementProperty('align', align);
        showPropertiesPanel(activeElementId);
    }

    // Background operations
    function updateCanvasBackground() {
        const val = document.getElementById('prop-bg-color').value;
        setCanvasBackground(val);
    }

    function setCanvasBackground(color, gradient = null) {
        templateState.background.color = color || '#ffffff';
        templateState.background.gradient = gradient;
        
        if (gradient) {
            canvas.style.background = gradient;
        } else {
            canvas.style.background = color;
        }
    }

    // Set SVG Pattern / Border overlay
    function setBgPattern(pattern) {
        if (!pattern) {
            templateState.background.image = null;
            initCanvas();
            return;
        }
        
        let svgContent = '';
        
        if (pattern === 'classic_gold') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 706" width="1000" height="706">
  <rect x="25" y="25" width="950" height="656" fill="none" stroke="#d4af37" stroke-width="4"/>
  <rect x="35" y="35" width="930" height="636" fill="none" stroke="#d4af37" stroke-width="1.5" stroke-dasharray="8 4"/>
  <path d="M 20,40 L 40,20 M 960,20 L 980,40 M 980,666 L 960,686 M 40,686 L 20,666" stroke="#d4af37" stroke-width="3"/>
  <rect x="20" y="20" width="20" height="20" fill="#d4af37"/>
  <rect x="960" y="20" width="20" height="20" fill="#d4af37"/>
  <rect x="20" y="666" width="20" height="20" fill="#d4af37"/>
  <rect x="960" y="666" width="20" height="20" fill="#d4af37"/>
</svg>
            `;
        } 
        else if (pattern === 'red_gold_corners') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 706" width="1000" height="706">
  <!-- Subtle diagonal shadow bands on background -->
  <path d="M 0,0 L 400,0 L 0,400 Z" fill="#f1f5f9" opacity="0.4"/>
  <path d="M 1000,706 L 600,706 L 1000,306 Z" fill="#f1f5f9" opacity="0.4"/>
  <path d="M 300,0 L 700,706 L 550,706 L 150,0 Z" fill="#f8fafc" opacity="0.5"/>
  <!-- Gold Rectangular Frame -->
  <rect x="35" y="35" width="930" height="636" fill="none" stroke="#d4af37" stroke-width="4"/>
  <!-- Top Right Corners (inspired by Image 1) -->
  <polygon points="700,0 1000,300 1000,0" fill="#d4af37"/>
  <polygon points="730,0 1000,270 1000,0" fill="#b91c1c"/>
  <polygon points="780,0 1000,220 1000,0" fill="#7f1d1d"/>
  <!-- Bottom Left Corners (inspired by Image 1) -->
  <polygon points="300,706 0,406 0,706" fill="#d4af37"/>
  <polygon points="270,706 0,436 0,706" fill="#b91c1c"/>
  <polygon points="220,706 0,486 0,706" fill="#7f1d1d"/>
  <!-- Decorative small polygon lines -->
  <polygon points="0,0 150,0 0,150" fill="#7f1d1d" opacity="0.1"/>
  <polygon points="1000,706 850,706 1000,556" fill="#7f1d1d" opacity="0.1"/>
</svg>
            `;
        }
        else if (pattern === 'teal_gold_ribbons') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 706" width="1000" height="706">
  <!-- Outer Border Frame -->
  <rect x="25" y="25" width="950" height="656" fill="none" stroke="#0f766e" stroke-width="6"/>
  <rect x="37" y="37" width="926" height="632" fill="none" stroke="#d4af37" stroke-width="1.5"/>
  <!-- Top Left Overlapping Geometric Ribbons (inspired by Image 2) -->
  <polygon points="0,0 240,0 0,240" fill="#0f766e"/>
  <polygon points="0,0 200,0 0,200" fill="#0d9488"/>
  <polygon points="0,0 150,0 0,150" fill="#14b8a6"/>
  <!-- Gold Stripe Accents -->
  <polygon points="240,0 255,0 0,255 0,240" fill="#d4af37"/>
  <polygon points="110,0 120,0 0,120 0,110" fill="#f59e0b"/>
  <!-- Bottom Right Overlapping Geometric Ribbons (inspired by Image 2) -->
  <polygon points="1000,706 760,706 1000,466" fill="#0f766e"/>
  <polygon points="1000,706 800,706 1000,506" fill="#0d9488"/>
  <polygon points="1000,706 850,706 1000,556" fill="#14b8a6"/>
  <!-- Gold Stripe Accents -->
  <polygon points="760,706 745,706 1000,451 1000,466" fill="#d4af37"/>
  <polygon points="890,706 880,706 1000,586 1000,596" fill="#f59e0b"/>
  <!-- Minor Corner Cuts -->
  <polygon points="0,706 50,706 0,656" fill="#0f766e"/>
  <polygon points="1000,0 950,0 1000,50" fill="#0f766e"/>
</svg>
            `;
        }
        else if (pattern === 'black_gold_waves') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 706" width="1000" height="706">
  <!-- Dark backdrop border -->
  <rect x="0" y="0" width="1000" height="706" fill="#111827"/>
  <!-- White Canvas Panel in the middle -->
  <rect x="50" y="50" width="900" height="606" fill="#ffffff" stroke="#d4af37" stroke-width="3"/>
  <!-- Top Left Corner Waves (inspired by Image 3) -->
  <path d="M 0,0 C 200,0 250,220 0,380 Z" fill="#d4af37"/>
  <path d="M 0,0 C 170,0 220,190 0,340 Z" fill="#1f2937"/>
  <path d="M 0,0 C 120,0 160,130 0,230 Z" fill="#d4af37"/>
  <path d="M 0,0 C 90,0 120,100 0,180 Z" fill="#111827"/>
  
  <!-- Bottom Right Corner Waves (inspired by Image 3) -->
  <path d="M 1000,706 C 800,706 750,486 1000,326 Z" fill="#d4af37"/>
  <path d="M 1000,706 C 830,706 780,516 1000,366 Z" fill="#1f2937"/>
  <path d="M 1000,706 C 880,706 840,576 1000,476 Z" fill="#d4af37"/>
  <path d="M 1000,706 C 910,706 880,606 1000,526 Z" fill="#111827"/>
  
  <!-- Outer Accent Borders -->
  <path d="M 600,0 Q 800,45 1000,0 Z" fill="#d4af37"/>
  <path d="M 400,706 Q 200,661 0,706 Z" fill="#d4af37"/>
</svg>
            `;
        }
        else if (pattern === 'vintage_guilloche') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 706" width="1000" height="706">
  <rect x="20" y="20" width="960" height="666" fill="none" stroke="#1e293b" stroke-width="6"/>
  <rect x="28" y="28" width="944" height="650" fill="none" stroke="#94a3b8" stroke-width="2"/>
  <rect x="34" y="34" width="932" height="638" fill="none" stroke="#1e293b" stroke-width="1.5" stroke-dasharray="6 3"/>
  <path d="M 10,10 L 50,10 L 50,50 L 10,50 Z" fill="none" stroke="#1e293b" stroke-width="2"/>
  <circle cx="30" cy="30" r="10" fill="none" stroke="#1e293b" stroke-width="2"/>
  <path d="M 950,10 L 990,10 L 990,50 L 950,50 Z" fill="none" stroke="#1e293b" stroke-width="2"/>
  <circle cx="970" cy="30" r="10" fill="none" stroke="#1e293b" stroke-width="2"/>
  <path d="M 10,656 L 50,656 L 50,696 L 10,696 Z" fill="none" stroke="#1e293b" stroke-width="2"/>
  <circle cx="30" cy="676" r="10" fill="none" stroke="#1e293b" stroke-width="2"/>
  <path d="M 950,656 L 990,656 L 990,696 L 950,696 Z" fill="none" stroke="#1e293b" stroke-width="2"/>
  <circle cx="970" cy="676" r="10" fill="none" stroke="#1e293b" stroke-width="2"/>
</svg>
            `;
        }
        
        // Encode SVG correctly to base64
        const encoded = btoa(unescape(encodeURIComponent(svgContent.trim())));
        templateState.background.image = 'data:image/svg+xml;base64,' + encoded;
        initCanvas();
    }

    // Element addition operations
    function addTextElement(content, fontFamily, fontSize, color, bold = false) {
        const id = 'text_' + Date.now();
        const el = {
            id,
            type: 'text',
            content,
            fontFamily,
            fontSize,
            color,
            bold,
            italic: false,
            underline: false,
            align: 'center',
            x: 250,
            y: 200,
            width: 500,
            height: 60,
            zIndex: 10
        };
        templateState.elements.push(el);
        renderElementOnCanvas(el);
        selectElement(id);
    }

    function addVariableElement(variable, fontFamily, fontSize, color, bold = false) {
        const id = 'var_' + Date.now();
        const el = {
            id,
            type: 'variable',
            content: variable,
            fontFamily,
            fontSize,
            color,
            bold,
            italic: false,
            underline: false,
            align: 'center',
            x: 250,
            y: 300,
            width: 500,
            height: 50,
            zIndex: 10
        };
        templateState.elements.push(el);
        renderElementOnCanvas(el);
        selectElement(id);
    }

    function addLogoElement(path, base64) {
        const id = 'logo_' + Date.now();
        const el = {
            id,
            type: 'logo',
            src: path,
            base64,
            x: 450,
            y: 50,
            width: 100,
            height: 100,
            zIndex: 10
        };
        templateState.elements.push(el);
        renderElementOnCanvas(el);
        selectElement(id);
    }

    function addSignatureElement(path, base64, name, position) {
        const id = 'sig_' + Date.now();
        const el = {
            id,
            type: 'signature',
            src: path,
            base64,
            name,
            position,
            x: 400,
            y: 520,
            width: 200,
            height: 120,
            zIndex: 10
        };
        templateState.elements.push(el);
        renderElementOnCanvas(el);
        selectElement(id);
    }

    function addBoxElement() {
        const id = 'box_' + Date.now();
        const el = {
            id,
            type: 'box',
            bgColor: 'transparent',
            borderColor: '#fbbf24',
            borderWidth: 4,
            borderStyle: 'solid',
            borderRadius: 0,
            x: 20,
            y: 20,
            width: 960,
            height: 666,
            zIndex: 2
        };
        templateState.elements.push(el);
        renderElementOnCanvas(el);
        selectElement(id);
    }

    // Generate dynamic shape vector image inside JS
    function generateShapeSvg(shapeType, bgColor, borderColor, borderWidth) {
        let svg = '';
        borderWidth = parseInt(borderWidth) || 0;
        
        if (shapeType === 'triangle') {
            svg = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <polygon points="50,5 95,95 5,95" fill="${bgColor}" stroke="${borderColor}" stroke-width="${borderWidth}"/>
</svg>
            `;
        }
        else if (shapeType === 'diamond') {
            svg = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <polygon points="50,5 95,50 50,95 5,50" fill="${bgColor}" stroke="${borderColor}" stroke-width="${borderWidth}"/>
</svg>
            `;
        }
        else if (shapeType === 'star') {
            svg = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <polygon points="50,5 64,36 98,36 70,57 81,91 50,70 19,91 30,57 2,36 36,36" fill="${bgColor}" stroke="${borderColor}" stroke-width="${borderWidth}"/>
</svg>
            `;
        }
        else if (shapeType === 'medal_seal') {
            svg = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <path d="M 50,2 L 56,15 L 70,10 L 72,24 L 86,22 L 83,36 L 95,38 L 88,51 L 97,58 L 86,68 L 91,81 L 78,86 L 80,98 L 66,97 L 62,108 L 50,102 L 38,108 L 34,97 L 20,98 L 22,86 L 9,81 L 14,68 L 3,58 L 12,51 L 5,38 L 17,36 L 14,22 L 28,24 L 30,10 L 44,15 Z" fill="${bgColor}" stroke="${borderColor}" stroke-width="${borderWidth}"/>
  <circle cx="50" cy="50" r="30" fill="none" stroke="${borderColor}" stroke-width="${borderWidth}" stroke-dasharray="4 2"/>
</svg>
            `;
        }
        else if (shapeType === 'ribbon_tail') {
            svg = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <polygon points="20,5 80,5 65,95 50,75 35,95" fill="${bgColor}" stroke="${borderColor}" stroke-width="${borderWidth}"/>
</svg>
            `;
        }
        else if (shapeType === 'corner_poly') {
            svg = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <polygon points="0,0 100,0 0,100" fill="${bgColor}" stroke="${borderColor}" stroke-width="${borderWidth}"/>
</svg>
            `;
        }
        else if (shapeType === 'ornate_line') {
            svg = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 30" width="300" height="30">
  <line x1="10" y1="15" x2="130" y2="15" stroke="${bgColor}" stroke-width="${borderWidth || 2}"/>
  <polygon points="150,5 160,15 150,25 140,15" fill="${bgColor}" stroke="${borderColor}" stroke-width="${borderWidth}"/>
  <line x1="170" y1="15" x2="290" y2="15" stroke="${bgColor}" stroke-width="${borderWidth || 2}"/>
</svg>
            `;
        }
        
        const encoded = btoa(unescape(encodeURIComponent(svg.trim())));
        return 'data:image/svg+xml;base64,' + encoded;
    }

    // Add shapes as editable Box presets or Shape elements
    function addShapePreset(shapeType) {
        if (shapeType === 'rect') {
            addBoxElement();
            return;
        }
        
        if (shapeType === 'circle') {
            const id = 'box_' + Date.now();
            const el = {
                id,
                type: 'box',
                bgColor: '#3b82f6',
                borderColor: '#1d4ed8',
                borderWidth: 2,
                borderStyle: 'solid',
                borderRadius: 9999, // Circle preset
                x: 420,
                y: 270,
                width: 150,
                height: 150,
                zIndex: 5
            };
            templateState.elements.push(el);
            renderElementOnCanvas(el);
            selectElement(id);
            return;
        }
        
        // Triangle, Diamond, Star, Medal Seal, Ribbon Tail, Corner Poly, Ornate Line: add as type 'shape'
        const id = 'shape_' + Date.now();
        let defaultBgColor = '#3b82f6';
        let defaultBorderColor = '#1d4ed8';
        let defaultBorderWidth = 2;
        let w = 120;
        let h = 120;

        if (shapeType === 'medal_seal') {
            defaultBgColor = '#fbbf24';
            defaultBorderColor = '#d97706';
            defaultBorderWidth = 2;
        } else if (shapeType === 'ribbon_tail') {
            defaultBgColor = '#ef4444';
            defaultBorderColor = '#dc2626';
            defaultBorderWidth = 0;
            w = 80;
            h = 130;
        } else if (shapeType === 'corner_poly') {
            defaultBgColor = '#b91c1c';
            defaultBorderColor = '#d4af37';
            defaultBorderWidth = 2;
            w = 200;
            h = 200;
        } else if (shapeType === 'ornate_line') {
            defaultBgColor = '#d4af37';
            defaultBorderColor = '#b45309';
            defaultBorderWidth = 1;
            w = 400;
            h = 30;
        }
        
        const base64 = generateShapeSvg(shapeType, defaultBgColor, defaultBorderColor, defaultBorderWidth);
        const el = {
            id,
            type: 'shape',
            shapeType: shapeType,
            bgColor: defaultBgColor,
            borderColor: defaultBorderColor,
            borderWidth: defaultBorderWidth,
            x: 440,
            y: 290,
            width: w,
            height: h,
            zIndex: 5,
            base64: base64
        };
        
        templateState.elements.push(el);
        renderElementOnCanvas(el);
        selectElement(id);
    }

    // Add SVG ornament as a draggable logo-type element
    function addOrnamentElement(type) {
        let svgContent = '';
        let w = 120;
        let h = 120;
        
        if (type === 'gold_badge') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <polygon points="50,0 60,35 95,35 68,57 78,90 50,70 22,90 32,57 5,35 40,35" fill="#f59e0b"/>
  <circle cx="50" cy="50" r="28" fill="#d97706" stroke="#fbbf24" stroke-width="2"/>
  <circle cx="50" cy="50" r="22" fill="#b45309"/>
  <polygon points="50,38 53,46 62,46 55,51 58,59 50,54 42,59 45,51 38,46 47,46" fill="#fbbf24"/>
</svg>
            `;
        } 
        else if (type === 'laurel_wreath') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <path d="M 30,80 Q 15,50 30,20" fill="none" stroke="#d4af37" stroke-width="3" stroke-linecap="round"/>
  <path d="M 70,80 Q 85,50 70,20" fill="none" stroke="#d4af37" stroke-width="3" stroke-linecap="round"/>
  <path d="M 22,70 C 15,65 18,58 24,62 Z" fill="#d4af37"/>
  <path d="M 18,55 C 10,50 14,43 20,47 Z" fill="#d4af37"/>
  <path d="M 20,40 C 14,34 18,28 24,32 Z" fill="#d4af37"/>
  <path d="M 26,27 C 22,20 28,16 32,22 Z" fill="#d4af37"/>
  <path d="M 78,70 C 85,65 82,58 76,62 Z" fill="#d4af37"/>
  <path d="M 82,55 C 90,50 86,43 80,47 Z" fill="#d4af37"/>
  <path d="M 80,40 C 86,34 82,28 76,32 Z" fill="#d4af37"/>
  <path d="M 74,27 C 78,20 72,16 68,22 Z" fill="#d4af37"/>
</svg>
            `;
        }
        else if (type === 'gold_corner') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <path d="M 10,10 L 90,10 L 90,20 L 20,20 L 20,90 L 10,90 Z" fill="#d4af37"/>
  <path d="M 30,30 L 70,30 L 70,40 L 40,40 L 40,70 L 30,70 Z" fill="#d4af37" opacity="0.6"/>
  <circle cx="20" cy="20" r="5" fill="#d4af37"/>
</svg>
            `;
        }
        else if (type === 'red_ribbon') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <path d="M 35,40 L 20,95 L 42,80 L 50,90 L 58,80 L 80,95 L 65,40 Z" fill="#dc2626"/>
  <path d="M 40,45 L 30,90 L 42,78 L 50,86 L 58,78 L 70,90 L 60,45 Z" fill="#991b1b" opacity="0.4"/>
  <circle cx="50" cy="40" r="25" fill="#ef4444" stroke="#dc2626" stroke-width="2"/>
  <circle cx="50" cy="40" r="18" fill="#f59e0b" stroke="#d97706" stroke-width="1.5"/>
</svg>
            `;
        }
        else if (type === 'gold_star') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" width="100" height="100">
  <polygon points="50,5 64,36 98,36 70,57 81,91 50,70 19,91 30,57 2,36 36,36" fill="#fbbf24" stroke="#d97706" stroke-width="2"/>
</svg>
            `;
        }
        else if (type === 'gold_divider') {
            svgContent = `
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 300 30" width="300" height="30">
  <line x1="10" y1="15" x2="130" y2="15" stroke="#d4af37" stroke-width="2"/>
  <polygon points="150,5 160,15 150,25 140,15" fill="#d4af37"/>
  <line x1="170" y1="15" x2="290" y2="15" stroke="#d4af37" stroke-width="2"/>
</svg>
            `;
            w = 300;
            h = 30;
        }

        const encoded = btoa(unescape(encodeURIComponent(svgContent.trim())));
        const base64Data = 'data:image/svg+xml;base64,' + encoded;
        
        const id = 'ornament_' + Date.now();
        const el = {
            id,
            type: 'logo',
            src: '',
            base64: base64Data,
            x: 440,
            y: 290,
            width: w,
            height: h,
            zIndex: 5
        };
        templateState.elements.push(el);
        renderElementOnCanvas(el);
        selectElement(id);
    }

    function deleteActiveElement() {
        if (!activeElementId) return;
        
        // Remove from DOM
        const div = document.getElementById('el-' + activeElementId);
        if (div) div.remove();
        
        // Remove from state
        templateState.elements = templateState.elements.filter(item => item.id !== activeElementId);
        deselectAll();
    }

    // Upload asset via AJAX
    function uploadNewAsset(input) {
        if (!input.files || input.files.length === 0) return;
        
        const file = input.files[0];
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        // Show loading state
        const originalText = input.previousElementSibling.textContent;
        input.previousElementSibling.textContent = 'Mengupload...';

        fetch('{{ route("admin.crm.certificates.builder-upload-asset") }}', {
            method: 'POST',
            body: formData
        })
        .then(res => {
            if (!res.ok) throw new Error('Upload gagal');
            return res.json();
        })
        .then(data => {
            input.previousElementSibling.textContent = originalText;
            input.value = ''; // Reset input
            
            // Show alert/notification
            Swal.fire({
                title: 'Sukses!',
                text: 'Aset berhasil diupload. Pilih opsi untuk menambahkannya ke Canvas:',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Sebagai Logo',
                cancelButtonText: 'Sebagai Tanda Tangan',
                confirmButtonColor: '#6366f1',
                cancelButtonColor: '#10b981'
            }).then((result) => {
                if (result.isConfirmed) {
                    addLogoElement(data.path, data.base64);
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    addSignatureElement(data.path, data.base64, 'Authorized Signee', 'Authorized Position');
                }
            });
        })
        .catch(err => {
            input.previousElementSibling.textContent = originalText;
            input.value = '';
            Swal.fire('Error', 'Gagal mengupload gambar aset.', 'error');
        });
    }

    function saveTemplate() {
        const json = JSON.stringify(templateState);
        document.getElementById('template_json_field').value = json;
        
        // Submit form via fetch to prevent whole page reloads if desired
        const form = document.getElementById('save-template-form');
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                Swal.fire('Berhasil!', data.message, 'success').then(() => {
                    // Navigate back
                    window.location.href = "{{ $event ? route('admin.crm.certificates.edit', $event) : route('admin.crm.certificates.edit-course', $course) }}";
                });
            } else {
                Swal.fire('Error', data.error || 'Gagal menyimpan template', 'error');
            }
        })
        .catch(err => {
            Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
        });
    }

    function resetTemplate() {
        Swal.fire({
            title: 'Hapus Semua?',
            text: "Semua komponen di canvas akan dikosongkan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Kosongkan!'
        }).then((result) => {
            if (result.isConfirmed) {
                templateState.elements = [];
                templateState.background.color = '#ffffff';
                templateState.background.gradient = null;
                initCanvas();
                deselectAll();
            }
        });
    }

    // Responsive Canvas Scaling inside workspace
    function getCanvasScale() {
        const workspace = document.getElementById('builder-workspace');
        if (!workspace) return 1;
        const workW = workspace.clientWidth - 64; // factoring margins
        const workH = workspace.clientHeight - 64;
        
        const scaleX = workW / 1000;
        const scaleY = workH / 706;
        
        return Math.min(1, scaleX, scaleY);
    }

    function adjustCanvasScale() {
        const scale = getCanvasScale();
        canvas.style.transform = `scale(${scale})`;
        
        // Also adjust wrapper container element sizes to avoid double borders
        const scaleWrapper = workspace = document.getElementById('builder-workspace');
    }

    // Set custom page canvas listeners
    canvas.addEventListener('click', (e) => {
        if (e.target === canvas) {
            deselectAll();
            // Show background properties when clicking canvas
            document.getElementById('properties-empty').style.display = 'none';
            document.getElementById('properties-content').style.display = 'block';
            document.getElementById('prop-bg-section').style.display = 'block';
            document.getElementById('prop-dimensions').style.display = 'none';
            document.getElementById('prop-text-section').style.display = 'none';
            document.getElementById('prop-sig-section').style.display = 'none';
            document.getElementById('prop-box-section').style.display = 'none';
            document.getElementById('prop-bg-color').value = templateState.background.color;
            
            // Hide element action footer since canvas background is selected, not an element
            const footer = document.getElementById('properties-footer');
            if (footer) footer.style.display = 'none';
        }
    });

    window.addEventListener('resize', adjustCanvasScale);
    
    // Initializer call
    document.addEventListener('DOMContentLoaded', () => {
        initCanvas();
        setTimeout(adjustCanvasScale, 100);
        setTimeout(adjustCanvasScale, 400);
    });
</script>
@endsection
