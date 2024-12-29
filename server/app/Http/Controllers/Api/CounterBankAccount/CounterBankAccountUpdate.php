<?php

namespace App\Http\Controllers\Api\CounterBankAccount;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CounterBankAccountUpdate extends Controller
{
    /**
     * @throws \Exception
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $counter_bank_account = $workspace->counter_bank_accounts()
            ->with(['organisation'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        if ($request->has('organisation')) {
            $organisation = $workspace->organisations()
                ->where('uuid', $request->input('organisation'))
                ->firstOrFail();

            $counter_bank_account->organisation_id = $organisation->id;
        }

        $counter_bank_account->save();

        return response()->json($counter_bank_account);
    }
}
