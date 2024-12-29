<?php

namespace App\Console\Commands;

use App\Models\BankAccount;
use app\Services\BankPayment\Moneta\MonetaBankPaymentService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SyncMoneta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-moneta';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync of single Moneta bank accounts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bank_accounts = BankAccount::where('bank', BankAccount::BANK_MONETA_API)->get();

        $mbps = new MonetaBankPaymentService();
        foreach ($bank_accounts as $bank_account) {
            $this->info("Starting sync of Moneta bank account $bank_account->id.");

            $mbps->syncAccount($bank_account, Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());

            $this->info("Sync is done.");
        }
    }
}
