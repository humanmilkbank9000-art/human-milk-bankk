<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Donation;
use App\Models\User;
use App\Notifications\SystemAlert;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendDonationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'donations:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification reminders to users one day before their scheduled donation';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for donations scheduled for tomorrow...');

        // Get tomorrow's date
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Find all donations scheduled for tomorrow
        $donations = Donation::where('donation_date', $tomorrow)
            ->whereIn('status', ['pending_walk_in', 'scheduled_home_collection'])
            ->with(['user', 'availability'])
            ->get();

        if ($donations->isEmpty()) {
            $this->info('No donations scheduled for tomorrow.');
            return Command::SUCCESS;
        }

        $remindersSent = 0;

        foreach ($donations as $donation) {
            if (!$donation->user) {
                $this->warn("Donation #{$donation->breastmilk_donation_id} has no associated user. Skipping...");
                continue;
            }

            try {
                // Format the donation details
                $donationMethod = ucfirst(str_replace('_', ' ', $donation->donation_method ?? 'donation'));
                $donationDate = Carbon::parse($donation->donation_date)->format('M d, Y');
                
                // Get time from availability or donation_time
                $donationTime = 'to be confirmed';
                if ($donation->availability) {
                    $donationTime = $donation->availability->formatted_time;
                } elseif ($donation->donation_time) {
                    $donationTime = Carbon::parse($donation->donation_time)->format('g:i A');
                } elseif ($donation->scheduled_pickup_time) {
                    $donationTime = Carbon::parse($donation->scheduled_pickup_time)->format('g:i A');
                }

                // Create notification message
                $title = 'Donation Reminder';
                $message = "Reminder: You have a {$donationMethod} scheduled for tomorrow, {$donationDate} at {$donationTime}. Please make sure you're prepared. Thank you for your generous donation!";

                // Send notification
                $donation->user->notify(new SystemAlert($title, $message, 'info', [
                    'donation_id' => $donation->breastmilk_donation_id,
                    'donation_date' => $donation->donation_date,
                    'reminder_type' => 'donation_reminder'
                ]));

                $remindersSent++;
                $this->info("Sent reminder to {$donation->user->first_name} {$donation->user->last_name} for donation #{$donation->breastmilk_donation_id}");

                // Log the reminder
                Log::info("Donation reminder sent", [
                    'donation_id' => $donation->breastmilk_donation_id,
                    'user_id' => $donation->user_id,
                    'donation_date' => $donation->donation_date,
                    'donation_method' => $donation->donation_method
                ]);

            } catch (\Exception $e) {
                $this->error("Failed to send reminder for donation #{$donation->breastmilk_donation_id}: {$e->getMessage()}");
                Log::error("Failed to send donation reminder", [
                    'donation_id' => $donation->breastmilk_donation_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Successfully sent {$remindersSent} reminder(s).");
        return Command::SUCCESS;
    }
}
