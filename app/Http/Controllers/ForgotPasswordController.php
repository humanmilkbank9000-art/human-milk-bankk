<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Notifications\SendRecoveryCodeNotification;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\SendRecoveryCodeRequest;
use App\Http\Requests\VerifyRecoveryCodeRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Services\PasswordResetService;

class ForgotPasswordController extends Controller
{
    protected int $codeExpiryMinutes = 10;
    protected PasswordResetService $service;

    public function __construct(PasswordResetService $service)
    {
        $this->service = $service;
    }

    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendRecoveryCode(SendRecoveryCodeRequest $request): RedirectResponse
    {
        $contactNumber = $request->input('contact_number');

        try {
            $code = $this->service->generateAndSendCode($contactNumber);

            session()->put('password_reset.contact_number', $contactNumber);
            session()->put('password_reset.code_sent_at', now());

            $usingLogDriver = strtolower((string) config('sms.driver', 'log')) === 'log';
            if ($usingLogDriver && config('app.debug')) {
                session()->put('password_reset.last_code', $code);
            } else {
                session()->forget('password_reset.last_code');
            }

            session()->forget('password_reset.verified');

            return redirect()->route('password.verify')->with('status', 'We sent a recovery code to your mobile number.');
        } catch (\RuntimeException $e) {
            return back()->withErrors(['contact_number' => $e->getMessage()])->withInput();
        }
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

    public function verifyCode(VerifyRecoveryCodeRequest $request): RedirectResponse
    {
        $contactNumber = session('password_reset.contact_number');

        if (!$contactNumber) {
            return redirect()->route('password.forgot');
        }
        try {
            $this->service->verifyCode($contactNumber, $request->input('code'));
            session()->put('password_reset.verified', true);
            session()->forget('password_reset.last_code');
            return redirect()->route('password.reset')->with('status', 'Code verified. You can now reset your password.');
        } catch (\RuntimeException $e) {
            throw ValidationException::withMessages(['code' => $e->getMessage()]);
        }
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

    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $contactNumber = session('password_reset.contact_number');
        $isVerified = session('password_reset.verified');

        if (!$contactNumber || !$isVerified) {
            return redirect()->route('password.forgot');
        }

        try {
            $this->service->resetPassword($contactNumber, $request->input('password'));
            session()->forget('password_reset');
            return redirect()->route('login')->with('status', 'Your password has been reset. You can now sign in.');
        } catch (\RuntimeException $e) {
            return redirect()->route('password.forgot')->withErrors(['contact_number' => $e->getMessage()]);
        }
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
