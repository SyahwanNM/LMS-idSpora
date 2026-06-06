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
            ['name' => 'Artificial Intelligence', 'description' => ''],
            ['name' => 'Machine Learning', 'description' => ''],
            ['name' => 'Literatur Review', 'description' => ''],
            ['name' => 'Digital Marketing', 'description' => ''],
            ['name' => 'UI/UX Design', 'description' => ''],
            ['name' => 'IT Management', 'description' => ''],
            ['name' => 'Programming', 'description' => ''],
            ['name' => 'Graphic Design', 'description' => ''],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}