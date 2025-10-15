<?php

namespace App\Notifications;

use App\Notifications\Channels\InfobipSmsChannel;
use App\Services\SmsService;
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

        if ($driver === 'qproxy') {
            return ['custom'];
        }

        // For 'log' driver or fallback, return empty array
        // The controller will handle logging directly
        return [];
    }

    /**
     * Send the notification via custom SMS service (Qproxy)
     *
     * @param mixed $notifiable
     * @return void
     */
    public function toCustom($notifiable): void
    {
        $smsService = app(SmsService::class);
        
        $mobile = $this->formatMobileNumber($notifiable->contact_number);
        $message = $this->getMessage();
        
        $smsService->send($mobile, $message);
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

    /**
     * Format mobile number to include country code
     * Assumes Philippine mobile numbers if no country code is present
     *
     * @param string $contactNumber
     * @return string
     */
    protected function formatMobileNumber(string $contactNumber): string
    {
        // Remove any spaces or dashes
        $contactNumber = preg_replace('/[\s\-]/', '', $contactNumber);
        
        // If starts with 0, replace with +63 (Philippines)
        if (substr($contactNumber, 0, 1) === '0') {
            return '+63' . substr($contactNumber, 1);
        }
        
        // If starts with 9 and is 10 digits, add +63
        if (substr($contactNumber, 0, 1) === '9' && strlen($contactNumber) === 10) {
            return '+63' . $contactNumber;
        }
        
        // If already has +, return as is
        if (substr($contactNumber, 0, 1) === '+') {
            return $contactNumber;
        }
        
        // If starts with 63, add +
        if (substr($contactNumber, 0, 2) === '63') {
            return '+' . $contactNumber;
        }
        
        // Default: return as is
        return $contactNumber;
    }
}
