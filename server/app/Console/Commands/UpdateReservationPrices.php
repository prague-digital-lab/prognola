<?php

namespace App\Console\Commands;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateReservationPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-reservation-prices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reservations = Reservation::where('date_from', '>', Carbon::now()->startOfDay())
            ->get();

        foreach ($reservations as $reservation) {
            $reservation->price_family = 1990;
            $reservation->price_adult = 690;
            $reservation->price_child = 540;
            $reservation->save();

            $this->info('Updated price of reservation '.$reservation->id);
        }
    }
}
