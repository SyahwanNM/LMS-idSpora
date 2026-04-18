@extends('layouts.admin')

@section('title', 'Edit Template: ' . $template->name)

@section('content')
    <div class="container-fluid px-4 py-4">
        <div style="max-width: 900px; margin: 0 auto;">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 30px;">
                <a href="{{ route('admin.templates.show', $template) }}"
                    style="display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; background: #e2e8f0; color: #2d3748; border-radius: 6px; text-decoration: none; font-size: 16px;">
                    <i class="bi bi-arrow-left"></i>
                </a>
                <div>
                    <h1 style="margin: 0; font-size: 28px; color: #1a202c; font-weight: 600;">
                        Edit Template
                    </h1>
                    <p style="margin: 5px 0 0 0; color: #718096; font-size: 14px;">{{ $template->name }}
                        (v{{ $template->version }})</p>
                </div>
            </div>

            @if($errors->any())
                <div
                    style="background: #fed7d7; border-left: 4px solid #c53030; padding: 12px 16px; border-radius: 4px; margin-bottom: 20px; color: #742a2a;">
                    <strong>Terjadi kesalahan:</strong>
                    <ul style="margin: 8px 0 0 0; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.templates.update', $template) }}" method="POST"
                style="background: white; border-radius: 8px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                @csrf
                @method('PUT')

                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 14px;">
                        Nama Template <span style="color: #c53030;">*</span>
                    </label>
                    <input type="text" name="name" value="{{ old('name', $template->name) }}"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;"
                        required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
                    <div>
                        <label
                            style="display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 14px;">
                            Kategori
                        </label>
                        <select name="category_id"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $template->category_id) == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label
                            style="display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 14px;">
                            Level Kesulitan <span style="color: #c53030;">*</span>
                        </label>
                        <select name="level"
                            style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px;"
                            required>
                            <option value="">-- Pilih Level --</option>
                            <option value="beginner" {{ old('level', $template->level) == 'beginner' ? 'selected' : '' }}>
                                Beginner</option>
                            <option value="intermediate" {{ old('level', $template->level) == 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                            <option value="advanced" {{ old('level', $template->level) == 'advanced' ? 'selected' : '' }}>
                                Advanced</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 24px;">
                    <label style="display: block; margin-bottom: 8px; color: #2d3748; font-weight: 600; font-size: 14px;">
                        Deskripsi
                    </label>
                    <textarea name="description" rows="3"
                        style="width: 100%; padding: 10px 12px; border: 1px solid #cbd5e0; border-radius: 6px; font-size: 14px; font-family: inherit;">{{ old('description', $template->description) }}</textarea>
                </div>

                <div style="border-top: 2px solid #e2e8f0; padding-top: 24px; margin-bottom: 24px;">
                    <h3 style="margin: 0 0 16px 0; color: #2d3748; font-size: 16px; font-weight: 600;">
                        <i class="bi bi-collection"></i> Struktur Modul
                    </h3>

                    <div id="modulesContainer"
                        style="display: flex; flex-direction: column; gap: 16px; margin-bottom: 16px;">
                        @forelse($template->modules as $index => $module)
                            <div class="module-item"
                                style="background: #f7fafc; border: 1px solid #cbd5e0; border-radius: 6px; padding: 16px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                                    <div>
                                        <label
                                            style="display: block; margin-bottom: 4px; color: #2d3748; font-weight: 600; font-size: 12px;">
                                            Judul Module
                                        </label>
                                        <input type="text" name="modules[{{ $index }}][title]" value="{{ $module->title }}"
                                            style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px;">
                                    </div>
                                    <div>
                                        <label
                                            style="display: block; margin-bottom: 4px; color: #2d3748; font-weight: 600; font-size: 12px;">
                                            Tipe
                                        </label>
                                        <select name="modules[{{ $index }}][type]"
                                            style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px;">
                                            <option value="video" {{ $module->type == 'video' ? 'selected' : '' }}>Video</option>
                                            <option value="pdf" {{ $module->type == 'pdf' ? 'selected' : '' }}>PDF</option>
                                            <option value="quiz" {{ $module->type == 'quiz' ? 'selected' : '' }}>Quiz</option>
                                        </select>
                                    </div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr 120px; gap: 12px;">
                                    <div>
                                        <label
                                            style="display: block; margin-bottom: 4px; color: #2d3748; font-weight: 600; font-size: 12px;">
                                            Durasi (menit)
                                        </label>
                                        <input type="number" name="modules[{{ $index }}][duration]"
                                            value="{{ $module->duration }}" min="0"
                                            style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px;">
                                    </div>
                                    <div style="display: flex; align-items: flex-end;">
                                        <label
                                            style="display: flex; align-items: center; gap: 8px; color: #2d3748; font-size: 13px; cursor: pointer;">
                                            <input type="checkbox" name="modules[{{ $index }}][is_required]" value="1" {{ $module->is_required ? 'checked' : '' }} style="cursor: pointer;">
                                            Wajib
                                        </label>
                                    </div>
                                    <div style="display: flex; align-items: flex-end;">
                                        <button type="button" onclick="this.parentElement.parentElement.parentElement.remove()"
                                            style="width: 100%; padding: 8px; background: #fed7d7; color: #c53030; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p style="color: #718096; text-align: center; padding: 20px;">Tidak ada modul</p>
                        @endforelse
                    </div>

                    <button type="button" onclick="addModule()"
                        style="display: inline-flex; align-items: center; gap: 6px; padding: 10px 16px; background: #edf2f7; color: #2d3748; border: 1px dashed #cbd5e0; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 13px;">
                        <i class="bi bi-plus-lg"></i> Tambah Module
                    </button>
                </div>

                <div
                    style="background: #f0fdf4; border-left: 4px solid #22c55e; padding: 12px 16px; border-radius: 4px; margin-bottom: 24px;">
                    <label
                        style="display: flex; align-items: center; gap: 8px; color: #166534; cursor: pointer; font-weight: 600;">
                        <input type="checkbox" name="create_new_version" value="1"
                            style="cursor: pointer; width: 18px; height: 18px;">
                        Buat versi baru template ini
                    </label>
                    <p style="margin: 8px 0 0 26px; color: #15803d; font-size: 13px;">
                        Jika dicentang, perubahan akan disimpan sebagai versi baru. Course lama tetap menggunakan versi
                        lama.
                    </p>
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end;">
                    <a href="{{ route('admin.templates.show', $template) }}"
                        style="padding: 10px 20px; border: 1px solid #cbd5e0; border-radius: 6px; text-decoration: none; color: #2d3748; font-weight: 600; background: white;">
                        Batal
                    </a>
                    <button type="submit"
                        style="padding: 10px 20px; background: #2d3748; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>

        <script>
            let moduleCount = {{ $template->modules->count() }};
            function addModule() {
                const container = document.getElementById('modulesContainer');
                const html = `
                    <div class="module-item" style="background: #f7fafc; border: 1px solid #cbd5e0; border-radius: 6px; padding: 16px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                            <div>
                                <label style="display: block; margin-bottom: 4px; color: #2d3748; font-weight: 600; font-size: 12px;">
                                    Judul Module
                                </label>
                                <input 
                                    type="text" 
                                    name="modules[${moduleCount}][title]" 
                                    placeholder="Contoh: Pengenalan Framework" 
                                    style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px;">
                            </div>
                            <div>
                                <label style="display: block; margin-bottom: 4px; color: #2d3748; font-weight: 600; font-size: 12px;">
                                    Tipe
                                </label>
                                <select name="modules[${moduleCount}][type]" style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px;">
                                    <option value="video">Video</option>
                                    <option value="pdf">PDF</option>
                                    <option value="quiz">Quiz</option>
                                </select>
                            </div>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr 120px; gap: 12px;">
                            <div>
                                <label style="display: block; margin-bottom: 4px; color: #2d3748; font-weight: 600; font-size: 12px;">
                                    Durasi (menit)
                                </label>
                                <input 
                                    type="number" 
                                    name="modules[${moduleCount}][duration]" 
                                    placeholder="30" 
                                    min="0"
                                    style="width: 100%; padding: 8px 10px; border: 1px solid #cbd5e0; border-radius: 4px; font-size: 13px;">
                            </div>
                            <div style="display: flex; align-items: flex-end;">
                                <label style="display: flex; align-items: center; gap: 8px; color: #2d3748; font-size: 13px; cursor: pointer;">
                                    <input type="checkbox" name="modules[${moduleCount}][is_required]" value="1" checked style="cursor: pointer;">
                                    Wajib
                                </label>
                            </div>
                            <div style="display: flex; align-items: flex-end;">
                                <button type="button" onclick="this.parentElement.parentElement.parentElement.remove()" style="width: 100%; padding: 8px; background: #fed7d7; color: #c53030; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: 600;">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                moduleCount++;
            }
        </script>
    </div>
@endsection