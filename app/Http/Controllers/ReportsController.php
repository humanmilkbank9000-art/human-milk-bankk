<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\BreastmilkRequest;
use App\Models\Donation;
use App\Models\PasteurizationBatch;
use App\Models\DispensedMilk;
use Carbon\Carbon;

class ReportsController extends Controller
{
    
public function admin_inventory() {
        
    return view('admin.inventory');
}


    public function admin_monthly_reports(Request $request) {
        $year = $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));
        // Ensure $month is always an integer (not a string like 'October')
        if (!is_numeric($month)) {
            $month = date('n', strtotime($month));
        }
        $month = (int) $month;

        // Breastmilk Request Reports
        $requests = BreastmilkRequest::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->get();
        $requestStats = [
            'month' => Carbon::create()->month($month)->format('F'),
            'total' => $requests->count(),
            // Count both 'approved' and 'dispensed' as approved for reporting
            'approved' => $requests->whereIn('status', ['approved', 'dispensed'])->count(),
            'declined' => $requests->where('status', 'declined')->count(),
            'pending' => $requests->where('status', 'pending')->count(),
        ];

        // Breastmilk Donation Reports
        // Include donations that were completed either as walk-ins (donation_date)
        // or as home collections (scheduled_pickup_date). Some home collection
        // records may have donation_date null and use scheduled_pickup_date
        // for their delivery date, so check both fields.
        $donations = Donation::whereIn('status', ['success_walk_in', 'success_home_collection'])
            ->where(function($q) use ($year, $month) {
                $q->whereYear('donation_date', $year)
                  ->whereMonth('donation_date', $month);

                $q->orWhere(function($q2) use ($year, $month) {
                    $q2->whereYear('scheduled_pickup_date', $year)
                       ->whereMonth('scheduled_pickup_date', $month);
                });
            })
            ->get();
        $donationStats = [
            'month' => Carbon::create()->month($month)->format('F'),
            'walk_in' => $donations->where('status', 'success_walk_in')->where('donation_method', 'walk_in')->count(),
            'home_collection' => $donations->where('status', 'success_home_collection')->where('donation_method', 'home_collection')->count(),
            'total' => $donations->count(),
            'total_volume' => $donations->sum('total_volume'),
        ];

        // Inventory Reports - single month aggregates
        $unpasteurized = Donation::where('pasteurization_status', 'unpasteurized')
            ->where(function($q) use ($year, $month) {
                $q->whereYear('donation_date', $year)
                  ->whereMonth('donation_date', $month);

                $q->orWhere(function($q2) use ($year, $month) {
                    $q2->whereYear('scheduled_pickup_date', $year)
                       ->whereMonth('scheduled_pickup_date', $month);
                });
            })
            ->sum('available_volume');
        $pasteurized = PasteurizationBatch::whereYear('date_pasteurized', $year)
            ->whereMonth('date_pasteurized', $month)
            ->sum('available_volume');
        $dispensed = DispensedMilk::whereYear('date_dispensed', $year)
            ->whereMonth('date_dispensed', $month)
            ->sum('volume_dispensed');
        $inventoryStats = [
            'month' => Carbon::create()->month($month)->format('F'),
            'unpasteurized' => $unpasteurized,
            'pasteurized' => $pasteurized,
            'dispensed' => $dispensed,
        ];

        // Return only single-month aggregates to simplify the view and reduce DB load
        return view('admin.monthly-reports', compact('requestStats', 'donationStats', 'inventoryStats', 'year', 'month'));
    }


}
