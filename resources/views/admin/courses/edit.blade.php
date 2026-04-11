@extends('layouts.app')

@section('title', 'Edit Course - ' . $course->name)

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

                <input type="hidden" name="status" id="status" value="{{ ((string) ($course->status ?? '')) === 'active' ? 'active' : 'archive' }}">

                @php
                    $discountStartValue = optional($course->discount_start)->format('Y-m-d');
                    $discountEndValue = optional($course->discount_end)->format('Y-m-d');

                    $rawExpenseRows = old('expenses');
                    if ($rawExpenseRows === null) {
                        $rawExpenseRows = $course->expenses_json;
                    }
                    $rawExpenseRows = is_array($rawExpenseRows) ? array_values($rawExpenseRows) : [];

                    $expenseRows = [];
                    foreach ($rawExpenseRows as $row) {
                        if (!is_array($row)) {
                            continue;
                        }
                        $item = trim((string) ($row['item'] ?? ''));

                        $qty = array_key_exists('quantity', $row) ? (int) ($row['quantity'] ?? 0) : null;
                        $unit = array_key_exists('unit_price', $row) ? (int) ($row['unit_price'] ?? 0) : null;
                        $total = array_key_exists('total', $row) ? (int) ($row['total'] ?? 0) : null;

                        if (($qty === null || $unit === null) && $total !== null) {
                            $qty = $qty ?? 1;
                            $unit = $unit ?? max(0, $total);
                        }

                        $qty = (int) max(0, $qty ?? 0);
                        $unit = (int) max(0, $unit ?? 0);
                        $expenseRows[] = [
                            'item' => $item,
                            'quantity' => $qty,
                            'unit_price' => $unit,
                        ];
                    }

                    if (empty($expenseRows)) {
                        $expenseRows[] = ['item' => '', 'quantity' => 0, 'unit_price' => 0];
                    }
                @endphp

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

                        <!-- Level (Status hidden; preserved as-is) -->
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
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
                        </div>

                        <!-- Hidden/Visible Category to satisfy required -->
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                            <div>
                                <label for="category_id"
                                    class="block text-sm font-medium text-gray-700 mb-2">Kategori</label>
                                <select name="category_id" id="category_id" required
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Trainer Assignment -->
                        <div>
                            <label for="trainer_id" class="block text-sm font-medium text-gray-700 mb-2">Trainer</label>
                            <select name="trainer_id" id="trainer_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                <option value="">-- Pilih Trainer --</option>
                                @foreach($trainers as $trainer)
                                    <option value="{{ $trainer->id }}" {{ old('trainer_id', $course->trainer_id) == $trainer->id ? 'selected' : '' }}>
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
                                value="{{ old('price', $course->price) }}" placeholder="Masukkan Harga Course">
                        </div>

                        <!-- Diskon -->
                        <div>
                            <label for="discount_percent" class="block text-sm font-medium text-gray-700 mb-2">Diskon (%)</label>
                            <input type="number" name="discount_percent" id="discount_percent" min="0" max="100" inputmode="numeric"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="{{ old('discount_percent', (int) ($course->discount_percent ?? 0)) }}" placeholder="0">
                            <p class="mt-1 text-xs text-gray-500">Isi 0 untuk nonaktifkan diskon.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="discount_start" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai Diskon</label>
                                <input type="date" name="discount_start" id="discount_start"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                    value="{{ old('discount_start', $discountStartValue) }}">
                            </div>

                            <div>
                                <label for="discount_end" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Berakhir Diskon</label>
                                <input type="date" name="discount_end" id="discount_end"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                    value="{{ old('discount_end', $discountEndValue) }}">
                            </div>
                        </div>

                        <!-- Pengeluaran (Breakdown) -->
                        <div>
                            <div class="flex items-center justify-between gap-4 mb-2">
                                <label class="block text-sm font-medium text-gray-700">Pengeluaran</label>
                                <button type="button" id="add-expense-row"
                                    class="px-3 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Tambah Pengeluaran
                                </button>
                            </div>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="grid grid-cols-12 gap-2 px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-600">
                                    <div class="col-span-5">Item</div>
                                    <div class="col-span-2">Qty</div>
                                    <div class="col-span-3">Harga Satuan</div>
                                    <div class="col-span-2">Aksi</div>
                                </div>

                                <div id="expense-rows" class="divide-y divide-gray-200">
                                    @foreach($expenseRows as $i => $row)
                                        <div class="expense-row grid grid-cols-12 gap-2 px-4 py-3 items-center" data-index="{{ $i }}">
                                            <div class="col-span-12 md:col-span-5">
                                                <input type="text" name="expenses[{{ $i }}][item]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                                    value="{{ old('expenses.' . $i . '.item', (string) ($row['item'] ?? '')) }}"
                                                    placeholder="Contoh: Iklan, Produksi, dll">
                                            </div>
                                            <div class="col-span-6 md:col-span-2">
                                                <input type="number" min="0" inputmode="numeric" name="expenses[{{ $i }}][quantity]"
                                                    class="expense-qty w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                                    value="{{ old('expenses.' . $i . '.quantity', (int) ($row['quantity'] ?? 0)) }}"
                                                    placeholder="0">
                                            </div>
                                            <div class="col-span-6 md:col-span-3">
                                                <input type="number" min="0" inputmode="numeric" name="expenses[{{ $i }}][unit_price]"
                                                    class="expense-unit w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                                    value="{{ old('expenses.' . $i . '.unit_price', (int) ($row['unit_price'] ?? 0)) }}"
                                                    placeholder="0">
                                            </div>
                                            <div class="col-span-12 md:col-span-2 flex items-center gap-2">
                                                <button type="button" class="remove-expense-row px-3 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Isi item, qty, dan harga satuan. Total dihitung otomatis saat disimpan.</p>
                        </div>

                        <!-- Akses Course Gratis -->
                        <div>
                            <label for="free_access_mode" class="block text-sm font-medium text-gray-700 mb-2">Akses Course
                                Gratis</label>
                            <select name="free_access_mode" id="free_access_mode"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                <option value="all" {{ old('free_access_mode', $course->free_access_mode ?? 'limit_2') === 'all' ? 'selected' : '' }}>Buka semua materi</option>
                                <option value="limit_2" {{ old('free_access_mode', $course->free_access_mode ?? 'limit_2') === 'limit_2' ? 'selected' : '' }}>Hanya 2 modul/video yang dibuka</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Berlaku jika harga course = 0 (gratis).</p>
                        </div>

                        <!-- Reseller Course -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reseller Course</label>
                            @php
                                $isResellerCourse = (int) old('is_reseller_course', (int) ($course->is_reseller_course ?? 0));
                            @endphp
                            <div class="reseller-course-radios mt-1 flex flex-wrap items-center gap-x-8 gap-y-2" role="radiogroup" aria-label="Reseller Course">
                                <div class="reseller-course-option inline-flex items-center whitespace-nowrap shrink-0">
                                    <input type="radio" name="is_reseller_course" id="is_reseller_course_0" value="0" class="h-4 w-4"
                                        {{ $isResellerCourse === 0 ? 'checked' : '' }}>
                                    <label for="is_reseller_course_0" class="text-sm text-gray-700">Tidak</label>
                                </div>
                                <div class="reseller-course-option inline-flex items-center whitespace-nowrap shrink-0">
                                    <input type="radio" name="is_reseller_course" id="is_reseller_course_1" value="1" class="h-4 w-4"
                                        {{ $isResellerCourse === 1 ? 'checked' : '' }}>
                                    <label for="is_reseller_course_1" class="text-sm text-gray-700">Ya</label>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Jika Ya, course ini ditandai sebagai course reseller.</p>
                            <style>
                                .reseller-course-radios .reseller-course-option{
                                    display: inline-flex !important;
                                    align-items: center !important;
                                    flex: 0 0 auto !important;
                                    white-space: nowrap !important;
                                }
                                .reseller-course-radios label{
                                    display: inline-flex !important;
                                    align-items: center !important;
                                    margin: 0 0 0 0.5rem !important;
                                    cursor: pointer;
                                    user-select: none;
                                }
                                .reseller-course-radios .reseller-course-option:first-child label{
                                    margin-left: 0.15rem !important;
                                }
                                .reseller-course-radios input[type="radio"]{
                                    appearance: auto !important;
                                    -webkit-appearance: radio !important;
                                    -moz-appearance: auto !important;
                                    margin: 0 !important;
                                    vertical-align: middle !important;
                                }
                            </style>
                        </div>

                        <!-- Deskripsi -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi
                                Course</label>
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
                                            <video src="{{ Storage::disk('public')->url($course->media) }}" controls
                                                class="h-32 w-48 object-cover rounded-lg border bg-black"></video>
                                        @else
                                            <img src="{{ Storage::disk('public')->url($course->media) }}"
                                                class="h-32 w-48 object-cover rounded-lg border" alt="Intro Media">
                                        @endif
                                    </div>
                                @endif
                                <div class="w-full">
                                    <input type="file" name="image" accept="image/*,video/mp4,video/webm,video/ogg"
                                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-300 rounded-lg">
                                    <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG, MP4. Digunakan untuk intro
                                        course.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Thumbnail -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Thumbnail Course</label>
                            <div class="flex items-center gap-4">
                                @if($course->card_thumbnail)
                                    <div class="shrink-0">
                                        <img src="{{ Storage::disk('public')->url($course->card_thumbnail) }}"
                                            class="h-16 w-16 object-cover rounded-lg border" alt="Thumbnail">
                                    </div>
                                @endif
                                <input type="file" name="card_thumbnail"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200 cursor-pointer border border-gray-300 rounded-lg">
                            </div>
                        </div>

                        <!-- Hidden inputs for other required fields not in design (Duration, etc) -->
                        <input type="hidden" name="duration" value="{{ $course->duration ?? 1 }}">
                    </div>
                </div>

                <template id="expense-row-template">
                    <div class="expense-row grid grid-cols-12 gap-2 px-4 py-3 items-center" data-index="__INDEX__">
                        <div class="col-span-12 md:col-span-5">
                            <input type="text" name="expenses[__INDEX__][item]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="" placeholder="Contoh: Iklan, Produksi, dll">
                        </div>
                        <div class="col-span-6 md:col-span-2">
                            <input type="number" min="0" inputmode="numeric" name="expenses[__INDEX__][quantity]"
                                class="expense-qty w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="0" placeholder="0">
                        </div>
                        <div class="col-span-6 md:col-span-3">
                            <input type="number" min="0" inputmode="numeric" name="expenses[__INDEX__][unit_price]"
                                class="expense-unit w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="0" placeholder="0">
                        </div>
                        <div class="col-span-12 md:col-span-2 flex items-center gap-2">
                            <button type="button" class="remove-expense-row px-3 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50">
                                Hapus
                            </button>
                        </div>
                    </div>
                </template>

                
                <!-- Footer Buttons -->
                <div class="flex justify-end gap-4 mt-12 pb-10">
                    <a href="{{ route('admin.courses.index') }}"
                        class="px-6 py-2.5 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition">Cancel</a>
                    <button type="submit"
                        class="px-6 py-2.5 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition">Save</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        (function() {
            const rowsWrap = document.getElementById('expense-rows');
            const addBtn = document.getElementById('add-expense-row');
            const tpl = document.getElementById('expense-row-template');

            if (!rowsWrap || !addBtn || !tpl) return;

            function nextIndex() {
                const els = rowsWrap.querySelectorAll('.expense-row');
                let max = -1;
                els.forEach((el) => {
                    const idx = parseInt(el.getAttribute('data-index') || '-1', 10);
                    if (!Number.isNaN(idx) && idx > max) max = idx;
                });
                return max + 1;
            }

            function wireRow(rowEl) {
                const removeBtn = rowEl.querySelector('.remove-expense-row');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        rowEl.remove();
                    });
                }
            }

            rowsWrap.querySelectorAll('.expense-row').forEach(wireRow);

            addBtn.addEventListener('click', function() {
                const idx = nextIndex();
                const html = tpl.innerHTML.replaceAll('__INDEX__', String(idx));
                const temp = document.createElement('div');
                temp.innerHTML = html.trim();
                const rowEl = temp.firstElementChild;
                if (!rowEl) return;
                rowsWrap.appendChild(rowEl);
                wireRow(rowEl);
            });
        })();
    </script>

    </div>

@endsection