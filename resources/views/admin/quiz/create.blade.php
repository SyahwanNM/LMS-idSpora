@extends('layouts.app')

@section('title', 'Add Quiz Question - ' . $module->title)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header -->
    <header class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <a href="{{ route('admin.courses.modules.quiz.index', [$course, $module]) }}" class="mr-4">
                        <svg class="w-6 h-6 text-gray-600 hover:text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Add Quiz Question</h1>
                        <p class="text-sm text-gray-600">{{ $module->title }} - {{ $course->name }}</p>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('admin.courses.modules.quiz.store', [$course, $module]) }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <!-- Question -->
                    <div>
                        <label for="question" class="block text-sm font-medium text-gray-700 mb-2">Question</label>
                        <textarea name="question" id="question" rows="4" required 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('question') border-red-500 @enderror"
                                  placeholder="Enter your question here">{{ old('question') }}</textarea>
                        @error('question')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Explanation -->
                    <div>
                        <label for="explanation" class="block text-sm font-medium text-gray-700 mb-2">Explanation (Optional)</label>
                        <textarea name="explanation" id="explanation" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('explanation') border-red-500 @enderror"
                                  placeholder="Provide explanation for the correct answer">{{ old('explanation') }}</textarea>
                        @error('explanation')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Points -->
                    <div>
                        <label for="points" class="block text-sm font-medium text-gray-700 mb-2">Points</label>
                        <input type="number" name="points" id="points" required min="1" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('points') border-red-500 @enderror"
                               value="{{ old('points', 1) }}" placeholder="1">
                        @error('points')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Answers -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Answer Choices</label>
                        <p class="text-sm text-gray-500 mb-4">Add at least 2 answer choices and mark the correct one(s).</p>
                        
                        <div id="answers-container">
                            @for($i = 0; $i < 4; $i++)
                            <div class="answer-item mb-4 p-4 border border-gray-200 rounded-lg">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <input type="checkbox" name="answers[{{ $i }}][is_correct]" value="1" 
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                                               {{ old("answers.{$i}.is_correct") ? 'checked' : '' }}>
                                    </div>
                                    <div class="flex-1">
                                        <input type="text" name="answers[{{ $i }}][text]" 
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Enter answer choice {{ $i + 1 }}"
                                               value="{{ old("answers.{$i}.text") }}">
                                    </div>
                                    <div class="flex-shrink-0">
                                        <span class="text-sm text-gray-500">Correct</span>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                        
                        @error('answers')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('answers.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('admin.courses.modules.quiz.index', [$course, $module]) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Create Question
                    </button>
                </div>
            </form>
        </div>
    </main>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add validation to ensure at least one correct answer is selected
    const form = document.querySelector('form');
    const checkboxes = document.querySelectorAll('input[name*="[is_correct]"]');
    
    form.addEventListener('submit', function(e) {
        const checkedBoxes = Array.from(checkboxes).filter(cb => cb.checked);
        
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one correct answer.');
            return false;
        }
        
        // Check if at least 2 answers have text
        const textInputs = document.querySelectorAll('input[name*="[text]"]');
        const filledInputs = Array.from(textInputs).filter(input => input.value.trim() !== '');
        
        if (filledInputs.length < 2) {
            e.preventDefault();
            alert('Please provide at least 2 answer choices.');
            return false;
        }
    });
    
    // Add visual feedback for correct answers
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const answerItem = this.closest('.answer-item');
            if (this.checked) {
                answerItem.classList.add('border-green-300', 'bg-green-50');
            } else {
                answerItem.classList.remove('border-green-300', 'bg-green-50');
            }
        });
    });
});
</script>
@endsection
