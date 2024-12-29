<?php

namespace App\Notifications\Reservation;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerReservationCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 20;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $reservation
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail(Reservation $reservation)
    {
        return (new MailMessage)
            ->subject('Rezervace byla zrušena')
            ->greeting('Dobrý den,')
            ->line('Potvrzujeme, že Vaše rezervace hry (č. '.$reservation->id.') byla zrušena.')
            ->line('Pokud by se jednalo o chybu nebo byste chtěli s čímkoli poradit, dejte nám prosím vědět. 😇')
            ->line('Za celý tým zdraví')
            ->salutation('Martin z Valašské pevnosti');
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
