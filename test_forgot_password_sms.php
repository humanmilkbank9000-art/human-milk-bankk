<?php

/**
 * Test script for forgot password SMS functionality
 * This tests if the OTP service can successfully send SMS using the new approach
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\OtpService;
use Illuminate\Support\Facades\Log;

echo "=== Testing IPROGTECH OTP Service ===\n\n";

// Get configuration
$apiToken = config('sms.iprogtech_otp.api_token');
$driver = config('sms.driver');

echo "Current SMS Driver: {$driver}\n";
echo "API Token configured: " . (empty($apiToken) ? "NO" : "YES") . "\n\n";

if (empty($apiToken)) {
    echo "ERROR: IPROGTECH_API_TOKEN not configured in .env file\n";
    exit(1);
}

// Test phone number - CHANGE THIS TO YOUR ACTUAL PHONE NUMBER
$testPhoneNumber = '09171234567'; // Change this to your actual number
echo "Test phone number: {$testPhoneNumber}\n";
echo "NOTE: Change the phone number in this script to your actual number!\n\n";

// Create OTP service instance
$otpService = new OtpService();

echo "--- Test 1: Send OTP ---\n";
$result = $otpService->sendOtp($testPhoneNumber);

echo "Success: " . ($result['success'] ? 'YES' : 'NO') . "\n";
echo "Message: {$result['message']}\n";
echo "HTTP Code: " . ($result['http_code'] ?? 'N/A') . "\n";

if (isset($result['data'])) {
    echo "Data: " . json_encode($result['data'], JSON_PRETTY_PRINT) . "\n";
}

if ($result['success']) {
    echo "\n✅ OTP sent successfully!\n";
    echo "Check your phone for the OTP code.\n\n";
    
    // Prompt user to enter the OTP they received
    echo "Enter the OTP code you received (or press Enter to skip verification test): ";
    $handle = fopen("php://stdin", "r");
    $otpCode = trim(fgets($handle));
    fclose($handle);
    
    if (!empty($otpCode)) {
        echo "\n--- Test 2: Verify OTP ---\n";
        $verifyResult = $otpService->verifyOtp($testPhoneNumber, $otpCode);
        
        echo "Success: " . ($verifyResult['success'] ? 'YES' : 'NO') . "\n";
        echo "Message: {$verifyResult['message']}\n";
        echo "HTTP Code: " . ($verifyResult['http_code'] ?? 'N/A') . "\n";
        
        if ($verifyResult['success']) {
            echo "\n✅ OTP verified successfully!\n";
        } else {
            echo "\n❌ OTP verification failed.\n";
        }
    } else {
        echo "Skipping verification test.\n";
    }
} else {
    echo "\n❌ Failed to send OTP.\n";
    echo "Check the logs for more details.\n";
}

echo "\n=== Test Complete ===\n";

