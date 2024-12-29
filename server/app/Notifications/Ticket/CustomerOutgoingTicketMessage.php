<?php

namespace App\Notifications\Ticket;

use App\Models\TicketMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerOutgoingTicketMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 20;

    public TicketMessage $message;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(TicketMessage $ticketMessage)
    {
        $this->message = $ticketMessage;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("{$this->message->ticket->name} [#{$this->message->ticket->id}]")
            ->view('livewire.is.ticket.outgoing_email_ticket_message', [
                'ticket_message' => $this->message,
            ]);
    }
}
