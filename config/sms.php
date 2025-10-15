<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "log", "infobip", "qproxy"
    | 
    | - log: Logs SMS to laravel.log (for development/testing)
    | - infobip: Sends real SMS via Infobip API
    | - qproxy: Sends real SMS via Qproxy API
    |
    */

    'driver' => env('SMS_DRIVER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | SMS Log Channel
    |--------------------------------------------------------------------------
    |
    | When using 'log' driver, specify which log channel to use.
    | Leave null to use default channel.
    |
    */

    'log_channel' => env('SMS_LOG_CHANNEL'),

    /*
    |--------------------------------------------------------------------------
    | Infobip Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Infobip SMS driver.
    | These values are pulled from config/services.php
    |
    */

    'infobip' => [
        'api_key' => config('services.infobip.api_key'),
        'base_url' => config('services.infobip.base_url'),
        'sender' => config('services.infobip.sender'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Qproxy Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Qproxy SMS driver.
    |
    */

    'qproxy' => [
        'token' => env('QPROXY_SMS_TOKEN'),
        'url' => env('QPROXY_SMS_URL', 'https://app.qproxy.xyz/api/sms/v1/send'),
    ],
];
