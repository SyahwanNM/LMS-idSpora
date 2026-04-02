@extends('layouts.app')

@section('title', 'Create Course')

@section('content')
    @include('partials.navbar-admin-course')

    <div class="min-h-screen bg-white p-6 md:p-10">
        <!-- Header -->
        <div class="max-w-4xl mx-auto mb-8">
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.courses.index') }}" class="p-2 border rounded-lg hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create Course</h1>
                    <p class="text-gray-500 text-sm">Setup course details sebelum dipublikasi</p>
                </div>
            </div>
            <div class="text-xs text-gray-400">Course Builder / Create Course</div>
        </div>

        <!-- Main Content -->
        <main class="max-w-4xl mx-auto">
            <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

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
                                value="{{ old('name') }}" placeholder="Masukkan Judul Course">
                        </div>

                        <!-- Level & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level Course</label>
                                <select name="level" id="level" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                    <option value="">Select Level</option>
                                    <option value="beginner" {{ old('level') == 'beginner' ? 'selected' : '' }}>Beginner
                                    </option>
                                    <option value="intermediate" {{ old('level') == 'intermediate' ? 'selected' : '' }}>
                                        Intermediate</option>
                                    <option value="advanced" {{ old('level') == 'advanced' ? 'selected' : '' }}>Advanced
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status" id="status" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="archive" {{ old('status') == 'archive' ? 'selected' : '' }}>Archive
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Category & Template -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="category_id"
                                    class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                                <select name="category_id" id="category_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="template_id" class="block text-sm font-medium text-gray-700 mb-2">Course
                                    Template</label>
                                <select name="template_id" id="template_id"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                    <option value="">-- Tanpa template --</option>
                                    @foreach($templates as $template)
                                        <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                            {{ $template->name }} ({{ $template->modules_count }} modules)
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Pilih template untuk pre-populate struktur modul
                                    course</p>
                            </div>
                        </div>

                        <!-- Trainer Assignment -->
                        <div>
                            <label for="trainer_id" class="block text-sm font-medium text-gray-700 mb-2">Trainer</label>
                            <select name="trainer_id" id="trainer_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                <option value="">-- Pilih Trainer --</option>
                                @foreach($trainers as $trainer)
                                    <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
                                        {{ $trainer->name }}{{ $trainer->email ? ' (' . $trainer->email . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Course hanya muncul di dashboard trainer jika trainer
                                dipilih.</p>
                        </div>

                        <!-- Harga -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Harga</label>
                            <input type="text" name="price" id="price" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="{{ old('price', 0) }}" placeholder="0">
                            <p class="mt-1 text-xs text-gray-500">Masukkan 0 untuk course gratis</p>
                        </div>

                        <!-- Akses Course Gratis -->
                        <div>
                            <label for="free_access_mode" class="block text-sm font-medium text-gray-700 mb-2">Akses Course
                                Gratis</label>
                            <select name="free_access_mode" id="free_access_mode"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                <option value="all" {{ old('free_access_mode') === 'all' ? 'selected' : '' }}>Buka semua
                                    materi</option>
                                <option value="limit_2" {{ old('free_access_mode', 'limit_2') === 'limit_2' ? 'selected' : '' }}>Hanya 2 modul/video yang dibuka</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Berlaku jika harga course = 0 (gratis).</p>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi
                                Course</label>
                            <textarea name="description" id="description" rows="6"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Deskripsi Course">{{ old('description') }}</textarea>
                        </div>

                        <!-- Intro Media -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Intro Media (Video/Image)</label>
                            <input type="file" name="image" accept="image/*,video/mp4,video/webm,video/ogg" required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-300 rounded-lg">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, MP4. Digunakan untuk intro course.</p>
                        </div>

                        <!-- Thumbnail -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Course</label>
                            <input type="file" name="card_thumbnail" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-300 rounded-lg">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WebP</p>
                        </div>

                        <!-- Hidden inputs for other required fields -->
                        <input type="hidden" name="duration" value="0">
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-4 mt-12 pb-10">
                    <a href="{{ route('admin.courses.index') }}"
                        class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">Cancel</a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition">Create
                        Course</button>
                </div>
            </form>
        </main>
    </div>
@endsection