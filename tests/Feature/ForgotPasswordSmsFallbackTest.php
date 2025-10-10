<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\SendRecoveryCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordSmsFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function testRecoveryCodeIsLoggedWhenSmsDriverIsLog(): void
    {
        Config::set('sms.driver', 'log');
        Config::set('sms.log_channel', null);
        Config::set('app.debug', true);

        Log::spy();
        Notification::fake();

        $user = User::factory()->create([
            'contact_number' => '09123456789',
        ]);

        $response = $this->post(route('password.forgot.send'), [
            'contact_number' => $user->contact_number,
        ]);

        $response->assertRedirect(route('password.verify'));
        $response->assertSessionHas('status', 'We sent a recovery code to your mobile number.');

        Notification::assertNothingSent();

        Log::shouldHaveReceived('info')->once()->withArgs(function (string $message, array $context) use ($user): bool {
            return $message === 'Account recovery code generated (SMS log driver).'
                && $context['contact_number'] === $user->contact_number
                && isset($context['code'])
                && $context['expires_in_minutes'] === 10;
        });

        $this->assertTrue(session()->has('password_reset.last_code'));
    }
}
