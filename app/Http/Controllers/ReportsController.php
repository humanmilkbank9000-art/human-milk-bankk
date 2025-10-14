<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\BreastmilkRequest;
use App\Models\Donation;
use App\Models\PasteurizationBatch;
use App\Models\DispensedMilk;
use Carbon\Carbon;
use App\Services\ReportService;

class ReportsController extends Controller
{
    protected ReportService $service;

    public function __construct(ReportService $service)
    {
        $this->service = $service;
    }

    public function admin_inventory()
    {
        return view('admin.inventory');
    }

    public function admin_monthly_reports(Request $request)
    {
        $year = (int) $request->input('year', date('Y'));
        $month = $request->input('month', date('n'));
        if (!is_numeric($month)) {
            $month = date('n', strtotime($month));
        }
        $month = (int) $month;

        // Get the active tab
        $activeTab = $request->input('tab', 'request');

        // Delegate to service to compute data
        $requestData = $this->service->buildRequestData($year, $month);
        $donationData = $this->service->buildDonationData($year, $month);
        $inventoryData = $this->service->buildInventoryData($year, $month);

        // Build request stats - use data directly from service
        $requestStats = [
            'month' => Carbon::create()->month($month)->format('F'),
            'total' => $requestData['total'] ?? 0,
            // approved already includes dispensed in the service
            'approved' => $requestData['approved'] ?? 0,
            'declined' => $requestData['declined'] ?? 0,
            // expose dispensed volume in ml for display
            'dispensed_volume' => $requestData['dispensed_volume'] ?? 0,
        ];

        // Build donation stats from records
        $donationRecords = $donationData['records'] ?? collect();
        $donationStats = [
            'month' => Carbon::create()->month($month)->format('F'),
            'walk_in' => $donationRecords->where('donation_type', 'Walk-in')->count(),
            'walkin' => $donationRecords->where('donation_type', 'Walk-in')->count(), // Alternative key name
            'home_collection' => $donationRecords->where('donation_type', 'Home Collection')->count(),
            'total' => $donationRecords->count(),
            'total_volume' => $donationData['total_volume'] ?? 0,
        ];

        // Build inventory stats from sections
        $inventoryStats = [
            'month' => Carbon::create()->month($month)->format('F'),
            'unpasteurized' => $inventoryData['sections'][0]['total'] ?? 0,
            'pasteurized' => $inventoryData['sections'][1]['total'] ?? 0,
            'dispensed' => $inventoryData['sections'][2]['total'] ?? 0,
        ];

        // Generate list of years and months for dropdowns
        $years = range(date('Y'), date('Y') - 5);
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];

        return view('admin.monthly-reports', compact(
            'requestStats', 
            'donationStats', 
            'inventoryStats', 
            'year', 
            'month',
            'years',
            'months',
            'activeTab'
        ));
    }


}
