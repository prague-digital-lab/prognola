<?php

namespace App\Http\Controllers\Api\CounterBankAccount;

use App\Http\Controllers\Controller;

class CounterBankAccountShow extends Controller
{
    /**
     * @throws \Exception
     */
    public function __invoke($workspace, $uuid)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $counter_bank_account = $workspace->counter_bank_accounts()
            ->with(['organisation'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json($counter_bank_account);
    }
}
