<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    // Admin methods for managing quiz questions
    public function index(Course $course, CourseModule $module)
    {
        $questions = $module->quizQuestions;
        return view('admin.quiz.index', compact('course', 'module', 'questions'));
    }

    public function create(Course $course, CourseModule $module)
    {
        return view('admin.quiz.create', compact('course', 'module'));
    }

    public function store(Request $request, Course $course, CourseModule $module)
    {
        $request->validate([
            'question' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string',
            'answers.*.is_correct' => 'boolean',
        ]);

        // Get next order number
        $nextOrder = $module->quizQuestions()->max('order_no') + 1;

        // Create question
        $question = QuizQuestion::create([
            'course_module_id' => $module->id,
            'question' => $request->question,
            'explanation' => $request->explanation,
            'points' => $request->points,
            'order_no' => $nextOrder,
        ]);

        // Create answers
        foreach ($request->answers as $index => $answerData) {
            QuizAnswer::create([
                'quiz_question_id' => $question->id,
                'answer_text' => $answerData['text'],
                'is_correct' => $answerData['is_correct'] ?? false,
                'order_no' => $index + 1,
            ]);
        }

        return redirect()->route('admin.courses.modules.quiz.index', [$course, $module])
            ->with('success', 'Quiz question created successfully!');
    }

    public function show(Course $course, CourseModule $module, QuizQuestion $question)
    {
        $question->load('answers');
        return view('admin.quiz.show', compact('course', 'module', 'question'));
    }

    public function edit(Course $course, CourseModule $module, QuizQuestion $question)
    {
        $question->load('answers');
        return view('admin.quiz.edit', compact('course', 'module', 'question'));
    }

    public function update(Request $request, Course $course, CourseModule $module, QuizQuestion $question)
    {
        $request->validate([
            'question' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string',
            'answers.*.is_correct' => 'boolean',
        ]);

        // Update question
        $question->update([
            'question' => $request->question,
            'explanation' => $request->explanation,
            'points' => $request->points,
        ]);

        // Delete existing answers
        $question->answers()->delete();

        // Create new answers
        foreach ($request->answers as $index => $answerData) {
            QuizAnswer::create([
                'quiz_question_id' => $question->id,
                'answer_text' => $answerData['text'],
                'is_correct' => $answerData['is_correct'] ?? false,
                'order_no' => $index + 1,
            ]);
        }

        return redirect()->route('admin.courses.modules.quiz.index', [$course, $module])
            ->with('success', 'Quiz question updated successfully!');
    }

    public function destroy(Course $course, CourseModule $module, QuizQuestion $question)
    {
        $question->delete();
        return redirect()->route('admin.courses.modules.quiz.index', [$course, $module])
            ->with('success', 'Quiz question deleted successfully!');
    }

    // User methods for taking quiz
    public function start(Course $course, CourseModule $module)
    {
        if ($module->type !== 'quiz') {
            abort(404, 'This module is not a quiz');
        }

        $questions = $module->quizQuestions;
        
        if ($questions->count() == 0) {
            return redirect()->route('user.modules.show', [$course, $module])
                ->with('error', 'No questions available for this quiz');
        }

        // Check if user already has an attempt
        $existingAttempt = QuizAttempt::where('user_id', Auth::id())
            ->where('course_module_id', $module->id)
            ->whereNull('completed_at')
            ->first();

        if ($existingAttempt) {
            return redirect()->route('user.quiz.take', [$course, $module, $existingAttempt]);
        }

        // Create new attempt
        $attempt = QuizAttempt::create([
            'user_id' => Auth::id(),
            'course_module_id' => $module->id,
            'total_questions' => $questions->count(),
            'started_at' => now(),
        ]);

        return redirect()->route('user.quiz.take', [$course, $module, $attempt]);
    }

    public function take(Course $course, CourseModule $module, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to quiz attempt');
        }

        if ($attempt->completed_at) {
            return redirect()->route('user.quiz.result', [$course, $module, $attempt]);
        }

        $questions = $module->quizQuestions;
        $currentQuestionIndex = count($attempt->answers ?? []);
        
        if ($currentQuestionIndex >= $questions->count()) {
            // Quiz completed
            $attempt->update(['completed_at' => now()]);
            return redirect()->route('user.quiz.result', [$course, $module, $attempt]);
        }

        $currentQuestion = $questions[$currentQuestionIndex];
        $currentQuestion->load('answers');

        return view('user.quiz.take', compact('course', 'module', 'attempt', 'currentQuestion', 'currentQuestionIndex'));
    }

    public function submitAnswer(Request $request, Course $course, CourseModule $module, QuizAttempt $attempt)
    {
        $request->validate([
            'answer_id' => 'required|exists:quiz_answers,id',
        ]);

        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to quiz attempt');
        }

        if ($attempt->completed_at) {
            return redirect()->route('user.quiz.result', [$course, $module, $attempt]);
        }

        $answer = QuizAnswer::findOrFail($request->answer_id);
        $question = $answer->question;

        // Get current answers
        $answers = $attempt->answers ?? [];
        
        // Add new answer
        $answers[] = [
            'question_id' => $question->id,
            'answer_id' => $answer->id,
            'is_correct' => $answer->is_correct,
            'points' => $answer->is_correct ? $question->points : 0,
            'answered_at' => now()->toISOString(),
        ];

        // Update attempt
        $attempt->update([
            'answers' => $answers,
            'correct_answers' => collect($answers)->where('is_correct', true)->count(),
            'score' => collect($answers)->sum('points'),
        ]);

        // Check if quiz is completed
        if (count($answers) >= $attempt->total_questions) {
            $attempt->update(['completed_at' => now()]);
            return redirect()->route('user.quiz.result', [$course, $module, $attempt]);
        }

        return redirect()->route('user.quiz.take', [$course, $module, $attempt]);
    }

    public function result(Course $course, CourseModule $module, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to quiz attempt');
        }

        $questions = $module->quizQuestions;
        $questions->load('answers');

        return view('user.quiz.result', compact('course', 'module', 'attempt', 'questions'));
    }
}