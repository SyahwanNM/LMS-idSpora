<?php
// refactor_views.php
$baseDir = __DIR__;
$viewsDir = $baseDir . '/resources/views';

$map = [
    'auth' => 'auth.auth',
    'sign-in' => 'auth.sign-in',
    'sign-up' => 'auth.sign-up',
    'forgot-password' => 'auth.forgot-password',
    'new-password' => 'auth.new-password',
    'verifikasi' => 'auth.verifikasi',

    'landing-page' => 'public.landing-page',
    'event' => 'public.event',
    'detail-event' => 'public.detail-event',

    'dashboard' => 'user.dashboard',
    'detail-event-registered' => 'user.detail-event-registered',
    'payment' => 'user.payment',

    'payment-course' => 'course.payment-course',
    'modul-course' => 'course.modul-course',
    'quiz-course' => 'course.quiz-course',
    'aturan-kuis' => 'course.aturan-kuis',
    'rating-course' => 'course.rating-course',
    'sertifikat-course' => 'course.sertifikat-course',
];

// 1. Move files
foreach ($map as $old => $new) {
    if ($old === 'dashboardcopy') continue;
    $oldPath = $viewsDir . '/' . $old . '.blade.php';
    
    // Convert dot notation to path
    $newPath = $viewsDir . '/' . str_replace('.', '/', $new) . '.blade.php';
    
    $newDir = dirname($newPath);
    if (!is_dir($newDir)) {
        mkdir($newDir, 0755, true);
    }
    
    if (file_exists($oldPath)) {
        rename($oldPath, $newPath);
    }
}

// DELETE dashboardcopy
if (file_exists($viewsDir . '/dashboardcopy.blade.php')) {
    unlink($viewsDir . '/dashboardcopy.blade.php');
}

function replaceInDir($dir, $map) {
    if (!is_dir($dir)) return;
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $ext = $file->getExtension();
            if ($ext === 'php') { // applies to both .php and .blade.php
                $path = $file->getRealPath();
                $content = file_get_contents($path);
                $original = $content;

                foreach ($map as $old => $new) {
                    $oldEscaped = preg_quote($old, '/');
                    
                    // Pattern 1: view('old')
                    $pattern = "/(view|make|@include|@extends|@component)(If)?\s*\(\s*(['\"])" . $oldEscaped . "\\3/";
                    $content = preg_replace($pattern, "$1$2($3$new$3", $content);
                    
                    // Pattern 2: view('/old')
                    $patternSlash = "/(view|make|@include|@extends|@component)(If)?\s*\(\s*(['\"])\/" . $oldEscaped . "\\3/";
                    $content = preg_replace($patternSlash, "$1$2($3$new$3", $content);
                    
                    // Pattern 3: return \view('old') -> very generic, just in case \view is used
                    // But (view) in regex already captures view without \. 
                    
                    // Allow optional spaces inside quotes? No, string literal is exact.
                }

                if ($content !== $original) {
                    file_put_contents($path, $content);
                }
            }
        }
    }
}

// Replace in routes
replaceInDir($baseDir . '/routes', $map);
// Replace in controllers and mailables
replaceInDir($baseDir . '/app', $map);
// Replace in views
replaceInDir($baseDir . '/resources/views', $map);

// Check if any files are somehow explicitly in other controllers using variable mapping e.g. "view( $page )", can't fix those dynamically, but normal views are fine.

echo "Views refactored successfully!\n";
