<?php

namespace App\Notifications\ReadinessTime;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordUpcomingUnassignedReadinessTime extends Notification implements ShouldQueue
{
    use Queueable;

    public mixed $upcoming_readiness_times;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($upcoming_times)
    {
        $this->upcoming_readiness_times = $upcoming_times->sortBy('date_from');

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
        $times = [];
        foreach ($this->upcoming_readiness_times as $time) {
            $route = route('admin.readiness_times.show', $time);

            $times[] = [
                'name' => $time->getDateTimeString(),
                'value' => "[zobrazit v IS]($route)",
            ];
        }

        $is_url = route('admin.readiness-times.index');

        $embed = [
            'title' => 'Tyto směny je potřeba obsadit',
            'url' => $is_url,
            'color' => 0xE32400,
            'fields' => $times,
        ];

        return DiscordMessage::create('Pozor! Tento týden jsou neobsazené směny.', $embed);
    }
}
