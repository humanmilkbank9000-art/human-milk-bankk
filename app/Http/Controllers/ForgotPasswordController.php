<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showForgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendRecoveryCode(Request $request): RedirectResponse
    {
        $request->validate([
            'contact_number' => 'required|regex:/^09\d{9}$/'
        ]);

        $contactNumber = $request->contact_number;

        // Check if user exists
        $user = User::where('contact_number', $contactNumber)->first();
        if (!$user) {
            return back()->withErrors(['contact_number' => 'We could not find an account with that mobile number.'])->withInput();
        }

        $url = 'https://sms.iprogtech.com/api/v1/sms_messages';
        $api_token = '91d56803aa4a36ef3e7b3b350297ce3b35dee465';

        $formatted_number = preg_replace('/^0/', '63', $contactNumber);
        $code = rand(100000, 999999);
        $message = "Your Human Milk Bank recovery code is: $code. Do not share this code with anyone.";

        $data = [
            'api_token' => $api_token,
            'message' => $message,
            'phone_number' => $formatted_number
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/x-www-form-urlencoded'
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if (stripos($response, 'successfully queued for delivery') !== false) {
            Session::put('verification_code', $code);
            Session::put('contact_number', $contactNumber);
            return redirect()->route('password.verify')->with('status', 'We sent a recovery code to your mobile number.');
        } else {
            return back()->withErrors(['contact_number' => 'Failed to send SMS. Please try again.'])->withInput();
        }
    }

    public function showVerifyCodeForm(): RedirectResponse|View
    {
        if (!Session::has('verification_code')) {
            return redirect()->route('password.forgot');
        }

        $contactNumber = Session::get('contact_number');
        $maskedContact = $this->maskContactNumber($contactNumber);

        return view('auth.verify-code', [
            'contactNumber' => $contactNumber,
            'maskedContactNumber' => $maskedContact,
        ]);
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|digits:6']);
        
        $entered = $request->code;
        $correct = Session::get('verification_code');

        if ($entered == $correct) {
            Session::put('code_verified', true);
            Session::forget('verification_code');
            return redirect()->route('password.reset')->with('status', 'Code verified. You can now reset your password.');
        } else {
            return back()->withErrors(['code' => 'The recovery code you entered is incorrect.']);
        }
    }

    public function showResetPasswordForm(): RedirectResponse|View
    {
        $contactNumber = Session::get('contact_number');
        $isVerified = Session::get('code_verified');

        if (!$contactNumber || !$isVerified) {
            return redirect()->route('password.forgot');
        }

        return view('auth.reset-password');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $contactNumber = Session::get('contact_number');
        $isVerified = Session::get('code_verified');

        if (!$contactNumber || !$isVerified) {
            return redirect()->route('password.forgot');
        }

        $user = User::where('contact_number', $contactNumber)->first();
        if (!$user) {
            return redirect()->route('password.forgot')->withErrors(['contact_number' => 'We could not find an account associated with that mobile number.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        Session::forget(['contact_number', 'code_verified']);

        return redirect()->route('login')->with('status', 'Your password has been reset. You can now sign in.');
    }

    protected function maskContactNumber(string $contactNumber): string
    {
        if (Str::startsWith($contactNumber, '0')) {
            return Str::mask($contactNumber, '*', 3, 4);
        }

        return Str::mask($contactNumber, '*', max(strlen($contactNumber) - 4, 0));
    }
}
