@extends('layouts.app')

@section('title', 'Quiz Result - ' . $module->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="{{ route('user.modules.show', [$course, $module]) }}" class="mr-4">
                        <svg class="w-6 h-6 text-gray-600 hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Quiz Result</h1>
                        <p class="text-sm text-gray-600">{{ $module->title }} - {{ $course->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Result Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Quiz Summary</h2>
                    
                    <!-- Score Circle -->
                    <div class="text-center mb-6">
                        <div class="relative inline-flex items-center justify-center w-24 h-24">
                            <svg class="w-24 h-24 transform -rotate-90" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="40" stroke="#e5e7eb" stroke-width="8" fill="none"/>
                                <circle cx="50" cy="50" r="40" stroke="{{ $attempt->isPassed() ? '#10b981' : '#ef4444' }}" 
                                        stroke-width="8" fill="none" 
                                        stroke-dasharray="{{ 2 * pi() * 40 }}" 
                                        stroke-dashoffset="{{ 2 * pi() * 40 * (1 - $attempt->percentage / 100) }}"
                                        class="transition-all duration-1000"/>
                            </svg>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-2xl font-bold {{ $attempt->isPassed() ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $attempt->percentage }}%
                                </span>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mt-2">Final Score</h3>
                        <p class="text-sm text-gray-500">Grade: {{ $attempt->grade }}</p>
                    </div>

                    <!-- Stats -->
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Correct Answers</span>
                            <span class="text-sm font-medium text-gray-900">{{ $attempt->correct_answers }}/{{ $attempt->total_questions }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Total Points</span>
                            <span class="text-sm font-medium text-gray-900">{{ $attempt->score }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Status</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $attempt->isPassed() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $attempt->isPassed() ? 'Passed' : 'Failed' }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Completed</span>
                            <span class="text-sm font-medium text-gray-900">{{ $attempt->completed_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-6 space-y-3">
                        <a href="{{ route('user.modules.show', [$course, $module]) }}" 
                           class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            <span>Back to Module</span>
                        </a>
                        
                        @if(!$attempt->isPassed())
                        <a href="{{ route('user.quiz.start', [$course, $module]) }}" 
                           class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            <span>Retake Quiz</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Question Review -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Question Review</h2>
                    
                    <div class="space-y-6">
                        @foreach($questions as $index => $question)
                        @php
                            $userAnswer = collect($attempt->answers)->firstWhere('question_id', $question->id);
                            $isCorrect = $userAnswer ? $userAnswer['is_correct'] : false;
                        @endphp
                        <div class="border border-gray-200 rounded-lg p-6 {{ $isCorrect ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                            <div class="flex items-start space-x-3 mb-4">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $isCorrect ? 'bg-green-100' : 'bg-red-100' }}">
                                        @if($isCorrect)
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">Question {{ $index + 1 }}</h3>
                                    <p class="text-gray-700 mb-4">{{ $question->question }}</p>
                                    
                                    <!-- Answer Choices -->
                                    <div class="space-y-2">
                                        @foreach($question->answers as $answer)
                                        <div class="flex items-center space-x-3 p-3 rounded-lg {{ $answer->is_correct ? 'bg-green-100 border border-green-200' : ($userAnswer && $userAnswer['answer_id'] == $answer->id ? 'bg-red-100 border border-red-200' : 'bg-gray-50') }}">
                                            <div class="flex-shrink-0">
                                                @if($answer->is_correct)
                                                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                @elseif($userAnswer && $userAnswer['answer_id'] == $answer->id)
                                                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                @else
                                                    <div class="w-4 h-4 border border-gray-300 rounded"></div>
                                                @endif
                                            </div>
                                            <span class="text-sm {{ $answer->is_correct ? 'font-medium text-green-800' : ($userAnswer && $userAnswer['answer_id'] == $answer->id ? 'font-medium text-red-800' : 'text-gray-700') }}">
                                                {{ $answer->answer_text }}
                                            </span>
                                            @if($answer->is_correct)
                                                <span class="ml-auto text-xs font-medium text-green-600">Correct Answer</span>
                                            @elseif($userAnswer && $userAnswer['answer_id'] == $answer->id)
                                                <span class="ml-auto text-xs font-medium text-red-600">Your Answer</span>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                    
                                    @if($question->explanation)
                                        <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                            <h4 class="text-sm font-medium text-blue-900 mb-1">Explanation:</h4>
                                            <p class="text-sm text-blue-800">{{ $question->explanation }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection
