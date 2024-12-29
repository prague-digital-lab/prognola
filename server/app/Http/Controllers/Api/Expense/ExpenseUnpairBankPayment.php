<?php

namespace App\Http\Controllers\Api\Expense;

use App\Http\Controllers\Controller;
use App\Services\BankPaymentPairingService;
use Illuminate\Http\Request;

class ExpenseUnpairBankPayment extends Controller
{
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $expense = $workspace->expenses()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $bank_payment = $workspace->bank_payments()
            ->where('uuid', $request->input('bank_payment'))
            ->firstOrFail();

        $expense->bank_payments()->detach($bank_payment);

        $bankPaymentPairingService = new BankPaymentPairingService;
        $bankPaymentPairingService->recountExpensePairingState($expense);
        $bankPaymentPairingService->recountBankPaymentPairingState($bank_payment);

        return response()->json($expense->load('bank_payments'));
    }
}
