<?php

namespace App\Jobs;

use App\Models\ReadinessTime;
use App\Notifications\ReadinessTime\DiscordRemindNextWeekReadinessTimesFriday;
use App\Notifications\ReadinessTime\DiscordRemindNextWeekReadinessTimesSaturday;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;

class RemindNextWeekReadinessTimes implements ShouldQueue
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
     * @return void
     */
    public function handle()
    {
        if (App::environment() == 'production') {
            $route = '1044920409142861876';

        } else {
            $route = '1111963282018947092';
        }

        $next_week_readiness_times = ReadinessTime::where('date_from', '>=', Carbon::now()->addWeek()->startOfWeek())
            ->where('date_from', '<=', Carbon::now()->addWeek()->endOfWeek())
            ->get();

        // If there is one or more
        if (Carbon::now()->isFriday()) {
            Notification::route('discord', $route)
                ->notify(new DiscordRemindNextWeekReadinessTimesFriday($next_week_readiness_times));
        } else {
            Notification::route('discord', $route)
                ->notify(new DiscordRemindNextWeekReadinessTimesSaturday($next_week_readiness_times));
        }
    }
}
