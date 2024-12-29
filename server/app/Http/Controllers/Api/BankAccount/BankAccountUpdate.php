<?php

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankAccountUpdate extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid, Request $request): JsonResponse
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $bank_account = $workspace->bank_accounts()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $bank_account->name = $request->input('name', $bank_account->name);
        $bank_account->bank = $request->input('bank', $bank_account->bank);
        $bank_account->account_number = $request->input('account_number', $bank_account->account_number);
        $bank_account->bank_number = $request->input('bank_number', $bank_account->bank_number);
        $bank_account->api_token = $request->input('api_token', $bank_account->api_token);
        $bank_account->save();

        return response()->json($bank_account);
    }
}
