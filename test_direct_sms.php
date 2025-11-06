<?php

/**
 * Direct SMS Test - Mimics the exact approach from working SMSController
 */

echo "=== Testing Direct SMS Approach ===\n\n";

$url = 'https://sms.iprogtech.com/api/v1/sms_messages';
$api_token = '91d56803aa4a36ef3e7b3b350297ce3b35dee465';

// CHANGE THIS TO YOUR PHONE NUMBER
$phone_number = '09171234567'; // ← CHANGE THIS!
echo "Target phone number: {$phone_number}\n";

$formatted_number = preg_replace('/^0/', '63', $phone_number);
echo "Formatted number: {$formatted_number}\n";

$code = rand(100000, 999999);
echo "Generated code: {$code}\n";

$message = "Your Human Milk Bank recovery code is: $code. Do not share this code with anyone.";
echo "Message: {$message}\n\n";

$data = [
    'api_token' => $api_token,
    'message' => $message,
    'phone_number' => $formatted_number
];

echo "Sending SMS...\n";

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/x-www-form-urlencoded'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Code: {$httpCode}\n";
echo "Response: {$response}\n\n";

if (stripos($response, 'successfully queued for delivery') !== false) {
    echo "✅ SUCCESS! SMS should be delivered shortly.\n";
    echo "Check your phone for the code: {$code}\n";
} else {
    echo "❌ FAILED! SMS was not queued.\n";
    echo "Check API credentials and phone number format.\n";
}

echo "\n=== Test Complete ===\n";
