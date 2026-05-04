<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseModule;
use App\Models\CourseTemplate;

class CourseTemplateCloneService
{
    /**
     * Clone template module slots into course modules.
     */
    public function cloneToCourse(Course $course, CourseTemplate $template, bool $replaceExisting = false): int
    {
        $existingCount = (int) $course->modules()->count();
        if ($existingCount > 0 && !$replaceExisting) {
            return 0;
        }

        if ($replaceExisting && $existingCount > 0) {
            $course->modules()->delete();
        }

        $rows = $template->modules()
            ->orderBy('order_no')
            ->get()
            ->map(function ($module) use ($course) {
                return [
                    'course_id' => $course->id,
                    'order_no' => (int) $module->order_no,
                    'title' => (string) $module->title,
                    'description' => $module->description,
                    'type' => (string) $module->type,
                    // Slot-only clone: trainer fills these later.
                    'content_url' => '',
                    'file_name' => null,
                    'mime_type' => null,
                    'file_size' => 0,
                    'is_free' => false,
                    'preview_pages' => 0,
                    'duration' => (int) ($module->duration ?? 0),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })
            ->values()
            ->all();

        if (empty($rows)) {
            return 0;
        }

        CourseModule::insert($rows);

        return count($rows);
    }

    /**
     * Clone exactly the number of slots needed based on unit count.
     * Uses template modules as blueprint, repeating the pattern if needed.
     */
    public function cloneSlotsByUnitCount(Course $course, CourseTemplate $template, int $unitCount): int
    {
        $slotsNeeded = $unitCount * 3;
        $templateModules = $template->modules()->orderBy('order_no')->get();
        
        if ($templateModules->isEmpty()) {
            return 0;
        }

        $rows = [];
        for ($i = 0; $i < $slotsNeeded; $i++) {
            // Use modulo to repeat template pattern if slotsNeeded > template modules count
            $blueprint = $templateModules->get($i % $templateModules->count());
            $unitNo = (int) floor($i / 3) + 1;
            $typeLabel = match($blueprint->type) {
                'pdf' => 'PDF Material',
                'video' => 'Video Lesson',
                'quiz' => 'Quiz',
                default => 'Material'
            };

            $rows[] = [
                'course_id' => $course->id,
                'order_no' => $i + 1,
                'title' => "Module $unitNo - $typeLabel",
                'description' => $blueprint->description,
                'type' => (string) $blueprint->type,
                'content_url' => '',
                'file_name' => null,
                'mime_type' => null,
                'file_size' => 0,
                'is_free' => false,
                'preview_pages' => 0,
                'duration' => (int) ($blueprint->duration ?? 0),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (empty($rows)) {
            return 0;
        }

        CourseModule::insert($rows);
        return count($rows);
    }
}
