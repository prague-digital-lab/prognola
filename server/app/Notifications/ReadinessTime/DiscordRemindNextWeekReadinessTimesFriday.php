<?php

namespace App\Notifications\ReadinessTime;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordRemindNextWeekReadinessTimesFriday extends Notification
{
    use Queueable;

    public mixed $next_week_readiness_times;

    public mixed $unassigned_readiness_times;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($next_week_readiness_times)
    {
        $this->next_week_readiness_times = $next_week_readiness_times;
        $this->unassigned_readiness_times = $next_week_readiness_times->where('user_id', null);
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
    public function toDiscord($notifiable)
    {
        $url = route('admin.readiness-times.index', [
            'date_from' => Carbon::now()->addWeek()->startOfWeek()->format('Y-m-d'),
            'date_to' => Carbon::now()->addWeek()->endOfWeek()->format('Y-m-d'),
        ]);

        return DiscordMessage::create("Ahoj recepční! Blíží se další týden a je potřeba si na něj dopsat směny.\nMějte pěkný víkend! 🔥🚀")
            ->components([
                [
                    'type' => 1,
                    'components' => [
                        [
                            'type' => 2,
                            'label' => 'Zapsat směny v IS',
                            'style' => 5,
                            'url' => $url,
                        ],
                    ],
                ],
            ]);
    }
}
