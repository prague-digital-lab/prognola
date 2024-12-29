<?php

namespace App\Console\Commands;

use App\Models\Income;
use App\Models\Invoice;
use Illuminate\Console\Command;

class SyncInvoicesToIncome extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-invoices-to-income';

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
        Income::truncate();
        $invoices = Invoice::whereIn('payment_status', [Invoice::PAYMENT_STATUS_PENDING, Invoice::PAYMENT_STATUS_PAID])->get();

        foreach ($invoices as $invoice) {
            $this->info("Syncing invoice {$invoice->id} to income.");
            $invoice->syncToIncome();
        }
    }
}
