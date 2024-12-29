<?php

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Controllers\Controller;
use app\Services\BankPayment\BankPaymentInternalTransferService;
use app\Services\BankPayment\Fio\FioBankPaymentService;
use app\Services\BankPayment\Moneta\MonetaBankPaymentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BankAccountSyncMoneta extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);
        $bank_account = $workspace->bank_accounts
            ->where('uuid', $uuid)
            ->firstOrFail();

        $mps = new MonetaBankPaymentService();
        $mps->syncAccount($bank_account, Carbon::now()->startOfYear(), Carbon::now()->endOfYear());

        (new BankPaymentInternalTransferService())->findAndUpdateBankPaymentTransfers($bank_account);

        return response()->json([
            'success' => true,
        ]);
    }
}
