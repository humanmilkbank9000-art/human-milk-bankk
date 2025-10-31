<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== FINAL VERIFICATION TEST ===\n\n";

echo "1. Database Raw Query:\n";
$rawDates = DB::table('admin_availability')
    ->where('status', 'available')
    ->where('available_date', '>=', date('Y-m-d'))
    ->orderBy('available_date')
    ->limit(5)
    ->get();

foreach ($rawDates as $row) {
    echo "   {$row->available_date}\n";
}

echo "\n2. Service Method Output:\n";
$service = app(\App\Services\AvailabilityService::class);
$serviceDates = $service->listAvailableDates();

foreach (array_slice($serviceDates, 0, 5) as $date) {
    echo "   {$date}\n";
}

echo "\n3. JSON Output (what frontend receives):\n";
echo "   " . json_encode(array_slice($serviceDates, 0, 5)) . "\n";

echo "\n4. First Date Analysis:\n";
$firstDate = $serviceDates[0] ?? null;
if ($firstDate) {
    echo "   First date: {$firstDate}\n";
    echo "   Type: " . gettype($firstDate) . "\n";
    echo "   Length: " . strlen($firstDate) . "\n";
    echo "   Equals '2025-11-01': " . ($firstDate === '2025-11-01' ? 'YES' : 'NO') . "\n";
    
    // Check for hidden characters
    $hex = bin2hex($firstDate);
    echo "   Hex: {$hex}\n";
}

echo "\n5. Check if November 1 will be highlighted:\n";
$testDate = '2025-11-01';
$isInArray = in_array($testDate, $serviceDates);
echo "   Is '2025-11-01' in availableDates array: " . ($isInArray ? 'YES ✓' : 'NO ✗') . "\n";

if (!$isInArray) {
    echo "\n   PROBLEM FOUND!\n";
    echo "   Checking similar dates in array:\n";
    foreach ($serviceDates as $d) {
        if (strpos($d, '2025-11') === 0) {
            echo "   - {$d}\n";
        }
    }
}

echo "\n=== END TEST ===\n";
