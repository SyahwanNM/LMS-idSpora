<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseModule;
use App\Models\Enrollment;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    private const PASSING_PERCENT   = 75;
    private const COOLDOWN_SECONDS  = 60;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function ensureEnrolled(Course $course, int $userId): bool
    {
        return Enrollment::where('user_id', $userId)
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->exists();
    }

    private function syncProgressIfPassed(Course $course, CourseModule $module, QuizAttempt $attempt): void
    {
        if (!$attempt->isPassed(self::PASSING_PERCENT)) {
            return;
        }
        \App\Models\Progress::updateOrCreate(
            ['user_id' => $attempt->user_id, 'course_module_id' => $module->id],
            ['completed_at' => $attempt->completed_at ?? now(), 'course_id' => $course->id]
        );
    }

    // -------------------------------------------------------------------------
    // GET /api/courses/{course}/modules/{module}/quiz
    // Returns quiz questions (without revealing correct answers)
    // -------------------------------------------------------------------------
    public function show(Request $request, Course $course, CourseModule $module)
    {
        if ($module->type !== 'quiz') {
            return response()->json(['status' => 'error', 'message' => 'Module ini bukan quiz.'], 404);
        }

        if (!$this->ensureEnrolled($course, $request->user()->id)) {
            return response()->json(['status' => 'error', 'message' => 'Anda belum terdaftar di course ini.'], 403);
        }

        $questions = $module->quizQuestions()->with('answers')->get()
            ->map(fn ($q) => [
                'id'       => $q->id,
                'question' => $q->question,
                'points'   => $q->points,
                'order_no' => $q->order_no,
                'answers'  => $q->answers->map(fn ($a) => [
                    'id'          => $a->id,
                    'answer_text' => $a->answer_text,
                    'order_no'    => $a->order_no,
                    // is_correct intentionally hidden from user
                ]),
            ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Quiz questions',
            'data'    => [
                'module_id'    => $module->id,
                'module_title' => $module->title,
                'total'        => $questions->count(),
                'questions'    => $questions,
            ],
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/courses/{course}/modules/{module}/quiz/start
    // Start a new quiz attempt
    // -------------------------------------------------------------------------
    public function start(Request $request, Course $course, CourseModule $module)
    {
        if ($module->type !== 'quiz') {
            return response()->json(['status' => 'error', 'message' => 'Module ini bukan quiz.'], 404);
        }

        $user = $request->user();

        if (!$this->ensureEnrolled($course, $user->id)) {
            return response()->json(['status' => 'error', 'message' => 'Anda belum terdaftar di course ini.'], 403);
        }

        $questions = $module->quizQuestions;
        if ($questions->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Belum ada soal untuk quiz ini.'], 422);
        }

        // Resume incomplete attempt if still valid
        $existing = QuizAttempt::where('user_id', $user->id)
            ->where('course_module_id', $module->id)
            ->whereNull('completed_at')
            ->first();

        if ($existing) {
            $isLastQuiz = !$course->modules()
                ->where('type', 'quiz')
                ->where('order_no', '>', $module->order_no)
                ->exists();
            $durationSeconds = ($isLastQuiz ? 15 : 10) * 60;
            $startedAt  = $existing->started_at ?? $existing->created_at;
            $expiredAt  = $startedAt?->copy()->addSeconds($durationSeconds);

            if ($expiredAt && $expiredAt->isPast()) {
                $existing->update(['completed_at' => Carbon::now('Asia/Jakarta')]);
                $this->syncProgressIfPassed($course, $module, $existing->fresh());
            } else {
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Melanjutkan attempt yang belum selesai.',
                    'data'    => $this->formatAttempt($existing),
                ]);
            }
        }

        // Cooldown check
        $lastFailed = QuizAttempt::where('user_id', $user->id)
            ->where('course_module_id', $module->id)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at')
            ->first();

        if ($lastFailed && !$lastFailed->isPassed(self::PASSING_PERCENT)) {
            $cooldownEndsAt = $lastFailed->completed_at->copy()->addSeconds(self::COOLDOWN_SECONDS);
            if ($cooldownEndsAt->isFuture()) {
                $remaining = (int) ceil(Carbon::now('Asia/Jakarta')->diffInSeconds($cooldownEndsAt, false));
                return response()->json([
                    'status'  => 'error',
                    'message' => "Tunggu {$remaining} detik sebelum mengulang kuis ini.",
                    'data'    => ['cooldown_seconds' => $remaining],
                ], 429);
            }
        }

        $attempt = QuizAttempt::create([
            'user_id'          => $user->id,
            'course_module_id' => $module->id,
            'total_questions'  => $questions->count(),
            'started_at'       => Carbon::now(),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Quiz dimulai.',
            'data'    => $this->formatAttempt($attempt),
        ], 201);
    }

    // -------------------------------------------------------------------------
    // POST /api/courses/{course}/modules/{module}/quiz/attempts/{attempt}/answer
    // Submit an answer for one question
    // -------------------------------------------------------------------------
    public function submitAnswer(Request $request, Course $course, CourseModule $module, QuizAttempt $attempt)
    {
        $user = $request->user();

        if ($attempt->user_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        if ($attempt->completed_at) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Quiz sudah selesai.',
                'data'    => $this->formatAttempt($attempt),
            ], 422);
        }

        $validated = $request->validate([
            'question_id' => 'required|integer|exists:quiz_questions,id',
            'answer_id'   => 'required|integer|exists:quiz_answers,id',
        ]);

        $question = QuizQuestion::where('id', $validated['question_id'])
            ->where('course_module_id', $module->id)
            ->firstOrFail();

        $answer = QuizAnswer::where('id', $validated['answer_id'])
            ->where('quiz_question_id', $question->id)
            ->firstOrFail();

        // Overwrite existing answer for this question
        $answers = collect($attempt->answers ?? [])
            ->reject(fn ($a) => (int) ($a['question_id'] ?? 0) === $question->id)
            ->values();

        $answers->push([
            'question_id' => $question->id,
            'answer_id'   => $answer->id,
            'is_correct'  => $answer->is_correct,
            'points'      => $answer->is_correct ? $question->points : 0,
            'answered_at' => Carbon::now('Asia/Jakarta')->toISOString(),
        ]);

        // Sort by question order
        $orderMap = $module->quizQuestions()->pluck('order_no', 'id')->toArray();
        $answers  = $answers->sortBy(fn ($a) => $orderMap[(int) ($a['question_id'] ?? 0)] ?? PHP_INT_MAX)->values();

        $attempt->update([
            'answers'         => $answers->all(),
            'correct_answers' => $answers->where('is_correct', true)->count(),
            'score'           => $answers->sum('points'),
        ]);

        $isCompleted = $answers->count() >= $attempt->total_questions;
        if ($isCompleted) {
            $attempt->update(['completed_at' => Carbon::now('Asia/Jakarta')]);
            $attempt->refresh();
            $this->syncProgressIfPassed($course, $module, $attempt);
        }

        return response()->json([
            'status'       => 'success',
            'message'      => $isCompleted ? 'Quiz selesai.' : 'Jawaban disimpan.',
            'is_completed' => $isCompleted,
            'data'         => $this->formatAttempt($attempt),
        ]);
    }

    // -------------------------------------------------------------------------
    // POST /api/courses/{course}/modules/{module}/quiz/attempts/{attempt}/finish
    // Force-finish the attempt (submit all answered so far)
    // -------------------------------------------------------------------------
    public function finish(Request $request, Course $course, CourseModule $module, QuizAttempt $attempt)
    {
        $user = $request->user();

        if ($attempt->user_id !== $user->id) {
            return response()->json(['status' => 'error', 'message' => 'Akses ditolak.'], 403);
        }

        if ($attempt->completed_at) {
            return response()->json([
                'status'  => 'success',
                'message' => 'Quiz sudah selesai sebelumnya.',
                'data'    => $this->formatResult($attempt, $module),
            ]);
        }

        $attempt->update(['completed_at' => Carbon::now('Asia/Jakarta')]);
        $attempt->refresh();
        $this->syncProgressIfPassed($course, $module, $attempt);

        return response()->json([
            'status'  => 'success',
            'message' => 'Quiz diselesaikan.',
            'data'    => $this->formatResult($attempt, $module),
        ]);
    }

    // -------------------------------------------------------------------------
    // GET /api/courses/{course}/modules/{module}/quiz/result
    // Get latest attempt result
    // -------------------------------------------------------------------------
    public function result(Request $request, Course $course, CourseModule $module)
    {
        $user = $request->user();

        $attempt = QuizAttempt::where('user_id', $user->id)
            ->where('course_module_id', $module->id)
            ->whereNotNull('completed_at')
            ->latest('completed_at')
            ->first();

        if (!$attempt) {
            return response()->json(['status' => 'error', 'message' => 'Belum ada hasil quiz.'], 404);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Hasil quiz',
            'data'    => $this->formatResult($attempt, $module),
        ]);
    }

    // -------------------------------------------------------------------------
    // Formatters
    // -------------------------------------------------------------------------

    private function formatAttempt(QuizAttempt $attempt): array
    {
        return [
            'attempt_id'      => $attempt->id,
            'total_questions' => $attempt->total_questions,
            'answered'        => count($attempt->answers ?? []),
            'correct_answers' => $attempt->correct_answers ?? 0,
            'score'           => $attempt->score ?? 0,
            'started_at'      => $attempt->started_at?->toISOString(),
            'completed_at'    => $attempt->completed_at?->toISOString(),
            'is_completed'    => !is_null($attempt->completed_at),
        ];
    }

    private function formatResult(QuizAttempt $attempt, CourseModule $module): array
    {
        $passingScore = self::PASSING_PERCENT;
        $isPassed     = $attempt->isPassed($passingScore);

        return [
            'attempt_id'      => $attempt->id,
            'score'           => $attempt->score ?? 0,
            'percentage'      => $attempt->percentage,
            'grade'           => $attempt->grade,
            'total_questions' => $attempt->total_questions,
            'correct_answers' => $attempt->correct_answers ?? 0,
            'passing_score'   => $passingScore,
            'is_passed'       => $isPassed,
            'started_at'      => $attempt->started_at?->toISOString(),
            'completed_at'    => $attempt->completed_at?->toISOString(),
        ];
    }
}
