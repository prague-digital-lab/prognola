<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DiscordTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

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
        $from = 'Prognola DIGAMENT <digament-rPVRrxjkVoMGqU9@inbox.prognola.com>';

        preg_match('#<(.*?)>#', $from, $sender);
        //        preg_match("#<(.*?)>#", $to, $recipient);
        $senderAddr = $sender[1];
        //        $recipientAddr = $recipient[1];

        dd($senderAddr);
    }
}
