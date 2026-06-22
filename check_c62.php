<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$modules = DB::table('course_module')->where('course_id', 62)->orderBy('order_no')->get();
foreach ($modules as $m) {
    echo $m->id . '|' . $m->order_no . '|' . $m->type . '|' . $m->title . PHP_EOL;
}
echo 'Total: ' . count($modules) . PHP_EOL;

echo PHP_EOL . '=== Units ===' . PHP_EOL;
$units = DB::table('course_units')->where('course_id', 62)->orderBy('unit_no')->get();
foreach ($units as $u) {
    echo $u->unit_no . '|' . $u->title . PHP_EOL;
}
