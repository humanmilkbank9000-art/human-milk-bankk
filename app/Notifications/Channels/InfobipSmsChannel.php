<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Infobip\Api\SendSmsApi;
use Infobip\Configuration;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;

class InfobipSmsChannel
{
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
            Log::warning('Infobip SMS: No phone number found for notifiable', [
                'notifiable_id' => $notifiable->id ?? null,
                'notifiable_type' => get_class($notifiable),
            ]);
            return;
        }

        // Convert Philippine format (09xxxxxxxxx) to international format (+639xxxxxxxxx)
        $to = $this->formatPhoneNumber($to);

        // Get the message from the notification
        if (!method_exists($notification, 'toInfobip') && !method_exists($notification, 'getMessage')) {
            Log::error('Infobip SMS: Notification missing toInfobip() or getMessage() method', [
                'notification' => get_class($notification),
            ]);
            return;
        }

        $message = method_exists($notification, 'toInfobip')
            ? $notification->toInfobip($notifiable)
            : $notification->getMessage();

        // Get Infobip configuration
        $config = config('sms.infobip');
        $apiKey = $config['api_key'];
        $baseUrl = $config['base_url'];
        $sender = $config['sender'];

        // Validate configuration
        if (!$apiKey) {
            throw new \Exception('Infobip API key not configured. Check INFOBIP_API_KEY in .env');
        }

        if (!$sender) {
            throw new \Exception('Infobip sender not configured. Set INFOBIP_SENDER in .env');
        }

        try {
            // Initialize Infobip client (version 4.0 syntax)
            $configuration = new Configuration();
            $configuration->setHost($baseUrl);
            $configuration->setApiKeyPrefix('Authorization', 'App');
            $configuration->setApiKey('Authorization', $apiKey);
            
            $client = new \GuzzleHttp\Client();
            $smsApi = new SendSmsApi($client, $configuration);

            // Create SMS destination
            $destination = new SmsDestination();
            $destination->setTo($to);

            // Create SMS message
            $smsMessage = new SmsTextualMessage();
            $smsMessage->setDestinations([$destination]);
            $smsMessage->setFrom($sender);
            $smsMessage->setText($message);

            // Create SMS request
            $request = new SmsAdvancedTextualRequest();
            $request->setMessages([$smsMessage]);

            // Send the SMS
            $response = $smsApi->sendSmsMessage($request);

            // Log success
            Log::info('Infobip SMS sent successfully', [
                'to' => $to,
                'message_id' => $response->getMessages()[0]->getMessageId() ?? null,
                'status' => $response->getMessages()[0]->getStatus()->getName() ?? 'sent',
            ]);

        } catch (\Exception $e) {
            // Log errors
            Log::error('Failed to send SMS via Infobip', [
                'to' => $to,
                'error_code' => $e->getCode(),
                'error_message' => $e->getMessage(),
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

        if (method_exists($notifiable, 'routeNotificationForInfobip')) {
            return $notifiable->routeNotificationForInfobip();
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
        if (str_starts_with($phoneNumber, '0')) {
            return '+63' . substr($phoneNumber, 1);
        }

        // If starts with 63, add +
        if (str_starts_with($phoneNumber, '63')) {
            return '+' . $phoneNumber;
        }

        // If already starts with +63, return as is
        if (str_starts_with($phoneNumber, '+63')) {
            return $phoneNumber;
        }

        // Default: assume it's a Philippine number and add +63
        return '+63' . $phoneNumber;
    }
}
