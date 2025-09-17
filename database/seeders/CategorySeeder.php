<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Web Development',
                'description' => 'Learn modern web development technologies and frameworks',
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'Build mobile applications for iOS and Android platforms',
            ],
            [
                'name' => 'Data Science',
                'description' => 'Analyze data and build machine learning models',
            ],
            [
                'name' => 'UI/UX Design',
                'description' => 'Create beautiful and user-friendly interfaces',
            ],
            [
                'name' => 'DevOps',
                'description' => 'Learn deployment, monitoring, and infrastructure management',
            ],
            [
                'name' => 'Cybersecurity',
                'description' => 'Protect systems and data from cyber threats',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}