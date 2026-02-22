<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "FOR_EVENT_34:\n";
$rows = \App\Models\Feedback::where('event_id',34)->get()->toArray();
echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo "\n\nRECENT_20:\n";
$rows2 = \App\Models\Feedback::orderBy('id','desc')->limit(20)->get()->toArray();
echo json_encode($rows2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

echo "\n";
