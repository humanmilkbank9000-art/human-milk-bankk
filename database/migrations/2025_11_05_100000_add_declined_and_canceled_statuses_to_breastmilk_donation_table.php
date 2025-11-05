<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add 'declined' and 'canceled' to the status enum so code can set these values safely.
        // We use a raw ALTER statement because Laravel's schema builder does not support
        // altering enum value lists portably.
        DB::statement("ALTER TABLE `breastmilk_donation` MODIFY COLUMN `status` ENUM('pending_walk_in','success_walk_in','pending_home_collection','scheduled_home_collection','success_home_collection','declined','canceled') NOT NULL DEFAULT 'pending_walk_in'");
    }

    public function down(): void
    {
        // Revert to the original enum values (remove 'declined' and 'canceled').
        DB::statement("ALTER TABLE `breastmilk_donation` MODIFY COLUMN `status` ENUM('pending_walk_in','success_walk_in','pending_home_collection','scheduled_home_collection','success_home_collection') NOT NULL DEFAULT 'pending_walk_in'");
    }
};
