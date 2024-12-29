<?php

namespace App\Services;

use App\Models\BankPayment;
use App\Models\Expense;
use App\Models\Income;
use Carbon\Carbon;

class BankPaymentPairingService
{
    /**
     * Recount pairing state of Expense.
     * Expense related BankPayments pairing state will be also recounted.
     */
    public function recountExpensePairingState(Expense $expense): Expense
    {
        $expense = $this->recountExpenseOnly($expense);

        foreach ($expense->bank_payments as $bankPayment) {
            $this->recountBankPaymentOnly($bankPayment);
        }

        return $expense;
    }

    /**
     * Recount pairing state of Income.
     * Income related BankPayments pairing state will be also recounted.
     */
    public function recountIncomePairingState(Income $income): Income
    {
        $income = $this->recountIncomeOnly($income);

        foreach ($income->bank_payments as $bankPayment) {
            $this->recountBankPaymentOnly($bankPayment);
        }

        return $income;
    }

    /**
     * Recount pairing state of BankPayment.
     * BankPayment related Incomes and Expenses pairing state will be also recounted.
     */
    public function recountBankPaymentPairingState(BankPayment $bank_payment): BankPayment
    {
        $this->recountBankPaymentOnly($bank_payment);

        // Recount related expenses pairing state
        foreach ($bank_payment->expenses as $expense) {
            $this->recountExpenseOnly($expense);
        }

        // Recount related incomes pairing state
        foreach ($bank_payment->incomes as $income) {
            $this->recountIncomeOnly($income);
        }

        return $bank_payment;
    }

    private function recountBankPaymentOnly(BankPayment $bank_payment): BankPayment
    {
        $paired_income_sum = $bank_payment->incomes()->sum('bank_payment_income.amount');
        $paired_expense_sum = $bank_payment->expenses()->sum('bank_payment_expense.amount');
        $paired_amount = $paired_income_sum + $paired_expense_sum;

        if ($paired_amount == $bank_payment->amount) {
            $bank_payment->paired_at = Carbon::now();
        } else {
            $bank_payment->paired_at = null;
        }

        $bank_payment->save();

        return $bank_payment;
    }

    private function recountIncomeOnly(Income $income): Income
    {
        // Do nothing when expense price is zero TODO: Why that?
        //        if ($income->amount === 0) {
        //            return $income;
        //        }

        if ($income->bank_payments()->sum('bank_payment_income.amount') == $income->amount) {
            $income->paired_at = Carbon::now();
            $income->payment_status = Income::PAYMENT_STATUS_PAID;

            // TODO: The addHours method looks like an ugly hack of some timezone issues.
            $income->paid_at = $income->bank_payments->sortByDesc('issued_at')->first()->issued_at->addHours(2);
        } else {
            $income->paired_at = null;
        }

        $income->save();

        return $income;
    }

    public function recountExpenseOnly(Expense $expense): Expense
    {
        if (abs($expense->bank_payments()->sum('bank_payment_expense.amount')) == $expense->price) {
            $expense->paired_at = Carbon::now();
            $expense->payment_status = Expense::PAYMENT_STATUS_PAID;

            // TODO: The addHours method looks like an ugly hack of some timezone issues.
            $expense->paid_at = $expense->bank_payments->sortByDesc('issued_at')->first()->issued_at->addHours(2);
        } else {
            $expense->paired_at = null;
        }

        $expense->save();

        return $expense;
    }
}
