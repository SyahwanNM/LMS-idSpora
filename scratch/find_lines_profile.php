<?php
$lines = file('resources/views/profile/index.blade.php');
foreach ($lines as $i => $line) {
    if (stripos($line, 'redeemVoucherModal') !== false || stripos($line, 'vouchers as') !== false) {
        echo ($i + 1) . ': ' . trim($line) . "\n";
    }
}
