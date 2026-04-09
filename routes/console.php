<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwalkan pembersihan event: setiap jam hapus event yang telah berjalan >= 6 jam
Schedule::command('events:cleanup')->hourly();

// Reminder SLA undangan trainer (12 jam & 1 jam tersisa)
Schedule::command('trainer:send-invitation-deadline-reminders')->hourly();

// Eskalasi keterlambatan deadline undangan trainer (ke trainer + admin)
Schedule::command('trainer:send-invitation-overdue-alerts')->everyMinute();

// Sinkronisasi status akun trainer (active/inactive/suspended)
Schedule::command('trainer:sync-account-statuses')->dailyAt('00:15');

// Pemutihan semesteran: 1 Januari & 1 Juli
Schedule::command('trainer:reset-semester-late-uploads')->cron('5 0 1 1,7 *');
