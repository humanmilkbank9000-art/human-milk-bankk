<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Full SMS System ===\n\n";

// Check config
echo "SMS Driver: " . config('sms.driver') . "\n";
echo "Qproxy Token: " . config('sms.qproxy.token') . "\n";
echo "Qproxy URL: " . config('sms.qproxy.url') . "\n\n";

// Test with direct phone number
$testPhone = '09353991258';

echo "=== Testing with phone: $testPhone ===\n\n";

// Test SMS Service directly
echo "1. Testing SmsService directly...\n";
$smsService = app(\App\Services\SmsService::class);
$result = $smsService->send('+639353991258', 'Direct test: Your recovery code is 999888');
echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

// Test via notification
echo "2. Testing via Notification Channel...\n";
try {
    // Create a test user object
    $testUser = new stdClass();
    $testUser->contact_number = $testPhone;
    $testUser->first_name = 'Test';
    $testUser->user_id = 999;
    
    // Create notification
    $notification = new \App\Notifications\SendRecoveryCodeNotification('123456', 10);
    
    // Get the channel
    $channel = new \App\Notifications\Channels\QproxySmsChannel($smsService);
    
    echo "Sending via channel...\n";
    $channel->send($testUser, $notification);
    
    echo "✓ Notification sent successfully!\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Check storage/logs/laravel.log for detailed logs ===\n";
