<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan pembersihan event: setiap jam hapus event yang telah berjalan >= 6 jam
Schedule::command('events:cleanup')->hourly();

// Sync status Midtrans pending payments setiap 5 menit
// (menggantikan webhook yang tidak bisa hit server lokal)
Schedule::command('midtrans:sync-status')->everyFiveMinutes();

// Fix stale snap tokens dan sync expired status setiap 10 menit
Schedule::command('midtrans:fix-stale')->everyTenMinutes();

// Reminder deadline undangan trainer (H-2 & H-1) dijalankan otomatis setiap hari
Schedule::command('trainer:send-invitation-deadline-reminders')->dailyAt('08:00');

// Eskalasi keterlambatan deadline undangan trainer (ke trainer + admin)
Schedule::command('trainer:send-invitation-overdue-alerts')->dailyAt('09:00');
