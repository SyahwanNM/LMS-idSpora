<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$admin = User::where('email', 'admin@idspora.com')->first();
Auth::login($admin);

$request = Request::create('/admin/crm/certificates?debug=1&tab=courses', 'GET');
$controller = app(\App\Http\Controllers\CRM\CertificateController::class);
$response = $controller->index($request);

$html = $response->render();
file_put_contents('rendered_output.html', $html);
echo "HTML saved to rendered_output.html\n";
