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
    <style>
        .sanity-msg { min-height: 20px; font-size: 12px; color: #dc3545; display: block; margin-top: 4px; }
    </style>
</head>

<body>
    @include("partials.navbar-admin-course")
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="box_luar_add_course" style="text-align:left;">
                    <div class="box_link d-flex align-items-center gap-2 text-muted small mb-2">
                        <a href="{{ route('admin.courses.index') }}" class="text-decoration-none">Course Builder</a>
                        <span>/</span>
                        <a href="{{ route('admin.add-course') }}" class="text-decoration-none">Add Course</a>
                    </div>
                    <div class="mb-3" style="display:flex; align-items:flex-start; gap:12px;">
                        <a href="{{ route('admin.courses.index') }}"
                           style="display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border:1px solid #dee2e6; border-radius:6px; color:#212529; text-decoration:none; flex-shrink:0; margin-top:4px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                            </svg>
                        </a>
                        <div style="text-align:left;">
                            <h1 class="h3 mb-1">Add Course</h1>
                            <p class="text-muted mb-0">Configure course details before publishing</p>
                        </div>
                    </div>

                    <form class="box_form" action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="status" value="archive">
                        <h4 class="h5 mb-2">Course Setup Form</h4>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="course-title">Course Title <span class="text-danger">*</span></label>
                            <input id="course-title" name="name" type="text" class="form-control" placeholder="Enter Course Title" required>
                            <div class="sanity-msg title" data-for="course-title"></div>
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
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="course-trainer">Trainer <span class="text-danger">*</span></label>
                                <select id="course-trainer" name="trainer_id" class="form-select" required data-selected="{{ old('trainer_id') }}">
                                    <option value="" selected disabled>Choose trainer</option>
                                </select>
                                <div class="sanity-msg" data-for="course-trainer"></div>
                            </div>
                        </div>
                        {{-- Module titles: shown dynamically based on selected level --}}
                        <div id="module-titles-section" class="mb-3" style="display:none;">
                            <label class="form-label text-dark fw-semibold">Input Title Module <span class="text-danger">*</span></label>
                            <div id="module-titles-grid" class="row g-3"></div>
                        </div>

                        <div class="row g-3 mb-3">
                            @if(isset($categories) && $categories->count())
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="course-category">Category <span class="text-danger">*</span></label>
                                <div style="position:relative;">
                                    <input type="text" id="course-category-input" class="form-control" placeholder="Type to search category..." autocomplete="off">
                                    <input type="hidden" id="course-category" name="category_id">
                                    <input type="hidden" id="course-category-name" name="category_name">
                                    <ul id="category-suggestions" style="display:none; position:absolute; top:calc(100% + 2px); left:0; right:0; background:#fff; border:1px solid #dee2e6; border-radius:6px; z-index:999; list-style:none; margin:0; padding:4px 0; max-height:180px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,.1);">
                                        @foreach($categories as $cat)
                                        <li data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" style="padding:5px 14px; cursor:pointer; font-size:13px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background=''">{{ $cat->name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="sanity-msg" data-for="course-category" style="margin-top: -5px;"></div>
                            </div>
                            @else
                            <input type="hidden" name="category_id" value="1">
                            @endif
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="course-price">Price <span class="text-danger">*</span></label>
                                <input id="course-price" name="price" type="text" class="form-control" inputmode="numeric" placeholder="0" required>
                                <div class="form-text harga-course">Enter 0 for free course</div>
                                <div class="sanity-msg" data-for="course-price"></div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="free_access_mode" class="form-label text-dark">Access Course</label>
                            <select name="free_access_mode" id="free_access_mode" class="form-select">
                                <option value="limit_2" {{ old('free_access_mode', 'limit_2') === 'limit_2' ? 'selected' : '' }}>Freemium (Module 1 Open)</option>
                                <option value="all" {{ old('free_access_mode') === 'all' ? 'selected' : '' }}>Open All Materials</option>
                                <option value="none" {{ old('free_access_mode') === 'none' ? 'selected' : '' }}>Close Review (Must Purchase First)</option>
                            </select>
                            <div class="form-text text-muted small">Choose how users can access materials before purchasing (for paid courses) or the access status for free courses.</div>
                        </div>

                        <input type="hidden" id="course-duration" name="duration" value="0">

                        <div class="mb-3">
                            <label class="form-label text-dark">Reseller Course</label>
                            @php
                                $isResellerCourse = (int) old('is_reseller_course', 0);
                            @endphp
                            <div class="reseller-course-radios d-flex flex-wrap align-items-center" style="column-gap: 2rem; row-gap: .5rem;" role="radiogroup" aria-label="Reseller Course">
                                <div class="reseller-course-option d-inline-flex align-items-center" style="white-space:nowrap; flex: 0 0 auto;">
                                    <input class="form-check-input m-0" type="radio" name="is_reseller_course" id="is_reseller_course_0" value="0"
                                        {{ $isResellerCourse === 0 ? 'checked' : '' }}>
                                    <label class="text-dark" for="is_reseller_course_0">No</label>
                                </div>
                                <div class="reseller-course-option d-inline-flex align-items-center" style="white-space:nowrap; flex: 0 0 auto;">
                                    <input class="form-check-input m-0" type="radio" name="is_reseller_course" id="is_reseller_course_1" value="1"
                                        {{ $isResellerCourse === 1 ? 'checked' : '' }}>
                                    <label class="text-dark" for="is_reseller_course_1">Yes</label>
                                </div>
                            </div>
                            <div class="form-text">If Yes, this course will be marked as a reseller course.</div>
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
                            <label class="form-label text-dark" for="course-description">Description Course</label>
                            <textarea id="course-description" name="description" class="form-control" placeholder="Describe the course in detail"></textarea>
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
                            <div class="form-text">Can upload images <b>or</b> video (mp4, webm, ogg)</div>
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
                            <div class="form-text">Upload an image for the course card thumbnail (jpg/png/webp)</div>
                            <div class="sanity-msg" data-for="card-thumbnail"></div>
                        </div>
                        <div class="box_select_diskon mb-3">
                            <label class="form-label text-dark" for="discount-percent">Discount (%)</label>
                            <input id="discount-percent" name="discount_percent" type="number" class="form-control" min="0" max="100" placeholder="Enter discount (0-100)">
                            <div class="form-text">Can be empty or 0%</div>
                            <div class="sanity-msg" data-for="discount-percent"></div>
                        </div>
                        <div class="box_select_tanggal_diskon row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="discount-start">Discount Start Date</label>
                                <input id="discount-start" name="discount_start" type="date" class="form-control" min="{{ now()->toDateString() }}" value="{{ old('discount_start', now()->toDateString()) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="discount-end">Discount End Date</label>
                                <input id="discount-end" name="discount_end" type="date" class="form-control" min="{{ now()->toDateString() }}" value="{{ old('discount_end', now()->toDateString()) }}">
                            </div>
                        </div>
                        <div class="box_select_diskon mb-3">
                            <label class="form-label text-dark" for="discount-percent">Expenses</label>
                            <div class="table-responsive">
                            <table class="table" id="courseExpensesTable">
                                <thead>
                                    <tr>
                                        <th scope="col">Number</th>
                                        <th scope="col">Need Name</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Unit Price</th>
                                        <th scope="col">Total Price</th>
                                        <th scope="col">Action</th>

                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                            </div>
                            <button type="button" class="tombol_tambah_pengeluaran" id="addCourseExpenseRow">Add Expense</button>
                            <div class="sanity-msg" data-for="discount-percent"></div>
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
    // Dynamic module title inputs based on level
    (function () {
        const levelModules = { beginner: 3, intermediate: 6, advanced: 12 };
        const levelSelect  = document.getElementById('course-level');
        const section      = document.getElementById('module-titles-section');
        const grid         = document.getElementById('module-titles-grid');

        function renderModuleTitles(level) {
            const count = levelModules[level] || 0;
            grid.innerHTML = '';
            if (!count) { section.style.display = 'none'; return; }

            section.style.display = 'block';
            for (let i = 1; i <= count; i++) {
                const col = document.createElement('div');
                col.className = 'col-md-4';
                col.innerHTML = `
                    <label class="form-label text-dark small" for="module-title-${i}">Module ${i}</label>
                    <input id="module-title-${i}" name="unit_titles[${i}]" type="text"
                           class="form-control form-control-sm"
                           placeholder="Title module ${i}" required>`;
                grid.appendChild(col);
            }
        }

        if (levelSelect) {
            levelSelect.addEventListener('change', function () {
                renderModuleTitles(this.value);
            });
            // init if old value exists
            if (levelSelect.value) renderModuleTitles(levelSelect.value);
        }
    })();
    </script>

    <script>
        (function(){
            const inp = document.getElementById('course-category-input');
            const hidden = document.getElementById('course-category');
            const hiddenName = document.getElementById('course-category-name');
            const list = document.getElementById('category-suggestions');
            
            if (inp && list) {
                const allItems = Array.from(list.querySelectorAll('li'));

                function showSuggestions() {
                    const q = inp.value.toLowerCase();
                    let any = false;
                    allItems.forEach(li => {
                        const match = li.dataset.name.toLowerCase().includes(q);
                        li.style.display = match ? '' : 'none';
                        if (match) any = true;
                    });
                    list.style.display = any ? 'block' : 'none';
                }

                inp.addEventListener('input', function() {
                    hidden.value = '';
                    hiddenName.value = '';
                    showSuggestions();
                });

                inp.addEventListener('focus', showSuggestions);
                inp.addEventListener('click', showSuggestions);

                inp.addEventListener('blur', function() {
                    const val = this.value.trim();
                    if (val && !hidden.value) {
                        hiddenName.value = val;
                    }
                    setTimeout(() => { list.style.display = 'none'; }, 250);
                });

                list.addEventListener('mousedown', function(e) {
                    const li = e.target.closest('li');
                    if (!li) return;
                    e.preventDefault();
                    inp.value = li.dataset.name;
                    hidden.value = li.dataset.id;
                    hiddenName.value = '';
                    list.style.display = 'none';
                    inp.classList.remove('is-invalid');
                    const m = document.querySelector('.sanity-msg[data-for="course-category"]');
                    if (m) { m.textContent = ''; m.classList.remove('show'); }
                });

                document.addEventListener('click', function(e) {
                    if (!inp.contains(e.target) && !list.contains(e.target)) {
                        list.style.display = 'none';
                    }
                });
            }
        })();
    </script>

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
                if (!listEl) return;
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
                    type,
                    title: titleBase,
                    subtitle: type === 'pdf' ? 'Dokumen materi' : 'Video pembelajaran',
                    filename: file.name,
                    mime: file.type || '',
                    order: nextOrder,
                };
                modules.push(mod);
                renderList();
            }

            function resetPdfModal() {
                selectedPdfFile = null;
                if(pdfModalTitle) pdfModalTitle.value = '';
                if(pdfModalDesc) pdfModalDesc.value = '';
                if(pdfModalOrder) pdfModalOrder.value = Math.max(0, ...modules.map(m => m.order || 0)) + 1;
                if(pdfModalFile) pdfModalFile.value = '';
                if(pdfFileName) pdfFileName.textContent = '';
            }

            function setSelectedPdf(file) {
                if (!file) return;
                selectedPdfFile = file;
                if(pdfFileName) pdfFileName.textContent = file.name;
                if (pdfModalTitle && !pdfModalTitle.value) {
                    pdfModalTitle.value = file.name.replace(/\.[^.]+$/, '');
                }
            }

            function bindPdfFileInput(el) {
                if(!el) return;
                el.addEventListener('change', (e) => {
                    const f = e.target.files && e.target.files[0];
                    setSelectedPdf(f);
                });
            }

            if (openPdfBtn) {
                openPdfBtn.addEventListener('click', () => {
                    resetPdfModal();
                    if (pdfModalInstance) pdfModalInstance.show();
                    else if(pdfModalEl) pdfModalEl.style.display = 'block';
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
                pdfDropZone.addEventListener('click', () => { if(pdfModalFile) pdfModalFile.click(); });
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
                    const title = (pdfModalTitle ? pdfModalTitle.value || '' : '').trim();
                    const desc = (pdfModalDesc ? pdfModalDesc.value || '' : '').trim();
                    const order = parseInt(pdfModalOrder ? pdfModalOrder.value || '1' : '1', 10) || 1;
                    if (title.length < 3) {
                        alert('Judul minimal 3 karakter.');
                        return;
                    }
                    if (!selectedPdfFile) {
                        alert('Silakan pilih file PDF.');
                        return;
                    }
                    const uid = 'm' + Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
                    
                    if (pdfModalFile && fileBucket) {
                        pdfModalFile.name = `module_files[${uid}]`;
                        fileBucket.appendChild(pdfModalFile);
                        
                        const newInput = document.createElement('input');
                        newInput.type = 'file';
                        newInput.id = 'pdf-modal-file';
                        newInput.accept = 'application/pdf';
                        newInput.style.display = 'none';
                        pdfModalFile = newInput;
                        
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

            function resetVideoModal() {
                selectedVideoFile = null;
                if(videoModalTitle) videoModalTitle.value = '';
                if(videoModalDesc) videoModalDesc.value = '';
                if(videoModalOrder) videoModalOrder.value = Math.max(0, ...modules.map(m => m.order || 0)) + 1;
                if(videoModalFile) videoModalFile.value = '';
                if(videoFileName) videoFileName.textContent = '';
            }

            function setSelectedVideo(file) {
                if (!file) return;
                selectedVideoFile = file;
                if(videoFileName) videoFileName.textContent = file.name;
                if (videoModalTitle && !videoModalTitle.value) {
                    videoModalTitle.value = file.name.replace(/\.[^.]+$/, '');
                }
            }

            function bindVideoFileInput(el) {
                if(!el) return;
                el.addEventListener('change', (e) => {
                    const f = e.target.files && e.target.files[0];
                    setSelectedVideo(f);
                });
            }

            if (openVideoBtn) {
                openVideoBtn.addEventListener('click', () => {
                    resetVideoModal();
                    if (videoModalInstance) videoModalInstance.show();
                    else if(videoModalEl) videoModalEl.style.display = 'block';
                });
            }
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
                videoDropZone.addEventListener('click', () => { if(videoModalFile) videoModalFile.click(); });
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
                    const title = (videoModalTitle ? videoModalTitle.value || '' : '').trim();
                    const desc = (videoModalDesc ? videoModalDesc.value || '' : '').trim();
                    const order = parseInt(videoModalOrder ? videoModalOrder.value || '1' : '1', 10) || 1;
                    if (title.length < 3) {
                        alert('Judul minimal 3 karakter.');
                        return;
                    }
                    if (!selectedVideoFile) {
                        alert('Silakan pilih file video.');
                        return;
                    }
                    const uid = 'm' + Date.now().toString(36) + Math.random().toString(36).slice(2, 8);
                    
                    if (videoModalFile && fileBucket) {
                        videoModalFile.name = `module_files[${uid}]`;
                        fileBucket.appendChild(videoModalFile);
                        
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

            let quizDraft = {
                title: '',
                description: '',
                questions: []
            };

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

            const btnGoToAddQuestion = document.getElementById('btn-goto-add-question');
            const btnGotoReview = document.getElementById('btn-goto-review');
            const btnCancelQuestion = document.getElementById('btn-cancel-question');
            const btnSaveQuestion = document.getElementById('btn-save-question');
            const btnBackToStep1 = document.getElementById('btn-back-to-step-1');
            const btnFinalSaveQuiz = document.getElementById('btn-final-save-quiz');

            const qTextInput = document.getElementById('q-text-input');
            const qOptionsContainer = document.getElementById('q-options-container');
            const quizQuestionNumberTitle = document.getElementById('quiz-question-number-title');

            const reviewQuizTitle = document.getElementById('review-quiz-title');
            const reviewQuizDesc = document.getElementById('review-quiz-desc');
            const reviewTotalQ = document.getElementById('review-total-q');
            const reviewQuestionsList = document.getElementById('review-questions-list');

            function switchStep(step) {
                [quizStep1, quizStep2, quizStep3].forEach(el => { if(el) el.style.display = 'none'; });
                [quizFooter1, quizFooter2, quizFooter3].forEach(el => { if(el) el.style.setProperty('display', 'none', 'important'); });

                if (step === 1) {
                    if(quizStep1) quizStep1.style.display = 'block';
                    if(quizFooter1) quizFooter1.style.display = 'flex';
                    renderQuestionsMinimal();
                } else if (step === 2) {
                    if(quizStep2) quizStep2.style.display = 'block';
                    if(quizFooter2) quizFooter2.style.display = 'flex';
                    const nextNum = quizDraft.questions.length + 1;
                    if (quizQuestionNumberTitle) quizQuestionNumberTitle.textContent = `Quiz #${nextNum}`;
                    resetQuestionForm();
                } else if (step === 3) {
                    if(quizStep3) quizStep3.style.display = 'block';
                    if(quizFooter3) quizFooter3.style.display = 'flex';
                    renderReview();
                }
            }

            function resetQuizDraft() {
                quizDraft = {
                    title: '',
                    description: '',
                    questions: []
                };
                if(quizTitleInput) quizTitleInput.value = '';
                if(quizDescInput) quizDescInput.value = '';
                switchStep(1);
            }

            function renderQuestionsMinimal() {
                if(!quizQuestionsList) return;
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
                if(quizQuestionCount) quizQuestionCount.textContent = quizDraft.questions.length;
            }

            if(quizQuestionsList) {
                quizQuestionsList.addEventListener('click', (e) => {
                    const btn = e.target.closest('.btn.text-danger');
                });
            }

            window.removeDraftQuestion = function(idx) {
                quizDraft.questions.splice(idx, 1);
                renderQuestionsMinimal();
            };

            function resetQuestionForm() {
                if(qTextInput) qTextInput.value = '';
                if(qOptionsContainer) {
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
            }

            if (openQuizBtn) {
                const newBtn = openQuizBtn.cloneNode(true);
                openQuizBtn.replaceWith(newBtn); 
                document.getElementById('open-add-quiz-modal').addEventListener('click', () => {
                    resetQuizDraft();
                    if (quizModalInstance) quizModalInstance.show();
                });
            }

            if(btnGoToAddQuestion) btnGoToAddQuestion.addEventListener('click', () => switchStep(2));
            if(btnCancelQuestion) btnCancelQuestion.addEventListener('click', () => switchStep(1));

            if(btnSaveQuestion) {
                btnSaveQuestion.addEventListener('click', () => {
                    const text = qTextInput ? qTextInput.value.trim() : '';
                    if (!text) {
                        alert('Please enter question text');
                        return;
                    }

                    const options = [];
                    let correctIdx = -1;
                    const optInputs = qOptionsContainer ? qOptionsContainer.querySelectorAll('.question-option-input') : [];
                    const radios = qOptionsContainer ? qOptionsContainer.querySelectorAll('.question-correct-radio') : [];

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
            }

            if(btnGotoReview) {
                btnGotoReview.addEventListener('click', () => {
                    quizDraft.title = quizTitleInput ? quizTitleInput.value.trim() : '';
                    quizDraft.description = quizDescInput ? quizDescInput.value.trim() : '';

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
            }

            if(btnBackToStep1) btnBackToStep1.addEventListener('click', () => switchStep(1));

            function renderReview() {
                if(reviewQuizTitle) reviewQuizTitle.textContent = quizDraft.title;
                if(reviewQuizDesc) reviewQuizDesc.textContent = quizDraft.description || '-';
                if(reviewTotalQ) reviewTotalQ.textContent = quizDraft.questions.length;

                if(reviewQuestionsList) {
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
            }

            if(btnFinalSaveQuiz) {
                btnFinalSaveQuiz.addEventListener('click', () => {
                    const nextOrder = Math.max(0, ...modules.map(m => m.order || 0)) + 1;
                    const uid = 'q' + Date.now().toString(36);

                    modules.push({
                        type: 'quiz',
                        title: quizDraft.title,
                        subtitle: `${quizDraft.questions.length} questions`,
                        filename: 'Quiz Module',
                        mime: 'application/json',
                        order: nextOrder,
                        uid: uid,
                        data: JSON.parse(JSON.stringify(quizDraft)) 
                    });

                    renderList();
                    if (quizModalInstance) quizModalInstance.hide();
                });
            }
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
                d = d.replace(/^0+(?=\d)/, '');
                if (d.length === 0) return '';
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
                const newCaret = caretPosForDigitIndex(formatted, digitIndex);
                try {
                    inputEl.setSelectionRange(newCaret, newCaret);
                } catch (e) {}
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
                setIndicator('course-title', ok, 'Course title is required.');
                return ok;
            }

            function validateStatus() {
                if (!fields.status) return true;
                const v = fields.status.value;
                const ok = (v === 'active' || v === 'archive');
                setIndicator('course-status', ok, 'Status is required.');
                return ok;
            }

            function validateLevel() {
                const v = fields.level.value;
                const ok = (v === 'beginner' || v === 'intermediate' || v === 'advanced');
                setIndicator('course-level', ok, 'Course level is required.');
                return ok;
            }

            function validateCategory() {
                if (!fields.category) return true;
                const categoryId = (fields.category.value || '').trim();
                const categoryName = (document.getElementById('course-category-name')?.value || '').trim();
                const categoryInput = (document.getElementById('course-category-input')?.value || '').trim();
                const ok = categoryId.length > 0 || categoryName.length > 0 || categoryInput.length > 0;
                
                const visibleInput = document.getElementById('course-category-input');
                if (visibleInput) visibleInput.classList.toggle('is-invalid', !ok);
                const m = msgEl('course-category');
                if (m) {
                    if (!ok) { m.textContent = 'Category is required.'; m.classList.add('show'); }
                    else { m.textContent = ''; m.classList.remove('show'); }
                }
                return ok;
            }

            function validateTrainer() {
                if (!fields.trainer) return true;
                const v = (fields.trainer.value || '').trim();
                const ok = v.length > 0;
                setIndicator('course-trainer', ok, 'Trainer is required.');
                return ok;
            }

            function validatePrice() {
                const raw = (fields.price.value || '').trim();
                const digits = raw.replace(/[^0-9]/g, '');
                if (digits.length === 0) {
                    setIndicator('course-price', false, 'Price is required.');
                    return false;
                }
                const val = parseInt(digits, 10);
                const ok = !isNaN(val) && val >= 0;
                setIndicator('course-price', ok, 'Price must be a number >= 0.');
                return ok;
            }

            function validateThumbnail() {
                const f = fields.thumbnail.files && fields.thumbnail.files[0];
                if (!f) {
                    setIndicator('course-thumbnail', false, 'Intro media is required.');
                    return false;
                }
                const allowed = [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg',
                    'video/mp4', 'video/webm', 'video/ogg'
                ];
                if (!allowed.includes(f.type)) {
                    setIndicator('course-thumbnail', false, 'File must be an image (jpg/png/webp/gif) or video (mp4/webm/ogg).');
                    return false;
                }
                setIndicator('course-thumbnail', true);
                return true;
            }

            function validateCardThumbnail() {
                const f = fields.cardThumbnail?.files && fields.cardThumbnail.files[0];
                if (!fields.cardThumbnail) return true;
                if (!f) {
                    setIndicator('card-thumbnail', false, 'Thumbnail card course is required.');
                    return false;
                }
                const allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];
                if (!allowed.includes(f.type)) {
                    setIndicator('card-thumbnail', false, 'Thumbnail must be an image (jpg/png/webp/gif).');
                    return false;
                }
                setIndicator('card-thumbnail', true);
                return true;
            }

            function validateDuration() {
                if (!fields.duration) return true;
                const raw = (fields.duration.value || '').trim();
                const val = parseInt(raw || '0', 10);
                const ok = !isNaN(val) && val >= 0;
                setIndicator('course-duration', ok, 'Duration is not valid.');
                return ok;
            }

            function validateAll() {
                const checks = [validateTitle(), validateStatus(), validateLevel(), validateCategory(), validateTrainer(), validatePrice(), validateThumbnail(), validateCardThumbnail(), validateDuration()];
                return checks.every(Boolean);
            }

            fields.title?.addEventListener('input', validateTitle);
            fields.level?.addEventListener('change', validateLevel);
            fields.category?.addEventListener('change', validateCategory);
            fields.trainer?.addEventListener('change', validateTrainer);
            fields.price?.addEventListener('input', function() {
                formatPriceFieldLive(fields.price);
                validatePrice();
            });
            fields.price?.addEventListener('blur', function() {
                formatPriceFieldLive(fields.price);
                validatePrice();
            });
            fields.thumbnail?.addEventListener('change', validateThumbnail);
            fields.cardThumbnail?.addEventListener('change', validateCardThumbnail);
            fields.duration?.addEventListener('input', validateDuration);

            validateAll();

            if (fields.price && (fields.price.value || '').trim() !== '') {
                formatPriceFieldLive(fields.price);
            }

            const formEl = document.querySelector('form.box_form');
            if (formEl) {
                formEl.addEventListener('submit', function(ev) {
                    const catInput = document.getElementById('course-category-input');
                    const catHidden = document.getElementById('course-category');
                    const catName = document.getElementById('course-category-name');
                    if (catInput && catHidden && catName && !catHidden.value.trim() && catInput.value.trim()) {
                        catName.value = catInput.value.trim();
                    }

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
                    
                    const statusInputs = formEl.querySelectorAll('input[name="status"], select[name="status"]');
                    statusInputs.forEach((el) => {
                        if (el) el.value = 'archive';
                    });
                    
                    const priceInput = document.getElementById('course-price');
                    if (priceInput) {
                        const digits = (priceInput.value || '').replace(/[^0-9]/g, '');
                        priceInput.value = digits;
                    }

                    if (typeof window.__recalcAllCourseExpenseRows === 'function') {
                        window.__recalcAllCourseExpenseRows();
                    }
                    
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

                if (startVal && startVal < todayStr) {
                    start.value = todayStr;
                }

                const effectiveStart = (start.value || '').trim();
                const minEnd = (effectiveStart && effectiveStart > todayStr) ? effectiveStart : todayStr;
                end.min = minEnd;

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
                    start.value = '';
                    end.value = '';
                    return;
                }

                if (!start.value) start.value = todayStr;
                if (!end.value) end.value = start.value;
                syncDiscountDates();
            }

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
                const qty = Math.max(1, parseInt(tr.querySelector('input[data-expense-qty]')?.value || '1', 10));
                const unit = parseInt(tr.querySelector('input[data-expense-unit]')?.value || '0', 10);
                const totalEl = tr.querySelector('input[data-expense-total]');
                const total = qty * (isNaN(unit) ? 0 : unit);
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
                    <td style="width:120px"><input type="number" class="form-control form-control-sm" name="expenses[${rowIndex}][quantity]" data-expense-qty min="1" step="1" value="1"></td>
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
                    trainerSelect.innerHTML = '<option value="" selected disabled>Choose trainer</option>';
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
                    trainerSelect.innerHTML = '<option value="" selected disabled>Gagal memuat trainer</option>';
                });
        })();

            // File previews (small box)
            (function(){
                function setPlaceholder(previewEl){
                    if(!previewEl) return;
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