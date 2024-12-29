<?php

namespace App\Console\Commands;

use App\Models\BankPayment;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PairStripeBankPaymentsWithInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:pair-stripe';

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
        // Find not paired Stripe bank payments
        $stripe_not_paired_bank_payments = BankPayment::where('description', 'STRIPE TECHNOLOGY EU')
            ->where('paired_at', null)
            ->orderBy('issued_at')
            ->get();

        if ($stripe_not_paired_bank_payments->count() == 0) {
            $this->info('There is no stripe cumulative payment waiting for pairing with invoices.');

            return;
        } else {
            $this->info('Found '.$stripe_not_paired_bank_payments->count().' unpaired Stripe bank payments.');
        }

        // Try to sync invoices to payment
        foreach ($stripe_not_paired_bank_payments as $bank_payment) {
            $this->info("Trying to pair Stripe cumulative bank payment $bank_payment->id to invoices.");

            $stripe_not_paired_invoices = Invoice::where('payment_type', Invoice::PAYMENT_TYPE_STRIPE)
                ->where('paired_at', null)
                ->orderBy('paid_at')
                ->get();

            foreach ($stripe_not_paired_invoices as $invoice) {
                $this->info('Syncing invoice '.$invoice->id);

                // Sync invoice
                $invoice->paired_at = Carbon::now();
                $invoice->save();

                $invoice->bank_payments()->attach($bank_payment, [
                    'amount' => $invoice->price,
                ]);

                // Count synced invoices sum
                $stripe_bank_payment_plus_fees_estimate = $bank_payment->amount / 0.983;

                $this->info('Bank payment invoice sum: '.$bank_payment->invoices()->sum('price'));

                if ($bank_payment->invoices()->sum('price') >= $stripe_bank_payment_plus_fees_estimate) {
                    $bank_payment->paired_at = Carbon::now();
                    $bank_payment->save();

                    $this->info('Bank payment paired! Invoices sum: '.$bank_payment->invoices()->sum('price'));

                    break;
                }

            }

        }
    }
}
