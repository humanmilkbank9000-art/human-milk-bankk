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

    public function generateAndSendCode(string $contactNumber): string
    {
        $user = User::where('contact_number', $contactNumber)->first();
        if (!$user) {
            throw new \RuntimeException('We could not find an account with that mobile number.');
        }

        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['contact_number' => $contactNumber],
            ['token' => Hash::make($code), 'created_at' => now()]
        );

        $usingLogDriver = strtolower((string) config('sms.driver', 'log')) === 'log';

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

        $user->forceFill(['password' => Hash::make($password)])->save();
        DB::table('password_reset_tokens')->where('contact_number', $contactNumber)->delete();
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
