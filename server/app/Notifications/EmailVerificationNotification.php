<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

class EmailVerificationNotification extends VerifyEmail
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
        $prefix = config('app.frontend_url').'/verify-submit?url=';
        $verificationUrl = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->greeting('Dobrý den!')
            ->line('Vítá vás Prognola. Pro dokončení registrace je potřeba ověřit Váš email.')
            ->action('Ověřit email', $prefix.urlencode($verificationUrl))
            ->line('Pokud jste účet nezřizovali, na tlačítko neklikejte.')
            ->subject('Ověřte Váš účet')
            ->from(config('mail.from.address'), 'Michael from Prognola');
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
