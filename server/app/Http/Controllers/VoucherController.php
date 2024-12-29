<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Voucher;
use App\Services\VoucherOrderService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use setasign\Fpdi\Tfpdf\Fpdi;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class VoucherController extends Controller
{
    private VoucherOrderService $voucher_service;

    public function __construct(VoucherOrderService $voucher_service)
    {
        $this->voucher_service = $voucher_service;
    }

    public function order()
    {
        return view('vouchers.order');
    }

    /**
     * @return RedirectResponse
     *
     * @throws Exception
     */
    public function orderStore(Request $request)
    {
        $voucher = $this->voucher_service->createOrder($request);

        return redirect()->route('voucher.detail', $voucher->uuid)
            ->with('submitted', 1);
    }

    public function payWithStripe(string $uuid)
    {
        $voucher = Voucher::where('uuid', $uuid)->firstOrFail();

        Stripe::setApiKey(config('services.stripe.api_key'));
        header('Content-Type: application/json');

        $checkout_session = Session::create([
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'czk',
                        'product_data' => [
                            'name' => 'Dárkový poukaz na Valašskou pevnost 🎁',
                            'description' => $voucher->getPersonStringPublic(),
                        ],
                        'unit_amount' => $voucher->price * 100,
                    ],
                    'quantity' => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('voucher.detail', [$voucher, 'uuid' => $uuid]),
            'cancel_url' => route('voucher.detail', [$voucher, 'uuid' => $uuid]),
            'automatic_tax' => [
                'enabled' => false,
            ],

            'customer_email' => $voucher->email,

            'client_reference_id' => $voucher->uuid,
        ]);

        return redirect($checkout_session->url);
    }

    /**
     * @throws ApiErrorException
     * @throws Exception
     */
    public function processWebhookFromStripe(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.api_key'));

        //        dump($request->getPayload());

        // You can find your endpoint's secret in your webhook settings
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            abort(400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            abort(400);
        }

        // Event verified
        if ($event->type == 'checkout.session.completed') {
            // Retrieve the session. If you require line items in the response, you may include them by expanding line_items.
            $session = \Stripe\Checkout\Session::retrieve([
                'id' => $event->data->object->id,
            ]);

            $voucher = Voucher::where('uuid', $session->client_reference_id)->first();

            if ($voucher->order_status == Voucher::ORDER_STATUS_WAITING_FOR_PAYMENT) {
                (new VoucherOrderService)->setPaid($voucher, true, Invoice::PAYMENT_TYPE_STRIPE);
            }
        }

        return response(['message' => 'Stripe webhook sucessfully processed.'], 200);
    }

    public function detail(string $uuid)
    {
        $voucher = Voucher::where('uuid', $uuid)->firstOrFail();

        return view('vouchers.detail', [
            'voucher' => $voucher,
        ]);
    }

    /**
     * @throws PdfParserException
     * @throws PdfReaderException
     */
    public function print(string $uuid)
    {
        $voucher = Voucher::where('uuid', $uuid)->firstOrFail();

        $pdf = new Fpdi('l', 'mm', 'A4');

        $pdf->setSourceFile(resource_path('voucher/strana_1.pdf'));
        $template_1 = $pdf->importPage(1);
        $pdf->AddPage();
        $pdf->useTemplate($template_1);

        // Set the default font to use
        $pdf->AddFont('DejaVu', 'B', 'DejaVuSans-Bold.ttf', true);
        $pdf->SetFont('DejaVu', 'B');

        // Add voucher code
        $pdf->SetFontSize('20'); // set font size
        $pdf->SetXY(197, 150); // set the position of the box
        $pdf->Cell(75, 35, $voucher->code, 0, 0, 'C');

        // Add ordered entries string
        $pdf = $this->addOrderEntries($pdf, $voucher);

        // Add custom text
        if ($voucher->custom_text) {
            $pdf = $this->addCustomTextToPdf($pdf, $voucher->custom_text);
        }

        //        // Add page two
        //        $pdf->setSourceFile(resource_path('voucher/strana_2.pdf'));
        //        $template_2 = $pdf->importPage(1);
        //        $pdf->AddPage();
        //        $pdf->useTemplate($template_2);

        return $pdf->Output();
    }

    public function printLabel(string $uuid)
    {
        $voucher = Voucher::where('uuid', $uuid)->firstOrFail();

        $pdf = new Fpdi('l', 'mm', 'A4');

        //        $pdf->setSourceFile(resource_path('voucher/voucher1.pdf'));
        //        $template_1 = $pdf->importPage(1);
        $pdf->AddPage();
        //        $pdf->useTemplate($template_1);

        // Set the default font to use
        $pdf->AddFont('DejaVu', 'B', 'DejaVuSans-Bold.ttf', true);
        $pdf->SetFont('DejaVu', 'B');

        // Add voucher code
        $pdf->SetFontSize('20'); // set font size
        $pdf->SetXY(197, 150); // set the position of the box
        $pdf->Cell(75, 35, $voucher->code, 0, 0, 'C');

        // Add ordered entries string
        $pdf = $this->addOrderEntries($pdf, $voucher);

        // Add custom text
        if ($voucher->custom_text) {
            $pdf = $this->addCustomTextToPdf($pdf, $voucher->custom_text);
        }

        // Add page two
        //        $pdf->setSourceFile(resource_path('voucher/strana_2.pdf'));
        //        $template_2 = $pdf->importPage(1);
        //        $pdf->useTemplate($template_2);

        return $pdf->Output();
    }

    private function addCustomTextToPdf(Fpdi $pdf, ?string $custom_text)
    {
        $char_count = mb_strlen($custom_text);

        //$custom_text =$char_count . $custom_text;

        if ($char_count < 20) {
            $pdf->SetFontSize('13');
            $pdf->SetXY(111, 130); // set the position of the box
            $pdf->MultiCell(75, 7, $custom_text, 0, 'C', '');
        } elseif ($char_count < 50) {
            $pdf->SetFontSize('13');
            $pdf->SetXY(111, 130); // set the position of the box
            $pdf->MultiCell(75, 7, $custom_text, 0, 'C', '');
        } elseif ($char_count < 100) {
            $pdf->SetFontSize('12');
            $pdf->SetXY(111, 130); // set the position of the box
            $pdf->MultiCell(75, 7, $custom_text, 0, 'C', '');
        } elseif ($char_count < 150) {
            $pdf->SetFontSize('12');
            $pdf->SetXY(111, 130); // set the position of the box
            $pdf->MultiCell(75, 7, $custom_text, 0, 'C', '');
        } elseif ($char_count < 170) {
            $pdf->SetFontSize('12');
            $pdf->SetXY(111, 125); // set the position of the box
            $pdf->MultiCell(75, 6, $custom_text, 0, 'C', '');
        } // 150+
        else {
            $pdf->SetFontSize('12');
            $pdf->SetXY(111, 120); // set the position of the box
            $pdf->MultiCell(75, 5, $custom_text, 0, 'C', '');
        }

        return $pdf;
    }

    private function addOrderEntries(Fpdi $pdf, Voucher $voucher)
    {
        $line_count = 0;
        if ($voucher->child_entry_count > 0) {
            $line_count++;
        }

        if ($voucher->custom_content > 0) {
            $line_count++;
            $line_count++;
        }
        if ($voucher->student_entry_count > 0) {
            $line_count++;
        }
        if ($voucher->adult_entry_count > 0) {
            $line_count++;
        }
        if ($voucher->family_entry_count > 0) {
            $line_count++;
            $line_count++;
        }

        $pdf->SetFontSize('15'); // set font size

        if ($line_count == 1) {
            $pdf->SetXY(197, 122); // set the position of the box
        } elseif ($line_count == 2) {
            $pdf->SetXY(197, 118); // set the position of the box
        } elseif ($line_count == 3) {
            $pdf->SetXY(197, 115); // set the position of the box
        } elseif ($line_count == 5) {
            $pdf->SetXY(197, 108); // set the position of the box
        }

        $pdf->MultiCell(75, 6.5, $voucher->getPersonString(), 0, 'C');

        return $pdf;
    }
}
