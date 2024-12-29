<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class FixInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:invoices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $invoices = Invoice::where('created_at', '>=', Carbon::now()->startOfYear())->get();
        foreach ($invoices as $invoice) {
            if ($invoice->voucher) {
                $invoice->number_serie_id = 2;
                $invoice->save();
            }
        }

        return Command::SUCCESS;
    }
}
