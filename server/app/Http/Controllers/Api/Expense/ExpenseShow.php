<?php

namespace App\Http\Controllers\Api\Expense;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpenseShow extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws \Exception
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $received_invoice = $workspace->expenses()
            ->with(['organisation', 'expense_category.department', 'scans', 'bank_payments'])
            ->where('uuid', $uuid)
            ->firstOrFail();

        return response()->json($received_invoice);
    }
}
