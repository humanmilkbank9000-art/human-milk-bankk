# Forgot Password Direct SMS Implementation - Complete

## Overview

Replaced the OtpService-based approach with a direct cURL SMS implementation that exactly mirrors your working SMS test. The system now sends SMS directly using the IPROGTECH API with form-encoded data.

## Changes Made

### 1. ForgotPasswordController.php

**Location:** `app/Http/Controllers/ForgotPasswordController.php`

**Complete rewrite to use direct SMS approach:**

```php
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
    // Direct SMS sending in sendRecoveryCode()
    // Session-based code verification
    // Password reset with Hash::make()
}
```

**Key Features:**

-   ✅ Direct cURL SMS sending (no service layer)
-   ✅ Form-encoded POST data (`http_build_query`)
-   ✅ Phone number conversion (09... → 63...)
-   ✅ Session-based verification code storage
-   ✅ Success detection via "successfully queued for delivery"

### 2. forgot-password.blade.php

**Location:** `resources/views/auth/forgot-password.blade.php`

**Changes:**

-   Added `pattern="^09\d{9}$"` to input for HTML5 validation
-   Simplified form (removed JavaScript validation)
-   Direct submission to backend

### 3. verify-code.blade.php

**Location:** `resources/views/auth/verify-code.blade.php`

**Changes:**

-   Removed developer debug code display
-   Simplified verification form
-   Clean error message display

### 4. Routes (Already Correct)

**Location:** `routes/web.php`

Routes are already properly configured:

```php
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotPasswordForm'])->name('password.forgot');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendRecoveryCode'])->name('password.forgot.send');

    Route::get('/verify-code', [ForgotPasswordController::class, 'showVerifyCodeForm'])->name('password.verify');
    Route::post('/verify-code', [ForgotPasswordController::class, 'verifyCode'])->name('password.verify.submit');

    Route::get('/reset-password', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('password.update');
});
```

## How It Works

### Flow Diagram

```
User enters phone → SMS sent via cURL → Code stored in session
                                              ↓
User enters code → Verified against session → Redirect to reset
                                              ↓
User sets new password → Hash & save → Login page
```

### Step-by-Step Process

#### 1. **Forgot Password Form** (`/forgot-password`)

```php
// User submits 09XXXXXXXXX format
Validation: regex:/^09\d{9}$/
Check: User exists in database
Convert: 09171234567 → 63171234567
Generate: 6-digit code (100000-999999)
Send SMS: Direct cURL to IPROGTECH API
Store in Session: verification_code, contact_number
```

#### 2. **Verify Code** (`/verify-code`)

```php
// User enters 6-digit code
Compare: Entered code vs Session code
Match: Set code_verified = true in session
Redirect: To reset-password page
```

#### 3. **Reset Password** (`/reset-password`)

```php
// User enters new password + confirmation
Validate: min:8|confirmed
Check: Session has contact_number & code_verified
Update: User password with Hash::make()
Clear: All session data
Redirect: To login page
```

## SMS Implementation Details

### Direct cURL Code (Exactly from your working example)

```php
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
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data)); // FORM-ENCODED!
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded' // NOT JSON!
]);
$response = curl_exec($ch);
curl_close($ch);

if (stripos($response, 'successfully queued for delivery') !== false) {
    // SUCCESS
}
```

## Testing

### Quick Test Script

Run the test script to verify SMS sending:

```bash
php test_direct_sms.php
```

**Before running:**

1. Edit `test_direct_sms.php`
2. Change `$phone_number = '09171234567';` to your actual number
3. Run the script
4. Check your phone for the SMS

### Full Flow Test (via Browser)

1. **Start Server:**

    ```bash
    php artisan serve
    ```

2. **Test Forgot Password:**

    - Go to: `http://localhost:8000/forgot-password`
    - Enter a registered phone number (format: 09XXXXXXXXX)
    - Click "Send Recovery Code"
    - Check your phone for SMS

3. **Test Verify Code:**

    - Should auto-redirect to `/verify-code`
    - Enter the 6-digit code you received
    - Click "Verify Code"

4. **Test Reset Password:**

    - Should auto-redirect to `/reset-password`
    - Enter new password (min 8 chars)
    - Confirm password
    - Click "Update Password"

5. **Test Login:**
    - Should redirect to `/login`
    - Login with new password

## Session Data Structure

```php
// After sending SMS:
Session::get('verification_code')  // e.g., 123456
Session::get('contact_number')     // e.g., 09171234567

// After verifying code:
Session::get('code_verified')      // true
Session::get('contact_number')     // still present

// After resetting password:
// All session data cleared
```

## Security Features

1. **Guest Middleware**: Only non-authenticated users can access
2. **Session Validation**: Each step validates previous step was completed
3. **User Verification**: Checks user exists before sending SMS
4. **Code Expiration**: Session-based (expires with session)
5. **Password Hashing**: Uses Laravel's `Hash::make()`
6. **One-time Use**: Code cleared after verification

## Troubleshooting

### SMS Not Received

1. Check phone number format (must be 09XXXXXXXXX)
2. Verify API token is correct
3. Check API credits at IPROGTECH dashboard
4. Run `test_direct_sms.php` to test directly

### "User not found" Error

-   Phone number must match exactly what's in database
-   Check `users` table `contact_number` column

### Code Verification Fails

-   Ensure you're entering the exact 6-digit code
-   Don't refresh the page (session might be lost)
-   Request a new code if needed

### Cannot Access Reset Password

-   Must complete verify-code step first
-   Session must have both `contact_number` and `code_verified`

## Files Modified

1. ✅ `app/Http/Controllers/ForgotPasswordController.php` - Complete rewrite
2. ✅ `resources/views/auth/forgot-password.blade.php` - Simplified validation
3. ✅ `resources/views/auth/verify-code.blade.php` - Removed debug code
4. ✅ `routes/web.php` - Already correct (no changes needed)

## Files Created

1. `test_direct_sms.php` - Standalone SMS test script
2. `DIRECT_SMS_IMPLEMENTATION.md` - This documentation

## What Was Removed

-   ❌ `PasswordResetService` dependency
-   ❌ `OtpService` usage
-   ❌ `SendRecoveryCodeNotification` usage
-   ❌ `password_reset_tokens` table usage
-   ❌ JSON API requests
-   ❌ Complex validation classes (now using direct Request validation)

## API Details

**Endpoint:** `https://sms.iprogtech.com/api/v1/sms_messages`
**Method:** POST
**Content-Type:** `application/x-www-form-urlencoded`
**Parameters:**

-   `api_token`: Your IPROGTECH API token
-   `message`: The SMS text to send
-   `phone_number`: Phone number in 63XXXXXXXXX format

**Success Response:**

```
"successfully queued for delivery"
```

## Next Steps

1. ✅ Test the complete flow in your browser
2. ✅ Verify SMS delivery to your phone
3. ✅ Test password reset works end-to-end
4. ✅ Monitor for any edge cases

## Support

If issues persist:

1. Check error messages in browser
2. Verify phone number format
3. Confirm user exists in database
4. Test with `test_direct_sms.php`
5. Check IPROGTECH API status/credits
