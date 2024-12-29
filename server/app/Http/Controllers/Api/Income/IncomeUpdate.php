<?php

namespace App\Http\Controllers\Api\Income;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomeUpdate extends Controller
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

        $request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'organisation_id' => 'nullable|exists:organisations,id',
            'amount' => 'nullable|decimal:0,2',

            'payment_status' => ['nullable', 'string', Rule::in([
                Income::PAYMENT_STATUS_PLAN,
                Income::PAYMENT_STATUS_PENDING,
                Income::PAYMENT_STATUS_PAID,
            ])],

            'income_category_id' => 'nullable|exists:income_categories,id',

            'paid_at' => 'nullable|date',
        ]);

        if ($income->invoice_id) {
            abort(422, 'Income with related invoice is read only.');
        }

        // Update expense
        if ($request->has('name')) {
            $income->name = $request->input('name');
        }

        if ($request->has('description')) {
            $income->description = $request->input('description');
        }

        if ($request->has('organisation_id')) {
            $income->organisation_id = $request->input('organisation_id');
        }

        if ($request->has('income_category_id')) {
            $income->income_category_id = $request->input('income_category_id');
        }

        if ($request->has('amount')) {
            $income->amount = $request->input('amount');
        }

        if ($request->has('payment_status')) {
            $income->payment_status = $request->input('payment_status');
        }

        if ($request->has('paid_at')) {
            $income->paid_at = Carbon::parse($request->input('paid_at'));
        }

        $income->save();

        return response()->json($income);
    }
}
