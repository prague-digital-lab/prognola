<?php

namespace App\Console\Commands;

use App\Models\BankPayment;
use Illuminate\Console\Command;

class SyncBankPaymentCounterBankAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-bank-payment-counter-bank-accounts';

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
        foreach (BankPayment::all() as $bankPayment) {
            $this->info('Syncing bank payment '.$bankPayment->id);
            $bankPayment->syncOrCreateCounterBankAccount();
        }
    }
}
