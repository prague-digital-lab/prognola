<?php

namespace App\Jobs;

use App\Models\Invoice;
use App\Models\Voucher;
use App\Services\VoucherOrderService;
use Carbon\Carbon;
use Exception;
use Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PairFioTransactionsWithVouchers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 5;

    /**
     * Execute the job.
     *
     * @throws Exception
     */
    public function handle(): void
    {
        Log::debug('Running automatical pairing of voucher orders (FIO API).');

        $transactions = $this->getFioTransactionsForLast30Days();
        $vouchers = $this->getVouchersWaitingForPayment();

        //        $corresponding_transactions = $transactions
        //            ->where('column5.value', '=', 2212544257);
        //
        //        dd(2200, $corresponding_transactions->first());

        /** @var Voucher $voucher */
        foreach ($vouchers as $voucher) {
            $searched_variable_symbol = $voucher->getVariableSymbol();

            // Try find corresponding transactions
            $corresponding_transactions = $transactions
                ->where('column5.value', '=', $searched_variable_symbol);

            if ($corresponding_transactions->count() == 0) {
                Log::debug("Voucher order $voucher->id: no payment found.");

                continue;
            }

            if ($corresponding_transactions->count() >= 2) {
                Log::debug("Voucher order $voucher->id: found two or more payments.");

                continue;
            }

            if ($voucher->price != $corresponding_transactions->first()['column1']['value']) {
                $t_price = $corresponding_transactions->first()['column1']['value'];

                Log::debug("Voucher order $voucher->id: found payment with different price. Order price: $voucher->price, found: $t_price");

                continue;
            }

            \Log::info("FIO transactions for voucher order $voucher->id found. Changing state to paid.");
            (new VoucherOrderService)->setPaid($voucher, true, Invoice::PAYMENT_TYPE_BANK_TRANSFER);
        }

    }

    /**
     * TODO: Get transactions from FIO API.
     */
    private function getFioTransactionsForLast30Days(): Collection
    {
        $date_from = Carbon::now()->subDays(30)->format('Y-m-d');
        $date_to = Carbon::now()->format('Y-m-d');

        $token = 'DpoyqyBSTPg56RYPLwtOSpewh2CP2LQG2M9vwVjUbKQF0pzLNxfvKgPRbs6AFeGI';

        $response = Http::get("https://www.fio.cz/ib_api/rest/periods/$token/$date_from/$date_to/transactions.json");

        $json = $response->json();

        return collect($json['accountStatement']['transactionList']['transaction']);
    }

    private function getVouchersWaitingForPayment(): \Illuminate\Database\Eloquent\Collection
    {
        return Voucher::where('order_status', Voucher::ORDER_STATUS_WAITING_FOR_PAYMENT)->get();
    }
}
