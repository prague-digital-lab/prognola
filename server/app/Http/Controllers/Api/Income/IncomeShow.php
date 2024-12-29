<?php

namespace App\Http\Controllers\Api\Income;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class IncomeShow extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $income = $workspace->incomes()
            ->with(['organisation', 'income_category', 'bank_payments'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json($income);
    }
}
