<?php
$content = file_get_contents(__DIR__ . '/../resources/views/trainer/dashboard.blade.php');
$lines = explode("\n", $content);
foreach ($lines as $i => $line) {
    if (strpos($line, 'wallet_balance') !== false || strpos($line, 'walletBalance') !== false) {
        echo "Line " . ($i + 1) . ": " . trim($line) . "\n";
    }
}
