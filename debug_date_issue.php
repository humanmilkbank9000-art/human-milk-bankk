<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TIMEZONE INFO ===\n";
echo "PHP timezone: " . date_default_timezone_get() . "\n";
echo "Current date/time: " . date('Y-m-d H:i:s') . "\n";
echo "Database timezone: " . DB::select("SELECT @@session.time_zone as tz")[0]->tz . "\n\n";

echo "=== AVAILABLE DATES FROM DB ===\n";
$dates = DB::table('admin_availability')
    ->where('status', 'available')
    ->where('available_date', '>=', date('Y-m-d'))
    ->orderBy('available_date')
    ->get();

foreach ($dates as $row) {
    echo "Date: {$row->available_date} (type: " . gettype($row->available_date) . ")\n";
}

echo "\n=== DATE PARSING TEST ===\n";
$testDate = '2025-11-01';
echo "Test date string: $testDate\n";

// Method 1: Direct new Date() in JS (what calendar might do)
echo "\nJavaScript simulation:\n";
echo "new Date('$testDate') would create: 2025-11-01T00:00:00 (local timezone)\n";

// Method 2: Parse components
$parts = explode('-', $testDate);
echo "\nParsed components:\n";
echo "Year: {$parts[0]}, Month: {$parts[1]}, Day: {$parts[2]}\n";

// Method 3: Carbon parse
$carbon = \Carbon\Carbon::parse($testDate);
echo "\nCarbon parse:\n";
echo "format('Y-m-d'): " . $carbon->format('Y-m-d') . "\n";
echo "toDateString(): " . $carbon->toDateString() . "\n";

echo "\n=== JAVASCRIPT DATE CONSTRUCTION TEST ===\n";
echo "If JS code does: new Date(startDate)\n";
echo "And then: date.setDate(startDate.getDate() + i)\n";
echo "This MUTATES the same Date object!\n";
echo "\nCorrect way: const date = new Date(startDate.getTime() + (i * 86400000));\n";
echo "Or: const date = new Date(startDate); date.setDate(date.getDate() + i);\n";
