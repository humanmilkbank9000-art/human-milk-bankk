<?php

namespace App\Notifications;

use App\Notifications\Channels\QproxySmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification
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
    public function via(object $notifiable): array
    {
        $driver = config('sms.driver', 'log');

        if ($driver === 'qproxy') {
            return [QproxySmsChannel::class];
        }

        // For 'log' driver or fallback, return empty array
        return [];
    }

    /**
     * Get the SMS message content for Qproxy.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toQproxy($notifiable): string
    {
        return $this->getMessage();
    }

    /**
     * Get the password changed message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return "Your password has been successfully changed. If you did not make this change, please contact us immediately.";
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
