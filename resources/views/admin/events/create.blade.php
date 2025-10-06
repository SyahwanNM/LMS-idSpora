@extends('layouts.admin')

@section('title', 'Tambah Event Baru')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0 text-dark">
                            <i class="bi bi-calendar-plus me-2"></i>
                            Tambah Event Baru
                        </h4>
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" id="eventForm">
        @csrf
                        
                        <div class="row">
                            <!-- Left Column -->
                            <div class="col-lg-8">
                                <div class="mb-4">
                                    <label for="title" class="form-label fw-semibold">
                                        <i class="bi bi-tag me-1"></i>Judul Event <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="title" id="title" class="form-control form-control-lg" 
                                           required value="{{ old('title') }}" 
                                           placeholder="Masukkan judul event yang menarik">
                                </div>

                                <div class="mb-4">
                                    <label for="speaker" class="form-label fw-semibold">
                                        <i class="bi bi-person me-1"></i>Pembicara <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="speaker" id="speaker" class="form-control form-control-lg" 
                                           required value="{{ old('speaker') }}" 
                                           placeholder="Nama pembicara atau instruktur">
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="form-label fw-semibold">
                                        <i class="bi bi-file-text me-1"></i>Deskripsi Event <span class="text-danger">*</span>
                                    </label>
                                    <textarea name="description" id="description" class="form-control" rows="8">{{ old('description') }}</textarea>
                                    <div class="form-text">Gunakan editor di bawah untuk membuat deskripsi yang menarik dengan format yang kaya.</div>
                                </div>

                                <div class="mb-4">
                                    <label for="terms_and_conditions" class="form-label fw-semibold">
                                        <i class="bi bi-shield-check me-1"></i>Terms and Conditions <span class="text-muted">(Opsional)</span>
                                    </label>
                                    <textarea name="terms_and_conditions" id="terms_and_conditions" class="form-control" rows="6">{{ old('terms_and_conditions') }}</textarea>
                                    <div class="form-text">Syarat dan ketentuan yang akan ditampilkan pada halaman detail event.</div>
                                </div>

                                <div class="mb-4">
                                    <label for="location" class="form-label fw-semibold">
                                        <i class="bi bi-geo-alt me-1"></i>Lokasi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="location" id="location" class="form-control form-control-lg" 
                                           required value="{{ old('location') }}" 
                                           placeholder="Lokasi event (contoh: Jakarta, Online, dll)">
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="col-lg-4">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <i class="bi bi-calendar3 me-1"></i>Detail Event
                                        </h6>
                                    </div>
                                    <div class="card-body">
        <div class="mb-3">
                                            <label for="event_date" class="form-label fw-semibold">
                                                Tanggal Event <span class="text-danger">*</span>
                                            </label>
                                            <input type="date" name="event_date" id="event_date" class="form-control" 
                                                   required value="{{ old('event_date') }}">
        </div>

        <div class="mb-3">
                                            <label for="event_time" class="form-label fw-semibold">
                                                Waktu Event <span class="text-danger">*</span>
                                            </label>
                                            <input type="time" name="event_time" id="event_time" class="form-control" 
                                                   required value="{{ old('event_time') }}">
        </div>

        <div class="mb-3">
                                            <label for="price" class="form-label fw-semibold">
                                                Harga Tiket (Rp) <span class="text-danger">*</span>
                                            </label>
                                            <input type="number" name="price" id="price" class="form-control" 
                                                   required min="0" step="1000" value="{{ old('price', 0) }}" 
                                                   placeholder="0">
        </div>

        <div class="mb-3">
                                            <label for="discount_percentage" class="form-label fw-semibold">
                                                <i class="bi bi-percent me-1"></i>Diskon (%) <span class="text-muted">(Opsional)</span>
                                            </label>
                                            <input type="number" name="discount_percentage" id="discount_percentage" 
                                                   class="form-control" min="0" max="100" step="1" 
                                                   value="{{ old('discount_percentage', 0) }}" placeholder="0">
                                            <div class="form-text">Masukkan persentase diskon (0-100%). Contoh: 10 untuk diskon 10%</div>
        </div>

        <div class="mb-3">
                                            <label for="image" class="form-label fw-semibold">
                                                Gambar Event <span class="text-danger">*</span>
                                            </label>
                                            <input type="file" name="image" id="image" class="form-control" 
                                                   accept="image/*" required>
                                            <div class="form-text">Format: JPG, PNG, GIF. Maksimal 2MB</div>
                                        </div>

                                        <div id="imagePreview" class="mt-3" style="display: none;">
                                            <img id="previewImg" src="#" alt="Preview" 
                                                 class="img-fluid rounded shadow-sm" 
                                                 style="max-height: 200px; width: 100%; object-fit: cover;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                        <i class="bi bi-x-circle me-1"></i> Batal
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg px-4" id="submitBtn">
                                        <i class="bi bi-check-circle me-1"></i> Simpan Event
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
        </div>
        </div>
        </div>
        </div>
</div>
@endsection

@section('styles')
<style>
/* CKEditor 5 Custom Styling */
.ck-editor__editable {
    min-height: 300px;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    font-size: 14px;
    line-height: 1.6;
}

.ck-editor__main {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.ck-editor__top {
    border-radius: 8px 8px 0 0;
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-bottom: none;
}

.ck-editor__editable {
    border: 1px solid #dee2e6;
    border-top: none;
    border-radius: 0 0 8px 8px;
}

.ck-editor__editable:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

/* CKEditor 5 Toolbar Styling */
.ck-toolbar {
    background: #f8f9fa !important;
    border-bottom: 1px solid #dee2e6 !important;
}

.ck-toolbar__separator {
    background: #dee2e6 !important;
}

.ck-button {
    border-radius: 4px !important;
    transition: all 0.2s ease !important;
}

.ck-button:hover {
    background-color: #e9ecef !important;
}

.ck-button.ck-on {
    background-color: #007bff !important;
    color: white !important;
}

/* CKEditor 5 Content Styling */
.ck-editor__editable h1,
.ck-editor__editable h2,
.ck-editor__editable h3,
.ck-editor__editable h4,
.ck-editor__editable h5,
.ck-editor__editable h6 {
    margin-top: 1.5rem;
    margin-bottom: 0.5rem;
    color: #2c3e50;
    font-weight: 600;
}

.ck-editor__editable p {
    margin-bottom: 1rem;
}

.ck-editor__editable ul,
.ck-editor__editable ol {
    padding-left: 20px;
    margin: 10px 0;
}

.ck-editor__editable li {
    margin: 5px 0;
}

.ck-editor__editable blockquote {
    border-left: 4px solid #007bff;
    padding-left: 16px;
    margin: 16px 0;
    color: #6c757d;
    font-style: italic;
    background: #f8f9fa;
    padding: 10px 16px;
    border-radius: 0 4px 4px 0;
}

.ck-editor__editable table {
    border-collapse: collapse;
    margin: 10px 0;
    width: 100%;
    border: 1px solid #dee2e6;
}

.ck-editor__editable table td,
.ck-editor__editable table th {
    border: 1px solid #dee2e6;
    padding: 8px;
    text-align: left;
}

.ck-editor__editable table th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.ck-editor__editable pre {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 10px;
    margin: 10px 0;
    font-family: 'Courier New', monospace;
    overflow-x: auto;
}

.ck-editor__editable img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
    margin: 10px 0;
}

.ck-editor__editable iframe {
    max-width: 100%;
    border-radius: 4px;
    margin: 10px 0;
}

/* CKEditor 5 Dropdown Styling */
.ck-dropdown__panel {
    border-radius: 8px !important;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
}

.ck-list__item {
    padding: 8px 12px !important;
    border-radius: 4px !important;
}

.ck-list__item:hover {
    background-color: #f8f9fa !important;
}

.ck-list__item.ck-on {
    background-color: #007bff !important;
    color: white !important;
}

/* Custom form styling */
.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
</style>
@endsection

@section('scripts')
<!-- CKEditor 5 CDN - Professional Rich Text Editor -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize CKEditor 5
    // Description editor
    ClassicEditor
        .create(document.querySelector('#description'), {
            toolbar: {
                items: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', '|',
                    'bulletedList', 'numberedList', 'todoList', '|',
                    'outdent', 'indent', '|',
                    'alignment', '|',
                    'link', 'blockQuote', 'insertTable', '|',
                    'imageUpload', 'mediaEmbed', '|',
                    'code', 'codeBlock', '|',
                    'horizontalLine', '|',
                    'undo', 'redo', '|',
                    'removeFormat'
                ],
                shouldNotGroupWhenFull: true
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                    { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
                ]
            },
            fontSize: {
                options: [
                    9, 11, 13, 'default', 17, 19, 21
                ],
                supportAllValues: true
            },
            fontFamily: {
                options: [
                    'default',
                    'Arial, Helvetica, sans-serif',
                    'Courier New, Courier, monospace',
                    'Georgia, serif',
                    'Lucida Sans Unicode, Lucida Grande, sans-serif',
                    'Tahoma, Geneva, sans-serif',
                    'Times New Roman, Times, serif',
                    'Trebuchet MS, Helvetica, sans-serif',
                    'Verdana, Geneva, sans-serif'
                ],
                supportAllValues: true
            },
            fontColor: {
                colors: [
                    { color: 'hsl(0, 0%, 0%)', label: 'Black' },
                    { color: 'hsl(0, 0%, 30%)', label: 'Dim grey' },
                    { color: 'hsl(0, 0%, 60%)', label: 'Grey' },
                    { color: 'hsl(0, 0%, 90%)', label: 'Light grey' },
                    { color: 'hsl(0, 0%, 100%)', label: 'White', hasBorder: true },
                    { color: 'hsl(0, 75%, 60%)', label: 'Red' },
                    { color: 'hsl(30, 75%, 60%)', label: 'Orange' },
                    { color: 'hsl(60, 75%, 60%)', label: 'Yellow' },
                    { color: 'hsl(90, 75%, 60%)', label: 'Light green' },
                    { color: 'hsl(120, 75%, 60%)', label: 'Green' },
                    { color: 'hsl(150, 75%, 60%)', label: 'Aquamarine' },
                    { color: 'hsl(180, 75%, 60%)', label: 'Turquoise' },
                    { color: 'hsl(210, 75%, 60%)', label: 'Light blue' },
                    { color: 'hsl(240, 75%, 60%)', label: 'Blue' },
                    { color: 'hsl(270, 75%, 60%)', label: 'Purple' }
                ]
            },
            fontBackgroundColor: {
                colors: [
                    { color: 'hsl(0, 75%, 60%)', label: 'Red' },
                    { color: 'hsl(30, 75%, 60%)', label: 'Orange' },
                    { color: 'hsl(60, 75%, 60%)', label: 'Yellow' },
                    { color: 'hsl(90, 75%, 60%)', label: 'Light green' },
                    { color: 'hsl(120, 75%, 60%)', label: 'Green' },
                    { color: 'hsl(150, 75%, 60%)', label: 'Aquamarine' },
                    { color: 'hsl(180, 75%, 60%)', label: 'Turquoise' },
                    { color: 'hsl(210, 75%, 60%)', label: 'Light blue' },
                    { color: 'hsl(240, 75%, 60%)', label: 'Blue' },
                    { color: 'hsl(270, 75%, 60%)', label: 'Purple' },
                    { color: 'hsl(0, 0%, 0%)', label: 'Black' },
                    { color: 'hsl(0, 0%, 30%)', label: 'Dim grey' },
                    { color: 'hsl(0, 0%, 60%)', label: 'Grey' },
                    { color: 'hsl(0, 0%, 90%)', label: 'Light grey' },
                    { color: 'hsl(0, 0%, 100%)', label: 'White', hasBorder: true }
                ]
            },
            alignment: {
                options: ['left', 'center', 'right', 'justify']
            },
            table: {
                contentToolbar: [
                    'tableColumn', 'tableRow', 'mergeTableCells',
                    'tableProperties', 'tableCellProperties'
                ]
            },
            image: {
                toolbar: [
                    'imageTextAlternative', 'toggleImageCaption', 'imageStyle:inline',
                    'imageStyle:block', 'imageStyle:side'
                ]
            },
            link: {
                decorators: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://',
                    toggleDownloadable: {
                        mode: 'manual',
                        label: 'Downloadable',
                        attributes: {
                            download: 'file'
                        }
                    }
                }
            },
            placeholder: 'Tulis deskripsi event yang menarik dengan format yang kaya...'
        })
        .then(editor => {
            window.editorDescription = editor;
            const form = document.getElementById('eventForm');
            if (form) {
                form.addEventListener('submit', function() {
                    document.querySelector('#description').value = window.editorDescription.getData();
                });
            }
        })
        .catch(error => {
            console.error('Error initializing CKEditor:', error);
        });

    // Terms and Conditions editor
    ClassicEditor
        .create(document.querySelector('#terms_and_conditions'), {
            toolbar: { items: [
                'heading', '|', 'bold', 'italic', 'underline', '|',
                'bulletedList', 'numberedList', '|', 'link', 'blockQuote', '|',
                'undo', 'redo', '|', 'removeFormat'
            ], shouldNotGroupWhenFull: true },
            placeholder: 'Tulis syarat dan ketentuan event...'
        })
        .then(editor => {
            window.editorTerms = editor;
            const form = document.getElementById('eventForm');
            if (form) {
                form.addEventListener('submit', function() {
                    document.querySelector('#terms_and_conditions').value = window.editorTerms.getData();
                });
            }
        })
        .catch(error => {
            console.error('Error initializing CKEditor for Terms:', error);
        });

    // Image preview functionality
    document.getElementById('image').addEventListener('change', function(event) {
        const [file] = event.target.files;
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('imagePreview');
                const img = document.getElementById('previewImg');
                img.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            document.getElementById('imagePreview').style.display = 'none';
        }
    });

    // Form validation
    document.getElementById('eventForm').addEventListener('submit', function(e) {
        console.log('Form submission started');
        
        // Sync CKEditor data to textarea before validation
        if (window.editorDescription) {
            const textarea = document.querySelector('#description');
            if (textarea) {
                textarea.value = window.editorDescription.getData();
                console.log('CKEditor description synced');
            }
        }
        if (window.editorTerms) {
            const tnc = document.querySelector('#terms_and_conditions');
            if (tnc) {
                tnc.value = window.editorTerms.getData();
                console.log('CKEditor terms synced');
            }
        }
        
        // Validate required fields
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;
        
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
                field.classList.add('border-danger');
                console.log('Required field missing:', field.name);
            } else {
                field.classList.remove('border-danger');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return false;
        }
        
        // Show loading state
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i> Menyimpan...';
            submitBtn.disabled = true;
            
            console.log('Form is valid, submitting...');
            
            // Re-enable button after 10 seconds (fallback)
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 10000);
        }
    });

    // Auto-save draft functionality (optional)
    let autoSaveTimeout;
    const form = document.getElementById('eventForm');
    
    form.addEventListener('input', function() {
        clearTimeout(autoSaveTimeout);
        autoSaveTimeout = setTimeout(function() {
            // Auto-save logic can be implemented here
            console.log('Auto-saving draft...');
        }, 30000); // Auto-save every 30 seconds
    });
    });
</script>
@endsection