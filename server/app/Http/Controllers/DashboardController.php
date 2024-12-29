<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function redirectToDashboard()
    {
        if (Auth::user()->isRole('admin')) {
            return redirect()->route('admin.dashboard.leader');
        } elseif (Auth::user()->isRole('accountant')) {
            return redirect()->route('admin.dashboard.accountant');
        } else {
            return redirect()->route('admin.dashboard.reception');
        }
    }

    public function accountantDashboard(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        $received_invoices_for_export = Expense::where('received_by_accountant_at', null)
            ->whereNotIn('payment_status', ['plan', 'draft'])
            ->orderBy('received_at')
            ->where('received_at', '>=', Carbon::parse('2023/11/01'))
            ->get();

        return view('admin.dashboard_accountant', [
            'received_invoices_for_export' => $received_invoices_for_export,
        ]);
    }
}
