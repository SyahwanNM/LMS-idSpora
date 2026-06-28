<?php
$content = file_get_contents(__DIR__ . '/../app/Http/Controllers/Trainer/TrainerController.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'function finance') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
