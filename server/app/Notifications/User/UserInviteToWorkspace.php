<?php

namespace App\Notifications\User;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class UserInviteToWorkspace extends VerifyEmail
{
    use Queueable;

    public mixed $sender_name;

    public mixed $workspace_name;

    /**
     * Create a new notification instance.
     */
    public function __construct($sender_name, $workspace_name)
    {
        $this->sender_name = $sender_name;
        $this->workspace_name = $workspace_name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $route = config('app.frontend_url').'/join-workspace-guest?email='.$notifiable->email;

        return (new MailMessage)
            ->greeting('Dobrý den!')
            ->line($this->sender_name.' Vás zve, abyste se připojili do organizace '.$this->workspace_name.' v Prognola.')
            ->action('Připojit se do '.$this->workspace_name, $route)
            ->subject($this->sender_name.' vás zve do Prognola')
            ->from(config('mail.from.address'), 'Prognola');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
