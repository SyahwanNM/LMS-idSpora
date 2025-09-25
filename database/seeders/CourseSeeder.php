<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Category;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        
        if ($categories->count() == 0) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        $courses = [
            [
                'name' => 'Complete Web Development Bootcamp',
                'description' => 'Learn full-stack web development from scratch. Master HTML, CSS, JavaScript, React, Node.js, and MongoDB.',
                'category_id' => $categories->where('name', 'Web Development')->first()->id,
                'level' => 'beginner',
                'price' => 299000,
                'duration' => 120,
                'image' => null, // Will use placeholder
            ],
            [
                'name' => 'Advanced React Patterns',
                'description' => 'Master advanced React patterns, hooks, context, and state management for building scalable applications.',
                'category_id' => $categories->where('name', 'Web Development')->first()->id,
                'level' => 'advanced',
                'price' => 199000,
                'duration' => 40,
                'image' => null, // Will use placeholder
            ],
            [
                'name' => 'Mobile App Development with Flutter',
                'description' => 'Build beautiful, fast mobile applications for iOS and Android using Flutter framework.',
                'category_id' => $categories->where('name', 'Mobile Development')->first()->id,
                'level' => 'intermediate',
                'price' => 249000,
                'duration' => 80,
                'image' => null, // Will use placeholder
            ],
            [
                'name' => 'Data Science with Python',
                'description' => 'Learn data analysis, machine learning, and visualization using Python and popular libraries.',
                'category_id' => $categories->where('name', 'Data Science')->first()->id,
                'level' => 'intermediate',
                'price' => 349000,
                'duration' => 100,
                'image' => null, // Will use placeholder
            ],
            [
                'name' => 'UI/UX Design Fundamentals',
                'description' => 'Master the principles of user interface and user experience design for modern applications.',
                'category_id' => $categories->where('name', 'UI/UX Design')->first()->id,
                'level' => 'beginner',
                'price' => 179000,
                'duration' => 60,
                'image' => null, // Will use placeholder
            ],
            [
                'name' => 'DevOps and Cloud Deployment',
                'description' => 'Learn Docker, Kubernetes, AWS, and CI/CD pipelines for modern software deployment.',
                'category_id' => $categories->where('name', 'DevOps')->first()->id,
                'level' => 'intermediate',
                'price' => 279000,
                'duration' => 70,
                'image' => null, // Will use placeholder
            ],
            [
                'name' => 'Cybersecurity Essentials',
                'description' => 'Understand security threats, vulnerabilities, and best practices for protecting systems and data.',
                'category_id' => $categories->where('name', 'Cybersecurity')->first()->id,
                'level' => 'beginner',
                'price' => 229000,
                'duration' => 50,
                'image' => null, // Will use placeholder
            ],
        ];

        foreach ($courses as $courseData) {
            Course::create($courseData);
        }

        $this->command->info('Sample courses created successfully!');
    }
}