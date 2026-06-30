<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Expenses where event is soft deleted or non-existent
$trashedEventExpenses = \App\Models\EventExpense::whereHas('event', function($q) {
    $q->onlyTrashed();
})->orWhereDoesntHave('event')->get();

echo "Found EventExpense records for deleted/soft-deleted events: " . $trashedEventExpenses->count() . "\n";

foreach ($trashedEventExpenses as $exp) {
    echo "Deleting expense ID {$exp->id}: {$exp->item} (Event ID: {$exp->event_id})\n";
    $exp->delete();
}

echo "Cleanup finished.\n";
