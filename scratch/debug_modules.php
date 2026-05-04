<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$modules = App\Models\CourseModule::take(20)->get(['id','course_id','title','type','order_no']);
foreach ($modules as $m) {
    echo "ID:{$m->id} course:{$m->course_id} order:{$m->order_no} type:{$m->type} title:{$m->title}\n";
}

echo "\n--- Units ---\n";
$units = App\Models\CourseUnit::take(20)->get();
foreach ($units as $u) {
    echo "Unit ID:{$u->id} course:{$u->course_id} unit_no:{$u->unit_no} title:{$u->title}\n";
}
