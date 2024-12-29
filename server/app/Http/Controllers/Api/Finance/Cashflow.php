<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Income;
use App\Services\StatsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Cashflow extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws \Exception
     */
    public function __invoke($workspace, Request $request)
    {
        $workspace = $this->findAndAuthorizeWorkspace($workspace);

        $date_from = Carbon::parse($request->input('from'));
        $date_to = Carbon::parse($request->input('to'));

        // Income
        $income = $workspace->incomes()
            ->where('paid_at', '>=', $date_from)
            ->where('paid_at', '<=', $date_to)
            ->where('payment_status', Income::PAYMENT_STATUS_PAID)
            ->get();

        // Chart data
        $income_stats = new StatsService($income, 'paid_at');
        $income_stats->setRange($date_from, $date_to);
        $income_stats->setResolution(StatsService::RESOLUTION_MONTH);
        $income_stats->prepareStats();

        // Income plan
        $income_plan = $workspace->incomes()
            ->where('paid_at', '>=', $date_from)
            ->where('paid_at', '<=', $date_to)
            ->whereIn('payment_status', [Income::PAYMENT_STATUS_PLAN, Income::PAYMENT_STATUS_PENDING])
            ->get();

        // Chart data
        $income_plan_stats = new StatsService($income_plan, 'paid_at');
        $income_plan_stats->setRange($date_from, $date_to);
        $income_plan_stats->setResolution(StatsService::RESOLUTION_MONTH);
        $income_plan_stats->prepareStats();

        // Expense
        $expenses = $workspace->expenses()
            ->where('paid_at', '>=', $date_from)
            ->where('paid_at', '<=', $date_to)
            ->where('payment_status', Expense::PAYMENT_STATUS_PAID)
            ->get();

        // Chart data
        $expense_stats = new StatsService($expenses, 'paid_at');
        $expense_stats->setRange($date_from, $date_to);
        $expense_stats->setResolution(StatsService::RESOLUTION_MONTH);
        $expense_stats->prepareStats();

        // Expense plan
        $expenses_plan = $workspace->expenses()
            ->where('paid_at', '>=', $date_from)
            ->where('paid_at', '<=', $date_to)
            ->whereIn('payment_status', [Expense::PAYMENT_STATUS_PLAN, Expense::PAYMENT_STATUS_PENDING])
            ->get();

        // Chart data
        $expense_plan_stats = new StatsService($expenses_plan, 'paid_at');
        $expense_plan_stats->setRange($date_from, $date_to);
        $expense_plan_stats->setResolution(StatsService::RESOLUTION_MONTH);
        $expense_plan_stats->prepareStats();

        // Balance prognosis records
        $balance_prognosis_records = $workspace->balance_prognosis_records()
            ->where('date', '>=', $date_from)
            ->where('date', '<=', $date_to)
            ->get();

        // Chart data
        $balance_stats = new StatsService($balance_prognosis_records, 'date');
        $balance_stats->setRange($date_from, $date_to);
        $balance_stats->setResolution(StatsService::RESOLUTION_MONTH);
        $balance_stats->prepareStats();

        return response()->json([
            'chart_labels' => $income_stats->getChartLabels(),

            'chart_data_income' => $income_stats->getChartValuesPropertySum('amount'),
            'chart_data_income_plan' => $income_plan_stats->getChartValuesPropertySum('amount'),

            'chart_data_expense' => $expense_stats->getChartValuesPropertySum('price'),
            'chart_data_expense_plan' => $expense_plan_stats->getChartValuesPropertySum('price'),

            'chart_data_balance' => $balance_stats->getChartValuesPropertySum('amount'),

            'income_sum' => $income->sum('amount'),
            'income_plan_sum' => $income_plan->sum('amount'),

            'expense_sum' => $expenses->sum('price'),
            'expense_plan_sum' => $expenses_plan->sum('price'),

            'profit_sum' => $income->sum('amount') - $expenses->sum('price'),
            'profit_plan_sum' => $income_plan->sum('amount') - $expenses_plan->sum('price'),
        ]);
    }
}
