<?php

namespace App\Jobs\Balance;

use App\Models\BalancePrognosisRecord;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Workspace;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CountBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private Workspace $workspace;

    /**
     * Create a new job instance.
     */
    public function __construct(Workspace $workspace)
    {
        $this->workspace = $workspace;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Delete old records
        $old_records = $this->workspace
            ->balance_prognosis_records()
            ->where('date', '<', Carbon::now()->startOfDay());

        $old_records->delete();

        $date_from = Carbon::now()->startOfDay();
        $date_to = Carbon::now()->addYear()->endOfYear();

        // Today balance
        $bank_accounts = $this->workspace->bank_accounts;
        $today_balance = $bank_accounts->sum('current_amount');

        // Correct today balance with due income and expenses
        $due_income = $this->workspace->incomes()
            ->where('paid_at', '<', Carbon::now()->startOfDay())
            ->whereIn('payment_status', [Income::PAYMENT_STATUS_PLAN, Income::PAYMENT_STATUS_PENDING])
            ->sum('amount');

        $due_expenses = $this->workspace->expenses()
            ->where('paid_at', '<', Carbon::now()->startOfDay())
            ->whereIn('payment_status', [Expense::PAYMENT_STATUS_PLAN, Expense::PAYMENT_STATUS_PENDING])
            ->sum('price');

        $today_balance = $today_balance + $due_income - $due_expenses;

        $incomes = $this->workspace->incomes()
            ->where('paid_at', '>=', $date_from)
            ->where('paid_at', '<=', $date_to)
            ->get();

        $expenses = $this->workspace->expenses()
            ->where('paid_at', '>=', $date_from)
            ->where('paid_at', '<=', $date_to)
            ->get();

        // Future days
        $current_date = Carbon::now()->startOfDay();
        $current_balance = $today_balance;

        while ($current_date <= $date_to) {

            $current_date_income = $incomes->where('paid_at', '>=', $current_date->copy()->startOfDay())
                ->where('paid_at', '<=', $current_date->copy()->endOfDay())
                ->sum('amount');

            $current_date_expense = $expenses->where('paid_at', '>=', $current_date->copy())
                ->where('paid_at', '<=', $current_date->copy()->endOfDay())
                ->sum('price');

            $current_amount_diff = $current_date_income - $current_date_expense;
            $current_balance = $current_balance + $current_amount_diff;

            if ($current_date->copy()->endOfDay()->eq($current_date->copy()->endOfMonth())) {
                $record = $this->findOrCreateForDate($this->workspace, $current_date->copy());
                $record->amount = $current_balance;
                $record->date = $current_date;
                $record->amount_diff = $current_amount_diff;
                $record->save();
            }

            $current_date->addDay();
        }
    }

    private function findOrCreateForDate(Workspace $workspace, Carbon $current_date): BalancePrognosisRecord
    {
        $record = $workspace->balance_prognosis_records()
            ->where('date', '>=', $current_date->copy()->startOfDay())
            ->where('date', '<=', $current_date->copy()->endOfDay())
            ->first();

        if ($record === null) {
            $record = new BalancePrognosisRecord;
            $record->uuid = Str::uuid();
            $record->workspace_id = $workspace->id;
            $record->date = $current_date;
        }

        return $record;
    }
}
