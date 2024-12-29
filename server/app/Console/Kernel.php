<?php

namespace App\Console;

use App\Console\Commands\CountBalancePrognosis;
use App\Console\Commands\SyncFioBankAccounts;
use App\Console\Commands\SyncMoneta;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->job(new SyncFioBankAccounts)->everyFourHours();
        $schedule->job(new SyncMoneta)->everyFourHours();

        // Bank pairing
        //        $schedule->command(PairWorldlineBankPaymentsWithIncomes::class)->hourly();
        //        $schedule->command(PairStripeBankPaymentsWithInvoices::class)->hourly();

        // Stats
        $schedule->command(CountBalancePrognosis::class)->hourlyAt(15);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
