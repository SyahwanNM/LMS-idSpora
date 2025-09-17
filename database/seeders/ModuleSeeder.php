<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CourseModule;

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

        foreach ($courses as $course) {
            // Create sample modules for each course
            $modules = [
                [
                    'title' => 'Introduction to ' . $course->name,
                    'description' => 'Get started with the basics and understand the fundamentals of ' . strtolower($course->name) . '.',
                    'type' => 'video',
                    'content_url' => 'modules/sample-intro-video.mp4',
                    'is_free' => true,
                    'preview_pages' => 0,
                    'duration' => 15,
                    'order_no' => 1,
                ],
                [
                    'title' => 'Core Concepts',
                    'description' => 'Learn the essential concepts and principles that form the foundation of this course.',
                    'type' => 'video',
                    'content_url' => 'modules/sample-core-concepts.mp4',
                    'is_free' => false,
                    'preview_pages' => 0,
                    'duration' => 30,
                    'order_no' => 2,
                ],
                [
                    'title' => 'Practical Examples',
                    'description' => 'See real-world examples and case studies that demonstrate the concepts in action.',
                    'type' => 'pdf',
                    'content_url' => 'modules/sample-practical-examples.pdf',
                    'is_free' => false,
                    'preview_pages' => 3,
                    'duration' => 45,
                    'order_no' => 3,
                ],
                [
                    'title' => 'Hands-on Exercise',
                    'description' => 'Practice what you\'ve learned with guided exercises and projects.',
                    'type' => 'video',
                    'content_url' => 'modules/sample-hands-on-exercise.mp4',
                    'is_free' => false,
                    'preview_pages' => 0,
                    'duration' => 60,
                    'order_no' => 4,
                ],
                [
                    'title' => 'Knowledge Check',
                    'description' => 'Test your understanding with this comprehensive quiz covering all topics.',
                    'type' => 'quiz',
                    'content_url' => 'modules/sample-quiz.json',
                    'is_free' => false,
                    'preview_pages' => 0,
                    'duration' => 20,
                    'order_no' => 5,
                ],
            ];

            foreach ($modules as $moduleData) {
                CourseModule::create([
                    'course_id' => $course->id,
                    'title' => $moduleData['title'],
                    'description' => $moduleData['description'],
                    'type' => $moduleData['type'],
                    'content_url' => $moduleData['content_url'],
                    'is_free' => $moduleData['is_free'],
                    'preview_pages' => $moduleData['preview_pages'],
                    'duration' => $moduleData['duration'],
                    'order_no' => $moduleData['order_no'],
                ]);
            }
        }

        $this->command->info('Sample modules created successfully!');
    }
}