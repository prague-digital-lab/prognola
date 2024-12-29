<?php

namespace App\Notifications\CashBalanceCheck;

use App\Models\CashBalanceCheck;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordCashBalanceCheckCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public CashBalanceCheck $cashBalanceCheck;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct() {}

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
    public function toDiscord(CashBalanceCheck $cashBalanceCheck)
    {
        $assigned_user_discord_id = $cashBalanceCheck->user->getDiscordIdSignatureOrName();

        return DiscordMessage::create($assigned_user_discord_id.' provedl kontrolu zůstatku hotovostní kasy.');
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
