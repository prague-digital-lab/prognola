<?php

namespace App\Notifications\Reservation;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class CustomerReservationStored extends Notification implements ShouldQueue
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
        return ['mail', DiscordChannel::class];
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
            ->subject('Potvrzení rezervace - Valašská pevnost')
            ->greeting('Dobrý den,')
            ->line('potvrzujeme Vaši rezervaci do Valašské pevnosti.')
            ->line('Kontakt: '.$reservation->email.', '.$reservation->phone.'.')
            ->line('Detail své rezervace si můžete zkontrolovat také na našem webu.')
            ->action('Zobrazit rezervaci', route('reservation.public', $reservation->uuid))
            ->line('Budeme se na Vás těšit '.$reservation->date_from->format('j.n.Y').' v '.$reservation->date_from->format('G:i')
                .' na adrese Kulturní 1794 v Rožnově pod Radhoštěm. 🤩')
            ->salutation('Tým Valašské pevnosti');
    }

    public function toDiscord(Reservation $reservation)
    {

        return DiscordMessage::create('Nová rezervace na Valašskou pevnost. 🏰', [
            'title' => 'Rezervace '.$reservation->id.' - '.$reservation->email,
            'url' => route('admin.reservations.show', $reservation),
            'color' => 8311585,
            'fields' => [
                [
                    'name' => 'Email',
                    'value' => $reservation->email,
                ],
                [
                    'name' => 'Telefon',
                    'value' => $reservation->phone,
                    'inline' => true,
                ],
                [
                    'name' => 'Počet lidí',
                    'value' => $reservation->team_count,
                ],
                [
                    'name' => 'Datum a čas',
                    'value' => $reservation->getDateStringWithYear().' '.$reservation->date_from->diffForHumans(),
                ],
                [
                    'name' => 'Poznámka od zákazníka',
                    'value' => $reservation->description ?: '-',
                ],
            ],
        ]);
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
