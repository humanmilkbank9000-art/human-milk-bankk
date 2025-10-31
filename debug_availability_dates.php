<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== RAW DATABASE QUERY ===\n";
$rawDates = DB::table('admin_availability')
    ->where('status', 'available')
    ->where('available_date', '>=', date('Y-m-d'))
    ->orderBy('available_date')
    ->pluck('available_date')
    ->toArray();

echo "Raw dates from DB (status=available, future):\n";
foreach ($rawDates as $date) {
    echo "  - $date\n";
}

echo "\n=== SERVICE METHOD ===\n";
$service = app(\App\Services\AvailabilityService::class);
$serviceDates = $service->listAvailableDates();

echo "Dates from AvailabilityService::listAvailableDates():\n";
foreach ($serviceDates as $date) {
    echo "  - $date\n";
}

echo "\n=== COMPARISON ===\n";
$rawSet = array_map('strval', $rawDates);
$serviceSet = array_map('strval', $serviceDates);

$onlyInRaw = array_diff($rawSet, $serviceSet);
$onlyInService = array_diff($serviceSet, $rawSet);

if (empty($onlyInRaw) && empty($onlyInService)) {
    echo "✓ Perfect match! Both lists are identical.\n";
} else {
    if (!empty($onlyInRaw)) {
        echo "MISMATCH: These dates are in DB but NOT in service:\n";
        foreach ($onlyInRaw as $d) echo "  - $d\n";
    }
    if (!empty($onlyInService)) {
        echo "MISMATCH: These dates are in service but NOT in DB:\n";
        foreach ($onlyInService as $d) echo "  - $d\n";
    }
}

echo "\n=== JSON OUTPUT (what frontend gets) ===\n";
echo json_encode($serviceDates, JSON_PRETTY_PRINT) . "\n";
