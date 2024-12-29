<?php

namespace App\Http\Controllers\Api\Income;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class IncomeDestroy extends Controller
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
            ->where('uuid', $uuid)
            ->firstOrFail();

        $income->delete();

        return response()->noContent();
    }
}
