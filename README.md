<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Project Overview

This application powers the Cagayan de Oro City Human Milk Bank portal, providing donor and requester onboarding, inventory management, and monthly reporting. A custom SMS-based password recovery flow ensures users can regain access using their registered mobile numbers.

## Local Setup

```powershell
git clone <repo-url>
cd volume
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate
```

Use `php artisan serve` (and `npm run dev` for assets) to boot the application locally.

## Configuration

Add the following entries to your `.env` file. `APP_TIMEZONE` should align with your deployment region (Asia/Manila by default).

```
APP_TIMEZONE=Asia/Manila
```

SMS configuration will be set up using Twilio in future updates.

## Password Recovery via SMS

The SMS-based password recovery system is currently being migrated from Vonage to Twilio. When a user requests a reset:

1. `ForgotPasswordController@sendRecoveryCode` generates a 6-digit code and stores a hashed token in `password_reset_tokens`.
2. `SendRecoveryCodeNotification` will deliver the code through SMS (Twilio integration pending).
3. Users verify the code at `/verify-code` and, on success, reset their password at `/reset-password`.

## Testing

```powershell
php artisan test
```

No feature tests currently ship with the project; feel free to add coverage alongside new features.

---

Laravel is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
