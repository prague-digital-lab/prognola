<?php

namespace App\Notifications\Invoice;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordInvoiceCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public Invoice $invoice;

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
     */
    public function toDiscord()
    {
        $code = $this->invoice->code;
        $route = route('admin.invoices.edit', $this->invoice);
        $price = number_format($this->invoice->price, 2, ',', ' ').' Kč';
        $user = $this->invoice->created_by_user->getDiscordIdSignatureOrName();

        return DiscordMessage::create("$user vystavil doklad [$code]($route) za $price.", $this->invoice->getDiscordMessageEmbed());
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
