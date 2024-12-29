<?php

namespace App\Http\Controllers\Api\IncomeCategory;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class IncomeCategoryIndex extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $income_categories = $workspace->income_categories()
            ->orderBy('name')
            ->get();

        return response()->json($income_categories);
    }
}
