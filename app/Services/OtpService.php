<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class OtpService
{
    protected string $baseUrl;
    protected ?string $apiToken;

    public function __construct()
    {
        $this->baseUrl = rtrim((string) config('sms.iprogtech_otp.base_url', 'https://sms.iprogtech.com/api/v1'), '/');
        $this->apiToken = config('sms.iprogtech_otp.api_token');
    }

    /**
     * Create and send OTP to a phone number.
     * @param string $phoneNumber Local PH format e.g. 09171234567
     * @param string|null $message Optional custom message with :otp placeholder
     * @return array{success:bool,message:string,data?:array,http_code?:int}
     */
    public function sendOtp(string $phoneNumber, ?string $message = null): array
    {
        if (empty($this->apiToken)) {
            Log::error('IPROGTECH OTP: Missing API token');
            return [
                'success' => false,
                'message' => 'OTP service not configured (missing API token).',
            ];
        }

        $payload = [
            'api_token' => $this->apiToken,
            'phone_number' => $this->normalizeLocalNumber($phoneNumber),
        ];
        // Prefer explicit message, else fallback to configured template if set
        $template = $message ?? (string) config('sms.iprogtech_otp.message', '');
        if ($template !== '') {
            $payload['message'] = $template; // backend replaces :otp
        }

        return $this->postJson('/otp/send_otp', $payload);
    }

    /**
     * Verify an OTP for a phone number.
     * @param string $phoneNumber Local PH format
     * @param string $otp Six-digit code
     * @return array{success:bool,message:string,http_code?:int}
     */
    public function verifyOtp(string $phoneNumber, string $otp): array
    {
        if (empty($this->apiToken)) {
            return [
                'success' => false,
                'message' => 'OTP service not configured (missing API token).',
            ];
        }

        $payload = [
            'api_token' => $this->apiToken,
            'phone_number' => $this->normalizeLocalNumber($phoneNumber),
            'otp' => $otp,
        ];

        return $this->postJson('/otp/verify_otp', $payload);
    }

    /**
     * Optionally list OTPs (for admin/debug).
     */
    public function listOtps(): array
    {
        if (empty($this->apiToken)) {
            return [
                'success' => false,
                'message' => 'OTP service not configured (missing API token).',
            ];
        }

        // The provided docs show GET /api/v1/otp with api_token; keep POST for safety in cURL usage if needed
        return $this->getJson('/otp', ['api_token' => $this->apiToken]);
    }

    protected function postJson(string $path, array $payload): array
    {
        $url = $this->baseUrl . $path;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    // Use default SSL verification settings for security
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $raw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno !== 0 || $error) {
            Log::error('IPROGTECH OTP cURL error', [
                'url' => $url,
                'payload' => $this->maskSensitive($payload),
                'errno' => $errno,
                'error' => $error,
            ]);
            return [
                'success' => false,
                'message' => 'Could not reach OTP service: ' . $error,
                'http_code' => $httpCode,
            ];
        }

        $resp = json_decode($raw, true) ?: [];
        $success = ($httpCode >= 200 && $httpCode < 300) && (($resp['status'] ?? '') === 'success');

        Log::info('IPROGTECH OTP API response', [
            'url' => $url,
            'http_code' => $httpCode,
            'response' => $resp,
        ]);

        return [
            'success' => $success,
            'message' => $resp['message'] ?? ($success ? 'OK' : 'Failed'),
            'data' => $resp['data'] ?? null,
            'http_code' => $httpCode,
        ];
    }

    protected function getJson(string $path, array $query = []): array
    {
        $url = $this->baseUrl . $path;
        if (!empty($query)) {
            $url .= (str_contains($url, '?') ? '&' : '?') . http_build_query($query);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    // Use default SSL verification settings for security
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $raw = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno !== 0 || $error) {
            Log::error('IPROGTECH OTP GET cURL error', [
                'url' => $url,
                'errno' => $errno,
                'error' => $error,
            ]);
            return [
                'success' => false,
                'message' => 'Could not reach OTP service: ' . $error,
                'http_code' => $httpCode,
            ];
        }

        $resp = json_decode($raw, true) ?: [];
        $success = ($httpCode >= 200 && $httpCode < 300) && (($resp['status'] ?? '') === 'success');
        return [
            'success' => $success,
            'message' => $resp['message'] ?? ($success ? 'OK' : 'Failed'),
            'data' => $resp['data'] ?? null,
            'http_code' => $httpCode,
        ];
    }

    protected function normalizeLocalNumber(string $phone): string
    {
        // Remove non-digits; keep leading 0 if present
        $digits = preg_replace('/\D+/', '', $phone);
        $normalized = $digits;

        if ($digits && $digits[0] !== '0' && strlen($digits) === 10) {
            // e.g. 9171234567 -> 09171234567
            $normalized = '0' . $digits;
        } elseif (str_starts_with($digits, '63') && strlen($digits) === 12) {
            // 63XXXXXXXXXX -> 0XXXXXXXXXX
            $normalized = '0' . substr($digits, 2);
        } elseif (str_starts_with($digits, '+63') && strlen($digits) === 13) {
            $normalized = '0' . substr($digits, 3);
        }

        return $normalized;
    }

    protected function maskSensitive(array $data): array
    {
        $masked = $data;
        if (isset($masked['api_token'])) {
            $masked['api_token'] = '***';
        }
        return $masked;
    }
}
