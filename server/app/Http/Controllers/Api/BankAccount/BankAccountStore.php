<?php

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BankAccountStore extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request): JsonResponse
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $bank_account = new BankAccount;
        $bank_account->uuid = Str::uuid();
        $bank_account->workspace_id = $workspace->id;
        $bank_account->name = $request->input('name');
        $bank_account->bank = $request->input('bank');
        $bank_account->account_number = $request->input('account_number');
        $bank_account->bank_number = $request->input('bank_number');

        $bank_account->save();

        return response()->json($bank_account);
    }
}
