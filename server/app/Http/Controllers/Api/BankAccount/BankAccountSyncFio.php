<?php

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Controllers\Controller;
use app\Services\BankPayment\BankPaymentInternalTransferService;
use app\Services\BankPayment\Fio\FioBankPaymentService;
use Exception;
use Illuminate\Http\Request;

class BankAccountSyncFio extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);
        $bank_account = $workspace->bank_accounts
            ->where('uuid', $uuid)
            ->firstOrFail();

        $bps = new FioBankPaymentService;
        $bps->syncFioAccount($bank_account);
        (new BankPaymentInternalTransferService())->findAndUpdateBankPaymentTransfers($bank_account);

        return response()->json([
            'success' => true,
        ]);
    }
}
