<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\NumberSerie;
use App\Models\Voucher;
use Carbon\Carbon;

class InvoiceService
{
    public function createInvoice(Customer $customer): Invoice
    {
        $invoice = new Invoice;
        $invoice->paid_at = null;
        $invoice->price = 0;
        $invoice->payment_status = Invoice::PAYMENT_STATUS_DRAFT;

        $invoice->payment_type = Invoice::PAYMENT_TYPE_CASH;
        $invoice->created_by_user_id = \Auth::id();
        $invoice->is_vat = true;
        $invoice->customer_id = $customer->id;
        $invoice->recipient_name = $customer->email;
        $invoice->due_at = Carbon::now()->addDays(14);
        $invoice->save();

        $invoice->syncToIncome();

        return $invoice;
    }

    public function setPaid(Invoice $invoice): Invoice
    {
        $invoice->payment_status = Invoice::PAYMENT_STATUS_PAID;
        $invoice->paid_at = Carbon::now();
        $invoice->save();

        $invoice->syncToIncome();

        return $invoice;
    }

    public function closeInvoiceDraft(Invoice $invoice): Invoice
    {
        // Get the highest number of the serie this year
        $latest_invoice_in_serie = Invoice::where('created_at', '>=', Carbon::now()->startOfYear())
            ->where('created_at', '<=', Carbon::now()->endOfYear())
            ->where('number_serie_id', $invoice->number_serie_id)
            ->orderByDesc('number_serie_number')
            ->first();

        if ($latest_invoice_in_serie) {
            $number_serie_number = $latest_invoice_in_serie->number_serie_number + 1;
        } else {
            $number_serie_number = 1;
        }

        $invoice->payment_status = Invoice::PAYMENT_STATUS_PENDING;

        // Invoice code according to number serie.
        $invoice->number_serie_number = $number_serie_number;
        $invoice->code = $this->generateInvoiceCode($invoice->number_serie, $invoice->created_at, $number_serie_number);
        $invoice->variable_symbol = preg_replace('/[^0-9]/', '', $invoice->code);
        $invoice->save();

        return $invoice;
    }

    public function createInvoiceByVoucherOrder(Voucher $voucher, string $payment_type = 'bank_transfer'): Invoice
    {
        $invoice = $this->createInvoice($voucher->customer);

        $invoice->voucher_id = $voucher->id;
        $invoice->payment_type = $payment_type;
        $invoice->save();

        if ($voucher->has_custom_price) {
            $invoice_item = new InvoiceItem;

            $invoice_item->invoice_id = $invoice->id;
            $invoice_item->name = 'dárkový poukaz na hru - individuální cena';
            $invoice_item->unit_price = $voucher->price;
            $invoice_item->count = 1;
            $invoice_item->price = $voucher->price;
            $invoice_item->save();
        } else {

            if ($voucher->family_entry_count) {
                $invoice_item = new InvoiceItem;

                $invoice_item->invoice_id = $invoice->id;
                $invoice_item->name = 'rodinný vstup';
                $invoice_item->unit_price = config('web.reservation_price_family');
                $invoice_item->count = $voucher->family_entry_count;
                $invoice_item->price = $invoice_item->unit_price * $voucher->family_entry_count;
                $invoice_item->save();
            }

            if ($voucher->child_entry_count) {
                $invoice_item = new InvoiceItem;

                $invoice_item->invoice_id = $invoice->id;
                $invoice_item->name = 'dítě (do 18 let)';
                $invoice_item->unit_price = config('web.reservation_price_child');
                $invoice_item->count = $voucher->child_entry_count;
                $invoice_item->price = $invoice_item->unit_price * $voucher->child_entry_count;
                $invoice_item->save();
            }

            if ($voucher->student_entry_count) {
                $invoice_item = new InvoiceItem;

                $invoice_item->invoice_id = $invoice->id;
                $invoice_item->name = 'student (do 26 let)';
                $invoice_item->unit_price = 480;
                $invoice_item->count = $voucher->student_entry_count;
                $invoice_item->price = $invoice_item->unit_price * $voucher->student_entry_count;
                $invoice_item->save();
            }

            if ($voucher->adult_entry_count) {
                $invoice_item = new InvoiceItem;

                $invoice_item->invoice_id = $invoice->id;
                $invoice_item->name = 'dospělý (od 18 let)';
                $invoice_item->unit_price = config('web.reservation_price_adult');
                $invoice_item->count = $voucher->adult_entry_count;
                $invoice_item->price = $invoice_item->unit_price * $voucher->adult_entry_count;
                $invoice_item->save();
            }

            if ($voucher->shipping_type == Voucher::SHIPPING_TYPE_PERSONAL) {
                $invoice_item = new InvoiceItem;

                $invoice_item->invoice_id = $invoice->id;
                $invoice_item->name = 'osobní převzetí';
                $invoice_item->unit_price = 30;
                $invoice_item->count = 1;
                $invoice_item->price = 30;
                $invoice_item->save();
            }

            if ($voucher->shipping_type == Voucher::SHIPPING_TYPE_LETTER) {
                $invoice_item = new InvoiceItem;

                $invoice_item->invoice_id = $invoice->id;
                $invoice_item->name = 'zaslání poštou';
                $invoice_item->unit_price = 60;
                $invoice_item->count = 1;
                $invoice_item->price = 60;
                $invoice_item->save();
            }
        }

        $invoice->syncToIncome();

        return $invoice;
    }

    public function recountPrices(Invoice $invoice)
    {
        $invoice->price = $invoice->invoice_items()->sum('price');
        $invoice->save();

        return $invoice;
    }

    public function generateInvoiceCode(NumberSerie $numberSerie, Carbon $date_of_issue, int $serie_number)
    {
        $count = str_pad($serie_number, $numberSerie->digits_count, '0', STR_PAD_LEFT);

        return $numberSerie->prefix.$date_of_issue->format('y').$count.$numberSerie->suffix;

    }
}
