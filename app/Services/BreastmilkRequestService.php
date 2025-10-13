<?php

namespace App\Services;

use App\Models\BreastmilkRequest;
use App\Models\Availability;
use App\Models\Donation;
use App\Models\PasteurizationBatch;
use App\Models\DispensedMilk;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BreastmilkRequestService
{
    public function createRequest(array $data, int $userId)
    {
        DB::beginTransaction();
        try {
            $availability = Availability::where('id', $data['availability_id'])->lockForUpdate()->first();
            if (!$availability) {
                DB::rollBack();
                throw new \RuntimeException('The selected appointment slot was not found.');
            }
            if ($availability->status !== 'available') {
                DB::rollBack();
                throw new \RuntimeException('The selected appointment slot is no longer available.');
            }

            // Check existing pending request
            $existing = BreastmilkRequest::where('user_id', $userId)
                ->where('infant_id', $data['infant_id'])
                ->where('status', 'pending')
                ->first();

            if ($existing) {
                DB::rollBack();
                throw new \RuntimeException('You already have a pending breastmilk request for this infant.');
            }

            $request = new BreastmilkRequest([
                'user_id' => $userId,
                'infant_id' => $data['infant_id'],
                'availability_id' => $data['availability_id'],
                'request_date' => $availability->available_date,
                'request_time' => $availability->start_time,
                'status' => 'pending'
            ]);

            $request->save();

            if (!empty($data['prescription']) && $data['prescription']->isValid()) {
                $request->storePrescriptionFile($data['prescription']);
            }

            $availability->markAsBooked();

            DB::commit();

            // Notify admins
            $this->notifyAdmins('New Breastmilk Request', 'A new breastmilk request has been submitted.');

            return ['request' => $request, 'availability' => $availability];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating breastmilk request: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function notifyAdmins(string $title, string $message)
    {
        $admins = Admin::all();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\SystemAlert($title, $message));
        }
    }

    public function approveAndDispense(BreastmilkRequest $breastmilkRequest, array $payload, int $adminId)
    {
        // This method will handle approval flow similar to previous controller code.
        DB::beginTransaction();
        try {
            $volumeRequested = $payload['volume_requested'];
            $milkType = $payload['milk_type'];
            $selectedItems = $payload['selected_items'];

            $totalSelectedVolume = collect($selectedItems)->sum('volume');
            if ($totalSelectedVolume < $volumeRequested) {
                throw new \RuntimeException('Selected volume is insufficient for requested volume.');
            }

            // validate availability of each source
            foreach ($selectedItems as $item) {
                if ($milkType === 'unpasteurized') {
                    $donation = Donation::findOrFail($item['id']);
                    if ($item['volume'] > $donation->available_volume) {
                        throw new \RuntimeException('Donation #' . $donation->breastmilk_donation_id . ' does not have sufficient volume.');
                    }
                } else {
                    $batch = PasteurizationBatch::findOrFail($item['id']);
                    if ($item['volume'] > $batch->available_volume) {
                        throw new \RuntimeException('Batch ' . $batch->batch_number . ' does not have sufficient volume.');
                    }
                }
            }

            $dispensedMilk = DispensedMilk::create([
                'breastmilk_request_id' => $breastmilkRequest->breastmilk_request_id,
                'guardian_user_id' => $breastmilkRequest->user_id,
                'recipient_infant_id' => $breastmilkRequest->infant_id,
                'volume_dispensed' => $volumeRequested,
                'date_dispensed' => now()->toDateString(),
                'time_dispensed' => now()->toTimeString(),
                'admin_id' => $adminId,
                'dispensing_notes' => $payload['admin_notes'] ?? null
            ]);

            // Deduct inventory proportionally
            if ($milkType === 'unpasteurized') {
                $this->deductSelectedUnpasteurizedInventory($selectedItems, $dispensedMilk->dispensed_id, $volumeRequested);
            } else {
                $this->deductSelectedPasteurizedInventory($selectedItems, $dispensedMilk->dispensed_id, $volumeRequested);
            }

            $breastmilkRequest->update([
                'status' => 'dispensed',
                'admin_id' => $adminId,
                'volume_requested' => $volumeRequested,
                'volume_dispensed' => $volumeRequested,
                'admin_notes' => $payload['admin_notes'] ?? null,
                'approved_at' => now(),
                'dispensed_at' => now(),
                'dispensed_milk_id' => $dispensedMilk->dispensed_id
            ]);

            DB::commit();

            // Notify user
            $user = \App\Models\User::find($breastmilkRequest->user_id);
            if ($user) {
                $user->notify(new \App\Notifications\SystemAlert('Request Approved', 'Your request has been approved and dispensed.'));
            }

            return $dispensedMilk;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving/dispensing request: ' . $e->getMessage());
            throw $e;
        }
    }

    public function decline(BreastmilkRequest $request, string $notes, int $adminId)
    {
        $request->update([
            'status' => 'declined',
            'admin_id' => $adminId,
            'admin_notes' => $notes,
            'declined_at' => now()
        ]);

        $user = \App\Models\User::find($request->user_id);
        if ($user) {
            $user->notify(new \App\Notifications\SystemAlert('Request Declined', 'Your breastmilk request #' . $request->breastmilk_request_id . ' has been declined.')); 
        }
    }

    public function dispense(BreastmilkRequest $breastmilkRequest, array $payload, int $adminId)
    {
        // Similar to approveAndDispense but supports flexible sources
        DB::beginTransaction();
        try {
            $volume = $payload['volume_dispensed'];
            $sources = $payload['sources'];

            $dispensedMilk = DispensedMilk::create([
                'breastmilk_request_id' => $breastmilkRequest->breastmilk_request_id,
                'guardian_user_id' => $breastmilkRequest->user_id,
                'recipient_infant_id' => $breastmilkRequest->infant_id,
                'volume_dispensed' => $volume,
                'date_dispensed' => now()->toDateString(),
                'time_dispensed' => now()->toTimeString(),
                'admin_id' => $adminId,
                'dispensing_notes' => $payload['dispensing_notes'] ?? null
            ]);

            foreach ($sources as $source) {
                if ($source['type'] === 'pasteurized') {
                    $batch = PasteurizationBatch::findOrFail($source['id']);
                    if ($batch->available_volume < $source['volume']) {
                        throw new \RuntimeException("Batch #{$batch->batch_number} does not have sufficient volume");
                    }
                    $batch->available_volume -= $source['volume'];
                    $batch->save();
                    DB::table('dispensed_milk_sources')->insert([
                        'dispensed_id' => $dispensedMilk->dispensed_id,
                        'source_type' => 'pasteurized',
                        'source_id' => $batch->batch_id,
                        'volume_used' => $source['volume'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                } else {
                    $donation = Donation::findOrFail($source['id']);
                    if ($donation->available_volume < $source['volume']) {
                        throw new \RuntimeException("Donation #{$donation->breastmilk_donation_id} does not have sufficient volume");
                    }
                    $donation->reduceVolume($source['volume']);
                    DB::table('dispensed_milk_sources')->insert([
                        'dispensed_id' => $dispensedMilk->dispensed_id,
                        'source_type' => 'unpasteurized',
                        'source_id' => $donation->breastmilk_donation_id,
                        'volume_used' => $source['volume'],
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }

            $breastmilkRequest->update([
                'status' => 'dispensed',
                'admin_id' => $adminId,
                'volume_requested' => $volume,
                'volume_dispensed' => $volume,
                'dispensing_notes' => $payload['dispensing_notes'] ?? null,
                'approved_at' => now(),
                'dispensed_at' => now(),
                'dispensed_milk_id' => $dispensedMilk->dispensed_id
            ]);

            DB::commit();

            $user = \App\Models\User::find($breastmilkRequest->user_id);
            if ($user) {
                $user->notify(new \App\Notifications\SystemAlert('Request Approved & Dispensed', 'Your request has been approved and dispensed.'));
            }

            return $dispensedMilk;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Dispense error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Deduct selected unpasteurized donations (kept private to Service)
     */
    private function deductSelectedUnpasteurizedInventory($selectedItems, $dispensedId, $volumeRequested)
    {
        $totalSelectedVolume = collect($selectedItems)->sum('volume');
        $remainingToDeduct = $volumeRequested;
        
        foreach ($selectedItems as $item) {
            if ($remainingToDeduct <= 0) break;
            
            $donation = Donation::findOrFail($item['id']);
            
            $proportionalAmount = ($item['volume'] / $totalSelectedVolume) * $volumeRequested;
            $volumeToTake = min($proportionalAmount, $remainingToDeduct, $donation->available_volume);
            
            if ($volumeToTake <= 0) continue;

            if (!$donation->isInInventory() || $donation->available_volume < $volumeToTake) {
                throw new \RuntimeException("Donation #{$donation->breastmilk_donation_id} does not have sufficient available volume.");
            }

            if (!$donation->reduceVolume($volumeToTake)) {
                throw new \RuntimeException("Failed to reduce volume for donation #{$donation->breastmilk_donation_id}");
            }

            $donation->dispensedMilk()->attach($dispensedId, [
                'source_type' => 'unpasteurized',
                'volume_used' => $volumeToTake
            ]);
            
            $remainingToDeduct -= $volumeToTake;
        }
    }

    private function deductSelectedPasteurizedInventory($selectedItems, $dispensedId, $volumeRequested)
    {
        $totalSelectedVolume = collect($selectedItems)->sum('volume');
        $remainingToDeduct = $volumeRequested;
        
        foreach ($selectedItems as $item) {
            if ($remainingToDeduct <= 0) break;
            
            $batch = PasteurizationBatch::findOrFail($item['id']);
            
            $proportionalAmount = ($item['volume'] / $totalSelectedVolume) * $volumeRequested;
            $volumeToTake = min($proportionalAmount, $remainingToDeduct, $batch->available_volume);
            
            if ($volumeToTake <= 0) continue;

            if ($batch->status !== 'active' || $batch->available_volume < $volumeToTake) {
                throw new \RuntimeException("Batch {$batch->batch_number} does not have sufficient volume available.");
            }

            if (!$batch->reduceVolume($volumeToTake)) {
                throw new \RuntimeException("Failed to reduce volume for batch {$batch->batch_number}");
            }

            $batch->dispensedMilk()->attach($dispensedId, [
                'source_type' => 'pasteurized',
                'volume_used' => $volumeToTake
            ]);
            
            $remainingToDeduct -= $volumeToTake;
        }
    }
}
