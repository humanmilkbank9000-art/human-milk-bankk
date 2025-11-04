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
                // No time component for walk-in availability; date-only scheduling
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

    // Consent captured client-side; no questionnaire fields persisted here
        
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

        // Stamp actual collection time/date at validation for walk-in donations
        // Keep existing donation_date if already set (usually the scheduled walk-in date)
        // Always set the collection time to now so inventory shows the true collection time
        try {
            $nowManila = now()->timezone('Asia/Manila');
            $donation->donation_time = $nowManila->format('H:i:s');
        } catch (\Exception $e) {
            // Fallback to app timezone if timezone conversion fails
            $donation->donation_time = now()->format('H:i:s');
        }

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

        // If admin provided corrected volumes during scheduling, apply them now
        if (!empty($data['bag_volumes']) && is_array($data['bag_volumes'])) {
            $vols = array_values(array_filter($data['bag_volumes'], function ($v) {
                return $v !== null && $v !== '' && is_numeric($v) && (float)$v > 0;
            }));

            if (count($vols) > 0) {
                // Update bag_details volumes if present; otherwise, construct minimal bag_details
                $bagDetails = $donation->bag_details ?? [];
                $newDetails = [];
                for ($i = 0; $i < count($vols); $i++) {
                    $existing = $bagDetails[$i] ?? [];

                    // Prefer admin-edited values from the request payload when present,
                    // otherwise fall back to existing bag details or sensible defaults.
                    $bagNumber = $data['bag_number'][$i] ?? $existing['bag_number'] ?? ($i + 1);
                    $bagTime = $data['bag_time'][$i] ?? $existing['time'] ?? null;
                    $bagDate = $data['bag_date'][$i] ?? $existing['date'] ?? null;
                    $bagVolume = (float)$vols[$i];
                    $bagStorage = $data['bag_storage'][$i] ?? $existing['storage_location'] ?? null;
                    $bagTemp = $data['bag_temp'][$i] ?? $existing['temperature'] ?? null;
                    $bagMethod = $data['bag_method'][$i] ?? $existing['collection_method'] ?? null;

                    $newDetails[$i] = [
                        'bag_number' => $bagNumber,
                        'time' => $bagTime,
                        'date' => $bagDate,
                        'volume' => $bagVolume,
                        'storage_location' => $bagStorage,
                        'temperature' => $bagTemp,
                        'collection_method' => $bagMethod,
                    ];
                }

                $donation->bag_details = $newDetails;
                $donation->number_of_bags = count($vols);

                // Recompute totals while still pending
                $total = array_sum($vols);
                $donation->total_volume = $total;
                // Keep available in sync pre-inventory
                $donation->available_volume = $total;

                $donation->save();
            }
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

        // If admin provided corrected volumes, update bag_details to reflect finalized volumes
        if (!empty($data['bag_volumes']) && is_array($data['bag_volumes'])) {
            $vols = array_values(array_filter($data['bag_volumes'], function ($v) {
                return $v !== null && $v !== '' && is_numeric($v) && (float)$v >= 0;
            }));

            if (count($vols) > 0) {
                $bagDetails = $donation->bag_details ?? [];
                $newDetails = [];
                for ($i = 0; $i < count($vols); $i++) {
                    $existing = $bagDetails[$i] ?? [];
                    $newDetails[$i] = [
                        'bag_number' => $existing['bag_number'] ?? ($i + 1),
                        'time' => $existing['time'] ?? null,
                        'date' => $existing['date'] ?? null,
                        'volume' => (float)$vols[$i],
                        'storage_location' => $existing['storage_location'] ?? null,
                        'temperature' => $existing['temperature'] ?? null,
                        'collection_method' => $existing['collection_method'] ?? null,
                    ];
                }

                $donation->bag_details = $newDetails;
                $donation->number_of_bags = count($vols);

                // Recompute totals so bag_details and individual_bag_volumes stay in sync
                $total = array_sum($vols);
                $donation->total_volume = $total;
                $donation->available_volume = $total;
            }
        }

        // Keep individual_bag_volumes in sync as well
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
