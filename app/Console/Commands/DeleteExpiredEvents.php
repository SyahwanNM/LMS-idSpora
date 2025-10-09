<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon;

class DeleteExpiredEvents extends Command
{
    protected $signature = 'events:cleanup';
    protected $description = 'Soft delete events that have been running for 6 hours since start time';

    public function handle(): int
    {
        $now = Carbon::now();
        $count = 0;
        // Ambil event yang sudah mulai (event_date + event_time) dan lebih dari 6 jam
        $events = Event::query()
            ->whereNull('deleted_at')
            ->get()
            ->filter(function(Event $e) use ($now){
                if(empty($e->event_date)) return false;
                // Build start datetime
                $dateStr = $e->event_date instanceof Carbon ? $e->event_date->format('Y-m-d') : (string) $e->event_date;
                $timeStr = '00:00:00';
                if(!empty($e->event_time)){
                    $timeStr = $e->event_time instanceof Carbon ? $e->event_time->format('H:i:s') : (is_string($e->event_time) ? $e->event_time : '00:00:00');
                }
                try {
                    $start = Carbon::parse($dateStr.' '.$timeStr, config('app.timezone'));
                } catch (\Throwable $ex) {
                    return false;
                }
                return $start->diffInHours($now, false) >= 6; // sudah berjalan >= 6 jam
            });

        foreach($events as $event){
            $event->ended_at = $now;
            $event->save();
            $event->delete(); // soft delete
            $count++;
        }

        $this->info("Cleaned up {$count} event(s).");
        return self::SUCCESS;
    }
}
