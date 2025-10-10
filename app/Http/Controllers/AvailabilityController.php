<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Availability;
use Carbon\Carbon;

class AvailabilityController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|array|min:1',
            'time.*' => 'required|date_format:H:i',
        ]);

        $date = $request->date;
        $savedSlots = [];
        $duplicateSlots = [];

        foreach ($request->time as $startTime) {
            $endTime = Carbon::createFromFormat('H:i', $startTime)->addHour()->format('H:i');
            
            // Check for existing slot to prevent duplicates
            $existing = Availability::where('available_date', $date)
                ->where('start_time', $startTime)
                ->first();
                
            if ($existing) {
                $duplicateSlots[] = $startTime . ' - ' . $endTime;
                continue;
            }

            // Create new availability slot
            Availability::create([
                'available_date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => 'available'
            ]);
            
            $savedSlots[] = $startTime . ' - ' . $endTime;
        }

        // Build success message
        $message = 'Availability saved successfully!';
        if (!empty($savedSlots)) {
            $message .= ' Slots added: ' . implode(', ', $savedSlots) . '.';
        }
        if (!empty($duplicateSlots)) {
            $message .= ' Note: Some slots already existed: ' . implode(', ', $duplicateSlots) . '.';
        }

        // Return JSON response for AJAX compatibility
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'savedSlots' => $savedSlots,
                'duplicateSlots' => $duplicateSlots
            ]);
        }

        // Fallback to redirect for non-AJAX requests
        return redirect()->back()->with('success', $message);
    }

    // For future appointment system - get available slots for a date
    public function getAvailableSlots(Request $request)
    {
        $date = $request->get('date');
        
        if (!$date) {
            return response()->json(['error' => 'Date required'], 400);
        }

        $slots = Availability::forDate($date)
            ->available()
            ->future()
            ->orderByTime()
            ->get();
        
        return response()->json([
            'date' => $date,
            'available_slots' => $slots->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'time_slot' => $slot->time_slot,
                    'formatted_time' => $slot->formatted_time
                ];
            })
        ]);
    }

    // For future use - remove availability slot
    public function destroy($id)
    {
        $availability = Availability::findOrFail($id);
        
        // Check if slot is booked before deleting
        if ($availability->status === 'booked') {
            return redirect()->back()->with('error', 'Cannot delete a booked time slot!');
        }
        
        $availability->delete();
        
        return redirect()->back()->with('success', 'Time slot removed successfully!');
    }
}
