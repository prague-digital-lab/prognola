<?php

namespace App\Notifications\ReadinessTime;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordRemindNextWeekReadinessTimesSaturday extends Notification
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
     */
    public function via(): array
    {
        return [DiscordChannel::class];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDiscord(): DiscordMessage
    {
        $url = route('admin.readiness-times.index', [
            'date_from' => Carbon::now()->addWeek()->startOfWeek()->format('Y-m-d'),
            'date_to' => Carbon::now()->addWeek()->endOfWeek()->format('Y-m-d'),
        ]);

        // Users info
        $users = User::all();
        $users = $users->map(function ($user) {
            $user_readiness_times = $this->next_week_readiness_times->where('user_id', $user->id);

            $user->readiness_times_count = $user_readiness_times->count();

            $user->readiness_times_hours = 0;
            foreach ($user_readiness_times as $readiness_time) {
                $user->readiness_times_hours += $readiness_time->date_from->diffInHours($readiness_time->date_to);
            }

            return $user;
        });

        $users = $users->filter(function ($user) {
            if ($user->readiness_times_count > 0 || $user->active_at_reception) {
                return true;
            }

            return false;
        });

        $users = $users->sortByDesc('readiness_times_hours');

        $discord_signature_array = [];

        foreach ($users as $user) {
            /** @var User $user */
            $discord_signature_array[] = $user->getDiscordIdSignatureOrName().': '.$user->readiness_times_count.' ('.$user->readiness_times_hours.' h)';
        }

        $users_with_counts_string = implode("\n", $discord_signature_array);

        $unassigned_count = $this->unassigned_readiness_times->count();

        if ($unassigned_count == 1) {
            $unassigned_count_string = $unassigned_count.' směnu';
        } elseif ($unassigned_count >= 2 && $unassigned_count <= 4) {
            $unassigned_count_string = $unassigned_count.' směny';
        } else {
            $unassigned_count_string = $unassigned_count.' směn';
        }

        if ($unassigned_count > 0) {

            $message = <<<MESSAGE
            ⚠️ Ahoj! Pokud jste to ještě nestihli, vyplňte si prosím dnes do 19:00 směny na příští týden. V tuto chvíli je stále potřeba pokrýt **$unassigned_count_string**.
            ### Zapsané směny
            $users_with_counts_string
            MESSAGE;

            return DiscordMessage::create($message)
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
        } else {
            return DiscordMessage::create('Hurá! Všechny směny na příští týden jsou zapsané. ♥️');
        }

    }
}
