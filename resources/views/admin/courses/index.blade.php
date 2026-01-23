<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    @include('partials.navbar-admin-course')
    @if(session('success'))
        <div aria-live="polite" aria-atomic="true" class="position-relative">
            <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
                <div id="courseUpdatedToast" class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                try{
                    var el = document.getElementById('courseUpdatedToast');
                    if(window.bootstrap && el){
                        var t = new bootstrap.Toast(el);
                        t.show();
                    }
                }catch(e){}
            });
        </script>
    @endif
    <!-- Publish warning toast (shown when course has missing material) -->
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div id="publishWarningToast" class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4500">
                <div class="d-flex">
                    <div class="toast-body">
                        <span class="me-2" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.964 0L.165 13.233c-.457.778.091 1.767.982 1.767h13.706c.89 0 1.438-.99.982-1.767L8.982 1.566zM8 5.5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 8 5.5Zm0 7a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z"/>
                            </svg>
                        </span>
                        Lengkapkan Material Course Modules terlebih dahulu sebelum menerbitkan Course
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
    <!-- Already published toast (shown when course is already active) -->
    <div aria-live="polite" aria-atomic="true" class="position-relative">
        <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
            <div id="alreadyPublishedToast" class="toast align-items-center text-bg-info border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="4000">
                <div class="d-flex">
                    <div class="toast-body">
                        Course ini sudah diterbitkan
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        </div>
    </div>
    <div class="box_luar_course_builder">
        <h1 class="judul_course_builder">Daftar Course</h1>
        <p class="deskripsi_course_builder">Atur detail course sebelum dipublikasi</p>
        <a href="{{ route('admin.add-course') }}" class="tambah_course" style="text-decoration: none;">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16">
                <path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2" />
            </svg>
            <p>Tambah Course</p>
        </a>
        <div class="box_daftar_course">
            <h4 class="judul_daftar_course">Daftar Course yang Ada</h4>
            <table class="tabel_daftar_course">
                <thead>
                    <tr>
                        <th>Nama Course</th>
                        <th>Level</th>
                        <th>Harga</th>
                        <th>Status Kelengkapan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        @php
                            $hasModules = ($course->modules && $course->modules->count() > 0);
                            $isPublished = ($course->status === 'active');
                        @endphp
                        <tr>
                            <td>{{ $course->name }}</td>
                            <td>{{ ucfirst($course->level) }}</td>
                            <td>Rp. {{ number_format($course->price, 0, ',', '.') }}</td>
                            <td>
                                @if($isPublished)
                                    <button class="status_kelengkapan_complete">Complete</button>
                                @elseif(!$hasModules)
                                    <button class="status_kelengkapan_miss">Missing Material</button>
                                @else
                                    <button class="status_kelengkapan_inprogress">In Progress</button>
                                @endif
                            </td>
                            <td>
                                <div class="aksi_daftar_course d-flex gap-2">
                                    <a href="{{ route('admin.courses.edit', $course) }}" title="Edit">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z" />
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z" />
                                        </svg>
                                    </a>
                                    @php
                                        $previewData = [
                                            'title' => $course->name,
                                            'image' => $course->card_thumbnail ? Storage::url($course->card_thumbnail) : '',
                                            'description' => trim($course->description),
                                            'modules' => ($course->modules && $course->modules->count() > 0) ? $course->modules->implode('title', '||') : '',
                                            'published' => $isPublished ? '1' : '0',
                                            'level' => ucfirst($course->level),
                                            'price' => 'Rp. ' . number_format($course->price, 0, ',', '.'),
                                            'duration' => $course->duration . ' jam',
                                        ];
                                    @endphp
                                    <button type="button" class="btn p-0 preview-course" title="Preview" data-course="{{ base64_encode(json_encode($previewData)) }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye" viewBox="0 0 16 16">
                                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8M1.173 8a13 13 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5s3.879 1.168 5.168 2.457A13 13 0 0 1 14.828 8q-.086.13-.195.288c-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5s-3.879-1.168-5.168-2.457A13 13 0 0 1 1.172 8z" />
                                            <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5M4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Hapus course ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn p-0" title="Delete">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                                <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5M11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47M8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">Belum ada course.</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">
                {{ $courses->links() }}
            </div>
        </div>
    </div>
    <!-- Bootstrap is provided by the Vite bundle (`resources/js/app.js`) to avoid duplicate initialisation -->
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            try{
                // If bootstrap is available, ensure the profile dropdown is initialised
                var toggle = document.getElementById('adminProfileDropdown');
                if(window.bootstrap && toggle){
                    // Explicitly create Dropdown instance to avoid issues from duplicate loads
                    try{ new bootstrap.Dropdown(toggle); }catch(e){}
                    // Prevent default navigation on anchor to avoid instant page jump
                    toggle.addEventListener('click', function(ev){ ev.preventDefault(); });
                }
            }catch(e){console && console.warn && console.warn(e);}
        });
    </script>

    <!-- Course Preview Modal -->
    <div class="modal fade" id="coursePreviewModal" tabindex="-1" aria-labelledby="coursePreviewLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-semibold" id="coursePreviewLabel">Preview Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-0">
                    <hr class="mt-0 mb-4" />
                    <div class="row g-4 align-items-start">
                        <div class="col-lg-5">
                            <img id="cp-image" src="" alt="Preview" class="img-fluid rounded" style="object-fit:cover; width:100%; max-height:260px; background:#f3f4f6;" />
                        </div>
                        <div class="col-lg-7">
                            <h5 class="mb-2">Deskripsi Course Singkat</h5>
                            <p id="cp-description" class="mb-3 text-secondary" style="white-space:pre-wrap"></p>
                            <h6 class="mb-2">Highlights</h6>
                            <ul class="mb-4" id="cp-highlights"></ul>
                            <div class="d-grid d-sm-flex gap-3 small">
                                <div><span class="fw-semibold">Level Course:</span> <span id="cp-level"></span></div>
                                <div><span class="fw-semibold">Harga:</span> <span id="cp-price"></span></div>
                                <div><span class="fw-semibold">Durasi Perkiraan:</span> <span id="cp-duration"></span></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <form id="cp-publish-form" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary" id="cp-publish-btn">Terbitkan Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            var modalEl = document.getElementById('coursePreviewModal');
            var modal = null;
            if(window.bootstrap && modalEl){
                try{ modal = new window.bootstrap.Modal(modalEl); }catch(e){}
            }
            // Fallback handling when Bootstrap JS is not available
            var _fallbackBackdrop = null;
            function showCourseModal(){
                if(window.bootstrap && modal){
                    try{ modal.show(); return; }catch(e){}
                }
                if(!modalEl) return;
                modalEl.classList.add('show');
                modalEl.style.display = 'block';
                modalEl.removeAttribute('aria-hidden');
                modalEl.setAttribute('aria-modal', 'true');
                document.body.classList.add('modal-open');
                if(!_fallbackBackdrop){
                    _fallbackBackdrop = document.createElement('div');
                    _fallbackBackdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(_fallbackBackdrop);
                }
            }
            function hideCourseModal(){
                if(window.bootstrap && modal){
                    try{ modal.hide(); return; }catch(e){}
                }
                if(!modalEl) return;
                modalEl.classList.remove('show');
                modalEl.style.display = 'none';
                modalEl.setAttribute('aria-hidden', 'true');
                modalEl.removeAttribute('aria-modal');
                document.body.classList.remove('modal-open');
                if(_fallbackBackdrop){ _fallbackBackdrop.remove(); _fallbackBackdrop = null; }
            }
            // Wire up any close buttons inside modal to fallback hide
            if(modalEl){
                modalEl.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(btn){
                    btn.addEventListener('click', function(){ hideCourseModal(); });
                });
                // clicking backdrop (outside modal-content) should also close
                modalEl.addEventListener('click', function(ev){
                    if(ev.target === modalEl){ hideCourseModal(); }
                });
            }
            var currentPreviewHasModules = false;
            var currentPreviewIsPublished = false;
            function setText(id, val){ var el=document.getElementById(id); if(el){ el.textContent = val || ''; } }
            function setImage(id, url){ var el=document.getElementById(id); if(el){ el.src = url || ''; el.style.display = url ? 'block':'none'; } }
            function setHighlights(modsString){
                var ul = document.getElementById('cp-highlights');
                if(!ul) return;
                ul.innerHTML = '';
                if(!modsString){ return; }
                // Split module titles by delimiter and render list (limit to 10)
                var titles = modsString.split('||').map(function(s){ return s.trim(); }).filter(Boolean).slice(0,10);
                titles.forEach(function(t){ var li=document.createElement('li'); li.textContent=t; ul.appendChild(li); });
            }
            function showPublishWarning(){
                var el = document.getElementById('publishWarningToast');
                if(window.bootstrap && el){
                    try{ new bootstrap.Toast(el).show(); }catch(e){}
                }
            }
            function showAlreadyPublished(){
                var el = document.getElementById('alreadyPublishedToast');
                if(window.bootstrap && el){
                    try{ new bootstrap.Toast(el).show(); }catch(e){}
                }
            }
            // Helper to get course id from edit link
            function getCourseIdFromEditLink(row){
                var editAnchor = row ? row.querySelector('a[href*="/admin/courses/"][href*="/edit"]') : null;
                if(editAnchor){
                    var href = editAnchor.getAttribute('href');
                    var match = href.match(/\/admin\/courses\/(\d+)/);
                    if(match){ return match[1]; }
                }
                return null;
            }

            // Use event delegation so clicks on the eye icon always trigger the preview
            document.addEventListener('click', function(ev){
                var btn = ev.target.closest && ev.target.closest('.preview-course');
                if(!btn) return;

                var raw = btn.getAttribute('data-course') || '';
                var data = {};
                if(raw){
                    try{ data = JSON.parse(atob(raw)); }catch(e){
                        try{ data = JSON.parse(raw); }catch(e2){ data = {}; }
                    }
                }
                var title = data.title || 'Preview Course';
                var img = data.image || '';
                var desc = data.description || '';
                var mods = data.modules || '';
                var published = data.published || '0';
                var level = data.level || '';
                var price = data.price || '';
                var duration = data.duration || '';
                // Set header
                var label = document.getElementById('coursePreviewLabel');
                if(label){ label.textContent = 'Preview Course: ' + title; }
                setImage('cp-image', img);
                setText('cp-description', desc);
                setHighlights(mods);
                currentPreviewHasModules = !!mods && mods.split('||').map(function(s){return s.trim();}).filter(Boolean).length > 0;
                currentPreviewIsPublished = String(published) === '1';
                setText('cp-level', level);
                setText('cp-price', price);
                setText('cp-duration', duration);

                // Set publish form action
                var row = btn.closest('tr');
                var courseId = getCourseIdFromEditLink(row);
                var publishForm = document.getElementById('cp-publish-form');
                var publishBtn = document.getElementById('cp-publish-btn');
                if(publishForm && courseId){
                    publishForm.action = '/admin/courses/' + courseId + '/publish';
                    publishForm.method = 'POST';
                    // Visual "disabled" state when already published
                    if(currentPreviewIsPublished){
                        publishBtn.setAttribute('disabled', 'true');
                        publishBtn.textContent = 'Sudah Diterbitkan';
                    }else{
                        publishBtn.removeAttribute('disabled');
                        publishBtn.textContent = 'Terbitkan Course';
                    }
                    publishForm.onsubmit = function(evt){
                        if(currentPreviewIsPublished){
                            evt.preventDefault();
                            showAlreadyPublished();
                            return false;
                        }
                        if(!currentPreviewHasModules){
                            evt.preventDefault();
                            showPublishWarning();
                            return false;
                        }
                        return true;
                    };
                }

                // Ensure modal instance exists (safe reference to window.bootstrap)
                if(!modal && window.bootstrap && modalEl){
                    try{ modal = new window.bootstrap.Modal(modalEl); }catch(e){}
                }
                // Use unified show function (will fallback when Bootstrap JS isn't present)
                try{ showCourseModal(); }catch(e){
                    if(modal){ try{ modal.show(); }catch(e2){} }
                }
            });
        });
    </script>
</body>

</html>