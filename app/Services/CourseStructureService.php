<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseTemplate;

class CourseStructureService
{
    public function buildModulesForLevel(string $level): array
    {
        $level = strtolower(trim($level));
        $sectionCount = match ($level) {
            'intermediate' => 7,
            'advanced' => 6,
            default => 5,
        };

        $rows = [];
        $orderNo = 1;

        for ($section = 1; $section <= $sectionCount; $section++) {
            $rows[] = [
                'order_no' => $orderNo++,
                'title' => 'Bagian ' . $section . ' - Materi',
                'description' => null,
                'type' => 'pdf',
                'is_required' => true,
                'duration' => 0,
            ];
            $rows[] = [
                'order_no' => $orderNo++,
                'title' => 'Bagian ' . $section . ' - Video',
                'description' => null,
                'type' => 'video',
                'is_required' => true,
                'duration' => 0,
            ];
            $rows[] = [
                'order_no' => $orderNo++,
                'title' => 'Bagian ' . $section . ' - Kuis',
                'description' => null,
                'type' => 'quiz',
                'is_required' => true,
                'duration' => 0,
            ];
        }

        return $rows;
    }

    public function syncCourse(Course $course, bool $replaceExisting = true): int
    {
        $existingCount = (int) $course->modules()->count();

        if ($existingCount > 0 && !$replaceExisting) {
            return 0;
        }

        if ($replaceExisting && $existingCount > 0) {
            $course->modules()->delete();
            $course->quizzes()->delete();
        }

        $rows = collect($this->buildModulesForLevel((string) $course->level))
            ->map(function (array $row) use ($course) {
                $row['course_id'] = $course->id;
                $row['content_url'] = '';
                $row['file_name'] = null;
                $row['mime_type'] = null;
                $row['file_size'] = 0;
                $row['preview_pages'] = 0;
                unset($row['is_required']);
                $row['created_at'] = now();
                $row['updated_at'] = now();

                return $row;
            })
            ->values()
            ->all();

        if (empty($rows)) {
            return 0;
        }

        CourseModule::insert($rows);

        // Auto-generate quizzes for the course
        $quizService = new QuizStructureService();
        $quizService->generateQuizzesForCourse($course, false);

        return count($rows);
    }

    public function syncTemplate(CourseTemplate $template, array $modules = [], ?string $level = null): void
    {
        $rows = $this->buildModulesForLevel($level ?? (string) $template->level);

        if (!empty($modules)) {
            foreach ($rows as $index => &$row) {
                if (!isset($modules[$index]) || !is_array($modules[$index])) {
                    continue;
                }

                $module = $modules[$index];

                if (array_key_exists('description', $module) && trim((string) $module['description']) !== '') {
                    $row['description'] = (string) $module['description'];
                }

                if (array_key_exists('duration', $module) && is_numeric($module['duration'])) {
                    $row['duration'] = max(0, (int) $module['duration']);
                }

                if (array_key_exists('is_required', $module)) {
                    $row['is_required'] = filter_var($module['is_required'], FILTER_VALIDATE_BOOLEAN);
                }
            }
            unset($row);
        }

        $template->modules()->delete();

        if (!empty($rows)) {
            $template->modules()->createMany($rows);
        }
    }
}