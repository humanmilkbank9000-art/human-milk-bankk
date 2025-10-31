<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Creating availability slots...\n";

for ($i = 1; $i <= 14; $i++) {
    $date = date('Y-m-d', strtotime('+' . $i . ' days'));
    
    \App\Models\Availability::create([
        'available_date' => $date,
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'status' => 'available'
    ]);
    
    echo "Created slot for $date\n";
}

echo "\nDone! Created 14 availability slots.\n";
