<?php

echo "=== Testing Exact Code You Provided ===\n\n";

$send_data = [];
$send_data['mobile'] = '+639758669139';
$send_data['message'] = 'Testing Message! XYZ';
$send_data['token'] = '79c86f1d1e497f5febc0ec9763f7e4b5';
$parameters = json_encode($send_data);

echo "Request Data:\n";
echo $parameters . "\n\n";

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://app.qproxy.xyz/api/sms/v1/send");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$headers = [];
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
echo "cURL Error: $curl_error\n";
echo "Response: $get_sms_status\n\n";

if ($curl_errno === 0 && $http_code === 200) {
    echo "✓ SUCCESS - SMS API is working!\n";
    $response = json_decode($get_sms_status, true);
    echo "Decoded Response:\n";
    print_r($response);
} else if ($curl_errno === 6) {
    echo "✗ FAILED - DNS Resolution Error\n";
    echo "The domain 'app.qproxy.xyz' cannot be resolved.\n";
    echo "Possible causes:\n";
    echo "1. Wrong domain name\n";
    echo "2. DNS server issue\n";
    echo "3. Network/firewall blocking the request\n";
    echo "\nPlease verify the correct API endpoint URL from Qproxy documentation.\n";
} else {
    echo "✗ FAILED - See error details above\n";
}
