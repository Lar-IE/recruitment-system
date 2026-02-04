<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminPasswordReset extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $temporaryPassword)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('Your password has been reset'))
            ->greeting(__('Hello :name,', ['name' => $notifiable->name]))
            ->line(__('An administrator has reset your password.'))
            ->line(__('Temporary password: :password', ['password' => $this->temporaryPassword]))
            ->line(__('Please log in and change your password immediately.'));
    }
}
