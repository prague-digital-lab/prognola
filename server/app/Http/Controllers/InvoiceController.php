<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{
    public function renderPdf(Invoice $invoice)
    {
        $pdf = Pdf::loadView('admin.invoices.fv', [
            'invoice' => $invoice,
        ]);

        Pdf::setOption([
            'isRemoteEnabled' => true,
        ]);

        return $pdf->stream('invoice.pdf');
    }
}
