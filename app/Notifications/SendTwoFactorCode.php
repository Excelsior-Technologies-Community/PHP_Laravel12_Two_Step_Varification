<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendTwoFactorCode extends Notification
{
    use Queueable;

    public function __construct()
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your Two-Factor Authentication Code')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your two-factor authentication verification code is:')
            ->line($notifiable->two_factor_code)
            ->line('This OTP is valid for 10 minutes.')
            ->line('You have a maximum of 5 verification attempts.')
            ->line('Repeated failed attempts will temporarily lock verification.')
            ->line('If you did not attempt to login, please secure your account immediately.')
            ->salutation('Regards, ' . config('app.name'));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'event' => 'two_factor_otp',
        ];
    }
}