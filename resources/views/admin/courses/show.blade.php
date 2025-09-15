@extends('layouts.app')

@section('title', $course->name)

@section('content')
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
                        <h1 class="text-2xl font-bold text-gray-900">{{ $course->name }}</h1>
                        <p class="text-sm text-gray-600">{{ $course->category->name ?? 'No Category' }} • {{ $course->modules->count() }} modules</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.courses.modules.index', $course) }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Manage Modules</span>
                    </a>
                    <a href="{{ route('admin.courses.edit', $course) }}" 
                       class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span>Edit Course</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Course Info -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="aspect-w-16 aspect-h-9 mb-6">
                        @if($course->image)
                            <img src="{{ Storage::url($course->image) }}" alt="{{ $course->name }}" class="w-full h-64 object-cover rounded-lg">
                        @else
                            <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center space-x-4 mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($course->level === 'beginner') bg-green-100 text-green-800
                            @elseif($course->level === 'intermediate') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ ucfirst($course->level) }}
                        </span>
                        <span class="text-sm text-gray-500">{{ $course->category->name ?? 'No Category' }}</span>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $course->name }}</h2>
                    
                    @if($course->description)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Description</h3>
                            <p class="text-gray-600">{{ $course->description }}</p>
                        </div>
                    @endif

                    <!-- Modules List -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Modules ({{ $course->modules->count() }})</h3>
                        
                        @if($course->modules->count() > 0)
                            <div class="space-y-3">
                                @foreach($course->modules as $module)
                                <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <span class="text-sm font-semibold text-blue-600">{{ $module->order_no }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4 flex-1">
                                        <h4 class="text-sm font-medium text-gray-900">{{ $module->title }}</h4>
                                        <div class="flex items-center space-x-2 mt-1">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                @if($module->type === 'video') bg-blue-100 text-blue-800
                                                @elseif($module->type === 'pdf') bg-red-100 text-red-800
                                                @else bg-green-100 text-green-800 @endif">
                                                {{ ucfirst($module->type) }}
                                            </span>
                                            <span class="text-xs text-gray-500">{{ $module->formatted_duration }}</span>
                                            @if($module->is_free)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Free
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <a href="{{ route('admin.courses.modules.show', [$course, $module]) }}" 
                                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                            View
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No modules</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating a new module.</p>
                                <div class="mt-6">
                                    <a href="{{ route('admin.courses.modules.create', $course) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Add Module
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Course Details Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Course Details</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Category</dt>
                            <dd class="text-sm text-gray-900">{{ $course->category->name ?? 'No Category' }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Level</dt>
                            <dd class="text-sm text-gray-900">{{ ucfirst($course->level) }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Duration</dt>
                            <dd class="text-sm text-gray-900">{{ $course->duration }} hours</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Price</dt>
                            <dd class="text-sm text-gray-900">Rp {{ number_format($course->price, 0, ',', '.') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Modules</dt>
                            <dd class="text-sm text-gray-900">{{ $course->modules->count() }} modules</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="text-sm text-gray-900">{{ $course->created_at->format('M d, Y') }}</dd>
                        </div>
                        
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="text-sm text-gray-900">{{ $course->updated_at->format('M d, Y') }}</dd>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                    
                    <div class="space-y-3">
                        <a href="{{ route('admin.courses.modules.create', $course) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <span>Add Module</span>
                        </a>
                        
                        <a href="{{ route('admin.courses.edit', $course) }}" 
                           class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            <span>Edit Course</span>
                        </a>
                        
                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this course? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Delete Course</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
