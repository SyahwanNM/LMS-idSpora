<?php
// refactor_routes.php

$webPath = __DIR__ . '/routes/web.php';
$adminPath = __DIR__ . '/routes/admin.php';
$crmPath = __DIR__ . '/routes/crm.php';
$userPath = __DIR__ . '/routes/user.php';

$lines = file($webPath);

$adminLines = [];
$crmLines = [];
$userLines = [];
$webLines = [];

$imports = [];
// Scrape imports from line 9 to 31
for ($i = 8; $i < 31; $i++) {
    $imports[] = $lines[$i];
}
$importStr = implode("", $imports);

$header = "<?php\n\n" . $importStr . "\n";

$adminLines[] = $header;
$crmLines[] = $header;
$userLines[] = $header;

$ranges = [
    // Move to ADMIN
    ['start' => 41, 'end' => 58, 'dest' => 'admin'],
    ['start' => 74, 'end' => 77, 'dest' => 'admin'],
    ['start' => 119, 'end' => 140, 'dest' => 'admin'],
    ['start' => 392, 'end' => 485, 'dest' => 'admin'],
    ['start' => 528, 'end' => 540, 'dest' => 'admin'],
    ['start' => 570, 'end' => 583, 'dest' => 'admin'],

    // Move to CRM
    ['start' => 541, 'end' => 568, 'dest' => 'crm'],

    // Move to USER (requires auth)
    ['start' => 61, 'end' => 67, 'dest' => 'user'], // reseller auth
    ['start' => 80, 'end' => 84, 'dest' => 'user'], // detail-event
    ['start' => 212, 'end' => 220, 'dest' => 'user'], // payment event
    ['start' => 222, 'end' => 354, 'dest' => 'user'], // events, notifications
    ['start' => 389, 'end' => 390, 'dest' => 'user'], // dashboard
    // Note: line 388 is `Route::middleware(['auth'])->group(function () {` and 584 is `});`
    // lines 494-526 are under auth
    ['start' => 494, 'end' => 526, 'dest' => 'user'], 
];

for ($i = 0; $i < count($lines); $i++) {
    $lineNum = $i + 1;
    $moved = false;
    foreach ($ranges as $range) {
        if ($lineNum >= $range['start'] && $lineNum <= $range['end']) {
            if ($range['dest'] == 'admin') {
                $adminLines[] = $lines[$i];
            } elseif ($range['dest'] == 'crm') {
                $crmLines[] = $lines[$i];
            } elseif ($range['dest'] == 'user') {
                $userLines[] = $lines[$i];
            }
            $moved = true;
            break;
        }
    }
    
    if (!$moved) {
        $webLines[] = $lines[$i];
    }
}

file_put_contents($webPath, implode("", $webLines));
file_put_contents($adminPath, implode("", $adminLines));
file_put_contents($crmPath, implode("", $crmLines));
file_put_contents($userPath, implode("", $userLines));

echo "Routes split successfully!";
