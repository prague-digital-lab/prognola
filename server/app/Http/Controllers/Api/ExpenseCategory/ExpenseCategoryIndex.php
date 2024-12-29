<?php

namespace App\Http\Controllers\Api\ExpenseCategory;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;

class ExpenseCategoryIndex extends Controller
{
    /**
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $expense_categories = $workspace->expense_categories()
            ->with('department')
            ->orderBy('department_id')
            ->orderBy('name')
            ->get();

        return response()->json($expense_categories);
    }
}
