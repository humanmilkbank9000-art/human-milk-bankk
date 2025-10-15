<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Services\SmsService;

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
        return ['custom'];
    }

    /**
     * Send the notification via custom SMS service
     *
     * @param mixed $notifiable
     * @return void
     */
    public function toCustom($notifiable): void
    {
        $smsService = app(SmsService::class);
        
        $mobile = $this->formatMobileNumber($notifiable->contact_number);
        $message = "Your password has been successfully changed. If you did not make this change, please contact us immediately.";
        
        $smsService->send($mobile, $message);
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
