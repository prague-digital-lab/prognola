<?php

namespace App\Jobs;

use App\Models\ReadinessTime;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncReadinessTimesWithReservations implements ShouldQueue
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
        // Sync readiness times and reservations
        $readiness_times = ReadinessTime::where('date_to', '>=', Carbon::now()->subMonth())
            ->get();

        foreach ($readiness_times as $time) {

            $reservations = Reservation::whereNotIn('status', [Reservation::STATUS_NOT_ARRIVED, Reservation::STATUS_CANCELLED])
                ->where(function (Builder $query) use ($time) {
                    $query
                        ->where(function (Builder $query) use ($time) {
                            $query->where('date_from', '>=', $time->date_from)
                                ->where('date_from', '<', $time->date_to);
                        })
                        ->orWhere(function (Builder $query) use ($time) {
                            $query
                                ->where('date_to', '>', $time->date_from)
                                ->where('date_to', '<=', $time->date_to);
                        });
                })
                ->orderBy('date_from')
                ->get();

            /** @var $time ReadinessTime */
            $time->reservations()->sync($reservations);

            if ($reservations->count() > 0) {
                $time->is_active = true;
                $time->save();
            } else {
                $time->is_active = false;
                $time->save();
            }
        }
    }
}
