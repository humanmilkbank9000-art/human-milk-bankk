<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Driver
    |--------------------------------------------------------------------------
    |
    | Supported: "log", "infobip", "qproxy", "iprogtech_otp"
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

    /*
    |--------------------------------------------------------------------------
    | IPROGTECH OTP Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for IPROGTECH OTP driver. This driver delegates OTP
    | generation and verification to the external API and should be used
    | for the forgot-password flow.
    |
    */

    'iprogtech_otp' => [
        // e.g. 91d56803aa4a36ef3e7b3b350297ce3b35dee465
        'api_token' => env('IPROGTECH_API_TOKEN'),
        // Base API URL (without trailing slash). Default per docs
        'base_url' => env('IPROGTECH_API_BASE_URL', 'https://sms.iprogtech.com/api/v1'),
        // Optional custom message; include :otp placeholder to be replaced by backend
        'message' => env('IPROGTECH_OTP_MESSAGE', ''),
    ],
];
