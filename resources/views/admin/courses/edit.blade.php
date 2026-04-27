@extends('layouts.app')

@section('title', 'Edit Course - ' . $course->name)

@section('content')
    @include('partials.navbar-admin-course')

    <div class="min-h-screen bg-white p-6 md:p-10">
        <!-- Header -->
        <div  class="max-w-4xl mx-auto mb-8">
            <div style="margin-left:62px;" class="text-xs text-gray-400">Course Builder / Edit Courses</div>
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.courses.index') }}" class="p-2 border rounded-lg hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        class="bi bi-arrow-left" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Courses</h1>
                    <p class="text-gray-500 text-sm">Set course details before publishing</p>
                </div>
            </div>

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
                    <h2 class="text-lg font-semibold text-gray-900 mb-6">Course Settings Form</h2>

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
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Course Title</label>
                            <input type="text" name="name" id="name" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="{{ old('name', $course->name) }}" placeholder="Enter Course Title">
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
                            {{-- Module titles: shown dynamically based on selected level --}}
                            <div id="module-titles-section" class="mb-3" style="display:none;">
                                <label class="form-label text-dark fw-semibold">Input Title Module <span class="text-danger">*</span></label>
                                <div id="module-titles-grid" class="row g-3"></div>
                            </div>
                        </div>

                        <!-- Hidden/Visible Category to satisfy required -->
                        <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                            <div>
                                <label for="category_id_input"
                                    class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <div style="position:relative;">
                                    <input type="text" id="category_id_input" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white" placeholder="Type to search category..." autocomplete="off"
                                        value="{{ old('category_id', $course->category_id) ? ($categories->firstWhere('id', old('category_id', $course->category_id))?->name ?? '') : '' }}">
                                    <input type="hidden" name="category_id" id="category_id" value="{{ old('category_id', $course->category_id) }}">
                                    <input type="hidden" name="category_name" id="category_name_hidden" value="">
                                    <ul id="edit-category-suggestions" style="display:none; position:absolute; top:100%; left:0; right:0; background:#fff; border:1px solid #dee2e6; border-radius:6px; z-index:999; list-style:none; margin:2px 0 0; padding:4px 0; max-height:220px; overflow-y:auto; box-shadow:0 4px 12px rgba(0,0,0,.1);">
                                        @foreach($categories as $cat)
                                        <li data-id="{{ $cat->id }}" data-name="{{ $cat->name }}" style="padding:8px 14px; cursor:pointer; font-size:14px;" onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background=''">{{ $cat->name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <script>
                                (function(){
                                    const inp = document.getElementById('category_id_input');
                                    const hidden = document.getElementById('category_id');
                                    const hiddenName = document.getElementById('category_name_hidden');
                                    const list = document.getElementById('edit-category-suggestions');
                                    const allItems = Array.from(list.querySelectorAll('li'));

                                    inp.addEventListener('input', function() {
                                        const q = this.value.toLowerCase();
                                        hidden.value = '';
                                        hiddenName.value = '';
                                        let any = false;
                                        allItems.forEach(li => {
                                            const match = li.dataset.name.toLowerCase().includes(q);
                                            li.style.display = match ? '' : 'none';
                                            if (match) any = true;
                                        });
                                        list.style.display = (q && any) ? 'block' : 'none';
                                    });

                                    inp.addEventListener('focus', function() {
                                        if (!this.value) {
                                            allItems.forEach(li => li.style.display = '');
                                            list.style.display = 'block';
                                        }
                                    });

                                    inp.addEventListener('blur', function() {
                                        const val = this.value.trim();
                                        if (val && !hidden.value) {
                                            hiddenName.value = val;
                                        }
                                        setTimeout(() => { list.style.display = 'none'; }, 150);
                                    });

                                    list.addEventListener('click', function(e) {
                                        const li = e.target.closest('li');
                                        if (!li) return;
                                        inp.value = li.dataset.name;
                                        hidden.value = li.dataset.id;
                                        hiddenName.value = '';
                                        list.style.display = 'none';
                                    });

                                    document.addEventListener('click', function(e) {
                                        if (!inp.contains(e.target) && !list.contains(e.target)) {
                                            list.style.display = 'none';
                                        }
                                    });

                                    // Sync on form submit
                                    inp.closest('form')?.addEventListener('submit', function() {
                                        if (!hidden.value.trim() && inp.value.trim()) {
                                            hiddenName.value = inp.value.trim();
                                        }
                                    });
                                })();
                                </script>
                            </div>
                        </div>

                        <!-- Trainer Assignment -->
                        <div>
                            <label for="trainer_id" class="block text-sm font-medium text-gray-700 mb-2">Trainer</label>
                            <select name="trainer_id" id="trainer_id"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                <option value="">-- Select Trainer --</option>
                                @foreach($trainers as $trainer)
                                    <option value="{{ $trainer->id }}" {{ old('trainer_id', $course->trainer_id) == $trainer->id ? 'selected' : '' }}>
                                        {{ $trainer->name }}{{ $trainer->email ? ' (' . $trainer->email . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Courses only appear on the trainer dashboard if the trainer is selected.</p>
                        </div>

                        <!-- Harga -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price</label>
                            <input type="text" name="price" id="price" required
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="{{ old('price', $course->price) }}" placeholder="Enter Course Price">
                        </div>

                        <!-- Diskon -->
                        <div>
                            <label for="discount_percent" class="block text-sm font-medium text-gray-700 mb-2">Discount (%)</label>
                            <input type="number" name="discount_percent" id="discount_percent" min="0" max="100" inputmode="numeric"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="{{ old('discount_percent', (int) ($course->discount_percent ?? 0)) }}" placeholder="0">
                            <p class="mt-1 text-xs text-gray-500">Enter 0 to disable discount.</p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="discount_start" class="block text-sm font-medium text-gray-700 mb-2">Discount Start Date</label>
                                <input type="date" name="discount_start" id="discount_start"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                    value="{{ old('discount_start', $discountStartValue) }}">
                            </div>

                            <div>
                                <label for="discount_end" class="block text-sm font-medium text-gray-700 mb-2">Discount End Date</label>
                                <input type="date" name="discount_end" id="discount_end"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                    value="{{ old('discount_end', $discountEndValue) }}">
                            </div>
                        </div>


                        <!-- Akses Course (Freemium Mode) -->
                        <div>
                            <label for="free_access_mode" class="block text-sm font-medium text-gray-700 mb-2">Access Course</label>
                            <select name="free_access_mode" id="free_access_mode"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 bg-white">
                                <option value="limit_2" {{ old('free_access_mode', $course->free_access_mode ?? 'limit_2') === 'limit_2' ? 'selected' : '' }}>Freemium (Module 1 Open)</option>
                                <option value="all" {{ old('free_access_mode', $course->free_access_mode ?? 'limit_2') === 'all' ? 'selected' : '' }}>Open All Materials</option>
                                <option value="none" {{ old('free_access_mode', $course->free_access_mode ?? 'limit_2') === 'none' ? 'selected' : '' }}>Close Review (Must Purchase First)</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">Choose how users can access materials before purchasing (for paid courses) or the access status for free courses.</p>
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
                                    <label for="is_reseller_course_0" class="text-sm text-gray-700">No</label>
                                </div>
                                <div class="reseller-course-option inline-flex items-center whitespace-nowrap shrink-0">
                                    <input type="radio" name="is_reseller_course" id="is_reseller_course_1" value="1" class="h-4 w-4"
                                        {{ $isResellerCourse === 1 ? 'checked' : '' }}>
                                    <label for="is_reseller_course_1" class="text-sm text-gray-700">Yes</label>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">If Yes, this course will be marked as a reseller course.</p>
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
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description Course</label>
                            <textarea name="description" id="description" rows="6"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                placeholder="Description Course">{!! old('description', strip_tags($course->description)) !!}</textarea>
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
                                    <p class="mt-1 text-xs text-gray-500">Formats: JPG, PNG, MP4. Used for course intro.</p>
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

                        <!-- Pengeluaran (Breakdown) -->
                        <div>
                            <div class="flex items-center justify-between gap-4 mb-2">
                                <label class="block text-sm font-medium text-gray-700">Expense Breakdown</label>
                                <button type="button" id="add-expense-row"
                                    class="px-3 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50">
                                    Add Expense
                                </button>
                            </div>
                            <div class="border border-gray-200 rounded-lg overflow-hidden">
                                <div class="grid grid-cols-12 gap-2 px-4 py-2 bg-gray-50 text-xs font-semibold text-gray-600">
                                    <div class="col-span-4">Item</div>
                                    <div class="col-span-2">Qty</div>
                                    <div class="col-span-2">Unit Price</div>
                                    <div class="col-span-2">Total Price</div>
                                    <div class="col-span-2">Action</div>
                                </div>

                                <div id="expense-rows" class="divide-y divide-gray-200">
                                    @foreach($expenseRows as $i => $row)
                                        <div class="expense-row grid grid-cols-12 gap-2 px-4 py-3 items-center" data-index="{{ $i }}">
                                            <div class="col-span-12 md:col-span-4">
                                                <input type="text" name="expenses[{{ $i }}][item]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                                    value="{{ old('expenses.' . $i . '.item', (string) ($row['item'] ?? '')) }}"
                                                    placeholder="Contoh: Iklan, Produksi, dll">
                                            </div>
                                            <div class="col-span-6 md:col-span-2">
                                                <input type="number" min="1" inputmode="numeric" name="expenses[{{ $i }}][quantity]"
                                                    class="expense-qty w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                                    value="{{ old('expenses.' . $i . '.quantity', max(1, (int) ($row['quantity'] ?? 1))) }}"
                                                    placeholder="1">
                                            </div>
                                            <div class="col-span-6 md:col-span-2">
                                                <input type="number" min="0" inputmode="numeric" name="expenses[{{ $i }}][unit_price]"
                                                    class="expense-unit w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                                    value="{{ old('expenses.' . $i . '.unit_price', (int) ($row['unit_price'] ?? 0)) }}"
                                                    placeholder="0">
                                            </div>
                                            <div class="col-span-6 md:col-span-2">
                                                <input type="number" min="0" name="expenses[{{ $i }}][total]"
                                                    class="expense-total w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600"
                                                    value="{{ old('expenses.' . $i . '.total', (int) ($row['total'] ?? 0)) }}"
                                                    readonly placeholder="0">
                                            </div>
                                            <div class="col-span-6 md:col-span-2 flex items-center gap-2">
                                                <button type="button" class="remove-expense-row px-3 py-2 text-sm font-medium border border-gray-300 rounded-lg hover:bg-gray-50">
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Enter the item, quantity, and unit price. The total is calculated automatically upon saving.</p>
                        </div>

                        <!-- Hidden inputs for other required fields not in design (Duration, etc) -->
                        <input type="hidden" name="duration" value="{{ $course->duration ?? 1 }}">
                    </div>
                </div>

                <template id="expense-row-template">
                    <div class="expense-row grid grid-cols-12 gap-2 px-4 py-3 items-center" data-index="__INDEX__">
                        <div class="col-span-12 md:col-span-4">
                            <input type="text" name="expenses[__INDEX__][item]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="" placeholder="Contoh: Iklan, Produksi, dll">
                        </div>
                        <div class="col-span-6 md:col-span-2">
                            <input type="number" min="1" inputmode="numeric" name="expenses[__INDEX__][quantity]"
                                class="expense-qty w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="1" placeholder="1">
                        </div>
                        <div class="col-span-6 md:col-span-2">
                            <input type="number" min="0" inputmode="numeric" name="expenses[__INDEX__][unit_price]"
                                class="expense-unit w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                value="0" placeholder="0">
                        </div>
                        <div class="col-span-6 md:col-span-2">
                            <input type="number" min="0" name="expenses[__INDEX__][total]"
                                class="expense-total w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-gray-600"
                                value="0" readonly placeholder="0">
                        </div>
                        <div class="col-span-6 md:col-span-2 flex items-center gap-2">
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
    // Dynamic module title inputs based on level (edit page)
    (function () {
        const levelModules  = { beginner: 3, intermediate: 6, advanced: 12 };
        const existingTitles = @json($course->units->pluck('title', 'unit_no'));
        const levelSelect   = document.getElementById('level');
        const section       = document.getElementById('module-titles-section');
        const grid          = document.getElementById('module-titles-grid');

        function renderModuleTitles(level) {
            const count = levelModules[level] || 0;
            grid.innerHTML = '';
            if (!count) { section.style.display = 'none'; return; }

            section.style.display = 'block';
            for (let i = 1; i <= count; i++) {
                const existing = existingTitles[i] || '';
                const col = document.createElement('div');
                col.className = 'col-md-4';
                col.innerHTML = `
                    <label class="form-label text-dark small" for="unit-title-${i}">Module ${i}</label>
                    <input id="unit-title-${i}" name="unit_titles[${i}]" type="text"
                           class="form-control form-control-sm"
                           placeholder="Title module ${i}"
                           value="${existing.replace(/"/g, '&quot;')}">`;
                grid.appendChild(col);
            }
        }

        if (levelSelect) {
            levelSelect.addEventListener('change', function () {
                renderModuleTitles(this.value);
            });
            if (levelSelect.value) renderModuleTitles(levelSelect.value);
        }
    })();
    </script>

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

            function recalcRow(rowEl) {
                const qtyInp = rowEl.querySelector('.expense-qty');
                const unitInp = rowEl.querySelector('.expense-unit');
                let qty = parseInt(qtyInp?.value || '1', 10);
                if (isNaN(qty) || qty < 1) {
                    qty = 1;
                    if (qtyInp) qtyInp.value = 1;
                }
                const unit = parseInt(unitInp?.value || '0', 10);
                const totalEl = rowEl.querySelector('.expense-total');
                if (totalEl) totalEl.value = Math.max(0, qty * (isNaN(unit) ? 0 : unit));
            }

            function wireRow(rowEl) {
                const removeBtn = rowEl.querySelector('.remove-expense-row');
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        rowEl.remove();
                    });
                }
                const qtyInp = rowEl.querySelector('.expense-qty');
                const unitInp = rowEl.querySelector('.expense-unit');
                if (qtyInp) qtyInp.addEventListener('input', () => recalcRow(rowEl));
                if (unitInp) unitInp.addEventListener('input', () => recalcRow(rowEl));
                recalcRow(rowEl);
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