<?php

namespace Database\Seeders;

use App\Models\Campaign;
use Illuminate\Database\Seeder;

class CampaignSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $c = new Campaign;
        $c->name = 'Google (organicky)';
        $c->save();

        $c = new Campaign;
        $c->name = 'Facebook (organicky)';
        $c->save();

        $c = new Campaign;
        $c->name = 'Google Ads (PPC)';
        $c->save();

        $c = new Campaign;
        $c->name = 'Facebook (PPC)';
        $c->save();

        $c = new Campaign;
        $c->name = 'Hotel 1';
        $c->save();

        $c = new Campaign;
        $c->name = 'Hotel 2';
        $c->save();

        $c = new Campaign;
        $c->name = 'Hotel 3';
        $c->save();

    }
}
