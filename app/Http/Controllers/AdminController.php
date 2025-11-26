<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HealthScreening;
use App\Models\Donation;
use App\Models\BreastmilkRequest;

class AdminController extends Controller
{
    /**
     * Get badge counts for admin sidebar
     * Returns JSON with pending counts for health screening, donations, and requests
     */
    public function getBadgeCounts()
    {
        $healthScreeningCount = HealthScreening::where('status', 'pending')->count();
        
        $donationCount = Donation::whereIn('status', [
            'pending', 
            'pending_walk_in', 
            'pending_home_collection'
        ])->count();
        
        $requestCount = BreastmilkRequest::where('status', 'pending')->count();

        return response()->json([
            'health_screening' => $healthScreeningCount,
            'donation' => $donationCount,
            'request' => $requestCount
        ]);
    }
}
