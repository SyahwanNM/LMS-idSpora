<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>

<body>
    @include("partials.navbar-admin-course")
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="box_luar_add_course">
                    <div class="box_link d-flex align-items-center gap-2 text-muted small mb-2">
                        <a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Course Builder</a>
                        <span>/</span>
                        <a href="{{ route('admin.add-course') }}" class="text-decoration-none">Add Course</a>
                    </div>
                    <div class="box_judul mb-3">
                        <h1 class="h3 mb-1">Tambah Course</h1>
                        <p class="text-muted mb-0">Atur detail course sebelum dipublikasikan</p>
                    </div>

                    <form class="box_form" action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="status" value="archive">
                        <h4 class="h5 mb-2">Formulir Pengaturan Course</h4>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="course-title">Judul Course <span class="text-danger">*</span></label>
                            <input id="course-title" name="name" type="text" class="form-control" placeholder="Masukkan Judul Course" required>
                            <div class="sanity-msg" data-for="course-title"></div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="course-level">Level Course <span class="text-danger">*</span></label>
                                <select id="course-level" name="level" class="form-select" required>
                                    <option value="" selected disabled>Choose your level</option>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                                <div class="sanity-msg" data-for="course-level"></div>
                            </div>
                            @if(isset($categories) && $categories->count())
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="course-category">Kategori <span class="text-danger">*</span></label>
                                <select id="course-category" name="category_id" class="form-select" required>
                                    <option value="" selected disabled>Pilih kategori</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <div class="sanity-msg" data-for="course-category"></div>
                            </div>
                            @else
                            <input type="hidden" name="category_id" value="1">
                            @endif
                        </div>

                        <div class="box_select_trainer mb-3">
                            <label class="form-label text-dark" for="course-trainer">Trainer <span class="text-danger">*</span></label>
                            <select id="course-trainer" name="trainer_id" class="form-select" required data-selected="{{ old('trainer_id') }}">
                                <option value="" selected disabled>Pilih trainer</option>
                            </select>
                            <div class="sanity-msg" data-for="course-trainer"></div>
                        </div>

                        <input type="hidden" id="course-duration" name="duration" value="0">

                        <div class="box_select_harga mb-3">
                            <label class="form-label text-dark" for="course-price">Harga <span class="text-danger">*</span></label>
                            <input id="course-price" name="price" type="text" class="form-control" inputmode="numeric" placeholder="0" required>
                            <div class="form-text">Isi 0 untuk course gratis</div>
                            <div class="sanity-msg" data-for="course-price"></div>
                        </div>

                        <!-- Akses Course (Freemium Mode) -->
                        <div class="mb-3">
                            <label for="free_access_mode" class="form-label text-dark">Akses Course</label>
                            <select name="free_access_mode" id="free_access_mode" class="form-select">
                                <option value="limit_2" {{ old('free_access_mode', 'limit_2') === 'limit_2' ? 'selected' : '' }}>Freemium (Modul 1 Terbuka)</option>
                                <option value="all" {{ old('free_access_mode') === 'all' ? 'selected' : '' }}>Buka Semua Materi</option>
                                <option value="none" {{ old('free_access_mode') === 'none' ? 'selected' : '' }}>Tutup Review (Harus Bayar Dulu)</option>
                            </select>
                            <div class="form-text text-muted small">Pilih bagaimana user dapat mengakses materi sebelum membeli (untuk course berbayar) atau status akses untuk course gratis.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark">Reseller Course</label>
                            @php
                                $isResellerCourse = (int) old('is_reseller_course', 0);
                            @endphp
                            <div class="reseller-course-radios d-flex flex-wrap align-items-center" style="column-gap: 2rem; row-gap: .5rem;" role="radiogroup" aria-label="Reseller Course">
                                <div class="reseller-course-option d-inline-flex align-items-center" style="white-space:nowrap; flex: 0 0 auto;">
                                    <input class="form-check-input m-0" type="radio" name="is_reseller_course" id="is_reseller_course_0" value="0"
                                        {{ $isResellerCourse === 0 ? 'checked' : '' }}>
                                    <label class="text-dark" for="is_reseller_course_0">Tidak</label>
                                </div>
                                <div class="reseller-course-option d-inline-flex align-items-center" style="white-space:nowrap; flex: 0 0 auto;">
                                    <input class="form-check-input m-0" type="radio" name="is_reseller_course" id="is_reseller_course_1" value="1"
                                        {{ $isResellerCourse === 1 ? 'checked' : '' }}>
                                    <label class="text-dark" for="is_reseller_course_1">Ya</label>
                                </div>
                            </div>
                            <div class="form-text">Jika Ya, course ini ditandai sebagai course reseller.</div>
                            <style>
                                .reseller-course-radios input[type="radio"]{
                                    appearance: auto !important;
                                    -webkit-appearance: radio !important;
                                    -moz-appearance: auto !important;
                                    vertical-align: middle !important;
                                }
                                .reseller-course-radios label{
                                    display: inline-flex !important;
                                    align-items: center !important;
                                    margin: 0 0 0 .5rem !important;
                                    cursor: pointer;
                                    user-select: none;
                                }
                                .reseller-course-radios .reseller-course-option:first-child label{
                                    margin-left: .05rem !important;
                                }
                            </style>
                        </div>

                        <div class="box_select_deskripsi mb-3">
                            <label class="form-label text-dark" for="course-description">Deskripsi Course</label>
                            <textarea id="course-description" name="description" class="form-control" placeholder="Deskripsikan course secara lengkap"></textarea>
                            <div class="sanity-msg" data-for="course-description"></div>
                        </div>

                        <div class="box_select_deskripsi mb-1">
                            <label class="form-label text-dark" for="course-thumbnail">Thumbnail/Intro Media <span class="text-danger">*</span></label>
                            <input id="course-thumbnail" name="image" type="file" class="form-control" accept="image/*,video/mp4,video/webm,video/ogg" required>
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <div id="course-thumbnail-preview" class="border rounded bg-light overflow-hidden d-flex align-items-center justify-content-center" style="width:72px;height:72px;">
                                    <small class="text-muted">Preview</small>
                                </div>
                            </div>
                            <div class="form-text">Bisa upload gambar <b>atau</b> video (mp4, webm, ogg)</div>
                            <div class="sanity-msg" data-for="course-thumbnail"></div>
                        </div>
                        <div class="box_select_deskripsi mb-3">
                            <label class="form-label text-dark" for="card-thumbnail">Thumbnail Card Course <span class="text-danger">*</span></label>
                            <input id="card-thumbnail" name="card_thumbnail" type="file" class="form-control" accept="image/*" required>
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <div id="card-thumbnail-preview" class="border rounded bg-light overflow-hidden d-flex align-items-center justify-content-center" style="width:72px;height:72px;">
                                    <small class="text-muted">Preview</small>
                                </div>
                            </div>
                            <div class="form-text">Upload gambar untuk thumbnail card course (jpg/png/webp)</div>
                            <div class="sanity-msg" data-for="card-thumbnail"></div>
                        </div>
                        <div class="box_select_diskon mb-3">
                            <label class="form-label text-dark" for="discount-percent">Diskon (%)</label>
                            <input id="discount-percent" name="discount_percent" type="number" class="form-control" min="0" max="100" placeholder="Masukkan diskon (0-100)">
                            <div class="form-text">Boleh kosong atau 0%</div>
                            <div class="sanity-msg" data-for="discount-percent"></div>
                        </div>
                        <div class="box_select_tanggal_diskon row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="discount-start">Tanggal Mulai Diskon</label>
                                <input id="discount-start" name="discount_start" type="date" class="form-control" min="{{ now()->toDateString() }}" value="{{ old('discount_start', now()->toDateString()) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="discount-end">Tanggal Berakhir Diskon</label>
                                <input id="discount-end" name="discount_end" type="date" class="form-control" min="{{ now()->toDateString() }}" value="{{ old('discount_end', now()->toDateString()) }}">
                            </div>
                        </div>
                        <div class="box_select_diskon mb-3">
                            <label class="form-label text-dark" for="discount-percent">Pengeluaran</label>
                            <div class="table-responsive">
                            <table class="table" id="courseExpensesTable">
                                <thead>
                                    <tr>
                                        <th scope="col">No</th>
                                        <th scope="col">Nama kebutuhan</th>
                                        <th scope="col">kuantitas</th>
                                        <th scope="col">Harga Satuan</th>
                                        <th scope="col">Harga Total</th>
                                        <th scope="col">Aksi</th>

                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            </div>
                            <button type="button" class="tombol_tambah_pengeluaran" id="addCourseExpenseRow">Tambah Pengeluaran</button>
                            <div class="sanity-msg" data-for="discount-percent"></div>
                        </div>


                        <!-- Serialized modules payload for backend (JSON) -->
                        <input type="hidden" name="modules_payload" id="modules-payload">
                        <!-- Hidden bucket to hold actual selected module files so they submit with the form -->
                        <div id="module-file-bucket" style="display:none"></div>
                </div>
                <div class="box_button d-flex justify-content-end gap-2 mt-3">
                    <a href="{{ route('admin.courses.index') }}" class="cancel btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="save_add btn btn-primary">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function() {
            const modules = [];
            const listEl = document.getElementById('modules-list');
            const payloadEl = document.getElementById('modules-payload');
            const fileBucket = document.getElementById('module-file-bucket');
            // Video modal elements
            const openVideoBtn = document.getElementById('open-add-video-modal');
            const videoModalEl = document.getElementById('addVideoModal');
            const videoModalTitle = document.getElementById('video-modal-title');
            const videoModalDesc = document.getElementById('video-modal-desc');
            const videoModalOrder = document.getElementById('video-modal-order');
            let videoModalFile = document.getElementById('video-modal-file');
            const videoDropZone = document.getElementById('video-drop-zone');
            const videoFileName = document.getElementById('video-file-name');
            const videoAddBtn = document.getElementById('video-modal-add-btn');
            // Quiz modal elements
            const openQuizBtn = document.getElementById('open-add-quiz-modal');
            const quizModalEl = document.getElementById('addQuizModal');
            const quizModalTitle = document.getElementById('quiz-modal-title');
            const quizModalDesc = document.getElementById('quiz-modal-desc');
            const quizModalOrder = document.getElementById('quiz-modal-order');
            let quizModalFile = document.getElementById('quiz-modal-file');
            const quizDropZone = document.getElementById('quiz-drop-zone');
            const quizFileName = document.getElementById('quiz-file-name');
            const quizAddBtn = document.getElementById('quiz-modal-add-btn');
            // PDF modal elements
            const openPdfBtn = document.getElementById('open-add-pdf-modal');
            const pdfModalEl = document.getElementById('addPdfModal');
            const pdfModalTitle = document.getElementById('pdf-modal-title');
            const pdfModalDesc = document.getElementById('pdf-modal-desc');
            const pdfModalOrder = document.getElementById('pdf-modal-order');
            let pdfModalFile = document.getElementById('pdf-modal-file');
            const pdfDropZone = document.getElementById('pdf-drop-zone');
            const pdfFileName = document.getElementById('pdf-file-name');
            const pdfAddBtn = document.getElementById('pdf-modal-add-btn');
            let quizModalInstance = null;

            if (window.bootstrap && bootstrap.Modal && quizModalEl) {
                quizModalInstance = new bootstrap.Modal(quizModalEl);
            }

            if (openQuizBtn) {
                openQuizBtn.addEventListener('click', () => {
                    if (quizModalInstance) quizModalInstance.show();
                });
            }

            let selectedVideoFile = null;
            let selectedPdfFile = null;
            let videoModalInstance = null;
            let pdfModalInstance = null;
            if (window.bootstrap && bootstrap.Modal) {
                if (videoModalEl) {
                    videoModalInstance = new bootstrap.Modal(videoModalEl);
                }
                if (pdfModalEl) {
                    pdfModalInstance = new bootstrap.Modal(pdfModalEl);
                }
            }

            function updatePayload() {
                try {
                    payloadEl.value = JSON.stringify(modules);
                } catch (_) {
                    payloadEl.value = '[]';
                }
            }

            function makeModuleCard(mod) {
                const idx = modules.indexOf(mod);
                const wrapper = document.createElement('div');
                wrapper.className = 'border rounded p-2 d-flex align-items-start gap-2';
                wrapper.innerHTML = `
                <div class="bg-light rounded p-2 d-flex align-items-center justify-content-center" style="width:44px;height:44px;">
                    <i class="bi ${mod.type==='pdf' ? 'bi-file-earmark-pdf' : (mod.type==='quiz'?'bi-patch-question':'bi-file-earmark-play')}" style="font-size:1.25rem;color:${mod.type==='pdf'?'#F40F02':(mod.type==='quiz'?'#4B2DBF':'#0d6efd')};"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <strong class="me-2">${mod.title}</strong>
                        <span class="badge bg-light text-secondary border">#${(mod.order ?? 1)}</span>
                    </div>
                    <div class="text-muted small">${mod.subtitle || ''}</div>
                    <div class="text-muted small">${mod.type === 'quiz' ? 'Quiz Interactive' : (mod.filename || 'File not found (reload)')}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger border-0" aria-label="Remove"><i class="bi bi-trash"></i></button>
                </div>
            `;
                const removeBtn = wrapper.querySelector('button');
                removeBtn.addEventListener('click', () => {
                    const i = modules.indexOf(mod);
                    if (i >= 0) {
                        modules.splice(i, 1);
                        renderList();
                    }
                });
                return wrapper;
            }

            // LocalStorage Key
            const STORAGE_KEY = 'course_draft_modules';

            function saveDraft() {
                localStorage.setItem(STORAGE_KEY, JSON.stringify(modules));
                updatePayload();
            }

            function loadDraft() {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved) {
                    try {
                        const parsed = JSON.parse(saved);
                        // Ensure structure is arrays
                        if (Array.isArray(parsed)) {
                            modules.splice(0, modules.length, ...parsed);
                            renderList();
                        }
                    } catch (e) {
                        console.error('Failed to load draft', e);
                    }
                }
            }

            function renderList() {
                listEl.innerHTML = '';

                const emptyState = document.getElementById('modules-empty-state');
                if (modules.length === 0) {
                    if (emptyState) emptyState.style.display = 'block';
                    listEl.style.display = 'none';
                    saveDraft();
                    return;
                }

                if (emptyState) emptyState.style.display = 'none';
                listEl.style.display = 'flex';

                // Grouping
                const pdfs = modules.filter(m => m.type === 'pdf').sort((a, b) => (a.order || 0) - (b.order || 0));
                const videos = modules.filter(m => m.type === 'video').sort((a, b) => (a.order || 0) - (b.order || 0));
                const quizzes = modules.filter(m => m.type === 'quiz').sort((a, b) => (a.order || 0) - (b.order || 0));

                const createSection = (title, items) => {
                    const section = document.createElement('div');
                    section.className = 'mb-4';
                    section.innerHTML = `<h6 class="fw-bold mb-3">${title}</h6>`;
                    const container = document.createElement('div');
                    container.className = 'd-flex flex-column gap-3';
                    items.forEach(m => container.appendChild(makeModuleCard(m)));
                    section.appendChild(container);
                    return section;
                };

                if (pdfs.length > 0) listEl.appendChild(createSection('PDF Document', pdfs));
                if (videos.length > 0) listEl.appendChild(createSection('Video Lesson', videos));
                if (quizzes.length > 0) listEl.appendChild(createSection('Quizzes', quizzes));

                saveDraft();
            }

            // Initial Load
            loadDraft();

            
            const mainForm = document.querySelector('form[action]');
            
            if (mainForm) {
                mainForm.addEventListener('submit', () => {
                
                });
            }

            function addModuleFromFile(file, type) {
                if (!file) return;
                const titleBase = file.name.replace(/\.[^.]+$/, '') || (type === 'pdf' ? 'PDF Module' : 'Video Module');
                const nextOrder = Math.max(0, ...modules.map(m => m.order || 0)) + 1;
                const mod = {
                    type, // 'pdf' | 'video'
                    title: titleBase,
                    subtitle: type === 'pdf' ? 'Dokumen materi' : 'Video pembelajaran',
                    filename: file.name,
                    mime: file.type || '',
                    order: nextOrder,
                };
                modules.push(mod);
                renderList();
            }

            // --- Add PDF Modal logic ---
            function resetPdfModal() {
                selectedPdfFile = null;
                pdfModalTitle.value = '';
                pdfModalDesc.value = '';
                pdfModalOrder.value = Math.max(0, ...modules.map(m => m.order || 0)) + 1;
                pdfModalFile.value = '';
                pdfFileName.textContent = '';
            }

            function setSelectedPdf(file) {
                if (!file) return;
                selectedPdfFile = file;
                pdfFileName.textContent = file.name;
                if (!pdfModalTitle.value) {
                    pdfModalTitle.value = file.name.replace(/\.[^.]+$/, '');
                }
            }

            function bindPdfFileInput(el) {
                el.addEventListener('change', (e) => {
                    const f = e.target.files && e.target.files[0];
                    setSelectedPdf(f);
                });
            }

            if (openPdfBtn) {
                openPdfBtn.addEventListener('click', () => {
                    resetPdfModal();
                    if (pdfModalInstance) pdfModalInstance.show();
                    else pdfModalEl.style.display = 'block';
                });
            }
            if (pdfDropZone) {
                ['dragenter', 'dragover'].forEach(evt => pdfDropZone.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    pdfDropZone.classList.add('hover');
                }));
                ['dragleave', 'drop'].forEach(evt => pdfDropZone.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    pdfDropZone.classList.remove('hover');
                }));
                pdfDropZone.addEventListener('click', () => pdfModalFile.click());
                pdfDropZone.addEventListener('drop', (e) => {
                    const f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
                    if (f) setSelectedPdf(f);
                });
            }
            if (pdfModalFile) {
                bindPdfFileInput(pdfModalFile);
            }
            if (pdfAddBtn) {
                pdfAddBtn.addEventListener('click', () => {
                    const title = (pdfModalTitle.value || '').trim();
                    const desc = (pdfModalDesc.value || '').trim();
                    const order = parseInt(pdfModalOrder.value || '1', 10) || 1;
                    if (title.length < 3) {
                        alert('Judul minimal 3 karakter.');
                        return;
                    }
                    if (!selectedPdfFile) {
                        alert('Silakan pilih file PDF.');
                        return;
                    }
                    const uid = 'm' + Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
                    // move the actual input with the selected file into the hidden form bucket so it submits
                    if (pdfModalFile && fileBucket) {
                        pdfModalFile.name = `module_files[${uid}]`;
                        fileBucket.appendChild(pdfModalFile);
                        // create a fresh input back into modal for next time
                        const newInput = document.createElement('input');
                        newInput.type = 'file';
                        newInput.id = 'pdf-modal-file';
                        newInput.accept = 'application/pdf';
                        newInput.style.display = 'none';
                        pdfModalFile = newInput;
                        // insert it after drop-zone
                        const parent = document.getElementById('addPdfModal').querySelector('.modal-body .mb-3:last-of-type');
                        // safer: place after drop zone container
                        const afterEl = document.getElementById('pdf-drop-zone');
                        if (afterEl && afterEl.parentNode) {
                            afterEl.parentNode.insertBefore(newInput, afterEl.nextSibling);
                        }
                        bindPdfFileInput(pdfModalFile);
                    }
                    modules.push({
                        type: 'pdf',
                        title: title,
                        subtitle: desc || 'Dokumen materi',
                        filename: selectedPdfFile.name,
                        mime: selectedPdfFile.type || 'application/pdf',
                        order: order,
                        uid: uid,
                    });
                    renderList();
                    if (pdfModalInstance) pdfModalInstance.hide();
                    resetPdfModal();
                });
            }

            // --- Add Video Modal logic ---
            function resetVideoModal() {
                selectedVideoFile = null;
                videoModalTitle.value = '';
                videoModalDesc.value = '';
                videoModalOrder.value = Math.max(0, ...modules.map(m => m.order || 0)) + 1;
                videoModalFile.value = '';
                videoFileName.textContent = '';
            }

            function setSelectedVideo(file) {
                if (!file) return;
                selectedVideoFile = file;
                videoFileName.textContent = file.name;
                if (!videoModalTitle.value) {
                    videoModalTitle.value = file.name.replace(/\.[^.]+$/, '');
                }
            }

            function bindVideoFileInput(el) {
                el.addEventListener('change', (e) => {
                    const f = e.target.files && e.target.files[0];
                    setSelectedVideo(f);
                });
            }

            if (openVideoBtn) {
                openVideoBtn.addEventListener('click', () => {
                    resetVideoModal();
                    if (videoModalInstance) videoModalInstance.show();
                    else videoModalEl.style.display = 'block';
                });
            }
            // drag & drop handlers
            if (videoDropZone) {
                ['dragenter', 'dragover'].forEach(evt => videoDropZone.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    videoDropZone.classList.add('hover');
                }));
                ['dragleave', 'drop'].forEach(evt => videoDropZone.addEventListener(evt, e => {
                    e.preventDefault();
                    e.stopPropagation();
                    videoDropZone.classList.remove('hover');
                }));
                videoDropZone.addEventListener('click', () => videoModalFile.click());
                videoDropZone.addEventListener('drop', (e) => {
                    const f = e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files[0];
                    if (f) setSelectedVideo(f);
                });
            }
            if (videoModalFile) {
                bindVideoFileInput(videoModalFile);
            }
            if (videoAddBtn) {
                videoAddBtn.addEventListener('click', () => {
                    const title = (videoModalTitle.value || '').trim();
                    const desc = (videoModalDesc.value || '').trim();
                    const order = parseInt(videoModalOrder.value || '1', 10) || 1;
                    if (title.length < 3) {
                        alert('Judul minimal 3 karakter.');
                        return;
                    }
                    if (!selectedVideoFile) {
                        alert('Silakan pilih file video.');
                        return;
                    }
                    const uid = 'm' + Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
                    // move the actual input with selected file into the bucket
                    if (videoModalFile && fileBucket) {
                        videoModalFile.name = `module_files[${uid}]`;
                        fileBucket.appendChild(videoModalFile);
                        // create a fresh input back into modal for next time
                        const newInput = document.createElement('input');
                        newInput.type = 'file';
                        newInput.id = 'video-modal-file';
                        newInput.accept = 'video/mp4,video/webm,video/ogg';
                        newInput.style.display = 'none';
                        videoModalFile = newInput;
                        const afterEl = document.getElementById('video-drop-zone');
                        if (afterEl && afterEl.parentNode) {
                            afterEl.parentNode.insertBefore(newInput, afterEl.nextSibling);
                        }
                        bindVideoFileInput(videoModalFile);
                    }
                    modules.push({
                        type: 'video',
                        title: title,
                        subtitle: desc || 'Video pembelajaran',
                        filename: selectedVideoFile.name,
                        mime: selectedVideoFile.type || '',
                        order: order,
                        uid: uid,
                    });
                    renderList();
                    if (videoModalInstance) videoModalInstance.hide();
                    resetVideoModal();
                });
            }

            // --- Quiz Logic (Multi-step) ---
            let quizDraft = {
                title: '',
                description: '',
                questions: []
            };

            // Elements
            const quizStep1 = document.getElementById('quiz-step-1');
            const quizStep2 = document.getElementById('quiz-step-2');
            const quizStep3 = document.getElementById('quiz-step-3');

            const quizFooter1 = document.getElementById('quiz-footer-step-1');
            const quizFooter2 = document.getElementById('quiz-footer-step-2');
            const quizFooter3 = document.getElementById('quiz-footer-step-3');

            const quizTitleInput = document.getElementById('quiz-title-input');
            const quizDescInput = document.getElementById('quiz-desc-input');
            const quizQuestionsList = document.getElementById('quiz-questions-list');
            const quizQuestionCount = document.getElementById('quiz-question-count');

            // Buttons
            const btnGoToAddQuestion = document.getElementById('btn-goto-add-question');
            const btnGotoReview = document.getElementById('btn-goto-review');
            const btnCancelQuestion = document.getElementById('btn-cancel-question');
            const btnSaveQuestion = document.getElementById('btn-save-question');
            const btnBackToStep1 = document.getElementById('btn-back-to-step-1');
            const btnFinalSaveQuiz = document.getElementById('btn-final-save-quiz');

            // Step 2 Inputs
            const qTextInput = document.getElementById('q-text-input');
            const qOptionsContainer = document.getElementById('q-options-container');
            const quizQuestionNumberTitle = document.getElementById('quiz-question-number-title');

            // Review Elements
            const reviewQuizTitle = document.getElementById('review-quiz-title');
            const reviewQuizDesc = document.getElementById('review-quiz-desc');
            const reviewTotalQ = document.getElementById('review-total-q');
            const reviewQuestionsList = document.getElementById('review-questions-list');

            function switchStep(step) {
                [quizStep1, quizStep2, quizStep3].forEach(el => el.style.display = 'none');
                [quizFooter1, quizFooter2, quizFooter3].forEach(el => el.style.setProperty('display', 'none', 'important'));

                if (step === 1) {
                    quizStep1.style.display = 'block';
                    quizFooter1.style.display = 'flex';
                    renderQuestionsMinimal();
                } else if (step === 2) {
                    quizStep2.style.display = 'block';
                    quizFooter2.style.display = 'flex';
                    // Update Quiz #X title
                    const nextNum = quizDraft.questions.length + 1;
                    if (quizQuestionNumberTitle) quizQuestionNumberTitle.textContent = `Quiz #${nextNum}`;

                    resetQuestionForm();
                } else if (step === 3) {
                    quizStep3.style.display = 'block';
                    quizFooter3.style.display = 'flex';
                    renderReview();
                }
            }

            function resetQuizDraft() {
                quizDraft = {
                    title: '',
                    description: '',
                    questions: []
                };
                quizTitleInput.value = '';
                quizDescInput.value = '';
                switchStep(1);
            }

            function renderQuestionsMinimal() {
                if (quizDraft.questions.length === 0) {
                    quizQuestionsList.innerHTML = '<div class="alert alert-light border text-center text-muted small py-3">No questions added yet.</div>';
                } else {
                    quizQuestionsList.innerHTML = quizDraft.questions.map((q, idx) => `
                            <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-success rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:24px;height:24px;">
                                        <i class="bi bi-check-lg" style="font-size:0.75rem;"></i>
                                    </span>
                                    <span class="fw-semibold text-dark">Q${idx + 1}. ${q.text.substring(0, 30)}${q.text.length > 30 ? '...' : ''}</span>
                                </div>
                                <button type="button" class="btn btn-sm text-danger" onclick="removeDraftQuestion(${idx})"><i class="bi bi-trash"></i></button>
                            </div>
                        `).join('');
                }
                quizQuestionCount.textContent = quizDraft.questions.length;

                // Add listener for remove button (since inline onclick won't verify easily with scoping)
                // We will use delegation
            }

            quizQuestionsList.addEventListener('click', (e) => {
                const btn = e.target.closest('.btn.text-danger');
                if (btn) {
                    // find index
                    // Simple way: re-render with onclick or data-index
                    // Re-render approach with valid delegation:
                    // Let's assume we render data-index
                }
            });

            // Global function for remove (attached to window for simplicity in this scope or use delegation)
            window.removeDraftQuestion = function(idx) {
                quizDraft.questions.splice(idx, 1);
                renderQuestionsMinimal();
            };

            function resetQuestionForm() {
                qTextInput.value = '';
                qOptionsContainer.innerHTML = [1, 2, 3, 4].map(i => `
                        <div class="input-group">
                            <input type="text" class="form-control question-option-input" placeholder="Option ${i}" data-index="${i}">
                            <div class="input-group-text bg-white">
                                <input class="form-check-input mt-0 question-correct-radio" type="radio" name="correctOption" value="${i}" aria-label="Correct answer">
                                <span class="ms-2 small text-muted">Correct</span>
                            </div>
                        </div>
                    `).join('');
            }

            // Event Listeners
            if (openQuizBtn) {
                // Override default
                openQuizBtn.replaceWith(openQuizBtn.cloneNode(true)); // remove old listeners
                document.getElementById('open-add-quiz-modal').addEventListener('click', () => {
                    resetQuizDraft();
                    if (quizModalInstance) quizModalInstance.show();
                });
            }

            btnGoToAddQuestion.addEventListener('click', () => switchStep(2));
            btnCancelQuestion.addEventListener('click', () => switchStep(1));

            btnSaveQuestion.addEventListener('click', () => {
                const text = qTextInput.value.trim();
                if (!text) {
                    alert('Please enter question text');
                    return;
                }

                const options = [];
                let correctIdx = -1;

                const optInputs = qOptionsContainer.querySelectorAll('.question-option-input');
                const radios = qOptionsContainer.querySelectorAll('.question-correct-radio');

                let allFilled = true;
                optInputs.forEach((inp, idx) => {
                    const val = inp.value.trim();
                    if (!val) allFilled = false;
                    options.push(val);
                    if (radios[idx].checked) correctIdx = idx;
                });

                if (!allFilled) {
                    alert('Please fill all 4 options');
                    return;
                }
                if (correctIdx === -1) {
                    alert('Please select the correct answer');
                    return;
                }

                quizDraft.questions.push({
                    text: text,
                    options: options,
                    correctIndex: correctIdx
                });

                switchStep(1);
            });

            btnGotoReview.addEventListener('click', () => {
                // Update Draft info
                quizDraft.title = quizTitleInput.value.trim();
                quizDraft.description = quizDescInput.value.trim();

                if (!quizDraft.title) {
                    alert('Please enter Quiz Title');
                    return;
                }
                if (quizDraft.questions.length === 0) {
                    alert('Please add at least one question');
                    return;
                }

                switchStep(3);
            });

            btnBackToStep1.addEventListener('click', () => switchStep(1));

            function renderReview() {
                reviewQuizTitle.textContent = quizDraft.title;
                reviewQuizDesc.textContent = quizDraft.description || '-';
                reviewTotalQ.textContent = quizDraft.questions.length;

                reviewQuestionsList.innerHTML = quizDraft.questions.map((q, i) => `
                        <div class="p-3 border-bottom bg-white">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="badge bg-light text-dark border">Q${i+1}</span>
                                <span class="fw-semibold">${q.text}</span>
                            </div>
                            <div class="ps-4">
                                ${q.options.map((opt, optI) => `
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <div style="width:16px;height:16px;border:1px solid #ccc;border-radius:4px;background-color:${optI === q.correctIndex ? '#d1e7dd' : '#fff'};border-color:${optI === q.correctIndex ? '#198754' : '#ccc'};"></div>
                                        <span class="small ${optI === q.correctIndex ? 'text-success fw-bold' : 'text-muted'}">${opt}</span>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    `).join('');
            }

            btnFinalSaveQuiz.addEventListener('click', () => {
                const nextOrder = Math.max(0, ...modules.map(m => m.order || 0)) + 1;
                const uid = 'q' + Date.now().toString(36);

                modules.push({
                    type: 'quiz',
                    title: quizDraft.title,
                    subtitle: `${quizDraft.questions.length} questions`,
                    filename: 'Quiz Module', // Placeholder
                    mime: 'application/json', // Virtual
                    order: nextOrder,
                    uid: uid,
                    data: JSON.parse(JSON.stringify(quizDraft)) // Deep copy
                });

                renderList();
                if (quizModalInstance) quizModalInstance.hide();
            });
        })();
    </script>
    <script>
        (function() {
            const fields = {
                title: document.getElementById('course-title'),
                level: document.getElementById('course-level'),
                category: document.getElementById('course-category'),
                trainer: document.getElementById('course-trainer'),
                price: document.getElementById('course-price'),
                description: document.getElementById('course-description'),
                thumbnail: document.getElementById('course-thumbnail'),
                cardThumbnail: document.getElementById('card-thumbnail'),
                duration: document.getElementById('course-duration'),
            };

            function onlyDigits(s) {
                return (s || '').toString().replace(/[^0-9]/g, '');
            }

            function formatThousandsID(digits) {
                let d = onlyDigits(digits);
                // keep at least one digit if user typed zeros
                d = d.replace(/^0+(?=\d)/, '');
                if (d.length === 0) return '';
                // group by thousands with '.'
                return d.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }

            function countDigitsBeforeCaret(value, caretPos) {
                if (caretPos == null) return 0;
                const before = (value || '').slice(0, Math.max(0, caretPos));
                const m = before.match(/\d/g);
                return m ? m.length : 0;
            }

            function caretPosForDigitIndex(formattedValue, digitIndex) {
                if (digitIndex <= 0) return 0;
                let seen = 0;
                for (let i = 0; i < formattedValue.length; i++) {
                    if (/[0-9]/.test(formattedValue[i])) {
                        seen++;
                        if (seen >= digitIndex) return i + 1;
                    }
                }
                return formattedValue.length;
            }

            function formatPriceFieldLive(inputEl) {
                if (!inputEl) return;
                const raw = inputEl.value || '';
                const caret = inputEl.selectionStart;
                const digitIndex = countDigitsBeforeCaret(raw, caret);

                const digits = onlyDigits(raw);
                const formatted = formatThousandsID(digits);
                if (formatted === raw) return;

                inputEl.value = formatted;
                // Restore caret so typing feels natural
                const newCaret = caretPosForDigitIndex(formatted, digitIndex);
                try {
                    inputEl.setSelectionRange(newCaret, newCaret);
                } catch (e) {
                    // ignore (some input types / browsers)
                }
            }

            function msgEl(id) {
                return document.querySelector('.sanity-msg[data-for="' + id + '"]');
            }

            function setIndicator(id, ok, msg) {
                const input = document.getElementById(id);
                const m = msgEl(id);

                if (input) {
                    input.classList.toggle('is-invalid', !ok);
                }
                if (m) {
                    if (msg && !ok) {
                        m.textContent = msg;
                        m.classList.add('show');
                    } else {
                        m.textContent = '';
                        m.classList.remove('show');
                    }
                }
            }

            function validateTitle() {
                const v = (fields.title.value || '').trim();
                const ok = v.length > 0;
                setIndicator('course-title', ok, 'Judul course wajib diisi.');
                return ok;
            }

            function validateStatus() {
                // Status is enforced as 'archive' for new courses; field may not exist in markup.
                if (!fields.status) return true;
                const v = fields.status.value;
                const ok = (v === 'active' || v === 'archive');
                setIndicator('course-status', ok, 'Status wajib dipilih.');
                return ok;
            }

            function validateLevel() {
                const v = fields.level.value;
                const ok = (v === 'beginner' || v === 'intermediate' || v === 'advanced');
                setIndicator('course-level', ok, 'Level course wajib dipilih.');
                return ok;
            }

            function validateCategory() {
                if (!fields.category) return true;
                const v = (fields.category.value || '').trim();
                const ok = v.length > 0;
                setIndicator('course-category', ok, 'Kategori wajib dipilih.');
                return ok;
            }

            function validateTrainer() {
                if (!fields.trainer) return true;
                const v = (fields.trainer.value || '').trim();
                const ok = v.length > 0;
                setIndicator('course-trainer', ok, 'Trainer wajib dipilih.');
                return ok;
            }

            function validatePrice() {
                const raw = (fields.price.value || '').trim();
                const digits = raw.replace(/[^0-9]/g, '');
                if (digits.length === 0) {
                    setIndicator('course-price', false, 'Harga wajib diisi.');
                    return false;
                }
                const val = parseInt(digits, 10);
                const ok = !isNaN(val) && val >= 0;
                setIndicator('course-price', ok, 'Harga harus angka >= 0.');
                return ok;
            }

            function validateThumbnail() {
                const f = fields.thumbnail.files && fields.thumbnail.files[0];
                if (!f) {
                    setIndicator('course-thumbnail', false, 'Intro media wajib dipilih.');
                    return false;
                }
                const allowed = [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg',
                    'video/mp4', 'video/webm', 'video/ogg'
                ];
                if (!allowed.includes(f.type)) {
                    setIndicator('course-thumbnail', false, 'File harus gambar (jpg/png/webp/gif) atau video (mp4/webm/ogg).');
                    return false;
                }
                setIndicator('course-thumbnail', true);
                return true;
            }

            function validateCardThumbnail() {
                const f = fields.cardThumbnail?.files && fields.cardThumbnail.files[0];
                if (!fields.cardThumbnail) return true;
                if (!f) {
                    setIndicator('card-thumbnail', false, 'Thumbnail card course wajib diupload.');
                    return false;
                }
                const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
                if (!allowed.includes(f.type)) {
                    setIndicator('card-thumbnail', false, 'Thumbnail harus gambar (jpg/png/webp/gif).');
                    return false;
                }
                setIndicator('card-thumbnail', true);
                return true;
            }

            function validateDuration() {
                // Some pages don't have #course-duration (legacy markup uses different fields).
                // Treat it as optional to avoid runtime errors that block other scripts.
                if (!fields.duration) return true;
                const raw = (fields.duration.value || '').trim();
                const val = parseInt(raw || '0', 10);
                // Backend allows duration >= 0; duration is a hidden field on this page.
                const ok = !isNaN(val) && val >= 0;
                setIndicator('course-duration', ok, 'Durasi tidak valid.');
                return ok;
            }

            function validateAll() {
                const checks = [validateTitle(), validateStatus(), validateLevel(), validateCategory(), validateTrainer(), validatePrice(), validateThumbnail(), validateCardThumbnail(), validateDuration()];
                return checks.every(Boolean);
            }

            // live validation (guard for optional fields)
            fields.title?.addEventListener('input', validateTitle);
            fields.level?.addEventListener('change', validateLevel);
            fields.category?.addEventListener('change', validateCategory);
            fields.trainer?.addEventListener('change', validateTrainer);
            fields.price?.addEventListener('input', function() {
                formatPriceFieldLive(fields.price);
                validatePrice();
            });
            fields.price?.addEventListener('blur', function() {
                // ensure final formatting on blur
                formatPriceFieldLive(fields.price);
                validatePrice();
            });
            fields.thumbnail?.addEventListener('change', validateThumbnail);
            fields.cardThumbnail?.addEventListener('change', validateCardThumbnail);
            fields.duration?.addEventListener('input', validateDuration);

            // initial state
            validateAll();

            // apply formatting if field has an initial value (e.g. browser autofill)
            if (fields.price && (fields.price.value || '').trim() !== '') {
                formatPriceFieldLive(fields.price);
            }

            const formEl = document.querySelector('form.box_form');
            if (formEl) {
                formEl.addEventListener('submit', function(ev) {
                    if (!validateAll()) {
                        ev.preventDefault();
                        const firstInvalid = formEl.querySelector('.is-invalid');
                        if (firstInvalid) {
                            const parent = firstInvalid.closest('.mb-3, .col-md-6, .mb-1') || document.body;
                            parent.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstInvalid.focus?.();
                        }
                        return;
                    }
                    // enforce archive on create
                    const statusInputs = formEl.querySelectorAll('input[name="status"], select[name="status"]');
                    statusInputs.forEach((el) => {
                        if (el) el.value = 'archive';
                    });
                    // normalize price to digits
                    const priceInput = document.getElementById('course-price');
                    if (priceInput) {
                        const digits = (priceInput.value || '').replace(/[^0-9]/g, '');
                        priceInput.value = digits;
                    }

                        // Ensure expense totals are recalculated before submit
                        if (typeof window.__recalcAllCourseExpenseRows === 'function') {
                            window.__recalcAllCourseExpenseRows();
                        }
                    // ensure level value is valid
                    const levelSel = document.getElementById('course-level');
                    if (levelSel && levelSel.value === 'advance') {
                        levelSel.value = 'advanced';
                    }
                });
            }
        })();

        // Diskon date guard: disallow past dates & ensure end >= start
        (function() {
            const percent = document.getElementById('discount-percent');
            const price = document.getElementById('course-price');
            const start = document.getElementById('discount-start');
            const end = document.getElementById('discount-end');
            if (!start || !end) return;

            const todayStr = '{{ now()->toDateString() }}';

            function clampDateInput(el, minStr) {
                if (!el) return;
                if (!el.value || String(el.value).trim() === '') return;
                if (el.value < minStr) el.value = minStr;
            }

            function syncDiscountDates() {
                const startVal = (start.value || '').trim();

                // Ensure start >= today
                if (startVal && startVal < todayStr) {
                    start.value = todayStr;
                }

                const effectiveStart = (start.value || '').trim();
                const minEnd = (effectiveStart && effectiveStart > todayStr) ? effectiveStart : todayStr;
                end.min = minEnd;

                // Ensure end >= minEnd
                clampDateInput(end, minEnd);
            }

            function parsePercent() {
                const raw = (percent?.value ?? '').toString().trim();
                if (raw === '') return 0;
                const val = parseInt(raw, 10);
                return Number.isFinite(val) ? val : 0;
            }

            function parsePrice() {
                const raw = (price?.value ?? '').toString().trim();
                if (raw === '') return null;
                const digits = raw.replace(/[^0-9]/g, '');
                if (digits === '') return null;
                const val = parseInt(digits, 10);
                return Number.isFinite(val) ? val : null;
            }

            function syncDiscountEnabledState() {
                const priceVal = parsePrice();
                const allowDiscount = (priceVal !== null && priceVal > 0);

                if (percent) {
                    percent.disabled = !allowDiscount;
                    if (!allowDiscount) {
                        percent.value = '';
                    }
                }

                const p = parsePercent();
                const enabled = p > 0;

                start.disabled = !enabled;
                end.disabled = !enabled;

                if (!enabled) {
                    // If discount is not set, do not submit dates.
                    start.value = '';
                    end.value = '';
                    return;
                }

                // If discount is set but date is empty, default to today
                if (!start.value) start.value = todayStr;
                if (!end.value) end.value = start.value;
                syncDiscountDates();
            }

            // Initial clamp (handles old values)
            clampDateInput(start, todayStr);
            syncDiscountDates();
            syncDiscountEnabledState();

            start.addEventListener('change', syncDiscountDates);
            end.addEventListener('change', syncDiscountDates);
            percent?.addEventListener('input', syncDiscountEnabledState);
            percent?.addEventListener('change', syncDiscountEnabledState);
            price?.addEventListener('input', syncDiscountEnabledState);
            price?.addEventListener('change', syncDiscountEnabledState);
        })();

        // Pengeluaran (Course) - dynamic editable rows
        (function() {
            const tableBody = document.querySelector('#courseExpensesTable tbody');
            const addBtn = document.getElementById('addCourseExpenseRow');
            if (!tableBody || !addBtn) return;

            let idx = 0;
            function clampNonNeg(inp) {
                if (!inp) return;
                inp.addEventListener('input', () => {
                    const v = parseInt(inp.value || '0', 10);
                    if (isNaN(v) || v < 0) inp.value = 0;
                });
            }

            function recalcRow(tr) {
                const qty = parseInt(tr.querySelector('input[data-expense-qty]')?.value || '0', 10);
                const unit = parseInt(tr.querySelector('input[data-expense-unit]')?.value || '0', 10);
                const totalEl = tr.querySelector('input[data-expense-total]');
                const total = (isNaN(qty) ? 0 : qty) * (isNaN(unit) ? 0 : unit);
                if (totalEl) totalEl.value = Math.max(0, total);
            }

            function renumberRows() {
                tableBody.querySelectorAll('tr').forEach((tr, i) => {
                    const no = tr.querySelector('[data-expense-no]');
                    if (no) no.textContent = String(i + 1);
                });
            }

            function addRow() {
                const tr = document.createElement('tr');
                const rowIndex = idx++;
                tr.innerHTML = `
                    <th scope="row" data-expense-no></th>
                    <td><input type="text" class="form-control form-control-sm" name="expenses[${rowIndex}][item]" placeholder="Nama kebutuhan"></td>
                    <td style="width:120px"><input type="number" class="form-control form-control-sm" name="expenses[${rowIndex}][quantity]" data-expense-qty min="0" step="1" value="0"></td>
                    <td style="width:180px"><input type="number" class="form-control form-control-sm" name="expenses[${rowIndex}][unit_price]" data-expense-unit min="0" step="1" value="0"></td>
                    <td style="width:180px"><input type="number" class="form-control form-control-sm" name="expenses[${rowIndex}][total]" data-expense-total readonly value="0"></td>
                    <td style="width:80px" class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm" data-action="remove-expense" title="Hapus">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                `;

                const qtyInp = tr.querySelector('input[data-expense-qty]');
                const unitInp = tr.querySelector('input[data-expense-unit]');
                clampNonNeg(qtyInp);
                clampNonNeg(unitInp);
                qtyInp?.addEventListener('input', () => recalcRow(tr));
                unitInp?.addEventListener('input', () => recalcRow(tr));

                tableBody.appendChild(tr);
                recalcRow(tr);
                renumberRows();
            }

            tableBody.addEventListener('click', (e) => {
                const btn = e.target.closest('button[data-action="remove-expense"]');
                if (!btn) return;
                btn.closest('tr')?.remove();
                renumberRows();
            });

            addBtn.addEventListener('click', addRow);

            // Expose for form submit hook
            window.__recalcAllCourseExpenseRows = function() {
                tableBody.querySelectorAll('tr').forEach((tr) => recalcRow(tr));
            };
        })();

        // Trainer dropdown - load from admin JSON endpoint
        (function() {
            const trainerSelect = document.getElementById('course-trainer');
            if (!trainerSelect) return;

            const selectedTrainerId = (trainerSelect.getAttribute('data-selected') || '').trim();

            const endpoint = '/admin/api/trainers';
            fetch(endpoint, {
                    headers: {
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                })
                .then((r) => r.ok ? r.json() : Promise.reject(r))
                .then((json) => {
                    const trainers = Array.isArray(json?.data) ? json.data : [];
                    if (trainers.length === 0) {
                        trainerSelect.innerHTML = '<option value="" selected disabled>Belum ada trainer</option>';
                        return;
                    }
                    trainerSelect.innerHTML = '<option value="" selected disabled>Pilih trainer</option>';
                    trainers.forEach((t) => {
                        if (!t || typeof t.id === 'undefined') return;
                        const opt = document.createElement('option');
                        opt.value = String(t.id);
                        opt.textContent = (t.name || ('Trainer #' + t.id));
                        if (selectedTrainerId !== '' && opt.value === selectedTrainerId) {
                            opt.selected = true;
                        }
                        trainerSelect.appendChild(opt);
                    });
                })
                .catch(() => {
                    // Keep UI usable even if endpoint not ready
                    trainerSelect.innerHTML = '<option value="" selected disabled>Gagal memuat trainer</option>';
                });
        })();

            // File previews (small box)
            (function(){
                function setPlaceholder(previewEl){
                    if(!previewEl) return;
                    // cleanup previous object URL
                    try {
                        const prevUrl = previewEl.dataset.objectUrl;
                        if(prevUrl) URL.revokeObjectURL(prevUrl);
                    } catch(_e) {}
                    previewEl.dataset.objectUrl = '';
                    previewEl.innerHTML = '<small class="text-muted">Preview</small>';
                }

                function renderPreview(inputEl, previewEl, allowVideo){
                    if(!inputEl || !previewEl) return;
                    const file = inputEl.files && inputEl.files[0];
                    if(!file){
                        setPlaceholder(previewEl);
                        return;
                    }

                    // cleanup old
                    try {
                        const prevUrl = previewEl.dataset.objectUrl;
                        if(prevUrl) URL.revokeObjectURL(prevUrl);
                    } catch(_e) {}

                    const url = URL.createObjectURL(file);
                    previewEl.dataset.objectUrl = url;
                    previewEl.innerHTML = '';

                    const type = (file.type || '').toLowerCase();
                    const isImage = type.startsWith('image/');
                    const isVideo = allowVideo && type.startsWith('video/');

                    if(isImage){
                        const img = document.createElement('img');
                        img.src = url;
                        img.alt = 'Preview';
                        img.style.width = '100%';
                        img.style.height = '100%';
                        img.style.objectFit = 'cover';
                        previewEl.appendChild(img);
                        return;
                    }

                    if(isVideo){
                        const video = document.createElement('video');
                        video.src = url;
                        video.muted = true;
                        video.playsInline = true;
                        video.loop = true;
                        video.autoplay = true;
                        video.style.width = '100%';
                        video.style.height = '100%';
                        video.style.objectFit = 'cover';
                        previewEl.appendChild(video);
                        return;
                    }

                    // fallback
                    const span = document.createElement('small');
                    span.className = 'text-muted';
                    span.textContent = file.name || 'File dipilih';
                    previewEl.appendChild(span);
                }

                const introInput = document.getElementById('course-thumbnail');
                const introPreview = document.getElementById('course-thumbnail-preview');
                const cardInput = document.getElementById('card-thumbnail');
                const cardPreview = document.getElementById('card-thumbnail-preview');

                setPlaceholder(introPreview);
                setPlaceholder(cardPreview);

                introInput?.addEventListener('change', () => renderPreview(introInput, introPreview, true));
                cardInput?.addEventListener('change', () => renderPreview(cardInput, cardPreview, false));
            })();
    </script>
</body>

</html>