@extends('layouts.app')

@section('title', 'Edit Module - ' . $module->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="{{ route('admin.courses.modules.show', [$course, $module]) }}" class="mr-4">
                        <svg class="w-6 h-6 text-gray-600 hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Module</h1>
                        <p class="text-sm text-gray-600">{{ $module->title }}</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('admin.courses.modules.update', [$course, $module]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Module Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Module Title</label>
                        <input type="text" name="title" id="title" required 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('title') border-red-500 @enderror"
                               value="{{ old('title', $module->title) }}" placeholder="Enter module title">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Module Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="4" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                                  placeholder="Describe what this module covers">{{ old('description', $module->description) }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Module Type, Order, and Duration -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Module Type</label>
                            <select name="type" id="type" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('type') border-red-500 @enderror">
                                <option value="">Select type</option>
                                <option value="video" {{ old('type', $module->type) == 'video' ? 'selected' : '' }}>Video</option>
                                <option value="pdf" {{ old('type', $module->type) == 'pdf' ? 'selected' : '' }}>PDF Document</option>
                                <option value="quiz" {{ old('type', $module->type) == 'quiz' ? 'selected' : '' }}>Quiz</option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="order_no" class="block text-sm font-medium text-gray-700 mb-2">Order Number</label>
                            <input type="number" name="order_no" id="order_no" required min="1" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('order_no') border-red-500 @enderror"
                                   value="{{ old('order_no', $module->order_no) }}" placeholder="1">
                            @error('order_no')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Duration (minutes)</label>
                            <input type="number" name="duration" id="duration" required min="0" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('duration') border-red-500 @enderror"
                                   value="{{ old('duration', $module->duration) }}" placeholder="0">
                            @error('duration')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Current File Display -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current File</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center space-x-3">
                                @if($module->isVideo())
                                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                @elseif($module->isPdf())
                                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                @else
                                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">{{ basename($module->content_url) }}</p>
                                    <p class="text-sm text-gray-500">{{ ucfirst($module->type) }} file</p>
                                </div>
                                <a href="{{ Storage::url($module->content_url) }}" target="_blank" 
                                   class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- New Content File Upload -->
                    <div>
                        <label for="content_file" class="block text-sm font-medium text-gray-700 mb-2">Replace File (Optional)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="content_file" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a new file</span>
                                        <input id="content_file" name="content_file" type="file" accept=".mp4,.avi,.mov,.pdf" 
                                               class="sr-only @error('content_file') border-red-500 @enderror">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">Leave empty to keep current file</p>
                            </div>
                        </div>
                        @error('content_file')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Additional Options -->
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_free" id="is_free" value="1" 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                   {{ old('is_free', $module->is_free) ? 'checked' : '' }}>
                            <label for="is_free" class="ml-2 block text-sm text-gray-900">
                                Make this module free for preview
                            </label>
                        </div>

                        <div id="preview-pages-field" class="{{ $module->type === 'pdf' ? '' : 'hidden' }}">
                            <label for="preview_pages" class="block text-sm font-medium text-gray-700 mb-2">Preview Pages (for PDF)</label>
                            <input type="number" name="preview_pages" id="preview_pages" min="0" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   value="{{ old('preview_pages', $module->preview_pages) }}" placeholder="0">
                            <p class="mt-1 text-sm text-gray-500">Number of pages users can preview for free</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.courses.modules.show', [$course, $module]) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Update Module
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('type');
    const previewPagesField = document.getElementById('preview-pages-field');
    
    function togglePreviewPages() {
        if (typeSelect.value === 'pdf') {
            previewPagesField.classList.remove('hidden');
        } else {
            previewPagesField.classList.add('hidden');
        }
    }
    
    typeSelect.addEventListener('change', togglePreviewPages);
    togglePreviewPages(); // Initial check
});
</script>
@endsection
