<?php
$content = file_get_contents(__DIR__ . '/../resources/views/admin/trainer/show.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'wallet') !== false || strpos($line, 'balance') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
