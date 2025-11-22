<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Donation table
        if (!Schema::hasColumn('breastmilk_donation', 'assist_option')) {
            Schema::table('breastmilk_donation', function (Blueprint $table) {
                $table->enum('assist_option', [
                    'no_account_direct_record',
                    'record_to_existing_user',
                    'milk_letting_activity'
                ])->nullable()->after('status');
            });
        }
        // Request table
        if (!Schema::hasColumn('breastmilk_request', 'assist_option')) {
            Schema::table('breastmilk_request', function (Blueprint $table) {
                $table->enum('assist_option', [
                    'no_account_direct_record',
                    'record_to_existing_user',
                    'milk_letting_activity'
                ])->nullable()->after('assisted_by_admin');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('breastmilk_donation', 'assist_option')) {
            Schema::table('breastmilk_donation', function (Blueprint $table) {
                $table->dropColumn('assist_option');
            });
        }
        if (Schema::hasColumn('breastmilk_request', 'assist_option')) {
            Schema::table('breastmilk_request', function (Blueprint $table) {
                $table->dropColumn('assist_option');
            });
        }
    }
};
