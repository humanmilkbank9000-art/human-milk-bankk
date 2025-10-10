<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\SendRecoveryCodeNotification;
use Illuminate\Console\Command;

class SendTestSms extends Command
{
    protected $signature = 'sms:test {contact_number}';
    protected $description = 'Send a test SMS to a specific contact number';

    public function handle(): int
    {
        $contactNumber = $this->argument('contact_number');
        
        $this->info('Searching for user with contact number: ' . $contactNumber);
        
        $user = User::where('contact_number', $contactNumber)->first();
        
        if (!$user) {
            $this->error('❌ No user found with contact number: ' . $contactNumber);
            $this->info('Available users:');
            User::all()->each(function($u) {
                $this->line('  - ' . $u->first_name . ' ' . $u->last_name . ': ' . $u->contact_number);
            });
            return 1;
        }
        
        $this->info('✓ Found user: ' . $user->first_name . ' ' . $user->last_name);
        $this->info('Contact number: ' . $user->contact_number);
        
        $testCode = '999888';
        $this->info('Sending test code: ' . $testCode);
        
        try {
            $user->notify(new SendRecoveryCodeNotification($testCode, 10));
            $this->info('✅ SMS sent successfully!');
            $this->info('Check your phone for code: ' . $testCode);
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Failed to send SMS');
            $this->error('Error: ' . $e->getMessage());
            
            if ($this->option('verbose')) {
                $this->error("\nStack trace:");
                $this->error($e->getTraceAsString());
            }
            
            return 1;
        }
    }
}
