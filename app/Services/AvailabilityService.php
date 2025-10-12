<?php

namespace App\Services;

use App\Models\Availability;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AvailabilityService
{
    /**
     * Create availability slots for a given date and array of start times.
     * Returns array with savedSlots and duplicateSlots.
     *
     * @param string $date
     * @param array $times
     * @return array
     */
    public function createSlots(string $date, array $times): array
    {
        $savedSlots = [];
        $duplicateSlots = [];

        foreach ($times as $startTime) {
            $endTime = Carbon::createFromFormat('H:i', $startTime)->addHour()->format('H:i');

            $existing = Availability::where('available_date', $date)
                ->where('start_time', $startTime)
                ->first();

            if ($existing) {
                $duplicateSlots[] = $startTime . ' - ' . $endTime;
                continue;
            }

            Availability::create([
                'available_date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'available'
            ]);

            $savedSlots[] = $startTime . ' - ' . $endTime;
        }

        return [
            'savedSlots' => $savedSlots,
            'duplicateSlots' => $duplicateSlots,
        ];
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
                'start_time' => $slot->start_time,
                'end_time' => $slot->end_time,
                'time_slot' => $slot->time_slot,
                'formatted_time' => $slot->formatted_time,
            ];
        });
    }

    /**
     * Delete an availability slot if not booked.
     * Throws exception if cannot delete.
     */
    public function deleteSlot(int $id): void
    {
        $availability = Availability::findOrFail($id);

        if ($availability->status === 'booked') {
            throw new \RuntimeException('Cannot delete a booked time slot');
        }

        $availability->delete();
    }
}
