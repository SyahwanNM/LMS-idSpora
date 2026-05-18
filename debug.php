<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Http\Controllers\Admin\FinanceController;
use Illuminate\Http\Request;

$controller = new FinanceController();
$request = Request::create('/admin/finance/trainers', 'GET');
$admin = \App\Models\User::where('email', 'admin@idspora.com')->first();
if ($admin) {
    auth()->login($admin);
}

try {
    $response = $controller->trainers($request);
    file_put_contents('debug.html', $response->render());
    echo "DONE\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
