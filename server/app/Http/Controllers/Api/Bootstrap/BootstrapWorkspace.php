<?php

namespace App\Http\Controllers\Api\Bootstrap;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BootstrapWorkspace extends Controller
{
    /**
     * Handle the incoming request.
     * @throws \Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $request->validate([
            'date_from' => 'required',
        ]);

        $date_from = Carbon::parse($request->input('date_from'));

        $expenses = $workspace->expenses()
//            ->withTrashed()
            ->where('updated_at', '>=', $date_from)
            ->get();

        $incomes = $workspace->incomes()
//            ->withTrashed()
            ->where('updated_at', '>=', $date_from)
            ->get();

        $bank_accounts = $workspace->bank_accounts()
//            ->withTrashed()
            ->where('updated_at', '>=', $date_from)
            ->get();

        $bank_payments = $workspace->bank_payments()
//            ->withTrashed()
            ->where('updated_at', '>=', $date_from)
            ->get();

        $organisations = $workspace->organisations()
//            ->withTrashed()
            ->where('updated_at', '>=', $date_from)
            ->get();


        return response()->json(
            [
                'expenses' => $expenses,
                'incomes' => $incomes,
                'bank_accounts' => $bank_accounts,
                'bank_payments' => $bank_payments,
                'organisations' => $organisations,
            ]);
    }
}
