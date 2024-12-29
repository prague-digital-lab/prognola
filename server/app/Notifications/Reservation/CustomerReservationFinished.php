<?php

namespace App\Notifications\Reservation;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerReservationFinished extends Notification implements ShouldQueue
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
            ->subject('Děkujeme za návštěvu')
            ->greeting('Dobrý den,')
            ->line('děkujeme, že jste navštívili Valašskou pevnost. 😇 ')
            ->line('Na webu si můžete zobrazit herní časy Vašich týmů.')
            ->action('Zobrazit herní časy týmů', route('reservation.public', $reservation->uuid))
            ->line('Doufáme, že se vám u nás líbilo. Pokud Vás napadá cokoli, co bychom měli zlepšit, napište nám to prosím do odpovědi na tento email.')
            ->line('Budeme moc rádi, pokud nám přidáte hodnocení na [mapách Google](https://goo.gl/maps/hdedMaWzyGxGk3GN6) nebo na [Mapy.cz](https://mapy.cz/s/lenucezosa).')
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
