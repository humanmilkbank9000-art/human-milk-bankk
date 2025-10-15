<?php

echo "=== Testing Correct API URL ===\n\n";

$send_data = [];
$send_data['mobile'] = '+639353991258';
$send_data['message'] = 'Test from Human Milk Bank - Password Recovery Code: 123456';
$send_data['token'] = '8759da3d7302494a1e0d3d8f2e246b21';
$parameters = json_encode($send_data);

echo "Request Data:\n";
echo $parameters . "\n\n";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://sms.ckent.dev/api/sms/v1/send");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$headers = array(
    "Content-Type: application/json"
);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$get_sms_status = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_errno = curl_errno($ch);
$curl_error = curl_error($ch);

curl_close($ch);

echo "HTTP Code: $http_code\n";
echo "cURL Error Number: $curl_errno\n";
if ($curl_error) {
    echo "cURL Error: $curl_error\n";
}
echo "Response: $get_sms_status\n\n";

if ($curl_errno === 0 && $http_code === 200) {
    echo "✓ SUCCESS - SMS sent successfully!\n";
    $response = json_decode($get_sms_status, true);
    if ($response) {
        echo "Decoded Response:\n";
        print_r($response);
    }
} else {
    echo "✗ FAILED - See error details above\n";
}
