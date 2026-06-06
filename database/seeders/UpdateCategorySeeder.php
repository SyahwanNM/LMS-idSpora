<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class UpdateCategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::query()->delete();

        $categories = [
            'Artificial Intelligence',
            'Machine Learning',
            'Literatur Review',
            'Digital Marketing',
            'UI/UX Design',
            'IT Management',
            'Programming',
            'Graphic Design',
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name, 'description' => '']);
        }
    }
}
