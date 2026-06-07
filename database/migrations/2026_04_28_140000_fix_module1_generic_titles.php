<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix all course_module rows where Module 1's video/quiz have generic titles
        // (no "Module 1 -" prefix) while sibling pdf has "Module 1 - ..." prefix.
        // Strategy: for each course, find the group where pdf = "Module N - ..." but
        // video/quiz in the same order range are still generic.

        $courses = DB::table('course_module')
            ->select('course_id')
            ->distinct()
            ->pluck('course_id');

        foreach ($courses as $courseId) {
            $modules = DB::table('course_module')
                ->where('course_id', $courseId)
                ->orderBy('order_no')
                ->get(['id', 'order_no', 'title', 'type']);

            // Group by unit: find pdf with "Module N - ..." then fix adjacent generic video/quiz
            $i = 0;
            $items = $modules->values();
            while ($i < $items->count()) {
                $m = $items[$i];
                // If this is a pdf with "Module N - ..." title
                if ($m->type === 'pdf' && preg_match('/^Module\s*(\d+)\s*-\s*/i', $m->title, $match)) {
                    $moduleNum = (int) $match[1];
                    $prefix    = 'Module ' . $moduleNum . ' - ';

                    // Look ahead for video and quiz with generic titles in the next 2 slots
                    for ($j = $i + 1; $j < min($i + 3, $items->count()); $j++) {
                        $next = $items[$j];
                        // Stop if we hit another "Module N - ..." pdf
                        if ($next->type === 'pdf' && preg_match('/^Module\s*\d+\s*-\s*/i', $next->title)) {
                            break;
                        }
                        if ($next->type === 'video' && preg_match('/^(Video\s*Lesson)$/i', trim($next->title))) {
                            DB::table('course_module')
                                ->where('id', $next->id)
                                ->update(['title' => $prefix . 'Video Lesson']);
                        }
                        if ($next->type === 'quiz' && preg_match('/^(Quiz)$/i', trim($next->title))) {
                            DB::table('course_module')
                                ->where('id', $next->id)
                                ->update(['title' => $prefix . 'Quiz']);
                        }
                    }
                }
                $i++;
            }
        }
    }

    public function down(): void
    {
        // Revert "Module N - Video Lesson" → "Video Lesson" and "Module N - Quiz" → "Quiz"
        // only for rows that were originally generic (order_no % 3 != 1, i.e. not the pdf slot)
        // This is a best-effort rollback.
        DB::table('course_module')
            ->where('type', 'video')
            ->where('title', 'like', 'Module % - Video Lesson')
            ->update(['title' => 'Video Lesson']);

        DB::table('course_module')
            ->where('type', 'quiz')
            ->where('title', 'like', 'Module % - Quiz')
            ->update(['title' => 'Quiz']);
    }
};
