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
                            <div id="modules-list" class="d-flex flex-column gap-2 mb-3"></div>

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

    <!-- Add Quiz Modal -->
    <div class="modal fade" id="addQuizModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Create Quiz</h5>
                    <div>
                        <p class="deskripsi_judul modal-title">Set up quiz details</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="judul_nama_quiz form-label">Judul Quiz</label>
                        <input type="text" id="quiz-modal-title" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="judul_nama_quiz form-label">Quiz Description</label>
                        <textarea id="quiz-modal-desc" class="form-control"></textarea>
                    </div>
                    <button class="box_tambah_pertanyaan">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="black" class="bi bi-plus" viewBox="0 0 16 16">
                            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4" />
                        </svg>
                        <p style="color: black; margin-top: 8px;">Add Question</p>
                    </button>
                    <div class="box_luar_tambah_kuis">
                        <div class="box_dalam_pertanyaan_kuis">
                            <h5>Quiz #1</h5>
                            <p>Fill all fields to add</p>
                        </div>
                        <h5>Question Text</h5>
                        <div class="isi_pertanyaan_kuis">
                            <textarea class="form-control" rows="3">Enter Your Question... </textarea>
                        </div>
                        <div class="box_luar_answer_option">
                            <h5>Answer Option</h5>
                            <div class="answer_option">
                                <div class="box_option">
                                    <p>Option 1</p>
                                </div>
                                <div>
                                    <div class="form-check">
                                        <input class="radio_button_kuis form-check-input" type="radio" name="radioDisabled" id="radioCheckedDisabled" checked disabled>
                                        <label class="text_label form-check-label" for="radioCheckedDisabled">
                                            Correct
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box_luar_answer_option">
                            <div class="answer_option">
                                <div class="box_option">
                                    <p>Option 2</p>
                                </div>
                                <div>
                                    <div class="form-check">
                                        <input class="radio_button_kuis form-check-input" type="radio" name="radioDisabled" id="radioCheckedDisabled" checked disabled>
                                        <label class="text_label form-check-label" for="radioCheckedDisabled">
                                            Correct
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box_luar_answer_option">
                            <div class="answer_option">
                                <div class="box_option">
                                    <p>Option 3</p>
                                </div>
                                <div>
                                    <div class="form-check">
                                        <input class="radio_button_kuis form-check-input" type="radio" name="radioDisabled" id="radioCheckedDisabled" checked disabled>
                                        <label class="text_label form-check-label" for="radioCheckedDisabled">
                                            Correct
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="box_luar_answer_option">
                            <div class="answer_option">
                                <div class="box_option">
                                    <p>Option 4</p>
                                </div>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="radioDisabled" id="radioCheckedDisabled" checked disabled>
                                        <label class="text_label form-check-label" for="radioCheckedDisabled">
                                            Correct
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="quiz-modal-add-btn" class="btn btn-primary">Save</button>
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
                    <i class="bi ${mod.type==='pdf' ? 'bi-file-earmark-pdf' : 'bi-file-earmark-play'}" style="font-size:1.25rem;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2">
                        <strong class="me-2">${mod.title}</strong>
                        <span class="badge bg-secondary">#${(mod.order ?? (idx+1))}</span>
                    </div>
                    <div class="text-muted small">${mod.subtitle || ''}</div>
                    <div class="text-muted small">${mod.filename}</div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger" aria-label="Remove"><i class="bi bi-trash"></i></button>
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

                function renderList() {
                    listEl.innerHTML = '';
                    const sorted = [...modules].sort((a, b) => (a.order ?? 1e9) - (b.order ?? 1e9));
                    sorted.forEach(m => listEl.appendChild(makeModuleCard(m)));
                    updatePayload();
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