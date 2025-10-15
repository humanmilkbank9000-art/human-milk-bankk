<?php

echo "=== Testing cURL and Network Connectivity ===\n\n";

// Check if cURL is enabled
if (!function_exists('curl_init')) {
    echo "ERROR: cURL is not enabled in PHP!\n";
    exit(1);
}

echo "✓ cURL is enabled\n\n";

// Test Google (to verify internet connectivity)
echo "Testing Google.com...\n";
$ch = curl_init('https://www.google.com');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
$errno = curl_errno($ch);
$error = curl_error($ch);
curl_close($ch);

if ($errno === 0) {
    echo "✓ Google test: Success (Internet is working)\n\n";
} else {
    echo "✗ Google test: Failed - $error\n\n";
}

// Test Qproxy API URL
echo "Testing Qproxy API URL...\n";
$ch = curl_init('https://app.qproxy.xyz/api/sms/v1/send');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    'mobile' => '+639353991258',
    'message' => 'Test',
    'token' => '79c86f1d1e497f5febc0ec9763f7e4b5'
]));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$errno = curl_errno($ch);
$error = curl_error($ch);

echo "HTTP Code: $http_code\n";
echo "cURL Error Number: $errno\n";
if ($errno) {
    echo "cURL Error: $error\n";
}
echo "Response: $result\n";

curl_close($ch);

echo "\n=== PHP Info ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "OpenSSL: " . (extension_loaded('openssl') ? 'Enabled' : 'Disabled') . "\n";
