<?php

namespace App\Console\Commands;

use App\Models\Expense;
use Illuminate\Console\Command;

class FixPaymentDate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:paid_at';

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
        $received_invoices = Expense::all();

        foreach ($received_invoices as $invoice) {
            if ($invoice->payment_status === 'plan') {
                $invoice->paid_at = $invoice->due_at ?: $invoice->received_at;
                $invoice->save();
            }
        }
    }
}
