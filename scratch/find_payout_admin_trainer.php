<?php
$content = file_get_contents(__DIR__ . '/../resources/views/admin/trainer/show.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (stripos($line, 'wallet') !== false || stripos($line, 'balance') !== false || stripos($line, 'payout') !== false || stripos($line, 'payment') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
