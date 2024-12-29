<?php

namespace App\Notifications\ReceivedInvoice;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordExpenseStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public Expense $expense;

    public User $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Expense $expense, User $user)
    {
        $this->expense = $expense;
        $this->user = $user;
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
     * @throws \Exception
     */
    public function toDiscord(): DiscordMessage
    {
        $route = 'https://prognola.com/'.$this->expense->workspace->url_slug.'/expenses/'.$this->expense->uuid;
        $embed = $this->getEmbed($this->expense, $route);

        $status_msg = $this->getStatusMessage();

        return DiscordMessage::create($status_msg, $embed);
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

    /**
     * @throws \Exception
     */
    private function getEmbed(Expense $expense, $route): array
    {
        $title = $expense->getTitleString();

        if ($this->expense->organisation !== null) {
            $organisation_route = 'https://prognola.com/'.$this->expense->workspace->url_slug.'/organisations/'.$this->expense->organisation->uuid;
        } else {
            $organisation_route = null;
        }

        return [
            'title' => $title,
            'url' => $route,
            'color' => 5921370,
            'fields' => [
                [
                    'name' => 'Organizace',
                    'value' => $this->expense->organisation !== null ? "[{$this->expense->organisation->name}]({$organisation_route})" : '-',
                    'inline' => true,
                ],

                [
                    'name' => 'Částka',
                    'value' => number_format($this->expense->price, 2, ',', ' ').' Kč',
                    'inline' => true,
                ],

                // New line in inline embeds
                [
                    'name' => '',
                    'value' => '',
                ],

                [
                    'name' => 'Datum úhrady',
                    'value' => $this->expense->paid_at->format('j.n.Y'),
                    'inline' => true,

                ],

                [
                    'name' => 'Stav',
                    'value' => $this->expense->getPaymentStatusString(),
                    'inline' => true,
                ],

            ],
        ];
    }

    private function getStatusMessage()
    {

        if ($this->expense->payment_status == Expense::PAYMENT_STATUS_PENDING) {
            return "{$this->user->getDiscordIdSignatureOrName()} nastavil výdaj {$this->expense->description} k úhradě.";
        }
        if ($this->expense->payment_status == Expense::PAYMENT_STATUS_PLAN) {
            return "{$this->user->getDiscordIdSignatureOrName()} změnil výdaj {$this->expense->description} na odhad.";
        }
        if ($this->expense->payment_status == Expense::PAYMENT_STATUS_PAID) {
            return "{$this->user->getDiscordIdSignatureOrName()} nastavil výdaj {$this->expense->description} jako uhrazený.";
        }
        if ($this->expense->payment_status == Expense::PAYMENT_STATUS_DRAFT) {
            return "{$this->user->getDiscordIdSignatureOrName()} nastavil výdaj {$this->expense->description} ke zpracování.";
        }
    }
}
