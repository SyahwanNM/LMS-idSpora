<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\UserPointsService;

class RecalculateUserPoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:recalculate-points {--user-id= : Recalculate for specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate points and badges for all users or a specific user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $userId = $this->option('user-id');
        $pointsService = app(UserPointsService::class);

        if ($userId) {
            $user = User::find($userId);
            if (!$user) {
                $this->error("User with ID {$userId} not found.");
                return 1;
            }
            
            $this->info("Recalculating points for user: {$user->name} (ID: {$user->id})");
            $pointsService->recalculateUserPoints($user);
            $this->info("✓ Points recalculated. New points: {$user->points}, Badge: {$user->badge}");
        } else {
            $this->info("Recalculating points for all users...");
            $users = User::all();
            $bar = $this->output->createProgressBar($users->count());
            $bar->start();

            foreach ($users as $user) {
                $pointsService->recalculateUserPoints($user);
                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info("✓ Recalculated points for {$users->count()} users.");
        }

        return 0;
    }
}
