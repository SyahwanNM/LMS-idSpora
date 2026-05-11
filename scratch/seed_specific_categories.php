<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$categories = [
    'Web Development',
    'UI/UX Design',
    'Mobile Programming',
    'Digital Marketing',
    'Data Science',
    'Artificial Intelligence',
    'Cyber Security',
    'Cloud Computing',
    'Graphic Design',
    'Project Management'
];

foreach ($categories as $name) {
    \App\Models\Category::firstOrCreate(
        ['name' => $name],
        ['description' => $name . ' related courses']
    );
}

echo "Categories seeded successfully.\n";
