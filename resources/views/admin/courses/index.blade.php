@extends('layouts.app')

@section('title', 'Manage Courses')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="mr-4">
                        <svg class="w-6 h-6 text-gray-600 hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Manage Courses</h1>
                        <p class="text-sm text-gray-600">{{ $courses->total() }} courses available</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.courses.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add Course</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($courses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow duration-200">
                    <div class="aspect-w-16 aspect-h-9">
                        @if($course->image)
                            <img src="{{ Storage::url($course->image) }}" alt="{{ $course->name }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($course->level === 'beginner') bg-green-100 text-green-800
                                @elseif($course->level === 'intermediate') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($course->level) }}
                            </span>
                            <span class="text-sm text-gray-500">{{ $course->modules->count() }} modules</span>
                        </div>
                        
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $course->name }}</h3>
                        <p class="text-sm text-gray-600 mb-4">{{ Str::limit($course->description, 100) }}</p>
                        
                        <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                            <span>{{ $course->category->name ?? 'No Category' }}</span>
                            <span>{{ $course->duration }}h</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-semibold text-gray-900">Rp {{ number_format($course->price, 0, ',', '.') }}</span>
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.courses.modules.index', $course) }}" 
                                   class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    Modules
                                </a>
                                <a href="{{ route('admin.courses.show', $course) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('admin.courses.edit', $course) }}" 
                                   class="text-yellow-600 hover:text-yellow-900 text-sm font-medium">
                                    Edit
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this course?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $courses->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No courses</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new course.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.courses.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Course
                    </a>
                </div>
            </div>
        @endif
    </main>
</div>
@endsection
