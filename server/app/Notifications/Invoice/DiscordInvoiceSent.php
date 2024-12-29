<?php

namespace App\Notifications\Invoice;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordInvoiceSent extends Notification implements ShouldQueue
{
    use Queueable;

    public Invoice $invoice;

    public string $recipient_email;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, string $recipient_email)
    {
        $this->invoice = $invoice;
        $this->recipient_email = $recipient_email;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [DiscordChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @return DiscordMessage
     *
     * @throws \Exception
     */
    public function toDiscord()
    {
        $code = $this->invoice->code;
        $route = route('admin.invoices.edit', $this->invoice);
        $recipient_email = $this->recipient_email;

        return DiscordMessage::create("Doklad [$code]($route) byl odeslán na email $recipient_email.", $this->invoice->getDiscordMessageEmbed());
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
