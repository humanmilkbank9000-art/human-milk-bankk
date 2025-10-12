<?php

namespace App\Notifications;

use App\Notifications\Channels\InfobipSmsChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SendRecoveryCodeNotification extends Notification
{
    use Queueable;

    protected string $code;
    protected int $expiryMinutes;

    public function __construct(string $code, int $expiryMinutes = 10)
    {
        $this->code = $code;
        $this->expiryMinutes = $expiryMinutes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(object $notifiable): array
    {
        $driver = config('sms.driver', 'log');

        if ($driver === 'infobip') {
            return [InfobipSmsChannel::class];
        }

        // For 'log' driver or fallback, return empty array
        // The controller will handle logging directly
        return [];
    }

    /**
     * Get the SMS message content.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    public function toInfobip($notifiable): string
    {
        return $this->getMessage();
    }

    /**
     * Get the recovery code message.
     *
     * @return string
     */
    public function getMessage(): string
    {
        return sprintf(
            'Your Human Milk Bank recovery code is %s. It expires in %d minutes. Do not share this code with anyone.',
            $this->code,
            $this->expiryMinutes
        );
    }
}
