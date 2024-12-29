<?php

namespace App\Http\Controllers\Api\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseIndex extends Controller
{
    /**
     * @throws Exception
     */
    public function __invoke($workspace, Request $request): JsonResponse
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);
        $expenses = $workspace->expenses();

        // Filter by query
        if ($request->has('query')) {

            $query_phrase = $request->input('query');

            $expenses = $expenses->where(function ($query) use ($query_phrase) {
                $query->where('description', 'LIKE', '%'.$query_phrase.'%')
                    ->orWhere('external_code', 'LIKE', '%'.$query_phrase.'%')
                    ->orWhere('id', 'LIKE', '%'.$query_phrase.'%')
                    ->orWhere(function ($q) use ($query_phrase) {
                        $q->where('price', '=', $query_phrase)
                            ->where('price', '!=', 0);
                    })
                    ->orWhereHas('organisation', function ($q) use ($query_phrase) {
                        $q->where('name', 'LIKE', '%'.$query_phrase.'%');
                    });
            });
        }

        // Date from
        if ($request->has('from')) {
            $from = Carbon::parse($request->input('from'))->startOfDay();
            $expenses = $expenses->where('paid_at', '>=', $from);
        }

        // Date to
        if ($request->has('to')) {
            $to = Carbon::parse($request->input('to'))->endOfDay();
            $expenses = $expenses->where('paid_at', '<=', $to);
        }

        if ($request->has('payment_status')) {
            if ($request->input('payment_status') == 'due') {
                $expenses = $expenses->whereIn('payment_status', [
                    Expense::PAYMENT_STATUS_PENDING,
                    Expense::PAYMENT_STATUS_PLAN,
                ]);
            } else {
                $expenses = $expenses->where('payment_status', $request->input('payment_status'));
            }
        }

        if ($request->has('price')) {
            $expenses = $expenses->where('price', '=', $request->input('price'));
        }

        if ($request->has('organisation')) {
            $organisation = $workspace->organisations()->where('uuid', $request->input('organisation'))->firstOrFail();

            $expenses = $expenses->where('organisation_id', '=', $organisation->id);
        }

        if ($request->has('is_paired')) {
            if ($request->input('is_paired') == 1) {
                $expenses = $expenses->where('paired_at', '!=', null);
            } else {
                $expenses = $expenses->where('paired_at', '=', null);

            }
        }

        $expenses = $expenses->orderBy('paid_at');
        $expenses = $expenses->with(['organisation', 'expense_category']);
        $expenses = $expenses->get();

        return response()->json([
            'data' => $expenses,
            'price_sum' => $expenses->sum('price'),
        ]);
    }
}
