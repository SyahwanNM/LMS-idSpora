@extends('layouts.app')

@section('title', 'Quiz - ' . $module->title)

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
                        <h1 class="text-2xl font-bold text-gray-900">Quiz</h1>
                        <p class="text-sm text-gray-600">{{ $module->title }} - {{ $course->name }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-sm text-gray-500">
                        Question {{ $currentQuestionIndex + 1 }} of {{ $attempt->total_questions }}
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Progress</span>
                    <span>{{ $currentQuestionIndex + 1 }} / {{ $attempt->total_questions }}</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                         style="width: {{ (($currentQuestionIndex + 1) / $attempt->total_questions) * 100 }}%"></div>
                </div>
            </div>

            <!-- Question -->
            <div class="mb-8">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-sm font-semibold text-green-600">{{ $currentQuestionIndex + 1 }}</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-semibold text-gray-900">Question {{ $currentQuestionIndex + 1 }}</h2>
                        <p class="text-sm text-gray-500">{{ $currentQuestion->points }} point{{ $currentQuestion->points > 1 ? 's' : '' }}</p>
                    </div>
                </div>
                
                <div class="bg-gray-50 rounded-lg p-6 mb-6">
                    <p class="text-lg text-gray-900 leading-relaxed">{{ $currentQuestion->question }}</p>
                </div>
            </div>

            <!-- Answer Choices -->
            <form action="{{ route('user.quiz.answer', [$course, $module, $attempt]) }}" method="POST">
                @csrf
                <div class="space-y-3 mb-8">
                    @foreach($currentQuestion->answers as $answer)
                    <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-colors duration-200">
                        <input type="radio" name="answer_id" value="{{ $answer->id }}" required
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                        <span class="ml-3 text-gray-900">{{ $answer->answer_text }}</span>
                    </label>
                    @endforeach
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200 flex items-center space-x-2">
                        <span>
                            @if($currentQuestionIndex + 1 < $attempt->total_questions)
                                Next Question
                            @else
                                Finish Quiz
                            @endif
                        </span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add visual feedback for selected answers
    const radioButtons = document.querySelectorAll('input[name="answer_id"]');
    const labels = document.querySelectorAll('label');
    
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove previous selections
            labels.forEach(label => {
                label.classList.remove('border-blue-500', 'bg-blue-100');
                label.classList.add('border-gray-200');
            });
            
            // Highlight selected answer
            if (this.checked) {
                const label = this.closest('label');
                label.classList.remove('border-gray-200');
                label.classList.add('border-blue-500', 'bg-blue-100');
            }
        });
    });
    
    // Prevent form submission without selection
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const selectedAnswer = document.querySelector('input[name="answer_id"]:checked');
        if (!selectedAnswer) {
            e.preventDefault();
            alert('Please select an answer before proceeding.');
            return false;
        }
    });
});
</script>
@endsection
