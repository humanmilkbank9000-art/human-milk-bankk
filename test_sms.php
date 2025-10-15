<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Testing Qproxy SMS Configuration ===\n\n";

// Check config
echo "SMS Driver: " . config('sms.driver') . "\n";
echo "Qproxy Token: " . config('sms.qproxy.token') . "\n";
echo "Qproxy URL: " . config('sms.qproxy.url') . "\n\n";

// Test SMS Service directly
echo "=== Testing SmsService directly ===\n";
$smsService = app(\App\Services\SmsService::class);
$result = $smsService->send('+639353991258', 'Test message from Human Milk Bank system.');

echo "Result: " . json_encode($result, JSON_PRETTY_PRINT) . "\n\n";

// Test notification channel
echo "=== Testing via Notification ===\n";
try {
    $user = \App\Models\User::where('contact_number', '09353991258')->first();
    
    if ($user) {
        echo "User found: " . $user->first_name . "\n";
        echo "Contact: " . $user->contact_number . "\n\n";
        
        $notification = new \App\Notifications\SendRecoveryCodeNotification('123456', 10);
        $user->notify($notification);
        
        echo "Notification sent successfully!\n";
    } else {
        echo "User not found with contact number 09353991258\n";
        
        // Try finding any user
        $anyUser = \App\Models\User::first();
        if ($anyUser) {
            echo "Testing with user: " . $anyUser->contact_number . "\n\n";
            $notification = new \App\Notifications\SendRecoveryCodeNotification('123456', 10);
            $anyUser->notify($notification);
            echo "Notification sent successfully!\n";
        }
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Check storage/logs/laravel.log for detailed logs ===\n";
