<?php

namespace Database\Seeders;

use App\Models\Reservation;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run()
    {
        Reservation::factory()
            ->count(1000)
            ->create();

        foreach (Reservation::all() as $reservation) {
            $reservation->campaigns()->attach([rand(1, 7)]);
        }
    }
}
