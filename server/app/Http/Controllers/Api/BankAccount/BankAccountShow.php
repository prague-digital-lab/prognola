<?php

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BankAccountShow extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws \Exception
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $bank_account = $workspace->bank_accounts()
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json($bank_account);
    }
}
