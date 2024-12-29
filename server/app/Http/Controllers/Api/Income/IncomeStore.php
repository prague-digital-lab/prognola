<?php

namespace App\Http\Controllers\Api\Income;

use App\Http\Controllers\Controller;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class IncomeStore extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $request->validate([
            'name' => 'nullable|string',
            'type' => 'nullable|string',
            'description' => 'nullable|string',
            'organisation_id' => 'nullable|exists:organisations,id',
            'income_category_id' => 'nullable|exists:income_categories,id',
            'amount' => 'nullable|decimal:0,2',
            'paid_at' => 'nullable|date',
            'payment_status' => 'nullable|string',
        ]);

        // Create expense
        $income = new Income;
        $income->type = $request->input('type', Income::INCOME_TYPE_GENERIC);
        $income->uuid = Str::uuid();
        $income->workspace_id = $workspace->id;
        $income->name = $request->input('name');
        $income->description = $request->input('description');
        $income->organisation_id = $request->input('organisation_id');
        $income->amount = $request->input('amount', 0);

        $income->payment_status = $request->input('payment_status', Income::PAYMENT_STATUS_PLAN);

        if ($request->has('paid_at')) {
            $income->paid_at = Carbon::parse($request->input('paid_at'));
        } else {
            $income->paid_at = Carbon::now();
        }

        $income->save();

        return response()->json($income);
    }
}
