<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Quiz;
use Illuminate\Support\Collection;

class QuizStructureService
{
    /**
     * Generate quizzes for a course based on its level and structure
     * 
     * Section quizzes: 5 questions, 10 minutes each
     * Final quiz: 20 questions, 30 minutes (or more based on sections)
     */
    public function generateQuizzesForCourse(Course $course, bool $replaceExisting = true): void
    {
        if ($replaceExisting) {
            $course->quizzes()->delete();
        }

        $modules = $course->modules()->orderBy('order_no')->get();
        if ($modules->isEmpty()) {
            return;
        }

        // Get quiz modules (every 3rd module starting from position 3 is a quiz)
        $quizModules = $modules->filter(function ($module, $key) {
            return ($key + 1) % 3 === 0; // positions 3, 6, 9, 12, 15...
        });

        $quizModuleCollection = $quizModules->values();
        if ($quizModuleCollection->isEmpty()) {
            return;
        }

        // Create section quizzes for each bagian
        foreach ($quizModuleCollection as $index => $quizModule) {
            $bagianNumber = (int) floor($index) + 1;

            // Check if this is the last quiz (final quiz)
            $isFinal = ($index === count($quizModuleCollection) - 1);

            $quizData = [
                'course_id' => $course->id,
                'course_module_id' => $isFinal ? null : $quizModule->id,
                'title' => $isFinal
                    ? "Final Quiz - Semua Modul"
                    : "Bagian {$bagianNumber} - Kuis",
                'description' => $isFinal
                    ? "Kuis final yang membahas semua bab dari awal hingga akhir. Pastikan Anda sudah mengerjakan semua kuis bagian terlebih dahulu."
                    : "Kuis untuk Bagian {$bagianNumber}. Kerjakan soal-soal berdasarkan materi di bagian ini saja.",
                'quiz_type' => $isFinal ? 'final_quiz' : 'section_quiz',
                'bagian_order_no' => $isFinal ? null : $bagianNumber,
                'duration_minutes' => $isFinal ? 30 : 10,
                'num_questions' => $isFinal ? 20 : 5,
                'is_active' => true,
                'total_questions' => $isFinal ? 20 : 5,
                'pass_score' => $isFinal ? 60 : 60,
            ];

            Quiz::create($quizData);
        }
    }

    /**
     * Get quiz structure for a course
     */
    public function getQuizStructure(Course $course): array
    {
        $quizzes = $course->quizzes()->orderByRaw(
            'CASE WHEN quiz_type = "section_quiz" THEN 1 ELSE 2 END, bagian_order_no'
        )->get();

        $sectionQuizzes = $quizzes->where('quiz_type', 'section_quiz')->values();
        $finalQuiz = $quizzes->firstWhere('quiz_type', 'final_quiz');

        return [
            'section_quizzes' => $sectionQuizzes,
            'final_quiz' => $finalQuiz,
            'total_quizzes' => $quizzes->count(),
        ];
    }

    /**
     * Get quiz stats for a section
     */
    public function getQuizStats(Course $course, ?int $bagianNumber = null): array
    {
        $query = $course->quizzes();

        if ($bagianNumber !== null) {
            $query->where('bagian_order_no', $bagianNumber)
                ->where('quiz_type', 'section_quiz');
        } else {
            $query->where('quiz_type', 'final_quiz');
        }

        $quiz = $query->first();
        if (!$quiz) {
            return [];
        }

        return [
            'quiz_id' => $quiz->id,
            'title' => $quiz->title,
            'duration_minutes' => $quiz->duration_minutes,
            'num_questions' => $quiz->num_questions,
            'pass_score' => $quiz->pass_score,
            'is_active' => $quiz->is_active,
            'is_final' => $quiz->quiz_type === 'final_quiz',
        ];
    }
}
