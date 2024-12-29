<?php

namespace App\Console\Commands;

use App\Models\BankPayment;
use App\Models\Workspace;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PairWorldlineBankPaymentsWithIncomes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bank:pair-worldline {workspace}';

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
        $workspace = Workspace::where('url_slug', $this->argument('workspace'))
            ->firstOrFail();

        $not_paired_worldline_bank_payments = $workspace->bank_payments()
            ->where('type', BankPayment::TYPE_INCOME)
            ->where('paired_at', null)
            ->where('counter_account_number', '=', '14210287')
            ->where('counter_bank_number', '=', '0100')
            ->get();

        $incomes = $workspace->incomes()
            ->where('description', 'LIKE', '%Způsob úhrady: kartou%')
            ->where('paired_at', null)
            ->get();

        $this->info('Found bank payments: '.$not_paired_worldline_bank_payments->count());
        $this->info('Found incomes: '.$incomes->count());

        foreach ($not_paired_worldline_bank_payments as $bank_payment) {

            $description = $bank_payment->sender_comment;

            // Find date
            $date = Str::between($description, 'FRM ', ' TIL');
            $date = Str::replace('/', '.', $date);
            $date = Carbon::parse($date.'.'.date('Y'))->startOfDay();
            $date_end = Carbon::parse($date.'.'.date('Y'))->endOfDay();

            $synced_invoices = $incomes->where('paid_at', '>=', $date)
                ->where('paid_at', '<=', $date_end);

            if ($synced_invoices->count() > 0) {

                foreach ($synced_invoices as $synced_invoice) {
                    $bank_payment->incomes()->attach($synced_invoice, [
                        'amount' => $synced_invoice->amount,
                    ]);

                    $synced_invoice->paired_at = Carbon::now();
                    $synced_invoice->save();
                }

                $bank_payment->paired_at = Carbon::now();
                $bank_payment->save();

                $this->info("Syncing invoices to bank payment $bank_payment->id.");
            }

        }
    }
}
