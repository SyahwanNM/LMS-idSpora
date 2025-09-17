@extends('layouts.app')

@section('title', 'Quiz Questions - ' . $module->title)

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
                        <h1 class="text-2xl font-bold text-gray-900">Quiz Questions</h1>
                        <p class="text-sm text-gray-600">{{ $module->title }} - {{ $course->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.courses.modules.quiz.create', [$course, $module]) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add Question</span>
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

        @if($questions->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Questions ({{ $questions->count() }})</h2>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @foreach($questions as $question)
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <span class="text-sm font-semibold text-green-600">{{ $question->order_no }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900">{{ Str::limit($question->question, 100) }}</h3>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <span class="text-sm text-gray-500">{{ $question->points }} point{{ $question->points > 1 ? 's' : '' }}</span>
                                            <span class="text-sm text-gray-500">{{ $question->answers->count() }} answers</span>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ $question->answers->where('is_correct', true)->count() }} correct
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($question->explanation)
                                    <p class="text-gray-600 text-sm mt-2">{{ Str::limit($question->explanation, 150) }}</p>
                                @endif

                                <!-- Answers Preview -->
                                <div class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Answers:</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                        @foreach($question->answers as $answer)
                                        <div class="flex items-center space-x-2 p-2 bg-gray-50 rounded-lg">
                                            <div class="flex-shrink-0">
                                                @if($answer->is_correct)
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @else
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <span class="text-sm text-gray-700 {{ $answer->is_correct ? 'font-medium' : '' }}">{{ $answer->answer_text }}</span>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-2 ml-4">
                                <a href="{{ route('admin.courses.modules.quiz.show', [$course, $module, $question]) }}" class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                    View
                                </a>
                                <a href="{{ route('admin.courses.modules.quiz.edit', [$course, $module, $question]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    Edit
                                </a>
                                <form action="{{ route('admin.courses.modules.quiz.destroy', [$course, $module, $question]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this question?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No questions</h3>
                <p class="mt-1 text-sm text-gray-500">Get started by creating a new quiz question.</p>
                <div class="mt-6">
                    <a href="{{ route('admin.courses.modules.quiz.create', [$course, $module]) }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Question
                    </a>
                </div>
            </div>
        @endif
    </main>
</div>
@endsection
