<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseModule;
use App\Services\CourseStructureService;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = Course::all();

        if ($courses->count() == 0) {
            $this->command->info('No courses found. Please create courses first.');
            return;
        }

        $structureService = app(CourseStructureService::class);

        foreach ($courses as $course) {
            CourseModule::where('course_id', $course->id)->delete();

            foreach ($structureService->buildModulesForLevel((string) $course->level) as $moduleData) {
                CourseModule::create([
                    'course_id' => $course->id,
                    'title' => $moduleData['title'],
                    'description' => 'Struktur standar untuk level ' . ucfirst((string) $course->level),
                    'type' => $moduleData['type'],
                    'content_url' => '',
                    'is_free' => false,
                    'preview_pages' => 0,
                    'duration' => $moduleData['duration'],
                    'order_no' => $moduleData['order_no'],
                ]);
            }
        }

        $this->command->info('Sample modules created successfully!');
    }
}