@extends('layouts.app')

@section('title', $module->title . ' - ' . $course->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="{{ route('user.modules.index', $course) }}" class="mr-4">
                        <svg class="w-6 h-6 text-gray-600 hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $module->title }}</h1>
                        <p class="text-sm text-gray-600">Module {{ $module->order_no }} of {{ $course->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @if($module->isPdf())
                        <a href="{{ route('user.modules.download', [$course, $module]) }}" 
                           class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span>Download PDF</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Module Content -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                @if($module->isVideo())
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                @elseif($module->isPdf())
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                @else
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <h2 class="text-xl font-semibold text-gray-900">{{ $module->title }}</h2>
                            <div class="flex items-center space-x-4 mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($module->type === 'video') bg-blue-100 text-blue-800
                                    @elseif($module->type === 'pdf') bg-red-100 text-red-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ ucfirst($module->type) }}
                                </span>
                                <span class="text-sm text-gray-500">{{ $module->formatted_duration }}</span>
                                @if($module->is_free)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Free Preview
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($module->description)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">About This Module</h3>
                            <p class="text-gray-600">{{ $module->description }}</p>
                        </div>
                    @endif

                    <!-- Content Display -->
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Content</h3>
                        
                        @if($module->isVideo())
                            <div class="bg-gray-900 rounded-lg overflow-hidden">
                                <video controls class="w-full h-64" preload="metadata">
                                    <source src="{{ route('user.modules.stream', [$course, $module]) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @elseif($module->isPdf())
                            <div class="bg-gray-100 rounded-lg p-6">
                                <div class="flex items-center justify-center space-x-4">
                                    <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <div class="text-center">
                                        <h4 class="text-lg font-medium text-gray-900">PDF Document</h4>
                                        <p class="text-sm text-gray-500 mt-1">{{ $module->file_extension }} file</p>
                                        <div class="mt-4 space-x-3">
                                            <a href="{{ route('user.modules.stream', [$course, $module]) }}" target="_blank" 
                                               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                                View PDF
                                            </a>
                                            <a href="{{ route('user.modules.download', [$course, $module]) }}" 
                                               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-100 rounded-lg p-6">
                                <div class="flex items-center justify-center space-x-4">
                                    <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="text-center">
                                        <h4 class="text-lg font-medium text-gray-900">Quiz Module</h4>
                                        <p class="text-sm text-gray-500 mt-1">{{ $module->quizQuestions->count() }} questions available</p>
                                        <div class="mt-4">
                                            <a href="{{ route('user.quiz.start', [$course, $module]) }}" 
                                               class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                                                Start Quiz
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Module Navigation Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Module Navigation</h3>
                    
                    <div class="space-y-3">
                        @if($prevModule)
                            <a href="{{ route('user.modules.show', [$course, $prevModule]) }}" 
                               class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Previous Module</p>
                                    <p class="text-xs text-gray-500">{{ $prevModule->title }}</p>
                                </div>
                            </a>
                        @endif
                        
                        <div class="flex items-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-sm font-semibold text-white">{{ $module->order_no }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Current Module</p>
                                <p class="text-xs text-blue-700">{{ $module->title }}</p>
                            </div>
                        </div>
                        
                        @if($nextModule)
                            <a href="{{ route('user.modules.show', [$course, $nextModule]) }}" 
                               class="flex items-center p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900">Next Module</p>
                                    <p class="text-xs text-gray-500">{{ $nextModule->title }}</p>
                                </div>
                                <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Module Progress -->
                <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Progress</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm text-gray-600 mb-1">
                                <span>Course Progress</span>
                                <span>0%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: 0%"></div>
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-500">
                            <p>0 of {{ $course->modules->count() }} modules completed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
