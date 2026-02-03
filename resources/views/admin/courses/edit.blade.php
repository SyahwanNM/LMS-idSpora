@extends('layouts.app')

@section('title', 'Edit Course - ' . $course->name)

@section('content')
@include('partials.navbar-admin-course')

<div class="min-h-screen bg-white p-6 md:p-10">
    <!-- Header -->
    <div class="max-w-4xl mx-auto mb-8">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('admin.courses.index') }}" class="p-2 border rounded-lg hover:bg-gray-50 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-arrow-left" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8"/>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Course</h1>
                <p class="text-gray-500 text-sm">Atur detail course sebelum dipublikasi</p>
            </div>
        </div>
        <div class="text-xs text-gray-400">Course Builder / Edit Course</div>
    </div>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto">
        <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Formulir Pengaturan Course -->
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 mb-6">Formulir Pengaturan Course</h2>
                
                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 text-red-700">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="space-y-6">
                    <!-- Judul Course -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Judul Course</label>
                        <input type="text" name="name" id="name" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                               value="{{ old('name', $course->name) }}" placeholder="Masukkan Judul Course">
                    </div>

                    <!-- Level & Status (And Category for Data Integrity) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level Course</label>
                            <select name="level" id="level" required 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                <option value="">Select Level</option>
                                <option value="beginner" {{ old('level', $course->level) == 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('level', $course->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level', $course->level) == 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                            <select name="status" id="status" required 
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                <option value="">Select Status</option>
                                <option value="active" {{ old('status', $course->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="archive" {{ old('status', $course->status) == 'archive' ? 'selected' : '' }}>Archive</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Hidden/Visible Category to satisfy required -->
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                        <select name="category_id" id="category_id" required 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                             <option value="">Pilih Kategori</option>
                             @foreach($categories as $category)
                                 <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                             @endforeach
                        </select>
                    </div>

                    <!-- Harga -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                        <input type="text" name="price" id="price" required 
                               class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                               value="{{ old('price', $course->price) }}" placeholder="Masukkan Harga Course">
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi Course</label>
                        <textarea name="description" id="description" rows="6" 
                                  class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                  placeholder="Deskripsi Course">{!! old('description', strip_tags($course->description)) !!}</textarea>
                    </div>

                    <!-- Intro Media -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Intro Media (Video/Image)</label>
                        <div class="flex flex-col md:flex-row gap-4 items-start">
                            @if($course->media)
                                <div class="shrink-0">
                                    @if($course->media_type === 'video')
                                        <video src="{{ Storage::url($course->media) }}" controls class="h-32 w-48 object-cover rounded-lg border bg-black"></video>
                                    @else
                                        <img src="{{ Storage::url($course->media) }}" class="h-32 w-48 object-cover rounded-lg border" alt="Intro Media">
                                    @endif
                                </div>
                            @endif
                            <div class="w-full">
                                <input type="file" name="image" accept="image/*,video/mp4,video/webm,video/ogg" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-300 rounded-lg">
                                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, MP4. Digunakan untuk intro course.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Thumbnail -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Course</label>
                        <div class="flex items-center gap-4">
                            @if($course->card_thumbnail)
                                <div class="shrink-0">
                                    <img src="{{ Storage::url($course->card_thumbnail) }}" class="h-16 w-16 object-cover rounded-lg border" alt="Thumbnail">
                                </div>
                            @endif
                            <input type="file" name="card_thumbnail" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-300 rounded-lg">
                        </div>
                    </div>
                    
                    <!-- Hidden inputs for other required fields not in design (Duration, etc) -->
                    <input type="hidden" name="duration" value="{{ $course->duration ?? 1 }}">
                    <input type="hidden" name="discount_percent" value="{{ $course->discount_percent ?? 0 }}">
                </div>
            </div>

            <!-- Course Modules -->
            <div class="mb-10">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Course Modules</h2>

                <div id="existing-modules-list" class="space-y-8">
                    <!-- PDF Document -->
                    <div>
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                             <span class="p-2 rounded-lg bg-red-100 text-red-600">
                                <i class="bi bi-file-earmark-pdf-fill"></i>
                             </span>
                             PDF Document
                        </h3>
                        <div class="space-y-3">
                            @forelse($course->modules->where('type', 'pdf') as $m)
                                <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-white shadow-sm gap-4">
                                    <div class="shrink-0 w-12 h-12 rounded-lg flex items-center justify-center bg-gray-100 text-purple-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-file-earmark-text-fill" viewBox="0 0 16 16">
                                            <path d="M9.293 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V4.707A1 1 0 0 0 13.707 4L10 .293A1 1 0 0 0 9.293 0zM9.5 3.5v-2l3 3h-2a1 1 0 0 1-1-1zM4.5 9a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1zM4.5 11a.5.5 0 0 1 0-1h7a.5.5 0 0 1 0 1zM4.5 13a.5.5 0 0 1 0-1h4a.5.5 0 0 1 0 1z"/>
                                        </svg>
                                    </div>
                                    <div class="grow">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h5 class="text-sm font-bold text-gray-900 m-0">{{ $m->title }}</h5>
                                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-0.5 rounded font-medium">#{{ $m->order_no }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 m-0">PDF Document • {{ $m->file_name }}</p>
                                    </div>
                                    <button type="button" data-id="{{ $m->id }}" class="btn-remove-existing text-gray-400 hover:text-red-500 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                          <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                        </svg>
                                    </button>
                                </div>
                            @empty
                                <div class="text-sm text-gray-400 italic px-4 py-2 border border-dashed rounded-lg bg-gray-50">No PDF modules.</div>
                            @endforelse
                            <!-- New PDFs -->
                            <div id="new-pdf-list" class="space-y-3"></div>
                        </div>
                    </div>

                    <!-- Video Lesson -->
                    <div>
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                             <span class="p-2 rounded-lg bg-blue-100 text-blue-600">
                                <i class="bi bi-play-circle-fill"></i>
                             </span>
                             Video Lesson
                        </h3>
                        <div class="space-y-3">
                            @forelse($course->modules->where('type', 'video') as $m)
                                <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-white shadow-sm gap-4">
                                    <div class="shrink-0 w-12 h-12 rounded-lg flex items-center justify-center bg-gray-100 text-purple-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-play-circle-fill" viewBox="0 0 16 16">
                                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM6.79 5.093A.5.5 0 0 0 6 5.5v5a.5.5 0 0 0 .79.407l3.5-2.5a.5.5 0 0 0 0-.814l-3.5-2.5z"/>
                                        </svg>
                                    </div>
                                    <div class="grow">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h5 class="text-sm font-bold text-gray-900 m-0">{{ $m->title }}</h5>
                                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-0.5 rounded font-medium">#{{ $m->order_no }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 m-0">Video Lesson • {{ $m->file_name }}</p>
                                    </div>
                                    <button type="button" data-id="{{ $m->id }}" class="btn-remove-existing text-gray-400 hover:text-red-500 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                          <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                        </svg>
                                    </button>
                                </div>
                            @empty
                                <div class="text-sm text-gray-400 italic px-4 py-2 border border-dashed rounded-lg bg-gray-50">No Video lessons.</div>
                            @endforelse
                            <!-- New Videos -->
                            <div id="new-video-list" class="space-y-3"></div>
                        </div>
                    </div>

                    <!-- Quizzes -->
                    <div>
                        <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                             <span class="p-2 rounded-lg bg-purple-100 text-purple-600">
                                <i class="bi bi-pencil-square"></i>
                             </span>
                             Quizzes
                        </h3>
                        <div class="space-y-3">
                            @forelse($course->modules->where('type', 'quiz') as $m)
                                <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-white shadow-sm gap-4">
                                    <div class="shrink-0 w-12 h-12 rounded-lg flex items-center justify-center bg-gray-100 text-purple-600">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                            <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                            <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
                                        </svg>
                                    </div>
                                    <div class="grow">
                                        <div class="flex items-center gap-2 mb-1">
                                            <h5 class="text-sm font-bold text-gray-900 m-0">{{ $m->title }}</h5>
                                            <span class="bg-gray-200 text-gray-700 text-xs px-2 py-0.5 rounded font-medium">#{{ $m->order_no }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 m-0">Quiz • {{ $m->subtitle }}</p>
                                    </div>
                                    <button type="button" data-id="{{ $m->id }}" class="btn-remove-existing text-gray-400 hover:text-red-500 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-trash3" viewBox="0 0 16 16">
                                          <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                        </svg>
                                    </button>
                                </div>
                            @empty
                                <div class="text-sm text-gray-400 italic px-4 py-2 border border-dashed rounded-lg bg-gray-50">No Quizzes.</div>
                            @endforelse
                            <!-- New Quizzes -->
                            <div id="new-quiz-list" class="space-y-3"></div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="modules-delete-ids" name="modules_delete_ids" value="">
                <input type="hidden" name="modules_payload_new" id="modules-payload-new">
                <div id="module-file-bucket" style="display:none"></div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 mt-8">
                    <button type="button" onclick="openAddModal('pdf')" class="px-5 py-2.5 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition flex items-center gap-2">
                        <i class="bi bi-plus-lg"></i> Add PDF Module
                    </button>
                    <button type="button" onclick="openAddModal('video')" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                        <i class="bi bi-plus-lg"></i> Add Video
                    </button>
                    <button type="button" onclick="openQuizModal()" class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition flex items-center gap-2">
                        <i class="bi bi-plus-lg"></i> Add Kuis
                    </button>
                </div>
            </div>

            <!-- Footer Buttons -->
            <div class="flex justify-end gap-4 mt-12 pb-10">
                <a href="{{ route('admin.courses.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">Cancel</a>
                <button type="submit" class="px-6 py-2.5 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition">Save</button>
            </div>
        </form>
    </main>
</div>

<!-- Modal for Adding Generic Module (PDF/Video) -->
<div class="modal fade" id="addGenericModuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-xl border-0 shadow-lg">
            <div class="modal-header border-b-0 pb-0">
                <h5 class="modal-title font-bold text-lg" id="genericModalTitle">Add Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-6">
                <!-- Inputs handled by JS -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" id="new-mod-title" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Judul Modul">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea id="new-mod-desc" class="w-full px-3 py-2 border border-gray-300 rounded-lg" rows="2" placeholder="Deskripsi Singkat"></textarea>
                </div>
                <!-- Hidden inputs for logic -->
                <input type="hidden" id="new-mod-type" value="video">
                <!-- Fallback to 1 if empty -->
                <input type="hidden" id="new-mod-order" value="{{ max(1, ($course->modules->count() ?? 0) + 1) }}">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">File Upload</label>
                    <input type="file" id="new-mod-file" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100 border border-gray-300 rounded-lg">
                    <div class="text-xs text-gray-400 mt-1" id="fileHelpText">Supported formats: PDF, MP4</div>
                </div>
            </div>
            <div class="modal-footer border-t-0 pt-0">
                <button type="button" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="btn-queue-module" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm font-medium">Add Module</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Quiz (Reuse existing structure but clean up) -->
<div class="modal fade" id="addQuizModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-xl border-0 shadow-lg">
            <div class="modal-header">
                <h5 class="modal-title font-bold text-lg">Add Quiz Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Step 1: Overview -->
                <div id="quiz-step-overview" class="p-6">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quiz Title</label>
                        <input type="text" id="quiz-title" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="e.g. Final Assessment">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (Optional)</label>
                        <textarea id="quiz-desc" class="w-full px-3 py-2 border border-gray-300 rounded-lg" rows="2" placeholder="Brief description..."></textarea>
                    </div>
                    <div class="flex justify-between items-center mb-3">
                        <label class="font-medium text-sm text-gray-700">Questions List</label>
                        <button type="button" class="text-sm text-purple-600 hover:text-purple-800 font-medium" id="btn-add-question-step">
                            + Add Question
                        </button>
                    </div>
                    <div class="border rounded-lg p-4 bg-gray-50 min-h-[150px] max-h-[300px] overflow-y-auto">
                        <ul id="quiz-questions-list" class="space-y-2">
                             <li class="text-center text-gray-400 text-sm py-8">No questions added yet.</li>
                        </ul>
                    </div>
                </div>

                <!-- Step 2: Add/Edit Question -->
                <div id="quiz-step-question" class="p-6 d-none">
                    <h6 class="font-bold text-gray-900 mb-4" id="question-form-title">Add Question</h6>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Question Text</label>
                        <textarea id="q-text" class="w-full px-3 py-2 border border-gray-300 rounded-lg" rows="2"></textarea>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Options (Select correct answer)</label>
                        <div class="space-y-3" id="q-options-container">
                            @for($i=0; $i<4; $i++)
                            <div class="flex items-center gap-2">
                                <input class="w-4 h-4 text-purple-600 focus:ring-purple-500 border-gray-300" type="radio" name="q-correct" value="{{$i}}" {{$i==0?'checked':''}}>
                                <input type="text" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm" placeholder="Option {{$i+1}}">
                            </div>
                            @endfor
                        </div>
                    </div>
                    <div class="flex justify-end gap-2">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 text-sm" id="btn-cancel-question">Cancel</button>
                        <button type="button" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm" id="btn-save-question">Save Question</button>
                    </div>
                </div>

                <!-- Step 3: Review -->
                <div id="quiz-step-review" class="p-6 d-none">
                     <div class="text-center mb-6">
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-2">
                            <i class="bi bi-check-lg text-xl"></i>
                        </div>
                        <h5 class="font-bold text-gray-900">Review Quiz</h5>
                        <p class="text-sm text-gray-500">Please review before saving.</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                         <div class="grid grid-cols-3 gap-2 text-sm">
                             <span class="font-medium text-gray-500">Title:</span>
                             <span class="col-span-2 text-gray-900 font-semibold" id="review-title">-</span>
                             
                             <span class="font-medium text-gray-500">Count:</span>
                             <span class="col-span-2 text-gray-900" id="review-count">0 items</span>
                         </div>
                    </div>
                    <div id="review-questions-detail" class="text-sm space-y-3 max-h-60 overflow-y-auto"></div>
                </div>
            </div>
            <div class="modal-footer bg-gray-50 rounded-b-xl">
                <button type="button" class="btn btn-sm btn-link text-gray-500 no-underline" id="btn-quiz-back" style="display:none;">Back</button>
                <button type="button" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-800 font-medium" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700" id="btn-quiz-next">Next: Review</button>
                <button type="button" class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700" id="btn-quiz-save" style="display:none;">Save Quiz</button>
            </div>
        </div>
    </div>
</div>

{{-- Inline Toast and Scripts --}}
<div aria-live="polite" aria-atomic="true" class="position-relative">
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1080">
        <div id="inlineValidationToast" class="toast align-items-center border-0 text-white" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div id="inlineValidationToastBody" class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
</div>

<script>
    // --- Helper for Toast ---
    window.showInlineToast = function(message, type){
        const el = document.getElementById('inlineValidationToast');
        const body = document.getElementById('inlineValidationToastBody');
        if(el && body){
            el.className = `toast align-items-center border-0 text-white ${type==='danger'?'bg-red-500':(type==='warning'?'bg-yellow-500':'bg-green-500')}`;
            body.textContent = message;
            if(window.bootstrap && window.bootstrap.Toast){
                const t = window.bootstrap.Toast.getOrCreateInstance(el);
                t.show();
            }
        } else {
            alert(message);
        }
    };

    // --- Modal Helpers ---
    window.openAddModal = function(type) {
        const modalEl = document.getElementById('addGenericModuleModal');
        const titleEl = document.getElementById('genericModalTitle');
        const typeInput = document.getElementById('new-mod-type');
        const fileInp = document.getElementById('new-mod-file');
        const helpText = document.getElementById('fileHelpText');

        if(window.bootstrap && modalEl){
            typeInput.value = type;
            titleEl.textContent = type === 'pdf' ? 'Add PDF Module' : 'Add Video Lesson';
            fileInp.accept = type === 'pdf' ? 'application/pdf' : 'video/mp4,video/webm,video/ogg';
            helpText.textContent = type === 'pdf' ? 'Supported: PDF' : 'Supported: MP4, WebM';
            
            // Clear inputs
            document.getElementById('new-mod-title').value = '';
            document.getElementById('new-mod-desc').value = '';
            fileInp.value = '';

            const m = new window.bootstrap.Modal(modalEl);
            m.show();
        }
    };

    window.openQuizModal = function() {
        if (window.bootstrap) {
            const m = new window.bootstrap.Modal(document.getElementById('addQuizModal'));
            m.show();
            if(typeof resetQuizDraft === 'function') resetQuizDraft();
        }
    };

    // --- Module Logic (Preserved) ---
    (function(){
        const deleteInput = document.getElementById('modules-delete-ids');
        const existingList = document.getElementById('existing-modules-list');
        const queued = [];
        const payloadInput = document.getElementById('modules-payload-new');
        const fileBucket = document.getElementById('module-file-bucket');
        const listNewPdf = document.getElementById('new-pdf-list');
        const listNewVideo = document.getElementById('new-video-list');
        const listNewQuiz = document.getElementById('new-quiz-list');
        
        // Count existing modules to determine next order
        const existingCount = {{ $course->modules->count() }};

        // Inputs in Generic Modal
        const btnQueue = document.getElementById('btn-queue-module');
        const typeSel = document.getElementById('new-mod-type'); 
        const titleInp = document.getElementById('new-mod-title');
        const descInp = document.getElementById('new-mod-desc');
        let fileInp = document.getElementById('new-mod-file');
        
        // Common Order logic
        function getNextOrder() {
             return existingCount + queued.length + 1; 
        }

        function setDeleteIds(ids){ deleteInput.value = ids.join(','); }
        function getDeleteIds(){ return (deleteInput.value||'').split(/[,\s]+/).filter(Boolean).map(v=>parseInt(v,10)||0); }
        function updatePayload(){ try{ payloadInput.value = JSON.stringify(queued); }catch(_){ payloadInput.value='[]'; } }
        
        function renderQueued(){
            if(listNewPdf) listNewPdf.innerHTML = '';
            if(listNewVideo) listNewVideo.innerHTML = '';
            if(listNewQuiz) listNewQuiz.innerHTML = '';

            queued.forEach((m, i) => {
                const el = document.createElement('div');
                el.className = 'flex items-center p-4 border border-purple-200 rounded-lg bg-purple-50 gap-4';
                
                let icon = '';
                let targetList = null;

                if(m.type === 'pdf') {
                    icon = '<i class="bi bi-file-earmark-text-fill"></i>';
                    targetList = listNewPdf;
                }
                else if(m.type === 'video') {
                    icon = '<i class="bi bi-play-circle-fill"></i>';
                    targetList = listNewVideo;
                }
                else {
                    icon = '<i class="bi bi-pencil-square"></i>';
                    targetList = listNewQuiz;
                }

                el.innerHTML = `
                    <div class="shrink-0 w-12 h-12 rounded-lg flex items-center justify-center bg-white text-purple-600 shadow-sm text-xl">${icon}</div>
                    <div class="grow">
                        <div class="flex items-center gap-2 mb-1">
                            <h5 class="text-sm font-bold text-gray-900 m-0">${m.title}</h5>
                            <span class="bg-purple-200 text-purple-800 text-xs px-2 py-0.5 rounded font-medium">New</span>
                        </div>
                        <p class="text-xs text-gray-500 m-0">${m.type.toUpperCase()}</p>
                    </div>
                    <button type="button" class="btn-remove-queue text-red-500 hover:text-red-700 transition"><i class="bi bi-x-lg"></i></button>
                `;
                el.querySelector('.btn-remove-queue').addEventListener('click', ()=>{ queued.splice(i,1); renderQueued(); updatePayload(); });
                
                if(targetList) targetList.appendChild(el);
            });
        }

        // Handle delete existing
        if (existingList) {
            existingList.querySelectorAll('.btn-remove-existing').forEach(btn => {
                btn.addEventListener('click', ()=>{
                    if(!confirm('Delete this module?')) return;
                    const id = parseInt(btn.dataset.id,10)||0;
                    if (!id) return;
                    const ids = getDeleteIds();
                    if (!ids.includes(id)) ids.push(id);
                    setDeleteIds(ids);
                    // Visualize removal
                    btn.closest('div.flex').classList.add('opacity-50', 'bg-red-50');
                    btn.disabled = true;
                });
            });
        }

        // Handle Add Generic Module
        if(btnQueue){
            btnQueue.addEventListener('click', () => {
                const type = typeSel.value; 
                const title = (titleInp.value||'').trim();
                const desc = (descInp.value||'').trim();
                const file = fileInp.files && fileInp.files[0];
                const order = getNextOrder();

                if (!title) { showInlineToast('Judul wajib diisi', 'warning'); return; }
                if (!file) { showInlineToast('File wajib diupload', 'warning'); return; }

                const uid = 'm'+Date.now().toString(36);
                
                // Move file to bucket
                const originalContainer = fileInp.parentNode;
                fileInp.name = `module_files[${uid}]`;
                fileBucket.appendChild(fileInp);
                
                // Create replacement input
                const newInput = document.createElement('input');
                newInput.type = 'file';
                newInput.id = 'new-mod-file';
                newInput.className = fileInp.className; 
                newInput.accept = (type === 'pdf' ? 'application/pdf' : 'video/mp4,video/webm,video/ogg');
                fileInp = newInput;
                if(originalContainer) originalContainer.appendChild(newInput);

                queued.push({ type, order, title, subtitle: desc, filename: file.name, uid });
                renderQueued();
                updatePayload();

                // Hide Modal
                const modalEl = document.getElementById('addGenericModuleModal');
                const m = bootstrap.Modal.getInstance(modalEl);
                if(m) m.hide();
            });
        }
        
        // --- QUIZ LOGIC (Simplified & Adapted) ---
        let quizDraft = { title: '', desc: '', questions: [] };
        let quizStep = 1;
        let editingQuestionIdx = -1;

        window.resetQuizDraft = function(){
             quizDraft = { title: '', desc: '', questions: [] };
             quizStep = 1;
             updateQuizUI();
             document.getElementById('quiz-title').value = '';
             document.getElementById('quiz-desc').value = '';
        }

        function updateQuizUI(){
             ['quiz-step-overview', 'quiz-step-question', 'quiz-step-review'].forEach((id, idx) => {
                 document.getElementById(id).classList.toggle('d-none', (idx + 1) !== quizStep);
             });
             document.getElementById('btn-quiz-back').style.display = (quizStep > 1) ? 'block' : 'none';
             document.getElementById('btn-quiz-next').style.display = (quizStep < 3) ? 'block' : 'none';
             document.getElementById('btn-quiz-save').style.display = (quizStep === 3) ? 'block' : 'none';
             renderQuizQuestionsList();
        }

        function renderQuizQuestionsList(){
            const list = document.getElementById('quiz-questions-list');
            list.innerHTML = '';
            if(quizDraft.questions.length === 0){
                list.innerHTML = '<li class="text-center text-gray-400 text-sm py-4">No questions added yet.</li>';
                return;
            }
            quizDraft.questions.forEach((q, i) => {
                const li = document.createElement('li');
                li.className = 'flex justify-between items-center bg-white border p-3 rounded-lg text-sm';
                li.innerHTML = `
                    <span class="truncate pr-4"><span class="font-bold mr-2">Q${i+1}.</span> ${q.text}</span>
                    <div class="shrink-0 flex gap-2">
                        <button type="button" class="text-blue-600 hover:text-blue-800 btn-edit-q" data-idx="${i}"><i class="bi bi-pencil"></i></button>
                        <button type="button" class="text-red-600 hover:text-red-800 btn-del-q" data-idx="${i}"><i class="bi bi-trash"></i></button>
                    </div>`;
                list.appendChild(li);
            });
            // Bind
            list.querySelectorAll('.btn-edit-q').forEach(b => b.addEventListener('click', () => editQuestion(parseInt(b.dataset.idx))));
            list.querySelectorAll('.btn-del-q').forEach(b => b.addEventListener('click', () => {
                quizDraft.questions.splice(parseInt(b.dataset.idx), 1);
                renderQuizQuestionsList();
            }));

            // Update Review logic similar to before...
            if(quizStep === 3){
                document.getElementById('review-title').textContent = document.getElementById('quiz-title').value || '-';
                document.getElementById('review-count').textContent = quizDraft.questions.length + ' Questions';
                const det = document.getElementById('review-questions-detail');
                det.innerHTML = quizDraft.questions.map((q,i)=>`
                    <div class="mb-3 border-b pb-2 last:border-0">
                        <div class="font-bold text-gray-800 mb-1">Q${i+1}: ${q.text}</div>
                        <ul class="pl-4 list-disc text-gray-500">
                           ${q.options.map((o, idx)=>`<li class="${idx==q.correctIndex?'text-green-600 font-bold':''}">${o}</li>`).join('')}
                        </ul>
                    </div>
                 `).join('');
            }
        }

        function editQuestion(idx){
            editingQuestionIdx = idx;
            const q = (idx === -1) ? { text:'', options:['','','',''], correctIndex:0 } : quizDraft.questions[idx];
            document.getElementById('q-text').value = q.text;
            const inputs = document.querySelectorAll('#q-options-container input[type=text]');
            inputs.forEach((inp, i) => inp.value = q.options[i] || '');
            const radios = document.querySelectorAll('input[name="q-correct"]');
            radios.forEach((r, i) => r.checked = (i === parseInt(q.correctIndex)));
            
            document.getElementById('question-form-title').textContent = (idx===-1 ? 'Add Question' : 'Edit Question');
            quizStep = 2; updateQuizUI();
        }

        document.getElementById('btn-add-question-step').addEventListener('click', () => editQuestion(-1));
        document.getElementById('btn-cancel-question').addEventListener('click', () => { quizStep = 1; updateQuizUI(); });
        document.getElementById('btn-save-question').addEventListener('click', () => {
             // Save question logic
             const text = document.getElementById('q-text').value.trim();
             if(!text) { alert('Question required'); return; }
             const opts = [];
             document.querySelectorAll('#q-options-container input[type=text]').forEach(i => opts.push(i.value.trim()));
             if(opts.some(o=>!o)) { alert('Options required'); return; }
             const correct = parseInt(document.querySelector('input[name="q-correct"]:checked').value);
             
             const q = { text, options: opts, correctIndex: correct, points: 10 };
             if(editingQuestionIdx === -1) quizDraft.questions.push(q);
             else quizDraft.questions[editingQuestionIdx] = q;
             quizStep = 1; updateQuizUI();
        });

        document.getElementById('btn-quiz-next').addEventListener('click', () => {
            if(quizDraft.questions.length === 0) { alert('Add at least one question'); return; }
            quizStep = 3; updateQuizUI();
        });
        document.getElementById('btn-quiz-back').addEventListener('click', ()=>{
             if(quizStep > 1) quizStep--; updateQuizUI();
        });
        
        document.getElementById('btn-quiz-save').addEventListener('click', () => {
             const title = document.getElementById('quiz-title').value.trim() || 'Untitled Quiz';
             const desc = document.getElementById('quiz-desc').value.trim();
             const order = getNextOrder();

             quizDraft.title = title;
             quizDraft.desc = desc;
             
             queued.push({ 
                 type: 'quiz', 
                 order: order, 
                 title: title, 
                 subtitle: desc,
                 filename: quizDraft.questions.length + ' Questions', 
                 uid: 'q'+Date.now(),
                 data: JSON.parse(JSON.stringify(quizDraft)) 
             });
             renderQueued();
             updatePayload();
             
             const m = bootstrap.Modal.getInstance(document.getElementById('addQuizModal'));
             if(m) m.hide();
        });

    })();
</script>
@endsection
