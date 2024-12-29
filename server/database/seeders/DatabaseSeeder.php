<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call(UserSeeder::class);
        $this->call(OpeningTimeSeeder::class);
        $this->call(ReservationSeeder::class);
        $this->call(CampaignSeeder::class);
        $this->call(VoucherSeeder::class);
    }
}
