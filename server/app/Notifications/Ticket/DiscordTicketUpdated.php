<?php

namespace App\Notifications\Ticket;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordTicketUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 20;

    public Ticket $ticket;

    public string $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Ticket $ticket, string $message)
    {
        $this->message = $message;
        $this->ticket = $ticket;
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

    public function toDiscord($notifiable)
    {
        return DiscordMessage::create($this->message);
    }
}
