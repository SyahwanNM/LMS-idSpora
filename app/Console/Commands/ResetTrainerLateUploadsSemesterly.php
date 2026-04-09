<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetTrainerLateUploadsSemesterly extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainer:reset-semester-late-uploads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset consecutive late-upload counters for all trainers (semester reset)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $affected = User::query()
            ->whereRaw('LOWER(role) = ?', ['trainer'])
            ->where(function ($query) {
                $query->where('late_uploads', '>', 0)
                    ->orWhere('consecutive_late_uploads', '>', 0);
            })
            ->update([
                'late_uploads' => 0,
                'consecutive_late_uploads' => 0,
                'updated_at' => now(),
            ]);

        $this->info('Semester reset completed. Trainers updated: ' . (int) $affected);

        return self::SUCCESS;
    }
}
