<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\CourseStructureService;
use Illuminate\Console\Command;

class SyncCourseStructure extends Command
{
    protected $signature = 'courses:sync-structure {--level= : Sync only one level (beginner, intermediate, advanced)} {--preview : Show what would be updated without saving}';

    protected $description = 'Sync all existing course modules to the standardized level-based structure.';

    public function handle(CourseStructureService $structureService): int
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
        $this->info(($preview ? 'Preview' : 'Sync') . ' untuk ' . $courses->count() . ' course.');

        $bar = $this->output->createProgressBar($courses->count());
        $bar->start();

        foreach ($courses as $course) {
            /** @var Course $course */
            if ($preview) {
                $this->newLine();
                $this->line(sprintf(
                    'Course #%d %s -> %d slot',
                    $course->id,
                    $course->name,
                    count($structureService->buildModulesForLevel((string) $course->level))
                ));
                $bar->advance();
                continue;
            }

            $structureService->syncCourse($course, true);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        if ($preview) {
            $this->info('Preview selesai, tidak ada data yang diubah.');
            return self::SUCCESS;
        }

        $this->info('Sinkronisasi course selesai.');

        return self::SUCCESS;
    }
}