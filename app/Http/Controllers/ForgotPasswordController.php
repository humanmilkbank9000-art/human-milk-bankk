<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SendRecoveryCodeNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    protected int $codeExpiryMinutes = 10;

    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendRecoveryCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'contact_number' => ['required', 'string', 'regex:/^0\d{10}$/'],
        ], [
            'contact_number.regex' => 'Please enter a valid 11-digit Philippine mobile number starting with 0.',
        ]);

        $contactNumber = $validated['contact_number'];

        $user = User::where('contact_number', $contactNumber)->first();

        if (!$user) {
            return back()->withErrors([
                'contact_number' => 'We could not find an account with that mobile number.',
            ])->withInput();
        }

        $code = (string) random_int(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['contact_number' => $contactNumber],
            [
                'token' => Hash::make($code),
                'created_at' => now(),
            ]
        );

        $usingLogDriver = $this->shouldLogSms();

        if ($usingLogDriver) {
            $this->logRecoveryCode($contactNumber, $code);
        } else {
            try {
                $user->notify(new SendRecoveryCodeNotification($code, $this->codeExpiryMinutes));
            } catch (\Throwable $exception) {
                Log::error('Failed to send recovery code SMS', [
                    'contact_number' => $contactNumber,
                    'error' => $exception->getMessage(),
                ]);

                $errorMessage = 'We were unable to send the recovery code. Please try again shortly.';

                if (config('app.debug')) {
                    $errorMessage .= ' (SMS error: ' . $exception->getMessage() . ')';
                }

                return back()->withErrors([
                    'contact_number' => $errorMessage,
                ])->withInput();
            }
        }

        session()->put('password_reset.contact_number', $contactNumber);
        session()->put('password_reset.code_sent_at', now());

        if ($usingLogDriver && config('app.debug')) {
            session()->put('password_reset.last_code', $code);
        } else {
            session()->forget('password_reset.last_code');
        }

        session()->forget('password_reset.verified');

        return redirect()->route('password.verify')->with('status', 'We sent a recovery code to your mobile number.');
    }

    public function showVerifyCodeForm(): RedirectResponse|View
    {
        $contactNumber = session('password_reset.contact_number');

        if (!$contactNumber) {
            return redirect()->route('password.forgot');
        }

        $maskedContact = $this->maskContactNumber($contactNumber);

        return view('auth.verify-code', [
            'contactNumber' => $contactNumber,
            'maskedContactNumber' => $maskedContact,
        ]);
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $contactNumber = session('password_reset.contact_number');

        if (!$contactNumber) {
            return redirect()->route('password.forgot');
        }

        $validated = $request->validate([
            'code' => ['required', 'digits:6'],
        ]);

        $record = DB::table('password_reset_tokens')->where('contact_number', $contactNumber)->first();

        if (!$record) {
            throw ValidationException::withMessages([
                'code' => 'The recovery code is invalid or has expired.',
            ]);
        }

        $createdAt = $record->created_at ? Carbon::parse($record->created_at) : now();

        if ($createdAt->addMinutes($this->codeExpiryMinutes)->isPast()) {
            DB::table('password_reset_tokens')->where('contact_number', $contactNumber)->delete();

            throw ValidationException::withMessages([
                'code' => 'The recovery code has expired. Please request a new one.',
            ]);
        }

        if (!Hash::check($validated['code'], $record->token)) {
            throw ValidationException::withMessages([
                'code' => 'The recovery code you entered is incorrect.',
            ]);
        }

        session()->put('password_reset.verified', true);
        session()->forget('password_reset.last_code');

        return redirect()->route('password.reset')->with('status', 'Code verified. You can now reset your password.');
    }

    public function showResetPasswordForm(): RedirectResponse|View
    {
        $contactNumber = session('password_reset.contact_number');
        $isVerified = session('password_reset.verified');

        if (!$contactNumber || !$isVerified) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $contactNumber = session('password_reset.contact_number');
        $isVerified = session('password_reset.verified');

        if (!$contactNumber || !$isVerified) {
            return redirect()->route('password.forgot');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('contact_number', $contactNumber)->first();

        if (!$user) {
            return redirect()->route('password.forgot')->withErrors([
                'contact_number' => 'We could not find an account associated with that mobile number.',
            ]);
        }

        $user->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        DB::table('password_reset_tokens')->where('contact_number', $contactNumber)->delete();

        session()->forget('password_reset');

        return redirect()->route('login')->with('status', 'Your password has been reset. You can now sign in.');
    }

    protected function maskContactNumber(string $contactNumber): string
    {
        if (Str::startsWith($contactNumber, '0')) {
            return Str::mask($contactNumber, '*', 3, 4);
        }

        return Str::mask($contactNumber, '*', max(strlen($contactNumber) - 4, 0));
    }

    protected function shouldLogSms(): bool
    {
        return strtolower((string) config('sms.driver', 'log')) === 'log';
    }

    protected function logRecoveryCode(string $contactNumber, string $code): void
    {
        $context = [
            'contact_number' => $contactNumber,
            'code' => $code,
            'expires_in_minutes' => $this->codeExpiryMinutes,
        ];

        if ($channel = config('sms.log_channel')) {
            Log::channel($channel)->info('Account recovery code generated (SMS log driver).', $context);
            return;
        }

        Log::info('Account recovery code generated (SMS log driver).', $context);
    }
}
