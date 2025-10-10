<?php

namespace App\Notifications;

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

    public function via(object $notifiable): array
    {
        // Will be updated to use Twilio
        return [];
    }

    public function getMessage(): string
    {
        return sprintf(
            'Your Human Milk Bank recovery code is %s. It expires in %d minutes.',
            $this->code,
            $this->expiryMinutes
        );
    }
}
