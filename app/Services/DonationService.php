<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\HealthScreening;
use App\Models\Availability;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DonationService
{
    public function createDonation(array $data, int $userId)
    {
        $healthScreening = HealthScreening::where('user_id', $userId)->latest()->first();
        if (!$healthScreening) {
            throw new \RuntimeException('Please complete your health screening first before donating.');
        }
        if ($healthScreening->status !== 'accepted') {
            $message = $healthScreening->status === 'pending'
                ? 'Your health screening is still pending approval. Please wait for admin approval before donating.'
                : 'Your health screening has been declined. You cannot donate at this time.';
            throw new \RuntimeException($message);
        }

        if ($data['donation_method'] === 'walk_in') {
            return $this->createWalkIn($data, $healthScreening, $userId);
        }

        return $this->createHomeCollection($data, $healthScreening, $userId);
    }

    protected function createWalkIn(array $data, $healthScreening, int $userId)
    {
        DB::beginTransaction();
        try {
            $availability = Availability::where('id', $data['availability_id'])->lockForUpdate()->first();
            if (!$availability) {
                DB::rollBack();
                throw new \RuntimeException('The selected time slot was not found.');
            }
            if ($availability->status !== 'available') {
                DB::rollBack();
                throw new \RuntimeException('The selected time slot is no longer available.');
            }
            // Do NOT mark the availability as booked so multiple users can select the same date.
            // Keeping status as 'available' allows multi-book per day while admin can still block dates when needed.

            Donation::create([
                'health_screening_id' => $healthScreening->health_screening_id,
                'admin_id' => 1,
                'user_id' => $userId,
                'donation_method' => 'walk_in',
                'status' => 'pending_walk_in',
                'availability_id' => $availability->id,
                'donation_date' => $availability->available_date,
                'donation_time' => $availability->start_time,
            ]);

            DB::commit();

            $this->notifyAdmins('New Donation (Walk-in)', 'A user scheduled a walk-in donation.');

            return $availability;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating walk-in donation: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function createHomeCollection(array $data, $healthScreening, int $userId)
    {
        // Prepare bag details from the form data
        $bagDetails = [];
        $bagTimes = $data['bag_time'] ?? [];
        $bagDates = $data['bag_date'] ?? [];
        $bagNumbers = $data['bag_number'] ?? [];
        $bagVolumes = $data['bag_volume'] ?? [];
        $bagStorage = $data['bag_storage'] ?? [];
        $bagTemps = $data['bag_temp'] ?? [];
        $bagMethods = $data['bag_method'] ?? [];

        $numberOfBags = count($bagVolumes);
        
        for ($i = 0; $i < $numberOfBags; $i++) {
            $bagDetails[] = [
                'bag_number' => $bagNumbers[$i] ?? ($i + 1),
                'time' => $bagTimes[$i] ?? null,
                'date' => $bagDates[$i] ?? null,
                'volume' => $bagVolumes[$i] ?? null,
                'storage_location' => $bagStorage[$i] ?? null,
                'temperature' => $bagTemps[$i] ?? null,
                'collection_method' => $bagMethods[$i] ?? null,
            ];
        }

        // Calculate total volume
        $totalVolume = array_sum($bagVolumes);

        $donation = new Donation();
        $donation->health_screening_id = $healthScreening->health_screening_id;
        $donation->admin_id = 1;
        $donation->user_id = $userId;
        $donation->donation_method = 'home_collection';
        $donation->status = 'pending_home_collection';
        
        // Home collection specific fields
        $donation->number_of_bags = $numberOfBags;
        $donation->total_volume = $totalVolume;
        $donation->available_volume = $totalVolume;
        $donation->first_expression_date = $data['first_expression_date'] ?? null;
        $donation->last_expression_date = $data['last_expression_date'] ?? null;
        $donation->latitude = $data['latitude'] ?? null;
        $donation->longitude = $data['longitude'] ?? null;
        $donation->bag_details = $bagDetails;
        
        $donation->save();

        $this->notifyAdmins('New Donation (Home Collection)', 'A user submitted a home collection donation request.');

        return $donation;
    }

    protected function notifyAdmins(string $title, string $message)
    {
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemAlert($title, $message));
        }
    }

    public function validateWalkIn(Donation $donation, array $data)
    {
        if ($donation->status !== 'pending_walk_in' || $donation->donation_method !== 'walk_in') {
            throw new \RuntimeException('Invalid donation status');
        }

        if (count($data['bag_volumes']) !== (int)$data['number_of_bags']) {
            throw new \RuntimeException('Number of bag volumes must match the number of bags');
        }

        $donation->setBagVolumes($data['bag_volumes']);
        $donation->status = 'success_walk_in';
        $donation->save();
        $donation->addToInventory();

        $user = \App\Models\User::find($donation->user_id);
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Donation Validated', 'Your walk-in donation has been validated and added to inventory.'));
        }

        return $donation;
    }

    public function schedulePickup(Donation $donation, array $data)
    {
        if ($donation->status !== 'pending_home_collection' || $donation->donation_method !== 'home_collection') {
            throw new \RuntimeException('Invalid donation status');
        }

        $donation->update([
            'scheduled_pickup_date' => $data['scheduled_pickup_date'],
            'scheduled_pickup_time' => $data['scheduled_pickup_time'],
            'status' => 'scheduled_home_collection'
        ]);

        $user = \App\Models\User::find($donation->user_id);
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Pickup Scheduled', 'Your home collection pickup has been scheduled.'));
        }

        return $donation;
    }

    /**
     * Reschedule an existing scheduled pickup (only allowed when currently scheduled)
     */
    public function reschedulePickup(Donation $donation, array $data)
    {
        if ($donation->status !== 'scheduled_home_collection' || $donation->donation_method !== 'home_collection') {
            throw new \RuntimeException('Donation must be scheduled to be rescheduled');
        }

        $donation->update([
            'scheduled_pickup_date' => $data['scheduled_pickup_date'],
            'scheduled_pickup_time' => $data['scheduled_pickup_time'],
        ]);

        $user = \App\Models\User::find($donation->user_id);
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Pickup Rescheduled', 'Your home collection pickup has been rescheduled.'));
        }

        return $donation;
    }

    public function validatePickup(Donation $donation, array $data)
    {
        if ($donation->status !== 'scheduled_home_collection' || $donation->donation_method !== 'home_collection') {
            throw new \RuntimeException('Invalid donation status');
        }

        if (count($data['bag_volumes']) !== (int)$data['number_of_bags']) {
            throw new \RuntimeException('Number of bag volumes must match the number of bags');
        }

        $donation->setBagVolumes($data['bag_volumes']);
        $donation->status = 'success_home_collection';
        $donation->save();
        $donation->addToInventory();

        $user = \App\Models\User::find($donation->user_id);
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Pickup Validated', 'Your home collection has been validated and added to inventory.'));
        }

        return $donation;
    }
}
