<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$svc = app(\App\Services\AvailabilityService::class);
$dates = $svc->listAvailableDates();

echo "Available dates (status=available, from today):\n";
echo implode(", ", $dates)."\n";