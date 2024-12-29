<?php

namespace App\Http\Controllers\Api\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Notifications\ReceivedInvoice\DiscordExpenseStatusChanged;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseUpdate extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, $uuid, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);
        $expense = $workspace->expenses()
            ->where('uuid', $uuid)
            ->firstOrFail();

        $request->validate([
            'description' => 'nullable|string',
            'internal_note' => 'nullable|string',
            'organisation' => 'nullable',
            'price' => 'nullable|decimal:0,2',

            'variable_symbol'=> 'nullable|numeric',

            'payment_status' => ['nullable', 'string', Rule::in([
                Expense::PAYMENT_STATUS_DRAFT,
                Expense::PAYMENT_STATUS_PLAN,
                Expense::PAYMENT_STATUS_PENDING,
                Expense::PAYMENT_STATUS_PAID,
            ])],

            'expense_category_id' => 'nullable|exists:expense_categories,id',

            'received_at' => 'nullable|date',
            'due_at' => 'nullable|date',
            'paid_at' => 'nullable|date',
        ]);

        // Update expense
        if ($request->has('description')) {
            $expense->description = $request->input('description');
        }

        if ($request->has('internal_note')) {
            $expense->internal_note = $request->input('internal_note');
        }

        if ($request->has('organisation')) {
            $organisation = $workspace->organisations()
                ->where('uuid', $request->input('organisation'))
                ->firstOrFail();

            $expense->organisation_id = $organisation->id;
        }

        if ($request->has('expense_category_id')) {
            $expense->expense_category_id = $request->input('expense_category_id');
        }

        if ($request->has('price')) {
            $expense->price = $request->input('price');
        }

        // Update expense
        if ($request->has('variable_symbol')) {
            $expense->variable_symbol = $request->input('variable_symbol');
        }

        if ($request->has('payment_status')) {
            $expense->payment_status = $request->input('payment_status');
        }

        if ($request->has('received_at')) {
            $expense->received_at = Carbon::parse($request->input('received_at'));
        }

        if ($request->has('due_at')) {
            $expense->due_at = Carbon::parse($request->input('due_at'));
        }

        if ($request->has('paid_at')) {
            $expense->paid_at = Carbon::parse($request->input('paid_at'));
        }

        $expense->save();

        if ($request->has('payment_status')) {
            $expense->workspace->notify(new DiscordExpenseStatusChanged($expense, $request->user()));
        }

        return response()->json($expense);
    }
}
