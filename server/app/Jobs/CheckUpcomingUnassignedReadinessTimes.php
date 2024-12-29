<?php

namespace App\Jobs;

use App\Models\ReadinessTime;
use App\Notifications\ReadinessTime\DiscordUpcomingUnassignedReadinessTime;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Discord\DiscordMessage;

class CheckUpcomingUnassignedReadinessTimes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return DiscordMessage
     */
    public function handle()
    {
        $upcoming_times = ReadinessTime::where('date_from', '>', Carbon::now())
            ->where('date_from', '<=', Carbon::now()->endOfWeek())
            ->where('user_id', null)
            ->get();

        //        $reservation->notify((new DiscordReservationCancelled()));

        if (App::environment() == 'production') {
            $route = '1044920409142861876';

        } else {
            $route = '1111963282018947092';
        }

        if ($upcoming_times->count() > 0) {

            Notification::route('discord', $route)
                ->notify(new DiscordUpcomingUnassignedReadinessTime($upcoming_times));
        }

    }
}
