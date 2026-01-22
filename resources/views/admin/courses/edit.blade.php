@extends('layouts.app')

@section('title', 'Edit Course - ' . $course->name)

@section('content')
@include('partials.navbar-admin-course-bootstrap')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="{{ route('admin.courses.index') }}" class="mr-4">
                        <svg class="w-6 h-6 text-gray-600 hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Course</h1>
                        <p class="text-sm text-gray-600">{{ $course->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                        <div class="font-semibold mb-2">Perubahan gagal disimpan. Mohon periksa error berikut:</div>
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-6">
                    <!-- Course Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Course</label>
                        <input type="text" name="name" id="name" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                               value="{{ old('name', $course->name) }}" placeholder="Enter course name">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select name="category_id" id="category_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('category_id') border-red-500 @enderror">
                            <option value="">Pilihan Kategori</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select name="status" id="status" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('status') border-red-500 @enderror">
                            <option value="">Select status</option>
                            <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="archive" {{ old('status', $course->status) == 'archive' ? 'selected' : '' }}>Archive</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-file-text me-1"></i>Deskripsi Course
                        </label>
                        <textarea name="description" id="description" class="form-control" rows="8">{!! old('description', $course->description) !!}</textarea>
                        <div class="form-text">Gunakan editor di bawah untuk membuat deskripsi yang menarik dengan format yang kaya.</div>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Level and Duration -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                            <select name="level" id="level" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('level') border-red-500 @enderror">
                                <option value="">Select level</option>
                                <option value="beginner" {{ old('level', $course->level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level', $course->level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                            @error('level')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Durasi Jam Belajar (jam)</label>
                            <input type="number" name="duration" id="duration" required min="0" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('duration') border-red-500 @enderror"
                                   value="{{ old('duration', $course->duration) }}" placeholder="0">
                            @error('duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Card Thumbnail (Current & Replace) -->
                    <div class="mt-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Card Thumbnail</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @if($course->card_thumbnail)
                                <img src="{{ Storage::url($course->card_thumbnail) }}" alt="Card Thumbnail" class="w-32 h-24 object-cover rounded-lg">
                            @else
                                <div class="w-32 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label for="card_thumbnail" class="block text-sm font-medium text-gray-700 mb-2">Replace Card Thumbnail (Optional)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="card_thumbnail" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a new card thumbnail</span>
                                        <input id="card_thumbnail" name="card_thumbnail" type="file" accept="image/*" class="sr-only @error('card_thumbnail') border-red-500 @enderror">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">Leave empty to keep current card thumbnail</p>
                            </div>
                        </div>
                        @error('card_thumbnail')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price (Rp)</label>
                        <input type="number" name="price" id="price" required min="0" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('price') border-red-500 @enderror"
                               value="{{ old('price', $course->price) }}" placeholder="0">
                        @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Current Media Display -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Media</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @if($course->media)
                                @if($course->media_type === 'video')
                                    <video src="{{ Storage::url($course->media) }}" controls class="w-32 h-24 object-cover rounded-lg"></video>
                                @else
                                    <img src="{{ Storage::url($course->media) }}" alt="{{ $course->name }}" class="w-32 h-24 object-cover rounded-lg">
                                @endif
                            @else
                                <div class="w-32 h-24 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- New Course Image -->
                    <div>
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Replace Media (Optional)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a new media (image/video)</span>
                                        <input id="image" name="image" type="file" accept="image/*,video/mp4,video/webm,video/ogg" 
                                               class="sr-only @error('image') border-red-500 @enderror">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">Leave empty to keep current media</p>
                            </div>
                        </div>
                        <!-- Live preview of selected image (optional, only for image) -->
                        <div id="imagePreviewContainer" class="mt-3" style="display:none;">
                            <img id="imagePreview" alt="Preview" class="w-full max-h-56 object-cover rounded-lg border border-gray-200" />
                            <div id="imagePreviewInfo" class="text-xs text-gray-500 mt-1"></div>
                            <button type="button" id="imagePreviewClear" class="mt-2 text-sm text-gray-600 hover:text-gray-800">Hapus pilihan</button>
                        </div>
                        @error('image')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Course Modules Management -->
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-2">Course Modules</h2>
                        <p class="text-sm text-gray-600 mb-4">Kelola modul yang sudah ada dan tambahkan modul baru.</p>

                        <!-- Existing modules list -->
                        <div class="space-y-2" id="existing-modules-list">
                            @forelse($course->modules as $m)
                                <div class="border rounded-lg p-3 flex items-start justify-between">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="inline-block px-2 py-0.5 text-xs rounded bg-gray-100">#{{ $m->order_no }}</span>
                                            <span class="font-medium">{{ $m->title }}</span>
                                            <span class="text-xs text-gray-500">({{ strtoupper($m->type) }})</span>
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $m->file_name }} @if($m->duration) • {{ $m->formatted_duration }} @endif</div>
                                    </div>
                                    <button type="button" data-id="{{ $m->id }}" class="btn-remove-existing px-3 py-1 text-sm border border-red-300 text-red-600 rounded hover:bg-red-50">Remove</button>
                                </div>
                            @empty
                                <div class="text-sm text-gray-500">Belum ada modul.</div>
                            @endforelse
                        </div>

                        <input type="hidden" id="modules-delete-ids" name="modules_delete_ids" value="">

                        <hr class="my-4">

                        <!-- Add new modules -->
                        <div>
                            <h3 class="text-md font-semibold mb-2">Tambah Modul Baru</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Modul</label>
                                    <select id="new-mod-type" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                        <option value="video">Video</option>
                                        <option value="pdf">PDF</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                                    <input type="number" id="new-mod-order" min="1" value="{{ max(1, ($course->modules->max('order_no') ?? 0) + 1) }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Judul</label>
                                <input type="text" id="new-mod-title" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Judul modul">
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                                <textarea id="new-mod-desc" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Deskripsi modul"></textarea>
                            </div>
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                                <input type="file" id="new-mod-file" class="w-full px-3 py-2 border border-gray-300 rounded-lg" accept="video/mp4,video/webm,video/ogg,application/pdf">
                            </div>
                            <div class="mt-3 flex justify-end">
                                <button type="button" id="btn-queue-module" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Tambahkan ke Daftar</button>
                            </div>

                            <div class="mt-4">
                                <h4 class="text-sm font-semibold mb-2">Modul Baru (antrian)</h4>
                                <div id="new-modules-list" class="space-y-2"></div>
                            </div>

                            <input type="hidden" name="modules_payload_new" id="modules-payload-new">
                            <div id="module-file-bucket" style="display:none"></div>
                        </div>
                    </div>

                </div>

                <!-- Discount Percent & Date Range -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="discount_percent" class="block text-sm font-medium text-gray-700 mb-2">Discount Percent (%)</label>
                        <input type="number" name="discount_percent" id="discount_percent" min="1" max="100" step="1"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('discount_percent') border-red-500 @enderror"
                               value="{{ old('discount_percent', $course->discount_percent) }}" placeholder="0">
                        <div class="text-xs text-gray-500">Isi 1-100. Tidak boleh 0.</div>
                        @error('discount_percent')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="discount_start" class="block text-sm font-medium text-gray-700 mb-2">Discount Start</label>
                        <input type="date" name="discount_start" id="discount_start"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('discount_start') border-red-500 @enderror"
                               value="{{ old('discount_start', $course->discount_start ? \Carbon\Carbon::parse($course->discount_start)->format('Y-m-d') : '') }}">
                        @error('discount_start')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="discount_end" class="block text-sm font-medium text-gray-700 mb-2">Discount End</label>
                        <input type="date" name="discount_end" id="discount_end"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('discount_end') border-red-500 @enderror"
                               value="{{ old('discount_end', $course->discount_end ? \Carbon\Carbon::parse($course->discount_end)->format('Y-m-d') : '') }}">
                        @error('discount_end')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.courses.show', $course) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update Course
                    </button>
                </div>
            </form>
        </div>
    </main>

<!-- Inline Toast Container (for validations) -->
<div aria-live="polite" aria-atomic="true" class="position-relative">
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
        <div id="inlineValidationToast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3500">
            <div class="d-flex">
                <div id="inlineValidationToastBody" class="toast-body"></div>
                <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <!-- Fallback container for when Bootstrap Toast is unavailable -->
    <div id="simpleToastContainer" class="simple-toast-container" style="display:none;"></div>
    <style>
        .simple-toast-container{position:fixed;top:12px;right:12px;z-index:1080;display:flex;flex-direction:column;gap:8px}
        .simple-toast{padding:10px 14px;border-radius:8px;color:#fff;box-shadow:0 6px 18px rgba(0,0,0,.18);font-size:14px}
        .simple-toast.success{background:#16a34a}
        .simple-toast.danger{background:#dc2626}
        .simple-toast.warning{background:#f59e0b}
    </style>
</div>

<script>
(function(){
    const deleteInput = document.getElementById('modules-delete-ids');
    const existingList = document.getElementById('existing-modules-list');
    const queued = [];
    const payloadInput = document.getElementById('modules-payload-new');
    const fileBucket = document.getElementById('module-file-bucket');
    const btnQueue = document.getElementById('btn-queue-module');
    const listNew = document.getElementById('new-modules-list');
    const typeSel = document.getElementById('new-mod-type');
    const orderInp = document.getElementById('new-mod-order');
    const titleInp = document.getElementById('new-mod-title');
    const descInp = document.getElementById('new-mod-desc');
    let fileInp = document.getElementById('new-mod-file');

    function setDeleteIds(ids){ deleteInput.value = ids.join(','); }
    function getDeleteIds(){ return (deleteInput.value||'').split(/[,\s]+/).filter(Boolean).map(v=>parseInt(v,10)||0); }
    function updatePayload(){ try{ payloadInput.value = JSON.stringify(queued); }catch(_){ payloadInput.value='[]'; } }
    function renderQueued(){
        listNew.innerHTML = '';
        queued.forEach((m, i) => {
            const el = document.createElement('div');
            el.className = 'border rounded p-2 flex items-center justify-between';
            el.innerHTML = `<div><span class="text-xs bg-gray-100 rounded px-2 py-0.5">#${m.order}</span> <span class="font-medium">${m.title}</span> <span class="text-xs text-gray-500">(${m.type.toUpperCase()})</span><div class="text-xs text-gray-500">${m.filename}</div></div><button type="button" class="px-3 py-1 text-sm border border-red-300 text-red-600 rounded hover:bg-red-50">Remove</button>`;
            el.querySelector('button').addEventListener('click', ()=>{ queued.splice(i,1); renderQueued(); updatePayload(); });
            listNew.appendChild(el);
        });
    }

    if (existingList) {
        existingList.querySelectorAll('.btn-remove-existing').forEach(btn => {
            btn.addEventListener('click', ()=>{
                const id = parseInt(btn.dataset.id,10)||0;
                if (!id) return;
                const ids = getDeleteIds();
                if (!ids.includes(id)) ids.push(id);
                setDeleteIds(ids);
                btn.disabled = true;
                btn.textContent = 'Marked';
            });
        });
    }

    function bindFileInput(el){ /* placeholder for future features */ }
    bindFileInput(fileInp);

    if (btnQueue) {
        btnQueue.addEventListener('click', ()=>{
            const type = (typeSel.value||'video');
            const order = parseInt(orderInp.value||'1',10)||1;
            const title = (titleInp.value||'').trim();
            const desc = (descInp.value||'').trim();
            const file = fileInp.files && fileInp.files[0];
            if (!title || title.length<3) { showInlineToast('Judul minimal 3 karakter', 'danger'); return; }
            if (!file) { showInlineToast('Pilih file modul', 'danger'); return; }
            const uid = 'm'+Date.now().toString(36)+Math.random().toString(36).slice(2,8);
            // move file input into bucket so it submits
            const originalContainer = fileInp.parentNode;
            fileInp.name = `module_files[${uid}]`;
            fileBucket.appendChild(fileInp);
            // recreate a clean file input
            const newInput = document.createElement('input');
            newInput.type = 'file';
            newInput.id = 'new-mod-file';
            newInput.accept = 'video/mp4,video/webm,video/ogg,application/pdf';
            newInput.className = 'w-full px-3 py-2 border border-gray-300 rounded-lg';
            fileInp = newInput;
            if (originalContainer) { originalContainer.appendChild(newInput); }
            bindFileInput(fileInp);

            queued.push({ type, order, title, subtitle: desc, filename: file.name, uid });
            renderQueued();
            updatePayload();
            // reset fields except order
            titleInp.value='';
            descInp.value='';
        });
    }
})();
</script>

<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview for Replace Image (Optional)
    try {
        var imgInput = document.getElementById('image');
        var prevCont = document.getElementById('imagePreviewContainer');
        var prevImg = document.getElementById('imagePreview');
        var prevInfo = document.getElementById('imagePreviewInfo');
        var prevClear = document.getElementById('imagePreviewClear');
        var currentUrl = null;
        function clearPreview(){
            if(currentUrl){ try{ URL.revokeObjectURL(currentUrl); }catch(_){} currentUrl = null; }
            if(prevImg){ prevImg.removeAttribute('src'); }
            if(prevCont){ prevCont.style.display = 'none'; }
            if(imgInput){ imgInput.value = ''; }
            if(prevInfo){ prevInfo.textContent = ''; }
        }
        if(prevClear){ prevClear.addEventListener('click', clearPreview); }
        if(imgInput){
            imgInput.addEventListener('change', function(){
                var f = imgInput.files && imgInput.files[0];
                if(!f){ clearPreview(); return; }
                if(!/^image\//.test(f.type||'')){
                    clearPreview();
                    window.showInlineToast && window.showInlineToast('File harus bertipe gambar', 'warning');
                    return;
                }
                try{
                    if(currentUrl){ URL.revokeObjectURL(currentUrl); }
                    currentUrl = URL.createObjectURL(f);
                    if(prevImg){
                        prevImg.onload = function(){
                            try{ URL.revokeObjectURL(currentUrl); }catch(_){}
                            currentUrl = null;
                        };
                        prevImg.src = currentUrl;
                    }
                    if(prevCont){ prevCont.style.display = 'block'; }
                    if(prevInfo){
                        var kb = Math.round((f.size||0)/1024);
                        prevInfo.textContent = (f.name||'') + ' • ' + kb + ' KB';
                    }
                }catch(e){ clearPreview(); }
            });
        }
    } catch(e) {}

    // Toast helper
    window.showInlineToast = function(message, type){
        try{
            var el = document.getElementById('inlineValidationToast');
            var body = document.getElementById('inlineValidationToastBody');
            if(el && body){
                // reset bg classes
                el.classList.remove('text-bg-success','text-bg-danger','text-bg-warning');
                var cls = type==='danger' ? 'text-bg-danger' : (type==='warning' ? 'text-bg-warning' : 'text-bg-success');
                el.classList.add(cls);
                body.textContent = message;
                if(window.bootstrap && window.bootstrap.Toast){
                    var t = window.bootstrap.Toast.getOrCreateInstance(el);
                    t.show();
                    return;
                }
            }
        }catch(e){}
        // Fallback simple toast
        try{
            var cont = document.getElementById('simpleToastContainer');
            if(cont){
                cont.style.display='flex';
                var n = document.createElement('div');
                n.className = 'simple-toast '+(type||'success');
                n.textContent = message;
                cont.appendChild(n);
                setTimeout(function(){ n.remove(); if(!cont.children.length){ cont.style.display='none'; } }, 3500);
            }
        }catch(e){}
    }
    // Normalize numeric fields on submit to avoid validation issues
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(ev){
            const priceEl = document.getElementById('price');
            if (priceEl) {
                const digits = (priceEl.value||'').toString().replace(/[^0-9]/g,'');
                if (digits) priceEl.value = digits;
            }
            const durationEl = document.getElementById('duration');
            if (durationEl) {
                const val = parseInt((durationEl.value||'').toString(),10)||0;
                durationEl.value = Math.max(0, val);
            }
        });
    }

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
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' }
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
            placeholder: 'Tulis deskripsi course yang menarik dengan format yang kaya...'
        })
        .then(editor => {
            window.editor = editor;
            
            // Sync with textarea on form submit
            const form2 = document.querySelector('form');
            if (form2) {
                form2.addEventListener('submit', function() {
                    const textarea = document.querySelector('#description');
                    textarea.value = editor.getData();
                });
            }
        })
        .catch(error => {
            console.error('Error initializing CKEditor:', error);
        });
});
</script>

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

.ck-editor__editable {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    padding: 16px;
}

.ck-editor__editable:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.ck-toolbar {
    border: 1px solid #d1d5db;
    border-bottom: none;
    border-radius: 8px 8px 0 0;
    background: #f9fafb;
}

.ck-toolbar__separator {
    background: #d1d5db;
}

.ck-button {
    color: #374151;
    border-radius: 4px;
    margin: 2px;
}

.ck-button:hover {
    background: #e5e7eb;
}

.ck-button.ck-on {
    background: #3b82f6;
    color: white;
}

.ck-button.ck-on:hover {
    background: #2563eb;
}

.ck-dropdown__button {
    border: 1px solid #d1d5db;
    border-radius: 4px;
    background: white;
}

.ck-dropdown__button:hover {
    background: #f3f4f6;
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
    border-color: #3b82f6;
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
}

.btn-primary {
    background-color: #3b82f6;
    border-color: #3b82f6;
}

.btn-primary:hover {
    background-color: #2563eb;
    border-color: #2563eb;
}
</style>
@endsection
