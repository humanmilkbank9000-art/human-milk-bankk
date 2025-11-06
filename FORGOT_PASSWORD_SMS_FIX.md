# Forgot Password SMS Fix - Implementation Summary

## Problem

The forgot password feature was deducting credits from the IPROGTECH API but SMS messages were not being delivered to phones. The working standalone SMS test was successfully delivering messages.

## Root Cause

The difference between the working code and the system was in how the API request was formatted:

1. **Working Code**: Used `application/x-www-form-urlencoded` with `http_build_query()`
2. **System Code**: Used `application/json` with `json_encode()`

The IPROGTECH API appears to require form-encoded data rather than JSON for proper SMS delivery.

## Changes Made

### 1. Updated OtpService.php (`app/Services/OtpService.php`)

#### Changed Content-Type and Payload Format

```php
// BEFORE:
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

// AFTER:
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);
```

#### Updated Phone Number Normalization

Changed from keeping local format (09XXXXXXXXX) to international format (63XXXXXXXXX):

```php
protected function normalizeLocalNumber(string $phone): string
{
    // Remove non-digits
    $digits = preg_replace('/\D+/', '', $phone);

    // Convert 09XXXXXXXXX to 63XXXXXXXXX format
    if (str_starts_with($digits, '0') && strlen($digits) === 11) {
        return '63' . substr($digits, 1);
    }

    // If already in 63XXXXXXXXX format, keep it
    if (str_starts_with($digits, '63') && strlen($digits) === 12) {
        return $digits;
    }

    // If it's 10 digits without leading 0, add 63
    if (strlen($digits) === 10) {
        return '63' . $digits;
    }

    return $digits;
}
```

#### Enhanced Response Parsing

Added support for text-based success messages (like "successfully queued for delivery"):

```php
// Check if response contains success indicators
$rawResponse = is_string($raw) ? $raw : '';
$isSuccessText = stripos($rawResponse, 'successfully queued') !== false;

// Try to decode as JSON
$resp = json_decode($rawResponse, true) ?: [];
$success = ($httpCode >= 200 && $httpCode < 300) &&
           ((($resp['status'] ?? '') === 'success') || $isSuccessText);
```

## Testing

### Test Script

A test script has been created at `test_forgot_password_sms.php` to verify the functionality:

```bash
php test_forgot_password_sms.php
```

**Before running the test:**

1. Edit the script and change `$testPhoneNumber` to your actual phone number
2. Ensure your `.env` has the correct configuration:
    ```
    SMS_DRIVER=iprogtech_otp
    IPROGTECH_API_TOKEN=91d56803aa4a36ef3e7b3b350297ce3b35dee465
    ```

### Manual Testing via Web Interface

1. Go to your forgot password page: `http://your-domain/forgot-password`
2. Enter a valid registered phone number
3. Click "Send Recovery Code"
4. You should receive an SMS with a 6-digit code
5. Enter the code on the verification page
6. Reset your password

## Configuration

The system is already configured correctly in `.env`:

```env
SMS_DRIVER=iprogtech_otp
IPROGTECH_API_TOKEN=91d56803aa4a36ef3e7b3b350297ce3b35dee465
```

## Key Differences from Working Example

The working standalone SMS example (`SMSController.php`) demonstrated these patterns that were adopted:

1. **Form-encoded POST data** instead of JSON
2. **Phone number format conversion** (09... → 63...)
3. **Text-based success detection** in addition to JSON parsing
4. **Direct cURL initialization** with `curl_init($url)`

## Files Modified

1. `app/Services/OtpService.php`
    - Updated `postJson()` method to use form-encoded data
    - Modified `normalizeLocalNumber()` to convert to 63 format
    - Enhanced response parsing for text-based success messages
    - Updated `sendOtp()` to check for "successfully queued" message

## Next Steps

1. Run the test script with your actual phone number
2. If successful, test through the web interface
3. Monitor the logs at `storage/logs/laravel.log` for detailed API responses
4. If issues persist, check:
    - API token validity
    - Phone number format
    - API endpoint availability
    - Raw API response in logs

## Rollback (if needed)

If you need to rollback these changes, you can restore the original OtpService.php from git:

```bash
git checkout HEAD -- app/Services/OtpService.php
```

## Support

If you encounter any issues:

1. Check `storage/logs/laravel.log` for detailed error messages
2. Verify the raw API response in the logs
3. Ensure the phone number is in correct format
4. Confirm API credits are available
