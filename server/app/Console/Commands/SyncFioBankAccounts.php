<?php

namespace App\Console\Commands;

use App\Models\Workspace;
use app\Services\BankPayment\Fio\FioBankPaymentService;
use Illuminate\Console\Command;

class SyncFioBankAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:sync-fio';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        (new FioBankPaymentService)->syncFioAccounts();

        return 0;
    }
}
