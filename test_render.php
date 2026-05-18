<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new App\Http\Controllers\Admin\FinanceController();
$request = request();
$admin = \App\Models\User::where('email', 'admin@idspora.com')->first();
if ($admin) {
    auth()->login($admin);
}
try {
    $response = $controller->trainers($request);
    $html = $response->render();
    echo "RENDER SUCCESS: " . strlen($html) . " bytes\n";
    file_put_contents('debug.html', $html);
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
