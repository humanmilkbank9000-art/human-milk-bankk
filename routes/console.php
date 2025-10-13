<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule donation reminders to run daily at 9:00 AM
Schedule::command('donations:send-reminders')
    ->dailyAt('09:00')
    ->timezone('Asia/Manila')
    ->name('Send donation reminders')
    ->description('Send notification reminders to users one day before their scheduled donation');
