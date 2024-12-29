<?php

namespace app\Services\BankPayment;

use App\Models\BankAccount;
use App\Models\Workspace;
use Carbon\Carbon;

class BankPaymentInternalTransferService
{
    public function findAndUpdateBankPaymentTransfers(BankAccount $bank_account): void
    {
        // Detect new transfers
        $payments = $bank_account->bank_payments;
        $bank_accounts = $bank_account->workspace->bank_accounts;

        foreach ($payments as $payment) {

            // Check if payment counter account is an internal organisation bank_account.
            $counter_bank_account = $bank_accounts
                ->where('account_number', $payment->counter_account_number)
                ->where('bank_number', $payment->counter_bank_number)
                ->first();

            // Attach counter bank account to payment.
            if ($counter_bank_account !== null && $counter_bank_account->id !== $payment->bank_account->id) {
                $payment->transfer_bank_account_id = $counter_bank_account->id;
                $payment->paired_at = $payment->issued_at;
                $payment->save();
            }
        }
    }
}
