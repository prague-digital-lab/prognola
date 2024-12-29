<?php

namespace App\Console\Commands;

use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateDuePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-due-payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expenses = Expense::where('paid_at', '<', Carbon::now()->startOfDay())
            ->whereIn('payment_status', [Expense::PAYMENT_STATUS_PLAN, Expense::PAYMENT_STATUS_PENDING])
            ->get();

        foreach ($expenses as $expense) {
            $expense->paid_at = Carbon::now()->startOfDay();
            $expense->save();
        }
    }
}
