<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\TrainerActivityService;
use Illuminate\Console\Command;

class SyncTrainerAccountStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trainer:sync-account-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync trainer account statuses based on inactivity and penalties';

    /**
     * Execute the console command.
     */
    public function handle(TrainerActivityService $activityService): int
    {
        $trainers = User::query()
            ->whereRaw('LOWER(role) = ?', ['trainer'])
            ->get();

        foreach ($trainers as $trainer) {
            /** @var User $trainer */
            $activityService->refresh($trainer);
        }

        $this->info('Trainer account statuses synced: ' . $trainers->count());

        return self::SUCCESS;
    }
}
