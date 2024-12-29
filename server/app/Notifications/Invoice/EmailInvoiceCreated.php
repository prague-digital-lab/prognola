<?php

namespace App\Notifications\Invoice;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailInvoiceCreated extends Notification implements ShouldQueue
{
    use Queueable;

    private Invoice $invoice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array
     */
    public function via()
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return MailMessage
     */
    public function toMail()
    {
        $pdf = Pdf::loadView('admin.invoices.fv', [
            'invoice' => $this->invoice,
        ]);

        if ($this->invoice->payment_status === Invoice::PAYMENT_STATUS_PAID) {
            $payment_info = 'Dobrý den. Děkujeme za Váš zájem o Valašskou pevnost. V příloze zasíláme fakturu, která slouží jako daňový doklad. Platba už proběhla dříve a není tedy potřeba nic platit.';
        } else {
            $payment_info = 'Dobrý den. Děkujeme za Váš zájem o Valašskou pevnost. V příloze zasíláme fakturu, kterou je možné uhradit převodem.';
        }

        return (new MailMessage)
            ->subject('Faktura - Valašská pevnost')
            ->greeting('Vaše faktura je připravena!')
            ->line($payment_info)
            ->line('Pokud byste náhodou narazili na jakýkoli problém nebo nepřesnost, ozvěte se nám prosím zpět. Můžete odpovědět přímo na tento email.')
            ->line('Děkujeme za Vaši přízeň. ♥️')
            ->salutation('Tým Valašské pevnosti')
            ->attachData($pdf->output(), $this->invoice->code.'.pdf');
    }
}
