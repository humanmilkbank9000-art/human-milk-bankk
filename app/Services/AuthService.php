<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthService
{
    public function attemptLogin(string $phoneOrUsername, string $password)
    {
        $role = null;
        $account = null;

        $isNumeric = preg_match('/^[0-9]+$/', $phoneOrUsername);

        if ($isNumeric) {
            $account = DB::table('user')->where('contact_number', $phoneOrUsername)->first();
            if ($account) $role = 'user';
        }

        if (!$account) {
            $account = DB::table('admin')->where('username', $phoneOrUsername)->first();
            if ($account) $role = 'admin';
        }

        if (!$account) {
            return [
                'success' => false,
                'error' => 'This contact number/username is not registered in our system.',
                'error_type' => 'not_found'
            ];
        }

        if (Hash::check($password, $account->password)) {
            Session::put('account_id', $role === 'admin' ? $account->admin_id : $account->user_id);
            Session::put('account_name', $role === 'admin' ? $account->full_name : $account->first_name);
            Session::put('account_role', $role);

            return [ 'success' => true, 'role' => $role ];
        }

        return [
            'success' => false,
            'error' => 'Incorrect password. Please try again.',
            'error_type' => 'incorrect_password'
        ];
    }

    public function updateUserPassword(int $userId, string $currentPassword, string $newPassword)
    {
        $user = User::find($userId);
        if (!$user) throw new \RuntimeException('User not found.');
        if (!Hash::check($currentPassword, $user->password)) throw new \RuntimeException('Current password is incorrect.');
        $user->password = Hash::make($newPassword);
        $user->save();
        
        // Send SMS notification about password change
        try {
            $user->notify(new \App\Notifications\PasswordChangedNotification());
        } catch (\Throwable $e) {
            Log::warning('Failed to send password change SMS notification', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
        
        return $user;
    }

    public function updateAdminSettings(int $adminId, array $data)
    {
        $admin = DB::table('admin')->where('admin_id', $adminId)->first();
        if (!$admin) throw new \RuntimeException('Admin not found');

        if (!Hash::check($data['current_password'], $admin->password)) {
            throw new \RuntimeException('Current password is incorrect');
        }

        $update = [];
        
        // Only update username if provided
        if (!empty($data['username'])) {
            $update['username'] = $data['username'];
        }
        
        // Only update password if provided
        if (!empty($data['password'])) {
            $update['password'] = Hash::make($data['password']);
        }

        // Ensure we have something to update
        if (empty($update)) {
            throw new \RuntimeException('No changes to update');
        }

        DB::table('admin')->where('admin_id', $adminId)->update($update);
        return DB::table('admin')->where('admin_id', $adminId)->first();
    }

    public function checkUsernameExists(string $username): bool
    {
        if (empty($username)) return false;
        return DB::table('admin')->where('username', $username)->exists();
    }

    public function logout()
    {
        Session::forget(['account_id', 'account_name', 'account_role']);
    }

    public function gatherAdminDashboardStats(int $year)
    {
        $availableDates = app(\App\Services\AvailabilityService::class)->listAvailableDates();

        $totalDonations = \App\Models\Donation::whereIn('status', ['success_walk_in', 'success_home_collection'])->count();
        $approvedRequests = \App\Models\BreastmilkRequest::whereIn('status', ['approved', 'dispensed'])->count();
        $totalHealthScreenings = \App\Models\HealthScreening::count();

        $walkInDonations = \App\Models\Donation::where('donation_method', 'walk_in')->where('status', 'success_walk_in')->count();
        $homeCollectionDonations = \App\Models\Donation::where('donation_method', 'home_collection')->where('status', 'success_home_collection')->count();

        $pendingScreenings = \App\Models\HealthScreening::where('status', 'pending')->count();
        $acceptedScreenings = \App\Models\HealthScreening::where('status', 'accepted')->count();
        $declinedScreenings = \App\Models\HealthScreening::where('status', 'declined')->count();

        $monthlyDonations = [];
        $monthlyRequests = [];
        for ($month = 1; $month <= 12; $month++) {
            $donationsCount = \App\Models\Donation::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereIn('status', ['success_walk_in', 'success_home_collection'])
                ->count();
            $monthlyDonations[] = $donationsCount;

            $requestsCount = \App\Models\BreastmilkRequest::whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->whereIn('status', ['approved', 'dispensed'])
                ->count();
            $monthlyRequests[] = $requestsCount;
        }

        return compact(
            'availableDates', 'totalDonations', 'approvedRequests', 'totalHealthScreenings',
            'monthlyDonations', 'monthlyRequests', 'walkInDonations', 'homeCollectionDonations',
            'pendingScreenings', 'acceptedScreenings', 'declinedScreenings'
        );
    }

    /**
     * Gather dashboard statistics for a specific user (donor)
     * Returns: total donations, total volume donated, and infants helped
     */
    public function gatherUserDashboardStats(int $userId)
    {
        // Total successful donations by this user
        $totalDonations = \App\Models\Donation::where('user_id', $userId)
            ->whereIn('status', ['success_walk_in', 'success_home_collection'])
            ->count();

        // Total volume donated (sum of all successful donations)
        $totalVolumeDonated = \App\Models\Donation::where('user_id', $userId)
            ->whereIn('status', ['success_walk_in', 'success_home_collection'])
            ->sum('total_volume');

        // Infants helped - get unique infants who received milk from this donor's donations
        // Count from unpasteurized donations (direct dispensing)
        $infantsHelpedUnpasteurized = DB::table('dispensed_milk')
            ->join('dispensed_milk_sources', 'dispensed_milk.dispensed_id', '=', 'dispensed_milk_sources.dispensed_id')
            ->join('breastmilk_donation', function($join) use ($userId) {
                $join->on('dispensed_milk_sources.source_id', '=', 'breastmilk_donation.breastmilk_donation_id')
                     ->where('dispensed_milk_sources.source_type', '=', 'unpasteurized')
                     ->where('breastmilk_donation.user_id', '=', $userId);
            })
            ->distinct()
            ->count('dispensed_milk.recipient_infant_id');

        // Check if pasteurization_batch table exists before querying
        $infantsHelpedPasteurized = 0;
        try {
            $tableExists = DB::select("SHOW TABLES LIKE 'pasteurization_batch'");
            
            if (!empty($tableExists)) {
                // Count from pasteurized batches (if table exists)
                $infantsHelpedPasteurized = DB::table('dispensed_milk')
                    ->join('dispensed_milk_sources', 'dispensed_milk.dispensed_id', '=', 'dispensed_milk_sources.dispensed_id')
                    ->join('pasteurization_batch', function($join) {
                        $join->on('dispensed_milk_sources.source_id', '=', 'pasteurization_batch.batch_id')
                             ->where('dispensed_milk_sources.source_type', '=', 'pasteurized');
                    })
                    ->join('breastmilk_donation', function($join) use ($userId) {
                        $join->on('pasteurization_batch.batch_id', '=', 'breastmilk_donation.pasteurization_batch_id')
                             ->where('breastmilk_donation.user_id', '=', $userId);
                    })
                    ->distinct()
                    ->count('dispensed_milk.recipient_infant_id');
            }
        } catch (\Exception $e) {
            // If table doesn't exist or query fails, default to 0
            Log::info("Pasteurization batch table not available: " . $e->getMessage());
        }

        // Get unique infants across both sources to avoid double counting
        // Use a simpler approach: collect unique infant IDs
        $uniqueInfantIds = collect();
        
        // Get infant IDs from unpasteurized
        $unpasteurizedInfants = DB::table('dispensed_milk')
            ->join('dispensed_milk_sources', 'dispensed_milk.dispensed_id', '=', 'dispensed_milk_sources.dispensed_id')
            ->join('breastmilk_donation', function($join) use ($userId) {
                $join->on('dispensed_milk_sources.source_id', '=', 'breastmilk_donation.breastmilk_donation_id')
                     ->where('dispensed_milk_sources.source_type', '=', 'unpasteurized')
                     ->where('breastmilk_donation.user_id', '=', $userId);
            })
            ->distinct()
            ->pluck('dispensed_milk.recipient_infant_id');
        
        $uniqueInfantIds = $uniqueInfantIds->merge($unpasteurizedInfants);

        // Get infant IDs from pasteurized (if table exists)
        if (!empty($tableExists)) {
            try {
                $pasteurizedInfants = DB::table('dispensed_milk')
                    ->join('dispensed_milk_sources', 'dispensed_milk.dispensed_id', '=', 'dispensed_milk_sources.dispensed_id')
                    ->join('pasteurization_batch', function($join) {
                        $join->on('dispensed_milk_sources.source_id', '=', 'pasteurization_batch.batch_id')
                             ->where('dispensed_milk_sources.source_type', '=', 'pasteurized');
                    })
                    ->join('breastmilk_donation', function($join) use ($userId) {
                        $join->on('pasteurization_batch.batch_id', '=', 'breastmilk_donation.pasteurization_batch_id')
                             ->where('breastmilk_donation.user_id', '=', $userId);
                    })
                    ->distinct()
                    ->pluck('dispensed_milk.recipient_infant_id');
                
                $uniqueInfantIds = $uniqueInfantIds->merge($pasteurizedInfants);
            } catch (\Exception $e) {
                Log::info("Error fetching pasteurized infants: " . $e->getMessage());
            }
        }

        // Count unique infants
        $infantsHelpedTotal = $uniqueInfantIds->unique()->count();

        return [
            'totalDonations' => $totalDonations,
            'totalVolumeDonated' => round($totalVolumeDonated, 2),
            'infantsHelped' => $infantsHelpedTotal,
        ];
    }
}
