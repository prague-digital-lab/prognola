<?php

namespace App\Http\Controllers\Api\CounterBankAccount;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class CounterBankAccountIndex extends Controller
{
    /**
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $bank_accounts = $workspace->counter_bank_accounts();

        if ($request->has('organisation')) {
            $organisation = $workspace->organisations()
                ->where('uuid', $request->input('organisation'))
                ->firstOrFail();

            $bank_accounts = $bank_accounts->where('organisation_id', $organisation->id);
        }

        return response()->json($bank_accounts->get());
    }
}
