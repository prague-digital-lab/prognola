<?php

namespace App\Http\Controllers\Api\Expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ExpenseStore extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $request->validate([
            'description' => 'nullable|string',
            'organisation' => 'nullable',
            'price' => 'nullable|decimal:0,2',
            'received_at' => 'nullable|date',
            'paid_at' => 'nullable|date',
            'due_at' => 'nullable|date',
            'payment_status' => ['nullable', 'string', Rule::in([
                Expense::PAYMENT_STATUS_DRAFT,
                Expense::PAYMENT_STATUS_PLAN,
                Expense::PAYMENT_STATUS_PENDING,
                Expense::PAYMENT_STATUS_PAID,
            ])],
        ]);

        if ($request->has('received_at')) {
            $received_at = Carbon::parse($request->input('received_at'));
        } else {
            $received_at = Carbon::now();
        }

        if ($request->has('due_at')) {
            $due_at = Carbon::parse($request->input('due_at'));
        } else {
            $due_at = null;
        }

        if ($request->has('paid_at')) {
            $paid_at = Carbon::parse($request->input('paid_at'));
        } else {
            $paid_at = Carbon::now();
        }

        // Create expense
        $expense = new Expense;
        $expense->uuid = Str::uuid();
        $expense->workspace_id = $workspace->id;
        $expense->description = $request->input('description');

        if ($request->has('organisation') && $request->input('organisation') !== null) {
            $organisation = $workspace->organisations()
                ->where('uuid', $request->input('organisation'))
                ->firstOrFail();

            $expense->organisation_id = $organisation->id;
        }

        $expense->price = $request->input('price', 0);

        $expense->payment_status = $request->input('payment_status', Expense::PAYMENT_STATUS_DRAFT);
        $expense->created_by_user_id = Auth::id();

        $expense->received_at = $received_at;
        $expense->due_at = $due_at;
        $expense->paid_at = $paid_at;

        $expense->save();

        return response()->json($expense);
    }
}
