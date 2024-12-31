<?php

namespace App\Http\Controllers\Api\BankPayment;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankPaymentIndex extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request): JsonResponse
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $bank_payments = $workspace->bank_payments()
            ->with(['incomes', 'expenses', 'bank_account']);

        if ($request->has('bank_account')) {
            $bank_account = $workspace->bank_accounts()
                ->where('uuid', $request->get('bank_account'))
                ->firstOrFail();

            $bank_payments = $bank_payments->where('bank_account_id', $bank_account->id);
        }

        if ($request->has('query')) {
            $bank_payments = $bank_payments->where(function ($query) use ($request) {
                return $query->where('description', 'LIKE', '%'.$request->input('query').'%')
                    ->orWhere('sender_comment', 'LIKE', '%'.$request->input('query').'%');
            });
        }

        if ($request->has('type')) {
            $bank_payments = $bank_payments->where('type', $request->input('type'));
        }

        if ($request->has('from')) {
            $bank_payments = $bank_payments->where('issued_at', '>=', Carbon::parse($request->input('from'))->startOfDay());
        }

        if ($request->has('to')) {
            $bank_payments = $bank_payments->where('issued_at', '<=', Carbon::parse($request->input('to'))->endOfDay());
        }

        if ($request->has('amount')) {
            $bank_payments = $bank_payments->where(function ($query) use ($request) {
                return $query->where('amount', '=', $request->input('amount'))
                    ->orWhere('amount', '=', $request->input('amount') * -1);
            });
        }

        if ($request->has('is_paired')) {
            if ($request->input('is_paired') === true) {
                $bank_payments = $bank_payments->where('paired_at', '!=', null);
            } else {
                $bank_payments = $bank_payments->where('paired_at', null);
            }
        }

        $bank_payments = $bank_payments->orderByDesc('issued_at');

        return response()->json($bank_payments->get());
    }
}
