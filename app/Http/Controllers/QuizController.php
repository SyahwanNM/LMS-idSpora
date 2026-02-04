<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\QuizQuestion;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\Enrollment;
use App\Models\Progress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class QuizController extends Controller
{
    private int $passingPercent = 75;

    private function syncProgressIfPassed(Course $course, CourseModule $module, QuizAttempt $attempt): void
    {
        if (($module->type ?? null) !== 'quiz') {
            return;
        }

        if (!$attempt->completed_at) {
            return;
        }

        if (!$attempt->isPassed($this->passingPercent)) {
            return;
        }

        $enrollment = Enrollment::query()
            ->where('user_id', $attempt->user_id)
            ->where('course_id', $course->id)
            ->first();

        if (!$enrollment) {
            return;
        }

        Progress::query()->updateOrCreate(
            [
                'enrollment_id' => $enrollment->id,
                'course_module_id' => $module->id,
            ],
            [
                'completed' => true,
            ]
        );
    }

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

        $questions = $module->quizQuestions()->with('answers')->get();

        if ($questions->count() === 0) {
            return redirect()->route('user.modules.show', [$course, $module])
                ->with('error', 'No questions available for this quiz');
        }

        $answers = collect($attempt->answers ?? []);
        $answeredQuestionIds = $answers->pluck('question_id')->filter()->unique()->values()->all();

        // Determine which question to show
        $requestedIndex = request()->query('q');
        $requestedIndex = is_numeric($requestedIndex) ? (int) $requestedIndex : null;

        if ($requestedIndex !== null) {
            $currentQuestionIndex = max(0, min($requestedIndex, $questions->count() - 1));
        } else {
            // Default: show first unanswered question, or finish if all answered
            $currentQuestionIndex = $questions->search(function ($q) use ($answeredQuestionIds) {
                return !in_array($q->id, $answeredQuestionIds, true);
            });
            if ($currentQuestionIndex === false) {
                $attempt->update(['completed_at' => now()]);
                return redirect()->route('user.quiz.result', [$course, $module, $attempt]);
            }
        }

        // Timer: default to 5 minutes if duration isn't set
        $durationSeconds = (int) ($module->duration ?? 0);
        if ($durationSeconds <= 0) {
            $durationSeconds = 300;
        }

        $endsAtIso = null;
        if ($attempt->started_at) {
            $endsAtIso = $attempt->started_at->copy()->addSeconds($durationSeconds)->toISOString();
        }

        $currentQuestion = $questions[$currentQuestionIndex];

        return view('user.quiz.take', [
            'course' => $course,
            'module' => $module,
            'attempt' => $attempt,
            'questions' => $questions,
            'currentQuestion' => $currentQuestion,
            'currentQuestionIndex' => $currentQuestionIndex,
            'answeredQuestionIds' => $answeredQuestionIds,
            'endsAtIso' => $endsAtIso,
        ]);
    }

    public function submitAnswer(Request $request, Course $course, CourseModule $module, QuizAttempt $attempt)
    {
        $request->validate([
            'question_id' => 'required|exists:quiz_questions,id',
            'answer_id' => 'required|exists:quiz_answers,id',
        ]);

        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to quiz attempt');
        }

        if ($attempt->completed_at) {
            return redirect()->route('user.quiz.result', [$course, $module, $attempt]);
        }

        $questionId = (int) $request->question_id;
        $answerId = (int) $request->answer_id;

        $question = QuizQuestion::where('id', $questionId)
            ->where('course_module_id', $module->id)
            ->firstOrFail();

        $answer = QuizAnswer::where('id', $answerId)
            ->where('quiz_question_id', $question->id)
            ->firstOrFail();

        // Overwrite existing answer for this question (if any)
        $answers = collect($attempt->answers ?? [])
            ->reject(fn ($a) => (int)($a['question_id'] ?? 0) === $question->id)
            ->values();

        $answers->push([
            'question_id' => $question->id,
            'answer_id' => $answer->id,
            'is_correct' => $answer->is_correct,
            'points' => $answer->is_correct ? $question->points : 0,
            'answered_at' => now()->toISOString(),
        ]);

        // Keep answers ordered by question order_no for consistent navigation
        $orderMap = $module->quizQuestions()->pluck('order_no', 'id')->toArray();
        $answers = $answers->sortBy(function ($a) use ($orderMap) {
            $qid = (int)($a['question_id'] ?? 0);
            return $orderMap[$qid] ?? PHP_INT_MAX;
        })->values();

        // Update attempt
        $attempt->update([
            'answers' => $answers->all(),
            'correct_answers' => $answers->where('is_correct', true)->count(),
            'score' => $answers->sum('points'),
        ]);

        // Check if quiz is completed
        if ($answers->count() >= $attempt->total_questions) {
            $attempt->update(['completed_at' => now()]);
            $this->syncProgressIfPassed($course, $module, $attempt);
            return redirect()->route('user.quiz.result', [$course, $module, $attempt]);
        }

        // Go to next unanswered question (or next sequential)
        $questions = $module->quizQuestions()->get();
        $answeredIds = $answers->pluck('question_id')->filter()->unique()->values()->all();
        $currentIdx = $questions->search(fn ($q) => (int)$q->id === $question->id);
        $currentIdx = $currentIdx === false ? 0 : (int) $currentIdx;

        $nextIdx = null;
        for ($i = $currentIdx + 1; $i < $questions->count(); $i++) {
            if (!in_array($questions[$i]->id, $answeredIds, true)) { $nextIdx = $i; break; }
        }
        if ($nextIdx === null) {
            for ($i = 0; $i < $questions->count(); $i++) {
                if (!in_array($questions[$i]->id, $answeredIds, true)) { $nextIdx = $i; break; }
            }
        }
        if ($nextIdx === null) {
            $attempt->update(['completed_at' => now()]);
            $this->syncProgressIfPassed($course, $module, $attempt);
            return redirect()->route('user.quiz.result', [$course, $module, $attempt]);
        }

        return redirect()->route('user.quiz.take', [$course, $module, $attempt, 'q' => $nextIdx]);
    }

    public function finish(Course $course, CourseModule $module, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to quiz attempt');
        }
        if (!$attempt->completed_at) {
            $attempt->update(['completed_at' => now()]);
        }
        $this->syncProgressIfPassed($course, $module, $attempt);
        return redirect()->route('user.quiz.result', [$course, $module, $attempt]);
    }

    public function result(Course $course, CourseModule $module, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to quiz attempt');
        }

        $this->syncProgressIfPassed($course, $module, $attempt);

        $questions = $module->quizQuestions;
        $questions->load('answers');

        return view('user.quiz.result', compact('course', 'module', 'attempt', 'questions'));
    }
}