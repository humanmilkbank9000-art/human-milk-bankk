<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Carbon\Carbon;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('breastmilk_request') || !Schema::hasTable('infant')) return;

        // Find orphaned requests (infant_id missing in infant table)
        $orphans = DB::table('breastmilk_request as r')
            ->leftJoin('infant as i', 'i.infant_id', '=', 'r.infant_id')
            ->whereNull('i.infant_id')
            ->select('r.breastmilk_request_id', 'r.user_id', 'r.infant_id')
            ->get();

        foreach ($orphans as $o) {
            // Attempt to find an existing infant for the user (unique after previous migration)
            $infant = DB::table('infant')->where('user_id', $o->user_id)->first();
            if (!$infant) {
                // Create placeholder infant record for this user
                $now = Carbon::now()->toDateString();
                $newId = DB::table('infant')->insertGetId([
                    'user_id' => $o->user_id,
                    'first_name' => 'Unknown',
                    'middle_name' => null,
                    'last_name' => 'Infant',
                    'suffix' => null,
                    'sex' => 'female', // default
                    'date_of_birth' => $now,
                    'age' => 0,
                    'birth_weight' => 3.00,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $infantId = $newId;
            } else {
                $infantId = $infant->infant_id;
            }

            // Reassign request to the valid infant
            DB::table('breastmilk_request')
                ->where('breastmilk_request_id', $o->breastmilk_request_id)
                ->update(['infant_id' => $infantId, 'updated_at' => Carbon::now()]);
        }
    }

    public function down(): void
    {
        // No rollback for data repair (would risk orphaning again)
    }
};
