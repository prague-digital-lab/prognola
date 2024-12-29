<?php

namespace App\Http\Controllers\Api\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\StatsService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StatsIncome extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date',
        ]);

        $date_from = Carbon::parse($request->input('from'));
        $date_to = Carbon::parse($request->input('to'));

        // Reservations
        $invoices = Invoice::where('created_at', '>=', $date_from)
            ->where('created_at', '<=', $date_to)
            ->whereNotIn('payment_status', [Invoice::PAYMENT_STATUS_STORNO, Invoice::PAYMENT_STATUS_DRAFT])
            ->get();

        // Chart
        $stats_service = new StatsService($invoices, 'created_at');
        $stats_service->setRange($date_from, $date_to);

        $date_range_days = $stats_service->getRangeInDays();

        if ($date_range_days > 180) {
            $stats_service->setResolution(StatsService::RESOLUTION_MONTH);
        } elseif ($date_range_days > 60) {
            $stats_service->setResolution(StatsService::RESOLUTION_WEEK);
        } else {
            $stats_service->setResolution(StatsService::RESOLUTION_DAY);
        }

        $stats_service->prepareStats();

        return [
            'reservation_income' => $invoices
                ->where('reservation_id', '!=', null)
                ->where('category', 'game')
                ->sum('price'),

            'food_income' => $invoices
                ->where('reservation_id', '!=', null)
                ->where('category', 'food')
                ->sum('price'),

            'voucher_income' => $invoices
                ->where('voucher_id', '!=', null)
                ->sum('price'),

            'cash_income' => $invoices
                ->where('payment_type', Invoice::PAYMENT_TYPE_CASH)
                ->sum('price'),

            'card_income' => $invoices
                ->where('payment_type', Invoice::PAYMENT_TYPE_CARD)
                ->sum('price'),

            'stripe_income' => $invoices
                ->where('payment_type', Invoice::PAYMENT_TYPE_STRIPE)
                ->sum('price'),

            'transfer_income' => $invoices
                ->where('payment_type', Invoice::PAYMENT_TYPE_BANK_TRANSFER)
                ->sum('price'),

            'slevomat_income' => $invoices
                ->where('payment_type', Invoice::PAYMENT_TYPE_SLEVOMAT)
                ->sum('price'),

            'whole_income' => $invoices->sum('price'),
            'average_daily' => $invoices->sum('price') / $date_range_days,
            'average_weekly' => $invoices->sum('price') / $date_range_days * 7,

            'date_from' => $date_from,
            'date_to' => $date_to,

            'chart_labels' => $stats_service->getChartLabels(),
            'chart_data_price' => $stats_service->getChartValuesPropertySum('price'),
        ];
    }
}
