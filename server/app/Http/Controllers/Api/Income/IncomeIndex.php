<?php

namespace App\Http\Controllers\Api\Income;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class IncomeIndex extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);
        $incomes = $workspace->incomes();

        // Filter by query
        if ($request->has('query')) {

            $query_phrase = $request->input('query');

            $incomes = $incomes->where(function ($query) use ($query_phrase) {
                $query->where('name', 'LIKE', '%'.$query_phrase.'%')
                    ->orWhere('id', 'LIKE', '%'.$query_phrase.'%')
                    ->orWhere(function ($q) use ($query_phrase) {
                        $q->where('amount', '=', $query_phrase)
                            ->where('amount', '!=', 0);
                    })
                    ->orWhereHas('organisation', function ($q) use ($query_phrase) {
                        $q->where('name', 'LIKE', '%'.$query_phrase.'%');
                    });
            });
        }

        // Date from
        if ($request->has('from')) {
            $from = Carbon::parse($request->input('from'))->startOfDay();
            $incomes = $incomes->where('paid_at', '>=', $from);
        }

        // Date to
        if ($request->has('to')) {
            $to = Carbon::parse($request->input('to'))->endOfDay();
            $incomes = $incomes->where('paid_at', '<=', $to);
        }

        if ($request->has('payment_status')) {
            if ($request->input('payment_status') == 'due') {
                $incomes = $incomes->whereIn('payment_status', [
                    Income::PAYMENT_STATUS_PENDING,
                    Income::PAYMENT_STATUS_PLAN,
                ]);
            } else {
                $incomes = $incomes->where('payment_status', $request->input('payment_status'));
            }
        }

        $incomes = $incomes->orderBy('paid_at');
        $incomes = $incomes->with(['organisation', 'income_category']);
        $incomes = $incomes->get();

        return response()->json([
            'data' => $incomes,
            'price_sum' => $incomes->sum('amount'),
        ]);
    }
}
