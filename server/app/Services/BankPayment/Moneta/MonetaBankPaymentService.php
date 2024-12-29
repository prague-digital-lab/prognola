<?php

namespace app\Services\BankPayment\Moneta;

use App\Models\BankAccount;
use App\Models\BankPayment;
use app\Services\BankPayment\BankPaymentInternalTransferService;
use Carbon\Carbon;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MonetaBankPaymentService
{

    /**
     * Sync Moneta bank account.
     *
     * @param BankAccount $bank_account
     * @return void
     */
    public function syncAccount(BankAccount $bank_account, Carbon$date_from, Carbon$date_to): void
    {
        Log::info('Syncing Moneta bank account ' . $bank_account->id);

        $mbps = new MonetaBankPaymentService();

        $api_endpoint = 'https://api.moneta.cz';
        $token = $bank_account->api_token;

        $bank_account->external_id = $this->fetchExternalIdByToken($bank_account);
        $bank_account->save();

        Log::info("Fetching transactions from {$api_endpoint}.");
        // MONETA transaction API is paginated. Fetch all pages.
        $transactions = $mbps->fetchTransactions($bank_account, $api_endpoint, $token, $date_from, $date_to);

        Log::info("Fetching done.");

        // Convert data to bank_payments.
        Log::info("Converting fetched data to bank_payments.");


        foreach ($transactions as $key => $transaction) {
            Log::info("Syncing transaction $key.");
            $mbps->createPaymentIfDoesNotExist($transaction, $bank_account);
        }

        // Update synced at date.
        $bank_account->synced_at = Carbon::now();
        $bank_account->current_amount = $this->fetchCurrentBalance($bank_account);
        $bank_account->save();

        Log::info("Finding internal bank transfers");
        (new BankPaymentInternalTransferService())->findAndUpdateBankPaymentTransfers($bank_account);
    }

    /**
     * Fetch external ID of Moneta bank account.
     *
     * @param BankAccount $bank_account
     * @return string
     * @throws ConnectionException
     */
    public function fetchExternalIdByToken(BankAccount $bank_account): string
    {
        $api_token = $bank_account->api_token;

        $res = Http::withToken($api_token)
            ->get('https://api.moneta.cz/api/v3/vip/aisp/my/accounts');

        return $res->json('accounts')[0]['id'];
    }

    public function fetchTransactions(BankAccount $bankAccount, $api_endpoint, $api_token, Carbon $date_from, Carbon $date_to): Collection
    {
        $current_page = 0;
        $max_page = 0;

        $transactions = collect();

        while ($current_page <= $max_page) {

            Log::info("Fetching transactions from API - page $current_page.");

            $res = Http::withToken($api_token)
                ->throw()
                ->get($api_endpoint . '/api/v3/vip/aisp/my/accounts/' . $bankAccount->external_id . '/transactions', [
                    'fromDate' => $date_from->toIso8601String(),
                    'toDate' => $date_to->toIso8601String(),
                    'size' => 10,
                    'page' => $current_page
                ]);

            $max_page = $res->json('pageCount') - 1;
            Log::info("Setting max page to $max_page.");

            $transactions = $transactions->merge($res->json('transactions'));

            $current_page++;
        }

        Log::info("Max page fetched. Fetching is done.");

        return $transactions;
    }

    /**
     * @param mixed $transaction
     * @param BankAccount $bank_account
     * @return void
     */
    public function createPaymentIfDoesNotExist(mixed $transaction, BankAccount $bank_account): void
    {
        // Try to find existing bank_payment
        $old_payment = BankPayment::where('external_id', $transaction['entryReference'])
            ->where('bank_account_id', $bank_account->id)
            ->first();

        if ($old_payment) {
            $payment = $old_payment;
        } else {
            $payment = new BankPayment();

            // 2. option - create new payment (income or expense)
            $payment->uuid = Str::uuid();
            $payment->workspace_id = $bank_account->workspace_id;
            $payment->bank_account_id = $bank_account->id;
            $payment->external_id = $transaction['entryReference'];
        }

        $payment->issued_at = Carbon::parse($transaction['valueDate']['date']);

        if ($transaction['creditDebitIndicator'] == 'DBIT') {
            $payment->amount = $transaction['amount']['value'] * -1;
            $payment->type = BankPayment::TYPE_EXPENSE;
        } else {
            $payment->amount = $transaction['amount']['value'];
            $payment->type = BankPayment::TYPE_INCOME;
        }

        // Update payment data
        $payment->description = @$transaction['entryDetails']['transactionDetails']['references']['transactionDescription'];
        $payment->sender_comment = @$transaction['entryDetails']['transactionDetails']['remittanceInformation']['unstructured'];

        $payment->variable_symbol = @$transaction['entryDetails']['transactionDetails']['references']['endToEndIdentification'];

        $counter_account_number_data = @$transaction['entryDetails']['transactionDetails']['relatedParties']['creditorAccount']['identification']['other']['identification'];

        if ($counter_account_number_data !== null) {
            $payment->counter_account_number = $this->parseCounterAccountNumber($counter_account_number_data);
            $payment->counter_bank_number = $this->parseCounterAccountBankNumber($counter_account_number_data);
        }

        $payment->syncOrCreateCounterBankAccount();

        $payment->save();
    }

    private function parseCounterAccountNumber(mixed $counter_account_number_data): string
    {
        $counter_account_number_data = Str::replace(' ', '-', $counter_account_number_data);
        return explode('/', $counter_account_number_data)[0];
    }

    private function parseCounterAccountBankNumber(mixed $counter_account_number_data): string
    {
        return explode('/', $counter_account_number_data)[1];
    }

    private function fetchCurrentBalance(BankAccount $bank_account)
    {
        $api_token = $bank_account->api_token;

        $res = Http::withToken($api_token)
            ->get('https://api.moneta.cz/api/v3/vip/aisp/my/accounts/' . $bank_account->external_id . '/balance');

        return $res->json('balances')[0]['amount']['value'];
    }
}
