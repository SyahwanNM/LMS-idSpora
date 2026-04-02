<?php

namespace App\Console\Commands;

use App\Models\CourseTemplate;
use Illuminate\Console\Command;

class EnsureDefaultCourseTemplates extends Command
{
    protected $signature = 'courses:ensure-default-templates
        {--beginner=3 : Academic units for beginner (each unit = 3 slots)}
        {--intermediate=6 : Academic units for intermediate (each unit = 3 slots)}
        {--advanced=12 : Academic units for advanced (each unit = 3 slots)}
        {--prune : If set, prune extra template slots to match the target count}';

    protected $description = 'Ensure default Auto Template exists for beginner/intermediate/advanced with minimum units.';

    public function handle(): int
    {
        $targets = [
            'beginner' => max(1, (int) $this->option('beginner')),
            'intermediate' => max(1, (int) $this->option('intermediate')),
            'advanced' => max(1, (int) $this->option('advanced')),
        ];
        $prune = (bool) $this->option('prune');
        $levels = array_keys($targets);

        foreach ($levels as $level) {
            $baseName = 'Auto Template - ' . ucfirst($level);

            $template = CourseTemplate::query()
                ->where('name', $baseName)
                ->where('level', $level)
                ->orderByDesc('version')
                ->first();

            if (!$template) {
                $nextVersion = ((int) CourseTemplate::query()->where('name', $baseName)->max('version')) + 1;
                $template = CourseTemplate::create([
                    'name' => $baseName,
                    'category_id' => null,
                    'level' => $level,
                    'version' => max(1, $nextVersion),
                    'status' => 'active',
                    'created_by' => auth()->id() ?? null,
                    'description' => 'Auto-generated default template for level ' . $level,
                ]);

                $this->info("Created: {$baseName}");
            }

            $this->ensureTemplateUnits($template, (int) $targets[$level], $prune);
        }

        $this->info('Done.');
        return self::SUCCESS;
    }

    private function ensureTemplateUnits(CourseTemplate $template, int $targetUnits, bool $prune): void
    {
        $existingSlots = (int) $template->modules()->count();
        $targetUnits = max(1, $targetUnits);
        $targetSlots = $targetUnits * 3;

        if ($existingSlots > $targetSlots && $prune) {
            $toDelete = $existingSlots - $targetSlots;
            $ids = $template->modules()->orderByDesc('order_no')->limit($toDelete)->pluck('id')->all();
            if (!empty($ids)) {
                $template->modules()->whereIn('id', $ids)->delete();
            }
            $existingSlots = (int) $template->modules()->count();
        }

        if ($existingSlots >= $targetSlots) {
            $this->line("OK: {$template->name} slots={$existingSlots} (target={$targetSlots})");
            return;
        }

        $maxOrderNo = (int) $template->modules()->max('order_no');
        $nextOrderNo = max(0, $maxOrderNo) + 1;
        $startUnit = (int) floor($existingSlots / 3) + 1;

        $rows = [];
        for ($unit = $startUnit; $unit <= $targetUnits; $unit++) {
            $rows[] = [
                'order_no' => $nextOrderNo++,
                'title' => 'Module ' . $unit . ' - PDF Material',
                'description' => null,
                'type' => 'pdf',
                'is_required' => true,
                'duration' => 0,
            ];
            $rows[] = [
                'order_no' => $nextOrderNo++,
                'title' => 'Module ' . $unit . ' - Video Lesson',
                'description' => null,
                'type' => 'video',
                'is_required' => true,
                'duration' => 0,
            ];
            $rows[] = [
                'order_no' => $nextOrderNo++,
                'title' => 'Module ' . $unit . ' - Quiz',
                'description' => null,
                'type' => 'quiz',
                'is_required' => true,
                'duration' => 0,
            ];
        }

        $template->modules()->createMany($rows);
        $this->info("Updated: {$template->name} slots=" . (int) $template->modules()->count() . " (target={$targetSlots})");
    }
}
