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
            $result = $this->service->createDateAvailability($data['date']);

            $message = 'Availability saved successfully!';
            if (!empty($result['reopened'])) {
                $message = 'Existing date re-opened for bookings.';
            } elseif (!empty($result['duplicates'])) {
                $message = 'This date already has availability set.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'saved' => $result['saved'],
                'duplicates' => $result['duplicates'],
                'reopened' => $result['reopened'] ?? [],
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

    // Remove availability date
    public function destroy(Request $request, $id)
    {
        try {
            $this->service->deleteSlot((int) $id);

            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Availability date removed successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Availability date removed successfully!');
        } catch (\RuntimeException $e) {
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 409);
            }

            return redirect()->back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Availability delete error: ' . $e->getMessage());
            if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to remove availability date'
                ], 500);
            }

            return redirect()->back()->with('error', 'Unable to remove availability date');
        }
    }
}
