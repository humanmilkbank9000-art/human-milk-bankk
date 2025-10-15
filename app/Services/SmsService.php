<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Send SMS using Qproxy API
     * 
     * @param string $mobile Mobile number with country code (e.g., +639758669139)
     * @param string $message Message content to send
     * @return array Response with status and message
     */
    public function send(string $mobile, string $message): array
    {
        $driver = config('sms.driver', 'log');

        // If using log driver, just log the message
        if (strtolower($driver) === 'log') {
            return $this->logSms($mobile, $message);
        }

        // If using qproxy driver, send via API
        if (strtolower($driver) === 'qproxy') {
            return $this->sendViaQproxy($mobile, $message);
        }

        // Fallback to log if driver is unknown
        Log::warning("Unknown SMS driver: {$driver}. Falling back to log.");
        return $this->logSms($mobile, $message);
    }

    /**
     * Send SMS via Qproxy API
     * 
     * @param string $mobile Mobile number with country code
     * @param string $message Message content
     * @return array Response with status and message
     */
    protected function sendViaQproxy(string $mobile, string $message): array
    {
        $token = config('sms.qproxy.token');
        $url = config('sms.qproxy.url', 'https://app.qproxy.xyz/api/sms/v1/send');

        if (empty($token)) {
            Log::error('Qproxy SMS token is not configured.');
            return [
                'success' => false,
                'message' => 'SMS configuration error',
            ];
        }

        // Prepare the data
        $send_data = [];
        $send_data['mobile'] = $mobile;
        $send_data['message'] = $message;
        $send_data['token'] = $token;
        $parameters = json_encode($send_data);

        // Initialize cURL
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $headers = array(
            "Content-Type: application/json"
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        $get_sms_status = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        // Log the response
        if ($curl_error) {
            Log::error('Qproxy SMS cURL error', [
                'mobile' => $mobile,
                'error' => $curl_error,
            ]);
            return [
                'success' => false,
                'message' => 'Failed to send SMS',
                'error' => $curl_error,
            ];
        }

        $response = json_decode($get_sms_status, true);

        Log::info('Qproxy SMS sent', [
            'mobile' => $mobile,
            'http_code' => $http_code,
            'response' => $response,
        ]);

        return [
            'success' => $http_code === 200,
            'message' => $http_code === 200 ? 'SMS sent successfully' : 'Failed to send SMS',
            'response' => $response,
            'http_code' => $http_code,
        ];
    }

    /**
     * Log SMS instead of sending (for development/testing)
     * 
     * @param string $mobile Mobile number
     * @param string $message Message content
     * @return array Response with status and message
     */
    protected function logSms(string $mobile, string $message): array
    {
        $channel = config('sms.log_channel');

        $context = [
            'mobile' => $mobile,
            'message' => $message,
        ];

        if ($channel) {
            Log::channel($channel)->info('SMS (log driver)', $context);
        } else {
            Log::info('SMS (log driver)', $context);
        }

        return [
            'success' => true,
            'message' => 'SMS logged successfully',
        ];
    }
}
