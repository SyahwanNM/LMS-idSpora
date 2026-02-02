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
                        <h4 class="h5 mb-3">Formulir Pengaturan Course</h4>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="course-title">Judul Course <span class="sanity-dot" data-for="course-title"></span></label>
                            <input id="course-title" name="name" type="text" class="form-control" placeholder="Masukkan Judul Course">
                            <div class="sanity-msg" data-for="course-title"></div>
                        </div>

                        <div class="row g-3 box_select_level_status">
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="course-status">Status <span class="sanity-dot" data-for="course-status"></span></label>
                                <select id="course-status" name="status" class="form-select">
                                    <option selected disabled>Choose your Status</option>
                                    <option value="active">Active</option>
                                    <option value="archive">Archive</option>
                                </select>
                                <div class="sanity-msg" data-for="course-status"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="course-level">Level Course <span class="sanity-dot" data-for="course-level"></span></label>
                                <select id="course-level" name="level" class="form-select">
                                    <option selected disabled>Choose your level</option>
                                    <option value="beginner">Beginner</option>
                                    <option value="intermediate">Intermediate</option>
                                    <option value="advanced">Advanced</option>
                                </select>
                                <div class="sanity-msg" data-for="course-level"></div>
                            </div>
                        </div>

                        @if(isset($categories) && $categories->count())
                        <div class="mb-3">
                            <label class="form-label text-dark" for="course-category">Kategori <span class="sanity-dot" data-for="course-category"></span></label>
                            <select id="course-category" name="category_id" class="form-select">
                                <option selected disabled>Pilih kategori</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                            <div class="sanity-msg" data-for="course-category"></div>
                        </div>
                        @else
                        <input type="hidden" name="category_id" value="1">
                        @endif

                        <div class="mb-3">
                            <label class="form-label text-dark" for="course-duration">Durasi (jam) <span class="sanity-dot" data-for="course-duration"></span></label>
                            <input id="course-duration" name="duration" type="number" class="form-control" min="1" placeholder="Masukkan durasi course dalam jam">
                            <div class="sanity-msg" data-for="course-duration"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark" for="course-price">Harga <span class="sanity-dot" data-for="course-price"></span></label>
                            <input id="course-price" name="price" type="text" class="form-control" placeholder="Masukkan Harga Course">
                            <div class="sanity-msg" data-for="course-price"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-dark" for="course-description">Deskripsi Course <span class="sanity-dot" data-for="course-description"></span></label>
                            <textarea id="course-description" name="description" class="form-control" placeholder="Deskripsikan course secara lengkap"></textarea>
                            <div class="sanity-msg" data-for="course-description"></div>
                        </div>

                        <div class="mb-1">
                            <label class="form-label text-dark" for="course-thumbnail">Thumbnail/Intro Media <span class="sanity-dot" data-for="course-thumbnail"></span></label>
                            <input id="course-thumbnail" name="image" type="file" class="form-control" accept="image/*,video/mp4,video/webm,video/ogg">
                            <div class="form-text">Bisa upload gambar <b>atau</b> video (mp4, webm, ogg)</div>
                            <div class="sanity-msg" data-for="course-thumbnail"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="card-thumbnail">Thumbnail Card Course <span class="sanity-dot" data-for="card-thumbnail"></span></label>
                            <input id="card-thumbnail" name="card_thumbnail" type="file" class="form-control" accept="image/*">
                            <div class="form-text">Upload gambar untuk thumbnail card course (jpg/png/webp)</div>
                            <div class="sanity-msg" data-for="card-thumbnail"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-dark" for="discount-percent">Diskon (%) <span class="sanity-dot" data-for="discount-percent"></span></label>
                            <input id="discount-percent" name="discount_percent" type="number" class="form-control" min="1" max="100" placeholder="Masukkan diskon (1-100)">
                            <div class="form-text">Diskon tidak boleh 0%</div>
                            <div class="sanity-msg" data-for="discount-percent"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="discount-start">Tanggal Mulai Diskon</label>
                                <input id="discount-start" name="discount_start" type="date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-dark" for="discount-end">Tanggal Berakhir Diskon</label>
                                <input id="discount-end" name="discount_end" type="date" class="form-control">
                            </div>
                        </div>

                        <!-- Course Modules Section -->
                        <div class="mt-4">
                            <h5 class="mb-2">Course Modules</h5>
                            <p class="small text-muted mb-2">Tambahkan materi untuk menyusun konten course Anda.</p>
                            <div id="modules-empty-state" class="text-center py-5 border rounded-3 mb-3" style="border-style: dashed !important; border-color: #dee2e6; background-color: #fff;">
                                <h6 class="text-muted fw-normal mb-1">No modules yet</h6>
                                <p class="text-muted small mb-0">Add your first module to structure your course</p>
                            </div>
                            <div id="modules-list" class="d-flex flex-column gap-2 mb-3" style="display:none;"></div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" id="open-add-pdf-modal" class="btn btn-primary">
                                    <i class="bi bi-plus-lg me-1"></i> Add PDF Module
                                </button>

                                <button type="button" id="open-add-video-modal" class="btn btn-outline-secondary">
                                    <i class="bi bi-plus-lg me-1"></i> Add Video
                                </button>

                                <button type="button" id="open-add-quiz-modal" class="btn btn-outline-secondary">
                                    <i class="bi bi-plus-lg me-1"></i> Add Quiz
                                </button>
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
    <!-- Add Video Modal -->
    <div class="modal fade" id="addVideoModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Video Module</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Video</label>
                        <input type="text" id="video-modal-title" class="form-control" placeholder="Masukkan Judul Video">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Module Description</label>
                        <textarea id="video-modal-desc" class="form-control" placeholder="What will students learn from this module"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Module Order</label>
                        <input type="number" id="video-modal-order" class="form-control" min="1" value="1">
                        <div class="form-text">Default: 1</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload Video</label>
                        <div id="video-drop-zone" class="drop-zone">
                            <i class="bi bi-upload icon"></i>
                            <div>Drag and drop your Video</div>
                            <div class="small text-muted">or click to browse</div>
                        </div>
                        <input type="file" id="video-modal-file" accept="video/mp4,video/webm,video/ogg" style="display:none;">
                        <div id="video-file-name" class="small text-muted mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="video-modal-add-btn" class="btn btn-primary">Add Module</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Add PDF Modal -->
    <div class="modal fade" id="addPdfModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add PDF Module</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul PDF</label>
                        <input type="text" id="pdf-modal-title" class="form-control" placeholder="Masukkan Judul PDF">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Module Description</label>
                        <textarea id="pdf-modal-desc" class="form-control" placeholder="What will students learn from this module"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Module Order</label>
                        <input type="number" id="pdf-modal-order" class="form-control" min="1" value="1">
                        <div class="form-text">Default: 1</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Upload PDF</label>
                        <div id="pdf-drop-zone" class="drop-zone">
                            <i class="bi bi-upload icon"></i>
                            <div>Drag and drop your PDF</div>
                            <div class="small text-muted">or click to browse</div>
                        </div>
                        <input type="file" id="pdf-modal-file" accept="application/pdf" style="display:none;">
                        <div id="pdf-file-name" class="small text-muted mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="pdf-modal-add-btn" class="btn btn-primary">Add Module</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Quiz Modal (Multi-step) -->
    <div class="modal fade" id="addQuizModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h5 class="modal-title fw-bold" id="quizModalTitle">Create Quiz</h5>
                        <p class="text-muted small mb-0" id="quizModalSubtitle">Add Question and Answer</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body pt-3">
                    <!-- Step 1: Quiz Overview (Title, Desc, List of Questions) -->
                    <div id="quiz-step-1">
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Judul Quiz</label>
                            <input type="text" id="quiz-title-input" class="form-control" placeholder="Masukkan Judul Quiz">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">Quiz Description</label>
                            <textarea id="quiz-desc-input" class="form-control" rows="3" style="resize:none;"></textarea>
                        </div>
                        
                        <!-- List of Added Questions -->
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark d-flex justify-content-between align-items-center">
                                <span>Question Added (<span id="quiz-question-count">0</span>)</span>
                                <span class="badge bg-light text-success border border-success px-2 rounded-pill" style="font-weight:500;">Ready to add more</span>
                            </label>
                            <div id="quiz-questions-list" class="d-flex flex-column gap-2">
                                <!-- Questions will be rendered here -->
                            </div>
                        </div>

                        <button type="button" id="btn-goto-add-question" class="btn btn-outline-secondary w-100 py-3 border-dashed d-flex align-items-center justify-content-center gap-2" style="border-style:dashed;">
                            <i class="bi bi-plus-lg"></i> Add Question
                        </button>
                    </div>

                    <!-- Step 2: Add/Edit Question Form -->
                    <div id="quiz-step-2" style="display:none;">
                        <div class="bg-light p-3 rounded-3 mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0" id="quiz-question-number-title">Quiz #1</h6>
                                <small class="text-muted">Fill all fields to add</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold small text-dark">Question Text</label>
                                <textarea id="q-text-input" class="form-control" rows="3" placeholder="Enter Your Question..."></textarea>
                            </div>

                            <label class="form-label fw-bold small text-dark">Answer Option</label>
                            <div class="d-flex flex-column gap-2" id="q-options-container">
                                <!-- Generated by JS -->
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Review -->
                    <div id="quiz-step-3" style="display:none;">
                        <div class="bg-light p-4 rounded-3 mb-4">
                            <div class="mb-2">
                                <small class="text-muted d-block">Quiz Title</small>
                                <h6 class="fw-bold" id="review-quiz-title">-</h6>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted d-block">Quiz Description</small>
                                <p class="mb-0 small text-dark" id="review-quiz-desc">-</p>
                            </div>
                            <div>
                                <small class="text-muted d-block">Total Question</small>
                                <h6 class="fw-bold mb-0"><span id="review-total-q">0</span> questions</h6>
                            </div>
                        </div>

                        <h6 class="fw-bold mb-3">Question Overview</h6>
                        <div id="review-questions-list" class="border rounded-3 p-0 overflow-hidden">
                            <!-- Review list rendered here -->
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 pt-0 pb-4 px-4 justify-content-between">
                    <!-- Footer Buttons Dynamic based on Step -->
                    <div id="quiz-footer-step-1" class="w-100 d-flex justify-content-between">
                        <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="btn-goto-review" class="btn btn-primary px-4" style="background-color: #4B2DBF; border-color: #4B2DBF;">Next Review</button>
                    </div>

                    <div id="quiz-footer-step-2" class="w-100 d-flex justify-content-end gap-2" style="display:none !important;">
                         <button type="button" id="btn-cancel-question" class="btn btn-light px-4">Cancel</button>
                         <button type="button" id="btn-save-question" class="btn btn-primary px-4" style="background-color: #4B2DBF; border-color: #4B2DBF;">+ Add Question</button>
                    </div>

                    <div id="quiz-footer-step-3" class="w-100 d-flex justify-content-between" style="display:none !important;">
                        <button type="button" id="btn-back-to-step-1" class="btn btn-light px-4">Back</button>
                        <button type="button" id="btn-final-save-quiz" class="btn btn-success px-4 bg-success border-success text-white">Save Quiz</button>
                    </div>
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

                if (window.bootstrap && bootstrap.Modal) {
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
                    videoModalInstance = new bootstrap.Modal(videoModalEl);
                    pdfModalInstance = new bootstrap.Modal(pdfModalEl);
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
                        } catch (e) { console.error('Failed to load draft', e); }
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
                    const pdfs = modules.filter(m => m.type === 'pdf').sort((a,b) => (a.order||0)-(b.order||0));
                    const videos = modules.filter(m => m.type === 'video').sort((a,b) => (a.order||0)-(b.order||0));
                    const quizzes = modules.filter(m => m.type === 'quiz').sort((a,b) => (a.order||0)-(b.order||0));

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

                    if(pdfs.length > 0) listEl.appendChild(createSection('PDF Document', pdfs));
                    if(videos.length > 0) listEl.appendChild(createSection('Video Lesson', videos));
                    if(quizzes.length > 0) listEl.appendChild(createSection('Quizzes', quizzes));

                    saveDraft();
                }

                // Initial Load
                loadDraft();
                
                // Clear draft on submit success (optional, or rely on page navigation)
                const mainForm = document.querySelector('form[action]'); 
                // There might be multiple forms, find the one wrapping this input
                // Or just assume the main one. Let's try to find if there is a main form
                if(mainForm) {
                    mainForm.addEventListener('submit', () => {
                        // We don't clear immediately in case validation fails, 
                        // but usually if it redirects, it's fine. 
                        // If it's an AJAX submit, we clear in the success callback.
                        // For now we keep it simple: persistence is for accidental refresh.
                        // If user submits, and it succeeds, they go to index. 
                        // If they come back to ADD page, maybe they want a fresh start?
                        // Yes, usually "Add" page should start fresh.
                        // So we clear it only if we detect a successful submission or explicitly.
                        // Actually, if we are on "Add Course" page, we probably want to load draft only if it exists.
                        // But if the user successfully created a course, we should clear it.
                        // For now, let's leave it. If they come back, they see their draft. 
                        // They can manually delete if they want.
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

                    if(step === 1) {
                        quizStep1.style.display = 'block';
                        quizFooter1.style.display = 'flex';
                        renderQuestionsMinimal();
                    } else if(step === 2) {
                        quizStep2.style.display = 'block';
                        quizFooter2.style.display = 'flex';
                        // Update Quiz #X title
                        const nextNum = quizDraft.questions.length + 1;
                        if(quizQuestionNumberTitle) quizQuestionNumberTitle.textContent = `Quiz #${nextNum}`;
                        
                        resetQuestionForm();
                    } else if(step === 3) {
                        quizStep3.style.display = 'block';
                        quizFooter3.style.display = 'flex';
                        renderReview();
                    }
                }

                function resetQuizDraft() {
                    quizDraft = { title: '', description: '', questions: [] };
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
                    if(btn) {
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
                         if(quizModalInstance) quizModalInstance.show();
                     });
                }

                btnGoToAddQuestion.addEventListener('click', () => switchStep(2));
                btnCancelQuestion.addEventListener('click', () => switchStep(1));
                
                btnSaveQuestion.addEventListener('click', () => {
                    const text = qTextInput.value.trim();
                    if(!text) { alert('Please enter question text'); return; }
                    
                    const options = [];
                    let correctIdx = -1;
                    
                    const optInputs = qOptionsContainer.querySelectorAll('.question-option-input');
                    const radios = qOptionsContainer.querySelectorAll('.question-correct-radio');
                    
                    let allFilled = true;
                    optInputs.forEach((inp, idx) => {
                        const val = inp.value.trim();
                        if(!val) allFilled = false;
                        options.push(val);
                        if(radios[idx].checked) correctIdx = idx;
                    });

                    if(!allFilled) { alert('Please fill all 4 options'); return; }
                    if(correctIdx === -1) { alert('Please select the correct answer'); return; }

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

                    if(!quizDraft.title) { alert('Please enter Quiz Title'); return; }
                    if(quizDraft.questions.length === 0) { alert('Please add at least one question'); return; }

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
                    if(quizModalInstance) quizModalInstance.hide();
                });
            })();
        </script>
        <script>
            (function() {
                const fields = {
                    title: document.getElementById('course-title'),
                    status: document.getElementById('course-status'),
                    level: document.getElementById('course-level'),
                    price: document.getElementById('course-price'),
                    description: document.getElementById('course-description'),
                    thumbnail: document.getElementById('course-thumbnail'),
                    duration: document.getElementById('course-duration'),
                };

                function dotEl(id) {
                    return document.querySelector('.sanity-dot[data-for="' + id + '"]');
                }

                function msgEl(id) {
                    return document.querySelector('.sanity-msg[data-for="' + id + '"]');
                }

                function setIndicator(id, ok, msg) {
                    const d = dotEl(id);
                    const m = msgEl(id);
                    if (d) d.classList.toggle('show', !ok);
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
                    const ok = v.length >= 3;
                    setIndicator('course-title', ok, 'Minimal 3 karakter.');
                    return ok;
                }

                function validateStatus() {
                    const v = fields.status.value;
                    const ok = (v === 'active' || v === 'archive');
                    setIndicator('course-status', ok, 'Pilih status yang valid.');
                    return ok;
                }

                function validateLevel() {
                    const v = fields.level.value;
                    const ok = (v === 'beginner' || v === 'intermediate' || v === 'advanced');
                    setIndicator('course-level', ok, 'Pilih level yang valid.');
                    return ok;
                }

                function validatePrice() {
                    const raw = (fields.price.value || '').trim();
                    const digits = raw.replace(/[^0-9]/g, '');
                    const val = parseInt(digits || '0', 10);
                    const ok = val > 0;
                    setIndicator('course-price', ok, 'Harga harus angka > 0.');
                    return ok;
                }

                function validateDescription() {
                    const v = (fields.description.value || '').trim();
                    const ok = v.length >= 10;
                    setIndicator('course-description', ok, 'Minimal 10 karakter.');
                    return ok;
                }

                function validateThumbnail() {
                    const f = fields.thumbnail.files && fields.thumbnail.files[0];
                    if (!f) {
                        setIndicator('course-thumbnail', false, 'Pilih gambar atau video.');
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

                function validateDuration() {
                    const raw = (fields.duration.value || '').trim();
                    const val = parseInt(raw || '0', 10);
                    const ok = val > 0;
                    setIndicator('course-duration', ok, 'Durasi harus lebih dari 0 menit.');
                    return ok;
                }

                function validateAll() {
                    const checks = [validateTitle(), validateStatus(), validateLevel(), validatePrice(), validateDescription(), validateThumbnail(), validateDuration()];
                    return checks.every(Boolean);
                }

                // live validation
                fields.title.addEventListener('input', validateTitle);
                fields.status.addEventListener('change', validateStatus);
                fields.level.addEventListener('change', validateLevel);
                fields.price.addEventListener('input', validatePrice);
                fields.description.addEventListener('input', validateDescription);
                fields.thumbnail.addEventListener('change', validateThumbnail);
                fields.duration.addEventListener('input', validateDuration);

                // initial state
                validateAll();

                const formEl = document.querySelector('form.box_form');
                if (formEl) {
                    formEl.addEventListener('submit', function(ev) {
                        if (!validateAll()) {
                            ev.preventDefault();
                            const firstDot = document.querySelector('.sanity-dot.show');
                            if (firstDot) {
                                const parent = firstDot.closest('.mb-3, .col-md-6, .mb-1') || document.body;
                                parent.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });
                            }
                            return;
                        }
                        // normalize price to digits
                        const priceInput = document.getElementById('course-price');
                        if (priceInput) {
                            const digits = (priceInput.value || '').replace(/[^0-9]/g, '');
                            priceInput.value = digits;
                        }
                        // ensure level value is valid
                        const levelSel = document.getElementById('course-level');
                        if (levelSel && levelSel.value === 'advance') {
                            levelSel.value = 'advanced';
                        }
                    });
                }
            })();
        </script>
</body>

</html>