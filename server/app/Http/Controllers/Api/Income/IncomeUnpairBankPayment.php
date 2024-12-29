<?php

namespace App\Http\Controllers\Api\Income;

use App\Http\Controllers\Controller;
use App\Services\BankPaymentPairingService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IncomeUnpairBankPayment extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid, Request $request): JsonResponse
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $income = $workspace->incomes()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $bank_payment = $workspace->bank_payments()
            ->where('uuid', $request->input('bank_payment'))
            ->firstOrFail();

        $income->bank_payments()->detach($bank_payment);

        $bankPaymentPairingService = new BankPaymentPairingService;
        $bankPaymentPairingService->recountIncomePairingState($income);
        $bankPaymentPairingService->recountBankPaymentPairingState($bank_payment);

        return response()->json($income->load('bank_payments'));
    }
}
