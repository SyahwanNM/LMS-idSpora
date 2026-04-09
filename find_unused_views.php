<?php
// find_unused_views.php

$baseDir = __DIR__;
$viewsDir = $baseDir . '/resources/views';

// Get all view files
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$viewFiles = [];

foreach ($iterator as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), '.blade.php')) {
        $path = $file->getRealPath();
        // Convert real path to view name format
        $relPath = str_replace([$viewsDir . DIRECTORY_SEPARATOR, $viewsDir . '/'], '', $path);
        $relPath = str_replace('\\', '/', $relPath); // Format to unix path
        
        // Exclude automatic/system folders
        if (str_starts_with($relPath, 'errors/') || str_starts_with($relPath, 'vendor/') || str_starts_with($relPath, 'mail/')) {
            continue;
        }
        
        $viewName = str_replace('/', '.', str_replace('.blade.php', '', $relPath));
        $viewPathName = str_replace('.blade.php', '', $relPath);
        $viewFiles[$path] = [
            'name' => $viewName,
            'path_name' => $viewPathName,
            'used' => false
        ];
    }
}

// Search all files in app/, routes/, and resources/
$searchDirs = [
    $baseDir . '/app',
    $baseDir . '/routes',
    $baseDir . '/resources',
];

foreach ($searchDirs as $dir) {
    if (!is_dir($dir)) continue;
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($it as $file) {
        if ($file->isFile() && in_array($file->getExtension(), ['php', 'vue', 'js'])) {
            $content = file_get_contents($file->getRealPath());
            $realPath = $file->getRealPath();
            
            foreach ($viewFiles as $path => &$info) {
                if ($info['used']) continue;
                
                $vName = $info['name'];
                $pName = $info['path_name'];
                
                if (str_contains($content, "'" . $vName . "'") || 
                    str_contains($content, '"' . $vName . '"') || 
                    str_contains($content, "'" . $pName . "'") || 
                    str_contains($content, '"' . $pName . '"')) {
                    
                    if ($realPath !== $path) {
                        $info['used'] = true;
                    }
                }
            }
        }
    }
}

$unused = [];
foreach ($viewFiles as $path => $info) {
    if (!$info['used']) {
        $unused[] = $info['path_name'] . '.blade.php';
    }
}

echo "Found " . count($unused) . " Potentially Unused Views:\n";
foreach ($unused as $u) {
    echo "- " . $u . "\n";
}
