<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;

class PasswordResetService
{
    protected int $codeExpiryMinutes = 10;
    protected OtpService $otpService;

    public function __construct(OtpService $otpService)
    {
        $this->otpService = $otpService;
    }

    public function generateAndSendCode(string $contactNumber): string
    {
        $user = User::where('contact_number', $contactNumber)->first();
        if (!$user) {
            throw new \RuntimeException('We could not find an account with that mobile number.');
        }

        $driver = strtolower((string) config('sms.driver', 'log'));

        // If using IPROGTECH OTP driver, send via external API (no local token storage)
        if ($driver === 'iprogtech_otp') {
            $this->codeExpiryMinutes = 5; // per provider default
            if (!$this->otpService) {
                throw new \RuntimeException('OTP service unavailable.');
            }

            $result = $this->otpService->sendOtp($contactNumber);
            if (!($result['success'] ?? false)) {
                throw new \RuntimeException($result['message'] ?? 'Failed to send recovery code.');
            }

            // Do not expose OTP; return empty string to keep debug UI off for real drivers
            return '';
        }

        // Fallback to existing local code generation + SMS notification
        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['contact_number' => $contactNumber],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        $usingLogDriver = $driver === 'log';

        if ($usingLogDriver) {
            $this->logRecoveryCode($contactNumber, $code);
        } else {
            try {
                $user->notify(new \App\Notifications\SendRecoveryCodeNotification($code, $this->codeExpiryMinutes));
            } catch (\Throwable $exception) {
                Log::error('Failed to send recovery code SMS', ['contact_number' => $contactNumber, 'error' => $exception->getMessage()]);
                throw new \RuntimeException('We were unable to send the recovery code. Please try again shortly.');
            }
        }

        return $code;
    }

    public function verifyCode(string $contactNumber, string $code): void
    {
        $driver = strtolower((string) config('sms.driver', 'log'));

        if ($driver === 'iprogtech_otp') {
            if (!$this->otpService) {
                throw new \RuntimeException('OTP service unavailable.');
            }
            $result = $this->otpService->verifyOtp($contactNumber, $code);
            if (!($result['success'] ?? false)) {
                throw new \RuntimeException($result['message'] ?? 'Invalid or expired code.');
            }
            // External service handles expiry; nothing to store locally
            return;
        }

        $record = DB::table('password_reset_tokens')->where('contact_number', $contactNumber)->first();
        if (!$record) {
            throw new \RuntimeException('The recovery code is invalid or has expired.');
        }

        $createdAt = $record->created_at ? Carbon::parse($record->created_at) : now();
        if ($createdAt->addMinutes($this->codeExpiryMinutes)->isPast()) {
            DB::table('password_reset_tokens')->where('contact_number', $contactNumber)->delete();
            throw new \RuntimeException('The recovery code has expired. Please request a new one.');
        }

        if (!Hash::check($code, $record->token)) {
            throw new \RuntimeException('The recovery code you entered is incorrect.');
        }
    }

    public function resetPassword(string $contactNumber, string $password): void
    {
        $user = User::where('contact_number', $contactNumber)->first();
        if (!$user) {
            throw new \RuntimeException('We could not find an account associated with that mobile number.');
        }

        $sanitizedPassword = trim(strip_tags($password));
        $user->forceFill(['password' => Hash::make($sanitizedPassword)])->save();
        // Clean up local token if present (legacy path)
        try {
            DB::table('password_reset_tokens')->where('contact_number', $contactNumber)->delete();
        } catch (\Throwable $e) {
            // table may not exist in some deployments; ignore
        }
        
        // Send SMS notification about password change
        try {
            $user->notify(new \App\Notifications\PasswordChangedNotification());
        } catch (\Throwable $e) {
            Log::warning('Failed to send password change SMS notification', [
                'contact_number' => $contactNumber,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function logRecoveryCode(string $contactNumber, string $code): void
    {
        $context = ['contact_number' => $contactNumber, 'code' => $code, 'expires_in_minutes' => $this->codeExpiryMinutes];
        if ($channel = config('sms.log_channel')) {
            Log::channel($channel)->info('Account recovery code generated (SMS log driver).', $context);
            return;
        }
        Log::info('Account recovery code generated (SMS log driver).', $context);
    }
}
