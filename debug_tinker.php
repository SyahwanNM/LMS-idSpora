<?php
$controller = new App\Http\Controllers\Admin\FinanceController();
$request = request();
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
