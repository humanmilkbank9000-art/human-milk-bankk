<?php

namespace App\Services;

use App\Models\Availability;
use App\Models\Donation;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AvailabilityService
{
    /**
     * Unified provider for available date strings (YYYY-MM-DD) used by both
     * admin and user calendars to ensure perfect parity.
     */
    public function listAvailableDates(): array
    {
        return Availability::available()
            ->future()
            ->orderBy('available_date')
            ->pluck('available_date')
            ->map(function ($date) {
                // Use toDateString() to get date without timezone shift
                // This ensures "2025-11-01" stays "2025-11-01" regardless of timezone
                if ($date instanceof \Carbon\Carbon) {
                    return $date->toDateString();
                }
                return $date;
            })
            ->values()
            ->all();
    }
    /**
     * Retrieve available slots for a date formatted for JSON response.
     */
    public function getAvailableSlotsForDate(string $date)
    {
        $slots = Availability::forDate($date)
            ->available()
            ->future()
            ->orderByTime()
            ->get();

        return $slots->map(function ($slot) {
            return [
                'id' => $slot->id,
                'formatted_date' => $slot->formatted_date,
            ];
        });
    }

    /**
     * Create a date-only availability entry.
     * Returns array with savedIds and duplicates.
     */
    public function createDateAvailability(string $date): array
    {
        $saved = [];
        $duplicate = [];
        $reopened = [];

        $existing = Availability::where('available_date', $date)->first();
        if ($existing) {
            // If a record exists but isn't currently available, reopen it
            if ($existing->status !== 'available') {
                $existing->status = 'available';
                $existing->save();
                $reopened[] = $date;
            } else {
                $duplicate[] = $date;
            }
        } else {
            $a = Availability::create([
                'available_date' => $date,
                'status' => 'available'
            ]);
            $saved[] = $a->id;
        }

        return [
            'saved' => $saved,
            'duplicates' => $duplicate,
            'reopened' => $reopened,
        ];
    }

    /**
     * Delete an availability date if not booked.
     * Throws exception if cannot delete.
     */
    public function deleteSlot(int $id): void
    {
        $availability = Availability::findOrFail($id);

        // Prevent deletion if any donations reference this availability (protect booked dates)
        $hasDonations = Donation::where('availability_id', $id)->exists();
        if ($hasDonations) {
            throw new \RuntimeException('Cannot delete an availability date that has existing donations');
        }

        $availability->delete();
    }
}
