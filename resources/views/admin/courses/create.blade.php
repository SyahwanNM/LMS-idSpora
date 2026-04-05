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
            <form id="admin-course-create-form" action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" novalidate>
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
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Judul Course <span class="text-red-600">*</span></label>
                            <input type="text" name="name" id="name" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="{{ old('name') }}" placeholder="Masukkan Judul Course">
                            <p id="name_error" class="mt-1 text-xs text-red-600 hidden"></p>
                        </div>

                        <!-- Level & Status -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Level Course <span class="text-red-600">*</span></label>
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
                                <p id="level_error" class="mt-1 text-xs text-red-600 hidden"></p>
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status <span class="text-red-600">*</span></label>
                                <select name="status" id="status" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                    <option value="">Select Status</option>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="archive" {{ old('status') == 'archive' ? 'selected' : '' }}>Archive
                                    </option>
                                </select>
                                <p id="status_error" class="mt-1 text-xs text-red-600 hidden"></p>
                            </div>
                        </div>

                        <!-- Category & Template -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="category_id"
                                    class="block text-sm font-medium text-gray-700 mb-2">Kategori <span class="text-red-600">*</span></label>
                                <select name="category_id" id="category_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <p id="category_id_error" class="mt-1 text-xs text-red-600 hidden"></p>
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
                            <label for="trainer_id" class="block text-sm font-medium text-gray-700 mb-2">Trainer <span class="text-red-600">*</span></label>
                            <select name="trainer_id" id="trainer_id" required
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
                            <p id="trainer_id_error" class="mt-1 text-xs text-red-600 hidden"></p>
                        </div>

                        <!-- Harga -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Harga <span class="text-red-600">*</span></label>
                            <input type="text" name="price" id="price" required inputmode="numeric"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="{{ old('price', 0) }}" placeholder="0">
                            <p class="mt-1 text-xs text-gray-500">Masukkan 0 untuk course gratis</p>
                            <p id="price_error" class="mt-1 text-xs text-red-600 hidden"></p>
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
                            <label class="block text-sm font-medium text-gray-700 mb-2">Intro Media (Video/Image) <span class="text-red-600">*</span></label>
                            <input type="file" name="image" id="image" accept="image/*,video/mp4,video/webm,video/ogg" required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-300 rounded-lg">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, MP4. Digunakan untuk intro course.</p>
                            <p id="image_error" class="mt-1 text-xs text-red-600 hidden"></p>
                        </div>

                        <!-- Thumbnail -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Course <span class="text-red-600">*</span></label>
                            <input type="file" name="card_thumbnail" id="card_thumbnail" accept="image/*" required
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-300 rounded-lg">
                            <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, WebP</p>
                            <p id="card_thumbnail_error" class="mt-1 text-xs text-red-600 hidden"></p>
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

    <script>
        (function() {
            const form = document.getElementById('admin-course-create-form');
            if (!form) return;

            const allowedIntroMimes = new Set([
                'image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp',
                'video/mp4', 'video/webm', 'video/ogg'
            ]);

            const allowedCardThumbMimes = new Set([
                'image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'
            ]);

            function getEl(id) {
                return document.getElementById(id);
            }

            function setError(fieldId, message) {
                const input = getEl(fieldId);
                const errorEl = getEl(fieldId + '_error');

                if (errorEl) {
                    if (message) {
                        errorEl.textContent = message;
                        errorEl.classList.remove('hidden');
                    } else {
                        errorEl.textContent = '';
                        errorEl.classList.add('hidden');
                    }
                }

                if (input) {
                    if (message) {
                        input.classList.add('border-red-500');
                    } else {
                        input.classList.remove('border-red-500');
                    }
                }
            }

            function normalizeDigits(value) {
                return String(value || '').replace(/[^0-9]/g, '');
            }

            function validate() {
                let firstInvalid = null;

                const name = getEl('name');
                const level = getEl('level');
                const status = getEl('status');
                const categoryId = getEl('category_id');
                const trainerId = getEl('trainer_id');
                const price = getEl('price');
                const image = getEl('image');
                const cardThumb = getEl('card_thumbnail');

                const nameVal = (name?.value || '').trim();
                if (nameVal.length === 0) {
                    setError('name', 'Judul course wajib diisi.');
                    firstInvalid ||= name;
                } else {
                    setError('name', '');
                }

                const levelVal = (level?.value || '').trim();
                if (!['beginner', 'intermediate', 'advanced'].includes(levelVal)) {
                    setError('level', 'Level course wajib dipilih.');
                    firstInvalid ||= level;
                } else {
                    setError('level', '');
                }

                const statusVal = (status?.value || '').trim();
                if (!['active', 'archive'].includes(statusVal)) {
                    setError('status', 'Status wajib dipilih.');
                    firstInvalid ||= status;
                } else {
                    setError('status', '');
                }

                const catVal = (categoryId?.value || '').trim();
                if (catVal.length === 0) {
                    setError('category_id', 'Kategori wajib dipilih.');
                    firstInvalid ||= categoryId;
                } else {
                    setError('category_id', '');
                }

                const trainerVal = (trainerId?.value || '').trim();
                if (trainerVal.length === 0) {
                    setError('trainer_id', 'Trainer wajib dipilih.');
                    firstInvalid ||= trainerId;
                } else {
                    setError('trainer_id', '');
                }

                const rawPrice = (price?.value || '').trim();
                const digits = normalizeDigits(rawPrice);
                if (digits.length === 0) {
                    setError('price', 'Harga wajib diisi.');
                    firstInvalid ||= price;
                } else {
                    const val = parseInt(digits, 10);
                    if (Number.isNaN(val) || val < 0) {
                        setError('price', 'Harga harus angka >= 0.');
                        firstInvalid ||= price;
                    } else {
                        setError('price', '');
                    }
                }

                const introFile = image?.files && image.files[0];
                if (!introFile) {
                    setError('image', 'Intro media wajib dipilih.');
                    firstInvalid ||= image;
                } else if (!allowedIntroMimes.has(introFile.type)) {
                    setError('image', 'Format intro media tidak didukung.');
                    firstInvalid ||= image;
                } else {
                    setError('image', '');
                }

                const cardFile = cardThumb?.files && cardThumb.files[0];
                if (!cardFile) {
                    setError('card_thumbnail', 'Thumbnail card course wajib diupload.');
                    firstInvalid ||= cardThumb;
                } else if (!allowedCardThumbMimes.has(cardFile.type)) {
                    setError('card_thumbnail', 'Format thumbnail harus gambar (jpg/png/webp/gif).');
                    firstInvalid ||= cardThumb;
                } else {
                    setError('card_thumbnail', '');
                }

                return firstInvalid;
            }

            // Live validation (lightweight)
            getEl('name')?.addEventListener('input', validate);
            getEl('level')?.addEventListener('change', validate);
            getEl('status')?.addEventListener('change', validate);
            getEl('category_id')?.addEventListener('change', validate);
            getEl('trainer_id')?.addEventListener('change', validate);
            getEl('price')?.addEventListener('input', validate);
            getEl('image')?.addEventListener('change', validate);
            getEl('card_thumbnail')?.addEventListener('change', validate);

            form.addEventListener('submit', function(e) {
                const firstInvalid = validate();
                if (firstInvalid) {
                    e.preventDefault();
                    firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    firstInvalid.focus?.();
                    return;
                }

                // normalize price so backend integer validation is consistent
                const price = getEl('price');
                if (price) price.value = normalizeDigits(price.value);
            });
        })();
    </script>
@endsection