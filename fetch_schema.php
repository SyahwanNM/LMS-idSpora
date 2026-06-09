<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$models = [
    'App\Models\TrainerNotification',
    'App\Models\TrainerAssignment',
    'App\Models\EventTrainerModule',
    'App\Models\Withdrawal',
    'App\Models\Enrollment',
    'App\Models\CourseModule',
    'App\Models\CourseUnit'
];

$schema = [];
foreach($models as $m) {
    if(class_exists($m)) {
        $table = (new $m)->getTable();
        $columns = Illuminate\Support\Facades\Schema::getColumnListing($table);
        $schema[$m] = [
            'table' => $table,
            'columns' => $columns
        ];
    }
}

echo json_encode($schema, JSON_PRETTY_PRINT);
