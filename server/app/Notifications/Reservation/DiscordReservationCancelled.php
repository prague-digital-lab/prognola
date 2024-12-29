<?php

namespace App\Notifications\Reservation;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordReservationCancelled extends Notification implements ShouldQueue
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
        return [DiscordChannel::class];
    }

    public function toDiscord(Reservation $reservation)
    {
        $assigned_info = $reservation->assigned_user ? 'Recepci měl na starost: '.$reservation->assigned_user->getDiscordIdSignatureOrName() : '';

        return DiscordMessage::create("Rezervace č. $reservation->id se ruší. ❌ $assigned_info ", [
            'title' => 'Rezervace '.$reservation->id.' - '.$reservation->email,
            'url' => route('admin.reservations.show', $reservation),
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
            ],
        ]);
    }
}
