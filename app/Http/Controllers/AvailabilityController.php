<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAvailabilityRequest;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AvailabilityController extends Controller
{
    protected AvailabilityService $service;

    public function __construct(AvailabilityService $service)
    {
        $this->service = $service;
    }

    public function store(StoreAvailabilityRequest $request)
    {
        $data = $request->validated();

        try {
            $result = $this->service->createSlots($data['date'], $data['time']);

            $message = 'Availability saved successfully!';
            if (!empty($result['savedSlots'])) {
                $message .= ' Slots added: ' . implode(', ', $result['savedSlots']) . '.';
            }
            if (!empty($result['duplicateSlots'])) {
                $message .= ' Note: Some slots already existed: ' . implode(', ', $result['duplicateSlots']) . '.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'savedSlots' => $result['savedSlots'],
                'duplicateSlots' => $result['duplicateSlots'],
            ]);
        } catch (\Exception $e) {
            Log::error('Availability store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while saving availability. Please try again.'
            ], 500);
        }
    }

    // For future appointment system - get available slots for a date
    public function getAvailableSlots(Request $request)
    {
        $date = $request->get('date');
        if (!$date) {
            return response()->json(['error' => 'Date required'], 400);
        }

        $slots = $this->service->getAvailableSlotsForDate($date);

        return response()->json([
            'date' => $date,
            'available_slots' => $slots,
        ]);
    }

    // For future use - remove availability slot
    public function destroy($id)
    {
        try {
            $this->service->deleteSlot((int) $id);
            return redirect()->back()->with('success', 'Time slot removed successfully!');
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Availability delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to remove time slot');
        }
    }
}
