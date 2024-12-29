<?php

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Controllers\Controller;
use App\Services\BankPaymentPairingService;
use Exception;
use Illuminate\Http\Request;

class BankAccountDestroy extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid, Request $request): void
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $bank_account = $workspace->bank_accounts()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $bank_payments = $bank_account->bank_payments();

        $bps = new BankPaymentPairingService;
        foreach ($bank_payments as $bank_payment) {
            $bps->recountBankPaymentPairingState($bank_payment);
        }

        $bank_payments->delete();

        $bank_account->delete();
    }
}
