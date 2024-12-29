<?php

namespace App\Http\Controllers\Api\Expense;

use App\Http\Controllers\Controller;
use App\Services\BankPaymentPairingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpensePairWithBankPayment extends Controller
{
    /**
     * @throws \Exception
     */
    public function __invoke($workspace, $uuid, Request $request): JsonResponse
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $expense = $workspace->expenses()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $bank_payment = $workspace->bank_payments()
            ->where('uuid', $request->input('bank_payment'))
            ->firstOrFail();

        $expense->bank_payments()->attach($bank_payment, [
            'bank_payment_expense.amount' => $bank_payment->amount,
        ]);

        $expense = (new BankPaymentPairingService)->recountExpensePairingState($expense);

        return response()->json($expense->load('bank_payments'));
    }
}
