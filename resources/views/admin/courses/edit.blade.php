@extends('layouts.app')

@section('title', 'Edit Course - ' . $course->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="{{ route('admin.courses.show', $course) }}" class="mr-4">
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
                
                <div class="space-y-6">
                    <!-- Course Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Course Name</label>
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
                            <option value="">Select Category</option>
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

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="bi bi-file-text me-1"></i>Description
                        </label>
                        <textarea name="description" id="description" class="form-control" rows="8">{{ old('description', $course->description) }}</textarea>
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
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Duration (hours)</label>
                            <input type="number" name="duration" id="duration" required min="0" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('duration') border-red-500 @enderror"
                                   value="{{ old('duration', $course->duration) }}" placeholder="0">
                            @error('duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
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

                    <!-- Current Image Display -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Image</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            @if($course->image)
                                <img src="{{ Storage::url($course->image) }}" alt="{{ $course->name }}" class="w-32 h-24 object-cover rounded-lg">
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
                        <label for="image" class="block text-sm font-medium text-gray-700 mb-2">Replace Image (Optional)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="image" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a new image</span>
                                        <input id="image" name="image" type="file" accept="image/*" 
                                               class="sr-only @error('image') border-red-500 @enderror">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">Leave empty to keep current image</p>
                            </div>
                        </div>
                        @error('image')
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
</div>

<!-- CKEditor 5 CDN -->
<script src="https://cdn.ckeditor.com/ckeditor5/40.1.0/classic/ckeditor.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
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
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function() {
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
