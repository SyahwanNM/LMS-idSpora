<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\QuizStructureService;
use Illuminate\Console\Command;

class SyncCourseQuizzes extends Command
{
    protected $signature = 'courses:sync-quizzes {--level= : Sync only one level (beginner, intermediate, advanced)} {--preview : Show what would be created without saving}';

    protected $description = 'Generate or sync quizzes for courses based on standardized structure (section + final).';

    public function handle(QuizStructureService $quizService): int
    {
        $level = strtolower(trim((string) $this->option('level')));
        if ($level !== '' && !in_array($level, ['beginner', 'intermediate', 'advanced'], true)) {
            $this->error('Level harus beginner, intermediate, atau advanced.');
            return self::FAILURE;
        }

        $query = Course::query()->orderBy('id');
        if ($level !== '') {
            $query->where('level', $level);
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, Course> $courses */
        $courses = $query->get();

        if ($courses->isEmpty()) {
            $this->info('Tidak ada course yang perlu disinkronkan.');
            return self::SUCCESS;
        }

        $preview = (bool) $this->option('preview');
        $this->info(($preview ? 'Preview' : 'Sync') . ' quiz untuk ' . $courses->count() . ' course.');

        $bar = $this->output->createProgressBar($courses->count());
        $bar->start();

        foreach ($courses as $course) {
            /** @var Course $course */
            if ($preview) {
                $structure = $quizService->getQuizStructure($course);
                $this->newLine();
                $this->line(sprintf(
                    'Course #%d %s -> %d kuis (%d section + 1 final)',
                    $course->id,
                    $course->name,
                    $structure['total_quizzes'] ?? 0,
                    ($structure['total_quizzes'] ?? 1) - 1
                ));
                $bar->advance();
                continue;
            }

            $quizService->generateQuizzesForCourse($course, true);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($preview) {
            $this->info('Preview selesai, tidak ada data yang diubah.');
            return self::SUCCESS;
        }

        $this->info('Sinkronisasi kuis selesai.');

        return self::SUCCESS;
    }
}
