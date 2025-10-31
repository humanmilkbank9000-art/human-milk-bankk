<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule donation reminders to run daily at 9:00 AM
Schedule::command('donations:send-reminders')
    ->dailyAt('09:00')
    ->timezone('Asia/Manila')
    ->name('Send donation reminders')
    ->description('Send notification reminders to users one day before their scheduled donation');

// Quick diagnostic command to test admin availability save flow end-to-end
Artisan::command('availability:test {date?}', function (?string $date = null) {
    $date = $date ?: now()->toDateString();
    $this->info("Testing availability save for date: {$date}");

    try {
        $service = app(\App\Services\AvailabilityService::class);
        $result = $service->createDateAvailability($date);
        $this->info('Result: ' . json_encode($result));
    } catch (\Throwable $e) {
        Log::error('availability:test error', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        $this->error('Exception: ' . $e->getMessage());
    }
})->purpose('Run a quick availability save test to validate DB and model setup');
