<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class UpdateDateOfIssue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:update';

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
        $invoices = Invoice::where('reservation_id', '!=', null)
            ->get();

        foreach ($invoices as $invoice) {
            $invoice->paid_at = $invoice->reservation->date_to;
            $invoice->save();
        }

        return Command::SUCCESS;
    }
}
