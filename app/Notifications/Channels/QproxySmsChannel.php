<?php

namespace App\Notifications\Channels;

use App\Services\SmsService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class QproxySmsChannel
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        // Get the phone number from the notifiable entity
        $to = $this->getPhoneNumber($notifiable);

        if (!$to) {
            Log::warning('Qproxy SMS: No phone number found for notifiable', [
                'notifiable_id' => $notifiable->id ?? null,
                'notifiable_type' => get_class($notifiable),
            ]);
            return;
        }

        // Format phone number to international format
        $to = $this->formatPhoneNumber($to);

        // Get the message from the notification
        if (!method_exists($notification, 'toQproxy') && !method_exists($notification, 'getMessage')) {
            Log::error('Qproxy SMS: Notification missing toQproxy() or getMessage() method', [
                'notification' => get_class($notification),
            ]);
            return;
        }

        $message = method_exists($notification, 'toQproxy')
            ? $notification->toQproxy($notifiable)
            : $notification->getMessage();

        try {
            // Send the SMS using the SmsService
            $result = $this->smsService->send($to, $message);

            if ($result['success']) {
                Log::info('Qproxy SMS sent successfully', [
                    'to' => $to,
                    'result' => $result,
                ]);
            } else {
                Log::error('Failed to send SMS via Qproxy', [
                    'to' => $to,
                    'error' => $result['message'] ?? 'Unknown error',
                    'result' => $result,
                ]);
                
                throw new \Exception($result['message'] ?? 'Failed to send SMS');
            }

        } catch (\Exception $e) {
            Log::error('Exception while sending SMS via Qproxy', [
                'to' => $to,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Get the phone number from the notifiable entity.
     *
     * @param  mixed  $notifiable
     * @return string|null
     */
    protected function getPhoneNumber($notifiable): ?string
    {
        if (isset($notifiable->contact_number)) {
            return $notifiable->contact_number;
        }

        if (isset($notifiable->phone)) {
            return $notifiable->phone;
        }

        if (isset($notifiable->phone_number)) {
            return $notifiable->phone_number;
        }

        if (method_exists($notifiable, 'routeNotificationForQproxy')) {
            return $notifiable->routeNotificationForQproxy();
        }

        return null;
    }

    /**
     * Format Philippine phone number to international format.
     *
     * Converts: 09123456789 → +639123456789
     *
     * @param  string  $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any spaces, dashes, or parentheses
        $phoneNumber = preg_replace('/[\s\-\(\)]/', '', $phoneNumber);

        // If starts with 0, replace with +63
        if (substr($phoneNumber, 0, 1) === '0') {
            return '+63' . substr($phoneNumber, 1);
        }

        // If starts with 9 and is 10 digits, add +63
        if (substr($phoneNumber, 0, 1) === '9' && strlen($phoneNumber) === 10) {
            return '+63' . $phoneNumber;
        }

        // If already has +, return as is
        if (substr($phoneNumber, 0, 1) === '+') {
            return $phoneNumber;
        }

        // If starts with 63, add +
        if (substr($phoneNumber, 0, 2) === '63') {
            return '+' . $phoneNumber;
        }

        // Default: return as is
        return $phoneNumber;
    }
}
