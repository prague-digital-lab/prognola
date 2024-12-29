<?php

namespace App\Http\Controllers\Api\BankPayment;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankPaymentShow extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid, Request $request): JsonResponse
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $bank_payment = $workspace->bank_payments()
            ->with(['bank_account', 'counter_bank_account', 'incomes', 'expenses', 'transfer_bank_account'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json($bank_payment);
    }
}
