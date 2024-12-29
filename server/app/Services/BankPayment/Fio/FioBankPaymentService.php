<?php

namespace app\Services\BankPayment\Fio;

use App\Http\ApiClient\FioBankClient;
use App\Models\BankAccount;
use App\Models\BankPayment;
use app\Services\BankPayment\BankPaymentInternalTransferService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FioBankPaymentService
{
    public function syncFioAccounts(): void
    {
        Log::info('Syncing all Fio bank accounts.');

        $fio_accounts = BankAccount::where('bank', BankAccount::BANK_FIO_API)->get();

        foreach ($fio_accounts as $bank_account) {
            $this->syncFioAccount($bank_account);
            (new BankPaymentInternalTransferService())->findAndUpdateBankPaymentTransfers($bank_account);
        }
    }

    public function syncFioAccount(BankAccount $bank_account): void
    {
        Log::info('Syncing bank account ' . $bank_account->id);

        // Pull transactions from API.
        $transactions_data = (new FioBankClient)->getTransactions($bank_account, Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth());

        $transactions_collection = collect($transactions_data->accountStatement->transactionList->transaction);
        $current_amount = $transactions_data->accountStatement->info->closingBalance;

        // Save new transactions
        foreach ($transactions_collection as $transaction) {
            $this->createPaymentIfDoesNotExist($transaction, $bank_account);
        }

        // Update synced at date.
        $bank_account->synced_at = Carbon::now();
        $bank_account->current_amount = $current_amount;
        $bank_account->save();
    }

    private function createPaymentIfDoesNotExist(mixed $transaction, BankAccount $bank_account): void
    {
        // 1. option - transaction is already saved.
        $old_payment = BankPayment::where('amount', $transaction->column1->value)
            ->where('external_id', $transaction->column22->value)
            ->where('bank_account_id', $bank_account->id)
            ->first();

        if ($old_payment) {
            $payment = $old_payment;
        } else {
            $payment = new BankPayment;

            // 2. option - create new payment (income or expense)
            $payment->uuid = Str::uuid();
            $payment->workspace_id = $bank_account->workspace_id;
            $payment->bank_account_id = $bank_account->id;
            $payment->external_id = $transaction->column22->value;
            $payment->amount = $transaction->column1->value;
            $payment->issued_at = $transaction->column0->value;

            if ($payment->amount > 0) {
                $payment->type = 'income';
            } else {
                $payment->type = 'expense';
            }
        }

        // Update payment data
        $payment->description = @$transaction->column25->value;

        // "Vklad pokladnou"
        if ($payment->description === null) {
            $payment->description = @$transaction->column8->value;
        }

        $payment->sender_comment = @$transaction->column16->value;

        $payment->variable_symbol = @$transaction->column5->value;

        $payment->counter_account_number = @$transaction->column2->value;

        if ($payment->counter_account_number === null) {
            $payment->counter_account_number = @$transaction->column10->value;
        }

        $payment->counter_bank_number = @$transaction->column3->value;

        $payment->syncOrCreateCounterBankAccount();

        $payment->save();

    }

    private function checkIfBankNumberExist($bank_number)
    {
        return BankAccount::where('bank_number', $bank_number)->first();
    }
}
