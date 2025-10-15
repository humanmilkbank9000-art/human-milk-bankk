<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Checking Database for User ===\n\n";

$phone = '09353991258';

$user = \App\Models\User::where('contact_number', $phone)->first();

if ($user) {
    echo "✓ User found!\n";
    echo "Name: " . $user->first_name . " " . $user->last_name . "\n";
    echo "Contact: " . $user->contact_number . "\n";
    echo "User ID: " . $user->user_id . "\n\n";
    
    echo "=== Testing Password Reset Flow ===\n";
    
    try {
        $service = new \App\Services\PasswordResetService();
        $code = $service->generateAndSendCode($phone);
        
        echo "✓ Recovery code generated and sent!\n";
        echo "Code: $code (for testing purposes)\n";
        echo "Check your phone for the SMS!\n";
        
    } catch (\Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n";
    }
    
} else {
    echo "✗ No user found with contact number: $phone\n\n";
    echo "Available users:\n";
    
    $users = \App\Models\User::take(5)->get();
    foreach ($users as $u) {
        echo "- " . $u->first_name . " " . $u->last_name . " (" . $u->contact_number . ")\n";
    }
}
