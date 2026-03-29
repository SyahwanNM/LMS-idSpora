<?php
// refactor_controllers.php

$baseDir = __DIR__;
$controllersDir = $baseDir . '/app/Http/Controllers';

$map = [
    'PublicCourseController' => 'PublicWeb',
    'PublicEventController' => 'PublicWeb',
    'PublicPagesController' => 'PublicWeb',
    'LandingPageController' => 'PublicWeb',
    'SocialAuthController' => 'PublicWeb',
    'AuthController' => 'PublicWeb',

    'DashboardController' => 'User',
    'ProfileController' => 'User',
    'ProfileReminderController' => 'User',
    'UserModuleController' => 'User',
    'FeedbackController' => 'User',
    'NotificationsController' => 'User',
    'EventParticipationController' => 'User',
    'LearningTimeController' => 'User',
    'ResellerController' => 'User',

    'AdminController' => 'Admin',
    'EventController' => 'Admin',
    'CourseController' => 'Admin',
    'ModuleController' => 'Admin',
    'QuizController' => 'Admin',
    'UserManagementController' => 'Admin',
    'InvoiceController' => 'Admin',
    'ManualPaymentController' => 'Admin',
    'CourseManualPaymentController' => 'Admin',

    'CRMController' => 'CRM',
    'CertificateController' => 'CRM',
];

// PHP < 8.0 cannot use `Public` as a namespace. To be safe, I will rename the folder `Public` to `PublicWeb` 
// but tell the user about it, or actually since they use PHP 8.2 (Laravel 11), `Public` is allowed.
// Let's stick strictly to what the user requested, but capitalize it.
$map['PublicCourseController'] = 'Public';
$map['PublicEventController'] = 'Public';
$map['PublicPagesController'] = 'Public';
$map['LandingPageController'] = 'Public';
$map['SocialAuthController'] = 'Public';
$map['AuthController'] = 'Public';

$updatedClasses = [];

foreach ($map as $controller => $folder) {
    $oldPath = $controllersDir . '/' . $controller . '.php';
    $newDir = $controllersDir . '/' . $folder;
    $newPath = $newDir . '/' . $controller . '.php';

    if (!file_exists($oldPath)) {
        continue;
    }

    if (!is_dir($newDir)) {
        mkdir($newDir, 0755, true);
    }

    $content = file_get_contents($oldPath);

    // Replace namespace
    $oldNamespace = 'namespace App\Http\Controllers;';
    $newNamespace = 'namespace App\Http\Controllers\\' . $folder . ';';
    
    // Add use App\Http\Controllers\Controller if extended but not imported
    if (strpos($content, 'extends Controller') !== false && strpos($content, 'use App\Http\Controllers\Controller;') === false) {
        $newNamespace .= "\n\nuse App\Http\Controllers\Controller;";
    }

    $content = str_replace($oldNamespace, $newNamespace, $content);

    file_put_contents($newPath, $content);
    unlink($oldPath);
    
    $updatedClasses[$controller] = $folder;
}

// Now replace usages in all routes and app files
function replaceInDir($dir, $updatedClasses) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $path = $file->getRealPath();
            $content = file_get_contents($path);
            $modified = false;

            foreach ($updatedClasses as $controller => $folder) {
                $oldUse = 'use App\Http\Controllers\\' . $controller . ';';
                $newUse = 'use App\Http\Controllers\\' . $folder . '\\' . $controller . ';';
                if (strpos($content, $oldUse) !== false) {
                    $content = str_replace($oldUse, $newUse, $content);
                    $modified = true;
                }
                
                $oldFqn = 'App\Http\Controllers\\' . $controller . '::class';
                $newFqn = 'App\Http\Controllers\\' . $folder . '\\' . $controller . '::class';
                if (strpos($content, $oldFqn) !== false) {
                    $content = str_replace($oldFqn, $newFqn, $content);
                    $modified = true;
                }
                
                $oldFqnStr = '\'App\Http\Controllers\\' . $controller . '\'';
                $newFqnStr = '\'App\Http\Controllers\\' . $folder . '\\' . $controller . '\'';
                if (strpos($content, $oldFqnStr) !== false) {
                    $content = str_replace($oldFqnStr, $newFqnStr, $content);
                    $modified = true;
                }
            }

            if ($modified) {
                file_put_contents($path, $content);
            }
        }
    }
}

replaceInDir($baseDir . '/routes', $updatedClasses);
replaceInDir($baseDir . '/app', $updatedClasses);

echo "Controllers grouped successfully!\n";
