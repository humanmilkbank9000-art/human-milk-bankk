<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$date = $argv[1] ?? date('Y-m-d');
$service = app(\App\Services\AvailabilityService::class);
$result = $service->getAvailableSlotsForDate($date);

echo "Date: $date\n";
if (empty($result) || count($result) === 0) {
    echo "No slots available\n";
} else {
    foreach ($result as $slot) {
        echo json_encode($slot, JSON_PRETTY_PRINT) . "\n";
    }
}
