<?php

namespace App\Notifications\ReceivedInvoice;

use App\Models\Expense;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\Discord\DiscordChannel;
use NotificationChannels\Discord\DiscordMessage;

class DiscordExpenseUploadedByEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public Expense $expense;

    public string $email;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Expense $expense, string $email)
    {
        $this->expense = $expense;
        $this->email = $email;
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
        $route = 'https://prognola.com/'.$this->expense->workspace->url_slug.'/expenses/'.$this->expense->uuid;
        $embed = $this->getEmbed($this->expense, $this->email, $route);

        return DiscordMessage::create('Nový výdaj nahrán prostřednictvím emailu.', $embed)
            ->components([
                [
                    'type' => 1,
                    'components' => [
                        [
                            'type' => 2,
                            'label' => 'Zobrazit v Prognole',
                            'style' => 5,
                            'url' => $route,
                        ],
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

    /**
     * @throws \Exception
     */
    private function getEmbed(Expense $expense, $email, $route): array
    {
        $title = $expense->getTitleString();

        return [
            'title' => $title,
            'url' => $route,
            'color' => 5921370,
            'fields' => [
                [
                    'name' => 'Přijato z emailu',
                    'value' => $email,
                    'inline' => true,
                ],

                [
                    'name' => 'Stav',
                    'value' => $expense->getPaymentStatusString(),
                    'inline' => true,
                ],

            ],
        ];
    }
}
