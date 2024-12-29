<?php

namespace App\Notifications\ReadinessTime;

use App\Models\ReadinessTime;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordUserAssignedToReadinessTime extends Notification implements ShouldQueue
{
    use Queueable;

    public User $assigned_user;

    public ?User $assigning_user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $assigned_user, User $assigned_by_user)
    {
        $this->assigned_user = $assigned_user;

        if ($this->assigned_user->id !== $assigned_by_user->id) {
            $this->assigning_user = $assigned_by_user;
        } else {
            $this->assigning_user = null;
        }
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
     * @param  mixed  $notifiable
     * @return DiscordMessage
     */
    public function toDiscord(ReadinessTime $readinessTime)
    {
        $assigned_user_discord_id = $this->assigned_user->getDiscordIdSignatureOrName();
        $readiness_time_title = $readinessTime->getDateTimeString();
        $time_route = route('admin.readiness_times.show', $readinessTime);

        if ($this->assigning_user === null) {
            $message = "$assigned_user_discord_id si zapsal směnu [$readiness_time_title]($time_route).";
        } else {
            $assigning_user_discord_id = $this->assigning_user->getDiscordIdSignatureOrName();

            $message = "$assigning_user_discord_id zapsal uživateli $assigned_user_discord_id směnu [$readiness_time_title]($time_route).";
        }

        if ($readinessTime->reservations->count() > 0) {
            $arr = $readinessTime->reservations->sortBy('date_from')->map(function (Reservation $reservation) {
                return $reservation->getTimeString().' - '.$reservation->email;
            });

            $res_str = implode("\n", $arr->toArray());
        } else {
            $res_str = 'zatím volno';
        }

        return DiscordMessage::create($message, [
            'title' => 'Směna '.$readiness_time_title,
            'url' => $time_route,
            'color' => 8311585,
            'fields' => [
                [
                    'name' => 'Recepční :technologist: ',
                    'value' => $assigned_user_discord_id,
                    'inline' => true,
                ],
                [
                    'name' => 'Rezervace :calendar_spiral:',
                    'value' => $res_str,
                    'inline' => true,
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
